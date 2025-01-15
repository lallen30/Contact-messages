<?php
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php _e('Contact Form Settings', 'bluestoneapp-contact-us'); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields('bluestoneapp_contact_options');
        do_settings_sections('bluestoneapp-contact-settings');
        submit_button();
        ?>
    </form>

    <div class="bluestoneapp-contact-shortcode-info">
        <h2><?php _e('Shortcode Usage', 'bluestoneapp-contact-us'); ?></h2>
        <p><?php _e('Use this shortcode to display the contact form:', 'bluestoneapp-contact-us'); ?></p>
        <code>[bluestoneapp_contact_form]</code>

        <h3><?php _e('Available Parameters', 'bluestoneapp-contact-us'); ?></h3>
        <ul>
            <li><code>title</code> - <?php _e('Custom form title', 'bluestoneapp-contact-us'); ?></li>
            <li><code>redirect</code> - <?php _e('URL to redirect after successful submission', 'bluestoneapp-contact-us'); ?></li>
        </ul>

        <h4><?php _e('Example with parameters:', 'bluestoneapp-contact-us'); ?></h4>
        <code>[bluestoneapp_contact_form title="Get in Touch" redirect="/thank-you"]</code>
    </div>
</div>
