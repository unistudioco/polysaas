<?php
namespace Polysaas\Customizer\Integrations;

use Polysaas\Core\Config;

/**
 * Kirki Elementor Integration
 */
class Kirki_Elementor {
    /**
     * Constructor
     */
    public function __construct() {
        // Register control type with Kirki
        add_filter('kirki_control_types', [$this, 'register_control_type']);
        
        // Enqueue assets for the control
        add_action('customize_controls_enqueue_scripts', [$this, 'enqueue_control_assets']);
        
        // Add helper function to get Elementor color values
        $this->add_elementor_color_helper();
    }
    
    /**
     * Register the control type with Kirki
     */
    public function register_control_type($controls) {
        $controls['polysaas-elementor-colors'] = '\Polysaas\Customizer\Controls\Elementor_Colors_Control';
        return $controls;
    }
    
    /**
     * Enqueue control assets
     */
    public function enqueue_control_assets() {
        wp_enqueue_style(
            Config::prefix('kirki-elementor-colors', 'id'),
            get_template_directory_uri() . '/assets/css/admin/kirki-elementor-colors.css',
            [],
            Config::get('version')
        );
        
        wp_enqueue_script(
            Config::prefix('kirki-elementor-colors', 'id'),
            get_template_directory_uri() . '/assets/js/admin/kirki-elementor-colors.js',
            ['jquery', 'customize-controls'],
            Config::get('version'),
            true
        );
    }
    
    /**
     * Add helper function to get Elementor color values
     */
    private function add_elementor_color_helper() {
        if (!function_exists('get_elementor_global_color')) {
            /**
             * Get the actual color value from Elementor global colors using ID
             *
             * @param string $color_id The color ID from Elementor
             * @return string The hex color value or empty string
             */
            function get_elementor_global_color($color_id) {
                if (empty($color_id) || !class_exists('\Elementor\Plugin')) {
                    return '';
                }
                
                $kit_id = \Elementor\Plugin::$instance->kits_manager->get_active_id();
                if (!$kit_id) {
                    return '';
                }
                
                $kit = \Elementor\Plugin::$instance->documents->get($kit_id);
                if (!$kit) {
                    return '';
                }
                
                $meta = $kit->get_meta('_elementor_page_settings');
                
                // Check system colors
                if (isset($meta['system_colors']) && is_array($meta['system_colors'])) {
                    foreach ($meta['system_colors'] as $color) {
                        if ($color['_id'] === $color_id) {
                            return $color['color'];
                        }
                    }
                }
                
                // Check custom colors
                if (isset($meta['custom_colors']) && is_array($meta['custom_colors'])) {
                    foreach ($meta['custom_colors'] as $color) {
                        if ($color['_id'] === $color_id) {
                            return $color['color'];
                        }
                    }
                }
                
                return '';
            }
        }
    }
}