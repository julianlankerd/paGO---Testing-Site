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

class PagoControllerCategories extends PagoController
{
	/**store
	* Custom Constructor
	*/
	private $_view = 'categories';

	function __construct( $default = array() )
	{
		parent::__construct( $default );

		$this->redirect_to = 'index.php?' . http_build_query( array(
			'option'=> JFactory::getApplication()->input->get( 'option' ),
			'view'=> JFactory::getApplication()->input->get( 'view' )
		));

		$this->model = $this->getModel( 'Categories','PagoModel' );

		$this->registerTask( 'new', 'add' );
		$this->registerTask( 'tree', 'tree' );
	}

	function add()
	{
		$id = $this->store(true);

		$this->setRedirect( 'index.php?option=com_pago&view=categories&task=edit&cid[]='.$id);
	}

	function edit()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function tree()
	{
		JFactory::getApplication()->input->set( 'layout', 'tree' );
		JFactory::getApplication()->input->set( 'format', 'tree' );
		parent::display();
	}

	function editgrid()
	{
		$operation = JFactory::getApplication()->input->get( 'oper' );

		switch( JFactory::getApplication()->input->get( 'oper' ) ){
			case 'del':
				$this->delete();
			break;
			case 'edit':
				$this->grid_edit();
			break;
			case 'setdefault':
				$this->grid_setdefault();
			break;
		}
	}

	function remove()
	{
		$ids = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );

		if( !is_array( $ids ) ) return;

		$table = JTable::getInstance( 'categoriesi', 'Table' );

		$fileModel = JModelLegacy::getInstance( 'File', 'PagoModel' );
		$itemModel = JModelLegacy::getInstance( 'Items', 'PagoModel' );

		foreach( $ids as $id ) {
			if ( $table->can_delete( $id ) ) {
				// delete category items
				$itemModel->deleteItemsByCategory($id);

				//delete category images
				$fileModel->delete_all( $id , array('category'));
				
				//delete category Relations
				$table->deleteRelations( $id );

				$table->delete( $id, true );
				
			} else {
				$cannot_delete[] = $id;
			}
		}

		if ( !empty( $cannot_delete ) ) {
			$msg = JTEXT::_('PAGO_CATEGORIES_NOT_DELETE') . ' : '
				. implode( ', ', $cannot_delete );
		} else {
			$msg = JTEXT::_('PAGO_CATEGORIES_DELETE');
		}

