<?php
/**
 * The template for displaying all pages
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Template_Helper;

get_header();

// Get page settings
$page_template = get_post_meta(get_the_ID(), 'page_template', true);
if (empty($page_template)) {
    $page_template = 'default'; // Default from customizer
}

// Define column classes based on template
$content_class = 'col-12';
$sidebar_class = 'col-lg-4';
$show_sidebar = false;

if ($page_template === 'sidebar-left' || $page_template === 'sidebar-right') {
    $content_class = 'col-lg-8';
    $show_sidebar = true;
}

// Render page header
Hooks::do_action('page_header_content');

// Before Page Content
Hooks::do_action('before_page_content');
?>

<div class="section panel overflow-hidden py-4 lg:py-6 xl:py-8">
    <div class="container">
        <div class="row g-4 lg:g-6">
            <div class="<?php echo esc_attr($content_class); ?> <?php echo $page_template === 'sidebar-left' ? 'order-lg-2' : ''; ?>">
                <main id="primary" class="site-main">
                    <?php
                    while (have_posts()) :
                        the_post();
                        
                        get_template_part('template-parts/content/page');
                        
                        // If comments are open or we have at least one comment, load up the comment template.
                        if (comments_open() || get_comments_number()) :
                            comments_template();
                        endif;
                        
                    endwhile; // End of the loop.
                    ?>
                </main><!-- #main -->
            </div>
            
            <?php if ($show_sidebar) : ?>
                <div class="<?php echo esc_attr($sidebar_class); ?> <?php echo $page_template === 'sidebar-left' ? 'order-lg-1' : ''; ?>">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php

// After Page Content
Hooks::do_action('after_page_content');

get_footer();