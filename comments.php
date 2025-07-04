<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Polysaas
 */

use Polysaas\Core\Hooks;

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php
    // Allow filtering the comments container opening
    Hooks::do_action('before_comments');
    
    if (have_comments()) :
        ?>
        <h2 class="comments-title">
            <?php
            $polysaas_comment_count = get_comments_number();
            if ('1' === $polysaas_comment_count) {
                printf(
                    /* translators: 1: title. */
                    esc_html__('One thought on &ldquo;%1$s&rdquo;', 'polysaas'),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf( 
                    /* translators: 1: comment count number, 2: title. */
                    esc_html(_nx(
                        '%1$s thought on &ldquo;%2$s&rdquo;',
                        '%1$s thoughts on &ldquo;%2$s&rdquo;',
                        $polysaas_comment_count,
                        'comments title',
                        'polysaas'
                    )),
                    number_format_i18n($polysaas_comment_count),
                    '<span>' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h2><!-- .comments-title -->

        <?php the_comments_navigation(); ?>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'avatar_size'=> 50,
                'short_ping' => true,
            ));
            ?>
        </ol><!-- .comment-list -->

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'polysaas'); ?></p>
            <?php
        endif;

    endif; // Check for have_comments().

    // Filter the comment form
    $comment_form_args = Hooks::apply_filters('comment_form_args', array(
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h3>',
        'class_form'         => 'comment-form',
        'class_submit'       => 'submit btn btn-primary',
    ));
    
    comment_form($comment_form_args);
    
    // Allow filtering the comments container closing
    Hooks::do_action('after_comments');
    ?>
</div><!-- #comments -->