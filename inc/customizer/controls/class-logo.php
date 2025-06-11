<?php
namespace Polysaas\Customizer\Controls;

class Logo extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'title_tagline';

    public function register() {

        add_action('customize_register', [$this, 'reorganize_customizer_panels'], 11);

        $this->add_logo_upload_controls();
        $this->add_logo_settings_controls();
    }


    public function add_logo_upload_controls() {

        $this->add_control('Upload', [
            'settings'    => $this->get_setting('logo_mobile'),
            'label'       => esc_html__('Upload Mobile Logo', $this->get_text_domain()),
            'priority'    => 12,
        ]);

    }

    public function add_logo_settings_controls() {

        // Another pro control
        $this->add_pro_control('Headline', [
            'settings'    => $this->get_setting('logo_settings_sep'),
            'default'     => '<h3 class="customize-section-title">' . __('Logo Settings', $this->get_text_domain()) . '</h3>',
            'priority'    => 15,
        ]);

        // $this->add_pro_control('InputSlider', [
        //     'settings'    => $this->get_setting('logo_max_width'),
        //     'label'       => __('Logo Max Width', $this->get_text_domain()),
        //     'priority'    => 17,
        //     'transport'   => 'auto',
        //     'choices'     => [
        //         'min'  => 64,
        //         'max'  => 256,
        //         'step' => 1,
        //     ],
        //     'responsive'  => true,
        //     'default'    => [
        //         'desktop' => 140,
        //         'mobile'  => 80,
        //     ],
        //     'output'      => [
        //         [
        //             'element'     => '.custom-logo',
        //             'property'    => 'max-width',
        //             'media_query' => [
        //                 'desktop' => '@media (min-width: 1024px)',
        //                 'mobile'  => '@media (max-width: 767px)',
        //             ],
        //         ]
        //     ]
        // ]);

    }

    /**
     * Reorganize Customizer Panels and Sections
     */
    public function reorganize_customizer_panels($wp_customize) {
        // Move Site Identity section to Header panel
        $site_identity = $wp_customize->get_section('title_tagline');
        if ($site_identity) {
            $site_identity->panel = $this->get_setting('header_options');
            $site_identity->title = __('Site Logo', $this->get_text_domain());
            $site_identity->priority = 20;
        }

        // Reposition default controls
        $wp_customize->get_control('custom_logo')->priority = 11;
        $wp_customize->get_control('site_icon')->priority = 13;
        $wp_customize->get_control('blogname')->priority = 30;
        $wp_customize->get_control('blogdescription')->priority = 31;
        $wp_customize->get_control('display_header_text')->priority = 32;

        $header_navigation = $wp_customize->get_section($this->get_setting('header_navigation'));
        if ($header_navigation) {
            $header_navigation->priority = 30;
        }
    }

}