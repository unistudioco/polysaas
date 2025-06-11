(function($) {
    'use strict';
    
    wp.customize.controlConstructor['polysaas-elementor-colors'] = wp.customize.Control.extend({
        ready: function() {
            var control = this;
            
            // Handle global color selection
            control.container.find('.elementor-global-color-item').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $this = $(this);
                var colorValue = $this.data('value'); // Get the actual color value
                var colorId = $this.data('id');
                
                // Store the actual color value in the main setting
                control.setting.set(colorValue);
                
                // Store the color ID in a separate setting if needed for reference
                if (wp.customize($this.data('id') + '_global')) {
                    wp.customize($this.data('id') + '_global').set(colorId);
                }
                
                // Update visual state
                control.container.find('.elementor-global-color-item').removeClass('active');
                $this.addClass('active');
            });
            
            // Set initial active state
            this.setActiveColor();
        },
        
        setActiveColor: function() {
            var control = this;
            var currentValue = control.setting.get();
            
            // Find if this is a global color by matching the hex value
            control.container.find('.elementor-global-color-item').each(function() {
                var $item = $(this);
                var colorValue = $item.data('value');
                
                if (colorValue === currentValue) {
                    $item.addClass('active');
                }
            });
        }
    });
    
})(jQuery);