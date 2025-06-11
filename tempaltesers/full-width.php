<?php
/**
 * Templatessss Name: Full Width
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Theme_Functions;

get_header();
?>

<div class="section panel overflow-hidden">
    <div class="container max-w-full">
        <main id="primary" class="site-main">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content/page', 'full-width');

                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
            endwhile;
            ?>
        </main>
    </div>
</div>

<?php
get_footer();