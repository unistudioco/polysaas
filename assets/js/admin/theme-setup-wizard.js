// Save as: assets/js/admin/theme-setup-wizard.js

jQuery(document).ready(function($) {
    
    var wizard = $('#theme-setup-wizard');
    var currentStep = 1;
    var totalSteps = 3;
    var requiredPlugins = [];
    var selectedRecommendedPlugins = [];
    var pluginsToInstall = [];
    var currentPluginIndex = 0;
    var selectedDemo = null;
    var importOptions = [];
    
    // Initialize wizard
    initWizard();
    
    function initWizard() {
        // Show wizard
        setTimeout(function() {
            wizard.addClass('show');
            checkPluginStatuses();
            loadDemos();
        }, 500);
        
        // Bind events
        bindEvents();
    }
    
    function bindEvents() {
        // Navigation
        $('.wizard-next').on('click', handleNextStep);
        $('.wizard-prev').on('click', handlePrevStep);
        $('.wizard-skip').on('click', skipWizard);
        $('.wizard-finish').on('click', finishWizard);
        
        // Plugin selection
        $('.recommended .plugin-checkbox input').on('change', handlePluginSelection);
        $('.plugin-item.recommended').on('click', function(e) {
            if (!$(e.target).is('input')) {
                var checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            }
        });
        
        // Demo selection
        $(document).on('click', '.demo-item', handleDemoSelection);
        
        // Test modal button (remove after testing)
        $(document).on('click', '#test-modal-btn', function() {
            console.log('Test button clicked - showing fallback modal');
            showFallbackImportModal('test-demo');
        });
        
        // Close modal events
        $(document).on('click', '.modal-close, .cancel-import', closeImportModal);
        $(document).on('click', '#import-options-modal', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });
        $(document).on('change', '.import-option input', handleImportOptionChange);
        
        // Prevent closing during processes
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // Escape key
                if (!$('.installation-progress, .import-progress').is(':visible')) {
                    skipWizard();
                }
            }
        });
    }
    
    // Step Navigation
    function handleNextStep() {
        var nextButton = $('.wizard-next');
        var buttonText = nextButton.text().trim();
        
        if (currentStep === 1) {
            if (buttonText.includes('Install')) {
                startPluginInstallation();
            } else {
                goToStep(2);
            }
        } else if (currentStep === 2) {
            if (selectedDemo && buttonText.includes('Import')) {
                startDemoImport();
            } else {
                goToStep(3);
            }
        }
    }
    
    function handlePrevStep() {
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        }
    }
    
    function goToStep(step) {
        // Reset step content when going to a step
        resetStepContent(step);
        
        // Update step indicators
        $('.progress-steps .step').removeClass('active completed');
        $('.progress-steps .step[data-step="' + step + '"]').addClass('active');
        
        for (var i = 1; i < step; i++) {
            $('.progress-steps .step[data-step="' + i + '"]').addClass('completed');
        }
        
        // Update progress bar
        var progressPercent = (step - 1) / (totalSteps - 1) * 100;
        $('.wizard-progress .progress-fill').css('width', progressPercent + '%');
        
        // Show/hide step content
        $('.wizard-step').removeClass('active');
        $('.wizard-step.step-' + step).addClass('active');
        
        // Update navigation buttons
        updateNavigationButtons(step);
        
        currentStep = step;
    }
    
    function resetStepContent(step) {
        if (step === 1) {
            // Reset plugin installation progress
            $('.step-1 .installation-progress').hide();
            $('.step-1 .plugins-section').show();
            $('.wizard-navigation button').prop('disabled', false);
            
            // Reset progress indicators
            $('.installation-progress .current').text('0');
            $('.installation-progress .total').text('0');
            $('.installation-progress .progress-fill').css('width', '0%');
            $('.installing-text').text('');
            
            // Reset plugin installation state
            pluginsToInstall = [];
            currentPluginIndex = 0;
            
        } else if (step === 2) {
            // Reset demo import progress
            $('.step-2 .import-progress').hide();
            $('.step-2 .demo-selection').show();
            $('.wizard-navigation button').prop('disabled', false);
            
            // Reset progress indicators
            $('.import-progress .progress-fill').css('width', '0%');
            $('.import-text').text('Preparing import...');
            
            // Restore demo selection state if user had previously selected something
            setTimeout(function() {
                restoreDemoSelectionState();
            }, 100);
        }
    }
    
    function updateNavigationButtons(step) {
        var nextButton = $('.wizard-next');
        var prevButton = $('.wizard-prev');
        var finishButton = $('.wizard-finish');
        var skipButton = $('.wizard-skip');
        
        // Previous button
        if (step > 1) {
            prevButton.show();
        } else {
            prevButton.hide();
        }
        
        // Next/Finish buttons
        if (step === totalSteps) {
            nextButton.hide();
            finishButton.show();
            skipButton.hide();
        } else {
            finishButton.hide();
            nextButton.show();
            skipButton.show();
        }
        
        // Update next button text based on step and state
        updateNextButtonText(step);
    }
    
    function updateNextButtonText(step) {
        var nextButton = $('.wizard-next');
        
        if (step === 1) {
            var pluginCount = getPluginsToInstallCount();
            if (pluginCount > 0) {
                nextButton.html('Install ' + pluginCount + ' Plugin' + (pluginCount > 1 ? 's' : '') + ' <span class="dashicons dashicons-arrow-right-alt2"></span>');
            } else {
                nextButton.html('Next Step <span class="dashicons dashicons-arrow-right-alt2"></span>');
            }
        } else if (step === 2) {
            if (selectedDemo) {
                nextButton.html('Import Demo Content <span class="dashicons dashicons-arrow-right-alt2"></span>');
            } else {
                nextButton.html('Skip Demo Import <span class="dashicons dashicons-arrow-right-alt2"></span>');
            }
        }
    }
    
    // Plugin Management
    function checkPluginStatuses() {
        $('.plugin-item').each(function() {
            var pluginItem = $(this);
            var plugin = pluginItem.data('plugin');
            var type = pluginItem.data('type');
            
            checkPluginStatus(plugin, type, pluginItem);
        });
    }
    
    function checkPluginStatus(plugin, type, pluginItem) {
        $.ajax({
            url: themeSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'get_plugin_status_wizard',
                plugin: plugin,
                type: type,
                nonce: themeSetupWizard.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePluginStatus(pluginItem, response.data.status, type);
                }
            },
            complete: function() {
                updateNextButtonText(currentStep);
            }
        });
    }
    
    function updatePluginStatus(pluginItem, status, type) {
        var statusEl = pluginItem.find('.status-indicator');
        
        statusEl.removeClass('checking not-installed installed active error');
        statusEl.addClass(status);
        
        switch (status) {
            case 'active':
                statusEl.html('<span class="dashicons dashicons-yes-alt"></span> ' + themeSetupWizard.strings.activated);
                pluginItem.addClass('plugin-active');
                break;
            case 'installed':
                statusEl.html('<span class="dashicons dashicons-download"></span> ' + themeSetupWizard.strings.installed);
                pluginItem.addClass('plugin-installed');
                break;
            case 'not-installed':
                statusEl.html('<span class="dashicons dashicons-warning"></span> Not Installed');
                pluginItem.addClass('plugin-not-installed');
                break;
        }
        
        // Update required plugins list
        if (type === 'required' && status !== 'active') {
            if (requiredPlugins.indexOf(pluginItem.data('plugin')) === -1) {
                requiredPlugins.push(pluginItem.data('plugin'));
            }
        }
    }
    
    function handlePluginSelection() {
        var plugin = $(this).val();
        
        if ($(this).is(':checked')) {
            if (selectedRecommendedPlugins.indexOf(plugin) === -1) {
                selectedRecommendedPlugins.push(plugin);
            }
        } else {
            var index = selectedRecommendedPlugins.indexOf(plugin);
            if (index > -1) {
                selectedRecommendedPlugins.splice(index, 1);
            }
        }
        
        updateNextButtonText(currentStep);
    }
    
    function getPluginsToInstallCount() {
        var count = 0;
        
        // Count required plugins that need installation/activation
        $('.plugin-item.required').each(function() {
            if (!$(this).hasClass('plugin-active')) {
                count++;
            }
        });
        
        // Count selected recommended plugins that need installation/activation
        selectedRecommendedPlugins.forEach(function(plugin) {
            var pluginItem = $('.plugin-item[data-plugin="' + plugin + '"]');
            if (!pluginItem.hasClass('plugin-active')) {
                count++;
            }
        });
        
        return count;
    }
    
    function startPluginInstallation() {
        // Prepare plugins to install
        pluginsToInstall = [];
        currentPluginIndex = 0;
        
        // Add required plugins
        $('.plugin-item.required').each(function() {
            if (!$(this).hasClass('plugin-active')) {
                pluginsToInstall.push({
                    key: $(this).data('plugin'),
                    type: 'required',
                    element: $(this),
                    status: $(this).hasClass('plugin-installed') ? 'installed' : 'not-installed'
                });
            }
        });
        
        // Add selected recommended plugins
        selectedRecommendedPlugins.forEach(function(plugin) {
            var pluginItem = $('.plugin-item[data-plugin="' + plugin + '"]');
            if (!pluginItem.hasClass('plugin-active')) {
                pluginsToInstall.push({
                    key: plugin,
                    type: 'recommended',
                    element: pluginItem,
                    status: pluginItem.hasClass('plugin-installed') ? 'installed' : 'not-installed'
                });
            }
        });
        
        if (pluginsToInstall.length === 0) {
            goToStep(2);
            return;
        }
        
        // Show installation progress
        $('.step-1 .plugins-section').hide();
        $('.step-1 .installation-progress').show();
        $('.wizard-navigation button').prop('disabled', true);
        
        // Update progress counter
        $('.installation-progress .total').text(pluginsToInstall.length);
        $('.installation-progress .current').text(0);
        $('.installation-progress .progress-fill').css('width', '0%');
        
        // Start installation
        installNextPlugin();
    }
    
    function installNextPlugin() {
        if (currentPluginIndex >= pluginsToInstall.length) {
            installationComplete();
            return;
        }
        
        var pluginData = pluginsToInstall[currentPluginIndex];
        var plugin = pluginData.key;
        var type = pluginData.type;
        var pluginElement = pluginData.element;
        var pluginName = pluginElement.find('h4').text();
        var currentStatus = pluginData.status;
        
        // Update progress
        updateInstallationProgress(currentPluginIndex + 1, pluginName);
        
        // Install or activate based on current status
        if (currentStatus === 'installed') {
            activatePlugin(plugin, type, pluginElement, function() {
                currentPluginIndex++;
                setTimeout(installNextPlugin, 1000);
            });
        } else {
            installPlugin(plugin, type, pluginElement, function(success) {
                if (success) {
                    activatePlugin(plugin, type, pluginElement, function() {
                        currentPluginIndex++;
                        setTimeout(installNextPlugin, 1000);
                    });
                } else {
                    currentPluginIndex++;
                    setTimeout(installNextPlugin, 1000);
                }
            });
        }
    }
    
    function installPlugin(plugin, type, pluginElement, callback) {
        var pluginName = pluginElement.find('h4').text();
        $('.installing-text').text(themeSetupWizard.strings.installing + ' ' + pluginName + '...');
        
        $.ajax({
            url: themeSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'install_plugin_wizard',
                plugin: plugin,
                type: type,
                nonce: themeSetupWizard.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePluginStatus(pluginElement, 'installed', type);
                    callback(true);
                } else {
                    updatePluginStatus(pluginElement, 'error', type);
                    callback(false);
                }
            },
            error: function() {
                updatePluginStatus(pluginElement, 'error', type);
                callback(false);
            }
        });
    }
    
    function activatePlugin(plugin, type, pluginElement, callback) {
        var pluginName = pluginElement.find('h4').text();
        $('.installing-text').text(themeSetupWizard.strings.activating + ' ' + pluginName + '...');
        
        $.ajax({
            url: themeSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'activate_plugin_wizard',
                plugin: plugin,
                type: type,
                nonce: themeSetupWizard.nonce
            },
            success: function(response) {
                if (response.success) {
                    updatePluginStatus(pluginElement, 'active', type);
                    callback(true);
                } else {
                    updatePluginStatus(pluginElement, 'error', type);
                    callback(false);
                }
            },
            error: function() {
                updatePluginStatus(pluginElement, 'error', type);
                callback(false);
            }
        });
    }
    
    function updateInstallationProgress(current, pluginName) {
        var percent = (current / pluginsToInstall.length) * 100;
        
        $('.installation-progress .current').text(current);
        $('.installation-progress .progress-fill').animate({width: percent + '%'}, 300);
    }
    
    function installationComplete() {
        $('.installing-text').text(themeSetupWizard.strings.complete);
        $('.wizard-navigation button').prop('disabled', false);
        
        setTimeout(function() {
            goToStep(2);
        }, 2000);
    }
    
    // Demo Management
    function loadDemos() {
        $.ajax({
            url: themeSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'get_demos_wizard',
                nonce: themeSetupWizard.nonce
            },
            success: function(response) {
                if (response.success) {
                    renderDemos(response.data);
                }
            }
        });
    }
    
    function renderDemos(demos) {
        var demosGrid = $('.demos-grid');
        demosGrid.empty();
        
        Object.keys(demos).forEach(function(demoKey) {
            var demo = demos[demoKey];
            var requiredPluginsText = '';
            
            // Show required plugins if any
            if (demo.required_plugins && demo.required_plugins.length > 0) {
                requiredPluginsText = '<div class="demo-requirements">' +
                    '<span class="dashicons dashicons-admin-plugins"></span>' +
                    '<span>Requires: ' + demo.required_plugins.join(', ') + '</span>' +
                '</div>';
            }
            
            var demoHtml = '<div class="demo-item" data-demo="' + demoKey + '">' +
                '<div class="demo-preview">' +
                    '<img src="' + demo.preview_image + '" alt="' + demo.name + '">' +
                    '<div class="demo-overlay">' +
                        '<div class="demo-actions">' +
                            '<a href="' + demo.demo_url + '" target="_blank" class="button button-secondary">' +
                                '<span class="dashicons dashicons-external"></span> Preview' +
                            '</a>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="demo-info">' +
                    '<h4>' + demo.name + '</h4>' +
                    '<p>' + demo.description + '</p>' +
                    requiredPluginsText +
                '</div>' +
            '</div>';
            
            demosGrid.append(demoHtml);
        });
        
        // Add empty state if no demos
        if (Object.keys(demos).length === 0) {
            demosGrid.append('<div class="no-demos-message">' +
                '<div class="no-demos-icon">' +
                    '<span class="dashicons dashicons-admin-appearance"></span>' +
                '</div>' +
                '<h4>No Demo Content Available</h4>' +
                '<p>Demo content will be available in future updates.</p>' +
            '</div>');
        }
    }
    
    // Demo selection
    function handleDemoSelection(e) {
        // Prevent event if clicking on preview link
        if ($(e.target).closest('.demo-actions').length) {
            return;
        }
        
        var demoKey = $(this).data('demo');
        console.log('Demo selected:', demoKey); // Debug log
        
        // Remove previous selection
        $('.demo-item').removeClass('selected');
        
        // Select current demo
        $(this).addClass('selected');
        selectedDemo = demoKey;
        
        // Load import options for selected demo in modal
        showImportOptionsModal(demoKey);
        
        // Update next button
        updateNextButtonText(currentStep);
    }
    
    function showImportOptionsModal(demoKey) {
        console.log('Showing modal for demo:', demoKey); // Debug log
        
        $.ajax({
            url: themeSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'get_demos_wizard',
                nonce: themeSetupWizard.nonce
            },
            success: function(response) {
                console.log('AJAX response:', response); // Debug log
                if (response.success && response.data[demoKey]) {
                    var demo = response.data[demoKey];
                    renderImportOptionsModal(demo, demoKey);
                } else {
                    console.error('Demo not found in response:', demoKey);
                    // Fallback: show a basic modal with default options
                    showFallbackImportModal(demoKey);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                // Fallback: show a basic modal with default options
                showFallbackImportModal(demoKey);
            }
        });
    }
    
    function showFallbackImportModal(demoKey) {
        console.log('Showing fallback modal for:', demoKey);
        
        var modalHtml = '<div id="import-options-modal" class="import-modal-overlay">' +
            '<div class="import-modal-container">' +
                '<div class="import-modal-header">' +
                    '<div class="demo-info-mini">' +
                        '<h3>Import Demo Content</h3>' +
                        '<p>Choose what content to import from the selected demo.</p>' +
                    '</div>' +
                    '<button type="button" class="modal-close">' +
                        '<span class="dashicons dashicons-no-alt"></span>' +
                    '</button>' +
                '</div>' +
                '<div class="import-modal-content">' +
                    '<h4>Select Import Options</h4>' +
                    '<div class="import-options-list">' +
                        '<div class="import-option-item">' +
                            '<div class="option-header">' +
                                '<label class="option-checkbox">' +
                                    '<input type="checkbox" name="import_options[]" value="pages" checked>' +
                                    '<span class="checkmark"></span>' +
                                    '<span class="option-title">Pages</span>' +
                                '</label>' +
                            '</div>' +
                        '</div>' +
                        '<div class="import-option-item">' +
                            '<div class="option-header">' +
                                '<label class="option-checkbox">' +
                                    '<input type="checkbox" name="import_options[]" value="posts" checked>' +
                                    '<span class="checkmark"></span>' +
                                    '<span class="option-title">Blog Posts</span>' +
                                '</label>' +
                            '</div>' +
                        '</div>' +
                        '<div class="import-option-item">' +
                            '<div class="option-header">' +
                                '<label class="option-checkbox">' +
                                    '<input type="checkbox" name="import_options[]" value="media" checked>' +
                                    '<span class="checkmark"></span>' +
                                    '<span class="option-title">Media Files</span>' +
                                '</label>' +
                            '</div>' +
                        '</div>' +
                        '<div class="import-option-item">' +
                            '<div class="option-header">' +
                                '<label class="option-checkbox">' +
                                    '<input type="checkbox" name="import_options[]" value="customizer" checked>' +
                                    '<span class="checkmark"></span>' +
                                    '<span class="option-title">Theme Settings</span>' +
                                '</label>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="import-modal-footer">' +
                    '<div class="import-actions">' +
                        '<button type="button" class="button button-secondary cancel-import">Cancel</button>' +
                        '<button type="button" class="button button-primary confirm-import">Import Selected Content</button>' +
                    '</div>' +
                    '<div class="import-note">' +
                        '<p><strong>Note:</strong> This will add demo content to your site. You can always delete it later if needed.</p>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        // Remove existing modal and add new one
        $('#import-options-modal').remove();
        $('body').append(modalHtml);
        
        // Show modal with animation
        setTimeout(function() {
            $('#import-options-modal').addClass('show');
        }, 50);
        
        // Bind modal events
        bindImportModalEvents();
    }
    
    function renderImportOptionsModal(demo, demoKey) {
        console.log('Rendering modal for demo:', demoKey, demo); // Debug log
        
        var modalHtml = '<div id="import-options-modal" class="import-modal-overlay">' +
            '<div class="import-modal-container">' +
                '<div class="import-modal-header">' +
                    '<div class="demo-preview-mini">' +
                        '<img src="' + demo.preview_image + '" alt="' + demo.name + '">' +
                    '</div>' +
                    '<div class="demo-info-mini">' +
                        '<h3>' + demo.name + '</h3>' +
                        '<p>' + demo.description + '</p>' +
                        '<a href="' + demo.demo_url + '" target="_blank" class="preview-link">' +
                            '<span class="dashicons dashicons-external"></span> Preview Demo' +
                        '</a>' +
                    '</div>' +
                    '<button type="button" class="modal-close">' +
                        '<span class="dashicons dashicons-no-alt"></span>' +
                    '</button>' +
                '</div>' +
                '<div class="import-modal-content">' +
                    '<h4>Choose What to Import</h4>' +
                    '<p>Select the content and settings you want to import from this demo.</p>' +
                    '<div class="import-options-list">' +
                        // Options will be populated here
                    '</div>' +
                '</div>' +
                '<div class="import-modal-footer">' +
                    '<div class="import-actions">' +
                        '<button type="button" class="button button-secondary cancel-import">Cancel</button>' +
                        '<button type="button" class="button button-primary confirm-import">Import Selected Content</button>' +
                    '</div>' +
                    '<div class="import-note">' +
                        '<p><strong>Note:</strong> This will add demo content to your site. You can always delete it later if needed.</p>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        // Remove existing modal and add new one
        $('#import-options-modal').remove();
        $('body').append(modalHtml);
        
        // Populate import options
        var optionsList = $('.import-options-list');
        if (demo.options && Object.keys(demo.options).length > 0) {
            Object.keys(demo.options).forEach(function(optionKey) {
                var option = demo.options[optionKey];
                var isChecked = option.default ? 'checked' : '';
                var isRecommended = option.default ? '' : '';
                
                var optionHtml = '<div class="import-option-item">' +
                    '<div class="option-header">' +
                        '<label class="option-checkbox">' +
                            '<input type="checkbox" name="import_options[]" value="' + optionKey + '" ' + isChecked + '>' +
                            '<span class="checkmark"></span>' +
                            '<span class="option-title">' + option.label + isRecommended + '</span>' +
                        '</label>' +
                    '</div>' +
                '</div>';
                
                optionsList.append(optionHtml);
            });
        } else {
            // Fallback if no options defined
            optionsList.append('<div class="no-options-message">' +
                '<p>No specific import options available for this demo. All content will be imported.</p>' +
            '</div>');
        }
        
        // Show modal with animation
        setTimeout(function() {
            $('#import-options-modal').addClass('show');
        }, 50);
        
        // Bind modal events
        bindImportModalEvents();
    }
    
    function bindImportModalEvents() {
        var modal = $('#import-options-modal');
        
        // Close modal events
        modal.find('.modal-close, .cancel-import').on('click', closeImportModal);
        
        // Close on backdrop click
        modal.on('click', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });
        
        // Close on escape key
        $(document).on('keydown.import-modal', function(e) {
            if (e.keyCode === 27) { // Escape key
                closeImportModal();
            }
        });
        
        // Confirm import
        modal.find('.confirm-import').on('click', function() {
            // Get selected options and update the global importOptions array
            var selectedOptions = [];
            modal.find('input[name="import_options[]"]:checked').each(function() {
                selectedOptions.push($(this).val());
            });
            
            // Update the global importOptions array with new selections
            importOptions = selectedOptions;
            console.log('Updated import options:', importOptions); // Debug log
            
            // Update demo selection display with new options
            updateDemoSelectionDisplay();
            
            // Close modal
            closeImportModal();
            
            // Update next button
            updateNextButtonText(currentStep);
        });
        
        // Option selection effects
        modal.find('.option-checkbox input').on('change', function() {
            var optionItem = $(this).closest('.import-option-item');
            if ($(this).is(':checked')) {
                optionItem.addClass('selected');
            } else {
                optionItem.removeClass('selected');
            }
            
            // Update confirm button text
            updateConfirmButtonText();
        });
        
        // Initial state - mark checked items as selected
        modal.find('.option-checkbox input:checked').each(function() {
            $(this).closest('.import-option-item').addClass('selected');
        });
        
        // Update confirm button text initially
        updateConfirmButtonText();
    }
    
    function updateConfirmButtonText() {
        var selectedCount = $('#import-options-modal input[name="import_options[]"]:checked').length;
        var confirmButton = $('.confirm-import');
        
        if (selectedCount === 0) {
            confirmButton.text('Skip Import').removeClass('button-primary').addClass('button-secondary');
        } else {
            confirmButton.text('Import ' + selectedCount + ' Item' + (selectedCount > 1 ? 's' : ''))
                         .removeClass('button-secondary').addClass('button-primary');
        }
    }
    
    function closeImportModal() {
        var modal = $('#import-options-modal');
        modal.removeClass('show');
        
        // Remove escape key listener
        $(document).off('keydown.import-modal');
        
        setTimeout(function() {
            modal.remove();
        }, 300);
    }
    
    function updateDemoSelectionDisplay() {
        var selectedDemoElement = $('.demo-item.selected');
        var importSummary = $('.import-summary');
        
        // Remove existing summary
        importSummary.remove();
        
        if (importOptions.length > 0) {
            // Create import summary
            var summaryHtml = '<div class="import-summary">' +
                '<h4>Selected for Import:</h4>' +
                '<div class="import-summary-items">';
            
            importOptions.forEach(function(option) {
                var optionLabel = getImportOptionLabel(option);
                summaryHtml += '<span class="import-item-tag">' + optionLabel + '</span>';
            });
            
            summaryHtml += '</div>' +
                '<button type="button" class="change-options-btn">' +
                    '<span class="dashicons dashicons-edit"></span> Change Options' +
                '</button>' +
            '</div>';
            
            selectedDemoElement.after(summaryHtml);
            
            // Bind change options event
            $('.change-options-btn').on('click', function() {
                showImportOptionsModal(selectedDemoElement.data('demo'));
            });
        }
        
        // Update next button text
        updateNextButtonText(currentStep);
    }
    
    function restoreDemoSelectionState() {
        // If there's a selected demo, restore its visual state
        if (selectedDemo) {
            $('.demo-item').removeClass('selected');
            $('.demo-item[data-demo="' + selectedDemo + '"]').addClass('selected');
            
            // Restore import summary if there are selected options
            updateDemoSelectionDisplay();
        }
    }
    
    function getImportOptionLabel(optionKey) {
        // Map option keys to readable labels
        var labels = {
            'pages': 'Pages',
            'posts': 'Blog Posts',
            'portfolio': 'Portfolio',
            'products': 'Products',
            'media': 'Media Files',
            'customizer': 'Theme Settings',
            'widgets': 'Widgets',
            'woocommerce': 'WooCommerce Settings'
        };
        
        return labels[optionKey] || optionKey;
    }
    
    function handleImportOptionChange() {
        var option = $(this).val();
        
        if ($(this).is(':checked')) {
            if (importOptions.indexOf(option) === -1) {
                importOptions.push(option);
            }
        } else {
            var index = importOptions.indexOf(option);
            if (index > -1) {
                importOptions.splice(index, 1);
            }
        }
    }
    
    function startDemoImport() {
        if (!selectedDemo) {
            goToStep(3);
            return;
        }
        
        // Get currently selected import options from the summary or defaults
        if (importOptions.length === 0) {
            // If no options were selected via modal, get them from any visible checkboxes
            $('.import-option input:checked').each(function() {
                importOptions.push($(this).val());
            });
        }
        
        // Show import progress
        $('.step-2 .demo-selection, .step-2 .import-options').hide();
        $('.step-2 .import-progress').show();
        $('.wizard-navigation button').prop('disabled', true);
        
        // Start import
        importDemoContent();
    }
    
    // Add function to allow users to clear selection and start over
    function clearDemoSelection() {
        selectedDemo = null;
        importOptions = [];
        $('.demo-item').removeClass('selected');
        $('.import-summary').remove();
        updateNextButtonText(currentStep);
    }
    
    // Add double-click handler to clear selection (optional enhancement)
    $(document).on('dblclick', '.demo-item.selected', function(e) {
        e.preventDefault();
        if (confirm('Clear demo selection and start over?')) {
            clearDemoSelection();
        }
    });
    
    function importDemoContent() {
        $('.import-text').text(themeSetupWizard.strings.importing);
        $('.import-progress .progress-fill').animate({width: '100%'}, 3000);
        
        $.ajax({
            url: themeSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'import_demo_content',
                demo: selectedDemo,
                import_options: importOptions,
                nonce: themeSetupWizard.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.import-text').text(themeSetupWizard.strings.complete);
                    setTimeout(function() {
                        goToStep(3);
                    }, 1500);
                } else {
                    $('.import-text').text('Import failed: ' + response.data);
                }
            },
            error: function() {
                $('.import-text').text('Import failed due to server error.');
            },
            complete: function() {
                $('.wizard-navigation button').prop('disabled', false);
            }
        });
    }
    
    // Wizard completion
    function skipWizard() {
        if (confirm('Are you sure you want to skip the setup wizard? You can always run it later.')) {
            dismissWizard();
        }
    }
    
    function finishWizard() {
        completeWizard();
    }
    
    function dismissWizard() {
        $.ajax({
            url: themeSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'dismiss_setup_wizard',
                nonce: themeSetupWizard.nonce
            },
            complete: function() {
                closeWizard();
            }
        });
    }
    
    function completeWizard() {
        $.ajax({
            url: themeSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'complete_setup_wizard',
                nonce: themeSetupWizard.nonce
            },
            complete: function() {
                closeWizard();
            }
        });
    }
    
    function closeWizard() {
        wizard.removeClass('show');
        setTimeout(function() {
            wizard.remove();
        }, 300);
    }

});