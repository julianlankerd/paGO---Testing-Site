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
 * Methods supporting a list of customers.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoModelCustomers extends JModelList
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
				'id', 'item.id',
				'username', 'u.username',
				'name', 'u.name',
				'email', 'u.email',
                'user_id', 'item.user_id',
                'last_name', 'item.last_name',
                'first_name', 'item.first_name',
                'phone_1', 'item.phone_1',
                'city', 'item.city',
                'state', 'item.state',
                'country', 'item.country',
                'zip', 'item.zip',
                'user_email', 'item.user_email',
				'cdate', 'item.cdate'
			);
		}

		parent::__construct($config);
	}

	public function getUsers()
	{
		$db		= $this->getDbo();
		$sql = "SELECT u.*, u.id as `value` , u.name as `text` FROM #__users as u WHERE u.id NOT IN(SELECT user_id FROM #__pago_user_info)";
		$db->setQuery( $sql );

		return $db->loadAssocList();
	}

	public function getJUsers($p)
	{
		$db		= $this->getDbo();

		$start = $this->state->get( 'list.start' );
		$limit = $this->state->get( 'list.limit' );
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$sql = "SELECT * FROM #__users";
		$db->setQuery( $sql );
		$count = count($db->loadAssocList());

		$sql = "SELECT * FROM #__users ORDER by ".$orderCol." ".$orderDirn." LIMIT ".$start.", ".$limit;
		$db->setQuery( $sql );

		$currentPage = ceil($start/$limit);
		$currentPage = $currentPage == 0 ? 1 : $currentPage+1;

		$totalPages = ceil($count/(int)$limit);

		$p->set( 'total', $count );
		
		$p->set('limitstart', $start);

		$version = new JVersion();

		if($version->RELEASE >= 3){
			$p->set('pagesTotal', $totalPages);
			$p->set('pagesStop', $totalPages);
			$p->set('pagesCurrent', $currentPage);
		}
		else{
			$p->set('pages.total', $totalPages);
			$p->set('pages.stop', $totalPages);
			$p->set('pages.current', $currentPage);
		}

		return $db->loadAssocList();
	}

	public function getCities(){

		$country = $this->getState( 'filter.country' );
		$state = $this->getState( 'filter.state' );

		$sql = "
		SELECT DISTINCT city
			FROM #__pago_user_info
				WHERE country = '{$country}' AND state = '{$state}'
		";
		$this->_db->setQuery($sql);

		$values = $this->_db->loadAssocList( 'city' );

		$return = array();

		foreach($values as $k=>$value){
			$return[ $k ] = $k;
		}

		return $return;
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
				'item.*'
			)
		);

		$query->from( '`#__pago_user_info` AS item' );
		// Join over user
		$query->select( 'u.username, u.name' );

		$query->join(
			'LEFT',
			'`#__users` AS u ON item.user_id = u.id'
		);
		$query->where( "item.address_type = 's'" );
		$query->group( 'item.user_id' );

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				//$query->where('o.order_id = '.(int) $search);
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(item.first_name LIKE '.$search.'
									OR item.last_name LIKE '.$search.')
				');

			}
		}

		$filter_var = $this->getState('filter.country');
		if (!empty($filter_var)) {
				$filter_var = $db->Quote($filter_var);
				$query->where('item.country = ' . $filter_var );
		}

		$filter_var = $this->getState('filter.state');
		if (!empty($filter_var)) {
				$filter_var = $db->Quote($filter_var);
				$query->where('item.state = ' . $filter_var );
		}

		$filter_var = $this->getState('filter.city');
		if (!empty($filter_var)) {
				$filter_var = $db->Quote($filter_var);
				$query->where('item.city = ' . $filter_var );
		}


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

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
		$id	.= ':'.$this->getState('filter.country');

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
	public function getTable($type = 'Items', $prefix = 'PagoTable', $config = array())
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
		$this->setup_state( 'search' );
		$this->setup_state( 'country' );
		$this->setup_state( 'state' );
		$this->setup_state( 'city' );

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('id', 'desc');
	}

	function setup_state( $filter_name ){

		$$filter_name = $this->getUserStateFromRequest($this->context.'.filter.' . $filter_name, 'filter_' . $filter_name );
		$this->setState('filter.' . $filter_name, $$filter_name);
	}



	function _buildQuery()
    {
        $query = ' SELECT * '
            . ' FROM #__pago_user_info '
        ;
        return $query;
    }
 /*
    function get_order()
    {
		$db =& JFactory::getDBO();

		if ( $this->getState('order_id') ) {
			$where[] = 'order_id = ' . $this->getState('order_id');
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$sql = "SELECT * FROM  #__pago_user_info $where";

		$db->setQuery( $sql );

		return $db->loadObject();
	}*/

    function getData()
    {
		$db =& JFactory::getDBO();

		$this->_buildContentOrderBy();

		if( $this->_items ) return $this->_items;

		$where = array();
		//address_type
		$where[] = "info.address_type = 'b'";

		if ( $this->getState('filter_search') ) {
			$where[] = 'info.first_name LIKE '.$db->Quote( '%'.$db->escape( $this->getState('filter_search'), true ).'%', false );
		}

		//$where[] = "user_info.address_type =  'b'";

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		//$cid = $this->getState('cid');

		$sql = "SELECT SQL_CALC_FOUND_ROWS
					info.*
				FROM #__pago_user_info as info
				$where $this->_order";


		$this->_items = $this->_getList( $sql, $this->getState('limitstart'), $this->getState('limit') );

		$db->setQuery('SELECT FOUND_ROWS();');
		$this->_total = $db->loadResult();

		return $this->_items;
    }

	/*function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}*/

	function _buildContentOrderBy()
	{
		$mainframe = JFactory::getApplication();
		$option = JFactory::getApplication()->input->get( 'option' );

		$filter_order     = $this->getState('filter_order');
		$filter_order_Dir = $this->getState('filter_order_Dir');

		/* Error handling is never a bad thing*/
		if(!empty($filter_order) && !empty($filter_order_Dir) ){
				$this->_order = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}

		return $this->_order;
	}

	function publish()
    {
       if( is_array( $this->getState('cid') ) )
	   foreach( $this->getState('cid') as $id ){
		   $this->_getList("
		   		UPDATE #__pago_orders
					SET published = 1
						WHERE id = $id
		   ");
	   }

       return $this->_data;
    }

	function unpublish()
    {
       if( is_array( $this->getState('cid') ) )
	   foreach( $this->getState('cid') as $id ){
		   $this->_getList("
		   		UPDATE #__pago_orders
					SET published = 0
						WHERE id = $id
		   ");
	   }

       return $this->_data;
    }

	function remove()
    {

	   if( is_array( $this->getState('cid') ) )
	   foreach( $this->getState('cid') as $id ){
		   $this->_getList("
		   		DELETE FROM #__pago_user_info
					WHERE user_id = $id
		   ");
	   }

       return true;
    }

	function store()
	{
		$row = $this->getTable();

		$data = JFactory::getApplication()->input->getArray($_POST);
		//$data = $data['params'];

		$id = 0;

		if( isset($data['order_id']) ) $id = $data['order_id'];

		if($id == 0){
			$data['cdate'] = time();
			$data['mdate'] = time();
		} else {
			$data['mdate'] = time();
		}

		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->check()) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );

		return $row->_db->insertid();
	}

	// Frontend stuff
	public function get_user()
	{
		$db   = JFactory::getDBO();
		$user =  JFactory::getUser();

		$sql = "SELECT ku.*, u.*, ku.id AS addr_id
					FROM #__users AS u 
					LEFT JOIN #__pago_user_info AS ku ON u.id = ku.user_id
					
						WHERE u.id = $user->id";

		$db->setQuery( $sql );
		return $db->loadObject();
	}

	public function get_user_addresses()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT *
					FROM #__pago_user_info
						WHERE user_id = $user->id";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_user_billing_addresses()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT *
					FROM #__pago_user_info
						WHERE address_type = 'b' and user_id = $user->id";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}
	public function get_user_shipping_addresses()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT *
					FROM #__pago_user_info
						WHERE address_type = 's' and user_id = $user->id";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_recent_orders_status($days = 15, $startdate = null, $enddate = null)
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();
		$ordId = JFactory::getApplication()->input->get('status_search'); 
		$appendOrd = '';
		
		if($ordId)
		{
			$appendOrd = " AND order_id='" . $ordId . "'";
		}


		if(!$startdate || !$enddate){
			$sql = "SELECT *
						FROM #__pago_orders
							WHERE user_id = {$user->id}
								AND order_status <> 'A' 
								AND TIMESTAMPDIFF( DAY , cdate, CURRENT_TIMESTAMP ) < $days" . $appendOrd;
		} else {
			$startdate = explode('-', $startdate);
			$enddate = explode('-',$enddate);

			$sql = "SELECT *
						FROM #__pago_orders
							WHERE user_id = {$user->id}
								AND order_status <> 'A' 
								AND cdate >= '$startdate[0]-$startdate[1]-$startdate[2] 00:00:01'
								AND cdate <= '$enddate[0]-$enddate[1]-$enddate[2] 23:59:59'" . $appendOrd;
		}

		$sql .= " ORDER BY cdate DESC
					LIMIT 5";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_recently_purchased_products()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		 $sql = "SELECT oi.*, i.*

					FROM #__pago_orders	AS o

					LEFT JOIN #__pago_orders_items AS oi ON oi.order_id = o.order_id

					LEFT JOIN #__pago_items AS i ON i.id = oi.item_id

						WHERE o.user_id = $user->id AND i.id!=''

							ORDER BY o.cdate DESC

								LIMIT 5";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function update_account()
	{
		jimport('joomla.filesystem.file');

		$post = JFactory::getApplication()->input->getArray($_POST);
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();
		
		//let's put some security here, Batman...
		$post['user_id'] = $user->id;
		
		if(!$post['name'])
		{
			$this->setError( JText::_( 'NAME_IS_REQUIRED' ) );
			return false;
		}

		if($post['user_email'] != $post['new_email'] && ($post['user_email'] || $post['new_email']) )
		{
			$this->setError( JText::_( 'Email and confirm email cant be different' ) );
			return false;
		}
		
		if($post['user_email']){	
			if (!filter_var($post['user_email'], FILTER_VALIDATE_EMAIL)) 
			{
	    		$this->setError( JText::_( 'Email is not valid' ) );
				return false;
			}
		}

		if($post['user_email']){
			$query = 'SELECT username FROM #__users WHERE email = "'.$post['user_email'].'" AND id <> '.$post['user_id'];
			$db->setQuery($query);
			$res = $db->loadResult();
			if(!is_null($res)){
				$this->setError( JText::_( 'Email is already exist' ) );
				return false;
			}
		}
		
		$sql = "UPDATE #__users SET name = '" .$post['name']. "' WHERE id='" .$post['user_id']. "'";
		$db->setQuery($sql);
		$result = $db->query();

		$sql = "UPDATE #__pago_user_info SET first_name = '" .$post['name']. "' WHERE id='" .$post['user_id']. "'";
		$db->setQuery($sql);
		$result = $db->query();
		
		if($post['user_email']){
			$sql = "UPDATE #__users SET email = '" .$post['new_email']. "' WHERE id='" .$post['user_id']. "'";
			$db->setQuery($sql);
			$result = $db->query();

			$sql = "UPDATE #__pago_user_info SET user_email = '" .$post['new_email']. "' WHERE user_id = '" .$post['user_id']. "'";
			$db->setQuery( $sql );
			$result = $db->query();
		}
		

		//$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'users';

		// if ($_FILES['avatar']['tmp_name'] && file($_FILES['avatar']['tmp_name']))
		// {
		// 	if(!JFolder::exists($path)){
		// 		mkdir($path, 0755, true);
		// 	}
		// 	$allowTypes = array('jpg','png');
		// 	$file_extn = explode(".", strtolower($_FILES['avatar']['name']));
		// 	$file_size = filesize ( $_FILES['avatar']['tmp_name'] );
			
		// 	if (in_array($file_extn[1], $allowTypes)) {
		// 		if($file_size <=  5000000){
		// 			move_uploaded_file($_FILES['avatar']['tmp_name'],$path.DIRECTORY_SEPARATOR.$post['user_id'].'.'.$file_extn[1]);
		// 		}else{
		// 			return 'big_file';
		// 		}
		// 	}else{
		// 		return 'wrong_ex';
		// 	}
		// }
		return $result;
	}

	public function update_primary_address()
	{
		$post = JFactory::getApplication()->input->getArray($_POST);
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		if(!$post) return;

		$sql = "SELECT id
					FROM #__pago_user_info
					WHERE user_id = '" .$user->get('id'). "'
						AND address_type = 'p'";
		$db->setQuery( $sql );
		$id = $db->loadResult();

		$sql = "UPDATE #__pago_user_info
					SET first_name = '" .$post['first_name']. "',
						middle_name = '" .$post['middle_name']. "',
						last_name = '" .$post['last_name']. "',
						company = '" .$post['company']. "',
						address_1 = '" .$post['address_1']. "',
						address_2 = '" .$post['address_2']. "',
						city = '" .$post['city']. "',
						state = '" .$post['state']. "',
						zip = '" .$post['zip']. "',
						phone_1 = '" .$post['phone_1']. "',
						phone_2 = '" .$post['phone_2']. "'
						WHERE id = $id";
		$db->setQuery( $sql );
		$result = $db->query();

		return $result;
	}

	public function add_address( $user_data = null )
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();
		$insert = '';
		
		if ( $user_data === null ) {
			$post = JFactory::getApplication()->input->getArray($_POST);

			if(!$post) return;

			if( $post['address_type'] == 'p' ) {
				// We have a new primary address
				$sql = "SELECT id
							FROM #__pago_user_info
							WHERE user_id = '" .$post['user_id']. "'
								AND address_type = 'p'";
				$db->setQuery( $sql );
				$id = $db->loadResult();

				// If there is no id - lets skip this query
				if( $id ) {
					// Lets update the old primary address to just billing
					$sql = "UPDATE #__pago_user_info
								SET address_type_name = '',
									address_type = ''
									WHERE id = " . (int) $id;
					$db->setQuery( $sql );
					$update = $db->query();
				}

				unset( $post['address_type_name'] );
				$post['address_type_name'] = 'Primary';
			}

			$insert = "(
							" .$post['user_id']. ",
							'" .$post['address_type']. "',
							'" .$post['address_type_name']. "',
							'" .$post['company']. "',
							'" .$post['last_name']. "',
							'" .$post['first_name']. "',
							'" .$post['middle_name']. "',
							'" .$post['phone_1']. "',
							'" .$post['phone_2']. "',
							'" .$post['address_1']. "',
							'" .$post['address_2']. "',
							'" .$post['city']. "',
							'" .$post['state']. "',
							'" .$post['zip']. "',
							'" .$post['country']. "',
							'" .$post['user_email']. "'
						)";

						$sql = "INSERT INTO #__pago_user_info
							( user_id,address_type,address_type_name,company,last_name,first_name,
								middle_name,phone_1,phone_2,address_1,address_2,city,state,zip,country,user_email) VALUES ";
						$sql .= $insert;

						$db->setQuery( $sql );

						if (!$result = $db->query()){
							echo $db->stderr();
							return false;
						}
		} else {
			$user_id = $user->get( 'id' );
			foreach ( $user_data as $data ) {
				$data->user_id = $user_id;
				$address = (array) $data;
				$addressId = Pago::get_instance( 'users' )->saveUserAddress($data->address_type, $address );
				if(!$addressId)
				{
					return false;
				}

			}

		}
		

		return true;
	}

	public function update_address()
	{
		$post = JFactory::getApplication()->input->getArray($_POST);
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		if(!$post) return;

		foreach ( $post['address'] as $k => $data ) {
			$address = $data;
			$type = $k;
			break;
		}

		if( !isset( $address['id'] ) )
			return;

		if( $type == 'p' ) {
			// We have a new primary address
			$sql = "SELECT id
						FROM #__pago_user_info
						WHERE user_id = '" .$post['user_id']. "'
							AND address_type = 'p'";
			$db->setQuery( $sql );
			$id = $db->loadResult();

			// If there is no id - lets skip this query
			if( $id ) {
				// Lets update the old primary address to just billing
				$sql = "UPDATE #__pago_user_info
							SET address_type_name = '',
								address_type = 'b'
								WHERE id = " . (int) $id;
				$db->setQuery( $sql );
				$update = $db->query();
			}
		}

		$address_type_name = '';
		if( $type == 's' ) {
			$address_type_name = 'Shipping';
		}

		if( $type == 'b' ) {
			$address_type_name = 'Billing';
		}

		if( $type == 'p' ) {
			$address_type_name = 'Primary';
		}
		$sql = "UPDATE #__pago_user_info
					SET	address_type = " . $db->Quote( $type ) . ",
						address_type_name = " . $db->Quote( $address_type_name ) . ",";

						if( isset( $address['company'] ) )
							$sql .= "company = " . $db->Quote( $address['company'] ) . ",";
						if( isset( $address['lastname'] ) )
							$sql .= "last_name = " . $db->Quote( $address['lastname'] ) . ",";
						if( isset( $address['firstname'] ) )
							$sql .= "first_name = " . $db->Quote( $address['firstname'] ) . ",";
						if( isset( $address['middlename'] ) )
							$sql .= "middle_name = " . $db->Quote( $address['middlename'] ) . ",";
						if( isset( $address['telephoneno'] ) )
							$sql .= "phone_1 = " . $db->Quote( $address['telephoneno'] ) . ",";
						if( isset( $address['phone_2'] ) )
							$sql .= "phone_2 = " . $db->Quote( $address['phone_2'] ) . ",";
						if( isset( $address['address1'] ) )
							$sql .= "address_1 = " . $db->Quote( $address['address1'] ) . ",";
						if( isset( $address['address2'] ) )
							$sql .= "address_2 = " . $db->Quote( $address['address2'] ) . ",";
						if( isset( $address['country'] ) )
							$sql .= "country = " . $db->Quote( $address['country'] ) . ",";
						if( isset( $address['city'] ) )
							$sql .= "city = " . $db->Quote( $address['city'] ) . ",";
						if( isset( $address['countystate'] ) )
							$sql .= "state = " . $db->Quote( $address['countystate'] ) . ",";
						if( isset( $address['postcodezip'] ) )
							$sql .= "zip = " . $db->Quote( $address['postcodezip'] ) . ", ";
						if( isset( $address['email'] ) )
							$sql .= "user_email = " . $db->Quote( $address['email'] ) . " ";

						$sql .= "WHERE id = " . $db->Quote( (int) $address['id'] );

		$db->setQuery( $sql );

		if (!$result = $db->query()){
			echo $db->stderr();
			return false;
		}

		return true;
	}

	public function delete_address()
	{
		$id = JFactory::getApplication()->input->getInt( 'id' );
		$db = JFactory::getDBO();

		$sql = "DELETE FROM #__pago_user_info WHERE id = " . (int) $id;
		$db->setQuery( $sql );

		if (!$result = $db->query()){
			echo $db->stderr();
			return false;
		}

		return true;
	}

	private function reset_old_address()
	{
	}

	public function update_password()
	{
		$mainframe = JFactory::getApplication();
		jimport('joomla.user.helper');

		$post = JFactory::getApplication()->input->getArray($_POST);

		// Make sure that we have a pasword
		if ( ! $post['txt_NewPassword'] )
		{
			$this->setError( JText::_( 'MUST_SUPPLY_PASSWORD' ) );
			return false;
		}

		// Verify that the passwords match
		if ( $post['txt_NewPassword'] != $post['txt_ConfirmPassword'] )
		{
			$this->setError( JText::_( 'PASSWORDS_DO_NOT_MATCH_LOW' ) );
			return false;
		}

		// Get the necessary variables
		$db			= JFactory::getDBO();
		$u			= JFactory::getUser();
		$salt		= JUserHelper::genRandomPassword( 32 );
		$crypt		= JUserHelper::getCryptedPassword( $post['txt_NewPassword'], $salt );
		$password	= $crypt.':'.$salt;

		// Get the user object
		$user = new JUser( $u->get( 'id' ) );

		// Fire the onBeforeStoreUser trigger
		JPluginHelper::importPlugin( 'user' );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onBeforeStoreUser', array( $user->getProperties(), false ) );

		// Build the query
		$query 	= 'UPDATE #__users'
				. ' SET password = ' . $db->Quote( $password )
				. ' , activation = ""'
				. ' WHERE id = ' . (int) $u->get( 'id' )
				. ' AND block = 0';

		$db->setQuery( $query );

		// Save the password
		if ( !$result = $db->query() )
		{
			$this->setError( JText::_( 'DATABASE_ERROR' ) );
			return false;
		}

		// Update the user object with the new values.
		$user->password			= $post['txt_NewPassword'];
		$user->activation		= '';
		$user->password_clear	= $post['txt_ConfirmPassword'];

		// Fire the onAfterStoreUser trigger
		$dispatcher->trigger( 'onAfterStoreUser', array( $user->getProperties(), false, $result, $this->getError() ) );

		return true;
	}
	
	public function getUserAddresses($userId)
	{
		$db = JFactory::getDBO();
		$sql= "SELECT * from #__pago_user_info where user_id='" . $userId . "'";
		$db->setQuery( $sql );
		return $user_addresses = $db->loadObjectList();
	}
	
	public function getUserData($userId)
	{
		$db = JFactory::getDBO();
		$sql= "SELECT * from #__users where id='" . $userId . "'";
		$db->setQuery( $sql );
		return $user_data = $db->loadAssocList();

	}
}
