<?php
namespace Polysaas\Setup;

use Polysaas\Core\Config;
use Polysaas\Core\Template_Helper;

/**
 * WooCommerce Setup
 *
 * @package Polysaas
 */
class Woocommerce_Setup {
    
    /**
     * Register default hooks and actions for WooCommerce
     */
    public function register() {
        // Only register if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        // Remove default WooCommerce styles
        add_filter('woocommerce_enqueue_styles', '__return_empty_array');
        
        // Add theme support for WooCommerce
        add_action('after_setup_theme', [$this, 'setup_woocommerce']);
        
        // Register WooCommerce sidebar
        add_action('widgets_init', [$this, 'register_sidebars']);
        
        // Configure products per page
        add_filter('loop_shop_per_page', [$this, 'products_per_page'], 20);
        
        // Configure products per row
        add_filter('loop_shop_columns', [$this, 'products_per_row']);
        
        // Remove and reposition result count and ordering
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    
        // Add custom wrappers for WooCommerce content
        add_action('woocommerce_before_main_content', [$this, 'before_content'], 10);
        add_action('woocommerce_after_main_content', [$this, 'after_content'], 10);
        
        // Modify WooCommerce templates
        add_filter('woocommerce_locate_template', [$this, 'locate_template'], 10, 3);

        // Remove shop title
        add_filter('woocommerce_show_page_title', '__return_false');
        
        // Add our custom wrapper for result count and ordering
        add_action('woocommerce_before_shop_loop', [$this, 'shop_header_wrapper_start'], 15);
        add_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        add_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
        add_action('woocommerce_before_shop_loop', [$this, 'shop_header_wrapper_end'], 31);

        // Custom single product layout
        add_filter('woocommerce_product_thumbnails_columns', [$this, 'product_thumbnails_columns']);

        // Customize product gallery width
        add_filter('woocommerce_sale_flash', [$this, 'custom_sale_badge'], 10, 3);

        // Modify single product tabs
        add_filter('woocommerce_product_tabs', [$this, 'customize_product_tabs']);

        // Add custom product meta section
        add_action('woocommerce_single_product_summary', [$this, 'add_custom_product_meta'], 39);
        
        // Product display options
        if (!Template_Helper::should_show_product_rating()) {
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        }
        
        // Add hooks for WooCommerce actions
        $this->add_hooks();
    }
    
    /**
     * Setup WooCommerce
     */
    public function setup_woocommerce() {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }
    
