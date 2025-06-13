<?php
// This file should be included in the main wizard class

$required_plugins = $this->plugins['required'];
$recommended_plugins = $this->plugins['recommended'];
$theme_name = wp_get_theme()->get('Name');
?>

<div id="theme-setup-wizard" class="wizard-overlay">
    <div class="wizard-container">
        <!-- Wizard Header -->
        <div class="wizard-header">
            <div class="wizard-header-details">
                <div class="wizard-logo">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" alt="<?php echo esc_attr($theme_name); ?>">
                </div>
                <div>
                    <h2 class="wizard-title"><?php printf(__('%s\'s Setup Wizard', 'textdomain'), $theme_name); ?></h2>
                    <p class="wizard-subtitle"><?php _e('Let\'s get your website ready in just a few steps!', 'textdomain'); ?></p>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="wizard-progress">
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-label"><?php _e('Plugins', 'textdomain'); ?></span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-label"><?php _e('Demo Content', 'textdomain'); ?></span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-label"><?php _e('Complete', 'textdomain'); ?></span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%;"></div>
                </div>
            </div>
        </div>

        <!-- Wizard Content -->
        <div class="wizard-content">
            
            <!-- Step 1: Plugin Installation -->
            <div class="wizard-step step-1 active">
                <div class="step-content">
                    <!-- Required Plugins -->
                    <div class="plugins-section required-plugins">
                        <h3>
                            <span class="dashicons dashicons-admin-plugins"></span>
                            <?php _e('Required Plugins', 'textdomain'); ?>
                            <small><?php _e('These plugins are necessary for your theme to work properly', 'textdomain'); ?></small>
                        </h3>
                        <div class="plugins-grid">
                            <?php foreach ($required_plugins as $plugin_key => $plugin) : ?>
                                <div class="plugin-item required" data-plugin="<?php echo esc_attr($plugin_key); ?>" data-type="required">
                                    <div class="plugin-header">
                                        <div class="plugin-icon">
                                            <img src="<?php echo esc_url($plugin['icon']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>">
                                        </div>
                                        <div class="plugin-info">
                                            <h4><?php echo esc_html($plugin['name']); ?></h4>
                                            <p><?php echo esc_html($plugin['description']); ?></p>
                                        </div>
                                    </div>
                                    <div class="plugin-status">
                                        <span class="status-indicator checking">
                                            <span class="dashicons dashicons-update-alt"></span>
                                            <?php _e('Checking...', 'textdomain'); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Recommended Plugins -->
                    <div class="plugins-section recommended-plugins">
                        <h3>
                            <span class="dashicons dashicons-admin-plugins"></span>
                            <?php _e('Recommended Plugins', 'textdomain'); ?>
                            <small><?php _e('Select the plugins you\'d like to install (optional)', 'textdomain'); ?></small>
                        </h3>
                        <div class="plugins-grid">
                            <?php foreach ($recommended_plugins as $plugin_key => $plugin) : ?>
                                <div class="plugin-item recommended" data-plugin="<?php echo esc_attr($plugin_key); ?>" data-type="recommended">
                                    <div class="plugin-checkbox">
                                        <input type="checkbox" id="plugin-<?php echo esc_attr($plugin_key); ?>" name="recommended_plugins[]" value="<?php echo esc_attr($plugin_key); ?>">
                                        <label for="plugin-<?php echo esc_attr($plugin_key); ?>"></label>
                                    </div>
                                    <div class="plugin-header">
                                        <div class="plugin-icon">
                                            <img src="<?php echo esc_url($plugin['icon']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>">
                                        </div>
                                        <div class="plugin-info">
                                            <h4><?php echo esc_html($plugin['name']); ?></h4>
                                            <p><?php echo esc_html($plugin['description']); ?></p>
                                        </div>
                                    </div>
                                    <div class="plugin-status">
                                        <span class="status-indicator checking">
                                            <span class="dashicons dashicons-update-alt"></span>
                                            <?php _e('Checking...', 'textdomain'); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Installation Progress -->
                    <div class="installation-progress" style="display: none;">
                        <div class="progress-header">
                            <h3><?php _e('Installing Plugins...', 'textdomain'); ?></h3>
                            <div class="progress-counter">
                                <span class="current">0</span> / <span class="total">0</span>
                            </div>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                        </div>
                        <div class="current-plugin-status">
                            <span class="installing-text"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Demo Content Selection -->
            <div class="wizard-step step-2">
                <div class="step-content">
                    
                    <!-- Demo Selection -->
                    <div class="demo-selection">
                        <h3 class="heading"><?php _e('Select a demo to install:', 'textdomain'); ?></h3>
                        <p class="desc"><?php _e('Click on a demo to select it and choose what content to import.', 'textdomain'); ?></p>
                        <div class="demos-grid">
                            <!-- Demos will be loaded via JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Import Progress -->
                    <div class="import-progress" style="display: none;">
                        <div class="progress-header">
                            <h3><?php _e('Importing Demo Content...', 'textdomain'); ?></h3>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                        </div>
                        <div class="import-status">
                            <span class="import-text"><?php _e('Preparing import...', 'textdomain'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Completion -->
            <div class="wizard-step step-3">
                <div class="step-content">
                    <div class="completion-message">
                        <div class="success-icon">
                            <span class="dashicons dashicons-saved"></span>
                        </div>
                        <h3><?php _e('Setup Complete!', 'textdomain'); ?></h3>
                        <p><?php _e('Your website is now ready. Here are some helpful next steps.', 'textdomain'); ?></p>
                    </div>
                    
                    <div class="next-steps">
                        <h3><?php _e('What\'s Next?', 'textdomain'); ?></h3>
                        <div class="next-steps-grid">
                            <div class="next-step">
                                <div class="step-icon" style="background:#ffe1e7; color: #e74161;">
                                    <span class="dashicons dashicons-admin-customizer"></span>
                                </div>
                                <div class="step-info">
                                    <h4><?php _e('Customize your site', 'textdomain'); ?></h4>
                                    <p><?php _e('Personalize your site\'s appearance via Customizer.', 'textdomain'); ?></p>
                                    <a href="<?php echo admin_url('customize.php'); ?>" class="button secondary"><?php _e('Customize', 'textdomain'); ?></a>
                                </div>
                            </div>
                            
                            <div class="next-step">
                                <div class="step-icon" style="background: #f6ddff; color: #d25bff;">
                                    <span class="dashicons dashicons-edit"></span>
                                </div>
                                <div class="step-info">
                                    <h4><?php _e('Edit pages', 'textdomain'); ?></h4>
                                    <p><?php _e('Start editing your pages with Elementor page builder.', 'textdomain'); ?></p>
                                    <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="button secondary"><?php _e('Edit Pages', 'textdomain'); ?></a>
                                </div>
                            </div>
                            
                            <div class="next-step">
                                <div class="step-icon" style="background:#e6ddff; color: #6b3ee4;">
                                    <span class="dashicons dashicons-visibility"></span>
                                </div>
                                <div class="step-info">
                                    <h4><?php _e('View your site', 'textdomain'); ?></h4>
                                    <p><?php _e('See how your website looks to visitors.', 'textdomain'); ?></p>
                                    <a href="<?php echo home_url(); ?>" class="button secondary" target="_blank"><?php _e('View Site', 'textdomain'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wizard Footer -->
        <div class="wizard-footer">
            <div class="wizard-navigation">
                <button type="button" class="button secondary wizard-skip">
                    <?php _e('Skip Setup', 'textdomain'); ?>
                </button>
                
                <div class="wizard-nav-buttons">
                    <button type="button" class="button secondary wizard-prev" style="display: none;">
                        <span class="dashicons dashicons-arrow-left-alt2"></span>
                        <?php _e('Previous', 'textdomain'); ?>
                    </button>
                    
                    <button type="button" class="button primary wizard-next">
                        <?php _e('Install Plugins', 'textdomain'); ?>
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </button>
                    
                    <button type="button" class="button primary wizard-finish" style="display: none;">
                        <?php _e('Finish Setup', 'textdomain'); ?>
                    </button>
                </div>
            </div>
            
            <div class="wizard-help">
                <p>
                    <?php _e('Need help?', 'textdomain'); ?>
                    <a href="#" target="_blank"><?php _e('View Documentation', 'textdomain'); ?></a>
                    |
                    <a href="#" target="_blank"><?php _e('Contact Support', 'textdomain'); ?></a>
                </p>
            </div>
        </div>
    </div>
</div>