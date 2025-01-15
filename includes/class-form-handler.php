<?php
/**
 * Form Handler Class
 * 
 * Handles form submissions and validation
 */

if (!defined('WPINC')) {
    die;
}

class BluestoneApp_Contact_Form_Handler {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_bluestoneapp_contact_submit', array($this, 'handle_form_submission'));
        add_action('wp_ajax_nopriv_bluestoneapp_contact_submit', array($this, 'handle_form_submission'));
    }

    public function handle_form_submission() {
        check_ajax_referer('bluestoneapp_contact_nonce', 'nonce');

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);

        // Validate required fields
        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error(__('Please fill in all required fields.', 'bluestoneapp-contact-us'));
        }

        // Validate email
        if (!is_email($email)) {
            wp_send_json_error(__('Please enter a valid email address.', 'bluestoneapp-contact-us'));
        }

        // Insert into database
        global $wpdb;
        $table_name = $wpdb->prefix . 'bluestoneapp_contact_messages';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message,
                'status' => 'unread'
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            wp_send_json_error(__('Failed to save your message. Please try again.', 'bluestoneapp-contact-us'));
        }

        // Send email notification if enabled
        $options = get_option('bluestoneapp_contact_options');
        if ($options['enable_notifications']) {
            $this->send_notification_email($name, $email, $phone, $subject, $message);
        }

        wp_send_json_success(array(
            'message' => $options['success_message']
        ));
    }

    private function send_notification_email($name, $email, $phone, $subject, $message) {
        $options = get_option('bluestoneapp_contact_options');
        $to = $options['notification_email'];
        
        $email_subject = str_replace(
            array('{name}', '{email}', '{phone}', '{subject}'),
            array($name, $email, $phone, $subject),
            $options['email_subject']
        );

        $email_body = str_replace(
            array('{name}', '{email}', '{phone}', '{subject}', '{message}'),
            array($name, $email, $phone, $subject, $message),
            $options['email_template']
        );

        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        wp_mail($to, $email_subject, $email_body, $headers);
    }
}

// Initialize the form handler
BluestoneApp_Contact_Form_Handler::get_instance();
