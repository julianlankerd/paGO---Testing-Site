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


class PagoModelPromos extends JModelLegacy
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

		$option = 'com_pago_sales';

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
			. ' FROM #__pago_sales ';
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

		if ( $this->getState('filter_search') ) {
			$where[] = 'sales.name LIKE '.
				$db->Quote(
					'%'.$db->escape( $this->getState('filter_search'), true ).'%',
					false
				);
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$cid = $this->getState('cid');

		$sql = "SELECT SQL_CALC_FOUND_ROWS sales.*
				FROM #__pago_sales as sales $where $this->_order";

		$this->_items = $this->_getList(
			$sql,
			$this->getState('limitstart'),
			$this->getState('limit')
		);

		$db->setQuery('SELECT FOUND_ROWS();');
		$this->_total = $db->loadResult();
		$sql = "SELECT cat_items_ref.item_id, cat_items.name
				FROM #__pago_categories_items as cat_items_ref
					LEFT JOIN #__pago_categories as cat_items
						ON (cat_items_ref.category_id = cat_items.id)";

		$categories = $this->_getList( $sql );

		if(is_array($categories)){
			foreach($categories as $cat){
				$cat_index[$cat->item_id][] = $cat->name;
			}
		}

		if(is_array($this->_items)){
			foreach($this->_items as $k=>$item){
				if($cat_index[$item->id]){
					$this->_items[$k]->category = implode(' | ', $cat_index[$item->id]);
				}

			}
		}

		return $this->_items;
	}

	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
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
		if( is_array( $this->getState('cid') ) )
		foreach( $this->getState('cid') as $id ){
			$this->_getList("
				UPDATE #__pago_sales
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
				UPDATE #__pago_sales
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
				DELETE FROM #__pago_sales
					WHERE id = $id
			");
		}

		return true;
	}
}
