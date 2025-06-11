<?php
/**
 * WP Widget: Recent Posts
 * 
 * @package Polysaas
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Polysaas_Recent_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'polysaas_recent_posts',
            'Polysaas Recent Posts',
            array('description' => 'Polysaas: Recent Posts Widget')
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // HTML for recent posts
        echo '<div class="polysaas-recent-posts">';
        $recent_posts = wp_get_recent_posts(array('numberposts' => 5));
        foreach ($recent_posts as $post) {
            echo '<div class="post-item">';
            echo '<a href="' . get_permalink($post['ID']) . '">' . $post['post_title'] . '</a>';
            echo '</div>';
        }
        echo '</div>';

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Recent Posts';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php 
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

function register_polysaas_recent_posts_widget() {
    register_widget('Polysaas_Recent_Posts_Widget');
}
add_action('widgets_init', 'register_polysaas_recent_posts_widget');
