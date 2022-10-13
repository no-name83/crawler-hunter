<?php


if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}



class Bot_Table extends WP_List_Table
{
  
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'bot_name',
            'plural' => 'bot_name',
        ));
    }


    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

  
    function column_age($item)
    {
        return '<em>' . $item['list_status'] . '</em>';
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
            'bot_name' => __('Name', 'crw_bot'),
            'access_time' => __('Time To Add', 'crw_bot'),
        );
        return $columns;
    }


    function get_sortable_columns()
    {
        $sortable_columns = array(
            'bot_name' => array('bot_name', true),
            'access_time' => array('access_time', false),
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Add To Blacklist'
        );
        return $actions;
    }



    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crawler_hunter'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
               // $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
                $wpdb->query("UPDATE $table_name SET list_status=1  WHERE id IN($ids)");
            }
        }
    }

 
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crawler_hunter'; 

        $per_page = 10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        
        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE list_status=0" );

        
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'access_time';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

       
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE list_status=0 ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page, 
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }
}

/**

