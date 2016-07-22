<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');


class PagoModelClients extends JModelLegacy
{
    /**
     * Hellos data array
     *
     * @var array
     */
    var $_data;
 	var $_order = array();
	var $_items;
	var $_primary_key = 'user_id';
	var $_user_id = false;

    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */

	function __construct()
	{
		parent::__construct();

		$option = 'com_pago_clients';

 		$mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

		$filter_search = $mainframe->getUserStateFromRequest( $option.'search', 			'search', 			'',				'string' );
		$filter_order     = $mainframe->getUserStateFromRequest(  $option.'filter_order', 'filter_order', $this->_primary_key, 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'filter_state', 		'filter_state', 	'',		'word' );

		$this->setState('filter_search', $filter_search);
        $this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
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
		$db = JFactory::getDBO();

		$this->_buildContentOrderBy();

		if( $this->_items ) return $this->_items;

		$where = array();
		//address_type
		$where[] = "info.address_type = 'b'";

		if ( $this->escape('filter_search') ) {
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

	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

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
}
