<?php
namespace Polysaas\Customizer\Controls;

class Header extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'header_general';

    /**
     * Register controls
     */
    public function register() {
        $this->add_header_layout_fields();
        $this->add_header_sticky_fields();
    }

    public function add_header_layout_fields() {

        // Add normal controls if no override
        $this->add_control('Radio_Buttonset', [
            'settings'    => $this->get_setting('header_layout_type'),
            'label'       => __('Select header layout', $this->get_text_domain()),
            'default'     => '_default',
            'choices'     => [
                '_default' => esc_html__('Theme', $this->get_text_domain()),
                '_gs'  => esc_html__('Elementor', $this->get_text_domain()),
            ],
        ]);

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('header_layout_source'),
            'label'       => __('Select Header', $this->get_text_domain()),
            'default'     => '',
            'transport'   => 'refresh',
            'placeholder' => __('Select a header', $this->get_text_domain()),
            'choices'     => $this->get_global_sections('header'),
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '===',
                    'value'    => '_gs',
                ],
            ],
        ]);

    }

    public function add_header_sticky_fields() {

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('header_position'),
            'label'       => __('Select position', $this->get_text_domain()),
            'default'     => '_none',
            'transport'   => 'refresh',
            'choices'     => [
                '_none'   => esc_html__( 'Relative', $this->get_text_domain() ),
                '_absolute' => esc_html__( 'Over content', $this->get_text_domain() ),
            ],
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
            ],
        ]);

        $this->add_inline_control('Number', [
            'settings'    => $this->get_setting('header_z_index'),
            'label'       => __('Header z-index', $this->get_text_domain()),
            'default'  => 999,
            'choices'  => [
                'min'  => -3,
                'max'  => 9999,
                'step' => 1,
            ],
            'transport'   => 'refresh',
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
            ],
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('header_sticky_enable'),
            'label'       => __('Enable sticky effect?', $this->get_text_domain()),
            'default'  => false,
            'transport'   => 'refresh',
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
            ],
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('header_sticky_show_on_up'),
            'label'       => __('Slide in on scroll up?', $this->get_text_domain()),
            'default'  => false,
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
                [
                    'setting'  => $this->get_setting('header_sticky_enable'),
                    'operator' => '===',
                    'value'    => true,
                ],
            ],
            'transport'   => 'refresh',
        ]);

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('header_sticky_start'),
            'label'       => __('Start positon', $this->get_text_domain()),
            'default'     => '_top',
            'choices'     => [
                '_top'   => esc_html__( 'Top', $this->get_text_domain() ),
                '_screen' => esc_html__( 'After First Screen', $this->get_text_domain() ),
                '_custom'  => esc_html__( 'Custom', $this->get_text_domain() ),
            ],
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
                [
                    'setting'  => $this->get_setting('header_sticky_enable'),
                    'operator' => '===',
                    'value'    => true,
                ],
            ],
            'transport'   => 'refresh',
        ]);

        $this->add_inline_control('Number', [
            'settings'    => $this->get_setting('header_sticky_custom_start'),
            'label'       => __('Define start position', $this->get_text_domain()),
            'default'  => 100,
            'choices'  => [
                'min'  => 0,
                'max'  => 1000,
                'step' => 8,
            ],
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
                [
                    'setting'  => $this->get_setting('header_sticky_enable'),
                    'operator' => '===',
                    'value'    => true,
                ],
                [
                    'setting'  => $this->get_setting('header_sticky_start'),
                    'operator' => '===',
                    'value'    => '_custom',
                ],
            ],
            'transport'   => 'refresh',
        ]);

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('header_sticky_animation'),
            'label'       => __('Animations', $this->get_text_domain()),
            'default'     => '_slide',
            'choices'     => [
                '_none'   => esc_html__( 'None', $this->get_text_domain() ),
                '_slide'   => esc_html__( 'Slide', $this->get_text_domain() ),
                '_fade'   => esc_html__( 'Fade', $this->get_text_domain() ),
            ],
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
                [
                    'setting'  => $this->get_setting('header_sticky_enable'),
                    'operator' => '===',
                    'value'    => true,
                ],
            ],
            'transport'   => 'refresh',
        ]);

        $this->add_inline_control('Number', [
            'settings'    => $this->get_setting('header_sticky_animation_duration'),
            'label'       => __('Animation duration (ms)', $this->get_text_domain()),
            'default'  => 200,
            'choices'  => [
                'min'  => 50,
                'max'  => 1000,
                'step' => 8,
            ],
            'active_callback' => [
                [
                    'setting'  => $this->get_setting('header_layout_type'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
                [
                    'setting'  => $this->get_setting('header_sticky_enable'),
                    'operator' => '===',
                    'value'    => true,
                ],
                [
                    'setting'  => $this->get_setting('header_sticky_animation'),
                    'operator' => '!==',
                    'value'    => '_none',
                ],
            ],
            'transport'   => 'refresh',
        ]);
        
    }

}