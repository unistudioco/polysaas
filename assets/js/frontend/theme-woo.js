/**
 * WooCommerce Variations - Convert selects to buttons
 * Add this to your theme's JavaScript file
 */
jQuery(document).ready(function($) {
    // Function to initialize variation controls
    function initVariationControls() {
      // Process each variations form on the page
      $('.variations_form').each(function() {
        const $form = $(this);
        
        // Process each variation row (size, color, etc.)
        $form.find('.variations tr').each(function() {
          const $row = $(this);
          const $select = $row.find('select');
          const attributeName = $select.data('attribute_name') || $select.attr('name');
          const selectId = $select.attr('id');
          
          // Skip if already processed
          if ($row.hasClass('variation-buttons-processed')) {
            return;
          }
          
          // Create container for the buttons
          const $buttonContainer = $('<div class="variation-buttons-container"></div>');
          
          // Add a label for screen readers that connects to the button group
          const containerId = 'variation-buttons-' + selectId;
          $buttonContainer.attr('id', containerId);
          $buttonContainer.attr('role', 'group');
          $buttonContainer.attr('aria-label', $row.find('label').text());
          
          // Process each option in the select
          $select.find('option').each(function() {
            const $option = $(this);
            const value = $option.val();
            const text = $option.text();
            
            // Skip the placeholder option
            if (!value) {
              return;
            }
            
            // Create button based on attribute type
            if (attributeName.indexOf('pa_color') > -1 || attributeName.indexOf('color') > -1) {
              // For color attributes
              const colorClass = 'color-' + value.toLowerCase().replace(/[^a-z0-9]/g, '-');
              const $button = $(`
                <button type="button" 
                  class="variation-button color-button ${colorClass}" 
                  data-value="${value}" 
                  title="${text}">
                  <span class="color-swatch"></span>
                  <span class="screen-reader-text">${text}</span>
                </button>
              `);
              
              $buttonContainer.append($button);
            } else {
              // For other attributes (size, etc.)
              const $button = $(`
                <button type="button" 
                  class="variation-button text-button" 
                  data-value="${value}">
                  ${text}
                </button>
              `);
              
              $buttonContainer.append($button);
            }
          });
          
          // Append the button container after the select
          $row.find('.value').append($buttonContainer);
          
          // Hide the original select
          $select.css('display', 'none');
          
          // Mark as processed
          $row.addClass('variation-buttons-processed');
          
          // Handle button clicks
          $buttonContainer.on('click', '.variation-button', function() {
            const $button = $(this);
            const value = $button.data('value');
            
            // Update the select value
            $select.val(value).trigger('change');
            
            // Remove active class from all buttons in this group
            $buttonContainer.find('.variation-button').removeClass('active');
            
            // Add active class to the clicked button
            $button.addClass('active');
          });
          
          // Update button states when the select changes
          $select.on('change', function() {
            const value = $(this).val();
            
            // Remove active class from all buttons
            $buttonContainer.find('.variation-button').removeClass('active');
            
            // Add active class to the matching button
            if (value) {
              $buttonContainer.find(`.variation-button[data-value="${value}"]`).addClass('active');
            }
          });
        });
      });
    }
    
    // Run when page loads
    initVariationControls();
    
    // Also run when variations are updated (for Ajax handling)
    $(document).on('woocommerce_update_variation_values', function() {
      initVariationControls();
    });
});