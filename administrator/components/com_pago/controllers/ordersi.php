<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerOrdersi extends PagoController
{
	private $_view = 'Ordersi';

	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );

		$this->redirect_to = 'index.php?' . http_build_query( array(
			'option'=> JFactory::getApplication()->input->get( 'option' ),
			'view'=> JFactory::getApplication()->input->get( 'view' )
		));

		$this->redirect_to_form = 'index.php?' . http_build_query( array(
			'option'=> JFactory::getApplication()->input->get( 'option' ),
			'view'=> JFactory::getApplication()->input->get( 'view' ),
			'task' => 'edit'
		) ) . '&cid[]=' . JFactory::getApplication()->input->get( 'id' );
		if(isset($_REQUEST['rel'])){
			$this->rel =  json_decode(str_replace( "'", '"', $_REQUEST['rel'] ));
		}
		else{
			$this->rel = null;
		}
	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}

	function cancel_subscription()
	{
		$manifest = $this->get_manifest( $this->rel->order_id );
		$item = $this->get_order_item( $this->rel->order_id, $this->rel->item_id );
		$txn_id = $item->sub_payment_data;

		$response = $this->process_operation(
			$manifest['gateway'],
			$txn_id,
			'cancel_subscription',
			false,
			$manifest
		);

		if ( $response->success )
			return $this->setRedirect( $this->redirect_to_form,
				JText::_( 'PAGO_GATEWAY_RESPONSE' ) . ': ' .
					$response->code . ' ' . $response->text );

		JFactory::getApplication()->enqueueMessage(JText::_('PAGO_OPERATION_FAILED') . ': ' . $response->code . ' ' . $response->text, 'error');

		return $this->setRedirect( $this->redirect_to_form, false );
	}

	function refund_payment()
	{
		$manifest = $this->get_manifest( $this->rel->order_id );

		$refund_amount = $manifest['total'] - $manifest['refund_total'];

		JFactory::getApplication()->input->set('amount', $this->rel->amount );

		$p_refund = JFactory::getApplication()->input->get( 'refund_partial' . $this->rel->txn_id );

		if ( $p_refund ) JFactory::getApplication()->input->set( 'amount', $p_refund );

		$response = $this->process_operation(
			$manifest['gateway'],
			$manifest['txn_id'],
			'financial_operations',
			'refund-order'
		);

		if ( $response->success )
			return $this->setRedirect( $this->redirect_to_form,
				JText::_( 'PAGO_GATEWAY_RESPONSE' ) . ': ' .
					$response->code . ' ' . $response->text );

		JFactory::getApplication()->enqueueMessage(JText::_('PAGO_OPERATION_FAILED') . ': ' . $response->code . ' ' . $response->text, 'error');

		return $this->setRedirect( $this->redirect_to_form, false );
	}

	function cancel_order()
	{
		//$order_id = $this->rel->order_id;
		$order_id = JFactory::getApplication()->input->get( 'id');
		$item_id = 0;
		$order_status = 'X';
		$tracking_number = '';
		$update_order 	= Pago::get_instance('orders')->updateOrderSatus($order_id, $order_status, $tracking_number, $item_id);

		if ($update_order)
		{
			ob_clean();
			return $this->setRedirect( $this->redirect_to_form,
				JText::_( 'PAGO_ADDORDER_ORDER_CANCELED' ) );
		}

		JFactory::getApplication()->enqueueMessage(JText::_('PAGO_OPERATION_FAILED'), 'error');

		return $this->setRedirect( $this->redirect_to_form, false );
	}

	function process_operation( $gateway, $txn_id, $operation, $sub_operation=false, $manifest=false )
	{
		JPluginHelper::importPlugin( 'pago_gateway', 'gateway' );

		$KGateway = array_pop(
			JDispatcher::getInstance()->trigger( 'kg_initialise', array( 'operation', $gateway ) )
		)
		->add_observer( Pago::get_instance( 'gateway_observer' ) )
		->process_operation( $txn_id, $operation, $sub_operation, $manifest );

		return $KGateway->response;
	}

	function get_manifest( $order_id )
	{
		JPluginHelper::importPlugin( 'pago_gateway', 'gateway' );

		return array_pop(
			JDispatcher::getInstance()->trigger( 'kg_load_manifest', array( false, $order_id ) )
		);
	}

	function get_order_item( $order_id, $item_id )
	{
		$db = JFactory::getDBO();

		$db->setQuery("
			SELECT *
				FROM #__pago_orders_items
					WHERE order_id = {$order_id}
						AND item_id  = {$item_id}
		");

		return $db->loadObject();
	}

	function edit()
	{
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function save()
	{
		$this->store();
		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_SAVED_PARAMETERS' ) );
	}

	function store()
	{
		$jinput = JFactory::getApplication()->input;
		
		if($jinput->get('pago_customer_number', '', 'string'))
			$this->update_cc();
		
		$order_id = $jinput->get('id', '', 'int');
		$b = (object) $jinput->get('address_billing', '', 'array');
		$s = (object) $jinput->get('address_shipping', '', 'array');
		
		
		$user_id = $jinput->get('pg-filter_user', '', 'int');
		$mdate = date('Y-m-d H:i:s', time());

		$db = JFactory::getDBO();

		//addresses
		$db->setQuery("
			DELETE FROM #__pago_orders_addresses WHERE order_id=$order_id;
		");

		$db->query();

		$db->setQuery(
				"INSERT INTO #__pago_orders_addresses (
					order_id,
					user_id,
					company,
					`title`,
					last_name,
					first_name,
					middle_name,
					phone_1,
					phone_2,
					address_1,
					address_2,
					city,
					fax,
					user_email,
					country,
					state,
					zip,
					address_type,
					mdate)
				VALUES(
					'{$order_id}',
					'{$user_id}',
					'{$b->company}',
					'{$b->title}',
					'{$b->last_name}',
					'{$b->first_name}',
					'{$b->middle_name}',
					'{$b->phone_1}',
					'{$b->phone_2}',
					'{$b->address_1}',
					'{$b->address_2}',
					'{$b->city}',
					'{$b->fax}',
					'{$b->user_email}',
					'{$b->country}',
					'{$b->region}',
					'{$b->postal_code}',
					'b',
					'{$mdate}'
				),(
					'{$order_id}',
					'{$user_id}',
					'{$s->company}',
					'{$s->title}',
					'{$s->last_name}',
					'{$s->first_name}',
					'{$s->middle_name}',
					'{$s->phone_1}',
					'{$s->phone_2}',
					'{$s->address_1}',
					'{$s->address_2}',
					'{$s->city}',
					'{$s->fax}',
					'{$s->user_email}',
					'{$s->country}',
					'{$s->region}',
					'{$s->postal_code}',
					's',
					'{$mdate}'
				)"
			);

		$db->query();

		$order_status = $jinput->get('order_status', '', 'string');
		Pago::get_instance('orders')->updateOrderSatus($order_id, $order_status);
	}

	function apply()
	{
		$this->store();
		$this->setRedirect( $this->redirect_to_form,  JText::_( 'PAGO_APPLIED_PARAMETERS' ) );
	}

	function cancel()
	{
		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_CANCELED_OPERATION' ) );
	}

	function invoice()
	{
		JFactory::getApplication()->input->set( 'layout', 'invoice' );
		parent::display();
	}

	function grid_edit()
	{
		$operation = JFactory::getApplication()->input->get( 'oper' );

		switch ( JFactory::getApplication()->input->get( 'oper' ) ) {
			case 'del':
				$this->delete();
			break;
			case 'edit':
				$this->save_status();
			break;
		}
	}

	function save_status()
	{
		$order_status = JFactory::getApplication()->input->get( 'order_status' );
		$id = JFactory::getApplication()->input->get( 'id' );

		$db = JFactory::getDBO();

		$db->setQuery("
			UPDATE #__pago_orders SET `order_status` = '$order_status'
			WHERE `order_id` = $id;
		");

		$db->query();
	}

	function remove()
	{
		if( !is_array( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) ) return;
		
		$where = false;

		foreach ( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) as $id ) {
			$where .= " `order_id`=$id OR";
		}

		$where = substr_replace( $where, '', -3 );

		$db = JFactory::getDBO();
		
		$db->setQuery( "SELECT * FROM #__pago_orders_items WHERE $where" );
		$items = $db->loadObjectList();
		
		foreach($items as $item){
			if($item->price_type == 'subscription'){
				
				$sub_payment_data = json_decode($item->sub_payment_data);
				//cancel_at_period_end":true,"canceled_at":1460364831,"
				if(is_object($sub_payment_data) && !$sub_payment_data->canceled_at){
					JFactory::getApplication()->enqueueMessage(
						JText::_( 'PAGO_ORDER_HAS_ACTIVE_SUBSCR' ), 'error');
						
					$this->setRedirect('index.php?option=com_pago&view=ordersi');
					return;
				}
			}
		}
		
		$db->setQuery( "DELETE FROM #__pago_orders WHERE $where" );
		$db->query();

		$db->setQuery( "DELETE FROM #__pago_orders_items WHERE $where" );
		$db->query();

		$msg = JText::_( 'Successfully Deleted Order' );
		$this->setRedirect(
			'index.php?option=com_pago&view=ordersi',
			$msg
		);
	}

	function _save()
	{
		$ship_method = JFactory::getApplication()->input->get( 'shipping' );

		$order_id = $this->store();

		if ( !$ship_method['ship_method_id'] ) {
			$msg = JText::_( 'PLEASE SELECT SHIPPING OPTIONS' );
			$this->setRedirect(
				'index.php?option=com_pago&view=orders&task=edit&cid[]=' . $order_id,
				$msg
			);
			return;
		}

		$msg = JText::_( 'Successfully Saved Parameters' );
		$this->setRedirect( 'index.php?option=com_pago&view=orders', $msg );
	}

	function _apply()
	{
		$order_id = $this->store();

		$msg = JText::_( 'Successfully Applied Parameters' );
		$this->setRedirect(
			'index.php?option=com_pago&view=orders&task=edit&cid[]=' . $order_id,
			$msg
		);
	}

	function _store()
	{
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('id');
		$details = $jinput->get( 'details' );
		$items = $jinput->get( 'params' );
		$ship_method = $jinput->get( 'shipping' );
		$discounts = $jinput->get( 'discounts' );
		$address_billing = $jinput->get( 'address_billing' );
		$address_shipping = $jinput->get( 'address_shipping' );

		$groups = $jinput->get( 'grouplist' );
		$groups = $groups['groups'];

		$user_id = $details['user_id'];

		$items = json_decode( $items['itemslist'], true );
		$ship_method = $ship_method['ship_method_id'];

		//$details['order_discount'] = $discounts['order_discount'];

		$details['order_subtotal'] = 0;

		foreach ( $items as $item ) {
			$details['order_subtotal'] =
				$details['order_subtotal'] + ( $item['qty'] * $item['price'] );
		}

		$details['order_subtotal'] = $details['order_subtotal'] - $details['order_discount'];

		$details['order_shipping'] = false;

		if ( $ship_method ) {
			$details['order_shipping'] = array_pop( explode('|', $ship_method) );
		}

		$details['order_total'] = $details['order_shipping'] + $details['order_subtotal'];

		if ( $order_id ) {
			$details['order_id'] = $order_id;
			//2011-01-26 16:10:48
		} else {
			$details['cdate'] = date( 'Y-m-d H:i:s', time() );
		}

		$details['ship_method_id'] = $ship_method;

		$set = false;

		foreach ( $details as $name => $value ) {
			$set .= "$name='$value',";
		}

		$set = substr_replace( $set, '', -1 );

		$db = JFactory::getDBO();

		$db->setQuery("
			INSERT INTO #__pago_orders SET $set
			ON DUPLICATE KEY UPDATE $set
		");

		$db->query();

		$insertid = $db->insertid();
		if ( $insertid ) {
			$order_id = $insertid;
		}

		$db->setQuery("
			DELETE FROM #__pago_orders_items WHERE order_id=$order_id;
		");

		$db->query();

		$set = false;

		foreach ( $items as $item ) {
			$set .= '('. $order_id . ',' . $item['id'] . ',' . $item['qty'] . ',"' .
				$item['price'] .'"),';
		}

		$set = substr_replace( $set, '', -1 );

		$db->setQuery("
			INSERT INTO #__pago_orders_items ( order_id, item_id, qty, price )
			VALUES $set;
		");

		$db->query();

		//addresses
		$db->setQuery("
			DELETE FROM #__pago_orders_addresses  WHERE order_id=$order_id;
		");

		$db->query();

		//billing address
		$set = "order_id={$order_id},user_id={$user_id},address_type='b',";

		foreach ( $address_billing as $name => $value ) {
			$set .= "$name='$value',";
		}

		$set = substr_replace( $set, '', -1 );

		$db = JFactory::getDBO();

		$db->setQuery("
			INSERT INTO #__pago_orders_addresses SET $set
		");

		$db->query();

		//shipping address
		$set = "order_id={$order_id},user_id={$user_id},address_type='s',";

		foreach ( $address_shipping as $name => $value ) {
			$set .= "$name='$value',";
		}

		$set = substr_replace( $set, '', -1 );

		$db = JFactory::getDBO();

		$db->setQuery("
			INSERT INTO #__pago_orders_addresses SET $set
		");

		$db->query();

		$db->setQuery( "DELETE FROM #__pago_groups_users WHERE user_id = {$user_id}" );
		$db->query();

		if ( !empty( $groups ) ) {
			foreach ( $groups as $group_id ) {
				$db->setQuery(
					"INSERT INTO #__pago_groups_users SET group_id = {$group_id},".
					"user_id = {$user_id}"
				);
				$db->query();
			}
		}

		return $order_id;
	}

	public function ipn()
	{
		$session 	= JFactory::getSession();
		$jinput = JFactory::getApplication()->input;
		$gateway = $jinput->get('gateway', '', 'string');

		// Event can be found here
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin('pago_gateway');
		$paymentResult = array();
		$dispatcher->trigger(
			'onAfterAuthorizePayment',
			array( &$paymentResult, &$gateway, &$jinput)
		);

		foreach ( $paymentResult as $pgateway => $result)
		{
			if ($pgateway == $gateway)
			{
				if( $result->order_status == "P" )
				{
					$order_id = $result->order_id;
					$order 	= Pago::get_instance('orders')->get($order_id);
					JFactory::getApplication()->enqueueMessage($result->message, 'error');
					return;
				}

				$order_id = $result->order_id;
				$order 	= Pago::get_instance('orders')->get($order_id);

				Pago::get_instance('orders')->onOrderComplete($order, $result);
				$successMessage = $result->message;
			}
		}

		$this->setRedirect ( 'index.php?option=com_pago&view=ordersi', JText::_( 'PAGO_ORDER_SAVED' ).$successMessage);
	}

	function removeOrderItem()
	{
		ob_clean();
		$db = JFactory::getDBO();
		$config = Pago::get_instance('config')->get('global');
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', '', 'int');
		$item_id = $jinput->get('item_id', '', 'int');

		$orderItem = $this->get_order_item($order_id, $item_id);

		// Update Order
		if ($config->get('checkout.shipping_type'))
		{
			$deduct_order_total = (($orderItem->price + $orderItem->order_item_tax) * $orderItem->qty ) + $orderItem->order_item_shipping + $orderItem->order_item_shipping_tax;
			$deduct_order_tax = ($orderItem->order_item_tax * $orderItem->qty ) + $orderItem->order_item_shipping_tax;
			$deduct_order_shipping = $orderItem->order_item_shipping;
		}
		else
		{
			$deduct_order_total = (($orderItem->price + $orderItem->order_item_tax) * $orderItem->qty );
			$deduct_order_tax = ($orderItem->order_item_tax * $orderItem->qty );
			$deduct_order_shipping = 0;
		}

		$deduct_order_subtotal = $orderItem->price;

		$db->setQuery("
			UPDATE #__pago_orders SET `order_total` = order_total- $deduct_order_total , `order_subtotal` = order_subtotal- $deduct_order_subtotal ,
			`order_tax` = order_tax- $deduct_order_tax, `order_shipping` = order_shipping- $deduct_order_shipping
			WHERE `order_id` = $order_id;
		");

		$db->query();

		// Remove Item from Database
		$where = " order_id = " . $order_id . " and item_id = " . $item_id;
		$db->setQuery("DELETE FROM #__pago_orders_items WHERE $where");
		$db->query();

		$order 	= Pago::get_instance('orders')->get($order_id);
		$order = $order['details'];
		echo json_encode($order);
		exit;


	}

	public function updateOrderSatus()
	{
		ob_clean();
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', '', 'int');
		$item_id = $jinput->get('item_id', '', 'int'); 
		$order_status = $jinput->get('order_status', '', 'string');
		$tracking_number = $jinput->get('tracking_number', '', 'string');
		$update_order 	= Pago::get_instance('orders')->updateOrderSatus($order_id, $order_status, $tracking_number, $item_id);

		if ($update_order)
		{
			ob_clean();
			echo $update_order;
			exit;
		}
	}

	public function checkOrderwithMaxmind()
	{
		ob_clean();
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', '', 'int');
		$maxmindResult 	= Pago::get_instance('orders')->checkOrderwithMaxmind($order_id);

		if ($maxmindResult[0] != "")
		{
			$maxmindResult = explode("###", $maxmindResult[0]);
			ob_clean();
			echo  "<strong>".JText::_('COM_PAGO_ORDERSI_FRAUD_SCORE')."</strong> : ". $maxmindResult[0];
			echo "<br/>";
			echo  "<strong>".JText::_('COM_PAGO_ORDERSI_FRAUD_RISK_SCORE')."</strong> : ". $maxmindResult[1];
			echo "<br/>";
			echo  "<strong>".JText::_('COM_PAGO_ORDERSI_FRAUD_MESSAGE')."</strong> : ". $maxmindResult[2];
			echo "<br/>";
			exit;
		}
	}

	public function refundOrderPayment()
	{
		ob_clean();
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', '', 'int');
		$refund_amount = $jinput->get('refund_amount', '', 'float');
		$refund_order 	= Pago::get_instance('orders')->refundOrderPayment($order_id, $refund_amount);
		ob_clean();
		echo $refund_order; exit;
	}

	function updateOrderItemRows()
	{
		ob_clean();
		$db = JFactory::getDBO();
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', '', 'int');
		$order 	= Pago::get_instance('orders')->get($order_id);
		$orderItems = $order['items'];
		$orderItemRows = '';
		$orderItemRows .= '<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">';
		$orderItemRows .= '<thead>
						<tr class="pg-sub-heading">
							<td class="pg-sku">
								<div class="pg-sort-indicator-wrapper">
									'.JText::_("PAGO_ADDORDER_REMOVE_ITEM").'
								</div>
							</td>
							<td class="pg-sku">
								<div class="pg-sort-indicator-wrapper">
									'.JText::_("PAGO_ADDORDER_ITEM_NAME").'
								</div>
							</td>
							<td class="pg-item-name">
								<div class="pg-sort-indicator-wrapper">
									'.JText::_("PAGO_ADDORDER_ITEM_PRICE_WITHOUT_TAX").'
								</div>
							</td>
							<td class="pg-item-name">
								<div class="pg-sort-indicator-wrapper">
									'.JText::_("PAGO_ADDORDER_ITEM_TAX").'
								</div>
							</td>
							<td class="pg-item-name">
								<div class="pg-sort-indicator-wrapper">
									'.JText::_("PAGO_ADDORDER_ITEM_QUANTITY").'
								</div>
							</td>
							<td class="pg-item-name">
								<div class="pg-sort-indicator-wrapper">
									'.JText::_("PAGO_ITEM_TOTAL_PRICE").'
								</div>
							</td>
						</tr>
					</thead>';
							foreach ($orderItems as $i=>$item):
						 	$item = (object)$item;
						 	$orderItemRows .= '<tr class="pg-table-content pg-row'.($i % 2).'" id="pg_order_item_'.$item->id.'">
								<td class="pg-checkbox">
								<button type="button" confirm="true" onclick ="removeOrderItem( '.$order_id.', '.$item->id.');">
									'.JText::_( "PAGO_ORDER_ITEM_REMOVE" ).'
								</button>
								</td>
								<td class="pg-item-sku"">'.$item->name.'</td>
								<td class="pg-item-name" >'.$item->price.'</td>
								<td class="pg-item-name" >'.$item->order_item_tax.'</td>
								<td class="pg-item">'.$item->qty.'</td>
								<td class="pg-item-price">'.(($item->price + $item->order_item_tax) * ($item->qty)) .'</td>
							</tr>';
						 endforeach;
						$orderItemRows .= '</table>';
		echo $orderItemRows; exit;

	}


	function saveOrderItem()
	{
		ob_clean();
		$db = JFactory::getDBO();
		$config = Pago::get_instance('config')->get('global');
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', '', 'int');
		$item_id = $jinput->get('item_id', '', 'int');
		$user_id = $jinput->get('user_id', '0', 'INT');
		$addressid = $jinput->get('address_id', '0', 'INT');
		$saddressid = $jinput->get('saddress_id', '0', 'INT');
		$qty = $jinput->get('qty', '0', 'INT');

		$orderItem = PagoHelper::get_product($item_id);
		$userData = Pago::get_instance('price')->getUserAddressInfoForTax($user_id, $addressid, $saddressid, $order_id);
		$itemTax = Pago::get_instance('price')->getProductTax($orderItem, $userData);
		$itemTaxAmount = $itemTax ['item_tax'];

		// Store Order Item
		$rowitem = JTable::getInstance('orders_items', 'Table');
		$data = array();
		$data['order_id'] = $order_id;
		$data['item_id'] = $item_id;
		$data['qty'] = $qty;
		$data['price'] = $orderItem->price;
		$data['price_type'] = '';
		$data['attributes'] = '';
		$data['sub_recur'] = '';
		$data['order_item_tax'] = $itemTaxAmount;
		$data['order_item_shipping_tax'] = 0;
		$data['order_item_shipping'] = 0;
		$data['order_item_ship_method_id'] = '';

		if (!$rowitem->bind($data))
		{
			return JFactory::getApplication()->enqueueMessage($rowitem->getError(), 'error');
		}

		if (!$rowitem->check())
		{
			return JFactory::getApplication()->enqueueMessage($rowitem->getError(), 'error');
		}

		if (!$rowitem->store())
		{
			return JFactory::getApplication()->enqueueMessage($rowitem->getError(), 'error');
		}

		// Update Order
		$add_order_total = ($orderItem->price + $itemTaxAmount) * $qty;
		$add_order_tax = $itemTaxAmount * $qty;
		$add_order_shipping = 0;
		$add_order_subtotal = $orderItem->price * $qty;

		$db->setQuery("
			UPDATE #__pago_orders SET `order_total` = order_total+ $add_order_total , `order_subtotal` = order_subtotal+ $add_order_subtotal ,
			`order_tax` = order_tax + $add_order_tax, `order_shipping` = order_shipping + $add_order_shipping
			WHERE `order_id` = $order_id;
		");

		$db->query();
		$order 	= Pago::get_instance('orders')->get($order_id);
		$order = $order['details'];
		ob_clean();
		echo json_encode($order);
		exit;
	}

	function getItemDetail()
	{
		ob_clean();
		$jinput = JFactory::getApplication()->input;
		$item_id = $jinput->get('item_id', '0', 'INT');
		$order_id = $jinput->get('order_id', '0', 'INT');
		$user_id = $jinput->get('user_id', '0', 'INT');
		$addressid = $jinput->get('addressId', '0', 'INT');
		$saddressid = $jinput->get('saddressId', '0', 'INT');
		$item = PagoHelper::get_product($item_id);
		$userData = Pago::get_instance('price')->getUserAddressInfoForTax($user_id, $addressid, $saddressid, $order_id);
		$itemTax = Pago::get_instance('price')->getProductTax($item, $userData);

		$itemTaxAmount = $itemTax ['item_tax'];
		$resultItem = array();
		$resultItem [] = $item->name;
		$resultItem [] = $item->price;
		$resultItem [] = $itemTaxAmount;
		$resultItem [] = $item->price + $itemTaxAmount;
		$resultItem [] = 1;
		$resultItem [] = $itemTax ['item_tax_rate'];
		$resultItem [] = $itemTax['apply_tax_on_shipping'];
		$resultItem [] = $item->free_shipping;

		$resultItem = implode("##", $resultItem);
		echo $resultItem;
		exit;
	}
	
	function export()
	{
		$jinput = JFactory::getApplication()->input;
		
		$startdate 		= $jinput->get('startdate');
		$enddate 		= $jinput->get('enddate');
		$orderstatus 	= $jinput->get('order_status');
		$ordercurrency 	= $jinput->get('order_currency');
		
		$model 		= $this->getModel ('ordersi');
		$order_id	= $model->orderExport($startdate, $enddate, $orderstatus, $ordercurrency);
		
 		$this->setRedirect( 'index.php?option=com_pago&view=ordersi');
	}
	
	function export_csv()
	{
		$jinput = JFactory::getApplication()->input;
		
		$cid 		= $jinput->get('cid', array(), 'array');
		$model 		= $this->getModel ('ordersi');
		$startdate 		= $jinput->get('startdate');
		$enddate 		= $jinput->get('enddate');
		$productIds 		= $jinput->get('products', '' , 'array');
		$order_id	= $model->orderExportCSV($startdate, $enddate, $productIds);
		
 		$this->setRedirect( 'index.php?option=com_pago&view=ordersi');
	}
	
	function update_cc()
	{
		$jinput = JFactory::getApplication()->input;
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$payload = (object)[
			'livemode' => $livemode,
			'id' => $jinput->get('pago_customer_id', '', 'string'),
			'account' => $account,
			'number' => $jinput->get('pago_customer_number', '', 'string'),
			'cvc' => $jinput->get('pago_customer_cvc', '', 'string'),
			'exp_month' => $jinput->get('pago_customer_exp_month', '', 'string'),
			'exp_year' => $jinput->get('pago_customer_exp_year', '', 'string')
		];
		
		$api = new PagoControllerApi;
		$res = $api->call('PUT', 'subscr', $payload, false);
		
		if(@$res->id){
	       	JFactory::getApplication()->enqueueMessage(JText::_('PAGO_ORDERI_CARDSUCCESS'), 'message');
		} else {
			JFactory::getApplication()->enqueueMessage($res->detail, 'warning');
		}
	}
	
	function subscr_patch($order_item_id, $quantity, $sub_payment_data){
		
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$payload = (object)[
			'livemode' => $livemode,
			'account' => $account,
			'id' => $sub_payment_data->id,
			'customer' => $sub_payment_data->customer,
			'quantity' => $quantity
		];
		
		$api = new PagoControllerApi;
		$res = $api->call('PATCH', 'subscr', $payload, false);
		
		if(@$res->id){
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');
	    	$order_item = JTable::getInstance('Orders_items', 'Table');
	       	
	       	$data = [
	       		'order_item_id' => $order_item_id,
	       		'sub_payment_data' => json_encode($res)
	       	];
	       	
	       	$order_item->bind($data);
	       	
	       	$order_item->store();
	       	
	       	JFactory::getApplication()->enqueueMessage(JText::_('PAGO_SUBSCR_UPDATED'), 'message');
		}
	}
	
	function subscr_cancel(){
		
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', 0, 'int');
		$sid = $jinput->get('id', 0, 'string');
		$order_item_id = $jinput->get('order_item_id', 0, 'int');
		$customer = $jinput->get('customer', 0, 'string');
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$payload = (object)[
			'id' => $sid,
			'livemode' => $livemode,
			'customer' => $customer,
			'account' => $account
		];
		
		$api = new PagoControllerApi;
		$res = $api->call('DELETE', 'subscr', $payload, false);
		
		if(@$res->id){
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');
	    	$order_item = JTable::getInstance('Orders_items', 'Table');
	       	
	       	$data = [
	       		'order_item_id' => $order_item_id,
	       		'sub_payment_data' => json_encode($res)
	       	];
	       	
	       	$order_item->bind($data);
	       	
	       	$order_item->store();
	       	
	       	JFactory::getApplication()->enqueueMessage(JText::_('PAGO_SUBSCR_CANCELED'), 'notice');
		}
		
 		$this->setRedirect( 'index.php?option=com_pago&controller=ordersi&task=edit&view=ordersi&cid[]='.$cid.'#tabs-3');
	}
	
	function subscr_reinstate(){
		
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', 0, 'int');
		$sid = $jinput->get('id', 0, 'string');
		$order_item_id = $jinput->get('order_item_id', 0, 'int');
		$customer = $jinput->get('customer', 0, 'string');
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$payload = (object)[
			'id' => $sid,
			'livemode' => $livemode,
			'customer' => $customer,
			'account' => $account
		];
		
		$api = new PagoControllerApi;
		$res = $api->call('POST', 'subscr', $payload, false);
		
		if(@$res->id){
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');
	    	$order_item = JTable::getInstance('Orders_items', 'Table');
	       	
	       	$data = [
	       		'order_item_id' => $order_item_id,
	       		'sub_payment_data' => json_encode($res)
	       	];
	       	
	       	$order_item->bind($data);
	       	
	       	$order_item->store();
	       	
	       	JFactory::getApplication()->enqueueMessage(JText::_('PAGO_SUBSCR_REINSTATED'), 'notice');
		}
		
 		$this->setRedirect( 'index.php?option=com_pago&controller=ordersi&task=edit&view=ordersi&cid[]='.$cid.'#tabs-3');
	}
	
	function charge_refund(){
		
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', 0, 'int');
		$id = $jinput->get('id', 0, 'string');
		$order_log_id = $jinput->get('order_log_id', 0, 'int');
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		$amount = $jinput->get('amount', 0, 'string');
		$refund = $jinput->get('refund', 0, 'string');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$payload = (object)[
			'livemode' => $livemode,
			'id' => $id,
			'account' => $account,
			'amount' => $refund
		];
		
		$api = new PagoControllerApi;
		$res = $api->call('DELETE', 'checkout', $payload, false);
		
		if(@$res->id || strstr(@$res->detail, 'already been refunded')){
			
			$refund = Pago::get_instance( 'price' )->format($refund);
			$amount = Pago::get_instance( 'price' )->format($amount);
			
			$db = JFactory::getDbo();

			$db->setQuery("UPDATE #__pago_orders_log 
							SET order_status = 'Refunded', 
								description = 'Actioned Partial Refund {$refund} from {$amount}'
								WHERE order_log_id = {$order_log_id}");
			$db->query();
	       	
	       	JFactory::getApplication()->enqueueMessage(JText::_('PAGO_CHARGE_REFUNDED'), 'notice');
		} elseif(@$res->detail){
			JFactory::getApplication()->enqueueMessage(JText::_('PAGO_REFUND_WARNING') . @$res->detail, 'warning');
		}
		
 		$this->setRedirect( 'index.php?option=com_pago&controller=ordersi&task=edit&view=ordersi&cid[]='.$cid.'#tabs-5');
	}
	
	function update_quantity()
	{
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', 0, 'int');
		//$order_item_id = $jinput->get('order_item_id', 0, 'int');
		$item_id = $jinput->get('item_id', 0, 'string');
		$updatedQty = $jinput->get('updatedQty', 0, 'int');
		
		$order = Pago::get_instance('orders')->get($order_id);
		
		foreach($order['items'] as $item){
			if($item->price_type == 'subscription' && $item->id == $item_id && $item->qty > 1)
				$this->subscr_patch($item->order_item_id, $updatedQty, json_decode($item->sub_payment_data));
		}
		
		$model = $this->getModel ('ordersi');
		$model->update_quantity($order_id, $item_id, $updatedQty);
		$this->setRedirect( 'index.php?option=com_pago&controller=ordersi&task=edit&view=ordersi&cid[]=' . $order_id . '#tabs-3');
	}
	
	public function print_orders()
	{
		$jinput = JFactory::getApplication()->input;
		$cid 		= $jinput->get('cid', array(), 'array');
		$cidStr = implode(",",$cid);
		$cidStrEncode = urlencode($cidStr);
		
		$this->setRedirect('index.php?option=com_pago&tmpl=component&view=ordersi&layout=print_orders&cidStr='. $cidStrEncode);
	}
}
