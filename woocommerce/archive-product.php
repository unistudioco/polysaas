<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * @package Polysaas
 */

defined('ABSPATH') || exit;

use Polysaas\Core\Hooks;
use Polysaas\Core\Template_Helper;

get_header();

// Get Shop Customizer Options
$show_breadcrumbs = Template_Helper::should_show_woo_breadcrumbs();
$sidebar_position = Template_Helper::get_shop_sidebar_position();

// Define column classes based on sidebar
$content_class = 'col-12';
$sidebar_class = 'col-lg-4';

if ($sidebar_position !== 'disabled') {
    $content_class = 'col-lg-8';
}

/**
 * Hook: woocommerce_before_main_content.
 */
do_action('woocommerce_before_main_content');
?>

<?php if ($show_breadcrumbs) : ?>
<div class="page-header shop-page-header py-2 bg-light hstack justify-center">
    <?php Template_Helper::breadcrumbs(); ?>
</div>
<?php endif; ?>

<?php Hooks::do_action('page_header_content'); ?>

<div class="section panel overflow-hidden py-4 lg:py-6 xl:pb-9">
    <div class="container">
        <div class="row g-4 lg:g-6" data-uc-grid>
            <div class="<?php echo esc_attr($content_class); ?> <?php echo $sidebar_position === 'left' ? 'order-lg-2' : ''; ?>">
                <main id="primary" class="site-main woocommerce-main">
                    <?php
                    // Show notices
                    if (function_exists('woocommerce_output_all_notices')) {
                        woocommerce_output_all_notices();
                    }
                    
                    if (woocommerce_product_loop()) {
                        /**
                         * Custom tools wrapper
                         */
                        ?>
                        <div class="shop-header-tools vstack lg:hstack justify-between gap-2 mb-4 xl:mb-6 fs-7">
                            <?php
                            woocommerce_result_count();
                            woocommerce_catalog_ordering();
                            ?>
                        </div>
                        <?php
                        
                        woocommerce_product_loop_start();
                        
                        if (wc_get_loop_prop('total')) {
                            while (have_posts()) {
                                the_post();
                                
                                /**
                                 * Hook: woocommerce_shop_loop.
                                 */
                                do_action('woocommerce_shop_loop');
                                
                                wc_get_template_part('content', 'product');
                            }
                        }
                        
                        woocommerce_product_loop_end();
                        
                        /**
                         * Hook: woocommerce_after_shop_loop.
                         */
                        do_action('woocommerce_after_shop_loop');
                    } else {
                        /**
                         * Hook: woocommerce_no_products_found.
                         */
                        do_action('woocommerce_no_products_found');
                    }
                    ?>
                </main>
            </div>
            
            <?php if ($sidebar_position !== 'disabled') : ?>
                <div class="<?php echo esc_attr($sidebar_class); ?> <?php echo $sidebar_position === 'left' ? 'order-lg-1' : ''; ?>">
                    <?php
                    /**
                     * Hook: woocommerce_sidebar.
                     */
                    do_action('woocommerce_sidebar');
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php

/**
 * Hook: woocommerce_after_main_content.
 */
do_action('woocommerce_after_main_content');

get_footer();