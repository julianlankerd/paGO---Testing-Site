<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<script>

	jQuery( document ).ready(function() {
		var custom = false;
		jQuery('#select_sale').change(function(e,custom) 
		{
			
			setTimeout( function() {}, 2000);
			var option = jQuery(this).val();
			var process = false;
			
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
				jQuery('#totalAvgLoader').css('display', 'block');
				jQuery('.pg-totals-averages-info').css('display', 'none');
				jQuery('#totalRecentSelloader').css('display', 'block');
				jQuery('#total-recent-sales-div').css('display', 'none');
				jQuery('#newCustomerloader').css('display', 'block');
				jQuery('#new_customer-count-div').css('display', 'none');
				jQuery('#abandonedCartloader').css('display', 'block');
				jQuery('#abandoned-cart-avg-div').css('display', 'none');
				
				var saveData = jQuery.ajax({
					type: 'POST',
					  url: "<?php echo JURI::root();?>administrator/modules/mod_pago_dashboard_totals_averages/ajax/ajax.php",
					  data: param,
					   async: true,
					  success: function(result)
					  {
						var resultArray = result.split(':');
						
						setTimeout( function() {
							jQuery('#totalAvgLoader').css('display', 'none');
							jQuery('.pg-totals-averages-info').css('display', 'block');
							jQuery('#totalRecentSelloader').css('display', 'none');
							jQuery('#total-recent-sales-div').css('display', 'block');
							jQuery('#newCustomerloader').css('display', 'none');
							jQuery('#new_customer-count-div').css('display', 'block');
							jQuery('#abandonedCartloader').css('display', 'none');
							jQuery('#abandoned-cart-avg-div').css('display', 'block');
							jQuery("#total-recent-sales").html(resultArray[0]);
							jQuery("#total-recent-sales-lbl").html(resultArray[1]);
							jQuery("#total-avg-sales").html(resultArray[2]);
							jQuery("#total-avg-sales-lbl").html(resultArray[1]);
							jQuery("#new_customer-count").html(resultArray[3]);
							jQuery("#new_customer-count-lbl").html(resultArray[1]);
							jQuery("#abandoned-cart-avg").html(resultArray[4]);
							jQuery("#abandoned-cart_avg-lbl").html(resultArray[1]);
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
<?php

echo modPagoTotalAvgHelper::average("months", $startDate='', $endDate='');

?>

