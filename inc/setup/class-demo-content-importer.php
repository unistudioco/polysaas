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
        
        if (!class_exists('WXR_Importer')) {
            // You'll need to include the WordPress Importer plugin files
            // Or use a custom XML importer
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
        // This is a simplified version - you'll need to implement
        // the actual XML import logic using WordPress Importer
        
        $imported_data = array(
            'posts' => 0,
            'pages' => 0,
            'attachments' => 0,
            'comments' => 0
        );
        
        // Example: Parse XML and import content
        $xml_data = simplexml_load_file($file);
        
        if (!$xml_data) {
            return new \WP_Error('xml_parse_error', 'Failed to parse XML file.');
        }
        
        // Import posts if selected
        if (in_array('posts', $options)) {
            $imported_data['posts'] = $this->import_posts($xml_data);
        }
        
        // Import pages if selected
        if (in_array('pages', $options)) {
            $imported_data['pages'] = $this->import_pages($xml_data);
        }
        
        // Import attachments/media if selected
        if (in_array('media', $options)) {
            $imported_data['attachments'] = $this->import_media($xml_data);
        }
        
        return $imported_data;
    }
    
    /**
     * Import posts from XML data
     */
    private function import_posts($xml_data) {
        $imported_count = 0;
        
        // Example implementation - you'll need to adapt this
        // based on your XML structure
        
        return $imported_count;
    }
    
    /**
     * Import pages from XML data
     */
    private function import_pages($xml_data) {
        $imported_count = 0;
        
        // Example implementation
        
        return $imported_count;
    }
    
    /**
     * Import media files from XML data
     */
    private function import_media($xml_data) {
        $imported_count = 0;
        
        // Example implementation - handle media import
        
        return $imported_count;
    }
    
    /**
     * Import customizer settings
     */
    public function import_customizer_settings($customizer_file) {
        if (!file_exists($customizer_file)) {
            return new \WP_Error('file_not_found', 'Customizer file not found.');
        }
        
        $customizer_data = file_get_contents($customizer_file);
        $settings = json_decode($customizer_data, true);
        
        if (!$settings) {
            return new \WP_Error('json_decode_error', 'Failed to decode customizer settings.');
        }
        
        $imported_settings = 0;
        
        foreach ($settings as $setting_key => $setting_value) {
            // Import theme mods
            if (strpos($setting_key, 'theme_mod_') === 0) {
                $mod_name = str_replace('theme_mod_', '', $setting_key);
                set_theme_mod($mod_name, $setting_value);
                $imported_settings++;
            }
            // Import other customizer settings
            else {
                update_option($setting_key, $setting_value);
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
     * Import widget settings
     */
    public function import_widgets($widgets_file) {
        if (!file_exists($widgets_file)) {
            return new \WP_Error('file_not_found', 'Widgets file not found.');
        }
        
        $widgets_data = file_get_contents($widgets_file);
        $widgets = json_decode($widgets_data, true);
        
        if (!$widgets) {
            return new \WP_Error('json_decode_error', 'Failed to decode widgets data.');
        }
        
        $imported_widgets = 0;
        
        // Import sidebars widgets
        if (isset($widgets['sidebars_widgets'])) {
            update_option('sidebars_widgets', $widgets['sidebars_widgets']);
        }
        
        // Import widget instances
        foreach ($widgets as $widget_type => $widget_data) {
            if ($widget_type !== 'sidebars_widgets') {
                update_option('widget_' . $widget_type, $widget_data);
                $imported_widgets++;
            }
        }
        
        return array(
            'success' => true,
            'imported_widgets' => $imported_widgets,
            'message' => "Imported {$imported_widgets} widget types"
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
            
            // Import widgets if selected
            if (in_array('widgets', $selected_options) && isset($demo_data['widgets_file'])) {
                $widgets_result = $this->import_widgets($demo_data['widgets_file']);
                if (is_wp_error($widgets_result)) {
                    $errors[] = $widgets_result->get_error_message();
                } else {
                    $results['widgets'] = $widgets_result;
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
            
            // Set up pages
            $pages_result = $this->setup_wordpress_pages();
            if (is_wp_error($pages_result)) {
                $errors[] = $pages_result->get_error_message();
            } else {
                $results['pages_setup'] = $pages_result;
            }
            
            // Final cleanup
            $cleanup_result = $this->cleanup_after_import();
            if (is_wp_error($cleanup_result)) {
                $errors[] = $cleanup_result->get_error_message();
            } else {
                $results['cleanup'] = $cleanup_result;
            }
            
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
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