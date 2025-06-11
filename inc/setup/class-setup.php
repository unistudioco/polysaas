<?php
namespace Polysaas\Setup;

use Polysaas\Core\Config;

/**
 * Theme Setup Class
 *
 * @package Polysaas
 */
class Setup {
    /**
     * Register default hooks and actions for WordPress
     */
    public function register() {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('after_setup_theme', [$this, 'content_width'], 0);
        add_action('widgets_init', [$this, 'widgets']);
    }

    /**
     * Theme setup
     */
    public function setup() {
        // Load theme text domain
        load_theme_textdomain(
            Config::get('text_domain'),
            get_template_directory() . '/languages'
        );

        // Add default posts and comments RSS feed links to head
        add_theme_support('automatic-feed-links');

        // Let WordPress manage the document title
        add_theme_support('title-tag');

        // Enable support for Post Thumbnails
        add_theme_support('post-thumbnails');

        // Register nav menus
        register_nav_menus([
            Config::prefix('primary')   => esc_html__('Primary Menu', Config::get('text_domain')),
            Config::prefix('mobile')    => esc_html__('Mobile Menu', Config::get('text_domain')),
            Config::prefix('footer')    => esc_html__('Footer Menu', Config::get('text_domain')),
        ]);

        // Switch default core markup to output valid HTML5
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ]);

        // Add theme support for selective refresh for widgets
        add_theme_support('customize-selective-refresh-widgets');

        // Add support for custom logo
        add_theme_support('custom-logo', [
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        ]);

        // Add support for Elementor
        add_theme_support('elementor');
    }

    /**
     * Set the content width in pixels
     */
    public function content_width() {
        $GLOBALS['content_width'] = apply_filters(
            Config::prefix('content_width'),
            640
        );
    }

    /**
     * Register widget areas
     */
    public function widgets() {
        register_sidebar([
            'name'          => esc_html__('Sidebar', Config::get('text_domain')),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', Config::get('text_domain')),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);
    }
}