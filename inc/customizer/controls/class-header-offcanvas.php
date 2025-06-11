<?php
namespace Polysaas\Customizer\Controls;

class Header_Offcanvas extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'header_offcanvas';

    /**
     * Register controls
     */
    public function register() {
        $this->add_header_offcanvas_layout();
        $this->add_header_offcanvas_options();
    }

    public function add_header_offcanvas_layout() {

        // Add normal controls if no override
        $this->add_control('Radio_Buttonset', [
            'settings'    => $this->get_setting('header_offcanvas_type'),
            'label'       => __('Off-Canvas Layout', $this->get_text_domain()),
            'default'     => '_default',
            'choices'     => [
                '_default' => esc_html__('Default', $this->get_text_domain()),
                '_gs'  => esc_html__('Elementor', $this->get_text_domain()),
            ],
        ]);

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('header_offcanvas_source'),
            'label'       => __('Select Template', $this->get_text_domain()),
            'default'     => '',
            'transport'   => 'refresh',
            'placeholder' => __('Select a template', $this->get_text_domain()),
            'choices'     => $this->get_global_sections('offcanvas'),
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_offcanvas_type'),
                    'operator' => '===',
                    'value'    => '_gs',
                ],
            ],
        ]);

    }

    public function add_header_offcanvas_options() {

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('header_offcanvas_position'),
            'label'       => __('Select position', $this->get_text_domain()),
            'default'     => 'left',
            'transport'   => 'refresh',
            'choices'     => [
                'left'   => esc_html__( 'Left', $this->get_text_domain() ),
                'right' => esc_html__( 'Right', $this->get_text_domain() ),
            ],
        ]);

        $this->add_inline_control('Number', [
            'settings'    => $this->get_setting('header_offcanvas_z_index'),
            'label'       => __('Off Canvas z-index', $this->get_text_domain()),
            'default'  => 999,
            'choices'  => [
                'min'  => -3,
                'max'  => 9999,
                'step' => 1,
            ],
            'transport'   => 'refresh',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('header_offcanvas_overlay_enable'),
            'label'       => __('Enable Overlay?', $this->get_text_domain()),
            'default'  => false,
            'transport'   => 'refresh',
        ]);

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('header_offcanvas_mode'),
            'label'       => __('Animation Mode', $this->get_text_domain()),
            'default'     => 'slide',
            'choices'     => [
                'slide'   => esc_html__( 'Slide', $this->get_text_domain() ),
                'reveal' => esc_html__( 'Reveal', $this->get_text_domain() ),
                'push'  => esc_html__( 'Push', $this->get_text_domain() ),
                'none'  => esc_html__( 'None', $this->get_text_domain() ),
            ],
            'transport'   => 'refresh',
        ]);
        
    }

}