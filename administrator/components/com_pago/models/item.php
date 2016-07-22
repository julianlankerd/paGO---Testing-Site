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


class PagoModelItem extends JModelLegacy
{
	var $_data;
	var $_order = array();

	function __construct()
	{
		parent::__construct();

		$array = JFactory::getApplication()->input->get( 'cid',  0, 'array' );
		$this->setId( (int) $array[0] );
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
		$this->_data = JTable::getInstance( 'item', 'table' );
		$this->_data->load( $this->_id );

		if ( $this->_data->id ) {
			//get our secondary cats
			$query = ' SELECT category_id FROM #__pago_categories_items '.
					'  WHERE item_id = '.$this->_id;

			$secondary_categories = $this->_getList( $query );

			$cats = array();

			if( is_array( $secondary_categories ) )
			foreach ( $secondary_categories as $cat ) {
				$cats[] = $cat->category_id;
			}

			if ( !empty( $cats ) ) {
				$this->_data->secondary_categories = $cats;
				$this->_data->secondary_category = $cats;
			}
		}

		return $this->_data;
	}

	function get_items()
	{
		$cids  = $this->getState( 'cid' );
		$items = array();

		if( !empty( $cids ) )
		foreach( $cids as $cid ){
			$this->setId( (int) $cid );
			$items[] = $this->getData();
		}

		return $items;
	}

	function copy()
	{
		$cids  = $this->getState( 'cid' );
		//$settings  = $this->getState( 'settings' );

		$where = 'items.id=' . array_shift( $cids );

		if ( !empty( $cids ) ) {
			foreach( $cids as $cid ){
				$where .= ' OR items.id=' . $cid;
			}
		}

		$query = "SELECT items.* FROM #__pago_items as items
								WHERE $where";
		$this->_db->setQuery( $query );
		$items = $this->_db->loadAssocList();

		$created_ids = array();

		foreach( $items as $item ){

			// get item meta data
			$query = "SELECT * FROM #__pago_meta_data as meta
								WHERE type = 'item' AND id = {$item['id']}";
			$this->_db->setQuery( $query );
			$meta = $this->_db->loadAssocList();

			$oldItemId = $item['id'];

			// get item category
			$query = "SELECT `category_id` FROM #__pago_categories_items as secondary_category
								WHERE  item_id = {$item['id']}";
			$this->_db->setQuery( $query );
			$secondary_category = $this->_db->loadResultArray();

			// get item images
			$query = "SELECT * FROM #__pago_files as images
								WHERE type = 'images' AND item_id = {$item['id']}";
			$this->_db->setQuery( $query );
			$images = $this->_db->loadAssocList();
			
			$item['id'] = 0;
			$item['name'] = $item['name'] . ' ' . JText::_('copy');
			$item['alias'] =  str_replace(" ", "-", $item['name']);
			$item['sku'] = $item['sku'] . ' ' . JText::_('copy');
			$item['published'] = 0;
			$item['meta'] = $meta[0];
			$item['images'] = $images;
			$item['secondary_category'] = $secondary_category;


			JFactory::getApplication()->input->post->set( 'params', $item );

			//var_dump(JFactory::getApplication()->input->post);
			//var_dump(JFactory::getApplication()->input->post->get( 'params',array(0),'array'));
			//exit();
			$created_ids[] = $this->store(true,false,$oldItemId);
		}
		Pago::get_instance( 'categories' )->clear_cache();
		
		$app = JFactory::getApplication();
		$app->Redirect("index.php?option=com_pago&view=items", JText::_("PAGO_ITEM_COPIED_SUCCESSFULLY"));
		//return $created_ids;
	}

	function store_item_attributes( $data )
	{
		$db = JFactory::getDBO();

		if(isset($data['custom_attribute'])){
			foreach ($data['custom_attribute'] as $attrId) {
				$query = "UPDATE #__pago_attr SET `attr_enable` = 1 WHERE 	id = {$attrId}";
				$this->_db->setQuery( $query );
				$this->_db->query();
			}
		}

		if(isset($data['custom_attribute_options'])){
			foreach ($data['custom_attribute_options'] as $optionId) {
				$query = "UPDATE #__pago_attr_opts SET `opt_enable` = 1 WHERE id = {$optionId}";
				$this->_db->setQuery( $query );
				$this->_db->query();
			}
		}
		return true;
	}

