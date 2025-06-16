<?php
namespace Polysaas\Customizer\Controls;

use Polysaas\Core\Config;
use Polysaas\Core\Theme_Functions;
use Kirki\Compatibility\Init;

class Global_Colors_Fonts extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'global_colors_fonts';

    /**
     * Register controls
     */
    public function register() {
        $this->add_global_colors();
        $this->add_global_fonts();
    }

    /**
     * Add Global Colors
     */
    protected function add_global_colors() {

        $this->add_heading_control( [
            'settings'    => $this->get_setting('global_colors_heading'),
            'heading'     => __('Global Colors', $this->get_text_domain()),
            'tab'         => 'colors',
            'divider'     => false,
        ]);

        // Elementor System Colors
        $this->add_control('ColorElementor', [
            'settings'      => $this->get_setting('primary_color'),
            'label'         => __('Primary Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'color_category' => 'system',
            'default'       => '#8435d8',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => ':root',
                    'property' => '--color-primary',
                ],
                [
                    'element'  => '.text-primary',
                    'property' => 'color',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.bg-primary',
                    'property' => 'background-color',
                ],
                [
                    'element'  => '.border-primary',
                    'property' => 'border-color',
                ],
                [
                    'element'  => '.uc-button, .btn.btn-primary',
                    'property' => '--uc-btn-bg',
                ],
                [
                    'element'  => '.uc-button, .btn.btn-primary',
                    'property' => '--uc-btn-border-color',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'      => $this->get_setting('secondary_color'),
            'label'         => __('Secondary Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'color_category' => 'system',
            'default'       => '#000000',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => ':root',
                    'property' => '--color-secondary',
                ],
                [
                    'element'  => '.text-secondary',
                    'property' => 'color',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.bg-secondary',
                    'property' => 'background-color',
                ],
                [
                    'element'  => '.border-secondary',
                    'property' => 'border-color',
                ],
                [
                    'element'  => '.uc-button, .btn.btn-secondary',
                    'property' => '--uc-btn-bg',
                ],
                [
                    'element'  => '.uc-button, .btn.btn-secondary',
                    'property' => '--uc-btn-border-color',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'      => $this->get_setting('text_color'),
            'label'         => __('Text Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'color_category' => 'system',
            'default'       => '#000000',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => ':root',
                    'property' => '--color-text',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'      => $this->get_setting('accent_color'),
            'label'         => __('Accent Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'color_category' => 'system',
            'default'       => '#b167ff',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => ':root',
                    'property' => '--color-accent',
                ],
            ],
        ]);

        $this->add_heading_control( [
            'settings'    => $this->get_setting('site_colors_heading'),
            'heading'     => __('Site Styling', $this->get_text_domain()),
            'tab'         => 'colors',
            'divider'     => 'top',
        ]);

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('body_bg_color'),
            'label'       => __('Body Background Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => 'body',
                    'property' => 'background-color',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('body_text_color'),
            'label'       => __('Body Text Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'       => '#000000',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => 'body',
                    'property' => 'color',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('headings_text_color'),
            'label'       => __('Headings Text Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'       => '#000000',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => '--uc-heading-color',
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'color',
                ],
            ],
        ]);

        $this->add_pro_control('Divider', [
                'settings' => $this->get_setting('link_divider_before'),
                'tab'         => 'colors',
                'choices'  => [
                    'color' => '#bebebe',
                ],
            ]
        );

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('link_color'),
            'label'       => __('Link Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'       => '#8435d8',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => 'a, a.uc-link',
                    'property' => 'color',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('link_hover_color'),
            'label'       => __('Link Hover Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'       => '#b167ff',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => 'a:hover, a.uc-link:hover',
                    'property' => 'color',
                ],
            ],
        ]);

        $this->add_pro_control('Divider', [
                'settings' => $this->get_setting('button_divider_before'),
                'tab'         => 'colors',
                'choices'  => [
                    'color' => '#bebebe',
                ],
            ]
        );

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('button_background_color'),
            'label'       => __('Button Background Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'       => '',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.uc-button, .btn.btn-primary',
                    'property' => '--uc-btn-bg',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.uc-button, .btn.btn-primary',
                    'property' => '--uc-btn-border-color',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.uc-button, .btn.btn-primary',
                    'property' => '--uc-btn-active-bg',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.uc-button, .btn.btn-primary',
                    'property' => '--uc-btn-active-border-color',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.wp-element-button',
                    'property' => 'background-color',
                ],
                [
                    'element'  => '.wp-element-button',
                    'property' => 'border-color',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('button_text_color'),
            'label'       => __('Button Text Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'     => '',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => '.uc-button, .btn.btn-primary',
                    'property' => 'color',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.wp-element-button',
                    'property' => 'color',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('button_background_color_hover'),
            'label'       => __('Button Background Hover Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'       => '',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.uc-button:hover, .btn.btn-primary:hover',
                    'property' => '--uc-btn-hover-bg',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.uc-button:hover, .btn.btn-primary:hover',
                    'property' => '--uc-btn-hover-border-color',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.wp-element-button:hover',
                    'property' => 'background-color',
                ],
                [
                    'element'  => '.wp-element-button:hover',
                    'property' => 'border-color',
                ],
            ],
        ]);

        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('button_text_color_hover'),
            'label'       => __('Button Text Hover Color', $this->get_text_domain()),
            'tab'         => 'colors',
            'default'     => '',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => '.uc-button:hover, .btn.btn-primary:hover',
                    'property' => 'color',
                    'value_pattern' => '$ !important',
                ],
                [
                    'element'  => '.wp-element-button:hover',
                    'property' => 'color',
                ],
            ],
        ]);
        
    }

    /**
     * Add Global Fonts
     */
    protected function add_global_fonts() {

        $this->add_heading_control( [
            'settings'    => $this->get_setting('global_fonts_heading'),
            'heading'     => __('Global Fonts', $this->get_text_domain()),
            'tab'         => 'fonts',
            'divider'     => false,
        ]);

        // Primary Font
        $this->add_control('Typography', [
            'settings'    => $this->get_setting('primary_font'),
            'label'     => __('Primary Font', $this->get_text_domain()),
            'tab'       => 'fonts',
            'default'     => [
                'font-family'    => '"Sunsive", "Inter", "IBM Plex Sans", sans-serif',
                'variant'        => '',
            ],
            'output'      => [
                [
                    'element'   => ':root',
                    'choice'   => 'font-family',
                    'property'  => '--uc-font-primary',
                ],
            ],
        ]);

        // Secondary Font
        $this->add_control('Typography', [
            'settings'    => $this->get_setting('secondary_font'),
            'label'     => __('Secondary Font', $this->get_text_domain()),
            'tab'       => 'fonts',
            'default'     => [
                'font-family'    => '"Sunsive", "Inter", "IBM Plex Sans", sans-serif',
                'variant'        => '',
            ],
            'output'     => [
                [
                    'element'   => ':root',
                    'choice'   => 'font-family',
                    'property'  => '--uc-font-secondary',
                ],
            ],
        ]);

        $this->add_heading_control( [
            'settings'    => $this->get_setting('global_fonts_body_heading'),
            'heading'     => __('Body', $this->get_text_domain()),
            'tab'         => 'fonts',
            'divider'     => 'top',
        ]);

        // Body Text Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('body_text_typography'),
            'label'       => __('Body Base', $this->get_text_domain()),
            'tab'       => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-primary-font-family)',
                    'font_size'      => '16',
                    'font_weight'    => '400',
                    'font_style'     => 'normal',
                    'line_height'    => '1.5',
                    'letter_spacing' => '0',
                    'text_transform' => 'none',
                ],
            ]),
            'transport' => 'auto',
            'output' => [
                // Desktop settings for body
                [
                    'element'  => 'body',
                    'property' => 'font-family',
                    'choice'   => 'font_family',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'body',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'body',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'body',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'body',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'body',
                    'property' => 'text-transform',
                    'choice'   => 'text_transform',
                    'context'  => ['desktop']
                ],
                [
                    'element'       => 'body',
                    'property'      => 'font-style',
                    'choice'        => 'font_style',
                    'value_pattern' => '$',
                    'context'       => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'     => 'body',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'     => 'body',
                    'property'    => 'line-height',
                    'choice'      => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'     => 'body',
                    'property'    => 'letter-spacing',
                    'choice'      => 'letter_spacing',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'     => 'body',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile']
                ],
                [
                    'element'     => 'body',
                    'property'    => 'line-height',
                    'choice'      => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile']
                ],
                [
                    'element'     => 'body',
                    'property'    => 'letter-spacing',
                    'choice'      => 'letter_spacing',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile']
                ],
            ],
        ]);

        $this->add_heading_control( [
            'settings'    => $this->get_setting('global_fonts_heading_heading'),
            'heading'     => __('Heading', $this->get_text_domain()),
            'tab'         => 'fonts',
            'divider'     => 'top',
        ]);

        // Heading Text Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('heading_text_typography'),
            'label'       => __('Heading Base', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-secondary-font-family)',
                    'font_size'      => '16',
                    'font_weight'    => '700',
                    'font_style'     => 'normal',
                    'line_height'    => '1.1',
                    'letter_spacing' => '-0.8',
                    'text_transform' => 'none',
                ]
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'font-family',
                    'choice'   => 'font_family',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'     => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'     => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('heading_h1_typography'),
            'label'       => __('Heading H1', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_size'      => '48',
                ],
                'tablet' => [
                    'font_size'      => '40',
                ],
                'mobile' => [
                    'font_size'      => '36',
                ],
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => 'h1',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h1',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h1',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h1',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h1',
                    'property' => 'text-transform',
                    'choice'   => 'text_transform',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'     => 'h1',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => 'h1',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => 'h1',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'     => 'h1',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => 'h1',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => 'h1',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('heading_h2_typography'),
            'label'       => __('Heading H2', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_size'      => '36',
                ],
                'tablet' => [
                    'font_size'      => '32',
                ],
                'mobile' => [
                    'font_size'      => '28',
                ],
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => 'h2',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h2',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h2',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h2',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h2',
                    'property' => 'text-transform',
                    'choice'   => 'text_transform',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'     => 'h2',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => 'h2',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => 'h2',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'     => 'h2',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => 'h2',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => 'h2',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('heading_h3_typography'),
            'label'       => __('Heading H3', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_size'      => '32',
                ],
                'tablet' => [
                    'font_size'      => '28',
                ],
                'mobile' => [
                    'font_size'      => '24',
                ],
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => 'h3',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h3',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h3',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h3',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h3',
                    'property' => 'text-transform',
                    'choice'   => 'text_transform',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'     => 'h3',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => 'h3',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => 'h3',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'     => 'h3',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => 'h3',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => 'h3',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('heading_h4_typography'),
            'label'       => __('Heading H4', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_size'      => '28',
                ],
                'tablet' => [
                    'font_size'      => '24',
                ],
                'mobile' => [
                    'font_size'      => '20',
                ],
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => 'h4',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h4',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h4',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h4',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h4',
                    'property' => 'text-transform',
                    'choice'   => 'text_transform',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'     => 'h4',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => 'h4',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => 'h4',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'     => 'h4',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => 'h4',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => 'h4',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('heading_h5_typography'),
            'label'       => __('Heading H5', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_size'      => '24',
                ],
                'tablet' => [
                    'font_size'      => '20',
                ],
                'mobile' => [
                    'font_size'      => '18',
                ],
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => 'h5',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h5',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h5',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h5',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h5',
                    'property' => 'text-transform',
                    'choice'   => 'text_transform',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'     => 'h5',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => 'h5',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => 'h5',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'     => 'h5',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => 'h5',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => 'h5',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('heading_h6_typography'),
            'label'       => __('Heading H6', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_size'      => '18',
                ],
                'tablet' => [
                    'font_size'      => '17',
                ],
                'mobile' => [
                    'font_size'      => '16',
                ],
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => 'h6',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h6',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h6',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h6',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'h6',
                    'property' => 'text-transform',
                    'choice'   => 'text_transform',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'     => 'h6',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => 'h6',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => 'h6',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'     => 'h6',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => 'h6',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => 'h6',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_heading_control( [
            'settings'    => $this->get_setting('global_fonts_button_heading'),
            'heading'     => __('Button', $this->get_text_domain()),
            'tab'         => 'fonts',
            'divider'     => 'top',
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('buttons_typography'),
            'label'       => __('Button Text', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-secondary-font-family)',
                    'font_size'      => '16',
                    'font_weight'    => '600',
                    'font_style'     => 'normal',
                    'line_height'    => '',
                    'letter_spacing' => '',
                    'text_transform' => 'none',
                ]
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'font-family',
                    'choice'   => 'font_family',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => 'button, .uc-button, .btn, input[type="button"], input[type="submit"]',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_heading_control( [
            'settings'    => $this->get_setting('global_fonts_menu_heading'),
            'heading'     => __('Menu', $this->get_text_domain()),
            'tab'         => 'fonts',
            'divider'     => 'top',
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('menu_base_typography'),
            'label'       => __('Menu Base', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-secondary-font-family)',
                    'font_size'      => '16',
                    'font_weight'    => '400',
                    'font_style'     => 'normal',
                    'line_height'    => '',
                    'letter_spacing' => '',
                    'text_transform' => 'none',
                ]
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => '.uc-navbar',
                    'property' => 'font-family',
                    'choice'   => 'font_family',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'  => '.uc-navbar',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => '.uc-navbar',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => '.uc-navbar',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'  => '.uc-navbar',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => '.uc-navbar',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => '.uc-navbar',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('menu_typography'),
            'label'       => __('Nav Item', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'inherit',
                    'font_size'      => '16',
                    'font_weight'    => '600',
                    'font_style'     => 'normal',
                    'line_height'    => '1',
                    'letter_spacing' => '0',
                    'text_transform' => 'none',
                ]
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'font-family',
                    'choice'   => 'font_family',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => '.uc-navbar .menu-item > a',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);

        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('submenu_typography'),
            'label'       => __('Submenu Nav Item', $this->get_text_domain()),
            'tab'         => 'fonts',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'inherit',
                    'font_size'      => '14',
                    'font_weight'    => '400',
                    'font_style'     => 'normal',
                    'line_height'    => '1',
                    'letter_spacing' => '0',
                    'text_transform' => 'none',
                ]
            ]),
            'output'      => [
                // Desktop settings
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'font-family',
                    'choice'   => 'font_family',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                
                // Tablet settings
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'  => ['tablet']
                ],
                
                // Mobile settings
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile'],
                ],
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
                [
                    'element'  => '.uc-navbar .uc-nav .menu-item > a',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'  => ['mobile']
                ],
            ],
        ]);
    }
}