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

class plgPago_shippersFedex extends JPlugin
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
		require_once dirname(__FILE__) . '/fedex.class.php';

		$cred['Key']           = $this->params->get('Key');
		$cred['Password']      = $this->params->get('Password');
		$cred['AccountNumber'] = $this->params->get('AccountNumber');
		$cred['MeterNumber']   = $this->params->get('MeterNumber');
		$test_mode = $this->params->get('test_mode');

		$path = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_pago' . DIRECTORY_SEPARATOR . 'helpers';
		require_once $path . '/ShippingCalculator.php';
		$ShippingCalculator = new ShippingCalculator;
		$config = Pago::get_instance('config')->get();
		$shipping_type = $config->get('checkout.shipping_type');

		$fedex = new fedex();
		$fedex->setCredentials($cred);

		$fedex->setShipper(
			array (
				'PostalCode' => $this->params->get('PostalCode'),
				'CountryCode' => $this->params->get('CountryCode')
			)
		);
		$fedex->setRecipient(
			array (
				'StreetLines' => array($user_data->address_1, $user_data->address_2),
				'City' => $user_data->city,
				'PostalCode' => $user_data->zip,
				'CountryCode' => $this->params->get('CountryCode')
			)
		);
		$FreeShipping = true;
		foreach ( $cart['items'] as $item )
		{
			if ($shipping_type)
			{
				$isTrue = $ShippingCalculator->get_shipping_methods($item->id, $str = "fedex");
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

						$shipping_options[ "fedex" ] = $options;
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
			$i = 1;
			$db = JFactory::getDBO();
			$sql = "SELECT `code` FROM #__pago_units where `default` = 1 and `type` = 'weight'";
			$db->setQuery($sql);
			$weightunit = $db->loadObject();
			$uom = strtoupper($weightunit->code);

			$sql = "SELECT `code` FROM #__pago_units where `default` = 1 and `type` = 'size'";
			$db->setQuery($sql);
			$sizeunit = $db->loadObject();
			$uol = strtoupper($sizeunit->code);
			$item->weight = $ShippingCalculator->convert_weight($item->weight, $weightunit->code, 'kg');
			$item->length = $ShippingCalculator->convert_size($item->length, $sizeunit->code, 'cm');
			$item->width = $ShippingCalculator->convert_size($item->width, $sizeunit->code, 'cm');
			$item->height = $ShippingCalculator->convert_size($item->height, $sizeunit->code, 'cm');
			$package = array ('SequenceNumber' => $i, 'GroupPackageCount' => $item->cart_qty, 'ItemDescription' => $item->name, 'Weight' => array ('Value' => $item->weight, 'Units' => 'KG' ), 'Dimensions' => array ('Length' => $item->length, 'Width' => $item->width, 'Height' => $item->height, 'Units' => 'CM') );
			$fedex->setPackage($package);
		}

		$shippingTypes = $this->params->get('shippingType', null);

		if (!empty($shippingTypes))
		{
			$shippingTypes = $this->params->get('shippingType');
		}
		elseif (is_string($shippingTypes) || $shippingTypes === null )
		{
			$shippingTypes = array ();
		}
		else
		{
			$shippingTypes = array ();
		}

		//try {
			$shipping_options ['FEDEX'] = $fedex->getRates ( $shippingTypes, $test_mode );
		//} catch ( Exception $e ) {
			//go for flat rate fall over
		//}
		if($FreeShipping)
		{
			$shipping_options = array();
			return $shipping_options;
		}
		return $shipping_options;
	}

	public function generate_link ($shipping_method, $tracking_number)
	{
		if(trim($shipping_method) != 'FEDEX')
		{
			return;
		}
		$link = "<a href='http://www.fedex.com/Tracking?action=track&tracknumbers=".$tracking_number."' target='_blank'>".$tracking_number."</a>";
		return $link;
	}
}
