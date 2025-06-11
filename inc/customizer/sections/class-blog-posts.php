<?php
namespace Polysaas\Customizer\Sections;

class Blog_Posts extends Section_Base {

    /**
     * Section panel
     */
    protected $panel = 'theme_options';

    /**
     * Section ID
     */
    protected $id = 'blog_posts';

    /**
     * Section title
     */
    protected $title = 'Blog Posts Single';

    /**
     * Section priority
     */
    protected $priority = 50;

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