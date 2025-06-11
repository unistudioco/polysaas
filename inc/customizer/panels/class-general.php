<?php
namespace Polysaas\Customizer\Panels;

class General extends Panel_Base {
    /**
     * Panel ID
     */
    protected $id = 'theme_options';

    /**
     * Panel title
     */
    protected $title = 'General Settings';

    /**
     * Panel priority
     */
    protected $priority = 10;

    /**
     * Panel description
     */
    protected $description = 'Customize your theme general settings.';
}