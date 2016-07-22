<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

class PagoControllerCart extends PagoController
{
	public function add($tpl = null) // ajax
	{
		$item_id = JFactory::getApplication()->input->getInt( 'id' );
		$varId = JFactory::getApplication()->input->getInt( 'varId' );
		$qty = JFactory::getApplication()->input->getInt( 'qty' );
		$attrib = JFactory::getApplication()->input->get( 'attrib', array(), 'array' );

		//$attrib = json_decode($attrib,true);

		if ( !$item_id ) {
			return JError::raiseNotice(false, JText::_('PAGO_CART_INVALID_ITEM_ID') );
		}
		if ( !$qty ) {
			$qty = 1; // must always have at least one to put into cart
		}

		// get additional field and pass to add function
		$return['message'] = '';
		$allInputData = JFactory::getApplication()->input;

		// trigger to check if item is bundled
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin( 'pago_products' );
		$dispatcher->trigger(
			'onAdditionalFieldSelection',
			array( &$item_id, $qty, $attrib,$varId, $allInputData, &$return)
		);
		
		if( empty( $return['message'] ) )
		{
			$return['message'] = Pago::get_instance( 'cart' )->add( $item_id, $qty, $attrib,$varId );
		}

		$cart = Pago::get_instance( 'cart' )->get();
		$return['total_qty'] = $cart['total_qty'];
		$price = Pago::get_instance( 'price' )->format($cart['total']);
		// var_dump($price);die;

		$return['total_price']= Pago::get_instance( 'price' )->removeNulls($price);	
			
				
		$return['status'] = "success";
		
		$return = json_encode($return);
		ob_clean();
		echo $return;
		exit();
	}
	// public function add($tpl = null) // not ajax
	// {
	// 	$item_id = JRequest::getInt( 'id' );
	// 	$qty = JRequest::getInt( 'qty' );
	// 	$attrib = JRequest::getVar( 'attrib', array() );

	// 	$return = JRequest::getVar( 'return', null );
	// 	$action = JRequest::getVar( 'action' );
		
	// 	if ( !$item_id ) {
	// 		return JError::raiseNotice(false, JText::_('PAGO_CART_INVALID_ITEM_ID') );
	// 	}
	// 	if ( !$qty ) {
	// 		$qty = 1; // must always have at least one to put into cart
	// 	}

	// 	$msg = Pago::get_instance( 'cart' )->add( $item_id, $qty, $attrib );

	// 	$this->set_redirect( $return );
	// 	$this->setMessage( $msg );
	// 	return;
	// }

	public function update($tpl = null)
	{
		$item_id = JFactory::getApplication()->input->get( 'id' );
		$qty = JFactory::getApplication()->input->getInt( 'qty' );
		$price_each = JFactory::getApplication()->input->getFloat( 'price_each' );
		$current_subtotal = JFactory::getApplication()->input->getFloat( 'current_subtotal' );
		$subtotal = JFactory::getApplication()->input->getFloat( 'subtotal' );
		$total = JFactory::getApplication()->input->getFloat( 'total' );
		$attrib = JFactory::getApplication()->input->get( 'attrib', array(), 'array' );
		$return = JFactory::getApplication()->input->get( 'return', null );
		$action = JFactory::getApplication()->input->get( 'action' );

		if ( !$item_id ) {
			return JError::raiseNotice(false, JText::_('PAGO_CART_INVALID_ITEM_ID') );
		}

		if($qty <= 0)
		{
			return JError::raiseNotice(false, JText::_('PAGO_CART_CANNOT_UPDATE_QTY') );
		}

		if( !$qty && empty( $attrib ) ){
			return JError::raiseNotice(false, JText::_('PAGO_CART_CANNOT_UPDATE_QTY') );
		}
		$item_id = str_replace('id_', '', $item_id); 
		
		//upd_info is array
		//[0] code
		//[1] message
		//[2] current subtotal
		//[3] overall subtotal
		//[4] overall total
		//[5] current subtotal no format
		//[6] overall subtotal no format
		//[7] overall total no format
		//[8] quantity
		//[9] disocunt
		//[10] discount no format
		//[11] tax
		//[12] tax no format
		//[13] discount message
		//[14] quick cart total
		
		$upd_info = Pago::get_instance( 'cart' )->update( $item_id, $qty, $attrib, $price_each, $current_subtotal, $subtotal, $total);

		$price = $upd_info['4'];
		$upd_info['14'] = Pago::get_instance( 'price' )->removeNulls($price);	
		
		$cart = Pago::get_instance( 'cart' )->get();
		$upd_info['total_qty'] = $cart['total_qty'];
		echo json_encode($upd_info);
		exit;
		

		$this->set_redirect( $return );

		$this->setMessage( $msg );
		return;
	}

