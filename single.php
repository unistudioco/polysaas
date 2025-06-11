<?php
/**
 * The template for displaying all single posts
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Template_Helper;

get_header();

// Get single post settings
$layout = Template_Helper::get_post_layout();
$container_size = Template_Helper::get_post_container_size();
$sidebar_position = Template_Helper::get_post_sidebar_position();
$show_featured_image = Template_Helper::should_show('blog_post_featured_image');
$image_ratio = Template_Helper::get_post_featured_image_ratio(true);

// Define container size for layout 3 or 4 only

$container_classes = ['container'];

// Apply card style specific classes
switch ($container_size) {
    case 'small':
        $container_classes[] = 'max-w-700px';
        break;
    case 'medium':
        $container_classes[] = 'max-w-850px';
        break;
    case 'large':
        $container_classes[] = 'max-w-1000px';
        break;
    case 'expand':
        $container_classes[] = 'container-expand';
        break;
}

// Define column classes based on layout and sidebar
$content_class = 'col-12';
$sidebar_class = 'col-lg-4';
$show_sidebar = false;

// Only layouts 1 and 2 support sidebar
if (in_array($layout, ['layout-1', 'layout-2'])) {
    $content_class = 'col-lg-8';
    $show_sidebar = true;
}

if ($layout === 'layout-2') {
    $sidebar_class = 'col-lg-4 xl:mt-8';
}

// Render page header
Hooks::do_action('page_header_content');

// Before Single Post
Hooks::do_action('before_single_post');
?>

<?php if($layout === 'layout-4' && $show_featured_image) : ?>
<div class="post-thumbnail featured-full<?php echo ($image_ratio ? ' ratio ' . $image_ratio : ''); ?>">
<?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
</div>
<?php endif; ?>
<div class="section panel overflow-hidden py-4 lg:py-6">
    <div class="<?php echo esc_attr(implode(' ', $container_classes)); ?>">
        <div class="row g-4 lg:g-6">
            <?php if($layout === 'layout-2' && $show_featured_image) : ?>
            <div class="col-12">
                <div class="post-thumbnail featured-large<?php echo ($image_ratio ? ' ratio ' . $image_ratio : ''); ?>">
                <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="<?php echo esc_attr($content_class); ?> <?php echo $sidebar_position === 'left' && $show_sidebar ? 'order-lg-2' : ''; ?>">
                <main id="primary" class="site-main">
                    <?php
                    while (have_posts()) :
                        the_post();
                        
                        // Pass layout to the template
                        set_query_var('post_layout', $layout);
                        get_template_part('template-parts/content/post');
                        
                        // If comments are open or we have at least one comment, load up the comment template.
                        if (comments_open() || get_comments_number()) :
                            if (Template_Helper::should_show('blog_post_comments_form')) {
                                comments_template();
                            }
                        endif;
                        
                    endwhile; // End of the loop.
                    ?>
                </main><!-- #main -->
            </div>
            
            <?php if ($show_sidebar) : ?>
                <div class="<?php echo esc_attr($sidebar_class); ?> <?php echo $sidebar_position === 'left' ? 'order-lg-1' : ''; ?>">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php

// After Single Post
Hooks::do_action('after_single_post');

get_footer();