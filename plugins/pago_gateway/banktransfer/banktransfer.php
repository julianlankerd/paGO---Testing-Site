<?php

/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

require_once ( JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_pago' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'pagoConfig.php');
defined('_JEXEC') or die('Restricted access');

class plgPago_gatewayBanktransfer extends JPlugin
{
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin('pago_gateway', 'banktransfer');
		$this->_params = new JRegistry($this->_plugin->params);

		// Hooks
		KDispatcher::add_filter('payment_set_options', array($this, 'payment_set_options'));
		KDispatcher::add_filter('onPayment', array($this, 'onPayment'));
	}

	public function payment_set_options(&$payment_options, $cart, $user_data)
	{
		$payment_options['banktransfer'] = array(
			'logo' => 'banktransfer.png',
			'name' => $this->_params->get("payment_gateway_name")
		);

		return $payment_options;
	}

	public function onPayment(&$paymentResult, $payment_option, $order)
	{
		if (strtoupper($payment_option) != strtoupper("banktransfer"))
		{
			return;
		}

		$result = new stdClass();
		$result->order_id = $order['details']->order_id;
		$result->paymentGateway = $payment_option;

		// Payment failed: display message to customer
		$result->order_status = "P";
		$result->order_payment_status = 'Unpaid';
		$result->payment_capture_status = '';
		$result->message = JText::_('COM_PAGO_ORDER_PLACED_SUCCESSFULLY');
		$result->txn_id = '';
		$result->isFraud = 0;
		$result->fraudMessage = '';
		$result->cardnumber  = '';
		$paymentResult [$payment_option] = $result;

		return $paymentResult;
	}
}



