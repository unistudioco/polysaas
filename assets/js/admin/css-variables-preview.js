/**
 * CSS Variables Live Preview
 * Specializes in updating CSS variables in the :root selector
 */
(function($) {
    'use strict';
    
    // Debug function
    function debug(message, data) {
        if (window.console && window.console.log) {
            console.log('CSS Variables Preview: ' + message, data || '');
        }
    }
    
    debug('Script loaded and running');
    
    // When the preview is ready
    wp.customize.bind('preview-ready', function() {
        debug('Preview ready');
        
        // Helper function to update CSS variables
        function updateCSSVariable(variableName, value) {
            debug('Updating CSS variable', { name: variableName, value: value });
            document.documentElement.style.setProperty(variableName, value);
        }
        
        // For each typography setting
        Object.values(polysaasTypography.settings).forEach(function(settingId) {
            wp.customize(settingId, function(value) {
                debug('Setting up binding for', settingId);
                
                // First apply current value
                var currentValue = value.get();
                if (currentValue) {
                    try {
                        var settings = typeof currentValue === 'string' ? JSON.parse(currentValue) : currentValue;
                        
                        // Apply CSS variables if desktop font family exists
                        // if (settings.desktop && settings.desktop.font_family) {
                        //     updateCSSVariable('--uc-font-secondary', settings.desktop.font_family);
                        // }
                    } catch (e) {
                        debug('Error processing current value', e);
                    }
                }
                
                // Then handle changes
                value.bind(function(newValue) {
                    debug('Value changed for ' + settingId, newValue);
                    
                    try {
                        var settings = typeof newValue === 'string' ? JSON.parse(newValue) : newValue;
                        
                        // Apply CSS variables if desktop font family exists
                        // if (settings.desktop && settings.desktop.font_family) {
                        //     updateCSSVariable('--uc-font-secondary', settings.desktop.font_family);
                        // }
                    } catch (e) {
                        debug('Error processing new value', e);
                    }
                });
            });
        });
    });
})(jQuery);