<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'crw_all_func' ) ) {

  class crw_all_func{
function crw_create_db()
{
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    // creates crawler_hunter in database if not exists
   $crw_table = $wpdb->prefix . "crawler_hunter";
    $charset_collate = $wpdb->get_charset_collate();
    $crw_sql = "CREATE TABLE IF NOT EXISTS $crw_table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `bot_name` varchar(1000) NOT NULL,
    `access_time` timestamp NOT NULL,
    `list_status` int(11),
    `total_access` int(11),
    UNIQUE (`id`)
    
    ) $charset_collate;";
    
   dbDelta($crw_sql);

/////////
  
     # code...


     
    $crw_table = $wpdb->prefix . "crawler_hunter_ip";
     $crw_charset_collate = $wpdb->get_charset_collate();
    $crw_sql = "CREATE TABLE IF NOT EXISTS $crw_table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `ip` varchar(255) NOT NULL,
        `banned_start_time` datetime,
    `list_status` int(11),
    `total_access` int(11),
    `banned_end_time` datetime,
    `banned_total_time` datetime,
     UNIQUE (`id`)
    
    ) $charset_collate;";
    
   dbDelta($crw_sql);
   










//////////////////////


   if ($crw_sql) {
    # code...
     $crw_table = $wpdb->prefix . "crawler_hunter";
    //$crw_bot_whitelist=array('google','bing');
     $crw_bot_whitelist=array('google','bing','facebook','yahoo','twitter');


    foreach ($crw_bot_whitelist as $crw_bot_add) {
      # code...

     $wpdb->insert( $crw_table, array('bot_name'=>$crw_bot_add, 'list_status'=> '0'),array( '%s', '%d'));
    }
    

    $crw_opt=update_option('crwopt','0');
    $crw_totalban_count=update_option('crw_totalban_count','5');
    $crw_startban_time = update_option('crw_startban_time','1');
    $crw_banexpire_time =update_option('crw_banexpire_time','240');
    $crw_ip_logs_delete = update_option('crw_ip_logs_delete','0');
   }
}


function crw_delete_records(){

global $wpdb;
$crw_table_name = $wpdb->prefix . 'crawler_hunter';
$crw_sql="DELETE FROM `wp_crawler_hunter` WHERE bot_name IN ('google','bing','facebook','yahoo','twitter')";
 $wpdb->query($crw_sql);


 //after delete records drop tables


  $crw_table_name = $wpdb->prefix . 'crawler_hunter';
    $crw_sql = "DROP TABLE IF EXISTS $crw_table_name";
    $wpdb->query($crw_sql);

///////////////
  $crw_table_name = $wpdb->prefix . 'crawler_hunter_ip';
    $crw_sql = "DROP TABLE IF EXISTS $crw_table_name";
    $wpdb->query($crw_sql);

   $crw_opt_delete=delete_option('crwopt');
   $crw_totalban_count=delete_option('crw_totalban_count','5');
   $crw_startban_time = delete_option('crw_startban_time','1');
   $crw_banexpire_time =delete_option('crw_banexpire_time','240');
   $crw_ip_logs_delete = delete_option('crw_ip_logs_delete','0');

   wp_clear_scheduled_hook( 'crw_delete_old_records_update' );


}



function crw_manuel_add_bot($crw_new_bot_name){



global $wpdb;
      
if (is_user_logged_in()) {
  # code...

    // $crw_new_bot_name=sanitize_text_field($_POST['crw_bot_name']);
    
   $crw_table_name=$wpdb->prefix."crawler_hunter";
  // header($_SERVER["SERVER_PROTOCOL"]." 403 Access Denied", true, 403);

   //$crw_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$crw_table_name." WHERE bot_name= '".$crw_new_bot_name."'"));
      $crw_query = $wpdb->get_results(
  $wpdb->prepare("SELECT  * FROM `$crw_table_name` 
 WHERE bot_name = %s
  ",
    $crw_new_bot_name
  )
);

   if($wpdb->num_rows == 0) {


     $wpdb->insert( $crw_table_name, array('bot_name'=>$crw_new_bot_name, 'list_status'=> '0'),array( '%s', '%d'));
 }





}


}

function crw_manuel_add_ip($crw_ip_addr_new){

  
 if (filter_var($crw_ip_addr_new, FILTER_VALIDATE_IP) && is_user_logged_in()) {
   // echo("$ip is a valid IP address");

   global $wpdb;
   $crw_table_name=$wpdb->prefix."crawler_hunter_ip";
//$crw_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$crw_table_name." WHERE ip= '".$crw_ip_addr_new."'"));
  // $crw_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$crw_table_name." WHERE ip= '".$crw_ip_addr_new."'"));
   $result = $wpdb->get_results(
  $wpdb->prepare("SELECT  * FROM `$crw_table_name` 
 WHERE ip = %s
  ",
    $crw_ip_addr_new
  )
);

   if($wpdb->num_rows == 0) {

   



$wpdb->insert( $crw_table_name, array('ip'=>$crw_ip_addr_new, 'list_status'=> '2'),array( '%s', '%d'));


}

}
}

