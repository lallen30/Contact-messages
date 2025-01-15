<?php
/**
 * Admin Class
 * 
 * Handles all admin-related functionality
 */

if (!defined('WPINC')) {
    die;
}

class BluestoneApp_Contact_Admin {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_bluestoneapp_delete_message', array($this, 'handle_delete_message'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Contact Messages', 'bluestoneapp-contact-us'),
            __('Contact Messages', 'bluestoneapp-contact-us'),
            'manage_options',
            'bluestoneapp-contact-messages',
            array($this, 'display_messages_page'),
            'dashicons-email',
            30
        );

        add_submenu_page(
            'bluestoneapp-contact-messages',
            __('Settings', 'bluestoneapp-contact-us'),
            __('Settings', 'bluestoneapp-contact-us'),
            'manage_options',
            'bluestoneapp-contact-settings',
            array($this, 'display_settings_page')
        );

        // Hidden menu item for viewing individual messages
        add_submenu_page(
            null,
            __('View Message', 'bluestoneapp-contact-us'),
            __('View Message', 'bluestoneapp-contact-us'),
            'manage_options',
            'bluestoneapp-contact-view-message',
            array($this, 'display_message_view')
        );
    }

    public function register_settings() {
        register_setting('bluestoneapp_contact_options', 'bluestoneapp_contact_options');

        add_settings_section(
            'bluestoneapp_contact_main',
            __('General Settings', 'bluestoneapp-contact-us'),
            null,
            'bluestoneapp-contact-settings'
        );

        add_settings_field(
            'notification_email',
            __('Notification Email', 'bluestoneapp-contact-us'),
            array($this, 'render_notification_email_field'),
            'bluestoneapp-contact-settings',
            'bluestoneapp_contact_main'
        );

        add_settings_field(
            'enable_notifications',
            __('Enable Email Notifications', 'bluestoneapp-contact-us'),
            array($this, 'render_enable_notifications_field'),
            'bluestoneapp-contact-settings',
            'bluestoneapp_contact_main'
        );

        add_settings_field(
            'success_message',
            __('Success Message', 'bluestoneapp-contact-us'),
            array($this, 'render_success_message_field'),
            'bluestoneapp-contact-settings',
            'bluestoneapp_contact_main'
        );
    }

    public function render_notification_email_field() {
        $options = get_option('bluestoneapp_contact_options');
        echo '<input type="email" name="bluestoneapp_contact_options[notification_email]" value="' . 
             esc_attr($options['notification_email']) . '" class="regular-text">';
    }

    public function render_enable_notifications_field() {
        $options = get_option('bluestoneapp_contact_options');
        echo '<input type="checkbox" name="bluestoneapp_contact_options[enable_notifications]" value="1" ' . 
             checked(1, $options['enable_notifications'], false) . '>';
    }

    public function render_success_message_field() {
        $options = get_option('bluestoneapp_contact_options');
        echo '<textarea name="bluestoneapp_contact_options[success_message]" class="large-text" rows="3">' . 
             esc_textarea($options['success_message']) . '</textarea>';
    }

    public function display_messages_page() {
        require_once BLUESTONEAPP_CONTACT_PLUGIN_DIR . 'admin/messages-list.php';
    }

    public function display_settings_page() {
        require_once BLUESTONEAPP_CONTACT_PLUGIN_DIR . 'admin/settings-page.php';
    }

    public function display_message_view() {
        require_once BLUESTONEAPP_CONTACT_PLUGIN_DIR . 'admin/message-view.php';
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'bluestoneapp-contact') !== false) {
            wp_enqueue_style(
                'bluestoneapp-contact-admin',
                BLUESTONEAPP_CONTACT_PLUGIN_URL . 'admin/css/admin.css',
                array(),
                BLUESTONEAPP_CONTACT_VERSION
            );

            wp_enqueue_script(
                'bluestoneapp-contact-admin',
                BLUESTONEAPP_CONTACT_PLUGIN_URL . 'admin/js/admin.js',
                array('jquery'),
                BLUESTONEAPP_CONTACT_VERSION,
                true
            );

            wp_localize_script('bluestoneapp-contact-admin', 'bluestoneappContact', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bluestoneapp_contact_admin_nonce')
            ));
        }
    }

    public function handle_delete_message() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'bluestoneapp_contact_admin_nonce')) {
            wp_send_json_error(array('message' => 'Invalid security token.'));
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'You do not have permission to perform this action.'));
        }

        // Get and validate message ID
        $message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
        if ($message_id <= 0) {
            wp_send_json_error(array('message' => 'Invalid message ID.'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'bluestoneapp_contact_messages';
        
        // Delete the message
        $result = $wpdb->delete(
            $table_name,
            array('id' => $message_id),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => 'Database error occurred while deleting the message.'));
        }

        wp_send_json_success(array('message' => 'Message deleted successfully.'));
    }
}

// Initialize the admin
BluestoneApp_Contact_Admin::get_instance();