		$this->setRedirect( 'index.php?option=com_pago&view=categories', $msg );
	}

	function grid_edit()
	{
		$this->store();
	}

	function moveup()
	{
		$id = JFactory::getApplication()->input->get( 'id' );

		$table = JTable::getInstance( 'categoriesi', 'Table' );
		$table->load( $id );

		if ( !$table->orderUp( null ) ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_CHANGED_CATEGORY_ORDER' ) );
	}

	function movedown()
	{
		$id = JFactory::getApplication()->input->get( 'id' );

		$table = JTable::getInstance( 'categoriesi', 'Table' );
		$table->load( $id );

		if ( !$table->orderDown( null ) ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_CHANGED_CATEGORY_ORDER' ) );
	}

	function publish()
	{
		foreach( JFactory::getApplication()->input->get('cid', 0, 'array') as $id ) {

			$table = JTable::getInstance( 'categoriesi', 'Table' );
			$table->load( $id );
			$table->published = 1;

			if ( !$table->store() ) {
				return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}

		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_PUBLISHED_CATEGORY' ) );
	}

	function unpublish()
	{
		foreach( JFactory::getApplication()->input->get('cid', 0, 'array') as $id ) {

			$table = JTable::getInstance( 'categoriesi', 'Table' );
			$table->load( $id );
			$table->published = 0;

			if ( !$table->store() ) {
				return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}

		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_UNPUBLISHED_CATEGORIES' ) );
	}

	function feature()
	{
		$id = JFactory::getApplication()->input->get( 'id' );

		$table = JTable::getInstance( 'categoriesi', 'Table' );
		$table->load( $id );
		$table->featured = 1;

		if ( !$table->store() ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_FEATURED_CATEGORY' ) );
	}

	function unfeature()
	{
		$id = JFactory::getApplication()->input->get( 'id' );

		$table = JTable::getInstance( 'categoriesi', 'Table' );
		$table->load( $id );
		$table->featured = 0;

		if ( !$table->store() ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_UNFEATURED_CATEGORY' ) );
	}

	function apply()
	{

		$cid = $this->store();

		if( JFactory::getApplication()->input->get( 'id' ) ) $cid = JFactory::getApplication()->input->get( 'id' );

		$msg = JText::_( 'Successfully Applied Parameters' );
		$this->setRedirect( 'index.php?option=com_pago&view='. $this->_view . '&task=edit&cid[]=' .
			$cid, $msg );
	}
	
	function save2new()
	{
		$this->store();
		$msg = JText::_('PAGO_CATEGORY_SAVE');
		$this->setRedirect('index.php?option=com_pago&view=categories&task=add', $msg);
	}
	
	function copy()
	{
		JFactory::getApplication()->input->set( 'layout', 'copy' );

		parent::display();
	}

	function save()
	{
		$this->store();

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_CATEGORY_SAVE' ) );
	}

	function store($new = false)
	{
		$table = JTable::getInstance( 'categoriesi', 'Table' );
		$data = JFactory::getApplication()->input->post->get( 'params', array(), 'array' );
		$db = JFactory::getDBO();

		if(!$new){

			if ( $data['id'] > 0 ) {
				$table->load( $data['id'] );
			}	
		
			if ( $table->parent_id != $data['parent_id'] || $data['id'] == 0 ) {
				$table->setLocation( $data['parent_id'], 'last-child');
			}
			$data['visibility'] = 1;
		}else{

			//create category for attach media image and set expiry date for remove if item not saved
			$timestamp = time() + 86400;
			$table->setLocation( 1, 'last-child');
			$data['visibility'] = 0;
			$data['expiry_date'] = $timestamp;
		}

		$data['name'] = $data['name'] != '' ? $db->escape($data['name']) : $data['name'];
		$data['meta']['title'] = $data['meta']['title'] != '' ? $db->escape($data['meta']['title']) : $data['meta']['title'];
		$data['meta']['author'] = $data['meta']['author'] != '' ? $db->escape($data['meta']['author']) : $data['meta']['author'];

		if ( !$table->bind( $data ) ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		if(!$new){
			if ( !$table->check() ) {
				return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}
		}

		if ( !$table->store() ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		if ( !$table->rebuildPath( $table->id ) ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		if ( !$table->rebuild( $table->id, $table->lft, $table->level, $table->path ) ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		if(!$new){
			$meta = Pago::get_instance( 'meta' );

			foreach ( $data['meta'] as $key => $value ) {
				$meta->update( 'category', $table->id, $key, $value );
			}

			$this->store_files( $data );
		}

		return $table->id;
	}


	function store_files( $data )
	{
		if ( !isset( $data['images'] ) ) {
			return;
		}

		$images = (array) $data['images'];

		$model = JModelLegacy::getInstance( 'file', 'PagoModel' );

		foreach ( $images as $id => $file ) {
			if ( empty( $file ) ) {
				continue;
			}
			if ( !$model->store( array_merge( $file, array( 'id' => $id ) ) ) ) {
				JFactory::getApplication()->enqueueMessage('An error has occurred: ' . $model->getError(), 'error');
			}
		}

		// Store ordering
		$model->save_order( $data['images_ordering'], $data['id'] );
	}

	function cancel()
	{
		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_CANECELLED_OPERATION' ) );
	}

	function rename()
	{
		$table = JTable::getInstance( 'categoriesi', 'Table' );
		$id = JFactory::getApplication()->input->getInt( 'id', 0 );
		$name = JFactory::getApplication()->input->get( 'name', null );

		if ( $id == 0 || $name == null ) {
			return;
		}

		$table->load( $id );
		$table->name = $name;

		if ( !$table->check() ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		if ( !$table->store() ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		if ( !$table->rebuildPath( $table->id ) ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		if ( !$table->rebuild( $table->id, $table->lft, $table->level, $table->path ) ) {
			return JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
		}

		return;
	}

	function move()
	{
		return;
	}
	function related_category()
	{
		$db = JFactory::getDBO();
		$word = JFactory::getApplication()->input->getString('q', null);
		$itemId = JFactory::getApplication()->input->get( 'itemId' );
		$exCatIds = JFactory::getApplication()->input->get( 'exCatIds' );
		$and = '';
		if($exCatIds != '' AND $exCatIds != false)
		{
			$and .= ' AND id NOT IN("'.$exCatIds.'")';
		}
        $query = "SELECT name as label,id as value FROM #__pago_categoriesi WHERE visibility != 0 AND published = 1 ".$and." AND name LIKE '%" . $word . "%'";
        $db->setQuery( $query );
        $result = $db->loadObjectList();
        echo json_encode($result);
		exit;
	}
	public function saveorder()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		JLog::add('MenusControllerItems::saveorder() is deprecated. Function will be removed in 4.0', JLog::WARNING, 'deprecated');

		// Get the arrays from the Request
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder))
		{
			parent::saveorder();
		}
		else
		{
			// Nothing to reorder
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return true;
		}
	}

	public function saveOrderAjax()
	{
		// Get the input
		$jinput = JFactory::getApplication()->input;
		
		$pks = $jinput->get('cid', array(), 'array');
		$order = $jinput->get('order', array(), 'array');
	

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model

		// Save the ordering
		
		$model = JModelLegacy::getInstance( 'Categories', 'PagoModel' );
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

}
