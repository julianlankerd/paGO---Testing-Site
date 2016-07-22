<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery('jqueryui');
PagoHtml::apply_layout_fixes();


JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';
PagoHtml::uniform();
PagoHtml::pago_top($menu_items, 'tabs');
$option = JFactory::getApplication()->input->get('option', '', 'string');
//start
$model = $this->getModel('Migrate','PagoModel');
$pluginEbaled = $model->checkForMigratePlugin();
//end
?>
<script type="text/javascript">
function migrateData(extension)
{
	alert("Please confirm that your pago category, items, users and orders are empty, if any data is there, those will be overright.");
	if(!confirm('Are you sure you want to migrate date?'))
	{
		return false;
	}
	jQuery.ajax({
	type: 'POST',
	url: 'index.php',
	data: ({
		option: "com_pago", // global field
		controller: "migrate",
		task : "migrateData",
		dataType: 'json',
		extension :extension,
	}),
	success: function( response ) {
		var str = response.split("_");
		jQuery( "#messagediv").show();
		jQuery('#category_number_div').html(str[0]);
		jQuery('#items_number_div').html(str[1]);
		jQuery('#users_number_div').html(str[2]);
		jQuery('#orders_number_div').html(str[3]);
		
	}
	});
}
</script>

<div class = "pg-container-header">
	<?php echo JText::_( 'PAGO_MIGRATE_MANAGER' ); ?>
</div>

<div class="pg-table-wrap">
<?php if($pluginEbaled) {?>
	<div class = "pg-white-bckg pg-border pg-pad-20">
		<table class="pg-table pg-table-migrate" id="pg-table-migrate">
			<tr class="pg-table-content">
				<td class="text-center">
					<div class="">
						<img src="<?php echo JURI::base()?>/components/com_pago/css/images/redshop-app.png">
					</div>
					
					<?php if($model->checkForInstalledExtension('redshop')) : ?>
				
					<a href="javascript:void(0);" class="pg-btn pg-btn-large pg-btn-light pg-btn-green" onclick="migrateData('redshop')"><?php echo JText::_('COM_PAGO_REDSHOP')?></a>
					
					<?php else: ?>
					
					<?php echo JText::_('COM_PAGO_INSTALL_ENABLE_REDSHOP_EXTENSION'); ?>
					
					<?php endif; ?>
				</td>
				<td class="text-center">
					<div class="">
						<img src="<?php echo JURI::root(); ?>administrator/components/com_pago/css/images/vm.jpg">
					</div>
					
					<?php if($model->checkForInstalledExtension('vm')) : ?>
					
					<a href="javascript:void(0);" class="pg-btn pg-btn-large pg-btn-light pg-btn-green" onclick="migrateData('vm')"><?php echo JText::_('COM_PAGO_VIRTUEMART')?></a>
					
					<?php else: ?>
					
					<?php echo JText::_('COM_PAGO_INSTALL_ENABLE_VM_EXTENSION'); ?>
					
					<?php endif; ?>
				</td>
				<td class="text-center">
					<div class="">
						<img src="<?php echo JURI::root(); ?>administrator/components/com_pago/css/images/mijoshop.png">
					</div>
					
					<?php if($model->checkForInstalledExtension('mijoshop')) : ?>
					
					<a href="javascript:void(0);" class="pg-btn pg-btn-large pg-btn-light pg-btn-green" onclick="migrateData('mijoshop')"><?php echo JText::_('COM_PAGO_MIJOSHOP')?></a>
					
					<?php else: ?>
					
					<?php echo JText::_('COM_PAGO_INSTALL_ENABLE_MIJOSHOP_EXTENSION'); ?>
					
					<?php endif; ?>
				</td>
				<td class="text-center">
					<div class="">
						<img src="<?php echo JURI::base()?>/components/com_pago/css/images/hikashop-app.png">
					</div>
					
					<?php if($model->checkForInstalledExtension('hikashop')) : ?>
					
					<a href="javascript:void(0);" class="pg-btn pg-btn-large pg-btn-light pg-btn-green" onclick="migrateData('hikashop')"><?php echo JText::_('COM_PAGO_HIKASHOP')?></a>
					
					<?php else: ?>
					
					<?php echo JText::_('COM_PAGO_INSTALL_ENABLE_HIKASHOP_EXTENSION'); ?>
					
					<?php endif; ?>
				</td>
			</tr>
			<tr class="pg-table-content migration-msg" id="messagediv" style="display:none;">
				<td>
					<div>
						<div id="category_migrated_div"><span id="category_number_div"></span>&nbsp;<?php echo JText::_('COM_PAGO_CATEGORIES_MIGRATED');?></div>
						<div id="items_migrated_div"><span id="items_number_div"></span>&nbsp;<?php echo JText::_('COM_PAGO_ITEMS_MIGRATED');?></div>
						<div id="users_migrated_div"><span id="users_number_div"></span>&nbsp;<?php echo JText::_('COM_PAGO_USERS_MIGRATED');?></div>
						<div id="orders_migrated_div"><span id="orders_number_div"></span>&nbsp;<?php echo JText::_('COM_PAGO_ORDERS_MIGRATED');?></div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?php
	}
	else
	{
		echo "<div class='migrateError'>" . JText::_('COM_PAGO_MIGRATION_PLUGIN_NOT_INSTALLED_OR_ENABLED') . "</div>";
	}
	?>
</div>


<!-- end pago content -->
<?php 
echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();