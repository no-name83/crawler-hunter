<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function crawlers_ip_monitor(){
require_once __DIR__ . '/crw_function.php';
require_once __DIR__ . '/crw_ip_table.php';
  

###ad ip 


  $crw_all_func = new   crw_all_func; 


    if (isset($_POST['crw_add'] ) &&   $crw_ip_addr=$_POST['ip_addr'] && wp_verify_nonce($_POST['_wpnonce'], 'crwhunter-nonce')) {
   
      //$crw_ip_addr=$_POST['ip_addr'];
      //$crw_ip_addr_new=wp_filter_nohtml_kses($crw_ip_addr=$_POST['ip_addr']);
      $crw_ip_addr_new=sanitize_text_field($crw_ip_addr=$_POST['ip_addr']);


     $crw_all_func->crw_manuel_add_ip($crw_ip_addr_new);


}
?>

<form method="post" action="">
  <?php wp_nonce_field('crwhunter-nonce'); ?>
<h1>Add Ip   To Blacklist</h1>
<input type="text" id="ip_addr" name="ip_addr">
                    <h3>BLACKLISTED</h3>
                    <b>IP addresses that do it more than 5 times in 1 minute are blocked for 4 hours</b>
                        <?php $crw_hnt_opt = get_option( 'crwopt','');

                         
                         ?>

<input type="checkbox" name="crw_ip_box" value="1"<?php checked(  get_option( 'crwopt' ) ); ?> /> <input type="submit" name="crw_opt_status" class="" value="Save"> <br>
<input type="submit" name="crw_add" class="button button-primary" value="Add  Ip">
</form>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php 


              $table = new Ip_Table();
    $table->prepare_items();

        $table->display() ?>
</form><br><br>
<div class="wrap">
<h2><center>Blocked Ip  Logs Monitor </center></h2>

<?php

global $wpdb;
      

        $qry="SELECT 
   *
  
FROM
    wp_crawler_hunter_ip WHERE list_status=1 AND total_access >=5
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


?>



</div>


</div>





    <?php
    if (isset($_POST['crw_opt_status'])) {
  # code...

  $crw_hnt_opt=get_option( 'crwopt' );

   if ($crw_hnt_opt==0) {
     # code...
     
      $crw_hnt_opt=update_option('crwopt','1');
      echo '<meta http-equiv="refresh" content="1">';
   }
   else if ($crw_hnt_opt==1) {
     # code...
    $crw_hnt_opt=update_option('crwopt','0');
    echo '<meta http-equiv="refresh" content="1">';
   }
  

 
 
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

?>