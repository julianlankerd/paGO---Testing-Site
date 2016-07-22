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

<?php if(empty(@$this->items)): ?>
	<?php echo JText::_( 'PAGO_TRANSFERS_NONE' ) ?>
<?php else: ?>

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
	
	<?php echo PagoHtml::module_top( JText::_( 'PAGO_TRANSFERS' ), null, null, null, null, null, null, false ); ?>
	<div class="pg-white-bckg pg-border pg-pad-20">
		<div class="pg-table-wrap">
			<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
				<tbody>
				<?php if(!empty($this->items)) foreach ($this->items as $i => $item) : ?>
					<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
						
						<td class="pg-cell" style="text-align:right">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&layout=get&view=transfers&id='.$item->id); ?>">
							<?php echo Pago::get_instance( 'price' )->format($item->amount / 100); ?> 
							<?php echo strtoupper($item->currency) ?>
							</a>
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

<?php PagoHtml::pago_bottom(); ?>

<?php endif ?>