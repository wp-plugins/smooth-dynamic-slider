<?php
	$whi = get_option('sds_slider_whi');	
	$whi = unserialize($whi);
	$w = $whi['width'];
	$h = $whi['height'];
	$inteval = $whi['interval'];

?>
<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
    <h2>Slider Settings</h2>
    <div id="message" class="updated below-h2" style="display:none;"><p></p></div>
    
    	<div class="form-wrap">
<form name="slider_settings" enctype="multipart/form-data" method="post" action="#">
<input type="hidden" name="action" value="sds_slider_update_settings" />
<table class="form-table">
      				<tbody>
        		
    				<tr>
    					<th><label for="slider_height">* Slider Height</label></th>
    					<td><input type="text" value="<?=$h;?>" id="slider_height" name="slider_height" class="regular-text required" maxlength="50"></td>    					
    				</tr>
    				
    				<tr>
    					<th><label for="slider_width">* Slider Width</label></th>
    					<td><input type="text" value="<?=$w;?>" id="slider_width" name="slider_width" class="regular-text required" maxlength="50"></td>    					
    				</tr>
    				
    				<tr>
    					<th><label for="slider_interval">* Slider Inteval</label></th>
    					<td><input type="text" value="<?=$inteval;?>" id="slider_interval" name="slider_interval" class="regular-text required" maxlength="50"></td>    					
    				</tr>
    				
    				</tbody>
</table>    		
				<p class="submit">
    			<input type="submit" value="Save Slider Settings" name="submit" onclick="return sds_setting_validation()" class="button-primary"/>
    			</p>    					
</form>				
	</div>
</div>

    	<style>
  tr.error th, tr.error td {background-color: #FFEBE8!important; border-bottom: solid 1px #CCC!important;}
  tr.error td .required {border-color: #C00!important;}
  tr.error td .url_required {border-color: #C00!important;}
</style>
    	
<script type="text/javascript" >
  jQuery(document).ready(function($) {    
  	jQuery("form[name=slider_settings]").submit(function(e){          	
      $('form[name=slider_settings] tr.error').removeClass("error");
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
  
  function sds_setting_validation(){
  	var $ = jQuery;
  		if (isNaN($('#slider_height').val() / 1) == true) {
		alert('Just enter height numeric number');
		return false;
	}
	
	if (isNaN($('#slider_width').val() / 1) == true) {
		alert('Just enter width numeric number');
		return false;
	}
	if (isNaN($('#slider_interval').val() / 1) == true) {
		alert('Just enter slider interval numeric number');
		return false;
	}
	
	if($('#slider_height').val()==''){
	alert('please enter slider interval');
	return false;
	}
	
	if($('#slider_width').val()==''){
	alert('please enter width size');
	return false;
	}
	if($('#slider_interval').val()==''){
	alert('please enter slider interval');
	return false;
	}
  }
</script>      

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