<?php
namespace Polysaas\Setup;

/**
 * Demo Content Importer
 * Handles the actual import of demo content, customizer settings, and widgets
 */
class Demo_Content_Importer {
    
    public function __construct() {
        // Include required WordPress files
        $this->include_required_files();
    }
    
    /**
     * Include required WordPress files for import functionality
     */
    private function include_required_files() {
        if (!class_exists('WP_Importer')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
        }
        
        if (!function_exists('wp_import_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/import.php';
        }
        
        if (!class_exists('WP_Import')) {
            $importer_path = ABSPATH . 'wp-admin/includes/class-wp-import.php';
            if (file_exists($importer_path)) {
                require_once $importer_path;
            }
        }
    }
    
    /**
     * Import demo content from XML file
     */
    public function import_content($content_file, $import_options = array()) {
        if (!file_exists($content_file)) {
            return new \WP_Error('file_not_found', 'Content file not found.');
        }
        
        // Import XML content
        $importer_result = $this->import_xml_content($content_file, $import_options);
        
        if (is_wp_error($importer_result)) {
            return $importer_result;
        }
        
        return array(
            'success' => true,
            'message' => 'Content imported successfully',
            'imported' => $importer_result
        );
    }
    
    /**
     * Import XML content using WordPress Importer
     */
    private function import_xml_content($file, $options) {
        // Include the importer installer
        $installer_file = get_template_directory() . '/inc/setup/class-importer-installer.php';
        if (!file_exists($installer_file)) {
            return new \WP_Error('installer_missing', 'Importer installer file not found');
        }
        require_once $installer_file;
        
        error_log('Demo Import: Starting XML import for file: ' . $file);
        error_log('Demo Import: Import options: ' . print_r($options, true));
        
        $imported_data = array(
            'posts' => 0,
            'pages' => 0,
            'attachments' => 0,
            'comments' => 0
        );
        
        // Store counts before import
        global $wpdb;
        $posts_before = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'");
        $pages_before = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'");
        $attachments_before = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment'");
        
        // Try WordPress Importer first (but don't fail if it doesn't work)
        $wp_importer_available = false;
        try {
            $importer_ready = Importer_Installer::ensure_wordpress_importer();
            error_log('Demo Import: WordPress Importer ready: ' . ($importer_ready ? 'Yes' : 'No'));
            
            if ($importer_ready && class_exists('WP_Import')) {
                error_log('Demo Import: Attempting WordPress Importer');
                
                // Create WP_Import instance
                $wp_import = new \WP_Import();
                
                // Set up import options
                $wp_import->fetch_attachments = in_array('media', $options);
                
                // Capture output to prevent it from being displayed
                ob_start();
                
                // Import the file
                $wp_import->import($file);
                
                // Clear the output buffer
                ob_end_clean();
                
                // Count newly imported items
                $posts_after = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'");
                $pages_after = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'");
                $attachments_after = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment'");
                
                $imported_data['posts'] = max(0, $posts_after - $posts_before);
                $imported_data['pages'] = max(0, $pages_after - $pages_before);
                $imported_data['attachments'] = max(0, $attachments_after - $attachments_before);
                
                error_log('Demo Import: WordPress Importer completed successfully');
                error_log('Demo Import: Imported data: ' . print_r($imported_data, true));
                
                $wp_importer_available = true;
            }
        } catch (\Exception $e) {
            if (ob_get_length()) {
                ob_end_clean();
            }
            error_log('Demo Import: WordPress Importer failed: ' . $e->getMessage());
            $wp_importer_available = false;
        }
        
        // If WordPress Importer didn't work or isn't available, use manual import
        if (!$wp_importer_available) {
            error_log('Demo Import: Using manual XML import as fallback');
            
            $manual_result = Importer_Installer::manual_xml_import($file, $options);
            
            if (is_wp_error($manual_result)) {
                error_log('Demo Import: Manual import also failed: ' . $manual_result->get_error_message());
                return $manual_result;
            }
            
            $imported_data = $manual_result;
            error_log('Demo Import: Manual import completed successfully');
            error_log('Demo Import: Imported data: ' . print_r($imported_data, true));
        }
        
        return $imported_data;
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
        $front_page = get_page_by_title('Home');
        if ($front_page) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $front_page->ID);
        }
        
        // Set blog page if imported
        $blog_page = get_page_by_title('Blog');
        if ($blog_page) {
            update_option('page_for_posts', $blog_page->ID);
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
            $page = get_page_by_title($page_title);
            if ($page) {
                update_option('woocommerce_' . $option . '_page_id', $page->ID);
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