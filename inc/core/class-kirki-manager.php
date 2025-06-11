<?php
namespace Polysaas\Core;

/**
 * Kirki Customizer Manager
 *
 * @package Polysaas
 */
class Kirki_Manager {
    /**
     * Register default hooks and actions for WordPress
     */
    public function register() {
        // Check if Kirki exists
        if (!class_exists('\Kirki')) {
            return;
        }

        add_action('init', [$this, 'setup']);
        

        // Move Site Identity to Header Panel
        add_action('customize_register', [$this, 'reorganize_customizer_panels'], 11);

        // Add AJAX handlers
        add_action('wp_ajax_' . Config::prefix('export_settings'), [$this, 'export_settings']);
        add_action('wp_ajax_' . Config::prefix('import_settings'), [$this, 'import_settings']);
        add_action('wp_ajax_' . Config::prefix('reset_settings'), [$this, 'reset_settings']);
    
        // Add scripts for import/export
        add_action('customize_controls_enqueue_scripts', [$this, 'enqueue_import_export_scripts']);
    }

    /**
     * Setup Kirki
     */
    public function setup() {
        $this->add_panels();
        $this->add_sections();
        $this->add_fields();
    }

    /**
     * Add Kirki panels
     */
    private function add_panels() {
        new \Kirki\Panel(
            Config::prefix('theme_options'),
            [
                'priority'    => 10,
                'title'       => __('General Settings', Config::get('text_domain')),
            ]
        );
        new \Kirki\Panel(
            Config::prefix('header_options'),
            [
                'priority'    => 20,
                'title'       => __('Header', Config::get('text_domain')),
            ]
        );
        new \Kirki\Panel(
            Config::prefix('footer_options'),
            [
                'priority'    => 30,
                'title'       => __('Footer', Config::get('text_domain')),
            ]
        );
    }

    /**
     * Add Kirki Sections
     */
    private function add_sections() {
        // Header Sections
        new \Kirki\Section(
            Config::prefix('header_general'),
            [
                'title'       => __('General', Config::get('text_domain')),
                'panel'       => Config::prefix('header_options'),
                'priority'    => 10,
            ]
        );
        new \Kirki\Section(
            Config::prefix('header_navigation'),
            [
                'title'       => __('Navigation', Config::get('text_domain')),
                'panel'       => Config::prefix('header_options'),
                'priority'    => 30,
            ]
        );

        // Footer Sections
        new \Kirki\Section(
            Config::prefix('footer_general'),
            [
                'title'       => __('General', Config::get('text_domain')),
                'panel'       => Config::prefix('footer_options'),
                'priority'    => 10,
            ]
        );
        new \Kirki\Section(
            Config::prefix('footer_copyrights'),
            [
                'title'       => __('Copyrights', Config::get('text_domain')),
                'panel'       => Config::prefix('footer_options'),
                'priority'    => 20,
            ]
        );

        // Global Colors Section
        new \Kirki\Section(
            Config::prefix('colors'),
            [
                'title'       => __('Global Colors', Config::get('text_domain')),
                'panel'       => Config::prefix('theme_options'),
                'priority'    => 10,
            ]
        );

        // Typography Section
        new \Kirki\Section(
            Config::prefix('typography'),
            [
                'title'       => __('Typography', Config::get('text_domain')),
                'panel'       => Config::prefix('theme_options'),
                'priority'    => 20,
            ]
        );

        // Archive Layout Section
        new \Kirki\Section(
            Config::prefix('archive_layout'),
            [
                'title'       => __('Archive Layout', Config::get('text_domain')),
                'panel'       => Config::prefix('theme_options'),
                'priority'    => 60,
            ]
        );

        new \Kirki\Section(
            Config::prefix('import_export'),
            [
                'priority'    => 40,
                'title'       => __('Import / Export', Config::get('text_domain')),
            ]
        );
    }

    /**
     * Add Kirki fields
     */
    private function add_fields() {
        $this->add_header_fields();
        $this->add_footer_fields();
        $this->add_logo_fields();
        $this->add_color_fields();
        $this->add_typography_fields();
        $this->add_archive_fields();
        $this->add_import_export_fields();
    }

