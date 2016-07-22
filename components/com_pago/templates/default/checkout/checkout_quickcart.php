<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php $step = Pago::get_instance('cookie')->get('checkout_previous_step'); ?>
<div id="pg-checkout-cart">
    <h3><?php echo JText::_('PAGO_CHECKOUT_ORDER_SUMMARY'); ?></h3>
    <a href="<?php echo JRoute::_('index.php?option=com_pago&view=cart'); ?>" class="pg-checkout-edit-link"><?php echo JText::_('PAGO_CHECKOUT_QUICK_CART_VIEW_CART'); ?></a>

    <div class="clearfix">
        <div class="pg-quick-cart-totals">
            <table class="pg-quick-cart-table pg-table">
                <tbody>
                    <tr class="pg-quick-cart-subtotal">
                        <th class="pg-cart-subtotal"><?php echo JText::_('PAGO_CHECKOUT_QUICK_CART_SUBTOTAL'); ?></th>
                        <td><?php echo $this->cart['format']['subtotal'] ?></td>
                    </tr>
					<?php
					if ( isset($this->cart['format']['discount']) && $this->cart['format']['discount'] !== '$0.00') :
					?>
                	<tr class="pg-quick-cart-discount">
                    	<th class="pg-cart-discount"><?php echo JText::_('PAGO_CART_DISCOUNT'); ?></td>
                        <td class="pg-cart-total"><?php echo $this->cart['format']['discount'] ?></td>
                    </tr>
					<?php endif; ?>
                    <tr class="pg-quick-cart-shipping">
                        <th class="pg-cart-shipping"><?php echo JText::_('PAGO_CHECKOUT_QUICK_CART_SHIPPING_TOTAL'); ?></th>
                        <td><?php echo $this->cart['format']['shipping'] ?></td>
                    </tr>
                    <tr class="pg-quick-cart-tax">
                        <th class="pg-cart-shipping"><?php echo JText::_('PAGO_CHECKOUT_QUICK_CART_TAX_TOTAL'); ?></th>
                        <td><?php echo $this->cart['format']['tax'] ?></td>
                    </tr>
                    <tr class="pg-quick-cart-order-total">
                        <th class="pg-cart-shipping"><?php echo JText::_('PAGO_CHECKOUT_QUICK_CART_ORDER_TOTAL'); ?></th>
                        <td><?php echo $this->cart['format']['total'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="display: none;" class="pg-quick-cart-contents">
            <table class="pg-quick-cart-table pg-table">
                <thead>
                    <tr>
                        <th class="pg-cart-item"><?php echo JText::_('PAGO_QUICK_CART_ITEM_DESC'); ?></th>
                        <th class="pg-cart-item-price"x><?php echo JText::_('PAGO_QUICK_CART_ITEM_TOTAL'); ?></th>
                    </tr>
                </thead>
                <tbody>
					<?php foreach($this->cart['items'] as $item): ?>
                    <tr class="pg-cart-item">
						<td class="pg-item-description">
                            <div class="pg-cart-item-name">
							<?php
								 $Itemid = $this->nav->getItemid($item->id, $item->cid);
								 $link = JRoute::_('index.php?option=com_pago&view=item&id=' . $item->id . '&cid=' . $item->cid .'&Itemid=' . $Itemid);
							?>
								<a href="<?php echo $link; ?>"><?php echo $item->name; ?></a>
                            </div>
                            <div class="pg-cart-item-details clearfix">
                                <a href="<?php echo $this->nav->build_url('item', $item->id) ?>">
                                    <?php template_functions::display_image($item->images, 'thumbnail', $item->name, $item->name); ?>
                                </a>
                                <ul>
                                    <li><span><?php echo JText::_('PAGO_QUICK_CART_ITEM_QTY'); ?>:</span> <?php echo $item->cart_qty; ?></li>
                                    <li><span><?php echo JText::_('PAGO_QUICK_CART_ITEM_PRICE'); ?>:</span> <?php echo $item->format_price; ?></li>
                                </ul>
							</div>
                        </td>
                        <td class="pg-cart-item-total"><?php echo $item->format_subtotal; ?></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
	</div>
    <a href="#" id="pg-checkout-quick-cart-show-items" class=""><?php echo JText::_('PAGO_CHECKOUT_QUICK_CART_SHOW_ITEMS'); ?></a>
</div>
