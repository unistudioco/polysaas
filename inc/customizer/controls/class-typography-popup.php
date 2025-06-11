<?php
/**
 * Typography with Popup control
 */
namespace Kirki\Control;

use Kirki\Control\Base;
use Kirki\Module\Webfonts\Fonts;
use Kirki\Module\Webfonts\Google;
use Polysaas\Core\Config;

if (!class_exists('WP_Customize_Control')) {
    return;
}

/**
 * Typography control with popup and responsive options
 */
class TypographyPopup extends Base {
    /**
     * The control type.
     *
     * @access public
     * @var string
     */
    public $type = 'polysaas-typography-popup';
    
    /**
     * Used to automatically generate all CSS output.
     *
     * @access public
     * @var array
     */
    public $output = [];
    
    /**
     * Data type
     *
     * @access public
     * @var string
     */
    public $option_type = 'theme_mod';
    
    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @access public
     */
    public function to_json() {
        parent::to_json();
        
        // Create a local variable to avoid modifying $this->json directly
        $json = $this->json;
        
        // Make sure the default value is properly parsed
        if (is_string($json['default'])) {
            $json['default'] = html_entity_decode($json['default']);
        }
        $json['output']  = $this->output;
        $json['value']   = $this->value();
        $json['choices'] = $this->choices;
        $json['link']    = $this->get_link();
        $json['inputAttrs'] = '';
        
        foreach ($this->input_attrs as $attr => $value) {
            $json['inputAttrs'] .= $attr . '="' . esc_attr($value) . '" ';
        }
        
        // Get the available font families
        $json['fontFamilies'] = $this->get_font_families();
    
        // Google fonts information
        $json['isGoogleFont'] = function_exists('kirki_get_google_fonts');
    
        // Pass choices for Select2
        $json['fontGroups'] = [
            'standard' => esc_html__('Standard Fonts', Config::get('text_domain')),
            'google' => esc_html__('Google Fonts', Config::get('text_domain')),
            'custom' => esc_html__('Custom Fonts', Config::get('text_domain')),
        ];
        
        // Get the available font weights
        $json['fontWeights'] = [
            '' => esc_html__('Default', Config::get('text_domain')),
            '300' => esc_html__('Light (300)', Config::get('text_domain')),
            '400' => esc_html__('Regular (400)', Config::get('text_domain')),
            '500' => esc_html__('Medium (500)', Config::get('text_domain')),
            '600' => esc_html__('Semi Bold (600)', Config::get('text_domain')),
            '700' => esc_html__('Bold (700)', Config::get('text_domain')),
            '800' => esc_html__('Extra Bold (800)', Config::get('text_domain')),
        ];
        
        // Get the available font styles
        $json['fontStyles'] = [
            'normal' => esc_html__('Normal', Config::get('text_domain')),
            'italic' => esc_html__('Italic', Config::get('text_domain')),
        ];
        
        // Get the available text transforms
        $json['textTransforms'] = [
            'none'      => esc_html__('None', Config::get('text_domain')),
            'uppercase' => esc_html__('Uppercase', Config::get('text_domain')),
            'lowercase' => esc_html__('Lowercase', Config::get('text_domain')),
            'capitalize'=> esc_html__('Capitalize', Config::get('text_domain')),
        ];
        
        // Update the json property with our modified values
        $this->json = $json;
    }
    
