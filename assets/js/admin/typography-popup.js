/**
 * Kirki Typography Popup Control
 */
(function($) {
    'use strict';
    
    // Initialize when customizer loads
    wp.customize.bind('ready', function() {
        // Find all typography controls
        setTimeout(function() {
            $('.rey-typography-control').each(function() {
                initTypographyControl(this);
            });
        }, 100);
    });
    
    function initTypographyControl(element) {
        var $input = $(element);
        var $control = $input.closest('.customize-control-polysaas-typography-popup');
        var $wrapper = $input.closest('.rey-control-wrap');
        var $typoBtn = $wrapper.find('.rey-typoBtn');
        var $resetBtn = $wrapper.find('.rey-typo-reset');
        var $popup = $wrapper.find('.rey-typo-popup');
        var $closeBtn = $wrapper.find('.rey-typo-popClose');
        var $responsiveHandlers = $wrapper.find('.rey-responsive-handlers span');
        var $fontControls = $popup.find('.typography-font-options');
        
        // Current responsive mode
        var currentDevice = 'desktop';
        
        // Flag to track if values have been changed by user
        var hasUserChanges = false;
        
        // Initialize responsive handlers
        initResponsiveHandlers();
        
        // Initially hide the reset button
        $resetBtn.hide();
        
        // Toggle typography popup
        $typoBtn.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Close all other popups
            $('.rey-typo-popup').not($popup).hide();
            $('.customize-control-polysaas-typography-popup').not($control).removeClass('popup-active');
            
            // Toggle the popup-active class on this control
            if ($popup.is(':visible')) {
                $control.removeClass('popup-active');
                $popup.hide();
            } else {
                $control.addClass('popup-active');
                $popup.show();
                
                // When opening the popup, sync with the current global device
                var currentGlobalDevice = getCurrentGlobalDevice();
                if (currentGlobalDevice !== currentDevice) {
                    switchDevice(currentGlobalDevice);
                }
            }
        });
        
        // Close popup
        $closeBtn.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $popup.hide();
            // Remove popup-active class
            $control.removeClass('popup-active');
        });
        
        // Handle input changes
        $popup.find('select, input').on('change keyup', function() {
            updateTypographySummary();
            updateTypographyValue();
            
            // Check if the values differ from defaults
            checkForChanges();
        });
        
        // Close popup when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest($popup).length && 
                !$(e.target).closest($typoBtn).length) {
                $popup.hide();
                $control.removeClass('popup-active');
            }
        });
        
        // Get default values
        function getDefaultValues() {
            // Try to get default from the default hidden field first
            var $defaultInput = $wrapper.find('.rey-typography-default-value');
            var defaultValue = $defaultInput.val() || '{}';
            
            // If that fails, try data-default attribute
            if (defaultValue === '{}') {
                defaultValue = $input.attr('data-default') || '{}';
            }
            
            try {
                return JSON.parse(defaultValue);
            } catch (e) {
                console.error('Error parsing default value:', e);
                return {};
            }
        }
        
        // Initialize the control
        function init() {
            var defaultValue = $control.data('default') || '{}';
            $input.attr('data-default', defaultValue);

            // Parse initial value
            var initialValue = $input.val() ? JSON.parse($input.val()) : {};
            
            // Get current global device
            var globalDevice = getCurrentGlobalDevice();
            
            // Set initial device
            switchDevice(globalDevice);
            
            // Set initial values
            updateControlsFromValue(initialValue);
            
            // Update summary based on current device
            updateTypographySummaryForDevice(globalDevice, initialValue);
            
            // Check if initial values differ from defaults
            checkForChanges();
            
            // Listen for global device preview changes
            listenToGlobalDeviceChanges();
        }
        
        // Initialize responsive handlers
        function initResponsiveHandlers() {
            $responsiveHandlers.on('click', function() {
                var device = $(this).data('device');
                switchDevice(device);
                
                // Also update the global responsive switcher
                updateGlobalResponsivePreview(device);
            });
        }
        
        // Switch between devices
        function switchDevice(device) {
            currentDevice = device;
            
            // Update UI
            $responsiveHandlers.removeClass('active');
            $responsiveHandlers.filter('[data-device="' + device + '"]').addClass('active');
            
            // Show/hide device-specific options
            $fontControls.removeClass('active');
            $fontControls.filter('[data-device="' + device + '"]').addClass('active');
            
            // Update control values for current device
            var value = $input.val() ? JSON.parse($input.val()) : {};
            updateControlsFromValue(value);
            
            // Update the summary based on this device's settings
            updateTypographySummaryForDevice(device, value);
        }
        
        // Get the current global device from WordPress customizer
        function getCurrentGlobalDevice() {
            // Default to desktop
            var device = 'desktop';
            
            // Try to get current device from WordPress preview
            if (wp.customize && wp.customize.previewedDevice) {
                var wpDevice = wp.customize.previewedDevice.get();
                
                // Map WordPress device names to our device names
                if (wpDevice === 'tablet') {
                    device = 'tablet';
                } else if (wpDevice === 'mobile') {
                    device = 'mobile';
                }
            }
            
            return device;
        }
        
        // Listen for changes in the global device preview
        function listenToGlobalDeviceChanges() {
            if (wp.customize && wp.customize.previewedDevice) {
                wp.customize.previewedDevice.bind(function(device) {
                    // Map WordPress device names to our device names
                    var mappedDevice = 'desktop';
                    
                    if (device === 'tablet') {
                        mappedDevice = 'tablet';
                    } else if (device === 'mobile') {
                        mappedDevice = 'mobile';
                    }
                    
                    // Switch our control to the same device
                    if (mappedDevice !== currentDevice) {
                        switchDevice(mappedDevice);
                    }
                    
                    // Update the summary to show this device's values
                    var value = $input.val() ? JSON.parse($input.val()) : {};
                    updateTypographySummaryForDevice(mappedDevice, value);
                });
            }
        }
        
        // Update the global responsive preview to match our device
        function updateGlobalResponsivePreview(device) {
            if (wp.customize && wp.customize.previewedDevice) {
                // Map our device names to WordPress device names (they should be the same)
                var wpDevice = device;
                
                // Set the global device
                wp.customize.previewedDevice.set(wpDevice);
            }
        }
        
        // Update controls based on the value
        function updateControlsFromValue(value) {
            // Get device-specific values or fallback to empty object
            var deviceValues = value[currentDevice] || {};
            
            // Set values for current device
            var $currentControls = $fontControls.filter('[data-device="' + currentDevice + '"]');
            
            if (currentDevice === 'desktop') {
                // Desktop gets all options
                $currentControls.find('.font-family select').val(deviceValues.font_family || '');
                $currentControls.find('.font-weight select').val(deviceValues.font_weight || '');
                $currentControls.find('.font-style select').val(deviceValues.font_style || '');
                $currentControls.find('.text-transform select').val(deviceValues.text_transform || '');
            }
            
            // These fields are common to all devices
            $currentControls.find('.font-size input').val(deviceValues.font_size || '');
            $currentControls.find('.line-height input').val(deviceValues.line_height || '');
            $currentControls.find('.letter-spacing input').val(deviceValues.letter_spacing || '');
        }
        
        // Update the summary display on the button
        function updateTypographySummary() {
            var value = getCurrentDeviceValues();
            
            // For responsive views, we need to combine with desktop values for the display
            var displayValues = {};
            
            if (currentDevice !== 'desktop') {
                // Get desktop values first (for font family and weight)
                var allValues = $input.val() ? JSON.parse($input.val()) : {};
                displayValues = allValues.desktop || {};
                
                // Override with current device values
                $.extend(displayValues, value);
            } else {
                displayValues = value;
            }
            
            var fontFamilyValue = displayValues.font_family ? displayValues.font_family : 'Default';
            var fontSize = value.font_size ? value.font_size + 'px' : '';
            var fontWeight = displayValues.font_weight ? displayValues.font_weight : '';
            var fontFamilyLabel = fontFamilyValue;

            // Try to get the label from the select options
            if (fontFamilyValue) {
                var $fontSelect = $fontControls.filter('[data-device="desktop"]').find('.font-family select');
                var $selectedOption = $fontSelect.find('option[value="' + fontFamilyValue + '"]');
                
                if ($selectedOption.length) {
                    fontFamilyLabel = $selectedOption.text();
                }
            }

            // Update the summary button
            $typoBtn.find('.font-family-label').text(fontFamilyLabel);
            $typoBtn.find('.font-size-label').text(fontSize);
            $typoBtn.find('.font-weight-label').text(fontWeight);
            
            // Add device indicator
            $typoBtn.removeClass('device-desktop device-tablet device-mobile');
            $typoBtn.addClass('device-' + currentDevice);
            
            // Check for changes to show/hide reset button and add has-value class
            checkForChanges();
        }
        
        // Update the summary based on a specific device's settings
        function updateTypographySummaryForDevice(device, values) {
            // Get the values for this device, fallback to desktop for common properties
            var deviceValues = values[device] || {};
            var desktopValues = values.desktop || {};
            
            // For responsive views, use desktop values for some properties
            var displayValues = {};
            
            if (device !== 'desktop') {
                // Use desktop values for font family and weight
                displayValues.font_family = desktopValues.font_family;
                displayValues.font_weight = desktopValues.font_weight;
                
                // Use device values for size-related properties
                displayValues.font_size = deviceValues.font_size;
            } else {
                displayValues = desktopValues;
            }
            
            // Extract the values we want to display
            var fontFamilyValue = displayValues.font_family ? displayValues.font_family : 'Default';
            var fontFamilyLabel = fontFamilyValue;
            var fontSize = deviceValues.font_size ? deviceValues.font_size + 'px' : '';
            var fontWeight = displayValues.font_weight ? displayValues.font_weight : '';
            
            // Try to get the label from the select options
            if (fontFamilyValue) {
                var $fontSelect = $fontControls.filter('[data-device="desktop"]').find('.font-family select');
                var $selectedOption = $fontSelect.find('option[value="' + fontFamilyValue + '"]');
                
                if ($selectedOption.length) {
                    fontFamilyLabel = $selectedOption.text();
                }
            }
            
            // Update the summary button
            $typoBtn.find('.font-family-label').text(fontFamilyLabel);
            $typoBtn.find('.font-size-label').text(fontSize);
            $typoBtn.find('.font-weight-label').text(fontWeight);
            
            // Add device indicator
            $typoBtn.removeClass('device-desktop device-tablet device-mobile');
            $typoBtn.addClass('device-' + device);
        }
        
        // Get current device values
        function getCurrentDeviceValues() {
            var $currentControls = $fontControls.filter('[data-device="' + currentDevice + '"]');
            
            // For desktop, get all values
            if (currentDevice === 'desktop') {
                return {
                    font_family: $currentControls.find('.font-family select').val(),
                    font_weight: $currentControls.find('.font-weight select').val(),
                    font_style: $currentControls.find('.font-style select').val(),
                    text_transform: $currentControls.find('.text-transform select').val(),
                    font_size: $currentControls.find('.font-size input').val(),
                    line_height: $currentControls.find('.line-height input').val(),
                    letter_spacing: $currentControls.find('.letter-spacing input').val()
                };
            } 
            // For tablet and mobile, only get responsive values
            else {
                return {
                    font_size: $currentControls.find('.font-size input').val(),
                    line_height: $currentControls.find('.line-height input').val(),
                    letter_spacing: $currentControls.find('.letter-spacing input').val()
                };
            }
        }
        
        // Check if current values differ from default values
        function checkForChanges() {
            var currentValue = $input.val() ? JSON.parse($input.val()) : {};
            var defaultValues = getDefaultValues();
            hasUserChanges = false;
            
            // Compare each device's values with defaults
            ['desktop', 'tablet', 'mobile'].forEach(function(device) {
                if (!currentValue[device]) {
                    return;
                }
                
                var currentDeviceValues = currentValue[device];
                var defaultDeviceValues = defaultValues[device] || {};
                
                // Compare each property
                for (var prop in currentDeviceValues) {
                    if (currentDeviceValues[prop] && 
                        currentDeviceValues[prop] !== defaultDeviceValues[prop]) {
                        hasUserChanges = true;
                        break;
                    }
                }
            });
            
            // If we don't have defaults, compare with empty string values
            if (!hasUserChanges && Object.keys(defaultValues).length === 0) {
                hasUserChanges = Object.keys(currentValue).some(function(device) {
                    return Object.keys(currentValue[device] || {}).some(function(prop) {
                        return currentValue[device][prop] !== '';
                    });
                });
            }
            
            // Show/hide reset button based on user changes
            if (hasUserChanges) {
                $resetBtn.show();
                $typoBtn.addClass('has-value');
            } else {
                $resetBtn.hide();
                $typoBtn.removeClass('has-value');
            }
        }
        
        // Update the hidden input value
        function updateTypographyValue() {
            var currentValue = $input.val() ? JSON.parse($input.val()) : {};
            
            // Update current device values
            currentValue[currentDevice] = getCurrentDeviceValues();
            
            // Update input value
            var newValue = JSON.stringify(currentValue);
            $input.val(newValue).trigger('change');
            
            // Update the summary to show this device's values
            updateTypographySummaryForDevice(currentDevice, currentValue);
        }

        // Reset button handling
        $resetBtn.off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Try to get default from the default hidden field first
            var $defaultInput = $wrapper.find('.rey-typography-default-value');
            var defaultValue = $defaultInput.val() || '{}';
            
            // If that fails, try data-default attribute
            if (defaultValue === '{}') {
                defaultValue = $input.attr('data-default') || '{}';
            }
        
            try {
                var defaultValues = JSON.parse(defaultValue);
                
                // Set the input value back to default
                $input.val(defaultValue).trigger('change');
                
                // Update all views
                var originalDevice = currentDevice;
                
                // Update desktop first
                switchDevice('desktop');
                updateControlsFromValue(defaultValues);
                
                // Then tablet
                switchDevice('tablet');
                updateControlsFromValue(defaultValues);
                
                // Then mobile
                switchDevice('mobile');
                updateControlsFromValue(defaultValues);
                
                // Switch back to original device
                switchDevice(originalDevice);
                
                // Reset user changes flag
                hasUserChanges = false;
                
                // Update UI
                $resetBtn.hide();
                $typoBtn.removeClass('has-value');

            } catch (error) {
                error_log('Error resetting typography: ' + error.message);
            }
        });
        
        // Initialize the control
        init();
    }
    
})(jQuery);