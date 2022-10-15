<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
if ( ! class_exists( 'crw_ip_table' ) ) {

class crw_ip_table extends WP_List_Table
{
  
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'ip',
            'plural' => 'ip',
        ));
    }

   
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

   
    function column_age($item)
    {
        return '<em>' . $item['ip'] . '</em>';
    }

    
    function column_name($item)
    {
       //this function is disable
    }

  
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

   
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'id' => __('id', 'crw_ip'),
            'ip' => __('Ip', 'crw_ip'),
           // 'banned_total_time' => __('Time To Add', 'crw_ip'),
        );
        return $columns;
    }

  
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'id' => array('id', true),
            'ip' => array('Ip', false),
            'banned_total_time' => array('access_time', false),
        );
        return $sortable_columns;
    }

   
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Add to Whitelist'
        );
        return $actions;
    }

    
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crawler_hunter_ip'; 

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("UPDATE $table_name SET list_status=0  WHERE id IN($ids)");
            }
        }
    }

    
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crawler_hunter_ip'; 

        $per_page =10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        
        $this->_column_headers = array($columns, $hidden, $sortable);

        
        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM WHERE list_status=2 $table_name");

        
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'ip';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE list_status=2 ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

      
        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page, 
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }
}
}
//global $wpdb;

?>