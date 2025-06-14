<?php
namespace Polysaas\Setup;

use Polysaas\Core\Config;
use Polysaas\Core\Functions;

/**
 * Complete Theme Setup Wizard
 * Multi-step installation process with plugins and demo content
 */
class Theme_Setup_Wizard {
    
    private $plugins = array();
    private $demos = array();
    private $theme_name;
    private $wizard_dismissed_option = 'theme_setup_wizard_dismissed';
    private $wizard_completed_option = 'theme_setup_wizard_completed';
    
    public function __construct() {
        $this->theme_name = get_template();
        $this->set_plugins();
        $this->set_demos();
        
        // Hook into theme activation
        add_action('after_switch_theme', array($this, 'theme_activated'));
        
        // Admin hooks
        add_action('admin_enqueue_scripts', array($this, 'enqueue_wizard_scripts'));
        add_action('admin_footer', array($this, 'render_wizard'));
        
        // AJAX handlers
        add_action('wp_ajax_install_plugin_wizard', array($this, 'install_plugin_ajax'));
        add_action('wp_ajax_activate_plugin_wizard', array($this, 'activate_plugin_ajax'));
        add_action('wp_ajax_get_plugin_status_wizard', array($this, 'get_plugin_status_ajax'));
        add_action('wp_ajax_get_demos_wizard', array($this, 'get_demos_ajax'));
        add_action('wp_ajax_import_demo_content', array($this, 'import_demo_content_ajax'));
        add_action('wp_ajax_dismiss_setup_wizard', array($this, 'dismiss_wizard_ajax'));
        add_action('wp_ajax_complete_setup_wizard', array($this, 'complete_wizard_ajax'));
    }
    
