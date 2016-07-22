<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_orders
{
	function get($order_id)
	{

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/models');
		$orders_model = JModelLegacy::getInstance('Orders', 'PagoModel');
		$orders_model->setState('order_id', $order_id);
		$order = $orders_model->getOrder();

		return $order;
	}

	function get_order_status( $order_status )
	{

		switch ( $order_status )
		{
			case 'P':
				return JText::_('PAGO_ORDER_STATUS_PENDING');
			break;
			case 'C':
				return JText::_('PAGO_ORDER_STATUS_COMPLETED');
			break;
			case 'X':
				return JText::_('PAGO_ORDER_STATUS_CANCELLED');
			break;
			case 'R':
				return JText::_('PAGO_ORDER_STATUS_REFUNDED');
			break;
			case 'S':
				return JText::_('PAGO_ORDER_STATUS_SHIPPED');
			break;
			case 'PA':
				return JText::_('PAGO_ORDER_STATUS_AUTHORIZED_ONLY');
			break;
			default:
				return JText::_('PAGO_ORDER_STATUS_PENDING');
		}
	}

	function get_all_order_status()
	{
		$orderStatus = array();
		$orderStatus[0]['text'] = JText::_('PAGO_PENDING');
		$orderStatus[0]['value'] = 'P';
		$orderStatus[1]['text'] = JText::_('PAGO_COMPLETED');
		$orderStatus[1]['value'] = 'C';
		$orderStatus[2]['text'] = JText::_('PAGO_CANCELLED');
		$orderStatus[2]['value'] = 'X';
		$orderStatus[3]['text'] = JText::_('PAGO_REFUNDED');
		$orderStatus[3]['value'] = 'R';
		$orderStatus[4]['text'] = JText::_('PAGO_SHIPPED');
		$orderStatus[4]['value'] = 'S';
		$orderStatus[5]['text'] = JText::_('PAGO_AUTHORIZED_NOT_CAPTURED');
		$orderStatus[5]['value'] = 'PA';



		return $orderStatus;
	}

	function checkOrderwithMaxmind($order_id)
	{
		// tigger event so we can do stuff when a new order is placed
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin( 'pago_orders' );
		$maxmindResult = $dispatcher->trigger(
			'checkOrderwithMaxmind',
			array( &$order_id, 1 )
		);

		if ($maxmindResult[0])
		{
			$maxmindResultExplode = explode("###", $maxmindResult[0]);

			$isFraud = 0;
			if($maxmindResultExplode[0] > 0)
			{
				$isFraud = 1;
			}

			$db = JFactory::getDbo();

			// Prepare query.
			$query = "UPDATE #__pago_orders_sub_payments SET isfraud =  " . $isFraud . " , fraud_message =  '". addslashes($maxmindResultExplode[2]) . "' WHERE order_id = " . (int) $order_id;
			$db->setQuery($query);
			$db->query();
		}

		return $maxmindResult;

	}

	function updateOrderSatus($order_id, $order_status,$tracking_number,$item_id='0')
	{
		// Initialiase variables.
		$db = JFactory::getDbo();

		// For capture Order
		// Get Order Data
		$order 	= Pago::get_instance('orders')->get($order_id);
		// For Capture Payment
		$payment_gateway = $order['details']->payment_gateway;

		// Event can be found here
		jimport('joomla.html.parameter');
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin('pago_gateway');
		$plugin = JPluginHelper::getPlugin('pago_gateway', $payment_gateway);
		$pluginParams = new JRegistry($plugin->params);
		$capture_event = $pluginParams->get('capture_event', '0');

		$payment = $order['payment'][0];
		if($payment->payment_capture_status == 'Authorized')
		{
			if(($order_status == "C" && $capture_event == "order_cmpl") || ($order_status == "S" && $capture_event == "order_shipped") )
			{
				$captureResult = $this->captureOrderPayment($order_id);
				$captureResult = explode("###", $captureResult);
				if($captureResult[1] == 'fail')
				{
					$orderStatusMessage = JText::_('COM_PAGO_ORDER_STATUS_NOT_UPDATED_DUE_TO_CAPTURE_FAIL');
					$captureMessage = $captureResult[0];
					return $orderStatusMessage." -- ".JText::_('COM_PAGO_ORDER_CAPTURE_MESSAGE_LBL')."&nbsp;".$captureMessage;
				}

				if($order_status == "C" && $capture_event == "order_shipped")
				{
					$captureMessage = JText::_('COM_PAGO_ORDER_CAPTURE_WILL_DONE_ON_SHIPPED');
				}
			}
		}
		else
		{
			$captureMessage = JText::_('COM_PAGO_ORDER_CAPTURE_IS_ALREADY_DONE');
		}

		// Prepare query.
		$query = "UPDATE #__pago_orders SET order_status =  " . $db->quote($order_status) . " WHERE order_id = " . (int) $order_id;
		$db->setQuery($query);
		$db->query();

		// set shipping tracking number
		if($order_status == 'S')
		{
			if($item_id == '0')
			{
				$query = "UPDATE #__pago_orders SET tracking_number =  '" . $tracking_number . "' WHERE order_id = " . (int) $order_id;
				$db->setQuery($query);
				$db->query();
			}
			else
			{
				$query = "UPDATE #__pago_orders_items SET tracking_number =  '" . $tracking_number . "', order_item_status = '" . $order_status . "' WHERE order_id = " . (int) $order_id . " AND item_id= " . $item_id ;
				$db->setQuery($query);
				$db->query();
			}
		} 
		else
		{
			if($item_id != '0')
			{
				$query = "UPDATE #__pago_orders_items SET order_item_status = '" . $order_status . "' WHERE order_id = " . (int) $order_id . " AND item_id= " . $item_id ;
				$db->setQuery($query);
				$db->query();
			}
		}
		
		$order_status_txt = $this->get_order_status($order_status);
		$message = JText::_('COM_PAGO_ORDER_STATUS_UPDATED')." to ".$order_status_txt;
		$order_date = date('Y-m-d H:i:s', time());

		$query = 'INSERT INTO #__pago_orders_log (orderid, date, order_status, action, description) VALUES ( "' . $order_id . '", "' . addslashes($order_date) . '", "' . $order_status_txt . '", "update_order_status","' . $message . '")';
		$db->setQuery($query);
		$db->query();



		$orderStatusMessage = JText::_('COM_PAGO_ORDER_STATUS_UPDATED');
		$order 	= Pago::get_instance('orders')->get($order_id);
		$this -> sendOrderUpdateMail($order);
		return $orderStatusMessage." -- ".JText::_('COM_PAGO_ORDER_CAPTURE_MESSAGE_LBL')."&nbsp;".$captureMessage;

	}
	
	function sendOrderUpdateMail($orderData)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT pgemail_name,pgemail_body FROM `#__pago_mail_templates` WHERE pgemail_type = 'email_update_order_status' AND pgemail_enable =1 AND template_for='site'";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		$subject = $res[0]->pgemail_name;
		$mail_body = $res[0]->pgemail_body;
		// trigger for  tracking number link
		if($orderData['details']->tracking_number != "" && $orderData['details']->order_status == 'S')
		{
			$shipping_method = explode("-", $orderData['details']->ship_method_id);
			
			$dispatcher = KDispatcher::getInstance();
			JPluginHelper::importPlugin('pago_shippers');
			$tracking_number = $dispatcher->trigger(
				'generate_link',
				array($shipping_method[0], $orderData['details']->tracking_number)
			);
			$mail_body = str_replace("{tracking_number}", $tracking_number[0], $mail_body);
		}
		else
		{
			$mail_body = str_replace("{tracking_number}", '', $mail_body);
		}

		$orderDetailLink = '';
		$orderDetailLink = JURI::ROOT() . "index.php?option=com_pago&view=account&layout=order_receipt&status=true&order_id=" . $orderData['details']->order_id;
		$mail_body = $this->replaceOrderDetailsInformations($orderData, $mail_body);
		$mail_body = str_replace("{order_id}", "#".$orderData['details']->order_id, $mail_body);
		$mail_body = str_replace("{order_status_msg}", $this->get_order_status($orderData['details']->order_status), $mail_body);
		$mail_body = str_replace("{order_detail_front_link}", $orderDetailLink, $mail_body);
		$mail_body = str_replace("{order_id_lbl}", JTEXT::_("COM_PAGO_ORDER_ID"), $mail_body);
		$mail_body = str_replace("{order_status_lbl}", JTEXT::_("COM_PAGO_ORDER_STATUS_LBL"), $mail_body);
		$mail_body = str_replace("{tracking_number_lbl}", JTEXT::_("COM_PAGO_ORDER_TRACKING_NUMBER_LBL"), $mail_body);
		$mail_body = str_replace("{order_detail_link_lbl}", JTEXT::_("COM_PAGO_ORDER_DETAIL_LINK_LBL"), $mail_body);
		// Prepare shipping Address
		$store_cfg 	= Pago::get_instance('config')->get();
		$from = array( $store_cfg->get('general.store_email'), $store_cfg->get('general.pago_store_name') );
		$subject = JTEXT::_("COM_PAGO_ORDER_STATUS_FOR")." # ".$orderData['details']->order_id." ".$this->get_order_status($orderData['details']->order_status);

		// Send to use
		$this->send_email($subject, $mail_body, $orderData['addresses']['billing']->user_email, $from, true);
		
	}

	function captureOrderPayment($order_id)
	{
		// Get Order Data
		$order 	= Pago::get_instance('orders')->get($order_id);
		// For Capture Payment
		$payment_gateway = $order['details']->payment_gateway;

		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin('pago_gateway');
		$captureResult = array();
		$dispatcher->trigger(
			'onCapturePayment',
			array( &$captureResult, &$payment_gateway, &$order)
		);

		foreach ( $captureResult as $pgateway => $result)
		{
			if ($pgateway == $payment_gateway)
			{
				$message = $result->message;
				Pago::get_instance('orders')->onOrderCaptured($order, $result);
				return $message."###".$result->rslt;
			}
		}
	}

	function refundOrderPayment($order_id, $refund_amount)
	{
		ob_clean();

		// Get Order Details
		$order 	= Pago::get_instance('orders')->get($order_id);
		$payment = $order['payment'][0];
		$payment_gateway = explode("-", $payment->payment_data);
		$payment_gateway = trim($payment_gateway[0]);
		$order['payment'][0]->refund_amount = $refund_amount;
		
		$old_refund_amount = $this->getOrderRefundAmount($order_id, true);
		$test_refund_amount = $refund_amount + $old_refund_amount;

		if($test_refund_amount > $order['details']->order_total){
			
			$remaining_amount = $order['details']->order_total - $old_refund_amount;
			
			return json_encode(array(
				'success' => false,
				'message' => JText::_( 'PAGO_REFUND_MAY_NOT_EXCEED_PAYMENT' ),
				'refund_amount' => $this->getOrderRefundAmount($order_id),
				'remaining_amount' => $remaining_amount
			));
		}
		
		// Load Gateway Plugin
		$dispatcher = KDispatcher::getInstance();
		
		JPluginHelper::importPlugin('pago_gateway');
		
		$refundResult = array();
		
		$dispatcher->trigger(
			'onRefundPayment',
			array( &$refundResult, &$payment_gateway, &$order)
		);

		foreach ( $refundResult as $pgateway => $result)
		{
			if ($pgateway == $payment_gateway)
			{
				$message = $result['message'];
				$success = $result['success'];
			}
		}
		
		if($success) $this->onOrderRefunded($order_id, $refund_amount);
		
		$refund_amount = $this->getOrderRefundAmount($order_id, true);
		$remaining_amount = $order['details']->order_total - $refund_amount;
		$remaining_amount = Pago::get_instance( 'price' )->format($remaining_amount);
	
		return json_encode(array(
			'success' => $success,
			'message' => $message,
			'refund_amount' => $this->getOrderRefundAmount($order_id),
			'remaining_amount' => $remaining_amount
		));
		
		// fuckwits
		// if ($success)
		// {
		// 	$this->onOrderRefunded($order_id, $refund_amount);
		// 	$refund_amount = $this->getOrderRefundAmount($order_id);
		// 	$result->message = $result->message."(".$order['payment'][0]->refund_amount.")";
		// 	$this->setOrderLog($order,$result,'refund_order');

		// }

		// return $refund_amount."###".$order['details']->order_total."###".$message;
	}

	function getOrderRefundAmount($order_id, $dont_format=false)
	{
		// Initialiase variables.
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// Prepare query.
		$query->select('orders.order_refundtotal');
		$query->from('#__pago_orders AS orders');
		$query->where('orders.order_id = ' . $order_id);

		// Inject the query and load the result.
		$db->setQuery($query);
		$refund_amount = $db->loadObject();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());

			return null;
		}
		
		if($dont_format)
			return $refund_amount->order_refundtotal;
			
		return Pago::get_instance( 'price' )->format($refund_amount->order_refundtotal);
	}

	function onOrderRefunded($order_id, $refund_amount)
	{
		// Initialiase variables.
		$db = JFactory::getDbo();

		// Prepare query.
		$query = "UPDATE #__pago_orders SET order_refundtotal =  order_refundtotal + " . $refund_amount . " WHERE order_id = " . (int) $order_id;
		$db->setQuery($query);
		$db->query();
	}

	function onOrderComplete($order, $paymentResult)
	{
		if (@$paymentResult->isFraud)
		{
			$this->sendFraudEmailNotification($order, $paymentResult);
		}
		$order_id = $order['details']->order_id;
		// Initialiase variables.
		$db = JFactory::getDbo();

		// Prepare query.
		$query = "UPDATE #__pago_orders SET order_status =  " . $db->quote(@$paymentResult->order_status) . " , payment_message =  " . $db->quote(@$paymentResult->message) . " WHERE order_id = " . (int) $order['details']->order_id;
		$db->setQuery($query);
		$db->query();
		
		//check if order already exists otherwise we get duplicate error
		//not sure why because this sub payments table implies multiple payments
		//yet it is bound to only one per order id....
		//because subscriptions :)
		$db->setQuery("
			SELECT order_id 
				FROM #__pago_orders_sub_payments 
					WHERE order_id = {$order['details']->order_id}
		");
		
		$order_exists = $db->loadResult();
		
		if(!$order_exists){
			$db->setQuery("
				INSERT INTO #__pago_orders_sub_payments
					SET
						order_id = '{$order['details']->order_id}',
						item_id = '{$order['details']->order_id}',
						txn_id = '{$paymentResult->txn_id}',
						payment = '{$order['details']->order_total}',
						status = '{$paymentResult->order_status}',
						payment_data = '{$paymentResult->paymentGateway} - {$paymentResult->message}',
						card_number = '{$paymentResult->cardnumber}',
						payment_capture_status = '{$paymentResult->payment_capture_status}',
						isfraud = '{$paymentResult->isFraud}',
						fraud_message = '{$paymentResult->fraudMessage}'
			");
			
			$db->query();
		} else {
			$db->setQuery("
				UPDATE #__pago_orders_sub_payments
					SET
						txn_id = '{$paymentResult->txn_id}',
						payment = '{$order['details']->order_total}',
						status = '{$paymentResult->order_status}',
						payment_data = '{$paymentResult->paymentGateway} - {$paymentResult->message}',
						card_number = '{$paymentResult->cardnumber}',
						payment_capture_status = '{$paymentResult->payment_capture_status}',
						isfraud = '{$paymentResult->isFraud}',
						fraud_message = '{$paymentResult->fraudMessage}'
					WHERE order_id = '{$order['details']->order_id}'
			");
			
			//seems dangerous to update this payment as it is possible to get null values
			//hence for now the update query is commented out...
			//$db->query();
		}
		
		//add subscription data to order item
		if(!empty($paymentResult->subs))
				foreach($paymentResult->subs as $subscription){
					$sub_payment_data = json_encode($subscription);
					$db->setQuery("
						UPDATE #__pago_orders_items
							SET
								sub_status = '1',
								sub_payment_data = '{$sub_payment_data}'
							WHERE order_id = {$order['details']->order_id} 
								AND item_id = {$subscription->metadata->item_id}
					");
					
					$db->query();
				}
		
		// tigger event so we can do stuff when a new order is placed
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin( 'pago_orders' );
		$dispatcher->trigger(
			'on_new_order',array( &$order_id )
		);

		$order 	= Pago::get_instance('orders')->get($order_id);
		$this->setOrderLog($order,$paymentResult);
		
		$this->sendInvoiceEmailNotifications($order);
	}

	function setOrderLog($order,$paymentResult, $action="order_placed")
	{
		// Initialiase variables.
		$db = JFactory::getDbo();
		$order_id = $order['details']->order_id;
		$order_date = $order['details']->cdate;
		$order_status = $this->get_order_status($order['details']->order_status);
		$message = $paymentResult->message;
		$query = 'INSERT INTO #__pago_orders_log (orderid,date,action,order_status,description) VALUES ( "' . $order_id . '", "' . addslashes($order_date) . '","' . $action . '","' . $order_status . '","' . $message . '")';
		$db->setQuery($query);
		$db->query();
	}


	function onOrderFail($order, $paymentResult)
	{
		if ($paymentResult->isFraud)
		{
			$this->sendFraudEmailNotification($order, $paymentResult);
		}
		$this->setOrderLog($order,$paymentResult);
		// Initialiase variables.
		$db = JFactory::getDbo();

		// Prepare query.
		$query = "UPDATE #__pago_orders SET order_status =  " . $db->quote($paymentResult->order_status) . " , payment_message =  " . $db->quote($paymentResult->message) . " WHERE order_id = " . (int) $order['details']->order_id;
		$db->setQuery($query);
		$db->query();

		$db->setQuery("
			INSERT INTO #__pago_orders_sub_payments
				SET
					order_id = '{$order['details']->order_id}',
					item_id = '{$order['details']->order_id}',
					txn_id = '{$paymentResult->txn_id}',
					payment = '{$order['details']->order_total}',
					status = '{$paymentResult->order_status}',
					payment_data = '{$paymentResult->paymentGateway} - {$paymentResult->message}',
					card_number = '{$paymentResult->cardnumber}',
					isfraud = '{$paymentResult->isFraud}',
					fraud_message = '{$paymentResult->fraudMessage}'
		");

		$db->query();
		$order_id = $order['details']->order_id;
		$order 	= Pago::get_instance('orders')->get($order_id);
		$this->setOrderLog($order,$result,'refund_order');

	}

	function sendFraudEmailNotification ($order, $paymentResult)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT pgemail_name,pgemail_body FROM `#__pago_mail_templates` WHERE pgemail_type = 'fraud_order_email' AND pgemail_enable =1 AND template_for='admin'";
		$db->setQuery($sql);
		$res = $db->loadObjectList();

		$subject = $res[0]->pgemail_name;
		$mail_body_desc = $res[0]->pgemail_body;

		$mail_body = $this->replaceOrderDetailsInformations($order, $mail_body_desc);

		// Replace Fraud Data
		$mail_body = str_replace("{fraud_data}", $paymentResult->fraudMessage, $mail_body);

		// Prepare shipping Address
		$store_cfg 	= Pago::get_instance('config')->get();

		$from = array( $store_cfg->get('general.store_email'), $store_cfg->get('general.pago_store_name') );

		// Send to admin
		if($mail_body != "")
		{
			$this->send_email($subject, $mail_body, $from[0], $from, true);
		}
	}

	function getVariantDetails($varationId, $id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT id, name, sku FROM #__pago_product_varation AS pv WHERE pv.id='".$varationId."' AND pv.item_id=".$id;
		$db->setQuery($sql);
		$res = $db->loadObject();
		return $res;
	}

	function replaceItems($item_template, $order)
	{
		$items='';

		for ($j = 0;$j < count($order['items']);$j++)
		{
			$mail_item_tmplt = $item_template;

			$prd_name = $order['items'][$j]->name;
			$prd_sku = $order['items'][$j]->sku;
			if($order['items'][$j] -> varation_id)
			{
				$varDetails = $this->getVariantDetails($order['items'][$j] -> varation_id, $order['items'][$j]->id);
				$prd_sku = $varDetails ->sku ;
				$prd_name = $varDetails -> name;
			}

			$prd_qty = $order['items'][$j]->qty;
			$prd_price = Pago::get_instance('price')->format($order['items'][$j]->price);
			$order_item_ship_method_id = $order['items'][$j]->order_item_ship_method_id;
			$order_item_shipping = $order['items'][$j]->order_item_shipping;
			$prd_ship_method = $order['items'][$j]->shipping_methods;
			$mail_item_tmplt = str_replace("{item_name}", $prd_name, $mail_item_tmplt);
			$mail_item_tmplt = str_replace("{item_sku}", $prd_sku, $mail_item_tmplt);
			$mail_item_tmplt = str_replace("{item_quantity}", $prd_qty, $mail_item_tmplt);
			$mail_item_tmplt = str_replace("{item_price}", $prd_price, $mail_item_tmplt);

			// Attribute start
			$attributes = '';
			$attributes = $this ->getAttribHtml($order['items'][$j] -> attributes);
			$mail_item_tmplt = str_replace("{item_attribute}", $attributes, $mail_item_tmplt);
			// Attribute end

			$config = Pago::get_instance('config')->get();
			$shipping_type = $config->get('checkout.shipping_type');

			if ($shipping_type == 1 && $order_item_ship_method_id != "")
			{
				$mail_item_tmplt = str_replace("{order_item_ship_method}", $order_item_ship_method_id . "(" . $order_item_shipping . ")", $mail_item_tmplt);

				if($order['items'][$j] -> order_item_status == 'S' && $order['items'][$j]->tracking_number != "")
				{
					$shipping_method = explode("-", $order['items'][$j]->order_item_ship_method_id);

					$dispatcher = KDispatcher::getInstance();
					JPluginHelper::importPlugin('pago_shippers');
					$tracking_number = $dispatcher->trigger(
						'generate_link',
						array($shipping_method[0], $order['items'][$j]->tracking_number)
					);

					$mail_item_tmplt = str_replace("{item_tracking_number}", $tracking_number[0], $mail_item_tmplt);
					$mail_item_tmplt = str_replace("{item_tracking_number_lbl}",  JText::_('COM_PAGO_ITEM_TRACK_NO_LBL'), $mail_item_tmplt);
				}
				else
				{
					$mail_item_tmplt = str_replace("{item_tracking_number}", '', $mail_item_tmplt);
					$mail_item_tmplt = str_replace("{item_tracking_number_lbl}",  '', $mail_item_tmplt);
				}
			}
			else
			{
				$mail_item_tmplt = str_replace("{order_item_ship_method}", '', $mail_item_tmplt);
				$mail_item_tmplt = str_replace("{item_tracking_number}", '', $mail_item_tmplt);
				$mail_item_tmplt = str_replace("{item_tracking_number_lbl}",  '', $mail_item_tmplt);
			}

			$items .= $mail_item_tmplt;
		}

		return $items;
	}
	
	//get subscription html for order items admin
	public static function getSubscrHtml($subscr, $order_item_id)
	{
		$plan = $subscr->plan;
		
		$item_id = $plan->metadata->item_id;
		
		$subscr->installments = @$subscr->metadata->installments;
		$subscr->enddate = @$subscr->metadata->enddate;
		
		unset($plan->metadata);
		unset($subscr->metadata);
		unset($subscr->plan);
		
		$plan->created = date('r', $plan->created);
		$plan->amount = money_format('%i', ($plan->amount / 100));
		
		$subscr->application_fee = $subscr->application_fee_percent * $plan->amount / 100;
		unset($subscr->application_fee_percent);
		$subscr->current_period_end = date('r', $subscr->current_period_end);
		$subscr->current_period_start = date('r', $subscr->current_period_start);
		$subscr->start = date('r', $subscr->start);
		$subscr->trial_end = date('r', $subscr->trial_end);
		$subscr->trial_start = date('r', $subscr->trial_start);
		
		if($subscr->cancel_at_period_end)
			$subscr->canceled_at = '<strong>' . date('r', $subscr->canceled_at) . '</strong>';
	
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', '', 'array');
		
		if($cid)
			$cid = $cid[0];

		ob_start();
?>
		<a style="color: rgb(255, 255, 255);" id="" class="label label-success" data-toggle="modal" data-target="#modal<?php echo $item_id ?>">Manage Subscription</a>
		
		<?php if($subscr->cancel_at_period_end): ?>
		<span style="color: rgb(255, 255, 255);" class="label label-warning">Cancelled</span>
		<?php endif ?>
		
		<div id="modal<?php echo $item_id ?>" class="modal fade" role="dialog" style="width:50%">
		  <div class="modal-dialog">
		
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h2 class="modal-title"><?php echo $plan->name ?>
		        
		        <?php if(!$subscr->cancel_at_period_end): ?>
		
					<a id="subscr_cancel<?php echo $item_id ?>" style1="position:absolute;bottom:50px;right:200px" 
						class="pg-btn-medium pg-btn-light pg-btn-red" 
						href="<?php echo JRoute::_('index.php?option=com_pago&view=ordersi&task=subscr_cancel&id=' . $subscr->id . '&order_item_id=' . $order_item_id . '&customer=' . $subscr->customer . '&cid='. $cid ) ?>" 
						role="button">
						Cancel Subscription
					</a>
					
					<?php else: ?>
					
					<a id="subscr_cancel<?php echo $item_id ?>" style1="position:absolute;bottom:50px;right:200px" 
						class="pg-btn-medium pg-btn-light pg-btn-green" 
						href="<?php echo JRoute::_('index.php?option=com_pago&view=ordersi&task=subscr_reinstate&id=' . $subscr->id . '&order_item_id=' . $order_item_id . '&customer=' . $subscr->customer . '&cid='. $cid ) ?>" 
						role="button">
						Re-instate Subscription
					</a>
							
				<?php endif ?>
		        
		        </h2>
		      </div>
		      <div class="modal-body">
		      	<p><?php echo JText::_('PAGO_ITEM_SUBSCR_DESC') ?></p>
		      	
		      	
		      	<div class="row-fluid">
					<div class="span6">
						<h4 class="modal-title"><?php echo JText::_('PAGO_ITEM_SUBSCR_SUBSCR_TITLE') ?></h4>
						<dl class="dl-horizontal">
							<?php foreach($subscr as $name=>$value) : 
								$name = str_replace('_', ' ', $name);
								$name = strtoupper($name);
								if(!$value) $value = '0';
							?>
								<dt><?php echo $name ?></dt>
								<dd><?php echo $value ?>&nbsp;</dd>
							<?php endforeach ?>
						</dl>
						
					</div>
					<div class="span6">
						
						<h4 class="modal-title"><?php echo JText::_('PAGO_ITEM_SUBSCR_PLAN_TITLE') ?></h4>
						<dl class="dl-horizontal">
							<?php foreach($plan as $name=>$value) : 
								$name = str_replace('_', ' ', $name);
								$name = strtoupper($name);
								if(!$value) $value = '0';
							?>
								<dt><?php echo $name ?></dt>
								<dd><?php echo $value ?>&nbsp;</dd>
							<?php endforeach ?>
							 <script>
						      	jQuery(function() {
								    jQuery('#subscr_cancel<?php echo $item_id ?>').click(function() {
								        return window.confirm("Are you sure?");
								    });
								});
						      </script>
						      
						    
		
						</dl>
						
						
					</div>
				</div>

		      </div>
		     
		      <div class="modal-footer">
		        <button type="button" href="index.php" class="btn btn-default" data-dismiss="modal"><?php echo JText::_('PAGO_ITEM_SUBSCR_DONE') ?></button>
		      </div>
		    </div>
		
		  </div>
		</div>
<?php
		return ob_get_clean();
	}
	
	public static function getAttribHtml($attributes)
	{
		$attributes = stripslashes($attributes);
		$attributes = str_replace('"', '', $attributes);
		$attributes = trim($attributes, '{}');
		$attributes = trim($attributes, '[]');
		$attributes = explode(",", $attributes);
		$db = JFactory::getDBO();
		$attribHtml = '';
		
		if(count($attributes) > 0)
		{
			$attribHtml = "<div class='pg-cart-attributes'>";
			for($f = 0;$f < count($attributes); $f++)
			{
				$attrVal = explode(':', $attributes[$f]);

				if(count($attrVal) > 0 && $attrVal[$f] != '')
				{
					$attribVal = $attrVal[0];
					$attribOpt = $attrVal[1];
					$sql = "SELECT a.id, a.name AS attrib_name, a.type, op.id, op.size, op.size_type, op.sku, op.price_sum, op.name AS opt_name FROM #__pago_attr AS a LEFT JOIN #__pago_attr_opts AS op on a.id = op.attr_id WHERE a.id='" . $attribVal . "' AND op.id='" . $attribOpt."'";
					$db->setQuery($sql); 
					$attribRes = $db->loadObjectList();

					if(count($attribRes) > 0)
					{
						$attribHtml .= "<div class='pg-cart-attribute'>";							
						$attribHtml .=  "<span class='pg-cart-attribute-name'>" . $attribRes[0] -> attrib_name . ": ";

						if(strlen( $attribRes[0]->sku) > 0)
						{
							$attribHtml .=  " <span class='pg-cart-attribute-sku'>(Sku: " . $attribRes[0]->sku . ")</span>";	
						}
						$attribHtml .=  "</span>";

						switch ($attribRes[0]->type) 
						{
							case '0':
								$attribHtml .=   $attribRes[0]->opt_name . "<span class='pg_color_option_form' style='background-color:" .  $attribRes[0]->opt_name . "'></span>";
								break;
							case '1':
								$attribHtml .=  $attribRes[0]->opt_name . " " . $attribRes[0]->size." (";
									switch ($attribRes[0]->size_type) 
									{
										case '0':
											$attribHtml .=  JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US');
										break;
										case '1':
											$attribHtml .=  JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL');
										break;
										case '2':
											$attribHtml .=  JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK');
										break;
										case '3':
											$attribHtml .=  JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE');
										break;
										case '4':
											$attribHtml .=  JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY');
										break;
										case '5':
											$attribHtml .=  JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA');
										break;
										case '6':
											$attribHtml .=  JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN');
										break;
									}
								$attribHtml .=  ")";	
								break;
							case '2':
								$attribHtml .=  $attribRes[0]->opt_name;
								break;
							case '3':
								$attribHtml .=  $attribRes[0]->opt_name;
								break;		
						}
					}

					// $attribHtml .=  "</div>";
					$attribHtml .=  "</div>";
				}

			}
			$attribHtml .= "</div>";
		}
		
		return $attribHtml;	
		
	}

	function onOrderCaptured($order, $captureResult)
	{
		// Initialiase variables.
		$db = JFactory::getDbo();

		if ($captureResult->payment_capture_status == 'Captured' && $captureResult->txn_id != "")
		{
			// Prepare query.
			$query = "UPDATE `#__pago_orders_sub_payments` SET `payment_capture_status` =  " . $db->quote($captureResult->payment_capture_status) . " , `txn_id` = " . $captureResult->txn_id . "  WHERE order_id = " . (int) $order['details']->order_id;
			$db->setQuery($query);
			$db->query();
		}

		$this->setOrderLog($order,$captureResult,'capture_order');
	}

	public function getOrderBillingAddressHtml($order)
	{
		// Billing Info

		$bill_id = $order['addresses']['billing']->id;
		$bill_address_type = $order['addresses']['billing']->address_type;
		$bill_address_type_name = $order['addresses']['billing']->address_type_name;
		$bill_company = $order['addresses']['billing']->company;
		$bill_title = $order['addresses']['billing']->title;
		$bill_last_name = $order['addresses']['billing']->last_name;
		$bill_first_name = $order['addresses']['billing']->first_name;
		$bill_middle_name = $order['addresses']['billing']->middle_name;
		$bill_phone_1 = $order['addresses']['billing']->phone_1;
		$bill_phone_2 = $order['addresses']['billing']->phone_2;
		$bill_fax = $order['addresses']['billing']->fax;
		$bill_address_1 = $order['addresses']['billing']->address_1;
		$bill_address_2 = $order['addresses']['billing']->address_2;
		$bill_city = $order['addresses']['billing']->city;
		$bill_state = $order['addresses']['billing']->state;
		$bill_country = $order['addresses']['billing']->country;
		$bill_zip = $order['addresses']['billing']->zip;
		$bill_user_email = $order['addresses']['billing']->user_email;

		// Prepare Billing Address
		$billAddress = '';

		if ($bill_first_name != "")
		{
			$billAddress .= JText::_("PAGO_BILL_FIRSTNAME") . ' : ' . $bill_first_name . '<br />';
		}

		if ($bill_middle_name != "")
		{
			$billAddress .= JText::_("PAGO_BILL_MIDDLENAME") . ' : ' . $bill_middle_name . '<br />';
		}

		if ($bill_last_name != "")
		{
			$billAddress .= JText::_("PAGO_BILL_LASTNAME") . ' : ' . $bill_last_name . '<br />';
		}

		if ($bill_user_email != "")
		{
			$billAddress .= JText::_("PAGO_BILL_EMAIL") . ' : ' . $bill_user_email . '<br />';
		}

		if ($bill_company != "")
		{
			$billAddress .= JText::_("PAGO_BILL_COMPANY") . ' : ' . $bill_company . '<br />';
		}
		
		if ($bill_address_1 != "")
		{
			$billAddress .= JText::_("PAGO_BILL_ADD1") . ' : ' . $bill_address_1 . '<br />';
		}

		if ($bill_address_2 != "")
		{
			$billAddress .= JText::_("PAGO_BILL_ADD2") . ' : ' . $bill_address_2 . '<br />';
		}

		if ($bill_city != "")
		{
			$billAddress .= JText::_("PAGO_BILL_CITY") . ' : ' . $bill_city . '<br />';
		}

		if ($bill_state != "")
		{
			$billAddress .= JText::_("PAGO_BILL_STATE") . ' : ' . $bill_state . '<br />';
		}

		if ($bill_country != "")
		{
			$billAddress .= JText::_("PAGO_BILL_COUNTRY") . ' : ' . $bill_country . '<br />';
		}

		if ($bill_zip != "")
		{
			$billAddress .= JText::_("PAGO_BILL_ZIP") . ' : ' . $bill_zip . '<br />';
		}

		if ($bill_fax != "")
		{
			$billAddress .= JText::_("PAGO_BILL_FAX") . ' : ' . $bill_fax . '<br />';
		}

		if ($bill_phone_1 != "")
		{
			$billAddress .= JText::_("PAGO_BILL_PHONE1") . ' : ' . $bill_phone_1 . '<br />';
		}

		if ($bill_phone_2 != "")
		{
			$billAddress .= JText::_("PAGO_BILL_PHONE2") . ' : ' . $bill_phone_2 . '<br />';
		}

		return $billAddress;
	}

	public function getOrderMailingAddressHtml($order)
	{
		// Mailing Info
		$mail_id = $order['addresses']['shipping']->id;
		$mail_address_type = $order['addresses']['shipping']->address_type;
		$mail_address_type_name = $order['addresses']['shipping']->address_type_name;
		$mail_company = $order['addresses']['shipping']->company;
		$mail_title = $order['addresses']['shipping']->title;
		$mail_last_name = $order['addresses']['shipping']->last_name;
		$mail_first_name = $order['addresses']['shipping']->first_name;
		$mail_middle_name = $order['addresses']['shipping']->middle_name;
		$mail_phone_1 = $order['addresses']['shipping']->phone_1;
		$mail_phone_2 = $order['addresses']['shipping']->phone_2;
		$mail_fax = $order['addresses']['shipping']->fax;
		$mail_address_1 = $order['addresses']['shipping']->address_1;
		$mail_address_2 = $order['addresses']['shipping']->address_2;
		$mail_city = $order['addresses']['shipping']->city;
		$mail_state = $order['addresses']['shipping']->state;
		$mail_country = $order['addresses']['shipping']->country;
		$mail_zip = $order['addresses']['shipping']->zip;
		$mail_user_email = $order['addresses']['shipping']->user_email;

		$mailAddress = '';

		if ($mail_first_name != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_FIRSTNAME") . ' : ' . $mail_first_name . '<br />';
		}

		if ($mail_middle_name != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_MIDDLENAME") . ' : ' . $mail_middle_name . '<br />';
		}

		if ($mail_last_name != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_LASTNAME") . ' : ' . $mail_last_name . '<br />';
		}

		if ($mail_user_email != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_EMAIL") . ' : ' . $mail_user_email . '<br />';
		}

		if ($mail_company != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_COMPANY") . ' : ' . $mail_company . '<br />';
		}

		if ($mail_address_1 != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_ADD1") . ' : ' . $mail_address_1 . '<br />';
		}

		if ($mail_address_2 != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_ADD2") . ' : ' . $mail_address_2 . '<br />';
		}

		if ($mail_city != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_CITY") . ' : ' . $mail_city . '<br />';
		}

		if ($mail_state != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_STATE") . ' : ' . $mail_state . '<br />';
		}

		if ($mail_country != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_COUNTRY") . ' : ' . $mail_country . '<br />';
		}

		if ($mail_zip != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_ZIP") . ' : ' . $mail_zip . '<br />';
		}

		if ($mail_fax != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_FAX") . ' : ' . $mail_fax . '<br />';
		}

		if ($mail_phone_1 != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_PHONE1") . ' : ' . $mail_phone_1 . '<br />';
		}

		if ($mail_phone_2 != "")
		{
			$mailAddress .= JText::_("PAGO_MAIL_PHONE2") . ' : ' . $mail_phone_2 . '<br />';
		}

		return $mailAddress;
	}

	function replaceOrderDetailsInformations($order, $mail_body)
	{

		if (strstr($mail_body, "{item_loop_start}") && strstr($mail_body, "{item_loop_end}"))
		{
			$mail_sdata = explode('{item_loop_start}', $mail_body);
			$mail_edata = explode('{item_loop_end}', $mail_sdata[1]);
			$mail_end = $mail_edata[1];
			$mail_middle = $mail_edata[0];
			$mail_middle = $this->replaceItems($mail_middle, $order);
			$mail_body = $mail_sdata[0] . $mail_middle . $mail_end;
		}

		$order_id = $order['details']->order_id;
		$user_email = $order['details']->user_email;
		$order_total = Pago::get_instance('price')->format($order['details']->order_total);
		$order_subtotal = Pago::get_instance('price')->format($order['details']->order_subtotal);
		$order_refundtotal = Pago::get_instance('price')->format($order['details']->order_refundtotal);
		$order_tax = Pago::get_instance('price')->format($order['details']->order_tax);
		$order_tax_details = $order['details']->order_tax_details;
		$order_shipping = Pago::get_instance('price')->format($order['details']->order_shipping);
		$order_shipping_tax = Pago::get_instance('price')->format($order['details']->order_shipping_tax);
		$coupon_discount = Pago::get_instance('price')->format($order['details']->coupon_discount);
		$coupon_code = $order['details']->coupon_code;
		$order_discount = Pago::get_instance('price')->format($order['details']->order_discount);
		$order_currency = $order['details']->order_currency;
		$order_status = $order['details']->order_status;
		$order_status_txt = $this->get_order_status($order_status);

		$items_det = '';
		foreach ($order['items'] as $key => $value) {
			$items_det .='<tr>';
			$items_det .='<td style="line-height:46px;border-bottom:1px solid #a3a19c;width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;border-left:none;">'.substr($value->name, 0, 20).'</td>';
			$items_det .='<td style="line-height:46px;border-bottom:1px solid #a3a19c;width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">'.$value->qty.'</td>';
			$items_det .='<td style="line-height:46px;border-bottom:1px solid #a3a19c;width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">'.$value->sku.'</td>';
			$items_det .='<td style="line-height:46px;border-bottom:1px solid #a3a19c;width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">'.Pago::get_instance('price')->format($value->price).'</td>';
			$items_det .='</tr>';
		}

		$cdate = $order['details']->cdate;
		$cdate = str_replace ( "/" , "-" ,  $cdate);
		$orderDate = date("m/d/Y", strtotime($cdate));

		// shipping Address Info
		$mailAddress = $this->getOrderMailingAddressHtml($order);

		// Billing Address Info
		$billAddress = $this->getOrderBillingAddressHtml($order);

		// replace Shipping Method Data
		$mail_body = $this->replaceShippingMethod($order,$mail_body);

		// replace Shipping Method Data
		$mail_body = $this->replacePaymentMethod($order,$mail_body);

		// replace Customer Note
		$mail_body = $this->replaceCustomerNote($order,$mail_body);

		$mail_body = str_replace("{orderid}", "#".$order_id, $mail_body);
		$mail_body = str_replace("{billingaddress}", $billAddress, $mail_body);
		$mail_body = str_replace("{mailingaddress}", $mailAddress, $mail_body);
		$mail_body = str_replace("{ordertotal}", $order_total, $mail_body);
		$mail_body = str_replace("{order_subtotal}", $order_subtotal, $mail_body);
		$mail_body = str_replace("{ordertax}", $order_tax, $mail_body);
		$mail_body = str_replace("{ordertax_detail}", $order_tax_details, $mail_body);
		$mail_body = str_replace("{order_shipping}", $order_shipping, $mail_body);
		$mail_body = str_replace("{order_shippingtax}", $order_shipping_tax, $mail_body);
		$mail_body = str_replace("{order_coupon_disc}", $coupon_discount, $mail_body);
		$mail_body = str_replace("{order_coupon_code}", $coupon_code, $mail_body);
		$mail_body = str_replace("{order_discount}", $order_discount, $mail_body);
		$mail_body = str_replace("{ordercurrency}", $order_currency, $mail_body);
		$mail_body = str_replace("{orderstatus}", $order_status_txt, $mail_body);
		$mail_body = str_replace("{order_cadte}", $orderDate, $mail_body);
		$mail_body = str_replace("{order_detail_link}", "", $mail_body);

		$mail_body = str_replace("{items_det}", $items_det, $mail_body);


		$mail_body = $this->parseLabels($mail_body);

		return $mail_body;
	}

	public function replaceCustomerNote($order, $order_template)
	{
		$customer_note = $order['details']->customer_note;
		if($customer_note!="")
		{
			$order_template = str_replace("{order_customernote}", $customer_note, $order_template);
			$mail_body = str_replace("{order_customernote_lbl}", JTEXT::_('PAGO_CUST_NOTE_LBL'), $order_template);
		
		}
		else
		{
			$order_template = str_replace("{order_customernote}","", $order_template);
			$order_template = str_replace("{order_customernote_lbl}", "", $order_template); ?>
			<script>
			    jQuery(function(){
			        jQuery( "#order_receipt_customernote_header" ).hide();
			    });
 			</script>
		<?php
		}

		return $order_template;
	}

	public function replacePaymentMethod($order, $order_template)
	{
		$payment_gateway = $order['details']->payment_gateway;
		$payment_message = $order['details']->payment_message;
		if($payment_gateway != "Free of Cost")
		{
			$dispatcher = KDispatcher::getInstance();
			JPluginHelper::importPlugin('pago_gateway');
			$plugin = JPluginHelper::getPlugin('pago_gateway',$payment_gateway);
			$pluginParams = new JRegistry(@$plugin->params);
		}

		if($payment_gateway!= "")
		{
			$order_template = str_replace("{order_payment_msg}", $payment_message, $order_template);
			
			if ($payment_gateway == "banktransfer")
			{
				$payment_gateway = $payment_gateway . "<br/>" . JTEXT::_("COM_PAGO_BANK_INFORMATION") . "<br/>" . $pluginParams->get('txtextra_info');
			}
			
			$order_template = str_replace("{paymentmethod}", $payment_gateway, $order_template);
			$order_template = str_replace("{payment_information_lbl}", JTEXT::_('COM_PAGO_PAYMENT_INFORMATION_LBL'), $order_template);
			$order_template = str_replace("{payment_lbl}", JTEXT::_('PAGO_PAYMENT_LBL'), $order_template);
			$order_template = str_replace("{order_payment_msg_lbl}", JTEXT::_('PAGO_PAYMENT_METHOD_MSG_LBL'), $order_template);

			if ($payment_gateway == "banktransfer")
			{
				$order_template = str_replace("{banktransfe_info_lbl}", JTEXT::_('PAGO_PAYMENT_METHOD_BANKTRANSFER_LBL'), $order_template);
				$order_template = str_replace("{banktransfer_information}", $pluginParams->get('txtextra_info'), $order_template);
			}
			else
			{
				$order_template = str_replace("{banktransfe_info_lbl}", "", $order_template);
				$order_template = str_replace("{banktransfer_information}", "", $order_template);
			}
		}
		else
		{
			$order_template = str_replace("{order_payment_msg}", "", $order_template);
			$order_template = str_replace("{paymentmethod}", "", $order_template);
			$order_template = str_replace("{payment_information_lbl}", "", $order_template);
			$order_template = str_replace("{payment_lbl}", "", $order_template);
			$order_template = str_replace("{order_payment_msg_lbl}", "", $order_template);
		}

		return $order_template;
	}

	public function replaceShippingMethod($order, $order_template)
	{
		$config = Pago::get_instance('config')->get();
		$shipping_type = $config->get('checkout.shipping_type');
		$ship_method_id = $order['details']->ship_method_id;
		if($ship_method_id != "")
		{
			$order_template = str_replace("{order_shipping_method}", $ship_method_id, $order_template);
			$order_template = str_replace("{mailing_information_lbl}", JTEXT::_('COM_PAGO_MAILING_INFORMATION_LBL'), $order_template);

			if ($shipping_type == 1)
			{
				$order_template = str_replace("{order_shipmethod_lbl}", "", $order_template);
				$order_template = str_replace("{order_item_ship_method_lbl}", JTEXT::_('PAGO_ORDER_ITEM_SHIP_METHOD_LBL'), $order_template);
			}
			else
			{
				$order_template = str_replace("{order_shipmethod_lbl}", JTEXT::_('PAGO_SHIP_METHOD_LBL'), $order_template);
				$order_template = str_replace("{order_item_ship_method_lbl}", "", $order_template);
			}
		}
		else if($shipping_type != 1)
		{
			$order_template = str_replace("{order_shipmethod_lbl}", "", $order_template);
			$order_template = str_replace("{mailing_information_lbl}", JTEXT::_('COM_PAGO_MAILING_INFORMATION_LBL'), $order_template);
			$order_template = str_replace("{order_shipping_method}", JTEXT::_('COM_PAGO_FREE_SHIPPING'), $order_template);

		}
		else
		{
			if ($shipping_type == 1)
			{
				$order_template = str_replace("{order_shipmethod_lbl}", "", $order_template);
				$order_template = str_replace("{order_item_ship_method_lbl}", JTEXT::_('PAGO_ORDER_ITEM_SHIP_METHOD_LBL'), $order_template);
			}
			else
			{
				$order_template = str_replace("{order_shipmethod_lbl}", JTEXT::_('PAGO_SHIP_METHOD_LBL'), $order_template);
				$order_template = str_replace("{order_item_ship_method_lbl}", "", $order_template);
			}
			$order_template = str_replace("{order_shipmethod_lbl}", "", $order_template);
			$order_template = str_replace("{mailing_information_lbl}", "", $order_template);
			$order_template = str_replace("{order_shipping_method}", "", $order_template);
		?>
			<script>
			    jQuery(function(){
			        jQuery( "#order_receipt_mailing_header" ).hide();
			    });
 			</script>
		<?php
		}

		return $order_template;
	}

	public function parseLabels($mail_body)
	{
		$mail_body = str_replace("{order_receipt_lbl}", JTEXT::_('COM_PAGO_ORDER_RECEIPT_LBL'), $mail_body);
		$mail_body = str_replace("{order_information_lbl}", JTEXT::_('COM_PAGO_ORDER_INFORMATION_LBL'), $mail_body);
		$mail_body = str_replace("{customer_information_lbl}", JTEXT::_('COM_PAGO_CUSTOMER_INFORMATION_LBL'), $mail_body);
		$mail_body = str_replace("{order_items_lbl}", JTEXT::_('COM_PAGO_ORDER_ITEMS_LBL'), $mail_body);
		$mail_body = str_replace("{ordertotal_lbl}", JTEXT::_('PAGO_ORD_TOTAL_LBL'), $mail_body);
		$mail_body = str_replace("{order_subtotal_lbl}", JTEXT::_('PAGO_ORD_SUBTOTAL_LBL'), $mail_body);
		$mail_body = str_replace("{order_tax_lbl}", JTEXT::_('PAGO_ORD_TAX_LBL'), $mail_body);
		$mail_body = str_replace("{order_tax_details_lbl}", JTEXT::_('PAGO_ORD_TAX_DETAILS_LBL'), $mail_body);
		$mail_body = str_replace("{order_ship_lbl}", JTEXT::_('PAGO_ORD_SHIP_LBL'), $mail_body);
		$mail_body = str_replace("{order_ship_tax_lbl}", JTEXT::_('PAGO_SHIP_TAX_DETAILS_LBL'), $mail_body);
		$mail_body = str_replace("{order_coupon_disc_lbl}", JTEXT::_('PAGO_COUPON_DISC_LBL'), $mail_body);
		$mail_body = str_replace("{order_disc_lbl}", JTEXT::_('PAGO_COP_DISC_LBL'), $mail_body);
		$mail_body = str_replace("{order_couponcode_lbl}", JTEXT::_('PAGO_COP_CODE_LBL'), $mail_body);
		$mail_body = str_replace("{order_currency_lbl}", JTEXT::_('PAGO_CURRENCY_LBL'), $mail_body);
		$mail_body = str_replace("{order_status_lbl}", JTEXT::_('PAGO_STATUS_LBL'), $mail_body);
		$mail_body = str_replace("{order_cdate_lbl}", JTEXT::_('PAGO_CDATE_LBL'), $mail_body);

		$mail_body = str_replace("{order_billing_add_lbl}", JTEXT::_('PAGO_BILLING_ADD_LBL'), $mail_body);
		$mail_body = str_replace("{order_mailing_add_lbl}", JTEXT::_('PAGO_MAILING_ADD_LBL'), $mail_body);
		$mail_body = str_replace("{orderid_lbl}", JTEXT::_('PAGO_MAILING_ORDIID_LBL'), $mail_body);
		$mail_body = str_replace("{item_name_lbl}", JTEXT::_('PAGO_ITEM_NAME_LBL'), $mail_body);
		$mail_body = str_replace("{item_quantity_lbl}", JTEXT::_('PAGO_ITEM_QTY_LBL'), $mail_body);
		$mail_body = str_replace("{item_sku_lbl}", JTEXT::_('PAGO_ITEM_SKU_LBL'), $mail_body);
		$mail_body = str_replace("{item_price_lbl}", JTEXT::_('PAGO_ITEM_PRICE_LBL'), $mail_body);
		$mail_body = str_replace("{item_item_ship_method_lbl}", JTEXT::_('PAGO_ITEM_SHIP_METHOD_LBL'), $mail_body);
		$mail_body = str_replace("{order_detail_link_lbl}", JTEXT::_('PAGO_ORDER_DETAIL_LINK_LBL'), $mail_body);

		return $mail_body;
	}

	function replaceadminInformations($order, $mail_body)
	{
		$order_link = "<a href = '" . JURI::root() . "administrator/index.php?option=com_pago&controller=ordersi&task=edit&view=ordersi&cid[]=" . $order['details']->order_id . "'>" . JTEXT::_('PAGO_ORDER_DETAIL_LINK') . "</a>";
		$mail_body = str_replace("{order_detail_link}", $order_link, $mail_body);

		return $mail_body;
	}


	function sendInvoiceEmailNotifications($order)
	{
		//make things a bit tidier
		$recipient = $order['addresses']['billing'];
		
		//get paGO backend store configuration
		$store_cfg 	= Pago::get_instance('config')->get();
		
		//send transactional emails to user and store email
		$result = Pago::get_instance('transaction_email')->set((object)[
			'recipients' => [(object)[
				'template' => 'email_invoice',
				'type' => 'site',
				'name' => $recipient->first_name . ' ' . $recipient->last_name,
				'email' => $recipient->user_email
			],(object)[
				'template' => 'email_invoice',
				'type' => 'admin',
				'name' => $store_cfg->get('general.pago_store_name'),
				'email' => $store_cfg->get('general.store_email')
			]],
			'data' => $order
		])->send();
		
		
		/*$db = JFactory::getDBO();
		// Fecth template for user
		$sql = "SELECT pgemail_name,pgemail_body FROM `#__pago_mail_templates` WHERE pgemail_type = 'email_invoice' AND pgemail_enable =1 AND template_for='site'";
		$db->setQuery($sql);

		$res             = $db->loadObjectList();
		$subject         = $res[0]->pgemail_name;
		$mail_body_desc_site  = $res[0]->pgemail_body;
		$mail_body_site       = $this->replaceOrderDetailsInformations($order, $mail_body_desc_site);

		// Fecth template for admin
		$sql = "SELECT pgemail_name,pgemail_body FROM `#__pago_mail_templates` WHERE pgemail_type = 'email_invoice' AND pgemail_enable =1 AND template_for='admin'";
		$db->setQuery($sql);

		$res             = $db->loadObjectList();
		$subject         = $res[0]->pgemail_name;
		$mail_body_desc_admin  = $res[0]->pgemail_body;
		$mail_body_desc_admin = $this->replaceadminInformations($order, $mail_body_desc_admin);
		$mail_body_admin      = $this->replaceOrderDetailsInformations($order, $mail_body_desc_admin);

		$bill_user_email = $order['addresses']['billing']->user_email;

		// Prepare shipping Address
		$store_cfg 	= Pago::get_instance('config')->get();
		$from = array( $store_cfg->get('general.store_email'), $store_cfg->get('general.pago_store_name') );

		// Send to use
		if($mail_body_site != '')
		{
			$this->send_email($subject, $mail_body_site, $bill_user_email, $from, true);
		}

		// Send to admin
		if($mail_body_admin != '')
		{
			$this->send_email($subject, $mail_body_admin, $from[0], $from, true);
		}

		return; */
	}

	function send_email( $subject, $body, $to, $from, $html=false )
	{
		// Invoke JMail Class
		$mailer = JFactory::getMailer();

		// Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

		// Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($to);
		$mailer->setSubject($subject);
		$mailer->setBody($body);

		// If you would like to send as HTML, include this line; otherwise, leave it out

		if ($html)
		{
			$mailer->isHTML();
		}

		// Send once you have set all of your options
		$mailer->send();
	}

	

	public function storeOrderAdress($address_type, $address)
	{
		require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'tables' . DS . 'orders_addresses.php');
		$db   = JFactory::getDBO();

		if ( $address_type == 's' )
		{
			$index = 0;
			$name = 'shipping';
		}
		elseif ( $address_type == 'b' )
		{
			$index = 1;
			$name = 'Billing';
		}

		$user_data = JTable::getInstance( 'Orders_addresses', 'Table' );
		if(isset($address['id']) && $address['id'])
		{
			$user_data->load($address['id']);
			$user_data->id = $address['id'];
		}
		else
		{
			$user_data->load();
		}
		$user_data->order_id = (isset($address['order_id'])) ? $address['order_id'] : '0';
		$user_data->user_id = (isset($address['user_id'])) ? $address['user_id'] : '0';
		$user_data->company = (isset($address['company'])) ? $address['company'] : '';
		$user_data->title = (isset($address['title'])) ? $address['title'] : '';
		$user_data->last_name = (isset($address['last_name'])) ? $address['last_name'] : '';
		$user_data->first_name = (isset($address['first_name'])) ? $address['first_name'] : '';
		$user_data->address_type = $address_type;
		$user_data->address_type_name = $name;
		$user_data->middle_name =(isset($address['middle_name'])) ? $address['middle_name'] : '';
		$user_data->phone_1 = (isset($address['phone_1'])) ? $address['phone_1'] : '';
		$user_data->phone_2 = (isset($address['phone_2'])) ? $address['phone_2'] : '';
		$user_data->address_1 = (isset($address['address_1'])) ? $address['address_1'] : '';
		$user_data->address_2 = (isset($address['address_2'])) ? $address['address_2'] : '';
		$user_data->city = (isset($address['city'])) ? $address['city'] : '';
		$user_data->fax = (isset($address['fax'])) ? $address['fax'] : '';
		$user_data->user_email = (isset($address['user_email'])) ? $address['user_email'] : '';
		$user_data->country = (isset($address['country'])) ? $address['country'] : '';
		$user_data->state = (isset($address['state'])) ? $address['state'] : '';
		$user_data->zip = (isset($address['zip'])) ? $address['zip'] : '';
		$user_data->cdate = (isset($address['cdate'])) ? $address['cdate'] : '';
		$user_data->mdate = (isset($address['mdate'])) ? $address['mdate'] : '';

		if (!$user_data->store())
		{
			echo $row->getError();
			return false;
		}

		$insertedId = $user_data->id;

		return $insertedId;
	}
	
	function get_all_order_currencies()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT order_currency as text,order_currency as value FROM `#__pago_orders` where order_currency!='' group by order_currency";
		$db->setQuery($sql);
		$res = $db->loadAssocList();
		
		return $res;
	}
}
