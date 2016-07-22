<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_price
{
	private $format = null;
	private $config = null;
	public $currency = '';
	public $currency_symbol = '';
	public $tax_rules = null;

	public function __construct()
	{
		$this->config = Pago::get_instance( 'config' )->get();
		// TODO : remove currecny commented code before go live
		/*$defaultCurrency = $this->getDefaultCurrency();

		$this->currency = $defaultCurrency->code;

		if($defaultCurrency->symbol != ''){
			$this->currency_symbol = $defaultCurrency->symbol;	
		}else{
			$this->currency_symbol = $defaultCurrency->code;	
		}*/
		
		// setup numberformatter with the locale picked in the backend
		// TODO: setup a basic formatter if the php module isn't available
	}

	/**
	 * Format currency based off of locale and currency
	 *
	 *
	 */
	public function format( $price, $currency = null )
	{
		$price = str_replace(",", ".", $price);
		if($price == ''){
			$price = 0;	
		}
		$price = (float)$price;

		$currenciesModel = JModelLegacy::getInstance('currencies', 'PagoModel');
		$currencyCourse = $currenciesModel->getCurrenciesCource();

		$defaultCurrency = $currenciesModel->getDefault();
		$symbol = $defaultCurrency->symbol;
		
		JPluginHelper::importPlugin( 'pago_products' );
		$dispatcher = JDispatcher::getInstance();
		$results    = $dispatcher->trigger('getCustomCurrency', array(&$defaultCurrency));

		$currentCurrencyId = Pago::get_instance( 'cookie' )->get( 'current_currency');
		
		if($currency!="")
		{
			$db	= JFactory::getDBO();
			$query = "select * from `#__pago_currency` AS item where code = '".$currency."' and published = 1";
			$db->setQuery($query);
			$items = $db->loadObjectList();
			
			$currentCurrencyId = $items[0]->id;
		}
		
		$currentCurrency = false;
		if($currentCurrencyId){
			$currentCurrency = $currenciesModel->getCurrencyById($currentCurrencyId);
		}
		if($currentCurrency){
			if($defaultCurrency->code != $currentCurrency->code){
				$price = ($price / $currencyCourse[$defaultCurrency->code]) * $currencyCourse[$currentCurrency->code];
			}
			$symbol = $currentCurrency->symbol;
		}
		
		$accuracy = $this->config->get( 'general.decimal_places', 2);
		
		///CURRENCY_SYMBOL
		$config = Pago::get_instance('config')->get('global');
		$currency_sym_position = $config->get('general.currency_symbol_display');
		$price_seperator = $config->get('general.price_seperator');
		//$price_seperator = ',';
		if($currency_sym_position == '1')	
		{
			$format_price = $symbol.number_format($price, $accuracy, $price_seperator, ',' );
		}
		else
		{

			$format_price = number_format($price, $accuracy, $price_seperator, ',' ).$symbol;
		}
		return $format_price;
	}
	
	public function checkForProductOnSale($item)
	{
		$on_discount = $item -> apply_discount;
		$disc_start_date = $item -> disc_start_date;
		$disc_end_date = $item -> disc_end_date;
		$disc_type = $item -> discount_type;
		$disc_amount = $item -> discount_amount;
		$price_array = array();
		
		if ($on_discount == 1 && ((strtotime($disc_start_date) <= time() && strtotime($disc_end_date) >= time()) && (strtotime($disc_start_date) != '' && strtotime($disc_end_date) != '')) || (strtotime($disc_start_date) <= time() && strtotime($disc_start_date) != '' &&  strtotime($disc_end_date) == ''))
		{
			$price_array[] = $item->price;
			if($disc_type == 0)
			{
				$price_array[] = $item->price - $disc_amount;
			}
			else
			{
				$disc_amount = ($item->price * $disc_amount)/100;
				$price_array[] = $item->price - $disc_amount;
			}	
		}
		
		return $price_array;
	}
	
	function getItemDisplayPrice($item)
	{
		$config = Pago::get_instance('config')->get('global');
		$display_price_with_tax = $config->get('checkout.display_price_with_tax');
		$itemPriceObj = new stdClass();

		$user = JFactory::getUser();
		$user_id = $user->id;
		$userData = $this->getUserAddressInfoForTax($user_id);
		
		JPluginHelper::importPlugin( 'pago_products' );
		$dispatcher = JDispatcher::getInstance();
		$results    = $dispatcher->trigger('beforeProductPrice', array(&$item));
		
		$priceArray = $this->checkForProductOnSale($item);
		if($item -> apply_discount && count($priceArray) > 0)
		{
			$item->old_price = $itemPriceObj->old_price =  $priceArray[0];
			$item->price =  $priceArray[1];
		}
		else
		{
			$itemPriceObj->old_price = '';
		}

		if ($display_price_with_tax)
		{
			$itemTax = Pago::get_instance('price')->getProductTax($item, $userData);
			$itemPriceObj->item_price_including_tax = $itemTax ['item_price_including_tax'];
			$itemPriceObj->item_price_excluding_tax = $itemTax ['item_price_excluding_tax'];
		}
		else
		{
			$itemPriceObj->item_price_including_tax = $item->price;
			$itemPriceObj->item_price_excluding_tax = $item->price;
		}
		return $itemPriceObj;
	}
	function getItemDisplayPrice_back($item)
	{	

		$userId = JFactory::getApplication()->input->get('user_id');
		$addressid = JFactory::getApplication()->input->get('addressid');
		$saddressid = JFactory::getApplication()->input->get('saddressid');


		$config = Pago::get_instance('config')->get('global');
		$display_price_with_tax = $config->get('checkout.display_price_with_tax');
		$itemPriceObj = new stdClass();

		$userData = $this->getUserAddressInfoForTax($userId,$addressid,$saddressid);
		$priceArray = $this->checkForProductOnSale($item);
		if(count($priceArray) > 0)
		{
			$item->old_price = $itemPriceObj->old_price =  $priceArray[0];
			$item->price =  $priceArray[1];
		}
		else
		{
			$itemPriceObj->old_price = '';
		}

		if ($display_price_with_tax)
		{
			$itemTax = Pago::get_instance('price')->getProductTax($item, $userData);
			$itemPriceObj->item_price_including_tax = $itemTax ['item_price_including_tax'];
			$itemPriceObj->item_price_excluding_tax = $itemTax ['item_price_excluding_tax'];
		}
		else
		{
			$itemPriceObj->item_price_including_tax = $item->price;
			$itemPriceObj->item_price_excluding_tax = $item->price;
		}
		return $itemPriceObj;
	}
	
	/**
	 * Calculate discount if it applies to cart
	 *
	 */

	public function calculateDiscount(&$cart)
	{
		$cart['total'] = $cart['total'] + @$cart['discount'] - @$cart['shipping'];
		$cart['discount'] = 0;
		$total_discount = 0;
		$config = Pago::get_instance('config')->get('global');
		//exit;
		$finalDiscountRule = Pago::get_instance('discounts')->getDiscountRule($cart);
		// coupon discount
		if(isset($cart['coupon']['code']))
		{
			$coupon_code = $cart['coupon']['code'];
			$coupon = Pago::get_instance('coupons');
			$coupon->set_code( $coupon_code );

			$coupon_model = JModelLegacy::getInstance('Coupon', 'PagoModel');
			$coupon_assign_type = $coupon_model->get_assign_category(0, $coupon_code);
			$discounts = $coupon->process( $cart, $coupon_assign_type);
			if($discounts)
			{
				$cart['discount'] = Pago::get_instance( 'cart' )->apply_coupon( $coupon_code, $discounts );
			}

			if($coupon_assign_type != 5)
			{
				foreach ( $discounts as $discount )
				{
					$a += $discount['total'];
					$cart['coupon']['percent_rate'] = $discount['percent_rate'];
				}
				$cart['coupon']['total'] = number_format($a, 2);
			//$cart['discount'] = $cart['coupon']['total'];
			}
		}
		if($finalDiscountRule)
		{
			$cart['discount_percent'] = $finalDiscountRule['discount_percent'];
		}
		$cart['discount'] = $cart['discount'] + $finalDiscountRule['discount_amount'];
		$cart['total'] = $cart['total'] - $cart['discount'] + @$cart['shipping'];
		$cart['discount_message'] = $finalDiscountRule['discount_message'];
	}

	/**
	 * Calculate tax if it applies to cart
	 *
	 */
	public function calculateTax(&$cart)
	{
	
		
		$total_tax = 0;
		$config = Pago::get_instance('config')->get('global');
		$totalTaxOnShipping = 0;

		$user = JFactory::getUser();
		$user_id = $user->id;
		$userData = $this->getUserAddressInfoForTax($user_id);

//print_r($userData);
		foreach ($cart['items'] as $k => $item)
		{
			$itemTax = $this->getProductTax($item, $userData);
			$item ->item_price_including_tax = $itemTax ['item_price_including_tax'];
			$item ->item_price_excluding_tax = $itemTax ['item_price_excluding_tax'];
			$item ->item_tax = $itemTax ['item_tax'];
			$item ->item_tax_rate = $itemTax ['item_tax_rate'];
			$item ->apply_tax_on_shipping = $itemTax['apply_tax_on_shipping'];
			$total_tax = $total_tax + ($item ->item_tax * $item ->cart_qty);
			$cart['items'][$k]->order_item_tax = $item ->item_tax;
			$cart['items'][$k]->order_item_shipping_tax = 0;

			// Calculate tax on Shipping
			if (!$item->free_shipping && $itemTax['apply_tax_on_shipping'])
			{
				$taxRate = $item->item_tax_rate;

				if ($config->get('checkout.shipping_type'))
				{
					$itemShipping = $item ->item_tax;
					$itemTaxOnShipping = ($item ->item_tax_rate * $itemShipping) / 100;
					$item->shipping_tax = $itemTaxOnShipping;
					$totalTaxOnShipping += $itemTaxOnShipping;
				}
				else
				{
					$totalShipping = $cart['shipping'];

					if ($totalShipping > 0)
					{
						$itemShipping = ($totalShipping * $item->item_price_excluding_tax) / $cart['subtotal'];
						$itemTaxOnShipping = ($item ->item_tax_rate * $itemShipping) / 100;
						$item->shipping_tax = $itemTaxOnShipping;
						$totalTaxOnShipping += $itemTaxOnShipping;
					}
				}
				$cart['items'][$k]->order_item_shipping_tax = $item->order_item_shipping_tax;
			}
		}

		$TaxAfterDiscount = 1;
		$cart['order_tax'] = $total_tax;
		if($TaxAfterDiscount)
		{
			if(isset($cart['discount_percent']) && $cart['discount_percent'] > 0 )
			{
				$discount_on_tax = ($cart['discount_percent'] * $total_tax)/100;
				$cart['order_tax'] = $total_tax-$discount_on_tax;
			}
		}
		else
		{
			$cart['order_tax'] = $total_tax;
		}

		$cart['shipping_tax'] = $totalTaxOnShipping;
		$cart['tax'] = $cart['shipping_tax'] + $cart['order_tax'];
		$cart['shipping'] = $cart['shipping'] + $cart['shipping_tax'];
	}

	public function calculateTaxWhenCheckout(&$cart, $add_id, $addData = false)
	{
		$total_tax = 0;
		$config = Pago::get_instance('config')->get('global');
		$totalTaxOnShipping = 0;
		
		$userData = array(
			'taxCountry' => 0,
			'taxState' => 0,
			'taxCity' => 0,
			'taxZipcode' => 0
		);
		
		if(!$add_id && $addData){
			$userData['taxCountry'] = $addData['country'];
			$userData['taxState'] = $addData['state'];
			$userData['taxCity'] = $addData['city'];
			$userData['taxZipcode'] = $addData['zip'];
		}
		elseif($add_id){
			$data = Pago::get_instance('users')->get_shipping_addresses($add_id);
			$userData['taxCountry'] = $data[0]->country;
			$userData['taxState'] = $data[0]->state;
			$userData['taxCity'] = $data[0]->city;
			$userData['taxZipcode'] = $data[0]->zip;
		}
		
		foreach ($cart['items'] as $k => $item)
		{

			$itemTax = $this->getProductTax($item, $userData);

			$item ->item_price_including_tax = $itemTax ['item_price_including_tax'];
			$item ->item_price_excluding_tax = $itemTax ['item_price_excluding_tax'];
			$item ->item_tax = $itemTax ['item_tax'];
			$item ->item_tax_rate = $itemTax ['item_tax_rate'];
			$item ->apply_tax_on_shipping = $itemTax['apply_tax_on_shipping'];
			$total_tax = $total_tax + ($item ->item_tax * $item ->cart_qty);
			$cart['items'][$k]->order_item_tax = $item ->item_tax;
			$cart['items'][$k]->order_item_shipping_tax = 0;

			// Calculate tax on Shipping
			if (!$item->free_shipping && $itemTax['apply_tax_on_shipping'])
			{
				$taxRate = $item->item_tax_rate;

				if ($config->get('checkout.shipping_type'))
				{
					$itemShipping = $item ->item_tax;
					$itemTaxOnShipping = ($item ->item_tax_rate * $itemShipping) / 100;
					$item->shipping_tax = $itemTaxOnShipping;
					$totalTaxOnShipping += $itemTaxOnShipping;
				}
				else
				{
					$totalShipping = $cart['shipping'];

					if ($totalShipping > 0)
					{
						$itemShipping = ($totalShipping * $item->item_price_excluding_tax) / $cart['subtotal'];
						$itemTaxOnShipping = ($item ->item_tax_rate * $itemShipping) / 100;
						$item->shipping_tax = $itemTaxOnShipping;
						$totalTaxOnShipping += $itemTaxOnShipping;
					}
				}
				
				$cart['items'][$k]->order_item_shipping_tax = $item->order_item_shipping_tax;

			}
		}

		$TaxAfterDiscount = 1;
		$cart['order_tax'] = $total_tax;
		if($TaxAfterDiscount)
		{
			if(isset($cart['discount_percent']) && $cart['discount_percent'] > 0 )
			{
				$discount_on_tax = ($cart['discount_percent'] * $total_tax)/100;
				$cart['order_tax'] = $total_tax-$discount_on_tax;
			}

		}
		else
		{
			$cart['order_tax'] = $total_tax;
		}
		$cart['shipping_tax'] = $totalTaxOnShipping;
		$cart['tax'] = $cart['shipping_tax'] + $cart['order_tax'];
		$cart['shipping'] = $cart['shipping'] + $cart['shipping_tax'];
		$this->calc_cart($cart);
		
		$this->calculateDiscount($cart);
		$this->calc_cart($cart);
		Pago::get_instance( 'cart' )->set( $cart );
		$Tax = Pago::get_instance( 'price' )->format($cart['tax']);
		$Total = Pago::get_instance( 'price' )->format( $cart['total']);
		$discount = Pago::get_instance( 'price' )->format( $cart['discount']);
		
		$taxTotal = array('tax' => $Tax, 'total' => $Total, 'discount' =>  $discount  );
		echo json_encode($taxTotal);
		exit();
	}

	private function useDefaultTaxClass($item, $userData) {
		$storeData = Pago::get_instance('config')->get('global')->get('general');
		JPluginHelper::importPlugin('pago_taxes');
    	$dispatcher = JDispatcher::getInstance();
    	$result = $dispatcher->trigger('calculate_tax', array(&$item, &$userData, &$storeData));
    	if(count($result) > 0)
    	{
    		return $result[0];
    	}
	}

	public function getProductTax($item, $userData) {
		if(isset($item->tax_exempt) && $item->tax_exempt) {
			return array(
				'item_price_including_tax' => $item->price,
				'item_price_excluding_tax' => $item->price,
				'item_tax' => 0,
				'item_tax_rate' => 0,
				'apply_tax_on_shipping' => 0
			);	
		}
	
		if (array_key_exists('taxCountry', $userData) && $userData['taxCountry'] != "")
		{
			$config = Pago::get_instance('config')->get('global');
			$default_tax_class = $config->get('checkout.default_tax_class');

			if (isset($item->pgtax_class_id) && $item->pgtax_class_id) {
				$default_tax_class = $item->pgtax_class_id;
			}
			else {
				$use_default_class = $this->useDefaultTaxClass($item, $userData);
				
				if(count($use_default_class)<=0)
				{
					$default_tax_class = $config->get('checkout.default_tax_class');
				}
				else
				{
					return $use_default_class;
				}
			}

			// For zipcode matches
			$onlyDigits = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", " ");

			$zipCond = "";
			$userZipcode = trim($userData['taxZipcode']);

			if (strlen(str_replace($onlyDigits, '', $userZipcode)) == 0 && $userZipcode != "")
			{
				$zipCond = ' AND ( ( pgzip_from <= "' . $userZipcode . '" AND pgzip_to >= "' . $userZipcode . '" )
				OR (pgzip_from = "0" AND pgzip_to = "0")
				OR (pgzip_from = "" AND pgzip_to = "") ) ';
			}

			$db = JFactory::getDBO();
			$query = 'SELECT tax.* FROM #__pago_tax_rates as tax '
				.'WHERE tax.pgtax_country="'.$userData['taxCountry'].'" '
				.'AND (tax.pgtax_state = "'.$userData['taxState'].'" OR tax.pgtax_state = "") '
				. 'AND  tax.pgtax_class_id = "'.$default_tax_class.'"'
				. 'AND  tax.pgtax_enable = 1'
				. $zipCond
				.' ORDER BY `priority` ASC ';
			$db->setQuery($query);
			$taxData = $db->loadObjectList();
			$finaltaxData = array();

//print_r($taxData);die;

			if (count($taxData) > 0)
			{
				if (strlen(str_replace($onlyDigits, '', $userZipcode)) != 0 && $userZipcode != "")
				{
					$f = 0;
					$flag = false;
					$userZipcodelength = ($this->strposa($userZipcode, $onlyDigits) !== false) ? ($this->strposa($userZipcode, $onlyDigits)) : strlen($userZipcode);

					foreach ($taxData as $taxRate)
					{
							$zipStart = $taxRate->pgzip_from;
							$zipEnd = $taxRate->pgzip_to;

							if ($zipStart != "" && $zipEnd != "")
							{
								$zipStartLength = ($this->strposa($zipStart, $onlyDigits) !== false) ? ($this->strposa($zipStart, $onlyDigits)) : strlen($zipStart);
								$zipEndLength = ($this->strposa($zipEnd, $onlyDigits) !== false) ? ($this->strposa($zipEnd, $onlyDigits)) : strlen($zipEnd);

								$len = $userZipcodelength;

								for ($j = 0;$j < $len;$j++)
								{
									if (ord(strtoupper($userZipcode[$j])) >= ord(strtoupper($zipStart[$j])) && ord(strtoupper($userZipcode[$j])) <= ord(strtoupper($zipEnd[$j])))
									{
										$flag = true;
									}
								}

								if ($flag)
								{
									$finaltaxData[$f] = $taxRate;
									$f++;
								}
							}
							else
							{
								$finaltaxData[$f] = $taxRate;
								$f++;
							}
					}
				}
				else
				{
					$finaltaxData = $taxData;
				}
			}

			if(count($finaltaxData)>0)
			{
				$taxData = $finaltaxData[0];
			}

			if (count($taxData) == 0)
			{
				$itemPriceExcldingTax = $item->price;
				$itemPriceIncldingTax = $item->price;
				$itemTaxAmount = 0;
				$taxRate = 0;
				$applyTaxOnShipping = 0;
			}
			else
			{
				// Calculate Tax Amount
				$itemPriceExcldingTax = $item->price;
				$taxRate = $taxData->pgtax_rate;
				$itemTaxAmount = $itemPriceExcldingTax * ($taxRate / 100);
				$itemPriceIncldingTax = $itemPriceExcldingTax + $itemTaxAmount;
				$applyTaxOnShipping = 0;
			}
		}
		else
		{
				$itemPriceExcldingTax = $item->price;
				$itemPriceIncldingTax = $item->price;
				$itemTaxAmount = 0;
				$taxRate = 0;
				$applyTaxOnShipping = 0;
		}
		if(empty($taxRate)) {
			$result_values = $this->useDefaultTaxClass($item, $userData);
			if(count($result_values) > 0)
			{
				return $result_values;
			}
		}
		$itemTax = array();
		$itemTax ['item_price_including_tax'] = $itemPriceIncldingTax;
		$itemTax ['item_price_excluding_tax'] = $itemPriceExcldingTax;
		$itemTax ['item_tax'] = $itemTaxAmount;
		$itemTax ['item_tax_rate'] = $taxRate;
		$itemTax ['apply_tax_on_shipping'] = $applyTaxOnShipping;

		return $itemTax;
	}

	// Function to find first digit position.
	function strposa($zipcode, $digits=array(), $offset=0)
	{
		$chr = array();

		foreach ($digits as $digit)
		{
			if (strpos($zipcode, $digit, $offset) !== false)
			{
				$chr[] = strpos($zipcode, $digit, $offset);
			}
		}

		if (empty($chr))
		{
			return false;
		}

		return min($chr);
	}

	// For back-end Order
	public function getUserAddressInfoForOfflineOrderTax($user_id, $address_id, $order_id)
	{
		$address = '';
		if($order_id)
		{
			$saved_addresses = Pago::get_instance('users')->get_order_user_address($user_id, $address_id, $order_id);
		}
		else
		{
			$saved_addresses = Pago::get_instance('users')->get_user_addresses($user_id);
		}

		foreach ( $saved_addresses as $user_address )
		{
			if ( $user_address->id == $address_id )
			{
				$address = $user_address;
				break;
			}
		}

		return $address;
	}

	public function getUserAddressInfoForTax($user_id, $address_id=0, $saddress_id=0, $order_id=0)
	{
		$config = Pago::get_instance('config')->get('global');
		$app = JFactory::getApplication();


		if ($user_id)
		{
			$calculate_tax_address = $config->get('checkout.calculate_tax_address');

			// Get our user groups, billing address and mailing address data
			$cart = Pago::get_instance('cookie')->get('cart_'.$user_id);
			$userDetails = Pago::get_instance('users')->get();

			$userData = array();

			if ($calculate_tax_address == 'shipping')
			{
				if (!$app->isSite())
				{
					$userDetails = $this->getUserAddressInfoForOfflineOrderTax($user_id, $saddress_id, $order_id);
				}
				else
				{
					if (isset($cart['user_data']) && count($cart['user_data']['0']) > 0)
					{
						$userDetails = (object) $cart['user_data']['0'];
					}
					else
					{
						$userDetails = $userDetails['shipping'];
					}
				}
				if(isset($userDetails->country)){
					$userData['taxCountry'] = $userDetails->country;
				}
				if(isset($userDetails->state)){
					$userData['taxState'] = $userDetails->state;
				}
				if(isset($userDetails->city)){
					$userData['taxCity'] = $userDetails->city;
				}
				if(isset($userDetails->zip)){
					$userData['taxZipcode'] = $userDetails->zip;
				}
			}
			elseif ($calculate_tax_address == 'billing')
			{
				if (!$app->isSite())
				{
					$userDetails = $this->getUserAddressInfoForOfflineOrderTax($user_id, $address_id, $order_id);
				}
				else
				{
					if (isset($cart['user_data']) && count($cart['user_data']['1']) > 0)
					{
						$userDetails = (object) @$cart['user_data']['1'];
					}
					else
					{
						$userDetails = @$userDetails['billing'];
					}
				}

				$userData['taxCountry'] = @$userDetails->country;
				$userData['taxState'] = @$userDetails->state;
				$userData['taxCity'] = @$userDetails->city;
				$userData['taxZipcode'] = @$userDetails->zip;
			}
			else
			{
				$userData['taxCountry'] = $config->get('general.country', "US");
				$userData['taxState'] = $config->get('general.state', "US");
				$userData['taxCity'] = $config->get('general.city', "US");
				$userData['taxZipcode'] = $config->get('general.zip', "18740");
			}
		}
		else
		{
			// if user is using guest checkout
			$cart = Pago::get_instance('cart')->get();
			$guest = JFactory::getApplication()->input->getInt('guest', '0');
			if($guest)
			{
				// We will assume the first index is the shipping because that is how set_address works
				$userData = array();
				$shipping_address = (object) @$cart['user_data'][0];
				$userData['taxCountry'] = @$shipping_address->country;
				$userData['taxState'] = @$shipping_address->state;
				$userData['taxCity'] = @$shipping_address->city;
				$userData['taxZipcode'] = @$shipping_address->zip;
			}
			else
			{
				$default_tax_address = $config->get('checkout.default_tax_address');

				if ($default_tax_address)
				{
					$userData = array();
					$userData['taxCountry'] = $config->get('general.country', "US");
					$userData['taxState'] = $config->get('general.state', "US");
					$userData['taxCity'] = $config->get('general.city', "US");
					$userData['taxZipcode'] = $config->get('general.zip', "18740");
				}
				else
				{
					$userData = array();
					$userData['taxCountry'] = "";
					$userData['taxState'] = "";
					$userData['taxCity'] = "";
					$userData['taxZipcode'] = "";
				}
			}
		}

		return $userData;
	}

	/**
	 * Calculate discount for the item
	 *
	 */
	public function calc_item_discount( $item_id, $price )
	{
		$discount['amount'] = 20;
		$discount['type'] = 'percent';
		$discount['price'] = $price + ( $price * ($discount['amount']/100) );
		$discount['price'] = $discount['price'];
		return $discount;
	}

	/**
	 * Calculate coupon against cart
	 *
	 */
	public function calc_coupon( $cart, $coupon_code )
	{
	}
	
	public function get_coupon_model(){
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/models');
		return JModelLegacy::getInstance('Coupon', 'PagoModel');
	}
	
	/**
	 * Calculate totals for cart and saves everything into cart
	 *
	 */
	public function calc_cart( &$cart )
	{
		$sub_total_price = 0;

		$defaultCurrency = $this->getDefaultCurrency();

		if($defaultCurrency->symbol != ''){
			$currency_symbol = $defaultCurrency->symbol;	
		}else{
			$currency_symbol = $defaultCurrency->code;	
		}

		if ( isset( $cart['items'] ) ){
			foreach ( $cart['items'] as $k => $item ){
				$cart['items'][$k]->currency_symbol = $currency_symbol;

				if ( !empty( $item->price ) && $item->price > 0) 
				{
					$cart['items'][$k]->subtotal = $item->price * $item->cart_qty;

					$cart['items'][$k]->format_subtotal = $cart['items'][$k]->subtotal;

					$cart['items'][$k]->format_price = $cart['items'][$k]->price;

					$sub_total_price += $cart['items'][$k]->subtotal;
				}
				else
				{
					$cart['items'][$k]->format_subtotal = 0;
					$cart['items'][$k]->format_price = 0;
				}
			}
		}
		
		$coupon_assign_type = false;
		
		if(isset($cart['coupon']['code'])){
			//we need to get the assign type because shipping coupons fall outside
			//normal price adjustment. We need to condition the shipping assign type 5
			$coupon_model = $this->get_coupon_model();
			$coupon_assign_type = $coupon_model->get_assign_category(1, $cart['coupon']['code']);
			$coupon_assign_type = $coupon_assign_type['type'];
		}
		
		//now we apply shipping coupon adjustments to shipping price if applicable
		$calc_ship_disc = false;
		
		if($coupon_assign_type == 5 && $cart['shipping']){
			$coupon = $cart['coupon'];
			$discount = $coupon['discounts'][0];
			$calc_ship_disc = false;
			
			if($discount['is_percent']){
				
				$prate = $discount['percent_rate'];
				$deduction = (($prate/100) * $cart['shipping']);
				$cart['shipping'] = $cart['shipping'] - $deduction;
				$cart['format']['discount'] = $deduction;
				$coupon['discounts'][0]['amount'] = $deduction;
				
				
			} elseif($cart['shipping'] > $discount['amount']) {
				$cart['shipping'] = $cart['shipping'] - $discount['amount'];
				
				$cart['format']['discount'] = $discount['amount'];
			} else {
				
				$cart['format']['discount'] =  $discount['amount'] - $cart['shipping'];
				$cart['shipping'] = 0;
			}
			
			$calc_ship_disc = $cart['format']['discount'];
			
		}
		
		if($coupon_assign_type == 5 && $cart['shipping_excluding_tax']){
			$coupon = $cart['coupon'];
			$discount = $coupon['discounts'][0];
			
			if($discount['is_percent']){
				$prate = $discount['percent_rate'];
				$cart['shipping_excluding_tax'] = $cart['shipping_excluding_tax'] - (($prate/100) * $cart['shipping_excluding_tax']);
			} elseif($cart['shipping_excluding_tax'] > $discount['amount']) {
				$cart['shipping_excluding_tax'] = $cart['shipping_excluding_tax'] - $discount['amount'];
			} else {
				$cart['shipping_excluding_tax'] = 0;
			}
			
		}
		
		//we also check for coupon assign type 5 which is a shipping coupon 
		//and doesn't apply here
		if ( !empty( $cart['coupon'] ) && isset( $cart['coupon']['code'] ) && $coupon_assign_type != 5 )
		{
			$this->apply_coupon($sub_total_price, $cart['coupon']);

			if (isset( $cart['order_tax']))
			{
				$cart['order_tax'] = $cart['order_tax'] - (($cart['order_tax']) * ($cart['coupon']['percent_rate'] / 100));
			}
		}
		
		$accuracy = 2;
		$config = Pago::get_instance('config')->get('global');
		$currency_sym_position = $config->get('general.currency_symbol_display');
		$price_seperator = $config->get('general.price_seperator');
		$cart['subtotal'] = number_format($sub_total_price, $accuracy, $price_seperator, '');//change accuracy
		
		
		if ( array_key_exists('shipping_excluding_tax', $cart) )
		{
			$sub_total_price += $cart['shipping_excluding_tax'];
		}

		if ( isset( $cart['tax'] ) )
		{
			$sub_total_price += $cart['tax'];
		}
		
		if ( isset( $cart['discount'] ) )
		{
			$sub_total_price -= $cart['discount'];
		}

		if ( isset( $cart['coupon']['total'] ) && $coupon_assign_type != 5 )
		{
			//$sub_total_price = $this->apply_coupon($sub_total_price, $cart['coupon']);
		}

		$accuracy = 2;
		$cart['total'] = number_format($sub_total_price, $accuracy, $price_seperator, '');//change accuracy
			 
		$this->format_cart($cart);
		
		if($calc_ship_disc)
			$cart['format']['discount'] = $calc_ship_disc;
			 
		JPluginHelper::importPlugin('pago_cart');
		$dispatcher = KDispatcher::getInstance();
		
		if($calc_ship_disc)
			 $cart['format']['discount'] = $calc_ship_disc;

		$dispatcher->trigger('after_cart_calc', array( &$cart ));

		return true;
	}

	protected function apply_coupon( $subtotal, $coupon )
	{
		$subtotal = $subtotal - $this->number_unformat($coupon['total'], true, '.', ',');

		return $subtotal;
	}

	protected function number_unformat($number, $force_number = true, $dec_point = '.', $thousands_sep = ',') {
		if ($force_number) {
			$number = preg_replace('/^[^\d]+/', '', $number);
		} else if (preg_match('/^[^\d]+/', $number)) {
			return false;
		}
		$type = (strpos($number, $dec_point) === false) ? 'int' : 'float';
		$number = str_replace(array($dec_point, $thousands_sep), array('.', ''), $number);
		settype($number, $type);
		return $number;
	}

	/**
	 * Format cart price totals. if none available insert default 0
	 */

	public function format_cart( &$cart )
	{
		//$places = $this->config->get( 'general.decimal_places', 2);
		//$places = 4;
		
		$cart['format']['subtotal'] = (isset($cart['subtotal'])) ? $cart['subtotal'] : 0;
		$cart['format']['total'] = (isset($cart['total'])) ? $cart['total'] : 0;
		$cart['format']['shipping'] = (isset($cart['shipping_excluding_tax'])) ? $cart['shipping_excluding_tax'] : 0 ;
		$cart['format']['tax'] = (isset($cart['tax'])) ? $cart['tax'] : 0 ;
		$cart['format']['discount'] = (isset($cart['coupon']['total'])) ? $cart['coupon']['total'] : 0;
		$cart['format']['discount'] = (isset($cart['discount'])) ? $cart['discount'] : 0;
	}
	// public function format_cart( &$cart )
	// {
	// 	$cart['format']['subtotal'] =
	// 		(isset($cart['subtotal'])) ? $this->format( $cart['subtotal'] ) : $this->format( 0 );
	// 	$cart['format']['total'] =
	// 		(isset($cart['total'])) ? $this->format( $cart['total'] ) : $this->format( 0 );
	// 	$cart['format']['shipping'] =
	// 		(isset($cart['shipping_excluding_tax'])) ? $this->format( $cart['shipping_excluding_tax'] ) : $this->format( 0 );
	// 	$cart['format']['tax'] =
	// 		(isset($cart['tax']))
	// 		? $this->format( $cart['tax'] ) : $this->format( 0 );
	// 	$cart['format']['discount'] =
	// 		(isset($cart['coupon']['total'])) ? $this->format( $cart['coupon']['total'] ) :
	// 		$this->format( 0 );
	// }
	
	public function calculateAttributePrice( $changeType, $itemPrice, $attrPrice ) {
		switch ($changeType) {
			case '0':
				$price = $attrPrice;
				break;
			case '1':
				$price = ($itemPrice * $attrPrice) / 100;
				break;
			case '2':
				$price = $attrPrice * $itemPrice;
				break;
		}
		return $price;//$this->format();
	}
	public function calculateVarationPrice( $varationId ) {
		$db = JFactory::getDBO();

		 $query = "SELECT v.`item_id`,v.`price` as `varation_price`,v.`price_type`,v.`default` as `v_default`,i.`price` as `item_price`
				FROM #__pago_product_varation AS v
				LEFT JOIN #__pago_items AS i ON ( v.item_id = i.id )
					WHERE v.`id` = ".$varationId." AND v.`var_enable` = 1";

		$db->setQuery( $query );
		$product_varation_price = $db->loadObject();
		if($product_varation_price){
			if($product_varation_price->v_default == 1){
				$price = $product_varation_price->item_price;
			}else{
				switch ($product_varation_price->price_type) {
					case '0':
						$price = $product_varation_price->item_price;
						break;
					case '1':
						$price = $product_varation_price->varation_price;
						break;
					case '2':
						$price = $product_varation_price->item_price + $product_varation_price->varation_price;
						break;
					case '3':
						$price = $product_varation_price->item_price * $product_varation_price->varation_price;
						break;
					case '4':
						$price = $product_varation_price->item_price + (($product_varation_price->item_price * $product_varation_price->varation_price) / 100);
						break;		
				}	
			}
		}
		return $price;
	}

	public function getDefaultCurrency(){
		$db = JFactory::getDBO();

		$sql = "SELECT * FROM #__pago_currency where `default` = 1";

		$db->setQuery($sql);
		$currency = $db->loadObject();
		
		return $currency;
	}
	public function removeNulls($price){
		$new_price = 0;
		if(is_int(substr($price, -1))){
			if(substr($price, -3)===".00"){
				$new_price = substr($price, 0, -3);
			}
		}else{	
			$priceSymbol = preg_replace("/[0-9 . , @]/","", $price);

			$new_price = str_replace($priceSymbol,"",$price);

			if(substr($new_price, -3)===".00"){
				$new_price = substr($new_price, 0, -3);
			}
		}

		$config = Pago::get_instance('config')->get('global');
		$currency_sym_position = $config->get('general.currency_symbol_display');

		if($currency_sym_position== '1'){
			return $priceSymbol.$new_price;
		}
		else{
			return $new_price.$priceSymbol;
		}
	}
	
}
