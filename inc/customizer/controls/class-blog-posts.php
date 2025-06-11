<?php
namespace Polysaas\Customizer\Controls;

use Polysaas\Core\Config;

class Blog_Posts extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'blog_posts';

    /**
     * Register controls
     */
    public function register() {
        $this->add_blog_posts_general();
        $this->add_blog_posts_design();
    }

    
    public function add_blog_posts_general() {

        $this->add_heading_control( [
            'settings'    => $this->get_setting('blog_post_heading_1'),
            'heading'     => __('Layout Options', $this->get_text_domain()),
            'divider'     => false,
            'tab'         => 'general',
        ]);

        // Post Single Layout
        $this->add_inline_control('Radio_Image', [
                'settings'    => $this->get_setting('blog_post_layout'),
                'label'       => __('Post Single Layout', $this->get_text_domain()),
                'default'     => 'layout-1',
                'choices'     => [
                    'layout-1' => get_template_directory_uri() . '/assets/images/customizer/post-layout-1.png',
                    'layout-2' => get_template_directory_uri() . '/assets/images/customizer/post-layout-2.png',
                    'layout-3' => get_template_directory_uri() . '/assets/images/customizer/post-layout-3.png',
                    'layout-4' => get_template_directory_uri() . '/assets/images/customizer/post-layout-4.png'
                ],
                'transport'   => 'refresh',
                'tab'         => 'general',
            ]
        );

        // Post Single Sidebar Placement
        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('blog_post_sidebar'),
                'label'       => __('Sidebar Placement', $this->get_text_domain()),
                'default'     => 'right',
                'choices'     => [
                    'left'  => __('Left', $this->get_text_domain()),
                    'right' => __('Right', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
                'tab'         => 'general',
                'active_callback'  => [
                    [
                        'setting'  => Config::prefix('blog_post_layout'),
                        'operator' => 'in',
                        'value'    => ['layout-1', 'layout-2'],
                    ],
                ],
            ]
        );

        // Post Single Container
        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('blog_post_container'),
                'label'       => __('Container Width', $this->get_text_domain()),
                'default'     => 'medium',
                'choices'     => [
                    ''  => __('Default', $this->get_text_domain()),
                    'small' => __('Small', $this->get_text_domain()),
                    'medium' => __('Medium', $this->get_text_domain()),
                    'large' => __('Large', $this->get_text_domain()),
                    'expand' => __('Expand', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
                'tab'         => 'general',
                'active_callback'  => [
                    [
                        'setting'  => Config::prefix('blog_post_layout'),
                        'operator' => 'in',
                        'value'    => ['layout-3', 'layout-4'],
                    ],
                ],
            ]
        );

        $this->add_heading_control( [
            'settings'    => $this->get_setting('blog_post_heading_2'),
            'heading'     => __('Hide or Display', $this->get_text_domain()),
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_post_breadcrumbs'),
            'label'       => __('Breadcrumbs', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_post_featured_image'),
            'label'       => __('Featured Image', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('blog_post_featured_image_ratio'),
                'label'       => __('Featured Image Ratio', $this->get_text_domain()),
                'tab'         => 'general',
                'default'     => 'ratio-16x9',
                'choices'     => [
                    ''   => __('Original', $this->get_text_domain()),
                    'ratio-1x1'   => __('1:1 (Square)', $this->get_text_domain()),
                    'ratio-3x2'   => __('3:2 (Common)', $this->get_text_domain()),
                    'ratio-4x3'   => __('4:3 (Landscape)', $this->get_text_domain()),
                    'ratio-16x9'   => __('16:9 (TV)', $this->get_text_domain()),
                    'ratio-21x9'   => __('21:9 (Panoramic)', $this->get_text_domain()),
                    'ratio-2x3'   => __('2:3 (Portrait)', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
                'active_callback' => [
                    [
                        'setting' => Config::prefix('blog_post_featured_image'),
                        'operator' => '==',
                        'value' => true,
                    ]
                ]
            ]
        );

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_post_footer'),
            'label'       => __('Post Footer', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_post_comments_form'),
            'label'       => __('Comments Form', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_post_related_posts'),
            'label'       => __('Related Posts', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_post_navigation'),
            'label'       => __('Navigation', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings' => $this->get_setting('blog_post_meta'),
            'label' => __('Show Meta Post', $this->get_text_domain()),
            'default' => true,
            'tab'         => 'general',
        ]);

        $this->add_control('Sortable', [
            'settings' => $this->get_setting('blog_post_meta_order'),
            'label' => __('Meta Post Order  Visibility', $this->get_text_domain()),
            'default' => ['author', 'date', 'categories', 'comments'],
            'tab'         => 'general',
            'choices' => [
                'author' => __('Author', $this->get_text_domain()),
                'date' => __('Date', $this->get_text_domain()),
                'categories' => __('Categories', $this->get_text_domain()),
                'comments' => __('Comments', $this->get_text_domain()),
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_post_meta'),
                    'operator' => '==',
                    'value' => true,
                ]
            ]
        ]);

    }
    
    public function add_blog_posts_design() {
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_post_design_heading_1'),
            'heading'     => __('Typography', $this->get_text_domain()),
            'divider'     => false,
            'tab'         => 'design',
        ]);
        
        // Post Title Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('blog_post_title_typography'),
            'label'       => __('Post Title', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-secondary-font-family)',
                    'font_size'      => '32',
                    'font_weight'    => '700',
                    'font_style'     => 'normal',
                    'line_height'    => '1.2',
                    'letter_spacing' => '-0.5',
                    'text_transform' => 'none',
                ],
                'tablet' => [
                    'font_size'      => '28',
                ],
                'mobile' => [
                    'font_size'      => '24',
                ],
            ]),
            'output'      => [
                [
                    'element'  => '.single-post .entry-title',
                    'property' => 'font-family',
                    'choice'   => 'font_family',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-title',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-title',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-title',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-title',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-title',
                    'property' => 'text-transform',
                    'choice'   => 'text_transform',
                    'context'  => ['desktop']
                ],
                // Tablet settings
                [
                    'element'     => '.single-post .entry-title',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                // Mobile settings
                [
                    'element'     => '.single-post .entry-title',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile']
                ],
            ],
        ]);
        
        // Post Content Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('blog_post_content_typography'),
            'label'       => __('Post Content', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-primary-font-family)',
                    'font_size'      => '16',
                    'font_weight'    => '400',
                    'line_height'    => '1.6',
                    'letter_spacing' => '0',
                ],
                'tablet' => [
                    'font_size'      => '15',
                ],
                'mobile' => [
                    'font_size'      => '14',
                ],
            ]),
            'output'      => [
                [
                    'element'  => '.single-post .entry-content',
                    'property' => 'font-family',
                    'choice'   => 'font_family',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-content',
                    'property' => 'font-size',
                    'choice'   => 'font_size',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-content',
                    'property' => 'font-weight',
                    'choice'   => 'font_weight',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-content',
                    'property' => 'line-height',
                    'choice'   => 'line_height',
                    'context'  => ['desktop']
                ],
                [
                    'element'  => '.single-post .entry-content',
                    'property' => 'letter-spacing',
                    'choice'   => 'letter_spacing',
                    'units'    => 'px',
                    'context'  => ['desktop']
                ],
                // Tablet settings
                [
                    'element'     => '.single-post .entry-content',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 991px)',
                    'context'     => ['tablet']
                ],
                // Mobile settings
                [
                    'element'     => '.single-post .entry-content',
                    'property'    => 'font-size',
                    'choice'      => 'font_size',
                    'units'       => 'px',
                    'media_query' => '@media (max-width: 479px)',
                    'context'     => ['mobile']
                ],
            ],
        ]);
        
        // Meta Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('blog_post_meta_typography'),
            'label'       => __('Post Meta', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-primary-font-family)',
                    'font_size'      => '14',
                    'font_weight'    => '400',
                    'font_style'     => 'normal',
                    'line_height'    => '1.4',
                    'letter_spacing' => '0',
                    'text_transform' => 'none',
                ],
            ]),
            'output'      => [
                'desktop' => [
                    [
                        'element'  => '.single-post .entry-meta, .single-post .entry-footer',
                        'property' => 'font-family',
                        'choice'   => 'font_family',
                    ],
                    [
                        'element'  => '.single-post .entry-meta, .single-post .entry-footer',
                        'property' => 'font-size',
                        'choice'   => 'font_size',
                        'units'    => 'px',
                    ],
                    [
                        'element'  => '.single-post .entry-meta, .single-post .entry-footer',
                        'property' => 'font-weight',
                        'choice'   => 'font_weight',
                    ],
                    [
                        'element'  => '.single-post .entry-meta, .single-post .entry-footer',
                        'property' => 'line-height',
                        'choice'   => 'line_height',
                    ],
                    [
                        'element'  => '.single-post .entry-meta, .single-post .entry-footer',
                        'property' => 'letter-spacing',
                        'choice'   => 'letter_spacing',
                        'units'    => 'px',
                    ],
                    [
                        'element'  => '.single-post .entry-meta, .single-post .entry-footer',
                        'property' => 'text-transform',
                        'choice'   => 'text_transform',
                    ],
                ]
            ],
        ]);
        
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_post_design_heading_2'),
            'heading'     => __('Colors', $this->get_text_domain()),
            'tab'         => 'design',
        ]);
        
        // Post Title Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_post_title_color'),
            'label'       => __('Post Title Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-secondary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.single-post .entry-title',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Post Content Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_post_content_color'),
            'label'       => __('Post Content Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-text)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.single-post .entry-content',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Meta Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_post_meta_color'),
            'label'       => __('Meta Text Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#777777',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.single-post .entry-meta, .single-post .entry-footer',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Meta Link Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_post_meta_link_color'),
            'label'       => __('Meta Link Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-primary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.single-post .entry-meta a, .single-post .entry-footer a',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Meta Link Hover Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_post_meta_link_hover_color'),
            'label'       => __('Meta Link Hover Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-secondary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.single-post .entry-meta a:hover, .single-post .entry-footer a:hover',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_post_design_heading_3'),
            'heading'     => __('Post Header', $this->get_text_domain()),
            'tab'         => 'design',
        ]);
        
        $this->add_control('Slider', [
            'settings'    => $this->get_setting('blog_post_featured_image_radius'),
            'label'       => __('Featured Image Radius', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 0,
            'choices'     => [
                'min'  => 0,
                'max'  => 48,
                'step' => 1,
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.single-post .post-thumbnail img',
                    'property' => 'border-radius',
                    'units'    => 'px',
                ],
            ],
        ]);
        
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_post_design_heading_4'),
            'heading'     => __('Background', $this->get_text_domain()),
            'tab'         => 'design',
        ]);
        
        // Post Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_post_background_color'),
            'label'       => __('Post Background Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => '.single-post .post',
                    'property' => 'background-color',
                ],
            ],
        ]);
        
        // Comments Section
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_comments_heading'),
            'heading'     => __('Comments Section', $this->get_text_domain()),
            'tab'         => 'design',
        ]);
        
        // Comments Title Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('blog_comments_title_typography'),
            'label'       => __('Comments Title', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-secondary-font-family)',
                    'font_size'      => '24',
                    'font_weight'    => '600',
                    'line_height'    => '1.3',
                ],
            ]),
            'output'      => [
                'desktop' => [
                    [
                        'element'  => '.comments-title, .comment-reply-title',
                        'property' => 'font-family',
                        'choice'   => 'font_family',
                    ],
                    [
                        'element'  => '.comments-title, .comment-reply-title',
                        'property' => 'font-size',
                        'choice'   => 'font_size',
                        'units'    => 'px',
                    ],
                    [
                        'element'  => '.comments-title, .comment-reply-title',
                        'property' => 'font-weight',
                        'choice'   => 'font_weight',
                    ],
                    [
                        'element'  => '.comments-title, .comment-reply-title',
                        'property' => 'line-height',
                        'choice'   => 'line_height',
                    ],
                ]
            ],
        ]);
        
        // Comments Content Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('blog_comments_content_typography'),
            'label'       => __('Comments Content', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => 'var(--e-global-typography-primary-font-family)',
                    'font_size'      => '15',
                    'font_weight'    => '400',
                    'line_height'    => '1.5',
                ],
            ]),
            'output'      => [
                'desktop' => [
                    [
                        'element'  => '.comment-body, .comment-metadata, .comment-author',
                        'property' => 'font-family',
                        'choice'   => 'font_family',
                    ],
                    [
                        'element'  => '.comment-body, .comment-metadata, .comment-author',
                        'property' => 'font-size',
                        'choice'   => 'font_size',
                        'units'    => 'px',
                    ],
                    [
                        'element'  => '.comment-body, .comment-metadata, .comment-author',
                        'property' => 'font-weight',
                        'choice'   => 'font_weight',
                    ],
                    [
                        'element'  => '.comment-body, .comment-metadata, .comment-author',
                        'property' => 'line-height',
                        'choice'   => 'line_height',
                    ],
                ]
            ],
        ]);
        
        // Comments Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_text_color'),
            'label'       => __('Comments Text Color', $this->get_text_domain()),
            'tab'         => 'design',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.comment-body, .comment-metadata, .comment-author',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Comments Link Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_link_color'),
            'label'       => __('Comments Link Color', $this->get_text_domain()),
            'tab'         => 'design',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.comment-metadata a, .comment-reply-link',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Comments Background
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_bg_color'),
            'label'       => __('Comments Background', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#f9f9f9',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => '.comment-list .comment-body',
                    'property' => 'background-color',
                ],
            ],
        ]);
        
        // Comments Form Inputs
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_comments_form_heading'),
            'heading'     => __('Comments Form', $this->get_text_domain()),
            'tab'         => 'design',
        ]);
        
        // Input Border Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_input_border_color'),
            'label'       => __('Input Border Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#dddddd',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.comment-form input[type="text"], .comment-form input[type="email"], .comment-form input[type="url"], .comment-form textarea',
                    'property' => 'border-color',
                ],
            ],
        ]);
        
        // Input Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_input_bg_color'),
            'label'       => __('Input Background Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => '.comment-form input[type="text"], .comment-form input[type="email"], .comment-form input[type="url"], .comment-form textarea',
                    'property' => 'background-color',
                ],
            ],
        ]);
        
        // Submit Button Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_submit_text_color'),
            'label'       => __('Submit Button Text Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.comment-form input[type="submit"]',
                    'property' => 'color',
                ],
            ],
        ]);
        
        // Submit Button Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_submit_bg_color'),
            'label'       => __('Submit Button Background', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--color-primary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.comment-form input[type="submit"]',
                    'property' => 'background-color',
                ],
            ],
        ]);
        
        // Submit Button Hover Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_submit_hover_text_color'),
            'label'       => __('Submit Hover Text Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.comment-form input[type="submit"]:hover',
                    'property' => 'color',
                ],
            ],
        ]);
        
        // Submit Button Hover Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_comments_submit_hover_bg_color'),
            'label'       => __('Submit Hover Background', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--color-secondary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.comment-form input[type="submit"]:hover',
                    'property' => 'background-color',
                ],
                [
                    'element'  => '.comment-form input[type="submit"]:hover',
                    'property' => 'border-color',
                ],
            ],
        ]);
    }

}