    /**
     * Add Header fields
     */
    private function add_header_fields() {
        // Header Layout
        new \Kirki\Pro\Field\Headline(
            [
                'settings'    => Config::prefix('header_layout_headline'),
                'label'       => esc_html__( 'Select Header Layout', Config::get('text_domain') ),
                'description' => sprintf(__( 'You can disable or keep the default theme header or use <a href="%s/wp-admin/edit.php?post_type=uc_header" target="_blank">Global Sections to create</a> a custom one.', Config::get('text_domain')), get_bloginfo('url') ),
                'section'     => Config::prefix('header_general'),
                // 'tooltip'     => __('You can disable or keep the default theme header or use <a href="http://rey-theme.local/wp-admin/edit.php?post_type=uc-global-sections" target="_blank">Global Sections to create</a> a custom one.', Config::get('text_domain')),
            ]
        );
        new \Kirki\Field\Radio_Buttonset(
            [
                'settings'    => Config::prefix('header_layout_type'),
                'section'     => Config::prefix('header_general'),
                'default'     => '_default',
                'choices'     => [
                    '_none'   => esc_html__( 'Disable', Config::get('text_domain') ),
                    '_default' => esc_html__( 'Default', Config::get('text_domain') ),
                    '_gs'  => esc_html__( 'Global Sections', Config::get('text_domain') ),
                ],
                'transport'   => 'refresh',
            ]
        );
        new \Kirki\Field\Select(
            [
                'settings'    => Config::prefix('header_layout_source'),
                'section'     => Config::prefix('header_general'),
                'default'     => '',
                'placeholder' => __( 'Select a header', Config::get('text_domain') ),
                'choices'     => \Kirki\Util\Helper::get_posts(
                    array(
                        'post_type'      => 'uc_header'
                    ) ,
                ),
                'active_callback'  => [
                    [
                        'setting'  => Config::prefix('header_layout_type'),
                        'operator' => '===',
                        'value'    => '_gs',
                    ],
                ],
            ]
        );
        new \Kirki\Pro\Field\Headline(
            [
                'settings'    => Config::prefix('header_layout_source_edit'),
                'label' => sprintf(__( '<a href="%s/wp-admin/edit.php?post_type=uc_header" target="_blank">Edit header</a> or <a href="%s/wp-admin/edit.php?post_type=uc_header" target="_blank">Create new header</a>', Config::get('text_domain')), get_bloginfo('url'), get_bloginfo('url') ),
                'section'     => Config::prefix('header_general'),
                'active_callback'  => [
                    [
                        'setting'  => Config::prefix('header_layout_type'),
                        'operator' => '===',
                        'value'    => '_gs',
                    ],
                    [
                        'setting'  => Config::prefix('header_layout_source'),
                        'operator' => '!==',
                        'value'    => '',
                    ],
                ],
            ]
        );
    }

    /**
     * Add Footer fields
     */
    private function add_footer_fields() {
        // Footer Layout
        new \Kirki\Pro\Field\Headline(
            [
                'settings'    => Config::prefix('footer_layout_headline'),
                'label'       => esc_html__( 'Select Footer Layout', Config::get('text_domain') ),
                'description' => sprintf(__( 'You can disable or keep the default theme header or use <a href="%s/wp-admin/edit.php?post_type=uc_header" target="_blank">Global Sections to create</a> a custom one.', Config::get('text_domain')), get_bloginfo('url') ),
                'section'     => Config::prefix('footer_general'),
                // 'tooltip'     => __('You can disable or keep the default theme footer or use <a href="http://rey-theme.local/wp-admin/edit.php?post_type=uc-global-sections" target="_blank">Global Sections to create</a> a custom one.', Config::get('text_domain')),
            ]
        );
        new \Kirki\Field\Radio_Buttonset(
            [
                'settings'    => Config::prefix('footer_layout_type'),
                'section'     => Config::prefix('footer_general'),
                'default'     => '_default',
                'choices'     => [
                    '_none'   => esc_html__( 'Disable', Config::get('text_domain') ),
                    '_default' => esc_html__( 'Default', Config::get('text_domain') ),
                    '_gs'  => esc_html__( 'Global Sections', Config::get('text_domain') ),
                ],
                'transport'   => 'refresh',
            ]
        );
        new \Kirki\Field\Select(
            [
                'settings'    => Config::prefix('footer_layout_source'),
                'section'     => Config::prefix('footer_general'),
                'default'     => '',
                'placeholder' => __( 'Select a footer', Config::get('text_domain') ),
                'choices'     => \Kirki\Util\Helper::get_posts(
                    array(
                        'post_type'      => 'uc_footer'
                    ) ,
                ),
                'active_callback'  => [
                    [
                        'setting'  => Config::prefix('footer_layout_type'),
                        'operator' => '===',
                        'value'    => '_gs',
                    ],
                ],
            ]
        );
        new \Kirki\Pro\Field\Headline(
            [
                'settings'    => Config::prefix('footer_layout_source_edit'),
                'label' => sprintf(__( '<a href="%s/wp-admin/edit.php?post_type=uc_footer" target="_blank">Edit footer</a> or <a href="%s/wp-admin/edit.php?post_type=uc_footer" target="_blank">Create new footer</a>', Config::get('text_domain')), get_bloginfo('url'), get_bloginfo('url') ),
                'section'     => Config::prefix('footer_general'),
                'active_callback'  => [
                    [
                        'setting'  => Config::prefix('footer_layout_type'),
                        'operator' => '===',
                        'value'    => '_gs',
                    ],
                    [
                        'setting'  => Config::prefix('footer_layout_source'),
                        'operator' => '!==',
                        'value'    => '',
                    ],
                ],
            ]
        );
        new \Kirki\Field\Textarea(
            [
                'settings'    => Config::prefix('footer_copyrights_text'),
                'label'       => esc_html__( 'Custom Copyrights Text', Config::get('text_domain') ),
                'section'     => Config::prefix('footer_copyrights'),
                'default'     => sprintf(__('2025 © %s - All rights reserved', Config::get('text_domain')), get_bloginfo('name')),
            ]
        );
    }

