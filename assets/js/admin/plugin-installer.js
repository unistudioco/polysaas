// Save as: assets/js/plugin-installer.js

jQuery(document).ready(function($) {
    
    // Install plugin
    $('.install-plugin').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var pluginCard = button.closest('.plugin-card');
        var plugin = button.data('plugin');
        
        installPlugin(plugin, button, pluginCard);
    });
    
    // Activate plugin
    $('.activate-plugin').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var pluginCard = button.closest('.plugin-card');
        var plugin = button.data('plugin');
        
        activatePlugin(plugin, button, pluginCard);
    });
    
    // Install all required plugins
    $('#install-all-plugins').on('click', function(e) {
        e.preventDefault();
        installBulkPlugins('required');
    });
    
    // Install all recommended plugins
    $('#install-recommended-plugins').on('click', function(e) {
        e.preventDefault();
        installBulkPlugins('all');
    });
    
    /**
     * Install a single plugin
     */
    function installPlugin(plugin, button, pluginCard) {
        showProgress(pluginCard, pluginInstaller.installing);
        button.prop('disabled', true);
        
        $.ajax({
            url: pluginInstaller.ajax_url,
            type: 'POST',
            data: {
                action: 'install_plugin',
                plugin: plugin,
                nonce: pluginInstaller.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePluginStatus(pluginCard, 'installed');
                    hideProgress(pluginCard);
                    
                    // Change button to activate
                    button.removeClass('install-plugin')
                          .addClass('activate-plugin')
                          .text(pluginInstaller.activating)
                          .prop('disabled', false);
                    
                    // Auto-activate after installation
                    setTimeout(function() {
                        activatePlugin(plugin, button, pluginCard);
                    }, 1000);
                    
                } else {
                    showError(pluginCard, response.data || pluginInstaller.error);
                    button.prop('disabled', false);
                }
            },
            error: function() {
                showError(pluginCard, pluginInstaller.error);
                button.prop('disabled', false);
            }
        });
    }
    
    /**
     * Activate a single plugin
     */
    function activatePlugin(plugin, button, pluginCard) {
        showProgress(pluginCard, pluginInstaller.activating);
        button.prop('disabled', true);
        
        $.ajax({
            url: pluginInstaller.ajax_url,
            type: 'POST',
            data: {
                action: 'activate_plugin',
                plugin: plugin,
                nonce: pluginInstaller.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePluginStatus(pluginCard, 'active');
                    hideProgress(pluginCard);
                    
                    // Update button
                    button.removeClass('activate-plugin button-primary')
                          .addClass('button-secondary')
                          .text(pluginInstaller.activated)
                          .prop('disabled', true);
                    
                } else {
                    showError(pluginCard, response.data || pluginInstaller.error);
                    button.prop('disabled', false);
                }
            },
            error: function() {
                showError(pluginCard, pluginInstaller.error);
                button.prop('disabled', false);
            }
        });
    }
    
    /**
     * Install plugins in bulk
     */
    function installBulkPlugins(type) {
        var plugins = [];
        
        if (type === 'required') {
            $('.plugin-card').each(function() {
                var card = $(this);
                if (card.find('.required').length && !card.hasClass('active')) {
                    plugins.push(card.data('plugin'));
                }
            });
        } else {
            $('.plugin-card').each(function() {
                var card = $(this);
                if (!card.hasClass('active')) {
                    plugins.push(card.data('plugin'));
                }
            });
        }
        
        if (plugins.length === 0) {
            return;
        }
        
        // Disable bulk buttons
        $('.bulk-actions button').prop('disabled', true);
        
        // Install plugins sequentially
        installPluginSequentially(plugins, 0);
    }
    
    /**
     * Install plugins one by one
     */
    function installPluginSequentially(plugins, index) {
        if (index >= plugins.length) {
            $('.bulk-actions button').prop('disabled', false);
            return;
        }
        
        var plugin = plugins[index];
        var pluginCard = $('.plugin-card[data-plugin="' + plugin + '"]');
        var button = pluginCard.find('.install-plugin, .activate-plugin');
        
        if (pluginCard.hasClass('active')) {
            // Skip if already active
            installPluginSequentially(plugins, index + 1);
            return;
        }
        
        if (pluginCard.hasClass('installed')) {
            // Activate if installed
            activatePlugin(plugin, button, pluginCard);
        } else {
            // Install if not installed
            installPlugin(plugin, button, pluginCard);
        }
        
        // Move to next plugin
        setTimeout(function() {
            installPluginSequentially(plugins, index + 1);
        }, 2000);
    }
    
    /**
     * Show progress indicator
     */
    function showProgress(pluginCard, text) {
        var progress = pluginCard.find('.plugin-progress');
        var progressText = progress.find('.progress-text');
        
        progressText.text(text);
        progress.show();
        
        // Animate progress bar
        var progressFill = progress.find('.progress-fill');
        progressFill.css('width', '0%').animate({width: '100%'}, 2000);
    }
    
    /**
     * Hide progress indicator
     */
    function hideProgress(pluginCard) {
        pluginCard.find('.plugin-progress').hide();
    }
    
    /**
     * Update plugin status
     */
    function updatePluginStatus(pluginCard, status) {
        pluginCard.removeClass('not-installed installed active')
                  .addClass(status);
        
        var statusText = pluginCard.find('.plugin-status span');
        
        switch (status) {
            case 'installed':
                statusText.removeClass('status-not-installed status-active')
                         .addClass('status-installed')
                         .text(pluginInstaller.installed);
                break;
            case 'active':
                statusText.removeClass('status-not-installed status-installed')
                         .addClass('status-active')
                         .text(pluginInstaller.activated);
                break;
        }
    }
    
    /**
     * Show error message
     */
    function showError(pluginCard, message) {
        hideProgress(pluginCard);
        
        // Show error message
        var errorDiv = $('<div class="plugin-error notice notice-error"><p>' + message + '</p></div>');
        pluginCard.append(errorDiv);
        
        // Hide error after 5 seconds
        setTimeout(function() {
            errorDiv.fadeOut(function() {
                errorDiv.remove();
            });
        }, 5000);
    }
    
});