<?php
/**
 * Template part for displaying single post content
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Template_Helper;

// Get layout, default to layout-1 if not set
$layout = get_query_var('post_layout', 'layout-1');
$show_featured_image = Template_Helper::should_show('blog_post_featured_image');
$image_ratio = Template_Helper::get_post_featured_image_ratio(true);
$show_post_footer = Template_Helper::should_show('blog_post_footer');
$show_navigation = Template_Helper::should_show('blog_post_navigation');
$show_related_posts = Template_Helper::should_show('blog_post_related_posts');
$show_meta = Template_Helper::should_show('blog_post_meta');
$meta_order = Template_Helper::get_blog_post_meta_order();

// Define classes based on layout
$article_classes = ['single-article', 'layout-' . $layout];
$header_classes = ['entry-header', 'mb-4', 'lg:mb-6'];
$meta_classes = ['entry-meta', 'fs-7', 'text-muted', 'mt-3', 'hstack', 'gap-3'];
$content_classes = ['entry-content', 'max-w-lg', 'lg:mb-6'];

// Apply layout specific classes
switch ($layout) {
    case 'layout-1': // Standard layout
        break;
    case 'layout-3': // Centered layout
        $article_classes[] = 'mx-auto';
        $header_classes[] = 'max-w-md mx-auto text-center';
        $meta_classes[] = 'justify-center text-center';
        $content_classes[] = 'max-w-lg mx-auto';
        break;
    case 'layout-4': // Full-width layout
        $article_classes[] = 'max-w-none';
        $header_classes[] = 'text-center';
        $meta_classes[] = 'justify-center text-center';
        break;
    default:
        break;
}
?>

<div>
    <article id="post-<?php the_ID(); ?>" <?php post_class($article_classes); ?>>
        <header class="<?php echo esc_attr(implode(' ', $header_classes)); ?>">
            <?php the_title('<h1 class="entry-title m-0">', '</h1>'); ?>
            <?php if ('post' === get_post_type() && $show_meta) : ?>
                <div class="<?php echo esc_attr(implode(' ', $meta_classes)); ?> justify-content-<?php echo in_array($layout, ['layout-3', 'layout-4']) ? 'center' : 'start'; ?>">
                    <?php Template_Helper::render_post_meta($meta_order); ?>
                </div>
            <?php endif; ?>
        </header>

        <?php if ($show_featured_image && has_post_thumbnail()) : 
            if ($layout === 'layout-1' || $layout === 'layout-3') {
                echo '<div class="post-thumbnail mb-4 lg:mb-6' . ($image_ratio ? ' ratio ' . $image_ratio : '') .'">';
                the_post_thumbnail('large', ['class' => 'w-full h-full object-cover']);
                echo '</div>';
            }
        endif; ?>

        <div class="<?php echo esc_attr(implode(' ', $content_classes)); ?>">
            <?php
            the_content();

            wp_link_pages([
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'polysaas'),
                'after'  => '</div>',
                'link_before' => '<span>',
                'link_after'  => '</span>',
            ]);
            ?>
        </div>

        <?php if ($show_post_footer) : ?>
        <footer class="entry-footer mt-4 lg:mt-6 pt-4 border-top">
            <?php if ('post' === get_post_type()) : ?>
                <div class="entry-meta">
                    <?php
                    $categories_list = get_the_category_list(', ');
                    if ($categories_list) {
                        printf(
                            '<div class="cat-links">%s %s</div>',
                            esc_html__('Posted in:', 'polysaas'),
                            $categories_list
                        );
                    }

                    $tags_list = get_the_tag_list('', ', ');
                    if ($tags_list) {
                        printf(
                            '<div class="tags-links">%s %s</div>',
                            esc_html__('Tagged:', 'polysaas'),
                            $tags_list
                        );
                    }
                    ?>
                </div>
            <?php endif; ?>
        </footer>
        <?php endif; ?>

        <?php if ($show_navigation) : ?>
            <?php if (get_previous_post() || get_next_post()) : ?>
                <div class="post-navigation-container p-4 mt-4 lg:mt-6 bg-light">
                    <?php
                    the_post_navigation([
                        'prev_text' => '<div class="nav-subtitle">' . esc_html__('Previous Post', 'polysaas') . '</div><div class="nav-title h6 m-0 mt-2 d-none lg:d-block">%title</div>',
                        'next_text' => '<div class="nav-subtitle">' . esc_html__('Next Post', 'polysaas') . '</div><div class="nav-title h6 m-0 mt-2 d-none lg:d-block">%title</div>',
                    ]);
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($show_related_posts) : ?>
            <div class="related-posts mt-4 lg:mt-6">
                <?php Hooks::do_action('related_posts'); ?>
            </div>
        <?php endif; ?>
    </article>
</div>