    /**
     * Add Import/Export fields
     */
    private function add_import_export_fields() {
    
        // Export Section
        new \Kirki\Field\Custom(
            [
                'settings'    => Config::prefix('export_button'),
                'section'     => Config::prefix('import_export'),
                'default'     => sprintf('
                    <span class="customize-control-title">%s</span>
                    <span class="description customize-control-description">%s</span>
                    <input type="button" class="button" name="kirki-export-button" value="%s" />',
                    __('Export', Config::get('text_domain')),
                    __('Click the button below to export the customization settings for this theme.', Config::get('text_domain')),
                    __('Export', Config::get('text_domain'))
                ),
            ]
        );

        new \Kirki\Pro\Field\Divider(
            [
                'settings' => Config::prefix('import_export_sep_1'),
                'section'  => Config::prefix('import_export'),
                'choices'  => [
                    'color' => '#d5d5d5',
                ],
            ]
        );

        // Import Section
        new \Kirki\Field\Custom(
            [
                'settings'    => Config::prefix('import_controls'),
                'section'     => Config::prefix('import_export'),
                'default'     => sprintf('
                    <span class="customize-control-title">%s</span>
                    <span class="description customize-control-description">%s</span>
                    <div class="kirki-import-controls">
                        <input type="file" name="kirki-import-file" class="kirki-import-file" />
                        <label class="kirki-import-images">
                            <input type="checkbox" name="kirki-import-images" value="1" /> %s
                        </label>
                        %s
                    </div>
                    <div class="kirki-uploading">%s</div>
                    <input type="button" class="button" name="kirki-import-button" value="%s" />',
                    __('Import', Config::get('text_domain')),
                    __('Upload a file to import customization settings for this theme.', Config::get('text_domain')),
                    __('Download and import image files?', Config::get('text_domain')),
                    wp_nonce_field(Config::prefix('importing'), 'nonce', true, false),
                    __('Uploading...', Config::get('text_domain')),
                    __('Import', Config::get('text_domain'))
                ),
            ]
        );

        new \Kirki\Pro\Field\Divider(
            [
                'settings' => Config::prefix('import_export_sep_2'),
                'section'  => Config::prefix('import_export'),
                'choices'  => [
                    'color' => '#d5d5d5',
                ],
            ]
        );
    
        // Reset Section
        new \Kirki\Field\Custom(
            [
                'settings'    => Config::prefix('reset_controls'),
                'section'     => Config::prefix('import_export'),
                'default'     => sprintf('
                    <span class="customize-control-title">%s</span>
                    <span class="description customize-control-description">%s</span>
                    <div class="kirki-reset-controls">
                        <input type="button" class="button button-danger" name="kirki-reset-button" value="%s" />
                        <input type="hidden" name="%s" value="%s" />
                    </div>',
                    __('Reset Settings', Config::get('text_domain')),
                    __('Warning: This will reset all customization settings to their default values.', Config::get('text_domain')),
                    __('Reset All Settings', Config::get('text_domain')),
                    Config::prefix('reset_nonce'),
                    wp_create_nonce(Config::prefix('reset_nonce'))
                ),
            ]
        );
    }

