<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.controller' );

class PagoControllerOrdersi extends JControllerLegacy
{
	public function ipn()
	{
		$session 	= JFactory::getSession();
		$jinput = JFactory::getApplication()->input;
		$gateway = $jinput->get('gateway', '', 'string');

		// Event can be found here
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin('pago_gateway');
		$paymentResult = array();
		
		if(strstr($gateway, 'pago_')){
			$dispatcher->trigger(
				'onGatewayCompletePayment',
				array( &$paymentResult, &$gateway, &$jinput )
			);
		} else {
			$dispatcher->trigger(
				'onAfterAuthorizePayment',
				array( &$paymentResult, &$gateway, &$jinput)
			);
		}

		foreach ( $paymentResult as $pgateway => $result)
		{
			if ($pgateway == $gateway)
			{
				if ( $result->order_status == "P" )
				{
					$order_id = $result->order_id;
					$order 	= Pago::get_instance('orders')->get($order_id);

					Pago::get_instance('cookie')->set('processed', 0);

					$session->set('payment_option', '', 'pago_cart');
					JError::raiseWarning(500, $result->message);
					$return = 'index.php?option=com_pago&view=checkout&task=billing';
					$this->setRedirect($return, $result->message);

					return;
				}

				$order_id = $result->order_id;
				$order 	= Pago::get_instance('orders')->get($order_id);

				Pago::get_instance('orders')->onOrderComplete($order, $result);
				$successMessage = $result->message;
			}
		}

		Pago::get_instance('cookie')->set('processed', 1);

		$checkout_model = JModelLegacy::getInstance('checkout', 'PagoModel');
		$checkout_model->deplete_inventory($order_id);

		if (!empty( $cart['coupon']))
		{
			if (isset($cart['coupon']['code']))
			{
				$coupon = Pago::get_instance('coupons');
				$coupon->set_code($cart['coupon']['code']);
				$coupon->incr_use();
			}
		}

		$return = 'index.php?option=com_pago&view=checkout&task=complete';
		$this->setRedirect($return, $successMessage);

		return;
	}

}
