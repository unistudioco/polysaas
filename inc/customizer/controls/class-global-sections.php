<?php
namespace Polysaas\Customizer\Controls;

class Global_Sections extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'global_sections';

    /**
     * Register controls
     */
    public function register() {
        $this->add_global_sections_fields();
    }

    public function add_global_sections_fields() {

        $this->add_control('Custom', [
            'settings'    => $this->get_setting('header_section_title'),
            'default'     => '<h3 class="kirki-custom-title">' . esc_html__('Header', $this->get_text_domain()) . '</h3>',
            'priority' => 10,
        ]);

        $this->add_control('Select', [
            'settings'    => $this->get_setting('default_header'),
            'default' => '',
            'priority' => 11,
            'choices' => $this->get_sections_by_type('header'),
            'transport' => 'refresh',
        ]);

        $this->add_control('Custom', [
            'settings'    => $this->get_setting('footer_section_title'),
            'default'     => '<h3 class="kirki-custom-title">' . esc_html__('Footer', $this->get_text_domain()) . '</h3>',
            'priority' => 20,
        ]);

        $this->add_control('Select', [
            'settings'    => $this->get_setting('default_footer'),
            'default' => '',
            'priority' => 21,
            'choices' => $this->get_sections_by_type('footer'),
            'transport' => 'refresh',
        ]);

        $this->add_control('Custom', [
            'settings'    => $this->get_setting('generic_sections_title'),
            'default'     => '<h3 class="kirki-custom-title">' . esc_html__('Generic Sections', $this->get_text_domain()) . '</h3>',
            'priority' => 30,
        ]);


        $this->add_control('Repeater', [
            'settings' => $this->get_setting('generic_sections'),
            'priority' => 31,
            'row_label' => [
                'type' => 'field',
                'value' => esc_html__('Section', $this->get_text_domain()),
                'field' => 'title',
            ],
            'button_label' => esc_html__('Add Section', $this->get_text_domain()),
            'fields' => [
                'title' => [
                    'type' => 'text',
                    'label' => esc_html__('Title', $this->get_text_domain()),
                ],
                'section_id' => [
                    'type' => 'select',
                    'label' => esc_html__('Section', $this->get_text_domain()),
                    'choices' => $this->get_sections_by_type('generic'),
                ],
                'position' => [
                    'type' => 'select',
                    'label' => esc_html__('Position', $this->get_text_domain()),
                    'choices' => [
                        'before_header' => esc_html__('Before Header', $this->get_text_domain()),
                        'after_header' => esc_html__('After Header', $this->get_text_domain()),
                        'before_footer' => esc_html__('Before Footer', $this->get_text_domain()),
                        'after_footer' => esc_html__('After Footer', $this->get_text_domain()),
                    ],
                ],
            ],
        ]);

    }

    private function get_sections_by_type($type) {
        $sections = [];
        $sections[''] = esc_html__('— Select —', $this->get_text_domain());

        $args = [
            'post_type' => 'uc_global_section',
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
    
}