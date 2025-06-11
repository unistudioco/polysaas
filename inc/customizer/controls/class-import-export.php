<?php
namespace Polysaas\Customizer\Controls;

use Polysaas\Core\Config;

class Import_Export extends Control_Base {
    /**
     * Section ID
     */
    protected $section = 'import_export';

    /**
     * Register controls
     */
    public function register() {
        add_action('customize_controls_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_' . Config::prefix('export_settings'), [$this, 'export_settings']);
        add_action('wp_ajax_' . Config::prefix('import_settings'), [$this, 'import_settings']);
        add_action('wp_ajax_' . Config::prefix('reset_settings'), [$this, 'reset_settings']);

        $this->add_export_control();
        $this->add_divider_1();
        $this->add_import_control();
        $this->add_divider_2();
        $this->add_reset_control();
    }

    /**
     * Add export control
     */
    protected function add_export_control() {
        $this->add_control('Custom', [
            'settings' => $this->get_setting('export_button'),
            'default' => sprintf('
                <span class="customize-control-title">%s</span>
                <span class="description customize-control-description">%s</span>
                <input type="button" class="button" name="kirki-export-button" value="%s" />',
                __('Export', $this->get_text_domain()),
                __('Click the button below to export the customization settings for this theme.', $this->get_text_domain()),
                __('Export', $this->get_text_domain())
            ),
        ]);
    }

    /**
     * Add first divider
     */
    protected function add_divider_1() {
        $this->add_pro_control('Divider', [
            'settings' => $this->get_setting('import_export_sep_1'),
            'choices'  => [
                'color' => '#d5d5d5',
            ],
        ]);
    }

    /**
     * Add import control
     */
    protected function add_import_control() {
        $this->add_control('Custom', [
            'settings' => $this->get_setting('import_controls'),
            'default' => sprintf('
                <span class="customize-control-title">%s</span>
                <span class="description customize-control-description">%s</span>
                <div class="kirki-import-controls">
                    <input type="file" name="kirki-import-file" class="kirki-import-file" />
                    <label class="kirki-import-images">
                        <input type="checkbox" name="kirki-import-images" value="1" /> %s
                    </label>
                    %s
                </div>
                <div class="kirki-uploading">%s</div>
                <input type="button" class="button" name="kirki-import-button" value="%s" />',
                __('Import', $this->get_text_domain()),
                __('Upload a file to import customization settings for this theme.', $this->get_text_domain()),
                __('Download and import image files?', $this->get_text_domain()),
                wp_nonce_field(Config::prefix('importing'), 'nonce', true, false),
                __('Uploading...', $this->get_text_domain()),
                __('Import', $this->get_text_domain())
            ),
        ]);
    }

    /**
     * Add second divider
     */
    protected function add_divider_2() {
        $this->add_pro_control('Divider', [
            'settings' => $this->get_setting('import_export_sep_2'),
            'choices'  => [
                'color' => '#d5d5d5',
            ],
        ]);
    }

    /**
     * Add reset control
     */
    protected function add_reset_control() {
        $this->add_control('Custom', [
            'settings' => $this->get_setting('reset_controls'),
            'default' => sprintf('
                <span class="customize-control-title">%s</span>
                <span class="description customize-control-description">%s</span>
                <div class="kirki-reset-controls">
                    <input type="button" class="button button-danger" name="kirki-reset-button" value="%s" />
                    <input type="hidden" name="%s" value="%s" />
                </div>',
                __('Reset Settings', $this->get_text_domain()),
                __('Warning: This will reset all customization settings to their default values.', $this->get_text_domain()),
                __('Reset all settings', $this->get_text_domain()),
                Config::prefix('reset_nonce'),
                wp_create_nonce(Config::prefix('reset_nonce'))
            ),
        ]);
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            Config::prefix('import-export'),
            THEME_URI . '/assets/js/admin/customizer-import-export.js',
            ['jquery', 'customize-controls'],
            THEME_VERSION,
            true
        );
    
