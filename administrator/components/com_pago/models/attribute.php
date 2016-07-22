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


class PagoModelAttribute extends JModelLegacy
{
	private $_data;
	private $_order  = array();

	function __construct(){
		parent::__construct();

		$array = JFactory::getApplication()->input->get('cid',  0, 'array');
		$this->setId((int)$array[0]);
	}

	function setId($id){
		// Set id and wipe data
		$this->_id        = $id;
		$this->_data    = null;
	}

	function &getData(){
		// Load the data
		if ( empty( $this->_data ) ) {

			$query = "SELECT * FROM #__pago_attr WHERE id = $this->_id";

			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}

		if ( !$this->_data ){
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->type = false;
			$this->_data->name = false;
			$this->_data->created = date( 'Y-m-d H:i:s', time() );
			$this->_data->modified = date( 'Y-m-d H:i:s', time() );
			$this->_data->options = Array();
			$this->_data->ordering = '';
		}

		return $this->_data;
	}

	function store_attribute_options( $data ){
		$table = $this->getTable( 'attribute_options' );

		$delete = false;
		$i = 1;
		$db = JFactory::getDBO();
		
		if($data['options']){
			foreach( $data['options'] as $option ){
				$option['attr_id'] = $data['id'];

				if(isset($option['new']) || ($data['type'] != $option['type'])){
					continue;
				}

				$option['ordering'] = $i;
				$option['opt_enable'] = 1;
				$i++;
				
				if (!$table->bind($option)) return JError::raiseWarning( 500, $table->getError() );
				if (!$table->check()) return JError::raiseWarning( 500, $table->getError() );
				if (!$table->store()) return JError::raiseWarning( 500, $table->getError() );

				$db = $table->getDBO();
				if ( isset($option['id']) and strlen($option['id'] > 0)  ){
					$ids[] = $option['id'];
				} 
				else if( $insert_id = $db->insertid() ){
					$ids[] = $db->insertid();
				}
			}

			/*
				make sure to remove attributes options. If attribute options aren't in the array above
				they should be gone from the db and removed from items
			*/

			if($ids){
				$id = implode( ',', $ids );
				$query = "SELECT `id` FROM #__pago_attr_opts WHERE id NOT IN ( $id ) AND attr_id = {$data['id']} AND for_item = 0";
				$db->setQuery( $query );
				$options = $db->loadObjectList();
				if($options){
					foreach ($options as $option) {
						$this->deleteOpt($option->id);
					}
				}
			}
			else{
				$delete = true;			
			}
		}
		else{
			$delete = true;
		}

		if($delete){
			$query = "SELECT `id` FROM #__pago_attr_opts WHERE `attr_id` = {$data['id']} AND for_item = 0";
			$db->setQuery( $query );
			$options = $db->loadObjectList();
			if($options){
				foreach ($options as $option) {
					$this->deleteOpt($option->id);
				}
			}
		}

		return true;
	}

	function store_attribute_option( $data, $itemId = false, $haveValue = false ){
		$table = $this->getTable( 'attribute_options' );
		$db = $table->getDBO();

		// if($data->preselected=='1'){
		// 	$sql = "UPDATE #__pago_attr_opts SET preselected=0 WHERE for_item = ".$data->for_item;
		// 	$db->setQuery($sql);
		// 	$db->query();
		// }
		if($itemId){
			$data->for_item = $itemId;
		}

		if(!$haveValue){
			$timestamp = time() + 86400;
			$data->opt_enable = 0;
			$data->expiry_date = $timestamp;

			$data->searchable = 1;
			$data->showfront = 1;
			if (!$table->bind($data)) return JError::raiseWarning( 500, $table->getError() );
			if (!$table->store()) return JError::raiseWarning( 500, $table->getError() );
		}
		else{
			$data->opt_enable = 1;
			if (!$table->bind($data)) return JError::raiseWarning( 500, $table->getError() );
			if (!$table->check()) return JError::raiseWarning( 500, $table->getError() );
			if (!$table->store()) return JError::raiseWarning( 500, $table->getError() );
		}

		$insert_id = $db->insertid();
		$id = $db->insertid();
			
			//echo $db->insertid();die;
		return $insert_id;
	}

	function store_product_varation( $data,$haveValue = false ){
		$table = $this->getTable( 'product_varation' );

		if(!$haveValue){
			$timestamp = time() + 86400;
			$data->var_enable = 0;
			$data->expiry_date = $timestamp;
			if (!$table->bind($data)) return JError::raiseWarning( 500, $table->getError() );
			if (!$table->store()) return JError::raiseWarning( 500, $table->getError() );
		}
		else{
			$data->var_enable = 1;
			if (!$table->bind($data)) return JError::raiseWarning( 500, $table->getError() );
			if (!$table->check()) return JError::raiseWarning( 500, $table->getError() );
			if (!$table->store()) return JError::raiseWarning( 500, $table->getError() );
		}

		$db = $table->getDBO();

		$insert_id = $db->insertid();
		$id = $db->insertid();
			
		return $insert_id;
	}

	function store_product_varation_attributes( $varationId, $attributes, $itemId ){
		$db = JFactory::getDBO();
			
		if($attributes){
			$query = "DELETE FROM #__pago_product_varation_rel WHERE varation_id = " .$varationId;
			$db->setQuery( $query );
			$db->query();

			foreach ($attributes as $attrId => $optId) {
				$query = "INSERT INTO #__pago_product_varation_rel VALUES ".
					"( '".$varationId."','".$itemId."', '".$attrId."', '".$optId."')";
				$db->setQuery( $query );
				$db->query();
			}
		}
		return;
	}
	
	function store($new = false){
		$row = $this->getTable();

		$data = JFactory::getApplication()->input->getArray($_POST);
		$data = $data['params'];

		$id = $data['id'];

		if($id == 0){
			$data['created'] = date( 'Y-m-d H:i:s', time() );
		}
		else{
			unset($data['created']);
			$data['modified'] = date( 'Y-m-d H:i:s', time() );	
		}

		if ( !$data['ordering'] ) {
			$data['ordering'] = $row->getNextOrder();
		}

		if($new){
			$timestamp = time() + 86400;
			$data['attr_enable'] = 0;
			$data['expiry_date'] = $timestamp;
		}
		else{
			$data['attr_enable'] = 1;
		}
		
		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );

