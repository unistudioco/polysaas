<?php
/**
 * Template part for displaying results in search pages
 */

use Polysaas\Core\Hooks;
use Polysaas\Core\Theme_Functions;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php Hooks::do_action('page_header_before'); ?>

    <?php if (Theme_Functions::should_render_page_header()) : ?>
    <header class="entry-header">
        <?php 
        if (!is_front_page()) :
            the_title('<h1 class="entry-title m-0">', '</h1>');
        endif;
        ?>
    </header>
    <?php endif; ?>

    <?php Hooks::do_action('page_header_after'); ?>

    <?php Hooks::do_action('page_content_before'); ?>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages([
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'polysaas'),
            'after'  => '</div>',
        ]);
        ?>
    </div>

    <?php Hooks::do_action('page_content_after'); ?>

    <?php if (get_edit_post_link()) : ?>
        <footer class="entry-footer">
            <?php
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post */
                        __('Edit <span class="screen-reader-text">%s</span>', 'polysaas'),
                        ['span' => ['class' => []]]
                    ),
                    wp_kses_post(get_the_title())
                ),
                '<span class="edit-link">',
                '</span>'
            );
            ?>
        </footer>
    <?php endif; ?>
</article>