<?php
/**
 * Template part for displaying posts in an archive loop
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Template_Helper;

// Get grid settings
$columns = Template_Helper::get_archive_grid_columns();
$is_masonry = Template_Helper::is_archive_masonry();
$pagination_type = Template_Helper::get_archive_pagination_type();
$match_height = Template_Helper::get_archive_grid_match_height();

// Define grid classes
$grid_class = 'row';
if ($columns !== '12') {
    $grid_class .= ' row-cols-1 row-cols-md-2 row-cols-lg-' . (12 / intval($columns));
}

// Add masonry attribute if enabled
$masonry_attr = '';
if ($is_masonry && $columns !== '12' && !$match_height) {
    $masonry_attr = 'masonry: true;';
}
?>

<?php if (have_posts()) : ?>
    <div class="<?php echo esc_attr($grid_class); ?> g-4 <?php echo ($is_masonry ? 'py-4' : '') ?>" data-uc-grid="<?php echo $masonry_attr; ?>">
        <?php
        // Start the Loop
        while (have_posts()) :
            the_post();
            
            /*
             * Include the post card template.
             * Pass the card style as a parameter
             */
            set_query_var('card_style', Template_Helper::get_archive_post_card_style());
            get_template_part('template-parts/content/archive');
        endwhile;
        ?>
    </div>

    <?php if ($pagination_type !== 'disabled') : ?>
        <div class="pagination-wrap mt-6">
            <?php 
            if ($pagination_type === 'numbered') {
                // Display standard pagination
                the_posts_pagination([
                    'prev_text' => '<i class="uil uil-angle-left"></i> ' . __('Previous', 'polysaas'),
                    'next_text' => __('Next', 'polysaas') . ' <i class="uil uil-angle-right"></i>',
                    'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'polysaas') . ' </span>',
                ]);
            } elseif ($pagination_type === 'ajax-loadmore') {
                // Display load more button
                ?>
                <div class="load-more-container text-center">
                    <button id="load-more-posts" class="btn btn-primary" data-page="1" data-max="<?php echo esc_attr(get_query_var('paged') ? get_query_var('paged') : 1); ?>">
                        <?php _e('Load More', 'polysaas'); ?>
                    </button>
                </div>
                <?php
            }
            ?>
        </div>
    <?php endif; ?>

<?php else : ?>
    <?php get_template_part('template-parts/content/none'); ?>
<?php endif; ?>