<?php
/**
 * Templatessss Name: Left Sidebar
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Theme_Functions;

get_header();
?>

<div class="section panel overflow-hidden">
    <div class="container max-w-2xl">
        <div class="row child-cols g-4 lg:gx-8" data-uc-grid>
            <!-- Sidebar Column -->
            <div class="lg:col-4">
                <?php get_sidebar(); ?>
            </div>
            
            <!-- Main Content Column -->
            <div class="lg:col-8">
                <main id="primary" class="site-main">
                    <?php
                    while (have_posts()) :
                        the_post();
                        get_template_part('template-parts/content/page');

                        // If comments are open or we have at least one comment, load up the comment template.
                        if (comments_open() || get_comments_number()) :
                            comments_template();
                        endif;
                    endwhile;
                    ?>
                </main>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();