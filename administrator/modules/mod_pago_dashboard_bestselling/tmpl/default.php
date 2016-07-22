<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
echo modPagoBestsellHelper::bestSelling($params);

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
				jQuery('#bestSellerLoader').css('display', 'block');
				jQuery('.pg-dashboard-best-selling-block').css('display', 'none');
			
				var saveData = jQuery.ajax({
					type: 'POST',
					  url: "<?php echo JURI::root();?>administrator/modules/mod_pago_dashboard_bestselling/ajax/ajax.php",
					  data: param,
					   async: true,
					  success: function(result)
					  {
						setTimeout(function() {
							jQuery('#bestSellerLoader').css('display', 'none');
							jQuery('.pg-dashboard-best-selling-block').css('display', 'block');
							jQuery(".bestSelling").html(result);
							jQuery('.pg-dashboard-best-selling-container .best-selling-block').css('height', 'auto');
					
						if (jQuery('.pg-dashboard-charts-container .pago_chart_overview').height() 
							> jQuery('.pg-dashboard-best-selling-container .best-selling-block').height()){
								jQuery('.pg-dashboard-best-selling-container .best-selling-block')
								.css('height', jQuery('.pg-dashboard-charts-container .pago_chart_overview').height());
							}
							else{
								jQuery('.pg-dashboard-charts-container .pago_chart_overview')
								.css('height', jQuery('.pg-dashboard-best-selling-container .best-selling-block').height());	
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
