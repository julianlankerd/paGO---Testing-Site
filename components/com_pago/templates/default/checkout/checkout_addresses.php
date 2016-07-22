<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php //$step = $this->session->get('checkout_step', false, 'pago_cart'); ?>
<div id="pg-checkout-mini-addresses">
    <h3><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_SUMMARY'); ?></h3>
    <div class="clearfix">
        <div class="pg-checkout-billing-address">
            <h4><?php echo JText::_('PAGO_CHECKOUT_BILLING_ADDRESS'); ?><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=checkout&task=address&guest=' . JFactory::getApplication()->input->getInt( 'guest', 0 ) ); ?>" class="pg-checkout-edit-link"><?php echo JText::_( 'PAGO_CHECKOUT_EDIT' ); ?></a></h4>
            <ul class="pg-billing-address">
                <li class="pg-billing-address-name"><span class="pg-billing-address-first-name"><?php echo $this->billing_address->first_name; ?></span> <span class="pg-shipping-address-last-name"><?php echo $this->billing_address->last_name; ?></span></li>
                <li class="pg-billing-addressreet"><?php echo $this->billing_address->address_1; ?></li>
                <?php if($this->billing_address->address_2) : ?><li class="pg-billing-address-street"><?php echo $this->billing_address->address_2; ?></li><?php endif; ?>
                <li><span class="pg-billing-address-city"><?php echo $this->billing_address->city; ?></span>, <span class="pg-shipping-address-state"><?php echo $this->billing_address->state;?></span> <span class="pg-shipping-address-zip"><?php echo $this->billing_address->zip; ?></span></li>
                <li class="pg-billing-address-country"><?php echo $this->billing_address->country; ?></li>
                <li class="pg-billing-address-phone"><?php echo $this->billing_address->phone_1; ?></li>
            </ul>
         </div>
        <div class="pg-checkout-shipping-address">
        	<h4><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_ADDRESS'); ?><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=checkout&task=address&guest=' . JFactory::getApplication()->input->getInt( 'guest', 0 ) ); ?>" class="pg-checkout-edit-link"><?php echo JText::_( 'PAGO_CHECKOUT_EDIT' ); ?></a></h4>
            <ul class="pg-shipping-address">
                <li class="pg-shipping-address-name"><span class="pg-shipping-address-first-name"><?php echo $this->shipping_address->first_name; ?></span> <span class="pg-shipping-address-last-name"><?php echo $this->shipping_address->last_name; ?></span></li>
                <li class="pg-shipping-addressreet"><?php echo $this->shipping_address->address_1; ?></li>
                <?php if($this->shipping_address->address_2) : ?><li class="pg-shipping-address-street"><?php echo $this->shipping_address->address_2; ?></li><?php endif; ?>
                <li><span class="pg-shipping-address-city"><?php echo $this->shipping_address->city; ?></span>, <span class="pg-shipping-address-state"><?php echo $this->shipping_address->state;?></span> <span class="pg-shipping-address-zip"><?php echo $this->shipping_address->zip; ?></span></li>
                <li class="pg-shipping-address-country"><?php echo $this->shipping_address->country; ?></li>
                <li class="pg-shipping-address-phone"><?php echo $this->shipping_address->phone_1; ?></li>
            </ul>
            <?php if( isset($this->shipper) ): ?>
            <h4><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_METHOD'); ?><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=checkout&task=shipping&guest=' . JFactory::getApplication()->input->getInt( 'guest', 0 ) ); ?>" class="pg-checkout-edit-link"><?php echo JText::_( 'PAGO_CHECKOUT_EDIT' ); ?></a></h4>
            <p><span class="pg-shipper-name"><?php echo $this->shipper['name']; ?></span></p>
            <?php endif; ?>
        </div>
	</div>
</div>
