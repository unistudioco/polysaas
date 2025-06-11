<?php
namespace Polysaas\Customizer\Controls;

use Polysaas\Core\Config;

class Blog_Archive extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'blog_archive';

    /**
     * Register controls
     */
    public function register() {
        $this->add_blog_archive_general();
        $this->add_blog_archive_design();
    }

    public function add_blog_archive_general() {

        $this->add_heading_control( [
            'settings'    => $this->get_setting('blog_archive_heading_1'),
            'heading'     => __('Page Header / Cover', $this->get_text_domain()),
            'tab'         => 'general',
            'divider'     => false,
        ]);

        // Archive Layout
        $this->add_control('Radio_Image', [
                'settings'    => $this->get_setting('blog_archive_header_layout'),
                'label'       => __('Select Placement', $this->get_text_domain()),
                'tab'         => 'general',
                'default'     => 'full-width',
                'choices'     => [
                    'full-width'    => get_template_directory_uri() . '/assets/images/customizer/page-header-full.png',
                    'main-content'  => get_template_directory_uri() . '/assets/images/customizer/page-header-main.png',
                    'disabled'      => get_template_directory_uri() . '/assets/images/customizer/page-header-disabled.png',
                ],
                'transport'   => 'refresh',
            ]
        );

        // Add normal controls if no override
        $this->add_control('Radio_Buttonset', [
            'settings'    => $this->get_setting('blog_archive_page_header_type'),
            'label'       => __('Select Template', $this->get_text_domain()),
            'tab'         => 'general',
            'default'     => '_default',
            'choices'     => [
                '_default' => esc_html__('Theme', $this->get_text_domain()),
                '_gs'  => esc_html__('Elementor', $this->get_text_domain()),
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_header_layout'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ],
            ]
        ]);

        $this->add_inline_control('Select', [
            'settings'    => $this->get_setting('blog_archive_page_header_template'),
            'label'       => __('Layout Template', $this->get_text_domain()),
            'tab'         => 'general',
            'default'     => '',
            'transport'   => 'refresh',
            'placeholder' => __('Select a layout', $this->get_text_domain()),
            'choices'     => $this->get_global_sections('pagecover'),
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_header_layout'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ],
                [
                    'setting'  => $this->get_setting('blog_archive_page_header_type'),
                    'operator' => '===',
                    'value'    => '_gs',
                ],
            ],
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_archive_breadcrumbs'),
            'label'       => __('Breadcrumbs', $this->get_text_domain()),
            'tab'         => 'general',
            'default'     => true,
            'transport'   => 'refresh',
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_header_layout'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ],
                [
                    'setting'  => $this->get_setting('blog_archive_page_header_type'),
                    'operator' => '===',
                    'value'    => '_default',
                ],
            ]
        ]);

        $this->add_heading_control( [
            'settings'    => $this->get_setting('blog_archive_heading_2'),
            'heading'     => __('Layout Options', $this->get_text_domain()),
            'tab'         => 'general',
            'divider'     => 'top',
        ]);

        // Grid Columns
        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('blog_archive_sidebar'),
                'label'       => __('Sidebar Placement', $this->get_text_domain()),
                'tab'         => 'general',
                'default'     => 'right',
                'choices'     => [
                    'left'  => __('Left', $this->get_text_domain()),
                    'right' => __('Right', $this->get_text_domain()),
                    'disabled'  => __('Disabled', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
            ]
        );

        // Grid Columns
        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('blog_archive_grid_columns'),
                'label'       => __('Grid Columns', $this->get_text_domain()),
                'tab'         => 'general',
                'default'     => '12',
                'choices'     => [
                    '12' => __('Classic', $this->get_text_domain()),
                    '6'  => __('2 Cols', $this->get_text_domain()),
                    '4'  => __('3 Cols', $this->get_text_domain()),
                    '3'  => __('4 Cols', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
            ]
        );

        // Archive Post Card Style
        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('blog_archive_post_card_style'),
                'label'       => __('Post Card Style', $this->get_text_domain()),
                'tab'         => 'general',
                'default'     => 'style-1',
                'choices'     => [
                    'style-1'   => __('Style 1', $this->get_text_domain()),
                    'style-2'   => __('Style 2', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
            ]
        );

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_archive_grid_match_height'),
            'label'       => __('Match Height', $this->get_text_domain()),
            'tab'         => 'general',
            'default'     => false,
            'transport'   => 'refresh',
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_grid_columns'),
                    'operator' => '!==',
                    'value' => '12',
                ]
            ]
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('blog_archive_grid_masonry'),
            'label'       => __('Masonry Grid', $this->get_text_domain()),
            'tab'         => 'general',
            'default'     => false,
            'transport'   => 'refresh',
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_grid_columns'),
                    'operator' => '!==',
                    'value' => '12',
                ],
                [
                    'setting' => Config::prefix('blog_archive_grid_match_height'),
                    'operator' => '!=',
                    'value' => true,
                ]
            ]
        ]);

        $this->add_heading_control( [
            'settings'    => $this->get_setting('blog_archive_heading_3'),
            'heading'     => __('Hide or Display', $this->get_text_domain()),
            'tab'         => 'general',
            'divider'     => 'top',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('show_post_excerpt'),
            'label'       => __('Show Excerpt', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('show_post_readmore'),
            'label'       => __('Show Read More', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Checkbox_Switch', [
            'settings'    => $this->get_setting('show_post_featured_image'),
            'label'       => __('Show Featured Image', $this->get_text_domain()),
            'default'     => true,
            'transport'   => 'refresh',
            'tab'         => 'general',
        ]);

        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('post_featured_image_ratio'),
                'label'       => __('Featured Image Ratio', $this->get_text_domain()),
                'tab'         => 'general',
                'default'     => 'ratio-1x1',
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
                        'setting' => Config::prefix('show_post_featured_image'),
                        'operator' => '==',
                        'value' => true,
                    ]
                ]
            ]
        );

        $this->add_inline_control('Checkbox_Switch', [
            'settings' => $this->get_setting('show_post_meta'),
            'label' => __('Show Meta Post', $this->get_text_domain()),
            'default' => true,
            'tab'         => 'general',
        ]);

        $this->add_control('Sortable', [
            'settings' => $this->get_setting('post_meta_order'),
            'label' => __('Meta Post Order  Visibility', $this->get_text_domain()),
            'default' => ['date', 'comments'],
            'tab'         => 'general',
            'choices' => [
                'author' => __('Author', $this->get_text_domain()),
                'date' => __('Date', $this->get_text_domain()),
                'categories' => __('Categories', $this->get_text_domain()),
                'comments' => __('Comments', $this->get_text_domain()),
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('show_post_meta'),
                    'operator' => '==',
                    'value' => true,
                ]
            ]
        ]);

        $this->add_heading_control( [
            'settings'    => $this->get_setting('blog_archive_heading_4'),
            'heading'     => __('Pagination', $this->get_text_domain()),
            'tab'         => 'general',
            'divider'     => 'top',
        ]);

        // Blog Archive Pagination
        $this->add_inline_control('Select', [
                'settings'    => $this->get_setting('blog_archive_pagination'),
                'label'       => __('Select Type', $this->get_text_domain()),
                'tab'         => 'general',
                'default'     => 'numbered',
                'choices'     => [
                    'numbered'          => __('Numbered', $this->get_text_domain()),
                    'ajax-loadmore'     => __('AJAX Load More', $this->get_text_domain()),
                    'disabled'          => __('Disabled', $this->get_text_domain()),
                ],
                'transport'   => 'refresh',
            ]
        );

    }

    public function add_blog_archive_design() {

        // Blog Archive Section
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_archive_design_heading'),
            'heading'     => __('Blog Archive', $this->get_text_domain()),
            'tab'         => 'design',
            'divider'     => false,
        ]);
        
        // Archive Post Title Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('blog_archive_title_typography'),
            'label'       => __('Archive Title', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => '',
                    'font_size'      => '',
                    'font_weight'    => '',
                    'line_height'    => '',
                ],
                'tablet' => [
                    'font_size'      => '',
                ],
                'mobile' => [
                    'font_size'      => '',
                ],
            ]),
            'output'      => [
                'desktop' => [
                    [
                        'element'  => '.blog .entry-title, .archive .entry-title',
                        'property' => 'font-family',
                        'choice'   => 'font_family',
                        'context'  => ['desktop']
                    ],
                    [
                        'element'  => '.blog .entry-title, .archive .entry-title',
                        'property' => 'font-size',
                        'choice'   => 'font_size',
                        'units'    => 'px',
                        'context'  => ['desktop']
                    ],
                    [
                        'element'  => '.blog .entry-title, .archive .entry-title',
                        'property' => 'font-weight',
                        'choice'   => 'font_weight',
                        'context'  => ['desktop']
                    ],
                    [
                        'element'  => '.blog .entry-title, .archive .entry-title',
                        'property' => 'line-height',
                        'choice'   => 'line_height',
                        'context'  => ['desktop']
                    ],
                    // Tablet settings
                    [
                        'element'     => '.blog .entry-title, .archive .entry-title',
                        'property'    => 'font-size',
                        'choice'      => 'font_size',
                        'units'       => 'px',
                        'media_query' => '@media (max-width: 991px)',
                        'context'     => ['tablet']
                    ],
                    // Mobile settings
                    [
                        'element'     => '.blog .entry-title, .archive .entry-title',
                        'property'    => 'font-size',
                        'choice'      => 'font_size',
                        'units'       => 'px',
                        'media_query' => '@media (max-width: 479px)',
                        'context'     => ['mobile']
                    ],
                ],
            ],
        ]);
        
        // Archive Post Excerpt Typography
        $this->add_control('TypographyPopup', [
            'settings'    => $this->get_setting('blog_archive_excerpt_typography'),
            'label'       => __('Archive Excerpt', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => json_encode([
                'desktop' => [
                    'font_family'    => '',
                    'font_size'      => '',
                    'font_style'     => 'normal',
                    'font_weight'    => '',
                    'line_height'    => '1.614',
                ],
            ]),
            'output'      => [
                'desktop' => [
                    [
                        'element'  => ['.blog .entry-summary', '.archive .entry-summary'],
                        'property' => 'font-family',
                        'choice'   => 'font_family',
                    ],
                    [
                        'element'  => ['.blog .entry-summary', '.archive .entry-summary'],
                        'property' => 'font-size',
                        'choice'   => 'font_size',
                        'units'    => 'px',
                    ],
                    [
                        'element'  => ['.blog .entry-summary', '.archive .entry-summary'],
                        'property' => 'font-weight',
                        'choice'   => 'font_weight',
                    ],
                    [
                        'element'  => ['.blog .entry-summary', '.archive .entry-summary'],
                        'property' => 'line-height',
                        'choice'   => 'line_height',
                    ],
                ],
            ],
        ]);
        
        // Archive Title Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_archive_title_color'),
            'label'       => __('Archive Title Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-secondary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.blog .entry-title a, .archive .entry-title a',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Archive Title Hover Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_archive_title_hover_color'),
            'label'       => __('Archive Title Hover Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-primary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.blog .entry-title a:hover, .archive .entry-title a:hover',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Archive Excerpt Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_archive_excerpt_color'),
            'label'       => __('Archive Excerpt Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-text)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => ['.blog .entry-summary', '.archive .entry-summary'],
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Archive Post Background
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_archive_item_bg_color'),
            'label'       => __('Archive Item Background', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'choices'     => ['alpha' => true],
            'output'      => [
                [
                    'element'  => '.blog article.post, .archive article.post',
                    'property' => 'background-color',
                ],
            ],
        ]);
        
        // Archive Post Padding
        $this->add_control('Dimensions', [
            'settings'    => $this->get_setting('blog_archive_item_padding'),
            'label'       => __('Archive Item Padding', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => [
                'top'    => '20px',
                'right'  => '20px',
                'bottom' => '20px',
                'left'   => '20px',
            ],
            'choices'     => [
                'labels' => [
                    'top'    => __('Top', $this->get_text_domain()),
                    'right'  => __('Right', $this->get_text_domain()),
                    'bottom' => __('Bottom', $this->get_text_domain()),
                    'left'   => __('Left', $this->get_text_domain()),
                ],
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.blog article.post, .archive article.post',
                    'property' => 'padding',
                    'choice'   => 'padding',
                ],
            ],
        ]);
        
        // Archive Item Border
        $this->add_control('Border', [
            'settings'    => $this->get_setting('blog_archive_item_border'),
            'label'       => __('Archive Item Border', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => [
                'width'  => [
                    'top'    => '1px',
                    'right'  => '1px',
                    'bottom' => '1px',
                    'left'   => '1px',
                ],
                'color'  => '#eeeeee',
                'style'  => 'solid',
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.blog article.post, .archive article.post',
                    'property' => 'border',
                ],
            ],
        ]);
        
        // Featured Image Border Radius
        $this->add_control('Slider', [
            'settings'    => $this->get_setting('post_featured_image_radius'),
            'label'       => __('Featured Image Radius', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 5,
            'choices'     => [
                'min'  => 0,
                'max'  => 48,
                'step' => 1,
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.blog .post-thumbnail, .blog .entry-media, .archive .post-thumbnail, .archive .entry-media',
                    'property' => 'border-radius',
                    'units'    => 'px',
                ],
            ],
        ]);
        
        // Read More Button
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_read_more_heading'),
            'heading'     => __('Read More Button', $this->get_text_domain()),
            'tab'         => 'design',
        ]);
        
        // Read More Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_read_more_text_color'),
            'label'       => __('Text Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.read-more-btn',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Read More Hover Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_read_more_text_hover_color'),
            'label'       => __('Text Hover Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.read-more-btn:hover',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Read More Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_read_more_bg_color'),
            'label'       => __('Background Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-primary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.read-more-btn',
                    'property' => 'background-color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
        
        // Read More Hover Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_read_more_bg_hover_color'),
            'label'       => __('Background Hover Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-secondary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.read-more-btn:hover',
                    'property' => 'background-color',
                    'value_pattern' => '$!important',
                ],
            ],
        ]);
            
        // Read More Border Radius
        $this->add_control('Slider', [
            'settings'    => $this->get_setting('blog_read_more_border_radius'),
            'label'       => __('Border Radius', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 4,
            'choices'     => [
                'min'  => 0,
                'max'  => 50,
                'step' => 1,
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.read-more-btn',
                    'property' => 'border-radius',
                    'units'    => 'px',
                ],
            ],
        ]);
        
        // Read More Padding
        $this->add_control('Dimensions', [
            'settings'    => $this->get_setting('blog_read_more_padding'),
            'label'       => __('Padding', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => [
                'top'    => '8px',
                'right'  => '16px',
                'bottom' => '8px',
                'left'   => '16px',
            ],
            'choices'     => [
                'labels' => [
                    'top'    => __('Top', $this->get_text_domain()),
                    'right'  => __('Right', $this->get_text_domain()),
                    'bottom' => __('Bottom', $this->get_text_domain()),
                    'left'   => __('Left', $this->get_text_domain()),
                ],
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.read-more-btn',
                    'property' => 'padding',
                    'choice'   => 'padding',
                ],
            ],
        ]);

        // Pagination
        $this->add_heading_control([
            'settings'    => $this->get_setting('blog_pagination_heading'),
            'heading'     => __('Pagination', $this->get_text_domain()),
            'tab'         => 'design',
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_pagination'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ]
            ]
        ]);
        
        // Pagination Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_pagination_text_color'),
            'label'       => __('Text Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#666666',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.pagination .page-numbers',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_pagination'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ]
            ]
        ]);
        
        // Pagination Active Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_pagination_active_text_color'),
            'label'       => __('Active Text Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.pagination .page-numbers.current',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_pagination'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ]
            ]
        ]);
        
        // Pagination Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_pagination_bg_color'),
            'label'       => __('Background Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#f5f5f5',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.pagination .page-numbers',
                    'property' => 'background-color',
                    'value_pattern' => '$!important',
                ],
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_pagination'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ]
            ]
        ]);
        
        // Pagination Active Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_pagination_active_bg_color'),
            'label'       => __('Active Background Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-primary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.pagination .page-numbers.current',
                    'property' => 'background-color',
                    'value_pattern' => '$!important',
                ],
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_pagination'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ]
            ]
        ]);
        
        // Pagination Hover Text Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_pagination_hover_text_color'),
            'label'       => __('Hover Text Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => '#ffffff',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.pagination .page-numbers:hover:not(.current)',
                    'property' => 'color',
                    'value_pattern' => '$!important',
                ],
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_pagination'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ]
            ]
        ]);
        
        // Pagination Hover Background Color
        $this->add_control('ColorElementor', [
            'settings'    => $this->get_setting('blog_pagination_hover_bg_color'),
            'label'       => __('Hover Background Color', $this->get_text_domain()),
            'tab'         => 'design',
            'default'     => 'var(--e-global-color-secondary)',
            'transport'   => 'auto',
            'output'      => [
                [
                    'element'  => '.pagination .page-numbers:hover:not(.current)',
                    'property' => 'background-color',
                    'value_pattern' => '$!important',
                ],
            ],
            'active_callback' => [
                [
                    'setting' => Config::prefix('blog_archive_pagination'),
                    'operator' => '!==',
                    'value' => 'disabled',
                ]
            ]
        ]);
    }

}