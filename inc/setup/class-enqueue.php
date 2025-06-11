<?php
namespace Polysaas\Setup;

use Polysaas\Core\Config;

/**
 * Enqueue scripts and styles
 *
 * @package Polysaas
 */
class Enqueue {
    /**
     * Register default hooks and actions for WordPress
     */
    public function register() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('enqueue_block_editor_assets', [$this, 'editor_assets']);
    }

    /**
     * Register and enqueue styles
     */
    public function enqueue_styles() {
        $styles = Assets::get_styles();
        
        foreach ($styles as $handle => $style) {
            // Check condition
            if (isset($style['condition'])) {
                $condition = $style['condition'];
                if (is_string($condition) && function_exists($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                } elseif (is_callable($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                }
            }
            
            wp_enqueue_style(
                Config::prefix($handle),
                THEME_URI . '/' . $style['src'],
                $style['deps'] ?? [],
                $style['version']
            );
        }

        // RTL support for main theme style
        wp_style_add_data(Config::prefix('theme'), 'rtl', 'replace');
    }

    /**
     * Register and enqueue scripts
     */
    public function enqueue_scripts() {
        $scripts = Assets::get_scripts();
        
        foreach ($scripts as $handle => $script) {
            // Check condition
            if (isset($script['condition'])) {
                $condition = $script['condition'];
                if (is_string($condition) && function_exists($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                } elseif (is_callable($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                }
            }
            
            wp_enqueue_script(
                Config::prefix($handle),
                THEME_URI . '/' . $script['src'],
                $script['deps'] ?? [],
                $script['version'],
                $script['in_footer'] ?? true
            );
        }

        // Comments
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        // Localize scripts
        wp_localize_script(
            Config::prefix('app'),
            'polysaasData',
            $this->get_localization_data()
        );
    }

    /**
     * Register and enqueue admin scripts
     */
    public function admin_enqueue_scripts() {
        $admin_assets = Assets::get_admin_assets();
    
        // Admin styles
        foreach ($admin_assets['styles'] as $handle => $style) {
            // Check condition
            if (isset($style['condition'])) {
                $condition = $style['condition'];
                if (is_string($condition) && function_exists($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                } elseif (is_callable($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                }
            }
    
            wp_enqueue_style(
                Config::prefix('admin-' . $handle),
                THEME_URI . '/' . $style['src'],
                $style['deps'] ?? [],
                $style['version']
            );
        }
    
        // Admin scripts - same pattern
        foreach ($admin_assets['scripts'] as $handle => $script) {
            if (isset($script['condition'])) {
                $condition = $script['condition'];
                if (is_string($condition) && function_exists($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                } elseif (is_callable($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                }
            }
    
            wp_enqueue_script(
                Config::prefix('admin-' . $handle),
                THEME_URI . '/' . $script['src'],
                $script['deps'] ?? [],
                $script['version'],
                $script['in_footer'] ?? true
            );
        }
    }

    /**
     * Register and enqueue editor assets
     */
    public function editor_assets() {
        $editor_assets = Assets::get_editor_assets();

        // Editor styles
        foreach ($editor_assets['styles'] as $handle => $style) {
            // Check condition
            if (isset($style['condition'])) {
                $condition = $style['condition'];
                if (is_string($condition) && function_exists($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                } elseif (is_callable($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                }
            }

            wp_enqueue_style(
                Config::prefix('editor-' . $handle),
                THEME_URI . '/' . $style['src'],
                $style['deps'] ?? [],
                $style['version']
            );
        }

        // Editor scripts
        foreach ($editor_assets['scripts'] as $handle => $script) {
            if (isset($script['condition'])) {
                $condition = $script['condition'];
                if (is_string($condition) && function_exists($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                } elseif (is_callable($condition)) {
                    if (!$condition()) {
                        continue;
                    }
                }
            }
            
            wp_enqueue_script(
                Config::prefix('editor-' . $handle),
                THEME_URI . '/' . $script['src'],
                $script['deps'] ?? [],
                $script['version'],
                $script['in_footer'] ?? true
            );
        }
    }

    /**
     * Get localization data for scripts
     */
    private function get_localization_data() {
        return [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(Config::prefix('nonce')),
            'textDomain' => Config::get('text_domain'),
            'themeUri' => THEME_URI,
            'i18n' => [
                'loading' => esc_html__('Loading...', Config::get('text_domain')),
                'loadMore' => esc_html__('Load More', Config::get('text_domain')),
                'noMore' => esc_html__('No more posts to load.', Config::get('text_domain')),
            ],
        ];
    }
}