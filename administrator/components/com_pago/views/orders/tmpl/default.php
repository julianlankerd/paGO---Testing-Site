<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_banners.category');
$saveOrder	= $listOrder=='ordering';

JHtml::_('behavior.keepalive');

PagoHtml::apply_layout_fixes();
PagoHtml::uniform();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items );
?>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get( '_name' ) ); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?>" />
			<div id="pg-button-search" class="pg-button pg-button-grey pg-button-search" tabindex="0"><div><button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button></div></div>
			<div id="pg-button-clear" class="pg-button pg-button-grey pg-button-clear" tabindex="0"><div><button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button></div></div>
		</div>
		<div class="pg-filter-options">
			<div class="filter-select fltrt">
				<select name="filter_order_status" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_PAGO_PAYMENT_STATUS');?></option>
					<?php echo JHtml::_('select.options', $this->order_status_options, 'value', 'text', $this->state->get('filter.order_status'));?>
				</select>
			</div>
		</div>
	</fieldset>
	                   
    <!--
    [order_id] => 291
    [user_id] => 501
    [vendor_id] => 1
    [order_number] => 501_a273a2122ceaf67ed29a25d606fe
    [user_info_id] => 20f60a1df0455566ac32b095a3019181
    [payment_gateway] => 
    [order_total] => 758.61000
    [order_subtotal] => 740.81700
    [order_refundtotal] => 0.00000
    [order_tax] => 0.00
    [order_tax_details] => a:1:{s:0:"";d:0;}
    [order_shipping] => 17.79
    [order_shipping_tax] => 0.00
    [coupon_discount] => 0.00
    [coupon_code] => 
    [order_discount] => 0.00
    [order_currency] => USD
    [order_status] => S
    [cdate] => 2010-01-24 10:55:24
    [mdate] => 2010-01-24 10:55:24
    [ship_method_id] => greensupply|UPS|UPS 2nd Day Air|17.79
    [customer_note] => 
    [ip_address] => 209.126.162.4
    [ipn_dump] => 
    [payment_message] => 
    [name] => 
    -->
	<div class="pg-table-wrap">
		<table id="pg-orders-manager" class="pg-table pg-repeated-rows pg-orders-manager">
			<thead>
				<tr class="pg-main-heading">
					<td colspan="7">
						<div class="pg-background-color">
							<?php echo JText::_( 'PAGO_ORDERS_MANAGER' ); ?>
						</div>
					</td>
				</tr>
				<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
					<td class="pg-checkbox">
						<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						<label for="checkall"></label>
					</td>
					<td class="pg-order-id <?php if ( $listOrder == 'order_id' ) { echo 'pg-currently-sorted'; } ?>">
						<div class="pg-sort-indicator-wrapper">
						<?php
							echo JHtml::_('grid.sort',  'PAGO_ORDER_ID', 'order_id', $listDirn, $listOrder);
							if ( $listOrder == 'order_id' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
						</div>
					</td>
					<td class="pg-date <?php if ( $listOrder == 'cdate' ) { echo 'pg-currently-sorted'; } ?>">
						<div class="pg-sort-indicator-wrapper">
						<?php
							echo JHtml::_('grid.sort', 'PAGO_ORDER_DATE', 'cdate', $listDirn, $listOrder);
							if ( $listOrder == 'cdate' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
						</div>
					</td>
					<td class="pg-user-name  <?php if ( $listOrder == 'first_name' ) { echo 'pg-currently-sorted'; } ?>">
						<div class="pg-sort-indicator-wrapper">
						<?php
							echo JHtml::_('grid.sort', 'PAGO_CUSTOMER_NAME', 'first_name', $listDirn, $listOrder);
							if ( $listOrder == 'first_name' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
						</div>
					</td>
					<td class="pg-payment-gateway <?php if ( $listOrder == 'payment_gateway' ) { echo 'pg-currently-sorted'; } ?>">
						<div class="pg-sort-indicator-wrapper">
						<?php
							echo JHtml::_('grid.sort', 'PAGO_PAYMENT_GATEWAY', 'payment_gateway', $listDirn, $listOrder);
							if ( $listOrder == 'payment_gateway' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
						</div>
					</td>
					<td class="pg-order-status <?php if ( $listOrder == 'order_status' ) { echo 'pg-currently-sorted'; } ?>">
						<div class="pg-sort-indicator-wrapper">
						<?php
							echo JHtml::_('grid.sort', 'PAGO_ORDER_STATUS', 'order_status', $listDirn, $listOrder);
							if ( $listOrder == 'order_status' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
						</div>
					</td>
					<td class="pg-order-total <?php if ( $listOrder == 'order_total' ) { echo 'pg-currently-sorted'; } ?>">
						<div class="pg-sort-indicator-wrapper">
						<?php
							echo JHtml::_('grid.sort',  'PAGO_ORDER_TOTAL', 'order_total', $listDirn, $listOrder);
							if ( $listOrder == 'order_total' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
						</div>
					</td>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				/*$ordering	= ($listOrder == 'ordering');
				$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_banners&task=edit&type=other&cid[]='. $item->catid);
				$canCreate	= $user->authorise('core.create',		'com_banners.category.'.$item->catid);
				$canEdit	= $user->authorise('core.edit',			'com_banners.category.'.$item->catid);
				$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canChange	= $user->authorise('core.edit.state',	'com_banners.category.'.$item->catid) && $canCheckin;*/
				?>
				<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
					<td class="pg-checkbox">
						<?php echo JHtml::_('grid.id', $i, $item->order_id); ?>
						<label for="cb<?php echo $i ?>"></label>
					</td>
					<td class="pg-id">
						<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=orders&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $item->order_id); ?>">
							<?php echo $this->escape($item->order_id); ?></a>
						
						<!-- <p class="smallsub"><?php echo JText::_( 'COM_PAGO_MODIFIED_DATE' ) . $item->mdate; ?></p> -->
					</td>
	                <td class="pg-date">
						<?php echo $item->cdate ?>
					</td>
					<td class="pg-user-name">
						<?php echo $item->name ?>
					</td>
					<td class="pg-payment-gateway">
						<?php echo $item->payment_gateway ?>
					</td>
					<td class="pg-order-status">
						<span title="<?php echo $item->order_status; ?>" class="pg-icon pg-<?php echo strtolower(preg_replace("/[^A-Za-z0-9]/", "-", $item->order_status)); ?>"><?php echo $item->order_status; ?></span>
					</td>
					<td class="pg-order-total">
						<?php echo $item->order_total;?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="pg-pagination">
		<?php echo PagoHtml::pago_pagination($this->pagination); ?>
		<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
	</div>

	<div>
		<input type="hidden" name="task" value="" />
        <input type="hidden" name="controller" value="orders" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();