    /**
     * Add Global Colors fields
     */
    private function add_color_fields() {

        // Primary Color
        new \Kirki\Field\Color(
            [
                'settings'    => Config::prefix('color_primary'),
                'label'       => __('Primary Color', Config::get('text_domain')),
                'section'     => Config::prefix('colors'),
                'default'     => '#553cdf',
                'transport'   => 'auto',
                'choices'     => ['alpha' => true],
                'output'      => [
                    [
                        'element'  => ':root',
                        'property' => '--color-primary',
                    ],
                    [
                        'element'  => ':root',
                        'property' => '--uc-primary',
                    ],
                    [
                        'element'  => '.btn-primary',
                        'property' => '--uc-btn-bg',
                    ],
                    [
                        'element'  => '.btn-primary',
                        'property' => '--uc-btn-border-color',
                    ],
                ],
            ]
        );

        // Secondary Color
        new \Kirki\Field\Color(
            [
                'settings'    => Config::prefix('color_secondary'),
                'label'       => __('Secondary Color', Config::get('text_domain')),
                'section'     => Config::prefix('colors'),
                'default'     => '#ea8dfb',
                'transport'   => 'auto',
                'choices'     => ['alpha' => true],
                'output'      => [
                    [
                        'element'  => ':root',
                        'property' => '--color-secondary',
                    ],
                ],
            ]
        );

        // Gray Colors
        new \Kirki\Field\Color(
            [
                'settings'    => Config::prefix('color_gray_dark'),
                'label'       => __('Gray Dark', Config::get('text_domain')),
                'section'     => Config::prefix('colors'),
                'default'     => '#4c4952',
                'transport'   => 'auto',
                'choices'     => ['alpha' => true],
                'output'      => [
                    [
                        'element'  => ':root',
                        'property' => '--color-gray-dark',
                    ],
                ],
            ]
        );

        new \Kirki\Field\Color(
            [
                'settings'    => Config::prefix('color_gray_light'),
                'label'       => __('Gray Light', Config::get('text_domain')),
                'section'     => Config::prefix('colors'),
                'default'     => '#f4f3f7',
                'transport'   => 'auto',
                'choices'     => ['alpha' => true],
                'output'      => [
                    [
                        'element'  => ':root',
                        'property' => '--color-gray-light',
                    ],
                ],
            ]
        );

        // Background Colors
        new \Kirki\Field\Background(
            [
                'settings'    => Config::prefix('archive_header_background'),
                'label'       => __('Archive Header Background', Config::get('text_domain')),
                'section'     => Config::prefix('archive_layout'),
                'default'     => [
                    'background-color'      => '#f4f3f7',
                    'background-image'      => '',
                    'background-repeat'     => 'no-repeat',
                    'background-position'   => 'center center',
                    'background-size'       => 'cover',
                    'background-attachment' => 'scroll',
                ],
                'transport'   => 'auto',
                'output' => [
                    [
                        'element'  => '.archive-header',
                        'property' => 'background', // Can be specific: background-color, background-image, etc.
                        'media_query' => '@media (min-width: 768px)', // Optional media query
                    ],
                    [
                        'element'  => '.category-header', // Can target multiple elements
                        'property' => 'background',
                    ]
                ],
                'choices'     => [
                    'alpha' => true,
                ],
            ]
        );
    }


    /**
     * Add Typography fields
     */
    private function add_typography_fields() {
        // Body Typography
        new \Kirki\Field\Typography(
            [
                'settings'    => Config::prefix('body_typography'),
                'label'       => __('Body Typography', Config::get('text_domain')),
                'section'     => Config::prefix('typography'),
                'default'     => [
                    'font-family'     => 'Inter',
                    'variant'         => 'regular',
                    'font-size'       => '16px',
                    'line-height'     => '1.65',
                    'letter-spacing'  => '0',
                    'text-transform'  => 'none',
                ],
                'transport'   => 'auto',
                'output'      => [
                    [
                        'element' => 'body',
                    ],
                ],
            ]
        );

        // Heading Typography
        new \Kirki\Field\Typography(
            [
                'settings'    => Config::prefix('heading_typography'),
                'label'       => __('Heading Typography', Config::get('text_domain')),
                'section'     => Config::prefix('typography'),
                'default'     => [
                    'font-family'     => 'Inter',
                    'variant'         => '700',
                    'text-transform'  => 'none',
                ],
                'transport'   => 'auto',
                'output'      => [
                    [
                        'element' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    ],
                ],
            ]
        );
    }

