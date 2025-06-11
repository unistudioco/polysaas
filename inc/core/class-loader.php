<?php
namespace Polysaas\Core;

/**
 * Class Loader
 * 
 * Simple PSR-4 autoloader for theme classes
 *
 * @package Polysaas
 */
class Loader {
    /**
     * An array of class name prefixes and their directories
     */
    protected static $prefixes = [];

    /**
     * Register loader with SPL autoloader stack
     */
    public static function register() {
        // Register base namespace
        self::add_namespace(
            Config::get('namespace'),
            get_template_directory() . '/inc'
        );

        // Register customizer namespace
        self::add_namespace(
            Config::get('namespace') . '\\Customizer',
            get_template_directory() . '/inc/customizer'
        );

        self::add_namespace(
            Config::get('namespace') . '\\Customizer\\Controls',
            get_template_directory() . '/inc/customizer/controls'
        );
        
        self::add_namespace(
            Config::get('namespace') . '\\Customizer\\Fields',
            get_template_directory() . '/inc/customizer/fields'
        );
        
        self::add_namespace(
            Config::get('namespace') . '\\Customizer\\Integrations',
            get_template_directory() . '/inc/customizer/integrations'
        );

        // Register the autoloader
        spl_autoload_register([__CLASS__, 'load_class']);
    }

    /**
     * Add a base directory for a namespace prefix
     */
    public static function add_namespace($prefix, $base_dir) {
        $prefix = trim($prefix, '\\') . '\\';
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';
        self::$prefixes[$prefix] = $base_dir;
    }

    /**
     * Load the class file for a given class name
     */
    public static function load_class($class) {        
        foreach (self::$prefixes as $prefix => $base_dir) {
            if (strpos($class, $prefix) === 0) {
                // Get the relative class name
                $relative_class = substr($class, strlen($prefix));
                
                // Split the relative class name into parts
                $parts = explode('\\', $relative_class);
                
                // Get the last part (the class name)
                $class_name = array_pop($parts);
                
                // Convert underscores to hyphens and lowercase
                $file_name = 'class-' . strtolower(str_replace('_', '-', $class_name));
                
                // Build the directory path
                $directory = $base_dir;
                if (!empty($parts)) {
                    $directory .= strtolower(
                        implode('/', array_map(function($part) {
                            return str_replace('_', '-', $part);
                        }, $parts))
                    ) . '/';
                }
                
                // Build the file path
                $file = $directory . $file_name . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
        }
        
        return false;
    }
}