		if(!$new){
			if (!$row->check()){
				JError::raiseWarning( 500, $row->getError() );
				return false;
			} 
		}

		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );

		$db = $row->getDBO();
		if( $insert_id = $db->insertid() ){
			$id 	= $db->insertid();
			$data['id'] = $id;
			$create = true;
		}

		if ( $data['id'] > 0 ) {
			$db = JFactory::getDBO();

			$query = "DELETE FROM #__pago_attr_assign WHERE attribut_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();

			$query = "DELETE FROM #__pago_attr_categories WHERE attribut_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();

			///////// attribute assign
			if($data['assign']['assign_type'] == 1){
				
				$query = "INSERT INTO #__pago_attr_assign VALUES ".
					"( '".$data['id']."', '".$data['assign']['assign_type']."', '".$data['assign']['assign_items']."')";
				$db->setQuery( $query );
				$db->query();
			}

			if($data['assign']['assign_type'] == 2){
				if(isset($data['assign']['assign_category'])){
					// category
					$assign_category = array_unique( $data['assign']['assign_category'] );

					$cat_values = array();
					$new_cats = array();
					foreach( $assign_category as $cid ){
						$cat_values[] = '('.$cid.','.$id.')';
						$new_cats[] = $cid;
					}

					$query = "INSERT INTO #__pago_attr_categories ( category_id, attribut_id ) VALUES ". implode( ',', $cat_values );

					$this->_db->setQuery($query);
					$this->_db->query();
				}
				$query = "INSERT INTO #__pago_attr_assign VALUES "."( '".$data['id']."', '".$data['assign']['assign_type']."', '".$data['assign']['assign_items']."')";
				$db->setQuery( $query );
				$db->query();
			}
		}

		$this->item_id = $id;

		/*if ( !$this->store_attribute_options( $data ) ) {
			return false;
		}*/

		//return true;
		return $id;
	}

	function addAutoVarantion($attrId, $itemId){
		$db = JFactory::getDBO();
				
		$query = "SELECT id FROM #__pago_product_varation WHERE item_id=".$itemId." AND var_enable = 1";
		$db->setQuery( $query );
		$varations = $db->loadObjectList();

		if($varations){
			foreach ($varations as $varation) {
				$query = "SELECT opt_id FROM #__pago_product_varation_rel 
				WHERE varation_id = ". $varation->id ."
				AND item_id = ".$itemId."
				AND attr_id = ".$attrId;
				$db->setQuery( $query );	
				$checkVarationHaveRel = $db->loadResult();

				if(!$checkVarationHaveRel){
					$query = "SELECT id FROM #__pago_attr_opts WHERE attr_id=".$attrId." ORDER BY id ASC LIMIT 0,1";
					$db->setQuery( $query );	
					$optId = $db->loadResult();
					

					$query = "INSERT INTO #__pago_product_varation_rel (varation_id, item_id, attr_id, opt_id) VALUES(".$varation->id.",".$itemId.",".$attrId.",".$optId.")";
					$db->setQuery( $query );
					$db->query();
				}
			}
		}
	}
	
	function storeCustom($data,$edit = false){
		$row = $this->getTable();

		if(isset($data['id'])){
			$id = $data['id'];
		}
		else{
			$id = 0;
		}

		if($id == 0){
			$data['created'] = date( 'Y-m-d H:i:s', time() );
		}
		else{
			unset($data['created']);
			$data['modified'] = date( 'Y-m-d H:i:s', time() );	
		}
		
		if ( !isset($data['ordering']) ) {
			$data['ordering'] = $row->getNextOrder();
		}
		
		// if(!$id){
		// 	$timestamp = time() + 86400;
		// 	$data['attr_enable'] = 0;
		// 	$data['expiry_date'] = $timestamp;
		// }else{
		// 	if(!$edit){
		// 		$data['attr_enable'] = 1;
		// 	}
		// }

		$data['attr_enable'] = 1;

		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );

		if (!$row->check()){
			JError::raiseWarning( 500, $row->getError() );
			return false;
		} 

		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );

		$db = $row->getDBO();
		if( $insert_id = $db->insertid() ){
			$id = $db->insertid();
			$data['id'] = $id;
			$create = true;
		}
		
		//return true;
		return $id;
	}

	function get_assign( $attrId ){
		$db = JFactory::getDBO();

		$query = 'SELECT * FROM #__pago_attr_assign where attribut_id = ' . $db->quote($attrId);
		$db->setQuery( $query );
		return $db->loadAssocList();
	}

	function deleteAttrOpt($attrId){
		$db = JFactory::getDBO();

		$query = "SELECT `id` FROM #__pago_attr_opts WHERE `attr_id` = ".$attrId;
		$db->setQuery( $query );
		$options = $db->loadObjectList();

		if($options){
			foreach ($options as $option) {
				$this->deleteOpt($option->id);
			}
		}

		return true;
	}

	public function checkOptVar($optId){
		$db = JFactory::getDBO();
		
		$return = array('varations'=>'0','required'=>'0','ifOne'=>'0');
		$req = '';
		$query = "SELECT `varation_id` FROM #__pago_product_varation_rel WHERE `opt_id` = ".$optId;
		$db->setQuery( $query );
		$varations = $db->loadObjectList();
		
		$query = "SELECT `required` FROM #__pago_attr_opts, #__pago_attr WHERE #__pago_attr_opts.id = ".$optId." AND #__pago_attr_opts.attr_id = #__pago_attr.id";
		$db->setQuery( $query );
		$required = $db->loadObjectList();
		
		$query = "SELECT count(*) FROM #__pago_attr_opts WHERE #__pago_attr_opts.attr_id = (SELECT attr_id FROM #__pago_attr_opts WHERE id = ".$optId.")";
		$db->setQuery( $query );
		$ifOne = $db->loadAssocList();
		
		if($required){
			foreach ($required as $required) {
				$req = $required->required;
			}
		}
		
		if($varations){
			$return['varations'] = '1';
		}
		
		if($req == 1){
			$return['required'] = '1';
		}
		
		if($ifOne[0]['count(*)'] > 1){
			$return['ifOne'] = '1';
		}
		
		return $return;
	}
	
	public function deleteOpt($optId){
		$db = JFactory::getDBO();
		$query =
			"DELETE FROM #__pago_attr_opts WHERE id = ". $optId;
		$db->setQuery( $query );

		if($db->query()){
			$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'attr_opt' .DIRECTORY_SEPARATOR. $optId .DIRECTORY_SEPARATOR;
			$this->delete_files($path);
		}

		$this->removeVaration($optId,"optionId");
		return true;
	}

	function removeVaration($id,$by ='id'){
		$db = JFactory::getDBO();
		if($by == 'id'){
			$query =
				"DELETE FROM #__pago_product_varation WHERE id = ". $id;
			$db->setQuery( $query );
			$db->query();
			$query =
				"DELETE FROM #__pago_product_varation_rel WHERE varation_id = ". $id;
			$db->setQuery( $query );
			if($db->query()){
				$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $id .DIRECTORY_SEPARATOR;
				$this->delete_files($path);
			}
		}

		if($by == 'optionId'){
			$query = "SELECT `varation_id` FROM #__pago_product_varation_rel WHERE `opt_id` = ".$id;
			$db->setQuery( $query );
			$varations = $db->loadObjectList();
			if($varations){
				foreach ($varations as $varation) {
					$this->removeVaration($varation->varation_id,'id');
				}
			}
		}

		return true;
	}

	function delete_files($target) {
	    if(is_dir($target)){
	        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
	        
	        foreach( $files as $file )
	        {
	            $this->delete_files( $file );      
	        }
	      
	        rmdir( $target );
	    } 
	    elseif(is_file($target)) {
	    	unlink( $target );
	   
	   		/*if(unlink( $target )){
	   			$path = dirname ( $target );

				$img = basename($target);
				$ext = explode('.', $img);
				$filename = $ext[0];
				if($filename && $path){
			 		foreach (glob($path. DIRECTORY_SEPARATOR . $filename ."-*") as $removeFile){
				    	unlink ($removeFile);
					}
				}
	   	    }*/  
	    }
	}

	function deleteAttrRel($attrId){
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__pago_items_attr_rel 
									WHERE attr_id = {$attrId}";
		$db->setQuery( $query );
		$db->query();

		return true;
	}

	function saveOrder( $cids, $limitstart ){
		$total = count( $cids );
		$row   = $this->getTable();
		
		$i = 1;
		$newOrders = array();
		if ( $cids ) {
			foreach ( $cids as $id ) {
				$row->load( $id );
				$newOrder = $i + $limitstart;
				if ( $newOrder != $row->ordering ) {
					$row->ordering = $newOrder;
					if ( !$row->store() ) {
						$this->setError( $row->getError() );
						return false;
					}
					$newOrders[$id] = $newOrder;
				}
				$i++;
			}
		}
		return $newOrders;
	}

	function get_custom_attributes($itemId){
		$db     = JFactory::getDBO();

		$query = 'SELECT `id`,`primary_category`,`price` FROM #__pago_items WHERE id = ' . $itemId;
		$db->setQuery( $query );
		$item = $db->loadObject();

		if($item){	
			$query = "SELECT a.*, s.*
				FROM #__pago_attr AS a
				LEFT JOIN #__pago_attr_assign AS s ON ( a.id = s.attribut_id )
					WHERE a.`attr_enable` = 1
					ORDER BY a.`ordering` ASC";
			
			$db->setQuery( $query );

			$attributes = $db->loadObjectList();

			$itemAttributes = array();
			if( $attributes ){
				foreach ($attributes as $attribute) {
					if($attribute->assign_type == 1){ //assign to item
						$attrItems = json_decode($attribute->assign_items);
						if($attrItems){
							foreach ($attrItems as $attrItem) {
								if($item->id == $attrItem->id){
									$itemAttributes[] = $attribute;	
								} 
							}
						}
					}
					elseif($attribute->assign_type == 2){ //assign to category
						$query = "SELECT `category_id` FROM #__pago_attr_categories WHERE `attribut_id` = ".$attribute->id." AND `category_id` = ". $item->primary_category;
						$db->setQuery( $query );
						$catsId = $db->loadObjectList();
						if($catsId){
							$itemAttributes[] = $attribute;
						}
					}
					else{ //assign global
						if($attribute->for_item == 0){ // check have concret item
							$itemAttributes[] = $attribute;
						}
						else{ 
							if($attribute->for_item == $item->id){ //for this item
								$itemAttributes[] = $attribute;	
							}
						}
					}
				}
			}
			
			foreach ($itemAttributes as $itemAttribute) {
				// get Attribute options
				$query = "SELECT * FROM #__pago_attr_opts WHERE `attr_id` = ".$itemAttribute->id." AND `opt_enable` = 1 ORDER by `ordering` ASC";
				$db->setQuery( $query );
				$attrOpts = $db->loadObjectList();
				if($attrOpts){
					foreach ($attrOpts as $attrOpt) {
						$files="";

						// get Attribute options images
						$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'attr_opt' .DIRECTORY_SEPARATOR. $attrOpt->id .DIRECTORY_SEPARATOR;
						
						if(file_exists($path)){
							if ($handle = opendir($path)){
								$pathinfo = pathinfo($path);
								$folder_name = $pathinfo['basename'];
								
								while (false !== ($entry = readdir($handle))) {
									if($entry!="." && $entry!=".." && !is_dir($path.DIRECTORY_SEPARATOR .$entry) && $entry!='fields.ini' && $entry!='index.html'){
										$file = new stdClass;
										$file->img = $entry; 
										$files[] = $file;
									}
								}
								
								// get Attribute iption images content (title, alt, desciption)
								$fields_file = $path . 'fields.ini';

								if(file_exists($fields_file)){
									$content = file_get_contents($fields_file);
									if($content!=''){
										$content = json_decode($content,true);
										if($files){
											foreach ($files as $file) {
												if(isset($content[$file->img])){
													$file->content = $content[$file->img];
												}
											}
										}
									}
								}
							}
						}

						$attrOpt->images = $files;
						// include attribute options to item
						$itemAttribute->options[] = $attrOpt;
					}
				}	
			}
			return $itemAttributes;
		}
		else{
			return false;
		}
	}

	function setDefault($optId){
		// get attribute Id
		$query = "SELECT `attr_id` FROM #__pago_attr_opts WHERE id = {$optId}";

		$this->_db->setQuery( $query );
		$attributeId = $this->_db->loadObject();
		
		$query = "UPDATE #__pago_attr_opts SET `default` = 0 WHERE attr_id = {$attributeId->attr_id} AND `default` = 1";
		$this->_db->setQuery( $query );
		$this->_db->query();	

		$query = "UPDATE #__pago_attr_opts SET `default` = 1 WHERE id = ".	$optId;
		$this->_db->setQuery( $query );
		$this->_db->query();

		return true;		
	}

	function assign_item_html($attrId){
		$html = '<div class="pg-row-item pg-col8 pg-assign-items">
					<div class="pg-row-item pg-col6">
						<ul class="coupon-assign-items">';

		$itemsUniqueId = array();
		$uniqueItems = array();

		if($attrId != 0){
			$assign = $this->get_assign($attrId);
			$itemModel = JModelLegacy::getInstance( 'Item', 'PagoModel' );
			if($assign){
				$itemsId = json_decode($assign['0']['assign_items']);
				if($itemsId){
					foreach ($itemsId as $value) {
						if(!in_array($value->id, $itemsUniqueId)){
							$itemsUniqueId[] = $value->id;
							$uniqueItems[] = $value;
							$item = $itemModel->getItemName($value->id);
							if ( $item ) {
								$html .= '<li class="itemAdded" id="'.$value->id.'">'.$item->name.'
											  <span title="Click to remove" class="coupon-remove-assign-item">x</span>
										  </li>';
							}
						}
					}
				}
			}
		}
		$html .= 		'</ul>
					</div>';
		$html .= '<div class="pg-col4">';
		$html .= 	'<input type="text"  id="coupon-assign-item-add" autocomplete="off" aria-autocomplete="list" aria-haspopup="true"></li>';
		$html .= 	'<input type="hidden" name="params[assign][assign_items]" id="params_assign_items" value=\''.json_encode($uniqueItems).'\' ></div>';
		$html .= '</div>
				</div>';

		return $html;		
	}

	function assign_category_html($attrId){
		$html = '<div class="pg-row-item pg-col4 pg-assign-category">';
		
		$cat_table = JTable::getInstance( 'categoriesi', 'Table' );
		$cats = $cat_table->getTree( 1 );
		
		$ctrl = "params[assign][assign_category][]";
		$attribs = ' multiple="true"';

		$key = 'id';
		$val = 'name';

		$value = 0;
		
		$control_name = "params_assign_category";

		foreach($cats as $cat){
			$cat_name = str_repeat('_', (($cat->level) * 2) );

			$cat_name .= '['. $cat->level .']_ ' . $cat->name;
			$options[] = array(
				'id' => $cat->id,
				'name' => $cat_name
			);
		}

		if($attrId){
			//get our secondary cats
			$query = ' SELECT category_id FROM #__pago_attr_categories '.
					'  WHERE attribut_id = '.$attrId;

			$secondary_categories = $this->_getList( $query );

			$cats = array();

			if( is_array( $secondary_categories ) )
			foreach ( $secondary_categories as $cat ) {
				$cats[] = $cat->category_id;
			}

			if ( !empty( $cats ) ) {
				$value = $cats;
			}
		}

		$html .= @JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name);

		$html .= '</div>';
		return $html;
	}
	
	function getSizeTypeName($sizeType)
	{
		switch ($sizeType) 
		{
			case 0:
				$name = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US');
				break;
				
			case 1:
				$name = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL');
				break;
			
			case 2:
				$name = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK');
				break;
				
			case 3:
				$name = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE');
				break;
				
			case 4:
				$name = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY');
				break;
				
			case 5:
				$name = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA');
				break;
			
			case 6:
				$name = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN');
				break;
				
			default:
				$name = '';
				break;
		}
		
		return $name;
	}

	function get_attribute_html($data){
		$html = '';
		$i = 1;
		
		
		if (!is_array($data))
			$data = array($data);
		
		foreach( $data as $option ) {
			$option->num = $i;
			
			if(isset($option->size_type) && $option->size_type)
			{
				$sizeName = $this ->getSizeTypeName($option->size_type);
			}
			
			switch ($option->type) {
				case 0:
					$fields = <<<HTML
					<td>{$option->color}</td>
HTML;
				break;
					
				case 1:
					$fields = <<<HTML
					<td>{$option->size}</td>
					<td>{$sizeName}</td>
HTML;
				break;
				
				default:
					$fields = '';
				break;
			}
			
			$json = json_encode($option);
			
			$html .= <<<HTML
			
				<tr id="row_{$option->num}" rel="{$option->id}" class="pg-table-content attr_opt_row bind attr_opt_type_head_{$option->type}">
					<td class="attr_opt_name">
						{$option->name}
					</td>
					{$fields}
					<td class="pg-sort">
						<span class="pg-sort-handle"></span>
						<input type="hidden" name="ordering" value="{$option->id}; ?>" />
					</td>
					<td class="attr_opt_edit pg-edit">
						<a href="javascript:void(0);" onclick='return jQuery.attributeOpts("editField", {$json})'></a>
					</td>
					<td class="attr_opt_remove pg-remove">
						<a href="javascript:void(0);" onclick="return jQuery.attributeOpts('delField', {$option->id})"></a>
					</td>
				</tr>
HTML;
			
			$i++;
		}
		
			
			
		return $html;
	}

	function get_attribute_form($data,$new = false,$bind = false){
		if($bind){
			$class =' bind ';
		}
		else{
			$class ='';
		}
		
		$name_lbl = JText::_('PAGO_ATTRIBUTE_TYPE_NAME_' . $data->type) .' '. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NAME');
		$name = (isset($data->name) ? $data->name : "");
		
		$fields = '';
		switch ($data->type) {
			case '0':
				$lbl = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_COLOR_ON_PALETTE');
				$value = (isset($data->color) ? $data->color : "");
				
				$fields = <<<HTML
				<div class="no-margin">
					<span class="field-heading">
						<label id="" for="params_options_color_palette_{$data->num}" class="hasTip" title="{$lbl}">{$lbl}</label>
					</span>
					<input  type="text" class="color" name="params[options][{$data->num}][color]" id="params_options_color_{$data->num}" value="{$value}" />
				</div>
				<script>
					jscolor.bind();
				</script>
HTML;
			break;
		
			case '1':
				$lbl = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE');
				$lbl2 = JText::_('PAGO_ATTRIBUTE_OPT_VALUES_CHANGE_SIZE_TYPE');
				$value = (isset($data->size) ? $data->size : "");
				
				$options = '
				<option value="0" '. (isset($data->size_type) && $data->size_type == 0 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US') .'</option>
				<option value="1" '. (isset($data->size_type) && $data->size_type == 1 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL') .'</option>
				<option value="2" '. (isset($data->size_type) && $data->size_type == 2 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK') .'</option>
				<option value="3" '. (isset($data->size_type) && $data->size_type == 3 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE') .'</option>
				<option value="4" '. (isset($data->size_type) && $data->size_type == 4 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY') .'</option>
				<option value="5" '. (isset($data->size_type) && $data->size_type == 5 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA') .'</option>
				<option value="6" '. (isset($data->size_type) && $data->size_type == 6 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN') .'</option>
				';
				
				$fields = <<<HTML
				<div class="">
					<span class="field-heading">
						<label id="" for="params_options_size_{$data->num}" class="hasTip" title="">{$lbl}</label>
					</span>
					<input  type="text"  name="params[options][{$data->num}][size]" id="params_options_size_{$data->num}" value="{$value}" />
				</div>
				<div class="no-margin">
					<span class="field-heading">
						<label id="" for="params_options_size_type_{$data->num}" class="hasTip" title="">{$lbl2}</label>
					</span>
					<div class="selector">
						<select name="params[options][{$data->num}][size_type]" id="params_options_size_type_{$data->num}" style="opacity: 0;">
							{$options}
						</select>
					</div>
				</div>
				<script>
					jQuery("#add-attribute-fields select").chosen();
				</script>
