<?php
namespace Polysaas\Customizer\Sections;

class Global_Sections extends Section_Base {
    /**
     * Section panel
     */
    protected $panel = '';

    /**
     * Section ID
     */
    protected $id = 'global_sections';

    /**
     * Section title
     */
    protected $title = 'Global Sections';

    /**
     * Section priority
     */
    protected $priority = 20;

    /**
     * Section description
     */
    protected $description = 'Manage your global sections, templates, and layouts.';
}