	function store_product_varations( $data ){

		if(isset($data['product_varation'])){
			foreach ($data['product_varation'] as $varationId) {
				$query = "UPDATE #__pago_product_varation SET `var_enable` = 1 WHERE id = {$varationId}";
				$this->_db->setQuery( $query );
				$this->_db->query();
			}
		}
		return true;		
	}
	/**
	* Store file information such as name, caption, and access
	*/
	function store_item_files( $data )
	{	
		$model = JModelLegacy::getInstance( 'file', 'PagoModel' );
		
		if ( isset( $data['images'] ) ) {
			$images = (array) $data['images'];

			foreach ( $images as $id => $file ) {
				if ( empty( $file ) ) {
					continue;
				}
				if ( !$model->store( array_merge( $file, array( 'id' => $id ) ) ) ) {
					JError::raiseWarning( 500, 'An error has occurred: ' . $model->getError() );
				}
			}
		}
		if ( isset( $data['downloadable'] ) ) {
			$downloadable = (array) $data['downloadable'];

			foreach ( $downloadable as $id => $file ) {
				if ( empty( $file ) ) {
					continue;
				}
				if ( !$model->store( array_merge( $file, array( 'id' => $id ) ) ) ) {
					JError::raiseWarning( 500, 'An error has occurred: ' . $model->getError() );
				}
			}
		}



		// Store ordering

		if ( isset( $data['images_ordering'] ) ) {
			$model->save_order( $data['images_ordering'], $data['id'] );
		}

		if ( isset( $data['downloads_ordering'] ) ) {
			$model->save_order( $data['downloads_ordering'], $data['id'] );
		}

		Pago::get_instance( 'categories' )->clear_cache();
	}
	function copy_item_files( $data , $copy_path )
	{
		if ( !isset( $data['images'] ) ) {
			return;
		}

		$images = (array) $data['images'];

		$model = JModelLegacy::getInstance( 'file', 'PagoModel' );

		foreach ( $images as $file ) {
			if ( empty( $file ) ) {
				continue;
			}
			if ( !$model->copy( $file, $data['id'], $copy_path)) {
				JError::raiseWarning( 500, 'An error has occurred: ' . $model->getError() );
			}
		}

		// Store ordering
		//$model->save_order( $data['images_ordering'], $data['id'] );

		Pago::get_instance( 'categories' )->clear_cache();	
	}

