<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
$doc =JFactory::getDocument();
$doc->addScript( $tmpl_uri . 'javascript.js' );
$doc->addScriptDeclaration("
	
	var txn_id = '$txn_id';
	var gateway = '$gateway';
	
	jQuery(document).ready(function() {
		
		var jq = jQuery;
		var g = new Gateway( gateway, txn_id );
		
		g.get_order();
	});
");

?>

<div style="border:1px solid #ccc;margin-bottom:5px;padding:5px 0 5px 5px">
	<img src="https://checkout.google.com/seller/images/google_checkout.gif" />
	<div style="padding:6px 5px 0 0;float:right">
		<a id="refresh" class="btn" href="#" style="float:right">PAGO_REFRESH_DATA</a>
		<div id="ajax-loader" style="display: none;">
			<span id="loader-msg">PAGO_CONNECTING_TO_GOOGLE_CHECKOUT</span>
			<img src="http://dev.pagocommerce.com/administrator/components/com_pago/css/images/ajax-loader.gif" />
		</div>
		<div style="clear:both"></div>
		<div id="loader-return-msg" style="margin:10px 5px 5px 5px"></div>
	</div>
	<div class="clear"></div>
</div>

<div id="gctabs" style="overflow:hidden;">
	
	<strong>Tab 1</strong>
	
	<div class="order_details">

		<div><?php echo JText::_( 'PAGO_SHIPPING' ) ?></div>
		<div class="shipping">
			<span class="shipping-name"></span>: <span class="shipping-cost"></span>
		</div>
		
		<div><?php echo JText::_( 'PAGO_TAX' ) ?></div>
		<div class="total-tax"></div>
		
		<div><?php echo JText::_( 'PAGO_TOTAL' ) ?></div>
		<div class="total-charge-amount"></div>  
		
		<div><?php echo JText::_( 'PAGO_REFUND' ) ?></div>
		<div class="total-refund-amount"></div>
	</div>
	
	<div class="order_details">
		
		<div><?php echo JText::_( 'PAGO_GATEWAY_TXN_ID' ) ?></div>
		<div class="google-order-number"></div>
		
		<div><?php echo JText::_( 'PAGO_TIMESTAMP' ) ?></div>
		<div class="timestamp"></div>
		
		<div><?php echo JText::_( 'PAGO_FINANCIAL_STATUS' ) ?></div>
		<div class="financial-order-state"></div>
		
		<div><?php echo JText::_( 'PAGO_FULFILLMENT_STATUS' ) ?></div>
		<div class="fulfillment-order-state"></div>
		<div>
			<a href="#" class="btn cancel-order">PAGO_CANCEL_ORDER</a>
			<div class="controls"> 
			   	<a href="#" class="btn deliver-order">PAGO_MARK_ORDER_DELIVERED</a>
				<a href="#" class="btn refund-order">PAGO_REFUND_AMOUNT</a> 
				<input class="refund_amount" name="refund_amount" />
				<label for="refund_amount">Max. <span class="total-available-amount"></span></label>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<strong>Tab 2</strong>

	<table id="g-order-items" width="100%" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<td><input class="order_item_all" type="checkbox" value="all" />PAGO_ALL</td>
				<td colspan="2">PAGO_ITEM_ID</td>
				<td>PAGO_ITEM_NAME</td>
				<td>PAGO_ITEM_PRICE</td>
				<td>PAGO_ITEM_STATUS</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input class="order_item_all" type="checkbox" value="all" />PAGO_ALL</td>
				<td colspan="2">PAGO_ITEM_ID</td>
				<td>PAGO_ITEM_NAME</td>
				<td>PAGO_ITEM_PRICE</td>
				<td>PAGO_ITEM_STATUS</td>
			</tr>
		</tbody>
	</table>

	<div class="controls"> 
		<a href="#" class="btn" rel="backorder-items">PAGO_BACKORDER</a>
		<a href="#" class="btn" rel="return-items">PAGO_RETURN</a>
		<a href="#" class="btn" rel="cancel-items">PAGO_CANCEL</a>
	</div>

	<strong>Tab 3</strong>

	<table id="g-order-subscription-items" width="100%" cellspacing="1">
		<thead>
			<tr>
				<td>PAGO_ITEM_ID</td>
				<td>PAGO_ITEM_NAME</td>
				<td>PAGO_BILLING_PERIOD</td>
				<td>PAGO_START_DATE</td>
				<td>PAGO_SHIPPING_COST</td>
				<td>PAGO_TAX_COST</td>
				<td>PAGO_INITIAL_PRICE</td>
				<td>PAGO_ITEM_PRICE</td>
				<td>PAGO_ITEM_STATUS</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>PAGO_ITEM_ID</td>
				<td>PAGO_ITEM_NAME</td>
				<td>PAGO_BILLING_PERIOD</td>
				<td>PAGO_START_DATE</td>
				<td>PAGO_SHIPPING_COST</td>
				<td>PAGO_TAX_COST</td>
				<td>PAGO_INITIAL_PRICE</td>
				<td>PAGO_ITEM_PRICE</td>
				<td>PAGO_ITEM_STATUS</td>
			</tr>
		</tbody>
	</table>

	<div class="controls">
		<a href="#" class="btn" rel="cancel-subscr">PAGO_CANCEL</a>
	</div>

	<strong>Tab 4</strong>
	<textarea style="width:98%;height:300px" class="order_xml"></textarea>
</div>
