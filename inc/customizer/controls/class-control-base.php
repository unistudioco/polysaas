<?php
namespace Polysaas\Customizer\Controls;

use Polysaas\Core\Config;
use Polysaas\Customizer\Traits\Typography;

abstract class Control_Base {
    use Typography;

    /**
     * Panel ID
     */
    protected $panel = '';

    /**
     * Section ID
     */
    protected $section = '';

    /**
     * Controls priority
     */
    protected $priority = 10;

    /**
     * Register control
     */
    abstract public function register();

    /**
     * Get panel ID
     */
    protected function get_panel() {
        return !empty($this->panel) ? Config::prefix($this->panel) : '';
    }

    /**
     * Get section ID
     */
    protected function get_section() {
        return Config::prefix($this->section);
    }

    /**
     * Get setting ID
     */
    protected function get_setting($id) {
        return Config::prefix($id);
    }

    /**
     * Get text domain
     */
    protected function get_text_domain() {
        return Config::get('text_domain');
    }

    /**
     * Get Kirki control classes
     */
    protected function get_kirki_classes($type) {
        // Map of special Kirki control types to their actual classes
        $type_map = [
            'Custom'            => 'custom',
            'ElementorColors'   => 'elementor-colors',
            'ColorElementor'    => 'color-elementor',
            'TypographyPopup'   => 'typography-popup',
            'Typography'        => 'typography',
            'Color'             => 'react-colorful',
            'Number'            => 'generic',
            'Text'              => 'generic',
            'Textarea'          => 'generic',
            'Checkbox_Switch'   => 'switch',
            'Select'            => 'react-select',
            'Radio_Buttonset'   => 'radio-buttonset',
            'Radio_Image'       => 'radio-image',
            'Repeater'          => 'repeater customize-control-repeater',
            'InputSlider'       => 'input-slider',
            // Add other special cases here
        ];

        // Get the correct type class
        $type_class = isset($type_map[$type]) ? $type_map[$type] : strtolower($type);

        return [
            'customize-control',
            'customize-control-kirki',
            'customize-control-kirki-' . $type_class
        ];
    }

    /**
     * Section tabs
     */
    protected $tabs = [];

    /**
     * Current tab
     */
    protected $current_tab = '';

    /**
     * Set section tabs
     */
    protected function set_tabs($tabs) {
        $this->tabs = $tabs;
    }

    /**
     * Set current tab
     */
    protected function set_current_tab($tab) {
        $this->current_tab = $tab;
    }

    /**
     * Get tab argument
     */
    protected function get_tab_arg() {
        return !empty($this->current_tab) ? ['tab' => $this->current_tab] : [];
    }

    /**
     * Start tab group
     */
    protected function start_tab($tab) {
        $this->set_current_tab($tab);
    }

    /**
     * End tab group
     */
    protected function end_tab() {
        $this->set_current_tab('');
    }

    /**
     * Add control
     * 
     * @param string $type The control type (e.g., 'Color', 'Typography')
     * @param array  $args The control arguments
     * @param bool   $pro  Whether this is a pro field
     */
    protected function add_control($type, $args, $pro = false) {
        // Add panel if set
        if (!empty($this->panel)) {
            $args['panel'] = $this->get_panel();
        }

        // Add section if set
        if (!empty($this->section)) {
            $args['section'] = $this->get_section();
        }

        // Add priority if set
        if (!empty($this->priority)) {
            $args['priority'] = $this->priority;
        }

        // Add tab if provided in args
        if (isset($args['tab'])) {
            $this->current_tab = $args['tab'];
        }

        // Get base Kirki classes
        $classes = $this->get_kirki_classes($type);

        // Add classes to wrapper_attrs if not already set
        if (!isset($args['wrapper_attrs'])) {
            $args['wrapper_attrs'] = ['class' => implode(' ', $classes)];
        } else {
            $existing_class = isset($args['wrapper_attrs']['class']) ? $args['wrapper_attrs']['class'] : '';
            $args['wrapper_attrs']['class'] = implode(' ', array_merge($classes, explode(' ', $existing_class)));
        }

        // Create class name
        $class_name = $pro ? '\Kirki\Pro\Field\\' . $type : '\Kirki\Field\\' . $type;

        if (class_exists($class_name)) {
            new $class_name($args);
        }
    }

    /**
     * Add inline control
     */
    protected function add_inline_control($type, $args, $class = '', $pro = false) {

        // Get base Kirki classes
        $classes = $this->get_kirki_classes($type);
        
        // Add control-inline and any custom classes
        $classes[] = 'control-inline';
        if (!empty($class)) {
            $classes[] = $class;
        }

        // Set wrapper attributes
        $args['wrapper_attrs'] = [
            'class' => implode(' ', $classes)
        ];

        $this->add_control($type, $args, $pro);
    }

