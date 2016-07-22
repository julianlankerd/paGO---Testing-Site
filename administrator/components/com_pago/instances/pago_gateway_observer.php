<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die();

class pago_gateway_observer extends KGatewayAbstractObserver
{
	public function on_error(  $code, $message )
	{
		echo $code . ' - ' . $message;
		JError::raiseWarning( $code, $message );
	}

	public function on_get_manifest( $manifest, $order_id=false )
	{
		$gateway = false;

		if( !$order_id ){

			$session 	= JFactory::getSession();
			$gateway 	= $session->get( 'payment_option', false, 'pago_cart' );
			$order_id 	= $session->get( 'order_id', 0, 'pago_cart' );
			$shipper 	= $session->get( 'carrier', false, 'pago_cart' );
			$user_info 	= Pago::get_instance( 'users' )->get();
			$cart 		= Pago::get_instance( 'cart' )->get();
			$store_cfg 	= Pago::get_instance( 'config' )->get();

			//fix shipping
			if ( isset( $cart['carrier'] ) ) {
				$shipper = $cart['carrier'];
			}

		} else {

			$order 	= Pago::get_instance( 'orders' )->get( $order_id );

			extract( $order );

			$cart = $order;

			$cart['subtotal'] = $details->order_subtotal;
			$cart['tax']['amount'] = $details->order_tax;
			$cart['total'] = $details->order_total;

			$gateway = $details->payment_gateway;

			if ( (int) $details->user_id === 0 ) {
				$user_info = $addresses;
			} else {
				$user_info 	= Pago::get_instance( 'users' )->get( $details->user_id );
			}

			$store_cfg 	= Pago::get_instance( 'config' )->get();

			$shipper = array(
				'name' => $details->ship_method_id,
				'value' => $details->order_shipping
			);
		}

		$order_refundtotal = false;
		$order_status = false;

		if( isset( $cart['details'] ) ) {
			$order_refundtotal = $cart['details']->order_refundtotal;
			//$order_status = $this->get_payment_status( $cart['details']->order_status );
			$order_status = $cart['details']->order_status;
		}

		$cc_expirationDate = '01-' . JFactory::getApplication()->input->get( 'cc_expirationDateMonth' ) . '-' . JFactory::getApplication()->input->get( 'cc_expirationDateYear' );

		if ( isset( $user_info['billing']->user_id ) && $user_info['billing']->user_id !== 0 ) {
			//$customer_email = $cart['user_email'];
			$customer_email = $user_info['billing']->user_email;
		} else {
			$customer_email = '';
		}


		if ( empty( $cart['tax'] ) ) {
			$cart['tax']['amount'] = 0.00;
		}

		$manifest = array(

			'id' => (int)1,
			// Transaction mode: 1=live 0=test.
			'mode' => 1,
			// Order id.
			'order_id' => $order_id,
			'txn_id' => @$details->ipn_dump,
			'gateway' => $gateway,
			'order_date' =>  @$details->cdate,
			// Order id.
			'order_status' => $order_status,
			// Order token.
			'order_token' => '',
			// Order description.
			'description' => '',
			// Session id of order session.
			'session_id' => '',
			// Customer's IP address.
			'customer_ip' => '',
			// Customer's user id.
			'customer_id' =>
				isset($user_info['billing']->user_id) ? $user_info['billing']->user_id : 0,
			'customer_email' => $customer_email,
			// Order currency code.
			'currency' => 'USD',
			// Order subtotal.
			'unit_of_measure' => 'LB',
			// Order subtotal.
			'subtotal' => $cart['subtotal'],
			'refund_total' => $order_refundtotal,
			// Order tax total.
			'tax_total' => $cart['tax']['amount'],
			// Order total.
			'total' => $cart['total'],
			// Number of store credits applied to the order.
			'store_credits' => 0,
			// Store credit total applied to the order.
			'store_credit' => 0.0,
			// List of applied promotional codes.
			'promotional_codes' => array(),
			// Single promotional code applied to the order.
			'promo_code' => '',
			// Total amount of discount from promotional code(s).
			'promo_discount' => 0.0,

	  		'continue_shopping_url' => JURI::root() . '?option=com_pago',
			'edit_cart_url' => JURI::root() . '?option=com_pago&view=cart',
	  		'merchant_calculations_url' => JURI::root() . '?option=com_pago&view=ipn&format=ipn&gateway=' . $gateway,
			'notify_url' => JURI::root() . '?option=com_pago&view=ipn&format=ipn&gateway=' . $gateway,
			'cc' => array(
				'cardNumber' =>  JFactory::getApplication()->input->get( 'cc_cardNumber' ),
				'expirationDate' => date( 'Y-m', strtotime( $cc_expirationDate ) ),
				'cv2' =>  JFactory::getApplication()->input->get( 'cc_cv2' )
			)
		);

		$items = array();

		foreach( $cart['items'] as $item ){

			$is_tangible = false;

			if( $item->type == 'tangible' ) $is_tangible = true;

			$subscription = array();

			if( $item->price_type == 'subscription' ){

				switch( $item->sub_recur ){
					case 'Day':
						$billing_period = 'DAILY';
					break;
					case 'Week':
						$billing_period = 'WEEKLY';
					break;
					case 'Month':
						$billing_period = 'MONTHLY';
					break;
					case 'Year':
						$billing_period = 'YEARLY';
					break;
					default:
						$billing_period = 'MONTHLY';
				}

				if( $item->subscr_start_num > 0 ){
					$start_date = strtotime( "now +{$item->subscr_start_num} {$item->subscr_start_type}");
				} else {
					$start_date = strtotime( "now +1 {$item->sub_recur}");
				}

				$start_date = date( 'Y-m-d', $start_date ) . 'T00:00:00Z';

				$db = JFactory::getDBO();

				$db->setQuery("
					SELECT * FROM #__pago_orders_sub_payments
						WHERE item_id = '{$item->id}'
							AND order_id = '{$order_id}'
								ORDER BY sdate DESC
				");

				$payments = $db->loadAssocList();

				$subscription = array(
					'txn_id' => @$item->sub_payment_data,
					'billing_period' => $billing_period,
					'initial_price' => $item->price,
					'start_date' => $start_date,
					'shipping_cost' => $item->subscr_shipping,
					'price_total' => $item->subscr_price,
					'status' => @$item->sub_status,
					'payments' => $payments
				);
			}

			if( isset( $item->cart_qty ) ) $item->qty = $item->cart_qty;

			$items[] = array(
				'id' => $item->id,
				'name' => $item->name,
				'description' => $item->description,
				'price' => $item->price,
				'tax_exempt' => $item->tax_exempt,
				'quantity' => $item->qty,
				'weight' => $item->weight,
				'height' => $item->height,
				'width' => $item->width,
				'length' => $item->length,
				'tangible' => $is_tangible,
				'subscription' => $subscription,
			);
		}

		//addresses
		extract( $user_info );

		//we need to use the orders_addresses data if it is present for the order
		//nasty to have this here should be a jtable call
		$db = JFactory::getDBO();

		$db->setQuery("
			SELECT * FROM #__pago_orders_addresses
				WHERE order_id={$order_id}
					ORDER BY address_type;
		");

		$addresses = $db->loadObjectList();
		
		if( is_array( $addresses ) && isset( $addresses[0] ) && isset( $addresses[1] ) ){
			JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/models' );

			$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );

			$billing = $addresses[0];
			$shipping = $addresses[1];

			//google checkout adds specific address state 2 char abbrv thus added condition
			$db->setQuery("
				SELECT s.* FROM #__pago_country as c
					LEFT JOIN #__pago_country_state as s
						ON c.country_id = s.country_id
							WHERE (s.state_name = '{$billing->state}' OR s.state_2_code = '{$billing->state}')
								OR (s.state_name = '{$shipping->state}' OR s.state_2_code = '{$shipping->state}')
			");

			$states = $db->loadAssocList( 'state_name' );

			$billing->state_2_code = $states[ $billing->state ]['state_2_code'];
			$shipping->state_2_code = $states[ $shipping->state ]['state_2_code'];

			$addresses = array(
				'ship_from' => array(
					'city' => $store_cfg->get( 'general.city' ),
					'region' => $store_cfg->get( 'general.state' ),
					'country_code' => $store_cfg->get( 'general.country' ),
					'postal_code' => $store_cfg->get( 'general.zip' )
				),
				'billing' => array(
					'first_name' => $billing->first_name,
					'middle_name' => $billing->middle_name,
					'last_name' => $billing->last_name,
					'company' => $billing->company,
					'address' => $billing->address_1,
					'address2' => $billing->address_2,
					'city' => $billing->city,
					'region' => $billing->state,
					'region_2_code' => @$billing->state_2_code,
					'postal_code' => $billing->zip,
					'country' => $billing->country,
					'country_2_code' => @$billing->country,
					'email' => $billing->user_email,
					'phone' => $billing->phone_1,
					'phone2' => $billing->phone_2
				),
				'shipping' => array(
					'first_name' => $shipping->first_name,
					'middle_name' => $shipping->middle_name,
					'last_name' => $shipping->last_name,
					'company' => $shipping->company,
					'address' => $shipping->address_1,
					'address2' => $shipping->address_2,
					'city' => $shipping->city,
					'region' => $shipping->state,
					'region_2_code' => @$shipping->state_2_code,
					'postal_code' => $shipping->zip,
					'country' => $shipping->country,
					'country_2_code' => @$shipping->country,
					'email' => $shipping->user_email,
					'phone' => $shipping->phone_1,
					'phone2' => $shipping->phone_2
				)
			);
		} else {
			$addresses = array(
				'ship_from' => array(
					'city' => $store_cfg->get( 'general.city' ),
					'region' => $store_cfg->get( 'general.state' ),
					'country_code' => $store_cfg->get( 'general.country' ),
					'postal_code' => $store_cfg->get( 'general.zip' )
				),
				'billing' => array(
					'first_name' => '',
					'middle_name' => '',
					'last_name' => '',
					'company' => '',
					'address' => '',
					'address2' => '',
					'city' => '',
					'region' => '',
					'region_2_code' => '',
					'postal_code' => '',
					'country' => '',
					'country_2_code' => '',
					'email' => '',
					'phone' => '',
					'phone2' => ''
				),
				'shipping' => array(
					'first_name' => '',
					'middle_name' => '',
					'last_name' => '',
					'company' => '',
					'address' => '',
					'address2' => '',
					'city' => '',
					'region' => '',
					'region_2_code' => '',
					'postal_code' => '',
					'country' => '',
					'country_2_code' => '',
					'email' => '',
					'phone' => '',
					'phone2' => ''
				)
			);
		}


		$manifest['shipments'] = array(
			array(
				'id' => (int)1,
				// Shipping method.
				'carrier' => @$shipper['name'],
				// Shipping method.
				'method' => @$shipper['name'],
				// Order handling total.
				//'handling_total' => 0.0,
				// Order duty total.
				//'duty_total' => 0.0,
				// Order duty total.
				//'tax_total' => 0.0,
				// Order shipping total.
				'shipping_total' => @$shipper['value'],
				'items' => $items,
				'addresses' => $addresses
			)
		);

		//print_r( $manifest );die();

		return $manifest;
	}

	function get_payment_status( $payment_status ){

		switch( $payment_status ){
			case 'P':
				return JText::_( 'K PENDING' );
			break;
			case 'C':
				return JText::_( 'K COMPLETED' );
			break;
			case 'X':
				return JText::_( 'K CANCELLED' );
			break;
			case 'R':
				return JText::_( 'K REFUNDED' );
			break;
			case 'S':
				return JText::_( 'K SHIPPED' );
			break;
			default:
				return JText::_( 'K PENDING' );
		}
	}

	public function on_get_tax( $tax_amount, $order_id, $country_code, $region, $postal_code, $amount ){

		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__pago_country_state AS s
						LEFT JOIN #__pago_country AS c
							ON c.country_id = s.country_id
								WHERE s.state_2_code = '$region'
									AND c.country_2_code = '$country_code'";

		$db->setQuery($query);

		$location = $db->loadObject();

		$order = Pago::get_instance( 'orders' )->get( $order_id );

		extract( $order );
		unset( $order );

		$user = Pago::get_instance( 'users' )->get( $details->user_id );
		$price = Pago::get_instance( 'price' );

		//check if tax rate defined for shipping address
		$tax_rule = $price->gen_tax( $user['groups'],
			$location->country_id,
			$location->state_id,
			$postal_code,
		'shipping' );

		//if shipping address rule not defined check for billing
		if( !$tax_rule ){
			$tax_rule = $price->gen_tax( $user['groups'],
			$location->country_id,
			$location->state_id,
			$postal_code,
		'billing' );
		}


		$tax = str_replace( '%', '', $tax_rule->tax ) / 100;

		$tax_amount = ( $amount * $tax);

		return $tax_amount;
	}

	public function on_merchant_calculation_callback( $tax, $order_id, $country_code, $region, $postal_code ){

		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__pago_country_state AS s
						LEFT JOIN #__pago_country AS c
							ON c.country_id = s.country_id
								WHERE s.state_2_code = '$region'
									AND c.country_2_code = '$country_code'";

		$db->setQuery($query);

		$location = $db->loadObject();

		$order = Pago::get_instance( 'orders' )->get( $order_id );

		extract( $order );
		unset( $order );

		$user = Pago::get_instance( 'users' )->get( $details->user_id );
		$price = Pago::get_instance( 'price' );

		//check if tax rate defined for shipping address
		$tax_rule = $price->gen_tax( $user['groups'],
			$location->country_id,
			$location->state_id,
			$postal_code,
		'shipping' );

		//if shipping address rule not defined check for billing
		if( !$tax_rule ){
			$tax_rule = $price->gen_tax( $user['groups'],
			$location->country_id,
			$location->state_id,
			$postal_code,
		'billing' );
		}

		if( !isset( $tax_rule->tax ) ) return 0;

		$tax = str_replace( '%', '', $tax_rule->tax ) / 100;

		$items_total = 0;

		//calculate total
		foreach( $items as $item ){
			//ignore items that are tax exempt
			if( !$item->tax_exempt){
				$items_total = $items_total + ( $item->price * $item->qty );
			}
		}

		$tax = ( $items_total * $tax);

		return $tax;
	}

	public function on_new_order_notification(  $txn_id, $order_id )
	{
		$db = JFactory::getDBO();

		$db->setQuery("
			UPDATE #__pago_orders
				SET ipn_dump='{$txn_id}'
					WHERE order_id=".(int) $order_id);
		$db->query();

		// we have a new order, customer has checkout need to clear cart
		$query = 'SELECT user_id FROM #__pago_orders WHERE order_id = ' . (int) $order_id;
		$db->setQuery( $query );
		$order_user = $db->loadResult();

		$query = 'SELECT id, data FROM #__pago_cookie WHERE user_id = ' . (int) $order_user;
		$db->setQuery( $query );
		$cookie = $db->loadAssoc();

		if ( !$cookie ) {
			// we couldn't find a cookie for the user nothing else to do
			return;
		}

		$data = json_decode( $cookie['data'] );

		$data->cart = false;

		$data = json_encode( $data );

		$query = 'UPDATE #__pago_cookie SET data = ' . $db->Quote( $data ) .
			' WHERE id = ' . $cookie['id'];

		$db->setQuery( $query );
		$db->query();

		// tigger event so we can do stuff when a new order is placed
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin( 'pago_orders' );
		$dispatcher->trigger(
			'on_new_order',
			array( &$order_id )
		);
	}

	public function on_new_order_notification_subscription( $order_id, $item_id, $payment_data )
	{
		$db = JFactory::getDBO();

		$db->setQuery("
			UPDATE #__pago_orders_items
				SET sub_payment_data = '{$payment_data}'
					WHERE order_id = {$order_id} AND item_id = {$item_id}
		");

		$db->query();

		// tigger event so we can do stuff when a new subscription is placed
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin( 'pago_orders' );
		$dispatcher->trigger(
			'on_new_order_subscription',
			array( &$order_id )
		);
	}

	public function on_payment_notification_subscription( $txn_id, $price, $payment_data )
	{
		$db = JFactory::getDBO();

		$db->setQuery("
			SELECT * FROM #__pago_orders_items
				WHERE sub_payment_data = '{$payment_data}'
		");

		$result = $db->loadObject();

		if( !is_object( $result ) ) return;

		$item_id = $result->item_id;

		$db->setQuery("
			INSERT INTO #__pago_orders_sub_payments
				SET
					order_id = {$result->order_id},
					item_id = {$result->item_id},
					txn_id = '{$txn_id}',
					payment = '{$price}',
					payment_data = '{$payment_data}'
		");

		$db->query();
	}

	public function on_order_refund( $order_id, $refund_amount, $total_refund_amount ){

		$dp = JDispatcher::getInstance();
		$db = JFactory::getDBO();

		$db->setQuery("UPDATE #__pago_orders
							SET order_refundtotal = '{$total_refund_amount}'
								WHERE order_id = {$order_id}");

		$db->query();

		$dp->trigger( 'onOrderRefund', array( $order_id, $refund_amount, $total_refund_amount ) );
	}

	public function on_subscription_state_change( $txn_id, $state ){

		$dp = JDispatcher::getInstance();
		$db = JFactory::getDBO();

		//update order with new status
		$db->setQuery("UPDATE #__pago_orders_items
							SET sub_status = '{$state}'
								WHERE sub_payment_data = '{$txn_id}'");

		$db->query();

		//$dp->trigger( 'onOrderRefund', array( $order_id, $refund_amount, $total_refund_amount ) );
	}

	public function on_order_state_change_notification( $order_id, $state ){

		$order 		= Pago::get_instance( 'orders' )->get( $order_id );
		//Instances for price formating, and proper template loading
		$template 	= Pago::get_instance( 'template' );
		$config		= Pago::get_instance( 'config' )->get();
		$price 		= Pago::get_instance( 'price' );

		//Current Theme
		$theme = $config->get('template.pago_theme', 'default');

		//Extract theme details from find_paths()
		list(
			$theme_path,
			$theme_css_path,
			$theme_css_url,
			$theme_functions_path
		) = $template->find_paths( $theme );

		$order_details = $order['details'];

		//if the ipn status is the same as the current order status
		//nothing has changed so we break out
		if( $state == $order_details->order_status ) return;

		$dp = JDispatcher::getInstance();
		$db = JFactory::getDBO();

		//update order with new status
		$db->setQuery("UPDATE #__pago_orders
							SET order_status = '{$state}'
								WHERE order_id = {$order_id}");

		$db->query();

		//trigger order status update event
		$dp->trigger( 'onOrderStatusUpdate', array( &$order, $state ) );

		//only send on order complete
		if( $state == 'C' ){

			$manifest = $this->on_get_manifest( array(), $order_id );

			//format the prices to USD (currently the static locale set) (this probably shouldn't be done here)
			$manifest['subtotal'] 			= $price->format( $manifest['subtotal'] );
			$manifest['refund_total']	 	= $price->format( $manifest['refund_total'] );
			$manifest['tax_total']			= $price->format( $manifest['tax_total'] );
			$manifest['total'] 				= $price->format( $manifest['total'] );

			foreach( $manifest['shipments'] as $s => $shipment ) {
				$manifest['shipments'][$s]['shipping_total'] = $price->format( $shipment['shipping_total'] );

				foreach( $shipment['items'] as $i => $item ) {
					$manifest['shipments'][$s]['items'][$i]['price'] = $price->format( $item['price'] );
				}
			}

			ob_start();

				include( $theme_path . '/emails/order_state_change_notification.php' );

			$body = ob_get_clean();

			ob_start();

				include( $theme_path . '/emails/order_state_change_notification_admin.php' );

			$admin_body = ob_get_clean();

			$to = $manifest['customer_email'];

			$store_cfg 	= Pago::get_instance( 'config' )->get();

			$from = array( $store_cfg->get( 'general.store_email' ), $store_cfg->get( 'general.pago_store_name' ) );

			$subject = JText::_( 'PAGO_EMAIL_ORDER_STATE_CHANGE_SUBJECT' );

			$this->on_send_email( $subject, $body, $to, $from, true );

			//send admin email receipt
			$subject = JText::_( 'PAGO_EMAIL_ORDER_STATE_CHANGE_SUBJECT_ADMIN' );

			$this->on_send_email( $subject, $admin_body, $from[0], $from, true );
		}
	}

	function on_send_email( $subject, $body, $to, $from, $html=false )
	{
		# Invoke JMail Class
		$mailer = JFactory::getMailer();

		# Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender( $from );

		# Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient( $to );

		$mailer->setSubject( $subject );
		$mailer->setBody( $body );

		# If you would like to send as HTML, include this line; otherwise, leave it out
		if( $html ) $mailer->isHTML();

		# Send once you have set all of your options
		$mailer->send();
	}

	public function on_order_state_change_notification_subscription( $order_id=false, $payment_data, $state ){

		$db = JFactory::getDBO();
		$dp = JDispatcher::getInstance();

		$db->setQuery("
			UPDATE #__pago_orders_items
				SET sub_status = '{$state}'
					WHERE sub_payment_data = '{$payment_data}'
		");

		$db->query();

		if( !$order_id ){
			$db->setQuery("
				SELECT order_id FROM #__pago_orders_items
						WHERE sub_payment_data = '{$payment_data}'
			");

			$data = $db->loadObject();
			$order_id = $data->order_id;
		}

		$order = Pago::get_instance( 'orders' )->get( $order_id );

		$dp->trigger( 'onOrderSubscrStatusUpdate', array( &$order, $state ) );

	}

	public function on_update_order_shipping( $order_id, $shipper_name=false, $shipper_cost=false )
	{

		$db = JFactory::getDBO();

		$db->setQuery("
			UPDATE #__pago_orders
				SET
				order_shipping='{$shipper_cost}',
				ship_method_id='{$shipper_name}'
					WHERE order_id={$order_id}");

		$db->query();
	}

	public function on_update_address( $order_id, $user_id, stdClass $billing=NULL, stdClass $shipping=NULL )
	{
		$db = JFactory::getDBO();

		if( is_object( $shipping ) ){

			$db->setQuery("DELETE FROM #__pago_orders_addresses
								WHERE order_id={$order_id}
									AND address_type = 'm'");

			$db->query();

			//added query to make sure full statename is saved as
			//2 char state code is ambiguos in regards to country
			$db->setQuery("
				SELECT s.state_name FROM #__pago_country_state as s
							WHERE (s.state_2_code = '{$shipping->region}')
			");

			$state = $db->loadObject();

			$db->setQuery("
			INSERT INTO #__pago_orders_addresses
				SET
					order_id = {$order_id},
					user_id = {$user_id},
					address_type = 'm',
					address_type_name='shipping',
					company='{$shipping->company_name}',
					last_name='{$shipping->first_name}',
					first_name='{$shipping->last_name}',
					phone_1='{$shipping->phone}',
					fax = '{$shipping->fax}',
					address_1='{$shipping->address1}',
					address_2='{$shipping->address2}',
					city='{$shipping->city}',
					state ='{$state->state_name}',
					country ='{$shipping->country_code}',
					zip ='{$shipping->postal_code}',
					user_email='{$shipping->email}'
			");

			$db->query();

		}

		if( is_object( $billing ) ){

			$db->setQuery("DELETE FROM #__pago_orders_addresses
								WHERE order_id={$order_id}
									AND address_type = 'b'");

			$db->query();

			//added query to make sure full statename is saved as
			//2 char state code is ambiguos in regards to country
			$db->setQuery("
				SELECT s.state_name FROM #__pago_country_state as s
							WHERE (s.state_2_code = '{$billing->region}')
			");

			$state = $db->loadObject();

			$db->setQuery("
			INSERT INTO #__pago_orders_addresses
				SET
					order_id = {$order_id},
					user_id = {$user_id},
					address_type = 'b',
					address_type_name='billing',
					company='{$billing->company_name}',
					last_name='{$billing->first_name}',
					first_name='{$billing->last_name}',
					phone_1='{$billing->phone}',
					fax = '{$billing->fax}',
					address_1='{$billing->address1}',
					address_2='{$billing->address2}',
					city='{$billing->city}',
					state ='{$state->state_name}',
					country ='{$billing->country_code}',
					zip ='{$billing->postal_code}',
					user_email='{$billing->email}'
			");

			$db->query();
		}
	}

	function on_redirect_completed( $order_id, $gateway=false ){

		JFactory::getApplication()->redirect(
			JURI::root() . '?option=com_pago&view=checkout&task=complete&order_id=' . $order_id . '&gateway=' . $gateway
		);
	}


}
