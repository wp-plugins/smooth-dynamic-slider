<?php
/*
  Plugin Name: Smooth Dynamic Slider  
  Description: Category wise smooth dynamic slider.
  Author: Kundan Yevale
  Version: 1.0
  Author URI: http://profiles.wordpress.org/kundanyevale
 */

// DEFINE CONSTANTS
if( !defined('SDSSLIDER_VER') ) define( 'SDSSLIDER_VER', '1.0' );

class sds_Slider {

  var $wpdb;
  var $whi;

public function __construct() {
	
	global $wpdb;
	$this->wpdb = $wpdb;
	$this->ds = DIRECTORY_SEPARATOR;
	$this->pluginPath = dirname(__FILE__) . $this->ds;
	$this->rootPath = dirname(dirname(dirname(dirname(__FILE__))));
	$this->pluginUrl = WP_PLUGIN_URL . '/sdsslider/';   
	
	add_action('admin_menu', array($this, 'sds_slider_plugin_admin_menu'));	
	add_shortcode('sds_slider', array($this, 'shortcode'));
	add_action('wp_ajax_sds_slider_save_cat', array($this, 'sds_slider_save_cat'));
	add_action('wp_ajax_sds_slider_delete_cat', array($this, 'sds_slider_delete_cat'));
	add_action('wp_ajax_sds_slider_cat_status', array($this, 'sds_slider_cat_status'));
	add_action('wp_ajax_sds_slider_status', array($this, 'sds_slider_status'));
	add_action('wp_ajax_sds_slider_save_slider', array($this, 'sds_slider_save_slider'));
	add_action('wp_ajax_sds_slider_delete_slider', array($this, 'sds_slider_delete_slider'));
	add_action('wp_ajax_sds_slider_update_settings', array($this, 'sds_slider_update_settings'));	
	
	add_action( 'wp_print_styles', array($this, 'sds_enqueue_styles') );
	add_action( 'wp_print_scripts', array($this, 'sds_enqueue_scripts') );
	
	if (isset($_GET['page']) && $_GET['page'] == 'add-sds_slider.php') {
      add_action('admin_print_scripts', array($this, 'wp_gear_manager_admin_scripts'));
      add_action('admin_print_styles', array($this, 'wp_gear_manager_admin_styles'));
      add_action('admin_head', array($this, 'include_js'));
    }
    require_once 'class.pagination.php';
}
  
public function sds_slider_plugin_admin_menu()
{
	add_menu_page('add-sds_slider.php','SDS Slider','publish_posts','add-sds_slider.php',array($this, 'sds_addsdsslider_dashbord'));
	add_submenu_page('add-sds_slider.php','Manage Slider','Manage Slider','publish_posts','add-sds_slider.php',array($this, 'sds_addsdsslider_dashbord'));
	add_submenu_page('add-sds_slider.php','Manage Category','Manage Category','publish_posts','add-slidercategory',array($this, 'sds_addslidercategory_dashbord'));
	add_submenu_page('add-sds_slider.php','Add Setting','Settings','publish_posts','slider-setting',array($this, 'sds_slider_setting'));	
}

public function shortcode($atts) { 	
	ob_start();
	global $wpdb;
	global $post;	
	$whi = get_option('sds_slider_whi');
	$whi = unserialize($whi);
	$w = $whi['width'];
	$h = $whi['height'];
	$interval = $whi['interval'];
	$sw = $w - 45;
	$cat_id = $atts['cat_id'];		
	$result = $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}sds_slider WHERE cat_id IN (".$cat_id.") AND slider_img_path != '' AND status='1'");	
	?>		
	<script type="text/javascript" language="javscript">
		jQuery(document).ready(function() {
    	jQuery('.slider<?=$post->ID;?>Image').find('img').css('width', <?php echo $w; ?>);    	
    	jQuery('.slider<?=$post->ID;?>Image').find('img').css('height', <?php echo $h; ?>);    	
    	jQuery('.slider<?=$post->ID;?>Image').css({"float":"left","position":"relative","display":"none", "list-style-type":"none", "padding":"0px", "margin":"0px"});
    	jQuery('.slider<?=$post->ID;?>Image').find('span').find('strong').css('font-size','14px');
    	jQuery('.slider<?=$post->ID;?>Image').find('span').css({"position":"absolute","font":"10px/15px Arial, Helvetica, sans-serif","padding":"10px 13px","width":"<?=$sw?>px","background-color":"#000","filter":"alpha(opacity=0.7)","-moz-opacity":"0.7","-khtml-opacity":"0.7","opacity":"0.7","color":"#fff","display":"none"});    	
    	
    	jQuery('#slider<?=$post->ID;?>').css({"width":"<?=$w?>px","height":"<?=$h?>px","position":"relative","overflow":"hidden"}); 
    	jQuery('#slider<?=$post->ID;?>Content').css({"width":"<?=$w?>px","position":"absolute","top":"0","margin-left":"0"});
    	
    	
        jQuery('#slider<?=$post->ID;?>').s3Slider({
            timeOut: <?php echo $interval; ?>
        });
    });
</script>
<?php $sdsurl = plugins_url().'/sdsslider/'; ?>
<?php if($result) { ?>

<div id="slider<?=$post->ID;?>">
        <ul id="slider<?=$post->ID;?>Content">
<?php 
		foreach ($result as $result): 
		$img = $result->slider_img_path;
		$imgurl = $getdirpath.$img;
?>
            <li class="slider<?=$post->ID;?>Image">
                <?php if($result->url!=''): ?>
				<a href="<?=$result->url?>" target="_blank">
					<img src="<?=$imgurl;?>" title="<?=$result->slider_title;?>" alt="1" />
				</a>	
				<?php else: ?>
					<img src="<?=$imgurl;?>" title="<?=$result->slider_title;?>" alt="1" />
				<?php endif; ?>								
                <span class="top"><strong><?=$result->slider_title;?></strong><br /><?=$result->slider_description;?></span>
            </li>            
<?php endforeach; ?>			
            <span class="clear slider<?=$post->ID;?>Image"></span>
        </ul>
</div>
<?php	
	
	}
	$myvariable = ob_get_clean();
	return $myvariable; 		
}