    /**
     * Add archive layout fields
     */
    private function add_archive_fields() {
        // Archive Layout
        new \Kirki\Field\Radio_Image(
            [
                'settings'    => Config::prefix('archive_header_layout'),
                'label'       => __('Archive Header Layout', Config::get('text_domain')),
                'section'     => Config::prefix('archive_layout'),
                'default'     => 'layout-1',
                'choices'     => [
                    'layout-1' => get_template_directory_uri() . '/assets/images/customizer/page-header-layout-1.png',
                    'layout-2' => get_template_directory_uri() . '/assets/images/customizer/page-header-layout-2.png'
                ],
                'transport'   => 'refresh',
            ]
        );

        // Grid Columns
        new \Kirki\Field\Select(
            [
                'settings'    => Config::prefix('archive_grid_columns'),
                'label'       => __('Grid Columns', Config::get('text_domain')),
                'section'     => Config::prefix('archive_layout'),
                'default'     => '12',
                'choices'     => [
                    '12' => __('Classic', Config::get('text_domain')),
                    '6'  => __('2 Cols', Config::get('text_domain')),
                    '4'  => __('3 Cols', Config::get('text_domain')),
                    '3'  => __('4 Cols', Config::get('text_domain')),
                ],
                'transport'   => 'refresh',
            ]
        );
    }

    /**
     * Reorganize Customizer Panels and Sections
     */
    public function reorganize_customizer_panels($wp_customize) {
        // Move Site Identity section to Header panel
        $site_identity = $wp_customize->get_section('title_tagline');
        if ($site_identity) {
            $site_identity->panel = Config::prefix('header_options');
            $site_identity->title = __('Logo', Config::get('text_domain'));
            $site_identity->priority = 20;
        }

        // Reposition default controls
        $wp_customize->get_control('custom_logo')->priority = 11;
        $wp_customize->get_control('site_icon')->priority = 13;
        $wp_customize->get_control('blogname')->priority = 30;
        $wp_customize->get_control('blogdescription')->priority = 31;
        $wp_customize->get_control('display_header_text')->priority = 32;

        $header_navigation = $wp_customize->get_section(Config::prefix('header_navigation'));
        if ($header_navigation) {
            $header_navigation->priority = 30;
        }
    }

    private function add_logo_fields() {

        new \Kirki\Field\Upload(
            [
                'settings'    => Config::prefix('logo_mobile'),
                'label'       => esc_html__( 'Upload Mobile Logo', Config::get('text_domain') ),
                'section'     => 'title_tagline',
                'priority'    => 12,
            ]
        );

        new \Kirki\Field\Custom(
            [
                'settings'    => Config::prefix('logo_settings_sep'),
                'section'     => 'title_tagline',
                'default'     => '<h3 class="customize-section-title">' . __('Logo Settings', Config::get('text_domain')) . '</h3>',
                'priority'    => 15,
            ]
        );
    
        // Logo Mobile Max Width
        // new \Kirki\Pro\Field\InputSlider(
        //     [
        //         'settings'    => Config::prefix('logo_max_width'),
        //         'label'       => __('Logo Max Width', Config::get('text_domain')),
        //         'section'     => 'title_tagline',
        //         'priority'    => 17,
        //         'transport'   => 'auto',
        //         'choices'     => [
        //             'min'  => 64,
        //             'max'  => 256,
        //             'step' => 1,
        //         ],
        //         'responsive'  => true,
        //         'default'    => [
        //             'desktop' => 140,
        //             'mobile'  => 80,
        //         ],
        //         'output'      => [
        //             [
        //                 'element'     => '.custom-logo',
        //                 'property'    => 'max-width',
        //                 'media_query' => [
        //                     'desktop' => '@media (min-width: 1024px)',
        //                     'mobile'  => '@media (max-width: 767px)',
        //                 ],
        //             ]
        //         ]
        //     ]
        // );
    
        // Spacing Label
        new \Kirki\Field\Custom(
            [
                'settings'    => Config::prefix('logo_site_identity_sep'),
                'section'     => 'title_tagline',
                'default'     => '<h3 class="customize-section-title">' . __('Site Identity', Config::get('text_domain')) . '</h3>',
                'priority'    => 20,
            ]
        );
    }

