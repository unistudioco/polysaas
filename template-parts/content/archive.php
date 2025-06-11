<?php
/**
 * Template part for displaying posts in an archive
 *
 * @package Polysaas
 */

use Polysaas\Core\Template_Helper;
use Polysaas\Core\Theme_Functions;

// Get card style, default to style-1 if not set
$card_style = get_query_var('card_style', 'style-1');
$show_meta = Template_Helper::should_show('show_post_meta');
$show_featured_image = Template_Helper::should_show('show_post_featured_image');
$meta_order = Template_Helper::get_post_meta_order();
$columns = Template_Helper::get_archive_grid_columns();
$match_height = Template_Helper::get_archive_grid_match_height();
$image_ratio = Template_Helper::get_post_featured_image_ratio();

// Define classes based on layout
$article_classes = ['archive-item', $card_style, 'overflow-hidden'];
$content_classes = ['entry-content', 'vstack', 'gap-2'];

// Apply card style specific classes
switch ($card_style) {
    case 'style-1':
        $article_classes[] = 'bg-white border rounded-lg shadow-sm' . ($match_height ? ' h-100' : '');
        $content_classes[] = 'p-3 lg:p-4';
        break;
    case 'style-2':
        $content_classes[] = 'p-0 mt-3';
        break;
    default:
        $article_classes[] = 'bg-white border rounded-lg shadow-sm' . ($match_height ? ' h-100' : '');
        $content_classes[] = 'p-3 lg:p-4';
        break;
}

// Adjust layout for classic (12 column) view
if ($columns === '12') {
    // Add classes for horizontal layout
    $article_classes[] = 'd-flex flex-column flex-md-row';
    if ($card_style === 'style-1') {
        $article_classes[] = 'p-0 overflow-hidden';
    }
} else {
    // Add classes for grid layout
    $article_classes[] = '';
}
?>

<div>
    <article id="post-<?php the_ID(); ?>" <?php post_class($article_classes); ?>>
        <?php if ($show_featured_image && has_post_thumbnail()) : ?>
            <div class="entry-media overflow-hidden<?php echo ($image_ratio ? ' ratio ' . $image_ratio : ''); ?><?php echo $columns === '12' ? ' flex-shrink-0' : ''; ?>" <?php echo $columns === '12' ? 'style="flex-basis: 35%;"' : ''; ?>>
                <a href="<?php the_permalink(); ?>">
                    <?php 
                    if ($columns === '12') {
                        the_post_thumbnail('medium_large', ['class' => 'w-full h-full object-cover transition-all duration-150 hover:scale-105']);
                    } else {
                        the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover transition-all duration-150 hover:scale-105']);
                    }
                    ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="<?php echo esc_attr(implode(' ', $content_classes)); ?>">
            <header class="entry-header">
                <?php
                the_title(
                    '<h4 class="entry-title m-0"><a href="' . esc_url(get_permalink()) . '" rel="bookmark" class="text-none hover:text-primary">',
                    '</a></h4>'
                );

                if ('post' === get_post_type() && $show_meta) : ?>
                    <div class="entry-meta fs-7 text-muted hstack gap-2 mt-2">
                        <?php Template_Helper::render_post_meta($meta_order); ?>
                    </div>
                <?php endif; ?>
            </header>

            <div class="entry-summary">
                <?php Theme_Functions::get_custom_excerpt(15); ?>
            </div>

            <footer class="entry-footer">
                <a href="<?php the_permalink(); ?>" class="uc-link">
                    <?php echo esc_html__('Read More', 'polysaas'); ?>
                    <i class="uil uil-arrow-right ml-1"></i>
                </a>
            </footer>
        </div>
    </article>
</div>