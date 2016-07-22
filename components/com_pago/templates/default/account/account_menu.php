<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
	$layout = JFactory::getApplication()->input->get('layout');
?>

<div class="pg-parent-menu clearfix">
	<!--<li class="outer dashboard-level1 account_home column-1of4">
		<div class="inner-column">
			<div class="wrap-pg-title">
				<h2 class="pg-title"><?php echo JText::_( 'PAGO_ACCOUNT_HOME' ); ?></h2>
			</div>
			<ul class="child-menu dashboard-level2">
				<li<?php if( $layout == '' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account' ); ?>"><?php echo JText::_( 'PAGO_DASHBOARD_TITLE' ); ?></a></li>
			</ul>
		</div>
	</li>
	<li class="outer dashboard-level1 account_orders column-2of4">
		<div class="inner-column">
			<div class="wrap-pg-title">
				<h2 class="pg-title"><?php echo JText::_( 'PAGO_ACCOUNT_ORDERS' ); ?></h2>
			</div>
			<ul class="child-menu dashboard-level2">
				<li<?php if( $layout == 'order-status' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order-status' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_ORDER_STATUS_TITLE' ); ?></a></li>
				<li<?php if( $layout == 'order-history' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order-history' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_ORDER_HISTORY_TITLE' ); ?></a></li>
				<li<?php if( $layout == 'downloads' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=downloads' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_PRODUCT_DOWNLOADS' ); ?></a></li>
			</ul>
		</div>
	</li>
	<li class="outer dashboard-level1 account_options column-3of4">
		<div class="inner-column">
			<div class="wrap-pg-title">
				<h2 class="pg-title"><?php echo JText::_( 'PAGO_ACCOUNT_OPTIONS' ); ?></h2>
			</div>
			<ul class="child-menu dashboard-level2">
				<li<?php if( $layout == 'account_settings' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=account_settings' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_SETTINGS' ); ?></a></li>
				<li<?php if( $layout == 'addresses' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=addresses' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_ADDRESSES' ); ?></a></li>
				<?php /*<li<?php if( $layout == 'affiliate' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=affiliate' ); ?>">Affiliate</a></li>*/ ?>
			</ul>
		</div>
	</li>
	<li class="outer dashboard-level1 account_shopping column-4of4">
		<div class="inner-column">
			<div class="wrap-pg-title">
				<h2 class="pg-title"><?php echo JText::_( 'PAGO_ACCOUNT_SHOPPING' ); ?></h2>
			</div>
			<ul class="child-menu dashboard-level2">
				<li<?php if( $layout == 'cart' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=cart' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_SHOPPING_CART' ); ?></a></li>
				<li<?php if( $layout == 'products_purchased' ) { echo ' class="current"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=products_purchased' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_PRODUCTS_PURCHASED' ); ?></a></li>
			</ul>
		</div>
	</li>-->
	<div <?php if( $layout == '' ) { echo ' class="active"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account' ); ?>"><?php echo JText::_( 'PAGO_DASHBOARD_TITLE' ); ?></a></div>
	<!--<div <?php if( $layout == 'account_settings' ) { echo ' class="active"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=account_settings' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_PROFILE' ); ?></a></div>-->
	<div <?php if( $layout == 'order_history' || $layout == 'order-history'  ) { echo ' class="active"'; } ?> class="dashboard-level2"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order_history' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_ORDER_HISTORY_TITLE' ); ?></a></div>
</div>