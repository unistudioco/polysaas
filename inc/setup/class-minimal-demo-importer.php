<?php
/**
 * Minimal Demo Importer for Testing
 * Use this to isolate the 500 error issue
 */

namespace Polysaas\Setup;

class Minimal_Demo_Importer {
    
    public function __construct() {
        add_action('wp_ajax_minimal_demo_import', array($this, 'handle_import'));
    }
    
    public function handle_import() {
        try {
            // Basic security check
            if (!current_user_can('import')) {
                wp_send_json_error('No permissions');
                return;
            }
            
            error_log('Minimal Demo Importer: Starting import');
            
            // Test 1: Basic response
            $result = array(
                'step' => 'basic_test',
                'success' => true,
                'message' => 'Basic AJAX working'
            );
            
            // Test 2: File check
            $demo_dir = get_template_directory() . '/inc/demo-data/creative-agency/';
            $content_file = $demo_dir . 'content.xml';
            $customizer_file = $demo_dir . 'customizer.dat';
            
            $result['files'] = array(
                'content_exists' => file_exists($content_file),
                'customizer_exists' => file_exists($customizer_file)
            );
            
            // Test 3: Try to read customizer file
            if (file_exists($customizer_file)) {
                $customizer_content = file_get_contents($customizer_file);
                $customizer_data = @unserialize($customizer_content);
                
                $result['customizer'] = array(
                    'file_size' => strlen($customizer_content),
                    'unserialize_success' => ($customizer_data !== false),
                    'data_structure' => is_array($customizer_data) ? array_keys($customizer_data) : 'invalid'
                );
                
                // Test 4: Try to import customizer settings
                if ($customizer_data && is_array($customizer_data)) {
                    $imported = 0;
                    
                    if (isset($customizer_data['mods']) && is_array($customizer_data['mods'])) {
                        foreach ($customizer_data['mods'] as $mod_name => $mod_value) {
                            set_theme_mod($mod_name, $mod_value);
                            $imported++;
                        }
                    }
                    
                    $result['customizer']['imported_mods'] = $imported;
                }
            }
            
            // Test 5: Try basic XML reading
            if (file_exists($content_file)) {
                $xml_content = file_get_contents($content_file);
                $xml = @simplexml_load_string($xml_content);
                
                $result['xml'] = array(
                    'file_size' => strlen($xml_content),
                    'parse_success' => ($xml !== false)
                );
                
                if ($xml) {
                    $items = $xml->xpath('//item');
                    $result['xml']['total_items'] = count($items);
                    
                    // Count post types
                    $post_types = array();
                    foreach ($items as $item) {
                        $post_type = (string) $item->children('wp', true)->post_type;
                        if (!isset($post_types[$post_type])) {
                            $post_types[$post_type] = 0;
                        }
                        $post_types[$post_type]++;
                    }
                    $result['xml']['post_types'] = $post_types;
                }
            }
            
            error_log('Minimal Demo Importer: Success - ' . print_r($result, true));
            wp_send_json_success($result);
            
        } catch (\Exception $e) {
            error_log('Minimal Demo Importer Exception: ' . $e->getMessage());
            wp_send_json_error('Exception: ' . $e->getMessage());
        } catch (\Throwable $e) {
            error_log('Minimal Demo Importer Fatal: ' . $e->getMessage());
            wp_send_json_error('Fatal: ' . $e->getMessage());
        }
    }
}

// Initialize the minimal importer
if (is_admin()) {
    new Minimal_Demo_Importer();
}

/**
 * Add admin page to test the minimal importer
 */
add_action('admin_menu', function() {
    if (current_user_can('manage_options')) {
        add_submenu_page(
            'tools.php',
            'Test Minimal Import',
            'Test Minimal Import',
            'manage_options',
            'test-minimal-import',
            function() {
                ?>
                <div class="wrap">
                    <h1>Test Minimal Import</h1>
                    <p>This will test the basic import functionality without the full wizard.</p>
                    
                    <button id="test-minimal-import" class="button button-primary">Test Import</button>
                    <div id="test-results" style="margin-top: 20px;"></div>
                    
                    <script>
                    jQuery(document).ready(function($) {
                        $('#test-minimal-import').click(function() {
                            var button = $(this);
                            button.prop('disabled', true).text('Testing...');
                            
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'minimal_demo_import'
                                },
                                success: function(response) {
                                    $('#test-results').html('<h3>Success!</h3><pre>' + JSON.stringify(response, null, 2) + '</pre>');
                                },
                                error: function(xhr, status, error) {
                                    $('#test-results').html('<h3>Error!</h3><p>Status: ' + status + '</p><p>Error: ' + error + '</p><p>Response: ' + xhr.responseText + '</p>');
                                },
                                complete: function() {
                                    button.prop('disabled', false).text('Test Import');
                                }
                            });
                        });
                    });
                    </script>
                </div>
                <?php
            }
        );
    }
});