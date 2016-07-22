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


class PagoModelPromo extends JModelLegacy
{
	var $_data;
	var $_order  = array();

	function __construct()
	{
		parent::__construct();

		$array = JFactory::getApplication()->input->get('cid',  0, 'array');
		$this->setId((int)$array[0]);
	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id      = $id;
		$this->_data    = null;
	}

	function &getData()
	{
		// Load the data
		if ( empty( $this->_data ) ) {
			$query = "SELECT * FROM #__pago_sales
						WHERE id = $this->_id";

			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			$this->_data->categories = explode( ":", $this->_data->categories );
		}
		if (!$this->_data) {
			// no reason for formating other then just felt like it
			$this->_data               = new stdClass();
			$this->_data->id           = 0;
			$this->_data->name         = null;
			$this->_data->value        = null;
			$this->_data->vendor       = null;
			$this->_data->amount       = null;
			$this->_data->created      = date( 'Y-m-d H:i:s', time() );
			$this->_data->modified     = date( 'Y-m-d H:i:s', time() );
			$this->_data->sale_end     = date( 'Y-m-d H:i:s', time() );
			$this->_data->published    = false;
			$this->_data->price_max    = null;
			$this->_data->price_min    = null;
			$this->_data->condition    = false;
			$this->_data->categories   = false;
			$this->_data->sale_start   = date( 'Y-m-d H:i:s', time() );
			$this->_data->quantity_min = null;
			$this->_data->quantity_max = null;
			$this->_data->manufacturer = null;
		}
		return $this->_data;
	}

	function store()
	{
		$row = $this->getTable();

		$data = JFactory::getApplication()->input->getArray($_POST);
		$data = $data['params'];

		$create = false;
		$id = $data['id'];

		if($id == 0){
			$data['created'] = date( 'Y-m-d H:i:s', time() );
		}
		// always update modified time
		$data['modified'] = date( 'Y-m-d H:i:s', time() );
		$data['categories'] = implode( ':', $data['categories'] );

		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->check()) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );

		if( $insert_id = $row->_db->insertid() ){
			$id 	= $row->_db->insertid();
			$data['id'] = $id;
			$create = true;
		}

		return $insert_id;
	}
}
