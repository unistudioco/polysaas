<?php
/**
 * Color with Elementor globals control
 */
namespace Kirki\Control;

use Kirki\Control\Base;
use Polysaas\Core\Config;

if (!class_exists('WP_Customize_Control')) {
    return;
}

/**
 * Color control with Elementor integration
 */
class ColorElementor extends Base {
    /**
     * The control type.
     *
     * @access public
     * @var string
     */
    public $type = 'polysaas-color-elementor';
    
    /**
     * Palette
     *
     * @access public
     * @var bool
     */
    public $palette = true;
    
    /**
     * Mode
     *
     * @access public
     * @var string
     */
    public $mode = 'full';
    
    /**
     * Refresh the parameters passed to the JavaScript via JSON.
     *
     * @access public
     */
    public function to_json() {
        parent::to_json();
        
        $this->json['palette'] = $this->palette;
        $this->json['choices']['alpha'] = (isset($this->choices['alpha']) && $this->choices['alpha']) ? 'true' : 'false';
        $this->json['mode'] = $this->mode;
        $this->json['inputAttrs'] = '';
        $this->json['default'] = $this->settings['default']->default;
    }
    
    /**
     * Enqueue control related scripts and styles.
     */
    public function enqueue() {
        parent::enqueue();
        
        wp_enqueue_style(
            'polysaas-color-elementor',
            get_template_directory_uri() . '/assets/css/admin/kirki-color-elementor.css',
            [],
            Config::get('version')
        );
        
        wp_enqueue_script(
            'polysaas-color-elementor',
            get_template_directory_uri() . '/assets/js/admin/kirki-color-elementor.js',
            ['jquery', 'customize-controls', 'wp-color-picker'],
            Config::get('version'),
            true
        );
    }
    
    /**
     * Get Elementor global colors
     */
    private function get_elementor_colors() {
        $colors = [];
        
        if (class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance->kits_manager)) {
            $kits_manager = \Elementor\Plugin::$instance->kits_manager;
            
            // Get system colors
            $system_colors = $kits_manager->get_current_settings('system_colors');
            if (!empty($system_colors)) {
                foreach ($system_colors as $color) {
                    $colors[] = [
                        'id' => $color['_id'],
                        'title' => $color['title'],
                        'value' => $color['color'],
                        'var' => sprintf('var(--e-global-color-%s)', $color['_id']),
                        'type' => 'system'
                    ];
                }
            }
            
            // Get custom colors
            $custom_colors = $kits_manager->get_current_settings('custom_colors');
            if (!empty($custom_colors)) {
                foreach ($custom_colors as $color) {
                    $colors[] = [
                        'id' => $color['_id'],
                        'title' => $color['title'],
                        'value' => $color['color'],
                        'var' => sprintf('var(--e-global-color-%s)', $color['_id']),
                        'type' => 'custom'
                    ];
                }
            }
        }
        
        return $colors;
    }
    
    /**
     * Control template
     */
    protected function content_template() {
        // Get Elementor colors
        $colors = $this->get_elementor_colors();
        ?>
        <#
        data = _.defaults(data, {
            label: '',
            description: '',
            mode: 'full',
            inputAttrs: '',
            'data-default-color': data['data-default-color'] ? data['data-default-color'] : '',
            'data-alpha': data['data-alpha'] ? data['data-alpha'] : false,
            value: '',
            'data-id': ''
        });
        #>
        
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
                <?php if (!empty($colors)) : ?>
                <div class="rey-colorGlobal">
                    <span class="dashicons dashicons-admin-site-alt3"></span>
                </div>
                
                <div class="rey-colorGlobal-pop">
                    <span class="rey-colorGlobal-popClose">
                        <svg aria-hidden="true" role="img" class="rey-icon rey-icon-close" viewBox="0 0 110 110">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="square">
                                <path d="M4.79541854,4.29541854 L104.945498,104.445498 L4.79541854,4.29541854 Z" stroke="currentColor" stroke-width="var(--stroke-width, 12px)"></path>
                                <path d="M4.79541854,104.704581 L104.945498,4.55450209 L4.79541854,104.704581 Z" stroke="currentColor" stroke-width="var(--stroke-width, 12px)"></path>
                            </g>
                        </svg>
                    </span>
                    <h3><?php esc_html_e('Global Colors (Elementor)', Config::get('text_domain')); ?></h3>
                    <div class="__list">
                        <?php foreach ($colors as $c) : ?>
                        <div class="__item" data-color-id="<?php echo esc_attr($c['id']); ?>" data-color-var="<?php echo esc_attr($c['var']); ?>" data-color="<?php echo esc_attr($c['value']); ?>">
                            <span class="__color" style="background-color:<?php echo esc_attr($c['value']); ?>"></span>
                            <span class="__title"><?php echo esc_html($c['title']); ?></span>
                            <span class="__hex"><?php echo esc_html($c['value']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="__tip"><?php 
                        printf(
                            __('These colors are pre-defined in Elementor Site Settings. <a href="%s" target="_blank">Learn more</a>.', Config::get('text_domain')), 
                            'https://elementor.com/help/global-layout-settings/'
                        ); 
                    ?></div>
                </div>
                <?php endif; ?>
                
                <!-- Just the input field - wp-color-picker will create the button and UI -->
                <input
                    type="text"
                    data-type="{{ data.mode }}"
                    {{{ data.inputAttrs }}}
                    data-default-color="{{ data.default }}"
                    data-alpha="{{ data['data-alpha'] }}"
                    value="{{ data.value }}"
                    data-id="{{ data['data-id'] }}"
                    class="rey-color-control"
                    {{{ data.link }}}
                />
            </div>
        </div>
        <?php
    }
}