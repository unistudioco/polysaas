<?php
namespace Polysaas\Setup;

use Polysaas\Core\Config;
use Polysaas\Core\Functions;

/**
 * Theme Activation Modal with Plugin Installer
 * Add this to your theme's functions.php
 */

class Theme_Activation_Modal {
    
    private $plugins = array();
    private $theme_name;
    private $modal_dismissed_option = 'theme_activation_modal_dismissed';
    
    public function __construct() {
        $this->theme_name = get_template();
        $this->set_plugins();
        
        // Hook into theme activation
        add_action('after_switch_theme', array($this, 'theme_activated'));
        
        // Admin hooks
        add_action('admin_enqueue_scripts', array($this, 'enqueue_modal_scripts'));
        add_action('admin_footer', array($this, 'render_modal'));
        
        // AJAX handlers
        add_action('wp_ajax_install_plugin_modal', array($this, 'install_plugin_ajax'));
        add_action('wp_ajax_activate_plugin_modal', array($this, 'activate_plugin_ajax'));
        add_action('wp_ajax_dismiss_activation_modal', array($this, 'dismiss_modal_ajax'));
        add_action('wp_ajax_get_plugin_status', array($this, 'get_plugin_status_ajax'));
    }
    
    /**
     * Define required plugins
     */
    private function set_plugins() {
        
        $this->plugins = array(
            'elementor' => array(
                'icon' => get_template_directory_uri() . '/inc/plugins/elementor.gif',
                'name' => 'Elementor',
                'slug' => 'elementor',
                'source' => 'repo', // 'repo' for WordPress.org, 'external' for external source
                'file_path' => 'elementor/elementor.php',
                'required' => true,
                'description' => 'Page builder for creating beautiful layouts.'
            ),
            'unistudio-core' => array(
                'icon' => get_template_directory_uri() . '/inc/plugins/unistudio-core.svg',
                'name' => 'UniStudio Core',
                'slug' => 'unistudio-core',
                'source' => 'external',
                'file_path' => 'unistudio-core/unicore.php',
                'external_url' => get_template_directory_uri() . '/inc/plugins/unistudio-core.zip',
                'required' => true,
                'description' => 'Custom Premium functionalities.'
            ),
            'acf' => array(
                'icon' => get_template_directory_uri() . '/inc/plugins/acf.svg',
                'name' => 'Advanced Custom Fields',
                'slug' => 'acf',
                'source' => 'external',
                'file_path' => 'advanced-custom-fields/acf.php',
                'external_url' => get_template_directory_uri() . '/inc/plugins/acf.zip',
                'required' => true,
                'description' => 'Custom Fields functionalities.'
            ),
            'kirki' => array(
                'icon' => get_template_directory_uri() . '/inc/plugins/kirki.jpg',
                'name' => 'Kirki',
                'slug' => 'kirki',
                'source' => 'repo',
                'file_path' => 'kirki/kirki.php',
                'required' => true,
                'description' => 'Custom UI Fields for WordPress Customizer.'
            ),
            'contact-form-7' => array(
                'icon' => get_template_directory_uri() . '/inc/plugins/contact-form-7.svg',
                'name' => 'Contact Form 7',
                'slug' => 'contact-form-7',
                'source' => 'repo',
                'file_path' => 'contact-form-7/wp-contact-form-7.php',
                'required' => false,
                'description' => 'Simple contact form plugin.'
            ),
            'woocommerce' => array(
                'icon' => get_template_directory_uri() . '/inc/plugins/woocommerce.svg',
                'name' => 'WooCommerce',
                'slug' => 'woocommerce',
                'source' => 'repo',
                'file_path' => 'woocommerce/woocommerce.php',
                'required' => false,
                'description' => 'Complete eCommerce solution for WordPress.'
            ),
            'elementor-animejs-addon' => array(
                'icon' => get_template_directory_uri() . '/inc/plugins/elementor-animejs-addon.gif',
                'name' => 'Elementor AnimeJS Addon',
                'slug' => 'elementor-animejs-addon',
                'source' => 'external',
                'file_path' => 'elementor-animejs-addon/index.php',
                'external_url' => get_template_directory_uri() . '/inc/plugins/elementor-animejs-addon.zip',
                'required' => false,
                'description' => 'Advanced Animations via AnimeJS Library for Elementor.'
            ),
            'wpml' => array(
                'icon' => get_template_directory_uri() . '/inc/plugins/wpml.png',
                'name' => 'WPML',
                'slug' => 'woocommerce-multilingual',
                'source' => 'repo',
                'file_path' => 'woocommerce-multilingual/wpml-woocommerce.php',
                'required' => false,
                'description' => 'WooCommerce Multilingual & Multicurrency with WPML.'
            )
        );
    }
    
