<?php
namespace Polysaas\Core;

/**
 * Helper Functions Manager
 *
 * @package Polysaas
 */
class Functions {
    /**
     * Store registered functions
     */
    private static $functions = [];

    /**
     * Register a new prefixed function
     *
     * @param string   $name     Function name without prefix
     * @param callable $callback Function callback
     */
    public static function register($name, $callback) {
        $prefixed_name = Config::prefix($name);        
        if (!function_exists($prefixed_name)) {
            self::$functions[$prefixed_name] = $callback;            
            // Create the function in global namespace
            $function = create_function_alias($prefixed_name, $callback);
        }
    }

    /**
     * Call a registered function
     *
     * @param string $name      Function name without prefix
     * @param array  $arguments Function arguments
     */
    public static function call($name, ...$arguments) {
        // Check if name already has prefix
        if (strpos($name, Config::prefix('')) === 0) {
            $prefixed_name = $name; // Already prefixed
        } else {
            $prefixed_name = Config::prefix($name);
        }        
        if (isset(self::$functions[$prefixed_name])) {
            return call_user_func_array(self::$functions[$prefixed_name], $arguments);
        }
        return null;
    }
}

/**
 * Helper function to create function alias
 */
function create_function_alias($name, $callback) {
    $namespace = Config::get('namespace');
    $class_name = "\\{$namespace}\\Core\\Functions";

    $eval_str = "
        function $name() {
            \$args = func_get_args();
            return call_user_func_array(['$class_name', 'call'], 
                array_merge(['$name'], \$args)
            );
        }
    ";
    
    return eval($eval_str);
}