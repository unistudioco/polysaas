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
     * Helper function to properly handle media file imports
     * This is hooked into wp_import_fetch_remote_file filter
     * 
     * @param string $url URL of item to fetch
     * @param array $post Attachment post details
     * @return array|WP_Error Local file location details on success, WP_Error otherwise
     */
    public static function handle_media_import($url, $post) {
        if (empty($url)) {
            error_log('Media Import: Empty URL provided');
            return new \WP_Error('empty_url', 'Empty URL provided for media import');
        }

        error_log('Media Import: Starting import for URL: ' . $url);
        error_log('Media Import: Post data: ' . print_r($post, true));

        // Configure request args for binary files
        $args = array(
            'timeout' => 300,
            'stream' => true,
            'filename' => wp_tempnam(),
            'headers' => array(
                'Accept-Encoding' => 'identity',
                'Accept' => '*/*'
            ),
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'sslverify' => false // For local development
        );

        error_log('Media Import: Temp file created at: ' . $args['filename']);

        // Download file directly to temp file
        $response = wp_safe_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            error_log('Media Import: Download failed - ' . $response->get_error_message());
            @unlink($args['filename']);
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_message = wp_remote_retrieve_response_message($response);
        $response_headers = wp_remote_retrieve_headers($response);
        
        error_log('Media Import: Response code: ' . $response_code);
        error_log('Media Import: Response message: ' . $response_message);
        error_log('Media Import: Response headers: ' . print_r($response_headers, true));

        if ($response_code !== 200) {
            error_log('Media Import: Invalid response code - ' . $response_code);
            @unlink($args['filename']);
            return new \WP_Error('http_404', trim($response_message));
        }

        // Get upload directory with post date
        $uploads = wp_upload_dir(isset($post['upload_date']) ? $post['upload_date'] : null);
        if ($uploads['error']) {
            error_log('Media Import: Upload directory error - ' . $uploads['error']);
            @unlink($args['filename']);
            return new \WP_Error('upload_dir_error', $uploads['error']);
        }

        error_log('Media Import: Upload directory: ' . print_r($uploads, true));

        // Extract filename from URL and sanitize it
        $url_filename = basename(parse_url($url, PHP_URL_PATH));
        error_log('Media Import: Original filename: ' . $url_filename);
        
        // Get file extension from content-type if missing
        $tmp_type = wp_check_filetype($url_filename);
        if (!$tmp_type['ext'] && isset($response_headers['content-type'])) {
            $tmp_ext = self::get_file_extension_by_mime_type($response_headers['content-type']);
            if ($tmp_ext) {
                $url_filename = $url_filename . '.' . $tmp_ext;
                error_log('Media Import: Added extension from content-type: ' . $url_filename);
            }
        }

        // Generate unique filename
        $filename = wp_unique_filename($uploads['path'], $url_filename);
        $new_file = $uploads['path'] . "/$filename";
        error_log('Media Import: Final filename: ' . $filename);
        error_log('Media Import: Full path: ' . $new_file);

        // Move temp file to uploads directory using WordPress filesystem
        global $wp_filesystem;
        if (!$wp_filesystem) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }

        // Check temp file
        $temp_file_size = filesize($args['filename']);
        $temp_file_readable = is_readable($args['filename']);
        error_log('Media Import: Temp file size: ' . $temp_file_size);
        error_log('Media Import: Temp file readable: ' . ($temp_file_readable ? 'yes' : 'no'));

        // Copy file using WordPress filesystem
        $move_result = $wp_filesystem->copy($args['filename'], $new_file, true);
        @unlink($args['filename']); // Clean up temp file

        if (!$move_result) {
            error_log('Media Import: Failed to move file to - ' . $new_file);
            return new \WP_Error('import_file_error', 'Could not move file to uploads directory');
        }

        // Set proper permissions
        $stat = stat(dirname($new_file));
        $perms = $stat['mode'] & 0000666;
        chmod($new_file, $perms);

        // Check final file
        $final_file_size = filesize($new_file);
        $final_file_readable = is_readable($new_file);
        error_log('Media Import: Final file size: ' . $final_file_size);
        error_log('Media Import: Final file readable: ' . ($final_file_readable ? 'yes' : 'no'));

        // Verify file is a valid image if it claims to be one
        $file_type = wp_check_filetype($new_file);
        error_log('Media Import: File type check: ' . print_r($file_type, true));

        if (strpos($file_type['type'], 'image/') === 0) {
            $image_size = @getimagesize($new_file);
            error_log('Media Import: Image size check: ' . ($image_size ? print_r($image_size, true) : 'failed'));
            
            if (!$image_size) {
                @unlink($new_file);
                error_log('Media Import: Invalid image file - ' . $new_file);
                return new \WP_Error('invalid_image', 'File is not a valid image');
            }
        }

        error_log('Media Import: Successfully imported file - ' . $new_file);
        return array(
            'file' => $new_file,
            'url' => $uploads['url'] . "/$filename"
        );
    }

    /**
     * Get file extension for a mime type
     * Copied from WordPress importer for consistency
     */
    private static function get_file_extension_by_mime_type($mime_type) {
        static $extensions = array(
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'image/bmp'  => 'bmp',
            'image/tiff' => 'tif',
        );

        $mime_type = strtolower($mime_type);
        return isset($extensions[$mime_type]) ? $extensions[$mime_type] : null;
    }

    public static function manual_xml_import($file_path, $options = array()) {
        error_log('Manual XML Import: Starting manual import of ' . $file_path);
        
        if (!file_exists($file_path)) {
            error_log('Manual XML Import: File not found - ' . $file_path);
            return new \WP_Error('file_not_found', 'Import file not found.');
        }
        
        // Initialize Demo Content Importer
        require_once get_template_directory() . '/inc/setup/class-demo-content-importer.php';
        $importer = new Demo_Content_Importer();
        
        try {
            // Import content using Demo Content Importer
            $result = $importer->import_content($file_path, $options);
            
            if (is_wp_error($result)) {
                error_log('Manual XML Import: Import failed - ' . $result->get_error_message());
                return $result;
            }
            
            error_log('Manual XML Import: Import completed successfully');
            error_log('Manual XML Import: Result - ' . print_r($result, true));
            
            return $result;
            
        } catch (\Exception $e) {
            error_log('Manual XML Import: Exception - ' . $e->getMessage());
            error_log('Manual XML Import: Stack trace - ' . $e->getTraceAsString());
            return new \WP_Error('import_error', $e->getMessage());
        }
    }
}