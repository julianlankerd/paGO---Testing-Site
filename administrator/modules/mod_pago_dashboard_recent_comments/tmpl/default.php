<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
echo modPagoRecentCommentsHelper::latestComments($params);

?>

<script>

	jQuery( document ).ready(function() {
		var custom = false;
		jQuery('#select_sale').change(function(e,custom) 
		{
			
			setTimeout( function() {}, 2000);
			var process = false;
			var option = jQuery(this).val();
			
			if(option == 'customdate' && custom == true)
			{
				var start_date = jQuery("#sale_start_date").val();
				var end_date = jQuery("#sale_end_date").val();
				var param = 'selected_sale=' + option + '&sale_start_date=' + start_date + '&sale_end_date=' + end_date;
				
				if(start_date > end_date)
				{
					process = false;
					jQuery("#dateErrMsg").css("display","block");
					jQuery("#dateErrMsg").css("color","red");
				}
				else
				{
					process = true;
					jQuery("#dateErrMsg").css("display","none");
				}
				
			}
			else if(option == 'customdate')
			{
				process = false;
			}
			else
			{
				var param = 'selected_sale=' + option;
				process = true;
			}
			
			
			if(process == true)
			{
				jQuery('#commentLoader').css('display', 'block');
				jQuery('.pg-dashboard-comment-block').css('display', 'none');
				
				var saveData = jQuery.ajax({
					type: 'POST',
					  url: "<?php echo JURI::root();?>administrator/modules/mod_pago_dashboard_recent_comments/ajax/ajax.php",
					  data: param,
					   async: true,
					  success: function(result)
					  {
						setTimeout(function() {
							jQuery('#commentLoader').css('display', 'none');
							jQuery('.pg-dashboard-comment-block').css('display', 'block');
							jQuery(".pg-dashboard-comment-block").html(result);
							jQuery('.pg-dashboard-comments-container .pg-dashboard-comment-block').css('height', 'auto');
								if (jQuery('.pg-dashboard-orders-container .pg-dashboard-orders-block').height() 
								> jQuery('.pg-dashboard-comments-container .pg-dashboard-comment-block').height()){
									jQuery('.pg-dashboard-comments-container .pg-dashboard-comment-block')
									.css('height', jQuery('.pg-dashboard-orders-container .pg-dashboard-orders-block').height());
								}
								else{
									jQuery('.pg-dashboard-orders-container .pg-dashboard-orders-block')
									.css('height', jQuery('.pg-dashboard-comments-container .pg-dashboard-comment-block').height());	
								}
						}, 2000);
					 }
					});
				}
		});
		
		jQuery( "#apply_sale" ).click(function(e) {
 jQuery( "#select_sale" ).trigger( "change", true);
});

	});
		</script>
