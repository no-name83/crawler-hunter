<?php
/**
Plugin Name: crawler-hunter
Tags: bad bots,block,ban,control bots,spiders,ip,crawlers,anti spam
Requires at least: 3.6
Description:Bots and web crawlers coming to your site check the content of the Full user agent string and block the ones other than the white list.
Tested up to: 6.0.2
Requires PHP: 5.6
Version: 1.0
Stable tag: 1.0
License: GPL2
**/
require_once __DIR__ . '/crw_function.php';
require_once __DIR__ . '/crw_ip.php';

require_once __DIR__ . '/crw_bot_table.php';
require_once __DIR__ . '/crw_ip_table.php';


if ( ! defined( 'ABSPATH' ) ) exit;




  function crawler_hunter_admin_menu_option()
  {
    add_menu_page('Crawler Hunter','crawler-hunter','manage_options','crawler-hunter-admin-menu','crawler_hunter_scripts_page','',200);
add_submenu_page('crawler-hunter-admin-menu', __('crawler-hunter-ip','menu-test'), __('crawler-hunter-ip','menu-test'), 'manage_options', 'crw_ip.php', 'crawlers_ip_monitor');


  }

  add_action('admin_menu','crawler_hunter_admin_menu_option');

  
  function crawler_hunter_scripts_page()
  {

    


    ?>




<?php


    $table = new crw_bot_table();
    $table->prepare_items();



?>

     <div class="">
      <h2></h2>
      <form method="post" action="">
   <?php wp_nonce_field('crwhunter-nonce'); ?>
      <h1>
Add Bot Name To Whitelist</h1><br>
 <h4><b>Examples: google,bing</b></h4>
<input type="text" id="crw_bot_name" name="crw_bot_name">
                    <h3>WHITELISTED</h3><br>

                         
                    

  
<?php








?>

    <input type="submit" name="crw_add" class="button button-primary" value="Add Bot">
      </form>
    </div>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>"/>
        <?php $table->display() ?>
    </form>



<br><br>
<div class="wrap">
<h2><center>Blocked Bot  Logs Monitor </center></h2>

<?php

global $wpdb;
      

       
        $qry="SELECT 
    *
    
FROM 
    wp_crawler_hunter WHERE list_status=1
";
        $result=$wpdb->get_results($qry, object);
        //print_r($result);
        //if($result):


         echo  '<div class="container">';

         echo ' <table  id="crw_demo_datatable" cellpadding="2" cellspacing="2"  border="4" width="100%" >
    <thead>
      <tr>
        <th>Bot Name</th>
    <th>Last  Ban Time</th>
    <th>Total Ban</th>
       
        
        
        
    </tr>
    </thead>';

    
  
  '<tbody>';
            foreach($result as $row){
        
        echo '<td>' . esc_attr($row->bot_name) ."</td>";
      echo '<td>' . esc_attr($row->access_time) ."</td>";

     echo '<td>' . esc_attr($row->total_access) ."</td>";

          echo "</tr>";
        
      }
               
         
          echo ' </tbody>
  </table>
</div>';


?>



</div>


</div>

  

    <?php
  }


  if ( ! function_exists( 'crw_manuel_adding' ) ) {
function crw_manuel_adding(){

 $crw_all_func = new   crw_all_func; 

  if (isset($_POST['crw_add'] ) &&   $crw_bot_name=$_POST['crw_bot_name'] && wp_verify_nonce($_POST['_wpnonce'], 'crwhunter-nonce')) {
    # code...
  $crw_new_bot_name=sanitize_text_field($_POST['crw_bot_name']);
   $crw_all_func->crw_manuel_add_bot($crw_new_bot_name);



     //echo '<meta http-equiv="refresh" content="1">';













  }


    if (isset($_POST['crw_add'] ) &&   $crw_ip_addr=$_POST['ip_addr'] && wp_verify_nonce($_POST['_wpnonce'], 'crwhunter-nonce')) {
   
      //$crw_ip_addr=$_POST['ip_addr'];
      $crw_ip_addr_new=sanitize_text_field($crw_ip_addr=$_POST['ip_addr']);


     $crw_all_func->crw_manuel_add_ip($crw_ip_addr_new);
    
   
    







    
}






}

}

