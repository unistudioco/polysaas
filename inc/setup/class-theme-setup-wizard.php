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
    private $debug_log_file;
    
    public function __construct() {
        $this->theme_name = get_template();
        $this->set_plugins();
        $this->set_demos();
        
        // Test logging immediately
        $this->debug_log('Theme Setup Wizard initialized', array(
            'theme_name' => $this->theme_name,
            'time' => current_time('mysql')
        ));
        
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
     * Debug logging function
     */
    private function debug_log($message, $data = null) {
        try {
            $timestamp = current_time('mysql');
            $log_message = "[{$timestamp}] {$message}";
            
            if ($data !== null) {
                if (is_array($data) || is_object($data)) {
                    $log_message .= "\nData: " . print_r($data, true);
                } else {
                    $log_message .= "\nData: {$data}";
                }
            }
            
            $log_message .= "\n----------------------------------------\n";
            
            // Log to WordPress debug log
            error_log($log_message);
            
            // Ensure uploads directory exists and is writable
            $upload_dir = wp_upload_dir();
            if (!empty($upload_dir['error'])) {
                error_log('Upload directory error: ' . $upload_dir['error']);
                return;
            }
            
            // Create logs directory if it doesn't exist
            $logs_dir = $upload_dir['basedir'] . '/logs';
            if (!file_exists($logs_dir)) {
                wp_mkdir_p($logs_dir);
            }
            
            // Set debug log path
            $this->debug_log_file = $logs_dir . '/theme_setup_debug.log';
            
            // Ensure the log file exists and is writable
            if (!file_exists($this->debug_log_file)) {
                touch($this->debug_log_file);
                chmod($this->debug_log_file, 0644);
            }
            
            // Write to our custom debug file
            if (is_writable($this->debug_log_file)) {
                file_put_contents($this->debug_log_file, $log_message, FILE_APPEND);
            } else {
                error_log('Debug log file is not writable: ' . $this->debug_log_file);
            }
            
        } catch (\Exception $e) {
            error_log('Error in debug_log: ' . $e->getMessage());
        }
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
                    'file_path' => 'unistudio-core/unistudio-core.php',
                    'external_url' => get_template_directory_uri() . '/inc/plugins/unistudio-core.zip',
                    'description' => 'Core functionalities and features, Elementor Widgets, Blocks, etc.',
                    'required' => true
                ),
                'acf' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/acf.svg',
                    'name' => 'Advanced Custom Fields',
                    'slug' => 'acf',
                    'source' => 'external',
                    'file_path' => 'advanced-custom-fields/acf.php',
                    'external_url' => get_template_directory_uri() . '/inc/plugins/acf.zip',
                    'description' => 'Custom Fields functionalities for Post Type Metaboxes.',
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
                ),
                'elementor-animejs-addon' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/elementor-animejs-addon.gif',
                    'name' => 'Elementor AnimeJS Addon',
                    'slug' => 'elementor-animejs-addon',
                    'source' => 'external',
                    'file_path' => 'elementor-animejs-addon/index.php',
                    'external_url' => get_template_directory_uri() . '/inc/plugins/elementor-animejs-addon.zip',
                    'description' => 'Advanced Animations via AnimeJS Library for Elementor.',
                    'required' => true
                ),
            ),
            'recommended' => array(
                'contact-form-7' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/contact-form-7.svg',
                    'name' => 'Contact Form 7',
                    'slug' => 'contact-form-7',
                    'source' => 'repo',
                    'file_path' => 'contact-form-7/wp-contact-form-7.php',
                    'description' => 'Create advanced contact and newsletter forms.',
                    'required' => false
                ),
                'woocommerce' => array(
                    'icon' => get_template_directory_uri() . '/inc/plugins/woocommerce.svg',
                    'name' => 'WooCommerce',
                    'slug' => 'woocommerce',
                    'source' => 'repo',
                    'file_path' => 'woocommerce/woocommerce.php',
                    'description' => 'Complete eCommerce solution for your business.',
                    'required' => false
                ),
                // 'wpml' => array(
                //     'icon' => get_template_directory_uri() . '/inc/plugins/wpml.png',
                //     'name' => 'WPML',
                //     'slug' => 'woocommerce-multilingual',
                //     'source' => 'repo',
                //     'file_path' => 'woocommerce-multilingual/wpml-woocommerce.php',
                //     'description' => 'WooCommerce Multilingual & Multicurrency with WPML.',
                //     'required' => false
                // )
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
        try {
            check_ajax_referer('theme_setup_wizard_nonce', 'nonce');
            
            if (!current_user_can('import')) {
                wp_send_json_error(__('Insufficient permissions.', 'textdomain'));
                return;
            }
            
            $demo_key = sanitize_text_field($_POST['demo']);
            $import_options = isset($_POST['import_options']) ? $_POST['import_options'] : array();
            
            $this->debug_log("Starting demo content import", array(
                'demo' => $demo_key,
                'options' => $import_options
            ));
            
            if (!isset($this->demos[$demo_key])) {
                $this->debug_log("Invalid demo key: {$demo_key}");
                wp_send_json_error(__('Invalid demo.', 'textdomain'));
                return;
            }
            
            $demo = $this->demos[$demo_key];
            
            // Verify demo files exist
            if (!file_exists($demo['content_file'])) {
                $this->debug_log("Content file not found", array('file' => $demo['content_file']));
                wp_send_json_error('Content file not found: ' . $demo['content_file']);
                return;
            }
            
            // Import content using WordPress Importer
            $result = Importer_Installer::manual_xml_import($demo['content_file'], $import_options);
            
            if (is_wp_error($result)) {
                $this->debug_log("Import failed", array(
                    'error' => $result->get_error_message()
                ));
                wp_send_json_error($result->get_error_message());
                return;
            }
            
            // Import customizer settings if selected
            if (in_array('customizer', $import_options) && isset($demo['customizer_file'])) {
                if (file_exists($demo['customizer_file'])) {
                    $this->import_customizer_settings($demo['customizer_file']);
                }
            }
            
            $this->debug_log("Import completed successfully");
            wp_send_json_success('Import completed successfully');
            
        } catch (\Exception $e) {
            $this->debug_log("Exception during import", array(
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ));
            wp_send_json_error('Import failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Import customizer settings
     */
    private function import_customizer_settings($file) {
        if (!file_exists($file)) {
            return false;
            }
            
        $this->debug_log("Importing customizer settings", array('file' => $file));
        
        $raw = file_get_contents($file);
        $data = @unserialize($raw);
        
        if ($data && is_array($data)) {
            foreach ($data as $key => $value) {
                set_theme_mod($key, $value);
            }
            $this->debug_log("Customizer settings imported successfully");
            return true;
        }
        
        $this->debug_log("Failed to import customizer settings");
        return false;
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