HTML;
			break;
		
			default:
			break;
		}
		
		$margin = ($fields == '') ? 'no-margin' : '' ;
		
		$ordering = (isset($data->ordering) ? $data->ordering : "");
		$id = (isset($data->id) ? $data->id : "");
		$type = (isset($data->type) ? $data->type : "");
		
		$newFields = ($new) ? '<input type="hidden" name="params[options]['.$data->num.'][new]" id="params_options_new_'.$data->num.'" value="1" />' : '';
		
		$html = <<<HTML
		<div class="{$margin}">
			<span class="field-heading">
				<label id="" for="params_options_name_{$data->num}" class="hasTip" title="{$name_lbl}">{$name_lbl}</label>
			</span>
			<input type="text" name="params[options][{$data->num}][name]" id="params_options_name_{$data->num}" value="{$name}" />
		</div>
		{$fields}
		<div class="">
			<input type="hidden" name="params[options][{$data->num}][ordering]" id="params_options_ordering_{$data->num}" value="{$ordering}" />
			<input type="hidden" name="params[options][{$data->num}][id]" id="params_options_id_{$data->num}" value="{$id}" />
			<input type="hidden" name="params[options][{$data->num}][type]" id="params_options_opt_type_{$data->num}" value="{$type}" />
			{$newFields}
		</div>
HTML;
		
		return $html;
	}

	/// custom attribute
	function get_custom_attribute_html($itemId,$data=false){
		$html = '';
		$html .= PagoHtml::module_top( JText::_( 'PAGO_CUSTOM_ATTRIBUTES_ADD_ATTRIBUTE' ) . '<a class="pg-btn-modal-close" onclick="return jQuery.customAttribute(\'closeAttributeForm\');"><span class="fa fa-close"></span></a>', '', null, null, null, '', '', '', false );
		$html .= '
			<input type="hidden" name="params[custom_attrubte][edit_id]" id="params_custom_attrubte_edit_id" value="'. (isset($data->id) ? $data->id : "") .'">
			<div class="pg-pad-20 pg-border" id="params_custom_attrubte_modal_wrapper">
				<div class="pg-row">
					<div class="pg-tab-content">
						<div class="pg-col-6 pg-text">
							<span class="field-heading">
								<label id="params_custom_attrubte_name-lbl" for="params_custom_attrubte_name" class="hasTip" title="">Name</label>
							</span>
							<input type="text" name="params[custom_attrubte][name]" id="params_custom_attrubte_name" value="'. (isset($data->name) ? $data->name : "") .'" size="30">
						</div>
						<div class="pg-col-6 pg-text">
							<span class="field-heading">
								<label id="" for="params_custom_attrubte_type" class="hasTip" title="">'. JText::_('PAGO_ATTRIBUTE_TYPE') .'</label>
							</span>					
							<div class="selector">
								<select name="params[options][custom_attrubte][type]" id="params_custom_attrubte_type" style="display:none;">
									<option value="0" '. (isset($data->type) && $data->type == 0 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_TYPE_COLOR') .'</option>
									<option value="1" '. (isset($data->type) && $data->type == 1 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_TYPE_SIZE') .'</option>
									<option value="2" '. (isset($data->type) && $data->type == 2 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_TYPE_MATERIAL') .'</option>
									<option value="3" '. (isset($data->type) && $data->type == 3 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_TYPE_CUSTOM') .'</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="pg-row">
					<div class="pg-tab-content no-margin">
						<div class="pg-col-6 pg-text">
							<span class="field-heading">
								<label id="" for="params_custom_attrubte_display_type" class="hasTip" title="">'. JText::_('PAGO_ATTRIBUTE_DISPLAY_TYPE') .'</label>
							</span>					
							<div class="selector">
								<select name="params[options][custom_attrubte][display_type]" id="params_custom_attrubte_display_type" style="opacity: 0;">
									<option value="0" '. (isset($data->display_type) && $data->display_type == 0 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_DISPLAY_TYPE_DROPDOWN_LIST') .'</option>
									<option value="1" '. (isset($data->display_type) && $data->display_type == 1 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_DISPLAY_TYPE_LIST') .'</option>
									<option value="2" '. (isset($data->display_type) && $data->display_type == 2 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_DISPLAY_TYPE_RADIO_BUTTON') .'</option>
								</select>
							</div>
						</div>
						<div class="pg-col-3 pg-text">
							<span class="field-heading">
								<label id="params_custom_attrubte_showfront-lbl" for="params_custom_attrubte_showfront" class="hasTip" title="">'. JText::_('PAGO_ATTRIBUTE_SHOW_FRONT') .'</label>
							</span>
							<fieldset class="radio border">
								<input name="params[custom_attrubte][showfront]" type="radio" value="1" '. (isset($data->showfront) && $data->showfront == 1 ? "checked='checked'" : "") .' id="params_custom_attrubte_showfront_yes"/>
								<label for="params_custom_attrubte_showfront_yes">' . JText::_('PAGO_YES') . '</label>
								<input name="params[custom_attrubte][showfront]" type="radio" value="0" '. (isset($data->showfront) && $data->showfront == 0 ? "checked='checked'" : "") .' id="params_custom_attrubte_showfront_no"/>
								<label for="params_custom_attrubte_showfront_no">' . JText::_('PAGO_NO') . '</label>
							</fieldset>
						</div>
						<div class="pg-col-3 pg-radio">
							<span class="field-heading">
								<label id="" for="params_custom_attrubte_required" class="hasTip" title="">'. JText::_('PAGO_ATTRIBUTE_REQUIRED') .'</label>
							</span>
							<fieldset class="radio border">
								<input name="params[options][custom_attrubte][required]" type="radio" value="1" '. (isset($data->required) && $data->required == 1 ? "checked='checked'" : "") .' id="params_custom_attrubte_required_yes"/>
								<label for="params_custom_attrubte_required_yes">' . JText::_('PAGO_YES') . '</label>
								<input name="params[options][custom_attrubte][required]" type="radio" value="0" '. (isset($data->required) && $data->required == 0 ? "checked='checked'" : "") .' id="params_custom_attrubte_required_no"/>
								<label for="params_custom_attrubte_required_no">' . JText::_('PAGO_NO') . '</label>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="pg-pad-20 text-center">
				<div class="clear"></div>
				<input type="button" class="attr_form_buttom apply pg-btn-medium pg-btn-light pg-btn-green" href="#" onclick="return jQuery.customAttribute(\'saveAttribute\','.$itemId.');" value="'. JText::_('PAGO_ATTRIBUTE_OPT_SAVE') .'">
				<div class="clear"></div>
			</div>
			<script>
				jQuery(function(){
					jQuery("#params_custom_attrubte_modal_wrapper select").chosen({"disable_search": true, "disable_search_threshold": 6, "width": "auto" });
				});
			</script>
