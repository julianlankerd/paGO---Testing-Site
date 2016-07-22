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
					<li class="first pg-information"><a href="#tabs-1" onclick="addTabPrefixInUrl(this);"><span class="icon"></span><?php echo JText::_('PAGO_CUSTOM_TEMPLATE_INFORMATION'); ?></a></li>
					<li class="pg-attributes"><a href="#tabs-2" onclick="addTabPrefixInUrl(this);"><span class="icon"></span><?php echo JText::_('PAGO_CUSTOM_TEMPLATE_HINTS'); ?></a></li>
					<?php
						// Needs to be a filter, otherwise the $counter gets lost
						$counter = 3;
						$dispatcher->trigger('backend_templates_tab_name', array( &$counter, $this->item));
					?>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="pg-tab-content">
				<div class="pg-border pg-white-bckg pg-pad-20">
					<div id="tabs-1">
						<?php echo PagoHtml::module_top(JText::_('PAGO_CUSTOM_TEMPLATE_BASE_PARAMETERS'), $this->item->pgtemplate_name, null, null, null, null, null, null, null) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
			                <?php echo $this->base_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
					<div id="tabs-2">
						<?php echo PagoHtml::module_top(JText::_('PAGO_CUSTOM_TEMPLATE_HINTS'), null, null, null, null, null, null, null, null); ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<div class="pg-row">
								
								<?php 
								if($this->item->pgtemplate_type)
								{
									$hints = PagoHtml::getCustomTemplateHints($this->item->pgtemplate_type); 
									$hints = str_replace("\n\r", "\n", $hints);
									$hints = explode("\n", $hints);
									
									$hints_table = array();
									$key = null;
									
									foreach ($hints as $hint) {
										$hint = explode(',', $hint);
										
										if (!isset($hint[1])) {
											$key = $hint[0];
											continue;
										}
										
										$hints_table[$key][] = $hint;
									}
									
									foreach ($hints_table as $type => $hints) :
								?>
								
								<div class="pg-col-6">
									<div class="pg-table-wrap">
										<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
											<thead>
												<tr class="pg-main-heading pg-multiple-headings pg-sortable-table">
													<td colspan="2">
														<?php echo $type; ?>
													</td>
												</tr>
												<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
													<td>
														<?php echo JText::_('PAGO_CUSTOM_TEMPLATE_HINT_TAG'); ?>
													</td>
													<td>
														<?php echo JText::_('PAGO_CUSTOM_TEMPLATE_HINT_VALUE'); ?>
													</td>
												</tr>
											</thead>
											<tbody>
												
												<?php foreach ($hints as $hint) : ?>
												
												<tr>
													<td><?php echo $hint[0]; ?></td>
													<td><?php echo $hint[1]; ?></td>
												</tr>
												
												<?php endforeach; ?>
												
											</tbody>
										</table>
									</div>
								</div>
								
								<?php
									endforeach;
									}
									else
									{
										echo  JText::_('PAGO_HINTS_AVAILABLE_AFTER_SAVING_TEMPLATE');
									}
								?>
								
							</div>
						</div>
						<?php echo PagoHtml::module_bottom(); ?>
					</div>
				</div>
			</div>
			<?php
				// Needs to be a filter, otherwise the $counter gets lost
				$counter = 3;
				$dispatcher->trigger('backend_templates_tab_data', array(&$counter, $this->item));
			?>
		</div>
		<input type="hidden" name="cid[]" value="<?php echo $this->item->pgtemplate_id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->item->pgtemplate_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="templates" />
		<?php echo JHTML::_('form.token'); ?>
	</form>

</div><!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();