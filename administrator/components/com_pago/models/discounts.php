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


class PagoModelDiscounts extends JModelLegacy
{
	/**
     * Hellos data array
     *
     * @var array
     */
	var $_data;
	var $_order  = array();
	var $_items;

	/**
	 * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */

	function __construct()
	{
		parent::__construct();

		$option = 'com_pago_discounts';

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest(
			'global.list.limit',
			'limit',
			$mainframe->getCfg('list_limit'),
			'int'
		);
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$filter_search = $mainframe->getUserStateFromRequest(
			$option.'search',
			'search',
			'',
			'string'
		);
		$filter_order = $mainframe->getUserStateFromRequest(
			$option.'filter_order',
			'filter_order',
			'id',
			'cmd'
		);
		$filter_order_Dir = $mainframe->getUserStateFromRequest(
			$option.'filter_order_Dir',
			'filter_order_Dir',
			'asc',
			'word'
		);
		$filter_state = $mainframe->getUserStateFromRequest(
			$option.'filter_state',
			'filter_state',
			'',
			'word'
		);

		$this->setState('filter_search', $filter_search);
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
	}

	function _buildQuery()
	{
		$query = ' SELECT * '
			. ' FROM #__pago_discount_rules ';
		return $query;
	}

	/**
     * Retrieves the hello data
     * @return array Array of objects containing the data from the database
     */
	function getData()
	{
		$db = JFactory::getDBO();

		$this->_buildContentOrderBy();

		if( $this->_items ) return $this->_items;

		$where = array();

		if ( $this->getState('filter_search') ) 
		{
			$where[] = 'r.rule_name LIKE '.
				$db->Quote(
					'%'.$db->escape( $this->getState('filter_search'), true ).'%',
					false
				);
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$cid = $this->getState('cid');

		$sql = "SELECT SQL_CALC_FOUND_ROWS r.*
				FROM #__pago_discount_rules as r $where $this->_order";

		$this->_items = $this->_getList(
			$sql,
			$this->getState('limitstart'),
			$this->getState('limit')
		);

		$db->setQuery('SELECT FOUND_ROWS();');
		$this->_total = $db->loadResult();

		return $this->_items;
	}

	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) 
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination(
				$this->_total,
				$this->getState('limitstart'),
				$this->getState('limit')
			);
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
		$db = JFactory::getDBO();
	
		if( is_array( $this->getState('cid') ) )
		foreach( $this->getState('cid') as $id )
		{
			// Select dicount event from discount ID
			$select_event_type = "SELECT discount_event FROM #__pago_discount_rules where id = '".$id."'";
			$db->setQuery( $select_event_type );
			$discount_event = $db->loadResult();
			
			if($discount_event != 4)
			{
				$delete_items_query = "SELECT item_id as id FROM #__pago_discount_items where discount_rule_id = '".$id."'";
				$db->setQuery( $delete_items_query );
				$delete_items_array = $db->loadColumn();
				if(count($delete_items_array) > 0 )
				{
					foreach( $delete_items_array as $item_old_id )
					{
						$query_update_item = "UPDATE #__pago_items set apply_discount=1 where id='".$item_old_id."'";
						$db->setQuery( $query_update_item );
						$db->query();
					}
				}
			}
			
			$delete_category_query = "SELECT category_id as id FROM #__pago_discount_categories where discount_rule_id = '".$id."'";
			$db->setQuery( $delete_category_query );
			$delete_category_array = $db->loadColumn();
			if(count($delete_category_array) > 0)
			{
				foreach( $delete_category_array as $category_old_id )
				{
					$query_category_items = "SELECT item_id as id FROM #__pago_categories_items where category_id  = '".$category_old_id."'";
					$db->setQuery( $query_category_items );
					$total_category_items = $db->loadColumn();
					if(count($total_category_items) > 0)
					{
						foreach( $total_category_items as $itemid )
						{
							$query_update_item = "UPDATE #__pago_items set apply_discount=1 where id='".$itemid."'";
							$db->setQuery( $query_update_item );
							$db->query();
						}
					}
				}
			}
			
			$this->_getList("
				UPDATE #__pago_discount_rules
					SET published = 1
						WHERE id = $id
			");
		}

		return $this->_data;
	}