public function sds_slider_setting()
{
	require($this->pluginPath . "sds_slider_setting.php");	
}

public function sds_addsdsslider_dashbord()
{
	require($this->pluginPath . "add-sds_slider.php");	
}

public function sds_addslidercategory_dashbord()
{
	require($this->pluginPath . "add-sds_slider_cat.php");	
}

//update slider settings
public function sds_slider_update_settings(){	
	if ($_POST['action'] == "sds_slider_update_settings") {
		$whi = array('width'=>$_REQUEST['slider_width'], 'height'=>$_REQUEST['slider_height'],'interval'=>$_REQUEST['slider_interval']);		
  		$whi = serialize($whi);  		
  		update_option('sds_slider_whi',$whi);
  		
  		if (mysql_error()) {
        $data['error'] = mysql_error();
      } else {
        $data['success'] = "The slider has been updated successfully.";
      }
      	echo json_encode($data);
	}
		die();
}

//get slider categories list
public function sds_get_slider_categories(){	
	$result = $this->wpdb->get_results("SELECT id, cat_title FROM {$this->wpdb->prefix}sds_slider_cat WHERE status='1'");
	return $result;	
}
//save categories
public function sds_slider_save_cat() {
    if ($_POST['action'] == "sds_slider_save_cat") {

      if ($_POST['cat_title']) {

        if (isset($_POST['id']) && $_POST['id']) {
          $this->wpdb->query("UPDATE {$this->wpdb->prefix}sds_slider_cat SET cat_title='{$_POST['cat_title']}', cat_description='{$_POST['cat_description']}' WHERE id='{$_POST['id']}'");
          $_success_msg = "The slider category has been updated successfully.";
        } else {
          $this->wpdb->query("INSERT INTO {$this->wpdb->prefix}sds_slider_cat (cat_title, cat_description, status) VALUES('{$_POST['cat_title']}', '{$_POST['cat_description']}', '1')");
          $_success_msg = "The slider category has been added successfully.";
        }

        if (mysql_error()) {
          $data['error'] = mysql_error();
        } else {
          $data['success'] = $_success_msg;
        }
      } else {
        $data['error'] = "Widget title must be required.";
      }
      echo json_encode($data);
      die();
      
    }
  }
  
