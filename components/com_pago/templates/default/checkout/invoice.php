<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>

<table width="100%" cellspacing="2" cellpadding="4" border="0">
    <tbody>
        <tr align="left" class="sectiontableheader">
            <th><?php echo JText::_( 'PAGO_CART_NAME' ) ?></th>
            <th><?php echo JText::_( 'PAGO_CART_SKU' ) ?></th>
            <th><?php echo JText::_( 'PAGO_CART_PRICE' ) ?></th>
            <th><?php echo JText::_( 'PAGO_CART_QTY' ) ?></th></th>
            <th  align="right"><?php echo JText::_( 'PAGO_CART_TOTALS' ) ?></th></th>
        </tr>
        
        <?php foreach($this->cart as $item): ?>
        <tr valign="top">
            <td><a href="<?php echo JRoute::_( 'index.php?view=item&id=' . $item->id ) ?>"><strong><?php echo $item->name ?></strong></a><br></td>
            <td><?php echo $item->sku ?></td>
            <td><?php echo $item->currency_symbol . $item->currency . $item->price ?></td>
           <td><?php echo $item->qty ?></td>
            <td align="right"><?php echo $item->currency_symbol . $item->currency . $item->subtotal_price ?></td>
        </tr>
         <?php endforeach ?>
        
        
        
        <!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->
        <tr class="sectiontableentry2">
            <td align="right" colspan="4"><?php echo JText::_( 'PAGO_CART_SUBTOTAL' ) ?></th>:</td>
            <td  align="right" colspan="3"><?php echo $item->currency_symbol . $item->currency . $this->sub_total_price ?></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
            <td colspan="4"><hr></td>
        </tr>
        <tr>
            <td align="right" colspan="4"><?php echo JText::_( 'PAGO_CART_TOTAL' ) ?></th>: </td>
            <td  align="right" colspan="3"><strong><?php echo $item->currency_symbol . $item->currency . $this->total_price ?></strong></td>
        </tr>
        <tr class="sectiontableentry2">
            <td valign="top" align="right" colspan="4"><?php echo JText::_( 'PAGO_CART_TOTAL_TAX' ) ?></th>: </td>
            <td  align="right" colspan="3">$0.00</td>
        </tr>
        <?php if(isset($this->shipping['value'])): ?>
        <tr class="sectiontableentry2">
            <td valign="top" align="right" colspan="4"><?php echo JText::_( 'PAGO_CART_TOTAL_SHIPPING' ) ?>: </td>
            <td  align="right" colspan="3"><?php echo $item->currency_symbol . $item->currency . $this->shipping['value'] ?></td>
        </tr>
        <tr>
            <td colspan="7">
				<?php echo JText::_( 'PAGO_SHIPPING_OPTION' ) . ': ' . $this->shipping['name'] ?><br /> 
                <?php echo JText::_( 'PAGO_PAYMENT_OPTION' ) . ': ' . $this->payment_option ?>
               
            </td>
        </tr>
         <?php endif ?>
        <tr>
            <td colspan="7"><hr></td>
        </tr>
    </tbody>
</table>