<?php
if (isset($_REQUEST['id']) && $_REQUEST['id']) {
  $_result = $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}sds_slider WHERE id='{$_REQUEST['id']}'");
  $_result = $_result[0];
  $id = $_result->id;
  $cat_id = $_result->cat_id;
  $slider_title = $_result->slider_title;
  $slider_description = $_result->slider_description;
  $url = $_result->url;  
  $slider_img_path = $_result->slider_img_path;
  $status = $_result->status;
}
?>
<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
    <h2>Manage Slider
    <?php if (isset($_GET['type']) && $_GET['type'] == "add") { ?>
      <a href="admin.php?page=add-sds_slider.php" class="add-new-h2">All Sliders List</a>
    <?php } else { ?>
      <a href="admin.php?page=add-sds_slider.php&type=add" class="add-new-h2">Add New Slider</a>
    <?php } ?>
    </h2>	
	<p><b>Note:</b> Use the shortcode <b>[sds_slider cat_id='1']</b> OR  <b>[sds_slider cat_id='3,7,21']</b> in the content area of a page or post where you want the image slider to appear, where <b>cat_id = slider category id</b>.
	</p>
    <div id="message" class="updated below-h2" style="display:none;"><p></p></div>
    
		<?php if (isset($_GET['type']) && $_GET['type'] == "add") {     ?>
    	<div class="form-wrap">	    	
    			<form name="add_slider" enctype="multipart/form-data" class="sds_slider_form" id="add_image" method="post" action="#">
    			<input type="hidden" name="action" value="sds_slider_save_slider" />
    			<?php
			    if (isset($_REQUEST['id']) && $_REQUEST['id']) {
			      echo '<input type="hidden" name="id" value="'. $_REQUEST['id'] .'" />';
			    }
			    ?>
    			<table class="form-table">
      				<tbody>
        		
    			<tr>
    			<th><label for="category">* Category Name</label>
				<p>List of Active categories</p>
				</th>
    			<td><?php $_category_list = $this->sds_get_slider_categories(); ?>
	    			<select name="category" id="category" class="required">
	    			<option value=''>Select Category</option>
	    			<?php 								
					foreach ($_category_list as $_category_list) {
	                  $_is_selected = $_category_list->id == $cat_id ? ' selected="selected"' : '';
	                  echo "<option value=\"{$_category_list->id}\"{$_is_selected}>{$_category_list->cat_title}</option>";
	                }
					?>
					</select>	
				</td>
				</tr>
				
				<tr>
				<th><label for="slider_title">* Title</label></th>    			
    			<td><input type="text" value="<?=$slider_title;?>" id="slider_title" name="slider_title" class="regular-text required" maxlength="50"></td>
    			</tr>
    			
    			<tr>
    			<th><label for="slider_description">&nbsp;Description</label></th>
    			<td><textarea name="slider_description" id="slider_description" cols="50" class="regular-textarea"><?=$slider_description;?></textarea></td>    			
    			</tr>
    			<tr>
    			<th><label for="slider_url">&nbsp;Url / Link</label></th>
   				<td><input type="text" value="<?=$url;?>" id="slider_url" name="slider_url" class="regular-text url_required" maxlength="50">
				<p>http://wwww.example.com<b> or </b>https://wwww.example.com</p>
				</td>
				</tr>
				
				<tr>
        		 <th><label for="slider_image">Upload Image</label></th>
          		<td><div class="sdsfaqsUploader" id="slider_image" name="Upload Large Image" value="<?php echo $slider_img_path; ?>"></div></td>
        		</tr>        
				
				
				</table>
      			</tbody>				
				<p class="submit">
    			<input type="submit" value="Save Slider" name="submit" class="button-primary"/>
    			</p>    			
    			</form>    		
    	</div>    	
    	<style>
  tr.error th, tr.error td {background-color: #FFEBE8!important; border-bottom: solid 1px #CCC!important;}
  tr.error td .required {border-color: #C00!important;}
  tr.error td .url_required {border-color: #C00!important;}
</style>
    	
<script type="text/javascript" >
  jQuery(document).ready(function($) {    
  	jQuery("form[name=add_slider]").submit(function(e){          	
      $('form[name=add_slider] tr.error').removeClass("error");
      var hasError = false;
      $('.required').each(function() {
        if(jQuery.trim($(this).val()) == '') {
          $(this).closest('tr').addClass("error");
          hasError = true;
        }
      });
     
     //$(this).closest('tr').removeClass("error");
      if($('#slider_url').val()!=''){	 
	      	var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	    if(RegExp.test($('#slider_url').val())){}
	    else{		
	    	$('.url_required').closest('tr').addClass("error");    	
	        hasError = true;
	    }
	} 
      if(hasError == false) {    
        var _this = jQuery(this);
        _this.find("input[type=submit]").addClass("button-disabled").attr("disabled", "disabled").removeClass("button-primary");
        jQuery.post(ajaxurl, _this.serialize(), function(data) {
          if(data.error) {
            jQuery("#message").show().addClass("error").removeClass('updated').html("<p>"+data.error+"</p>");
          } else if(data.success) {
            jQuery("#message").removeClass('error')
            jQuery("#message").show().addClass('updated').html("<p>"+data.success+"</p>");
            
              if(data.form_reset) {
              jQuery("form[name=add_slider]")[0].reset();
              jQuery('#slider_image_img_src').css('display', 'none').html('');
              jQuery('input[name=slider_image]').val('');
            }
            
          }
          _this.find("input[type=submit]").addClass("button-primary").removeClass("button-disabled").attr("disabled", false);
        }, 'json');
      } 
      e.preventDefault();
    });
  });
</script>
<?php }else{	

global $wpdb;
$query = "SELECT * FROM {$this->wpdb->prefix}sds_slider";
$getRecords = $wpdb->get_results($query,ARRAY_A);
        $tableFields	=	"*";
		$orderby = 'slider_title';
		$pagination = new Pagination("{$this->wpdb->prefix}sds_slider", $tableFields, '',$query, '', $orderby);
		$pagination -> perPage = 5;
    	$pagination -> pluginUrl = '?page=add-sds_slider.php&amp;';
		$_results = $pagination -> startPaging(@$_GET['wpMailinglistPage']);
?>
 
        <div class="sds_pagingdiv">
		<?php echo $pagination->pagination; ?>
		</div>
 
<div class="wrap">

  <table class="wp-list-table widefat fixed testimonials" cellspacing="0">
    <thead>
      <tr>
        <th>Category Id</th>
        <th>Title</th>
        <th>Description</th>
        <th>Status</th>        
        <th width="100">Thumb Image</th>
      </tr>
    </thead>

    <tbody>
      <?php
      if ($_results) {
          foreach ($_results as $_results) {
        	$status = ($_results['status']) ? 'Active' : 'Deactive';
          $_thumb_img = $_results['slider_img_path'] ? "<a href=\"{$_results['slider_img_path']}\" target=\"_blank\"><img src=\"{$_results['slider_img_path']}\" width=\"100\" height=\"100\" /></a>" : false;
          
          $_row['is_featured'] = $_row['is_featured'] ? "<br /><code style=\"color:#C00;\"> Featured </code>" : "";
          echo "<tr>
                    <td>{$_results['cat_id']}</td>
                    <td>
                      {$_results['slider_title']}
                      <div class=\"row-actions\">
                        <span class=\"edit\"><a href=\"admin.php?page=add-sds_slider.php&type=add&id={$_results['id']}\" title=\"Edit this item\">Edit</a> | </span>
                        <span class=\"trash\"><a class=\"submitdelete\" title=\"Delete this item\" href=\"javascript:void(0)\" rel=\"{$_results['id']}\">Delete</a></span>
                      </div>
                    </td>
                    <td>{$_results['slider_description']}</td>                    
					      <td>
                    <span class=\"status\"><a class=\"submitstatus\" title=\"Active/Deactive Item\" href=\"javascript:void(0)\" rel=\"{$_results['id']},{$_results['status']}\">
                    {$status}
                    </a></span>
                    </td>            
                    <td>{$_thumb_img}</td>
                  </tr>";
        }
      } else {
        echo '<tr><td colspan="4">No Slider Found.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<style>
  tr:nth-child(even) td {background-color: #fff;}
</style>

<script>
  jQuery(document).ready(function(){
    jQuery("table.testimonials tr td span.trash a").click(function(e){
      
      var _this = jQuery(this);
      
      var r = confirm("Are you sure want to delete this item?");
      if(r == true) {
        
        jQuery.post(ajaxurl, {action:"sds_slider_delete_slider", id:jQuery(this).attr('rel')}, function(data){
          
          if(data.error) {
            jQuery("#message").show().addClass("error").removeClass('updated').find('p').html(data.error);
          } else if(data.success) {
            jQuery("#message").removeClass('error')
            jQuery("#message").show().addClass('updated').find('p').html(data.success);
            _this.closest("tr").remove();
          }
        }, 'json')
      }
      e.preventDefault();
    });
	
	    jQuery("table.testimonials tr td span.status a").click(function(e){
        
          var _this = jQuery(this);
        
            jQuery.post(ajaxurl, {action:"sds_slider_status", id_status:jQuery(this).attr('rel')}, function(data){
            
              if(data.error) {
                jQuery("#message").show().addClass("error").removeClass('updated').find('p').html(data.error);
              } else if(data.success) {
                //jQuery("#message").removeClass('error')
                //jQuery(".status").html(data.success);
				location.reload();
              }
            }, 'json')
          
          e.preventDefault();
        });
  });
</script>	
<?php } ?>

<div style="display:block; background-color:#F5F5F5;">
	<p style="margin:7px;">
	<strong>If you love Smooth Dynamic Slider, any donation would be appreciated! It helps to continue the development and support of the plugin.</strong>
	</p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="3L85D7GEHZJ82">
<input type="image" src="http://www.marutiplastorub.com/plugins/images/Donate_thumb.png" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>
</div>