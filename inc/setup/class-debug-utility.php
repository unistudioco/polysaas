<?php
namespace Polysaas\Setup;

/**
 * Debug and Testing Utility for Setup Wizard
 * Helps debug import issues and test functionality
 */
class Debug_Utility {
    
    /**
     * Test customizer file format and content
     */
    public static function test_customizer_file($file_path) {
        if (!file_exists($file_path)) {
            return array(
                'success' => false,
                'error' => 'File not found: ' . $file_path
            );
        }
        
        $content = file_get_contents($file_path);
        $extension = pathinfo($file_path, PATHINFO_EXTENSION);
        
        $result = array(
            'file_path' => $file_path,
            'file_size' => filesize($file_path),
            'extension' => $extension,
            'content_preview' => substr($content, 0, 200) . '...',
        );
        
        // Test different parsing methods
        if ($extension === 'dat') {
            $unserialized = @unserialize($content);
            $result['unserialize_success'] = $unserialized !== false;
            if ($unserialized) {
                $result['parsed_keys'] = array_keys($unserialized);
                $result['has_mods'] = isset($unserialized['mods']);
                $result['has_options'] = isset($unserialized['options']);
            }
        } elseif ($extension === 'json') {
            $json_decoded = json_decode($content, true);
            $result['json_decode_success'] = $json_decoded !== null;
            if ($json_decoded) {
                $result['parsed_keys'] = array_keys($json_decoded);
            }
        }
        
        return $result;
    }
    
    /**
     * Test XML file structure
     */
    public static function test_xml_file($file_path) {
        if (!file_exists($file_path)) {
            return array(
                'success' => false,
                'error' => 'File not found: ' . $file_path
            );
        }
        
        $xml_content = file_get_contents($file_path);
        $xml = @simplexml_load_string($xml_content);
        
        $result = array(
            'file_path' => $file_path,
            'file_size' => filesize($file_path),
            'xml_valid' => $xml !== false,
        );
        
        if ($xml) {
            $items = $xml->xpath('//item');
            $result['total_items'] = count($items);
            
            // Count different post types
            $post_types = array();
            foreach ($items as $item) {
                $post_type = (string) $item->children('wp', true)->post_type;
                if (!isset($post_types[$post_type])) {
                    $post_types[$post_type] = 0;
                }
                $post_types[$post_type]++;
            }
            
            $result['post_types'] = $post_types;
            
            // Check for categories and terms
            $categories = $xml->xpath('//wp:category');
            $result['categories_count'] = count($categories);
        } else {
            $result['xml_errors'] = libxml_get_errors();
        }
        
        return $result;
    }
    
    /**
     * Test WordPress Importer availability
     */
    public static function test_importer_availability() {
        $result = array();
        
        // Check if WP_Import class exists
        $result['wp_import_class'] = class_exists('WP_Import');
        
        // Check if WordPress Importer plugin is installed
        $installed_plugins = get_plugins();
        $result['importer_plugin_installed'] = isset($installed_plugins['wordpress-importer/wordpress-importer.php']);
        
        // Check if WordPress Importer plugin is active
        $result['importer_plugin_active'] = is_plugin_active('wordpress-importer/wordpress-importer.php');
        
        // Check file paths
        $importer_base = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
        $importer_main = ABSPATH . 'wp-admin/includes/class-wp-import.php';
        
        $result['base_importer_file'] = file_exists($importer_base);
        $result['main_importer_file'] = file_exists($importer_main);
        
        return $result;
    }
    
    /**
     * Log import attempts for debugging
     */
    public static function log_import_attempt($demo_key, $options, $result) {
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'demo' => $demo_key,
            'options' => $options,
            'result' => $result,
            'user_id' => get_current_user_id(),
        );
        
        $existing_logs = get_option('polysaas_import_logs', array());
        $existing_logs[] = $log_entry;
        
        // Keep only last 10 logs
        if (count($existing_logs) > 10) {
            $existing_logs = array_slice($existing_logs, -10);
        }
        
        update_option('polysaas_import_logs', $existing_logs);
    }
    
    /**
     * Get import logs
     */
    public static function get_import_logs() {
        return get_option('polysaas_import_logs', array());
    }
    
    /**
     * Clear import logs
     */
    public static function clear_import_logs() {
        delete_option('polysaas_import_logs');
    }
    
    /**
     * Test demo files existence
     */
    public static function test_demo_files($demos) {
        $results = array();
        
        foreach ($demos as $demo_key => $demo) {
            $demo_result = array(
                'name' => $demo['name'],
                'content_file_exists' => file_exists($demo['content_file']),
                'customizer_file_exists' => file_exists($demo['customizer_file']),
            );
            
            if (isset($demo['preview_image'])) {
                $demo_result['preview_image_exists'] = file_exists(str_replace(get_template_directory_uri(), get_template_directory(), $demo['preview_image']));
            }
            
            $results[$demo_key] = $demo_result;
        }
        
        return $results;
    }
    
    /**
     * Generate debug report
     */
    public static function generate_debug_report() {
        $report = array(
            'timestamp' => current_time('mysql'),
            'wordpress_version' => get_bloginfo('version'),
            'theme_version' => wp_get_theme()->get('Version'),
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        );
        
        // Test importer availability
        $report['importer_status'] = self::test_importer_availability();
        
        // Get recent import logs
        $report['recent_logs'] = array_slice(self::get_import_logs(), -5);
        
        // Check theme setup wizard files
        $setup_files = array(
            'class-theme-setup-wizard.php' => get_template_directory() . '/inc/setup/class-theme-setup-wizard.php',
            'class-demo-content-importer.php' => get_template_directory() . '/inc/setup/class-demo-content-importer.php',
            'class-importer-installer.php' => get_template_directory() . '/inc/setup/class-importer-installer.php',
            'setup-wizard-template.php' => get_template_directory() . '/inc/setup-wizard-template.php',
            'wizard-js' => get_template_directory() . '/assets/js/admin/theme-setup-wizard.js',
            'wizard-css' => get_template_directory() . '/assets/css/admin/theme-setup-wizard.css',
        );
        
        foreach ($setup_files as $name => $path) {
            $report['setup_files'][$name] = file_exists($path);
        }
        
        return $report;
    }
    
    /**
     * Display debug report in admin (for testing)
     */
    public static function display_debug_report() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        echo '<div class="wrap">';
        echo '<h1>Setup Wizard Debug Report</h1>';
        
        $report = self::generate_debug_report();
        
        echo '<pre style="background: #f1f1f1; padding: 15px; font-size: 12px; overflow: auto;">';
        echo esc_html(print_r($report, true));
        echo '</pre>';
        
        echo '</div>';
    }
}