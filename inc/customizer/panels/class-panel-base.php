<?php
namespace Polysaas\Customizer\Panels;

use Polysaas\Core\Config;

/**
 * Base Panel Class
 *
 * @package Polysaas
 */
abstract class Panel_Base {
    /**
     * Panel ID
     */
    protected $id = '';

    /**
     * Panel title
     */
    protected $title = '';

    /**
     * Panel priority
     */
    protected $priority = 10;

    /**
     * Panel description
     */
    protected $description = '';

    /**
     * Panel capabilities
     */
    protected $capability = 'edit_theme_options';

    /**
     * Theme mode
     */
    protected $theme_supports = '';

    /**
     * Active callback
     */
    protected $active_callback = '';

    /**
     * Constructor
     */
    public function __construct() {
        $this->register();
    }

    /**
     * Register panel
     */
    protected function register() {
        if (empty($this->id)) {
            return;
        }

        new \Kirki\Panel(
            Config::prefix($this->id),
            $this->get_args()
        );
    }

    /**
     * Get panel arguments
     */
    protected function get_args() {
        $args = [
            'priority'        => $this->priority,
            'title'          => $this->get_title(),
            'capability'     => $this->capability,
        ];

        // Add description if set
        if (!empty($this->description)) {
            $args['description'] = $this->get_description();
        }

        // Add theme supports if set
        if (!empty($this->theme_supports)) {
            $args['theme_supports'] = $this->theme_supports;
        }

        // Add active callback if set
        if (!empty($this->active_callback)) {
            $args['active_callback'] = $this->active_callback;
        }

        return $args;
    }

    /**
     * Get panel title
     */
    protected function get_title() {
        return __($this->title, Config::get('text_domain'));
    }

    /**
     * Get panel description
     */
    protected function get_description() {
        return __($this->description, Config::get('text_domain'));
    }
}