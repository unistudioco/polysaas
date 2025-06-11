<?php
/**
 * Color with Elementor globals field
 */
namespace Kirki\Field;

use Kirki\Field;

/**
 * Color field with Elementor integration
 */
class ColorElementor extends Field {
    /**
     * The field type.
     *
     * @access public
     * @var string
     */
    public $type = 'polysaas-color-elementor';
    
    /**
     * The control class-name.
     *
     * @access protected
     * @var string
     */
    protected $control_class = '\Kirki\Control\ColorElementor';
    
    /**
     * Whether alpha is enabled.
     *
     * @access protected
     * @var bool
     */
    protected $alpha = true;
    
    /**
     * Whether palette is enabled.
     *
     * @access protected
     * @var bool
     */
    protected $palette = true;
    
    /**
     * Mode
     *
     * @access protected
     * @var string
     */
    protected $mode = 'full';
    
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
            
            if (isset($this->alpha)) {
                $args['choices']['alpha'] = $this->alpha;
            }
            
            if (isset($this->mode)) {
                $args['mode'] = $this->mode;
            }
            
            if (isset($this->palette)) {
                $args['palette'] = $this->palette;
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
            
            // If no sanitize callback defined, use our own
            if (!isset($args['sanitize_callback']) || !$args['sanitize_callback']) {
                $args['sanitize_callback'] = 'sanitize_text_field';
            }
        }
        
        return $args;
    }
}