	function copy_item_attrs($newItemId, $oldId){

		$query =  'SELECT * FROM #__pago_attr WHERE for_item = ' . $oldId .' AND attr_enable=1' ;

		$this->_db->setQuery( $query );

		$oldItemAttrs = $this->_db->loadAssocList();

		$attributesIds = array();
		$optsIds = array();
		$variationsIds = array();
		if($oldItemAttrs){
			foreach ($oldItemAttrs as $oldItemAttr) {

				$oldAttrId = $oldItemAttr['id'];

				$oldItemAttr['id'] = 0;
				$oldItemAttr['for_item'] = $newItemId;		

				$query = "INSERT INTO #__pago_attr
				(`" .implode( '`,`',array_keys($oldItemAttr))."` ) VALUES ('". implode( "','", $oldItemAttr )."')";
				$this->_db->setQuery($query);
				$this->_db->query();

				$newAttrId = $this->_db->insertid();

				$attributesIds[$oldAttrId] = $newAttrId;

				if($newAttrId){
					$query =  'SELECT * FROM #__pago_attr_opts WHERE attr_id = '.$oldAttrId.' AND opt_enable=1' ;
					$this->_db->setQuery($query);
					
					$oldItemOpts= $this->_db->loadAssocList();


					foreach($oldItemOpts as $oldItemOpt){

						$oldOptId = $oldItemOpt['id']; 
						$oldItemOpt['id'] = 0;
						$oldItemOpt['attr_id'] = $newAttrId;		
						$oldItemOpt['for_item'] = $newItemId;		
						$cols=implode( "`,`",array_keys($oldItemOpt));
						$vals=implode( "','",$oldItemOpt);
						$query = "INSERT INTO #__pago_attr_opts ( `".$cols."` ) VALUES ('".$vals."')";

						$this->_db->setQuery($query);
						$this->_db->query();


						$newOptId =  $this->_db->insertid();
						$optsIds[$oldOptId] = $newOptId;
					}
				}

			}

			$query =  'SELECT * FROM #__pago_product_varation WHERE item_id = ' . $oldId .' AND var_enable=1' ;

			$this->_db->setQuery( $query );

			$oldItemVariations = $this->_db->loadAssocList();
			
			foreach($oldItemVariations as $oldItemVariation){
				$oldVariationId = $oldItemVariation['id']; 
				$oldItemVariation['id'] = 0;
				$oldItemVariation['item_id'] = $newItemId;
				
				$oldItemVariation['sku'] = $oldItemVariation['sku'].' copy';
				$skuExists = $this->checkVariationSku($oldItemVariation['id'],$oldItemVariation['sku']);
				if($skuExists){
					$i = 1;
					while (true) {
						$sku = $oldItemVariation['sku'].'('.$i.')';
						$checkNewSku = $this->checkVariationSku($oldItemVariation['id'], $sku);
						if(!$checkNewSku){
							break;
						}
						$i++;
					}
					$oldItemVariation['sku']=$sku;
				}
				
				
				$cols=implode( "`,`",array_keys($oldItemVariation));
				$vals=implode( "','",$oldItemVariation);
				$query = "INSERT INTO #__pago_product_varation ( `".$cols."` ) VALUES ('".$vals."')";

				
				$this->_db->setQuery($query);
				$this->_db->query();
				$newVariationId = $this->_db->insertid();
				$variationsIds[$oldVariationId]=$newVariationId;

			}
		}
	

		foreach($variationsIds as $oldVariationId => $newVariationId){	
			$query =  'SELECT * FROM #__pago_product_varation_rel WHERE varation_id = ' . $oldVariationId;

			$this->_db->setQuery( $query );

			$oldItemVariationRels = $this->_db->loadAssocList();
			if(count($oldItemVariationRels)>0){
				foreach($oldItemVariationRels as $oldItemVariationRel){

					$newVarationId=$variationsIds[$oldItemVariationRel['varation_id']];
					$newAttrId=$attributesIds[$oldItemVariationRel['attr_id']];
					$newOptId=$optsIds[$oldItemVariationRel['opt_id']];
					$query = "INSERT INTO #__pago_product_varation_rel (`varation_id`,`item_id`,`attr_id`,`opt_id`)
					VALUES ('".$newVarationId."','".$newItemId."','".$newAttrId."','".$newOptId."')";

					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}

			$old_var_path = $path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $oldVariationId .DIRECTORY_SEPARATOR;
			if ( JFolder::exists( $old_var_path ) ) {

				if(count(JFolder::files($old_var_path))>0){
					$new_var_path = $path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $newVarationId .DIRECTORY_SEPARATOR;
					
					if ( !JFolder::exists( $new_var_path ) ) {

						if ( !JFolder::create( $new_var_path ) ) {
							JError::raiseWarning( 100, 'Unable to move files. Please create this folder:'
								. $new_var_path );
						}

						$path = $new_var_path.DS.'index.html';
						$con = '<html />';

						JFile::write( $path, $con );
					}
					foreach(JFolder::files($old_var_path) as $image){

						$ret = JFile::copy( $old_var_path .DS. $image,
							$new_var_path .DS. $image );

						if ( $ret !== true ) {
							$errors[] = $ret;
						}
					}
				}
			}
		}
	}

	/**
	* Store item meta data
	* Stores data to #__pago_items_meta
	*/
	function store_item_meta( $data )
	{
		$meta = Pago::get_instance( 'meta' );

		foreach ( $data['meta'] as $key => $value ) {
			$meta->update( 'item', $data['id'], $key, $value );
		}

		Pago::get_instance( 'categories' )->clear_cache();
	}

	function store($copy = false, $new = false, $oldItemId = false)
	{
		$config = Pago::get_instance( 'config' )->get();
		$row = $this->getTable();

		$data = JFactory::getApplication()->input->post;


		$data = $data->get('params',array(0),'array');
		$id   = $data['id'];
		
		if(!$data['sku'] && $data['name'] != '')
		{
			$itemnm =  str_replace(" ", "-", strtolower($data['name']));
			$randomnm = rand(10000,99999);
			$data['sku'] = $itemnm . "-" . $randomnm;	
		}

		$skuExists = $this->checkItemsku($data['sku'], $id);
		
		if($skuExists && $copy){
			$i = 1;
			while (true) {
				$sku = $data['sku'].'('.$i.')';
				$checkNewSku = $this->checkItemsku($sku, $id);
				if(!$checkNewSku){
					break;
				}
				$i++;
			}
			$data['sku']=$sku;
		}

		if($skuExists)
		{	
			JError::raiseWarning( 500, JText::_('COM_PAGO_ITEM_SKU_ALREADY_EXISTS') );
			return false;
		}

		// Load current item
		$current = $this->getTable( 'item' );
		$current->load( $data['id'] );

		$domove = false;
		$create = false;

		if ( $id == 0 ) {
			$data['created'] = date( 'Y-m-d H:i:s', time() );
		}
		// Shipping method changes start
		if(isset($data['shipping_methods']) && $data['shipping_methods']!="" && $data['shipping_methods']!="0")
		{
			$shipping_method = implode(",", $data['shipping_methods']);
			$data['shipping_methods'] = $shipping_method;
		}
		else
		{
			$data['shipping_methods'] = "";
		}
		
		if(isset($data['type']) && $data['type'] == '2')
		{
			$data['free_shipping'] = '1';
		}

		// Shipping method changes end

		// Check to see if we need to move images folder
		if ( $data['id'] && $current->primary_category != $data['primary_category'] ) {
			jimport( 'joomla.filesystem.file' );
			jimport( 'joomla.filesystem.folder' );
			$errors = array();
			$path = JPATH_ROOT .DS.
				trim( $config->get( 'images_file_path', 'media' .DS. 'pago' .DS. 'items' ), DS );
			$prev_category = Pago::get_instance( 'categoriesi' )->get( $current->primary_category );
			$new_category  = Pago::get_instance( 'categoriesi' )->get( $data['primary_category'] );
			$prev_path     = $path .DS. JFilterOutput::stringURLSafe( $prev_category->id );
			$new_path      = $path .DS. JFilterOutput::stringURLSafe( $new_category->id );


			// Make sure new path exists
			if ( !JFolder::exists( $new_path ) ) {
			
				if ( !JFolder::create( $new_path ) ) {
					JError::raiseWarning( 100, 'Unable to move files. Please create this folder:'
						. $new_path );
				}
				// Create index.html file
				$path = $new_path.DS.'index.html';
				$con = '<html />';
				
				JFile::write( $path, $con );
			}

			$domove = true;
		}

		// Add plugin for additional save options
		$dispatcher = KDispatcher::getInstance();
		$dispatcher->trigger( 'backend_item_store_data', array( &$data ) );

		if ( $new ) {
			//create item for attach media image and set expiry date for remove if item not saved
			$timestamp = time() + 86400;
			$data['visibility'] = 0;
			$data['expiry_date'] = $timestamp;
			$data['primary_category'] = 1;
		} else {
			$data['visibility'] = 1;
		}

		if ( !$row->bind( $data ) ) {
			return JError::raiseWarning( 500, $row->getError() );
		}

		if(!$new){
			if ( !$row->check() ) {
				JError::raiseWarning( 500, $row->getError() );
				return false;
			}
		}

		if ( !$row->store() ) {
			return JError::raiseWarning( 500, $row->getError() );
		}

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('pago_products');
		$dispatcher->trigger( 'item_store_data', array( &$data) );

		// trigger for smart search
		if(!$new)
		{
			$ndispatcher	= JDispatcher::getInstance();
       		JPluginHelper::importPlugin('content');
       		
			$finderObject = (object) array(
				'id'			=> $row->id,
				'title'			=> $row->name,
				'alias'			=> $row->alias,
				'introtext'		=> $row->description,
				'fulltext'		=> $row->content,
				'text'			=> $row->name,
				'state'			=> $row->published,
				'list_price'	=> $row->price,
				'sale_price'	=> $row->discount_amount,
				'access'		=> 1
			);
			
			$results = $ndispatcher->trigger( 'onContentAfterSave', array( 'com_pago.items', &$finderObject,
			 	&$params, 0 ) );

		}

		$row_db = $row->get( '_db' );
		if( $insert_id = $row_db->insertid() ){
			$id = $row_db->insertid();
			$data['id'] = $id;
			$create = true;
		}
		else
		{
			$insert_id = $data['id'];
		}
		
		$this->store_item_meta( $data );
		if($copy){
			jimport( 'joomla.filesystem.file' );
			jimport( 'joomla.filesystem.folder' );
			$path = JPATH_ROOT .DS.
				trim( $config->get( 'images_file_path', 'media' .DS. 'pago' .DS. 'items' ), DS );
			$category   = Pago::get_instance( 'categoriesi' )->get( $data['primary_category'] );
			$copy_path  = $path .DS. JFilterOutput::stringURLSafe( $category->id );

			$this->copy_item_files( $data , $copy_path);

			$this->copy_item_attrs( $data['id'], $oldItemId);
		}else{
			$this->store_item_files( $data );
		}
		$this->store_item_attributes( $data );

		$this->store_product_varations( $data );

		$old_cats = array();
		$new_cats = array();
		$this->_db->setQuery( 'SELECT category_id FROM #__pago_categories_items WHERE'.
			' item_id = ' . $id );
		$old_cats = $this->_db->loadResultArray();

		$this->_db->setQuery( "DELETE FROM #__pago_categories_items
			WHERE item_id = {$id}" );
		$this->_db->query();


		if( is_array( $data['secondary_category'] ) ){
			array_unshift( $data['secondary_category'], $data['primary_category']);
			$data['secondary_category'] = array_unique( $data['secondary_category'] );
			$cat_values = array();
			$new_cats = array();
			foreach( $data['secondary_category'] as $cid ){
				$cat_values[] = '('.$cid.','.$id.')';
				$new_cats[] = $cid;
			}
		} else {
			$cat_values = array( '('. $data['primary_category'] .','. $id .')' );
			$new_cats = array( $data['primary_category'] );
		}

		$query = "INSERT INTO #__pago_categories_items
			( category_id, item_id ) VALUES ". implode( ',', $cat_values );


		$this->_db->setQuery($query);
		$this->_db->query();

		$cat_table = $this->getTable( 'categoriesi' );
		if($old_cats){
			$new_cats = array_merge( $old_cats, $new_cats );
		}
		$new_cats = array_unique( $new_cats );

		foreach( $new_cats as $single_cat ) {
			$cat_table->load( $single_cat );
			$cat_table->get_item_count();
			$cat_table->reset();
		}


		// Move images to new folder
		if ( $domove ) {
			$_images = $this->_getList( "SELECT `file_name`, `file_meta`
				FROM #__pago_files
					WHERE `item_id` = {$data['id']}" );
			foreach ( $_images as $_image ) {
				$_image->file_meta = PagoHelper::maybe_unserialize( $_image->file_meta );

				// Move original file
				$ret = JFile::move( $prev_path .DS. $_image->file_name,
					$new_path .DS. $_image->file_name );

				if ( $ret !== true ) {
					$errors[] = $ret;
				}

				// Do all of the smaller sizes
				if(is_array($_image->file_meta)){
					foreach ( (array) $_image->file_meta['sizes'] as $_size ) {
						$ret = JFile::move( $prev_path .DS. $_size['file'],
							$new_path .DS. $_size['file'] );

						if ( $ret !== true ) {
							$errors[] = $ret;
						}
					}
				}
			}

			if ( !empty( $errors ) ) {
				foreach ( $errors as $error ) {
					JError::raiseWarning( 100, $error );
				}
				return false;
			}
		}

		Pago::get_instance( 'categories' )->clear_cache();


		return $id;
	}
	function getItemName($id){
		$this->_db->setQuery( "SELECT `name` FROM #__pago_items
			WHERE id = {$id}" );
		$this->_db->query();
		$item = $this->_db->loadObject();

		if( !$item ) {
			return false;
		}
		return $item;
	}
	function getCategoryName($id)
	{
		$this->_db->setQuery( "SELECT `name` FROM #__pago_categoriesi
			WHERE id = {$id}" );
		$this->_db->query();
		$item = $this->_db->loadObject();

		if( !$item ) {
			return false;
		}
		return $item;
	}
	function getItem($id){
		$id = (int)$id;
		$this->_db->setQuery( "SELECT * FROM #__pago_items
			WHERE id = {$id}" );
		$this->_db->query();
		$item = $this->_db->loadObject();

		if( !$item ) {
			return false;
		}
		return $item;
	}

	function checkItemsku($sku, $id)
	{
		$where = "";
		if($id > 0)
		{
			$where = " and id != ".$id;
		}

		$return = true;

		$this->_db->setQuery( 'SELECT `id` FROM #__pago_product_varation WHERE `var_enable` = 1 AND `default` != 1  AND `sku`="'.$sku.'"');

		$this->_db->query();
		$checkVariationSku = $this->_db->loadObject();
		
		if( $checkVariationSku )  {
			return true;
		}

		$this->_db->setQuery( "SELECT `sku` FROM #__pago_items
			WHERE sku = '".$sku."'".$where. " AND visibility = 1" );
		
		$this->_db->query();
		$item = $this->_db->loadObject();

		if( $item ) {
			return true;	
		}

		return false;
	}


	function checkVariationSku($varId,$sku){

		$db  = JFactory::getDBO();
		
		$query = 'SELECT `id` FROM #__pago_items WHERE `visibility` = 1 AND `sku`="'.$sku.'"';
		$db->setQuery( $query );
		$checkItemSku = $db->loadObject();
		if($checkItemSku){
			return true;
		}

		$query = 'SELECT `id` FROM #__pago_product_varation WHERE `var_enable` = 1 AND id != '.$varId.'  AND `sku`="'.$sku.'"';
		$db->setQuery( $query );
		$checkVarationSku = $db->loadObject();
		if($checkVarationSku){
			return true;
		}

		return false;
	}
	
	function AssignCategory($data)
	{
            $config = Pago::get_instance( 'config' )->get();
            
            if(!$data['cid']){
                return false;
            }
		$cid = $data['cid'];
		$assign_primary_category = $data['assign_primary_category'];
		$cid = explode(',', $cid);
	
            if (count($cid) > 0)
            {
                jimport( 'joomla.filesystem.file' );
                jimport( 'joomla.filesystem.folder' );
                
                for ($j = 0; $j < count($cid); $j++)
                {
                    $itemCat = 'SELECT primary_category FROM #__pago_items WHERE `id` = ' . $cid[$j];
                    $this->_db->setQuery($itemCat);
                    $oldCat = $this->_db->loadResult();

                    if ($oldCat != $assign_primary_category) 
                    {

                        $query = 'UPDATE #__pago_items SET `primary_category` = "' . intval($assign_primary_category) . '" ' . ' WHERE `id` = "' . $cid[$j] . '"';
                        $this->_db->setQuery($query);

                        if (!$this->_db->query())
                        {
                                $this->setError($this->_db->getErrorMsg());

                                return false;
                        }
                        
                        $query = 'SELECT `item_id` FROM #__pago_categories_items WHERE `item_id` = "' . $cid[$j] . '" AND category_id="' . intval($assign_primary_category) . '"';
                        $this->_db->setQuery($query);
                        $relExist = $this->_db->loadObject();

                        if(!$relExist){
                            $query = 'UPDATE #__pago_categories_items SET `category_id` = "' . intval($assign_primary_category) . '" ' . ' WHERE `item_id` = "' . $cid[$j] . '" AND category_id="' . $oldCat . '"';
                            $this->_db->setQuery($query);
                            if (!$this->_db->query())
                            {
                                    $this->setError($this->_db->getErrorMsg());
                                    return false;
                            }
                        }
                        

                        $path = JPATH_ROOT .DS .
                                trim( $config->get( 'images_file_path', 'media' .DS. 'pago' .DS. 'items' ), DS );
                        $prev_path     = $path .DS. JFilterOutput::stringURLSafe( $oldCat );
                        $new_path      = $path .DS. JFilterOutput::stringURLSafe( $assign_primary_category );
                        
                        if ( !JFolder::exists( $new_path ) ) {
			
				if ( !JFolder::create( $new_path ) ) {
					JError::raiseWarning( 100, 'Unable to move files. Please create this folder:'
						. $new_path );
				}
				// Create index.html file
				$path = $new_path.DS.'index.html';
				$con = '<html />';
				
				JFile::write( $path, $con );
			}
                        
                        $_images = $this->_getList( "SELECT `file_name`, `file_meta`
                                                    FROM #__pago_files
                                                    WHERE `item_id` = {$cid[$j]}" );

                                
                        foreach ( $_images as $_image ) {
                            $_image->file_meta = PagoHelper::maybe_unserialize( $_image->file_meta );

                            // Move original file
                            $ret = JFile::move( $prev_path .DS. $_image->file_name,
                                    $new_path .DS. $_image->file_name );

                            if ( $ret !== true ) {
                                    $errors[] = $ret;
                            }

                            // Do all of the smaller sizes
                            if(is_array($_image->file_meta)){
                                    foreach ( (array) $_image->file_meta['sizes'] as $_size ) {
                                            $ret = JFile::move( $prev_path .DS. $_size['file'],
                                                    $new_path .DS. $_size['file'] );

                                            if ( $ret !== true ) {
                                                    $errors[] = $ret;
                                            }
                                    }
                            }

                            if ( !empty( $errors ) ) {
                                foreach ( $errors as $error ) {
                                        JError::raiseWarning( 100, $error );
                                }
                                return false;
                            }
                        }                        
                    }
		}
            }
            return true;
	}
        
	public function rate($userId,$itemId,$rating){
		$userId = (int)$userId;
		$itemId = (int)$itemId;
		$rating = (int)$rating;

		$query = 'SELECT id FROM #__pago_item_rating WHERE `user_id` = ' . $userId . ' AND `item_id` = '.$itemId;
		$this->_db->setQuery($query);
		$userRateed = $this->_db->loadResult();

		if($userRateed != NULL){
			return false;
		}else{
			$query = "INSERT INTO #__pago_item_rating
			( `user_id`, `item_id`, `rating`, `date` ) VALUES (".$userId.",".$itemId.",".$rating.",'". date( 'Y-m-d H:i:s', time() )."')";
			$this->_db->setQuery($query);
			$this->_db->query();
			$rate = $this->updateItemRating($itemId);
			return $rate;
		}
	}
	public function updateItemRating($itemId){
		$query = 'SELECT round(avg(`rating`)) FROM #__pago_item_rating WHERE `item_id` = '.$itemId;
		$this->_db->setQuery($query);
		$rate = $this->_db->loadResult();
		$query = "UPDATE #__pago_items SET `rating` = ".$rate." WHERE id = {$itemId}";
		$this->_db->setQuery( $query );
		$this->_db->query();
		return $rate;
	}

	public function getItemSecondaryCat($id){
		$id - (int)$id;
		$sql = 'SELECT category_id FROM #__pago_categories_items WHERE item_id = ' . $id;
		$this->_db->setQuery($sql);
		$result = $this->_db->loadAssocList();
		return $result;
	}

	public function getItemPrimaryCat($id){
		$id - (int)$id;
		$sql = 'SELECT primary_category FROM #__pago_items WHERE id = ' . $id;
		$this->_db->setQuery($sql);
		$result = $this->_db->loadResult();
		return $result;
	}

	public function get_search_items($word)
	{
		$and = '';
		$module = JModuleHelper::getModule('mod_pago_search');
		$searchParams = new JRegistry($module->params);
		$fuzzy_search_enable = (int) $searchParams['fuzzy_search_enable'];
		$itemNameQuery = '';
		$itemDescQuery = '';
		$itemContentQuery = '';
		$attrOptsQuery = '';
		$variationNmQuery = '';
		$and = '';

		if ($fuzzy_search_enable)
		{
			$words = array();
			$searchWord = $word;
		
			for ($i = 0; $i < strlen($searchWord); $i++)
			{
				// Insertions
				$words[] = substr($searchWord, 0, $i) . '_' . substr($searchWord, $i);

				// Deletions
				$words[] = substr($searchWord, 0, $i) . substr($searchWord, $i + 1);

				// Substitutions
				$words[] = substr($searchWord, 0, $i) . '_' . substr($searchWord, $i + 1);
			}

			// Last insertion
			$words[] = $searchWord . '_';

			if (count($words) > 0)
			{
				for ($g = 0; $g < count($words); $g++)
				{
					$varLimit = count($words) - 1;
					$itemNameQuery .= "i.name LIKE '%{$words[$g]}%' OR ";
					$itemDescQuery .= "i.description LIKE '%{$words[$g]}%' OR ";
					$itemContentQuery .= "i.content LIKE '%{$words[$g]}%' OR ";
				
					if ($g < $varLimit)
					{
						$variationNmQuery .= "v.name LIKE '%{$words[$g]}%' OR ";
					}
					else
					{
						$variationNmQuery .= "v.name LIKE '%{$words[$g]}%' ";
					}
					
					$attrOptsQuery .= "AND opts.name LIKE '%{$words[$g]}%' !=0 ";
				}
			} 
		}
		else
		{
			$itemNameQuery .= "i.name LIKE '%{$word}%' OR ";
			$itemDescQuery .= "i.description LIKE '%{$word}%' OR ";
			$itemContentQuery .= "i.content LIKE '%{$word}%' OR ";
			$variationNmQuery .= "v.name LIKE '%{$word}%' ";
			$attrOptsQuery .= "AND opts.name LIKE '%{$word}%' !=0 ";
  		}
		
		$and .= $itemNameQuery . $itemDescQuery . $itemContentQuery . "(" . $variationNmQuery . " 
				AND v.published = 1  
				AND v.var_enable = 1
			)OR
			(SELECT count(*) FROM #__pago_product_varation_rel as rel LEFT JOIN #__pago_attr_opts as opts ON rel.opt_id = opts.id 
				WHERE i.id = rel.item_id
				AND opts.opt_enable = 1
				AND opts.published = 1
				AND v.published = 1
				AND v.var_enable = 1
				" . $attrOptsQuery . " 
			 )" ;
		 
		$query = "SELECT DISTINCT i.id as value,i.name as label FROM #__pago_items AS i LEFT JOIN #__pago_product_varation as v ON v.item_id=i.id WHERE i.visibility != 0 AND i.published = 1 AND (" . $and . ")";
        $this->_db->setQuery($query);
        $result = $this->_db->loadObjectList();
        
        return $result;
	}
	
	public function getFileDetails($id)
	{
		$query = "SELECT f.file_name,i.primary_category FROM #__pago_files AS f LEFT JOIN  #__pago_items AS i ON f.item_id=i.id WHERE f.id=" . $id;
		$this->_db->setQuery( $query );
		
		return $this->_db->loadAssocList();
	}
}
