<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Polysaas
 */

use Polysaas\Core\Config;
use Polysaas\Core\Functions;
use Polysaas\Core\Hooks;

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
Functions::register('body_classes', function($classes) {
    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    // Adds a class of no-sidebar when there is no sidebar present.
    if ( ! is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'no-sidebar';
    }

    return $classes;
});
Hooks::add_filter( 'body_class', 'body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
Functions::register('pingback_header', function($classes) {
    if ( is_singular() && pings_open() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
});
Hooks::add_filter( 'wp_head', 'pingback_header' );

/**
 * Global Sections Integration
 */

/**
 * Render the header content
 */
Functions::register('header_content_display', function() {
    // Get the default content by loading the theme template
    ob_start();
    get_template_part('template-parts/header/default');
    $default_content = ob_get_clean();
    
    // Check for page-specific header override
    $header_type = '';
    
    if (is_singular()) {
        $header_type = Functions::call('get_acf_value', 'header_type', false, 'inherit');
        
        // If page-specific setting is set to custom, get the section ID
        if ($header_type === 'custom') {
            $header_id = Functions::call('get_acf_value', 'header_override');
            Functions::call('display_gs_override', $header_id);

            return;
        } elseif ($header_type === 'disabled') {
            // If header is disabled for this page, return empty
            return;
        } elseif ($header_type === 'theme') {
            // If explicitly set to theme header, use the default
            echo $default_content;
            return;
        }
    }
    
    // If no page-specific setting or set to inherit, use the theme setting
    // Fall back to global default from ACF options if available
    echo Hooks::render_global_section('header', $default_content);
});
Hooks::add_action('header_content_display', 'header_content_display');

/**
 * Render the footer content
 */
Functions::register('footer_content_display', function() {
    // Get the default content by loading the theme template
    ob_start();
    get_template_part('template-parts/footer/default');
    $default_content = ob_get_clean();
    
    // Check for page-specific footer override
    $footer_type = '';
    
    if (is_singular()) {
        $footer_type = Functions::call('get_acf_value', 'footer_type', false, 'inherit');
        
        // If page-specific setting is set to custom, get the section ID
        if ($footer_type === 'custom') {
            $footer_id = Functions::call('get_acf_value', 'footer_override');
            Functions::call('display_gs_override', $footer_id);

            return;
        } elseif ($footer_type === 'disabled') {
            // If footer is disabled for this page, return empty
            return;
        } elseif ($footer_type === 'theme') {
            // If explicitly set to theme footer, use the default
            echo $default_content;
            return;
        }
    }
    
    // If no page-specific setting or set to inherit, use the theme setting
    echo Hooks::render_global_section('footer', $default_content);

});
Hooks::add_action('footer_content_display', 'footer_content_display');

/**
 * Render the page cover content
 */
Functions::register('page_cover_content_display', function() {
    // Get the default content by loading the theme template
    ob_start();
    get_template_part('template-parts/common/page-cover');
    $default_content = ob_get_clean();
    
    // Use global section if available, otherwise use default
    echo Hooks::render_global_section('page_cover', $default_content);
});
Hooks::add_action('page_cover_content_display', 'page_cover_content_display');

/**
 * Render popups from global sections
 */
Functions::register('popup_content_display', function() {
    // Check if the UniStudio Core plugin is active
    if (!class_exists('\UniStudioCore\Global_Sections')) {
        return;
    }
    
    // Get the global sections instance
    $global_sections = \UniStudioCore\Global_Sections::getInstance();
    
    // If page-specific setting is set to disabled, return empty
    if (is_singular()) {
        $popup_disabled = Functions::call('get_acf_value', 'popup_disabled', false, false);
        if ($popup_disabled) {
            return;
        }
        
        // Check for page-specific popup override
        $popup_override = Functions::call('get_acf_value', 'popup_override');
        if ($popup_override) {
            Functions::call('render_unicore_modal', $popup_override);
            return;
        }
    }
    
    // Use the render_template_section method to get popup section
    $content = $global_sections->render_template_section('popup', '');
    if (!empty($content)) {
        echo '<div class="uc-modal" id="global-popup">' . $content . '</div>';
        echo '<script>UniCore.modal(document.getElementById("global-popup")).show();</script>';
    }
});
Hooks::add_action('wp_footer', 'popup_content_display', 30);

/**
 * Render Unicore Modal for a global section
 */
Functions::register('render_unicore_modal', function($section_id) {
    if (!$section_id || !class_exists('\UniStudioCore\Global_Sections')) {
        return;
    }
    
    $content = '';
    
    if (class_exists('\Elementor\Plugin')) {
        $elementor = \Elementor\Plugin::instance();
        $content = $elementor->frontend->get_builder_content_for_display($section_id);
    } else {
        $global_sections = \UniStudioCore\Global_Sections::getInstance();
        $content = $global_sections->render_global_section($section_id);
    }
    
    if (empty($content)) {
        return;
    }
    
    // Generate a unique ID for this modal
    $modal_id = 'unicore-modal-' . $section_id;
    
    // Output the modal HTML
    echo '<div class="uc-modal" id="' . esc_attr($modal_id) . '">' . $content . '</div>';
    
    // Initialize the modal with JavaScript
    echo '<script>UniCore.modal(document.getElementById("' . esc_attr($modal_id) . '")).show();</script>';
});

/**
 * Helper function to safely display custom ACF override Global Section template
 */
Functions::register('display_gs_override', function($section_id) {
    if ($section_id && $section_id !== 'theme' && class_exists('\UniStudioCore\Global_Sections')) {
        $global_sections = \UniStudioCore\Global_Sections::getInstance();
        
        if (class_exists('\Elementor\Plugin')) {
            $elementor = \Elementor\Plugin::instance();
            echo $elementor->frontend->get_builder_content_for_display($section_id);
        } else {
            echo $global_sections->render_global_section($section_id);
        }
    }
});

/**
 * Helper function to safely check ACF field value
 */
Functions::register('get_acf_value', function($field_name, $post_id = false, $default = null) {
    if (!function_exists('get_field')) {
        return $default;
    }

    return get_field($field_name, $post_id) ?: $default;
});

/**
 * Helper function to safely check ACF options value
 */
Functions::register('get_acf_option', function($field_name, $default = null) {
    if (!function_exists('get_field')) {
        return $default;
    }

    return get_field($field_name, 'option') ?: $default;
});

/**
 * Function to customize the comment form fields
 */
Functions::register('customize_comment_form', function($fields) {
    // Add classes to the comment form fields
    foreach ($fields as $key => $field) {
        if (in_array($key, array('comment_field', 'author', 'email', 'url'))) {
            $fields[$key] = preg_replace('/(<input|<textarea)/', '$1 class="form-control"', $field);
        }
    }
    
    return $fields;
});

/**
 * Function to customize the comment form defaults
 */
Functions::register('customize_comment_form_defaults', function($defaults) {
    // Change the submit button to match your theme styles
    $defaults['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />';
    $defaults['submit_field'] = '<div class="form-submit">%1$s %2$s</div>';
    $defaults['class_submit'] = 'submit btn btn-primary';
    
    // Change title reply classes
    $defaults['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title">';
    $defaults['title_reply_after'] = '</h3>';
    
    return $defaults;
});

/**
 * Custom callback to display comments
 */
Functions::register('custom_comment_template', function($comment, $args, $depth) {
    // Get comment classes
    $comment_class = empty($args['has_children']) ? '' : 'parent';
    
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class($comment_class); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <div class="comment-meta">
                <div class="comment-author vcard">
                    <?php 
                    if ($args['avatar_size'] != 0) {
                        echo get_avatar($comment, $args['avatar_size']); 
                    }
                    ?>
                    <b class="fn"><?php comment_author_link(); ?></b>
                </div><!-- .comment-author -->

                <div class="comment-metadata">
                    <a href="<?php echo esc_url(get_comment_link($comment)); ?>">
                        <time datetime="<?php comment_time('c'); ?>">
                            <?php printf(_x('%1$s at %2$s', '1: date, 2: time', 'polysaas'), get_comment_date(), get_comment_time()); ?>
                        </time>
                    </a>
                    <?php edit_comment_link(esc_html__('Edit', 'polysaas'), '<span class="edit-link">', '</span>'); ?>
                </div><!-- .comment-metadata -->

                <?php if ('0' == $comment->comment_approved) : ?>
                <p class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'polysaas'); ?></p>
                <?php endif; ?>
            </div><!-- .comment-meta -->

            <div class="comment-content">
                <?php comment_text(); ?>
            </div><!-- .comment-content -->

            <?php
            comment_reply_link(array_merge($args, array(
                'add_below' => 'div-comment',
                'depth'     => $depth,
                'max_depth' => $args['max_depth'],
                'before'    => '<div class="reply">',
                'after'     => '</div>'
            )));
            ?>
        </article><!-- .comment-body -->
    <?php
});

/**
 * Hook the comment form modifications
 */
add_action('init', function() {
    add_filter('comment_form_fields', function($fields) {
        return Functions::call('customize_comment_form', $fields);
    });
    
    add_filter('comment_form_defaults', function($defaults) {
        return Functions::call('customize_comment_form_defaults', $defaults);
    });
    
    // Register the custom comment template
    add_filter('wp_list_comments_args', function($args) {
        $args['callback'] = function($comment, $args, $depth) {
            Functions::call('custom_comment_template', $comment, $args, $depth);
        };
        return $args;
    });
});

add_filter('polysaas_comment_form_args', function($args) {
    return $args;
});