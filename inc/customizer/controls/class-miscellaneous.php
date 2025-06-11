<?php
namespace Polysaas\Customizer\Controls;

class Miscellaneous extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'miscellaneous';

    /**
     * Register controls
     */
    public function register() {
        $this->add_miscellaneous_fields();
    }

    
    public function add_miscellaneous_fields() {

        $this->add_heading_control([
            'settings'    => $this->get_setting('magic_cursor_heading'),
            'heading'     => __('Magic cursor', $this->get_text_domain()),
            'description' => __('Enable/disable creative cursor effects.', $this->get_text_domain()),
            'divider'     => false,
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('enable_magic_cursor'),
            'label'       => __('Enable Magic Cursor', $this->get_text_domain()),
            'default'     => false,
        ]);

        $this->add_heading_control([
            'settings'    => $this->get_setting('preloader_heading'),
            'heading'     => __('Page Preloader', $this->get_text_domain()),
            'description' => __('Enable/disable preloader and choose between styles.', $this->get_text_domain()),
            'divider'     => 'top',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('enable_preloader'),
            'label'       => __('Enable Preloader', $this->get_text_domain()),
            'default'     => true,
        ]);

        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('preloader_style'),
                'label'       => __('Preloader Style', $this->get_text_domain()),
                'default'     => 'default',
                'choices'     => [
                    'default' => __('Default', $this->get_text_domain()),
                    'square' => __('Square', $this->get_text_domain()),
                    'modern' => __('Modern', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
                'active_callback' => [
                    [
                        'setting' => $this->get_setting('enable_preloader'),
                        'operator' => '==',
                        'value' => true,
                    ]
                ]
            ]
        );

        $this->add_heading_control([
            'settings'    => $this->get_setting('theme_schema_heading'),
            'heading'     => __('Theme Schema', $this->get_text_domain()),
            'description' => __('Enable/disable dark mode and choose your theme\'s default schema.', $this->get_text_domain()),
            'divider'     => 'top',
        ]);


        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('enable_dark_mode'),
            'label'       => __('Enable Dark Mode', $this->get_text_domain()),
            'default'     => false,
        ]);

        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('theme_schema'),
                'label'       => __('Default Schema', $this->get_text_domain()),
                'default'     => 'light',
                'choices'     => [
                    'light' => __('Light', $this->get_text_domain()),
                    'dark' => __('Dark', $this->get_text_domain()),
                    'system' => __('User System', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
                'active_callback' => [
                    [
                        'setting' => $this->get_setting('enable_dark_mode'),
                        'operator' => '==',
                        'value' => true,
                    ]
                ]
            ]
        );

        $this->add_heading_control([
            'settings'    => $this->get_setting('to_top_heading'),
            'heading'     => __('To Top Button', $this->get_text_domain()),
            'description' => __('Enable/disable to top button.', $this->get_text_domain()),
            'divider'     => 'top',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('enable_to_top'),
            'label'       => __('Enable To Top', $this->get_text_domain()),
            'default'     => true,
        ]);

        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('to_top_placement'),
                'label'       => __('To Top Placement', $this->get_text_domain()),
                'default'     => 'right',
                'choices'     => [
                    'right' => __('Right', $this->get_text_domain()),
                    'left' => __('Left', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
                'active_callback' => [
                    [
                        'setting' => $this->get_setting('enable_to_top'),
                        'operator' => '==',
                        'value' => true,
                    ]
                ]
            ]
        );

    }

}