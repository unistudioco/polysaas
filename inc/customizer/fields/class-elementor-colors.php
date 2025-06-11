<?php
/**
 * Elementor Colors Field for Kirki
 *
 * @package Polysaas
 */

namespace Kirki\Field;

use Kirki\Field;

/**
 * Field overrides.
 */
class ElementorColors extends Field {

    /**
     * The field type.
     *
     * @access public
     * @var string
     */
    public $type = 'polysaas-elementor-colors';

    /**
     * The control class-name.
     *
     * @access protected
     * @var string
     */
    protected $control_class = '\Kirki\Control\ElementorColors';

    /**
     * The color category.
     *
     * @access protected
     * @var string
     */
    protected $color_category = 'system';

    /**
     * Whether we should register the control class for JS-templating or not.
     *
     * @access protected
     * @var bool
     */
    protected $control_has_js_template = true;

    /**
     * Additional logic for the field.
     *
     * @since 1.0
     * @access protected
     *
     * @param array $args The field arguments.
     */
    protected function init($args) {
        if (isset($args['color_category'])) {
            $this->color_category = $args['color_category'];
        }
    }

    /**
     * Filter arguments before creating the setting.
     *
     * @access public
     * @param array                $args         The field arguments.
     * @param WP_Customize_Manager $wp_customize The customizer instance.
     * @return array
     */
    public function filter_setting_args($args, $wp_customize) {
        if ($args['settings'] === $this->args['settings']) {
            $args = parent::filter_setting_args($args, $wp_customize);
        }
        return $args;
    }

    /**
     * Filter arguments before creating the control.
     *
     * @access public
     * @param array                $args         The field arguments.
     * @param WP_Customize_Manager $wp_customize The customizer instance.
     * @return array
     */
    public function filter_control_args($args, $wp_customize) {
        if ($args['settings'] === $this->args['settings']) {
            $args = parent::filter_control_args($args, $wp_customize);
            
            // Add color_category to control
            if (isset($this->color_category)) {
                $args['color_category'] = $this->color_category;
            }
        }
        return $args;
    }
}