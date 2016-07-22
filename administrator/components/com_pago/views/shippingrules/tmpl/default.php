<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_banners.category');
$saveOrder	= $listOrder == 'ordering';

PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php');

PagoHtml::pago_top($menu_items, '', $this->top_menu);

?>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get('_name')); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="no-margin pg-mb-20">
		<div class="filter-search">
			<input class="pg-left pg-mr-20" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('PAGO_SEARCH_IN_TITLE'); ?>" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="submit" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button id="pg-button-reset" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button"  onclick=""><?php echo JText::_('PAGO_FILTER_RESET'); ?></button>
		</div>
		<div class="clear"></div>
	</fieldset>
	
	<div class="pg-container-header">
		<?php echo JText::_('PAGO_CUSTOM_SHIPPING_RULES'); ?>
	</div>
	
	<div class="pg-border pg-white-bckg pg-pad-20">
		<div class="pg-table-wrap">
			<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
				<thead>
					<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
						<td class="pg-checkbox">
							<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'td.pg-checkbox input');" />
							<label for="checkall"></label>
						</td>
						<td class="pg-rulename <?php if ($listOrder == 'rule_name') { echo 'pg-currently-sorted'; } ?>">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort', 'PAGO_SHIPPING_RULE_NAME', 'rule_name', $listDirn, $listOrder);
	
								if ($listOrder == 'rule_name')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-price <?php if ( $listOrder == 'shipping_price' ) { echo 'pg-currently-sorted'; } ?>">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort',  'PAGO_SHIPPING_PRICE', 'shipping_price', $listDirn, $listOrder);
	
								if ($listOrder == 'shipping_price')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-priority <?php if ( $listOrder == 'priority' ) { echo 'pg-currently-sorted'; } ?>">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort',  'PAGO_CUSTOM_SHIPPING_RULE_PRIORITY', 'priority', $listDirn, $listOrder);
	
								if ($listOrder == 'priority')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-published <?php if ( $listOrder == 'published' ) { echo 'pg-currently-sorted'; } ?>">
							<div class="pg-published">
								
									<div class="pg-sort-indicator-wrapper">
									<?php
										echo JHtml::_('grid.sort', 'PAGO_PUBLISHED', 'published', $listDirn, $listOrder);
	
										if ($listOrder == 'published')
										{
											echo '<span class="pg-sorted-' . $listDirn . '"></span>';
										}
									?>
									</div>
							
							</div>
						</td>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
						<td class="pg-checkbox">
							<?php echo JHtml::_('grid.id', $i, $item->rule_id); ?>
							<label for="cb<?php echo $i ?>"></label>
						</td>
						<td class="pg-item-rulename">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=shippingrules&task=edit&view=' . $this->get('_name') . '&cid[]='.(int) $item->rule_id); ?>"><?php echo $item->rule_name; ?></a>
						</td>
						<td class="pg-item-price">
							<?php echo Pago::get_instance('price')->format($item->shipping_price);?>
						</td>
						<td class="pg-item-price">
							<?php echo $item->priority; ?>
						</td>
		                <td class="pg-published">
			                <?php echo PagoHelper::published( $item, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="shippingrules" rel="' .$item->rule_id. '"' ); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="pg-pagination">
		<?php echo $this->pagination->getListFooter(); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="shippingrules" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();