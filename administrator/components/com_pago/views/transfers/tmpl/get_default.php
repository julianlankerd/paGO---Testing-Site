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
JHtml::_('behavior.keepalive');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$dispatcher = KDispatcher::getInstance();

PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';

PagoHtml::pago_top($menu_items, '', $this->top_menu);

?>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get('_name')); ?>" method="post" name="adminForm" id="adminForm">
	
	<!--
	<fieldset id="filter-bar" class = "no-margin pg-mb-20">
		<div class="filter-search pg-left">
			<input class = "pg-left pg-mr-20" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape(''); ?>" title="<?php echo JText::_('PAGO_SEARCH'); ?>" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			<button id="pg-button-clear" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button" ><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="pg-filter-options">
			<div class = "pg-limit-box pg-right">
				<?php //echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>
		</div>
	</fieldset>
	-->
	
	<?php $item = $this->item;
	
	?>
	<?php echo PagoHtml::module_top( JText::_( 'PAGO_TRANSFER' ), null, null, null, null, null, null, false ); ?>
	<div class="pg-white-bckg pg-border pg-pad-20">
		<div class="pg-table-wrap">
			<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
					<tr class="">
						<td class="pg-cell" style="text-align:right">
							<?php echo Pago::get_instance( 'price' )->format($item->amount / 100); ?> 
							<?php echo strtoupper($item->currency) ?>
						</td>
						<td class="pg-cell" style="width:100px">
						<?php 
							$label = 'success';
							if($item->status != 'paid')
								$label = 'warning';
						?>
							<span style="color:#fff" class="label label-<?php echo $label ?>">
								<?php echo $item->status; ?></span>
						</td>
						<td class="pg-cell">
							<?php echo $item->bank_account->bank_name; ?> #****<?php echo $item->bank_account->last4; ?>
						</td>
						<td class="pg-cell">
							<?php echo date('d/m/Y', $item->date); ?>
						</td>
					</tr>
			</table>
		</div>
	</div>
	
	<?php echo PagoHtml::module_top( JText::_( 'PAGO_TRANSACTIONS' ), null, null, null, null, null, null, false ); ?>
	<div class="pg-white-bckg pg-border pg-pad-20">
		<div class="pg-table-wrap">
			<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
				<thead>
					<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
						
						<td class="pg-cell">
							<?php echo JText::_('PAGO_TYPE') ?>
						</td>
						
						<td class="pg-cell">
							<?php echo JText::_('PAGO_USER') ?>
						</td>
						
						<td class="pg-cell">
							<?php echo JText::_('PAGO_ORDERID') ?>
						</td>
						
						<td class="pg-cell">
							<?php echo JText::_('PAGO_CREATED') ?>
						</td>
						
						<td class="pg-cell" style="text-align:right">
							<?php echo JText::_('PAGO_GROSS') ?>
						</td>
						<td class="pg-cell" style="text-align:right">
							<?php echo JText::_('PAGO_FEE') ?>
						</td>
						<td class="pg-cell" style="text-align:right">
							<?php echo JText::_('PAGO_NET') ?>
						</td>
						
					</tr>
				</thead>
				<tbody>	
				<?php if(!empty($this->items)) foreach ($this->items as $i => $item) : $item->detail = json_decode($item->description) ?>
					<tr class="">
						
						<td class="pg-cell">
							<?php echo $item->type; ?>
						</td>
						
						<td class="pg-cell">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=customers&task=edit&view=customers&user_id='.$item->detail->user_id); ?>">
							<?php if($item->detail) echo $item->detail->name; ?>
							</a>
						</td>
						
						<td class="pg-cell">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=ordersi&task=edit&view=ordersi&cid[]='.$item->detail->order_id); ?>">
							<?php if($item->detail) echo $item->detail->order_id; ?>
							</a>
						</td>
						
						<td class="pg-cell">
							<?php echo date('d/m/Y', $item->created); ?>
						</td>
						
						<td class="pg-cell" style="text-align:right">
							<?php echo str_replace('$-', '-$', Pago::get_instance( 'price' )->format($item->amount / 100) ); ?> 
							<?php echo strtoupper($item->currency) ?>
						</td>
						<td class="pg-cell" style="text-align:right">
							<?php echo str_replace('$-', '-$', Pago::get_instance( 'price' )->format($item->fee / 100) ); ?> 
							<?php echo strtoupper($item->currency) ?>
						</td>
						<td class="pg-cell" style="text-align:right">
							<?php echo str_replace('$-', '-$', Pago::get_instance( 'price' )->format($item->net / 100) ); ?> 
							<?php echo strtoupper($item->currency) ?>
						</td>
						
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="pg-pagination">
		<?php //echo PagoHtml::pago_pagination($this->pagination); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="emails" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();