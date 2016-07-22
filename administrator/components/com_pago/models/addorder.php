<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelAddorder extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getUsers()
	{
		$db		= $this->getDbo();
	/*	$sql = "SELECT 	userinfo.*, userinfo.user_id as `value` , userinfo.first_name as `text`
						FROM #__pago_user_info as userinfo
						group by userinfo.user_id
						ORDER BY userinfo.id DESC
			"; */

		$sql ="SELECT u.*, u.id as `value` , u.name as `text` FROM #__users as u";
		$db->setQuery( $sql );
		return $db->loadAssocList();
	}


	public function getItems()
	{
		$db = Jfactory::getDBO();
		$query = "SELECT * FROM #__pago_items WHERE published = 1";
		$db->setQuery($query);
		$items = $db->LoadObjectList();

		return $items;
	}


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Orders', $prefix = 'PagoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function store($data)
	{
		$data['cdate'] = date('Y-m-d H:i:s', time());
		$data['mdate'] = date('Y-m-d H:i:s', time());
		$data['ushma'] = date('Y-m-d H:i:s', time());

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');
		$row = JTable::getInstance('orders', 'Table');

		if (!$row->bind($data))
		{
			return JError::raiseWarning(500, $row->getError());
		}

		if (!$row->check())
		{
			return JError::raiseWarning(500, $row->getError());
		}

		if (!$row->store())
		{
			return JError::raiseWarning(500, $row->getError());
		}

		$db = $row->getDbo();
		$order_id = $db->insertid();

		return $order_id;
	}

	public function storeOrderItems($order_id, $order_item)
	{
		foreach ($order_item as $k => $item)
		{
			$order_item_ship_method_id = trim($item->ship_method_id);
			$shippingData = explode("|", $order_item_ship_method_id);
			$order_item_shipping = $shippingData[3];

			$rowitem = JTable::getInstance('orders_items', 'Table');
			$data = array();
			if($item->item_id > 0)
			{
				$data['order_id'] = $order_id;
				$data['item_id'] = $item->item_id;
				$data['varation_id'] = $item->varation_id;
				$data['qty'] = $item->quantity;
				$data['price'] = $item->item_price_without_tax;
				$data['price_type'] = '';
				$data['attributes'] = '';
				$data['sub_recur'] = '';
				$data['order_item_tax'] = $item->item_tax;
				$data['order_item_shipping_tax'] = $item->item_shipping_tax;
				$data['order_item_shipping'] = $order_item_shipping;
				$data['order_item_ship_method_id'] = $shippingData[0] . " - " . $shippingData[2];

				if (!$rowitem->bind($data))
				{
					return JError::raiseWarning(500, $rowitem->getError());
				}

				if (!$rowitem->check())
				{
					return JError::raiseWarning(500, $rowitem->getError());
				}

				if (!$rowitem->store())
				{
					return JError::raiseWarning(500, $rowitem->getError());
				}
			}
		}
	}

	public function storeOrderAdresses($order_id, $user_info)
	{
		// Store Order addresses
		$rowBillingAddress = JTable::getInstance('orders_addresses', 'Table');
		$billingData = $user_info['billing'];
		unset($billingData['id']);
		$billingData['order_id'] = $order_id;

		if (!$rowBillingAddress->bind($billingData))
		{
			return JError::raiseWarning(500, $rowBillingAddress->getError());
		}

		if (!$rowBillingAddress->check())
		{
			return JError::raiseWarning(500, $rowBillingAddress->getError());
		}

		if (!$rowBillingAddress->store())
		{
			return JError::raiseWarning(500, $rowBillingAddress->getError());
		}

		$rowShippingAddress = JTable::getInstance('orders_addresses', 'Table');
		$shippingData = $user_info['shipping'];
		unset($shippingData['id']);
		$shippingData['order_id'] = $order_id;

		if (!$rowShippingAddress->bind($shippingData))
		{
			return JError::raiseWarning(500, $rowShippingAddress->getError());
		}

		if (!$rowShippingAddress->check())
		{
			return JError::raiseWarning(500, $rowShippingAddress->getError());
		}

		if (!$rowShippingAddress->store())
		{
			return JError::raiseWarning(500, $rowShippingAddress->getError());
		}
	}

	public function DoOffilineOrderPayment($order)
	{

		$payment_gateway = $order['details']->payment_gateway;
		$paymentResult = array();
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin('pago_gateway');
		$dispatcher->trigger(
			'onPayment',
			array( &$paymentResult, &$payment_gateway, &$order )
		);

		foreach ( $paymentResult as $pgateway => $result)
		{
			if ($pgateway == $payment_gateway)
			{
				if ( $result->order_status == "P" )
				{
					JError::raiseWarning(500, $result->message);

					return;
				}

				Pago::get_instance('orders')->onOrderComplete($order, $result);
				$successMessage = $result->message;
			}
		}

		return $successMessage = $result->message;;
	}

}