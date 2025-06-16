<?php
namespace PolySaaS\Setup;

use WP_Error;
use WP_Import;
use PolySaaS\Setup\Parsers\WXR_Parser;
use PolySaaS\Setup\Parsers\WXR_Parser_SimpleXML;
use PolySaaS\Setup\Parsers\WXR_Parser_XML;
use PolySaaS\Setup\Parsers\WXR_Parser_Regex;
use Polysaas\Setup\Attachment_Downloader;

/**
 * Demo Content Importer
 * Handles the actual import of demo content, customizer settings, and widgets
 */
class Demo_Content_Importer {
    
    private $upload_dir;
    private $upload_url;
    private $processed_attachments = array();
    private $url_remap = array();
    private $featured_images = array();
    private $attachment_downloader;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Include required files
        $result = $this->include_required_files();
        if (is_wp_error($result)) {
            error_log('Demo Import: Failed to include required files - ' . $result->get_error_message());
            throw new \Exception($result->get_error_message());
        }
        
        // Set up upload directory info
        $upload_dir = wp_upload_dir();
        $this->upload_dir = $upload_dir['basedir'];
        $this->upload_url = $upload_dir['baseurl'];
        
        // Initialize attachment downloader
        $this->attachment_downloader = new Attachment_Downloader();
    }
    
    /**
     * Include required WordPress files for import functionality
     * @return bool|WP_Error Returns true on success, WP_Error on failure
     */
    private function include_required_files() {
        if (!class_exists('WP_Importer')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
        }
        
        if (!function_exists('wp_import_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/import.php';
        }
        
        // Include WXR Parser and its parsers
        if (!class_exists('PolySaaS\\Setup\\Parsers\\WXR_Parser')) {
            require_once get_template_directory() . '/inc/setup/parsers.php';
        }
        
        if (!class_exists('WP_Import')) {
            $importer_path = get_template_directory() . '/inc/setup/class-wp-import.php';
            if (file_exists($importer_path)) {
                require_once $importer_path;
            } else {
                return new WP_Error('missing_importer', 'WordPress Importer class not found.');
            }
        }
        
        return true;
    }
    
    /**
     * Import demo content from XML file
     */
    public function import_content($content_file, $import_options = array()) {
        error_log('Demo Import: Starting content import from ' . $content_file);
        
        if (!file_exists($content_file)) {
            return new WP_Error('file_not_found', 'Content file not found.');
        }
        
        // Include required files
        $result = $this->include_required_files();
        if (is_wp_error($result)) {
            return $result;
        }
        
        // Ensure uploads directory is writable
        $upload_dir = wp_upload_dir();
        if (!empty($upload_dir['error'])) {
            return new WP_Error('upload_dir_error', $upload_dir['error']);
        }
        
        // Create necessary directories
        $paths_to_create = array(
            $upload_dir['basedir'],
            $upload_dir['path']
        );
        
        foreach ($paths_to_create as $path) {
            if (!file_exists($path)) {
                wp_mkdir_p($path);
            }
            if (!is_writable($path)) {
                chmod($path, 0755);
            }
        }
        
        // Initialize importer
        try {
            $importer = new WP_Import();
            if (!$importer) {
                return new WP_Error('importer_init_failed', 'Failed to initialize WordPress Importer.');
            }
            
            // Parse the file first
            $parser = new Parsers\WXR_Parser();
            $import_data = $parser->parse($content_file);
            if (is_wp_error($import_data)) {
                return $import_data;
            }
            
            // Set up importer data
            $importer->version = $import_data['version'];
            $importer->get_authors_from_import($import_data);
            $importer->posts = $import_data['posts'];
            $importer->terms = $import_data['terms'];
            $importer->categories = $import_data['categories'];
            $importer->tags = $import_data['tags'];
            $importer->base_url = esc_url($import_data['base_url']);
            
            // Configure import settings
            $importer->fetch_attachments = true;
            
            // Add our custom filters
            add_filter('import_allow_fetch_attachments', '__return_true');
            add_filter('import_allow_create_users', '__return_false');
            add_filter('wp_import_post_data_raw', array($this, 'filter_post_data'));
            add_filter('import_post_meta_key', array($this, 'filter_post_meta_key'));
            add_filter('wp_import_attachment_url', array($this, 'filter_attachment_url'));
            add_filter('wp_import_fetch_remote_file', array($this, 'fetch_remote_file'), 10, 2);
            
            // Debug logging for attachment creation
            add_action('add_attachment', function($post_id) {
                $post = get_post($post_id);
                error_log('Demo Import: Attachment created - ' . print_r($post, true));
            });
            
            // Start import
            ob_start();
            error_log('Demo Import: Running import...');
            
            // Suspend cache invalidation
            wp_suspend_cache_invalidation(true);
            wp_defer_term_counting(true);
            wp_defer_comment_counting(true);
            
            // Process content
            $importer->process_categories();
            $importer->process_tags();
            $importer->process_terms();
            $importer->process_posts();
            
            // Resume cache invalidation
            wp_suspend_cache_invalidation(false);
            wp_defer_term_counting(false);
            wp_defer_comment_counting(false);
            
            // Cleanup
            $importer->backfill_parents();
            $importer->backfill_attachment_urls();
            $importer->remap_featured_images();
            
            // Process any remaining attachments
            $this->process_remaining_attachments();
            
            // Update content URLs
            $this->update_content_urls();
            
            // Remap featured images
            $this->remap_featured_images();
            
            error_log('Demo Import: Import completed successfully');
            return array(
                'success' => true,
                'message' => 'Content imported successfully'
            );
            
        } catch (\Exception $e) {
            error_log('Demo Import: Exception - ' . $e->getMessage());
            return new WP_Error('import_error', $e->getMessage());
        } finally {
            ob_end_clean();
            
            // Remove our custom filters
            remove_filter('import_allow_fetch_attachments', '__return_true');
            remove_filter('import_allow_create_users', '__return_false');
            remove_filter('wp_import_post_data_raw', array($this, 'filter_post_data'));
            remove_filter('import_post_meta_key', array($this, 'filter_post_meta_key'));
            remove_filter('wp_import_attachment_url', array($this, 'filter_attachment_url'));
            remove_filter('wp_import_fetch_remote_file', array($this, 'fetch_remote_file'));
        }
    }
    
    /**
     * Filter post data before import
     */
    public function filter_post_data($post) {
        if ($post['post_type'] === 'attachment') {
            // Store attachment for later processing
            $this->processed_attachments[] = $post;
            
            // Ensure we have a valid file URL
            if (!empty($post['attachment_url'])) {
                $post['attachment_url'] = $this->validate_attachment_url($post['attachment_url']);
            }
            
            // Store featured image relationships
            if (!empty($post['post_parent'])) {
                $this->featured_images[$post['post_parent']] = $post['attachment_url'];
            }
        }
        return $post;
    }
    
    /**
     * Filter post meta key to handle special cases
     */
    public function filter_post_meta_key($key) {
        // skip attachment metadata since we'll regenerate it from scratch
        // skip _edit_lock as not relevant for import
        if (in_array($key, array('_wp_attached_file', '_wp_attachment_metadata', '_edit_lock'), true)) {
            return false;
        }
        return $key;
    }
    
    /**
     * Filter attachment URL before import
     */
    public function filter_attachment_url($url) {
        return $this->validate_attachment_url($url);
    }
    
    /**
     * Custom handler for fetching remote files
     */
    public function fetch_remote_file($url, $post) {
        error_log('Demo Import: Fetching remote file - ' . $url);
        
        if (empty($url)) {
            return new \WP_Error('empty_url', 'Empty URL provided for attachment');
        }
        
        // Initialize attachment downloader if not already done
        if (!$this->attachment_downloader) {
            require_once get_template_directory() . '/inc/setup/class-attachment-downloader.php';
            $this->attachment_downloader = new Attachment_Downloader();
        }
        
        // Process the attachment
        $attachment_id = $this->attachment_downloader->process_attachment($post, $url);
        
        if (is_wp_error($attachment_id)) {
            error_log('Demo Import: Failed to process attachment - ' . $attachment_id->get_error_message());
            return $attachment_id;
        }
        
        // Store the remapped URL
        $this->url_remap[$url] = wp_get_attachment_url($attachment_id);
        
        // Store featured image relationship if this is one
        if (!empty($post['post_parent'])) {
            $this->featured_images[$post['post_parent']] = $attachment_id;
        }
        
        error_log('Demo Import: Successfully processed attachment - ID: ' . $attachment_id);
        return wp_get_attachment_url($attachment_id);
    }
    
    /**
     * Validate and potentially modify attachment URL
     */
    private function validate_attachment_url($url) {
        // Remove query strings
        $url = preg_replace('/\?.*/', '', $url);
        
        // Handle relative URLs
        if (strpos($url, '//') === 0) {
            $url = 'https:' . $url;
        } elseif (strpos($url, '/') === 0) {
            $url = home_url($url);
        }
        
        // Handle demo domain URLs
        $demo_domains = array(
            'demo.yoursite.com',
            'demos.yoursite.com',
            'preview.yoursite.com'
        );
        
        foreach ($demo_domains as $demo_domain) {
            if (strpos($url, $demo_domain) !== false) {
                $url = str_replace($demo_domain, $_SERVER['HTTP_HOST'], $url);
            }
        }
        
        return $url;
    }
    
    /**
     * Process any remaining attachments that failed during the main import
     */
    private function process_remaining_attachments() {
        global $wpdb;
        
        // Get all attachment posts that don't have files
        $attachments = $wpdb->get_results(
            "SELECT ID, guid, post_title, post_parent 
            FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            AND ID NOT IN (
                SELECT post_id 
                FROM {$wpdb->postmeta} 
                WHERE meta_key = '_wp_attached_file'
            )"
        );
        
        foreach ($attachments as $attachment) {
            $file_url = $this->validate_attachment_url($attachment->guid);
            
            if (!empty($file_url)) {
                error_log("Demo Import: Attempting to download attachment {$attachment->ID} from {$file_url}");
                
                // Create post data array for the attachment
                $post_data = array(
                    'post_title' => $attachment->post_title,
                    'post_parent' => $attachment->post_parent,
                    'post_type' => 'attachment',
                    'guid' => $file_url
                );
                
                // Process the attachment
                $id = $this->attachment_downloader->process_attachment($post_data, $file_url);
                
                if (!is_wp_error($id)) {
                    error_log("Demo Import: Successfully processed attachment {$attachment->ID}");
                    
                    // Store the remapped URL
                    $this->url_remap[$file_url] = wp_get_attachment_url($id);
                    
                    // Update featured image if this was one
                    if (!empty($attachment->post_parent)) {
                        update_post_meta($attachment->post_parent, '_thumbnail_id', $id);
                    }
                } else {
                    error_log("Demo Import: Failed to process attachment {$attachment->ID}: " . $id->get_error_message());
                }
            }
        }
        
        // Update content URLs
        $this->update_content_urls();
    }
    
    /**
     * Update content URLs after import
     */
    private function update_content_urls() {
        global $wpdb;
        
        // Only process if we have URLs to remap
        if (empty($this->url_remap)) {
            return;
        }
        
        // Get all posts that might contain URLs
        $posts = $wpdb->get_results(
            "SELECT ID, post_content, post_excerpt 
            FROM {$wpdb->posts} 
            WHERE post_type != 'attachment' 
            AND (post_content LIKE '%src=%' 
                OR post_content LIKE '%href=%'
                OR post_excerpt LIKE '%src=%'
                OR post_excerpt LIKE '%href=%')"
        );
        
        foreach ($posts as $post) {
            $update_post = false;
            $content = $post->post_content;
            $excerpt = $post->post_excerpt;
            
            // Update URLs in content
            foreach ($this->url_remap as $old_url => $new_url) {
                if (strpos($content, $old_url) !== false) {
                    $content = str_replace($old_url, $new_url, $content);
                    $update_post = true;
                }
                if (strpos($excerpt, $old_url) !== false) {
                    $excerpt = str_replace($old_url, $new_url, $excerpt);
                    $update_post = true;
                }
            }
            
            // Update post if needed
            if ($update_post) {
                $wpdb->update(
                    $wpdb->posts,
                    array(
                        'post_content' => $content,
                        'post_excerpt' => $excerpt
                    ),
                    array('ID' => $post->ID)
                );
            }
        }
    }
    
    /**
     * Remap featured images after import
     */
    private function remap_featured_images() {
        foreach ($this->featured_images as $post_id => $attachment_data) {
            if (is_numeric($attachment_data)) {
                // If we already have the attachment ID
                update_post_meta($post_id, '_thumbnail_id', $attachment_data);
            } else {
                // If we have the URL, try to find the attachment by URL
                global $wpdb;
                $attachment = $wpdb->get_row($wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} 
                    WHERE post_type = 'attachment' 
                    AND (guid = %s OR guid = %s)",
                    $attachment_data,
                    $this->url_remap[$attachment_data] ?? ''
                ));
                
                if ($attachment) {
                    update_post_meta($post_id, '_thumbnail_id', $attachment->ID);
                    error_log("Demo Import: Remapped featured image for post {$post_id} to attachment {$attachment->ID}");
                }
            }
        }
    }
    
    /**
     * Import customizer settings from .dat file (PHP serialized format)
     */
    public function import_customizer_settings($customizer_file) {
        if (!file_exists($customizer_file)) {
            return new \WP_Error('file_not_found', 'Customizer file not found.');
        }
        
        $customizer_data = file_get_contents($customizer_file);
        
        // Handle .dat files (PHP serialized format)
        if (pathinfo($customizer_file, PATHINFO_EXTENSION) === 'dat') {
            $settings = unserialize($customizer_data);
        } else {
            // Handle JSON format as fallback
            $settings = json_decode($customizer_data, true);
        }
        
        if (!$settings || !is_array($settings)) {
            return new \WP_Error('decode_error', 'Failed to decode customizer settings.');
        }
        
        $imported_settings = 0;
        
        // Import theme mods if they exist
        if (isset($settings['mods']) && is_array($settings['mods'])) {
            foreach ($settings['mods'] as $mod_name => $mod_value) {
                set_theme_mod($mod_name, $mod_value);
                $imported_settings++;
            }
        }
        
        // Import options if they exist
        if (isset($settings['options']) && is_array($settings['options'])) {
            foreach ($settings['options'] as $option_name => $option_value) {
                update_option($option_name, $option_value);
                $imported_settings++;
            }
        }
        
        return array(
            'success' => true,
            'imported_settings' => $imported_settings,
            'message' => "Imported {$imported_settings} customizer settings"
        );
    }
    
    /**
     * Import WordPress options
     */
    public function import_options($options_data) {
        $imported_options = 0;
        
        foreach ($options_data as $option_name => $option_value) {
            // Skip sensitive options
            $skip_options = array(
                'siteurl',
                'home',
                'admin_email',
                'users_can_register',
                'default_role'
            );
            
            if (!in_array($option_name, $skip_options)) {
                update_option($option_name, $option_value);
                $imported_options++;
            }
        }
        
        return array(
            'success' => true,
            'imported_options' => $imported_options,
            'message' => "Imported {$imported_options} options"
        );
    }
    
    /**
     * Set up WordPress pages after import
     */
    public function setup_wordpress_pages() {
        // Set front page if imported
        $front_page = get_posts([
            'post_type' => 'page',
            'post_status' => 'publish',
            'title' => 'Home',
            'posts_per_page' => 1,
        ]);
        if (!empty($front_page[0])) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $front_page[0]->ID);
        }
        
        // Set blog page if imported
        $blog_page = get_posts([
            'post_type' => 'page',
            'post_status' => 'publish',
            'title' => 'Blog',
            'posts_per_page' => 1,
        ]);
        if (!empty($blog_page[0])) {
            update_option('page_for_posts', $blog_page[0]->ID);
        }
        
        // Set WooCommerce pages if WooCommerce is active
        if (class_exists('WooCommerce')) {
            $this->setup_woocommerce_pages();
        }
        
        return array(
            'success' => true,
            'message' => 'WordPress pages configured'
        );
    }
    
    /**
     * Set up WooCommerce pages
     */
    private function setup_woocommerce_pages() {
        $pages = array(
            'shop' => 'Shop',
            'cart' => 'Cart',
            'checkout' => 'Checkout',
            'myaccount' => 'My Account'
        );
        
        foreach ($pages as $option => $page_title) {
            $page = get_posts([
                'post_type' => 'page',
                'post_status' => 'publish',
                'title' => $page_title,
                'posts_per_page' => 1,
            ]);
            if (!empty($page[0])) {
                update_option('woocommerce_' . $option . '_page_id', $page[0]->ID);
            }
        }
    }
    
    /**
     * Import WooCommerce settings
     */
    public function import_woocommerce_settings($wc_settings) {
        if (!class_exists('WooCommerce')) {
            return new \WP_Error('woocommerce_not_active', 'WooCommerce is not active.');
        }
        
        $imported_settings = 0;
        
        foreach ($wc_settings as $setting_key => $setting_value) {
            if (strpos($setting_key, 'woocommerce_') === 0) {
                update_option($setting_key, $setting_value);
                $imported_settings++;
            }
        }
        
        return array(
            'success' => true,
            'imported_settings' => $imported_settings,
            'message' => "Imported {$imported_settings} WooCommerce settings"
        );
    }
    
    /**
     * Clean up after import
     */
    public function cleanup_after_import() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear any caches
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Update permalink structure if needed
        $permalink_structure = get_option('permalink_structure');
        if (empty($permalink_structure)) {
            update_option('permalink_structure', '/%postname%/');
            flush_rewrite_rules();
        }
        
        return array(
            'success' => true,
            'message' => 'Cleanup completed'
        );
    }
    
    /**
     * Complete demo import process
     */
    public function complete_demo_import($demo_data, $selected_options) {
        $results = array();
        $errors = array();
        
        try {
            // Import content if selected
            if (in_array('pages', $selected_options) || 
                in_array('posts', $selected_options) || 
                in_array('portfolio', $selected_options) ||
                in_array('products', $selected_options)) {
                
                $content_result = $this->import_content($demo_data['content_file'], $selected_options);
                if (is_wp_error($content_result)) {
                    $errors[] = $content_result->get_error_message();
                } else {
                    $results['content'] = $content_result;
                }
            }
            
            // Import customizer settings if selected
            if (in_array('customizer', $selected_options) && isset($demo_data['customizer_file'])) {
                $customizer_result = $this->import_customizer_settings($demo_data['customizer_file']);
                if (is_wp_error($customizer_result)) {
                    $errors[] = $customizer_result->get_error_message();
                } else {
                    $results['customizer'] = $customizer_result;
                }
            }
            
            // Import WooCommerce settings if selected
            if (in_array('woocommerce', $selected_options) && isset($demo_data['woocommerce_settings'])) {
                $wc_result = $this->import_woocommerce_settings($demo_data['woocommerce_settings']);
                if (is_wp_error($wc_result)) {
                    $errors[] = $wc_result->get_error_message();
                } else {
                    $results['woocommerce'] = $wc_result;
                }
            }
            
            // Set up pages - these functions return arrays, not WP_Error objects
            $pages_result = $this->setup_wordpress_pages();
            if (isset($pages_result['success']) && $pages_result['success']) {
                $results['pages_setup'] = $pages_result;
            } else {
                $errors[] = isset($pages_result['message']) ? $pages_result['message'] : 'Pages setup failed';
            }
            
            // Final cleanup - these functions return arrays, not WP_Error objects
            $cleanup_result = $this->cleanup_after_import();
            if (isset($cleanup_result['success']) && $cleanup_result['success']) {
                $results['cleanup'] = $cleanup_result;
            } else {
                $errors[] = isset($cleanup_result['message']) ? $cleanup_result['message'] : 'Cleanup failed';
            }
            
        } catch (\Exception $e) {
            $errors[] = 'Exception: ' . $e->getMessage();
        } catch (\Throwable $e) {
            $errors[] = 'Fatal Error: ' . $e->getMessage();
        }
        
        // Return results
        if (!empty($errors)) {
            return new \WP_Error('import_errors', 'Import completed with errors: ' . implode(', ', $errors), $results);
        }
        
        return array(
            'success' => true,
            'message' => 'Demo content imported successfully!',
            'results' => $results
        );
    }
    
    /**
     * Get import progress for AJAX updates
     */
    public function get_import_progress() {
        // This could be implemented to track import progress
        // using transients or database options
        
        return array(
            'current_step' => get_transient('demo_import_current_step') ?: 'starting',
            'progress_percent' => get_transient('demo_import_progress') ?: 0,
            'status_message' => get_transient('demo_import_status') ?: 'Preparing import...'
        );
    }
    
    /**
     * Update import progress
     */
    public function update_import_progress($step, $percent, $message) {
        set_transient('demo_import_current_step', $step, 300);
        set_transient('demo_import_progress', $percent, 300);
        set_transient('demo_import_status', $message, 300);
    }
    
    /**
     * Clear import progress
     */
    public function clear_import_progress() {
        delete_transient('demo_import_current_step');
        delete_transient('demo_import_progress');
        delete_transient('demo_import_status');
    }
}