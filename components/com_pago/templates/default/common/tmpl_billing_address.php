<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();

$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.chained.mini.js' );
$setShipSelected = '0';
?>
							<div class="pg-checkout-billing-details">
                            <!--<h3><?php //echo JText::_('PAGO_ACCOUNT_REGIESTER_BILLING_DETAILS'); ?></h3>-->
                            <div>
                                <form id="pg-checkout-billing-form" action="<?php echo JRoute::_('index.php'); ?>" method="post">
                                <?php if( !empty( $this->saved_addresses ) ) : ?>
                                    <div class="pg-checkout-billing-addresses clearfix">
                                        <h4><?php echo JText::_('PAGO_CHECKOUT_SELECT_BILLING_ADDRESS'); ?></h4>
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
                                        <div class="pg-checkout-billing-address">
                                            <fieldset class="pg-fieldset pg-billing-address-fieldset">
                                                <legend class="pg-legend">
                                                    <input type="radio" <?php echo $selected ?>  id="pg-billing-address-<?php echo $user_address->id; ?>" name="address[b][id]" value="<?php echo $user_address->id; ?>" class="pg-radiobutton required <?php echo $checked;?>" />
                                                    <label for="pg-billing-address-<?php echo $user_address->id; ?>" class="pg-label">
                                                        <?php echo JText::_('PAGO_CHECKOUT_ADDRESSES_USE_THIS_ADDRESS'); ?>
                                                    </label>
                                                    <a href="<?php echo JRoute::_('index.php?option=com_pago&view=account&checkout=1&layout=edit_address&addr_id=' . $user_address->id) ; ?>" class="pg-checkout-edit-link"><?php echo JText::_('PAGO_CHECKOUT_EDIT'); ?></a>
                                                </legend>
                                                <ul class="pg-billing-address">
                                                    <li class="pg-billing-address-name">
                                                        <span class="pg-billing-address-first-name"><?php echo $user_address->first_name; ?></span> <span class="pg-billing-address-last-name"><?php echo $user_address->last_name; ?></span>
                                                    </li>
                                                    <li class="pg-billing-address-street">
                                                        <?php echo $user_address->address_1; ?>
                                                    </li>
                                                    <?php if( !empty( $user_address->address_2 ) ) : ?>
                                                        <li class="pg-billing-address-street">
                                                            <?php echo $user_address->address_2; ?>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <span class="pg-billing-address-city"><?php echo $user_address->city; ?></span>, <span class="pg-billing-address-state"><?php echo $user_address->state;?></span> <span class="pg-billing-address-zip"><?php echo $user_address->zip; ?></span>
                                                    </li>
                                                    <li class="pg-billing-address-country">
                                                        <?php echo $user_address->country; ?>
                                                    </li>
                                                    <li class="pg-billing-address-phone">
                                                        <?php echo $user_address->phone_1; ?>
                                                    </li>
                                                </ul>
                                            </fieldset>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="radio" id="pg-billing-address-add" name="address[b][id]" value="0" class="pg-radiobutton required" /><label id="pg-billing-address-add-label" for="pg-billing-address-add" class="pg-label"><?php echo JText::_('PAGO_CHECKOUT_ADD_BILLING_ADDRESS'); ?></label>
                                    <?php endif; ?>
                                    <input type="hidden" name="guest" value="<?php echo JFactory::getApplication()->input->getInt('guest', 0) ?>">
                                    <?php echo JHTML::_( 'form.token' ) ?>

                                </form>
                                	<div class="pg-checkout-billing-address-fields">

                                    <?php $this->prefix = 'b'; $this->preset_number = '1'; ?>

                                    <?php if ( $add_address = PagoHelper::load_template( 'common', 'tmpl_add_address' ) ) require $add_address; ?>
                                 </div>
                            </div>
                        </div>