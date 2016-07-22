<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php $this->load_header(); 
$return = base64_encode( JURI::current() );

$security_level = $this->config->get( 'checkout.checkout_register_security', 0 );

if($security_level == 1){
    JPluginHelper::importPlugin('captcha');
    $dispatcher = JDispatcher::getInstance();
    $dispatcher->trigger('onInit','pago_recaptcha_placeholder');
}
?>
<!-- added because joomla captch implementation can't be initiated without
showing the captcha... silly -->
<?php if($security_level == 1): ?>
<div style="display:none">
    <div id="pago_recaptcha_placeholder"></div>
</div>
<?php endif ?>

<div id="pg-checkout" class="pg-step1">
    <div class="pg-title">
        <h1><?php echo JTEXT::_('PAGO_CART_CHECKOUT'); ?></h1>
    </div>
    <div class="pg_checkout_notice"></div>

    <div class="pg-checkout-panel-group">
        <div class = "pg-checkout-panel pg-checkout-options">
            <div class="pg-checkout-heading clearfix">
                <div class="pg-checkout-heading-title">
                    <?php echo JText::_('PAGO_CHECKOUT_CHECKOUT_OPTION'); ?>
                </div>
                <div class="pg-checkout-heading-change">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>
            <div class="pg-checkout-content">
                <div class = "row">
                    <div class = "col-sm-6">
                        <div class = "pg-checkout-option-left">
                            <span class = "pg-checkout-options-title">
                                <?php echo JTEXT::_('PAGO_CHECKOUT_CHECKOUT_OPTION_NEW_CUSTOMER')?>
                            </span>

                            <span class = "pg-checkout-options-text">
                                <?php echo JText::_('PAGO_CHECKOUT_CHECKOUT_OPTION'); ?>
                            </span>
                            <div id="pg-checkout-account-action" class="clearfix">
                                <div class="pg_checkout_guest_forum">
                                    <input type="radio" name="checkout_user_type" id ="user_register" value="register" checked="checked" class="active" />
                                    <label for="user_register" class = "css-label"><?php echo JText::_('PAGO_CHECKOUT_REGISTER_ACCOUNT'); ?></label></br>
                                    <?php if ( !$this->config->get( 'checkout.force_checkout_register', 0 ) ) : ?>
                                    <input type="radio" name="checkout_user_type" id="user_guest" value="guest" />
                                    <label for="user_guest" class = "css-label"><?php echo JText::_('PAGO_CHECKOUT_CHECKOUT_AS_GUEST'); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php echo JTEXT::_('PAGO_CHECKOUT_CHECKOUT_INFO')?>

                            <div class="pg_checkout_continue_container">
                                <button type="button" class="pg_checkout_guest_continue" class="pg-button"><?php echo JText::_('PAGO_CHECKOUT_CONTINUE_BUTTON'); ?></button>
                            </div>
                        </div>
                    </div>

                    <div class = "col-sm-6">
                        <div class = "pg-checkout-option-right">
                            <span class = "pg-checkout-options-title">
                                <?php echo JTEXT::_('PAGO_CHECKOUT_CHECKOUT_OPTION_RETURNING_CUSTOMER')?>
                            </span>

                             <span class = "pg-checkout-options-text">
                                <?php echo JText::_('PAGO_CHECKOUT_CHECKOUT_OPTION_ALREADY_USER'); ?>
                            </span>

                            <div class = "pg-login-form">
                                <div class="pg-module pg-login pg-main-container">
                                    <div class='pg-login-notice'></div>
                                    <form method="post" name="login" class="pg-login-form" >
                                        <p class="pg-login-form-username">
                                            <input type="text" name="username" placeholder="<?php echo JText::_('PAGO_EMAIL'); ?>" class="pg-inputbox" alt="username" size="18" />
                                        </p>
                                        <p class="pg-login-form-password">
                                            <input type="password" name="password" placeholder="<?php echo JText::_('PAGO_PASSWORD') ?>" class="pg-inputbox" size="18" alt="password" />
                                        </p>

                                        <?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
                                            <p class="pg-login-form-remember">
                                                <input id="pg-login-remember" type="checkbox" name="remember" class="pg-checkbox" value="yes" alt="<?php echo JText::_('PAGO_REMEMBER_ME'); ?>" />
                                                <label for="pg-login-remember"><?php echo JText::_('PAGO_REMEMBER_ME'); ?></label>
                                            </p>
                                        <?php endif; ?>

                                        <input type="submit" name="Submit" class="pg-button" value="<?php echo JText::_('PAGO_LOGIN') ?>" />

                                        <?php
                                        $usersConfig = JComponentHelper::getParams('com_users');
                                        if ($usersConfig->get('allowUserRegistration')) : ?>
                                        <div class = "pg-login-create-account">
                                            <a href="<?php echo JRoute::_('index.php?option=com_pago&view=register'); ?>">
                                                <?php echo JText::_('PAGO_REGISTER'); ?>
                                            </a>
                                        </div>
                                        <?php endif; ?>

                                        <input type="hidden" name="option" value="com_users" />
                                        <input type="hidden" name="task" value="user.login" />
                                        <input type="hidden" name="return" value="<?php echo $return; ?>" />
                                        <?php echo JHTML::_( 'form.token' ); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class = "pg-checkout-panel pg-checkout-register">
            <div class="pg-checkout-heading clearfix">
                <div class="pg-checkout-heading-title">
                    <?php echo JText::_('PAGO_CHECKOUT_REGISTRATION_FORM'); ?>
                </div>
                <!-- <div class="pg-checkout-heading-change">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div> -->
            </div>

            <div class="pg-checkout-content">
                <div class="pg_user_register"></div>

                <div class="pg_checkout_continue_container">
                    <button type="button" class="pg_checkout_guest_continue" class="pg-button"><?php echo JText::_('PAGO_CHECKOUT_CONTINUE_BUTTON'); ?></button>
                </div>
            </div>
        </div>

        <div class = "pg-checkout-panel pg-checkout-register-guest hide-change">
            <div class="pg-checkout-heading clearfix">
                <div class="pg-checkout-heading-title">
                    <?php echo JText::_('PAGO_CHECKOUT_SHIPPING_INFORMATION'); ?>
                </div>
                <div class="pg-checkout-heading-change">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>

            <div class="pg-checkout-content">
                <div class="pg_user_guest"></div>
            </div>
        </div>

        <div class = "pg-checkout-panel pg-checkout-billing hide-change">
            <div class="pg-checkout-heading clearfix">
                <div class="pg-checkout-heading-title">
                    <?php echo JTEXT::_('PAGO_ACCOUNT_REGIESTER_BILLING_DETAILS'); ?>
                </div>
                <div class="pg-checkout-heading-change">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>
            <div class="pg-checkout-content">
                <div class="billing_form"></div>

                <button type="submit" class="pg-button pg-checkout-continue pg_checkout_save_address">
                    <?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?>
                </button>
            </div>
        </div>
        <div class = "pg-checkout-panel pg-checkout-shipping-method hide-change">
            <div class="pg-checkout-heading clearfix">
                <div class="pg-checkout-heading-title">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_SHIPPING_METHODS_TITLE'); ?>
                </div>
                <div class="pg-checkout-shipping-method-heading-change">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>
            <div class="pg-checkout-content">
                <div class="pg-checkout-shipping-methods"></div>
            </div>
        </div>

         <div class = "pg-checkout-panel pg-checkout-payment-method hide-change">
            <div class="pg-checkout-heading clearfix">
                <div class="pg-checkout-heading-title">
                    <?php echo JTEXT::_('PAGO_CHECKOUT_PAYMENT_METHODS_TITLE'); ?>
                </div>
                <div class="pg-checkout-payment-method-heading-change">
                    <?php //echo JTEXT::_('PAGO_CHECKOUT_CHANGE'); ?>
                </div>
            </div>
            <div class="pg-checkout-content">
                <div class="pg-checkout-payment-methods"></div>
            </div>
        </div>
        <!-- this is hidden form start -->
        <?php if ( !$this->config->get( 'checkout.force_checkout_register', 0 ) ) : ?>
            <div id="pg-account-guest">
                <div>
                    <form id="pg-checkout-guest-checkout-form" action="<?php echo JRoute::_( 'index.php' ); ?>" method="get">
                        <input type="hidden" name="option" value="com_pago" />
                        <input type="hidden" name="view" value="checkout" />
                        <input type="hidden" name="task" value="address" />
                        <input type="hidden" name="guest" value="1" />
                        <?php echo JHTML::_( 'form.token' ) ?>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <!-- this is hidden form end -->
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
                                        <div><span class="discountInCheckout"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['discount']) ?></span></div>
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
</div>
<?php $this->load_footer(); ?>
