<?php
/**
 * Template Helper Class
 *
 * @package Polysaas
 */

namespace Polysaas\Core;

use Polysaas\Core\Config;

class Template_Helper {
    /**
     * Get theme mod with prefix
     *
     * @param string $option_name Option name without prefix
     * @param mixed $default Default value
     * @return mixed Option value
     */
    public static function get_option($option_name, $default = null) {
        // Apply prefix to option name
        $prefixed_name = Config::prefix($option_name);
        
        // Get option from theme_mod
        return get_theme_mod($prefixed_name, $default);
    }

    /**
     * Check if a feature should be shown based on theme_mod
     *
     * @param string $feature_name Feature name
     * @return bool Whether to show the feature
     */
    public static function should_show($feature_name) {
        return (bool) self::get_option($feature_name, true);
    }

    /**
     * Get the order of post meta items
     *
     * @return array Post meta items in order
     */
    public static function get_post_meta_order() {
        return self::get_option('post_meta_order', ['date', 'author', 'comments', 'categories']);
    }

    /**
     * Get the order of single post meta items
     *
     * @return array Single post meta items in order
     */
    public static function get_blog_post_meta_order() {
        return self::get_option('blog_post_meta_order', ['author', 'date', 'comments', 'categories']);
    }

    /**
     * Get blog archive sidebar position
     *
     * @return string Sidebar position (left, right, disabled)
     */
    public static function get_archive_sidebar_position() {
        return self::get_option('blog_archive_sidebar', 'right');
    }
    
    /**
     * Get blog single post sidebar position
     *
     * @return string Sidebar position (left, right)
     */
    public static function get_post_sidebar_position() {
        return self::get_option('blog_post_sidebar', 'right');
    }
    
    /**
     * Get blog single post container size
     *
     * @return string Container Size (null, small, large, expand)
     */
    public static function get_post_container_size() {
        return self::get_option('blog_post_container', '');
    }
    
    /**
     * Get blog archive grid columns
     *
     * @return string Grid columns (12, 6, 4, 3)
     */
    public static function get_archive_grid_columns() {
        return self::get_option('blog_archive_grid_columns', '12');
    }
    
    /**
     * Get grid match height
     *
     * @return string Grid Match Height (boolean)
     */
    public static function get_archive_grid_match_height() {
        return self::get_option('blog_archive_grid_match_height', false);
    }
    
    /**
     * Get Featured Image Ratio
     *
     * @return string Featured Image Ratio (default, 1x1, 3x2, 4x3, 16x9, 2x3)
     */
    public static function get_post_featured_image_ratio($single = false) {
        return $single ? self::get_option('blog_post_featured_image_ratio', '') : self::get_option('post_featured_image_ratio', '');
    }
    
    /**
     * Get blog archive post card style
     *
     * @return string Post card style (style-1, style-2)
     */
    public static function get_archive_post_card_style() {
        return self::get_option('blog_archive_post_card_style', 'style-1');
    }
    
    /**
     * Check if blog archive uses masonry layout
     *
     * @return bool Whether to use masonry layout
     */
    public static function is_archive_masonry() {
        return self::get_option('blog_archive_grid_masonry', false);
    }
    
    /**
     * Get blog archive header layout
     *
     * @return string Header layout (full-width, main-content, disabled)
     */
    public static function get_archive_header_layout() {
        return self::get_option('blog_archive_header_layout', 'full-width');
    }
    
    /**
     * Get blog page header / cover template source (Default or Global Section)
     *
     * @return string Page Header / Cover Source (_default, _gs)
     */
    public static function get_archive_header_source() {
        return self::get_option('blog_archive_header_source', '_default');
    }

