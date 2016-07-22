<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');?>
<div class="pg-cart-shipping-estimation-div">
<div>&nbsp;</div>
<?php if(count($this->lowest_rates) > 0){?>
        <div>
	       <div class="pg-cart-shipping-estimation-info">
                <?php if($config->get('checkout.shipping_type')) : ?>
				 <h3><?php echo JText::_('PAGO_CART_SHIPPING_ESTIMATION'); ?></h3>
                    <?php foreach($this->lowest_rates as $product => $lowest_rate) : ?>
						<div class="cart_ship_prd_rate">
                        <div class="cart_shi_prd"><?php echo JText::_('PAGO_CART_SHIPPING_PRODUCT'); ?><strong><?php echo $product ?></strong></div>
                        <div class="cart_shi_rate"><?php echo $lowest_rate['name']?> ($<?php echo number_format($lowest_rate['value'],2); ?>) </div>
						</div>
                    <?php endforeach; ?>
                <?php else : ?>
						<div class="cart_ship_prd_rate">
						<?php if(isset($this->lowest_rates['value'])) { ?>
						 <h3><?php echo JText::_('PAGO_CART_SHIPPING_ESTIMATION'); ?></h3>
                        <div class="cart_shi_rate"><?php echo $this->lowest_rates['name']?> ($<?php echo number_format($this->lowest_rates['value'],2); ?>) </div>
						<?php }
						else
						{
							echo JTEXT::_('COM_PAGO_SHIPPING_NOTICE');
						}
						?>
						</div>
                <?php endif; ?>
           </div>
        </div>
	<?php }
	else
	{
		echo JTEXT::_('COM_PAGO_SHIPPING_NOTICE');
	}
	?>
</div>
