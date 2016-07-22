<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

class PagoAttributesHelper
{
	/**
	 * Gets all of files for an item
	 *
	 * @since 1.0
	 *
	 * @param item Model
	 **/
	static public function get_item_attributes( $item )
	{
		$db     = JFactory::getDBO();


		$query = "SELECT a.*, s.*
			FROM #__pago_attr AS a
			LEFT JOIN #__pago_attr_assign AS s ON ( a.id = s.attribut_id )
			WHERE !(SELECT count(*) from #__pago_items_attr_rel where attr_id = a.id and item_id = ".$item->id.") AND
			(SELECT count(*) from #__pago_product_varation_rel where attr_id = a.id and item_id = ".$item->id.")
				ORDER BY a.`ordering` ASC";

		// es selecta arac menak publish varatianer@ bayc vonc vor te petq chexav, nereqvi selectum et draca normal ashxatuma
		
		// $query = "SELECT a.*, s.*
		// 	FROM #__pago_attr AS a
		// 	LEFT JOIN #__pago_attr_assign AS s ON ( a.id = s.attribut_id )
		// 	WHERE !(SELECT count(*) from #__pago_items_attr_rel where attr_id = a.id and item_id = ".$item->id.") AND
		// 	(SELECT count(*) from #__pago_product_varation_rel as rel 
		// 		LEFT JOIN #__pago_product_varation as varation 
		// 		ON (rel.varation_id = varation.id)  
		// 		WHERE attr_id = a.id and rel.item_id = ".$item->id."
		// 		AND varation.var_enable = 1 AND varation.published = 1
		// 	)
		// 		ORDER BY a.`ordering` ASC";

		$db->setQuery( $query );


		$attributes = $db->loadObjectList();

		$itemAttributes = array();
		if( $attributes ){
			foreach ($attributes as $attribute) {
				
				if($attribute->assign_type == 1){ //assign to item
					if (!empty($attribute->assign_items)) {
						$attrItems = json_decode($attribute->assign_items);
						foreach ($attrItems as $attrItem) {
							if($item->id == $attrItem->id){
								$itemAttributes[] = $attribute;
							} 
						}
					}
				} elseif($attribute->assign_type == 2){ //assign to category	
					$query = "SELECT `category_id` FROM #__pago_attr_categories WHERE `attribut_id` = ".$attribute->id." AND `category_id` = ". $item->primary_category;
					$db->setQuery( $query );
					$catsId = $db->loadObjectList();
					if($catsId){
						$itemAttributes[] = $attribute;
					}
				}else{ //assign global
					if($attribute->for_item == 0){ // check have concret item
						$itemAttributes[] = $attribute;
					}else{ 
						if($attribute->for_item == $item->id){ //for this item
							$itemAttributes[] = $attribute;
						}
					}
				}
			}
		}
		
		foreach ($itemAttributes as $itemAttribute) {
			
			// get Attribute options
			$query = "SELECT * 
					FROM #__pago_attr_opts as opt
					WHERE `attr_id` = ".$itemAttribute->id." 
					AND `opt_enable` = 1 AND ( `for_item` = 0 OR for_item = ".$item->id.")
					AND !(SELECT count(*) from #__pago_items_attr_opt_rel where option_id = opt.id and item_id = ".$item->id.") 
					AND (SELECT count(*) from #__pago_product_varation_rel as rel 
						LEFT JOIN #__pago_product_varation as varation 
						ON (rel.varation_id = varation.id) 
						WHERE attr_id = ".$itemAttribute->id." 
						AND rel.opt_id = opt.id 
						AND rel.item_id = ".$item->id."
						AND varation.var_enable = 1 AND varation.published = 1
						) 
					ORDER by `ordering` ASC";

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
								if($entry!="." && $entry!=".." && !is_dir($path.DIRECTORY_SEPARATOR .$entry) && $entry!='fields.ini' && $entry!='index.html'  && !strpos($entry, "-")){
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
	static function get_attribute_for_cart($items){
		$db     = JFactory::getDBO();
		$itemAttributes = array();
		foreach ($items as $item) {
			if(isset($item->attrib)){
				foreach ($item->attrib as $key => $value) {
					$attributeVal = array();
					$attribute = false;
					$attribute_option = false;

					$query = "SELECT * FROM #__pago_attr WHERE `id` = {$key} AND `showfront` = 1";
					$db->setQuery( $query );

					$attribute = $db->loadObject();

					if($attribute){
						$optionid = $value;
						// disable option qty
						//$optionid = key($value);
						//$qty = $value[key($value)];
						
						$query = "SELECT * FROM #__pago_attr_opts WHERE `id` = {$optionid} AND `opt_enable` = 1 AND `attr_id` = {$key}";
						$db->setQuery( $query );
						$attribute_option = $db->loadObject();
					}

					if($attribute AND $attribute_option){
						$attributeVal['attribute'] = $attribute; 
						$attributeVal['attribute_option'] = $attribute_option; 	
						//$attributeVal['qty'] = $qty;
					}

					$item->attrsVal[] = $attributeVal;
				}
			}			
		}

		return $items;
	}
	static function get_preselected_varation($itemId){
		$db = JFactory::getDBO();

		$query = "SELECT `id` FROM #__pago_product_varation WHERE `item_id` = {$itemId} AND `preselected` = 1 AND var_enable = 1 AND published = 1";
		$db->setQuery( $query );
		$preselectedVarationId = $db->loadObject();

		if(!$preselectedVarationId){
			$query = "SELECT `id` FROM #__pago_product_varation WHERE `item_id` = {$itemId} AND `default` = 1 AND var_enable = 1 AND published = 1";
			$db->setQuery( $query );
			$preselectedVarationId = $db->loadObject();
		}
		return $preselectedVarationId;
	}
	static function get_default_varation($itemId){
		$db = JFactory::getDBO();


		$query = "SELECT `id` FROM #__pago_product_varation WHERE `item_id` = {$itemId} AND `default` = 1 AND var_enable = 1 AND published = 1";
		$db->setQuery( $query );
		$defaultVarationId = $db->loadObject();

		return $defaultVarationId;
	}

	static function get_all_varation($itemId){
		$db = JFactory::getDBO();
		$query = "SELECT `id` FROM #__pago_product_varation WHERE `item_id` = {$itemId}  AND var_enable = 1 AND published = 1";
		$db->setQuery( $query );
		$allVarations = $db->loadObjectList();
		return $allVarations;
	}

	static public function get_varations($itemId,$type='-1'){
		Pago::load_helpers( array( 'attributes' ) );
		$html = '';
		$varations = false;

		if($type == "full"){
			$type ='';
		}else if ($type != '-1'){
			$type ='-'.$type;
		}
		if($itemId){
			$attributeModel = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
			$varations = $attributeModel->get_product_varations_by_item_id( $itemId, true);
			if ( $varations && $type != "-1" ) {
				foreach ($varations as $varation) {
					if($varation->default == 1){
						continue;
					}
					$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $varation->id .DIRECTORY_SEPARATOR;
					$urlPath = JURI::root() . 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $varation->id .DIRECTORY_SEPARATOR;

					$files = false;
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

							$html .= "<li style='list-style: none;' class = 'swiper-slide'>";
								$html .= "<img title='".$alt."' imageType='images' class='changeAttributeSelect' type='varation' itemId='".$varation->id."' fullurl='".$urlPath.$filename.$type.'.'.$filetype."' src='".$urlPath.$filename.'-thumbnail.'.$filetype."' >";
							$html .= "</li>";
						}
					}
				}
			}
		}
		$varationsJson = PagoAttributesHelper::objectToArray($varations);
		if($type != "-1"){
			$return['images'] = $html;
		}
		$return['jsonVarations'] = json_encode($varationsJson);

		return  $return;
	}
	static public function objectToArray($data)
    {
		if(is_array($data) || is_object($data))
		{
	            $result = array();
	            foreach ($data as $key => $value)
	            {
	                $result[$key] = PagoAttributesHelper::objectToArray($value);
	            }
	            return $result;
		}
		return $data;
    }

    static function display_attribute( $item ) {

	$attributes = PagoAttributesHelper::get_item_attributes( $item );

	$removeDefault = false;
	$html = '';

	$attr_type=array('color','size','material','custom');
	$size_type=array(
		JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US'),
		JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL'),
		JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK'),
		JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE'),
		JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY'),
		JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA'),
		JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN')
		);
	if ( $attributes ) {
		foreach ($attributes as $attribute) {
			if(isset($attribute->options)){
				$isBorder = 'has-border-bottom';
				if ($attribute->display_type == 0){
					$isBorder = '';
				}

				$isColorList = '';
				if ($attribute->display_type == 1 && $attribute->type==0){
					$isColorList = 'color-list';
				}

				$html .= "<div class='pg-attribute-product-container pg-product-field ".$isBorder." clearfix' type='".$attr_type[$attribute->type]."'>";
					$html .= '<div class = "pg-attr-'.$attr_type[$attribute->type].'"><label class="pg-attribute-label '.$isColorList.'" for="pg-attribute-' . $attribute->id . '">' . $attribute->name . ':</label></div>';
					if( $attribute->options ) {
						$html .= "<div class='pg_attr_options pg_attr_". $attribute->id ."' attr_id='".$attribute->id."' attrType='".$attribute->type."' attrDisplayType='".$attribute->display_type."'>";

						switch ($attribute->display_type) {
							case '0': //dropdown
								if($attribute->type==0){
									$doc = JFactory::$document;
									$style_colors = '';
									foreach ($attribute->options as $option) {
										$style_colors .= '.pg-color-'.$option->name.':after{
											background-color:'.$option->color.';
										}';
									}
									$doc->addStyleDeclaration($style_colors);
								}

								$html .= "<select name='attrib[".$attribute->id."]' onchange='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",\"\",".$item->id.")'>";
								if($attribute->required != 1){
									$html .= "<option value='0' selected = 'selected' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list pg-".$attr_type[$attribute->type]."-none' rel = 'none'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</option>";
								}
								foreach ($attribute->options as $option) {
									$html .= "<option value='".$option->id."' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list pg-".$attr_type[$attribute->type]."-".$option->name." attr_option_". $option->id ."' attr_option='".$option->id."' rel='".$option->name."'>".$option->name."</option>";
								}
								$html .= "</select>";
							break;
							case '1': //List
								foreach ($attribute->options as $option) {
									$preValue = 0;
									$custom_style='';
									if($attribute->type==0){
										$custom_style = "style='background-color:". $option->color."'";
									}
									$required = "";
									if($attribute->required == 1){
										$required = "required='1'";
									}

									$html .= "<input class='attr_input attr_option_". $option->id ."' opt_id='". $option->id ."' type='hidden' name='attrib[".$attribute->id."][".$option->id."][selected]' value='".$preValue."' />";
									$html .= "<span ".$required." title=".$option->name." onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list attr_option_". $option->id ."' attr_option='".$option->id."' ".$custom_style." >";
									if($attribute->type==0) $html .= "</span>";
									else $html .= $option->name."</span>";	
								}
							break;
							case '2':

								if($attribute->required != 1){
									$html .= "<input name='attrib[".$attribute->id."]' type='radio' title=".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')." value='0' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class='attr_radio attr_option_0' >";
									if($attribute->type==0){
										$html .= "<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class='pg_".$attr_type[$attribute->type]."_option_list pg_".$attr_type[$attribute->type]."_radio pg-".$attr_type[$attribute->type]."-none'></span>";
										$html .= "<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class = 'pg-attr-".$attr_type[$attribute->type]."-radio-none-name'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
									}else{
										$html .= "<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class = 'pg_".$attr_type[$attribute->type]."_option_list pg-attr-".$attr_type[$attribute->type]."-radio-none-name'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
									}
								}
								foreach ($attribute->options as $option) {
									$custom_style='';
									if($attribute->type==0){
										$custom_style = "style='background-color:". $option->color."'";
									}
									$html .= "<input name='attrib[".$attribute->id."]' type='radio' title=".$option->name." value='".$option->id."' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_attribute_option attr_radio attr_option_". $option->id ."' >";
									if($attribute->type==0){
										$html .="<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_".$attr_type[$attribute->type]."_option_list' ".$custom_style."></span>";
										$html .="<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class = 'pg-attr-".$attr_type[$attribute->type]."-radio-name'>".$option->name."</span>";
									}else{
										$html .= "<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class = 'pg_".$attr_type[$attribute->type]."_option_list pg-attr-".$attr_type[$attribute->type]."-radio-name'>".$option->name."</span>";
									}
								}
							break;
						}

						$html .= "</div>";
					}
				$html.="</div>";
			}
		}
	}
	return $html;
	}

}

?>