';
	if(isset($data->id)){
		$html .= "
				
				";	
	}				
	return $html;
	}
	// function publishedAttr($async,$attrId,$itemId){
	// 	$html = '';
		
	// 	$attrItemRel = PagoModelAttribute::publishedAttr(false,$data->id,$itemId);
	// 	if($attrItemRel){
	// 		$html .= '<a href="javascript:void(0);" onclick="javascript:changePublish(\'.attribute value-checkboxes\');" class="pg-title-button pg-button-delete" rel="">
	// 				<div class="pg-published-attr">
	// 					<span class="icon-32-delete" title="'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_UNPUBLISH" ).'"></span>
	// 					'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_UNPUBLISH" ).'
	// 				</div>
	// 			</a>';	
	// 	}else{
	// 		$html .= '<a href="javascript:void(0);" onclick="javascript:changePublish(\'.attribute value-checkboxes\');" class="pg-title-button pg-button-delete" rel="">
	// 				<div class="pg-published-attr">
	// 					<span class="icon-32-delete" title="'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_UNPUBLISH" ).'"></span>
	// 					'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_UNPUBLISH" ).'
	// 				</div>
	// 			</a>';
	// 	}
	// 	return $html;		
	// }
	// function checkAttrPublish($attrId,$itemId){
	// 	$query = "SELECT * FROM #__pago_items_attr_rel 
	// 					WHERE attr_id = {$attrId} AND item_id = {$itemId}";

	// 	$this->_db->setQuery( $query );
	// 	$attrItemRel = $this->_db->loadObject();	
	// 	if($attrItemRel){
	// 		return false;
	// 	}else{
	// 		return true;
	// 	}
	// }

	// attribute publish unpublish
	function publishedAttr($attrId,$itemId){
		$html = '';
		$attrItemRel = PagoModelAttribute::checkAttrPublish($attrId,$itemId);
		if($attrItemRel){
			$html .= '<a href="javascript:void(0);" onclick="return jQuery.customAttribute(\'changePublish\','.$attrId.','.$itemId.');" class="pg-left pg-btn-small pg-btn-dark pg-btn-red pg-btn-unpublish" rel="">
					'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_UNPUBLISH" ).'
				</a>';	
		}else{
			$html .= '<a href="javascript:void(0);" onclick="return jQuery.customAttribute(\'changePublish\','.$attrId.','.$itemId.');" class="pg-left pg-btn-small pg-btn-dark pg-btn-green pg-btn-publish" rel="">
					'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_PUBLISH" ).'
				</a>';
		}
		return $html;
	}
	function checkAttrPublish($attrId,$itemId){
		$query = "SELECT * FROM #__pago_items_attr_rel 
						WHERE attr_id = {$attrId} AND item_id = {$itemId}";

		$this->_db->setQuery( $query );
		$attrItemRel = $this->_db->loadObject();
		if($attrItemRel){
			return false;
		}else{
			return true;
		}
	}
	function changeAttrPublish($attrId,$itemId){
		$attrItemRel = PagoModelAttribute::checkAttrPublish($attrId,$itemId);
		if($attrItemRel){
			$query = "INSERT INTO #__pago_items_attr_rel ( `item_id`, `attr_id` )".
				"VALUES ({$itemId}, {$attrId})";
			$this->_db->setQuery( $query );
			$this->_db->query();	
		}else{
			$query = "DELETE FROM #__pago_items_attr_rel 
									WHERE attr_id = {$attrId} AND item_id = {$itemId}";
			$this->_db->setQuery( $query );
			$this->_db->query();	
		}
		return PagoModelAttribute::publishedAttr($attrId,$itemId);
	}
	// attribute publish unpublish end

	// attribute option publish unpublish
	function changeAttrOptionPublish($optionId,$itemId){
		$optionItemRel = PagoModelAttribute::checkAttrOptionPublish($optionId,$itemId);
		if($optionItemRel){
			$query = "INSERT INTO #__pago_items_attr_opt_rel ( `item_id`, `option_id` )".
				"VALUES ({$itemId}, {$optionId})";
			$this->_db->setQuery( $query );
			$this->_db->query();	
		}
		else{
			$query = "DELETE FROM #__pago_items_attr_opt_rel
									WHERE option_id = {$optionId} AND item_id = {$itemId}";
			$this->_db->setQuery( $query );
			$this->_db->query();	
		}
		return PagoModelAttribute::publishedAttrOption($optionId,$itemId);
	}

	function publishedAttrOption($optionId,$itemId){
		$html = '';
		$optionItemRel = PagoModelAttribute::checkAttrOptionPublish($optionId,$itemId);
		if($optionItemRel){
			$html .= '<a href="javascript:void(0);" onclick="return jQuery.customAttribute(\'changeOptPublish\','.$optionId.','.$itemId.');" class="pg-title-button pg-button-delete" rel="">
						<img src="components/com_pago/css/img-new/publish.png" border="0" alt="Published" class="item-unpublish">	
					</a>';	
		}else{
			$html .= '<a href="javascript:void(0);" onclick="return jQuery.customAttribute(\'changeOptPublish\','.$optionId.','.$itemId.');" class="pg-title-button pg-button-new" rel="">
						<img src="components/com_pago/css/img-new/unpublish.png" border="0" alt="Published" class="item-unpublish">
					</a>';
		}
		return $html;
	}

	function checkAttrOptionPublish($optionId,$itemId){
		$query = "SELECT * FROM #__pago_items_attr_opt_rel 
						WHERE option_id = {$optionId} AND item_id = {$itemId}";

		$this->_db->setQuery( $query );
		$optItemRel = $this->_db->loadObject();

		if($optItemRel){
			return false;
		}
		else{
			return true;
		}
	}

	// attribute publish unpublish end
	function get_custom_attribute_title($data,$itemId){
		$html = 	'';
		$html .= 	'<div class="pg-container-header pg-mt-20" id="attr_block_'.$data->id.'">
						'.$data->name.'
						(';
							switch ($data->type) {
								case "0":
									$html .= JText::_( "PAGO_ATTRIBUTE_TYPE_COLOR" );
									break;
								case "1":
									$html .= JText::_( "PAGO_ATTRIBUTE_TYPE_SIZE" );
									break;
								case "2":
									$html .= JText::_( "PAGO_ATTRIBUTE_TYPE_MATERIAL" );
									break;
								case "3":
									$html .= JText::_( "PAGO_ATTRIBUTE_TYPE_CUSTOM" );
									break;			
								default:
									break;
							}
		$html .= 		')';
		// <div class="pg-published">
		// 					'. PagoHelper::published( $row, $i, 'tick.png',  'publish_x.png', '', ' class="publish-attr-buttons" rel="' .$row->id. '"' ).'
		// 				</div>	
		
		if($data->for_item !=  $itemId){
				$html .= '<div class="pg-right pg-container-header-buttons">
							<div class = "pg-add-attr pg-left">
								<a href="javascript:void(0)" onclick="return jQuery.customAttribute(\'showAddValue\','.$data->id.','.$data->type.','.$itemId.');" class="pg-btn-small pg-btn-dark pg-btn-add" data-toggle="modal" rel="">
									'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_ADD_ATTRIBUTE_VALUE" ).'
								</a>
							</div>';
				$html .= '<div class="pg-published-attr pg-left">';
					$html .= PagoModelAttribute::publishedAttr($data->id,$itemId);
				$html .= '</div>';
				$html .= '</div>';
		}
		else{
			$html .= '<div class="pg-right pg-container-header-buttons">
						<div class = "pg-add-attr pg-left">
							<a href="javascript:void(0)" onclick="return jQuery.customAttribute(\'showAddValue\','.$data->id.','.$data->type.','.$itemId.');" class="pg-btn-small pg-btn-dark pg-btn-add" data-toggle="modal" rel="">
								'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_ADD_ATTRIBUTE_VALUE" ).'
							</a>
						</div>';
				$html .= '<div class="pg-published-attr pg-left">';
					$html .= PagoModelAttribute::publishedAttr($data->id,$itemId);
				$html .= '</div>';

				$html .= '<div class="pg-delete-attr pg-left">
							<a href="javascript:void(0)" onclick="return jQuery.customAttribute(\'delAttr\','.$data->id.');" class="pg-btn-small pg-btn-dark pg-btn-delete" rel="">
								'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_DELETE" ).'
							</a>
							</div>';
				$html .= '<div class="pg-edit-attr pg-left">
							<a href="javascript:void(0)" onclick="return jQuery.customAttribute(\'editAttr\','.$data->id.');" class="pg-btn-small pg-btn-dark pg-btn-edit" data-toggle="modal" data-target="#addAttribute" rel="">
								'.JText::_( "PAGO_CUSTOM_ATTRIBUTES_EDIT" ).'
							</a>
							</div>';			
				$html .= '</div>';
		}	
		$html .= ' </div>';
		return $html;
	}

	function get_custom_attribute_thead($data,$itemId){
		$html = '';
		$html .= '<thead>
			<tr class="pg-sub-heading pg-multiple-headings">
				<td class="pg-name">
					<div class="pg-name">
						'.JText::_("PAGO_CUSTOM_ATTRIBUTES_NAME_LBL").'
					</div>
				</td>
				<td class="pg-published">
					'.JText::_('PAGO_PUBLISHED').'
				</td>
				<td class="pg-value">
					<div class="pg-value">
						'.JText::_("PAGO_CUSTOM_ATTRIBUTES_VALUE_LBL").'
					</div>
				</td>
				<td class="pg-edit">
					'.JText::_("PAGO_CUSTOM_ATTRIBUTES_EDIT_LBL").'
				</td>
				<td class="pg-remove">
					'.JText::_("PAGO_CUSTOM_ATTRIBUTES_DELETE_LBL").'
				</td>
			</tr>';
		if($data->for_item ==  $itemId){
			$html .= '<input type="hidden" name="params[custom_attribute][]" value="'. (isset($data->id) ? $data->id : "") .'" />';
		}
		$html .= '</thead>';
			return $html;
	}

	function get_custom_attribute_tbody($data,$item){
		$html = '';

		if ( !empty( $data->options ) ) {
			for ( $i = 0; $i < count( $data->options ); $i++ ) {
				$totalRows = count ( $data->options ) -1;
				
				$row = clone $data->options[$i];

				$class = '';
				if( $i == 0 ){
					$class .= ' pg-first-row ';
				}
				if( $i == $totalRows ){
					$class .= ' pg-last-row ';
				}
				// <td>
				// 	<div class="pg-checkbox pg-first-column">
				// 		<input type="checkbox" class="attrCheck_'. $data->id .'-checkboxes" value="'.$row->id.'" name="cidcusa[]" id="custom_attr'.$i.'" onclick="pago_highlight_row(this);" />
				// 	</div>
				// </td>
				// <td class="pg-published">
				// 		<div class="pg-published">
				// 			'. PagoHelper::published( $row, $i, 'tick.png',  'publish_x.png', '', ' class="publish-attr-buttons" rel="' .$row->id. '"' ).'
				// 		</div>
				// 	</td>
				
				
				$html .='<tr class="pg-table-content'. $class .'" id="optionId_'.$row->id.'" rel="cid-'.$row->id.'">
					<td>
						<div class="pg-name">';
							
							if($row->for_item !=  $item->id){
							$html .='<label>'.$row->name.'</label>';
							} else {
							$html .='<label><a href="javascript:void(0);" onclick="return jQuery.customAttribute(\'showEditValue\','.$row->id.','.$row->for_item.');">'.$row->name.'</a></label>';
							}
						$html .='</div>
					</td>
					<td class="pg-published">
			     		<div class="pg-published-attr-opt">';
							
							
							
							
							$html .= PagoModelAttribute::publishedAttrOption($row->id,$item->id);
			$html .=   '</div>
					</td>
					<td>
						<div class="pg-value">';
							switch ($data->type) {
								case "0":
									$html .='<span class="pg_color_option_form" style="background-color:'. $row->color .'"></span> ('.$row->color.')';
									break;
								case "1":
									$html .= $row->size;
										switch ($row->size_type) {
											case "0":
												$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US').')';
											break;
											case "1":
												$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL').')';
											break;
											case "2":
												$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK').')';
											break;
											case "3":
												$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE').')';
											break;
											case "4":
												$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY').')';
											break;
											case "5":
												$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA').')';													
											break;
											case "6":
												$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN').')';
											break;
											
										}
									break;
								case "2":
									$html .='---';
									break;
								case "3":
									$html .='---';
									break;			
								
								default:
									
									break;
							}						
				$html .='</div>
					</td>';
				$html .='<td class="pg-edit">';

				if($row->for_item ==  $item->id){
					$html .= '<a href="javascript:void(0);" onclick="return jQuery.customAttribute(\'showEditValue\','.$row->id.','.$row->for_item.');"></a>';
				}
				else{
					$html .= '<label></label>';//None
				}							

				$html .= '</td>';
				$html .='<td class = "pg-remove">';
				
				if($row->for_item ==  $item->id){
					$html .= '<a href="javascript:void(0);" onclick="return jQuery.customAttribute(\'deleteCustomOption\','.$row->id.');"></a>';
				}
				else{
					$html .= '<label></label>';//None
				}							

				$html .= '</td>';	

				if($data->for_item ==  $item->id){
					$html .= '<input type="hidden" name="params[custom_attribute_options][]" value="'. (isset($row->id) ? $row->id : "") .'" />';
				}	
				$html .= '</tr>';
			}
		}
		return $html;
	}

	function get_custom_attribute_value_html($data,$itemId,$bind=false){
		if($bind){
			$class =' bind ';
		}else{
			$class ='';
		}
		
		$html = PagoHtml::module_top( JText::_( 'PAGO_CUSTOM_ATTRIBUTES_ADD_ATTRIBUTE_VALUE' ) . '<button class="pg-btn-modal-close" onclick="return jQuery.customAttribute(\'closeAttributeValueForm\');"><span class="fa fa-close"></span></button>', '', null, null, null, '', '', '', false );
		$html .= '
		<input type="hidden" name="params[custom_attrubte_value][ordering]"  id="params_custom_attrubte_value_ordering"  value="'. (isset($data->ordering) ? $data->ordering : "") .'" />
		<input type="hidden" name="params[custom_attrubte_value][id]"        id="params_custom_attrubte_value_id"        value="'. (isset($data->id) ? $data->id : "") .'" />
		<input type="hidden" name="params[custom_attrubte_value][type]"      id="params_custom_attrubte_value_type"      value="'. (isset($data->type) ? $data->type : "") .'" />
		<input type="hidden" name="params[custom_attrubte_value][published]" id="params_custom_attrubte_value_published" value="'. (isset($data->published) ? $data->published : "") .'" />
		<input type="hidden" name="params[custom_attrubte][edit_id]"         id="params_custom_attrubte_edit_id"         value="'. (isset($data->id) ? $data->id : "") .'">
		<input type="hidden" name="params[custom_attrubte_value][new]"       id="params_custom_attrubte_value_new"       value="1" />
			<div class="pg-pad-20 pg-border showAttrForm attr_opt_type_'.$data->type.'" id="params_custom_attrubte_value_modal_wrapper">
				<div class="pg-row">
					<div class="pg-tab-content">
						<div class="pg-col-12 pg-text">
							<span class="field-heading">
								<label id="" for="params_custom_attrubte_value_name" class="hasTip" title="">'. JText::_('PAGO_ATTRIBUTE_TYPE_NAME_'.$data->type) .' '. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NAME') .'</label>
							</span>
							<input type="text" name="params[custom_attrubte_value][name]" id="params_custom_attrubte_value_name" value="'. (isset($data->name) ? $data->name : "") .'" />
						</div>
					</div>
				</div>';
				
		switch ($data->type) {
			case '0':
				$html .= '
				<div class="pg-row no-margin">
					<div class="pg-tab-content">
						<div class="pg-col-12 pg-text">
							<span class="field-heading">
								<label id="" for="params_custom_attrubte_value_color_palette" class="hasTip" title="">'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_COLOR_ON_PALETTE') .'</label>
							</span>
							<input  type="text" class="color" name="params[custom_attrubte_value][color]" id="params_custom_attrubte_value_color" value="'. (isset($data->color) ? $data->color : "") .'" />
						</div>
					</div>
				</div>
