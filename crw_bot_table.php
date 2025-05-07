<?php


if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if ( ! class_exists( 'crw_bot_table' ) ) {

class crw_bot_table extends WP_List_Table
{
  
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'botname', 
                'plural'   => 'botnames',
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
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->
            _args['singular'], 
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
       $table_name = $wpdb->prefix . 'crawler_hunter'; 

        if ( 'delete' === $this->current_action() ) {
            if ( isset( $_GET['botname'] ) ) {
                $crw_i = 0;
                foreach ( $_GET['botname'] as $bot_id ) {
                    $crw_i++;
                    $result = $wpdb->update(
                        $table_name,
                        array(
                            'list_status' =>              '1',
                                                              
                        ),
                        array( 'id' => sanitize_text_field( $bot_id ) )
                    );
                 
                    
                }
              
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

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable,
        );
        $this->process_bulk_action();
        
        if ( isset( $_GET['order'] ) ) {
            $order = sanitize_text_field( $_GET['order'] );
        } else {
            $order = 'asc';
        }
        if ( isset( $_GET['orderby'] ) ) {
            $orderby = sanitize_text_field( $_GET['orderby'] );
        } else {
            $orderby = 'bot_name';
        }

      
         $order   = sanitize_sql_orderby( $order );
         $orderby = str_replace( ' ', '', $orderby );

        

          

          

          

                    $results = $wpdb->get_results(
                "SELECT * FROM `$table_name` WHERE list_status=0  order by " . $orderby .
                ' ' . $order
            );

      

        $data = array();
        
        foreach ( $results as $crw_query ) {
            array_push( $data, (array) $crw_query );
        }
        $current_page = $this->get_pagenum();
        $total_items  = count( $data );
        $data         = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
        $this->items  = $data;
        $this->set_pagination_args(
            array(
                'total_items' => $total_items, 
                'per_page'    => $per_page, 
                'total_pages' => ceil( $total_items / $per_page ), 
            )
        );
    }
}

}


