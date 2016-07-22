<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Pago  component
 */
class PagoHelperPrice
{
	var $subtotal, $total, $shipping, $tax;

	function get_symbol($tpl = null) {

		return '$';
    }

	function set_amounts( $cart, $shipper ){

		$total_price = 0;
		$sub_total_price = 0;

		if(isset($cart['items'])){
			foreach($cart['items'] as $k=>$item){
				$cart['items'][$k]->currency_symbol = $this->get_symbol( $item->currency );
				if ( !empty($item->price) && $item->price > 0 ) {
					$cart['items'][$k]->subtotal_price = $item->price * $item->cart_qty;
					$cart['items'][$k]->subtotal_price =
						number_format( $cart['items'][$k]->subtotal_price, 2, '.', '' );
					$cart['items'][$k]->price =
						number_format( $cart['items'][$k]->price, 2, '.', '' );

					$sub_total_price = $sub_total_price + $cart['items'][$k]->subtotal_price;
				} else {
					// check attributes for price
					foreach( $item->attrib as $j => $attrib ) {
						foreach ( $attrib as $i => $opt ) {
							if ( array_key_exists('price', $opt ) && !empty( $opt['price'] ) ) {
								$opt['price'] = preg_replace( '/^\$/', '', $opt['price']);
								$cart['items'][$k]->attrib[$j][$i]['subtotal_price'] =
								   $opt['price'] * $opt['qty'];
								$cart['items'][$k]->attrib[$j][$i]['subtotal_price'] =
									number_format( $cart['items'][$k]->attrib[$j][$i]['subtotal_price'], 2, '.', '' );
								$sub_total_price =
									$sub_total_price + $cart['items'][$k]->attrib[$j][$i]['subtotal_price'];
							}
						}
					}
				}
			}
		}

		$this->subtotal = number_format( $sub_total_price, 2, '.', '' );
		$this->total = $sub_total_price + $shipper['value'];
		$this->shipping = $shipper['value'];

		return true;
	}
}
?>
