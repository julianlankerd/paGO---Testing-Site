<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

class plgPago_shippersCustom extends JPlugin
{
	public function __construct($subject, $plugin)
	{
		parent::__construct($subject, $plugin);

		// Set shipping options
		KDispatcher::add_filter('set_shipping_options', array ($this, 'set_options'));
	}

	public function set_options(&$shipping_options, $cart, $user_data)
	{

		$params = $this->params;

		$rules = json_decode($params->get('custom_shipping_rules'));
		$country = $user_data->country;
		$state = $user_data->state;
		$zip = $user_data->zip;
		$weight = 0;
		$orderTotal = 0;
		$path = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_pago' . DIRECTORY_SEPARATOR . 'helpers';
		require_once $path . '/ShippingCalculator.php';
		$ShippingCalculator = new ShippingCalculator;
		$config = Pago::get_instance('config')->get();
		$shipping_type = $config->get('checkout.shipping_type');
		$db = JFactory::getDBO();
		$prd_id = array();
		$categoryFilter = "";
		$itemFilter = "";
		$FreeShipping = true;
		foreach ($cart['items'] as $item)
		{
			$weight = $weight + ( $item->weight * $item->cart_qty );
			$categories = $item->categories;

			if ($shipping_type)
			{
				$isTrue = $ShippingCalculator->get_shipping_methods($item->id, $str = "custom");
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

						$shipping_options[ "Custom Shipping" ] = $options;
						return $shipping_options;
				}
			}else
			{
				$isTrue = $ShippingCalculator->checkFreeShipping($item->id); 
				if ($isTrue)
				{
					continue;
				}
			}
			$FreeShipping = false;
			foreach ($categories as $category)
			{
				$categoryFilter .= "FIND_IN_SET('" . $category['id'] . "', category)";
				$categoryFilter .= " or ";
			}

			$itemFilter .= "FIND_IN_SET('" . $item->id . "', items)";
			$itemFilter .= " or ";
			$orderTotal = $orderTotal + ($item->price * $item->cart_qty);
		}

		$countryFilter = " country = '' OR  country = '" . $country . "'";
		$stateFilter = "FIND_IN_SET('" . $state . "', state)";
		$zipcodeFilter = "FIND_IN_SET('" . $zip . "', zipcode)";
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM `#__pago_custom_shipping_rules` WHERE (( '$orderTotal' BETWEEN order_total_start AND order_total_end) OR (order_total_start = 0 AND order_total_start = 0))
				AND (( '$weight' BETWEEN weight_start AND weight_end)  OR ( weight_end = 0))
				AND ( $categoryFilter category = '' OR $itemFilter items = '') AND ( $countryFilter ) AND ( $stateFilter OR state = '' ) AND ( $zipcodeFilter OR zipcode = '' ) ORDER BY priority ASC";
		$db->setQuery($sql);
		$rates = $db->loadObject();

		$options = array();
		$shipping_options = array();

		if (count($rates) > 0 )
		{
			$options[] = array(
				'code' => $rates->rule_id,
				'name' => $rates->rule_name,
				'value' => $rates->shipping_price
				);
		}
		if(count($options) > 0)
		{
			$shipping_options[ "Custom Shipping" ] = $options;
		}
		if($FreeShipping)
		{
			$shipping_options = array();
			return $shipping_options;
		}

		return $shipping_options;
	}
}
