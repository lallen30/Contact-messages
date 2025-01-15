jQuery(document).ready(function($) {
    // Handle single message deletion
    $('.delete-message').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to delete this message?')) {
            return;
        }

        var messageId = $(this).data('id');
        
        $.ajax({
            url: bluestoneappContact.ajax_url,
            type: 'POST',
            data: {
                action: 'bluestoneapp_delete_message',
                message_id: messageId,
                nonce: bluestoneappContact.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Reload the page to show updated list
                    location.reload();
                } else {
                    alert('Error deleting message: ' + response.data.message);
                }
            },
            error: function() {
                alert('Error deleting message. Please try again.');
            }
        });
    });

    // Handle bulk actions
    $('#doaction, #doaction2').on('click', function(e) {
        var action = $(this).prev('select').val();
        if (action === 'delete') {
            if (!confirm('Are you sure you want to delete the selected messages?')) {
                e.preventDefault();
            }
        }
    });
});
