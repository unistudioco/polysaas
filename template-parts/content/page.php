<?php
/**
 * Template part for displaying page content
 *
 * @package Polysaas
 */

?>

<div>
    <article id="post-<?php the_ID(); ?>" <?php post_class('page-article'); ?>>
        <?php if (has_post_thumbnail() && !is_front_page()) : ?>
            <div class="page-thumbnail mb-4">
                <?php the_post_thumbnail('large', ['class' => 'w-full h-auto']); ?>
            </div>
        <?php endif; ?>

        <div class="entry-content prose dark:prose-invert max-w-none">
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

        <?php if (get_edit_post_link()) : ?>
            <footer class="entry-footer mt-4 pt-3 border-top">
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
</div>