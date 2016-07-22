<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerItems extends PagoController
{
	function __construct( $default = array() )
	{
		parent::__construct( $default );

		$this->redirect_to = 'index.php?' . http_build_query( array(
			'option'=> JFactory::getApplication()->input->get( 'option' ),
			'view'=> JFactory::getApplication()->input->get( 'view' )
		));

		$this->registerTask( 'new', 'add' );
	}
	
	function get_autosuggest_items(){

		$model = $this->getModel('Items', 'PagoModel');

		$state = $model->getState();
		$state-> set( 'filter.search', JFactory::getApplication()->input->get( 'q' ) );

		$items = ( object )$model->getItems();

		$json_data = array();

		foreach( $items as $item ){
			array_push( $json_data, array(
				'value' => $item->id,
				'name' => $item->id . ':' . $item->name,
				'id' => $item->id
			));
		}

		header("Content-type: application/json");

		exit( json_encode( $json_data ) );
	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}

	function publish()
	{
		$this->set_published( true );

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_ITEMS_PUBLISHED' ) );
	}

	function unpublish()
	{
		$this->set_published( false );

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_ITEMS_UNPUBLISHED' ) );
	}

	function cancel()
	{
		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_CANECELLED_OPERATION' ) );
	}
	
	private function set_published( $state = true )
	{
		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$table = JTable::getInstance( 'Items', 'Table' );

		if ( !is_array( JFactory::getApplication()->input->get( 'cid',array(0),'array' ) ) )
			$this->setRedirect( $this->redirect_to,
				JText::_( 'PAGO_CID_MUST_BE_AN_ARRAY' ) );

		foreach ( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) as $item_id ) {
			$data = array(
				'id' => $item_id,
				'published' => $state
			);

			if ( !$table->bind( $data ) ) {
				JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}

			$table->store();
			$table->reset();
		}
	}

	function edit()
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );

		JFactory::getApplication()->input->set( 'layout', 'form' );

		parent::display();
	}

	function add()
	{
		$filterCategory = JFactory::getApplication()->input->getInt( 'filter_primary_category' );

		$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );
		$id = $model->store(false,true);
		
		
		$this->setRedirect( 'index.php?option=com_pago&view=items&task=edit&filterCategory='.$filterCategory.'&cid[]='.$id);	
	}

	function save()
	{
		$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );
		if ( $model->store() ) {
			$msg = JText::_( 'PAGO_ITEMS_SAVED' );
		} else {
			$msg = JText::_( 'PAGO_ITEMS_ERROR' );
		}
		$this->setRedirect( $this->redirect_to, $msg );
	}

	function save2new(){
		
		$this->redirect_to = 'index.php?option=com_pago&view=items&task=new';
		
		$this->save();
	}
	
	function apply()
	{
		$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );
		$id = $model->store();

		if ( $id ) {
			$msg = JText::_( 'PAGO_ITEMS_SAVED' );
		} else {
			$msg = JText::_( 'PAGO_ITEMS_ERROR' );
		}
		
		if ( ! $id ) {
			$id = JFactory::getApplication()->input->get( 'id', 0, 'int' );
		}

		$selectedTab = JFactory::getApplication()->input->get( 'selectedTab');
		
		$link = 'index.php?option=com_pago&view=items&task=edit&cid[]=' . $id.'#'.$selectedTab;
		$this->setRedirect( $link, $msg );
	}

	function remove()
	{ 
		if( !is_array( JFactory::getApplication()->input->get( 'cid',array(0),'array' ) ) )
			$this->setRedirect( $this->redirect_to,
				JText::_( 'PAGO_CID_MUST_BE_AN_ARRAY' ) );
	
		
		$model = JModelLegacy::getInstance( 'Items', 'PagoModel' );
		$model->deleteItems( JFactory::getApplication()->input->get( 'cid',array(0),'array' ) );

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_ITEMS_DELETED' ) );
	}

	function copy()
	{
		JFactory::getApplication()->input->set( 'layout', 'copy' );

		parent::display();
	}

	function copy_run()
	{
		$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );

		$model->setState( 'cid', JFactory::getApplication()->input->get( 'cid', 0, 'ARRAY' ) );
		$model->setState( 'settings', JFactory::getApplication()->input->get( 'copy', 0, 'ARRAY' ) );
		$model->copy();

		$msg = JText::_( 'PAGO ITEMS COPIED' );
		$link = 'index.php?option=com_pago&view=items';
		$this->setRedirect( $link, $msg );
	}
	function related_items()
	{
		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$word = JFactory::getApplication()->input->get('q', null, 'string');
		$itemId = JFactory::getApplication()->input->get( 'itemId' );
		$existPrdId = JFactory::getApplication()->input->get( 'existPrdId' ); 
		$table = JTable::getInstance( 'Items', 'Table' );
		//$items = $table->search_item($word,$itemId,$catid,$existPrdId);
		$items = $table->search_item($word,$itemId,$existPrdId);
		echo json_encode($items);
        exit();
	}
	
	function AssignCategory()
	{
		$post = JFactory::getApplication()->input->getArray($_POST);
		$model = JModelLegacy::getInstance('Item', 'PagoModel');
		
		if ( $model->AssignCategory($post)) 
		{
			$msg = JText::_( 'PAGO_ITEMS_MOVED' );
		}
		else 
		{
			$msg = JText::_( 'PAGO_ITEMS_ERROR_IN_MOVING_CATEGORY' );
		}
		
		$this->setRedirect( "index.php?tmpl=component&option=com_pago&view=items&layout=massmove&msg=1", $msg);
	}

	function ItemOrdering()
	{
		$model = $this->getModel('Items', 'PagoModel');
		
		$post = JFactory::getApplication()->input->getArray($_POST);		

		$exploded = explode("drag-item-", $post['id']);

		$itemIDs = array_filter($exploded);		
		
		$model->sortItems($itemIDs);
	}


	function getBestsellingItemsList($itemsCount=5){
		$itemsCount = $_POST['itemsCount'];
		$db = JFactory::getDBO();
		$itemModel = JModelLegacy::getInstance( 'item', 'PagoModel' );
		$attributeModel = JModelLegacy::getInstance( 'attribute', 'PagoModel' );

		$sql = "SELECT *
			FROM (
				SELECT i.id, i.name,o.price,o.varation_id , SUM( o.qty ) AS quantity
				FROM #__pago_items AS i
				LEFT JOIN #__pago_orders_items AS o ON i.id = o.item_id
				GROUP BY i.id, o.varation_id
			) AS q
			WHERE q.quantity IS NOT NULL
			ORDER BY q.quantity DESC
			LIMIT ".$itemsCount;

		$db->setQuery($sql);

		$items =  $db->loadObjectList();
	
			Pago::load_helpers( 'imagehandler' );
			$arr=array();
	
		foreach($items as $item){
			$item=(array)$item;
			$item['primary_category']=$itemModel->getItemPrimaryCat($item['id']);
			$itemImage = PagoImageHandlerHelper::get_item_files($item['id']);
			if(count($itemImage)>0){
				$item['file_name']=JURI::ROOT().'media/pago/items/'.$item['primary_category'].'/'.$itemImage[0]->file_name;
				
			}
			
			$item['catName'] = $itemModel->getCategoryName($item['primary_category']);
			 
			 if($item['varation_id']!=0){

			 	$item['file_name']=$attributeModel->getVarationImages($item['varation_id'],"-large");
			 	$item['name']=$attributeModel->getVarationName($item['varation_id']);

			 }
			 array_push ($arr,$item);
			
		}
		$items = $arr;
		
		$html = "";
		$counter = 1;

		foreach ($items as $item){
			if(array_key_exists('file_name',$item)){
				if($item['file_name']!=""){
					$image = $item['file_name'] ;
				}else{
					$image = JURI::root() . "administrator/components/com_pago/css/img-new/category-noimage.jpg";
				}

			}else{

				$image = JURI::root() . "administrator/components/com_pago/css/img-new/category-noimage.jpg";
			}
		// var_dump($image);die;

			$html .= '<tr>';
			$html .= '<td class = "pg-numbering">'.$counter.'</td>';
			$html .= '<td class = "pg-preview"><div class ="pg-preview-small-image" style = \'background:url("'.$image.'")\'></div></td>';
			$html .= '<td><a class = "product-name" href="'.JRoute::_('index.php?option=com_pago&controller=items&task=edit&view=items&cid[]='.$item['id']).'">';
			$html .= $item['name'].'</a>';
			$html .= '<span class = "product-category">'.$item['catName']->name.'</span>';
			$html .= '</td>';
			$html .= '<td class = "pg-price">'. Pago::get_instance('price')->format($item['price']).'</td>';
			$html .= '<td class = "pg-qty">'. $item['quantity'].'</td>';
			$html .= '</tr>';

			$counter++;

			}
		echo json_encode($html);
        exit();
	
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
		$model = JModelLegacy::getInstance( 'items', 'PagoModel' );
		$return = $model->sortItems($pks);

		if ($return)
		{	ob_clean();
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

}
?>
