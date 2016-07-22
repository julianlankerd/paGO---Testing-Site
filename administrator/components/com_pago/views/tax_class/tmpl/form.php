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
PagoHtml::uniform();
PagoHtml::tooltip();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';
PagoHtml::pago_top($menu_items, 'tabs', $this->top_menu);

?>
<div class="pg-content">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php PagoHtml::deploy_tabpanel('tabs') ?>
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li class="first pg-information"><a href="#tabs-1"><span class="icon"></span><?php echo JText::_('PAGO_TAX_CLASS_INFORMATION'); ?></a></li>
					<?php
						// Needs to be a filter, otherwise the $counter gets lost
						$counter = 2;
						$dispatcher->trigger('backend_tax_class_tab_name', array( &$counter, $this->item));
					?>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="pg-tab-content">
				<div id="tabs-1">
					<div class="pg-border pg-pad-20 pg-white-bckg">
						<?php echo PagoHtml::module_top(JText::_('PAGO_TAX_CLASS_BASE_PARAMETERS'), $this->item->pgtax_class_name, null, null, null, null, null, false) ?>
						<div class="pg-border pg-pad-20 pg-white-bckg">
							<?php echo $this->base_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
			</div>
			<?php
				// Needs to be a filter, otherwise the $counter gets lost
				$counter = 2;
				$dispatcher->trigger( 'backend_tax_class_tab_data', array( &$counter, $this->item ) );
			?>
		</div>
		<input type="hidden" name="cid[]" value="<?php echo $this->item->pgtax_class_id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->item->pgtax_class_id; ?>" />
        <input type="hidden" name="pgtax_class_id" value="<?php echo $this->item->pgtax_class_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="tax_class" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
<!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();