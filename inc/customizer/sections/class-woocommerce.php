<?php
namespace Polysaas\Customizer\Sections;

use Polysaas\Core\Config;

class Woocommerce extends Section_Base {

    /**
     * Section panel
     */
    protected $panel = 'theme_options';

    /**
     * Section ID
     */
    protected $id = 'woocommerce';

    /**
     * Section title
     */
    protected $title = 'WooCommerce';

    /**
     * Section priority
     */
    protected $priority = 80;

    /**
     * Section description
     */
    protected $description = '';
    
    /**
     * Register section only if WooCommerce is active
     */
    protected function register() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        parent::register();
    }
    
    /**
     * Get the tabs args
     */
    protected function get_args() {
        $args = parent::get_args();
        
        // Add tabs configuration
        $args['tabs'] = [
            'general' => [
                'label' => __('General', $this->get_text_domain()),
            ],
            'design' => [
                'label' => __('Design', $this->get_text_domain()),
            ],
        ];
        
        return $args;
    }
}