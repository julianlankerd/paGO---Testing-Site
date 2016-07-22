<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 

$order = $this->order;
$addy = $order['addresses']['shipping'];
$details = $order['details'];
$items = $order['items'];
$details->cdate = strtotime( str_replace( '/','-', $details->cdate ) );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	
	<title>Editable Invoice</title>
	
	<link rel='stylesheet' type='text/css' href='<?php echo JURI::base() ?>/components/com_pago/css/invoice/style.css' />

</head>

<body>

	<div id="page-wrap">

		<textarea id="header">INVOICE</textarea>
		
		<div id="identity">
		
            <textarea id="address"><?php echo $addy->first_name . ' ' . $addy->middle_name . ' ' . $addy->last_name ."\n" ?>
<?php echo $addy->address_1 ."\n" ?>
<?php echo $addy->address_2 ."\n" ?>
<?php echo $addy->city ?>, <?php echo $addy->state ?> <?php echo $addy->zip ?>

Phone: <?php echo $addy->phone_1 ?></textarea>

            <div id="logo">

              

              <img id="image" src="images/logo.png" alt="logo" />
            </div>
		
		</div>
		
		<div style="clear:both"></div>

		
		<div id="customer">

            <textarea id="customer-title"><?php echo $addy->company ?>
            
c/o <?php echo $addy->first_name . ' ' . $addy->middle_name . ' ' . $addy->last_name ?></textarea>

            <table id="meta">
                <tr>
                    <td class="meta-head">Invoice #</td>
                    <td><textarea>000<?php echo $details->order_id ?></textarea></td>

                </tr>
                <tr>

                    <td class="meta-head">Date</td>
                    <td><textarea id="date"><?php echo date( 'F d, Y', $details->cdate ) ?></textarea></td>
                </tr>
                <tr>
                    <td class="meta-head">Amount Due</td>

                    <td><div class="due">$<?php echo number_format( $details->order_total, 2 ) ?></div></td>
                </tr>

            </table>
		
		</div>
		
		<table id="items">
		
		  <tr>
		      <th>Item</th>

		      <th>Description</th>
		      <th>Unit Cost</th>
		      <th>Quantity</th>
		      <th>Price</th>
		  </tr>
		  
          <?php foreach( $items as $item ): ?>
          <tr class="item-row">
		      <td class="item-name"><textarea><?php echo $item->name ?></textarea></td>

		      <td class="description"><textarea><?php echo strip_tags( $item->description ) ?></textarea></td>
		      <td><textarea class="cost">$<?php echo number_format( $item->price, 2 ) ?></textarea></td>
		      <td><textarea class="qty"><?php echo $item->qty ?></textarea></td>
		      <td><span class="price">$<?php echo number_format( ( $item->price * $item->qty ), 2 ) ?></span></td>
		  </tr>
          <?php endforeach ?>
		  
		  <tr id="hiderow">

		    <td colspan="5"></td>
		  </tr>
		  
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Subtotal</td>
		      <td class="total-value"><div id="subtotal">$<?php echo number_format( ( $details->order_subtotal ), 2 ) ?></div></td>
		  </tr>
          <tr>
		      <td colspan="2" class="blank">Coupon Code: <?php echo $details->coupon_code ?></td>
		      <td colspan="2" class="total-line">Coupon Discount</td>
		      <td class="total-value"><div id="total">-$<?php echo number_format( $details->coupon_discount, 2 ) ?></div></td>
		  </tr>
          <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Discount</td>
		      <td class="total-value"><div id="total">-$<?php echo number_format( $details->order_discount, 2 ) ?></div></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"><?php echo $details->ship_method_id ?></td>
		      <td colspan="2" class="total-line">Shipping</td>
		      <td class="total-value"><div id="total">$<?php echo number_format( $details->order_shipping, 2 ) ?></div></td>
		  </tr>
          <tr>
		      <td colspan="2" class="blank">Tax Details: <?php echo $details->order_tax_details ?></td>
		      <td colspan="2" class="total-line">Tax</td>
		      <td class="total-value"><div id="total">$<?php echo number_format( $details->order_tax, 2 ) ?></div></td>
		  </tr>
		  <tr>

		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Total</td>
		      <td class="total-value"><div id="total">$<?php echo number_format( $details->order_total, 2 ) ?></div></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>

		      <td colspan="2" class="total-line">Amount Paid</td>

		      <td class="total-value"><textarea id="paid">$0.00</textarea></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance">Balance Due</td>

		      <td class="total-value balance"><div class="due">$<?php echo number_format( $details->order_total, 2 ) ?></div></td>
		  </tr>
		
		</table>
		
		<div id="terms">
		  <h5>Terms</h5>
		  <textarea>NET 30 Days. Finance Charge of 1.5% will be made on unpaid balances after 30 days.</textarea>
		</div>

	
	</div>
	
</body>

</html>