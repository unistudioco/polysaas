<?php
namespace Polysaas\Core;

/**
 * Theme Configuration
 *
 * @package Polysaas
 */

class Config {
    /**
     * Theme configuration
     */
    private static $config = array(
        'prefix'     => 'polysaas',
        'namespace'  => 'Polysaas',
        'name'       => 'Polysaas',
        'version'    => '1.0.0',
        'text_domain'=> 'polysaas',
        'options'    => array(
            'primary_color'   => '#b180c4',
            'secondary_color' => '#101828',
            'body_font' => 'Tomato Grotesk',
            'heading_font' => 'Tomato Grotesk',
        )
    );

    /**
     * Get config value
     */
    public static function get($key = null) {
        if (is_null($key)) {
            return self::$config;
        }
        
        return isset(self::$config[$key]) ? self::$config[$key] : null;
    }

    /**
     * Get theme prefix
     */
    public static function prefix($string = '', $format = 'settings') {
        if (empty($string)) {
            return self::$config['prefix'];
        }

        // Clean the string first
        $clean_string = str_replace(['-', ' '], '_', $string);

        switch ($format) {
            case 'class':
                // For CSS classes: prefix-string
                return self::$config['prefix'] . '-' . str_replace('_', '-', $string);
            
            case 'id':
                // For HTML IDs: prefix_string (no double underscores)
                return self::$config['prefix'] . '_' . $clean_string;
            
            case 'settings':
            default:
                // For Kirki/WP settings: prefix_string
                return self::$config['prefix'] . '_' . $clean_string;
        }
    }

    /**
     * Get theme namespace
     */
    public static function namespace($path = '') {
        return !empty($path) ? self::$config['namespace'] . '\\' . $path : self::$config['namespace'];
    }

    /**
     * Define theme constants
     */
    public static function define_constants() {
        $constants = array(
            'THEME_PREFIX'    => self::$config['prefix'],
            'THEME_NAMESPACE' => self::$config['namespace'],
            'THEME_NAME'      => self::$config['name'],
            'THEME_VERSION'   => self::$config['version'],
            'THEME_DIR'       => get_template_directory(),
            'THEME_URI'       => get_template_directory_uri(),
            'THEME_ASSETS'    => get_template_directory_uri() . '/assets',
            'THEME_INC'       => get_template_directory() . '/inc',
        );

        foreach ($constants as $name => $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }
}