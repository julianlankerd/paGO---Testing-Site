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


class PagoModelOrders_items extends JModelLegacy
{
    /**
     * Hellos data array
     *
     * @var array
     */
    var $_data;
 	var $_order = array();
	var $_items;
	var $_primary_key = 'order_id';
	var $_order_id = false;

    function _buildQuery()
    {
        $query = ' SELECT * '
            . ' FROM #__pago_orders '
        ;
        return $query;
    }

    function get_order()
    {
		$db = JFactory::getDBO();

		if ( $this->getState('order_id') ) {
			$where[] = 'order_id = ' . $this->getState('order_id');
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$sql = "SELECT * FROM  #__pago_orders $where";

		$db->setQuery( $sql );

		return $db->loadObject();
	}

    function getData()
    {
		$db = JFactory::getDBO();

		$this->_buildContentOrderBy();

		if( $this->_items ) return $this->_items;

		$where = array();

		if ( $this->getState('filter_search') ) {
			$where[] = 'orders.order_id LIKE '.$db->Quote( '%'.$db->escape( $this->getState('filter_search'), true ).'%', false );
		}

		//$where[] = "user_info.address_type =  'b'";

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		//$cid = $this->getState('cid');

		$sql = "SELECT SQL_CALC_FOUND_ROWS
					orders.*, user_info.name
				FROM #__pago_orders as orders
				LEFT JOIN #__users as user_info
				ON orders.user_id = user_info.id
				$where $this->_order";

		$sql1 = "SELECT SQL_CALC_FOUND_ROWS orders.*
				FROM #__pago_orders as orders $where $this->_order";

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
		   		DELETE FROM #__pago_orders
					WHERE order_id = $id
		   ");
	   }

       return true;
    }

	function get_order_items_for_grid( $order_id )
	{
		$db = JFactory::getDBO();

		$query = "SELECT items.price as current_price, items.qty as instock, items.*, order_items.* FROM #__pago_orders_items as order_items
		LEFT JOIN #__pago_items as items
		ON items.id = order_items.item_id
		WHERE order_items.order_id = $order_id";

		$db->setQuery( $query );
		return $db->loadAssocList();
		//return $db->loadObjectList('item_id');
	//echo mysql_error();
	//return $return;
	}

	function get_order_items( $order_id )
	{
		$db = JFactory::getDBO();

		$query = "SELECT items.*, items.price as current_price, order_items.* FROM #__pago_orders_items as order_items
		LEFT JOIN #__pago_items as items
		ON items.id = order_items.item_id
		WHERE order_items.order_id = $order_id";

		$db->setQuery( $query );
		//return $db->loadAssocList();
		return $db->loadObjectList('item_id');
	//echo mysql_error();
	//return $return;
	}

	function store($order_id, $cart)
	{
		$db = JFactory::getDBO();
		$config = Pago::get_instance('config')->get('global');
		$values = array();

		if (isset($cart['items']))
		{
			foreach ($cart['items'] as $k => $item)
			{
				$attributes = $db->escape(json_encode($item->attrib));
				$order_item_shipping  = 0;
				$order_item_ship_method_id  = "";

				if ($config->get('checkout.shipping_type'))
				{
					if(count($item->carrier) > 0)
					{
						$order_item_shipping  = $item->carrier['value'];
						$order_item_ship_method_id  = $item->carrier['name'];
					}
				}

				$row = $this->getTable();
				$data = array();
				$data['order_id'] = $order_id;
				$data['item_id'] = $item->id;
				$data['qty'] = $item->cart_qty;
				$data['price'] = $item->price;
				$data['price_type'] = $item->price_type;
				$data['attributes'] = $attributes;
				$data['varation_id'] = $item->varationId;
				$data['sub_recur'] = $item->sub_recur;
				$data['order_item_shipping'] = $order_item_shipping;
				$data['order_item_ship_method_id'] = $order_item_ship_method_id;
				$data['order_item_tax'] = $item->order_item_tax;
				$data['order_item_shipping_tax'] = $item->order_item_shipping_tax;

				if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );
				if (!$row->check()) return JError::raiseWarning( 500, $row->getError() );
				if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );
			}
		}
		return true;
	}
}