	function unpublish()
	{
		$db = JFactory::getDBO();
		
		if( is_array( $this->getState('cid') ) )
		foreach( $this->getState('cid') as $id )
		{
		
			// Select dicount event from discount ID
			$select_event_type = "SELECT discount_event FROM #__pago_discount_rules where id = '".$id."'";
			$db->setQuery( $select_event_type );
			$discount_event = $db->loadResult();
			
			if($discount_event != 4)
			{
				$delete_items_query = "SELECT item_id as id FROM #__pago_discount_items where discount_rule_id = '".$id."'";
				$db->setQuery( $delete_items_query );
				$delete_items_array = $db->loadColumn();
				if(count($delete_items_array) > 0 )
				{
					foreach( $delete_items_array as $item_old_id )
					{
						$query_update_item = "UPDATE #__pago_items set apply_discount=0 where id='".$item_old_id."'";
						$db->setQuery( $query_update_item );
						$db->query();
					}
				}
			}
			
			$delete_category_query = "SELECT category_id as id FROM #__pago_discount_categories where discount_rule_id = '".$id."'";
			$db->setQuery( $delete_category_query );
			$delete_category_array = $db->loadColumn();
			if(count($delete_category_array) > 0)
			{
				foreach( $delete_category_array as $category_old_id )
				{
					$query_category_items = "SELECT item_id as id FROM #__pago_categories_items where category_id  = '".$category_old_id."'";
					$db->setQuery( $query_category_items );
					$total_category_items = $db->loadColumn();
					if(count($total_category_items) > 0)
					{
						foreach( $total_category_items as $itemid )
						{
							$query_update_item = "UPDATE #__pago_items set apply_discount=0 where id='".$itemid."'";
							$db->setQuery( $query_update_item );
							$db->query();
						}
					}
				}
			}
		
			$this->_getList("
				UPDATE #__pago_discount_rules
					SET published = 0
						WHERE id = $id
			");
		}

		return $this->_data;
	}

	function remove()
	{
		$db = JFactory::getDBO();

		if( is_array( $this->getState('cid') ) )
		foreach( $this->getState('cid') as $id )
		{
			// Select dicount event from discount ID
			$select_event_type = "SELECT discount_event FROM #__pago_discount_rules where id = '".$id."'";
			$db->setQuery( $select_event_type );
			$discount_event = $db->loadResult();
			
			
			$this->_getList("
				DELETE FROM #__pago_discount_rules
					WHERE id = $id
			");
			if($discount_event != 4)
			{
				$delete_items_query = "SELECT item_id as id FROM #__pago_discount_items where discount_rule_id = '".$id."'";
				$db->setQuery( $delete_items_query );
				$delete_items_array = $db->loadColumn();
				if(count($delete_items_array) > 0 )
				{
					foreach( $delete_items_array as $item_old_id )
					{
						$query_update_item = "UPDATE #__pago_items set apply_discount=0 where id='".$item_old_id."'";
						$db->setQuery( $query_update_item );
						$db->query();
					}
				}
			}
			
			$delete_category_query = "SELECT category_id as id FROM #__pago_discount_categories where discount_rule_id = '".$id."'";
			$db->setQuery( $delete_category_query );
			$delete_category_array = $db->loadColumn();
			if(count($delete_category_array) > 0)
			{
				foreach( $delete_category_array as $category_old_id )
				{
					$query_category_items = "SELECT item_id as id FROM #__pago_categories_items where category_id  = '".$category_old_id."'";
					$db->setQuery( $query_category_items );
					$total_category_items = $db->loadColumn();
					if(count($total_category_items) > 0)
					{
						foreach( $total_category_items as $itemid )
						{
							$query_update_item = "UPDATE #__pago_items set apply_discount=0 where id='".$itemid."'";
							$db->setQuery( $query_update_item );
							$db->query();
						}
					}
				}
			}

			$this->_getList("
				DELETE FROM #__pago_discount_items
					WHERE discount_rule_id = $id
			");

			$this->_getList("
				DELETE FROM #__pago_discount_categories
					WHERE discount_rule_id = $id
			");
		}

		return true;
	}
}
