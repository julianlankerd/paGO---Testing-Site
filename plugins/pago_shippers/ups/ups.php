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



class plgPago_shippersUps extends JPlugin
{

	public function __construct($subject, $plugin)
	{
		parent::__construct($subject, $plugin);

		// Set shipping options
		KDispatcher::add_filter('set_shipping_options', array($this, 'set_options'));
		KDispatcher::add_filter('generate_link', array($this, 'generate_link'));
	}

	public function set_options(&$shipping_options, $cart, $user_data)
	{
		require_once (dirname ( __FILE__ ) . '/ups.class.php');
		$path = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_pago' . DIRECTORY_SEPARATOR . 'helpers';
		require_once $path . '/ShippingCalculator.php';
		require_once $path . '/helper.php';
		$ShippingCalculator = new ShippingCalculator;
		$PagoHelper = new PagoHelper;
		$config = Pago::get_instance('config')->get();
		$shipping_type = $config->get('checkout.shipping_type');

		$cred ['AccessLicenseNumber'] = $this->params->get('AccessLicenseNumber');
		$cred ['UserId'] = $this->params->get('UserId');
		$cred ['Password'] = $this->params->get('Password');
		$cred ['testmode'] = $this->params->get('testmode');

		$ups = new ups();
		$ups->setCredentials($cred);
		$ups->setShipper(array('PostalCode' => $this->params->get('PostalCode'), 'CountryCode' => $this->params->get('CountryCode')));
		$ups->setRecipient(array('PostalCode' => $user_data->zip, 'CountryCode' => $user_data->country));
		$FreeShipping = true;
		foreach ( $cart['items'] as $item )
		{
			$prd_id[] = $item->id;

	
			if ($shipping_type)
			{
				$isTrue = $ShippingCalculator->get_shipping_methods($item->id, $str = "ups");

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

						$shipping_options[ "UPS" ] = $options;
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
			// Convert to pound
			$i = 1;
			$db = JFactory::getDBO();

			//  Unit for length
			$sql = "SELECT `code` FROM #__pago_units where `default` = 1 and `type` = 'size'";
			$db->setQuery($sql);
			$sizeunit = $db->loadObject();
			$uol = $sizeunit->code;

			//  Unit for weight
			$sql = "SELECT `code` FROM #__pago_units where `default` = 1 and `type` = 'weight'";
			$db->setQuery($sql);
			$weightunit = $db->loadObject();
			$uom = $weightunit->code;


			// Convert to weight pounds
			$itemWeight = $ShippingCalculator->convert_weight($item->weight, $uom, 'lb');
			$itemTotalWeight = ceil($itemWeight * $item->cart_qty);

			$itemVolData	= $ShippingCalculator->getItemVolumeShipping($item);
			$item->length = $itemVolData['length'];
			$item->width = $itemVolData['width'];
			$item->height = $itemVolData['height'];
			

			// Convert Length, width, height to Inches
			$itemLength = $ShippingCalculator->convert_size($item->length, $uol, 'in');
			$itemWidth = $ShippingCalculator->convert_size($item->width, $uol, 'in');
			$itemHeight = $ShippingCalculator->convert_size($item->height, $uol, 'in');

			// Set Minimum and Maximun Weight
			if ($itemTotalWeight < 1)
			{
				$itemTotalWeight = 1;
			}

			if ($itemTotalWeight > 150)
			{
				$itemTotalWeight = 150.00;
			}

			$package = array ('Weight' => array ('Value' => $itemTotalWeight, 'Units' => $uom ),'Dimensions' => array ('Length' => $itemLength, 'Width' => $itemWidth, 'Height' => $itemHeight, 'Units' => $uol) );
			
			$ups->setPackage($package);
		}

		$shippingTypes = $this->params->get('shippingType', null);
		$handling_fee = $this->params->get('handling_fee', 0);

		if (is_string($shippingTypes) && ! empty($shippingTypes))
		{
			$shippingTypes = array($this->params->get('shippingType'));
		}
		elseif (is_string($shippingTypes) || $shippingTypes === null)
		{
			$shippingTypes = array ();
		}

		try
		{
			$shipping_options ['UPS'] = $ups->getRates($shippingTypes);
			
			$error = @$ups->get('_rateReply')->Response->Error;

			if($error)
				@$shipping_options ['UPS']['error']['error'] = (array)$error;
		}
		catch ( Exception $e )
		{
			// Try flat
		}

		if($FreeShipping)
		{
			$shipping_options['UPS'] = [];

			$shipping_options['UPS'][] = [
				'code' => 0,
				'name' => 'Free Shipping',
				'value' => 0
			];

			return $shipping_options;
		}
		foreach ($shipping_options ['UPS'] as $key => $ups) {
			$shipping_options ['UPS'][$key]['value'] = $ups['value'] + $handling_fee;
		}
		return $shipping_options;
	}
	public function generate_link ($shipping_method, $tracking_number)
	{
		if(trim($shipping_method) != 'UPS')
		{
			return;
		}
		$link = "<a href='http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=".$tracking_number."' target='_blank'>".$tracking_number."</a>";
		return $link;
	}
}

