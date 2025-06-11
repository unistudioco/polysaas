<?php
namespace Polysaas\Customizer\Sections;

use Polysaas\Core\Config;

/**
 * Base Section Class
 *
 * @package Polysaas
 */
abstract class Section_Base {
    /**
     * Section ID
     */
    protected $id = '';

    /**
     * Section title
     */
    protected $title = '';

    /**
     * Section priority
     */
    protected $priority = 10;

    /**
     * Section description
     */
    protected $description = '';

    /**
     * Section panel
     */
    protected $panel = '';

    /**
     * Section capabilities
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
     * Register section
     */
    protected function register() {
        if (empty($this->id)) {
            return;
        }

        new \Kirki\Section(
            Config::prefix($this->id),
            $this->get_args()
        );
    }

    /**
     * Get text domain
     */
    protected function get_text_domain() {
        return Config::get('text_domain');
    }

    /**
     * Get section arguments
     */
    protected function get_args() {
        $args = [
            'priority'        => $this->priority,
            'title'          => $this->get_title(),
            'capability'     => $this->capability,
        ];

        // Add panel if set
        if (!empty($this->panel)) {
            $args['panel'] = Config::prefix($this->panel);
        }
        

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
     * Get section title
     */
    protected function get_title() {
        return __($this->title, Config::get('text_domain'));
    }

    /**
     * Get section description
     */
    protected function get_description() {
        return __($this->description, Config::get('text_domain'));
    }
}