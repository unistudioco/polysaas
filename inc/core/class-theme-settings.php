<?php
namespace Polysaas\Core;

use Polysaas\Core\Config;
use Polysaas\Core\Hooks;

/**
 * Theme Settings Manager
 */
class Theme_Settings {
    private static $instance = null;
    private $settings_pages = [];
    private $current_page = '';
    private $sections = [];
    private $fields = [];
    private $tabs = [];
    private $capability = 'manage_options';
    
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', [$this, 'register_menu'], 5);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        
        // Allow plugins/theme to register settings using our custom hooks
        Hooks::do_action('register_theme_settings', $this);
        Hooks::add_action('after_theme_settings_menu', [$this, 'add_menu_links']);

        
        // Get current page
        $this->current_page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';
    }

    public function add_menu_links($parent_slug) {
        if (!$parent_slug) {
            return;
        }
        add_submenu_page(
            $parent_slug,
            __('Library', 'unistudio-core'),
            __('Library', 'unistudio-core'),
            'manage_options',
            'customize.php'
        );
        add_submenu_page(
            $parent_slug,
            __('Demo Importer', 'unistudio-core'),
            __('Demo Importer', 'unistudio-core'),
            'manage_options',
            'customize.php'
        );
        add_submenu_page(
            $parent_slug,
            __('Customizer', 'unistudio-core'),
            __('Customizer', 'unistudio-core'),
            'manage_options',
            'customize.php'
        );
    }

    public function register_menu() {
        // Add main menu
        $main_menu = add_menu_page(
            sprintf(__('%s Theme', Config::get('text_domain')), Config::get('name')),
            sprintf(__('%s Theme', Config::get('text_domain')), Config::get('name')),
            $this->capability,
            Config::prefix('general'), // This is important
            [$this, 'render_page'],
            'dashicons-tide',
            59
        );
    
        // Update the first menu item to match the parent
        if (isset($this->settings_pages[Config::prefix('general')])) {
            $page = $this->settings_pages[Config::prefix('general')];
            add_submenu_page(
                Config::prefix('general'),
                $page['title'],
                $page['menu_title'],
                $this->capability,
                Config::prefix('general'),
                [$this, 'render_page']
            );
        }
    
        // Register other submenu pages
        foreach ($this->settings_pages as $page_id => $page) {
            if ($page_id === Config::prefix('general')) {
                continue; // Skip the main page as we've already added it
            }
            add_submenu_page(
                Config::prefix('general'),
                $page['title'],
                $page['menu_title'],
                $this->capability,
                $page['id'],
                [$this, 'render_page']
            );
        }
        
        // Let others add their submenu pages using our hooks
        Hooks::do_action('after_theme_settings_menu', Config::prefix('general'));
    }

    public function add_settings_page($id, $args = []) {
        $defaults = [
            'title' => '',
            'menu_title' => '',
            'description' => '',
            'position' => 10
        ];
        
        $args = wp_parse_args($args, $defaults);
        $args['id'] = Config::prefix($id);
        
        $this->settings_pages[$args['id']] = $args;
        
        return $this;
    }

    public function add_section($id, $args = []) {
        $defaults = [
            'title' => '',
            'description' => '',
            'page' => '',
            'tab' => '',  // Add tab support
            'position' => 10
        ];
        
        $args = wp_parse_args($args, $defaults);
        $args['id'] = Config::prefix($id);
        
        // Store the section
        $this->sections[$args['id']] = $args;
        
        return $this;
    }

    public function add_field($section_id, $id, $args = []) {
        $defaults = [
            'type' => 'text',
            'label' => '',
            'description' => '',
            'default' => '',
            'sanitize_callback' => '',
            'render_callback' => '',
            'position' => 10
        ];
        
        $args = wp_parse_args($args, $defaults);
        $args['id'] = Config::prefix($id);
        $args['section'] = Config::prefix($section_id);
        
        $this->fields[$args['section']][$args['id']] = $args;
        
        return $this;
    }

    public function add_tab($page_id, $tab_id, $args = []) {
        $defaults = [
            'title' => '',
            'description' => '',
            'priority' => 10
        ];
        
        $args = wp_parse_args($args, $defaults);
        $args['id'] = Config::prefix($tab_id);
        
        if (!isset($this->tabs[$page_id])) {
            $this->tabs[$page_id] = [];
        }
        
        $this->tabs[$page_id][$args['id']] = $args;
        
        return $this;
    }
    
    private function get_current_tab($page_id) {
        if (!isset($this->tabs[$page_id])) {
            return '';
        }
        
        $current = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';
        
        // If the current tab doesn't have a prefix, add it
        if ($current && strpos($current, Config::prefix('')) !== 0) {
            $current = Config::prefix($current);
        }
        
        // If current tab doesn't exist or empty, get first tab
        if (!$current || !isset($this->tabs[$page_id][$current])) {
            // Get first tab
            reset($this->tabs[$page_id]);
            return key($this->tabs[$page_id]);
        }
        
        return $current;
    }

    public function register_settings() {
        foreach ($this->settings_pages as $page_id => $page) {
            // Register setting
            register_setting(
                Config::prefix($page_id . '_settings'),
                Config::prefix($page_id . '_options'),
                [
                    'sanitize_callback' => [$this, 'sanitize_settings']
                ]
            );
            
            // Get current tab
            $current_tab = $this->get_current_tab($page_id);
            
            // Register sections
            foreach ($this->sections as $section_id => $section) {
                // Only register sections for current page and tab
                if ($section['page'] === $page_id && 
                    (!$section['tab'] || $section['tab'] === $current_tab)) {
                    
                    add_settings_section(
                        $section_id,
                        $section['title'],
                        function() use ($section) {
                            if (!empty($section['description'])) {
                                echo '<p class="section-description" style="margin-top: 0;">' . esc_html($section['description']) . '</p>';
                            }
                            // Allow additional content via hooks
                            Hooks::do_action('settings_section_content', $section);
                        },
                        Config::prefix($page_id . '_settings')
                    );
                    
                    // Register fields for this section
                    if (isset($this->fields[$section_id])) {
                        foreach ($this->fields[$section_id] as $field_id => $field) {
                            add_settings_field(
                                $field_id,
                                $field['label'],
                                [$this, 'render_field'],
                                Config::prefix($page_id . '_settings'),
                                $section_id,
                                $field
                            );
                        }
                    }
                }
            }
        }
    }

    public function render_page() {
        $this->debug_info(); // Add this line
        $current_page = $this->current_page;
        $page = $this->settings_pages[$current_page] ?? null;
        
        if (!$page) {
            wp_die(esc_html__('Invalid settings page.', Config::get('text_domain')));
        }
        
        // Allow pre-render actions
        Hooks::do_action('before_settings_page_render', $current_page);
        
        ?>
        <div class="<?php echo esc_attr(Config::prefix('dashboard', 'class')); ?> wrap <?php echo esc_attr(Config::prefix('settings-wrap', 'class')); ?>">

            <div class="page-header">
                <h1 class="settings-title"><span class="icon dashicons dashicons-tide"></span><?php echo esc_html($page['title']); ?></h1>
                
                <?php if (!empty($page['description'])) : ?>
                    <p class="page-description"><?php echo esc_html($page['description']); ?></p>
                <?php endif; ?>
            </div>
            
            <?php if (isset($this->tabs[$current_page]) && count($this->tabs[$current_page]) > 1) : ?>
            <div class="tabs-content">
            <?php endif; ?>
            
                <?php 
                Hooks::do_action('before_settings_tabs', $current_page);
                $this->render_tabs();
                Hooks::do_action('after_settings_tabs', $current_page);
                ?>

                <form class="<?php echo esc_attr(Config::prefix('settings-form', 'class')); ?>" method="post" action="options.php">
                    <?php
                    Hooks::do_action('before_settings_fields', $current_page);
                    
                    settings_fields(Config::prefix($current_page . '_settings'));
                    do_settings_sections(Config::prefix($current_page . '_settings'));
                    
                    Hooks::do_action('before_settings_submit', $current_page);
                    submit_button(
                        __('Save Changes', 'unistudio-core'),
                        Config::prefix('button', 'class') . ' btn-md btn-primary',
                        'submit',
                        false,
                        ['id' => Config::prefix('save_submit')]
                    );
                    Hooks::do_action('after_settings_submit', $current_page);
                    ?>
                </form>

            <?php if (isset($this->tabs[$current_page]) && count($this->tabs[$current_page]) > 1) : ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        
        // Allow post-render actions
        Hooks::do_action('after_settings_page_render', $current_page);
    }

    private function render_tabs() {
        $current_page = $this->current_page;
        
        // Render page tabs if we have multiple pages
        if (count($this->settings_pages) > 1) {
            Hooks::do_action('before_settings_tabs_content');
            
            echo '<h2 class="nav-tab-wrapper">';
            
            foreach ($this->settings_pages as $page_id => $page) {
                $active = ($current_page === $page_id) ? 'nav-tab-active' : '';
                $url = admin_url('admin.php?page=' . $page_id);
                
                printf(
                    '<a href="%s" class="nav-tab %s">%s</a>',
                    esc_url($url),
                    esc_attr($active),
                    esc_html($page['menu_title'])
                );
            }
            
            echo '</h2>';
            
            Hooks::do_action('after_settings_tabs_content');
        }
        
        // Render section tabs if we have them for current page
        if (isset($this->tabs[$current_page]) && count($this->tabs[$current_page]) > 1) {
            $current_tab = $this->get_current_tab($current_page);
            
            echo '<div class="tabs-nav nav-tab-wrapper">';
            
            foreach ($this->tabs[$current_page] as $tab_id => $tab) {
                $active = ($current_tab === $tab_id) ? 'nav-tab-active' : '';
                // Remove the prefix from tab_id before adding to URL
                $tab_slug = str_replace(Config::prefix(''), '', $tab_id);
                // Also remove any remaining underscores from the beginning
                $tab_slug = ltrim($tab_slug, '_');
                $url = add_query_arg('tab', $tab_slug, admin_url('admin.php?page=' . $current_page));
                
                printf(
                    '<a href="%s" class="nav-tab %s">%s</a>',
                    esc_url($url),
                    esc_attr($active),
                    esc_html($tab['title'])
                );
            }
            
            echo '</div>';
        }
    }   

    public function render_field($args) {
        $field_type = $args['type'];
        $field_id = $args['id'];
        $section_id = $args['section'];
        $page_id = $this->get_page_by_section($section_id);
        
        // Get the current value
        $options = get_option(Config::prefix($page_id . '_options'), []);
        $value = $options[$field_id] ?? $args['default'];
        
        // Allow pre-field content
        Hooks::do_action('before_settings_field_render', $args, $value);
        
        // Check for custom render callback
        if (!empty($args['render_callback']) && is_callable($args['render_callback'])) {
            call_user_func($args['render_callback'], $args, $value);
        } else {
            // Use pre-built field renderer if available
            $renderer_method = 'render_' . $field_type . '_field';
            if (method_exists($this, $renderer_method)) {
                $this->$renderer_method($args, $value);
            } else {
                // Allow custom field types via hooks
                Hooks::do_action('render_custom_field_type', $field_type, $args, $value);
            }
        }
        
        // Add description if exists
        if (!empty($args['description'])) {
            printf(
                '<p class="description">%s</p>',
                esc_html($args['description'])
            );
        }
        
        // Allow post-field content
        Hooks::do_action('after_settings_field_render', $args, $value);
    }

    private function render_text_field($args, $value) {
        $name = Config::prefix($args['page'] . '_options') . '[' . $args['id'] . ']';
        $classes = ['regular-text'];
        
        if (!empty($args['class'])) {
            $classes[] = $args['class'];
        }
        
        // Allow modifying classes via hooks
        $classes = Hooks::apply_filters('settings_text_field_classes', $classes, $args);
        
        printf(
            '<input type="text" id="%1$s" name="%2$s" value="%3$s" class="%4$s" %5$s>',
            esc_attr($args['id']),
            esc_attr($name),
            esc_attr($value),
            esc_attr(implode(' ', $classes)),
            isset($args['attrs']) ? $this->parse_attrs($args['attrs']) : ''
        );
    }
    
    private function render_select_field($args, $value) {
        if (empty($args['choices']) || !is_array($args['choices'])) {
            return;
        }
    
        $name = Config::prefix($args['page'] . '_options') . '[' . $args['id'] . ']';
        $classes = ['regular-select'];
        
        if (!empty($args['class'])) {
            $classes[] = $args['class'];
        }
        
        // Allow modifying classes via hooks
        $classes = Hooks::apply_filters('settings_select_field_classes', $classes, $args);
        
        printf(
            '<select id="%1$s" name="%2$s" class="%3$s" %4$s>',
            esc_attr($args['id']),
            esc_attr($name),
            esc_attr(implode(' ', $classes)),
            isset($args['attrs']) ? $this->parse_attrs($args['attrs']) : ''
        );
        
        // Add placeholder if exists
        if (!empty($args['placeholder'])) {
            printf(
                '<option value="">%s</option>',
                esc_html($args['placeholder'])
            );
        }
        
        // Add options
        foreach ($args['choices'] as $choice_value => $choice_label) {
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr($choice_value),
                selected($value, $choice_value, false),
                esc_html($choice_label)
            );
        }
        
        echo '</select>';
    }

    private function get_page_by_section($section_id) {
        return $this->sections[$section_id]['page'] ?? '';
    }

    private function parse_attrs($attrs) {
        if (!is_array($attrs)) {
            return '';
        }
        
        $html = '';
        foreach ($attrs as $key => $value) {
            $html .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
        }
        
        return $html;
    }

    public function sanitize_settings($input) {
        if (!is_array($input)) {
            return [];
        }
        
        $output = [];
        
        foreach ($input as $key => $value) {
            if (isset($this->fields[$key]['sanitize_callback']) && is_callable($this->fields[$key]['sanitize_callback'])) {
                $output[$key] = call_user_func($this->fields[$key]['sanitize_callback'], $value);
            } else {
                $output[$key] = sanitize_text_field($value);
            }
        }
        
        return $output;
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, Config::prefix('general')) === false) {
            return;
        }
        
        wp_enqueue_style(
            Config::prefix('admin-settings', 'id'),
            THEME_ASSETS . '/css/admin/settings.css',
            [],
            Config::get('version')
        );
        
        wp_enqueue_script(
            Config::prefix('admin-settings', 'id'),
            THEME_ASSETS . '/js/admin/settings.js',
            ['jquery'],
            Config::get('version'),
            true
        );
        
        // Let others add their assets using our hooks
        Hooks::do_action('theme_settings_enqueue_assets', $hook);
    }
    
    // Add this to Theme_Settings class
    private function debug_info() {
        error_log('Current Page: ' . $this->current_page);
        error_log('Settings Pages: ' . print_r($this->settings_pages, true));
        error_log('Tabs: ' . print_r($this->tabs, true));
        error_log('Sections: ' . print_r($this->sections, true));
        error_log('Fields: ' . print_r($this->fields, true));
    }

}