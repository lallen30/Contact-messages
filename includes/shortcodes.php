<?php
/**
 * Shortcodes
 * 
 * Handles the contact form shortcode functionality
 */

if (!defined('WPINC')) {
    die;
}

function bluestoneapp_contact_form_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'title' => __('Contact Us', 'bluestoneapp-contact-us'),
        'redirect' => '',
    ), $atts);

    // Enqueue necessary scripts and styles
    wp_enqueue_style(
        'bluestoneapp-contact-form',
        BLUESTONEAPP_CONTACT_PLUGIN_URL . 'public/css/contact-form.css',
        array(),
        BLUESTONEAPP_CONTACT_VERSION
    );

    wp_enqueue_script(
        'bluestoneapp-contact-form',
        BLUESTONEAPP_CONTACT_PLUGIN_URL . 'public/js/contact-form.js',
        array('jquery'),
        BLUESTONEAPP_CONTACT_VERSION,
        true
    );

    wp_localize_script('bluestoneapp-contact-form', 'bluestoneappContact', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bluestoneapp_contact_nonce'),
        'redirect_url' => esc_url($atts['redirect'])
    ));

    // Start output buffering
    ob_start();
    ?>
    <div class="bluestoneapp-contact-form-wrapper">
        <?php if (!empty($atts['title'])): ?>
            <h2 class="bluestoneapp-contact-form-title"><?php echo esc_html($atts['title']); ?></h2>
        <?php endif; ?>

        <form id="bluestoneapp-contact-form" class="bluestoneapp-contact-form" method="post">
            <div class="form-group">
                <label for="contact-name"><?php _e('Name', 'bluestoneapp-contact-us'); ?> *</label>
                <input type="text" id="contact-name" name="name" required>
            </div>

            <div class="form-group">
                <label for="contact-email"><?php _e('Email', 'bluestoneapp-contact-us'); ?> *</label>
                <input type="email" id="contact-email" name="email" required>
            </div>

            <div class="form-group">
                <label for="contact-phone"><?php _e('Phone', 'bluestoneapp-contact-us'); ?></label>
                <input type="tel" id="contact-phone" name="phone">
            </div>

            <div class="form-group">
                <label for="contact-subject"><?php _e('Subject', 'bluestoneapp-contact-us'); ?></label>
                <input type="text" id="contact-subject" name="subject">
            </div>

            <div class="form-group">
                <label for="contact-message"><?php _e('Message', 'bluestoneapp-contact-us'); ?> *</label>
                <textarea id="contact-message" name="message" rows="5" required></textarea>
            </div>

            <!-- Honeypot field for spam protection -->
            <div class="bluestoneapp-hp-field">
                <input type="text" name="website" value="" autocomplete="off">
            </div>

            <?php wp_nonce_field('bluestoneapp_contact_nonce', 'contact_nonce'); ?>
            
            <div class="form-group submit-group">
                <button type="submit" class="bluestoneapp-contact-submit">
                    <?php _e('Send Message', 'bluestoneapp-contact-us'); ?>
                </button>
            </div>

            <div class="bluestoneapp-contact-message" style="display: none;"></div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('bluestoneapp_contact_form', 'bluestoneapp_contact_form_shortcode');
