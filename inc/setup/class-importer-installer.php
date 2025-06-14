<?php
namespace Polysaas\Setup;

/**
 * WordPress Importer Auto-Installer
 * Handles automatic installation of WordPress Importer plugin if not available
 */
class Importer_Installer {
    
    /**
     * Check if WordPress Importer is available and install if needed
     */
    public static function ensure_wordpress_importer() {
        error_log('Importer Installer: Starting WordPress Importer check');
        
        // Check if WP_Import class is already available
        if (class_exists('WP_Import')) {
            error_log('Importer Installer: WP_Import class already available');
            return true;
        }
        
        // Try to include the WordPress Importer from default WordPress location
        $core_importer_path = ABSPATH . 'wp-admin/includes/class-wp-import.php';
        if (file_exists($core_importer_path)) {
            error_log('Importer Installer: Found core WordPress importer, including it');
            
            // Include base importer class first
            $base_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
            if (file_exists($base_importer)) {
                require_once $base_importer;
            }
            
            require_once $core_importer_path;
            
            if (class_exists('WP_Import')) {
                error_log('Importer Installer: Successfully loaded core WordPress importer');
                return true;
            }
        }
        
        // Check if WordPress Importer plugin is installed
        if (self::is_wordpress_importer_plugin_installed()) {
            error_log('Importer Installer: WordPress Importer plugin is installed');
            
            // Try to activate it if not active
            if (!is_plugin_active('wordpress-importer/wordpress-importer.php')) {
                error_log('Importer Installer: Activating WordPress Importer plugin');
                $result = activate_plugin('wordpress-importer/wordpress-importer.php');
                if (is_wp_error($result)) {
                    error_log('Importer Installer: Failed to activate plugin: ' . $result->get_error_message());
                } else {
                    error_log('Importer Installer: Successfully activated WordPress Importer plugin');
                }
            } else {
                error_log('Importer Installer: WordPress Importer plugin already active');
            }
            
            // Try different possible file locations for the plugin
            $possible_plugin_paths = array(
                WP_PLUGIN_DIR . '/wordpress-importer/class-wp-import.php',
                WP_PLUGIN_DIR . '/wordpress-importer/class-wp-importer.php', // Old incorrect path - removed
                WP_PLUGIN_DIR . '/wordpress-importer/wordpress-importer.php',
            );
            
            // Also include the base importer class
            $base_plugin_importer = WP_PLUGIN_DIR . '/wordpress-importer/class-wp-importer.php';
            if (file_exists($base_plugin_importer)) {
                require_once $base_plugin_importer;
                error_log('Importer Installer: Loaded base plugin importer class');
            }
            
            foreach ($possible_plugin_paths as $plugin_path) {
                if (file_exists($plugin_path)) {
                    error_log('Importer Installer: Found plugin importer at: ' . $plugin_path);
                    require_once $plugin_path;
                    
                    if (class_exists('WP_Import')) {
                        error_log('Importer Installer: Successfully loaded plugin WordPress importer');
                        return true;
                    }
                }
            }
            
            error_log('Importer Installer: Plugin installed but class files not found in expected locations');
        } else {
            error_log('Importer Installer: WordPress Importer plugin not installed, attempting installation');
            
            // Try to install WordPress Importer plugin
            if (current_user_can('install_plugins')) {
                $install_result = self::install_wordpress_importer_plugin();
                if ($install_result) {
                    error_log('Importer Installer: Successfully installed and activated WordPress Importer plugin');
                    return true;
                } else {
                    error_log('Importer Installer: Failed to install WordPress Importer plugin');
                }
            } else {
                error_log('Importer Installer: User does not have permission to install plugins');
            }
        }
        
        error_log('Importer Installer: All attempts failed, will use manual import fallback');
        return false;
    }
    
    /**
     * Check if WordPress Importer plugin is installed
     */
    private static function is_wordpress_importer_plugin_installed() {
        $installed_plugins = get_plugins();
        return isset($installed_plugins['wordpress-importer/wordpress-importer.php']);
    }
    
