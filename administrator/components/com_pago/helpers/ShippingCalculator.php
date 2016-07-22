<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

class ShippingCalculator  {
	// Defaults
	var $weight = 1;
	var $weight_unit = "lb";
	var $size_length = 4;
	var $size_width = 8;
	var $size_height = 2;
	var $size_unit = "in";
	var $debug = false; // Change to true to see XML sent and recieved

	// Batch (get all rates in one go, saves lots of time)
	var $batch_ups = false; // Currently Unavailable
	var $batch_usps = true;
	var $batch_fedex = false; // Currently Unavailable

	// Config (you can either set these here or send them in a config array when creating an instance of the class)
	var $services;
	var $from_zip;
	var $from_state;
	var $from_country;
	var $to_zip;
	var $to_state;
	var $to_country;
	var $ups_access;
	var $ups_user;
	var $ups_pass;
	var $ups_account;
	var $usps_user;
	var $fedex_account;
	var $fedex_meter;

	// Results
	var $rates;

	// Setup Class with Config Options
	function ShippingCalculator($config = NULL) {
		if($config) {
			foreach($config as $k => $v) $this->$k = $v;
		}
	}

	// Calculate
	function calculate($company = NULL,$code = NULL) {
		$this->rates = NULL;
		$services = $this->services;
		if($company and $code) $services[$company][$code] = 1;
		foreach($services as $company => $codes) {
			foreach($codes as $code => $name) {
				switch($company) {
					case "ups":
						/*if($this->batch_ups == true) $batch[] = $code; // Batch calculation currently unavaiable
						else*/ $this->rates[$company][$code] = $this->calculate_ups($code);
						break;
					case "usps":
						if($this->batch_usps == true) $batch[] = $code;
						else $this->rates[$company][$code] = $this->calculate_usps($code);
						break;
					case "fedex":
						/*if($this->batch_fedex == true) $batch[] = $code; // Batch calculation currently unavaiable
						else*/ $this->rates[$company][$code] = $this->calculate_fedex($code);
						break;
				}
			}
			// Batch Rates
			//if($company == "ups" and $this->batch_ups == true and count($batch) > 0) $this->rates[$company] = $this->calculate_ups($batch);
			if($company == "usps" and $this->batch_usps == true and count($batch) > 0) $this->rates[$company] = $this->calculate_usps($batch);
			//if($company == "fedex" and $this->batch_fedex == true and count($batch) > 0) $this->rates[$company] = $this->calculate_fedex($batch);
		}

		//echo $company;

		//print_r($this->rates);

		$return = array();

		foreach($this->rates[$company] as $name=>$value){
			 if($value){
				 $return[] = array(
				 	'code' => 0,
					'name' => $name,
					'value' => $value
				 );
			 }
		 }

		return $return;

		return $this->rates;
	}

	// Calculate UPS
	function calculate_ups($code) {
		$url = "https://www.ups.com/ups.app/xml/Rate";
    	$data = '<?xml version="1.0"?>
<AccessRequest xml:lang="en-US">
	<AccessLicenseNumber>'.$this->ups_access.'</AccessLicenseNumber>
	<UserId>'.$this->ups_user.'</UserId>
	<Password>'.$this->ups_pass.'</Password>
</AccessRequest>
<?xml version="1.0"?>
<RatingServiceSelectionRequest xml:lang="en-US">
	<Request>
		<TransactionReference>
			<CustomerContext>Bare Bones Rate Request</CustomerContext>
			<XpciVersion>1.0001</XpciVersion>
		</TransactionReference>
		<RequestAction>Rate</RequestAction>
		<RequestOption>Rate</RequestOption>
	</Request>
	<PickupType>
		<Code>01</Code>
	</PickupType>
	<Shipment>
		<Shipper>
			<Address>
				<PostalCode>'.$this->from_zip.'</PostalCode>
				<CountryCode>'.$this->from_country.'</CountryCode>
			</Address>
		<ShipperNumber>'.$this->ups_account.'</ShipperNumber>
		</Shipper>
		<ShipTo>
			<Address>
				<PostalCode>'.$this->to_zip.'</PostalCode>
				<CountryCode>'.$this->to_country.'</CountryCode>
			<ResidentialAddressIndicator/>
			</Address>
		</ShipTo>
		<ShipFrom>
			<Address>
				<PostalCode>'.$this->from_zip.'</PostalCode>
				<CountryCode>'.$this->from_country.'</CountryCode>
			</Address>
		</ShipFrom>
		<Service>
			<Code>'.$code.'</Code>
		</Service>
		<Package>
			<PackagingType>
				<Code>02</Code>
			</PackagingType>
			<Dimensions>
				<UnitOfMeasurement>
					<Code>IN</Code>
				</UnitOfMeasurement>
				<Length>'.($this->size_unit != "in" ? $this->convert_sze($this->size_length,$this->size_unit,"in") : $this->size_length).'</Length>
				<Width>'.($this->size_unit != "in" ? $this->convert_sze($this->size_width,$this->size_unit,"in") : $this->size_width).'</Width>
				<Height>'.($this->size_unit != "in" ? $this->convert_sze($this->size_height,$this->size_unit,"in") : $this->size_height).'</Height>
			</Dimensions>
			<PackageWeight>
				<UnitOfMeasurement>
					<Code>LBS</Code>
				</UnitOfMeasurement>
				<Weight>'.($this->weight_unit != "lb" ? $this->convert_weight($this->weight,$this->weight_unit,"lb") : $this->weight).'</Weight>
			</PackageWeight>
		</Package>
	</Shipment>
</RatingServiceSelectionRequest>';

		// Curl
		$results = $this->curl($url,$data);

		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}

