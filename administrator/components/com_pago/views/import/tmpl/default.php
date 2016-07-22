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
//include JPATH_ADMINISTRATOR . '/components/com_pago/helpers/helper.php';

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';
PagoHtml::uniform();
PagoHtml::pago_top($menu_items, '', $this->top_menu);
$option = JFactory::getApplication()->input->get('option', '', 'string');
?>
<script type="text/javascript">

	Joomla.submitbutton = function (pressbutton) {
		submitbutton(pressbutton);
	}

	submitbutton = function (pressbutton) 
	{
		var form = document.adminForm;
		var importVal = jQuery('input[name=import]:checked').val();
		if(typeof(importVal) == "undefined")
		{
			alert("<?php echo JTEXT::_("COM_PAGO_PLEASE_SELECT_FILE"); ?>");
			return false;
		}
		else
		{
			var importVar = "importpgdata"+importVal;
			var finalVal = jQuery("#"+importVar).val();
			var splitVal = finalVal.split('.');
		}
		if (jQuery('input[name=import]:checked').length == 0) 
		{
    		alert("<?php echo JTEXT::_("COM_PAGO_SELECT_RADIO_FOR_IMPORT"); ?>");
		}
		else if(splitVal[1] != 'csv')
		{
			alert("<?php echo JTEXT::_("COM_PAGO_FILE_EXTENSION_WRONG"); ?>");
		}
		else {
		
			submitform(pressbutton);
		}
	}
	
	jQuery(function(){
		
		jQuery("input:radio").on("change", function(){
			
			jQuery("#adminForm").find("input:file").fadeOut(200);
			jQuery(this).parent().next("input:file").fadeIn(200);
			
		});
		
	});
	
</script>

<div class="pg-content">
	<form action="<?php echo 'index.php?option=' . $option; ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php echo PagoHtml::module_top( JText::_( 'PAGO_IMPORT_CLASS' ), null, null, null, null, null, null, false ); ?>
		<div class="pg-white-bckg pg-border pg-pad-20">
			<div class="pg-row">
				<div class="pg-col-3">
					<fieldset class="radio no-margin">
						<input type="radio" name="import" id="import_items" value="items"/>
						<label for="import_items"><?php echo JText::_('COM_PAGO_IMPORT_ITEMS'); ?></label>
					</fieldset>
					<input type="file" name="importpgdataitems" id="importpgdataitems" size="75" style="display: none;"/>
				</div>
				<div class="pg-col-3">
					<fieldset class="no-margin">
						<label for="import_sample_file"><a href="<?php echo JURI::root()?>administrator/components/com_pago/views/import/samplefiles/pago_items.csv"><?php echo JText::_('COM_PAGO_DOWNLOAD_SAMPLE_FILE'); ?></a></label>
					</fieldset>
				</div>
			</div>
			<div class="pg-row">
				<div class="pg-col-3">
					<fieldset class="radio no-margin">
						<input type="radio" name="import" id="import_categories" value="categories"/>
						<label for="import_categories"><?php echo JText::_('COM_PAGO_IMPORT_CATEGORIES'); ?></label>
					</fieldset>
					<input type="file" name="importpgdatacategories" id="importpgdatacategories" size="75" style="display: none;"/>
				</div>
				<div class="pg-col-3">
					<fieldset class="no-margin">
						<label for="import_sample_file"><a href="<?php echo JURI::root()?>administrator/components/com_pago/views/import/samplefiles/pago_categories.csv"><?php echo JText::_('COM_PAGO_DOWNLOAD_SAMPLE_FILE'); ?></a></label>
					</fieldset>
				</div>
			</div>
			<div class="pg-row">
				<div class="pg-col-3">
					<fieldset class="radio no-margin">
						<input type="radio" name="import" id="import_customers" value="customers"/>
						<label for="import_customers"><?php echo JText::_('COM_PAGO_IMPORT_CUSTOMERS'); ?></label>
					</fieldset>
					<input type="file" name="importpgdatacustomers" id="importpgdatacustomers" size="75" style="display: none;"/>
				</div>
				<div class="pg-col-3">
					<fieldset class="no-margin">
						<label for="import_sample_file"><a href="<?php echo JURI::root()?>administrator/components/com_pago/views/import/samplefiles/pago_customers.csv"><?php echo JText::_('COM_PAGO_DOWNLOAD_SAMPLE_FILE'); ?></a></label>
					</fieldset>
				</div>
			</div>
		</div>
		<?php echo PagoHtml::module_bottom(); ?>

		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="import" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>


<!-- end pago content -->
<?php 
echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();