<?php
namespace Polysaas\Setup;

use Polysaas\Core\Config;
use Polysaas\Core\Functions;
use Polysaas\Core\Hooks;
use Polysaas\Core\Theme_Functions;
use Polysaas\Core\Template_Helper;

/**
 * Template Parts Handler
 */
class Template_Parts {
    /**
     * Register template functions and hooks
     */
    public function register() {
        $this->register_header_parts();
        $this->register_footer_parts();
        $this->register_page_header_parts();
        $this->register_breadcrumbs();
        $this->register_archive_headers();
        $this->register_content_parts();
        $this->register_pagination();
        $this->register_post_meta();
        $this->register_related_posts();
    }

    /**
     * Helper function to safely check ACF field value
     */
    private function get_acf_value($field_name, $post_id = false, $default = null) {
        if (!function_exists('get_field')) {
            return $default;
        }

        return get_field($field_name, $post_id) ?: $default;
    }

    /**
     * Helper function to safely check ACF options value
     */
    private function get_acf_option($field_name, $default = null) {
        if (!function_exists('get_field')) {
            return $default;
        }

        return get_field($field_name, 'option') ?: $default;
    }

    /**
     * Register header template parts
     */
    private function register_header_parts() {
        // Add new function to get header section ID
        Functions::register('get_header_section', function() {
            // Check for page-specific settings first
            if (is_singular()) {
                $header_type = $this->get_acf_value('header_type', false, 'inherit');
                
                // Return specific values based on header type
                switch ($header_type) {
                    case 'disabled':
                        return 'disabled'; // Special flag for completely disabled
                    case 'theme':
                        return 'theme'; // Special flag for theme default
                    case 'custom':
                        $override = $this->get_acf_value('header_override');
                        if ($override) {
                            return $override;
                        }
                        break;
                    case 'inherit':
                    default:
                        break; // Continue with inheritance logic
                }
            }

            // Check theme customizer setting
            $header_type = get_theme_mod(Config::prefix('header_layout_type'), '_default');
            $section_id = '';
            if ($header_type === '_gs') {
                $section_id = get_theme_mod(Config::prefix('header_layout_source'), '');
            }

            // Fall back to global default from ACF options if available
            if (!$section_id) {
                $section_id = $this->get_acf_option('default_header');
            }

            return $section_id;
        });

        // Add new function to render header content
        Functions::register('header_content', function() {
            $section_id = Functions::call('get_header_section');
            
            // Handle different header types
            if ($section_id === 'disabled') {
                return; // Don't render anything
            }
            
            if ($section_id === 'theme') {
                // Always render theme default header
                get_template_part('template-parts/header/default');
                return;
            }
            
            if ($section_id && $section_id !== 'theme' && class_exists('\UniStudioCore\Global_Sections')) {
                $global_sections = \UniStudioCore\Global_Sections::getInstance();
                
                if (class_exists('\Elementor\Plugin')) {
                    $elementor = \Elementor\Plugin::instance();
                    echo $elementor->frontend->get_builder_content_for_display($section_id);
                } else {
                    echo $global_sections->render_global_section($section_id);
                }
            } else {
                // Fallback to default header for inheritance
                get_template_part('template-parts/header/default');
            }
        });

        Functions::register('header_default', function() {
            get_template_part('template-parts/header/default');
        });

        // Add hooks
        Hooks::add_action('header_content', 'header_content');
        Hooks::add_action('header_default', 'header_default');
    }