if ( ! function_exists( 'crw_auto_detectet_bot' ) ) {
function crw_auto_detectet_bot(){


$crw_all_func = new   crw_all_func; 

//$crw_browser=$_SERVER['HTTP_USER_AGENT']."\n";
 $crw_browser_new=sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
$crw_url_not_secure_plus=strpos($crw_browser_new, "+http");
 $crw_url_secure_plus=strpos($crw_browser_new, "+https");
$crw_url_not_secure=strpos($crw_browser_new, "http");
 $crw_url_secure=strpos($crw_browser_new, "https");
 $crw_url_other=strpos($crw_browser_new, "compatible");
 
if (sanitize_text_field( $_SERVER['REQUEST_METHOD']==='GET')) {
 if ($crw_url_not_secure_plus || $crw_url_secure_plus|| $crw_url_not_secure || $crw_url_secure || $crw_url_other ) {
 
  //$whitelisted=array();
  $crw_whitelisted=array();
   global $wpdb;
 
 //$home_site=get_site_url();
 //$crw_home_site=get_site_url();


$crw_table_name=$wpdb->prefix . "crawler_hunter";

//$get_whitelisted = $wpdb->get_results($wpdb->prepare( "SELECT * FROM `wp_crawler_hunter` WHERE  list_status=0" ));
 $crw_get_whitelisted = $wpdb->get_results($wpdb->prepare( "SELECT * FROM `$crw_table_name` WHERE  list_status=0" ));



        foreach ($crw_get_whitelisted as $crw_get_whitelisted){

          // $get_whitelisted->bot_name;
          $crw_get_whitelisted->bot_name;
          // $whitelisted[]=$get_whitelisted->bot_name;
           $crw_whitelisted[]=$crw_get_whitelisted->bot_name;
   


}

//$whitelisted[]=$home_site;
//$crw_whitelisted[]=$home_site;
 



   foreach ($crw_whitelisted as $check_crawler) {
  # code...


  if(strpos($crw_browser_new, $check_crawler) !== false){
    //echo "Found!";
    $crawler_find=$check_crawler;
} 
}

     if (!isset($crawler_find)) {
    

   $crw_all_func-> crawler_insert_block_url($crw_browser_new);

         //header($_SERVER["SERVER_PROTOCOL"]." 403 Access Denied", true, 403);
   //header(sanitize_text_field($_SERVER["SERVER_PROTOCOL"])." 403 Access Denied", true, 403);
   header( 'HTTP/1.1 403 Forbidden' );
    header( 'Status: 403 Forbidden' );
    header( 'Connection: Close' );
   

exit();
     }


  

  


 }

 


else{


// $get_crawler_ip=$_SERVER['REMOTE_ADDR'] ;
  
  //$get_crawler_ip_new=wp_filter_nohtml_kses($_SERVER['REMOTE_ADDR']);
  $get_crawler_ip_new=sanitize_text_field($_SERVER['REMOTE_ADDR']);

$crw_all_func->crawler_ip_check($get_crawler_ip_new);




}

}

}

}












add_action( 'wp_enqueue_scripts', 'crw_datatables_script_js', 10 );

    
  function crw_datatables_script_js() {
  
  wp_enqueue_script('jquery-datatable' ,  plugin_dir_url( __FILE__ ). 'js/jquery.dataTables.min.js',  array('jquery' ));   //+++
  wp_enqueue_style( 'crw-style', plugins_url( '/css/jquery.dataTables.min.css', __FILE__ ), false, '1.0', 'all' );
//?>
<script>  
  jQuery(document).ready(function() { 
    jQuery('#crw_demo_datatable').dataTable({


    "order": [1, 'desc']

    });
  });
  </script> 
<?php   
  }


////////////////////
    add_action('admin_head', 'crw_datatables_script_js');
   
   






  add_action('init','crw_manuel_adding', 10,2);
   add_action('init','crw_auto_detectet_bot', 10,4);
  

  
 
 
  register_activation_hook( __FILE__, 'crw_all_func::crw_create_db' );
  
    register_uninstall_hook(__FILE__, 'crw_all_func::crw_delete_db');




































