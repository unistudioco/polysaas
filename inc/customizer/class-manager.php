<?php
namespace Polysaas\Customizer;

use Polysaas\Core\Config;
use Polysaas\Core\Hooks;

/**
 * Kirki Customizer Manager
 *
 * @package Polysaas
 */
class Manager {
    /**
     * Control instances
     */
    private $controls = [];

    /**
     * Register default hooks and actions for WordPress
     */
    public function register() {
        // Check if Kirki exists
        if (!class_exists('\Kirki')) {
            return;
        }

        // Load our custom control and field classes
        $this->load_custom_classes();
        
        // Register our field type with Kirki
        add_filter('kirki_control_types', [$this, 'register_kirki_control_types']);

        add_action('customize_controls_enqueue_scripts', [$this, 'add_elementor_colors_to_customizer']);
                
        // Add helper function
        $this->add_elementor_color_helper();

        // Initiate the Kirki Customizer setup
        add_action('init', [$this, 'setup']);
    }

    /**
     * Setup Kirki
     */
    public function setup() {
        $this->add_panels();
        $this->add_sections();
        $this->register_controls();

        // Allow others to register customizer elements using our hooks
        Hooks::do_action('after_customizer_elements_registered');
    }

    /**
     * Add Kirki panels
     */
    private function add_panels() {
        $panels = [
            new Panels\General(),
            new Panels\Header(),
            new Panels\Footer(),
        ];
    }

    /**
     * Add Kirki Sections
     */
    private function add_sections() {
        $sections = [
            new Sections\Global_Sections(),
            new Sections\Global_Colors_Fonts(),
            new Sections\Header_General(),
            new Sections\Header_Offcanvas(),
            new Sections\Footer_General(),
            new Sections\Footer_Copyrights(),
            new Sections\Blog_Posts(),
            new Sections\Blog_Archive(),
            new Sections\Miscellaneous(),
            new Sections\Woocommerce(),
            new Sections\Import_Export(),
        ];
    }

    /**
     * Initialize and register controls
     */
    private function register_controls() {
        $this->controls = [
            'global_colors_fonts'   => new Controls\Global_Colors_Fonts(),
            'global_sections'       => new Controls\Global_Sections(),
            'blog_archive'          => new Controls\Blog_Archive(),
            'blog_posts'            => new Controls\Blog_Posts(),
            'header'                => new Controls\Header(),
            'header_offcanvas'      => new Controls\Header_Offcanvas(),
            'logo'                  => new Controls\Logo(),
            'footer'                => new Controls\Footer(),
            'copyrights'            => new Controls\Copyrights(),
            'miscellaneous'         => new Controls\Miscellaneous(),
            'woocommerce'           => new Controls\Woocommerce(),
            'import_export'         => new Controls\Import_Export(),
        ];

        // Register all controls
        foreach ($this->controls as $control) {
            $control->register();
        }
    }
    
    /**
     * Load custom control and field classes
     */
    private function load_custom_classes() {
        // Include our custom classes - these are in the normal Polysaas namespace
        require_once THEME_INC . '/customizer/controls/class-color-elementor.php';
        require_once THEME_INC . '/customizer/fields/class-color-elementor.php';
        require_once THEME_INC . '/customizer/controls/class-typography-popup.php';
        require_once THEME_INC . '/customizer/fields/class-typography-popup.php';
    }
    
    /**
     * Register control types with Kirki
     */
    public function register_kirki_control_types($controls) {
        $controls['polysaas-color-elementor'] = '\Kirki\Control\ColorElementor';
        $controls['polysaas-typography-popup'] = '\Kirki\Control\TypographyPopup';
        return $controls;
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

    /**
     * Add Elementor global colors to customizer
     */
    public function add_elementor_colors_to_customizer() {
        $css = $this->generate_elementor_colors_css();
        
        if (!empty($css)) {
            wp_add_inline_style('customize-controls', $css);
        }
    }
    
    /**
     * Generate CSS for Elementor global colors
     */
    private function generate_elementor_colors_css() {
        $colors = $this->get_elementor_colors();
        
        if (empty($colors)) {
            return '';
        }
        
        $css = ":root {\n";
        $prefix = Config::prefix('color', 'class');
        
        foreach ($colors as $color) {
            $title_key = sanitize_title($color['title']);

            // Add comment with color title for easier identification
            $css .= sprintf("  /* %s (%s) */\n", $color['title'], $color['type']);
            
            // Keep the Elementor variable names for compatibility
            $css .= sprintf("  --e-global-color-%s: %s;\n", $title_key, $color['value']);
            
            // Also add with sanitized title for more readable variables
            $css .= sprintf("  --%s-%s: var(--e-global-color-%s);\n", $prefix, $title_key, $title_key);
        }
        
        $css .= "}\n";
        
        return $css;
    }
    
    /**
     * Get Elementor global colors
     * 
     * @return array Array of color data
     */
    public function get_elementor_colors() {
        $colors = [];
        
        if (class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance->kits_manager)) {
            $kits_manager = \Elementor\Plugin::$instance->kits_manager;
            
            // Get system colors
            $system_colors = $kits_manager->get_current_settings('system_colors');
            if (!empty($system_colors)) {
                foreach ($system_colors as $color) {
                    $colors[] = [
                        'id' => $color['_id'],
                        'title' => $color['title'],
                        'value' => $color['color'],
                        'var' => sprintf('var(--e-global-color-%s)', $color['_id']),
                        'type' => 'system'
                    ];
                }
            }
            
            // Get custom colors
            $custom_colors = $kits_manager->get_current_settings('custom_colors');
            if (!empty($custom_colors)) {
                foreach ($custom_colors as $color) {
                    $colors[] = [
                        'id' => $color['_id'],
                        'title' => $color['title'],
                        'value' => $color['color'],
                        'var' => sprintf('var(--e-global-color-%s)', $color['_id']),
                        'type' => 'custom'
                    ];
                }
            }
        }
        
        return $colors;
    }

}