    /**
     * Add a pro control
     */
    protected function add_pro_control($type, $args) {
        $this->add_control($type, $args, true);
    }

    /**
     * Add a pro inline control
     */
    protected function add_pro_inline_control($type, $args, $class = '') {
        $this->add_inline_control($type, $args, $class, true);
    }

    /**
     * Add Elementor colors control
     * 
     * @param array $args Control arguments
     */
    protected function add_elementor_colors_control($args) {
        // Add panel if set
        if (!empty($this->panel)) {
            $args['panel'] = $this->get_panel();
        }

        // Add section if set
        if (!empty($this->section)) {
            $args['section'] = $this->get_section();
        }

        // Add priority
        if (!isset($args['priority'])) {
            $args['priority'] = $this->priority++;
        }

        // Create field instance
        new \Kirki\Field\ElementorColors($args);
    }
    
    /**
     * Add color elementor control
     * 
     * @param array $args Control arguments
     */
    protected function add_color_elementor_control($args) {
        // Add panel if set
        if (!empty($this->panel)) {
            $args['panel'] = $this->get_panel();
        }

        // Add section if set
        if (!empty($this->section)) {
            $args['section'] = $this->get_section();
        }

        // Add priority
        if (!isset($args['priority'])) {
            $args['priority'] = $this->priority++;
        }

        // Set default alpha
        if (!isset($args['choices'])) {
            $args['choices'] = ['alpha' => true];
        } elseif (!isset($args['choices']['alpha'])) {
            $args['choices']['alpha'] = true;
        }

        // Create field instance
        new \Kirki\Field\ColorElementor($args);
    }

    public function get_global_sections($type) {
        $sections = [];
        
        $args = [
            'post_type' => 'uc_global_sections',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => 'uc_section_type',
                    'field' => 'slug',
                    'terms' => $type,
                ],
            ],
        ];

        $query = new \WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $sections[get_the_ID()] = get_the_title();
            }
        }
        
        wp_reset_postdata();
        
        return $sections;
    }

    /**
     * Get the WP_Customize_Manager instance
     */
    protected function get_customizer() {
        global $wp_customize;
        
        if(is_customize_preview()) {
            if (!$wp_customize instanceof \WP_Customize_Manager) {
                trigger_error('WP_Customize_Manager not found. Are you outside the customizer context?');
                return null;
            }
        }
        
        return $wp_customize;
    }

    protected function add_notice_control($args) {
        if (!isset($args['settings'])) {
            return;
        }
    
        $notice_type = isset($args['notice_type']) ? $args['notice_type'] : 'info';
        $message = isset($args['message']) ? $args['message'] : '';
        
        // Create control args
        $control_args = [
            'settings' => $args['settings'],
            'label'    => isset($args['label']) ? $args['label'] : '',
            'section'  => $this->section,
            'default'  => sprintf(
                '<div class="customize-control-kirki-notice">
                    <div class="notice inline notice-%s">
                        <p>%s</p>
                    </div>
                </div>',
                esc_attr($notice_type),
                esc_html($message)
            )
        ];

        // Add tab if provided
        if (isset($args['tab'])) {
            $control_args['tab'] = $args['tab'];
        }

        $this->add_control('Custom', $control_args);
    }

    protected function add_heading_control($args) {
        if (!isset($args['settings'])) {
            return;
        }

        $heading = isset($args['heading']) ? '<span class="heading">' . $args['heading'] . '</span>' : '';
        $desc = isset($args['description']) ? '<span class="description">' . $args['description'] . '</span>' : '';
        $divider = isset($args['divider']) ? $args['divider'] : 'top';

        // Create control args
        $control_args = [
            'settings'    => $args['settings'],
            'section'     => $this->section,
            'default'     => '<span class="customize-control-kirki-heading ' . esc_attr($divider) . '">' . $heading . $desc . '</span></span>',
        ];

        // Add tab if provided
        if (isset($args['tab'])) {
            $control_args['tab'] = $args['tab'];
        }

        $this->add_control('Custom', $control_args);
    }

    // Helper method to check for ACF override
    protected function check_for_acf_override($field_name) {
        // Get current preview URL
        $preview_url = $this->get_preview_url();
        if (!$preview_url) {
            return false;
        }

        // Get post ID from URL
        $post_id = url_to_postid($preview_url);
        if (!$post_id) {
            return false;
        }

        // Check for ACF override
        if (function_exists('get_field')) {
            $override = get_field($field_name, $post_id);
            return !empty($override);
        }

        return false;
    }

    protected function get_preview_url() {
        $wp_customize = $this->get_customizer();
        if (!$wp_customize) {
            return false;
        }

        return $wp_customize->get_preview_url();
    }
}