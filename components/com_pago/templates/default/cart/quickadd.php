<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
$config = Pago::get_instance('config')->get();
$checkout_continue_shopping_link = $config->get('checkout.checkout_continue_shopping_link');
 ?>
	<div id="pg-quick-addcart">
    	<div class="pg-quick-addcart-item">
            <div class="pg-item-image"><img src="http://placehold.it/125x140"></div>
            <div class="pg-item-title">ITEM_TITLE</div>
            <div class="pg-item-price">$45.98</div>
            <div class="pg-item-quantity">
                <form class="pg-cart-actions" method="post" action="<?php echo JRoute::_('index.php') ?>">
                    <input type="hidden" value="com_pago" name="option">
                    <input type="hidden" value="cart" name="view">
                    <input type="hidden" value="update" name="task">
                    <input type="hidden" value="<?php echo $item->id ?>" name="id">
                    <input type="text" value="<?php echo $item->cart_qty ?>" name="qty" maxlength="4" size="4" class="pg-inputbox" title="<?php echo JText::_('PAGO_CART_UPDATE_IPUTBOX_TITLE'); ?>">
                    <button class="pg-cart-update pg-button" type="submit"><?php echo JText::_('PAGO_CART_UPDATE'); ?></button>
                    <?php echo JHTML::_( 'form.token' ) ?>
                </form>
            </div>
        </div>
		<?php
				if(isset($checkout_continue_shopping_link))
				{
					$clink = $checkout_continue_shopping_link;
				}
				else
				{
					$clink =  JURI::root();
				}
				?>
        <div class="pg-quick-addcart-checkout">
            <a href="<?php echo $clink; ?>" title="<?php echo JText::_('PAGO_CART_CONTINUE_SHOPPING'); ?>" class="pg-continue-shopping pg-button">&lt;&lt;&nbsp; <?php echo JText::_('PAGO_CART_CONTINUE_SHOPPING'); ?></a>
            <a href="index.php?option=com_pago&view=checkout" title="<?php echo JText::_('PAGO_CART_CHECKOUT'); ?>" class="pg-checkout-link pg-button"><?php echo JText::_('PAGO_CART_CHECKOUT'); ?> &nbsp;&gt;&gt;</a>
        </div>
    </div>
<?php die(); ?>