    /**
     * Register footer template parts
     */
    private function register_footer_parts() {
        // Add new function to get footer section ID
        Functions::register('get_footer_section', function() {
            // Check for page-specific settings first
            if (is_singular()) {
                $footer_type = $this->get_acf_value('footer_type', false, 'inherit');
                
                // Return specific values based on footer type
                switch ($footer_type) {
                    case 'disabled':
                        return 'disabled'; // Special flag for completely disabled
                    case 'theme':
                        return 'theme'; // Special flag for theme default
                    case 'custom':
                        $override = $this->get_acf_value('footer_override');
                        if ($override) {
                            return $override;
                        }
                        break;
                    case 'inherit':
                    default:
                        break; // Continue with inheritance logic
                }
            }

            // Check theme customizer setting
            $footer_type = get_theme_mod(Config::prefix('footer_layout_type'), '_default');
            if ($footer_type === '_gs') {
                $section_id = get_theme_mod(Config::prefix('footer_layout_source'), '');
                if ($section_id) {
                    return $section_id;
                }
            }

            // Fall back to global default from ACF options if available
            return $this->get_acf_option('default_footer');
        });

        // Add new function to render footer content
        Functions::register('footer_content', function() {
            $section_id = Functions::call('get_footer_section');
            
            // Handle different footer types
            if ($section_id === 'disabled') {
                return; // Don't render anything
            }
            
            if ($section_id === 'theme') {
                // Always render theme default footer
                get_template_part('template-parts/footer/default');
                return;
            }
            
            if ($section_id && $section_id !== 'theme' && class_exists('\UniStudioCore\Global_Sections')) {
                $global_sections = \UniStudioCore\Global_Sections::getInstance();
                
                if (class_exists('\Elementor\Plugin')) {
                    $elementor = \Elementor\Plugin::instance();
                    echo $elementor->frontend->get_builder_content_for_display($section_id);
                } else {
                    echo $global_sections->render_global_section($section_id);
                }
            } else {
                // Fallback to default footer for inheritance
                get_template_part('template-parts/footer/default');
            }
        });

        Functions::register('footer_default', function() {
            get_template_part('template-parts/footer/default');
        });

        // Add hooks
        Hooks::add_action('footer_content', 'footer_content');
        Hooks::add_action('footer_default', 'footer_default');
    }

    /**
     * Register page header template parts
     */
    private function register_page_header_parts() {
        // Add new function to get page header/cover section ID
        Functions::register('get_page_header_section', function() {
            $settings = Template_Helper::get_page_header_settings();
            
            // Return 'disabled' if page header is disabled
            if ($settings['layout'] === 'disabled') {
                return 'disabled';
            }
            
            // Return 'theme' if using theme default
            if ($settings['template_type'] === '_default') {
                return 'theme';
            }
            
            // Return global section ID if set
            if ($settings['template_id']) {
                return $settings['template_id'];
            }
            
            // Fallback to theme default
            return 'theme';
        });

        // Add new function to render page header content
        Functions::register('page_header_content', function() {
            $section_id = Functions::call('get_page_header_section');
            $settings = Template_Helper::get_page_header_settings();
            
            // Handle different page header types
            if ($section_id === 'disabled') {
                return; // Don't render anything
            }
            
            if ($section_id === 'theme') {
                // Always render theme default page header
                if (is_singular()) {
                    // For singular pages
                    $args = [
                        'header_layout' => $settings['layout'],
                        'show_breadcrumbs' => $settings['show_breadcrumbs'],
                        'title' => get_the_title(),
                        'alignment' => 'left',
                    ];
                    get_template_part('template-parts/content/page-header', null, $args);
                } else {
                    // For archives, pass to appropriate template part
                    if (is_category()) {
                        Hooks::do_action('category_header');
                    } elseif (is_tag()) {
                        Hooks::do_action('tag_header');
                    } elseif (is_author()) {
                        Hooks::do_action('author_header');
                    } elseif (is_date()) {
                        Hooks::do_action('date_header');
                    } else {
                        Hooks::do_action('archive_header');
                    }
                }
                return;
            }
            
            if ($section_id && $section_id !== 'theme' && class_exists('\UniStudioCore\Global_Sections')) {
                // Render global section template
                if (class_exists('\Elementor\Plugin')) {
                    $elementor = \Elementor\Plugin::instance();
                    echo $elementor->frontend->get_builder_content_for_display($section_id);
                } else if (class_exists('\UniStudioCore\Global_Sections')) {
                    $global_sections = \UniStudioCore\Global_Sections::getInstance();
                    echo $global_sections->render_global_section($section_id);
                }
            } else {
                // Fallback to default page header
                if (is_singular()) {
                    $args = [
                        'header_layout' => $settings['layout'],
                        'show_breadcrumbs' => $settings['show_breadcrumbs'],
                        'title' => get_the_title(),
                        'alignment' => 'left',
                    ];
                    get_template_part('template-parts/content/page-header', null, $args);
                } else {
                    if (is_category()) {
                        Hooks::do_action('category_header');
                    } elseif (is_tag()) {
                        Hooks::do_action('tag_header');
                    } elseif (is_author()) {
                        Hooks::do_action('author_header');
                    } elseif (is_date()) {
                        Hooks::do_action('date_header');
                    } else {
                        Hooks::do_action('archive_header');
                    }
                }
            }
        });

        // Add hook
        Hooks::add_action('page_header_content', 'page_header_content');
    }

