<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<div id="pg-checkout-express-checkout">
	<a name="pg-express-checkout"></a>
    <h3><?php echo JText::_('PAGO_CHECKOUT_EXPRESS_CHECKOUT_TITLE'); ?></h3>
    <div class="clearfix">
        <p><?php echo JText::_('PAGO_CHECKOUT_EXPRESS_CHECKOUT_DESC'); ?></p>
        <form id="pg-checkout-express-checkout-form" action="<?php echo JRoute::_('index.php'); ?>" method="post">
            <ul class="pg-checkout-express-payment-options">
                <li class="pg-checkout-express-payment-option">
                    <input type="radio" class="pg-radiobutton" name="payment_option" id="pg-checkout-express-payment-gcheckout" value="gcheckout" /><label for="pg-checkout-express-payment-gcheckout" class="pg-label"><img src="https://checkout.google.com/seller/images/badge_pill_white.gif" class="pg-gcheckout-image" /></label>
                </li>
            </ul>
            <input type="checkbox" name="terms_conditions" class="pg-checkbox" id="pg-checkout-express-terms" /><label for="pg-checkout-express-terms" class="pg-label">I agree to the Terms &amp; Conditions</label>
            <input type="hidden" name="option" value="com_pago" />
            <input type="hidden" name="view" value="checkout" />
            <input type="hidden" name="task" value="express_checkout" />
            <?php echo JHTML::_( 'form.token' ); ?>
            <button class="pg-button" type="submit"><?php echo JText::_('PAGO_CHECKOUT_EXPRESS_CHECKOUT_BUTTON'); ?></button>
        </form>
    </div>
</div>
