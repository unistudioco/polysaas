<?php
namespace Polysaas\Settings;

use Polysaas\Core\Config;
use Polysaas\Core\Hooks;
use Polysaas\Core\Theme_Settings;

class General_Settings {
    public function __construct() {
        Hooks::add_action('register_theme_settings', [$this, 'register_settings']);
    }

    public function register_settings($settings) {
        $page_id = Config::prefix('general');
        
        // Add General Settings Page
        $settings->add_settings_page('general', [
            'title' => sprintf(__('%s', Config::get('text_domain')), Config::get('name')),
            'menu_title' => __('Dashboard', Config::get('text_domain')),
            'description' => __('Through this control panel you can set up the template to suit your requirements.', Config::get('text_domain')),
        ]);
    
        // Add Tabs
        $settings->add_tab($page_id, 'general_settings', [
            'title' => __('General', Config::get('text_domain')),
            'priority' => 10,
        ]);
    
        $settings->add_tab($page_id, 'header', [
            'title' => __('Header', Config::get('text_domain')),
            'priority' => 30
        ]);
    
        $settings->add_tab($page_id, 'footer', [
            'title' => __('Footer', Config::get('text_domain')),
            'priority' => 30
        ]);
        
        // Add General Section
        $settings->add_section('general', [
            'title' => __('General Settings', Config::get('text_domain')),
            'description' => __('Customize your site general settings.', Config::get('text_domain')),
            'page' => $page_id,
            'tab' => Config::prefix('general_settings')
        ]);
    
        // Add Fields under Global Sections tab
        $settings->add_field('general', 'body_custom_font', [
            'type' => 'select',
            'label' => __('Body Custom Font', Config::get('text_domain')),
            'description' => __('Select your site body font.', Config::get('text_domain')),
            'page' => $page_id,
            'default' => 'default',
            'choices' => [
                'default' => __('Default', Config::get('text_domain')),
                'inter' => __('Inter', Config::get('text_domain')),
                'roboto' => __('Roboto', Config::get('text_domain')),
                'work_sans' => __('Work Sans', Config::get('text_domain')),
                'montserrat' => __('Montserrat', Config::get('text_domain')),
                'lato' => __('Lato', Config::get('text_domain')),
            ]
        ]);
    
        // Add Header Section
        $settings->add_section('header', [
            'title' => __('Header Settings', Config::get('text_domain')),
            'description' => __('Customize your header settings.', Config::get('text_domain')),
            'page' => $page_id,
            'tab' => Config::prefix('header')
        ]);
    
        // Add Fields under Header tab
        $settings->add_field('header', 'header_layout', [
            'type' => 'select',
            'label' => __('Header Layout', Config::get('text_domain')),
            'description' => __('Choose your header layout.', Config::get('text_domain')),
            'page' => $page_id,
            'default' => 'default',
            'choices' => [
                'default' => __('Default Layout', Config::get('text_domain')),
                'centered' => __('Centered Layout', Config::get('text_domain')),
                'minimal' => __('Minimal Layout', Config::get('text_domain'))
            ]
        ]);
    
        // Add Header Section
        $settings->add_section('header', [
            'title' => __('Header Settings', Config::get('text_domain')),
            'description' => __('Customize your header settings.', Config::get('text_domain')),
            'page' => $page_id,
            'tab' => Config::prefix('header')
        ]);
    
        // Add Fields under Header tab
        $settings->add_field('header', 'header_layout', [
            'type' => 'select',
            'label' => __('Header Layout', Config::get('text_domain')),
            'description' => __('Choose your header layout.', Config::get('text_domain')),
            'page' => $page_id,
            'default' => 'default',
            'choices' => [
                'default' => __('Default Layout', Config::get('text_domain')),
                'centered' => __('Centered Layout', Config::get('text_domain')),
                'minimal' => __('Minimal Layout', Config::get('text_domain'))
            ]
        ]);

        // Add Footer Section
        $settings->add_section('footer', [
            'title' => __('Footer Settings', Config::get('text_domain')),
            'description' => __('Customize your footer settings.', Config::get('text_domain')),
            'page' => $page_id,
            'tab' => Config::prefix('footer')
        ]);
    
        // Add Fields under Footer tab
        $settings->add_field('footer', 'header_layout', [
            'type' => 'select',
            'label' => __('Footer Layout', Config::get('text_domain')),
            'description' => __('Choose your footer layout.', Config::get('text_domain')),
            'page' => $page_id,
            'default' => 'default',
            'choices' => [
                'default' => __('Default Layout', Config::get('text_domain')),
                'centered' => __('Centered Layout', Config::get('text_domain')),
                'minimal' => __('Minimal Layout', Config::get('text_domain'))
            ]
        ]);
    }
}