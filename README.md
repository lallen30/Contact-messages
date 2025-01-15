# BluestoneApps Contact Us Plugin

A comprehensive WordPress contact form solution with advanced admin management, spam protection, and customization options.

## Description

BluestoneApps Contact Us Plugin provides a feature-rich contact form solution for WordPress websites. It includes an intuitive admin interface for managing contact form submissions, customizable form fields, and email notifications.

## Features

- **Easy-to-Use Contact Form**
  - Name, Email, Phone, Subject, and Message fields
  - Customizable success messages
  - AJAX form submission
  - Built-in validation

- **Advanced Admin Management**
  - Dedicated admin dashboard for contact messages
  - View, sort, and search through all submissions
  - Bulk actions (delete, mark as read/unread)
  - Detailed message view with email reply option

- **Email Notifications**
  - Configurable notification emails
  - Customizable email templates
  - Option to enable/disable notifications

- **Security Features**
  - WordPress nonce verification
  - Input sanitization
  - XSS protection
  - AJAX security measures

## Installation

1. Upload the `BluestoneApps-contact-us` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Contact Messages' in the admin menu to configure the plugin

## Usage

### Adding the Contact Form

Use the shortcode `[BluestoneApps_contact_form]` in any post or page where you want the contact form to appear.

### Managing Messages

1. Access the admin panel through WordPress dashboard
2. Navigate to 'Contact Messages'
3. View all messages in the list
4. Click on names to view full message details
5. Use bulk actions for multiple messages
6. Search through messages using the search box

### Plugin Settings

1. Go to 'Contact Messages' → 'Settings'
2. Configure the following options:
   - Notification email address
   - Enable/disable email notifications
   - Customize success message
   - Set required fields

## Customization

The plugin can be customized through WordPress filters and actions:

```php
// Customize form fields
add_filter('BluestoneApps_contact_form_fields', 'custom_form_fields');

// Customize email template
add_filter('BluestoneApps_contact_email_template', 'custom_email_template');

// Add custom validation
add_filter('BluestoneApps_contact_validate_form', 'custom_validation');
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## Support

For support, feature requests, or bug reporting, please contact:
- Email: support@BluestoneApps.com
- Website: https://BluestoneApps.com/plugins/contact-us

## Changelog

### 1.0.0
- Initial release
- Basic contact form functionality
- Admin management interface
- Email notifications
- Search and sort capabilities
- Bulk actions
- Message view page

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
```

## Credits

Developed by BluestoneApps - https://BluestoneApps.com
