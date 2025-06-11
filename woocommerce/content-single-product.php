<?php
/**
 * The template for displaying single product content
 *
 * @package Polysaas
 */

defined('ABSPATH') || exit;

use Polysaas\Core\Template_Helper;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}

// Get product card style settings for consistency
$card_style = 'style-1'; // Default style to match product cards
$panel_classes = ['single-product-panel', $card_style, 'panel'];
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class($panel_classes, $product); ?>>
    
    <?php
    // Show notices
    if (function_exists('woocommerce_output_all_notices')) {
        woocommerce_output_all_notices();
    }
    ?>
    
    <div class="product-content-wrapper row g-4 lg:g-6">
        <div class="col-12 col-lg-6">
            <div class="product-gallery-wrapper">
                <?php
                /**
                 * Hook: woocommerce_before_single_product_summary.
                 *
                 * @hooked woocommerce_show_product_sale_flash - 10
                 * @hooked woocommerce_show_product_images - 20
                 */
                do_action('woocommerce_before_single_product_summary');
                ?>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="product-details-wrapper">
                <div class="product-summary-wrapper">
                    <?php
                    /**
                     * Hook: woocommerce_single_product_summary.
                     *
                     * @hooked woocommerce_template_single_title - 5
                     * @hooked woocommerce_template_single_rating - 10
                     * @hooked woocommerce_template_single_price - 10
                     * @hooked woocommerce_template_single_excerpt - 20
                     * @hooked woocommerce_template_single_add_to_cart - 30
                     * @hooked woocommerce_template_single_meta - 40
                     * @hooked woocommerce_template_single_sharing - 50
                     */
                    do_action('woocommerce_single_product_summary');
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="product-tabs-wrapper mt-6">
        <?php
        /**
         * Hook: woocommerce_after_single_product_summary.
         *
         * @hooked woocommerce_output_product_data_tabs - 10
         * @hooked woocommerce_upsell_display - 15
         * @hooked woocommerce_output_related_products - 20
         */
        do_action('woocommerce_after_single_product_summary');
        ?>
    </div>
</div>

<?php do_action('woocommerce_after_single_product'); ?>