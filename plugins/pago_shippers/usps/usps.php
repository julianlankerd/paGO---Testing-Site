<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
/**
 * @package		Pago Shipping Plugin
 * @author 		'corePHP' LLC.
 * @copyright 	(C) 2010- 'corePHP' LLC.
 * @license 	GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Support: http://support.corephp.com/
 */

class plgPago_shippersUsps extends JPlugin {

	public function __construct($subject, $plugin)
	{
		parent::__construct($subject, $plugin);

		// Set shipping options
		KDispatcher::add_filter('set_shipping_options', array($this, 'set_options'));
		KDispatcher::add_filter('generate_link', array($this, 'generate_link'));
	}

	public function set_options(&$shipping_options, $cart, $user_data)
	{
		require_once dirname(__FILE__) . '/usps.class.php';
		$path = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_pago' . DIRECTORY_SEPARATOR . 'helpers';
		require_once $path . '/ShippingCalculator.php';
		require_once $path . '/helper.php';
		$ShippingCalculator = new ShippingCalculator;
		$PagoHelper = new PagoHelper;
		$cred ['userName'] = $this->params->get('userId');
		$config = Pago::get_instance('config')->get();
	    $shipping_type = $config->get('checkout.shipping_type');
		$db = JFactory::getDBO();
		$prd_id = array();
		$usps = new usps;
		$usps->setCredentials($cred);
		
		$InternationalShiping = 0;
		
		$FreeShipping = true;
		
		foreach ( $cart['items'] as $item )
		{
			// Convert to pound
			$i = 1;
			$db = JFactory::getDBO();
			$sql = "SELECT `code` FROM #__pago_units where `default` = 1 and `type` = 'size'";
			$db->setQuery($sql);
			$sizeunit = $db->loadObject();
			$uol = $sizeunit->code;

			$sql = "SELECT `code` FROM #__pago_units where `default` = 1 and `type` = 'weight'";
			$db->setQuery($sql);
			$weightunit = $db->loadObject();
			$uom = $weightunit->code;

			// Convert to weight pounds
			$itemWeight = $ShippingCalculator->convert_weight($item->weight, $uom, 'pound');
			$itemTotalWeight = $itemWeight * $item->cart_qty;

			$itemVolData	= $ShippingCalculator->getItemVolumeShipping($item);
			$item->length = $itemVolData['length'];
			$item->width = $itemVolData['width'];
			$item->height = $itemVolData['height'];

			// Convert Length, width, height to Inches
			$itemLength = $ShippingCalculator->convert_size($item->length, $uol, 'in');
			$itemWidth = $ShippingCalculator->convert_size($item->width, $uol, 'in');
			$itemHeight = $ShippingCalculator->convert_size($item->height, $uol, 'in');

			$girth = 2 * ceil($itemWidth) + 2 * ceil($itemHeight);
			$size = (ceil($itemLength) + $girth);
			$prd_id[] = $item->id;
			
			$FreeShipping = true;
		
			if ($shipping_type)
			{
				$isTrue = $ShippingCalculator->get_shipping_methods($item->id, $str = "usps");

				if ($isTrue == 2)
				{
						$options = array();
						//$shipping_options[ "Custom Shipping" ] = $options;
						return $shipping_options;
				}
				if ($isTrue)
				{
						$options = array();
						$options[] = array(
								'code' => 0,
								'name' => 'This Item is Free of Shipping',
								'value' => 0
								);

						$shipping_options[ "USPS" ] = $options;
						return $shipping_options;
				}
			}
			else
			{
				$isTrue = $ShippingCalculator->checkFreeShipping($item->id); 
				
				if ($isTrue)
				{
					continue;
				}
			}
			$FreeShipping = false;

			$machinable = "False";
			$machinable_param = $this->params->get('machinable');

			if ($machinable_param)
			{
				$machinable = "True";
			}

			$shipping_pounds = floor($itemTotalWeight);
			$shipping_ounces = ceil(16 * ($itemTotalWeight - floor($itemTotalWeight)));

			if ($size <= 12)
			{
				$size = "REGULAR";
			}
			else
			{
				$size = "LARGE";
			}

			$InternationalShiping = 1;
			
			if ($user_data->country == "US" || $user_data->country == "FM" || $user_data->country == "VI" || $user_data->country == "PR")
			{
				$InternationalShiping = 0;
				$package = array('Service' => 'ALL',
								 'ZipOrigination' => $this->params->get('PostalCode'),
								 'ZipDestination' => $user_data->zip,
								 'Pounds' => $shipping_pounds,
								 'Ounces' => $shipping_ounces,
								 'Size' => $size,
								 'Width' => $itemWidth,
								 'Length' => $itemLength,
								 'Height' => $itemHeight,
								 'Girth' => $girth,
								 'Machinable' => $machinable,
								 'Container' => 'VARIABLE');
				$usps->setPackage($package);
			}
			else
			{
				$country = $PagoHelper->getCountryName($user_data->country);

				$ValueOfContents = $item->price * $item->qty;
				$package = array('Pounds' => $shipping_pounds,
								 'Ounces' => $shipping_ounces,
								 'Size' => $size,
								 'Width' => $itemWidth,
								 'Length' => $itemLength,
								 'Height' => $itemHeight,
								 'Girth' => $girth,
								 'Machinable' => $machinable,
								 'Container' => 'RECTANGULAR',
								 'MailType'=>'Package',
								 'Country'=> $country->country_name,
								 'ValueOfContents' => $ValueOfContents);
				$usps->setIntlPackage($package);
			}
		}

		if ($InternationalShiping)
		{
			$shippingTypes = $this->params->get('intShippingType', null);

			if (is_string($shippingTypes) && ! empty($shippingTypes))
			{
				$shippingTypes = array($this->params->get('intShippingType'));
			}
			elseif (is_string($shippingTypes) || $shippingTypes === null)
			{
				$shippingTypes = array ();
			}
		}
		else
		{
			$shippingTypes = $this->params->get('shippingType', null);

			if (is_string($shippingTypes) && ! empty($shippingTypes))
			{
				$shippingTypes = array($this->params->get('shippingType'));
			}
			elseif (is_string($shippingTypes) || $shippingTypes === null)
			{
				$shippingTypes = array ();
			}
		}

		try {
			$shipping_options ['USPS'] = $usps->getRates($shippingTypes, $InternationalShiping);
		} catch(Exception $e){
			//try flat rate
		}

		if($FreeShipping)
		{
			$shipping_options['USPS'] = [];

			$shipping_options['USPS'][] = [
				'code' => 0,
				'name' => 'Free Shipping',
				'value' => 0
			];

			return $shipping_options;
		}

		return $shipping_options;
	}

	public function generate_link ($shipping_method, $tracking_number)
	{
		if(trim($shipping_method) != 'USPS')
		{
			return;
		}
		$link = "<a href='https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=".$tracking_number."' target='_blank'>".$tracking_number."</a>";
		return $link;
	}
}