//delete slider category
public function sds_slider_delete_cat() {
    if (isset($_POST['id']) && $_POST['id']) {

      $this->wpdb->query("DELETE FROM {$this->wpdb->prefix}sds_slider_cat WHERE id='{$_POST['id']}'");
      if (mysql_error()) {
        $data['error'] = mysql_error();
      } else {
        $data['success'] = "The slider category has been delete successfully.";
      }
      echo json_encode($data);
    }
    die();
  }
  
//change category status
public function sds_slider_cat_status() {
     if (isset($_POST['id_status']) && $_POST['id_status']) {
     	$id_status = array();
     	$id_status = explode(',',$_POST['id_status']);
     	$id = $id_status[0];
     	$status = $id_status[1];
     	if($status==1){
     	$status = 0;
     	$statustext = 'Deactive';
     	}
     	else{
     	$status = 1;
     	$statustext = 'Active';
     	}
     	
     	$array  =   array("status"=> "$status");
		$where  =   array("id"=>$id);
		$update =   $this->wpdb->update("{$this->wpdb->prefix}sds_slider_cat",$array,$where);
		
		$array2  =   array("status"=> "$status");
		$where  =   array("cat_id"=>$id);
		$update =   $this->wpdb->update("{$this->wpdb->prefix}sds_slider",$array2,$where);
     }
	
      if (mysql_error()) {
        $data['error'] = mysql_error();
      } else {
        $data['success'] = "success";
      }
      echo json_encode($data);
    
    die();
  }  
  
//change slider status
public function sds_slider_status() {
     if (isset($_POST['id_status']) && $_POST['id_status']) {
     	$id_status = array();
     	$id_status = explode(',',$_POST['id_status']);
     	$id = $id_status[0];
     	$status = $id_status[1];
     	if($status==1){
     	$status = 0;
     	$statustext = 'Deactive';
     	}
     	else{
     	$status = 1;
     	$statustext = 'Active';
     	}     	
     	$array  =   array("status"=> "$status");
		$where  =   array("id"=>$id);
		$update =   $this->wpdb->update("{$this->wpdb->prefix}sds_slider",$array,$where);

     }
	
      if (mysql_error()) {
        $data['error'] = mysql_error();
      } else {
        $data['success'] = "success";
      }
      echo json_encode($data);
    
    die();
  }   

//save slider
public function sds_slider_save_slider(){	
	$_search = array("http://", "https://");
    $_replace = array("", "");
	
	if ($_POST['action'] == "sds_slider_save_slider") {		
	global $wpdb;		
	$_data = array(
	 	"cat_id" => $_POST['category'],
        "slider_title" => stripcslashes($_POST['slider_title']),
        "slider_description" => stripcslashes($_POST['slider_description']),
        "url" => $_POST['slider_url'],        
        "slider_img_path" => str_replace($_SERVER['HTTP_HOST'], "", str_replace($_search, $_replace, $_POST['slider_image'])),
        "status" => 1        
	);
	if (isset($_POST['id']) && $_POST['id']) {
      $this->wpdb->update("{$this->wpdb->prefix}sds_slider", $_data, array("id" => $_POST['id']));
    } else {
      $this->wpdb->insert("{$this->wpdb->prefix}sds_slider", $_data);
    }
	
	  if (mysql_error()) {
      $data['error'] = mysql_error();
    } else {
      if($this->wpdb->insert_id) {      	
        $data['form_reset'] = true;
      }      	
      $data['success'] = "The slider has been saved successfully..";
    }
	echo json_encode($data);
    die();
      
	}

}
//delete slider
public function sds_slider_delete_slider() {
    if (isset($_POST['id']) && $_POST['id']) {

      $this->wpdb->query("DELETE FROM {$this->wpdb->prefix}sds_slider WHERE id='{$_POST['id']}'");
      if (mysql_error()) {
        $data['error'] = mysql_error();
      } else {
        $data['success'] = "The slider has been delete successfully.";
      }
      echo json_encode($data);
      die();
    }
    
  }