    /**
     * Register content template parts
     */
    private function register_content_parts() {
        // Register content wrapper functions
        Functions::register('content_wrap_before', function() {
            $template = Theme_Functions::getInstance()->get_field_value('page_template', false, 'default');
            $container_class = 'container';
            $main_class = '';
            
            // Adjust container and main column classes based on template
            switch ($template) {
                case 'full-width':
                    // $container_class .= ' max-w-full';
                    $main_class = '';
                    break;
                case 'sidebar-left':
                    // $container_class .= ' max-w-2xl';
                    $main_class = 'lg:order-2';
                    break;
                case 'sidebar-right':
                    // $container_class .= ' max-w-2xl';
                    $main_class = 'lg:order-1';
                    break;
                default:
                    // $container_class .= ' max-w-2xl';
                    break;
            }
            ?>
            <div class="section panel overflow-hidden py-4 lg:py-6 xl:py-8">
                <div class="<?php echo esc_attr($container_class); ?>">
                    <div class="row child-cols g-4 lg:gx-6 xl:gx-8" data-uc-grid>
                        <div class="<?php echo esc_attr($main_class); ?>">
            <?php
        });

        Functions::register('content_wrap_after', function() {
            $template = Theme_Functions::getInstance()->get_field_value('page_template', false, 'default');
            
            // Close main column div
            echo '</div>';
            
            // Add sidebar based on template
            if ($template === 'default' || $template === 'sidebar-left' || $template === 'sidebar-right') {
                // Add sidebar with appropriate classes
                $sidebar_class = ($template === 'sidebar-left') ? 'lg:order-1' : 'lg:order-2';
                ?>
                <div class="lg:col-4 <?php echo esc_attr($sidebar_class); ?>">
                    <?php get_sidebar(); ?>
                </div>
                <?php
            }
            ?>
                    </div>
                </div>
            </div>
            <?php
        });

        // Add hooks
        Hooks::add_action('content_wrap_before', 'content_wrap_before');
        Hooks::add_action('content_wrap_after', 'content_wrap_after');
    }

    /**
     * Register archive navigation
     */
    private function register_pagination() {
        Functions::register('archive_pagination', function() {
            ?>
            <div class="pagination-wrap mt-8">
                <?php
                $args = array(
                    'prev_text' => sprintf(
                        '%s <span class="nav-prev-text">%s</span>',
                        '<i class="uil uil-arrow-left"></i>',
                        __('Newer Posts', Config::get('text_domain'))
                    ),
                    'next_text' => sprintf(
                        '<span class="nav-next-text">%s</span> %s',
                        __('Older Posts', Config::get('text_domain')),
                        '<i class="uil uil-arrow-right"></i>'
                    ),
                    'class' => 'pagination hstack justify-center items-center gap-4'
                );
                
                the_posts_pagination($args);
                ?>
            </div>
            <?php
        });
    
        Hooks::add_action('archive_pagination', 'archive_pagination');
    }

    /**
     * Register post meta
     */
    private function register_post_meta() {
        Functions::register('post_meta', function() {
            $show_meta = Template_Helper::should_show('show_post_meta');
            
            if (!$show_meta) {
                return;
            }
            
            $meta_order = Template_Helper::get_post_meta_order();
            ?>
            <div class="hstack flex-wrap gap-2">
                <?php Template_Helper::render_post_meta($meta_order); ?>
            </div>
            <?php
        });

        Hooks::add_action('post_meta', 'post_meta');
    }

    /**
     * Register archive header parts
     */
    private function register_archive_headers() {
        Functions::register('archive_header', function() {
            get_template_part('template-parts/content/archive-header');
        });
        
        Functions::register('category_header', function() {
            get_template_part('template-parts/content/archive-header');
        });
        
        Functions::register('tag_header', function() {
            get_template_part('template-parts/content/archive-header');
        });
        
        Functions::register('author_header', function() {
            get_template_part('template-parts/content/archive-header');
        });
        
        Functions::register('date_header', function() {
            get_template_part('template-parts/content/archive-header');
        });
        
        // Register hooks
        Hooks::add_action('archive_header', 'archive_header');
        Hooks::add_action('category_header', 'category_header');
        Hooks::add_action('tag_header', 'tag_header');
        Hooks::add_action('author_header', 'author_header');
        Hooks::add_action('date_header', 'date_header');
    }

    /**
     * Register related posts hook
     */
    private function register_related_posts() {
        Functions::register('related_posts', function() {
            get_template_part('template-parts/content/related-posts');
        });
        
        Hooks::add_action('related_posts', 'related_posts');
    }

    /**
     * Register breadcrumbs
     */
    private function register_breadcrumbs() {
        Functions::register('breadcrumbs', function() {
            Template_Helper::breadcrumbs();
        });

        Hooks::add_action('breadcrumbs', 'breadcrumbs');
    }
}