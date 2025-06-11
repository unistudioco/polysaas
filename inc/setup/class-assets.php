<?php
namespace Polysaas\Setup;

/**
 * Asset Definitions
 *
 * @package Polysaas
 */
class Assets {
    /**
     * Get style definitions
     */
    public static function get_styles() {
        return [
            'fonts' => [
                'src'     => 'assets/css/fonts.css',
                'version' => THEME_VERSION,
            ],
            'swiper' => [
                'src'     => 'assets/css/swiper-bundle.min.css',
                'version' => THEME_VERSION,
            ],
            'icons' => [
                'src'     => 'assets/css/unicons.min.css',
                'version' => THEME_VERSION,
            ],
            'unicore' => [
                'src'     => 'assets/css/unicore.min.css',
                'version' => THEME_VERSION,
            ],
            'widgets' => [
                'src'     => 'assets/css/frontend/widgets.css',
                'version' => THEME_VERSION,
            ],
            'main' => [
                'src'     => 'style.css',
                'version' => THEME_VERSION,
            ],
            'theme-core' => [
                'src'     => 'assets/css/frontend/theme.css',
                'version' => THEME_VERSION,
            ],
            'blog-layouts' => [
                'src'     => 'assets/css/frontend/blog.css',
                'version' => THEME_VERSION,
                'condition' => function() {
                    return is_home() || is_category() || is_tag() || is_author() || is_date() || is_singular('post');
                },
            ],
            'comments' => [
                'src'     => 'assets/css/frontend/comments.css',
                'version' => THEME_VERSION,
                'condition' => function() {
                    return is_singular('post') && (comments_open() || get_comments_number() > 0);
                },
            ],
            'theme-woo' => [
                'src'     => 'assets/css/frontend/theme-woo.css',
                'version' => THEME_VERSION,
                'condition' => function() {
                    // Check if WooCommerce is active first
                    if (!class_exists('WooCommerce')) {
                        return false;
                    }
                    return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
                },
            ],
        ];
    }

    /**
     * Get script definitions
     */
    public static function get_scripts() {
        return [
            'navigation' => [
                'src'       => 'assets/js/custom/navigation.js',
                'deps'      => [],
                'version'   => THEME_VERSION,
                'in_footer' => true,
            ],
            'widgets' => [
                'src'       => 'assets/js/custom/wp-widgets.js',
                'deps'      => [],
                'version'   => THEME_VERSION,
                'in_footer' => true,
            ],
            'unicore' => [
                'src'       => 'assets/js/uni-core-bundle.min.js',
                'deps'      => [],
                'version'   => THEME_VERSION,
                'in_footer' => true,
            ],
            'app-head' => [
                'src'       => 'assets/js/app-head-bs.js',
                'deps'      => [],
                'version'   => THEME_VERSION,
                'in_footer' => true,
            ],
            'app' => [
                'src'       => 'assets/js/app.js',
                'deps'      => [],
                'version'   => THEME_VERSION,
                'in_footer' => true,
            ],
            'theme-woo' => [
                'src'     => 'assets/js/frontend/theme-woo.js',
                'deps'      => [],
                'version' => THEME_VERSION,
                'in_footer' => true,
                'condition' => function() {
                    // Check if WooCommerce is active first
                    if (!class_exists('WooCommerce')) {
                        return false;
                    }
                    return is_woocommerce();
                },
            ],
        ];
    }

    /**
     * Get admin asset definitions
     */
    public static function get_admin_assets() {
        return [
            'styles' => [
                'acf-fields' => [
                    'src'     => 'assets/css/admin/acf-fields.css',
                    'version' => THEME_VERSION,
                    'condition' => function() {
                        return is_admin() && !is_customize_preview();
                    },
                ],
                'theme-settings' => [
                    'src'     => 'assets/css/admin/theme-settings.css',
                    'version' => THEME_VERSION,
                    'condition' => function() {
                        return is_admin() && !is_customize_preview();
                    },
                ],
                'customizer' => [
                    'src'     => 'assets/css/admin/customizer-controls.css',
                    'version' => THEME_VERSION,
                    'deps'    => ['wp-admin', 'customize-controls'],
                    'condition' => function() {
                        return is_customize_preview() && class_exists('\Kirki');
                    },
                ],
                'customizer-icons' => [
                    'src'     => 'assets/css/unicons.min.css',
                    'version' => THEME_VERSION,
                    'deps'    => ['wp-admin', 'customize-controls'],
                    'condition' => function() {
                        return is_customize_preview();
                    },
                ],
                'kirki-elementor-control' => [
                    'src'     => 'assets/css/admin/kirki-elementor-colors.css',
                    'version' => THEME_VERSION,
                    'deps'    => ['wp-admin', 'customize-controls'],
                    'condition' => function() {
                        return is_customize_preview();
                    },
                ],
            ],
            'scripts' => [
                'kirki-elementor-control' => [
                    'src'       => 'assets/js/admin/kirki-elementor-colors.js',
                    'deps'      => ['jquery', 'customize-controls'],
                    'version'   => THEME_VERSION,
                    'in_footer' => true,
                    'condition' => function() {
                        return is_customize_preview();
                    },
                ],
            ],
        ];
    }

    /**
     * Get editor asset definitions
     */
    public static function get_editor_assets() {
        return [
            'styles' => [
                'editor' => [
                    'src'     => 'assets/css/editor.css',
                    'version' => THEME_VERSION,
                    'condition' => function() {
                        return is_admin() && !is_customize_preview();
                    },
                ],
            ],
            'scripts' => [
                'editor' => [
                    'src'       => 'assets/js/editor.js',
                    'deps'      => ['wp-blocks', 'wp-dom'],
                    'version'   => THEME_VERSION,
                    'in_footer' => true,
                    'condition' => function() {
                        return is_admin() && !is_customize_preview();
                    },
                ],
            ],
        ];
    }

    /**
     * Get dependency definitions
     */
    public static function get_dependencies() {
        return [
            'swiper' => [
                'styles'  => ['swiper-css'],
                'scripts' => ['swiper-js'],
            ],
            'lightbox' => [
                'styles'  => ['lightbox-css'],
                'scripts' => ['lightbox-js'],
            ],
        ];
    }
}