<?php
/**
 * The header for our theme
 */
use Polysaas\Core\Hooks;
use Polysaas\Core\Config;

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class('body'); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'polysaas'); ?></a>
    <?php

    if (function_exists('get_field')) {
        $body_class = get_field('body_custom_class');
        if ($body_class) {
            add_filter('body_class', function($classes) use ($body_class) {
                $classes[] = $body_class;
                return $classes;
            });
        }
    }

    // Get header type and settings
    $header_position = get_theme_mod(Config::prefix('header_position'), '_none');
    $header_sticky = get_theme_mod(Config::prefix('header_sticky_enable'), false);
    $z_index = get_theme_mod(Config::prefix('header_z_index'), 999);
    
    // Build header classes
    $header_classes = ['site-header', 'uc-header'];
    if ($header_position === '_absolute') {
        $header_classes[] = 'uc-position-absolute uc-width-1-1';
    }
    
    // Build sticky attributes
    $sticky_attrs = [];
    if ($header_sticky) {
        $sticky_params = [];
        
        // Add sticky parameters based on customizer settings
        if (get_theme_mod(Config::prefix('header_sticky_show_on_up'), false)) {
            $sticky_params[] = 'show-on-up: true';
        }
        
        // Get start position
        $start_type = get_theme_mod(Config::prefix('header_sticky_start'), '_top');
        if ($start_type === '_custom') {
            $sticky_params[] = 'start: ' . get_theme_mod(Config::prefix('header_sticky_custom_start'), 100);
        } elseif ($start_type === '_screen') {
            $sticky_params[] = 'start: !window.innerHeight';
        }
        
        // Get animation type
        $animation_type = get_theme_mod(Config::prefix('header_sticky_animation'), '_slide');
        if ($animation_type === '_slide') {
            $sticky_params[] = 'animation: uc-animation-slide-small-top';
        } elseif ($animation_type === '_fade') {
            $sticky_params[] = 'animation: uc-animation-fade';
        }
        
        // Add common parameters
        $sticky_params[] = 'sel-target: .uc-navbar-container';
        $sticky_params[] = 'cls-active: uc-navbar-sticky';
        $sticky_params[] = 'cls-inactive: uc-navbar-transparent';
        $sticky_params[] = 'end: !*';
        
        $sticky_attrs['data-uc-sticky'] = implode('; ', $sticky_params);
    }

    // Before Header Content
    Hooks::do_action('before_header_content');
    ?>
    <header id="masthead" 
            class="<?php echo esc_attr(implode(' ', $header_classes)); ?>" 
            style="z-index: <?php echo esc_attr($z_index); ?>"
            <?php foreach ($sticky_attrs as $attr => $value) : ?>
                <?php echo esc_attr($attr) . '="' . esc_attr($value) . '"'; ?>
            <?php endforeach; ?>>
        <nav class="uc-navbar-container">
            <?php 
            Hooks::do_action('header_content_display');
            ?>
        </nav>
    </header>
    <?php
    // After Header Content
    Hooks::do_action('after_header_content');
    ?>