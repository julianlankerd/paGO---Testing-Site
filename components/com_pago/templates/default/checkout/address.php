<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

 ?>
<?php $this->load_header(); ?>
<?php
$doc = JFactory::getDocument();
$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.chained.mini.js' );
$setShipSelected = '0';
?>
<div id="pg-checkout" class="pg-step2">
	<h1 class="pg-title">Checkout</h1>
    <div id="pg-checkout-shipping-info" class="grid-2">
        <div class = "pg-checkout-shipping-info-panel">
            <div class = "pg-checkout-shipping-info-heading">
                <div class="pg-checkout-shipping-info-heading-title">
                    <?php echo JText::_('PAGO_ACCOUNT_REGIESTER_SHIPPING_DETAILS'); ?>                   
                </div>
                <div class="pg-checkout-shipping-info-heading-change">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>
            <div class = "clearfix"></div>
            <div class = "pg-checkout-shipping-info-content">
                <div class = "row">
                    <div class = "col-sm-6">
                        <div class="pg-checkout-shipping-details">
                            <?php //echo JText::_('PAGO_ACCOUNT_REGIESTER_SHIPPING_DETAILS'); ?>
                            <div>
                                <?php if( !empty( $this->saved_addresses ) ) : ?>
                                    <form id="pg-checkout-shipping-form" action="<?php echo JRoute::_('index.php'); ?>" method="post">
                                    <div class="pg-checkout-shipping-addresses clearfix">
                                        <h4><?php echo JText::_('PAGO_CHECKOUT_SELECT_SHIPPING_ADDRESS'); ?></h4>
                                        <?php foreach( $this->saved_addresses as $user_address ) : 
										//changed
                                        $checked = '';
                                        $selected = '';
                                        if($setShipSelected ==  0)
                                        {
                                            $checked ='active';
                                            $selected = 'checked="checked"';
                                        }
                                        $setShipSelected++;
										?>
                                        <div class="pg-checkout-shipping-address">
                                            <fieldset class="pg-fieldset pg-shipping-address-fieldset">
                                                <legend class="pg-legend">
                                                    <input type="radio" <?php echo $selected ?>  id="pg-shipping-address-<?php echo $user_address->id; ?>" name="address[s][id]" value="<?php echo $user_address->id; ?>" class="pg-radiobutton required <?php echo $checked;?>" />
                                                    <label for="pg-shipping-address-<?php echo $user_address->id; ?>" class="pg-label">
                                                        <?php echo JText::_('PAGO_CHECKOUT_ADDRESSES_USE_THIS_ADDRESS'); ?>
                                                    </label>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_pago&view=account&checkout=1&layout=edit_address&addr_id=' . $user_address->id) ; ?>" class="pg-checkout-edit-link"><?php echo JText::_('PAGO_CHECKOUT_EDIT'); ?></a>
                                                </legend>
                                                <ul class="pg-shipping-address">
                                                    <li class="pg-shipping-address-name">
                                                        <span class="pg-shipping-address-first-name"><?php echo $user_address->first_name; ?></span> <span class="pg-shipping-address-last-name"><?php echo $user_address->last_name; ?></span>
                                                    </li>
                                                    <li class="pg-shipping-address-street">
                                                        <?php echo $user_address->address_1; ?>
                                                    </li>
                                                    <?php if( !empty( $user_address->address_2 ) ) : ?>
                                                        <li class="pg-shipping-address-street">
                                                            <?php echo $user_address->address_2; ?>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <span class="pg-shipping-address-city"><?php echo $user_address->city; ?></span>, <span class="pg-shipping-address-state"><?php echo $user_address->state;?></span> <span class="pg-shipping-address-zip"><?php echo $user_address->zip; ?></span>
                                                    </li>
                                                    <li class="pg-shipping-address-country">
                                                        <?php echo $user_address->country; ?>
                                                    </li>
                                                    <li class="pg-shipping-address-phone">
                                                        <?php echo $user_address->phone_1; ?>
                                                    </li>
                                                </ul>
                                            </fieldset>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="radio" id="pg-shipping-address-add" name="address[s][id]" value="0" class="pg-radiobutton required" /><label id="pg-shipping-address-add-label" for="pg-shipping-address-add" class="pg-label"><?php echo JText::_('PAGO_CHECKOUT_ADD_SHIPPING_ADDRESS'); ?></label>
                                    
                                    <input type="hidden" name="option" value="com_pago" />
                                    <input type="hidden" name="call" value="post" />
                                    
                                    <input type="hidden" name="guest" value="<?php echo JFactory::getApplication()->input->getInt('guest', 0) ?>">
                                    <?php echo JHTML::_( 'form.token' ) ?>
                                   
                                </form>
                                <button type="submit" class="pg-button pg-checkout-shipping-continue pg-green-background-btn pg-no-hover" id="pg-checkout-shipping-continue">
                                        <?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?>
                                    </button>
                                    <?php endif; ?>
                                <div class="pg-checkout-shipping-address-fields">
                                    <?php $this->prefix = 's'; $this->preset_number = '0'; ?>
                                    <?php if ( $add_address = PagoHelper::load_template( 'common', 'tmpl_add_address' ) ) require $add_address; ?>
                                    
                                    <br />
                                    <input checked="checked" style="display:none;" type="radio" id="pg-shipping-address-add" name="address[s][id]" value="0" class="pg-radiobutton required" />
                                    
                                    <?php if(!$this->guest): ?>
                                        <input type="checkbox" class="pg-checkbox save_address" id='save_this_address'  name="save_address" value="yes" />
                                        <label for="save_this_address" class="pg-label"><?php echo JText::_('PAGO_CHECKOUT_SAVE_SHIPPING_ADDRESS'); ?></label>
                                        <br />
                                    <?php endif ?>
                                        
                                    <button type="submit" class="pg-button pg-checkout-continue pg_checkout_save_address">
                                        <?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?>
                                    </button>
                                </div>
                               <!--  <a id="pg-checkout-express-button" class="pg-button" href="#pg-express-checkout"><?php echo JText::_('PAGO_CHECKOUT_EXPRESS_CHECKOUT_BUTTON'); ?></a> -->
                            </div>
                        </div>
                    </div>
                    <div class = "col-sm-6">
                        <?php //if ( $quickcart = PagoHelper::load_template( 'checkout', 'checkout_quickcart' ) ) require $quickcart; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class = "pg-checkout-shipping-info-panel">
            <div class = "pg-checkout-shipping-info-heading">
                <div class="pg-checkout-shipping-info-heading-title">
                    <?php echo JText::_('PAGO_ACCOUNT_REGIESTER_BILLING_DETAILS'); ?>
                </div>
                <div class="pg-checkout-billing-info-heading-change">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>
            <div class = "pg-checkout-shipping-info-content">
                <div class="pg-saved_billing_address"></div>
                <div class="billing_form"></div>

                <button type="submit" class="pg-button pg-checkout-continue pg_checkout_save_address">
                    <?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?>
                </button>
            </div>
        </div>
    </div>
    <div id="pg-checkout-shipping-method" class="grid-2">
        <div class ="pg-checkout-shipping-method-panel">
           <div class = "pg-checkout-shipping-method-heading">
                <div class="pg-checkout-shipping-method-heading-title">
                    <?php echo JText::_('PAGO_CHECKOUT_SHIPPING_METHODS_TITLE'); ?>
                </div>
                 <div class="pg-checkout-shipping-method-heading-change">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>
             <div class = "pg-checkout-shipping-method-content">
                <div class="pg-checkout-shipping-methods"></div>
                <!--<button type="submit" class="pg-button pg-checkout-continue pg_checkout_save_shipping_method">
                    <?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?>
                </button>-->
            </div>
        </div>

    </div>
    <div id="pg-checkout-payment-method" class="grid-2">
        <div class ="pg-checkout-payment-method-panel">
           <div class = "pg-checkout-payment-method-heading">
                <div class="pg-checkout-payment-method-heading-title">
                    <?php echo JText::_('PAGO_CHECKOUT_PAYMENT_METHODS_TITLE'); ?>
                </div>
                  <div class="pg-checkout-payment-method-heading-change">
                    <?php //echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>
             <div class = "pg-checkout-payment-method-content">
                <div class="pg-checkout-payment-methods"></div>
                <!--<button type="submit" class="pg-button pg-checkout-continue pg_checkout_save_shipping_method">
                    <?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?>
                </button>-->
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class = "checkout-details">
                <div class="row">
                    <div class="col-sm-7">
                       <div class = "checkout-details-desc">
                            <p>
                                <?php echo str_replace("{STORE NAME}", $this->storeName, JText::_('PAGO_CHECKOUT_DETAILS')); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class = "checkout-details-info">
                            <div class = "checkout-details-left">
                                <div class = "checkout-details-left-header">
                                    <div><strong><?php echo JText::_('PAGO_CART_SUBTOTAL'); ?>: </strong></div>
                                    <div><strong><?php echo JText::_('PAGO_CART_DISCOUNT'); ?>: </strong> </div>
                                    <div><strong><?php echo JText::_('PAGO_CART_SHIPPING_TOTAL'); ?>: </strong> </div>
                                    <div><strong><?php echo JText::_('PAGO_CART_TAX_TOTAL'); ?>: </strong> </div>
                                </div>
                                <div class = "checkout-details-left-footer">
                                    <strong><?php echo JText::_('PAGO_CART_TOTAL'); ?>: </strong>
                                </div>
                            </div>

                            <div class = "checkout-details-right">
                                <div class = "checkout-details-right-header">
                                    <div><span><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['subtotal']) ?></span></div>
                                    <div><div><span class="discountInCheckout"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['discount']) ?></span></div>
                                    <div><span class="shippingInCheckout"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['shipping']) ?></span></div>
                                    <div><span class="taxInCheckout"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['tax']) ?></span></div>
                                </div>
                                <div class = "checkout-details-right-footer">
                                    <div><span class="totalInCheckout"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['total']) ?></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load_footer();?>