	public function delete($tpl = null)
	{
		$item_id = JFactory::getApplication()->input->get( 'id' );
		$attrib = JFactory::getApplication()->input->get( 'attrib', array(), 'array' );
		$return = JFactory::getApplication()->input->get( 'return', null );
		$action = JFactory::getApplication()->input->get( 'action' );

		if ( !$item_id ) {
			return JError::raiseNotice(false, JText::_('PAGO_CART_INVALID_ITEM_ID') );
		}
		$item_id = str_replace('id_', '', $item_id); 

		$id = Pago::get_instance( 'cart' )->get();
		$varId = $id['items'][$item_id]->varationId;
		$id = $id['items'][$item_id]->id;

		Pago::get_instance( 'cookie' )->del_guest_item( $id, $varId );

		$msg = Pago::get_instance( 'cart' )->delete( $item_id, $attrib );

		

		$this->set_redirect($return);

		$this->setMessage( $msg );
		return;
	}

	public function clear($tpl = null)
	{
		$attrib = JFactory::getApplication()->input->get( 'attrib', array(), 'array' );
		$return = JFactory::getApplication()->input->get( 'return', null );
		$action = JFactory::getApplication()->input->get( 'action' );

		$msg = Pago::get_instance( 'cart' )->clear( );

		$this->set_redirect( $return );

		$this->setMessage( $msg );
		return;
	}

	public function coupon( $tpl = null )
	{
		$return = JFactory::getApplication()->input->get( 'return', null );
		$coupon_code = JFactory::getApplication()->input->get( 'couponcode' );

		$coupon = Pago::get_instance('coupons');

		if ( !$coupon->set_code( $coupon_code ) ) {
			$this->set_redirect( $return );
			$this->setMessage( JText::_( 'PAGO_COUPON_NOT_VALID' ) );
			return;
		}

		$cart = Pago::get_instance( 'cart' )->get();
		$config = Pago::get_instance('config')->get('global');
		
		if(!$config->get('checkout.combine_coupon_discount') && isset($cart['discount']))
		{
			
			if($cart['discount']!="" && $cart['discount']>0)
			{
				$this->set_redirect( $return );
				$this->setMessage( JText::_( 'PAGO_COUPON_APPLY_ONLY_WHEN_DISCOUNT_NOT_APPLY' ) );
				return;
			}
		}
		
		if($cart['total'] <= 0)
		{
			$this->set_redirect( $return );
			$this->setMessage( JText::_( 'PAGO_COUPON_APPLY_ONLY_WHEN_TOTAL_GREATER_ZERO' ) );
			return;
		}
		

		$coupon_model = JModelLegacy::getInstance('Coupon', 'PagoModel');
		$coupon_assign_type = $coupon_model->get_assign_category(0, $coupon_code);
		$discounts = $coupon->process( $cart,$coupon_assign_type );
		if($discounts)
		{
			Pago::get_instance( 'cart' )->apply_coupon( $coupon_code, $discounts );
			$this->set_redirect( $return );
			$this->setMessage( JText::_( 'PAGO_COUPON_VALID' ) );
			return;
		}
		else
		{
			$this->set_redirect( $return );
			$this->setMessage( JText::_( 'PAGO_COUPON_NOT_VALID' ) );
			return;
		}
	}
	
