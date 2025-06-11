<?php
namespace Polysaas\Customizer\Sections;

class Global_Colors_Fonts extends Section_Base {

    /**
     * Section panel
     */
    protected $panel = 'theme_options';

    /**
     * Section ID
     */
    protected $id = 'global_colors_fonts';

    /**
     * Section title
     */
    protected $title = 'Design System';

    /**
     * Section priority
     */
    protected $priority = 10;

    /**
     * Section description
     */
    protected $description = '';
    
    /**
     * Get the tabs args
     */
    protected function get_args() {
        $args = parent::get_args();
        
        // Add tabs configuration
        $args['tabs'] = [
            'colors' => [
                'label' => __('Colors', $this->get_text_domain()),
            ],
            'fonts' => [
                'label' => __('Typography', $this->get_text_domain()),
            ],
        ];
        
        return $args;
    }
    
}