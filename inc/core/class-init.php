<?php
namespace Polysaas\Core;

use Polysaas\Customizer\Manager;
use Polysaas\Setup\Setup;
use Polysaas\Setup\Enqueue;
use Polysaas\Setup\Template_Parts;
use Polysaas\Setup\Woocommerce_Setup;

/**
 * Theme Initialization
 *
 * @package Polysaas
 */
class Init {
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    private static function get_services() {
        return [
            Theme_Settings::getInstance(),
            Theme_Functions::getInstance(),
            Template_Helper::class,
            Setup::class,
            Enqueue::class,
            Manager::class,
            Template_Parts::class,
            Woocommerce_Setup::class,
        ];
    }

    /**
     * Loop through the classes, initialize them, and call the register() method if it exists
     * @return void
     */
    public static function register_services() {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param mixed $class class from the services array or singleton instance
     * @return object class instance
     */
    private static function instantiate($class) {
        // If $class is already an object (singleton instance), return it
        if (is_object($class)) {
            return $class;
        }
        // Otherwise create new instance
        return new $class();
    }

    /**
     * Initialize the theme
     */
    public static function init() {
        // Initialize General Settings first
        new \Polysaas\Settings\General_Settings();
        new \Polysaas\Settings\Custom_Fonts();

        // Define constants
        Config::define_constants();
        
        // Register services
        self::register_services();
        
        // Load files
        self::load_files();
    }

    /**
     * Load required files
     */
    private static function load_files() {
        require_once THEME_INC . '/core/class-functions.php';
        require_once THEME_INC . '/core/class-hooks.php';
        require_once THEME_INC . '/template-functions.php';
        require_once THEME_INC . '/template-tags.php';
    }
}