function crw_custom_cron_job() {
    if (!wp_next_scheduled('crw_delete_old_records_update')) {
      
      wp_schedule_event( time(), 'hourly', 'crw_delete_old_records_update' );
      
    }
}

function crw_delete_old_records() {
    global $wpdb;

    $crw_ip_logs_new=get_option('crw_delete_ip_logs');
    
    
    $table_name = $wpdb->prefix . 'crawler_hunter_ip'; 
   
    $date_to_delete = date('Y-m-d', strtotime('-'.''.$crw_ip_logs_new.''.' days')); 
    
    $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE banned_total_time < %s", $date_to_delete));
}

function crw_delete_old_ip_logs($crw_ip_logs_new){


update_option('crw_delete_ip_logs',$crw_ip_logs_new);


}

function crw_limit_ip($crw_totalban_count_new,$crw_startban_time_new,$crw_banexpire_time_new){
  
$crw_hnt_opt=get_option( 'crwopt' );
$crw_totalban_count=get_option( 'crw_totalban_count' );
$crw_startban_time=get_option( 'crw_startban_time' );
$crw_banexpire_time =get_option('crw_banexpire_time');

   
   if ($crw_hnt_opt==0) {
    # code...

    update_option('crwopt','1');
    $crw_totalban_count=get_option( 'crw_totalban_count' );
    $crw_startban_time=get_option( 'crw_startban_time' );
    $crw_banexpire_time =get_option('crw_banexpire_time');
   
   }

   else if($crw_hnt_opt==1 ){
  

  update_option('crw_totalban_count',$crw_totalban_count_new);
  update_option('crw_startban_time',$crw_startban_time_new);
  update_option('crw_banexpire_time',$crw_banexpire_time_new);
  }
} 

function crw_unlimit_ip($crw_totalban_count_new,$crw_startban_time_new,$crw_banexpire_time_new){
  
$crw_hnt_opt=get_option( 'crwopt' );
$crw_totalban_count=get_option( 'crw_totalban_count' );
   
   if ($crw_hnt_opt==1) {
    # code...

    update_option('crwopt','0');

    
    $crw_totalban_count=get_option( 'crw_totalban_count' );
    $crw_startban_time=get_option( 'crw_startban_time' );
   $crw_banexpire_time =get_option('crw_banexpire_time');


   }

  
} 


function crawler_insert_block_url($crw_browser_new){

 global $wpdb;

 $crw_brw_total;

$crw_table_name=$wpdb->prefix . "crawler_hunter";

 // $crw_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$crw_table_name." WHERE ip= '".$crw_ip_addr_new."'"));
   $result = $wpdb->get_results(
  $wpdb->prepare("SELECT  * FROM `$crw_table_name` 
 WHERE bot_name = %s
  ",
    $crw_browser_new
  )
);

   if($wpdb->num_rows == 0) {

   
  


//$wpdb->insert( $crw_table_name, array('ip'=>$crw_ip_addr_new, 'list_status'=> '2'),array( '%s', '%d'));

    //$wpdb->insert( $crw_table_name, array('bot_name'=>$crw_browser_new, 'list_status'=> '1'),array( '%s', '%d'));
    $wpdb->insert( $crw_table_name, array('bot_name'=>$crw_browser_new, 'list_status'=> '1','total_access'=> '1'),array( '%s', '%d','%d'));


}

else{



     foreach ($result as $key) {
       # code...
          $key->total_access;
          $crw_brw_total=$key->total_access;

     }

     $crw_i=$crw_brw_total+1;

      $wpdb->update($crw_table_name, array('total_access'=> $crw_i),array('bot_name'=>$crw_browser_new));


}


 //  $wpdb->insert( $crw_table_name, array('bot_name'=>$crw_browser_new, 'list_status'=> '1'),array( '%s', '%d'));


}



