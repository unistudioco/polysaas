<?php
namespace Polysaas\Core;

/**
 * Hooks Manager
 *
 * @package Polysaas
 */
class Hooks {
    /**
     * Register a hook name
     *
     * @param string $name Hook name without prefix
     * @return string Prefixed hook name
     */
    public static function name($name) {
        return Config::prefix($name);
    }

    /**
     * Do action with prefixed hook name
     *
     * @param string $name Hook name without prefix
     * @param mixed  ...$args Arguments to pass to the hook
     */
    public static function do_action($name, ...$args) {
        $hook_name = self::name($name);
        do_action($hook_name, ...$args);
    }

    /**
     * Add action with prefixed hook name
     *
     * @param string   $name     Hook name without prefix
     * @param callable $callback Function to be called
     * @param int      $priority Priority of the action
     * @param int      $args     Number of arguments the function accepts
     */
    public static function add_action($name, $callback, $priority = 10, $args = 1) {
        $hook_name = self::name($name);
        
        // If callback is a string, assume it's a function name that needs prefixing
        if (is_string($callback) && strpos($callback, Config::prefix('')) === false) {
            $callback = Config::prefix($callback);
        }
        
        add_action($hook_name, $callback, $priority, $args);
    }

    /**
     * Check if hook has any actions
     *
     * @param string $name Hook name without prefix
     * @return bool True if the hook has actions
     */
    public static function has_action($name) {
        $hook_name = self::name($name);
        $has = has_action($hook_name);
        return $has;
    }

    /**
     * Apply filters with prefixed hook name
     *
     * @param string $name Hook name without prefix
     * @param mixed  $value Value to filter
     * @param mixed  ...$args Additional arguments
     * @return mixed Filtered value
     */
    public static function apply_filters($name, $value, ...$args) {
        $hook_name = self::name($name);
        return apply_filters($hook_name, $value, ...$args);
    }

    /**
     * Add filter with prefixed hook name
     *
     * @param string   $name     Hook name without prefix
     * @param callable $callback Function to be called
     * @param int      $priority Priority of the filter
     * @param int      $args     Number of arguments the function accepts
     */
    public static function add_filter($name, $callback, $priority = 10, $args = 1) {
        $hook_name = self::name($name);
        add_filter($hook_name, $callback, $priority, $args);
    }
    
    /**
     * Render a global section with display conditions
     * 
     * @param string $section_type The type of section ('header', 'footer', 'page_cover')
     * @param string $default_content Default content to display if no section is found
     * @return string The rendered content
     */
    public static function render_global_section($section_type, $default_content = '') {
        // Check if the UniStudio Core plugin is active
        if (!class_exists('\UniStudioCore\Global_Sections')) {
            return $default_content;
        }
        
        // Get the global sections instance
        $global_sections = \UniStudioCore\Global_Sections::getInstance();
        
        // Use the render_template_section method to render the section
        return $global_sections->render_template_section($section_type, $default_content);
    }
}