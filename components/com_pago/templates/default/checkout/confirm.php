<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<div id="pg-checkout" class="pg-step4">
	<h1 class="pg-title">Checkout</h1>
    <?php $this->step = 4; ?>
	<?php template_functions::load_template('checkout', 'checkout', 'progress'); ?>
    <div id="pg-checkout-confirm" class="grid-2">
    	<div id="pg-checkout-col1">
			<?php template_functions::load_template('checkout', 'checkout', 'quickcart'); ?>
        </div>
        <div id="pg-checkout-col2">
            <div id="pg-checkout-order-details">
                <h3><?php echo JText::_('PAGO_CHECKOUT_CONFIRM_ORDER_DETAILS'); ?></h3>
                <div class="clearfix">
                    <div id="pg-checkout-shipping-info">
                        <h4><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_ADDRESS'); ?> <a href="<?php echo JRoute::_('index.php?option=com_pago&view=checkout&step=shipping'); ?>" class="pg-checkout-edit-link"><?php echo JText::_('PAGO_CHECKOUT_EDIT'); ?></a></h4>
                        <ul class="pg-shipping-address">
                            <li class="pg-shipping-address-name"><span class="pg-shipping-address-first-name"><?php echo $this->shipping_address->first_name; ?></span> <span class="pg-shipping-address-last-name"><?php echo $this->shipping_address->last_name; ?></span></li>
                            <li class="pg-shipping-addressreet"><?php echo $this->shipping_address->address_1; ?></li>
                            <?php if($this->shipping_address->address_2) : ?><li class="pg-shipping-address-street"><?php echo $this->shipping_address->address_2; ?></li><?php endif; ?>
                            <li><span class="pg-shipping-address-city"><?php echo $this->shipping_address->city; ?></span>, <span class="pg-shipping-address-state"><?php echo $this->shipping_address->state;?></span> <span class="pg-shipping-address-zip"><?php echo $this->shipping_address->zip; ?></span></li>
                            <li class="pg-shipping-address-country"><?php echo $this->shipping_address->country; ?></li>
                            <li class="pg-shipping-address-phone"><?php echo $this->shipping_address->phone_1; ?></li>
                        </ul>
                        <?php if( $this->shipper ): ?>
                        <h4><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_METHOD'); ?> <a href="<?php echo JRoute::_('index.php?option=com_pago&view=checkout&step=ship_method'); ?>" class="pg-checkout-edit-link"><?php echo JText::_('PAGO_CHECKOUT_EDIT'); ?></a></h4>
                        <p><span class="pg-shipper-name"><?php echo $this->shipper['name']; ?></span></p>
                        <?php endif; ?>
                    </div>
                    <div id="pg-checkout-billing-info">
                        <h4><?php echo JText::_('PAGO_CHECKOUT_BILLING_ADDRESS'); ?> <a href="<?php echo JRoute::_('index.php?option=com_pago&view=checkout&step=billing'); ?>" class="pg-checkout-edit-link"><?php echo JText::_('PAGO_CHECKOUT_EDIT'); ?></a></h4>
                        <ul class="pg-billing-address">
                            <li class="pg-billing-address-name"><span class="pg-billing-address-first-name"><?php echo $this->billing_address->first_name; ?></span> <span class="pg-billing-address-last-name"><?php echo $this->billing_address->last_name; ?></span></li>
                            <li class="pg-billing-addressreet"><?php echo $this->billing_address->address_1; ?></li>
                            <?php if($this->billing_address->address_2) : ?><li class="pg-billing-address-street"><?php echo $this->billing_address->address_2; ?></li><?php endif; ?>
                            <li><span class="pg-billing-address-city"><?php echo $this->billing_address->city; ?></span>, <span class="pg-billing-address-state"><?php echo $this->billing_address->state;?></span> <span class="pg-billing-address-zip"><?php echo $this->billing_address->zip; ?></span></li>
                            <li class="pg-billing-address-country"><?php echo $this->billing_address->country; ?></li>
                            <li class="pg-billing-address-phone"><?php echo $this->billing_address->phone_1; ?></li>
                        </ul>
                        <?php if( $this->shipper ): ?>
                        <h4><?php echo JText::_('PAGO_CHECKOUT_PAYMENT_METHOD'); ?> <a href="<?php echo JRoute::_('index.php?option=com_pago&view=checkout&step=billing'); ?>" class="pg-checkout-edit-link"><?php echo JText::_('PAGO_CHECKOUT_EDIT'); ?></a></h4>
                        <p><span class="pg-shipper-name"><?php print_r( $this->payment_option ); ?></span></p>
                        <?php endif; ?>
                    </div>
                    <div id="pg-checkout-confirm-cart" class="clearfix">
                        <h4><?php echo JText::_('PAGO_CHECKOUT_ORDER_SUMMARY'); ?></h4>
                        <div id="pg-cart-contents">
                            <table id="pg-cart-table" class="pg-table">
                                <thead>
                                    <tr>
                                        <th class="pg-cart-item" colspan="2"><?php echo JText::_('PAGO_CART_ITEM_DESC'); ?></th>
                                        <th class="pg-cart-item-quantity"><?php echo JText::_('PAGO_CART_QTY'); ?></th>
                                        <th class="pg-cart-item-price"><?php echo JText::_('PAGO_CART_PRICE'); ?></th>
                                        <th class="pg-cart-item-total"><?php echo JText::_('PAGO_CART_TOTAL'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($this->cart['items'] as $item): ?>
                                    <tr class="pg-cart-item">
                                        <td class="pg-cart-item-image">
                                            <a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=item&id=' . $item->id ); ?>" class="product_image_link"><img src="http://placehold.it/40x45"></a>
                                        </td>
                                        <td class="pg-cart-item-name">
                                            <a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=item&id=' . $item->id ); ?>"><?php echo $item->name; ?></a>
                                            <?php //item attributes when that's ready
                                                  //<p class="pg-cart-attributes">XL, Black</p> ?>
                                        </td>
                                        <td class="pg-cart-item-qty"><?php echo $item->cart_qty; ?></td>
                                        <td class="pg-cart-item-price"><?php echo $item->format_price; ?></td>
                                        <td class="pg-cart-item-total"><?php echo $item->format_subtotal; ?></td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="pg-cart-totals">
                            <table id="pg-cart-totals-table" class="pg-table">
                                <tbody>
                                    <tr class="pg-cart-subtotal">
                                        <td><span><?php echo JText::_('PAGO_CHECKOUT_SUBTOTAL'); ?></span></td>
                                        <td class="pg-cart-total"><?php echo $this->cart['format']['subtotal'] ?></td>
                                    </tr>
                                    <tr class="pg-cart-shipping-total">
                                        <td><span><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_TOTAL'); ?></span></td>
                                        <td class="pg-cart-total"><?php echo $this->cart['format']['shipping'] ?></td>
                                    </tr>
                                    <?php if(isset($promo_code)) : ?>
                                    <tr class="pg-cart-promo-total">
                                        <td><span><?php echo (isset($promo_codes)) ? $promo_codes : '' ?></span></td>
                                        <td class="pg-cart-total"><?php echo $this->cart['format']['promos'] ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr class="pg-cart-tax-total">
                                        <td><span><?php echo JText::_('PAGO_CHECKOUT_TAX_TOTAL'); ?></span></td>
                                        <td class="pg-cart-total"><?php echo $this->cart['format']['tax'] ?></td>
                                    </tr>
                                    <tr class="pg-cart-grand-total">
                                        <td><span><?php echo JText::_('PAGO_CHECKOUT_ORDER_TOTAL'); ?></span></td>
                                        <td class="pg-cart-total"><?php echo $this->cart['format']['total'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <form id="pg-checkout-complete-order-form" action="<?php echo JRoute::_('index.php'); ?>" method="post">
                        <div id="pg-checkout-confirm-notes">
                            <h4><?php echo JText::_('PAGO_CHECKOUT_CONFIRM_NOTES_TITLE'); ?></h4>
                            <textarea id="pg-checkout-notes" class="pg-textarea" name="notes"></textarea>
                        </div>
                        <input type="checkbox" name="terms_conditions" class="pg-checkbox" id="pg-checkout-express-terms" /><label for="pg-checkout-express-terms" class="pg-label">I agree to the Terms &amp; Conditions</label>
                        <input type="hidden" name="option" value="com_pago" />
                        <input type="hidden" name="view" value="checkout" />
                        <input type="hidden" name="step" value="complete" />
                        <button type="submit" class="pg-button" id="pg-checkout-complete-button"><?php echo JText::_('PAGO_CHECKOUT_COMPLETE_ORDER_BUTTON'); ?></button>
                    </form>
                </div>
			</div>
        </div>
    </div>
</div>