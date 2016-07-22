<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<div id="pago-sidemenu">
	<ul class="listMenu">
		<li>
			<h1>Account Home</h1>
			<ul class="listChild">
				<li class="current"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account' ); ?>">Dashboard</a></li>
			</ul>
		</li>
		<li>
			<h1>Orders</h1>
			<ul class="listChild">
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order_status' ); ?>">Order Status</a></li>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order_history' ); ?>">Order History</a></li>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=downloads' ); ?>">Product Downloads</a></li>
			</ul>
		</li>
		<li>
			<h1>Options</h1>
			<ul class="listChild">
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=account_settings' ); ?>">Account Settings</a></li>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=address_book' ); ?>">Address Book</a></li>
			</ul>
		</li>
		<li>
			<h1>Shopping</h1>
			<ul class="listChild">
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=cart' ); ?>">Shopping Cart</a></li>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=products_purchased' ); ?>">Products Purchased</a></li>
			</ul>
		</li>
	</ul>
</div>