    /**
     * Install WordPress Importer plugin
     */
    private static function install_wordpress_importer_plugin() {
        // Include required files
        if (!function_exists('plugins_api')) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }
        if (!class_exists('WP_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        
        try {
            // Get plugin info from WordPress.org
            $api = plugins_api('plugin_information', array('slug' => 'wordpress-importer'));
            
            if (is_wp_error($api)) {
                return false;
            }
            
            // Install the plugin
            $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($api->download_link);
            
            if (is_wp_error($result)) {
                return false;
            }
            
            // Activate the plugin
            $activate_result = activate_plugin('wordpress-importer/wordpress-importer.php');
            
            if (is_wp_error($activate_result)) {
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Alternative manual XML importer (fallback if WordPress Importer fails)
     */
    public static function manual_xml_import($file_path, $options = array()) {
        error_log('Manual XML Import: Starting manual import of ' . $file_path);
        
        if (!file_exists($file_path)) {
            error_log('Manual XML Import: File not found - ' . $file_path);
            return new \WP_Error('file_not_found', 'Import file not found.');
        }
        
        // Load XML content
        $xml_content = file_get_contents($file_path);
        if (!$xml_content) {
            error_log('Manual XML Import: Could not read file content');
            return new \WP_Error('file_read_error', 'Could not read import file.');
        }
        
        // Use libxml to handle encoding issues
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xml_content, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        if (!$xml) {
            $xml_errors = libxml_get_errors();
            $error_messages = array();
            foreach ($xml_errors as $error) {
                $error_messages[] = trim($error->message);
            }
            error_log('Manual XML Import: XML parse errors - ' . implode(', ', $error_messages));
            return new \WP_Error('xml_parse_error', 'Failed to parse XML file: ' . implode(', ', $error_messages));
        }
        
        error_log('Manual XML Import: XML parsed successfully');
        
        $imported_data = array(
            'posts' => 0,
            'pages' => 0,
            'attachments' => 0,
            'comments' => 0
        );
        
        // Import posts and pages
        if (isset($xml->channel->item)) {
            $total_items = count($xml->channel->item);
            error_log('Manual XML Import: Found ' . $total_items . ' items to import');
            
            foreach ($xml->channel->item as $item) {
                try {
                    $wp_ns = $item->children('wp', true);
                    $post_type = (string) $wp_ns->post_type;
                    $post_status = (string) $wp_ns->status;
                    
                    // Skip if not in selected options
                    if ($post_type === 'post' && !in_array('posts', $options)) continue;
                    if ($post_type === 'page' && !in_array('pages', $options)) continue;
                    if ($post_type === 'attachment' && !in_array('media', $options)) continue;
                    
                    // Get content from CDATA section
                    $content_ns = $item->children('content', true);
                    $excerpt_ns = $item->children('excerpt', true);
                    
                    // Prepare post data
                    $post_data = array(
                        'post_title' => (string) $item->title,
                        'post_content' => (string) $content_ns->encoded,
                        'post_excerpt' => (string) $excerpt_ns->encoded,
                        'post_type' => $post_type,
                        'post_status' => $post_status === 'publish' ? 'publish' : 'draft',
                        'post_date' => (string) $wp_ns->post_date,
                        'post_name' => (string) $wp_ns->post_name,
                        'menu_order' => (int) $wp_ns->menu_order,
                        'comment_status' => (string) $wp_ns->comment_status,
                        'ping_status' => (string) $wp_ns->ping_status,
                    );
                    
                    // Check if post already exists by title and type
                    $existing_post = get_page_by_title($post_data['post_title'], OBJECT, $post_type);
                    if ($existing_post) {
                        error_log('Manual XML Import: Skipping existing post - ' . $post_data['post_title']);
                        continue; // Skip if already exists
                    }
                    
                    // Insert post
                    $post_id = wp_insert_post($post_data, true);
                    
                    if (is_wp_error($post_id)) {
                        error_log('Manual XML Import: Failed to insert post - ' . $post_id->get_error_message());
                        continue;
                    }
                    
                    if ($post_id) {
                        // Import post meta
                        if (isset($wp_ns->postmeta)) {
                            foreach ($wp_ns->postmeta as $meta) {
                                $meta_wp_ns = $meta->children('wp', true);
                                $meta_key = (string) $meta_wp_ns->meta_key;
                                $meta_value = (string) $meta_wp_ns->meta_value;
                                
                                // Skip internal meta that shouldn't be imported
                                if (strpos($meta_key, '_edit_') === 0) continue;
                                if (strpos($meta_key, '_wp_old_') === 0) continue;
                                
                                // Handle serialized data
                                $unserialized_value = @unserialize($meta_value);
                                if ($unserialized_value !== false) {
                                    $meta_value = $unserialized_value;
                                }
                                
                                update_post_meta($post_id, $meta_key, $meta_value);
                            }
                        }
                        
                        // Import categories and tags
                        if (isset($item->category)) {
                            $categories = array();
                            $tags = array();
                            
                            foreach ($item->category as $category) {
                                $domain = (string) $category->attributes()->domain;
                                $nicename = (string) $category->attributes()->nicename;
                                $cat_name = (string) $category;
                                
                                if ($domain === 'category') {
                                    $cat = get_category_by_slug($nicename);
                                    if (!$cat) {
                                        $cat_id = wp_create_category($cat_name);
                                        if ($cat_id && !is_wp_error($cat_id)) {
                                            $categories[] = $cat_id;
                                        }
                                    } else {
                                        $categories[] = $cat->term_id;
                                    }
                                } elseif ($domain === 'post_tag') {
                                    $tags[] = $cat_name;
                                }
                            }
                            
                            if (!empty($categories)) {
                                wp_set_post_categories($post_id, $categories);
                            }
                            if (!empty($tags)) {
                                wp_set_post_tags($post_id, $tags);
                            }
                        }
                        
                        // Count imported items
                        if ($post_type === 'post') {
                            $imported_data['posts']++;
                        } elseif ($post_type === 'page') {
                            $imported_data['pages']++;
                        } elseif ($post_type === 'attachment') {
                            $imported_data['attachments']++;
                        }
                        
                        error_log('Manual XML Import: Successfully imported - ' . $post_data['post_title'] . ' (ID: ' . $post_id . ')');
                    }
                    
                } catch (\Exception $e) {
                    error_log('Manual XML Import: Error importing item - ' . $e->getMessage());
                    continue;
                }
            }
        }
        
        error_log('Manual XML Import: Completed - ' . print_r($imported_data, true));
        return $imported_data;
    }
}