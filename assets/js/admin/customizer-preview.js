/**
 * Customizer live preview script
 */
(function($) {
    'use strict';

    // Get the prefix
    const prefix = polysaasCustomizer.prefix;

    // Update colors
    wp.customize(`${prefix}_primary_color`, function(value) {
        value.bind(function(newval) {
            // Update CSS variables
            document.documentElement.style.setProperty('--primary-color', newval);

            // Update specific elements
            $(`.bg-primary`).css('background-color', newval);
            $(`.text-primary`).css('color', newval);
            $(`.border-primary`).css('border-color', newval);
        });
    });

    // Update typography
    wp.customize(`${prefix}_body_font`, function(value) {
        value.bind(function(newval) {
            // Load font if not already loaded
            loadGoogleFont(newval);
            
            // Update body font
            $('body').css('font-family', newval);
        });
    });

    // Update footer copyright
    wp.customize(`${prefix}_footer_copyright`, function(value) {
        value.bind(function(newval) {
            $('.site-info').html(newval);
        });
    });

    /**
     * Load Google Font
     */
    function loadGoogleFont(font) {
        const fontName = font.replace(' ', '+');
        const fontUrl = `https://fonts.googleapis.com/css2?family=${fontName}:wght@400;500;600;700&display=swap`;
        
        if (!document.querySelector(`link[href="${fontUrl}"]`)) {
            const link = document.createElement('link');
            link.href = fontUrl;
            link.rel = 'stylesheet';
            document.head.appendChild(link);
        }
    }

})(jQuery);