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


class PagoModelCategory extends JModelLegacy
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
		$this->_id        = $id;
		$this->_data    = null;
	}

	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {

			$query = "SELECT * FROM #__pago_categoriesi WHERE id = {$this->_id}";

			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();

		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->name = false;
			$this->_data->parent_id = 0;
			$this->_data->path = '';
			$this->_data->alias = '';
			$this->_data->description = '';
			$this->_data->meta_html_title = '';
			$this->_data->meta_tag_title = '';
			$this->_data->meta_tag_author = '';
			$this->_data->meta_tag_robots = '';
			$this->_data->meta_tag_keywords = '';
			$this->_data->meta_tag_description = '';
			$this->_data->item_count = '';
			$this->_data->created_time = date( 'Y-m-d H:i:s', time() );
			$this->_data->created_user_id = JFactory::getUser()->get( 'id' );
			$this->_data->modified_user_id = '';
			$this->_data->modified_time = '0000-00-00 00:00:00';
			$this->_data->published = 1;
			$this->_data->featured = 0;
			$this->_data->truncate_desc = 100;
		}
		return $this->_data;
	}
}
