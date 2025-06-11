<?php
namespace Polysaas\Customizer\Controls;

class Copyrights extends Control_Base {

    /**
     * Section ID
     */
    protected $section = 'footer_copyrights';

    /**
     * Register controls
     */
    public function register() {
        $this->add_footer_copyrights_fields();
    }

    public function add_footer_copyrights_fields() {
        $this->add_control('Textarea', [
                'settings'    => $this->get_setting('footer_copyrights_text'),
                'label'       => esc_html__( 'Custom Copyrights Text', $this->get_text_domain() ),
                'default'     => sprintf(__('2025 © %s - All rights reserved', $this->get_text_domain()), get_bloginfo('name')),
            ]
        );
    }
}