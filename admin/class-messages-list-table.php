<?php
if (!defined('WPINC')) {
    die;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class BluestoneApp_Contact_Messages_List_Table extends WP_List_Table {
    
    public function __construct() {
        parent::__construct(array(
            'singular' => __('Message', 'bluestoneapp-contact-us'),
            'plural'   => __('Messages', 'bluestoneapp-contact-us'),
            'ajax'     => false
        ));
    }

    public function get_columns() {
        return array(
            'cb'             => '<input type="checkbox" />',
            'name'           => __('Name', 'bluestoneapp-contact-us'),
            'email'          => __('Email', 'bluestoneapp-contact-us'),
            'phone'          => __('Phone', 'bluestoneapp-contact-us'),
            'subject'        => __('Subject', 'bluestoneapp-contact-us'),
            'message'        => __('Message', 'bluestoneapp-contact-us'),
            'date_submitted' => __('Date', 'bluestoneapp-contact-us'),
            'status'         => __('Status', 'bluestoneapp-contact-us')
        );
    }

    public function get_sortable_columns() {
        return array(
            'name'           => array('name', false),
            'email'          => array('email', false),
            'subject'        => array('subject', false),
            'date_submitted' => array('date_submitted', true),
            'status'         => array('status', false)
        );
    }

    private function validate_orderby($orderby) {
        $allowed_orderby = array('name', 'email', 'subject', 'date_submitted', 'status');
        return in_array($orderby, $allowed_orderby) ? $orderby : 'date_submitted';
    }

    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bluestoneapp_contact_messages';
        
        // Process bulk actions first
        $this->process_bulk_action();

        $per_page = 20;
        $current_page = $this->get_pagenum();

        // Handle search
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        $where = '';
        if (!empty($search)) {
            $where = $wpdb->prepare(
                " WHERE name LIKE %s OR email LIKE %s OR subject LIKE %s OR message LIKE %s",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }

        // Get total items with search condition
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name" . $where);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // Order parameters
        $orderby = isset($_REQUEST['orderby']) ? $this->validate_orderby($_REQUEST['orderby']) : 'date_submitted';
        $order = (isset($_REQUEST['order']) && in_array(strtoupper($_REQUEST['order']), array('ASC', 'DESC'))) 
            ? strtoupper($_REQUEST['order']) 
            : 'DESC';

        $offset = ($current_page - 1) * $per_page;

        // Build the query
        $query = sprintf(
            "SELECT * FROM %s%s ORDER BY %s %s LIMIT %d OFFSET %d",
            $table_name,
            $where,
            esc_sql($orderby),
            esc_sql($order),
            $per_page,
            $offset
        );

        // Get items with search and order
        $this->items = $wpdb->get_results($query, ARRAY_A);
    }

    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'message':
                return wp_trim_words($item[$column_name], 10);
            case 'date_submitted':
                return mysql2date('F j, Y g:i a', $item[$column_name]);
            default:
                return $item[$column_name];
        }
    }

    protected function column_name($item) {
        $view_url = add_query_arg(
            array(
                'page' => 'bluestoneapp-contact-view-message',
                'id' => $item['id']
            ),
            admin_url('admin.php')
        );

        $actions = array(
            'view'   => sprintf(
                '<a href="%s">%s</a>',
                esc_url($view_url),
                __('View', 'bluestoneapp-contact-us')
            ),
            'delete' => sprintf(
                '<a href="#" class="delete-message" data-id="%s">%s</a>',
                $item['id'],
                __('Delete', 'bluestoneapp-contact-us')
            )
        );

        return sprintf(
            '<a href="%s">%s</a> %s',
            esc_url($view_url),
            esc_html($item['name']),
            $this->row_actions($actions)
        );
    }

    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="message_ids[]" value="%s" />',
            $item['id']
        );
    }

    protected function get_bulk_actions() {
        return array(
            'mark_read'   => __('Mark as Read', 'bluestoneapp-contact-us'),
            'mark_unread' => __('Mark as Unread', 'bluestoneapp-contact-us'),
            'delete'      => __('Delete', 'bluestoneapp-contact-us')
        );
    }

    public function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bluestoneapp_contact_messages';
        
        // Security check
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
            $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
            if (!wp_verify_nonce($nonce, 'bulk-' . $this->_args['plural'])) {
                wp_die('Security check failed!');
            }
        }

        $action = $this->current_action();
        
        if (!$action) {
            return;
        }

        // Get the messages to process
        $message_ids = isset($_POST['message_ids']) ? $_POST['message_ids'] : array();
        
        if (empty($message_ids)) {
            return;
        }

        // Make sure ids are integers
        $message_ids = array_map('intval', $message_ids);

        switch ($action) {
            case 'delete':
                foreach ($message_ids as $id) {
                    $wpdb->delete(
                        $table_name,
                        array('id' => $id),
                        array('%d')
                    );
                }
                break;

            case 'mark_read':
                foreach ($message_ids as $id) {
                    $wpdb->update(
                        $table_name,
                        array('status' => 'read'),
                        array('id' => $id),
                        array('%s'),
                        array('%d')
                    );
                }
                break;

            case 'mark_unread':
                foreach ($message_ids as $id) {
                    $wpdb->update(
                        $table_name,
                        array('status' => 'unread'),
                        array('id' => $id),
                        array('%s'),
                        array('%d')
                    );
                }
                break;
        }
    }

    protected function column_status($item) {
        $status = $item['status'] === 'read' 
            ? '<span class="status-read">' . __('Read', 'bluestoneapp-contact-us') . '</span>'
            : '<span class="status-unread">' . __('Unread', 'bluestoneapp-contact-us') . '</span>';
        
        return $status;
    }
}
