<?php
/**
 * Customizer Control: Elementor Colors
 *
 * @package Polysaas
 */

namespace Kirki\Control;

use Kirki\Control\Base;
use Polysaas\Core\Config;

if (!class_exists('WP_Customize_Control')) {
    return;
}

/**
 * Elementor Global Colors control.
 */
class ElementorColors extends Base {
    
    /**
     * The control type.
     *
     * @access public
     * @var string
     */
    public $type = 'polysaas-elementor-colors';

    /**
     * The color category.
     *
     * @access public
     * @var string
     */
    public $color_category = null;

    /**
     * The version.
     *
     * @access public
     * @var string
     */
    public static $control_ver = '1.0';

    /**
     * Enqueue control related scripts/styles.
     *
     * @access public
     */
    public function enqueue() {
        parent::enqueue();
    }

    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @access public
     */
    public function to_json() {
        parent::to_json();
        $this->json['elementorColors'] = $this->get_elementor_colors($this->color_category);
    }

    /**
     * An Underscore (JS) template for this control's content (but not its container).
     *
     * Class variables for this control class are available in the `data` JS object;
     * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
     *
     * @see WP_Customize_Control::print_template()
     *
     * @access protected
     */
    protected function content_template() {
        ?>
        <# if (data.label) { #>
            <span class="customize-control-title">{{{ data.label }}}</span>
        <# } #>
        
        <# if (data.description) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
        <# } #>
        
        <div class="elementor-global-colors-wrapper">
            <# if (data.elementorColors && data.elementorColors.length) { #>
                <# _.each(data.elementorColors, function(color) { #>
                    <div class="elementor-global-color-item" 
                        data-id="{{ color.id }}"
                        data-value="{{ color.value }}">
                        <span class="color-swatch" style="background-color: {{ color.value }}"></span>
                        <span class="color-title">{{ color.title }}</span>
                        <span class="color-value">{{ color.value }}</span>
                    </div>
                <# }); #>
            <# } else { #>
                <div class="customize-control-kirki-notice"><p class="notice notice-warning"><?php esc_html_e('No Elementor global colors found. Please install and activate Elementor first.', Config::get('text_domain')); ?></p></div>
            <# } #>
        </div>
        
        <!-- Store the actual color value in the main setting -->
        <input type="hidden" id="{{ data.id }}" name="{{ data.id }}" value="{{ data.value }}" {{{ data.link }}} />
        <?php
    }

    /**
     * Get Elementor global colors.
     * 
     * @param string $category 'system' or 'custom'
     * @return array
     */
    private function get_elementor_colors($category = 'system') {
        $colors = [];
        
        if (!class_exists('\Elementor\Plugin')) {
            return $colors;
        }
        
        $kit_id = \Elementor\Plugin::$instance->kits_manager->get_active_id();
        if (!$kit_id) {
            return $colors;
        }
        
        $kit = \Elementor\Plugin::$instance->documents->get($kit_id);
        if (!$kit) {
            return $colors;
        }
        
        $meta = $kit->get_meta('_elementor_page_settings');
    
        // Return both system and custom colors if category is null or 'all'
        if ($category === null || $category === 'all') {
            // Add system colors
            if (isset($meta['system_colors']) && is_array($meta['system_colors'])) {
                foreach ($meta['system_colors'] as $color) {
                    $colors[] = [
                        'id' => $color['_id'],
                        'title' => $color['title'],
                        'value' => $color['color'],
                        'type' => 'system'
                    ];
                }
            }
            
            // Add custom colors
            if (isset($meta['custom_colors']) && is_array($meta['custom_colors'])) {
                foreach ($meta['custom_colors'] as $color) {
                    $colors[] = [
                        'id' => $color['_id'],
                        'title' => $color['title'],
                        'value' => $color['color'],
                        'type' => 'custom'
                    ];
                }
            }
        } else {
            // Return only the requested category
            $category_key = ($category === 'custom') ? 'custom_colors' : 'system_colors';
            
            if (isset($meta[$category_key]) && is_array($meta[$category_key])) {
                foreach ($meta[$category_key] as $color) {
                    $colors[] = [
                        'id' => $color['_id'],
                        'title' => $color['title'],
                        'value' => $color['color'],
                        'type' => ($category === 'custom') ? 'custom' : 'system'
                    ];
                }
            }
        }
        
        return $colors;
    }
}