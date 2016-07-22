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
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';
PagoHtml::pago_top($menu_items, '', $this->top_menu);
?>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get('_name')); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class = "no-margin pg-mb-20">
		<div class="pg-filter-options">
			<div class="pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>
		</div>
		<div class="clear"></div>
	</fieldset>
	
	<div class="pg-container-header">
		<?php echo JText::_( 'PAGO_TAX_CLASS' ); ?>
	</div>
	<div class="pg-white-bckg pg-border pg-pad-20">
		<div class="pg-table-wrap">
			<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
				<thead>
					<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
						<td class="pg-checkbox">
							<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'td.pg-checkbox input');" />
							<label for="checkall"></label>
						</td>
						<td class="pg-taxname
						<?php
						if ($listOrder == 'pgtax_class_name')
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort', 'PAGO_TAX_CLASS_NAME', 'pgtax_rate', $listDirn, $listOrder);
	
								if ($listOrder == 'pgtax_class_name')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-taxname">
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_('PAGO_TAX_CLASS_VIEW_TAX_RATES'); ?>
							</div>
						</td>
						<td class="pg-published
						<?php
						if ( $listOrder == 'pgtax_class_enable' )
						{
							echo 'pg-currently-sorted';
						}
						?>
							">
							<?php echo JText::_('PAGO_PUBLISHED'); ?>
						</td>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
						<td class="pg-checkbox">
							<?php echo JHtml::_('grid.id', $i, $item->pgtax_class_id); ?>
							<label for="cb<?php echo $i ?>"></label>
						</td>
						<td class="pg-item-mailname">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=tax_class&task=edit&view=' . $this->get('_name') . '&cid[]=' . (int) $item->pgtax_class_id); ?>">
								<?php echo $item->pgtax_class_name; ?></a>
						</td>
						<td class="pg-item-mailname">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&view=tax&taxcid=' . (int) $item->pgtax_class_id); ?>">
								<?php echo JText::_('PAGO_TAX_CLASS_VIEW_TAX_RATES'); ?></a>
						</td>
		                <td class="pg-published">
			                <?php 
			                	$item->published = $item->pgtax_class_enable;
			                
			                	echo PagoHelper::published( $item, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="tax_class" rel="' .$item->pgtax_class_id. '"' ); ?>
			                <?php //echo JHtml::_('jgrid.published', $item->pgtax_class_enable, $i); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="pg-pagination">
		<?php echo PagoHtml::pago_pagination($this->pagination); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="tax_class" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php PagoHtml::pago_bottom();