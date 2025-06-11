<?php
/**
 * The template for displaying product content within loops
 *
 * @package Polysaas
 */

defined('ABSPATH') || exit;

use Polysaas\Core\Template_Helper;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}

// Get card style classes
$card_style = 'style-1'; // Default style
$card_classes = ['product-card', $card_style, 'panel', 'text-none'];
?>
<li <?php wc_product_class($card_classes, $product); ?>>
    <div class="product-inner">
        <?php
        /**
         * Hook: woocommerce_before_shop_loop_item.
         */
        do_action('woocommerce_before_shop_loop_item');
        ?>

        <div class="product-thumbnail ratio ratio-1x1">
            <?php
            /**
             * Hook: woocommerce_before_shop_loop_item_title.
             */
            do_action('woocommerce_before_shop_loop_item_title');
            ?>
        </div>

        <div class="product-content pt-2">
            <?php
            /**
             * Hook: woocommerce_shop_loop_item_title.
             */
            do_action('woocommerce_shop_loop_item_title');

            /**
             * Hook: woocommerce_after_shop_loop_item_title.
             */
            do_action('woocommerce_after_shop_loop_item_title');
            ?>

            <div class="product-actions mt-2">
                <?php
                /**
                 * Hook: woocommerce_after_shop_loop_item.
                 */
                do_action('woocommerce_after_shop_loop_item');
                ?>
            </div>
        </div>
    </div>
</li>