    /**
     * Export settings
     */
    public function export_settings() {
        // Verify nonce and capabilities
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (!check_ajax_referer(Config::prefix('import_export'), 'nonce', false)) {
            wp_send_json_error(['message' => 'Invalid nonce']);
            return;
        }

        $theme = get_stylesheet();
        $template = get_template();
        $charset = get_option('blog_charset');
        
        // Get only the mods with our prefix
        $all_mods = get_theme_mods();
        $mods = array_filter($all_mods, function($key) {
            return strpos($key, Config::prefix('')) === 0;
        }, ARRAY_FILTER_USE_KEY);
        
        $data = [
            'template' => $template,
            'theme' => $theme,
            'mods' => $mods,
            'options' => [],
            'wp_css' => function_exists('wp_get_custom_css') ? wp_get_custom_css() : ''
        ];

        // Set download headers
        header('Content-disposition: attachment; filename=' . $theme . '-export.dat');
        header('Content-Type: application/octet-stream; charset=' . $charset);

        // Output serialized data
        echo serialize($data);
        die();
    }

    /**
     * Import settings
     */
    public function import_settings() {
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (!check_ajax_referer(Config::prefix('import_export'), 'nonce', false)) {
            wp_send_json_error(['message' => 'Invalid nonce']);
            return;
        }

        if (!isset($_FILES['import_file'])) {
            wp_send_json_error(['message' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['import_file'];
        
        // Validate file type
        $validate = wp_check_filetype($file['name'], ['dat' => 'application/octet-stream']);
        if ('application/octet-stream' !== $validate['type']) {
            wp_send_json_error(['message' => 'Invalid file type']);
            return;
        }

        // Read file content
        $raw = file_get_contents($file['tmp_name']);
        $data = @unserialize($raw);

        if (!is_array($data) || !isset($data['theme']) || !isset($data['mods'])) {
            wp_send_json_error(['message' => 'Invalid import data']);
            return;
        }

        // Check theme compatibility
        if ($data['theme'] !== get_stylesheet()) {
            wp_send_json_error(['message' => 'Settings are not compatible with this theme']);
            return;
        }

        // Remove existing theme mods with our prefix
        $existing_mods = get_theme_mods();
        foreach ($existing_mods as $key => $value) {
            if (strpos($key, Config::prefix('')) === 0) {
                remove_theme_mod($key);
            }
        }

        // Import new mods
        foreach ($data['mods'] as $key => $value) {
            set_theme_mod($key, $value);
        }

        // Import custom CSS if available
        if (!empty($data['wp_css'])) {
            wp_update_custom_css_post($data['wp_css']);
        }

        wp_send_json_success(['message' => 'Settings imported successfully']);
    }

    /**
     * Reset settings
     */
    public function reset_settings() {
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], Config::prefix('reset_nonce'))) {
            wp_send_json_error(['message' => 'Invalid nonce']);
            return;
        }

        // Get all theme mods
        $theme_mods = get_theme_mods();
        
        if ($theme_mods) {
            foreach ($theme_mods as $key => $value) {
                remove_theme_mod($key);
            }
        }

        // Reset custom CSS
        if (function_exists('wp_update_custom_css_post')) {
            wp_update_custom_css_post('');
        }

        wp_send_json_success(['message' => 'All settings have been reset to defaults']);
    }

    /**
     * Enqueue scripts for import/export functionality
     */
    public function enqueue_import_export_scripts() {
        wp_enqueue_script(
            Config::prefix('import-export'),
            THEME_URI . '/assets/js/admin/customizer-import-export.js',
            ['jquery', 'customize-controls'],
            THEME_VERSION,
            true
        );
    
        wp_localize_script(
            Config::prefix('import-export'),
            'kirkiImportExport',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce(Config::prefix('import_export')),
                'action_export' => Config::prefix('export_settings'),
                'action_import' => Config::prefix('import_settings'),
                'action_reset' => Config::prefix('reset_settings'),
                'reset_nonce_id' => Config::prefix('reset_nonce') // This should match the name in the hidden input
            ]
        );
    
        // Add inline styles
        wp_add_inline_style(
            'customize-controls',
            '
            .kirki-uploading { display: none; }
            .kirki-import-controls { margin: 1em 0; }
            .kirki-import-file { margin-bottom: 1em; }
            .kirki-import-images { display: block; margin: 1em 0; }
            '
        );
    }
}