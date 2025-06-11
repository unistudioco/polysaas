<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Template_Helper;

get_header();

// Define column classes based on sidebar
$content_class = 'col-12';

// Render page header
Hooks::do_action('page_header_content');

// Before Search Content (you can use the same hook as archive or create a specific one)
Hooks::do_action('before_search_content');
?>

<div class="section panel overflow-hidden py-4 lg:py-6 xl:py-8">
    <div class="container max-w-lg">
        <!-- Search title and form section -->
        <div class="search-header mb-4 lg:mb-6 xl:mb-8">
            <h1 class="page-title h5 fw-normal mb-2">
                <?php
                /* translators: %s: search query. */
                printf(esc_html__('Search Results for: %s', 'polysaas'), '<span class="search-query">"' . get_search_query() . '"</span>');
                ?>
            </h1>
            <div class="search-form-container mb-4">
                <?php get_search_form(); ?>
            </div>
        </div>
		<main id="primary" class="site-main">
			<?php 
			if (have_posts()) :
				get_template_part('template-parts/content/archive-loop');
			else :
			?>
				<div class="no-results">
					<div class="alert alert-info">
						<p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'polysaas'); ?></p>
					</div>
				</div>
			<?php
			endif;
			?>
		</main>
    </div>
</div>

<?php
// After Search Content
Hooks::do_action('after_search_content');

get_footer();