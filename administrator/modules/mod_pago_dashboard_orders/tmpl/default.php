<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
echo modPagoOrderHelper::latestOrders($params);

?>

<script>

	jQuery( document ).ready(function() {
		var custom = false;
		jQuery('#select_sale').change(function(e,custom) 
		{	
			setTimeout( function() {}, 2000);
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
				jQuery('#dashOrdersLoader').css('display', 'block');
				jQuery('.pg-dashboard-orders-block').css('display', 'none');
				
				
				var saveData = jQuery.ajax({
					type: 'POST',
					  url: "<?php echo JURI::root();?>administrator/modules/mod_pago_dashboard_orders/ajax/ajax.php",
					  data: param,
					   async: true,
					  success: function(result)
					  {
						setTimeout(function() {
							jQuery('#dashOrdersLoader').css('display', 'none');
							jQuery('.pg-dashboard-orders-block').css('display', 'block');
							jQuery("#recentOrders").html(result);
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
