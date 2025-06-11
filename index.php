<?php
/**
 * The template for displaying archive pages
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Template_Helper;

get_header();

// Get archive settings
$sidebar_position = Template_Helper::get_archive_sidebar_position();

// Define column classes based on sidebar
$content_class = 'col-12';
$sidebar_class = 'col-lg-4';

if ($sidebar_position !== 'disabled') {
    $content_class = 'col-lg-8';
}

// Render page header
Hooks::do_action('page_header_content');

// Before Archive/Index Content
Hooks::do_action('before_archive_content');
?>

<div class="section panel overflow-hidden py-4 lg:py-6 xl:py-8">
    <div class="container">
        <div class="row g-4 lg:g-6" data-uc-grid>
            <div class="<?php echo esc_attr($content_class); ?> <?php echo $sidebar_position === 'left' ? 'order-lg-2' : ''; ?>">
                <main id="primary" class="site-main">
                    <?php get_template_part('template-parts/content/archive-loop'); ?>
                </main>
            </div>
            
            <?php if ($sidebar_position !== 'disabled') : ?>
                <div class="<?php echo esc_attr($sidebar_class); ?> <?php echo $sidebar_position === 'left' ? 'order-lg-1' : ''; ?>">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// After Archive/Index Content
Hooks::do_action('after_archive_content');

get_footer();