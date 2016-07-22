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

class PagoControllerItem_types extends PagoController
{
	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );
		$this->registerTask( 'save', 'save' );
		//$this->registerTask( 'default', 'makeDefault' );
		$this->registerTask( 'unpublish', 'publish' );
	}
	function delete(){
		$id = JFactory::getApplication()->input->get( 'id', '', '' );
		$model     = JModelLegacy::getInstance('item_types', 'PagoModel');
		$result = $model->delete($id);
		echo $result;
		die();
	}
	// function makeDefault()
	// {
	// 	$model = $this->getModel( 'item_types' );

	// 	if ( $model->makeDefault() ) {
	// 		$msg = 1;
	// 	} else {
	// 		$msg = JText::_( 'An error has occurred: '.$model->getError() );
	// 	}

	// 	echo $msg;
	// 	jexit();
	// }
	function publish()
	{
		$db  = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get( 'id', '', '' );

		$publish = ( $this->getTask() == 'publish' ? 1 : 0 );


		$query = 'UPDATE #__pago_item_types SET published = ' . (int) $publish
			. ' WHERE id =  ' .$id. ' ';
		$db->setQuery( $query );

		if ( !$db->query() ) {
			JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
		}

		$query	= $db->getQuery(true);
		// Instantiate an article table object
		$query->select(' * ');
		$query->from( '`#__pago_item_types` AS item' );
		$query->where("`id` = {$id}");
		$db->setQuery($query);	
		
		$row = $db->loadObject();

		echo PagoHelper::published( $row, 0, 'tick.png',  'publish_x.png',
			'', ' class="publish-buttons" type="item_types" rel="' .$row->id. '"' );
		jexit();
	}
	
	function add(){
		$data['name'] = JFactory::getApplication()->input->get('itemTypeName', '', '' );
		$data['physical'] = JFactory::getApplication()->input->get('itemTypePhysical', '', '' );
		$model = $this->getModel( 'item_types' );	
		$id = $model->store($data);
		$return['error'] = 0;
		if($id){
			$return['id'] = $id;	
		}else{
			$return['error'] = 1;
			$return['message'] = $model->getError();
		}
		echo json_encode($return);
		exit();
	}
	
	function item_tags()
	{
		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$word = JFactory::getApplication()->input->get('q', null, 'string');
		$table = JTable::getInstance( 'Item_types', 'Table' );
		$tags = $table->search_tag($word);
		echo json_encode($tags);
        exit();
	}
}
