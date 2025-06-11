<?php
namespace Polysaas\Customizer\Controls;

class Footer extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'footer_general';

    /**
     * Register controls
     */
    public function register() {
        $this->add_footer_general_fields();
    }

    public function add_footer_general_fields() {

        $this->add_control('Radio_Buttonset', [
            'settings'    => $this->get_setting('footer_layout_type'),
            'label'       => __('Select footer layout', $this->get_text_domain()),
            'default'     => '_default',
            'choices'     => [
                '_default' => esc_html__( 'Theme', $this->get_text_domain() ),
                '_gs'  => esc_html__( 'Elementor', $this->get_text_domain() ),
            ],
        ]);

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('footer_layout_source'),
            'label'       => __('Select Footer', $this->get_text_domain()),
            'default'     => '',
            'transport'   => 'refresh',
            'placeholder' => __('Select a footer', $this->get_text_domain()),
            'choices'     => $this->get_global_sections('footer'),
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('footer_layout_type'),
                    'operator' => '===',
                    'value'    => '_gs',
                ],
            ],
        ]);
    }
    
}