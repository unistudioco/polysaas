<?php
namespace Polysaas\Customizer\Traits;

trait Typography {
    /**
     * Add custom typography control
     */
    protected function add_typography_control($args) {

        // Default values
        $defaults = [
            'settings' => '',
            'label' => '',
            'description' => '',
            'section' => $this->section,
            'default' => [
                'font-family' => '',
                'variant' => 'regular',
                'font-size' => '',
                'line-height' => '',
                'letter-spacing' => '',
                'text-transform' => '',
                'color' => ''
            ],
            'priority' => 10,
            'transport' => 'auto'
        ];

        $args = wp_parse_args($args, $defaults);

        // Create variants options HTML
        $variants_html = '';
        foreach ($this->get_variant_choices() as $value => $label) {
            $variants_html .= sprintf(
                '<option value="%s"%s>%s</option>',
                esc_attr($value),
                selected($args['default']['variant'], $value, false),
                esc_html($label)
            );
        }
    
        // Create text transform options HTML
        $transform_html = '';
        foreach ($this->get_text_transform_choices() as $value => $label) {
            $transform_html .= sprintf(
                '<option value="%s"%s>%s</option>',
                esc_attr($value),
                selected($args['default']['text-transform'], $value, false),
                esc_html($label)
            );
        }
    
        $current_font = !empty($args['default']['font-family']) ? $args['default']['font-family'] : __('Default font', $this->get_text_domain());
        $current_variant = !empty($args['default']['variant']) ? $args['default']['variant'] : 'regular';
        $preview_text = $current_font . ' / ' . $current_variant;    

        // Create custom HTML for the control
        $html = sprintf(
            '<div class="rey-cstTypo-wrapper">
                <span class="rey-cstTypo-btn">
                    <span class="dashicons dashicons-edit"></span>
                    <span class="rey-cstTypo-text">%s</span>
                </span>
                <div class="typography-wrapper position-right">
                    <div class="font-family">
                        <h5>%s</h5>
                        <select class="font-family-select">%s</select>
                    </div>
                    <div class="variant">
                        <h5>%s</h5>
                        <select class="variant-select">%s</select>
                    </div>
                    <div class="font-size">
                        <h5>%s</h5>
                        <input type="text" value="%s" placeholder="eg: 14px">
                    </div>
                    <div class="line-height">
                        <h5>%s</h5>
                        <input type="text" value="%s" placeholder="eg: 1.5">
                    </div>
                    <div class="letter-spacing">
                        <h5>%s</h5>
                        <input type="text" value="%s" placeholder="eg: 0px">
                    </div>
                    <div class="text-transform">
                        <h5>%s</h5>
                        <select class="text-transform-select">%s</select>
                    </div>
                    <div class="color">
                        <h5>%s</h5>
                        <input type="text" class="typography-color" value="%s">
                    </div>
                </div>
            </div>',
            esc_html($preview_text),
            __('Font Family', $this->get_text_domain()),
            $this->get_fonts_options_html(),
            __('Variant', $this->get_text_domain()),
            $variants_html,
            __('Font Size', $this->get_text_domain()),
            esc_attr($args['default']['font-size']),
            __('Line Height', $this->get_text_domain()),
            esc_attr($args['default']['line-height']),
            __('Letter Spacing', $this->get_text_domain()),
            esc_attr($args['default']['letter-spacing']),
            __('Text Transform', $this->get_text_domain()),
            $transform_html,
            __('Color', $this->get_text_domain()),
            esc_attr($args['default']['color'])
        );
    
        // Add control
        $this->add_inline_control('Custom', [
            'settings' => $args['settings'],
            'section' => $args['section'],
            'label' => $args['label'],
            'description' => $args['description'],
            'default' => $html,
            'priority' => $args['priority'],
        ]);
    }

    /**
     * Get fonts options HTML
     */
    protected function get_fonts_options_html() {
        $fonts = $this->get_font_choices();
        $html = '';

        foreach ($fonts as $group => $group_fonts) {
            $html .= sprintf('<optgroup label="%s">', esc_attr($group));
            foreach ($group_fonts as $value => $label) {
                $html .= sprintf(
                    '<option value="%s">%s</option>',
                    esc_attr($value),
                    esc_html($label)
                );
            }
            $html .= '</optgroup>';
        }

        return $html;
    }