';
			break;
			case '1':
				$html .= '
				<div class="pg-row no-margin">
					<div class="pg-tab-content">
						<div class="pg-col-6 pg-text">
							<span class="field-heading">
								<label id="" for="params_custom_attrubte_value_size" class="hasTip" title="">'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE') .'</label>
							</span>
							<input type="text" name="params[custom_attrubte_value][size]" id="params_custom_attrubte_value_size" value="'. (isset($data->size) ? $data->size : "") .'" />
						</div>
						<div class="pg-col-6 pg-text">
							<span class="field-heading">
								<label id="" for="params_custom_attrubte_value_size_type" class="hasTip" title="">'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_CHANGE_SIZE_TYPE') .'</label>
							</span>
							<div class="selector">
								<select name="params[custom_attrubte_value][size_type]" id="params_custom_attrubte_value_size_type" style="opacity: 0;">
									<option value="0" '. (isset($data->size_type) && $data->size_type == 0 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US') .'</option>
									<option value="1" '. (isset($data->size_type) && $data->size_type == 1 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL') .'</option>
									<option value="2" '. (isset($data->size_type) && $data->size_type == 2 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK') .'</option>
									<option value="3" '. (isset($data->size_type) && $data->size_type == 3 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE') .'</option>
									<option value="4" '. (isset($data->size_type) && $data->size_type == 4 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY') .'</option>
									<option value="5" '. (isset($data->size_type) && $data->size_type == 5 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA') .'</option>
									<option value="6" '. (isset($data->size_type) && $data->size_type == 6 ? "selected='selected'" : "") .'>'. JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN') .'</option>
								</select>
							</div>
						</div>
					</div>
				</div>
';
			break;
			default:
			break;
		}
		
		$html .= '
			</div>
			<div class="pg-pad-20 text-center">
				<div class="clear"></div>
';
		if ($bind) {
			$html .= '<input type="button" class="attr_form_buttom apply pg-btn-medium pg-btn-light pg-btn-green" href="javascript:void(0);" onclick="return jQuery.customAttribute(\'saveOption\','.$itemId.','.$data->attr_id.',\'editOption\');" value="'. JText::_('PAGO_ATTRIBUTE_OPT_SAVE') .'" />';
		} else {
			$html .= '<input type="button" class="attr_form_buttom apply pg-btn-medium pg-btn-light pg-btn-green" href="javascript:void(0);" onclick="return jQuery.customAttribute(\'saveOption\','.$itemId.','.$data->attr_id.',\'addOption\');" value="'. JText::_('PAGO_ATTRIBUTE_OPT_SAVE') .'" />';
		}						

		$html .= '
				<div class="clear"></div>
			</div>
			<script>
				jQuery(function(){
					jQuery("#params_custom_attrubte_value_modal_wrapper select").chosen({"disable_search": true, "disable_search_threshold": 6, "width": "auto" });
				});
			</script>
';
		
		return $html;
	}
	//product varation
	function get_product_varation_html($itemId,$data,$attributes,$status){
		
		$existingAttributesArray = array();
		$existingOptionArray = array();

		if(isset($data->attributes)){
			foreach ($data->attributes as $exValue) {
				$existingAttributesArray[] = $exValue->attribute->id;
				$existingOptionArray[] = $exValue->option->id;
			}
		}

		if ($status == 'add')
			$html = PagoHtml::module_top( JText::_( 'PAGO_PRODUCT_VARAION_ADD' ) . '<button class="pg-btn-modal-close" onclick="return jQuery.customAttribute(\'closeProductVarationForm\');"><span class="fa fa-close"></span></button>', '', null, null, null, '', '', '', false );
		else 
			$html = PagoHtml::module_top( JText::_( 'PAGO_PRODUCT_VARAION_EDIT' ) . '<button class="pg-btn-modal-close" onclick="return jQuery.customAttribute(\'closeProductVarationForm\');"><span class="fa fa-close"></span></button>', '', null, null, null, '', '', '', false );

		$html .= '
		<input type="hidden" id="product_varation_form_item_id" value="'. $itemId .'" />
		<input type="hidden" id="product_varation_id"           value="'. (isset($data->id) ? $data->id : "") .'" />
		<input type="hidden" id="product_varation_published"    value="'. (isset($data->published) ? $data->published : "") .'" />	
		<div class="pg-pad-20 pg-border" id="product_varation_form_wrapper">
			<div class="pg-row">
				<div class="pg-tab-content">
					<div class="pg-col-3 pg-text hiddener clearfix">
						<span class="field-heading">
							<label id="" for="product_varation_default" class="hasTip" title="">'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_DEFAULT') .'</label>
						</span>
						<fieldset class="radio border">
							<input type="radio" '. (isset($data->default) && $data->default==1  ? "checked": "").' name="default_varation" value="1" id="accept" /><label for="accept" class="radiobutton"> Yes</label>
							<input type="radio" '. (!isset($data->default)  || (isset($data->default) && $data->default==0 )  ? "checked": "").' name="default_varation" value="0" id="refuse" /><label for="refuse" class="radiobutton">No</label>
						</fieldset>
					</div>
				</div>
			</div>
			<div class="pg-row">
				<div class="pg-tab-content">
