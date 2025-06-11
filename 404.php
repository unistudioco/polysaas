<?php
namespace Polysaas;

use Polysaas\Core\Config;
use Polysaas\Core\Hooks;

/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Polysaas
 */

get_header();

// Before Error Page Content
Hooks::do_action('before_error_page_content');
?>

<main id="primary" class="site-main section py-6 lg:py-8 lg:pb-10 text-center min-h-500px vstack items-center">
    <div class="container">
        <section class="no-results not-found vstack gap-4 max-w-md mx-auto">
            <header class="error-page-header">
                <h2 class="display-1 text-gray-200 mb-2"><?php esc_html_e('404', 'polysaas'); ?></h2>
                <h1 class="error-page-title h2 fw-medium m-0"><?php esc_html_e('Oops! Page Not Found', 'polysaas'); ?></h1>
            </header>
            
            <div class="error-page-content">
                <p class="lead mb-5"><?php esc_html_e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'polysaas'); ?></p>
                
                <div class="search-form-container max-w-400px mx-auto mb-5">
                    <?php get_search_form(); ?>
                </div>
                
                <div class="error-page-actions hstack justify-center gap-3">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="uc-link border-bottom fw-normal pb-narrow fs-7">
                        <?php esc_html_e('Back to Home', 'polysaas'); ?>
                    </a>
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="uc-link border-bottom fw-normal pb-narrow fs-7">
                        <?php esc_html_e('Explore Blog', 'polysaas'); ?>
                    </a>
                </div>
            </div>
        </section>
    </div>
</main><!-- #main -->

<?php
// After Error Page Content
Hooks::do_action('after_error_page_content');

get_footer();