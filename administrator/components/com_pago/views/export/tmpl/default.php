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
		
		if (jQuery('input[name=export]:checked').length == 0) 
		{
    		alert("<?php echo JTEXT::_("PAGO_PLEASE_SELECT_SECTION"); ?>");
		}
		else {
		
			submitform(pressbutton);
		}
	}
</script>
<form action="<?php echo 'index.php?option=' . $option; ?>" method="post" name="adminForm" id="adminForm">
	<div class="pg-content pg-tab-content">
		<?php echo PagoHtml::module_top( JText::_( 'PAGO_EXPORT_CLASS' ), null, null, null, null, null, null, false ); ?>
		<div class="pg-border pg-white-bckg pg-pad-20">
			<div class="pg-row">
				<div class="pg-col-3">
					<div class="pg-col-12">
						<fieldset class="radio no-margin">
							<input type="radio" name="export" value="items" id="export_items"/>
							<label onclick="return product_cat_tree('items')" for="export_items"><?php echo JText::_('COM_PAGO_EXPORT_ITEMS'); ?></label>
						</fieldset>
					</div>	
					<div class="pg-col-12">
						<fieldset class="radio no-margin">
							<input type="radio" name="export" value="categories" id="export_categories"/>
							<label onclick="return product_cat_tree('categories')" for="export_categories"><?php echo JText::_('COM_PAGO_EXPORT_CATEGORIES'); ?></label>
						</fieldset>
					</div>
					<div class="pg-col-12">
						<fieldset class="radio no-margin">
							<input type="radio" name="export" value="customers" id="export_customers"/>
							<label onclick="return product_cat_tree('customers')" for="export_customers"><?php echo JText::_('COM_PAGO_EXPORT_CUSTOMERS'); ?></label>
						</fieldset>
					</div>
				</div>
				<div class="pg-col-6">
					<div id="prd_cat_tree" style="display: none;">
						<?php echo JText::_('COM_PAGO_PRODUCT_CATEGORY'); ?>
						<?php PagoHelper::get_category_tree(); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo PagoHtml::module_bottom(false); ?>
	</div>
	<input type="hidden" name="option" value="com_pago" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="export" />
	<?php echo JHTML::_('form.token'); ?>
</form>

<script type=" text/javascript">
	function product_cat_tree(val) {
		if (val == "items") {
			document.getElementById('prd_cat_tree').style.display = "";
		} else {
			document.getElementById('prd_cat_tree').style.display = "none";
		}
	}
</script>

<!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();