<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_cart
{
	public function __construct()
	{
		$user = JFactory::getUser();
		if($user->guest){
			$this->userId = 0;
		}else{
			$this->userId = $user->id;
		}
	}

	public function get( $key = null )
	{
		if ( $key === null ) {
			// define some default values if cart doesn't exist
			$cart = array(
				'items' => array(),
				'item_count' => 0,
				'total_qty' => 0,
				'total' => 0.00,
				'subtotal' => 0.00,
				'coupon' => array(),
				'tax' => 0.00,
				'order_tax' => 0.00,
				'shipping' => 0.00,
				'discount' => 0.00,
				'shipping_excluding_tax' => 0.00,
				'shipping_tax' => 0.00,
				'format' => array()
			);
			
			Pago::get_instance( 'price' )->format_cart( $cart );

			$cart = Pago::get_instance( 'cookie' )->get( 'cart_'.$this->userId, $cart );
			
			//need to do this for backwards compatiblity due to the
			//fact that json_encode does not seem to retain type
			if( !empty( $cart['items'] ) )
				foreach( $cart['items'] as $k=>$v )
					$cart['items'][ $k ] = (object)$v;
			
			return $cart;
		}


		//this should be a method of it's own as it
		//seems quite utilitarian to be in a procedural
		//type routine - asd
		$key_chain = explode( '.', $key );
		$num_keys = count( $key_chain );
		$cart = $this->get();

		if ( $num_keys == 1 ) {
			if ( isset( $cart[$key_chain[0]] ) ) {
				return $cart[$key_chain[0]];
			}

			return false;
		} else {
			$temp = array();
			// gets value from keys via dot notation
			for ( $i = 0; $i < $num_keys; $i++ ) {

				if ( $i == 0 ) {
					if ( isset( $cart[$key_chain[$i]] ) ) {
						$temp = $cart[$key_chain[$i]];
					} else {
						return false;
					}
				}

				if ( $i < ($num_keys - 1) && $i != 0 ) {
					if ( isset( $temp[$key_chain[$i]] ) ) {
						$temp = $temp[$key_chain[$i]];
					} else {
						return false;
					}
				}

				if ( $i == ($num_keys - 1) ) {
					if ( isset( $temp[$key_chain[$i]] ) ) {
						return $temp[$key_chain[$i]];
					} else {
						return false;
					}
				}
			}
		}
	}

	public function set( $cart )
	{
		Pago::get_instance( 'cookie' )->set( 'cart_'.$this->userId, $cart );
		return;
		/*JFactory::getSession()->set( 'cart', $cart, 'pago_cart' );
		return;*/
	}

	public function add( $item_id, $qty, $attrib = array(),$varationId )
	{
		JLoader::import( 'itemslist', JPATH_ADMINISTRATOR . '/components/com_pago/models' );
		Pago::load_helpers( 'imagehandler' );

		$items_model = JModelLegacy::getInstance('Itemslist','PagoModel');

		$items_model->setState('id', $item_id);

		$item = $items_model->get(false);
		$cart = $this->get();
		

		$varationId = (int)$varationId;

		$checkItemQty = true;

		if($varationId){
			$db = JFactory::getDBO();

		 	$query = "SELECT `qty`,`qty_limit`,`default` FROM #__pago_product_varation WHERE `id` = ".$varationId;
			
			$db->setQuery( $query );
			$varation = $db->loadObject();
			if($varation->default != 1){
				if ( $varation->qty_limit==0 && $varation->qty-$qty < 0 ) {
					return JText::_('PAGO_QUANTITY_UNAVAILABLE');
				}
				$checkItemQty = false;

				$item->images = true;
			}else{
				$item->images = PagoImageHandlerHelper::get_item_files( $item->id, true, array( 'images' ) );
			}
		}else{
			$item->images = PagoImageHandlerHelper::get_item_files( $item->id, true, array( 'images' ) );
		}
		if($checkItemQty){
			if ( $item->qty_limit==0 && $item->qty-$qty < 0 ) {
				return JText::_('PAGO_QUANTITY_UNAVAILABLE');
			}
		}

		JPluginHelper::importPlugin( 'pago_cart' );
		$dispatcher = KDispatcher::getInstance();
		$response = $dispatcher->trigger(
			'before_add_cart_item',
			array(
				&$cart,
				$item_id,
				$qty,
				$attrib
			)
		);
		if ( !empty($response[0]) ) {
			foreach( $response as $resp ) {
				if ( $resp[0] == 'Failure' ){
					return $resp[1];
				}
			}
		}


		if($attrib){
			$attrib = $this->constructAttrib($attrib);
		}
		$sameItem = 0;
		$itemKey = null;
		if(isset($cart['items'])){ //check same item with same attributes
			foreach ($cart['items'] as $itemK => $checkItem) {
				if($checkItem->id == $item->id){
					if(isset($checkItem->attrib) AND count($checkItem->attrib) > 0){
						foreach ($checkItem->attrib as $ak => $checkAttrib){
							if(isset($attrib[$ak]) AND $attrib[$ak] == $checkAttrib){
								$sameItem = 1;
							}else{
								$sameItem = 0;
								break;
							}
						}
					}else{
						if(empty($attrib)){
							$sameItem = 1;
						}else{
							$sameItem = 0;
						}
					}
					if($sameItem == 1){
						$itemKey = ''.$itemK;
						break;
					}else{
						$itemKey = null;
					}
				}
			}
		}
		if($sameItem == 1 AND $itemKey != null){

			$checkItemQty = true;
			if($varationId){
				if($varation->default != 1){
					if ( $varation->qty_limit==0 && $varation->qty-$qty < 0 ) {
						return JText::_('PAGO_QUANTITY_UNAVAILABLE');
					}
					$checkItemQty = false;
				}
			}
			if($checkItemQty){
				if ( $item->qty_limit==0 && $item->qty-$qty < 0 && $item->qty!='') {
					return JText::_('PAGO_QUANTITY_UNAVAILABLE');
				}
			}

			$cart['items'][$itemKey]->cart_qty = $cart['items'][$itemKey]->cart_qty + $qty;
		} else {

			$model = JModelLegacy::getInstance('Attribute','PagoModel');
			$return = $model->considerPrice($item->id,$attrib,$qty);

			$item->cart_qty = $qty;
			$item->product_price = $return['price'];
			$item->price = $return['unit_price'];
			$item->sku = $return['sku'];
			$item->name = $return['name'];
			if($varationId){
				$item->varationId = $return['name'];
			}
			if(isset($return['varationId'])){
				$item->varationId = $return['varationId'];
			}else
			{
				$item->varationId = 0;
 			}



			$item->attrib = $attrib;

			$cart['items'][] = $item;
		}
		
		$TaxAfterDiscount = 1;
		if($TaxAfterDiscount)
		{
			// Apply Realtime discount
			Pago::get_instance( 'price' )->calc_cart( $cart );
			Pago::get_instance('price')->calculateDiscount($cart);
			// Add tax
			Pago::get_instance( 'price' )->calc_cart( $cart );
			Pago::get_instance('price')->calculateTax($cart);
		}
		else
		{
			// Add tax
			Pago::get_instance( 'price' )->calc_cart( $cart );
			Pago::get_instance('price')->calculateTax($cart);
			// Apply Realtime discount
			Pago::get_instance( 'price' )->calc_cart( $cart );
			Pago::get_instance('price')->calculateDiscount($cart);
		}


		$response = $dispatcher->trigger(
			'add_cart_item',
			array(
				&$cart,
				$item_id,
				$qty,
				$attrib
			)
		);
		$this->calc_total_qty($cart);

		Pago::get_instance( 'price' )->calc_cart( $cart );

		$this->set( $cart );

		return JText::_('PAGO_CART_ADDED_TEXT_ADDED').' '.$item->name.' '.JText::_('PAGO_CART_ADDED_TEXT_TO_CART');
	}
	public function constructAttrib($attrib){
		
		$newAttrib = array();

		if(isset($attrib)){
			foreach($attrib as $k => $attr){
				if(is_array($attr)){
					$ch = 0;
					foreach ($attr as $o => $option) {
						if($option['selected'] == 0){
							unset($attr[$o]);
						}else{
							$ch = 1;
							$attr = $o;
						}
					}
					if($ch == 0){
						unset($attr);
					}
				}else{
					if($attr == 0){
						unset($attr);
					}else{
						
					}

				}

				if(isset($attr)){
					$newAttrib[$k] = $attr;
				}
			}
		}
		if(isset($newAttrib)){
			return $newAttrib;
		}

		return false;
	}
	public function update( $item_id, $qty, $attrib = array(), $price_each = '', $current_subtotal = '', $subtotal = '', $total = '' )
	{
		$cart = $this->get();
		$set_to_max_quantity = false;

		JPluginHelper::importPlugin( 'pago_cart' );
		$dispatcher = KDispatcher::getInstance();

		$response = $dispatcher->trigger(
			'before_update_cart_item',
			array(
				&$cart,
				$item_id,
				$qty,
				$attrib
			)
		);

		if ( !empty($response[0]) ) {
			foreach( $response as $resp ) {
				if ( $resp[0] == 'Failure' ){
					return array(0, $resp[1], '', '', '');
				}
			}
		}
		$items_model = JModelLegacy::getInstance('Itemslist','PagoModel');
		$attr_model = JModelLegacy::getInstance('Attribute','PagoModel');

		$items_model->setState('id', $cart['items'][$item_id]->id);
		$item = $items_model->get(false);


		if( !$qty && empty( $attrib ) ){
			unset($cart['items'][$item_id]);
		} else {

			$checkItemQty = true;

			if(isset($cart['items'][$item_id]->varationId) && $cart['items'][$item_id]->varationId){
				$db = JFactory::getDBO();

			 	$query = "SELECT `qty`,`qty_limit`,`default` FROM #__pago_product_varation WHERE `id` = ".$cart['items'][$item_id]->varationId;
				
				$db->setQuery( $query );
				$varation = $db->loadObject();
				if($varation->default != 1){
					if ( $varation->qty_limit==0 && $varation->qty-$qty < 0 ) {
						$new_current_subtotal = Pago::get_instance( 'price' )->format($varation->qty*$price_each);
						$qty = $varation->qty;
						$set_to_max_quantity = true;
					}
					$checkItemQty = false;
				}
			}
			if($checkItemQty){
				if ( $item->qty_limit==0 && $item->qty-$qty < 0 && $item->qty!='') {
					$new_current_subtotal = Pago::get_instance( 'price' )->format($item->qty*$price_each);
					$qty = $item->qty;
					$set_to_max_quantity = true;
				}
			}

			$cart['items'][$item_id]->cart_qty = $qty;
		}

		if ( !empty( $attrib ) ) {

			$attrib = $this->constructAttrib($attrib);

		}

		$this->calc_total_qty($cart);

		$TaxAfterDiscount = 1;
		if($TaxAfterDiscount)
		{
			// Apply realtime discount
			Pago::get_instance( 'price' )->calc_cart( $cart );
			Pago::get_instance('price')->calculateDiscount($cart);

			// Update tax
			Pago::get_instance('price')->calculateTax($cart);
		}
		else
		{
			// Update tax
			Pago::get_instance('price')->calculateTax($cart);
			Pago::get_instance( 'price' )->calc_cart( $cart );
			// Apply realtime discount
			Pago::get_instance('price')->calculateDiscount($cart);
		}
		

		$response = $dispatcher->trigger(
			'update_cart_item',
			array(
				&$cart,
				$item_id,
				$qty,
				$attrib
			)
		);

		Pago::get_instance( 'price' )->calc_cart( $cart );

		$this->set($cart);
		$new_current_subtotal_noformat = $qty*$price_each;
		$new_current_subtotal = Pago::get_instance( 'price' )->format($qty*$price_each);
		$subtotal_noformat = $subtotal - $current_subtotal + $new_current_subtotal_noformat;
		$subtotal = Pago::get_instance( 'price' )->format($subtotal_noformat);
		$discount_noformat = $cart['discount'];
		$discount = Pago::get_instance( 'price' )->format($cart['discount']);
		$tax_noformat = $cart['tax'];
		$tax = Pago::get_instance( 'price' )->format($cart['tax']);
		$discount_message = $cart['discount_message'];
		$total_noformat = $subtotal_noformat + $tax_noformat - $discount_noformat;
		$total = Pago::get_instance( 'price' )->format($total_noformat);
		$response = array(
				1, 
				JText::_('PAGO_CART_UPDATED'), 
				$new_current_subtotal, 
				$subtotal, 
				$total, 
				$new_current_subtotal_noformat, 
				$subtotal_noformat, 
				$total_noformat, 
				$qty, 
				$discount, 
				$discount_noformat, 
				$tax, 
				$tax_noformat,
				$discount_message, 
				$total
		);
		if ($set_to_max_quantity){
			$response[1] = JText::_('PAGO_QUANTITY_UNAVAILABLE')."<br>".JText::_('PAGO_QUANTITY_AVAILABLE')." ".$qty;
		}
		return $response;
	}

	public function delete( $item_id, $attrib = array() )
	{
		$cart = $this->get();

		JPluginHelper::importPlugin( 'pago_cart' );
		$dispatcher = KDispatcher::getInstance();

		$response = $dispatcher->trigger( 'before_delete_cart_item', array( $item_id ) );


		if ( !empty($response[0]) ) {
			foreach( $response as $resp ) {
				if ( $resp[0] == 'Failure' ){
					return $resp[1];
				}
			}
		}
		if ( !empty( $attrib ) ) {
			foreach ( $attrib as $k => $options ) {
				if ( is_array( $options ) ) {
					foreach ( $options as $ke => $opt ) {
						unset( $cart['items'][$item_id]->attrib[$k][$ke] );
					}
				} else {
					unset( $cart['items'][$item_id]->attrib[$k][$options] );
				}

				if ( empty( $cart['items'][$item_id]->attrib[$k] ) ) {
					unset( $cart['items'][$item_id]->attrib[$k] );
				}
			}
		}
		if( isset($cart['items'][$item_id]) && empty( $cart['items'][$item_id]->attrib ) ){
			unset($cart['items'][$item_id]);
		}
		if( isset($cart['items'][$item_id]) && empty( $attrib ) ){
			unset($cart['items'][$item_id]);
		}

		if ( empty($cart['items']) ) {
			// set cart to empty if no items in cart
			$cart = array();
		}

		
		$response = $dispatcher->trigger( 'delete_cart_item', array( $item_id ) );

		if ( !empty($response[0]) ) {
			foreach( $response as $resp ) {
				if ( $resp[0] == 'Failure' ){
					return $resp[1];
				}
			}
		}
		$TaxAfterDiscount = 1;
		if($TaxAfterDiscount)
		{
			// Apply realtime discount
			Pago::get_instance( 'price' )->calc_cart( $cart );
			Pago::get_instance('price')->calculateDiscount($cart);

			// Update tax
			Pago::get_instance('price')->calculateTax($cart);
		}
		else
		{
			// Update tax
			Pago::get_instance('price')->calculateTax($cart);
			Pago::get_instance( 'price' )->calc_cart( $cart );
			// Apply realtime discount
			Pago::get_instance('price')->calculateDiscount($cart);
		}
		Pago::get_instance( 'price' )->calc_cart( $cart );
		$this->calc_total_qty($cart);
		$this->set( $cart );

		return JText::_('PAGO_CART_DELETED');
	}

	public function clear()
	{
		//JFactory::getSession()->clear('cart', 'pago_cart');
		Pago::get_instance( 'cookie' )->set( 'cart_'.$this->userId, false );
		Pago::get_instance( 'cookie' )->set( 'cart_0', false );

		JPluginHelper::importPlugin( 'pago_cart' );
		$dispatcher = KDispatcher::getInstance();

		$dispatcher->trigger( 'clear_cart_session' );
	}

	private function calc_total_qty( &$cart )
	{
		// reset every time we re calc
		$cart['total_qty'] = 0;
		$cart['item_count'] = 0;

		foreach ( $cart['items'] as $item ) {
			$cart['total_qty'] += $item->cart_qty;
			$cart['item_count']++;
		}

		JPluginHelper::importPlugin( 'pago_cart' );
		$dispatcher = KDispatcher::getInstance();

		$dispatcher->trigger( 'calc_cart_total_qty', array( $cart ) );
	}

	/**
	 * Apply given coupon discount/s to cart.
	 * discounts are amounts to deduct from the price and some info on the discount applied
	 *
	 * @params array $discounts Array with 1 or more discounts to apply to cart.
	 */
	public function apply_coupon( $coupon_code, $discounts )
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/models');
		
		$coupon_model = JModelLegacy::getInstance('Coupon', 'PagoModel');
		$coupon_assign_type = $coupon_model->get_assign_category(0, $coupon_code);
		$coupon_assign_type = $coupon_assign_type['type'];
		
		$cart = $this->get();
		$cart['coupon']['code'] = $coupon_code;
		$cart['coupon']['discounts'] = $discounts;
		$cart['coupon']['total'] = 0.00;
		$cart['coupon']['precent_rate'] = 0.00;
		$a = 0.00;
		
		if($coupon_assign_type != 5){
			foreach ( $discounts as $discount )
			{
				$a += $discount['total'];
				$cart['coupon']['percent_rate'] = $discount['percent_rate'];
			}
			$cart['coupon']['total'] = number_format($a, 2);
			$cart['discount'] = $cart['coupon']['total'];
		}
		
		Pago::get_instance('price')->calc_cart($cart);
		
		$this->set($cart);
		return $cart['discount'];
	}
}
