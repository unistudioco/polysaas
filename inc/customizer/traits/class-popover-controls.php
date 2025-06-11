<?php
namespace Polysaas\Customizer\Traits;

use Polysaas\Core\Config;

/**
 * Popover Controls Trait
 * Adds popover functionality to control classes
 *
 * @package Polysaas
 */
trait Popover_Controls {
    /**
     * Current popover group ID
     */
    protected $current_group_id = '';
    
    /**
     * Start a popover group
     * 
     * @param array $args Popover arguments
     */
    protected function start_popover($args = []) {
        if (!isset($args['settings'])) {
            return;
        }

        // Generate a unique group ID if not provided
        if (!isset($args['group_id'])) {
            $args['group_id'] = 'popover_' . substr(md5($args['settings']), 0, 8);
        }

        // Store the current group ID
        $this->current_popover_group = $args['group_id'];

        // Set up default arguments
        $defaults = [
            'label' => '',
            'is_open' => false,
            'icon' => 'dashicons-admin-generic',
            'icon_position' => 'before',
            'description_text' => '',
            'section' => $this->section,
            'priority' => $this->priority,
        ];

        // Merge with provided args
        $args = array_merge($defaults, $args);

        // Create the popover HTML using the template
        $popover_html = sprintf(
            '<div class="polysaas-popover-control %1$s" data-group="%2$s">
                <div class="polysaas-popover-toggle">
                    %3$s
                    <span class="customize-control-title">%4$s</span>
                    <button class="popover-toggle" aria-expanded="%5$s">
                        <span class="screen-reader-text">%6$s</span>
                        <span class="toggle-indicator" aria-hidden="true"></span>
                    </button>
                </div>
                %7$s
            </div>',
            $args['is_open'] ? 'is-open' : '',
            esc_attr($args['group_id']),
            ($args['icon_position'] === 'before' && $args['icon']) ? '<i class="' . esc_attr($args['icon']) . '"></i>' : '',
            esc_html($args['label']),
            $args['is_open'] ? 'true' : 'false',
            esc_html__('Toggle', 'polysaas'),
            !empty($args['description_text']) ? '<span class="description customize-control-description">' . esc_html($args['description_text']) . '</span>' : ''
        );

        // Add the control as a Custom control
        $this->add_control('Custom', [
            'settings' => $args['settings'],
            'section'  => $args['section'],
            'default'  => $popover_html,
            'priority' => $args['priority']
        ]);

        // Add tab if provided
        if (isset($args['tab'])) {
            $this->set_current_tab($args['tab']);
        }

        // Increment priority for subsequent controls
        $this->priority += 5;
    }

    /**
     * End a popover group
     */
    protected function end_popover() {
        // Reset current group ID
        $this->current_group_id = '';
    }

    /**
     * Add a control inside a popover group
     * 
     * @param string $type The control type
     * @param array  $args The control arguments
     * @param bool   $pro  Whether this is a pro field
     */
    protected function add_popover_control($type, $args, $pro = false) {
        // Add group ID to wrapper attributes if we're inside a popover group
        if (!empty($this->current_group_id)) {
            if (!isset($args['wrapper_attrs'])) {
                $args['wrapper_attrs'] = ['class' => 'popover-control-item', 'data-group' => $this->current_group_id];
            } else {
                $existing_class = isset($args['wrapper_attrs']['class']) ? $args['wrapper_attrs']['class'] . ' ' : '';
                $args['wrapper_attrs']['class'] = $existing_class . 'popover-control-item';
                $args['wrapper_attrs']['data-group'] = $this->current_group_id;
            }
        }
        
        // Use the standard add_control method
        $this->add_control($type, $args, $pro);
    }

    /**
     * Add a pro control inside a popover group
     */
    protected function add_popover_pro_control($type, $args) {
        $this->add_popover_control($type, $args, true);
    }
}