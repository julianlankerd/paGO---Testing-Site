<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die();

class plgPago_OrdersPago_maxmind extends JPlugin
{

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin('pago_orders', 'pago_maxmind');
		$this->_params = new JRegistry($this->_plugin->params);

		// Hooks
		KDispatcher::add_filter('on_new_order', array($this, 'on_new_order'));
		KDispatcher::add_filter('checkOrderwithMaxmind', array($this, 'checkOrderwithMaxmind'));
	}
	/* Gets called whan a new one-off order is placed
	 *
	 * @param int $order_id The id of the new order
	 */
	public function on_new_order( $order_id )
	{
		$output = $this->checkOrderwithMaxmind($order_id);
		$isFraud = 0;
		if($output['score'] > 0)
		{
			$isFraud = 1;
		}

		$db = JFactory::getDbo();

		// Prepare query.
		$query = "UPDATE #__pago_orders_sub_payments SET isfraud =  " . $isFraud . " , fraud_message =  '". addslashes($output['explanation']) . "' WHERE order_id = " . (int) $order_id;
		$db->setQuery($query);
		$db->query();


	}

	public function  checkOrderwithMaxmind($order_id, $isAdmin = 0)
	{
		require_once ( JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'pago_orders' . DIRECTORY_SEPARATOR . 'pago_maxmind' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'CreditCardFraudDetection.php');
		$order 	= Pago::get_instance('orders')->get($order_id);


		$billing = $order['addresses']['billing'];
		$shipping = $order['addresses']['shipping'];
		$ip_address = $order['details']->ip_address;


		// Create a new CreditCardFraudDetection object
		$ccfs = new CreditCardFraudDetection();

		// Set inputs and store them in a hash
		// See http://www.maxmind.com/app/ccv for more details on the input fields

		// Enter your license key here (Required)
		$input["license_key"] = $this->_params->get("maxmind_licence_key");//"MteIzkyEfFuw";

		// Required fields
		$input['i']       = $order['details']->ip_address;        // set the client ip address
		$input['city']    = $billing->city;                       // set the billing city
		$input['region']  = $billing->state;                      // set the billing state
		$input['postal']  = $billing->zip;                        // set the billing zip code
		$input['country'] = $billing->country;                    // set the billing country

		$emailDomain = explode("@", $billing->user_email);

		// Recommended fields
		$input['domain']      = $emailDomain[1];      // Email domain
	//	$input['bin']         = '549099';         // bank identification number
		$input['forwardedIP'] = $order['details']->ip_address;    // X-Forwarded-For or Client-IP HTTP Header

		/**
		 * CreditCardFraudDetection.php will take MD5 hash of e-mail address passed
		 * to emailMD5 if it detects '@' in the string.
		 */
		$input['emailMD5'] = $billing->user_email;

		/**
		 * CreditCardFraudDetection.php will take the MD5 hash of the username/password
		 * if the length of the string is not 32.
		 */
		$input['usernameMD5'] = 'test_carder_username';
		$input['passwordMD5'] = 'test_carder_password';

		// Optional fields
	//	$input['binName']         = 'MBNA America Bank';      // bank name
	//	$input['binPhone']        = '800-421-2110';           // bank customer service phone number on back of credit card
		$input['custPhone']       = '212-242';                // Area-code and local prefix of customer phone number
		$input['requested_type']  = 'premium';                // minFraud service type to use ('free', 'standard', 'premium')
		$input['shipAddr']        = $shipping->address_1;    // Shipping Address
		$input['shipCity']        = $shipping->city;              // the City to Ship to
		$input['shipRegion']      = $shipping->state;                     // the Region to Ship to
		$input['shipPostal']      = $shipping->zip;                  // the Postal Code to Ship to
		$input['shipCountry']     = $shipping->country;                     // the country to Ship to11
		$input['txnID']           = $order_id;                   // Transaction ID
		$input['sessionID']       = 'abcd9876';               // Session ID
		$input['accept_language'] = 'en-gb';
		$input['user_agent']      = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_5; en-gb) AppleWebKit/525.18 (KHTML, like Gecko) Version/3.1.2 Safari/525.20.1';

		/**
		 * If you want to enable Secure HTTPS, have Curl and OpenSSL
		 * installed, change the next line to true.
		 */
		$ccfs->isSecure = false;

		// Set the timeout to be five seconds.
		$ccfs->timeout = 10;

		// Uncomment to turn on debugging.
		// $ccfs->debug = true;

		// Add the input array to the object.
		$ccfs->input($input);

		// Query the server.
		$ccfs->query();

		// Get the result from the server.
		$output = $ccfs->output();

		if($isAdmin)
		{
			ob_clean();
			return $output['score']."###".$output['riskScore']."###".$output['explanation'];
		}
		return $output;
	}

	/* Gets called when a new subscription order is placed
	 *
	 * @param int $order_id The id of the new order
	 */
	public function on_new_order_subscription( $order_id )
	{
	}
}
