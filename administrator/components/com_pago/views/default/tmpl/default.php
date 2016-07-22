<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items );
?>
<script>

	jQuery( document ).ready(function() {
		jQuery('#select_sale').change(function() 
		{
			var option = jQuery(this).val();
			
			
			if(option == 'customdate')
			{
				jQuery("#sales_date_dropdwon").show();
			}
			else
			{
				jQuery("#sales_date_dropdwon").hide();
			}
		
		});
	});
</script>
<div>
				<div class="no-margin dashboardDrp">
				
					<div style="width:20%; float: right;">
					
						<select name="select_sale" id="select_sale" style="width:100px;">
							<option value="days">7 <?php echo JTEXT::_('PAGO_DASHBOARD_DAYS'); ?></option>
							<option value="months" selected="selected"><?php echo JTEXT::_('PAGO_MONTHLY'); ?></option>
							<option value="year"><?php echo JTEXT::_('PAGO_YEARLY_DRP'); ?></option>
							<option value="customdate"><?php echo JTEXT::_('PAGO_Custom'); ?></option>
						</select>
					</div>
					
					<div id="sales_date_dropdwon" class="sales_dropdwon" style="display:none">	
						<?php $currentDate = date("d-m-Y"); 
						echo JHTML::calendar($currentDate,'sale_start_date','sale_start_date','%Y-%m-%d');
						echo "<span class='to_separator'>TO</span>";
						echo JHTML::calendar($currentDate,'sale_end_date','sale_end_date','%Y-%m-%d'); ?>
						<input type="button" name="apply_sale" value="Apply" id="apply_sale" class="pg-button-search pg-btn-medium pg-btn-light dashboardApply"/>
					</div>
					<div>&nbsp;</div>
					<div id="dateErrMsg" class="totals-averages-title" style="display:none;"><?php echo JTEXT::_('PAGO_START_DATE_SHOULD_BE_LESS_THAN_END_DATE'); ?></div>
				</div>
</div>
<div><br/></div>

<div class="pg-content pg-dashboard">
	<div class = "pg-row pg-mb-20">
		<div class="pg-col-6">
			<div id="pg-dashboard-tabs">
				<?php
					foreach( JModuleHelper::getModules('pago_dashboard_graph') as $module) :
						echo JFactory::getDocument()->loadRenderer('module')->render($module);
					endforeach;
				?>
			</div>
		</div>

		<div class="pg-col-6">
			<?php
				// Please clean up the code when completed.
				foreach( JModuleHelper::getModules('pago_dashboard_right') as $module) :
					echo JFactory::getDocument()->loadRenderer('module')->render($module);
				endforeach;
			?>
		</div>
	</div>

	<?php 
		foreach( JModuleHelper::getModules('pago_dashboard_left_tab') as $module) :
			echo JFactory::getDocument()->loadRenderer('module')->render($module);
		endforeach;
	?>

	<div class = "pg-row">
		<div class = "pg-col-6">
			<?php
				foreach( JModuleHelper::getModules('pago_dashboard_left') as $module) :
					echo JFactory::getDocument()->loadRenderer('module')->render($module);
				endforeach;

				foreach( JModuleHelper::getModules('pago_dashboard_orders') as $module) :
					echo JFactory::getDocument()->loadRenderer('module')->render($module);
				endforeach;
			?>
		</div>
		<div class = "pg-col-6">
			<?php
				foreach( JModuleHelper::getModules('pago_dashboard_recent_comments') as $module) :
					echo JFactory::getDocument()->loadRenderer('module')->render($module);
				endforeach;
			?>
		</div>
	</div>
</div>
<?php
PagoHtml::pago_bottom();
?>