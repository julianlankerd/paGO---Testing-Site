<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
	
	$cart = Pago::get_instance( 'cart' );
	$user =& JFactory::getUser();
 
	if ( (  $this->tmpl_params->get( 'show_account', 1 ) + 
			$this->tmpl_params->get( 'show_sign_in', 1 ) + 
			$this->tmpl_params->get( 'show_mini_cart', 1 ) ) == 0 ) {
		$hide_options = 'hide_content';
	} else {
		$hide_options = '';
	}
?>
<div id="wrap-pg-options" class="outer <?php echo $hide_options; ?> clearfix">
	<div id="pg-options" class="inner <?php echo $hide_options; ?> clearfix">
		<h2 class="pg-options-title pg-title"><?php echo JText::_( 'PAGO_STORE_OPTIONS' ); ?></h2>
		<ul class="pg-options-list">
		
			<li class="skiplink"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=category' ); ?>"><?php echo JText::_( 'PAGO_SKIP_TO_CAT_MENU' ); ?></a></li>
			<?php // Check if user is signed in
			if( $user->id ) {
				if ( $this->tmpl_params->get( 'show_account', 1 ) ) { ?>
					<li class="options-account"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_MY_ACCOUNT' ); ?></a></li>
				<?php 
				}
			} else {
				if ( $this->tmpl_params->get( 'show_sign_in', 1 ) ) { ?>
					<li class="options-register"><a href="<?php echo JRoute::_( 'index.php?option=com_user&view=login' ); ?>"><?php echo JText::_( 'PAGO_SIGN_IN' ); ?></a>/<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=register' ); ?>"><?php echo JText::_( 'PAGO_REGISTER' ); ?></a></li>
				<?php 
				}
			}
			if ( $this->tmpl_params->get( 'show_mini_cart', 1 ) && $cart->get( 'item_count' ) ) { ?>
				<li class="mini-cart">
					<h3 class="mini-cart-title"><?php echo JText::_( 'PAGO_CART_OVERVIEW' ); ?>:</h3>
					<ul class="mini-cart-view">
						<li class="mini-cart-quantity"><?php echo $cart->get( 'item_count' ); ?> <?php echo ( $cart->get( 'item_count') == 1 ) ? JText::_('PAGO_MINI_CART_ITEM') : JText::_('PAGO_MINI_CART_ITEMS'); ?></li>
						<li class="mini-cart-balance"><?php echo $cart->get('format.total'); ?></li>
						<li class="mini-cart-viewcart"><a href="<?php echo JRoute::_( 'index.php?view=cart' ) ?>"><?php echo JText::_( 'PAGO_CART' ) ?></a></li>
						<li class="mini-cart-checkout"><a href="<?php echo JRoute::_( 'index.php?view=checkout' ) ?>"><?php echo JText::_( 'PAGO_CHECKOUT' ) ?></a></li>
					</ul>
					<!-- NOTE: TODO //-->
				</li>
			<?php 
			} ?>
		</ul>
	</div>
</div><!-- end #wrap-pg-options //-->
