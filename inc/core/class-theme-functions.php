<?php
namespace Polysaas\Core;

/**
 * Implement ACF field values in theme
 */
class Theme_Functions {
    private static $instance = null;
    
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {

        add_filter('body_class', [$this, 'add_custom_body_classes']);

    }

    /**
     * Safely get ACF field value
     */
    public function get_field_value($field_name, $post_id = false, $default = null) {
        if (!function_exists('get_field')) {
            return $default;
        }
        return get_field($field_name, $post_id) ?: $default;
    }

    /**
     * Get page title visibility setting
     * Used in template files to determine whether to show the page title
     */
    public function get_page_header_visibility() {
        if (!is_singular()) {
            return true;
        }

        return $this->get_field_value('page_header_layout', false, 'default');
    }

    /**
     * Helper function to check if page title should be displayed
     */
    public static function should_render_page_header() {
        $instance = self::getInstance();
        return $instance->get_page_header_visibility() === true;
    }

    /**
     * Add custom body classes
     */
    public function add_custom_body_classes($classes) {
        if (!is_singular()) {
            return $classes;
        }

        // Add custom body class if set
        $custom_class = $this->get_field_value('body_custom_class');
        if ($custom_class) {
            $classes[] = esc_attr($custom_class);
        }

        // Add page cover class if enabled
        $page_header = $this->get_field_value('page_header');
        if ($page_header) {
            $classes[] = 'has-page-cover';
        }

        return $classes;
    }

    /**
     * Get custom excrept length
     */
    public static function get_custom_excerpt($limit) {

        $excerpt = explode(' ', get_the_excerpt(), $limit);
        
        if (count($excerpt)>=$limit) {
        
            array_pop($excerpt);
        
            $excerpt = implode(" ",$excerpt).'..';
        
        } else {
        
            $excerpt = implode(" ",$excerpt);
        
        }       
                
        echo $excerpt;

    }

}