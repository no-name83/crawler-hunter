<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'crawlers_ip_monitor' ) ) {
function crawlers_ip_monitor(){
require_once __DIR__ . '/crw_function.php';
require_once __DIR__ . '/crw_ip_table.php';
  

###ad ip 





  $crw_all_func = new   crw_all_func; 
  $crw_totalban_count_new="";
  $crw_startban_time_new="";
  $crw_banexpire_time_new="";


    if (isset($_POST['crw_add'] ) &&   $crw_ip_addr=$_POST['ip_addr'] && wp_verify_nonce($_POST['_wpnonce'], 'crwhunter-nonce')) {
   
      
      $crw_ip_addr_new=sanitize_text_field($crw_ip_addr=$_POST['ip_addr']);


     $crw_all_func->crw_manuel_add_ip($crw_ip_addr_new);




}

  $crw_hnt_opt=get_option( 'crwopt' );
 
     if (isset($_POST['crw_ip_box']) && $_SERVER['REQUEST_METHOD'] === 'POST' && wp_verify_nonce($_POST['_wpnonce'], 'crwhunter-nonce')) {
  # code...
      $crw_totalban_count_new=intval($_POST['crw_totalban_count']);
      $crw_startban_time_new=intval($_POST['crw_startban_time']);
      $crw_banexpire_time_new=intval($_POST['crw_banexpire_time']);

   $crw_all_func->crw_limit_ip($crw_totalban_count_new,$crw_startban_time_new,$crw_banexpire_time_new);
}

else if(!isset($_POST['crw_ip_box']) && $_SERVER['REQUEST_METHOD'] === 'POST' && wp_verify_nonce($_POST['_wpnonce'], 'crwhunter-nonce')){
	 $crw_totalban_count_new=intval($_POST['crw_totalban_count']);
      $crw_startban_time_new=intval($_POST['crw_startban_time']);
      $crw_banexpire_time_new=intval($_POST['crw_banexpire_time']);
  
   $crw_totalban_count_new=intval($_POST['crw_totalban_count']);



   $crw_all_func->crw_unlimit_ip($crw_totalban_count_new,$crw_startban_time_new,$crw_banexpire_time_new);
  
}

if (isset($_POST["crw_ip_logs"]) && wp_verify_nonce($_POST['_wpnonce'], 'crwhunter-nonce')) {
	# code...
  
  $crw_ip_logs_new=sanitize_text_field($_POST["crw_ip_logs_delete"]);
	
	$crw_all_func->crw_delete_old_ip_logs($crw_ip_logs_new);
}
?>

<form method="post" action="">
  <?php wp_nonce_field('crwhunter-nonce'); ?>
<h1>Add Ip   To Blacklist</h1>
<input type="text" id="ip_addr" name="ip_addr">
                    <h3>BLACKLISTED</h3>
                    <b>IP addresses that make more than  <input type="text" id="pin" name="crw_totalban_count" maxlength="20"  value ="<?php if(get_option( 'crwopt' )==1) echo get_option("crw_totalban_count"); ?>" size="1">  times requests in <input type="text" id="pin" name="crw_startban_time" maxlength="4" value="<?php if(get_option( 'crwopt' )==1) echo get_option("crw_startban_time"); ?>" size="1"> minutes will be blocked for   <input type="text" id="pin" name="crw_banexpire_time" maxlength="4" value="<?php if(get_option( 'crwopt' )==1) echo get_option("crw_banexpire_time"); ?>" size="1">minutes &nbsp;
                        <?php $crw_hnt_opt = get_option( 'crwopt','');

                         
                         ?>

<input type="checkbox" name="crw_ip_box" value="1"<?php checked(  get_option( 'crwopt' ) ); ?> /> <input type="submit" name="crw_opt_status" class="button button-primary" value="Save"> <br>
<hr>
sdadad </br>
<hr>
<input type="submit" name="crw_add_ip" class="button button-primary" value="Add  Ip">
</form>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>"/>
        <?php 

              $crw_delete_ip_logs=get_option('crw_delete_ip_logs');
              $table = new crw_ip_table();
    $table->prepare_items();

        $table->display() ?>
</form><br><br>
<div class="wrap">
<h2><center>Blocked Ip  Logs Monitor </center></h2>

<?php

global $wpdb;
$table_name = $wpdb->prefix . 'crawler_hunter_ip'; 
      

        $qry="SELECT 
   *
  
FROM
    $table_name WHERE list_status=1 AND total_access >=5
";
        $result=$wpdb->get_results($qry, object);
        //print_r($result);
        //if($result):


         echo  '<div class="container">';

         echo ' <table  id="crw_demo_datatable" cellpadding="2" cellspacing="2"  border="4" width="100%" >
    <thead>
      <tr>
        <th>Ip</th>
    <th>Total Ban</th>
    <th>Ban Expire Time</th>
       
        
        
        
    </tr>
    </thead>';

    
  
  '<tbody>';
            foreach($result as $row){
        
        echo '<td>' . esc_attr($row->ip) ."</td>";
        echo '<td>' . esc_attr($row->total_access)."</td>";
        echo '<td>' . esc_attr($row->banned_total_time) ."</td>";
          echo "</tr>";
        
      }
               
         
          echo ' </tbody>
  </table>
</div>';

echo "Delete Ip Logs Before:";
echo '<select name="crw_ip_logs_delete" style="width:150px;">';
if ($crw_delete_ip_logs==0) {
	# code...
	echo' <option value="0" selected> Never </option>
  <option value="3">3 Days</option>
  <option value="7">7 Days</option>
  <option value="30">30 Days</option>';
}
else if ($crw_delete_ip_logs==3) {
	# code...
	echo' <option value="0"> Never </option>
  <option value="3" selected>3 Days</option>
  <option value="7">7 Days</option>
  <option value="30">30 Days</option>';
}
else if ($crw_delete_ip_logs==7) {
	# code...
	echo' <option value="0"> Never </option>
  <option value="3">3 Days</option>
  <option value="7" selected>7 Days</option>
  <option value="30">30 Days</option>';
}
else if ($crw_delete_ip_logs==30) {
	# code...
	echo' <option value="0"> Never </option>
  <option value="3">3 Days</option>
  <option value="7">7 Days</option>
  <option value="30" selected>30 Days</option>';
}
echo "</select>";


echo '<input type="submit" name="crw_ip_logs" class="button button-primary" value="Save">';






?>



</div>


</div>





    <?php
  
  }
}

add_action( 'wp_enqueue_scripts', 'crw_datatables_script1_js', 10 );

   
  function crw_datatables_script1_js() {

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


 

 $crw_all_func = new   crw_all_func; 

 add_action('init', array($crw_all_func,'crw_custom_cron_job'));

 add_action( 'crw_delete_old_records_update', array($crw_all_func, 'crw_delete_old_records'));

?>