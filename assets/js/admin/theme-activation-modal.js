// Save as: assets/js/theme-activation-modal.js

jQuery(document).ready(function($) {
    
    var modal = $('#theme-activation-modal');
    var totalPlugins = $('.plugin-item').length;
    var currentPluginIndex = 0;
    var pluginsToInstall = [];
    var pluginStatusChecked = 0;
    
    // Show modal on page load
    setTimeout(function() {
        modal.addClass('show');
        checkPluginStatuses();
    }, 500);
    
    // Check status of all plugins
    function checkPluginStatuses() {
        pluginStatusChecked = 0;
        pluginsToInstall = []; // Reset the array
        
        $('.plugin-item').each(function() {
            var pluginItem = $(this);
            var plugin = pluginItem.data('plugin');
            
            checkPluginStatus(plugin, pluginItem);
        });
    }
    
    // Check individual plugin status
    function checkPluginStatus(plugin, pluginItem) {
        $.ajax({
            url: themeActivationModal.ajax_url,
            type: 'POST',
            data: {
                action: 'get_plugin_status',
                plugin: plugin,
                nonce: themeActivationModal.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePluginStatus(pluginItem, response.data.status);
                    
                    // Only add to install list if not active (covers both not-installed and installed-but-not-active)
                    if (response.data.status !== 'active') {
                        pluginsToInstall.push({
                            key: plugin,
                            element: pluginItem,
                            status: response.data.status
                        });
                    }
                }
            },
            complete: function() {
                pluginStatusChecked++;
                // Only update button when all plugins have been checked
                if (pluginStatusChecked >= totalPlugins) {
                    updateInstallButtonText();
                }
            }
        });
    }
    
    // Update plugin status display
    function updatePluginStatus(pluginItem, status) {
        var statusEl = pluginItem.find('.status-indicator');
        
        statusEl.removeClass('checking not-installed installed active error');
        statusEl.addClass(status);
        
        switch (status) {
            case 'active':
                statusEl.html('<span class="dashicons dashicons-yes-alt"></span> ' + themeActivationModal.activated);
                pluginItem.removeClass('plugin-not-installed plugin-installed').addClass('plugin-active');
                break;
            case 'installed':
                statusEl.html('<span class="dashicons dashicons-download"></span> ' + themeActivationModal.installed);
                pluginItem.removeClass('plugin-not-installed plugin-active').addClass('plugin-installed');
                break;
            case 'not-installed':
                statusEl.html('<span class="dashicons dashicons-warning"></span> Not Installed');
                pluginItem.removeClass('plugin-installed plugin-active').addClass('plugin-not-installed');
                break;
            case 'error':
                statusEl.html('<span class="dashicons dashicons-dismiss"></span> ' + themeActivationModal.error);
                pluginItem.addClass('plugin-error');
                break;
        }
    }
    
    // Update install button text with plugin count
    function updateInstallButtonText() {
        var installButton = $('.start-installation');
        var getStartedButton = $('.get-started');
        var count = pluginsToInstall.length;
        
        if (count === 0) {
            // All required plugins are active
            installButton.hide();
            $('.completion-message').show();
            getStartedButton.show().prop('disabled', false);
        } else {
            // There are plugins that need installation or activation
            installButton.text('Install ' + count + ' Required Plugin' + (count > 1 ? 's' : ''))
                         .prop('disabled', false)
                         .show();
            getStartedButton.hide();
            $('.completion-message').hide();
        }
    }
    
    // Start installation process
    $('.start-installation').on('click', function() {
        if (pluginsToInstall.length === 0) {
            return;
        }
        
        // Reset installation index
        currentPluginIndex = 0;
        
        // Hide initial content and show progress
        $('.required-plugins-section').hide();
        $('.installation-progress').show();
        $('.modal-actions .button').prop('disabled', true);
        
        // Update progress counter
        $('.progress-counter .total').text(pluginsToInstall.length);
        $('.progress-counter .current').text('0');
        $('.progress-fill').css('width', '0%');
        
        // Start installing plugins
        installNextPlugin();
    });
    
    // Install plugins sequentially
    function installNextPlugin() {
        if (currentPluginIndex >= pluginsToInstall.length) {
            // All plugins processed
            installationComplete();
            return;
        }
        
        var pluginData = pluginsToInstall[currentPluginIndex];
        var plugin = pluginData.key;
        var pluginElement = pluginData.element;
        var pluginName = pluginElement.find('h4').text();
        var currentStatus = pluginData.status;
        
        // Update progress
        updateProgress(currentPluginIndex + 1, pluginName);
        
        // If plugin is already installed but not active, just activate it
        if (currentStatus === 'installed') {
            activatePlugin(plugin, pluginElement, function(activated) {
                currentPluginIndex++;
                setTimeout(function() {
                    installNextPlugin();
                }, 1000);
            });
        } else {
            // Install plugin first, then activate
            installPlugin(plugin, pluginElement, function(success) {
                if (success) {
                    // Activate plugin after installation
                    activatePlugin(plugin, pluginElement, function(activated) {
                        currentPluginIndex++;
                        setTimeout(function() {
                            installNextPlugin();
                        }, 1000);
                    });
                } else {
                    // Continue with next plugin even if one fails
                    currentPluginIndex++;
                    setTimeout(function() {
                        installNextPlugin();
                    }, 1000);
                }
            });
        }
    }
    
    // Install single plugin
    function installPlugin(plugin, pluginElement, callback) {
        var pluginName = pluginElement.find('h4').text();
        $('.installing-text').text('Installing ' + pluginName + '...');
        
        $.ajax({
            url: themeActivationModal.ajax_url,
            type: 'POST',
            data: {
                action: 'install_plugin_modal',
                plugin: plugin,
                nonce: themeActivationModal.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePluginStatus(pluginElement, 'installed');
                    callback(true);
                } else {
                    updatePluginStatus(pluginElement, 'error');
                    callback(false);
                }
            },
            error: function() {
                updatePluginStatus(pluginElement, 'error');
                callback(false);
            }
        });
    }
    
    // Activate single plugin
    function activatePlugin(plugin, pluginElement, callback) {
        var pluginName = pluginElement.find('h4').text();
        $('.installing-text').text('Activating ' + pluginName + '...');
        
        $.ajax({
            url: themeActivationModal.ajax_url,
            type: 'POST',
            data: {
                action: 'activate_plugin_modal',
                plugin: plugin,
                nonce: themeActivationModal.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePluginStatus(pluginElement, 'active');
                    callback(true);
                } else {
                    updatePluginStatus(pluginElement, 'error');
                    callback(false);
                }
            },
            error: function() {
                updatePluginStatus(pluginElement, 'error');
                callback(false);
            }
        });
    }
    
    // Update installation progress
    function updateProgress(current, pluginName) {
        var percent = (current / pluginsToInstall.length) * 100;
        
        $('.progress-counter .current').text(current);
        $('.progress-fill').animate({width: percent + '%'}, 300);
    }
    
    // Installation complete
    function installationComplete() {
        $('.installation-progress').hide();
        $('.completion-message').show();
        $('.get-started').show().prop('disabled', false);
        $('.skip-installation').hide();
        $('.start-installation').hide();
        
        // Update the installing text to show completion
        $('.installing-text').text(themeActivationModal.complete_message);
    }
    
    // Skip installation
    $('.skip-installation').on('click', function() {
        dismissModal();
    });
    
    // Get started (close modal)
    $('.get-started').on('click', function() {
        dismissModal();
    });
    
    // Dismiss modal
    function dismissModal() {
        $.ajax({
            url: themeActivationModal.ajax_url,
            type: 'POST',
            data: {
                action: 'dismiss_activation_modal',
                nonce: themeActivationModal.nonce
            },
            complete: function() {
                modal.removeClass('show');
                setTimeout(function() {
                    modal.remove();
                }, 300);
            }
        });
    }
    
    // Prevent modal close on backdrop click during installation
    modal.on('click', function(e) {
        if (e.target === this && !$('.installation-progress').is(':visible')) {
            dismissModal();
        }
    });
    
    // Escape key to close modal
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27 && modal.hasClass('show') && !$('.installation-progress').is(':visible')) {
            dismissModal();
        }
    });
    
});