    /**
     * Get font choices
     */
    protected function get_font_choices() {
        return [
            'Theme Fonts' => [
                'var(--primary-ff)' => 'Primary Font',
                'var(--secondary-ff)' => 'Secondary Font',
            ],
            'System Fonts' => [
                'Arial, sans-serif' => 'Arial',
                'Helvetica, sans-serif' => 'Helvetica',
                'Georgia, serif' => 'Georgia',
                'Tahoma, sans-serif' => 'Tahoma',
                'Verdana, sans-serif' => 'Verdana',
                'Inter, sans-serif' => 'Inter',
                'Roboto, sans-serif' => 'Roboto',
                'Open Sans, sans-serif' => 'Open Sans',
                'Lato, sans-serif' => 'Lato',
                'Poppins, sans-serif' => 'Poppins',
            ],
            'Generic Families' => [
                'serif' => 'Serif',
                'sans-serif' => 'Sans Serif',
                'monospace' => 'Monospace',
                'cursive' => 'Cursive',
                'fantasy' => 'Fantasy'
            ],
            'CSS Defaults' => [
                '' => 'Default font',
                'inherit' => 'Inherit',
                'initial' => 'Initial',
            ]
        ];
    }

    /**
     * Get variants choices
     */
    protected function get_variant_choices() {
        return [
            '' => __('Default', $this->get_text_domain()),
            '100' => '100',
            '100italic' => '100 Italic',
            '200' => '200',
            '200italic' => '200 Italic',
            '300' => '300',
            '300italic' => '300 Italic',
            'regular' => 'Regular',
            'italic' => 'Italic',
            '500' => '500',
            '500italic' => '500 Italic',
            '600' => '600',
            '600italic' => '600 Italic',
            '700' => '700',
            '700italic' => '700 Italic',
            '800' => '800',
            '800italic' => '800 Italic',
            '900' => '900',
            '900italic' => '900 Italic'
        ];
    }

    /**
     * Get text transform choices
     */
    protected function get_text_transform_choices() {
        return [
            '' => __('Default', $this->get_text_domain()),
            'none' => __('None', $this->get_text_domain()),
            'capitalize' => __('Capitalize', $this->get_text_domain()),
            'uppercase' => __('Uppercase', $this->get_text_domain()),
            'lowercase' => __('Lowercase', $this->get_text_domain()),
            'initial' => __('Initial', $this->get_text_domain()),
            'inherit' => __('Inherit', $this->get_text_domain()),
        ];
    }

    /**
     * Get fonts select HTML
     */
    private function get_fonts_select_html($setting_id) {
        $fonts = $this->get_fonts_list();
        $html = '<select id="kirki-typography-font-family-' . esc_attr($setting_id) . '" class="font-family-select">';
        
        foreach ($fonts as $group => $group_fonts) {
            $html .= '<optgroup label="' . esc_attr($group) . '">';
            foreach ($group_fonts as $font => $label) {
                $html .= '<option value="' . esc_attr($font) . '">' . esc_html($label) . '</option>';
            }
            $html .= '</optgroup>';
        }
        
        $html .= '</select>';
        return $html;
    }

    /**
     * Get text transform select HTML
     */
    private function get_text_transform_select_html($setting_id) {
        $options = [
            '' => __('Default', $this->get_text_domain()),
            'none' => __('None', $this->get_text_domain()),
            'capitalize' => __('Capitalize', $this->get_text_domain()),
            'uppercase' => __('Uppercase', $this->get_text_domain()),
            'lowercase' => __('Lowercase', $this->get_text_domain())
        ];

        $html = '<select id="kirki-typography-text-transform-' . esc_attr($setting_id) . '" class="text-transform-select">';
        foreach ($options as $value => $label) {
            $html .= '<option value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * Get fonts list
     */
    private function get_fonts_list() {
        // Add your fonts list here
        return [
            'System Fonts' => [
                'Arial' => 'Arial',
                'Georgia' => 'Georgia',
                'Helvetica' => 'Helvetica',
                // Add more system fonts
            ],
            'Google Fonts' => [
                'Roboto' => 'Roboto',
                'Open Sans' => 'Open Sans',
                'Lato' => 'Lato',
                // Add more Google fonts
            ]
        ];
    }
}