    /**
     * Enqueue control related scripts and styles.
     */
    public function enqueue() {
        parent::enqueue();
    
        // Enqueue Select2
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], '4.0.13', true);
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', [], '4.0.13');
        
        wp_enqueue_style(
            Config::prefix('typography-popup'),
            get_template_directory_uri() . '/assets/css/admin/typography-popup.css',
            [],
            Config::get('version')
        );
        
        wp_enqueue_script(
            Config::prefix('typography-popup'),
            get_template_directory_uri() . '/assets/js/admin/typography-popup.js',
            ['jquery', 'customize-controls'],
            Config::get('version'),
            true
        );

        // Pass Google Fonts data to JavaScript
        if (class_exists('\Kirki\Modules\Webfonts\Fonts')) {
            $google_fonts = Fonts::get_google_fonts();
            
            // Prepare a simplified version for JS
            $fonts_for_js = [];
            
            foreach ($google_fonts as $font => $data) {
                if (count($fonts_for_js) >= 50) {
                    break; // Limit to 50 fonts for performance
                }
                
                $fonts_for_js[$font] = [
                    'label' => $font,
                    'variants' => $data['variants'],
                    'category' => isset($data['category']) ? $data['category'] : '',
                ];
            }
            
            wp_localize_script('polysaas-typography-popup', 'polysaasGoogleFonts', [
                'fonts' => $fonts_for_js,
                'standardFonts' => [
                    'Arial, Helvetica, sans-serif' => 'Arial',
                    'Courier, monospace' => 'Courier',
                    'Georgia, serif' => 'Georgia',
                    'Helvetica, Arial, sans-serif' => 'Helvetica',
                    'Impact, Charcoal, sans-serif' => 'Impact',
                    'Tahoma, Geneva, sans-serif' => 'Tahoma',
                    'Times New Roman, Times, serif' => 'Times New Roman',
                    'Trebuchet MS, Helvetica, sans-serif' => 'Trebuchet MS',
                    'Verdana, Geneva, sans-serif' => 'Verdana',
                    'inherit' => 'Inherit',
                ],
                'customFonts' => isset($this->choices['fonts']['custom']) ? $this->choices['fonts']['custom'] : [],
            ]);
        }
    }
    
    /**
     * Get all available font families
     */
    private function get_font_families() {
        $font_families = [];
        
        // Add default font options
        $font_families[''] = esc_html__('Default', Config::get('text_domain'));

        // Add theme fonts
        $theme_fonts = [
            'var(--uc-font-primary)' => 'Primary Font (Theme)',
            'var(--uc-font-secondary)' => 'Secondary Font (Theme)',
        ];
        
        foreach ($theme_fonts as $stack => $label) {
            $font_families[$stack] = $label;
        }
        
        
        // Get Elementor Global fonts from Elementor if available
        if (class_exists('\Elementor\Plugin')) {
            $elementor_fonts = [
                'var(--e-global-typography-primary-font-family)' => 'Primary (Elementor)',
                'var(--e-global-typography-secondary-font-family)' => 'Secondary (Elementor)',
                'var(--e-global-typography-text-font-family)' => 'Text (Elementor)',
                'var(--e-global-typography-accent-font-family)' => 'Accent (Elementor)',
            ];
            foreach ($elementor_fonts as $stack => $label) {
                $font_families[$stack] = $label;
            }
        }
        
        // Add custom fonts if defined in choices
        if (isset($this->choices['fonts']['custom']) && is_array($this->choices['fonts']['custom'])) {
            foreach ($this->choices['fonts']['custom'] as $font_family => $font_label) {
                $font_families[$font_family] = $font_label;
            }
        }
        
        // Get Google fonts from Kirki if available
        if (class_exists('\Kirki\Modules\Webfonts\Fonts')) {
            $google_fonts = Fonts::get_google_fonts();
            
            // Limit to top 50 popular fonts
            $popular_fonts = array_slice($google_fonts, 0, 50);
            
            foreach ($popular_fonts as $font_family => $font_data) {
                $font_families[$font_family] = $font_family;
            }
        }
        
        // Add default font options
        $font_families['inherit'] = esc_html__('Inherit', Config::get('text_domain'));
        $font_families['initial'] = esc_html__('Initial', Config::get('text_domain'));
        $font_families['unset'] = esc_html__('Unset', Config::get('text_domain'));
        
        return $font_families;
    }
    
    /**
     * Control content template
     */
    protected function content_template() {
        ?>
        <# const savedValue = data.value ? JSON.parse(data.value) : {}; #>
        
        <div class="rey-control-wrap">
            <label>
                <# if (data.label) { #>
                    <span class="customize-control-title rey-control-title">{{{ data.label }}}</span>
                <# } #>
                <# if (data.description) { #>
                    <span class="description customize-control-description">{{{ data.description }}}</span>
                <# } #>
            </label>
            
            <div class="customize-control-content">
                <!-- Typography Button -->
                <div class="rey-typo-controls">
                    <span class="rey-typo-reset">
                        <span class="dashicons dashicons-image-rotate"></span>
                    </span>
                    <div class="rey-typoBtn">
                        <span class="dashicons dashicons-edit"></span>
                        <div class="rey-typoBtn-summary">
                            <# const desktop = savedValue.desktop || {}; #>
                            <span class="font-family-label">{{ desktop.font_family || 'Default' }}</span>
                            <span class="font-size-label">{{ desktop.font_size || '' }}</span>
                            <span class="font-weight-label">{{ desktop.font_weight || '' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Typography Popup -->
                <div class="rey-typo-popup">
                    <div class="rey-typo-popup-header">
                        <h3 class="rey-typo-popup-title">{{{ data.label }}}</h3>
                        <div class="rey-responsive-handlers">
                            <span data-device="desktop" class="active"><i class="dashicons dashicons-desktop"></i></span>
                            <span data-device="tablet"><i class="dashicons dashicons-tablet"></i></span>
                            <span data-device="mobile"><i class="dashicons dashicons-smartphone"></i></span>
                        </div>
                        <span class="rey-typo-popClose">
                            <svg aria-hidden="true" role="img" class="rey-icon rey-icon-close" viewBox="0 0 110 110">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="square">
                                    <path d="M4.79541854,4.29541854 L104.945498,104.445498 L4.79541854,4.29541854 Z" stroke="currentColor" stroke-width="var(--stroke-width, 12px)"></path>
                                    <path d="M4.79541854,104.704581 L104.945498,4.55450209 L4.79541854,104.704581 Z" stroke="currentColor" stroke-width="var(--stroke-width, 12px)"></path>
                                </g>
                            </svg>
                        </span>
                    </div>
                    
                    <!-- Typography Options - Desktop -->
                    <div class="typography-font-options active" data-device="desktop">
                        <# const desktopValues = savedValue.desktop || {}; #>
                        
                        <div class="typography-option-group font-family">
                            <label><?php esc_html_e('Font Family', Config::get('text_domain')); ?></label>
                            <select>
                                <# _.each(data.fontFamilies, function(label, family) { #>
                                    <option value="{{ family }}" <# if (desktopValues.font_family === family) { #>selected<# } #>>{{ label }}</option>
                                <# }); #>
                            </select>
                        </div>
                        
                        <div class="typography-responsive-row">
                            <div class="typography-option-group font-weight">
                                <label><?php esc_html_e('Weight', Config::get('text_domain')); ?></label>
                                <select>
                                    <option value=""><?php esc_html_e('Default', Config::get('text_domain')); ?></option>
                                    <# _.each(data.fontWeights, function(label, weight) { #>
                                        <option value="{{ weight }}" <# if (desktopValues.font_weight === weight) { #>selected<# } #>>{{ label }}</option>
                                    <# }); #>
                                </select>
                            </div>

                            <div class="typography-option-group font-size">
                                <label><?php esc_html_e('Size', Config::get('text_domain')); ?> <span class="unit">px</span></label>
                                <input type="number" min="0" step="1" value="{{ desktopValues.font_size || '' }}">
                            </div>
                        </div>
                        
                        <div class="typography-responsive-row">
                            <div class="typography-option-group font-style">
                                <label><?php esc_html_e('Style', Config::get('text_domain')); ?></label>
                                <select>
                                    <option value=""><?php esc_html_e('Default', Config::get('text_domain')); ?></option>
                                    <# _.each(data.fontStyles, function(label, style) { #>
                                        <option value="{{ style }}" <# if (desktopValues.font_style === style) { #>selected<# } #>>{{ label }}</option>
                                    <# }); #>
                                </select>
                            </div>
                            
                            <div class="typography-option-group line-height">
                                <label><?php esc_html_e('Line Height', Config::get('text_domain')); ?> <span class="unit">em</span></label>
                                <input type="number" min="0" step="0.1" value="{{ desktopValues.line_height || '' }}">
                            </div>
                        </div>
                        
                        <div class="typography-responsive-row">
                            <div class="typography-option-group text-transform">
                                <label><?php esc_html_e('Transform', Config::get('text_domain')); ?></label>
                                <select>
                                    <option value=""><?php esc_html_e('Default', Config::get('text_domain')); ?></option>
                                    <# _.each(data.textTransforms, function(label, transform) { #>
                                        <option value="{{ transform }}" <# if (desktopValues.text_transform === transform) { #>selected<# } #>>{{ label }}</option>
                                    <# }); #>
                                </select>
                            </div>
                            <div class="typography-option-group letter-spacing">
                                <label><?php esc_html_e('Letter Spacing', Config::get('text_domain')); ?> <span class="unit">px</span></label>
                                <input type="number" min="-5" max="5" step="0.1" value="{{ desktopValues.letter_spacing || '' }}">
                            </div>
                        </div>

                    </div>
                    
                    <!-- Typography Options - Tablet -->
                    <div class="typography-font-options" data-device="tablet">
                        <# const tabletValues = savedValue.tablet || {}; #>
                        
                        <div class="typography-device-info">
                            <p class="description"><?php esc_html_e('These are only the responsive properties for tablet devices other properties will inherit from desktop settings.', Config::get('text_domain')); ?></p>
                        </div>

                        <div class="typography-option-group font-size">
                            <label><?php esc_html_e('Size', Config::get('text_domain')); ?> <span class="unit">px</span></label>
                            <input type="number" min="0" step="1" value="{{ tabletValues.font_size || '' }}">
                        </div>
                        
                        <div class="typography-responsive-row">
                            <div class="typography-option-group line-height">
                                <label><?php esc_html_e('Line Height', Config::get('text_domain')); ?> <span class="unit">em</span></label>
                                <input type="number" min="0" step="0.1" value="{{ tabletValues.line_height || '' }}">
                            </div>
                            <div class="typography-option-group letter-spacing">
                                <label><?php esc_html_e('Letter Spacing', Config::get('text_domain')); ?> <span class="unit">px</span></label>
                                <input type="number" min="-5" max="5" step="0.1" value="{{ tabletValues.letter_spacing || '' }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Typography Options - Mobile -->
                    <div class="typography-font-options" data-device="mobile">
                        <# const mobileValues = savedValue.mobile || {}; #>
                        
                        <div class="typography-device-info">
                            <p class="description"><?php esc_html_e('These are only the responsive properties for mobile devices other properties will inherit from desktop settings.', Config::get('text_domain')); ?></p>
                        </div>

                        <div class="typography-option-group font-size">
                            <label><?php esc_html_e('Size', Config::get('text_domain')); ?> <span class="unit">px</span></label>
                            <input type="number" min="0" step="1" value="{{ mobileValues.font_size || '' }}">
                        </div>
                        
                        <div class="typography-responsive-row">
                            <div class="typography-option-group line-height">
                                <label><?php esc_html_e('Line Height', Config::get('text_domain')); ?> <span class="unit">em</span></label>
                                <input type="number" min="0" step="0.1" value="{{ mobileValues.line_height || '' }}">
                            </div>
                            <div class="typography-option-group letter-spacing">
                                <label><?php esc_html_e('Letter Spacing', Config::get('text_domain')); ?> <span class="unit">px</span></label>
                                <input type="number" min="-5" max="5" step="0.1" value="{{ mobileValues.letter_spacing || '' }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden Input to Store Typography Data -->
                <input
                    type="hidden"
                    class="rey-typography-control"
                    value="{{ data.value }}"
                    data-default="{{ data.default }}"
                    {{{ data.link }}}
                />
                <!-- Default values storage -->
                <input 
                    type="hidden"
                    class="rey-typography-default-value"
                    value="{{ data.default }}"
                />
            </div>
        </div>
        <?php
    }
}