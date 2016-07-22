<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of orders.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoModelOrders extends JModelList
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
	function checkOrderOwner($orderId){
		$db = $this->_db;

		$user = JFactory::getUser();
 	    $return = array();

		if ($user->guest) {
			return false;
		}else{
			$query = 'SELECT count(order_id)
				FROM  #__pago_orders
			 	WHERE user_id = '.$user->id.' AND order_id = ' . (int) $orderId;
			 	$db->setQuery( $query );
			 	$userIsOwner = $db->loadResult();
			 	if(!$userIsOwner){
			 		return false;
			 	}
		}
		return true;
	}

	function getOrder($checkUser = false)
	{
		$db = $this->_db;

		$order_id = $this->getState('order_id');

		// Check to see if it is set
		if(! $order_id) {
			$order_id = JFactory::getApplication()->input->getInt( 'order_id' );
		}

		if($checkUser){
			if(!$this->checkOrderOwner($order_id)){
				return false;
			}
		}
		JTable::addIncludePath( JPATH_COMPONENT . DS . 'tables' );

		$table = JTable::getInstance( 'Orders', 'Table');

		$table->load( $order_id );

		$order['details'] = $table;

		$query = 'SELECT items.*, items.qty as stock, order_items.* ' .
			'FROM  #__pago_orders_items as order_items ' .
			'LEFT JOIN #__pago_items as items ON order_items.item_id = items.id ' .
			'WHERE order_items.order_id = ' . (int) $order_id;

		$db->setQuery( $query );
		$data = $db->loadObjectList();

		if ( is_array( $data ) ) {
			foreach( $data as $k => $item ) {
				$item->total =
					Pago::get_instance( 'price' )->format( $item->price * $item->qty );
				$data[$k] = $item;
			}
		}

		$order['items'] = $data;

		// Add Payment Data needed for capture
		$query = 'SELECT order_payment.*,  orders.order_id ' .
			'FROM  #__pago_orders  as orders ' .
			'LEFT JOIN #__pago_orders_sub_payments as order_payment ON orders.order_id = order_payment.order_id ' .
			'WHERE order_payment.order_id = ' . (int) $order_id; 


		$db->setQuery($query);
		$data = $db->loadObjectList();
		$order['payment'] = $data;

		$order['shipments'] = array(
			array(
				'id' => (int)1,
				// Shipping method.
				'carrier' => @$order['details']->ship_method_id,
				// Shipping method.
				'method' => @$order['details']->ship_method_id,
				'shipping_total' => @$order['details']->order_shipping,
				'shipping_tax' => @$order['details']->order_shipping_tax,
			)
		);

		$query = 'SELECT * FROM  #__pago_orders_addresses WHERE order_id = ' . (int) $order_id;

		$db->setQuery( $query );
		$data = $db->loadObjectList();

		$order['addresses']['billing'] = false;
		$order['addresses']['shipping'] = false;
		foreach ( $data as $addy ) {
			switch( $addy->address_type ) {
				case 'b':
					$order['addresses']['billing'] = $addy;
					break;
				case 's':
					$order['addresses']['shipping'] = $addy;
					break;
			}
		}

		if ( (int) $order['details']->user_id ) {
			$sql = 'SELECT groups.group_id ' .
				'FROM #__pago_groups_users as groups_users '.
				'LEFT JOIN #__pago_groups as groups ON groups_users.group_id = groups.group_id '.
				'WHERE groups_users.user_id = ' . (int) $order['details']->user_id;

			$db->setQuery( $sql );
			$order['details']->groups = $db->loadResultArray();
		} else {
			$order['details']->groups = array();
		}

		return $order;
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
				'DISTINCT o.*'
			)
		);

		$query->from('`#__pago_orders` AS o');

		// Join over the user
		$query->select('u.first_name, u.last_name');

		$query->join('LEFT', '`#__pago_user_info` AS u ON o.user_id = u.user_id');

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('o.order_id = '.(int) $search);
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(u.first_name LIKE '.$search.'
									OR u.last_name
										LIKE '.$search.')
								OR (o.order_id = '.$db->quote( $this->getState('filter.search') ).')');

			}
		}

		$order_status = $this->getState('filter.order_status');
		if (!empty($order_status)) {
				$order_status = $db->Quote($db->escape($order_status, true));
				$query->where('o.order_status = ' . $order_status );
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

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
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.category_id');
		$id .= ':'.$this->getState('filter.language');

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
	public function getTable($type = 'Orders', $prefix = 'Table', $config = array())
	{
		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/tables' );
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
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$clientId = $this->getUserStateFromRequest($this->context.'.filter.order_status', 'filter_order_status', '');
		$this->setState('filter.order_status', $clientId);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('order_id', 'desc');
	}

	function store($orderData)
	{
		$user_info = $this->getState('user_info');

		$row = $this->getTable();

		$data = $orderData; //JFactory::getApplication()->input->getArray($_POST);

		$id = 0;

		if( isset($data['order_id']) ) $id = $data['order_id'];

		if( $id == 0 || !$id ){
			$data['cdate'] = date( 'Y-m-d H:i:s', time() );
			$data['mdate'] = date( 'Y-m-d H:i:s', time() );
		} else {
			$data['mdate'] = date( 'Y-m-d H:i:s', time() );
		}

		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->check()) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );

		if( !$id ){
			$db = $row->getDbo();
			$order_id = $db->insertid();
		} else {
			$order_id = $id;
		}

		// Store Order address
		if( $user_info ){

			$b = (array)$user_info['billing'];
			$s = (array)$user_info['shipping'];

			unset( $b['id'] );
			unset( $s['id'] );
			$b['order_id'] = $order_id;
			$s['order_id'] = $order_id;
			$b['cdate'] = $data['cdate'];
			$b['mdate'] = $data['mdate'];
			$s['cdate'] = $data['cdate'];
			$s['mdate'] = $data['mdate'];

			// make sure if no user_id we just set to 0
			if ( !isset( $b['user_id'] ) ) {
				$b['user_id'] = 0;
			}

			if ( !isset( $s['user_id'] ) ) {
				$s['user_id'] = 0;
			}

			// Store billing address
			$BaddressId = Pago::get_instance( 'orders' )->storeOrderAdress('b', $b );
			// Store Shipping address
			$SaddressId = Pago::get_instance( 'orders' )->storeOrderAdress('s', $s );
		}

		return $order_id;
	}
}
