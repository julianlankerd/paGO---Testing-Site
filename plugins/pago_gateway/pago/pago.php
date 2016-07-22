<?php

/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
require_once ( JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_pago' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'pagoConfig.php');
defined('_JEXEC') or die('Restricted access');

//fix if server isn't compiled with bcmath
if (!function_exists('bcmul')) {
  function bcmul($_ro, $_lo, $_scale=0) {
    return round($_ro*$_lo, $_scale);
  }
}
  
if (!function_exists('bcdiv')) {
  function bcdiv($_ro, $_lo, $_scale=0) {
    return round($_ro/$_lo, $_scale);
  }
}

class plgPago_gatewayPago extends JPlugin
{

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin('pago_gateway', 'pago');
		$this->_params = new JRegistry($this->_plugin->params);
		
		// Hooks
		KDispatcher::add_filter('payment_set_options', array($this, 'payment_set_options'));
		KDispatcher::add_filter('onRefundPayment', array($this, 'onRefundPayment'));
		KDispatcher::add_filter('onGatewayPayment', array($this, 'onGatewayPayment'));
		KDispatcher::add_filter('onGatewayCompletePayment', array($this, 'onGatewayCompletePayment'));
		KDispatcher::add_filter('onPayment', array($this, 'onPayment'));
		KDispatcher::add_filter('onAfterAuthorizePayment', array($this, 'onAfterAuthorizePayment'));
		KDispatcher::add_filter('onCapturePayment', array($this, 'onCapturePayment'));
		
		KDispatcher::add_filter('pago.hook.invoice.payment_succeeded', array($this, 'onHookInvoicePaymentSucceeded'));
		KDispatcher::add_filter('pago.hook.charge.succeeded', array($this, 'onHookChargeSucceeded'));
		KDispatcher::add_filter('pago.hook.charge.refunded', array($this, 'onHookChargeRefunded'));
		KDispatcher::add_filter('pago.hook.invoice.payment_failed', array($this, 'onHookInvoicePaymentFailed'));

	}
	
	public function onHookInvoicePaymentFailed($event){
		
		//this will stop us sending out an invoice for the first payment
		if(!$event->data->object->amount_due) return;
		
		$object = $event->data->object->lines->data[0];
		$invoice = $event->data->object;
		
		$order_id = $object->metadata->order_id;
		//$order_id = 221;
		
		$date = date('Y/m/d H:i:s', time());
		$order_status_txt = 'Failed';
		$name = $object->plan->name;
		
		$data = json_encode([
			'event_id' => $event->id,
			'type' => $event->type,
			'id' => $object->id
		]);
		
		$db = JFactory::getDbo();
		
		$db->setQuery("
			INSERT INTO 
				#__pago_orders_log (orderid, date, order_status, description, action)
			VALUES ({$order_id}, '{$date}', '{$order_status_txt}', '{$name}', '$data')
		");
		
		$db->query();
		
		$order 	= Pago::get_instance('orders')->get($order_id);
		
		$order['details']->order_total = $invoice->total / 100;
		$order['details']->order_subtotal = $invoice->subtotal / 100;
		$order['details']->order_tax = $invoice->tax / 100;
		$order['details']->order_shipping = 0;
		
		$inv_items = [];
		
		foreach($invoice->lines->data as $inv_item){
			$inv_items[$inv_item->metadata->item_id] = $inv_item;
		}
        
        foreach($order['items'] as $k=>$item){
        	
        	if(!isset($inv_items[$item->id])){
        		unset($order['items'][$k]);
        		continue;
        	}
        	
        	$inv_item = $inv_items[$item->id];
        	
        	//$inv_item->amount = 3800;
        	
        	$item->price = $inv_item->amount / 100;
        	$item->total = Pago::get_instance( 'price' )->format($inv_item->amount / 100);
        	
        	$order['items'][$k] = $item;
        } 
        
        $recipient = $order['addresses']['billing'];
		$recipient->name = $recipient->first_name . ' ' . $recipient->last_name;
		
		//get paGO backend store configuration
		$store_cfg 	= Pago::get_instance('config')->get();
		
		//send transactional emails to user and store email
		$result = Pago::get_instance('transaction_email')->set((object)[
			'recipients' => [(object)[
				'template' => 'email_invoice_failed',
				'type' => 'site',
				'name' => $recipient->name,
				'email' => $recipient->user_email
			],(object)[
				'template' => 'email_invoice_failed',
				'type' => 'admin',
				'name' => $store_cfg->get('general.pago_store_name'),
				'email' => $store_cfg->get('general.store_email')
			]],
			'data' => $order
		])->send();
		
		return $event;
	}
	
	public function onHookInvoicePaymentSucceeded($event){
		
		//this will stop us sending out an invoice for the first payment
		if(!$event->data->object->amount_due) return;
		
		$object = $event->data->object->lines->data[0];
		$invoice = $event->data->object;
		
		$order_id = $object->metadata->order_id;
		$date = date('Y/m/d H:i:s', time());
		$order_status_txt = 'Completed';
		$name = $object->plan->name;
		
		$data = json_encode([
			'event_id' => $event->id,
			'type' => $event->type,
			'id' => $object->id
		]);
		
		$db = JFactory::getDbo();
		
		$db->setQuery("
			INSERT INTO 
				#__pago_orders_log (orderid, date, order_status, description, action)
			VALUES ({$order_id}, '{$date}', '{$order_status_txt}', '{$name}', '$data')
		");
		
		$db->query();
		
		$order 	= Pago::get_instance('orders')->get($order_id);
		
		//$invoice->total = 4300;
		//$invoice->subtotal = 3800;
		//$invoice->tax = 500;
		
		$order['details']->order_total = $invoice->total / 100;
		$order['details']->order_subtotal = $invoice->subtotal / 100;
		$order['details']->order_tax = $invoice->tax / 100;
		$order['details']->order_shipping = 0;
		
		$inv_items = [];
		
		foreach($invoice->lines->data as $inv_item){
			$inv_items[$inv_item->metadata->item_id] = $inv_item;
		}
        
        foreach($order['items'] as $k=>$item){
        	
        	if(!isset($inv_items[$item->id])){
        		unset($order['items'][$k]);
        		continue;
        	}
        	
        	$inv_item = $inv_items[$item->id];
        	
        	//$inv_item->amount = 3800;
        	
        	$item->price = $inv_item->amount / 100;
        	$item->total = Pago::get_instance( 'price' )->format($inv_item->amount / 100);
        	
        	$order['items'][$k] = $item;
        } 
        
        $recipient = $order['addresses']['billing'];
		$recipient->name = $recipient->first_name . ' ' . $recipient->last_name;
		
		//get paGO backend store configuration
		$store_cfg 	= Pago::get_instance('config')->get();
		
		//send transactional emails to user and store email
		$result = Pago::get_instance('transaction_email')->set((object)[
			'recipients' => [(object)[
				'template' => 'email_invoice',
				'type' => 'site',
				'name' => $recipient->name,
				'email' => $recipient->user_email
			],(object)[
				'template' => 'email_invoice',
				'type' => 'admin',
				'name' => $store_cfg->get('general.pago_store_name'),
				'email' => $store_cfg->get('general.store_email')
			]],
			'data' => $order
		])->send();
		
		return $event;
	}
	
	public function onHookChargeSucceeded($event){
		
		$charge = $event->data->object;
		$details = json_decode($charge->description);
		$order_id = $details->order_id;
		$date = date('Y/m/d H:i:s', $charge->created);
		$order_status_txt = 'Completed';
		$price = Pago::get_instance( 'price' )->format($charge->amount / 100);
		
		$data = json_encode([
			'event_id' => $event->id,
			'type' => $event->type,
			'id' => $charge->id,
			'amount' => number_format($charge->amount / 100, 2)
		]);
		
		$db = JFactory::getDbo();
		
		$db->setQuery("
			INSERT INTO 
				#__pago_orders_log (orderid, date, order_status, description, action)
			VALUES ({$order_id}, '{$date}', '{$order_status_txt}', 'charge {$price}', '$data')
		");
		
		$db->query();
		
		$order 	= Pago::get_instance('orders')->get($order_id);
		
		return $event;
	}
	
	public function onHookChargeRefunded($event){
		
		$object = $event->data->object;
		$details = json_decode($object->description);
		$order_id = $details->order_id;
		$date = date('Y/m/d H:i:s', $object->created);
		$order_status_txt = 'Refunded';
		
		$amount_refunded = $object->amount_refunded / 100;
		
		$price = Pago::get_instance( 'price' )->format($amount_refunded);
		
		$balance = ($object->amount - $object->amount_refunded) / 100;
		
		$data = json_encode([
			'event_id' => $event->id,
			'type' => $event->type,
			'id' => $object->id,
			'balance' => number_format($balance, 2)
		]);
		
		$db = JFactory::getDbo();
		
		$db->setQuery("
			INSERT INTO 
				#__pago_orders_log (orderid, date, order_status, description, action)
			VALUES ({$order_id}, '{$date}', '{$order_status_txt}', 'Total Charge Refunded {$price}', '$data')
		");
		
		$db->query();
		
		$db->setQuery("
			UPDATE  #__pago_orders 
				SET  `order_refundtotal` = {$amount_refunded} 
					WHERE  `order_id` = {$order_id};
		");
		
		$db->query();
		
		//get paGO backend store configuration
		$store_cfg 	= Pago::get_instance('config')->get();
		$order 	= Pago::get_instance('orders')->get($order_id);
		$recipient = $order['addresses']['billing'];
		
		$recipient->name = $recipient->first_name . ' ' . $recipient->last_name;
		
		//send transactional emails to user and store email
		$result = Pago::get_instance('transaction_email')->set((object)[
			'recipients' => [(object)[
				'template' => 'email_update_order_status',
				'type' => 'site',
				'name' => $recipient->name,
				'subject' => "{$price} Refunded for Order #{$order_id}",
				'email' => $recipient->user_email
			],(object)[
				'template' => 'email_update_order_status',
				'type' => 'site',
				'name' => $store_cfg->get('general.pago_store_name'),
				'subject' => "{$price} Refunded for Order #{$order_id}",
				'email' => $store_cfg->get('general.store_email')
			]],
			'data' => $order
		])->send();
		
		return $event;
	}
	
	public function payment_set_options(&$payment_options, $cart, $user_data)
	{
		if(Pago::get_instance('params')->get('payoptions.active', 0))
			$payment_options['pago'] = array(
				'logo' => 'pago.png',
				'name' => $this->_params->get("payment_gateway_name")
			);
		
		$paygates = Pago::get_instance('params')->get('paygates');
		
		foreach($paygates as $pg){
			if(!isset($pg->active) || !$pg->active) continue;
			
			$payment_options['pago_' . $pg->data->id] = array(
				'logo' => $pg->data->img,
				'name' => $pg->data->name
			);
		}
		//print_r($payment_options);
		return $payment_options;
	}
			
	public function onRefundPayment(&$refundResult, $payment_gateway, $order)
	{
		if (strtoupper($payment_gateway) != strtoupper("pago"))
			return;
		
		$refundResult['pago'] = array(
			//'success' => true,
			'success' => $response->isSuccessful(),
			'message' => $response->getMessage()
		);
	}
	
	public function onGatewayPayment(&$paymentResult, $payment_option, $order)
	{
		// For price and currency code
		$this->setCurrencyPrice($order['details']->order_total);
		$price = $this->price;
		$currentCurrency = $this->currentCurrency;
		
		// Get Card Expiry Date and year
		$expiryDate = explode("-", $order['PaymentInformation']['expirationDate']);
		
		$gateway = str_replace('pago_', '', $payment_option);
		$params = Pago::get_instance('params')->get('paygates.'.$gateway);
		
		unset($params->data);
		
		$payload = (object)[
			"gateway" => $gateway,
		    "customer_id" => Pago::get_instance('params')->get('payoptions.customer_id'),
		    "params" => $params,
		    "amount" => $price,
		    "currency" => $currentCurrency->code,
		    "description" => 'Order ID: '.$order['details']->order_id,
		    "clientIp" => '',
		    "transactionId" => $order['details']->order_id,
		    "payerID" => $order['details']->order_id,
		    "returnUrl" => JURI::base() . 'index.php?option=com_pago&view=ordersi&task=ipn&gateway='.$payment_option,
		    "cancelUrl" => JURI::base() . 'index.php?option=com_pago&view=ordersi&task=cancel&gateway='.$payment_option,
		    "card" => [
		    	'company' => @$order['addresses']['billing']->company,
				'firstName' => @$order['addresses']['billing']->first_name,
		        'lastName' => @$order['addresses']['billing']->last_name,
		        'email' => @$order['addresses']['billing']->user_email,
		        
		        'number' => @$order['PaymentInformation']['cardNumber'],
		        'expiryMonth' => @$expiryDate[1],
		        'expiryYear' => @$expiryDate[0],
		        'cvv' => @$order['PaymentInformation']['cv2'],
		        
		        'billingAddress1' => @$order['addresses']['billing']->address_1,
		        'billingAddress2' => @$order['addresses']['billing']->address_2,
		        'billingCity' => @$order['addresses']['billing']->city,
		        'billingPostcode' => $order['addresses']['billing']->zip,
		        'billingState' => @$order['addresses']['billing']->state,
		        'billingCountry' => @$order['addresses']['billing']->country,
		        'billingPhone' => @$order['addresses']['billing']->phone_1,
		        
		        'shippingAddress1' => @$order['addresses']['shipping']->address_1,
		        'shippingAddress2' => @$order['addresses']['shipping']->address_2,
		        'shippingCity' => @$order['addresses']['shipping']->city,
		        'shippingPostcode' => $order['addresses']['shipping']->zip,
		        'shippingState' => @$order['addresses']['shipping']->state,
		        'shippingCountry' => @$order['addresses']['shipping']->country,
		        'shippingPhone' => @$order['addresses']['shipping']->phone_1,
		    ]
		];
	
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$api = new PagoControllerApi;
		$res = $api->call('POST', 'pay', $payload, false);

		$result = new stdClass();
		$result->order_id = $order['details']->order_id;
		$result->paymentGateway = JText::_('PAGO_PG_'.strtoupper($payment_option));
		$result->cardnumber = 0;
		$result->txn_id = 0;
		$result->isFraud = 0;
		$result->fraudMessage = 0;
		
		if($res->status == 200){
			
			if($res->redirect){
				if(JFactory::getApplication()->input->get('defer_redirect', 0)){
					$result->order_status = "P";
					$result->order_payment_status = 'Pending';
					$result->txn_id = $res->detail;
					$result->redirectUrl = $res->redirect;
					$paymentResult[$payment_option] = $result;
					return $paymentResult;
				} else {
					//$session 	= JFactory::getSession();
					//$session->set('txn_id', $res->detail, 'pago_cart');
					// Redirect to offsite payment gateway
					JFactory::getApplication()->redirect($res->redirect);
					return;
				}
			//successful credit card payment / capture
			} else {
				$result->txn_id = $res->detail;
				$result->cardnumber = $res->last4;
				$result->order_status = "C";
				$result->order_payment_status = 'Paid';
				$result->payment_capture_status = 'Captured';
				$result->message = JText::_('COM_PAGO_ORDER_PLACED_SUCCESSFULLY');
			}
			
		} else {
			$result->order_status = "P";
			$result->order_payment_status = 'Unpaid';
			$result->payment_capture_status = '';
			$result->message = str_replace("'", '', $res->detail);
		}
		
		$paymentResult[$payment_option] = $result;

		return $paymentResult;
	}
	
	public function onGatewayCompletePayment(&$captureResult, $payment_option, $input)
	{
    	$session 	= JFactory::getSession();
		
    	$payment_option = $session->get('payment_option', 0, 'pago_cart');
		$order_id = $session->get('order_id', 0, 'pago_cart');
    	
    	$order 	= Pago::get_instance('orders')->get($order_id);
    	
		$token = $input->get('token', '', 'string');
		$payerID = $input->get('PayerID', '', 'string');
		
		/*$x_MD5_Hash = $input->get('x_MD5_Hash', '', 'string');
		$x_response_code = $input->get('x_response_code', '', 'int');
		$x_trans_id = $input->get('x_trans_id', '', 'string');
		$total = $input->get('x_amount', '', 'float');
		$x_invoice_num = $input->get('x_invoice_num', '', 'int');*/
		
    	// For price and currency code
		$this->setCurrencyPrice($order['details']->order_total);
		$price = $this->price;
		$currentCurrency = $this->currentCurrency;
    	
    	$gateway = str_replace('pago_', '', $payment_option);
		$params = Pago::get_instance('params')->get('paygates.'.$gateway);
		
		unset($params->data);
		
    	$payload = (object)[
			"gateway" => $gateway,
		    "customer_id" => Pago::get_instance('params')->get('payoptions.customer_id'),
		    "params" => $params,
		    "amount" => $price,
		    "currency" => $currentCurrency->code,
		    "transactionReference" => $token,
		    "token" => $token,
		    "payerID" => $payerID
		];
	
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$api = new PagoControllerApi;
			
		$res = $api->call('POST', 'pay', $payload, false);
		
		$result = new stdClass();
		$result->order_id = $order['details']->order_id;
		$result->paymentGateway = JText::_('PAGO_PG_'.strtoupper($payment_option));
		$result->txn_id = $res->detail;
		
		if($res->status == 200){
			$result->order_status = "C";
			$result->order_payment_status = 'Paid';
			$result->payment_capture_status = 'Captured';
			$result->message = JText::_('COM_PAGO_ORDER_PLACED_SUCCESSFULLY');
			
		} else {
			$result->order_status = "P";
			$result->order_payment_status = 'Unpaid';
			$result->payment_capture_status = '';
			$result->message = str_replace("'", '', $res->detail);
		}
		
		$captureResult[$payment_option] = $result;

		return $captureResult;
	}
	
	public function onPayment(&$paymentResult, $payment_option, $order)
	{
		if(strtoupper($payment_option) != strtoupper("pago")) return;
		
		// For price and currency code
		$this->setCurrencyPrice($order['details']->order_total);
		$price = $this->price;
		$currentCurrency = $this->currentCurrency;

		// Get Card Expiry Date and year
		$expiryDate = explode("-", $order['PaymentInformation']['expirationDate']);
	   			
	   	$name = @$order['addresses']['billing']->first_name . ' ' 
	   			. @$order['addresses']['billing']->last_name;
	   	
	   	Pago::load_helpers('pagoparameter');
		
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		$items = [];
		
		$subscriptions = [];
		
		foreach($order['items'] as $item){
			
			if($item->price_type == 'subscription'){
				
				//Just in case subscr_init_price(interval_count) comes thru empty
				//we must populate as it is used to generate the start date of
				//the subscription
				if(!$item->subscr_init_price) $item->subscr_init_price = 1;
				
				$start = strtotime('+'.$item->subscr_init_price.' '.$item->sub_recur);
				
				if(@$item->subscr_startdate){
						$start = strtotime($item->subscr_startdate);
						
						//start subscription now if subscription start time
						//is earlier than the current date
						if($start < time()) $start = 'now';
						//echo date('r', $start);die;
				}
				elseif(@$item->subscr_start_num){
					$start = strtotime('+'.$item->subscr_start_num.' '.$item->subscr_start_type);
				}
				
				$subscriptions[] = (object)[
					"id" => $item->id,
					'order_id' => $order['details']->order_id,
					"name" => $item->name,
					'amount' => bcmul($item->subscr_price, 100), 
					"currency" => $currentCurrency->code,
					'interval' => $item->sub_recur, 
					'interval_count' => $item->subscr_init_price,
					'quantity' => $item->qty,
					'start' => $start,
					'installments' => $item->subscr_installments,
					'enddate' => $item->subscr_enddate
				];
			}	
			$items[] = $item->id;
		}
		
		$details = json_encode([
			'origin' => $_SERVER['SERVER_NAME'],
			'order_id' => $order['details']->order_id,
			'user_id' => $order['details']->user_id,
			'name' => $name,
			'email' => @$order['addresses']['billing']->user_email,
			'items' => $items
		]);
		
		$payload = (object)[
			"livemode" => $livemode,
		    "account" => $account,
		    "amount" => bcmul($price, 100), //cast to cents
		    "amount_currency" => $currentCurrency->code,
		    "description" => $details,
		    "subscriptions" => $subscriptions,
		    "number" => $order['PaymentInformation']['cardNumber'],
		    "exp_month" => $expiryDate[1],
		    "exp_year" => $expiryDate[0],
		    "cvc" => $order['PaymentInformation']['cv2'],
		    "name" => $name,
		    "address_line1" => @$order['addresses']['billing']->address_1,
		    "address_line2" => null,
		    "address_city" => @$order['addresses']['billing']->city,
		    "address_state" => @$order['addresses']['billing']->state,
		    "address_zip" => $order['addresses']['billing']->zip
		];
		
		require_once( JPATH_SITE.'/components/com_pago/controllers/api.php' );
		
		$api = new PagoControllerApi;

		$result = new stdClass();
		$result->order_id = $order['details']->order_id;
		$result->paymentGateway = JText::_('PAGO_PG_'.strtoupper($payment_option));
		$result->cardnumber = 0;
		$result->txn_id = 0;
		$result->isFraud = 0;
		$result->fraudMessage = 0;
		
		//Check for duplicate payment or unfinished payment process for userid
		if(!$this->duplicationCheck($order['details']->user_id)){
			$result->order_status = "X";
			$result->order_payment_status = 'Duplicate';
			$result->payment_capture_status = '';
			$result->message = 'Duplicate Attempt';
			
			$paymentResult[$payment_option] = $result;

			return $paymentResult;
		}
		
		$res = $api->call('POST', 'checkout', $payload, false);
		
		if(@$res->status == 500){
			
		} else {
			$subs = @$res->subscriptions;
			$res = @$res->charge;
		}	
		
		if(!$res){
			$result->txn_id = 0;
			$result->cardnumber = $res->source->last4;
			$result->order_status = "C";
			$result->order_payment_status = 'Paid';
			$result->payment_capture_status = 'Trial';
			$result->message = JText::_('COM_PAGO_ORDER_PLACED_SUCCESSFULLY');
			$result->subs = $subs;
		} 
		elseif($res->paid){
			$result->txn_id = $res->id;
			$result->cardnumber = $res->source->last4;
			$result->order_status = "C";
			$result->order_payment_status = 'Paid';
			$result->payment_capture_status = 'Captured';
			$result->message = JText::_('COM_PAGO_ORDER_PLACED_SUCCESSFULLY');
			$result->subs = $subs;
			
			
			
		} else {
			$result->order_status = "P";
			$result->order_payment_status = 'Unpaid';
			$result->payment_capture_status = '';
			$result->message = str_replace("'", '', $res->detail);
		}
		
		$paymentResult[$payment_option] = $result;

		return $paymentResult;
	}
	
	private function setCurrencyPrice($order_total){
		
		$price = $order_total;
		
		$currenciesModel = JModelLegacy::getInstance('currencies', 'PagoModel');
		$currencyCourse = $currenciesModel->getCurrenciesCource();
		$defaultCurrency = $currenciesModel->getDefault();
		$currentCurrencyId = Pago::get_instance( 'cookie' )->get( 'current_currency');
		$currentCurrency = false;
		
		if($currentCurrencyId){
			$currentCurrency = $currenciesModel->getCurrencyById($currentCurrencyId);
		}
		else{
			$currentCurrency = Pago::get_instance( 'price' )->getDefaultCurrency();
		}

		if($defaultCurrency->code != $currentCurrency->code){
			$price = ($order['details']->order_total / $currencyCourse[$defaultCurrency->code]) * $currencyCourse[$currentCurrency->code];
		}
		$symbol = $currentCurrency->symbol;
		
		$this->price = $price;
		$this->currentCurrency = $currentCurrency;
	}
	
	private function duplicationCheck($user_id){
		
		$lt_time = Pago::get_instance( 'params' )->get(
			'pago.last_transaction_time'.$user_id
		);
		
		//if less than 5 seconds have elapsed since last
		//transaction there is some kind of duplication
		//happening that could be resulting from multipe
		//ajax calls all presses of the submit button
		//thus we return false and cancel the transaction
		if((time()-$lt_time) < 5){
			return false;
		}
		
		Pago::get_instance( 'params' )->set(
			'pago.last_transaction_time'.$user_id,
			time()
		);
			
		return true;
	}
}