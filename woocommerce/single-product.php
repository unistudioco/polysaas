<?php
/**
 * The Template for displaying all single products
 *
 * @package Polysaas
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Polysaas\Core\Hooks;
use Polysaas\Core\Template_Helper;

get_header();

// Get sidebar position for single product
$sidebar_position = Template_Helper::get_option('shop_product_sidebar_position', 'disabled');

// Define column classes based on sidebar
$content_class = 'col-12';
$sidebar_class = 'col-lg-4';

if ($sidebar_position !== 'disabled') {
    $content_class = 'col-lg-8';
}

// Get Shop Customizer Options for breadcrumbs - matching archive-product.php
$show_breadcrumbs = Template_Helper::should_show_woo_breadcrumbs();

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
                    
                    while (have_posts()) : 
                        the_post();
                        
                        // Get the product content template
                        wc_get_template_part('content', 'single-product');
                        
                    endwhile; // end of the loop.
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