<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if( !empty( $this->orders ) ) : ?>
	<table id="pg-account-orders-status-table" class="pg-account-table">
		<thead>
			<tr>
				<th>date</th>
				<th>order number</th>                
				<th>status</th>                        
				<th>price</th>
				<th><?php echo JTEXT::_("COM_PAGO_PAYMENT_INFORMATION") ?></th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $this->orders as $order ) :
				$order_status = Pago::get_instance( 'orders' )->get_order_status( $order->order_status ); ?>
			<tr>
				<td class="pg-account-recent-order-date">
					<?php echo date( 'n/j/Y', strtotime( $order->cdate ) ); ?>
				</td>
				<td class="pg-account-recent-order-number">
					<?php echo JText::_('PAGO_ACCOUNT_ORDER_NUMBER'); ?> 
					<?php echo $order->order_id; ?>
				</td>
				<td class="pg-account-recent-order-status">
					<?php echo $order_status; ?>
				</td>
				<td class="pg-account-recent-order-total">
					<?php echo Pago::get_instance( 'price' )->format( $order->order_total ); ?>
				</td>
				<td class="pg-account-recent-order-total">

					<?php 
					
					if($order->payment_gateway == "banktransfer")
					{
						$dispatcher = KDispatcher::getInstance();
						JPluginHelper::importPlugin('pago_gateway');
						$plugin = JPluginHelper::getPlugin('pago_gateway',$order->payment_gateway);
						$pluginParams = new JRegistry(@$plugin->params);
						$order->payment_gateway = $order->payment_gateway . "<br/>" . $pluginParams->get('txtextra_info');
					}
					
					echo $order->payment_gateway; ?>

				</td>

				<td>
					<a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order_receipt&status=true&order_id=' . $order->order_id ); ?>">
						View Order
					</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : 
		$ordId = JFactory::getApplication()->input->get('status_search');
		
		if($ordId)
		{
			?>
			<b><p><?php echo JText::_('PAGO_ACCOUNT_ORDER_NOT_FOUND'); ?></p></b>
			<?php 
		}
		else
		{
			?>
			<p><?php echo JText::_('PAGO_ACCOUNT_ORDERS_NO_RECENT_ORDERS'); ?></p>
			<?php 
		}
		?>
<?php endif; ?>
