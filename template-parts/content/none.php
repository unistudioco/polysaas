<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package Polysaas
 */
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title h3"><?php esc_html_e('Nothing Found', 'polysaas'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :
            printf(
                '<p>' . wp_kses(
                    /* translators: 1: link to WP admin new post page. */
                    __('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'polysaas'),
                    [
                        'a' => [
                            'href' => [],
                        ],
                    ]
                ) . '</p>',
                esc_url(admin_url('post-new.php'))
            );

        elseif (is_search()) :
            ?>
            <p class="mb-4"><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'polysaas'); ?></p>
            <?php
            get_search_form();
        else :
            ?>
            <p class="mb-4"><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'polysaas'); ?></p>
            <?php
            get_search_form();
        endif;
        ?>
    </div><!-- .page-content -->
</section><!-- .no-results -->