    /**
     * Define required and recommended plugins
     */
    private function set_plugins() {
        $this->plugins = array(
            'required' => array(
                'elementor' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/elementor.gif',
                    'name' => 'Elementor',
                    'slug' => 'elementor',
                    'source' => 'repo',
                    'file_path' => 'elementor/elementor.php',
                    'description' => 'Page builder for creating beautiful layouts.',
                    'required' => true
                ),
                'unistudio-core' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/unistudio-core.svg',
                    'name' => 'UniStudio Core',
                    'slug' => 'unistudio-core',
                    'source' => 'external',
                    'file_path' => 'unistudio-core/unicore.php',
                    'external_url' => get_template_directory_uri() . '/inc/plugins/unistudio-core.zip',
                    'description' => 'Custom Premium functionalities.',
                    'required' => true
                ),
                'acf' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/acf.svg',
                    'name' => 'Advanced Custom Fields',
                    'slug' => 'acf',
                    'source' => 'external',
                    'file_path' => 'advanced-custom-fields/acf.php',
                    'external_url' => get_template_directory_uri() . '/inc/plugins/acf.zip',
                    'description' => 'Custom Fields functionalities.',
                    'required' => true
                ),
                'kirki' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/kirki.jpg',
                    'name' => 'Kirki',
                    'slug' => 'kirki',
                    'source' => 'repo',
                    'file_path' => 'kirki/kirki.php',
                    'description' => 'Custom UI Fields for WordPress Customizer.',
                    'required' => true
                )
            ),
            'recommended' => array(
                'contact-form-7' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/contact-form-7.svg',
                    'name' => 'Contact Form 7',
                    'slug' => 'contact-form-7',
                    'source' => 'repo',
                    'file_path' => 'contact-form-7/wp-contact-form-7.php',
                    'description' => 'Simple contact form plugin.',
                    'required' => false
                ),
                'woocommerce' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/woocommerce.svg',
                    'name' => 'WooCommerce',
                    'slug' => 'woocommerce',
                    'source' => 'repo',
                    'file_path' => 'woocommerce/woocommerce.php',
                    'description' => 'Complete eCommerce solution for WordPress.',
                    'required' => false
                ),
                'elementor-animejs-addon' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/elementor-animejs-addon.gif',
                    'name' => 'Elementor AnimeJS Addon',
                    'slug' => 'elementor-animejs-addon',
                    'source' => 'external',
                    'file_path' => 'elementor-animejs-addon/index.php',
                    'external_url' => get_template_directory_uri() . '/inc/plugins/elementor-animejs-addon.zip',
                    'description' => 'Advanced Animations via AnimeJS Library for Elementor.',
                    'required' => false
                ),
                'wpml' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/wpml.png',
                    'name' => 'WPML',
                    'slug' => 'woocommerce-multilingual',
                    'source' => 'repo',
                    'file_path' => 'woocommerce-multilingual/wpml-woocommerce.php',
                    'description' => 'WooCommerce Multilingual & Multicurrency with WPML.',
                    'required' => false
                )
            )
        );
    }
    
    /**
     * Define available demo content
     */
    private function set_demos() {
        $this->demos = array(
            'creative-agency' => array(
                'name' => 'Creative Agency',
                'description' => 'Perfect for creative agencies and design studios',
                'preview_image' => get_template_directory_uri() . '/inc/demo-data/creative-agency/preview.jpg',
                'demo_url' => 'https://demo.example.com/creative-agency',
                'content_file' => get_template_directory() . '/inc/demo-data/creative-agency/content.xml',
                'customizer_file' => get_template_directory() . '/inc/demo-data/creative-agency/customizer.dat',
                'options' => array(
                    'pages' => array(
                        'label' => 'Pages',
                        'description' => 'Import all demo pages',
                        'default' => true
                    ),
                    'posts' => array(
                        'label' => 'Blog Posts',
                        'description' => 'Import demo blog posts',
                        'default' => true
                    ),
                    'media' => array(
                        'label' => 'Media Files',
                        'description' => 'Import demo images and media',
                        'default' => true
                    ),
                    'customizer' => array(
                        'label' => 'Theme Settings',
                        'description' => 'Import theme customizer settings',
                        'default' => true
                    )
                )
            ),
            'business-corporate' => array(
                'name' => 'Business Corporate',
                'description' => 'Ideal for corporate websites and business portfolios',
                'preview_image' => get_template_directory_uri() . '/inc/demo-data/business-corporate/preview.jpg',
                'demo_url' => 'https://demo.example.com/business-corporate',
                'content_file' => get_template_directory() . '/inc/demo-data/business-corporate/content.xml',
                'customizer_file' => get_template_directory() . '/inc/demo-data/business-corporate/customizer.dat',
                'options' => array(
                    'pages' => array(
                        'label' => 'Pages',
                        'description' => 'Import all demo pages',
                        'default' => true
                    ),
                    'posts' => array(
                        'label' => 'Blog Posts',
                        'description' => 'Import demo blog posts',
                        'default' => true
                    ),
                    'portfolio' => array(
                        'label' => 'Portfolio Items',
                        'description' => 'Import portfolio/project items',
                        'default' => true
                    ),
                    'media' => array(
                        'label' => 'Media Files',
                        'description' => 'Import demo images and media',
                        'default' => true
                    ),
                    'customizer' => array(
                        'label' => 'Theme Settings',
                        'description' => 'Import theme customizer settings',
                        'default' => true
                    )
                )
            ),
            'ecommerce-shop' => array(
                'name' => 'eCommerce Shop',
                'description' => 'Complete online store with WooCommerce integration',
                'preview_image' => get_template_directory_uri() . '/inc/demo-data/ecommerce-shop/preview.jpg',
                'demo_url' => 'https://demo.example.com/ecommerce-shop',
                'content_file' => get_template_directory() . '/inc/demo-data/ecommerce-shop/content.xml',
                'customizer_file' => get_template_directory() . '/inc/demo-data/ecommerce-shop/customizer.dat',
                'required_plugins' => array('woocommerce'),
                'options' => array(
                    'pages' => array(
                        'label' => 'Pages',
                        'description' => 'Import all demo pages',
                        'default' => true
                    ),
                    'products' => array(
                        'label' => 'Products',
                        'description' => 'Import demo products',
                        'default' => true
                    ),
                    'posts' => array(
                        'label' => 'Blog Posts',
                        'description' => 'Import demo blog posts',
                        'default' => false
                    ),
                    'media' => array(
                        'label' => 'Media Files',
                        'description' => 'Import demo images and media',
                        'default' => true
                    ),
                    'customizer' => array(
                        'label' => 'Theme Settings',
                        'description' => 'Import theme customizer settings',
                        'default' => true
                    ),
                    'woocommerce' => array(
                        'label' => 'WooCommerce Settings',
                        'description' => 'Import WooCommerce configuration',
                        'default' => true
                    )
                )
            ),
            'minimal-portfolio' => array(
                'name' => 'Minimal Portfolio',
                'description' => 'Clean and minimal design for creative professionals',
                'preview_image' => get_template_directory_uri() . '/inc/demo-data/minimal-portfolio/preview.jpg',
                'demo_url' => 'https://demo.example.com/minimal-portfolio',
                'content_file' => get_template_directory() . '/inc/demo-data/minimal-portfolio/content.xml',
                'customizer_file' => get_template_directory() . '/inc/demo-data/minimal-portfolio/customizer.dat',
                'options' => array(
                    'pages' => array(
                        'label' => 'Pages',
                        'description' => 'Import all demo pages',
                        'default' => true
                    ),
                    'portfolio' => array(
                        'label' => 'Portfolio Items',
                        'description' => 'Import portfolio/project items',
                        'default' => true
                    ),
                    'media' => array(
                        'label' => 'Media Files',
                        'description' => 'Import demo images and media',
                        'default' => true
                    ),
                    'customizer' => array(
                        'label' => 'Theme Settings',
                        'description' => 'Import theme customizer settings',
                        'default' => true
                    )
                )
            ),
            'saas-software' => array(
                'name' => 'SaaS Software',
                'description' => 'SaaS, Softwares, Startups',
                'preview_image' => get_template_directory_uri() . '/inc/demo-data/saas-software/preview.jpg',
                'demo_url' => 'https://demo.example.com/saas-software',
                'content_file' => get_template_directory() . '/inc/demo-data/saas-software/content.xml',
                'customizer_file' => get_template_directory() . '/inc/demo-data/saas-software/customizer.dat',
                'required_plugins' => array('Contact Form 7'),
                'options' => array(
                    'pages' => array(
                        'label' => 'Pages',
                        'description' => 'Import all demo pages',
                        'default' => true
                    ),
                    'posts' => array(
                        'label' => 'Blog Posts',
                        'description' => 'Import demo blog posts',
                        'default' => false
                    ),
                    'media' => array(
                        'label' => 'Media Files',
                        'description' => 'Import demo images and media',
                        'default' => true
                    ),
                    'customizer' => array(
                        'label' => 'Theme Settings',
                        'description' => 'Import theme customizer settings',
                        'default' => true
                    )
                )
            ),
            'marketing-agency' => array(
                'name' => 'Marketing Agency',
                'description' => 'Marketing agencies, Businesses',
                'preview_image' => get_template_directory_uri() . '/inc/demo-data/marketing-agency/preview.jpg',
                'demo_url' => 'https://demo.example.com/marketing-agency',
                'content_file' => get_template_directory() . '/inc/demo-data/marketing-agency/content.xml',
                'customizer_file' => get_template_directory() . '/inc/demo-data/marketing-agency/customizer.dat',
                'options' => array(
                    'pages' => array(
                        'label' => 'Pages',
                        'description' => 'Import all demo pages',
                        'default' => true
                    ),
                    'posts' => array(
                        'label' => 'Blog Posts',
                        'description' => 'Import demo blog posts',
                        'default' => false
                    ),
                    'media' => array(
                        'label' => 'Media Files',
                        'description' => 'Import demo images and media',
                        'default' => true
                    ),
                    'customizer' => array(
                        'label' => 'Theme Settings',
                        'description' => 'Import theme customizer settings',
                        'default' => true
                    )
                )
            ),
        );
    }
    
    /**
     * Called when theme is activated
     */
    public function theme_activated() {
        // Set a transient to show the wizard on next admin page load
        set_transient('show_theme_setup_wizard', true, 300); // 5 minutes
        
        // Reset dismissed status for new theme activation
        delete_option($this->wizard_dismissed_option);
        delete_option($this->wizard_completed_option);
    }
    
    /**
     * Enqueue wizard scripts and styles
     */
    public function enqueue_wizard_scripts() {
        // Only show wizard if not dismissed/completed and transient exists
        if (get_transient('show_theme_setup_wizard') && 
            !get_option($this->wizard_dismissed_option) && 
            !get_option($this->wizard_completed_option)) {
            
            wp_enqueue_script('theme-setup-wizard', get_template_directory_uri() . '/assets/js/admin/theme-setup-wizard.js', array('jquery'), '1.0.0', true);
            wp_enqueue_style('theme-setup-wizard', get_template_directory_uri() . '/assets/css/admin/theme-setup-wizard.css', array(), '1.0.0');
            
            wp_localize_script('theme-setup-wizard', 'themeSetupWizard', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('theme_setup_wizard_nonce'),
                'theme_name' => wp_get_theme()->get('Name'),
                'strings' => array(
                    'installing' => __('Installing...', 'textdomain'),
                    'activating' => __('Activating...', 'textdomain'),
                    'installed' => __('Installed', 'textdomain'),
                    'activated' => __('Activated', 'textdomain'),
                    'error' => __('Error occurred', 'textdomain'),
                    'success' => __('Success!', 'textdomain'),
                    'importing' => __('Importing...', 'textdomain'),
                    'complete' => __('Import Complete!', 'textdomain'),
                    'processing' => __('Processing...', 'textdomain')
                )
            ));
        }
    }
    
    /**
     * Render the setup wizard HTML
     */
    public function render_wizard() {
        // Only show wizard if conditions are met
        if (!get_transient('show_theme_setup_wizard') || 
            get_option($this->wizard_dismissed_option) || 
            get_option($this->wizard_completed_option)) {
            return;
        }
        
        include get_template_directory() . '/inc/setup-wizard-template.php';
    }
    
    /**
     * AJAX: Install plugin
     */
    public function install_plugin_ajax() {
        check_ajax_referer('theme_setup_wizard_nonce', 'nonce');
        
        if (!current_user_can('install_plugins')) {
            wp_send_json_error(__('Insufficient permissions.', 'textdomain'));
        }
        
        $plugin_key = sanitize_text_field($_POST['plugin']);
        $plugin_type = sanitize_text_field($_POST['type']); // 'required' or 'recommended'
        
        if (!isset($this->plugins[$plugin_type][$plugin_key])) {
            wp_send_json_error(__('Invalid plugin.', 'textdomain'));
        }
        
        $plugin = $this->plugins[$plugin_type][$plugin_key];
        
        // Include required files
        if (!function_exists('plugins_api')) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }
        if (!class_exists('WP_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        
        // Install plugin based on source
        if ($plugin['source'] === 'repo') {
            $api = plugins_api('plugin_information', array('slug' => $plugin['slug']));
            
            if (is_wp_error($api)) {
                wp_send_json_error($api->get_error_message());
            }
            
            $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($api->download_link);
        } else {
            // Handle external plugin installation
            $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($plugin['external_url']);
            
            // For external plugins, update the file_path to reflect actual installed folder
            if (!is_wp_error($result) && $plugin['source'] === 'external') {
                $actual_file_path = $this->detect_actual_plugin_path($plugin['slug'], $plugin['file_path']);
                if ($actual_file_path) {
                    // Update the plugin array with the correct path
                    $this->plugins[$plugin_type][$plugin_key]['file_path'] = $actual_file_path;
                }
            }
        }
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Plugin installed successfully.', 'textdomain'));
    }
    
    /**
     * Detect the actual plugin file path after installation
     */
    private function detect_actual_plugin_path($expected_slug, $expected_file_path) {
        $plugins_dir = WP_PLUGIN_DIR;
        $expected_folder = dirname($expected_file_path);
        $plugin_file = basename($expected_file_path);
        
        // Check if expected path exists
        if (file_exists($plugins_dir . '/' . $expected_file_path)) {
            return $expected_file_path;
        }
        
        // Look for folders that start with the expected slug
        $dirs = glob($plugins_dir . '/' . $expected_slug . '*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            $folder_name = basename($dir);
            $potential_file_path = $folder_name . '/' . $plugin_file;
            
            if (file_exists($plugins_dir . '/' . $potential_file_path)) {
                return $potential_file_path;
            }
        }
        
        return false;
    }
    
    /**
     * AJAX: Activate plugin
     */
    public function activate_plugin_ajax() {
        check_ajax_referer('theme_setup_wizard_nonce', 'nonce');
        
        if (!current_user_can('activate_plugins')) {
            wp_send_json_error(__('Insufficient permissions.', 'textdomain'));
        }
        
        $plugin_key = sanitize_text_field($_POST['plugin']);
        $plugin_type = sanitize_text_field($_POST['type']);
        
        if (!isset($this->plugins[$plugin_type][$plugin_key])) {
            wp_send_json_error(__('Invalid plugin.', 'textdomain'));
        }
        
        $plugin = $this->plugins[$plugin_type][$plugin_key];
        
        // Get the actual plugin path (handles suffix variations)
        $actual_path = $this->get_actual_plugin_path($plugin['file_path']);
        
        $result = activate_plugin($actual_path);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Plugin activated successfully.', 'textdomain'));
    }
    
    /**
     * AJAX: Get plugin status
     */
    public function get_plugin_status_ajax() {
        check_ajax_referer('theme_setup_wizard_nonce', 'nonce');
        
        $plugin_key = sanitize_text_field($_POST['plugin']);
        $plugin_type = sanitize_text_field($_POST['type']);
        
        if (!isset($this->plugins[$plugin_type][$plugin_key])) {
            wp_send_json_error(__('Invalid plugin.', 'textdomain'));
        }
        
        $plugin = $this->plugins[$plugin_type][$plugin_key];
        $is_installed = $this->is_plugin_installed($plugin['file_path']);
        $is_active = $this->is_plugin_active($plugin['file_path']);
        
        $status = 'not-installed';
        if ($is_active) {
            $status = 'active';
        } elseif ($is_installed) {
            $status = 'installed';
        }
        
        wp_send_json_success(array(
            'status' => $status,
            'is_installed' => $is_installed,
            'is_active' => $is_active
        ));
    }
    
    /**
     * AJAX: Get available demos
     */
    public function get_demos_ajax() {
        check_ajax_referer('theme_setup_wizard_nonce', 'nonce');
        
        wp_send_json_success($this->demos);
    }
    
    /**
     * AJAX: Import demo content
     */
    public function import_demo_content_ajax() {
        // Enable error reporting for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        
        try {
            check_ajax_referer('theme_setup_wizard_nonce', 'nonce');
            
            if (!current_user_can('import')) {
                wp_send_json_error(__('Insufficient permissions.', 'textdomain'));
                return;
            }
            
            $demo_key = sanitize_text_field($_POST['demo']);
            $import_options = isset($_POST['import_options']) ? $_POST['import_options'] : array();
            
            if (!isset($this->demos[$demo_key])) {
                wp_send_json_error(__('Invalid demo.', 'textdomain'));
                return;
            }
            
            $demo = $this->demos[$demo_key];
            
            // Verify demo files exist before attempting import
            if (!file_exists($demo['content_file'])) {
                wp_send_json_error('Content file not found: ' . $demo['content_file']);
                return;
            }
            
            if (isset($demo['customizer_file']) && in_array('customizer', $import_options) && !file_exists($demo['customizer_file'])) {
                wp_send_json_error('Customizer file not found: ' . $demo['customizer_file']);
                return;
            }
            
            // Log the import attempt
            error_log('Polysaas Setup Wizard: Starting import for demo: ' . $demo_key);
            error_log('Import options: ' . print_r($import_options, true));
            
            // For now, let's use the manual import directly since we know it works
            if (in_array('pages', $import_options) || in_array('posts', $import_options) || in_array('media', $import_options)) {
                error_log('Polysaas Setup Wizard: Using direct manual import for content');
                
                // Include the importer installer for manual import
                $installer_file = get_template_directory() . '/inc/setup/class-importer-installer.php';
                if (!file_exists($installer_file)) {
                    wp_send_json_error('Importer installer file not found');
                    return;
                }
                require_once $installer_file;
                
                // Use manual import directly
                $content_result = \Polysaas\Setup\Importer_Installer::manual_xml_import($demo['content_file'], $import_options);
                
                if (is_wp_error($content_result)) {
                    error_log('Polysaas Setup Wizard: Manual import failed: ' . $content_result->get_error_message());
                    wp_send_json_error('Content import failed: ' . $content_result->get_error_message());
                    return;
                }
                
                error_log('Polysaas Setup Wizard: Manual import successful: ' . print_r($content_result, true));
            }
            
            // Import customizer settings if selected
            if (in_array('customizer', $import_options) && isset($demo['customizer_file'])) {
                error_log('Polysaas Setup Wizard: Importing customizer settings');
                
                // Include the demo content importer for customizer
                $importer_file = get_template_directory() . '/inc/setup/class-demo-content-importer.php';
                if (!file_exists($importer_file)) {
                    wp_send_json_error('Demo content importer file not found');
                    return;
                }
                require_once $importer_file;
                
                $importer = new Demo_Content_Importer();
                $customizer_result = $importer->import_customizer_settings($demo['customizer_file']);
                
                if (is_wp_error($customizer_result)) {
                    error_log('Polysaas Setup Wizard: Customizer import failed: ' . $customizer_result->get_error_message());
                    wp_send_json_error('Customizer import failed: ' . $customizer_result->get_error_message());
                    return;
                }
                
                error_log('Polysaas Setup Wizard: Customizer import successful: ' . print_r($customizer_result, true));
            }
            
            // Set up pages
            if (isset($content_result) && $content_result['pages'] > 0) {
                // Set front page if imported
                $front_page = get_page_by_title('Home');
                if ($front_page) {
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $front_page->ID);
                    error_log('Polysaas Setup Wizard: Set Home page as front page');
                }
                
                // Set blog page if imported
                $blog_page = get_page_by_title('Blog');
                if ($blog_page) {
                    update_option('page_for_posts', $blog_page->ID);
                    error_log('Polysaas Setup Wizard: Set Blog page for posts');
                }
            }
            
            // Flush rewrite rules
            flush_rewrite_rules();
            
            // Clear any caches
            if (function_exists('wp_cache_flush')) {
                wp_cache_flush();
            }
            
            // Build success response
            $success_message = 'Demo content imported successfully!';
            $results = array();
            
            if (isset($content_result)) {
                $results['content'] = $content_result;
                $success_message .= ' Imported ' . $content_result['pages'] . ' pages and ' . $content_result['posts'] . ' posts.';
            }
            
            if (isset($customizer_result)) {
                $results['customizer'] = $customizer_result;
                $success_message .= ' Applied theme customizations.';
            }
            
            error_log('Polysaas Setup Wizard: Import completed successfully');
            
            wp_send_json_success(array(
                'message' => $success_message,
                'results' => $results
            ));
            
        } catch (\Exception $e) {
            error_log('Polysaas Setup Wizard: Exception in import_demo_content_ajax: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            wp_send_json_error('Import failed: ' . $e->getMessage());
        } catch (\Throwable $e) {
            error_log('Polysaas Setup Wizard: Fatal error in import_demo_content_ajax: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            wp_send_json_error('Fatal error during import: ' . $e->getMessage());
        }
    }
    
    /**
     * Perform the actual demo import
     */
    private function perform_demo_import($demo, $import_options) {
        try {
            // Check if Demo_Content_Importer class exists
            if (!class_exists('Polysaas\Setup\Demo_Content_Importer')) {
                $importer_file = get_template_directory() . '/inc/setup/class-demo-content-importer.php';
                if (!file_exists($importer_file)) {
                    throw new \Exception('Demo Content Importer file not found: ' . $importer_file);
                }
                require_once $importer_file;
            }
            
            // Check if Importer_Installer class exists
            if (!class_exists('Polysaas\Setup\Importer_Installer')) {
                $installer_file = get_template_directory() . '/inc/setup/class-importer-installer.php';
                if (!file_exists($installer_file)) {
                    throw new \Exception('Importer Installer file not found: ' . $installer_file);
                }
                require_once $installer_file;
            }
            
            // Create importer instance
            $importer = new Demo_Content_Importer();
            
            // Log the import attempt
            error_log('Polysaas Setup Wizard: Created Demo_Content_Importer instance');
            error_log('Demo data: ' . print_r($demo, true));
            
            // Perform the complete import
            $import_result = $importer->complete_demo_import($demo, $import_options);
            
            error_log('Polysaas Setup Wizard: Import result: ' . print_r($import_result, true));
            
            return $import_result;
            
        } catch (\Exception $e) {
            error_log('Polysaas Setup Wizard: Exception in perform_demo_import: ' . $e->getMessage());
            return new \WP_Error('import_exception', $e->getMessage());
        } catch (\Throwable $e) {
            error_log('Polysaas Setup Wizard: Fatal error in perform_demo_import: ' . $e->getMessage());
            return new \WP_Error('import_fatal_error', $e->getMessage());
        }
    }
    
    /**
     * AJAX: Dismiss wizard
     */
    public function dismiss_wizard_ajax() {
        check_ajax_referer('theme_setup_wizard_nonce', 'nonce');
        
        update_option($this->wizard_dismissed_option, true);
        delete_transient('show_theme_setup_wizard');
        
        wp_send_json_success(__('Wizard dismissed.', 'textdomain'));
    }
    
    /**
     * AJAX: Complete wizard
     */
    public function complete_wizard_ajax() {
        check_ajax_referer('theme_setup_wizard_nonce', 'nonce');
        
        update_option($this->wizard_completed_option, true);
        delete_transient('show_theme_setup_wizard');
        
        wp_send_json_success(__('Setup completed successfully!', 'textdomain'));
    }
    
    /**
     * Check if plugin is installed (with dynamic path detection)
     */
    private function is_plugin_installed($plugin_path) {
        $installed_plugins = get_plugins();
        
        // Check exact path first
        if (isset($installed_plugins[$plugin_path])) {
            return true;
        }
        
        // For external plugins, check for variations with suffixes
        $plugin_folder = dirname($plugin_path);
        $plugin_file = basename($plugin_path);
        
        foreach ($installed_plugins as $installed_path => $plugin_data) {
            $installed_folder = dirname($installed_path);
            $installed_file = basename($installed_path);
            
            // Check if this matches our plugin (accounting for suffix)
            if ($installed_file === $plugin_file && 
                (strpos($installed_folder, $plugin_folder) === 0)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if plugin is active (with dynamic path detection)
     */
    private function is_plugin_active($plugin_path) {
        // Check exact path first
        if (is_plugin_active($plugin_path)) {
            return true;
        }
        
        // For external plugins, check for variations with suffixes
        $plugin_folder = dirname($plugin_path);
        $plugin_file = basename($plugin_path);
        
        $installed_plugins = get_plugins();
        foreach ($installed_plugins as $installed_path => $plugin_data) {
            $installed_folder = dirname($installed_path);
            $installed_file = basename($installed_path);
            
            // Check if this matches our plugin (accounting for suffix)
            if ($installed_file === $plugin_file && 
                (strpos($installed_folder, $plugin_folder) === 0)) {
                return is_plugin_active($installed_path);
            }
        }
        
        return false;
    }
    
    /**
     * Get the actual plugin path (for activation)
     */
    private function get_actual_plugin_path($expected_path) {
        $installed_plugins = get_plugins();
        
        // Check exact path first
        if (isset($installed_plugins[$expected_path])) {
            return $expected_path;
        }
        
        // For external plugins, find the actual path
        $plugin_folder = dirname($expected_path);
        $plugin_file = basename($expected_path);
        
        foreach ($installed_plugins as $installed_path => $plugin_data) {
            $installed_folder = dirname($installed_path);
            $installed_file = basename($installed_path);
            
            // Check if this matches our plugin (accounting for suffix)
            if ($installed_file === $plugin_file && 
                (strpos($installed_folder, $plugin_folder) === 0)) {
                return $installed_path;
            }
        }
        
        return $expected_path; // Fallback to original
    }
    
    /**
     * Get all plugins (required + recommended)
     */
    public function get_all_plugins() {
        return $this->plugins;
    }
    
    /**
     * Get all demos
     */
    public function get_all_demos() {
        return $this->demos;
    }
}