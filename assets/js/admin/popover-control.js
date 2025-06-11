(function($) {
    'use strict';

    var TypographyControl = {
        init: function() {
            this.initColorPicker();
            this.initSelect2();
            this.bindEvents();
            this.initValues(); // Add this to initialize values on load
        },

        initValues: function() {
            $('.typography-wrapper').each(function() {
                var $control = $(this).closest('.customize-control');
                var value = TypographyControl.getValue($control);
                TypographyControl.updatePreview($control, value);
            });
        },

        initColorPicker: function() {
            $('.typography-color').wpColorPicker({
                change: function(event, ui) {
                    $(this).trigger('change');
                }
            });
        },

        initSelect2: function() {
            $('.font-family-select, .variant-select, .text-transform-select').each(function() {
                $(this).select2({
                    dropdownParent: $(this).closest('.typography-wrapper'),
                    width: '100%',
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: 10,
                    containerCssClass: function() {
                        return 'select2-container--accessible';
                    },
                    dropdownCssClass: 'select2-dropdown--accessible'
                }).on('select2:open', function() {
                    $(this).removeAttr('aria-hidden');
                    setTimeout(function() {
                        $('.select2-search__field').focus();
                    }, 0);
                }).on('select2:close', function() {
                    $(this).focus();
                });
            });
        },

        bindEvents: function() {
            // Toggle typography panel
            $(document).on('click', '.rey-cstTypo-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $wrapper = $(this).closest('.customize-control').find('.typography-wrapper');
                
                // Close other popovers
                $('.typography-wrapper').not($wrapper).removeClass('active');
                
                // Toggle current popover
                $wrapper.toggleClass('active');

                // Position check
                TypographyControl.checkPosition($wrapper);
            });

            // Prevent popover close when clicking inside
            $(document).on('click', '.typography-wrapper', function(e) {
                e.stopPropagation();
            });

            // Close all popovers when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.rey-cstTypo-wrapper').length) {
                    $('.typography-wrapper').removeClass('active');
                }
            });

            // Update preview
            $(document).on('change', '.typography-wrapper select, .typography-wrapper input', function() {
                var $control = $(this).closest('.customize-control');
                var value = TypographyControl.getValue($control);
                TypographyControl.updatePreview($control, value);
            });
        },

        checkPosition: function($wrapper) {
            // Reset position classes
            $wrapper.removeClass('position-left position-right');

            // Get wrapper offset
            var offset = $wrapper.offset();
            var windowWidth = $(window).width();

            // Check if popover is too close to the right edge
            if (offset.left + $wrapper.outerWidth() > windowWidth - 20) {
                $wrapper.addClass('position-left');
            } else {
                $wrapper.addClass('position-right');
            }
        },

        getValue: function($control) {
            var $wrapper = $control.find('.typography-wrapper');
            return {
                'font-family': $wrapper.find('.font-family-select').val() || 'Default font',
                'variant': $wrapper.find('.variant-select').val() || 'regular',
                'font-size': $wrapper.find('.font-size input').val(),
                'line-height': $wrapper.find('.line-height input').val(),
                'letter-spacing': $wrapper.find('.letter-spacing input').val(),
                'text-transform': $wrapper.find('.text-transform-select').val(),
                'color': $wrapper.find('.color .wp-color-picker').val()
            };
        },

        updatePreview: function($control, value) {
            var $button = $control.find('.rey-cstTypo-btn');
            var previewText = '';

            if (value['font-family'] && value['variant']) {
                previewText = value['font-family'] + ' / ' + value['variant'];
            } else if (value['font-family']) {
                previewText = value['font-family'];
            } else {
                previewText = 'Default font';
            }

            $button.html(`
                <span class="dashicons dashicons-edit"></span>
                <span class="rey-cstTypo-text">${previewText}</span>
            `);

            if (value['font-family'] !== 'Default font') {
                $button.find('.rey-cstTypo-text').css({
                    'font-family': value['font-family'],
                    'font-weight': value['variant'].replace('regular', 'normal')
                });
            }
        },
    };

    // Initialize on customizer ready
    wp.customize.bind('ready', function() {
        TypographyControl.init();
    });

})(jQuery);