';
//echo $itemId;die;
		$html .= '
					<div class="pg-col-6 pg-text">
						<span class="field-heading">
							<label id="" for="product_varation_name" class="hasTip" title="">'.JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_NAME') .'</label>
						</span>
						<input type="text" id="product_varation_name" value="'. (isset($data->name) ? htmlspecialchars($data->name) : "") .'" />
					</div>
					<div class="pg-col-6 pg-text">
						<span class="field-heading">
							<label id="" for="product_varation_sku" class="hasTip" title="">'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_SKU') .'</label>
						</span>
						<input type="text" id="product_varation_sku" value="'. (isset($data->sku) ? $data->sku : "") .'" />
					</div>
					<div class="pg-col-6 pg-text">
						<span class="field-heading">
							<label id="" for="product_varation_price_type" class="hasTip" title="">'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRICE_TYPE') .'</label>
						</span>					
						<div class="selector">
							<select id="product_varation_price_type" style="opacity: 0;">
								<option value="0" '. (isset($data->price_type) && $data->price_type == 0 ? "selected='selected'" : "") .'>'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRICE_NO_CHANGE') .'</option>
								<option value="1" '. (isset($data->price_type) && $data->price_type == 1 ? "selected='selected'" : "") .'>'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRICE_EQUAL') .'</option>
								<option value="2" '. (isset($data->price_type) && $data->price_type == 2 ? "selected='selected'" : "") .'>'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRICE_PLUS') .'</option>
								<option value="3" '. (isset($data->price_type) && $data->price_type == 3 ? "selected='selected'" : "") .'>'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRICE_MULTIPLY') .'</option>
								<option value="4" '. (isset($data->price_type) && $data->price_type == 4 ? "selected='selected'" : "") .'>'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRICE_PERCENT') .'</option>
							</select>
						</div>
					</div>
					<div class="pg-col-6 pg-text">
						<span class="field-heading">
							<label id="" for="product_varation_price" class="hasTip" title="">'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRICE') .'</label>
						</span>
						<input type="text" id="product_varation_price" value="'. (isset($data->price) ? $data->price : "") .'" />
					</div>
					<div class="pg-col-6 pg-text hiddener">
						<span class="field-heading">
							<label id="" for="product_varation_qty_limit" class="hasTip" title="">'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_QTY_LIMIT') .'</label>
						</span>
						<select id="product_varation_qty_limit">
							<option value="1" '.(!isset($data->qty_limit) || $data->qty_limit=='1' ? "selected='selected'" : "").'>'.JText::_('PAGO_UNLIMITED').'</option>
							<option value="0" '.(isset($data->qty_limit) && $data->qty_limit=='0' ? "selected='selected'" : "").'>'.JText::_('PAGO_LIMITED').'</option>
						</select>
					</div>
					<div class="pg-col-6 pg-text to-hide">
						<span class="field-heading">
							<label id="" for="product_varation_qty" class="hasTip" title="">'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_QTY') .'</label>
						</span>
						<input type="text" id="product_varation_qty" value="'. (isset($data->qty) ? $data->qty  :"0").'" />
					</div>
					<div class="clear"></div>
					
					'.$this->get_product_varation_medialist($data->id, $itemId).'
					
					
				</div>
			</div>
			
			<br /><br />
			<div class="pg-row">
				<div class="pg-tab-content">
';

		//attributes start
		if($attributes)
			foreach ($attributes as $attribute) {
				$html .= '
					<div class="pg-col-6 pg-text">
						<span class="field-heading">
';
				switch ($attribute->type) {
					case "0":
						$label = JText::_( "PAGO_ATTRIBUTE_TYPE_COLOR" );
					break;
					case "1":
						$label = JText::_( "PAGO_ATTRIBUTE_TYPE_SIZE" );
					break;
					case "2":
						$label = JText::_( "PAGO_ATTRIBUTE_TYPE_MATERIAL" );
					break;
					case "3":
						$label = JText::_( "PAGO_ATTRIBUTE_TYPE_CUSTOM" );
					break;
					default:
					break;
				}
				
				$html .= '<label id="" for="params_product_varation_name" class="hasTip" title="">'. $attribute->name .' (' . $label . ')</label>
						</span>
						<div class="selector">
';

				$html .= '
							<select class="product_varation_form_attr" id="product_varation_form_attr_'. $attribute->id .'" style="opacity: 0;">';

				if (in_array($attribute->id, $existingAttributesArray)) {
					$selected = '';
				} else {
					$selected = 'selected="selected"';	
				}
				
				if ($attribute->required==0) {
					$html .= '<option value="0" '.$selected.' >'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_NONE') .'</option>';
				}
								
				if(isset($attribute->options)){
					foreach ($attribute->options as $option) {
						if (in_array($option->id, $existingOptionArray)) {
							$selected = 'selected="selected"';	
						}else{
							$selected = '';
						}
						
						switch ($attribute->type) {
							case "0":
								$html .=  '<option value="'.$option->id.'" '.$selected.'>'.$option->name.' ('.$option->color.')</option>';
							break;
							case "1":
								$html .=  '<option value="'.$option->id.'" '.$selected.'>'.$option->name.' ('.$option->size.' ';
								
								switch ($option->size_type) {
									case "0":
										$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US').')';
									break;
									case "1":
										$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL').')';
									break;
									case "2":
										$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK').')';
									break;
									case "3":
										$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE').')';
									break;
									case "4":
										$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY').')';
									break;
									case "5":
										$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA').')';													
									break;
									case "6":
										$html .='('.JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN').')';
									break;
								}
								
								$html .=  ' )</option>';
							break;
							case "2":
								$html .=  '<option value="'.$option->id.'" '.$selected.'>'.$option->name.'</option>';
							break;
							case "3":
								$html .=  '<option value="'.$option->id.'" '.$selected.'>'.$option->name.'</option>';
							break;
							default:
							break;
						}
					}
				}

				$html .= ' 
							</select>
						</div>
					</div>
';	
			};
			//attributes end

		$html .= '
				</div>
			</div>
		</div>
		<div class="pg-pad-20 text-center modal-actions">
			<div class="clear"></div>
			<input type="button" class="attr_form_buttom apply pg-btn-medium pg-btn-light pg-btn-green" href="#" onclick="return jQuery.customAttribute(\'saveProductVaration\','.$itemId.','."'$status'".');" value="'. JText::_('PAGO_ATTRIBUTE_OPT_SAVE') .'">
			<div class="clear"></div>
		</div>
		<script>
			jQuery(function(){
				jQuery("#product_varation_form_wrapper select").chosen({"disable_search": true, "disable_search_threshold": 6, "width": "auto" });
			});
		</script>
