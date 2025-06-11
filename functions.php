<?php
/**
 * Theme functions and definitions
 *
 * @package Polysaas
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define base theme constants first
define('THEME_VERSION', '1.0.0');
define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());
define('THEME_INC', THEME_DIR . '/inc');
define('THEME_ASSETS', THEME_URI . '/assets');

// Composer autoload if it exists
if (file_exists(THEME_DIR . '/vendor/autoload.php')) {
    require_once THEME_DIR . '/vendor/autoload.php';
}

// Load core classes
require_once THEME_INC . '/core/class-config.php';
require_once THEME_INC . '/core/class-loader.php';
require_once THEME_INC . '/core/class-init.php';

// Load Typography Output
require_once THEME_INC . '/customizer/output.php';

// Register Autoloader
Polysaas\Core\Loader::register();

// Initialize theme
Polysaas\Core\Init::init();