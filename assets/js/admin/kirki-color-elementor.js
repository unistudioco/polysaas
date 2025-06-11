/**
 * Elementor Color Control
 */
(function($) {
    'use strict';
    
    // Initialize when customizer loads
    wp.customize.bind('ready', function() {
        // Find all color controls
        $('.rey-color-control').each(function() {
            initColorControl(this);
        });
    });
    
    function initColorControl(element) {
        var $input = $(element);
        var $control = $input.closest('.customize-control-kirki-color-elementor');
        var $wrapper = $input.closest('.rey-control-wrap');
        var $globalBtn = $wrapper.find('.rey-colorGlobal');
        var $popup = $wrapper.find('.rey-colorGlobal-pop');
        var $closeBtn = $wrapper.find('.rey-colorGlobal-popClose');
        var $colorItems = $wrapper.find('.__item');
    
        // Add a flag to track if change is coming from global color selection
        var isGlobalColorChange = false;
        
        // Store all global colors in a lookup map for faster checking
        var globalColorsMap = {};
        $colorItems.each(function() {
            var $item = $(this);
            var colorValue = $item.data('color').toLowerCase();
            var colorId = $item.data('color-id');
            
            // Store the color in our map for easy lookup
            globalColorsMap[colorValue] = {
                id: colorId,
                element: $item
            };
        });
        
        // Initialize wp-color-picker with all options
        $input.wpColorPicker({
            defaultColor: $input.data('default-color') || '#FFFFFF',
            change: function(event, ui) {
                // When manually changing color with the picker
                setTimeout(function() {
                    var newColor = $input.val().toLowerCase();
                    $input.trigger('change');
                    
                    // Check if the new color matches a global color
                    if (globalColorsMap[newColor] && isGlobalColorChange) {
                        // Color matches a global color and was set via global selection
                        var colorData = globalColorsMap[newColor];
                        
                        // Update active states
                        $colorItems.removeClass('active');
                        colorData.element.addClass('active');
                        $globalBtn.addClass('active');
                        
                        // Store that this is a global color
                        $wrapper.attr('data-using-global-color', 'true');
                        $wrapper.attr('data-global-color-id', colorData.id);
                    } else if (!isGlobalColorChange) {
                        // Manual color selection
                        $colorItems.removeClass('active');
                        $globalBtn.removeClass('active');
                        $wrapper.removeAttr('data-using-global-color');
                        $wrapper.removeAttr('data-global-color-id');
                    }
                    
                    // Reset the flag
                    isGlobalColorChange = false;
                }, 100);
            },
            clear: function() {
                $colorItems.removeClass('active');
                $globalBtn.removeClass('active');
                $wrapper.removeAttr('data-using-global-color');
                $wrapper.removeAttr('data-global-color-id');
                $control.removeClass('picker-active');
            },
            hide: true,
            palettes: false,
            width: 255,
            mode: 'hsv',
            type: 'full',
            slider: 'horizontal',
            alphaEnabled: $input.data('alpha') === 'true'
        });
        
        // Position the global button correctly after wp-color-picker creates its UI
        // This is important to make it appear in the right spot
        setTimeout(positionGlobalButton, 100);
        
        function positionGlobalButton() {
            // Get the color picker button
            var $colorButton = $wrapper.find('.wp-color-result');
            
            // Position global button relative to color picker button
            if ($colorButton.length) {
                $globalBtn.css({
                    'top': $colorButton.position().top + 'px',
                    'height': $colorButton.outerHeight() + 'px'
                });
            }
        }
        
        // Add click handler for the color picker button to add picker-active class
        $wrapper.on('click', '.wp-color-result', function(e) {
            // Close any other open pickers first
            $('.customize-control-kirki-color-elementor').not($control).removeClass('picker-active');
            
            // Add picker-active class when the color picker is opened
            if (!$(this).hasClass('wp-picker-open')) {
                $control.addClass('picker-active');
            } else {
                $control.removeClass('picker-active');
            }
        });
        
        // Toggle global colors popup
        $globalBtn.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Close any other open pickers first
            $('.customize-control-kirki-color-elementor').not($control).removeClass('picker-active');
            
            // Toggle the picker-active class on this control
            if ($popup.is(':visible')) {
                $control.removeClass('picker-active');
            } else {
                $control.addClass('picker-active');
            }
            
            $popup.toggle();
        });
        
        // Close popup
        $closeBtn.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $popup.hide();
            // Remove picker-active class
            $control.removeClass('picker-active');
        });

        // Handle global color selection
        $colorItems.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $item = $(this);
            var colorId = $item.data('color-id');
            var colorVar = $item.data('color-var');
            var colorValue = $item.data('color');
            
            // Update active state on items
            $colorItems.removeClass('active');
            $item.addClass('active');
            
            // Add active class to globe icon
            $globalBtn.addClass('active');
            
            // Set flag before updating color picker to prevent change event from removing active class
            isGlobalColorChange = true;
            
            // Update color picker
            $input.wpColorPicker('color', colorValue);
            
            // Close popup
            $popup.hide();
            
            // Remove picker-active class when popup is closed
            $control.removeClass('picker-active');
            
            // Store that this is a global color
            $wrapper.attr('data-using-global-color', 'true');
            $wrapper.attr('data-global-color-id', colorId);
        });
        
        // Initial state check
        function setActiveColorState() {
            var currentValue = ($input.val() || '').toLowerCase();
            
            // Check if current color matches any global color
            if (globalColorsMap[currentValue]) {
                var colorData = globalColorsMap[currentValue];
                
                // Update active states
                $colorItems.removeClass('active');
                colorData.element.addClass('active');
                $globalBtn.addClass('active');
                
                // Store that this is a global color
                $wrapper.attr('data-using-global-color', 'true');
                $wrapper.attr('data-global-color-id', colorData.id);
                
                // There might be a delay in wp-color-picker rendering, so update the visual state
                setTimeout(function() {
                    var $colorButton = $wrapper.find('.wp-color-result');
                    $colorButton.addClass('global-color-active');
                }, 200);
            } else {
                $colorItems.removeClass('active');
                $globalBtn.removeClass('active');
                $wrapper.removeAttr('data-using-global-color');
                $wrapper.removeAttr('data-global-color-id');
            }
        }
        
        // Set initial active state
        setTimeout(setActiveColorState, 100);
        
        // Update position when window is resized
        $(window).on('resize', positionGlobalButton);
        
        // Close popup and remove picker-active class when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest($popup).length && 
                !$(e.target).closest($globalBtn).length && 
                !$(e.target).closest('.wp-picker-container').length) {
                $popup.hide();
                
                // Only remove the picker-active class if the popup is visible
                // This prevents conflicts with the color picker toggle
                if ($popup.is(':visible')) {
                    $control.removeClass('picker-active');
                }
            }
        });
        
        // Add a specific handler for the iris picker's close button
        $(document).on('click', '.wp-picker-clear, .wp-picker-default, .iris-picker-inner, .iris-square-handle', function(e) {
            e.stopPropagation(); // Prevent the document click from immediately firing
        });
        
        // Remove picker-active class when color picker is closed
        $wrapper.on('click', '.wp-picker-clear, .wp-picker-default', function() {
            $control.removeClass('picker-active');
        });
        
        // Handle the class based on the wp-picker-open class 
        $(document).on('click', '.wp-color-result', function() {
            var $this = $(this);
            var $currentControl = $this.closest('.customize-control-kirki-color-elementor');
            
            // We need to use setTimeout because the wp-picker-open class is added after the click event
            setTimeout(function() {
                if ($this.hasClass('wp-picker-open')) {
                    $currentControl.addClass('picker-active');
                } else {
                    $currentControl.removeClass('picker-active');
                }
            }, 10);
        });
        
        // Close iris color picker when clicking outside and remove picker-active class
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.iris-picker').length && 
                !$(e.target).closest('.wp-color-result').length) {
                $('.customize-control-kirki-color-elementor').removeClass('picker-active');
            }
        });
    }
    
})(jQuery);