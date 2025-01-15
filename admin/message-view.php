<?php
if (!defined('WPINC')) {
    die;
}

// Check for message ID
$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($message_id <= 0) {
    wp_die(__('Invalid message ID', 'bluestoneapp-contact-us'));
}

// Get message data
global $wpdb;
$table_name = $wpdb->prefix . 'bluestoneapp_contact_messages';
$message = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $message_id
    ),
    ARRAY_A
);

if (!$message) {
    wp_die(__('Message not found', 'bluestoneapp-contact-us'));
}

// Mark as read if it's unread
if ($message['status'] === 'unread') {
    $wpdb->update(
        $table_name,
        array('status' => 'read'),
        array('id' => $message_id),
        array('%s'),
        array('%d')
    );
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo esc_html(sprintf(__('Message from %s', 'bluestoneapp-contact-us'), $message['name'])); ?>
    </h1>
    
    <a href="<?php echo esc_url(admin_url('admin.php?page=bluestoneapp-contact-messages')); ?>" class="page-title-action">
        <?php _e('Back to Messages', 'bluestoneapp-contact-us'); ?>
    </a>
    
    <hr class="wp-header-end">
    
    <div class="message-details">
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Name', 'bluestoneapp-contact-us'); ?></th>
                <td><?php echo esc_html($message['name']); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Email', 'bluestoneapp-contact-us'); ?></th>
                <td>
                    <a href="mailto:<?php echo esc_attr($message['email']); ?>">
                        <?php echo esc_html($message['email']); ?>
                    </a>
                </td>
            </tr>
            <?php if (!empty($message['phone'])): ?>
            <tr>
                <th scope="row"><?php _e('Phone', 'bluestoneapp-contact-us'); ?></th>
                <td>
                    <a href="tel:<?php echo esc_attr($message['phone']); ?>">
                        <?php echo esc_html($message['phone']); ?>
                    </a>
                </td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($message['subject'])): ?>
            <tr>
                <th scope="row"><?php _e('Subject', 'bluestoneapp-contact-us'); ?></th>
                <td><?php echo esc_html($message['subject']); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th scope="row"><?php _e('Date Submitted', 'bluestoneapp-contact-us'); ?></th>
                <td><?php echo mysql2date('F j, Y g:i a', $message['date_submitted']); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Message', 'bluestoneapp-contact-us'); ?></th>
                <td>
                    <div class="message-content">
                        <?php echo nl2br(esc_html($message['message'])); ?>
                    </div>
                </td>
            </tr>
        </table>
        
        <div class="message-actions">
            <a href="mailto:<?php echo esc_attr($message['email']); ?>" class="button">
                <?php _e('Reply by Email', 'bluestoneapp-contact-us'); ?>
            </a>
            
            <a href="#" class="button delete-message" data-id="<?php echo esc_attr($message['id']); ?>">
                <?php _e('Delete Message', 'bluestoneapp-contact-us'); ?>
            </a>
        </div>
    </div>
</div>

<style>
.message-details {
    background: #fff;
    padding: 20px;
    margin-top: 20px;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}
.message-content {
    background: #f8f9fa;
    padding: 15px;
    border: 1px solid #e2e4e7;
    border-radius: 4px;
    white-space: pre-wrap;
}
.message-actions {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e4e7;
}
.message-actions .button {
    margin-right: 10px;
}
</style>
