<?php
if (!defined('WPINC')) {
    die;
}

// Create an instance of our custom list table
require_once BLUESTONEAPP_CONTACT_PLUGIN_DIR . 'admin/class-messages-list-table.php';
$messages_table = new BluestoneApp_Contact_Messages_List_Table();
$messages_table->prepare_items();

// Handle form submission method
$form_method = (isset($_REQUEST['action']) && $_REQUEST['action'] !== -1) 
    || (isset($_REQUEST['action2']) && $_REQUEST['action2'] !== -1) 
    ? 'post' 
    : 'get';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Contact Messages', 'bluestoneapp-contact-us'); ?></h1>
    
    <form method="<?php echo $form_method; ?>">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
        <?php
        wp_nonce_field('bulk-' . $messages_table->_args['plural']);
        $messages_table->search_box(__('Search Messages', 'bluestoneapp-contact-us'), 'search_id');
        $messages_table->display();
        ?>
    </form>
</div>
