<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
    <h2>Manage Category
    <?php if (isset($_GET['type']) && $_GET['type'] == "add") { ?>
      <a href="admin.php?page=add-slidercategory" class="add-new-h2">All Categories List</a>
    <?php } else { ?>
      <a href="admin.php?page=add-slidercategory&type=add" class="add-new-h2">Add New</a>
    <?php } ?>
    </h2>  
			<div id="message" class="updated below-h2" style="display:none;"><p></p></div>
			
			<?php if (isset($_GET['type']) && $_GET['type'] == "add") { 
				if (isset($_GET['id']) && $_GET['id'] && is_numeric($_GET['id'])) {
				      $_current_cat = $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}sds_slider_cat WHERE id='{$_GET['id']}'");
				      $_current_cat = $_current_cat[0];
				      $id = $_current_cat->id;
				      $title = $_current_cat->cat_title;
				      $cat_slug = $_current_cat->cat_slug;
				      $cat_description = $_current_cat->cat_description;
				      $cat_status = $_current_cat->status;      
				    }
				?>
    		<div class="form-wrap">	    	
    			<form name="add_slider_cat" enctype="multipart/form-data" method="post" action="#">
    			<input type="hidden" name="action" value="sds_slider_save_cat" />
		      <?php 
		      if ($id) {
		        echo "<input type=\"hidden\" name=\"id\" value=\"{$id}\" />";
		      }
		      ?>
    			<table class="form-table">
      				<tbody>        		
    			<tr>
    				<th><label for="cat_title">* Category Title</label></th>
    				<td><input type="text" value="<?=$title?>" class="regular-text required" id="cat_title" name="cat_title" maxlength="50"></td>
    			</tr>
    			<tr>
    				<th><label for="cat_description">Category Description</label></th>
    				<td><textarea name="cat_description" id="cat_description" cols="50" ><?=$cat_description?></textarea></td>
    			</tr>
    			
    			</tbody>
    			</table>
    			<p class="submit"><input type="submit" value="Save Category" name="action" class="button button-primary" /></p>
    			</form>
    		</div>
    			<style>
  tr.error th, tr.error td {background-color: #FFEBE8!important; border-bottom: solid 1px #CCC!important;}
  tr.error td .required {border-color: #C00!important;}
</style>

<script type="text/javascript" >
  jQuery(document).ready(function($) {    
  	  	
  	jQuery("form[name=add_slider_cat]").submit(function(e){          	
      $('form[name=add_slider_cat] tr.error').removeClass("error");
      var hasError = false;
      $('.required').each(function() {
        if(jQuery.trim($(this).val()) == '') {
          $(this).closest('tr').addClass("error");
          hasError = true;
        }
      });
      
      if(hasError == false) {        
        var _this = jQuery(this);
        _this.find("input[type=submit]").addClass("button-disabled").attr("disabled", "disabled").removeClass("button-primary");
        jQuery.post(ajaxurl, _this.serialize(), function(data) {
          if(data.error) {
            jQuery("#message").show().addClass("error").removeClass('updated').html("<p>"+data.error+"</p>");
          } else if(data.success) {
            jQuery("#message").removeClass('error')
            jQuery("#message").show().addClass('updated').html("<p>"+data.success+"</p>");
            
          }
          _this.find("input[type=submit]").addClass("button-primary").removeClass("button-disabled").attr("disabled", false);
        }, 'json');        
      } 
      e.preventDefault();
    });
  });
</script>
    		<?php } else { ?>	
    		 <table class="wp-list-table widefat fixed testimonials" cellspacing="0">
      <thead>
        <tr>
          <th>Title</th>
          <th width="150">Description</th>
          <th width="150">Status</th>          
        </tr>
      </thead>

      <tbody>
        <?php
        global $wpdb;
        $query = "SELECT * FROM {$this->wpdb->prefix}sds_slider_cat";
        $getRecords = $wpdb->get_results($query,ARRAY_A);
        $tableFields	=	"*";
		$orderby = 'cat_title';
		$pagination = new Pagination("{$this->wpdb->prefix}sds_slider_cat", $tableFields, '',$query, '', $orderby);
		$pagination -> perPage = 5;
    	$pagination -> pluginUrl = '?page=add-slidercategory&amp;';
		$_results = $pagination -> startPaging(@$_GET['wpMailinglistPage']);	        
		
		
        //$_results = $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}sds_slider_cat LIMIT 5");
        ?>
        <div class="sds_pagingdiv">
		<?php echo $pagination->pagination; ?>
		</div>
        <?php
        if ($_results) {
          foreach ($_results as $_results) {          	
          	print_r($_results->cat_title);
          	
            $status = ($_results['status']) ? 'Active' : 'Deactive';
            echo "<tr>
                    <td>{$_results['cat_title']}
                      <div class=\"row-actions\">
                        <span class=\"edit\"><a href=\"admin.php?page=add-slidercategory&type=add&id={$_results['id']}\" title=\"Edit this item\">Edit</a> | </span>
                        <span class=\"trash\"><a class=\"submitdelete\" title=\"Delete this item\" href=\"javascript:void(0)\" rel=\"{$_results['id']}\">Delete</a></span>
                      </div>
                    </td>
                    <td>{$_results['cat_description']}</td>                    
                    <td>
                    <span class=\"status\"><a class=\"submitstatus\" title=\"Active/Deactive Item\" href=\"javascript:void(0)\" rel=\"{$_results['id']},{$_results['status']}\">
                    {$status}
                    </a></span>
                    </td>                    
                  </tr>";
          }
        } else {
          echo "<tr><td colspan=\"3\">No Widget Template Found.</td></tr>";
        }
        ?>
      </tbody>
    </table>    	
    <style>
      tr:nth-child(even) td {background-color: #fff;}
    </style>

    <script>
      jQuery(document).ready(function(){
        jQuery("table.testimonials tr td span.trash a").click(function(e){
        
          var _this = jQuery(this);
        
          var r = confirm("Are you sure want to delete this template?");
          if(r == true) {
          
            jQuery.post(ajaxurl, {action:"sds_slider_delete_cat", id:jQuery(this).attr('rel')}, function(data){
            
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
        
            jQuery.post(ajaxurl, {action:"sds_slider_cat_status", id_status:jQuery(this).attr('rel')}, function(data){
            
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
</div>

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