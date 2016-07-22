<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery();
PagoHtml::behaviour_jqueryui();
PagoHtml::apply_layout_fixes();
PagoHtml::tooltip();
PagoHtml::uniform();
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';

PagoHtml::pago_top($menu_items, 'tabs', $this->top_menu);
$doc = JFactory::getDocument();
$doc->addScriptDeclaration("
	jQuery(document).ready(function(){
		jQuery('#params_price-lbl').append(' ($this->defaultCurrency)');
	})
	");
?>
<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	jQuery.noConflict();

	if(task == 'cancel'){
		Joomla.submitform(task);
		return;
	}

	var params_name= jQuery("#params_rule_name").val();
	

	if(params_name == '')
	{
		jQuery('#params_rule_name').css('border','solid 1px #FF0000');
		return false;
	}

	Joomla.submitform(task);
}
</script>
<div class="pg-content">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php PagoHtml::deploy_tabpanel('tabs') ?>
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li class="first pg-information">
						<a href="#tabs-1" onclick="addTabPrefixInUrl(this);">
							<span class="icon"></span>
							<?php echo JText::_( 'PAGO_SHIPPING_RULE_TITLE_GENERAL_PARAMETERS' ); ?>
						</a>
					</li>
					<li class="pg-information">
						<a href="#tabs-2" onclick="addTabPrefixInUrl(this);">
							<span class="icon"></span>
							<?php echo JText::_( 'PAGO_SHIPPING_RULE_TITLE_PRICING_PARAMETERS' ); ?>
						</a>
					</li>
					<li class="pg-information">
						<a href="#tabs-3" onclick="addTabPrefixInUrl(this);">
							<span class="icon"></span>
							<?php echo JText::_( 'PAGO_SHIPPING_RULE_TITLE_CATEGORY_PARAMETERS' ); ?>
						</a>
					</li>
					<li class="pg-information">
						<a href="#tabs-4" onclick="addTabPrefixInUrl(this);">
							<span class="icon"></span>
							<?php echo JText::_( 'PAGO_SHIPPING_RULE_TITLE_ORDER_PARAMETERS' ); ?>
						</a>
					</li>
					<li class="pg-information">
						<a href="#tabs-5" onclick="addTabPrefixInUrl(this);">
							<span class="icon"></span>
							<?php echo JText::_( 'PAGO_SHIPPING_RULE_TITLE_WEIGHT_PARAMETERS' ); ?>
						</a>
					</li>
					<li class="pg-information">
						<a href="#tabs-6" onclick="addTabPrefixInUrl(this);">
							<span class="icon"></span>
							<?php echo JText::_( 'PAGO_SHIPPING_RULE_TITLE_ADDRESS_PARAMETERS' ); ?>
						</a>
					</li>
					<?php
						// Needs to be a filter, otherwise the $counter gets lost
						$counter = 2;
						$dispatcher->trigger( 'backend_shipping_rules_tab_name', array( &$counter, $this->item ) );
					?>
				</ul>
				<div class="clear"></div>
			</div>

			<div class="pg-tab-content">
				<div id="tabs-1">
					<div class="pg-pad-20 pg-border pg-white-bckg">
						<?php echo PagoHtml::module_top(JText::_('PAGO_ITEM_TITLE_BASE_PARAMETERS'), $this->item->rule_name, null, null, null, null, null, null) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<?php echo $this->base_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
				<div id="tabs-2">
					<div class="pg-pad-20 pg-border pg-white-bckg">
						<?php echo PagoHtml::module_top(JText::_('PAGO_SHIPPING_RULE_TITLE_PRICING_PARAMETERS'), $this->item->rule_name, null, null, null, null, null, null) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<?php echo $this->pricing_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
				<div id="tabs-3">
					<div class="pg-pad-20 pg-border pg-white-bckg">
						<?php echo PagoHtml::module_top(JText::_('PAGO_SHIPPING_RULE_TITLE_CATEGORY_PARAMETERS'), $this->item->rule_name, null, null, null, null, null, null) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<?php echo $this->category_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
				<div id="tabs-4">
					<div class="pg-pad-20 pg-border pg-white-bckg">
						<?php echo PagoHtml::module_top(JText::_('PAGO_SHIPPING_RULE_TITLE_ORDER_PARAMETERS'), $this->item->rule_name, null, null, null, null, null, null) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<?php echo $this->order_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
				<div id="tabs-5">
					<div class="pg-pad-20 pg-border pg-white-bckg">
						<?php echo PagoHtml::module_top(JText::_('PAGO_SHIPPING_RULE_TITLE_WEIGHT_PARAMETERS'), $this->item->rule_name, null, null, null, null, null, null) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<?php echo $this->weight_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
				<div id="tabs-6">
					<div class="pg-pad-20 pg-border pg-white-bckg">
						<?php echo PagoHtml::module_top(JText::_('PAGO_SHIPPING_RULE_TITLE_ADDRESS_PARAMETERS'), $this->item->rule_name, null, null, null, null, null, null) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<?php echo $this->address_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
			</div>
			<?php
				// Needs to be a filter, otherwise the $counter gets lost
				$counter = 2;
				$dispatcher->trigger('backend_shipping_rules_tab_data', array(&$counter, $this->item));
			?>
		</div>
		<input type="hidden" name="cid[]" value="<?php echo $this->item->rule_id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->item->rule_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="shippingrules" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>

</div><!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();