function include_js() {
    echo "<script type='text/javascript'>
      jQuery(document).ready(function(){
        jQuery('div.sdsfaqsUploader').each(function(){
          var uploader_id = jQuery(this).attr('id');          
          if(uploader_id != undefined) {
          
              var uploader_btn_name = jQuery(this).attr('name') ? jQuery(this).attr('name') : 'Upload Image';
              var uploader_old_file = jQuery(this).attr('value') ? jQuery(this).attr('value') : '';
              
              
              jQuery(this).html('<div id=\"'+uploader_id+'_img_src\" style=\"width:100px; height:100px; display:none; vertical-align:middle; border: 2px solid #BBB; border-radius:10px;\"></div><input type=\"hidden\" name=\"'+uploader_id+'\" id=\"'+uploader_id+'_field\" value=\"'+uploader_old_file+'\" /><input id=\"'+uploader_id+'_btn\" type=\"button\" value=\"'+uploader_btn_name+'\" class=\"button-secondary\" />');
            
              jQuery('#'+uploader_id+'_btn').click(function() {
                formfield = uploader_id;
                tb_show('', 'media-upload.php?type=image&TB_iframe=true');
                
                window.send_to_editor = function(html) {
                  imgurl = jQuery('img',html).attr('src');
                  jQuery('#'+uploader_id+'_field').val(imgurl);
                  jQuery('#'+uploader_id+'_field').trigger('change');
                  tb_remove();
                }                
                return false;
              });
              
              jQuery('#'+uploader_id+'_field').change(function(){
                  var _current_img = jQuery('#'+uploader_id+'_field').val();
                  if(_current_img.length > 0) {
                    jQuery('#'+uploader_id+'_img_src').css('display', 'block');
                    jQuery('#'+uploader_id+'_img_src').html('<img src=\"'+_current_img+'\" style=\"width:98%; height:98%; cursor:pointer; border: 1px solid #FFF; border-radius: 7px;\" title=\"Click to Remove\" />');
                  } else {
                    jQuery('#'+uploader_id+'_img_src').css('display', 'none');
                    jQuery('#'+uploader_id+'_img_src').html('');
                  }
                });
                
                jQuery('#'+uploader_id+'_img_src img').live('click', function(){
                  jQuery('#'+uploader_id+'_field').val('');
                  jQuery('#'+uploader_id+'_field').trigger('change');
                });
              
              jQuery('#'+uploader_id+'_field').trigger('change');

            }            
            });
      });
      </script>";
  }
  
  function wp_gear_manager_admin_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('jquery');
  }
  function wp_gear_manager_admin_styles() {
    wp_enqueue_style('thickbox');
  }
  
  function sds_enqueue_styles(){
			// Loads our styles, only on the front end of the site
			if( !is_admin() ){
				wp_enqueue_style( 'sdsslider_main', plugins_url('/css/frontslider.css', __FILE__) );
			}
		}
		
 	function sds_enqueue_scripts(){
			// Loads our scripts, only on the front end of the site
			if( !is_admin() ){				
				// Load javascript
				$load_js_in_footer = '';
				wp_enqueue_script( 'sdsslider_main', plugins_url('/js/s3Slider.js', __FILE__), array('jquery'), FALSE, $load_js_in_footer );
				// Localize plugin options
				$data = array('version' => SDSSLIDER_VER);
				wp_localize_script('sdsslider_main', 'sds_slider_options', $data);
			}
		}		

}

add_action("init", "register_sds_slider_plugin");
function register_sds_slider_plugin() {
  global $sds_Slider;
  $sds_Slider = new sds_Slider();
}

register_activation_hook(__FILE__, 'sdsSliderInstall');

function sdsSliderInstall() {
	global $wpdb;  
  	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  	
  	dbDelta("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sds_slider_cat (id int(11) NOT NULL AUTO_INCREMENT,
			  cat_title varchar(50) NOT NULL,
			  cat_slug varchar(100) NOT NULL,  
			  cat_description varchar(500) NOT NULL,
			  status char(1) NOT NULL,  
			  PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");	
  
  	dbDelta("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sds_slider (id int(11) NOT NULL AUTO_INCREMENT,
			  cat_id int(11) NOT NULL,
			  slider_title varchar(50) NOT NULL,
			  slider_description varchar(500) NOT NULL,
			  url varchar(255) NOT NULL,
			  slider_img_path varchar(255) NOT NULL,
			  status char(1) NOT NULL,
			  PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");  
  	
  	$whi = array('width'=>'400', 'height'=>'320', 'interval'=>'1800'); //add slider default height width 
  	$whi = serialize($whi);  
  	add_option( 'sds_slider_whi', $whi, '', 'yes' ); 
  	
}
?>