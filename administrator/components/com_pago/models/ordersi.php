<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelOrdersi extends JModelList
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

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'order_id', 'o.order_id',
				'cdate', 'o.cdate',
				'payment_gateway', 'o.payment_gateway',
				'order_status', 'o.status',
				'order_total', 'o.order_total',
				'first_name', 'u.first_name',
				'middle_name', 'u.middle_name',
				'last_name', 'u.last_name',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'o.*'
			)
		);

		$query->from('`#__pago_orders` AS o');

		// Join over the user
		$query->select('u.first_name, u.last_name');

		$query->join('LEFT', '`#__pago_orders_addresses` AS u ON o.order_id = u.order_id');

		$query->where('u.address_type = "b"');

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('o.order_id = '.(int) $search);
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(u.first_name LIKE '.$search.'
							OR u.last_name
								LIKE '.$search.' OR u.user_email
								= '.$db->quote( $this->getState('filter.search') ).')
						OR (o.order_id = '.$db->quote( $this->getState('filter.search') ).')');

			}
		}

		$postArray = JFactory::getApplication()->input->getArray($_POST);
		
		if(isset($postArray['filter_order_status']))
		{
			$order_status = $postArray['filter_order_status'];
		}
		
		if (!empty($order_status)) {
				$order_status = $db->Quote($db->escape($order_status, true));
				$query->where('o.order_status = ' . $order_status );
		}
		
		//don't show archived orders
		$order_status = $db->Quote($db->escape('A', true));
		$query->where('o.order_status <> ' . $order_status );
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->group('o.order_id');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		/*SELECT o.*,u.first_name, u.last_name FROM `jos_pago_orders` AS o LEFT JOIN `jos_pago_orders_addresses` AS u ON o.order_id = u.order_id WHERE u.address_type = "b" ORDER BY order_id desc*/
		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		/*$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.type');
		$id	.= ':'.$this->getState('filter.price_type');
		$id .= ':'.$this->getState('filter.primary_category');*/

		return parent::getStoreId($id);
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

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$filter = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $filter);

		$filter = $this->getUserStateFromRequest($this->context.'.filter.publish', 'filter_publish');
		$this->setState('filter.publish', $filter);

		$filter = $this->getUserStateFromRequest($this->context.'.filter.country_id', 'filter_country_id');
		$this->setState('filter.country_id', $filter);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('order_id', 'desc');
	}

	public function getAllitems()
	{
		$db = Jfactory::getDBO();
		$query = "SELECT * FROM #__pago_items WHERE published = 1";
		$db->setQuery($query);
		$items = $db->LoadObjectList();

		return $items;
	}

	public function getOrderLogs()
	{
		$cid = JFactory::getApplication()->input->get('cid',  array(0), 'array');

		$db = Jfactory::getDBO();
		$query = "SELECT * FROM #__pago_orders_log WHERE orderid=" . $cid[0] . " ORDER BY order_log_id ASC";
		$db->setQuery($query);
		$orderLogs = $db->LoadObjectList();
		return $orderLogs;
	}

	public function orderExport( $startdate='', $enddate='' )
	{
		if ($startdate!="" && $enddate!="")
		{
		
			if($orderstatus!="")
			{
				$select_status = " AND order_status = '".$orderstatus."' ";
			}
			
			if($ordercurrency!="")
			{
				$select_currency = " AND order_currency = '".$ordercurrency."' ";
			}
		
			$db = Jfactory::getDBO();
			$query = "SELECT order_id FROM #__pago_orders WHERE (cdate between '".$startdate."' and '".$enddate."') ".$select_status." ".$select_currency." ORDER BY order_id ASC";
			$db->setQuery($query);
			$orders = $db->LoadObjectList();

			JPluginHelper::importPlugin( 'pago_export' );
			$dispatcher = JEventDispatcher::getInstance();
			$results = $dispatcher->trigger( 'on_order_export', array ('com_pago.ordersi', $orders));
 		} 
	}
	
	public function orderExportCSV($startdate='', $enddate='', $prdIdArray=array())
	{
	
		$productStr='';
		
		
		
		if ($startdate!="" && $enddate!="")
		{
			$and = '';
			
			if(count($prdIdArray) > 0)
			{
				$productStr = implode("','", $prdIdArray);
			}
		
			if($productStr!="")
			{
				$and = " AND oi.item_id IN('". $productStr ."') ";
			}
			
			if($ordercurrency!="")
			{
				$select_currency = " AND order_currency = '".$ordercurrency."' ";
			}
		
			$db = Jfactory::getDBO();
			$query = "SELECT DISTINCT(o.order_id) FROM #__pago_orders AS o LEFT JOIN #__pago_orders_items AS oi ON o.order_id=oi.order_id WHERE o.cdate >= '".$startdate." 00:00:00' and o.cdate <= '".$enddate." 24:60:60'  " . $and . " ORDER BY order_id ASC";
			$db->setQuery($query);
			$orders = $db->LoadObjectList();

			JPluginHelper::importPlugin( 'pago_export' );
			$dispatcher = JEventDispatcher::getInstance();
			$results = $dispatcher->trigger( 'on_order_export_csv', array ('com_pago.ordersi', $orders));
 		} 
	}
	
	public function update_quantity($order_id, $item_id, $updatedQty)
	{
		$db = Jfactory::getDBO();
		$order     = Pago::get_instance('orders')->get($order_id);
		$order_item_total = 0;
		$orderItems = $order['items'];
		
		if(count($orderItems) > 0)
		{
			for($s = 0; $s < count($orderItems); $s++)
			{
				if($orderItems[$s]->id != $item_id)
				{
					$item_price = ($orderItems[$s]->price + $orderItems[$s]->order_item_tax) * $orderItems[$s]->qty;
					$order_item_total += $item_price;
				}
				else
				{
					$price = $orderItems[$s]->price + $orderItems[$s]->order_item_tax;
					$previousQty = $orderItems[$s]->qty;
				}
				
			}
		}
		

		$order_subtotal = $order_item_total + ($updatedQty * $price);
		$order_total = ($order['details']->order_shipping + $order_subtotal) - $order['details']->order_discount;
		
		$query = "UPDATE #__pago_orders SET order_total='" . $order_total . "',order_subtotal='" . $order_subtotal . "' WHERE order_id = " . $order_id;
		$db->setQuery($query);
		$db->Query();
		
		$query = "UPDATE #__pago_orders_items SET qty='" . $updatedQty . "' WHERE order_id = '" . $order_id  . "' and item_id= " . $item_id;
		$db->setQuery($query);
		$db->Query();
		
		$order_status     = Pago::get_instance('orders')->get_order_status($order['details']->order_status);
		
		$itemNmQuery = "SELECT name FROM #__pago_items WHERE id=" . $item_id;
		$db->setQuery($itemNmQuery);
		$itemNm = $db->loadResult();
			
		if(!$updatedQty){
			$query = "DELETE FROM #__pago_orders_items WHERE order_id = '" . $order_id  . "' and item_id= " . $item_id;
			$db->setQuery($query);
			$db->Query();
			
			$description = JTEXT::_("COM_PAGO_ORDER_ITEM_REMOVED");
			$query = "INSERT  #__pago_orders_log (orderid, date, action, description, order_status) VALUES ('" . $order_id  . "', '" . $order['details']->cdate  . "', 'item_deleted', '" . $description  . "', '" . $order_status  . "')";
			$db->setQuery($query);
			$db->Query();
		} else {
			$description = JTEXT::_("COM_PAGO_QUANTITY_UPDATED_FROM") . $previousQty . JTEXT::_("COM_PAGO_TO") . $updatedQty . JTEXT::_("COM_PAGO_FOR") . $itemNm;
			$query = "INSERT  #__pago_orders_log (orderid, date, action, description, order_status) VALUES ('" . $order_id  . "', '" . $order['details']->cdate  . "', 'order_qty_updated', '" . $description  . "', '" . $order_status  . "')";
			$db->setQuery($query);
			$db->Query();
		}
	}

}