		// Match Rate
		preg_match('/<MonetaryValue>(.*?)<\/MonetaryValue>/',$results,$rate);

		return $rate[1];
	}

	// Calculate USPS
	function calculate_usps($code) {
		// Weight (in lbs)
		if($this->weight_unit != 'lb') $weight = $this->convert_weight($weight,$this->weight_unit,'lb');
		else $weight = $this->weight;
		// Split into Lbs and Ozs
		$lbs = floor($weight);
		$ozs = ($weight - $lbs)  * 16;
		if($lbs == 0 and $ozs < 1) $ozs = 1;
		// Code(s)
		$array = true;
		if(!is_array($code)) {
			$array = false;
			$code = array($code);
		}

		$url = "http://Production.ShippingAPIs.com/ShippingAPI.dll";
		$data = 'API=RateV2&XML=<RateV2Request USERID="'.$this->usps_user.'">';
		foreach($code as $x => $c) $data .= '<Package ID="'.$x.'"><Service>'.$c.'</Service><ZipOrigination>'.$this->from_zip.'</ZipOrigination><ZipDestination>'.$this->to_zip.'</ZipDestination><Pounds>'.$lbs.'</Pounds><Ounces>'.$ozs.'</Ounces><Size>REGULAR</Size><Machinable>TRUE</Machinable></Package>';
		$data .= '</RateV2Request>';

		// Curl
		$results = $this->curl($url,$data);

		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}

		// Match Rate(s)
		preg_match_all('/<Package ID="([0-9]{1,3})">(.+?)<\/Package>/',$results,$packages);
		foreach($packages[1] as $x => $package) {
			preg_match('/<Rate>(.+?)<\/Rate>/',$packages[2][$x],$rate);
			@$rates[$code[$package]] = $rate[1];
		}
		if($array == true) return $rates;
		else return $rate[1];
	}

	// Calculate FedEX
	function calculate_fedex($code) {
		$url = "https://gatewaybeta.fedex.com/GatewayDC";
		$data = '<?xml version="1.0" encoding="UTF-8" ?>
<FDXRateRequest xmlns:api="http://www.fedex.com/fsmapi" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="FDXRateRequest.xsd">
	<RequestHeader>
		<CustomerTransactionIdentifier>Express Rate</CustomerTransactionIdentifier>
		<AccountNumber>'.$this->fedex_account.'</AccountNumber>
		<MeterNumber>'.$this->fedex_meter.'</MeterNumber>
		<CarrierCode>'.(in_array($code,array('FEDEXGROUND','GROUNDHOMEDELIVERY')) ? 'FDXG' : 'FDXE').'</CarrierCode>
	</RequestHeader>
	<DropoffType>REGULARPICKUP</DropoffType>
	<Service>'.$code.'</Service>
	<Packaging>YOURPACKAGING</Packaging>
	<WeightUnits>LBS</WeightUnits>
	<Weight>'.number_format(($this->weight_unit != 'lb' ? convert_weight($this->weight,$this->weight_unit,'lb') : $this->weight), 1, '.', '').'</Weight>
	<OriginAddress>
		<StateOrProvinceCode>'.$this->from_state.'</StateOrProvinceCode>
		<PostalCode>'.$this->from_zip.'</PostalCode>
		<CountryCode>'.$this->from_country.'</CountryCode>
	</OriginAddress>
	<DestinationAddress>
		<StateOrProvinceCode>'.$this->to_state.'</StateOrProvinceCode>
		<PostalCode>'.$this->to_zip.'</PostalCode>
		<CountryCode>'.$this->to_country.'</CountryCode>
	</DestinationAddress>
	<Payment>
		<PayorType>SENDER</PayorType>
	</Payment>
	<PackageCount>1</PackageCount>
</FDXRateRequest>';

		// Curl
		$results = $this->curl($url,$data);

		// Debug
		if($this->debug == true) {
			print "<xmp>".$data."</xmp><br />";
			print "<xmp>".$results."</xmp><br />";
		}

		// Match Rate
		preg_match('/<NetCharge>(.*?)<\/NetCharge>/',$results,$rate);

		if(isset($rate[1])){
		return $rate[1];
		}
		return false;
	}

	// Curl
	function curl($url,$data = NULL) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if($data) {
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$contents = curl_exec ($ch);

		return $contents;

		curl_close ($ch);
	}

	// Convert Weight
	function convert_weight($weight,$old_unit,$new_unit) {
		$units['oz'] = 1;
		$units['lb'] = 0.0625;
		$units['pound'] = 0.0625;
		$units['gram'] = 28.3495231;
		$units['kg'] = 0.0283495231;

		// Convert to Ounces (if not already)
		if($old_unit != "oz") $weight = $weight / $units[$old_unit];

		// Convert to New Unit
		$weight = $weight * $units[$new_unit];

		// Minimum Weight
		if($weight < .1) $weight = .1;

		// Return New Weight
		return round($weight,2);
	}

	// Convert Size
	function convert_size($size,$old_unit,$new_unit) {
		$units['in'] = 1;
		$units['cm'] = 2.54;
		$units['mm'] = 25.4;
		$units['m'] = 0.0254;
		$units['feet'] = 0.083333;

		// Convert to Inches (if not already)
		if($old_unit != "in") $size = $size / $units[$old_unit];

		// Convert to New Unit
		$size = $size * $units[$new_unit];

		// Minimum Size
		if($size < .1) $size = .1;

		// Return New Size
		return round($size,2);
	}

	// Set Value
	function set_value($k,$v) {
		$this->$k = $v;
	}

	// Check For Shipping Methods In Products
	function get_shipping_methods($prd_id,$str)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT shipping_methods FROM `#__pago_items` WHERE id=" . $prd_id . "";
		$db->setQuery($sql);
		$methods = $db->loadResult();
		$method_array = explode(',', $methods);

		if (!in_array($str, $method_array) && !in_array("0", $method_array))
		{
			return 2;
		}

		$sql = "SELECT * FROM `#__pago_items` WHERE id=" . $prd_id . " AND free_shipping=1";
		$db->setQuery($sql);
		$methods = $db->loadResult();

		if(count($methods) > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	// Check For Free Shipping In Products
	function checkFreeShipping($prd_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM `#__pago_items` WHERE id=" . $prd_id . " AND free_shipping=1";
		$db->setQuery($sql);
		$methods = $db->loadResult();

		if(count($methods) > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}


	function displayShippingOptions($items, $user_id, $saddress_id)
	{
		ob_clean();
		$dispatcher = KDispatcher::getInstance();
		$config = Pago::get_instance('config')->get('global');

		$saved_addresses = Pago::get_instance('users')->get_user_addresses($user_id);

		foreach ( $saved_addresses as $user_address )
		{
			if ( $user_address->id == $saddress_id )
			{
				$shipping_address = $user_address;
				break;
			}
		}

		$orderItems['items'] = array();

		foreach ($items as $item)
		{
			$items_model = JModelLegacy::getInstance('Itemslist', 'PagoModel');
			$items_model->setState('id', $item->itemid);
			$orderItem = $items_model->get($item->itemid);
			$orderItems['items'] [$item->itemid] = $orderItem;
			$orderItems['items'] [$item->itemid]->cart_qty = $item->qty;
		}

		$cart = $orderItems;

		// Shipping options trigger
		JPluginHelper::importPlugin('pago_shippers');
		$shipping_options = array();

		if ($config->get('checkout.shipping_type'))
		{
				foreach ($orderItems['items'] as $item)
				{
						// Shipping options trigger
						$shipping_option = array();
						$tempCart = array();
						$tempCart['items'][$item->id] = $item;
						JPluginHelper::importPlugin('pago_shippers');
						$dispatcher->trigger(
							'set_shipping_options',
							array( &$shipping_option, $tempCart, $shipping_address)
						);

						$shipping_options[$item->id] = $shipping_option;
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

		$shippingHtml = '<div id="pg-shipping-methods">';

		if ($config->get('checkout.shipping_type'))
		{
			$counter = 1;

			foreach ($shipping_options as $product => $shipping_options)
			{
				$shippingHtml .= '<div>' . $cart['items'][$product]->name . '</div>';
				$checkedCounter = 0;
				$checked = false;

				foreach ( $shipping_options as $shipper => $opt )
				{
					if (count($opt) > 0)
					{
						$shippingHtml .= '<div class="pg-shipping-method">
                                <fieldset class="pg-fieldset">
                                    <legend class="pg-legend">' . $shipper . '</legend>';

									foreach ($opt as $shipType => $shipping)
									{
										if ($checkedCounter == 0 && !$checked)
										{
											$checkedStyle = "checked = checked";
											$checked = true;
											$shippingCost += $shipping['value'];
										}

										$shippingHtml .= '<div class="pg-shipper-option">
											<input ' . $checkedStyle . ' type="radio" value="' . $shipper . '|' . $shipType . '|' . $shipping['name'] . '|' . $shipping['value'] . '" name="carrier_option[' . $cart['items'][$product]->id . ']" 0="" id="pg_addorder_product_shipping_type" class="pg-radiobutton required">
											<label for="' . $shipType . @$counter . '" class="pg-label">
												<span class="pg-shipper-option-name">' . $shipping['name'] . '</span> <span class="pg-shipper-option-price">( $' . $shipping['value'] . ')</span>
											</label>
										</div>';
										$checkedStyle = "";
									}

								$shippingHtml .= '</fieldset>
                            </div><input type="hidden" name="pg_item_based_shipping_name' . $cart['items'][$product]->id . '" value="" id="pg_item_based_shipping_name' . $cart['items'][$product]->id . '">';
					}

					$checkedCounter++;
				}

				$counter = $counter + 1;
			}
		}
		else
		{
			$checked = false;

			foreach ( $shipping_options as $shipper => $opt )
			{
				if (count($opt) > 0)
				{
					$shippingHtml .= '<div class="pg-shipping-method">
					<fieldset class="pg-fieldset">
					<legend class="pg-legend">' . $shipper . '</legend>';

					$counter = 0;

					foreach ($opt as $shipType => $shipping )
					{
						if ($counter == 0 && !$checked)
						{
							$checkedStyle = "checked = checked";
							$checked = true;
							$shippingCost = $shipping['value'];

						}

						$shippingHtml .= '<div class="pg-shipper-option">
							<input ' . $checkedStyle . ' type="radio" value="' . $shipper . '|' . $shipType . '|' . $shipping['name'] . '|' . $shipping['value'] . '" name="carrier_option" 0="" id="pg_addorder_shipping' . $counter . '" class="pg-radiobutton required">
							<label for="' . $shipType . @$counter . '" class="pg-label">
								<span class="pg-shipper-option-name">' . $shipping['name'] . '</span> <span class="pg-shipper-option-price">( $' . $shipping['value'] . ')</span>
							</label>
							</div>';
						$counter++;
						$checkedStyle = "";
					}

					$shippingHtml .= '</fieldset>
				    </div>';
				}
			}
		}

		$shippingHtml .= '</div>';

		return $shippingHtml . '###' . $shippingCost;
	}

	public function getItemVolumeShipping($item)
	{
		$length = $item->length ;
		$width = $item->width ;
		$height = $item->height ;
		$tmparr = array($length, $width, $height);
		$switch = array_search(min($tmparr),$tmparr);
		switch($switch)
		{
			case 0:
				$length_q = $length * $item->cart_qty;
				$width_q = $width;
				$height_q = $height;
				break;
			case 1:
				$length_q = $length;
				$width_q = $width * $item->cart_qty;
				$height_q = $height;
				break;
			case 2:
				$length_q = $length;
				$width_q = $width;
				$height_q = $height * $item->cart_qty;
				break;
		}

		$cases = array();
		$cases['length'] = $length_q;
		$cases['width'] = $width_q;
		$cases['height'] = $height_q;
		return $cases;
	}


}