function crawler_ip_check($get_crawler_ip_new){


//check blocked status
$crw_hnt_opt=get_option('crwopt');

//get settings
 $crw_totalban_count=get_option( 'crw_totalban_count' );
    $crw_startban_time=get_option( 'crw_startban_time' );
   $crw_banexpire_time =get_option('crw_banexpire_time');




global $wpdb;
 $crw_table_name=$wpdb->prefix."crawler_hunter_ip";
 
 $crw_banned_start_time=current_time( 'mysql' ); //first visit time 

  $crw_banned_end_time=date( 'Y-m-d H:i:s', strtotime( $crw_banned_start_time ) + $crw_startban_time * 60 ); //Added time after 1 minute
  $crw_banned_total_time=date( 'Y-m-d H:i:s', strtotime( $crw_banned_start_time ) + $crw_banexpire_time * 60 ); //time blocked for 4 hours

 //$crawler_ip_result = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_crawler_hunter_ip WHERE ip= '".$get_crawler_ip_new."'"));
    $crawler_ip_result = $wpdb->get_results(
  $wpdb->prepare("SELECT  * FROM `$crw_table_name` 
 WHERE ip = %s
  ",
    $get_crawler_ip_new
  )
);
   if (filter_var($get_crawler_ip_new, FILTER_VALIDATE_IP)) {
   
$banned_total_time;

     if ($crawler_ip_result  && !is_user_logged_in() ) {
    # code...

      $time;
      $total;
      $banned_end_time;

      $crw_list_status;
       

    foreach ($crawler_ip_result  as $key) {
      # code...
      global $wpdb;
       $crw_table_name=$wpdb->prefix."crawler_hunter_ip";
           // $key->total_access;  
          $crw_total=$key->total_access; 
           $crw_banned_end_time=$key->banned_end_time ;
        $crw_banned_total_time=$key->banned_total_time;
        $crw_list_status=$key->list_status;
    }
    $crw_banned_total_time=$crw_banned_total_time;
    
      $crw_i=$crw_total+1;

    //$crw_i=$crw_total;
  
            if ($crw_hnt_opt==1) {
  # code...
             
  $wpdb->update( $crw_table_name, array( 'total_access' =>$crw_i ,'banned_start_time'=>$crw_banned_start_time ),array('ip'=>$get_crawler_ip_new));



          
   $crw_banned_total_time1=$crw_banned_total_time;
            if ($crw_i>=$crw_totalban_count && $crw_banned_start_time <=$crw_banned_end_time ) {
              # code...
                 //   header($_SERVER["SERVER_PROTOCOL"]." 403 Access Denied", true, 403);
                 header( 'HTTP/1.1 403 Forbidden' );
                  header( 'Status: 403 Forbidden' );
                  header( 'Connection: Close' );
                  

    exit();
            }
      
             if ($crw_i>=$crw_totalban_count && $crw_banned_start_time >=$crw_banned_end_time && $crw_banned_start_time <= $crw_banned_total_time1 && $crw_list_status==1) {
              # code...

            // header($_SERVER["SERVER_PROTOCOL"]." 403 Access Denied", true, 403);
              header( 'HTTP/1.1 403 Forbidden' );
              header( 'Status: 403 Forbidden' );
              header( 'Connection: Close' );

                
              exit();
            }


          

           if ($crw_i>=$crw_totalban_count && $crw_banned_start_time > $crw_banned_total_time1 && $crw_list_status==1 )
            {

                $crw_banned_end_time=date( 'Y-m-d H:i:s', strtotime( $crw_banned_start_time ) + $crw_startban_time * 60 );
                $crw_banned_total_time=date( 'Y-m-d H:i:s', strtotime( $crw_banned_start_time ) + $crw_banexpire_time * 60);

         //14 400


$wpdb->update( $crw_table_name, array( 'total_access' =>'0' ,'banned_start_time'=>$crw_banned_start_time , 'banned_end_time'=>$crw_banned_end_time, 'banned_total_time'=>$crw_banned_total_time ),array('ip'=>$get_crawler_ip_new));
            }
      


     if ($crw_i< $crw_totalban_count && $crw_banned_start_time > $crw_banned_end_time && $crw_list_status==1 )
            {

                $crw_banned_end_time=date( 'Y-m-d H:i:s', strtotime( $crw_banned_start_time ) + $crw_startban_time * 60  );
                $crw_banned_total_time=date( 'Y-m-d H:i:s', strtotime( $crw_banned_start_time ) + $crw_banexpire_time * 60 );

         //14 400


$wpdb->update( $crw_table_name, array( 'total_access' =>'0' ,'banned_start_time'=>$crw_banned_start_time , 'banned_end_time'=>$crw_banned_end_time, 'banned_total_time'=>$crw_banned_total_time ),array('ip'=>$get_crawler_ip_new));
            }

}
   
   if ($crawler_ip_result && $crw_list_status==2) {
     # code...
  //  header($_SERVER["SERVER_PROTOCOL"]." 403 Access Denied", true, 403);
     //header(sanitize_text_field($_SERVER["SERVER_PROTOCOL"])." 403 Access Denied", true, 403);
    //exit();
       header( 'HTTP/1.1 403 Forbidden' );
    header( 'Status: 403 Forbidden' );
    header( 'Connection: Close' );
    //die();

exit();
   }

           
   }


       $crw_table_name=$wpdb->prefix . "crawler_hunter_ip";    
      
  

    if (!$crawler_ip_result  && !is_user_logged_in()  && $crw_hnt_opt==1) {
     



     $wpdb->insert( $crw_table_name, array('ip'=>$get_crawler_ip_new, 'banned_start_time'=> $crw_banned_start_time ,'list_status'=>'1', 'total_access'=>'1','banned_end_time'=>$crw_banned_end_time ,'banned_total_time'=>$crw_banned_total_time ));

    }





 }

}


function crw_bot_update($crw_bot){
global $wpdb;
$crw_table_name=$wpdb->prefix . "crawler_hunter";

    

            $wpdb->query($wpdb->prepare("UPDATE $crw_table_name SET list_status = '%d' WHERE id = '%d'", array('1', $crw_bot)));
         


}
function crw_ip_update($crw_ip){
global $wpdb;
$crw_table_name=$wpdb->prefix . "crawler_hunter_ip";

    

    
             $wpdb->query($wpdb->prepare("UPDATE $crw_table_name SET list_status = '%d' WHERE id = '%d'", array('0', $crw_ip)));
         


}


}

  }



?>