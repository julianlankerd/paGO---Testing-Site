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
$taxClassName = '';
foreach($this->taxClasses as $taxClass)
{
	if($taxClass['value'] == $this->state->get('filter.pgtax_class_id'))
	{
		$taxClassName = $taxClass['text'];
	}
}
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';

PagoHtml::pago_top($menu_items, '', $this->top_menu);

?>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get('_name')); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="no-margin pg-mb-20">
		<div class="pg-filter-options">
			<div class="pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>
			<div class="filter-pgtax_class_id fltrt pg-filter-pgtax_class_id pg-right pg-mr-20">
				<select name="filter_pgtax_class_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('PAGO_SEL_TAX_CLASS');?></option>
					<?php echo JHtml::_('select.options', $this->taxClasses, 'value', 'text', $this->state->get('filter.pgtax_class_id'));?>
				</select>
			</div>
		</div>
	</fieldset>
	
	<div class="pg-container-header">
		<?php echo JText::_('PAGO_TAX_RATES'); ?> - <?php echo$taxClassName;?>
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
						<td class="pg-itemname
						<?php
						if ($listOrder == 'pgtax_rate_name')
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort', 'PAGO_TAX_RATE_NAME', 'pgtax_rate_name', $listDirn, $listOrder);
	
								if ($listOrder == 'pgtax_rate_name')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-mailname
						<?php
						if ($listOrder == 'pgtax_rate')
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort', 'PAGO_TAX_RATE', 'pgtax_rate', $listDirn, $listOrder);
	
								if ($listOrder == 'pgtax_rate')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-price
						<?php
						if ( $listOrder == 'pgtax_country' )
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort',  'PAGO_TAX_COUNTRY', 'pgtax_country', $listDirn, $listOrder);
	
								if ($listOrder == 'pgtax_country')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
	                    <td class="pg-price
						<?php
						if ( $listOrder == '	pgtax_state' )
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort',  'PAGO_TAX_STATE', '	pgtax_state', $listDirn, $listOrder);
	
								if ($listOrder == 'pgtax_state')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-priority
						<?php
						if ( $listOrder == 'priority' )
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort',  'PAGO_TAX_RATE_PRIORITY', 'priority', $listDirn, $listOrder);
	
								if ($listOrder == 'priority')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-published
						<?php
						if ( $listOrder == 'pgtax_enable' )
						{
							echo 'pg-currently-sorted';
						}
						?>
							">
							<div class="pg-published">
									<div class="pg-sort-indicator-wrapper">
									<?php
										echo JHtml::_('grid.sort', 'PAGO_EMAIL_PUBLISHED', 'pgtax_enable', $listDirn, $listOrder);
	
										if ($listOrder == 'pgtax_enable')
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
							<?php echo JHtml::_('grid.id', $i, $item->	pgtax_id); ?>
							<label for="cb<?php echo $i ?>"></label>
						</td>
						<td class="pg-item-name">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=tax&task=edit&view=' . $this->get('_name') . '&taxcid=' . (int) $item->pgtax_class_id . '&cid[]='.(int) $item->pgtax_id); ?>">
								<?php echo $item->pgtax_rate_name; ?></a>
						</td>
						<td class="pg-item-name">
							<?php echo $item->pgtax_rate; ?>%</a>
						</td>
						<td class="pg-item-price">
							<?php echo $item->pgtax_country;?>
						</td>
	                    <td class="pg-item-price">
							<?php echo $item->pgtax_state;?>
						</td>
						 <td class="pg-item-priority">
							<?php echo $item->priority;?>
						</td>
						<td class="pg-published">
							<?php 
								$item->published = $item->pgtax_enable;
								$item->id = $item->pgtax_id;
								echo PagoHelper::published( $item, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="tax" rel="' .$item->id. '"' ); 
							?>
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
		<input type="hidden" name="controller" value="tax" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();