<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerUnits extends PagoController
{
	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );
		$this->registerTask( 'default', 'makeDefault' );
		$this->registerTask( 'unpublish', 'publish' );
	}

	function delete()
	{
		$id = JFactory::getApplication()->input->get('id', '', '');
		$model     = JModelLegacy::getInstance('units', 'PagoModel');
		$result = $model->delete($id);
		echo $result;
		die();
	}

	function makeDefault()
	{
		$model = $this->getModel('units');

		if ($model->makeDefault())
		{
			$msg = 1;
		}
		else
		{
			$msg = JText::_('An error has occurred: ' . $model->getError());
		}

		echo $msg;
		jexit();
	}

	function publish()
	{
		$db  = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get('id', '', '');
		$type = JFactory::getApplication()->input->get('type', '', '');
		$publish = ( $this->getTask() == 'publish' ? 1 : 0 );


		$query = 'UPDATE #__pago_units SET published = ' . (int) $publish
			. ' WHERE id =  ' .$id. ' ';
		$db->setQuery($query);

		if (!$db->query())
		{
			JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
		}

		$query	= $db->getQuery(true);

		// Instantiate an article table object
		$query->select(' * ');
		$query->from('`#__pago_units` AS item');
		$query->where("`id` = {$id}");
		$db->setQuery($query);

		$row = $db->loadObject();

		if ($type == "weight")
		{
			echo PagoHelper::published($row, 0, 'tick.png', 'publish_x.png',
			'', ' class="publish-buttons" type="weight_unit" rel="' .$row->id. '"');
		}
		else
		{
			echo PagoHelper::published($row, 0, 'tick.png', 'publish_x.png',
			'', ' class="publish-buttons" type="size_unit" rel="' .$row->id. '"');
		}

		jexit();
	}


	function add()
	{
		$data['name'] = JFactory::getApplication()->input->get('unitName', '', '');
		$data['code'] = JFactory::getApplication()->input->get('unitCode', '', '');
		$data['type'] = JFactory::getApplication()->input->get('unitType', '', '');

		$model = $this->getModel('units');
		$id = $model->store($data);
		$return['error'] = 0;

		if($id)
		{
			$return['id'] = $id;
		}
		else
		{
			$return['error'] = 1;
			$return['message'] = $model->getError();
		}

		echo json_encode($return);
		exit();
	}
}