';
			
		return $html;
	}
	
	function get_product_varation_medialist($variation_id, $item_id){
		Pago::load_helpers( 'imagehandler' );
		$data = PagoImageHandlerHelper::get_item_files( $item_id, false, array( 'variation' ), $variation_id );
		ob_start();
		?>
		
		
		
<div class="pg-tab-content pg-media pg-pad-20 pg-border">
	<div class = "media-add-container pg-pad-20 pg-mb-20 clearfix">
		
		<a href="javascript:void(0);" id="mediaImageUploadBTNV" type="variation" variation_id="<?php echo $variation_id ?>" item_id="<?php echo $item_id ?>" class="new add-image add-image-cat media-add-ico"></a>
		<span><?php echo JTEXT::_('PAGO_ADD_NEW_IMAGE'); ?></span>
				
		

	</div>

	<div class = "pg-product-images">
		<table class = "pg-images-manager-v">
			<thead>
				<tr class="pg-sub-heading pg-multiple-headings">
					<!--<td class="pg-checkbox">
						<div class="pg-checkbox pg-first-column">
							<input type="checkbox" name="toggle" id="checkall" value="" onclick="pago_check_all(this, '.<?php echo $this->callback; ?>-checkboxes');" />
							<label for="checkall"></label>
						</div>
					</td>-->

					<td class="pg-sort">
						<a href = "javascript:void(0)"></a>
					</td>

					<td class="pg-preview">
						<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_PREVIEW') ?>
					</td>

					<td class="pg-name">
						<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_NAME') ?>
					</td>

					<!--<td class="pg-caption">
						<?php echo JText::_('PAGO_CAPTION') ?>
					</td>-->

					<td class="pg-published">
						<?php echo JText::_('PAGO_PUBLISHED') ?>
					</td>

					<!--<td class="pg-default">
						<?php echo JText::_('PAGO_PRIMARY'); ?>
					</td>-->

					<td class="pg-remove">
						<?php echo JText::_( 'PAGO_REMOVE' ); ?>
					</td>
				</tr>
			</thead>
			<tbody>
				<?php
				$js = '';
				if ( !empty( $data ) ) {
					for ( $i = 0; $i < count( $data ); $i++ ) {
						$totalRows = count ( $data ) - 1;
						if ( $data[$i]->type == 'store_default' ) { continue; }
						$row = clone $data[$i];

						$img = PagoImageHandlerHelper::get_image_from_object( $data[$i], 'thumbnail', false,
							 'id="img-tooltip-' .$row->id. '" class="images-grid-thumbnail"', true, false );

						$video = '';
						$img_tooltip = PagoImageHandlerHelper::get_image_from_object( $data[$i], 'medium', false );
						if($row->type == 'video'){
							$img_tooltip = $tagReplace[$row->provider];
							$img_tooltip = str_replace("{SOURCE}", $row->video_key, $img_tooltip);
							$video = 'video';
							/*$js .= 'jQuery("#img-tooltip-' .$row->id. '").tooltip( {
							content: function(){ 
							return \'' .str_replace( "'", "\'", $video_tooltip ). '\' 
							},
							position: { my: "left top", at: "right top", offset: "20px -10px", collision: "none none" }
							});';*/	
						}else{
							/*$js .= 'jQuery("#img-tooltip-' .$row->id. '").tooltip( {
							content: function(){ return \'' .str_replace( "'", "\'", $img_tooltip ). '\' },
							position: { my: "left top", at: "right top", offset: "20px -10px", collision: "none none" }
							});';	*/
						}
						?>
						<tr class="pg-table-content<?php if ( $i == 0 ) { echo ' pg-first-row'; } if ( $i == $totalRows ) { echo ' pg-last-row'; } ?>" rel="cid-<?php echo $row->id; ?>">
							<!--<td class="pg-checkbox">
								<div class="pg-first-column">
									<input type="checkbox" class="<?php echo $this->callback; ?>-checkboxes" value="<?php echo $row->id; ?>" name="cidimgs[]" id="cb<?php echo $i; ?>" onclick="pago_highlight_row(this);" />
									<label for="cb<?php echo $i; ?>"></label>
								</div>
							</td>-->

							<td class="pg-sort">
								<div class="pg-sort">
									<span class="pg-sort-handle"></span>
									<input type="hidden" name="params[images_ordering][]" value="<?php echo $row->id; ?>" />
								</div>
							</td>

							<td class="pg-preview">
								<div class = "pg-preview-small-image <?php echo $video; ?>" style = "background:url('<?php echo $img; ?>')">
									
									<?php if($row->type == 'video' && false){ ?>
										<img class="pg-play" src="components/com_pago/css/img/pg-play.png">
									<?php } ?>
								</div>
								<div class = "pg-preview-large-image">
									<?php echo $img_tooltip; ?>
								</div>
							</td>

							<td class="pg-name">
								<input type="text" name="<?php echo $_name ?>[<?php echo $row->id; ?>][title]" value="<?php echo $row->title; ?>" />
							</td>

							<!--<td class="pg-caption">
								<input type="text" name="<?php echo $_name ?>[<?php echo $row->id; ?>][title]" value="<?php echo $row->title; ?>" />
							</td>-->

							<td class="pg-published">
								<?php echo PagoHelper::published( $row, $i, 'publish.png',  'unpublish.png', '', ' class="publish-buttons id-for-delete" type="file" rel="' .$row->id. '"' ); ?>
							</td>
<!--
							<td class="pg-default">
								<a href="javascript:void(0);" class="pg-icon <?php echo $this->callback; ?>-default is-default-<?php echo $row->default; ?> id-for-delete" rel="<?php echo $row->id; ?>"></a>
							</td> -->

							<td class = "pg-remove">
								<a href = "javascript:void(0)"></a>
							</td>

							<!-- <td>
								<div class="pg-caption">
									<input type="text" name="<?php echo $_name ?>[<?php echo $row->id; ?>][caption]" value="<?php echo $row->caption; ?>">
								</div>
							</td> -->
													
							<!--<td class="pg-access">
								<div class="pg-access">
								<?php echo JHTML::_( 'access.assetgrouplist', $_name. '[' .$row->id. '][access]', $row->access, array( 'size' => 1, 'style' => 'max-width:130px' ) );	?>
								</div>
							</td>-->

							<!-- <td>
								<div class="pg-description">
									<a href="javascript:void(0);" onClick="img_edit_desc(<?php //echo $row->id; ?>);"><?php //echo JText::_('PAGO_EDIT'); ?></a>
								</div>
							</td> -->

							<!--<td>
								<div class="pg-id pg-last-column">
									<?php echo $row->id; ?>
								</div>
							</td>-->
						</tr>
					<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>


		<?php
		
		
		return ob_get_clean();
	}
	
	function get_product_varation_list($item){
		$html ='';
		$html .='<div class="pg-table-wrap">
					<table class="product_varation_list pg-table pg-repeated-rows">
						<thead>
						<tr class="pg-sub-heading pg-multiple-headings">
							<td class="pg-name">
								<div class="pg-name">
									'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_NAME') .'
								</div>
							</td>
							<td class="pg-published">
								<div class="pg-published">
									<span class="pg-icon"></span>
								</div>
							</td>
							<td class="pg-value">
								<div class="pg-value">
									'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_ATTRIBUTES') .'
								</div>
							</td>
							<td class="pg-price">
								<div class="pg-price">
									'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRICE') .'
								</div>
							</td>
							<td class="pg-sku">
								<div class="pg-sku">
									'. JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_SKU') .'
								</div>
							</td>
							<td class="pg-preselected">
								<div class="pg-preselected">
									'. JTEXT::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRESELECTED').'
								</div>
							</td>
							<td class="pg-default-varation">
								<div class="pg-default-varation">
									'.JText::_("PAGO_DEFAULT").'
								</div>
							</td>
							<td class="pg-edit">
								<div class="pg-edit">
									'.JText::_("PAGO_CUSTOM_ATTRIBUTES_EDIT_LBL").'
								</div>
							</td>
							<td class="pg-delete-varation">
								<div class="pg-delete-varation">
									'.JText::_("PAGO_CUSTOM_ATTRIBUTES_DELETE_LBL").'
								</div>
							</td>
						</tr></thead><tbody>';
		$product_varations = $this->get_product_varations_by_item_id($item->id,false);
		if($product_varations){
			$html .= $this->get_product_varations_tr($product_varations);
		}
		$html .= '</tbody></table></div>';
		return $html;			
	}
	function get_varation_html($id){
		$varation = $this->get_product_varations_by_id($id);
		$html ='';

		if($varation){
			$varation = array($varation);
			$html .= $this->get_product_varations_tr($varation);
		}

		return $html;
	}
	function get_product_varations_tr($data){
		$html = '';
		if ( !empty( $data ) ) {
			for ( $i = 0; $i < count( $data ); $i++ ) {
				$totalRows = count ( $data ) -1;
				
				$row = clone $data[$i];

				$class = '';
				if( $i == 0 ){
					$class .= ' pg-first-row ';
				}
				if( $i == $totalRows ){
					$class .= ' pg-last-row ';
				}
				$html .='<tr class="pg-table-content'. $class .'" id="varationId_'.$row->id.'" rel="cid-'.$row->id.'">
					<td>
						<div class="pg-name">
							'.$row->name.'
						</div>
					</td>
					<td class="pg-published">
						<div class="pg-published">
							'. PagoHelper::published( $row, $i, 'publish.png',  'unpublish.png', '', ' class="publish-buttons" type="varation" rel="' .$row->id. '"' ).'	
						</div>
					</td>
					<td>
						<div class="pg-product-varation-attributes">';
				$attributesCount = count($row->attributes);
				if($attributesCount > 0){
					$v = 0;
					// $html .='<label>';
					$attrNames ='';
					foreach ($row->attributes as $value) {
						if($v == 2){
							break;
						}
						$attrNames .= $value->attribute->name.':'.$value->option->name.' / ';
						$v++;
					}
					$html .= substr($attrNames, 0, -2);
					if($attributesCount > $v){
						$moreCount = $attributesCount - $v;
						$html .= JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_AND') .' '. $moreCount .' ' .JText::_('PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_MORE');
					}

					// $html .='</label>';	
				}
				$html .='</div>
					</td>';
				$html .='<td>
							<div class="pg-price">';
					$html .='	'.Pago::get_instance( 'price' )->calculateVarationPrice( $row->id ).'
							</div>
						</td>';
				$html .='<td>
							<div class="pg-sku">';
					$html .='	'.$row->sku.'
							</div>
						</td>';
				$html .= '<td class="pg-preselected">
							<div class="pg-preselected">
								'. PagoHelper::preselected( $row, $i, 'publish.png',  'unpublish.png', '', ' class="preselected-varation-buttons" rel="' .$row->id. '"' ).'	
							</div>
						</td>';
				$html .= '<td class="pg-default-varation">
							<div class="pg-default-varation">
								<a href="javascript:void(0);"'.(!isset($row->default)  || (isset($row->default) && $row->default==0 )  ? "class='is-default-0'": "class='is-default-1'").'></a>	
							</div>
						</td>';		
				$html .='<td class="pg-edit">
						 <div class="pg-edit">';
					$html .= '<a href="javascript:void(0);" onclick="showEditProductVaration('.$row->id.','.$row->item_id.');"><span><img src="components/com_pago/css/img/pg-edit.png" /></span></a>';
				$html .= '</div>
						</td>';

				$html .='<td class="pg-delete-varation">
						<div class="pg-delete-varation">';
					$html .= '<a href="javascript:void(0);" onclick="return jQuery.customAttribute(\'deleteProductVaration\','.$row->id.');"><span><img src="components/com_pago/css/img/pg-delete.png" /></span></a>';						
				$html .= '</div>
						</td>';
				$html .= '</tr>';
				$html .= '<input  type="hidden" name="params[product_varation][]" value="'. (isset($row->id) ? $row->id : "") .'" />';
			}
		}
		return $html;	
	}
	function get_product_varations_by_item_id( $itemId, $published = false){
		$db = JFactory::getDBO();
		$itemId = (int)$itemId;
		if($published){
			$query = "SELECT * FROM #__pago_product_varation WHERE `item_id` = ".$itemId." AND var_enable = 1 AND published = 1";
		}else{
			$query = "SELECT * FROM #__pago_product_varation WHERE `item_id` = ".$itemId." AND var_enable = 1";
		}

		$db->setQuery( $query );
		$product_varations = $db->loadObjectList();
		if($product_varations){
			foreach ($product_varations as $key => $varation) {
				if($varation->default == 1){
					$varation = $this->replaceVarationByItem($varation,$itemId);
				}
				$varationAttributes = $this->get_product_varation_attribute($varation->id);
				if($varationAttributes){
					$varation->attributes = $varationAttributes;
				}else{
					unset($product_varations[$key]);
					$product_varations = array_values($product_varations);
				}
			}
		}
		return $product_varations;
	}
	function replaceVarationByItem($varation,$itemId){
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__pago_items WHERE id = ' . $itemId;
		$db->setQuery( $query );
		$item = $db->loadObject();
		
		$varation->name = $item->name;
		$varation->price = $item->price;
		$varation->sku = $item->sku;
		$varation->qty_limit = $item->qty_limit;
		$varation->qty = $item->qty;
		$varation->price_type = 0;
		
		$find = array("'",'"');
		$varation->name=str_replace($find,"",$varation->name);
		$varation->sku=str_replace($find,"",$varation->sku);

		return $varation;

	}
	function get_product_varations_by_id( $varationId, $published = false ){
		$db = JFactory::getDBO();
		
		if($published){
			$query = "SELECT * FROM #__pago_product_varation WHERE `id` = ".$varationId." AND var_enable = 1 AND published = 1";
		}else{
			$query = "SELECT * FROM #__pago_product_varation WHERE `id` = ".$varationId." AND var_enable = 1";
		}
		
		$db->setQuery( $query );
		$product_varation = $db->loadObject();
		if($product_varation){
			$varationAttributes = $this->get_product_varation_attribute($varationId);
			if($varationAttributes){
				$product_varation->attributes = $varationAttributes; 
			}else{
				unset($product_varation);
			}

		}
		return $product_varation;
	}
	function get_only_varations( $varationId ){
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__pago_product_varation WHERE `id` = ".$varationId." AND var_enable = 1 AND published = 1";

		$db->setQuery( $query );
		$product_varation = $db->loadObject();
		return $product_varation;
	}
	function get_product_varation_attribute( $varation_id ){
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__pago_product_varation_rel WHERE `varation_id` = ".$varation_id;
		$db->setQuery( $query );
		$varation_rel = $db->loadObjectList();
		$returnArray = array();
		if($varation_rel){
			foreach ($varation_rel as $rel) {
				$data = new stdClass();
				// get Attribute
				$query = "SELECT `id`,`name`,`type` FROM #__pago_attr WHERE `id` = ".$rel->attr_id;
				$db->setQuery( $query );
				$attr = $db->loadObject();
				if($attr){ // can delete varation if attr not exist
					$data->attribute = $attr;

					// get option
					$query = "SELECT `id`,`name` FROM #__pago_attr_opts WHERE `id` = ".$rel->opt_id;
					$db->setQuery( $query );
					$opt = $db->loadObject();
					if($opt){ // can delete varation if opt not exist
						$data->option = $opt;
					}else{
						$this->removeVaration($varation_id);
						return false;						
					}
				}else{
					$this->removeVaration($varation_id);
					return false;
				}
				$returnArray[] = $data;
			}
		}
		return $returnArray;
	}
	function calcVarationPrice( $itemId, $varation_attributes ){
		$db = JFactory::getDBO();

		$query = 'SELECT `price` FROM #__pago_items WHERE id = ' . $itemId;
		$db->setQuery( $query );
		$item = $db->loadObject();
		$price = $item->price;
		if($varation_attributes){
			foreach ($varation_attributes as $option) {
				$query = "SELECT `price_sign`,`price_type`,`price_sum` FROM #__pago_attr_opts WHERE `id` = ".$option;
				$db->setQuery( $query );
				$opt = $db->loadObject();
				if($opt and $opt->price_sign != 0){
					$optionPrice = Pago::get_instance( 'price' )->calculateAttributePrice( $opt->price_type, $item->price, $opt->price_sum );
					if($optionPrice > 0){
						$price = $price + $optionPrice; 	
					}
				}
			}
		}
		return $price;
	}
	function check_varation_exist($varationId,$varation_attributes,$itemId){
		$exist = true;
		$another_varations = $this->get_product_varations_by_item_id($itemId,false);
		if($another_varations){
			foreach ($another_varations as $another_varation) {
				if($another_varation->id != $varationId){
					if(isset($another_varation->attributes)){
						$newArrayForCheck = Array();
						foreach ($another_varation->attributes as $attrs) {
							$newArrayForCheck[$attrs->attribute->id] = $attrs->option->id; 
						}
						$arraysAreEqual = ($varation_attributes == $newArrayForCheck);
						if($arraysAreEqual){
							$exist = false;	
						}
					}
				}
			}	
		}
		return $exist;
	}

	function considerPrice($itemId = false,$selected_attributes = false,$itemQty = false){

		$db = JFactory::getDBO();

		$varation = $this->get_varation_if_exist($selected_attributes,$itemId);
		
		if($varation && $varation->default == 1){
			$varation = $this->replaceVarationByItem($varation,$itemId);	
		}

		$query = 'SELECT `id`,`price`,`sku`,`name`,`apply_discount`,`disc_start_date`,`disc_end_date`,`discount_type`,`discount_amount`,`qty_limit`,`qty`,`tax_exempt`,`pgtax_class_id` FROM #__pago_items WHERE id = ' . $itemId;
		$db->setQuery( $query );
		$item = $db->loadObject();

		if($varation){
			$price = Pago::get_instance( 'price' )->calculateVarationPrice( $varation->id );
			$item->price = $price;
			$productPriceObj = Pago::get_instance( 'price' )->getItemDisplayPrice($item);
			$price = $productPriceObj->item_price_including_tax;
			$return['sku'] = $varation->sku;
			$return['name'] = $varation->name;
			$return['varationId'] = $varation->id;
			$return['varationDefault'] = $varation->default;

			if($varation->qty_limit == 1){
				$return['limit'] = JText::_('PAGO_UNLIMITED');
			}else{
				$return['limit'] = $varation->qty;
			}

		}else{
		
			if($item -> apply_discount)
			{
				$priceDisc = Pago::get_instance( 'price' )->checkForProductOnSale($item);
				if(count($priceDisc) > 0)
				{
					$itemCreatedPrice = $priceDisc[1];
				}
				else
				{
					$itemCreatedPrice = $item->price;
				}
			}
			else
			{
				$itemCreatedPrice = $item->price;
			}
			$productPriceObj = Pago::get_instance( 'price' )->getItemDisplayPrice($item);
			$price = $productPriceObj->item_price_including_tax;
			//$price = $itemCreatedPrice;
			$return['sku'] = $item->sku;
			$return['name'] = $item->name;

			if($item->qty_limit == 1){
				$return['limit'] = JText::_('PAGO_UNLIMITED');
			}else{
				$return['limit'] = $item->qty;
			}
		}
		$return['unit_price'] = $price;
		if($itemQty > 0){
			$price = $itemQty * $price;
		}

		$return['price'] = $price;

		return $return;
	}

	function get_varation_if_exist($varation_attributes,$itemId){
		$exist = false;
		$itemVarations = $this->get_product_varations_by_item_id($itemId,true);//itemId, Select only published
		if($itemVarations){
			foreach ($itemVarations as $itemVaration) {
				if(isset($itemVaration->attributes)){
					$newArrayForCheck = Array();
					foreach ($itemVaration->attributes as $attrs) {
						$newArrayForCheck[$attrs->attribute->id] = $attrs->option->id; 
					}
					$arraysAreEqual = ($varation_attributes == $newArrayForCheck);
					if($arraysAreEqual){
						return $itemVaration;
						break;
					}
				}
			}	
		}
		return false;
	}
	function check_varation_can_be($varation_attributes,$itemId,$removeDefault=false){
		if(count($varation_attributes) < 2 ){
			return $removeDefault;
		}
		$exist = false;
		$itemVarations = $this->get_product_varations_by_item_id($itemId,true);//itemId, Select only published
		if($itemVarations){
			foreach ($itemVarations as $itemVaration) {
				if(isset($itemVaration->attributes)){
					$newArrayForCheck = Array();
					foreach ($itemVaration->attributes as $attrs) {
						$newArrayForCheck[$attrs->attribute->id] = $attrs->option->id; 
					}
					foreach ($varation_attributes as $attrId => $optionId) {
						if(isset($newArrayForCheck[$attrId]) && $newArrayForCheck[$attrId] == $optionId){
							$exist = true;
							continue;
						}else{
							$exist = false;
							break;
						}
					}
					if($exist){
						break;
					}
				}
			}	
		}
		if(!$exist){
			end($varation_attributes);
			$key = key($varation_attributes);
			$removeDefault[] = $key;
			unset($varation_attributes[$key]);
			return $this->check_varation_can_be($varation_attributes,$itemId,$removeDefault);
		}else{
			return $removeDefault;
			exit();
		}
	}
	public function findSameVaration($varation){
		$attributeModel = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
		$varations = $attributeModel->get_product_varations_by_item_id( $varation->item_id, false);

		$sameVarationIds = array();
		if(isset($varation->attributes)){
			$varationAttributes = Array();
			foreach ($varation->attributes as $varAttr) {
				if($varAttr->attribute->type == 1){
					continue;
				}
				$varationAttributes[$varAttr->attribute->id] = $varAttr->option->id;
			}
		}
		if($varations){
			foreach ($varations as $sameVaration) {
				if($sameVaration->id == $varation->id){
					continue;
				}
				if(isset($sameVaration->attributes)){
					$newArrayForCheck = Array();
					foreach ($sameVaration->attributes as $attrs) {
						if($attrs->attribute->type == 1){
							continue;
						}
						$newArrayForCheck[$attrs->attribute->id] = $attrs->option->id;
					}
					$arraysAreEqual = ($varationAttributes == $newArrayForCheck);
					if($arraysAreEqual){
						$sameVarationIds[] = $sameVaration->id;	
					}
				}
			}
		}
		return $sameVarationIds;
	}
	public function getVarationName($varationId){
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__pago_product_varation WHERE id=".$varationId;
			// die($sql);
		$db->setQuery($sql);


		return  $db->loadResult();

	}
	public function getVarationImages($varationId,$type='-1',$image_tag=true){
		$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $varationId .DIRECTORY_SEPARATOR;
		$urlPath = JURI::root() . 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $varationId .DIRECTORY_SEPARATOR;

		$files = false;
		$havaImage = false;
		$html = '';
		if ( $type != "-1" ) {
			if(file_exists($path)){
				if ($handle = opendir($path)){
					$pathinfo = pathinfo($path);
					$folder_name = $pathinfo['basename'];

					while (false !== ($entry = readdir($handle))) {
						if($entry!="." && $entry!=".." && !is_dir($path.DIRECTORY_SEPARATOR .$entry) && $entry!='fields.ini' && $entry!='index.html'  && !strpos($entry, "-")){
							$file = new stdClass;
							$file->img = $entry;
							$files[] = $file;
						}
					}
					$fields_file = $path . 'fields.ini';

					if(file_exists($fields_file)){
						$content = file_get_contents($fields_file);
						if($content!=''){
							$content = json_decode($content,true);
							if($files){
								foreach ($files as $file) {
									if(isset($content[$file->img])){
										$file->content = $content[$file->img];
									}
								}
							}
						}
					}
				}
			}
			if( $files ) {
	
				foreach ($files as $image) {
					$alt = '';
					if(isset($image->content)){
						$alt = $image->content['alt'];
					}
					$ext = explode('.', $image->img);
					$filename = $ext[0];
					$filetype = $ext[1];

					if ($image_tag){
						$html .= "<img title='".$alt."' imageType='images' type='varation' itemId='".$varationId."' fullurl='".$urlPath.$filename.$type.'.'.$filetype."' src='".$urlPath.$filename.$type.'.'.$filetype."' >";
					}
					else{
						$html .= $urlPath.$filename.$type.'.'.$filetype;	
					}
					$havaImage = true;
				}
			}
		}
		if($havaImage){
			return $html;
		}else{
			return false;
		}
	}
	public function getPhotoByType($optionId,$type,$imageSize = false){
		$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. $type .DIRECTORY_SEPARATOR. $optionId .DIRECTORY_SEPARATOR;
		$config = Pago::get_instance('config')->get('global');
		//echo $path;die;
		$pathForWeb = JURI::root() . 'media/pago/'. $type .'/'. $optionId .'/';

		$file = false;
		if(file_exists($path)){
			if ($handle = opendir($path)){
				$pathinfo = pathinfo($path);
				$folder_name = $pathinfo['basename'];
				while (false !== ($entry = readdir($handle))) {
					if($entry!="." && $entry!=".." && !is_dir($path.DIRECTORY_SEPARATOR .$entry) && $entry!='fields.ini' && $entry!='index.html'  && !strpos($entry, "-")){
						$file = new stdClass;
						$file->img = $entry; 
						break;
					}
				}
			}
		}
		if($file){
			$ext = explode('.', $file->img);
			$filename = $ext[0];
			$filetype = $ext[1];

			if($imageSize){
				return $pathForWeb.$filename.'-'.$imageSize.'.'.$filetype;
			}else{
				return $pathForWeb.$filename.'-large.'.$filetype;
			}
		}else{
			
			$sizes = (array) $config->get( 'media.image_sizes' );
			
			$optionId = (int)$optionId;
			
			//note this is ready to load all variation image, but we don't have front to support it yet
			$db = JFactory::getDBO();
			$query = "SELECT * FROM #__pago_files 
						WHERE provider = {$optionId} AND published = 1 
							ORDER BY ordering";
	
			$db->setQuery( $query );
			$image = $db->loadObject();
			
			if(!$image) return false;
			
			return $pathForWeb.$image->file_name;
		}
	}
	public function getSameVarationImagePath($varationId,$imageSize){
		$varationId = (int)$varationId;
		$varation = $this->get_product_varations_by_id( $varationId, true);
		
		$imagePath = false;
		
		$sameVarations = $this->findSameVaration($varation);
		if($sameVarations){
			$findImage = false;

			foreach ($sameVarations as $sameVarationId) {
				$imagePath = $this->getPhotoByType($sameVarationId,'product_variation',$imageSize);

				if($imagePath){
					$html = $imagePath;
					break;
				}
			}		
		}
		return $imagePath;
	}
	public function checkDefaultVariation($varationId){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__pago_product_varation WHERE id = ".$varationId;

		$db->setQuery( $query );
		$defVar = $db->loadObject();
		if($defVar->default==1){
			return true;
		}
		
		return false;
	}
	
	public function deleteAttributeOption($optId)
	{
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__pago_attr_opts WHERE id = '" . $optId . "'";
		$db->setQuery( $query );
		$db->query();
	}
}