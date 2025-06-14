<?php
/**
 * Theme functions and definitions
 *
 * @package Polysaas
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define base theme constants first
define('THEME_VERSION', '1.0.0');
define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());
define('THEME_INC', THEME_DIR . '/inc');
define('THEME_ASSETS', THEME_URI . '/assets');

// Composer autoload if it exists
if (file_exists(THEME_DIR . '/vendor/autoload.php')) {
    require_once THEME_DIR . '/vendor/autoload.php';
}

// Load core classes
require_once THEME_INC . '/core/class-config.php';
require_once THEME_INC . '/core/class-loader.php';
require_once THEME_INC . '/core/class-init.php';

// Load Typography Output
require_once THEME_INC . '/customizer/output.php';

// Register Autoloader
Polysaas\Core\Loader::register();

// Initialize theme
Polysaas\Core\Init::init();


/**
 * Quick Fix Test Script
 * Add this to functions.php temporarily to test the fix
 */

// Test the WordPress Importer availability
add_action('wp_ajax_test_wp_importer', 'test_wp_importer_availability');

function test_wp_importer_availability() {
    // Include the importer installer
    require_once get_template_directory() . '/inc/setup/class-importer-installer.php';
    
    $results = array();
    
    // Test 1: Check if WP_Import class exists before
    $results['wp_import_before'] = class_exists('WP_Import');
    
    // Test 2: Try to ensure WordPress Importer
    $results['ensure_importer'] = \Polysaas\Setup\Importer_Installer::ensure_wordpress_importer();
    
    // Test 3: Check if WP_Import class exists after
    $results['wp_import_after'] = class_exists('WP_Import');
    
    // Test 4: Check plugin status
    $installed_plugins = get_plugins();
    $results['importer_plugin_installed'] = isset($installed_plugins['wordpress-importer/wordpress-importer.php']);
    $results['importer_plugin_active'] = is_plugin_active('wordpress-importer/wordpress-importer.php');
    
    // Test 5: Check file paths
    $results['core_importer_file'] = file_exists(ABSPATH . 'wp-admin/includes/class-wp-import.php');
    $results['plugin_importer_file'] = file_exists(WP_PLUGIN_DIR . '/wordpress-importer/class-wp-import.php');
    
    // Test 6: User permissions
    $results['can_install_plugins'] = current_user_can('install_plugins');
    $results['can_activate_plugins'] = current_user_can('activate_plugins');
    
    wp_send_json_success($results);
}

// Test manual XML import specifically
add_action('wp_ajax_test_manual_import', 'test_manual_xml_import');

function test_manual_xml_import() {
    require_once get_template_directory() . '/inc/setup/class-importer-installer.php';
    
    $demo_file = get_template_directory() . '/inc/demo-data/creative-agency/content.xml';
    $options = array('pages', 'posts');
    
    if (!file_exists($demo_file)) {
        wp_send_json_error('Demo file not found: ' . $demo_file);
        return;
    }
    
    $result = \Polysaas\Setup\Importer_Installer::manual_xml_import($demo_file, $options);
    
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    } else {
        wp_send_json_success($result);
    }
}

// Test the full import process with better error handling
add_action('wp_ajax_test_full_import_process', 'test_full_import_process');

function test_full_import_process() {
    try {
        // Include required files
        require_once get_template_directory() . '/inc/setup/class-demo-content-importer.php';
        require_once get_template_directory() . '/inc/setup/class-importer-installer.php';
        
        // Create demo data
        $demo_data = array(
            'name' => 'Test Creative Agency',
            'content_file' => get_template_directory() . '/inc/demo-data/creative-agency/content.xml',
            'customizer_file' => get_template_directory() . '/inc/demo-data/creative-agency/customizer.dat',
        );
        
        $import_options = array('customizer'); // Start with just customizer to test
        
        // Test file existence
        if (!file_exists($demo_data['content_file'])) {
            wp_send_json_error('Content file not found: ' . $demo_data['content_file']);
            return;
        }
        
        if (!file_exists($demo_data['customizer_file'])) {
            wp_send_json_error('Customizer file not found: ' . $demo_data['customizer_file']);
            return;
        }
        
        // Create importer instance
        $importer = new \Polysaas\Setup\Demo_Content_Importer();
        
        // Test import
        $result = $importer->complete_demo_import($demo_data, $import_options);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success($result);
        }
        
    } catch (Exception $e) {
        wp_send_json_error('Exception: ' . $e->getMessage());
    } catch (Throwable $e) {
        wp_send_json_error('Fatal Error: ' . $e->getMessage());
    }
}

