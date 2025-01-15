<?php
/**
 * Plugin Name: BluestoneApp Contact Us Plugin
 * Plugin URI: https://bluestoneapp.com/plugins/contact-us
 * Description: A comprehensive contact form solution with admin management, spam protection, and customization options.
 * Version: 1.0.0
 * Author: BluestoneApp
 * Author URI: https://bluestoneapp.com
 * Text Domain: bluestoneapp-contact-us
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('BLUESTONEAPP_CONTACT_VERSION', '1.0.0');
define('BLUESTONEAPP_CONTACT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BLUESTONEAPP_CONTACT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation hook
register_activation_hook(__FILE__, 'bluestoneapp_contact_activate');

function bluestoneapp_contact_activate() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'bluestoneapp_contact_messages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(50),
        subject varchar(200),
        message longtext NOT NULL,
        date_submitted datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'unread',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Add default plugin options
    $default_options = array(
        'notification_email' => get_option('admin_email'),
        'enable_notifications' => true,
        'success_message' => 'Thank you for your message. We will get back to you soon.',
        'required_fields' => array('name', 'email', 'message'),
        'email_subject' => 'New Contact Form Submission',
        'email_template' => "New contact form submission:\n\nName: {name}\nEmail: {email}\nSubject: {subject}\nMessage: {message}"
    );
    
    add_option('bluestoneapp_contact_options', $default_options);
}

// Load required files
require_once BLUESTONEAPP_CONTACT_PLUGIN_DIR . 'includes/class-form-handler.php';
require_once BLUESTONEAPP_CONTACT_PLUGIN_DIR . 'includes/class-admin.php';
require_once BLUESTONEAPP_CONTACT_PLUGIN_DIR . 'includes/shortcodes.php';

// Initialize the plugin
function bluestoneapp_contact_init() {
    load_plugin_textdomain('bluestoneapp-contact-us', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'bluestoneapp_contact_init');
