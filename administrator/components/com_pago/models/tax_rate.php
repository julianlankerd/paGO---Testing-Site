<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');
jimport('joomla.application.component.model');


class PagoModelTax_rate extends JModelLegacy
{
	var $_data;

	var $_order = array();

	function __construct()
	{
		parent::__construct();

		$array = JFactory::getApplication()->input->get('cid',  0, 'array');
		$this->setId((int) $array[0]);
	}

	function setId( $id )
	{
		// Set id and wipe data
		$this->_id   = $id;
		$this->_data = null;
	}

	function getData()
	{
		// Load the data
		$this->_data = JTable::getInstance('tax', 'table');
		$this->_data->load($this->_id);

		return $this->_data;
	}

	function store($copy = false)
	{
		$config = Pago::get_instance('config')->get();
		$row = $this->getTable('tax');

		$data = JFactory::getApplication()->input->getArray($_POST);
		$id   = $data['pgtax_id'];

		$data = $data['params'];
		if(!array_key_exists ("pgtax_state", $data)){
			$data['pgtax_state']="";
		}
		if (!$row->bind($data))
		{
			return JError::raiseWarning(500, $row->getError());
		}

		if (!$row->check())
		{
			JError::raiseWarning(500, $row->getError());

			return false;
		}

		if (!$row->store())
		{
			return JError::raiseWarning(500, $row->getError());
		}

		$row_db = $row->get('_db');

		if( $insert_id = $row_db->insertid() )
		{
			$id = $row_db->insertid();
			$data['id'] = $id;
			$create = true;
		}

		if($id){
			return $id;
		}
		return $insert_id;
	}

}