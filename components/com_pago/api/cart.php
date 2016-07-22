<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiCart
{
	static public function clear($dta)
	{
		$code = 200;
	    $status = 'success';
		
		require_once( JPATH_COMPONENT.DS.'controllers'.DS.'cart'.'.php' );
		
		$cart = new PagoControllerCart;
		$response = $cart->clear();
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => $cart
		];
	}
	
	static public function get($dta)
	{
		$code = 200;
	    $status = 'success';
	    
	    Pago::load_helpers(['imagehandler', 'attributes']);
	    
		$cart = Pago::get_instance('cart')->get();
		$cart['raw_prices'] = $cart['format'];
		$cart['items'] = PagoAttributesHelper::get_attribute_for_cart($cart['items']);
		
		foreach($cart['format'] as $k=>$price)
			$cart['format'][$k] = Pago::get_instance('price')->format($price);
		
		$fixedIndexes = [];
		
		if(!empty($cart['items']))
			foreach($cart['items'] as $k=>$item){
				
				$cart['items'][$k]->images = 0;
				
				$price = $cart['items'][$k]->format_price;
				$cart['items'][$k]->format_price
					= Pago::get_instance('price')->format($price);
				
				if($item->images)
					$cart['items'][$k]->image = self::get_item_images($item->id, $item->varationId);
				
				$start = date('l jS \of F Y', time());
				
				if($item->subscr_start_num){
					$start = strtotime('+'.$item->subscr_start_num.' '.$item->subscr_start_type);
					$start = date('l jS \of F Y', $start);
				}
				
				$subscription = (object)[
					'amount' => Pago::get_instance('price')->format($item->subscr_price), 
					'interval' => $item->sub_recur, 
					'interval_count' => $item->subscr_init_price, 
					'start' => $start
				];
				
				$cart['items'][$k]->subscription = $subscription;
				
				$fixedIndexes[] = $cart['items'][$k];
			}
			
		$cart['items'] = $fixedIndexes;
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => $cart
		];
	}
	
	static private function get_item_images($id, $varationId){
		
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		
		$attributeModel = JModelLegacy::getInstance('Attribute','PagoModel');
		$config = Pago::get_instance('config')->get('global');
	    $cart_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($config->get('cart.cart_image_size'));
	    
		if($varationId){
		
			$noImage = false;
			$image = $attributeModel->getPhotoByType($varationId,'product_variation',$cart_image_size_title);
			$defVar = $attributeModel->checkDefaultVariation($varationId);
			
			if($defVar){
				$images = PagoImageHandlerHelper::get_item_files( $id, true, array( 'images' ) );
				if($images){
					$image = PagoImageHandlerHelper::get_image_from_object( $images[0], $cart_image_size_title, true );
				}else{
					$image = false;	
				}
			}

		}else{
			$images = PagoImageHandlerHelper::get_item_files( $id, true, array( 'images' ) );
			if($images){
				$image = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))) . '://' . $_SERVER['HTTP_HOST'] . PagoImageHandlerHelper::get_image_from_object( $images[0], $cart_image_size_title, true );
			}else{
				$image = false;	
			}
		}
		
		if(!$image){
			$image = $attributeModel->getSameVarationImagePath($varationId,$cart_image_size_title);	
			
			if(!$image){
				$image = JURI::base() . 'components/com_pago/images/noimage.jpg';	
				$noImage = true;
			}	
		}
			
		return $image;
	}
	
	static public function app_item($dta)
	{
		$code = 200;
	    $status = 'success';
	
		if(!isset($dta[0]))
			return [
				'code' => 400,
				'status' => 'failure - empty dta payload',
				'model' => [@$model]	
			];
		
		$model = [];
		
		foreach($dta as $item){
			
			$attrs = [];
			
			if(!empty($item['attributes']))
				foreach($item['attributes'] as $attr=>$options){
					foreach($options as $option){
						$attrs[$attr][$option]['selected'] = true;
					}
				}
			
			$response = Pago::get_instance('cart')->add(
				$item['id'], // item id
				$item['quantity'], //quantity
				$attrs, // attributes
				@$item['variation_id'] //variation id
			);

			if(!$response){
				$code = 400;
	    		$status = 'failure - one or more operations did not apply';
			}
			
		}

		return [
			'code' => $code,
			'status' => $status,
			'model' => self::get(false)
		];
	}
	
	static public function app_address($dta)
	{
		$code = 200;
	    $status = 'success';
		$dta = $dta[0];
		$addresses = [];
		
		foreach($dta['addresses'] as $address){
			$addresses[$address['address_type']] = $address;
		}
		$config = Pago::get_instance('config')->get('global');
  		$skip_shipping = $config->get('checkout.skip_shipping');

  		if($skip_shipping)
  		{
  			Pago::get_instance( 'cookie' )->set( 'shipping_set', 1 );
  		}
		$jinput = JFactory::getApplication()->input;
		
		$jinput->set('address', $addresses);
		$jinput->set('call', 'api');
		$jinput->set('guest', false);
		$jinput->set('sameasshipping', $dta['sameasshipping']);
		$jinput->set('save_address', $dta['save_address']);
		
		require_once( JPATH_COMPONENT.DS.'controllers'.DS.'checkout'.'.php' );
		
		$checkout = new PagoControllerCheckout;
		$response = $checkout->set_address();
	
		$model = [];
		
		if($response['status'] != 'success')
			return [
				'code' => 400,
				'status' => 'failure - unspecified error',
				'model' => [@$model]	
			];

		return [
			'code' => $code,
			'status' => $status,
			'model' => self::get(false)
		];
	}
	
	static public function app_shipper($dta)
	{
		$code = 200;
	    	$status = 'success';
		$dta = $dta[0];

		$config = Pago::get_instance('config')->get('global');
  		$skip_shipping = $config->get('checkout.skip_shipping');
		

		$carrier_option = implode('|', [
			$dta['text'],
			$dta['code'],
			$dta['name'],
			$dta['value']
		]);
		
		$jinput = JFactory::getApplication()->input;
		$jinput->set('carrier_option', $carrier_option);
		$jinput->set('skip_shipping', $skip_shipping);				

		require_once( JPATH_COMPONENT.DS.'controllers'.DS.'checkout'.'.php' );
		
		$checkout = new PagoControllerCheckout;
		
		ob_start();
			$response = $checkout->set_shipping($carrier_option);
		ob_get_clean();

		return [
			'code' => $code,
			'status' => $status,
			'model' => self::get(false)
		];
	}
	
	static public function app_paygate($dta)
	{
		$code = 200;
	    $status = 'success';
		$dta = $dta[0];
		
		$jinput = JFactory::getApplication()->input;
		
		$dta = array_merge([
			'payment_option' => 'pago',
			'cc_number'	=> '',
			'cc_month'	=> '',
			'cc_year'	=> '',
			'cc_cvv'	=> ''
		], $dta);
		
		$jinput->set('defer_redirect', true);
		$jinput->set('payment_option', $dta['payment_option']);
		$jinput->set('cc_cardNumber', $dta['cc_number']);
		$jinput->set('cc_expirationDateMonth', $dta['cc_month']);
		$jinput->set('cc_expirationDateYear', $dta['cc_year']);
		$jinput->set('cc_cv2code', $dta['cc_cvv']);
		
		require_once( JPATH_COMPONENT.DS.'controllers'.DS.'checkout'.'.php' );
		
		$checkout = new PagoControllerCheckout;
		
		ob_start();
			$response = $checkout->process(true);
		ob_get_clean();
		
		$cart = self::get(false);
		$cart['paygate_response'] = $response;
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => $cart
		];
	}
	
	static public function app_coupon($dta)
	{
		$code = 200;
	    $status = 'success';
		$dta = $dta[0];
		
		$coupon_code = $dta['code'];

		$coupon = Pago::get_instance('coupons');

		if ( !$coupon->set_code( $coupon_code ) ) {
			return [
				'code' => 400,
				'status' => JText::_( 'PAGO_COUPON_NOT_VALID' ),
				'model' => [@$model]	
			];
		}

		$cart = Pago::get_instance( 'cart' )->get();
		$config = Pago::get_instance('config')->get('global');
		
		if(!$config->get('checkout.combine_coupon_discount') && isset($cart['discount']))
		{
			
			if($cart['discount']!="" && $cart['discount']>0)
			{
				return [
					'code' => 400,
					'status' => JText::_( 'PAGO_COUPON_APPLY_ONLY_WHEN_DISCOUNT_NOT_APPLY' ),
					'model' => [@$model]	
				];
			}
		}
		
		if($cart['total'] <= 0)
		{
			return [
				'code' => 400,
				'status' => JText::_( 'PAGO_COUPON_APPLY_ONLY_WHEN_TOTAL_GREATER_ZERO' ),
				'model' => [@$model]	
			];
		}
		
		$discounts = $coupon->process( $cart );

		Pago::get_instance( 'cart' )->apply_coupon( $coupon_code, $discounts );
		
		$cart = self::get(false);
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => $cart
		];
	}
}
