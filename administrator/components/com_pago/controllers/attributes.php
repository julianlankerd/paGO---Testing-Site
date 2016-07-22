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
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');

class PagoControllerAttributes extends PagoController
{
	/**
	* Custom Constructor
	*/
	private $_view = 'attributes';

	function __construct( $default = array() )
	{
		parent::__construct( $default );

		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'unpublish_var', 'publish_var' );
		$this->registerTask( 'not_selected', 'preselected' );
	}

	function display( $cachable = false, $urlparams = false )
	{
		parent::display( $cachable = false, $urlparams = false );
	}

	function saveorder()
	{
		$cid = JFactory::getApplication()->input->get( 'ids', array(), 'post', 'array' );
		$limitstart = JFactory::getApplication()->input->get( 'limitstart', '', '' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');
		$newOrders = $model->saveOrder( $cid,$limitstart );

		echo json_encode($newOrders);
		exit();
	}

	function edit()
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set('hidemainmenu', 1);

		$jinput->set( 'layout', 'form' );
		parent::display();
	}

	function add()
	{
		$model = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
		$id = $model->store(true);

		$this->setRedirect( 'index.php?option=com_pago&view=attributes&task=edit&cid[]='.$id);
	}

	function save()
	{
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		if ($model->store()) {
			$msg = JText::_( 'Attribute Saved!' );
		} else {
			$msg = JText::_( 'Error Saving Attribute' );
		}
		$link = 'index.php?option=com_pago&view='. $this->_view;
		$this->setRedirect($link, $msg);
	}

	function apply()
	{
		$jinput = JFactory::getApplication()->input;
		$model = JModelLegacy::getInstance('Attribute','PagoModel');
		$model->store();

		$id = $model->item_id;

		if (!$id) {
			$id = $jinput->get('id',  0, 'int');
		}

		$msg = JText::_( 'Attribute Saved!' );
		$link = 'index.php?option=com_pago&view='. $this->_view .'&controller='. $this->_view
			.'&task=edit&cid[]='. $id;

		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = JModelLegacy::getInstance('Attributes','PagoModel');


		$model->setState('cid', JFactory::getApplication()->input->get( 'cid', array(), 'post', 'array' ) );

		if ( $model->remove() ) {
			$msg 	= JText::_('Attribute Removed');
		} else {
			$msg 	= JText::_( 'PAGO_ATTRIBUTES_CANNOT_DELETE' );
		}
		$link 	= 'index.php?option=com_pago&view='. $this->_view;
		$this->setRedirect($link, $msg);
	}

	function showOptForm()
	{
		$input = JFactory::getApplication()->input;
		$num = $input->get( 'num', '', '' );
		$type = $input->get( 'type', '', '' );
		$attrId = $input->get( 'attrId', '', '' );

		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$data = (object) $input->getArray();

		$data->num = $num;
		$data->type = $type;
		$data->attr_id = $attrId;

		$optionId = $model->store_attribute_option($data);
		$data->id = $optionId;


		$addForm = $model->get_attribute_form($data,true);

		echo $addForm;
		exit();
	}
	function addOptValue()
	{
		$data = new stdClass;
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$data->id = JFactory::getApplication()->input->get( 'id', '', '' );
		$data->num = JFactory::getApplication()->input->get( 'num', '', '' );
		$data->type = JFactory::getApplication()->input->get( 'opt_type', '', '' );

		$data->name = JFactory::getApplication()->input->get( 'name', '', '' );
		$data->ordering = JFactory::getApplication()->input->get( 'ordering', '', '' );


		if($data->type == 0){
			$data->color = JFactory::getApplication()->input->get( 'color', '', '' );
		}
		if($data->type == 1){
			$data->size = JFactory::getApplication()->input->get( 'size', '', '' );
			$data->size_type = JFactory::getApplication()->input->get( 'size_type', '', '' );
		}
		
		$model->store_attribute_option($data,false,true);
		
		$form = $model->get_attribute_html($data);

		echo $form;
		exit();
	}

	function addCustomOption()
	{
		$data = new stdClass;
		$model = JModelLegacy::getInstance('Attribute','PagoModel');
		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$attrId = JFactory::getApplication()->input->get( 'attrId', '', '' );
		$status = JFactory::getApplication()->input->get( 'status', '', '' );
		//$preselected = JFactory::getApplication()->input->get( 'preselected', '', '' );

		$data->id = JFactory::getApplication()->input->get( 'id', '', '' );
		$data->num = JFactory::getApplication()->input->get( 'num', '', '' );
		$data->type = JFactory::getApplication()->input->get( 'opt_type', '', '' );

		$data->name = JFactory::getApplication()->input->get( 'name', '', '' );
		$data->ordering = JFactory::getApplication()->input->get( 'ordering', '', '' );
		$data->published = JFactory::getApplication()->input->get( 'published', '', '' );
		//$data->preselected = $preselected;
		if($data->type == 0){
			$data->color = JFactory::getApplication()->input->get( 'color', '', '' );
		}
		if($data->type == 1){
			$data->size = JFactory::getApplication()->input->get( 'size', '', '' );
			$data->size_type = JFactory::getApplication()->input->get( 'size_type', '', '' );
		}
		$data->for_item = $itemId;

		$model->store_attribute_option($data,false,true);//data//item_id//saveDataOrJustCreate

		// get Item
		$db = JFactory::getDBO();
		$query = 'SELECT `id`,`primary_category`,`price` FROM #__pago_items WHERE id = ' . $itemId;
		$db->setQuery( $query );
		$item = $db->loadObject();
		///

		$returnDate = new stdClass;
		$returnDate->id = $attrId;
		$returnDate->type = $data->type;
		$returnDate->for_item = $itemId;
		$options = array($data);
		$returnDate->options = $options;

		$attributeTbody = $model->get_custom_attribute_tbody($returnDate,$item);

		$return['attributeTbody'] = $attributeTbody;

		if($status == 'addOption'){
			$return['status'] = 'success';
		}else{
			$return['status'] = 'editOption';
		}

		$return = json_encode($return);

		echo $return;
		exit();
	}

	function addProductVaration()
	{
		$data = new stdClass;
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$id = JFactory::getApplication()->input->get( 'id', '', '' );

		$data->id = $id;
		$data->item_id = $itemId;
		$data->name = JFactory::getApplication()->input->get( 'name', '', '' );
		$data->price_type = JFactory::getApplication()->input->get( 'price_type', '', '' );
		$data->price = JFactory::getApplication()->input->get( 'price', '', '' );
		$data->sku = JFactory::getApplication()->input->get( 'sku', '', '' );
		$data->qty_limit = JFactory::getApplication()->input->get( 'qty_limit', '', '' );
		$data->qty = JFactory::getApplication()->input->get( 'qty', '', '' );
		$data->published = JFactory::getApplication()->input->get( 'published', '', '' );
		$data->default = JFactory::getApplication()->input->get( 'def', '', '' );

		$find = array("'",'"');
		$data->name=str_replace($find,"",$data->name);
		$data->price=str_replace($find,"",$data->price);
		$data->sku=str_replace($find,"",$data->sku);

		$varation_attributes = JFactory::getApplication()->input->get( 'variation_attr', '', '' );

		if(!$model->check_varation_exist($id,$varation_attributes,$itemId)){
			$return['condition'] = "error";
			$return['errorMessage'] = JText::_( 'PAGO_PRODUCT_VARAION_HAVE_SAME_VARATION' );
			$return = json_encode($return);
			echo $return;
			exit();
		}

		$db = JFactory::getDBO();
		$return['removeVarationId'] = 'false';
		
		if($data->default == 1){
			$query = 'SELECT `id` FROM #__pago_product_varation WHERE `item_id` = '.$itemId.' AND `default` = 1';
			$db->setQuery( $query );
			$varData = $db->loadObject();
			if($varData && $varData->id != $id){

				if ( $model->removeVaration($varData->id,'id') ) {
					$return['removeVarationId'] = $varData->id;
				}else{
					echo "error, can't delete old varation";
					exit();
				}				
			}
		}
		$model->store_product_varation($data,true);//data//saveDataOrJustCreate

		$model->store_product_varation_attributes($id,$varation_attributes,$itemId);
		// get Item
		
		$query = 'SELECT `id`,`primary_category`,`price` FROM #__pago_items WHERE id = ' . $itemId;
		$db->setQuery( $query );
		$item = $db->loadObject();
		///
		
		$varationHtml = $model->get_varation_html($id);

		$return['productVarationTr'] = $varationHtml;
		$return['condition'] = "success";

		$return = json_encode($return);

		echo $return;
		exit();
	}
	function calcVarationPrice(){
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$varation_attributes = JFactory::getApplication()->input->get( 'variation_attr', '', '' );

		$price = $model->calcVarationPrice($itemId,$varation_attributes);

		echo $price;
		exit();
	}
	function checkAttrRequiredAlert(){
		$model = JModelLegacy::getInstance('Attributes','PagoModel');

		$attrId = JFactory::getApplication()->input->get( 'attrId', '', '' );
		
		if ( $model->checkAttrRequiredAlert($attrId) ) {
			echo "success";
			exit();
		}
	}
	function checkCustomAttrReq()
	{
		$model = JModelLegacy::getInstance('Attributes','PagoModel');

		$attrId = JFactory::getApplication()->input->get( 'attrId', '', '' );
		
		echo json_encode($model->checkCustomAttrReq($attrId), true);
		exit();
	}
	function removeCustomAttr()
	{
		$model = JModelLegacy::getInstance('Attributes','PagoModel');

		$attrId = JFactory::getApplication()->input->get( 'attrId', '', '' );

		$model->setState('cid', array($attrId) );

		if ( $model->remove() ) {
			$return['status'] = "success";
			echo json_encode($return);
			exit();
		}
	}
	function removeVaration()
	{
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$varationId = JFactory::getApplication()->input->get( 'varationId', '', '' );

		if ( $model->removeVaration($varationId,'id') ) {
			echo "success";
			exit();
		}
	}
	
	function checkCustomOptVar()
	{
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$optId = JFactory::getApplication()->input->get( 'optId', '', '' );

		echo json_encode($model->checkOptVar($optId), true);
		exit();
	}
	
	function removeCustomOpt()
	{
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$optId = JFactory::getApplication()->input->get( 'optId', '', '' );

		if ( $model->deleteOpt($optId) ) {
			$return['status'] = "success";
			echo json_encode($return);
			exit();
		}
	}

	function setDefault(){
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$optId = JFactory::getApplication()->input->get( 'optId', '', '' );

		$model->setDefault($optId);// return new default id, old default id;

		exit();
	}

	function assign_item()
	{
		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );
		$jinput = JFactory::getApplication()->input;
		$word = $jinput->getString('q', null);
		$table = JTable::getInstance( 'Items', 'Table' );
		$items = $table->search_item($word);
		echo json_encode($items);
        exit();
	}

	function show_assign_items(){
		$jinput = JFactory::getApplication()->input;
		$attrId = $jinput->get( 'attrId' );

		$model = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
		$html = $model->assign_item_html($attrId);

		echo json_encode($html);
        exit();
	}

	function show_assign_category(){
		$jinput = JFactory::getApplication()->input;
		$attrId = $jinput->get( 'attrId' );

		$model = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
		$html = $model->assign_category_html($attrId);

		echo json_encode($html);
        exit();
	}

	function ajaxImage(){

		$action = JFactory::getApplication()->input->get( 'action', '', '' );
		$imageType = JFactory::getApplication()->input->get( 'imageType', '', '' );

		switch ($action) {
			case 'images':
					$folder = JFactory::getApplication()->input->get( 'folder', '', '' );
					$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. $imageType .DIRECTORY_SEPARATOR. $folder .DIRECTORY_SEPARATOR;

					$files="";
					if(!file_exists ($path)){
						mkdir($path, 0755, true);										
					}
					if ($handle = opendir($path)){
						$pathinfo = pathinfo($path);
						$folder_name = $pathinfo['basename'];
						while (false !== ($entry = readdir($handle))) {
							if($entry!="." && $entry!=".." && !is_dir($path.DIRECTORY_SEPARATOR .$entry) && $entry!='fields.ini' && $entry!='index.html' && !strpos($entry, "-")){
								$files[] = $entry;
							}
						}
					}
					$files = json_encode($files);
					echo $files;
					exit();
				break;
			case 'images_all':
					$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. $imageType .DIRECTORY_SEPARATOR;

					$files="";
					$folders ="";

					if ($handle = opendir($path)){
						$pathinfo = pathinfo($path);
						$folder_name = $pathinfo['basename'];

						while (false !== ($entry = readdir($handle))) {
							if($entry!="." && $entry!=".." && is_dir($path.DIRECTORY_SEPARATOR .$entry)) $folders[] = $entry;

						}
						closedir($handle);

						if($folders){
							foreach($folders as $f){
								if ($handle = opendir($path.DIRECTORY_SEPARATOR .$f)){
									while (false !== ($entry = readdir($handle))) {
										if($entry!="." && $entry!=".." && !is_dir($path.DIRECTORY_SEPARATOR .$entry) && $entry!='fields.ini' && $entry!='index.html' && !strpos($entry, "-")){
											$files[$f][] = $entry;	
										}
									}
								}
							}
						}
					}
					$files = json_encode($files);
					echo $files;
					exit();
				break;
			case "load_data":

				$folder = JFactory::getApplication()->input->get( 'folder', '', '' );
				$img = JFactory::getApplication()->input->get( 'img', '', '' );

				$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. $imageType .DIRECTORY_SEPARATOR. $folder;

				$fields_file = $path. DIRECTORY_SEPARATOR . 'fields.ini';


				if(!file_exists($fields_file)){
					echo '-1';
					exit();
				}
				$content = file_get_contents($fields_file);
				if($content==''){
					echo '-1';
					exit();
				}
				$content = json_decode($content,true);
				if(!isset($content[$img])){
					echo '-1';
					exit();
				}
				echo json_encode($content[$img]);
				exit();
			break;
			case "save_data":
				$folder = JFactory::getApplication()->input->get( 'folder', '', '' );
				$img = JFactory::getApplication()->input->get( 'img', '', '' );
				$title = JFactory::getApplication()->input->get( 'title', '', '' );
				$alt = JFactory::getApplication()->input->get( 'alt', '', '' );
				$desc = JFactory::getApplication()->input->get( 'desc', '', '' );
				$imageType = JFactory::getApplication()->input->get( 'imageType', '', '' );

				$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. $imageType .DIRECTORY_SEPARATOR. $folder;

				$fields_file = $path. DIRECTORY_SEPARATOR . 'fields.ini';

				if(!file_exists($fields_file)){
					file_put_contents($fields_file,"");
				}
				$content = file_get_contents($fields_file);
				if($content!='')
					$content = json_decode($content,true);
				$content[$img]['title'] = $title;
				$content[$img]['alt'] = $alt;
				$content[$img]['description'] = $desc;
				$content = json_encode($content);
				echo file_put_contents($fields_file,$content);
				exit();
			break;
			case "delete_data":
				$folder = JFactory::getApplication()->input->get( 'folder', '', '' );
				$img = JFactory::getApplication()->input->get( 'img', '', '' );

				$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. $imageType .DIRECTORY_SEPARATOR. $folder;

				$fields_file = $path. DIRECTORY_SEPARATOR . 'fields.ini';

				if(!file_exists($fields_file)){
					file_put_contents($fields_file,"");
				}
				$content = file_get_contents($fields_file);
				if($content!='')
					$content = json_decode($content,true);

				if ( isset( $content[$img] ) ) {
					unset($content[$img]);
				}

				if(unlink($path. DIRECTORY_SEPARATOR . $img)){
					$ext = explode('.', $img);
					$filename = $ext[0];
					if($filename){
						foreach (glob($path. DIRECTORY_SEPARATOR . $filename ."-*") as $removeFile)
						{
						    unlink ($removeFile);
						}
					}
				}

				$content = json_encode($content);
				echo file_put_contents($fields_file,$content);
				exit();
			break;
		}
	}
	function publish()
	{
		$db  = JFactory::getDBO();
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->post->get( 'cid', array(0), 'array' );
		$async = $jinput->getInt( 'async', '0' );

		JArrayHelper::toInteger( $cid, array(0) );
		$publish = ( $this->getTask() == 'publish' ? 1 : 0 );

		if ( count( $cid ) < 1 ) {
			$action = $publish ? JText::_( 'publish' ) : JText::_( 'unpublish' );
			JError::raiseError(500, JText::_( 'Unable to '.$action ) );
		}

		$cid = implode( ',', $cid );

		$query = 'UPDATE #__pago_attr_opts SET published = ' . (int) $publish
			. ' WHERE id = ' .$cid;

		$db->setQuery( $query );
		if ( !$db->query() ) {
			JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
		}

		if ( $async ) {
			// Instantiate an article table object
			$row = JTable::getInstance( 'attribute_options', 'Table' );
			$row->load( (int) $cid);

			echo PagoHelper::published( $row, 0, 'publish.png',  'unpublish.png',
				'', ' class="publish-attr-buttons" rel="' .$row->id. '"' );
			jexit();
		} else {
			$this->setRedirect( "index.php?option=pago&view=items", $msg );
		}
	}
	function publish_var()
	{
		$db  = JFactory::getDBO();
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->post->get( 'cid', array(0), 'array');
		$async = $jinput->getInt( 'async', '0' );

		JArrayHelper::toInteger( $cid, array(0) );
		
		$publish = ( $this->getTask() == 'publish_var' ? 1 : 0 );

		if ( count( $cid ) < 1 ) {
			$action = $publish ? JText::_( 'publish' ) : JText::_( 'unpublish' );
			JFactory::getApplication()->enqueueMessage('Unable to '.$action , 'error');
		}

		$cid = implode( ',', $cid );

		$query = 'UPDATE #__pago_product_varation SET published = ' . (int) $publish
			. ' WHERE id = ' .$cid;

		$db->setQuery( $query );
		if ( !$db->query() ) {
			JFactory::getApplication()->enqueueMessage($db->getErrorMsg() , 'error');
		}

		if ( $async ) {
			// Instantiate an article table object
			$row = JTable::getInstance( 'product_varation', 'Table' );
			$row->load( (int) $cid);

			echo PagoHelper::published( $row, 0, 'publish.png',  'unpublish.png',
				'', ' class="publish-varation-buttons" rel="' .$row->id. '"' );
			jexit();
		}
	}
	function preselected()
	{
		$db  = JFactory::getDBO();
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->post->get( 'cid', array(0), 'array');
		$async = $jinput->getInt( 'async', '0' );

		JArrayHelper::toInteger( $cid, array(0) );
		//var_dump( $this->getTask() );
		$preselected = ( $this->getTask() == 'preselected' ? 1 : 0 );
		// var_dump($preselected);
		// exit();
		
		if (count( $cid ) < 1 ) {
			$action = $preselected ? 'preselected' : 'not_selected';
			JFactory::getApplication()->enqueueMessage('Unable to ' . $action, 'error');
		}

		$cid = implode( ',', $cid );


		$query = 'UPDATE #__pago_product_varation SET preselected = ' . (int) $preselected
			. ' WHERE id = ' .$cid;

		$db->setQuery( $query );
		if ( !$db->query() ) {
			JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
		}else{
			$query = 'SELECT `item_id` FROM #__pago_product_varation WHERE id = ' . $cid;
			$db->setQuery( $query );
			$itemId = $db->loadObject();
			if($itemId){
				$query = 'UPDATE #__pago_product_varation SET preselected = ' . (int) 0
			. ' WHERE item_id = '.$itemId->item_id.' AND id != ' .$cid;

				$db->setQuery( $query );
				$db->query();
			}
		}
		if ( $async ) {
			// Instantiate an article table object
			$row = JTable::getInstance( 'product_varation', 'Table' );
			$row->load( (int) $cid);

			echo PagoHelper::preselected( $row, 0, 'publish.png',  'unpublish.png',
				'', ' class="preselected-varation-buttons" rel="' .$row->id. '"' );
			jexit();
		}
	}
	function getItemInfo(){
		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$itemModel = JModelLegacy::getInstance('Item','PagoModel');
		$itemModel->setId($itemId);
		$item = $itemModel->getData();

		Pago::load_helpers( 'imagehandler' );
		
		$images = PagoImageHandlerHelper::get_item_files( $item->id, true, array( 'images' ) );
		if($images){
			$image = PagoImageHandlerHelper::get_image_from_object( $images[0], 'medium', false );
		}else{
			$image = false;	
		}
		$item->image = $image;
		$return = json_encode($item);
		echo $return;
		exit();
	}
	function checkItemDefaultVar(){
		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$varId = JFactory::getApplication()->input->get( 'varId', '', '' );

		$db  = JFactory::getDBO();
		$query = 'SELECT `id` FROM #__pago_product_varation WHERE `item_id` = '.$itemId.' AND `default` = 1';
		$db->setQuery( $query );
		$data = $db->loadObject();
		if($data && $data->id != $varId){
			$result['status'] = "wrong";
			$result['message'] = JText::_( 'PAGO_CUSTOM_ATTRIBUTES_OTHER_DEFAULT' );
		}else{
			$result['status'] = "success";
		}
		$return = json_encode($result);
		echo $return;
		exit();
	}


	function checkItemSku(){
		$model = JModelLegacy::getInstance('Item','PagoModel');
		$varId = JFactory::getApplication()->input->get( 'varId', '', '' );
		$sku = JFactory::getApplication()->input->get( 'sku', '', '' );

		$return = array();

		$return['status'] = 'success';
		$result = $model->checkVariationSku($varId,$sku);
		if($result==true){
			$return['status'] = 'wrong';
			$return['message'] = JText::_( 'COM_PAGO_ITEM_SKU_ALREADY_EXISTS' );
		}
		
		$return = json_encode($return);
		echo $return;
		exit();
	}

	function publishAttr()
	{
		$attrId = JFactory::getApplication()->input->get( 'attrId', '', '' );
		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );

		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$html = $model->changeAttrPublish($attrId,$itemId);

		echo $html;
		exit();
	}
	function publishedAttrOption()
	{
		$optionId = JFactory::getApplication()->input->get( 'optionId', '', '' );
		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );

		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$html = $model->changeAttrOptionPublish($optionId,$itemId);

		echo $html;
		exit();
	}
	function showAddCutsomAttr(){
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$attrId = JFactory::getApplication()->input->get( 'attrId', '', '' );

		if(!$attrId){
			$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
			$form = $model->get_custom_attribute_html($itemId,false);
		}else{
			$db  = JFactory::getDBO();
			$query = 'SELECT * FROM #__pago_attr WHERE id = ' . $attrId;
			$db->setQuery( $query );
			$data = $db->loadObject();
			$form = $model->get_custom_attribute_html($data->for_item,$data);
		}

		echo $form;
		exit();
	}
	function showAddProductVaration(){
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$varationId = JFactory::getApplication()->input->get( 'varationId', '', '' );

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$status = JFactory::getApplication()->input->get( 'status', '', '' );

		$attributes = $model->get_custom_attributes($itemId);

		if(!$varationId){
			$data = new stdClass;

			$data->published = 1;
			$data->item_id = $itemId;

			$newVarationId = $model->store_product_varation($data);
			$data->id = $newVarationId;

			$form = $model->get_product_varation_html($itemId,$data,$attributes,$status);//itemId,Data,new,add

			$return['varationForm'] = $form;
			$return['varationId'] = $newVarationId;
		}else{
			$varation = $model->get_product_varations_by_id($varationId);
			$form = $model->get_product_varation_html($itemId,$varation,$attributes,$status);//itemId,Data,new,edit
			$return['varationForm'] = $form;
		}

		$return = json_encode($return);
		echo $return;
		exit();
	}
	function showAddCutsomAttrValue(){
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$attrId = JFactory::getApplication()->input->get( 'attrId', '', '' );
		$attrType = JFactory::getApplication()->input->get( 'attrType', '', '' );
		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$num = JFactory::getApplication()->input->get( 'num', '', '' );

		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$data = new stdClass;

		$data->num = $num;
		$data->type = $attrType;
		$data->attr_id = $attrId;
		$data->ordering = $num;
		$data->published = 1;// default 1
		//$data->preselected = 0;

		$optionId = $model->store_attribute_option($data,$itemId);
		$data->id = $optionId;

		$addForm = $model->get_custom_attribute_value_html($data,$itemId,false);

		$return['attributeValueHtml'] = $addForm;
		$return['attributeValueId'] = $optionId;
		$return['status'] = 'showNewValue';

		$return = json_encode($return);
		echo $return;
		exit();
	}
	function showEditCutsomAttrValue(){
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$optionId = JFactory::getApplication()->input->get( 'optionId', '', '' );
		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );

		$db  = JFactory::getDBO();
		$query = 'SELECT * FROM #__pago_attr_opts WHERE id = ' . $optionId;
		$db->setQuery( $query );
		$data = $db->loadObject();

		$addForm = $model->get_custom_attribute_value_html($data,$itemId,true);

		$return['attributeValueHtml'] = $addForm;
		$return['attributeValueId'] = $optionId;
		$return['status'] = 'showEditValue';

		$return = json_encode($return);
		echo $return;
		exit();
	}
	function addCustomAttr(){
		$attrId = JFactory::getApplication()->input->get( 'edit_id', '', '' );

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$attrType = JFactory::getApplication()->input->get( 'attrType', '', '' );
		$name = JFactory::getApplication()->input->get( 'name', '', '' );
		$showfront = JFactory::getApplication()->input->get( 'showfront', '', '' );
		$display_type = JFactory::getApplication()->input->get( 'display_type', '', '' );
		$required = JFactory::getApplication()->input->get( 'required', '', '' );

		$data = array();
		$data['for_item'] = $itemId;
		$data['type'] = $attrType;
		$data['name'] = $name;
		$data['showfront'] = $showfront;
		$data['display_type'] = $display_type;
		$data['required'] = $required;

		$model = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );

		$new = false;
		if($attrId){
			$data['id'] = $attrId;
			$id = $model->storeCustom($data,true);//edit
		}else{
			$new = true;
			$id = $model->storeCustom($data,false);//add
		}
		
		if($required == '1'){
			$model->addAutoVarantion($attrId, $itemId);
		}

		if($new){
			$data = new stdClass();
			$data->name = $name;
			$data->type = $attrType;
			$data->id = $id;
			$data->for_item = $itemId;

			$attributeThead = $model->get_custom_attribute_thead($data,$itemId,true);
			$attributeHtml = '<table id="attr_table_'.$data->id.'" class="pg-table pg-custom-attr-table pg-repeated-rows">';
				$attributeHtml .= $attributeThead;
			$attributeHtml .= '<tbody></tbody>';
			$attributeHtml .= '</table>';

			$return['attributeHtml'] = $attributeHtml;
			$return['attributeId'] = $id;
			$return['status'] = 'addNew';

			//$attr_data = $model->get_custom_attributes($itemId);
			
			$attr_title=$model->get_custom_attribute_title($data,$itemId);
			
			$return['attributeTitle'] = $attr_title;
		}else{
			$return['status'] = 'edit';
			$return['attributeName'] = $name;
			$return['attributeId'] = $id;
		}

		$return = json_encode($return);
		echo $return;
		exit();
	}

	function uploadfile(){

		Pago::load_helpers( 'imagehandler' );

		// Set the uplaod directory
		$uploadDir  = $_GET['folder'];
		$JPATH_ROOT = $_GET['JPATH_ROOT'];
		$imageType  = $_GET['imageType'];

		// Set the allowed file extensions
		$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions

		//$verifyToken = md5('unique_salt' . $_POST['timestamp']);

		$path = $JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. $imageType;


		//$uploadDir
		if (!empty($_FILES)) {
			$tempFile   = $_FILES['Filedata']['tmp_name'];
			$filename = $_FILES['Filedata']['name'];

			//generate image name
			$ext = end(explode('.', $filename));
		    $ext = substr(strrchr($filename, '.'), 1);
		    $ext = substr($filename, strrpos($filename, '.') + 1);
		    $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $filename);

			$arr = explode(' ',microtime());
			$newfilename = $arr[0] + $arr[1]+rand(1,1000);
			$newfilename =	str_replace('.','',$newfilename);
			$newfilename = $newfilename.".".$ext;
			///////
			
			$newfilename = strtolower($newfilename);
			$targetFile = $path . DIRECTORY_SEPARATOR . $uploadDir . DIRECTORY_SEPARATOR . $newfilename;

			if (!file_exists($path . DIRECTORY_SEPARATOR . $uploadDir)) {
		    	mkdir($path . DIRECTORY_SEPARATOR . $uploadDir, 0755, true);
			}

			// Validate the filetype
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			if (in_array(strtolower($fileParts['extension']), $fileTypes)) {
				
				// Save the file
				if(move_uploaded_file($tempFile,$targetFile)){
					PagoImageHandlerHelper::generate_attribute_image($targetFile);
					echo 1;
				}else{
					echo 'Invalid file upload.';
				}

			} else {
				// The file type wasn't allowed
				echo 'Invalid file type.';
			}
		}
	}
	
	function delete_attr_opt()
	{
		$optId = JFactory::getApplication()->input->get( 'optId', '', '' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');
		$model->deleteAttributeOption($optId);
		exit();
	}
	
	function attributeOrdering()
	{
		$model = $this->getModel('Attributes', 'PagoModel');
		$post = JFactory::getApplication()->input->getArray($_POST);		
		$exploded = explode("drag-item-", $post['id']);
		$itemIDs = array_filter($exploded);	
		$model->sortAttributes($itemIDs);

	}
	
	function attributeOptionsOrdering()
	{
		$model = $this->getModel('Attributes', 'PagoModel');
		$post = JFactory::getApplication()->input->getArray($_POST);
		$exploded = explode(",", $post['id']);
		$itemIDs = array_filter($exploded);	
		$model->sortAttributesOptions($itemIDs);

	}



}