    /**
     * Get page header settings for current page or archive
     * Considers both global settings and local ACF overrides
     * 
     * @return array Array of page header settings
     */
    public static function get_page_header_settings() {
        $settings = [
            'layout' => 'default',         // default, boxed, disabled
            'template_type' => '_default', // _default, _gs (Global Section)
            'template_id' => '',           // ID of global section if using _gs
            'show_breadcrumbs' => true,    // Whether to show breadcrumbs
        ];
        
        // Check if we're on a singular page that might have ACF fields
        if (is_singular()) {
            // Get layout from ACF field
            $acf_layout = get_post_meta(get_the_ID(), 'page_header_layout', true);
            if (!empty($acf_layout)) {
                $settings['layout'] = $acf_layout;
            }
            
            // Get template type from ACF field
            $acf_template = get_post_meta(get_the_ID(), 'page_header_template', true);
            if (!empty($acf_template)) {
                $settings['template_type'] = ($acf_template === 'custom') ? '_gs' : '_default';
            }
            
            // Get global section ID if using custom template
            if ($settings['template_type'] === '_gs') {
                $acf_template_id = get_post_meta(get_the_ID(), 'page_cover_override', true);
                if (!empty($acf_template_id)) {
                    $settings['template_id'] = $acf_template_id;
                }
            }
            
            // Get breadcrumbs setting
            $acf_breadcrumbs = get_post_meta(get_the_ID(), 'breadcrumbs', true);
            if ($acf_breadcrumbs !== '') {
                $settings['show_breadcrumbs'] = (bool)$acf_breadcrumbs;
            }
        } else if (is_archive() || is_home() || is_search()) {
            // For archives, use global archive settings
            $settings['layout'] = self::get_archive_header_layout();
            $settings['template_type'] = self::get_option('blog_archive_page_header_type', '_default');
            
            if ($settings['template_type'] === '_gs') {
                $settings['template_id'] = self::get_option('blog_archive_page_header_template', '');
            }
            
            $settings['show_breadcrumbs'] = self::should_show('blog_archive_breadcrumbs');
        }
        
        return $settings;
    }
    
    /**
     * Get blog archive pagination type
     *
     * @return string Pagination type (numbered, ajax-loadmore, disabled)
     */
    public static function get_archive_pagination_type() {
        return self::get_option('blog_archive_pagination', 'numbered');
    }
    
    /**
     * Get blog single post layout
     *
     * @return string Post layout (layout-1, layout-2, layout-3, layout-4)
     */
    public static function get_post_layout() {
        return self::get_option('blog_post_layout', 'layout-1');
    }
    
    /**
     * Render post meta items in the specified order
     *
     * @param array $meta_order Order of meta items
     * @return void
     */
    public static function render_post_meta($meta_order = null) {
        if (is_null($meta_order)) {
            $meta_order = self::get_post_meta_order();
        }
        
        foreach ($meta_order as $meta_item) {
            switch ($meta_item) {
                case 'date':
                    echo '<span class="hstack gap-1 meta-date">';
                    echo '<i class="icon unicon-calendar"></i>';
                    echo '<time datetime="' . esc_attr(get_the_date('c')) . '">' . get_the_date() . '</time>';
                    echo '</span>';
                    break;
                case 'author':
                    echo '<span class="hstack gap-1 meta-author">';
                    echo '<i class="icon unicon-user"></i>';
                    echo '<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . get_the_author() . '</a>';
                    echo '</span>';
                    break;
                case 'comments':
                    if (comments_open()) {
                        echo '<span class="hstack gap-1 meta-comments">';
                        echo '<i class="icon unicon-chat"></i>';
                        echo '<a href="' . esc_url(get_comments_link()) . '">' . get_comments_number_text('0', '1', '%') . '</a>';
                        echo '</span>';
                    }
                    break;
                case 'categories':
                    $categories_list = get_the_category_list(', ');
                    if ($categories_list) {
                        echo '<span class="hstack gap-1 meta-categories">';
                        echo '<i class="icon unicon-folder"></i>';
                        echo $categories_list;
                        echo '</span>';
                    }
                    break;
            }
        }
    }

