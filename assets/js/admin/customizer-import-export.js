(function($) {
    'use strict';

    // Add styles
    $('<style>').text(`
        .kirki-import-controls, .kirki-reset-controls { margin: 1em 0; }
        .kirki-import-file { margin-bottom: 1em; }
        .kirki-import-images { display: block; margin: 1em 0; }
        .kirki-uploading { display: none; margin: 1em 0; }
        .kirki-hr { margin: 20px 0; border: none; border-top: 1px solid #ddd; }
        .button-danger { 
            background: #dc3545 !important; 
            border-color: #dc3545 !important;
            color: #fff !important;
        }
        .button-danger:hover {
            background: #c82333 !important;
            border-color: #bd2130 !important;
        }
    `).appendTo('head');

    // Export
    $(document).on('click', 'input[name=kirki-export-button]', function(e) {
        e.preventDefault();
        const $button = $(this);
        
        // Disable the button temporarily
        $button.prop('disabled', true);
        
        // Get current customizer values
        wp.customize.state('saved').set(true);
        
        const downloadURL = `${kirkiImportExport.ajaxurl}?action=${kirkiImportExport.action_export}&nonce=${kirkiImportExport.nonce}`;
        window.location.href = downloadURL;
        
        // Re-enable the button after a short delay
        setTimeout(() => {
            $button.prop('disabled', false);
        }, 1000);
    });

    // Import
    $(document).on('click', 'input[name=kirki-import-button]', function(e) {
        e.preventDefault();

        const $button = $(this);
        const $file = $('input[name=kirki-import-file]');
        const $message = $('.kirki-uploading');
        
        if (!$file.val()) {
            alert('Please choose a file to import.');
            return;
        }

        // Disable button and show message
        $button.prop('disabled', true);
        $message.show();

        // Create FormData object
        const formData = new FormData();
        formData.append('action', kirkiImportExport.action_import);
        formData.append('nonce', kirkiImportExport.nonce);
        formData.append('import_file', $file[0].files[0]);

        // Perform AJAX upload
        $.ajax({
            url: kirkiImportExport.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Settings imported successfully. The page will now reload.');
                    wp.customize.state('saved').set(true);
                    window.location.reload();
                } else {
                    alert(response.data?.message || 'Import failed');
                }
            },
            error: function() {
                alert('Import failed');
            },
            complete: function() {
                $button.prop('disabled', false);
                $message.hide();
            }
        });
    });

    // Reset
    $(document).on('click', 'input[name=kirki-reset-button]', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to reset all settings to their defaults? This cannot be undone!')) {
            return;
        }

        const $button = $(this);
        const $nonceField = $('input[name="' + kirkiImportExport.reset_nonce_id + '"]');
        
        if (!$nonceField.length) {
            alert('Security check failed');
            return;
        }

        $button.prop('disabled', true);

        $.ajax({
            url: kirkiImportExport.ajaxurl,
            type: 'POST',
            data: {
                action: kirkiImportExport.action_reset,
                nonce: $nonceField.val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Settings reset successfully. The page will now reload.');
                    wp.customize.state('saved').set(true);
                    window.location.reload();
                } else {
                    alert(response.data?.message || 'Reset failed');
                }
            },
            error: function() {
                alert('Reset failed');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });

})(jQuery);