    /**
     * Register WooCommerce sidebar
     */
    public function register_sidebars() {
        register_sidebar([
            'name'          => esc_html__('WooCommerce Sidebar', Config::get('text_domain')),
            'id'            => 'woocommerce-sidebar',
            'description'   => esc_html__('Add widgets here for WooCommerce pages.', Config::get('text_domain')),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);
    }
    
    /**
     * Set number of products per page
     */
    public function products_per_page() {
        return Template_Helper::get_products_per_page();
    }
    
    /**
     * Set number of products per row
     */
    public function products_per_row() {
        return Template_Helper::get_products_per_row();
    }
    
    /**
     * Before main content wrapper
     */
    public function before_content() {
        echo '<div class="woocommerce-content-wrapper">';
    }
    
    /**
     * After main content wrapper
     */
    public function after_content() {
        echo '</div>';
    }
    
    /**
     * Add hooks for custom WooCommerce actions
     */
    private function add_hooks() {
        // Product display options
        if (!Template_Helper::get_option('shop_show_rating', true)) {
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        }
        
        if (!Template_Helper::get_option('shop_show_price', true)) {
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
        }
        
        if (!Template_Helper::get_option('shop_show_add_to_cart', true)) {
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        }
        
        // Single product display options
        if (!Template_Helper::get_option('shop_product_show_meta', true)) {
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        }
        
        if (!Template_Helper::get_option('shop_product_show_tabs', true)) {
            remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
        }
        
        // Related products
        add_filter('woocommerce_output_related_products_args', [$this, 'related_products_args']);
        
        if (!Template_Helper::get_option('shop_product_show_related', true)) {
            remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
        }
    }

    /**
     * Shop header wrapper start
     */
    public function shop_header_wrapper_start() {
        echo '<div class="shop-header-tools d-flex justify-between items-center mb-4">';
    }

    /**
     * Shop header wrapper end
     */
    public function shop_header_wrapper_end() {
        echo '</div>';
    }
    
    /**
     * Customize related products display
     */
    public function related_products_args($args) {
        $args['posts_per_page'] = Template_Helper::get_option('shop_product_related_count', 4);
        return $args;
    }
    
    /**
     * Locate WooCommerce templates
     */
    public function locate_template($template, $template_name, $template_path) {
        global $woocommerce;
        
        $_template = $template;
        
        if (!$template_path) {
            $template_path = $woocommerce->template_url;
        }
        
        $theme_template = locate_template([
            'woocommerce/' . $template_name,
            $template_name
        ]);
        
        // Use theme template if exists
        if ($theme_template) {
            $template = $theme_template;
        }
        
        // Return default template if nothing found
        if (!$template) {
            $template = $_template;
        }
        
        return $template;
    }

    /**
     * Set number of thumbnails per row in product gallery
     */
    public function product_thumbnails_columns() {
        return 4; // Change this number to adjust thumbnails per row
    }

    /**
     * Customize sale badge
     */
    public function custom_sale_badge($html, $post, $product) {
        if (!Template_Helper::get_option('shop_show_sale_badge', true)) {
            return '';
        }
        
        if ($product->is_on_sale()) {
            $percentage = '';
            
            // Calculate percentage discount for simple products
            if ($product->is_type('simple')) {
                $regular_price = (float) $product->get_regular_price();
                $sale_price = (float) $product->get_sale_price();
                
                if ($regular_price > 0) {
                    $percentage = round(100 - (($sale_price / $regular_price) * 100));
                }
            }
            
            if ($percentage) {
                return '<span class="onsale">-' . $percentage . '%</span>';
            } else {
                return '<span class="onsale">' . esc_html__('Sale', Config::get('text_domain')) . '</span>';
            }
        }
        
        return $html;
    }

    /**
     * Customize product tabs
     */
    public function customize_product_tabs($tabs) {
        // Remove reviews tab if configured
        if (!Template_Helper::get_option('shop_product_show_reviews', true)) {
            unset($tabs['reviews']);
        }
        
        // Rename the description tab
        if (isset($tabs['description'])) {
            $tabs['description']['title'] = __('Product details', Config::get('text_domain'));
        }
        
        // Add a new custom tab
        if (Template_Helper::get_option('shop_product_show_custom_tab', false)) {
            
            $title = Template_Helper::get_option('shop_product_custom_tab_title', '');
            
            $tabs['custom_tab'] = [
                'title'    => ($title ? $title : __('Custom tab', Config::get('text_domain'))),
                'priority' => 30,
                'callback' => [$this, 'custom_tab_content'],
            ];
        }
        
        return $tabs;
    }

    /**
     * Custom tab content
     */
    public function custom_tab_content() {
        // Get custom tab content from theme options
        $content = Template_Helper::get_option('shop_product_custom_tab_content', '');
        
        if ($content) {
            echo wp_kses_post(wpautop($content));
        } else {
            echo '<p>' . esc_html__('This is a custom tab. You can add any content here via the Customizer.', Config::get('text_domain')) . '</p>';
        }
    }

    /**
     * Add custom product meta
     */
    public function add_custom_product_meta() {
        global $product;
        
        if (!$product || !Template_Helper::get_option('shop_product_show_custom_meta', false)) {
            return;
        }
        
        echo '<div class="product-custom-meta mt-4">';
        
        // Example: Display SKU in a custom format
        if ($product->get_sku()) {
            echo '<div class="product-sku hstack gap-2 mb-2">';
            echo '<span class="meta-label">' . esc_html__('SKU:', Config::get('text_domain')) . '</span>';
            echo '<span class="meta-value">' . esc_html($product->get_sku()) . '</span>';
            echo '</div>';
        }
        
        // Example: Display categories in a custom format
        $categories = wc_get_product_category_list($product->get_id(), ', ');
        if ($categories) {
            echo '<div class="product-categories hstack gap-2 mb-2">';
            echo '<span class="meta-label">' . esc_html__('Categories:', Config::get('text_domain')) . '</span>';
            echo '<span class="meta-value">' . $categories . '</span>';
            echo '</div>';
        }
        
        echo '</div>';
    }

}