	public function  shippingEstimation()
	{
		$dispatcher = KDispatcher::getInstance();
		$config = Pago::get_instance('config')->get('global');
		$cart = Pago::get_instance('cart')->get();
		$guest = JFactory::getApplication()->input->getInt('guest', '0');

		// Shipping options trigger
		JPluginHelper::importPlugin('pago_shippers');
		$shipping_options = array();
		$shipping_address = new stdClass();
		$shipping_address->country = "";
		if(isset($cart['user_data']))
		{
			if(count($cart['user_data'][0]) > 0)
			{
				$shipping_address = (object) $cart['user_data'][0];
			}
		}
		
		if(count($shipping_address) > 0 )
		{
			if($shipping_address->country =='')
			{
				$shipping_address->country = "US";
				$shipping_address->zip = "49001";
			}
		}
		else
		{
			$shipping_address->country = "US";
			$shipping_address->zip = "49001";
		}
		

		if ($config->get('checkout.shipping_type'))
		{
			$cartItemcount = 0;

			foreach ($cart['items'] as $item)
			{
					// Shipping options trigger
					$shipping_option = array();
					$tempCart = $cart;
					unset($tempCart['items']);
					$tempCart['items'][$cartItemcount] = $item;

					JPluginHelper::importPlugin('pago_shippers');
					$dispatcher->trigger(
						'set_shipping_options',
						array( &$shipping_option, $tempCart, $shipping_address)
					);

					$shipping_options[$cartItemcount] = $shipping_option;
					$cartItemcount = $cartItemcount + 1;
			}
		}
		else
		{
			// Shipping options trigger
			JPluginHelper::importPlugin('pago_shippers');
			$dispatcher->trigger(
				'set_shipping_options',
				array( &$shipping_options, $cart, $shipping_address)
			);

			// If we have empty shipping options then get flat rate.

			if (empty($shipping_options))
			{
				$dispatcher->trigger(
					'set_shipping_options_empty',
					array( &$shipping_options, $cart, $shipping_address )
				);
			}
		}

	
		$lowest_rates = array();
		if ($config->get('checkout.shipping_type'))
		{
			foreach ( $shipping_options as $product => $shipping_option )
			{
				$shipping_rates = array();
				$shipping_names = array();
				$shipping_rates = array();
				foreach ( $shipping_option as $shipper => $opt )
				{
					if(count($opt) > 0 )
					{
						foreach ($opt as $shipType => $shipping )
						{
							$shipping_rates[] = $shipping['value'];
							$shipping_names[] = $shipping['name'];
							$shipping_item[] = $cart['items'][$product]->name;
						}
					}
				}
				$lowest_rate_key = array_search(min($shipping_rates), $shipping_rates);

				$lowest_rate = $shipping_rates[$lowest_rate_key];
				$lowest_shipping_name = $shipping_names[$lowest_rate_key];

				$lowest_rates[$cart['items'][$product]->name]['name'] = $lowest_shipping_name;
				$lowest_rates[$cart['items'][$product]->name]['value'] = $lowest_rate;

			}

		}
		else
		{
			$shipping_rates = array();
			$shipping_names = array();
			
			foreach ( $shipping_options as $shipper => $opt )
			{
				if(count($opt) > 0 )
				{
					foreach ($opt as $shipType => $shipping )
					{
						$shipping_rates[] = $shipping['value'];
						$shipping_names[] = $shipping['name'];
					}
					
				}
				
				$lowest_rate_key = array_search(min($shipping_rates), $shipping_rates);
	
				$lowest_rate = $shipping_rates[$lowest_rate_key];
				$lowest_shipping_name = $shipping_names[$lowest_rate_key];

				$lowest_rates['name'] = $lowest_shipping_name;
				$lowest_rates['value'] = $lowest_rate;
			}
		}
		$this->lowest_rates = $lowest_rates;
		
		$shipping_estimation = PagoHelper::load_template( 'common', 'tmpl_shipping_estimation' );
		$return = array();

		ob_start();
			require $shipping_estimation;
			$return['formHtml'] = ob_get_contents();
		ob_end_clean();

		$return['status'] = "success";
		ob_clean();
		
		echo json_encode($return);
		exit();
		
	}

	

	/* set internal redirect for controller
	 *
	 * makes sure a redirect url is actually set
	 *
	 * @param $return base64 encoded string
	 * @return null
	 */
	private function set_redirect( $return )
	{
		if ( $return != null ) {
			$return = base64_decode($return);
		} else {
			// just return back to cart
			$item_id = JFactory::getApplication()->input->getInt( 'Itemid', 0 );
			$return = 'index.php?option=com_pago&view=cart';
			if ( $item_id ) {
				$return .= '&Itemid=' . $item_id;
			}
		}

		// make sure we only redirect to internal links
		if (!JURI::isInternal($return)) {
			$return = '';
		}

		if ( !empty( $return ) ) {
			$this->setRedirect( $return );
		}
		return;
	}

