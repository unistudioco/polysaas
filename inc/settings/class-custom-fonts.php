<?php
namespace Polysaas\Settings;

use Polysaas\Core\Config;
use Polysaas\Core\Hooks;
use Polysaas\Core\Theme_Settings;

class Custom_Fonts {
    public function __construct() {
        Hooks::add_action('register_theme_settings', [$this, 'register_settings']);
    }

    public function register_settings($settings) {
        $page_id = Config::prefix('general');
    
        // Add Tabs
        $settings->add_tab($page_id, 'custom_fonts_settings', [
            'title' => __('Custom Fonts', Config::get('text_domain')),
            'priority' => 10,
        ]);
        
        // Add custom fonts Section
        $settings->add_section('custom_fonts', [
            'title' => __('Manage Custom Fonts', Config::get('text_domain')),
            'description' => __('You can manage your custom fonts from here globaly.', Config::get('text_domain')),
            'page' => $page_id,
            'tab' => Config::prefix('custom_fonts_settings')
        ]);
    
        // Add Fields under Branding tab
        $settings->add_field('custom_fonts', 'add_custom_fonts', [
            'type' => 'select',
            'label' => __('Add new font', Config::get('text_domain')),
            'description' => __('Add custom fonts to select from Customizer and Elementor.', Config::get('text_domain')),
            'page' => $page_id,
            'default' => 'default',
            'choices' => [
                'default' => __('Upload font', Config::get('text_domain')),
                'blog' => __('Use Default', Config::get('text_domain')),
            ]
        ]);

    }
}