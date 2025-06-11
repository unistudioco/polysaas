<?php
/**
 * Template part for displaying related posts
 *
 * @package Polysaas
 */

use Polysaas\Core\Template_Helper;

$current_post_id = get_the_ID();
$current_categories = get_the_category();

if ($current_categories) {
    $category_ids = [];
    foreach ($current_categories as $category) {
        $category_ids[] = $category->term_id;
    }
    
    $args = [
        'category__in' => $category_ids,
        'post__not_in' => [$current_post_id],
        'posts_per_page' => 4,
        'orderby' => 'rand',
    ];
    
    $related_query = new WP_Query($args);
    
    if ($related_query->have_posts()) :
        ?>
        <section class="related-posts">
            <h3 class="mb-3"><?php echo esc_html__('Related Posts', 'polysaas'); ?></h3>
            
            <div class="row row-cols-1 row-cols-md-2 g-2 lg:g-3">
                <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                    <div class="col">
                        <div class="related-post overflow-hidden">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="max-w-128px ratio ratio-1x1 overflow-hidden">
                                    <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover transition-all duration-150 hover:scale-105']); ?>
                                </a>
                            <?php endif; ?>
                            
                            <div class="px-2">
                                <?php the_title('<h5 class="h6 m-0"><a href="' . esc_url(get_permalink()) . '" class="text-inherit text-none">', '</a></h5>'); ?>
                                
                                <?php if (Template_Helper::should_show('show_post_meta')) : ?>
                                    <div class="entry-meta fs-7 text-muted mt-1 opacity-50">
                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date(); ?></time>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
        <?php
    endif;
    
    wp_reset_postdata();
}