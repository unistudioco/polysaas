<?php
namespace Polysaas\Setup;

use Polysaas\Core\Config;
use Polysaas\Core\Functions;

/**
 * Theme Plugin Installation Wizard
 * Add this to your theme's functions.php or create a separate file and include it
 */

class Plugin_Installer {
    
    private $plugins = array();
    private $theme_name;
    
    public function __construct() {
        $this->theme_name = get_template();
        $this->set_plugins();
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_install_plugin', array($this, 'install_plugin_ajax'));
        add_action('wp_ajax_activate_plugin', array($this, 'activate_plugin_ajax'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_notices', array($this, 'admin_notice'));
    }
    
    /**
     * Define required/recommended plugins
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
                'file_path' => 'unistudio-core/unistudio-core.php',
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
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_theme_page(
            'Plugins Manager',
            'Plugins Manager',
            'manage_options',
            'theme-install-plugins',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_scripts($hook) {
        if ($hook !== 'appearance_page_theme-install-plugins') {
            return;
        }
        
        wp_enqueue_script('theme-plugin-installer', get_template_directory_uri() . '/assets/js/admin/plugin-installer.js', array('jquery'), '1.0.0', true);
        wp_enqueue_style('theme-plugin-installer', get_template_directory_uri() . '/assets/css/admin/plugin-installer.css', array(), '1.0.0');
        
        wp_localize_script('theme-plugin-installer', 'pluginInstaller', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('plugin_installer_nonce'),
            'installing' => __('Installing...', 'textdomain'),
            'activating' => __('Activating...', 'textdomain'),
            'installed' => __('Installed', 'textdomain'),
            'activated' => __('Activated', 'textdomain'),
            'error' => __('Error occurred', 'textdomain')
        ));
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p style="margin: 0;"><?php _e('Install and activate the recommended plugins for your theme.', 'textdomain'); ?></p>
            
            <div class="plugin-installer-container">
                <?php foreach ($this->plugins as $plugin_key => $plugin) : ?>
                    <?php
                    $is_installed = $this->is_plugin_installed($plugin['file_path']);
                    $is_active = $this->is_plugin_active($plugin['file_path']);
                    $status_class = $is_active ? 'active' : ($is_installed ? 'installed' : 'not-installed');
                    ?>
                    
                    <div class="plugin-card <?php echo $status_class; ?>" data-plugin="<?php echo esc_attr($plugin_key); ?>">
                        <div class="plugin-card-top">
                            <div class="plugin-name">
                                <div class="plugin-icon-image">
                                <?php if (isset($plugin['icon'])) : ?>
                                    <img src="<?php echo esc_attr($plugin['icon']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>" />
                                <?php else : ?>
                                    <img src="<?php echo esc_attr(get_template_directory_uri() . '/inc/plugins/placeholder.svg'); ?>" alt="<?php echo esc_attr($plugin['name']); ?>" />
                                <?php endif; ?>
                                </div>
                                <h3><?php echo esc_html($plugin['name']); ?></h3>
                                <?php if ($plugin['required']) : ?>
                                    <span class="required"><?php _e('Required', 'textdomain'); ?></span>
                                <?php else : ?>
                                    <span class="recommended"><?php _e('Recommended', 'textdomain'); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="plugin-description">
                                <p><?php echo esc_html($plugin['description']); ?></p>
                            </div>
                        </div>
                        
                        <div class="plugin-card-bottom">
                            <div class="plugin-status">
                                <?php if ($is_active) : ?>
                                    <span class="status-active"><?php _e('Active', 'textdomain'); ?></span>
                                <?php elseif ($is_installed) : ?>
                                    <span class="status-installed"><?php _e('Installed', 'textdomain'); ?></span>
                                <?php else : ?>
                                    <span class="status-not-installed"><?php _e('Not Installed', 'textdomain'); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="plugin-actions">
                                <?php if ($is_active) : ?>
                                    <button class="button button-secondary" disabled><?php _e('Active', 'textdomain'); ?></button>
                                <?php elseif ($is_installed) : ?>
                                    <button class="button button-primary activate-plugin" data-plugin="<?php echo esc_attr($plugin_key); ?>">
                                        <?php _e('Activate', 'textdomain'); ?>
                                    </button>
                                <?php else : ?>
                                    <button class="button button-primary install-plugin" data-plugin="<?php echo esc_attr($plugin_key); ?>">
                                        <?php _e('Install', 'textdomain'); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="plugin-progress" style="display: none;">
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <span class="progress-text"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="bulk-actions">
                <button class="button button-primary" id="install-all-plugins">
                    <?php _e('Install All Required Plugins', 'textdomain'); ?>
                </button>
                <button class="button button-secondary" id="install-recommended-plugins">
                    <?php _e('Install All Recommended Plugins', 'textdomain'); ?>
                </button>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler for plugin installation
     */
    public function install_plugin_ajax() {
        check_ajax_referer('plugin_installer_nonce', 'nonce');
        
        if (!current_user_can('install_plugins')) {
            wp_die(__('You do not have sufficient permissions to install plugins.', 'textdomain'));
        }
        
        $plugin_key = sanitize_text_field($_POST['plugin']);
        
        if (!isset($this->plugins[$plugin_key])) {
            wp_send_json_error(__('Invalid plugin specified.', 'textdomain'));
        }
        
        $plugin = $this->plugins[$plugin_key];
        
        // Include required files
        if (!function_exists('plugins_api')) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }
        if (!class_exists('WP_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        
        if ($plugin['source'] === 'repo') {
            // Install from WordPress.org repository
            $api = plugins_api('plugin_information', array('slug' => $plugin['slug']));
            
            if (is_wp_error($api)) {
                wp_send_json_error($api->get_error_message());
            }
            
            $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($api->download_link);
            
        } else {
            // Install from external source
            $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
            $result = $upgrader->install($plugin['external_url']);
        }
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Plugin installed successfully.', 'textdomain'));
    }
    
    /**
     * AJAX handler for plugin activation
     */
    public function activate_plugin_ajax() {
        check_ajax_referer('plugin_installer_nonce', 'nonce');
        
        if (!current_user_can('activate_plugins')) {
            wp_die(__('You do not have sufficient permissions to activate plugins.', 'textdomain'));
        }
        
        $plugin_key = sanitize_text_field($_POST['plugin']);
        
        if (!isset($this->plugins[$plugin_key])) {
            wp_send_json_error(__('Invalid plugin specified.', 'textdomain'));
        }
        
        $plugin = $this->plugins[$plugin_key];
        $result = activate_plugin($plugin['file_path']);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(__('Plugin activated successfully.', 'textdomain'));
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
    
    /**
     * Show admin notice for required plugins
     */
    public function admin_notice() {
        $screen = get_current_screen();
        
        // Don't show on the plugin installer page
        if ($screen && $screen->id === 'appearance_page_theme-install-plugins') {
            return;
        }
        
        $required_plugins = array();
        foreach ($this->plugins as $plugin_key => $plugin) {
            if ($plugin['required'] && !$this->is_plugin_active($plugin['file_path'])) {
                $required_plugins[] = $plugin['name'];
            }
        }
        
        if (!empty($required_plugins)) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <?php _e('Your theme requires the following plugins to work properly:', 'textdomain'); ?>
                    <strong><?php echo implode(', ', $required_plugins); ?></strong>
                </p>
                <p>
                    <a href="<?php echo admin_url('themes.php?page=theme-install-plugins'); ?>" class="button button-primary">
                        <?php _e('Install Required Plugins', 'textdomain'); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }
}