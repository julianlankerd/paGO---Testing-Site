<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php //$this->load_header();
$shipRateSele = 0; 
 ?>
<div id="pg-checkout" class="pg-step3">
    <!--<h1 class="pg-title">Checkout</h1>-->
    <?php $this->step = 2; $address_id = 0; ?>
    <?php //if ( $progress = PagoHelper::load_template( 'checkout', 'checkout_progress' ) ) require $progress; ?>
    <div id="pg-checkout-shipping-info" class="grid-2">
        <form id="pg-checkout-shipping-method-form" method="post" action="">
            <div id="pg-checkout-col1">
                <?php //if ( $quickcart = PagoHelper::load_template( 'checkout', 'checkout_quickcart' ) ) require $quickcart; ?>
            </div>
            <div id="pg-checkout-col2">
                 <?php //if ( $addresses = PagoHelper::load_template( 'checkout', 'checkout_addresses' ) ) require $addresses; ?>
                <div id="pg-checkout-shipping-options" class="pg-checkout-shipping">
                    <?php //echo JText::_('PAGO_CHECKOUT_SHIPPING_METHODS_TITLE'); ?>
                    <div id="pg-shipping-methods">
                        <?php if ($this->productBasedShipping) : ?>
                            <?php foreach ( $this->shipping_options as $product => $shipping_options ) : ?>
                            <?php if(isset($shipping_options['error']) && $shipping_options['error']) {
                                echo "<div><h2>".$shipping_options['error_message']."</h2></div>";
                                }
                                else{
                                 ?>
                            <div><?php echo $this->cart['items'][$product]->name; ?></div>
							  <?php $activeItem ='';$shipRateItemSele = 0; ?>
                            <?php foreach ( $shipping_options as $shipper => $opt ) :?>
                            <?php $checked =""; ?>
                            <?php if (count($opt) > 0) : ?>
                            <div class="pg-shipping-method">
                                <fieldset class="pg-fieldset">
                                    <legend class="pg-legend"><?php echo $shipper; ?></legend>
                                    <?php //$activeItem ='';$shipRateItemSele = 0; ?>
                                <?php foreach ($opt as $shipType => $shipping ): ?>
                                 <?php //if(count($opt) == 1) { $checked ='checked="checked"';} ?>
                                    <div class="pg-shipper-option">
									<?php $checked ="";?>
                                    <?php   ++$shipRateItemSele;$active=''; if($shipRateItemSele == '1'){$activeItem ='active';$checked ='checked="checked"';}  ?>
                                        <input type="radio" value="<?php echo $shipper; ?>|<?php echo $shipType; ?>|<?php echo $shipping['name']; ?>|<?php echo $shipping['value']; ?>" name="carrier_option[<?php echo $product; ?>]"  id="<?php echo $shipType . $product. @$shipRateItemSele; ?>" class="pg-radiobutton required <?php echo $activeItem;?>" <?php echo $checked;?> >
                                        <label for="<?php echo $shipType . $product . @$shipRateItemSele; ?>" class="pg-label">
                                            <span class="pg-shipper-option-name"><?php echo $shipping['name']; ?></span> <span class="pg-shipper-option-price">( $<?php echo number_format($shipping['value'],2); ?> )</span>
                                        </label>
                                        <input type="hidden" name="product_id<?php echo $product ?>" value="<?php echo $product ?>" id="product_id<?php echo $product ?>" >
                                    </div>
                                <?php endforeach; ?>
                                </fieldset>
                            </div>
                            <?php endif ?>
                            <?php endforeach ?>
                            <?php }?>
                            <?php endforeach ?>
                            <input type="hidden" name="total_products_cart" value="<?php echo count($this->cart['items']); ?>" id="total_products_cart" >

                        <?php else: ?>
                            <?php foreach ( $this->shipping_options as $shipper => $opt ) :?>
                            
                            <?php if(isset($opt['error']) && $opt['error'] ){
                                echo "<div><h2>".$opt['error_message']."</h2></div>";
                                }
                                else{
                                 ?>
                            <?php if (count($opt) > 0) : ?>
                            <div class="pg-shipping-method">
                                <fieldset class="pg-fieldset">
                                    <legend class="pg-legend"><?php echo $shipper; ?></legend>
                                <?php foreach ($opt as $shipType => $shipping ): 
									  $checked = '';
								?>
                                    <div class="pg-shipper-option">
                                    <?php  ++$shipRateSele;$active=''; if($shipRateSele == '1'){$active ='active';$checked ='checked="checked"';} ?>
                                        <input type="radio" value="<?php echo $shipper; ?>|<?php echo $shipType; ?>|<?php echo $shipping['name']; ?>|<?php echo $shipping['value']; ?>" name="carrier_option" 0="" id="<?php echo $shipType . @$counter; ?>" class="pg-radiobutton required <?php echo $active;?>" <?php echo $checked;?>>
                                        <label for="<?php echo $shipType . @$counter; ?>" class="pg-label">
                                            <span class="pg-shipper-option-name"><?php echo $shipping['name']; ?></span> <span class="pg-shipper-option-price">( $<?php echo number_format($shipping['value'],2); ?> )</span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                </fieldset>
                            </div>
                            <?php endif;?>
                            <?php } ?>
                            <?php endforeach; ?>
                       <?php endif;?>
                        <input type="hidden" id="productBasedShipping" name="productBasedShipping" value="<?php echo $this->productBasedShipping; ?>" />
                        <input type="hidden" name="guest" value="<?php echo JFactory::getApplication()->input->getInt( 'guest', 0 ) ?>">
                        <?php JHTML::_( 'form.token ' ) ?>
                        <button type="button" class="pg-button pg-btn-green pg-checkout-set-shipping-continue"><?php echo JText::_('PAGO_CHECKOUT_CONTINUE'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php //$this->load_footer() ?>
