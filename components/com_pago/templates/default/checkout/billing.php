<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php //$this->load_header() ?>
<div id="pg-checkout" class="pg-step3">
    <!--<h1 class="pg-title">Checkout</h1>-->
    <?php $this->step = 3; ?>
    <?php //if ( $progress = PagoHelper::load_template( 'checkout', 'checkout_progress' ) ) require $progress; ?>
    <div id="pg-checkout-billing-info" class="grid-2">
        <form id="pg-checkout-billing-payment-form" action="<?php JRoute::_('index.php'); ?>" method="post" class="checkoutBilling"  >
            <div id="pg-checkout-col1">
                <?php //if ( $quickcart = PagoHelper::load_template( 'checkout', 'checkout_quickcart' ) ) require $quickcart; ?>
                <?php //if ( $addresses = PagoHelper::load_template( 'checkout', 'checkout_addresses' ) ) require $addresses; ?>
            </div>
            <div id="pg-checkout-col2">
                <div class="pg-checkout-billing-details">
                    <!--<h3><?php //echo JText::_('PAGO_ACCOUNT_REGIESTER_BILLING_DETAILS'); ?></h3>-->
                    <div>
                        <?php $this->prefix = 'b'; $this->preset_number = '1'; ?>
                        <!--<label for="sameasshipping" class="pg-label"><strong>
                            <?php echo JText::_('PAGO_ACCOUNT_REGISTER_SAME_AS_SHIPPING'); ?></strong>
                        </label><input type="radio" value="yes" name="sameasshipping" class="pg-radiobutton" />Yes <input type="radio" value="no" name="sameasshipping" checked="checked" class="pg-radiobutton" />No
                        <div id="pg-checkout-billing-fields">
                            <?php if ( $user_fields = PagoHelper::load_template( 'common', 'tmpl_user_fields' ) ) require $user_fields; ?>
                        </div>-->
                        <?php  $total_amount = $this->cart['total'];?>
                        <?php if(count($this->payment_options) > 0 && $total_amount > 0 ) : ?>
                        <div id="pg-payment-methods">
                            <h4 class="pg-title">Please choose your Payment Method</h4>
                            <div class="pg-payment-methods clearfix">
                             <?php
                                $CardStyle = "style = 'display:none'";
                                $style = '';
                                $payCheckedStyle ='';
                                if(count($this->payment_options) == 1)
                                {
                                    foreach( $this->payment_options as $gateway => $gateway_option )
                                    {
                                        $style= "style= 'display:none'";
                                        
                                        //check if new payment system
                        				if(strstr($gateway, 'pago_')){
                        					$id = str_replace('pago_', '', $gateway);
                        					$pluginParams = Pago::get_instance('params')->get('paygates.'.$id);
                        					$credit_card =  $pluginParams->data->creditcard;
                        				} else {
                                            $plugin = JPluginHelper::getPlugin('pago_gateway', $gateway);
                                            $pluginParams = new JRegistry($plugin->params);
                                            $credit_card = $pluginParams->get('creditcard', '0');
                        				}
                                        $payCheckedStyle = "checked = checked";
                                        if($credit_card)
                                        {
                                            $CardStyle = "";
                                        }
                                    }
                                }
                                else if(count($this->payment_options) > 1)
                                {
                                    foreach( $this->payment_options as $gateway => $gateway_option )
                                    {
                                        //check if new payment system
                        				if(strstr($gateway, 'pago_')){
                        					$id = str_replace('pago_', '', $gateway);
                        					$pluginParams = Pago::get_instance('params')->get('paygates.'.$id);
                        					$credit_card =  $pluginParams->data->creditcard;
                        				} else {
                                            $plugin = JPluginHelper::getPlugin('pago_gateway', $gateway);
                                            $pluginParams = new JRegistry($plugin->params);
                                            $credit_card = $pluginParams->get('creditcard', '0');
                        				}
                                
                                        if($credit_card)
                                        {
                                            $CardStyle = "";
                                        }
                                    }
                                }
                            ?>
                            <?php $activeItem ='';$paymentSele = 0; ?>
                            <?php foreach( $this->payment_options as $gateway => $gateway_option ) : ?>
                            <?php $checked ="";?>
                            <?php   ++$paymentSele;$active=''; if($paymentSele == '1'){$activeItem ='active';$checked ='checked="checked"';}  ?>
                                 
                                 <div class="pg-payment-method default">

                                        <input id="pg-<?php echo $gateway; ?>" type="radio"  class="pg-radiobutton required <?php echo $activeItem;?>" <?php echo $checked;?> name="payment_option" value="<?php echo $gateway; ?>" onChange="getCreditcardForm(this.value);" <?php echo $style;?> <?php echo $payCheckedStyle;?> />
                                        
                                        <?php //echo $gateway_option['name']; ?>                                        
                                       
                                       
                                       <label for="pg-<?php echo $gateway; ?>" class="pg-label">                                      
                                            
                                       
                                            <!-- <span class="pg-payment-option-name"><?php echo $gateway_option['name']; ?> -->
                                                <!-- <img src="components/com_pago/templates/default/images/payment/icon-authorize.jpg" alt="authorize"> -->
                                                
                                                <?php if(strstr($gateway, 'pago_')): ?>
                                                    <img src="<?php echo $gateway_option['logo']; ?>" alt="<?php echo $gateway_option['name']; ?>" title="<?php echo $gateway_option['name']; ?>">
                                                <?php else: ?>
                                                    <img src="<?php echo JURI::base();?>/plugins/pago_gateway/<?php echo $gateway;?>/icon.jpg" alt="<?php echo $gateway_option['name']; ?>" title="<?php echo $gateway_option['name']; ?>">
                                                <?php endif ?>
                                            
                                            
                                            <!-- </span> -->
                                       
                                        </label>
                                    </div>


                                 <?php endforeach; ?>
                            </div>
                            <div class="pg-checkout-payment-method">
                                 <div id="creditCardForm" <?php echo $CardStyle;?> >
                                <fieldset class="pg-fieldset">
                                    <legend class="pg-legend">Credit/debit card payment options</legend>
                                   
                                    Note that billing address details are used for Card Verification routines 
                                    so your billing address should reflect that of the card holder.
                                 
                                    <label for="pg-checkout-cc-number" class="pg-label">Credit/debit card number</label>
                                    <input id="pg-checkout-cc-number" type="text" name="cc_cardNumber" value="" class="pg-inputbox required creditcard" autocomplete="off"/>
                                    
                                    <label for="pg-checkout-cc-expire-month" class="pg--label">Expiry date month</label>
                                    <select id="pg-checkout-cc-expire-month" name="cc_expirationDateMonth" class="pg-selectbox creditcardmonth">
                                    <?php for ($m = 1; $m <= 12; $m++):
                                        $s = mktime( 0, 0, 0, 0 + $m, 1, date( "y" ) );
                                        $selected = false;
                                        if( str_pad( $m, 2, '0', STR_PAD_LEFT ) == JFactory::getApplication()->input->get( 'sel_Expirydatemonth' ) ){
                                            $selected = 'selected="selected"';
                                        } ?>
                                        <option <?php echo $selected ?> value="<?php echo date("m", $s) ?>"><?php echo JText::_( date("F", $s) ) ?></option>
                                    <?php endfor ?>
                                    </select>
                                    
                                    <label for="pg-checkout-cc-expire-year" class="pg-label">Expiry date year</label>
                                    <select id="pg-checkout-cc-expire-year" name="cc_expirationDateYear" class="pg-selectbox creditcardmonth">
                                    <?php for ($i = 0; $i <= 10; $i++): 
                                        $year = date( 'Y', strtotime( "now +{$i} years" ) );
                                        $selected = false;
                                        if( $year == JFactory::getApplication()->input->get( 'sel_Expirydateyear' ) ){
                                            $selected = 'selected="selected"';
                                        } ?>
                                        <option <?php echo $selected ?> value="<?php echo $year ?>"><?php echo $year ?></option>                            
                                    <?php endfor ?>
                                    </select>
                                    
                                    <label for="pg-checkout-cc-cv2code" class="pg-label">CV2 (3 digit security code on back of card)</label>
                                    <input id="pg-checkout-cc-cv2code" type="password" name="cc_cv2code" value="" autocomplete="off" class="pg-inputbox required"/>
                                </fieldset>
                                </div>
                        
                            </div>
                            </div>

                        <input type="hidden" name="option" value="com_pago" />
                        <input type="hidden" name="view" value="checkout" />
                        <input type="hidden" name="task" value="process" />
                        <input type="hidden" name="guest" value="<?php echo $this->guest; ?>">
                        <input type="hidden" name="nopayment" value="0">
                        <?php echo JHTML::_( 'form.token' ) ?>
                         <?php 
                        JHTML::_('behavior.modal');
                        $url            = JURI::base();
                        $congig_link   = $url . "index.php?option=com_pago&view=checkout&&task=terms&tmpl=component";
                        $termscondition = '<input type="checkbox" id="termscondition" name="termscondition" value="1" class="required" />';
                        echo $termscondition .= ' <a class="modal" href="' . $congig_link . '" rel="{handler: \'iframe\', size: {x:550, y:400}}">' . JText::_('COM_PAGO_TERMS_AND_CONDITIONS_FOR_LBL') . '</a>';
                                ?>
                        <br />
                        <div class="pg-cart-qty-update-message-wrapper">
            <div class="pg-cart-qty-update-message" style="display: none;"><?php echo JTEXT::_('PAGO_WARNING_CHOOSE_PAYMENT_METHOD'); ?><div class="pg-addtocart-success-block-close"></div></div>
        </div>
                        <button type="submit" class="pg-button pg-checkout-continue"><?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?></button>
                    </div>
                    <?php elseif($total_amount == 0):?>
                    <div>
                        <div><?php echo JText::_('PAGO_ORDER_IS_FREE_MESSAGE');?></div>
                        <input type="hidden" name="option" value="com_pago" />
                        <input type="hidden" name="view" value="checkout" />
                        <input type="hidden" name="task" value="process" />
                        <input type="hidden" name="guest" value="<?php echo $this->guest; ?>">
                        <input type="hidden" name="nopayment" value="1">
                        <?php echo JHTML::_( 'form.token' ) ?>
                         <?php 
                        JHTML::_('behavior.modal');
                        $url            = JURI::base();
                        $congig_link   = $url . "index.php?option=com_pago&view=checkout&&task=terms&tmpl=component";
                        $termscondition = '<input type="checkbox" id="termscondition" name="termscondition" value="1" class="required" />';
                        echo $termscondition .= ' <a class="modal" href="' . $congig_link . '" rel="{handler: \'iframe\', size: {x:550, y:400}}">' . JText::_('COM_PAGO_TERMS_AND_CONDITIONS_FOR_LBL') . '</a>';
                                ?>
                        <br />
                        <div class="pg-cart-qty-update-message-wrapper">
                            <div class="pg-cart-qty-update-message" style="display: none;"><?php echo JTEXT::_('PAGO_WARNING_CHOOSE_PAYMENT_METHOD'); ?><div class="pg-addtocart-success-block-close"></div></div>
                        </div>
                        <button type="submit" class="pg-button pg-checkout-continue"><?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?></button>
                    </div>
                    <?php else: ?>
                            <div id="pg-payment-methods">
                                <div class="pg-checkout-payment-method">
                                <div id="pg-checkout-payment-error-message"><strong>
                            <div class = "modal fade" aria-hidden="true" role="dialog" id="contact_info_modal">
                                <div class="contact_info_title">
                                    <?php echo JTEXT::_('PAGO_CHECKOUT_PAYMENT_ERROR_FORM_TITLE'); ?>
                                    <a href = "javascript:void(0)" class="contact_info_modal_close"></a>
                                </div>
                                <div class="modal-body">
                                </div>
                            </div>
                                <?php 
                                    
                                //echo '<a rel="" class="pg-title-button pg-button-new pg-green-text-btn green-btn" data-toggle="modal" data-target="#contact_info_modal" onclick="pull_upload_checkout_error();" href="javascript:void(0);">' . JTEXT::_('COM_PAGO_CONTACT_FOR_MORE_INFO') . '</a>';
                                echo JText::_('COM_PAGO_PAYMENT_METHOD_ERROR');
                                echo "<br/>";
                                echo '<button rel="" class="pg-contact-merchant pg-title-button pg-button-new pg-btn-green" data-toggle="modal" data-target="#contact_info_modal" onclick="pull_paymentErrorForm();" >' . JTEXT::_('COM_PAGO_CLICK_FOR_MORE_INFO') . '</button>';
                                
                                ?></strong></div>
                                </div>
                            </div>

                    <?php endif;?>
                </div>
            </div>
        </form>
    </div>
</div>
<?php //$this->load_footer() ?>