        wp_localize_script(
            Config::prefix('import-export'),
            'kirkiImportExport',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce(Config::prefix('import_export')),
                'action_export' => Config::prefix('export_settings'),
                'action_import' => Config::prefix('import_settings'),
                'action_reset' => Config::prefix('reset_settings'),
                'reset_nonce_id' => Config::prefix('reset_nonce')
            ]
        );
    
        wp_add_inline_style(
            'customize-controls',
            '
            .kirki-uploading { display: none; }
            .kirki-import-controls { margin: 1em 0; }
            .kirki-import-file { margin-bottom: 1em; }
            .kirki-import-images { display: block; margin: 1em 0; }
            .button-danger { 
                background: #dc3545 !important; 
                border-color: #dc3545 !important;
                color: #fff !important;
            }
            .button-danger:hover {
                background: #c82333 !important;
                border-color: #bd2130 !important;
            }
            '
        );
    }

    /**
     * Export settings
     */
    public function export_settings() {
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (!check_ajax_referer(Config::prefix('import_export'), 'nonce', false)) {
            wp_send_json_error(['message' => 'Invalid nonce']);
            return;
        }

        $theme = get_stylesheet();
        $template = get_template();
        $charset = get_option('blog_charset');
        
        // Get only the mods with our prefix
        $all_mods = get_theme_mods();
        $mods = array_filter($all_mods, function($key) {
            return strpos($key, Config::prefix('')) === 0;
        }, ARRAY_FILTER_USE_KEY);
        
        $data = [
            'template' => $template,
            'theme' => $theme,
            'mods' => $mods,
            'options' => [],
            'wp_css' => function_exists('wp_get_custom_css') ? wp_get_custom_css() : ''
        ];

        // Set download headers
        header('Content-disposition: attachment; filename=' . $theme . '-export.dat');
        header('Content-Type: application/octet-stream; charset=' . $charset);

        // Output serialized data
        echo serialize($data);
        die();
    }

    /**
     * Import settings
     */
    public function import_settings() {
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (!check_ajax_referer(Config::prefix('import_export'), 'nonce', false)) {
            wp_send_json_error(['message' => 'Invalid nonce']);
            return;
        }

        if (!isset($_FILES['import_file'])) {
            wp_send_json_error(['message' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['import_file'];
        
        // Validate file type
        $validate = wp_check_filetype($file['name'], ['dat' => 'application/octet-stream']);
        if ('application/octet-stream' !== $validate['type']) {
            wp_send_json_error(['message' => 'Invalid file type']);
            return;
        }

        // Read file content
        $raw = file_get_contents($file['tmp_name']);
        $data = @unserialize($raw);

        if (!is_array($data) || !isset($data['theme']) || !isset($data['mods'])) {
            wp_send_json_error(['message' => 'Invalid import data']);
            return;
        }

        // Check theme compatibility
        if ($data['theme'] !== get_stylesheet()) {
            wp_send_json_error(['message' => 'Settings are not compatible with this theme']);
            return;
        }

        // Remove existing theme mods with our prefix
        $existing_mods = get_theme_mods();
        foreach ($existing_mods as $key => $value) {
            if (strpos($key, Config::prefix('')) === 0) {
                remove_theme_mod($key);
            }
        }

        // Import new mods
        foreach ($data['mods'] as $key => $value) {
            set_theme_mod($key, $value);
        }

        // Import custom CSS if available
        if (!empty($data['wp_css'])) {
            wp_update_custom_css_post($data['wp_css']);
        }

        wp_send_json_success(['message' => 'Settings imported successfully']);
    }

    /**
     * Reset settings
     */
    public function reset_settings() {
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
            return;
        }

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], Config::prefix('reset_nonce'))) {
            wp_send_json_error(['message' => 'Invalid nonce']);
            return;
        }

        // Get all theme mods
        $theme_mods = get_theme_mods();
        
        if ($theme_mods) {
            foreach ($theme_mods as $key => $value) {
                remove_theme_mod($key);
            }
        }

        // Reset custom CSS
        if (function_exists('wp_update_custom_css_post')) {
            wp_update_custom_css_post('');
        }

        wp_send_json_success(['message' => 'All settings have been reset to defaults']);
    }
}