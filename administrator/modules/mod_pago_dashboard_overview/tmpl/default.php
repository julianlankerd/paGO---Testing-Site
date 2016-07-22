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
				jQuery('#chartOverviewLoader').css('display', 'block');
				jQuery('.pago_chart_overview').css('display', 'none');
				var saveData = jQuery.ajax({
					type: 'POST',
					  url: "<?php echo JURI::root();?>administrator/modules/mod_pago_dashboard_overview/ajax/ajax.php",
					  data: param,
					  success: function(result)
					  {
						jQuery.getScript( "https://www.google.com/jsapi" );
						var chart_arr = eval(result);
						setTimeout( function() {
							jQuery('#chartOverviewLoader').css('display', 'none');
							jQuery('.pago_chart_overview').css('display', 'block');
							pago_chart_overview(chart_arr);
						}, 2000);
					  }
					});
			}
		});

		function pago_chart_overview(ajaxData)
	  	{
  			try
			{
				var jData = jQuery.parseJSON(ajaxData);
			}
			catch (e) 
			{
				 console.error("Parsing error:", e); 
			}
			var data = google.visualization.arrayToDataTable(ajaxData);
			var options = {width: '100%',height: 400,title: 'Sales Chart'};
			var chart = new google.visualization.AreaChart(document.getElementById('pago_chart_overview'));
			chart.draw(data, options);
		}
		
		jQuery( "#apply_sale" ).click(function(e) {
 jQuery( "#select_sale" ).trigger( "change", true);
});
		
	});
		</script>
<?php
echo modPagoOverviewHelper::GetOverviewChart($params);

?>



