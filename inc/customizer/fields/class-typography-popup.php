<?php
/**
 * Typography with Popup field
 */
namespace Kirki\Field;

use Kirki\Field;

/**
 * Typography field with popup and responsive options
 */
class TypographyPopup extends Field {
    /**
     * The field type.
     *
     * @access public
     * @var string
     */
    public $type = 'polysaas-typography-popup';
    
    /**
     * The control class-name.
     *
     * @access protected
     * @var string
     */
    protected $control_class = '\Kirki\Control\TypographyPopup';
    
    /**
     * Whether we should register the control class for JS-templating or not.
     *
     * @access protected
     * @var bool
     */
    protected $control_has_js_template = true;
    
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
            
            // Add our output argument to control
            if (isset($this->args['output'])) {
                $args['output'] = $this->args['output'];
            }
        }
        return $args;
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
            
            // Set the sanitize_callback if none is defined.
            if (!isset($args['sanitize_callback']) || !$args['sanitize_callback']) {
                $args['sanitize_callback'] = [$this, 'sanitize'];
            }
        }
        return $args;
    }
    
    /**
     * Sanitizes typography controls
     *
     * @param string $value The value to be sanitized.
     * @return string
     */
    public function sanitize($value) {
        if (is_string($value)) {
            // Make sure the value is valid JSON.
            if (is_string($value) && is_array(json_decode($value, true))) {
                return $value;
            }
            return '{}';
        }
        
        // If we got this far, the value is not a string, probably an empty array.
        return '{}';
    }
}