    /**
     * Display breadcrumbs for the current page
     *
     * @return void
     */
    public static function breadcrumbs() {
        global $post;
        
        // Don't display on the homepage
        if (is_front_page()) {
            return;
        }
        
        // Get delimiter and homepage text from customizer
        $delimiter = '<i class="icon unicon-chevron-right mx-1 opacity-50"></i>';
        $home_text = __('Home', 'polysaas');
        
        // Start breadcrumbs
        echo '<div class="breadcrumbs">';
        
        // Home link
        echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html($home_text) . '</a>';
        
        // Category, single post, pages, and archives
        if (is_category()) {
            echo $delimiter;
            echo __('Category: ', 'polysaas') . single_cat_title('', false);
        } elseif (is_tag()) {
            echo $delimiter;
            echo __('Tag: ', 'polysaas') . single_tag_title('', false);
        } elseif (is_author()) {
            echo $delimiter;
            echo __('Author: ', 'polysaas') . get_the_author();
        } elseif (is_day()) {
            echo $delimiter;
            echo __('Day: ', 'polysaas') . get_the_date();
        } elseif (is_month()) {
            echo $delimiter;
            echo __('Month: ', 'polysaas') . get_the_date('F Y');
        } elseif (is_year()) {
            echo $delimiter;
            echo __('Year: ', 'polysaas') . get_the_date('Y');
        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $archive_link = get_post_type_archive_link(get_post_type());
                echo $delimiter;
                echo '<a href="' . esc_url($archive_link) . '">' . esc_html($post_type->labels->name) . '</a>';
                echo $delimiter;
                the_title();
            } else {
                $categories = get_the_category();
                if ($categories) {
                    $category = $categories[0];
                    echo $delimiter;
                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                    echo $delimiter;
                    the_title();
                } else {
                    echo $delimiter;
                    the_title();
                }
            }
        } elseif (is_page() && !is_front_page()) {
            echo $delimiter;
            
            // Check if page has parent and if $post is available
            if (isset($post) && is_object($post) && $post->post_parent) {
                $ancestors = get_post_ancestors($post->ID);
                $ancestors = array_reverse($ancestors);
                
                foreach ($ancestors as $ancestor) {
                    echo '<a href="' . esc_url(get_permalink($ancestor)) . '">' . esc_html(get_the_title($ancestor)) . '</a>' . $delimiter;
                }
            }
            
            the_title();
        } elseif (is_search()) {
            echo $delimiter;
            echo __('Search results for: ', 'polysaas') . get_search_query();
        } elseif (is_404()) {
            echo $delimiter;
            echo __('Error 404', 'polysaas');
        } elseif (is_post_type_archive()) {
            echo $delimiter;
            echo post_type_archive_title('', false);
        } elseif (is_tax()) {
            echo $delimiter;
            echo single_term_title('', false);
        } elseif (is_home()) {
            echo $delimiter;
            echo get_the_title(get_option('page_for_posts', true));
        }
        
        echo '</div>';
    }

    // WooCommerce Options
    /**
     * Get WooCommerce shop sidebar position
     *
     * @return string Sidebar position (left, right, disabled)
     */
    public static function get_shop_sidebar_position() {
        return self::get_option('shop_sidebar_position', 'right');
    }

    /**
     * Get WooCommerce product sidebar position
     *
     * @return string Sidebar position (left, right, disabled)
     */
    public static function get_product_sidebar_position() {
        return self::get_option('shop_product_sidebar_position', 'disabled');
    }

    /**
     * Get WooCommerce products per page
     *
     * @return int Number of products per page
     */
    public static function get_products_per_page() {
        return (int) self::get_option('shop_products_per_page', 12);
    }

    /**
     * Get WooCommerce products per row
     *
     * @return int Number of products per row
     */
    public static function get_products_per_row() {
        return (int) self::get_option('shop_products_per_row', 3);
    }

    /**
     * Get WooCommerce product card style
     *
     * @return string Product card style
     */
    public static function get_product_card_style() {
        return self::get_option('shop_product_card_style', 'style-1');
    }

    /**
     * Check if WooCommerce should show product rating
     *
     * @return bool Whether to show product rating
     */
    public static function should_show_product_rating() {
        return self::get_option('shop_show_rating', true);
    }

    /**
     * Check if WooCommerce should show product price
     *
     * @return bool Whether to show product price
     */
    public static function should_show_product_price() {
        return self::get_option('shop_show_price', true);
    }

    /**
     * Check if WooCommerce should show add to cart button
     *
     * @return bool Whether to show add to cart button
     */
    public static function should_show_add_to_cart() {
        return self::get_option('shop_show_add_to_cart', true);
    }

    /**
     * Check if WooCommerce should show sale badge
     *
     * @return bool Whether to show sale badge
     */
    public static function should_show_sale_badge() {
        return self::get_option('shop_show_sale_badge', true);
    }

    /**
     * Check if WooCommerce should show product meta
     *
     * @return bool Whether to show product meta
     */
    public static function should_show_product_meta() {
        return self::get_option('shop_product_show_meta', true);
    }

    /**
     * Check if WooCommerce should show product tabs
     *
     * @return bool Whether to show product tabs
     */
    public static function should_show_product_tabs() {
        return self::get_option('shop_product_show_tabs', true);
    }

    /**
     * Check if WooCommerce should show related products
     *
     * @return bool Whether to show related products
     */
    public static function should_show_related_products() {
        return self::get_option('shop_product_show_related', true);
    }

    /**
     * Get WooCommerce related products count
     *
     * @return int Number of related products to show
     */
    public static function get_related_products_count() {
        return (int) self::get_option('shop_product_related_count', 4);
    }

    /**
     * Get WooCommerce breadcrumb settings
     *
     * @return bool Whether to show WooCommerce breadcrumbs
     */
    public static function should_show_woo_breadcrumbs() {
        return self::get_option('shop_breadcrumbs', true);
    }
}