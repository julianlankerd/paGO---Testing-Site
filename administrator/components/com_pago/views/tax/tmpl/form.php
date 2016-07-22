<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery('jqueryui');
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';
PagoHtml::pago_top($menu_items, 'tabs', $this->top_menu);

	
?>
<div class="pg-content">
	<form action="index.php" method="post" name="adminForm"  id="adminForm">
		<?php PagoHtml::deploy_tabpanel('tabs') ?>
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li class="first pg-information"><a href="#tabs-1"><span class="icon"></span><?php echo JText::_('PAGO_TAX_INFORMATION'); ?></a></li>
					<?php
						// Needs to be a filter, otherwise the $counter gets lost
						$counter = 2;
						$dispatcher->trigger('backend_tax_tab_name', array( &$counter, $this->item));
					?>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="pg-tab-content">
				<div class="pg-pad-20 pg-white-bckg pg-border">
					<div id="tabs-1">
						<?php echo PagoHtml::module_top(JText::_('PAGO_TAX_PARAMETERS'), $this->item->pgtax_rate_name, null, null, null, null, null, false) ?>
						<div class="pg-pad-20 pg-white-bckg pg-border">
			                <?php echo $this->base_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
			</div>
			<?php
				// Needs to be a filter, otherwise the $counter gets lost
				$counter = 2;
				$dispatcher->trigger( 'backend_tax_tab_data', array( &$counter, $this->item ) );
			?>
		</div>
		<input type="hidden" name="cid[]" value="<?php echo $this->item->pgtax_id; ?>" />
		<input type="hidden" name="taxcid" value="<?php echo $this->item->pgtax_class_id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->item->pgtax_id; ?>" />
        <input type="hidden" name="pgtax_id" value="<?php echo $this->item->pgtax_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="tax" />
		<?php echo JHTML::_('form.token'); ?>
	</form>

</div><!-- end pago content -->
<?php PagoHtml::add_js( JURI::root( true ). '/components/com_pago/javascript/jquery.chained.mini.js' );
$doc = JFactory::getDocument();
$js='jQuery(document).ready(function() {

			jQuery("#params_pgtax_stateparamspgtax_state").chained("#params_pgtax_countryparamspgtax_country");

			jQuery("#params_pgtax_stateparamspgtax_state").trigger("chosen:updated");
			jQuery(document).on("change","#params_pgtax_countryparamspgtax_country",function(){

				jQuery("#params_pgtax_stateparamspgtax_state option:selected").removeAttr("selected");

				jQuery("#params_pgtax_stateparamspgtax_state").trigger("chosen:updated");

				return false;
		})
	});

';

  
$doc->addScriptDeclaration( $js ); ?>
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();