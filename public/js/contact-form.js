jQuery(document).ready(function($) {
    $('#bluestoneapp-contact-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitButton = $form.find('button[type="submit"]');
        var $message = $form.find('.bluestoneapp-contact-message');

        // Disable submit button
        $submitButton.prop('disabled', true);

        // Clear previous messages
        $message.removeClass('success error').hide();

        // Get form data
        var formData = new FormData($form[0]);
        formData.append('action', 'bluestoneapp_contact_submit');
        formData.append('nonce', bluestoneappContact.nonce);

        // Submit form via AJAX
        $.ajax({
            url: bluestoneappContact.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $message.addClass('success')
                            .html(response.data.message)
                            .fadeIn();
                    
                    // Reset form
                    $form[0].reset();

                    // Redirect if URL is provided
                    if (bluestoneappContact.redirect_url) {
                        setTimeout(function() {
                            window.location.href = bluestoneappContact.redirect_url;
                        }, 2000);
                    }
                } else {
                    $message.addClass('error')
                            .html(response.data)
                            .fadeIn();
                }
            },
            error: function() {
                $message.addClass('error')
                        .html('An error occurred. Please try again.')
                        .fadeIn();
            },
            complete: function() {
                // Re-enable submit button
                $submitButton.prop('disabled', false);
            }
        });
    });
});