// Add admin menu for quick testing
add_action('admin_menu', function() {
    if (current_user_can('manage_options')) {
        add_submenu_page(
            'tools.php',
            'Import Fix Test',
            'Import Fix Test',
            'manage_options',
            'import-fix-test',
            function() {
                ?>
                <div class="wrap">
                    <h1>Import Fix Test</h1>
                    <p>Click the buttons below to test different aspects of the import system:</p>
                    
                    <div style="margin: 20px 0;">
                        <button id="test-wp-importer" class="button button-primary">Test WordPress Importer</button>
                        <button id="test-manual-import" class="button button-secondary">Test Manual Import</button>
                        <button id="test-full-process" class="button button-secondary">Test Full Process (Customizer Only)</button>
                    </div>
                    
                    <div id="test-results" style="margin-top: 20px; background: #f1f1f1; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto;"></div>
                    
                    <script>
                    jQuery(document).ready(function($) {
                        function runTest(action, button) {
                            button.prop('disabled', true).text('Testing...');
                            $('#test-results').text('Running test: ' + action + '...\n');
                            
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: { action: action },
                                success: function(response) {
                                    $('#test-results').text('SUCCESS for ' + action + ':\n' + JSON.stringify(response, null, 2));
                                },
                                error: function(xhr, status, error) {
                                    $('#test-results').text('ERROR for ' + action + ':\n' + 
                                        'Status: ' + status + '\n' + 
                                        'Error: ' + error + '\n' + 
                                        'Response: ' + xhr.responseText);
                                },
                                complete: function() {
                                    button.prop('disabled', false).text(button.data('original-text'));
                                }
                            });
                        }
                        
                        // Store original button text
                        $('.button').each(function() {
                            $(this).data('original-text', $(this).text());
                        });
                        
                        $('#test-wp-importer').click(function() {
                            runTest('test_wp_importer', $(this));
                        });
                        
                        $('#test-manual-import').click(function() {
                            runTest('test_manual_import', $(this));
                        });
                        
                        $('#test-full-process').click(function() {
                            runTest('test_full_import_process', $(this));
                        });
                    });
                    </script>
                </div>
                <?php
            }
        );
    }
});

/**
 * Simple Import Fix - Direct Approach
 * Add this to functions.php to test the working import directly
 */

// Simple working demo import handler
add_action('wp_ajax_simple_demo_import', 'handle_simple_demo_import');

function handle_simple_demo_import() {
    try {
        // Security check
        if (!current_user_can('import')) {
            wp_send_json_error('No permissions');
            return;
        }
        
        // Get parameters
        $demo_key = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : 'creative-agency';
        $import_options = isset($_POST['import_options']) ? $_POST['import_options'] : array('pages', 'posts', 'customizer');
        
        error_log('Simple Demo Import: Starting import for ' . $demo_key);
        error_log('Simple Demo Import: Options - ' . print_r($import_options, true));
        
        // Define demo files
        $demo_dir = get_template_directory() . '/inc/demo-data/' . $demo_key . '/';
        $content_file = $demo_dir . 'content.xml';
        $customizer_file = $demo_dir . 'customizer.dat';
        
        $results = array();
        
        // Import content if selected
        if ((in_array('pages', $import_options) || in_array('posts', $import_options) || in_array('media', $import_options)) && file_exists($content_file)) {
            error_log('Simple Demo Import: Importing content from ' . $content_file);
            
            // Use the working manual import
            require_once get_template_directory() . '/inc/setup/class-importer-installer.php';
            $content_result = \Polysaas\Setup\Importer_Installer::manual_xml_import($content_file, $import_options);
            
            if (is_wp_error($content_result)) {
                wp_send_json_error('Content import failed: ' . $content_result->get_error_message());
                return;
            }
            
            $results['content'] = $content_result;
            error_log('Simple Demo Import: Content imported successfully - ' . print_r($content_result, true));
        }
        
        // Import customizer if selected
        if (in_array('customizer', $import_options) && file_exists($customizer_file)) {
            error_log('Simple Demo Import: Importing customizer from ' . $customizer_file);
            
            // Import customizer settings directly
            $customizer_content = file_get_contents($customizer_file);
            $customizer_data = @unserialize($customizer_content);
            
            if ($customizer_data && is_array($customizer_data)) {
                $imported_settings = 0;
                
                // Import theme mods
                if (isset($customizer_data['mods']) && is_array($customizer_data['mods'])) {
                    foreach ($customizer_data['mods'] as $mod_name => $mod_value) {
                        set_theme_mod($mod_name, $mod_value);
                        $imported_settings++;
                    }
                }
                
                // Import options
                if (isset($customizer_data['options']) && is_array($customizer_data['options'])) {
                    foreach ($customizer_data['options'] as $option_name => $option_value) {
                        update_option($option_name, $option_value);
                        $imported_settings++;
                    }
                }
                
                $results['customizer'] = array(
                    'success' => true,
                    'imported_settings' => $imported_settings,
                    'message' => "Imported {$imported_settings} customizer settings"
                );
                
                error_log('Simple Demo Import: Customizer imported successfully - ' . $imported_settings . ' settings');
            } else {
                error_log('Simple Demo Import: Failed to parse customizer file');
            }
        }
        
        // Set up pages
        if (isset($results['content']) && $results['content']['pages'] > 0) {
            // Set front page
            $front_page = get_page_by_title('Home');
            if ($front_page) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $front_page->ID);
                error_log('Simple Demo Import: Set Home as front page');
            }
            
            // Set blog page
            $blog_page = get_page_by_title('Blog');
            if ($blog_page) {
                update_option('page_for_posts', $blog_page->ID);
                error_log('Simple Demo Import: Set Blog page for posts');
            }
        }
        
        // Cleanup
        flush_rewrite_rules();
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Build success message
        $message = 'Demo content imported successfully!';
        if (isset($results['content'])) {
            $message .= ' Imported ' . $results['content']['pages'] . ' pages and ' . $results['content']['posts'] . ' posts.';
        }
        if (isset($results['customizer'])) {
            $message .= ' Applied customizations.';
        }
        
        error_log('Simple Demo Import: Completed successfully');
        
        wp_send_json_success(array(
            'message' => $message,
            'results' => $results
        ));
        
    } catch (Exception $e) {
        error_log('Simple Demo Import Exception: ' . $e->getMessage());
        wp_send_json_error('Exception: ' . $e->getMessage());
    } catch (Throwable $e) {
        error_log('Simple Demo Import Fatal: ' . $e->getMessage());
        wp_send_json_error('Fatal Error: ' . $e->getMessage());
    }
}