	public function considerPrice(){

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$selected_attributes = JFactory::getApplication()->input->get( 'selected_attributes', '', '' );
		$itemQty = JFactory::getApplication()->input->get( 'itemQty', '', '' );
		$changePhotoOptionId = JFactory::getApplication()->input->get( 'changePhotoOptionId', '', '' );

		$return = $model->considerPrice($itemId,$selected_attributes,$itemQty);
		if($return['price'] > 0)
		{
			$return['price'] = Pago::get_instance( 'price' )->format( $return['price'] );
		}
		else
		{
			$return['price'] = JText::_("PAGO_ITEM_IS_FREE");
		}
		$return = json_encode($return);
		echo $return;
		exit();
	}
	public function getFullVaration(){
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$varationId = JFactory::getApplication()->input->get( 'varationId', '', '' );
		$varation = $model->get_product_varations_by_id($varationId, true);
		$return = false;
		if($varation && $varation->attributes){
			$attributes = array();
			foreach ($varation->attributes as $attribute) {
				$attributes[$attribute->attribute->id] = $attribute->option->id; 	
			}
			if(!empty($attributes)){
				$return['varation_attributes'] = $attributes;
			}
		}
		$return['varation'] = $varation;

		$return = json_encode($return);
		echo $return;
		exit();
	}
	public function getVaration(){
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$varationId = JFactory::getApplication()->input->get( 'varationId', '', '' );
		$varation = $model->get_product_varations_by_id($varationId, true);
		$return = false;
		if($varation && $varation->attributes){
			$attributes = array();
			foreach ($varation->attributes as $attribute) {
				$attributes[$attribute->attribute->id] = $attribute->option->id; 	
			}
			if(!empty($attributes)){
				$return = json_encode($attributes);
			}
		}
		echo $return;
		exit();
	}
	public function getDefaultVaration(){
		Pago::load_helpers( array( 'attributes' ) );

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$itemId = (int)$itemId;

		$defaultVarationId = PagoAttributesHelper::get_default_varation($itemId);
		if($defaultVarationId){
			$return['status'] = 'success';
			$return['defaultVarationId'] = $defaultVarationId->id;
		}else{
			$return['status'] = 'fail';
		}
		$return = json_encode($return);
		echo $return;
		exit();
	}
	public function checkVarationExist(){
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$selected_attributes = JFactory::getApplication()->input->get( 'selected_attributes', '', '' );

		$varation = $model->get_varation_if_exist($selected_attributes,$itemId);
		if($varation){
			$return['success'] = "success";
			$return['varationId'] = $varation->id;
		}else{
			$return['success'] = "fail";
		}
		$return = json_encode($return);
		echo $return;
		exit();
	}

	public function changeImage(){
		$changePhotoId = JFactory::getApplication()->input->get( 'changePhotoId', '', '' );	
		$changePhotoIds = JFactory::getApplication()->input->get( 'changePhotoIds', '', '' );
		$changePhotoType = JFactory::getApplication()->input->get( 'changePhotoType', '', '' );
		$imageSize = JFactory::getApplication()->input->get( 'imageSize', '', '' );

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$attributeModel = JModelLegacy::getInstance('Attribute','PagoModel');

		$return['imagePath'] = false;
		$return['status'] = "fail";

		if($changePhotoType == 'varation'){
			$image = $attributeModel->getPhotoByType($changePhotoId,'product_variation',$imageSize);
			if($image){
				$return['imagePath'] = $image;
			}else{
				$return['imagePath'] = $attributeModel->getSameVarationImagePath($changePhotoId,$imageSize);	
				if(!$return['imagePath']){
					$return['imagePath'] = JURI::root() . 'components/com_pago/images/noimage.jpg';	
				}
			}
		}
		
		if($return['imagePath']){
			$return['status'] = "success";	
		}

		$return = json_encode($return);
		echo $return;
		exit();
	}
}