    /**
     * Called when theme is activated
     */
    public function theme_activated() {
        // Set a transient to show the modal on next admin page load
        set_transient('show_theme_activation_modal', true, 60);
        
        // Reset dismissed status for new theme activation
        delete_option($this->modal_dismissed_option);
    }
    
    /**
     * Enqueue modal scripts and styles
     */
    public function enqueue_modal_scripts() {
        // Only show modal if not dismissed and transient exists
        if (get_transient('show_theme_activation_modal') && !get_option($this->modal_dismissed_option)) {
            wp_enqueue_script('theme-activation-modal', get_template_directory_uri() . '/assets/js/admin/theme-activation-modal.js', array('jquery'), '1.0.0', true);
            wp_enqueue_style('theme-activation-modal', get_template_directory_uri() . '/assets/css/admin/theme-activation-modal.css', array(), '1.0.0');
            
            wp_localize_script('theme-activation-modal', 'themeActivationModal', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('theme_activation_modal_nonce'),
                'theme_name' => wp_get_theme()->get('Name'),
                'installing' => __('Installing...', 'textdomain'),
                'activating' => __('Activating...', 'textdomain'),
                'installed' => __('Installed', 'textdomain'),
                'activated' => __('Activated', 'textdomain'),
                'error' => __('Error occurred', 'textdomain'),
                'success' => __('Success!', 'textdomain'),
                'complete_message' => __('All required plugins have been installed and activated successfully!', 'textdomain')
            ));
        }
    }
    
    /**
     * Render the modal HTML
     */
    public function render_modal() {
        // Only show modal if not dismissed and transient exists
        if (!get_transient('show_theme_activation_modal') || get_option($this->modal_dismissed_option)) {
            return;
        }
        
        // Get required plugins only
        $required_plugins = array_filter($this->plugins, function($plugin) {
            return $plugin['required'] === true;
        });
        
        ?>
        <div id="theme-activation-modal" class="theme-modal-overlay">
            <div class="theme-modal-container">
                <div class="theme-modal-header">
                    <h2><?php printf(__('Welcome to %s!', 'textdomain'), wp_get_theme()->get('Name')); ?></h2>
                    <p><?php _e('To get started, we need to install some required plugins that will enhance your website functionality.', 'textdomain'); ?></p>
                </div>
                
                <div class="theme-modal-content">
                    <div class="required-plugins-section">
                        <h3><?php _e('Required Plugins', 'textdomain'); ?></h3>
                        <div class="plugins-grid">
                            <?php foreach ($required_plugins as $plugin_key => $plugin) : ?>
                                <div class="plugin-item" data-plugin="<?php echo esc_attr($plugin_key); ?>">
                                    <div class="plugin-icon">
                                        <span class="dashicons <?php echo esc_attr($plugin['icon']); ?>"></span>
                                    </div>
                                    <div class="plugin-info">
                                        <h4><?php echo esc_html($plugin['name']); ?></h4>
                                        <p><?php echo esc_html($plugin['description']); ?></p>
                                    </div>
                                    <div class="plugin-status">
                                        <span class="status-indicator checking">
                                            <span class="dashicons dashicons-update-alt"></span>
                                            <?php _e('Checking...', 'textdomain'); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="installation-progress" style="display: none;">
                        <div class="progress-header">
                            <h3><?php _e('Installing Plugins...', 'textdomain'); ?></h3>
                            <div class="progress-counter">
                                <span class="current">0</span> / <span class="total"><?php echo count($required_plugins); ?></span>
                            </div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <div class="current-plugin-status">
                            <span class="installing-text"></span>
                        </div>
                    </div>
                    
                    <div class="completion-message" style="display: none;">
                        <div class="success-icon">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </div>
                        <h3><?php _e('All Set!', 'textdomain'); ?></h3>
                        <p><?php _e('All required plugins have been installed and activated successfully. You can now start building your website!', 'textdomain'); ?></p>
                    </div>
                </div>
                
                <div class="theme-modal-footer">
                    <div class="modal-actions">
                        <button type="button" class="button button-secondary skip-installation">
                            <?php _e('Skip for Now', 'textdomain'); ?>
                        </button>
                        <button type="button" class="button button-primary start-installation">
                            <?php _e('Install Required Plugins', 'textdomain'); ?>
                        </button>
                        <button type="button" class="button button-primary get-started" style="display: none;">
                            <?php _e('Get Started', 'textdomain'); ?>
                        </button>
                    </div>
                    
                    <div class="modal-footer-note">
                        <p><?php _e('You can always install these plugins later from Appearance → Install Plugins', 'textdomain'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Install plugin
     */
    public function install_plugin_ajax() {
        check_ajax_referer('theme_activation_modal_nonce', 'nonce');
        
        if (!current_user_can('install_plugins')) {
            wp_send_json_error(__('Insufficient permissions.', 'textdomain'));
        }
        
        $plugin_key = sanitize_text_field($_POST['plugin']);
        
        if (!isset($this->plugins[$plugin_key])) {
            wp_send_json_error(__('Invalid plugin.', 'textdomain'));
        }
        
        $plugin = $this->plugins[$plugin_key];
        
        // Include required files
        if (!function_exists('plugins_api')) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }
        if (!class_exists('WP_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        
        // Install plugin
        if ($plugin['source'] === 'repo') {
            $api = plugins_api('plugin_information', array('slug' => $plugin['slug']));
            
            if (is_wp_error($api)) {
                wp_send_json_error($api->get_error_message());
            }
            
            $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($api->download_link);
        }
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Plugin installed successfully.', 'textdomain'));
    }
    
    /**
     * AJAX: Activate plugin
     */
    public function activate_plugin_ajax() {
        check_ajax_referer('theme_activation_modal_nonce', 'nonce');
        
        if (!current_user_can('activate_plugins')) {
            wp_send_json_error(__('Insufficient permissions.', 'textdomain'));
        }
        
        $plugin_key = sanitize_text_field($_POST['plugin']);
        
        if (!isset($this->plugins[$plugin_key])) {
            wp_send_json_error(__('Invalid plugin.', 'textdomain'));
        }
        
        $plugin = $this->plugins[$plugin_key];
        $result = activate_plugin($plugin['file_path']);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Plugin activated successfully.', 'textdomain'));
    }
    
    /**
     * AJAX: Dismiss modal
     */
    public function dismiss_modal_ajax() {
        check_ajax_referer('theme_activation_modal_nonce', 'nonce');
        
        update_option($this->modal_dismissed_option, true);
        delete_transient('show_theme_activation_modal');
        
        wp_send_json_success(__('Modal dismissed.', 'textdomain'));
    }
    
    /**
     * AJAX: Get plugin status
     */
    public function get_plugin_status_ajax() {
        check_ajax_referer('theme_activation_modal_nonce', 'nonce');
        
        $plugin_key = sanitize_text_field($_POST['plugin']);
        
        if (!isset($this->plugins[$plugin_key])) {
            wp_send_json_error(__('Invalid plugin.', 'textdomain'));
        }
        
        $plugin = $this->plugins[$plugin_key];
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
     * Check if plugin is installed
     */
    private function is_plugin_installed($plugin_path) {
        $installed_plugins = get_plugins();
        return isset($installed_plugins[$plugin_path]);
    }
    
    /**
     * Check if plugin is active
     */
    private function is_plugin_active($plugin_path) {
        return is_plugin_active($plugin_path);
    }
}