/**
 * Override the original wizard AJAX handler temporarily
 */
add_action('wp_ajax_import_demo_content', 'override_wizard_import', 1);

function override_wizard_import() {
    // Call our working simple import instead
    handle_simple_demo_import();
    
    // Prevent the original handler from running
    wp_die();
}

/**
 * Add a test page for the simple import
 */
add_action('admin_menu', function() {
    if (current_user_can('manage_options')) {
        add_submenu_page(
            'tools.php',
            'Simple Demo Import',
            'Simple Demo Import',
            'manage_options',
            'simple-demo-import',
            function() {
                ?>
                <div class="wrap">
                    <h1>Simple Demo Import Test</h1>
                    <p>This uses the working manual import method directly.</p>
                    
                    <div style="margin: 20px 0;">
                        <h3>Import Options:</h3>
                        <label><input type="checkbox" id="opt-pages" checked> Pages</label><br>
                        <label><input type="checkbox" id="opt-posts" checked> Posts</label><br>
                        <label><input type="checkbox" id="opt-media"> Media</label><br>
                        <label><input type="checkbox" id="opt-customizer" checked> Customizer</label><br>
                    </div>
                    
                    <button id="simple-import-btn" class="button button-primary">Import Creative Agency Demo</button>
                    <div id="simple-results" style="margin-top: 20px;"></div>
                    
                    <script>
                    jQuery(document).ready(function($) {
                        $('#simple-import-btn').click(function() {
                            var button = $(this);
                            button.prop('disabled', true).text('Importing...');
                            
                            var options = [];
                            if ($('#opt-pages').is(':checked')) options.push('pages');
                            if ($('#opt-posts').is(':checked')) options.push('posts');
                            if ($('#opt-media').is(':checked')) options.push('media');
                            if ($('#opt-customizer').is(':checked')) options.push('customizer');
                            
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'simple_demo_import',
                                    demo: 'creative-agency',
                                    import_options: options
                                },
                                success: function(response) {
                                    $('#simple-results').html('<div style="background:#d4edda;padding:15px;border-radius:5px;color:#155724;"><h3>✅ Success!</h3><p>' + response.data.message + '</p><pre>' + JSON.stringify(response.data.results, null, 2) + '</pre></div>');
                                },
                                error: function(xhr, status, error) {
                                    $('#simple-results').html('<div style="background:#f8d7da;padding:15px;border-radius:5px;color:#721c24;"><h3>❌ Error!</h3><p>' + (xhr.responseJSON ? xhr.responseJSON.data : error) + '</p></div>');
                                },
                                complete: function() {
                                    button.prop('disabled', false).text('Import Creative Agency Demo');
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