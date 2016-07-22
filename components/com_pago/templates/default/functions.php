<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

class template_functions extends JViewLegacy
{
	/**
	 * Method to load template
	 *
	 * @access	public
	 * @return	template
	 * @since	1.0
	 *
	 */
	public function load_template( $folder, $layout, $template )
	{
		jimport( 'joomla.application.component.view' );

		$this->addTemplatePath( JPATH_ROOT . '/components/com_pago/templates/default/'. $folder );
		$this->setLayout( $layout );
		echo $this->loadTemplate( $template );
	}


	//Display Attributes NEW
	public function display_attribute( $item ) {

		Pago::load_helpers( array( 'attributes' ) );


		$attributes = PagoAttributesHelper::get_item_attributes( $item );
		$removeDefault = false;
		if ( $attributes ) {
			$default = array();
			foreach ($attributes as $attribute) {
				if(isset($attribute->options) && $attribute->preselected == 1){
					foreach ($attribute->options as $option) {
						if($option->default == 1){
							$default[$attribute->id] = $option->id;
						}
					}
				}
			}

			if(!empty($default) && count($default) > 1) {
				$attributeModel = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
				$removeDefault = $attributeModel->check_varation_can_be($default,$item->id);
			}
		}

		$html = '';

		if ( $attributes ) {
			foreach ($attributes as $attribute) {
				if($removeDefault && count($removeDefault) > 0){
					if (in_array($attribute->id, $removeDefault)) {
						$attribute->preselected = 0;
					}
				}
				if(isset($attribute->options)){
					$html .= '<div><label class="pg-attribute-label" for="pg-attribute-' . $attribute->id . '">' . $attribute->name . ':</label></div>';
					switch ($attribute->type) {
							case '0': // color
								if( $attribute->options ) {
									$html .= "<div class='pg_attr_options' attrType='".$attribute->type."' attrDisplayType='".$attribute->display_type."' id='pg_attr_". $attribute->id ."'>";

									switch ($attribute->display_type) {
										case '0':
											$html .= "<select name='attrib[".$attribute->id."]' onchange='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.")'>";
												$html .= "<option value='0' class='pg_attribute_option pg_color_option_list' >".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</option>";
											foreach ($attribute->options as $option) {
												$selected = '';
												if($attribute->preselected == 1 && $option->default == 1){
													$selected = 'selected="selected"';
												}
												$html .= "<option value='". $option->id ."' ".$selected." class='pg_attribute_option pg_color_option_list' style='background-color:". $option->color ."' id='attr_option_". $option->id ."'>".$option->name."</option>";
											}
											$html .= "</select>";
										break;
										case '1':
											foreach ($attribute->options as $option) {
												$class = '';
												$preValue = 0;
												if($attribute->preselected == 1 && $option->default == 1){
													$class = 'active';
													$preValue = 1;
												}
												$html .= "<input class='attr_input' type='hidden' id='attr_input_". $option->id ."' name='attrib[".$attribute->id."][".$option->id."][selected]' value='".$preValue."' />";
												$html .= "<span title=".$option->name." onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_attribute_option pg_color_option_list ".$class."' style='background-color:". $option->color ."' id='attr_option_". $option->id ."'></span>";
											}
										break;
										case '2':
											// es em avelacrel
											$html .= "<input name='attrib[".$attribute->id."]' type='radio' title=".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')." value='0' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0);' class='attr_radio' id='attr_option_0'><span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0);' class='pg_color_option_list pg_color_radio'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
											foreach ($attribute->options as $option) {
												$checked = '';
												$class = '';

												if($attribute->preselected == 1 && $option->default == 1){
													$checked = 'checked';
													$class = 'active';
												}
												$html .= "<input name='attrib[".$attribute->id."]' ".$checked." type='radio' title=".$option->name." value='".$option->id."' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_attribute_option attr_radio'  id='attr_option_". $option->id ."'><span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_color_option_list ". $class ."' style='background-color:". $option->color ."'></span>";
											}
										break;
									}

									$html .= "</div>";
									$html .= "<div class='attribute_con_both'></div>";
									$html .= template_functions::attribute_option_form( $attribute, $item->price );
								}
							break;

							case '1': // size
								if( $attribute->options ) {
									$html .= "<div class='pg_attr_options' attrType='".$attribute->type."' attrDisplayType='".$attribute->display_type."' id='pg_attr_". $attribute->id ."'>";

									switch ($attribute->display_type) {
										case '0':
											$html .= "<select name='attrib[".$attribute->id."]' onchange='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.")'>";
												$html .= "<option value='0' class='pg_attribute_option pg_size_option_list' >".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</option>";
											foreach ($attribute->options as $option) {
												$selected = '';

												if($attribute->preselected == 1 && $option->default == 1){
													$selected = 'selected="selected"';
												}
												$html .= "<option value='". $option->id ."' ".$selected." class='pg_attribute_option pg_size_option_list' id='attr_option_". $option->id ."'>".$option->name."</option>";
											}
											$html .= "</select>";
										break;
										case '1':
											foreach ($attribute->options as $option) {
												$class = '';
												$preValue = 0;
												if($attribute->preselected == 1 && $option->default == 1){
													$class = 'active';
													$preValue = 1;
												}
												$html .= "<input class='attr_input' type='hidden' id='attr_input_". $option->id ."' name='attrib[".$attribute->id."][".$option->id."][selected]' value='".$preValue."' />";
												$html .= "<span title=".$option->name." onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_attribute_option pg_size_option_list ".$class."' id='attr_option_". $option->id ."'>".$option->name."</span>";
											}
										break;
										case '2':
											$html .= "<input name='attrib[".$attribute->id."]' type='radio' name='".$attribute->id."' title=".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')." value='0' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0);' class='attr_radio'  id='attr_option_0'><span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0);' class='pg_size_option_list'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
											foreach ($attribute->options as $option) {
												$checked = '';
												$class = '';

												if($attribute->preselected == 1 && $option->default == 1){
													$checked = 'checked';
													$class = 'active';
												}
												$html .= "<input name='attrib[".$attribute->id."]' ".$checked." type='radio' name='".$attribute->id."' title=".$option->name." value='".$option->id."' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_attribute_option attr_radio'  id='attr_option_". $option->id ."'><span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_size_option_list ". $class ."'>".$option->name."</span>";
											}
										break;
									}

									$html .= "</div>";
									$html .= "<div class='attribute_con_both'></div>";
									$html .= template_functions::attribute_option_form( $attribute, $item->price );
								}
							break;

							case '2': // Material
								if( $attribute->options ) {
									$html .= "<div class='pg_attr_options' attrType='".$attribute->type."' attrDisplayType='".$attribute->display_type."' id='pg_attr_". $attribute->id ."'>";

									switch ($attribute->display_type) {
										case '0':
											$html .= "<select name='attrib[".$attribute->id."]' onchange='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.")'>";
												$html .= "<option value='0' class='pg_attribute_option pg_material_option_list' >".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</option>";
											foreach ($attribute->options as $option) {
												$selected = '';

												if($attribute->preselected == 1 && $option->default == 1){
													$selected = 'selected="selected"';
												}
												$html .= "<option value='". $option->id ."' ".$selected." class='pg_attribute_option pg_material_option_list' id='attr_option_". $option->id ."'>".$option->name."</option>";
											}
											$html .= "</select>";
										break;
										case '1':
											foreach ($attribute->options as $option) {
												$class = '';
												$preValue = 0;
												if($attribute->preselected == 1 && $option->default == 1){
													$class = 'active';
													$preValue = 1;
												}
												$html .= "<input class='attr_input' type='hidden' id='attr_input_". $option->id ."' name='attrib[".$attribute->id."][".$option->id."][selected]' value='".$preValue."' />";
												$html .= "<span title=".$option->name." onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_attribute_option pg_material_option_list ".$class."' id='attr_option_". $option->id ."'>".$option->name."</span>";
											}
										break;
										case '2':
												$html .= "<input name='attrib[".$attribute->id."]' type='radio' name='".$attribute->id."' title=".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')." value='0' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0);' class='attr_radio' id='attr_option_0'><span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0);' class='pg_material_option_list'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
											foreach ($attribute->options as $option) {
												$checked = '';
												$class = '';

												if($attribute->preselected == 1 && $option->default == 1){
													$checked = 'checked';
													$class = 'active';
												}
												$html .= "<input name='attrib[".$attribute->id."]' ".$checked." type='radio' name='".$attribute->id."' title=".$option->name." value='".$option->id."' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_attribute_option attr_radio'  id='attr_option_". $option->id ."'><span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_material_option_list ". $class ."'>".$option->name."</span>";
											}
										break;
									}

									$html .= "</div>";
									$html .= "<div class='attribute_con_both'></div>";
									$html .= template_functions::attribute_option_form( $attribute, $item->price );
								}
							break;
							case '3': // Custom
								if( $attribute->options ) {
									$html .= "<div class='pg_attr_options' attrType='".$attribute->type."' attrDisplayType='".$attribute->display_type."' id='pg_attr_". $attribute->id ."'>";

									switch ($attribute->display_type) {
										case '0':
											$html .= "<select name='attrib[".$attribute->id."]' onchange='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.")'>";
												$html .= "<option value='0' class='pg_attribute_option pg_custom_option_list' >".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</option>";
											foreach ($attribute->options as $option) {
												$selected = '';

												if($attribute->preselected == 1 && $option->default == 1){
													$selected = 'selected="selected"';
												}
												$html .= "<option value='". $option->id ."' ".$selected." class='pg_attribute_option pg_custom_option_list' id='attr_option_". $option->id ."'>".$option->name."</option>";
											}
											$html .= "</select>";
										break;
										case '1':
											foreach ($attribute->options as $option) {
												$class = '';
												$preValue = 0;
												if($attribute->preselected == 1 && $option->default == 1){
													$class = 'active';
													$preValue = 1;
												}
												$html .= "<input class='attr_input' type='hidden' id='attr_input_". $option->id ."' name='attrib[".$attribute->id."][".$option->id."][selected]' value='".$preValue."' />";
												$html .= "<span title=".$option->name." onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_attribute_option pg_custom_option_list ".$class."' id='attr_option_". $option->id ."'>".$option->name."</span>";
											}
										break;
										case '2':
											$html .= "<input name='attrib[".$attribute->id."]' type='radio' name='".$attribute->id."' title=".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')." value='0' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0);' class='attr_radio' id='attr_option_0'><span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0);' class='pg_custom_option_list'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
											foreach ($attribute->options as $option) {
												$checked = '';
												$class = '';

												if($attribute->preselected == 1 && $option->default == 1){
													$checked = 'checked';
													$class = 'active';
												}
												$html .= "<input name='attrib[".$attribute->id."]' ".$checked." type='radio' name='".$attribute->id."' title=".$option->name." value='".$option->id."' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_attribute_option attr_radio'  id='attr_option_". $option->id ."'><span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.");' class='pg_custom_option_list ". $class ."'>".$option->name."</span>";
											}
										break;
									}

									$html .= "</div>";
									$html .= "<div class='attribute_con_both'></div>";
									$html .= template_functions::attribute_option_form( $attribute, $item->price );
								}
							break;

							default:

								break;
					}
				}
			}
		}
		return $html;
	}
	static public function get_attribute_images($itemId,$type){
		Pago::load_helpers( array( 'attributes' ) );
		$itemModel = JModelLegacy::getInstance( 'Item', 'PagoModel' );
		$item = $itemModel->getItem($itemId);
		$html = '';
		if($type == "full"){
			$type ='';
		}else{
			$type ='-'.$type;
		}
		if($item){
			$attributes = PagoAttributesHelper::get_item_attributes( $item );
			if ( $attributes ) {
				foreach ($attributes as $attribute) {
					if(isset($attribute->options)){
						foreach ( $attribute->options as $option ) {
							$path = JURI::root() . 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'attr_opt' .DIRECTORY_SEPARATOR. $option->id .DIRECTORY_SEPARATOR;
							if( $option->images ) {
								foreach ($option->images as $image) {
									$alt = '';
									if(isset($image->content)){
										$alt = $image->content['alt'];
									}
									$ext = explode('.', $image->img);
									$filename = $ext[0];
									$filetype = $ext[1];

									$html .= "<li style='list-style: none;'>";
										$html .= "<img title='".$alt."' class='changeAttributeSelect' type='attribute' itemId='".$option->id."' fullurl='".$path.$filename.$type.'.'.$filetype."' src='".$path.$filename.'-thumbnail.'.$filetype."' >";
									$html .= "</li>";
								}
							}
						}
					}
				}
			}
		}
		return $html;
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
		$varationsJson = template_functions::objectToArray($varations);
		if($type != "-1"){
			$return['images'] = $html;
		}
		$return['jsonVarations'] = json_encode($varationsJson);

		return  $return;
	}
	static public function get_varation($varationId,$type='-1'){
		Pago::load_helpers( array( 'attributes' ) );
		$html = '';
		$varations = false;

		if($type == "full"){
			$type ='';
		}else if ($type != '-1'){
			$type ='-'.$type;
		}
		
		if($varationId){
			$attributeModel = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
			$varation = $attributeModel->get_product_varations_by_id( $varationId, true);

			if ( $varation && $type != "-1" ) {
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

						$html .= "<img title='".$alt."' imageType='images' type='varation' itemId='".$varation->id."' fullurl='".$urlPath.$filename.$type.'.'.$filetype."' src='".$urlPath.$filename.$type.'.'.$filetype."' >";
					}
				}else{
					$sameVarations = $attributeModel->findSameVaration($varation);
					if($sameVarations){
						$findImage = false;

						foreach ($sameVarations as $sameVarationId) {
							$sameImages = $attributeModel->getVarationImages($sameVarationId,$type);
							if($sameImages){
								$html = $sameImages;
								break;
							}
						}		
					}
				}
			}
		}
		$varation = $varation;
		if($type != "-1"){
			$return['images'] = $html;
		}
		$return['varation'] = $varation;

		return  $return;
	}
	static public function objectToArray($data)
    {
		if(is_array($data) || is_object($data))
		{
	            $result = array();
	            foreach ($data as $key => $value)
	            {
	                $result[$key] = template_functions::objectToArray($value);
	            }
	            return $result;
		}
		return $data;
    }

	public function attribute_option_form( $attribute, $price ){
		$html = '';
		if( $attribute->options ) {
			foreach ( $attribute->options as $option ) {
				$class = '';

				if($attribute->preselected == 1 && $option->default == 1){
					$class = 'attribute-active';
				}
				$optionPrice = false;
				$html .= "<div class='pg_attr_option_form pg_form_option_attr_".$attribute->id." ". $class ."' attr_id=". $attribute->id ." id='pg_attr_option_form_". $option->id ."'>";

					switch ($attribute->type) {
						case '0':
							$html .= "<div style='float:left'>";
								$html .= $option->name;
								$html .= "<span class='pg_color_option_form' style='background-color:". $option->color ."'></span>";
							$html .= "</div>";
						break;
						case '1':
							$html .= "<div style='float:left'>";
								$html .= $option->name . " " . $option->size;
								switch ($option->size_type) {
									case '0':
										$html .= " (".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US').")";
									break;
									case '1':
										$html .= " (".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL').")";
									break;
									case '2':
										$html .= " (".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK').")";
									break;
									case '3':
										$html .= " (".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE').")";
									break;
									case '4':
										$html .= " (".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY').")";
									break;
									case '5':
										$html .= " (".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA').")";
									break;
									case '6':
										$html .= " (".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN').")";
									break;
								}
								$html .= "<span class='pg_size_option_form'></span>";
							$html .= "</div>";
						break;
						case '2'://material
							$html .= "<div style='float:left'>";
								$html .= $option->name;
								$html .= "<span class='pg_material_option_form'></span>";
							$html .= "</div>";
						break;
						case '3'://custom
							$html .= "<div style='float:left'>";
								$html .= $option->name;
								$html .= "<span class='pg_custom_option_form'></span>";
							$html .= "</div>";
						break;
						default:

							break;
					}

					// if(strlen($option->sku) > 0){
					// 	$html .= "<span class='pg-item-sku'><span>".JText::_( 'PAGO_ITEM_SKU' )."</span>: ".$option->sku."</span>";
					// }
					$html .= "<div style='float:right;'>";

					if( !isset($option->in_stock) || ($option->in_stock != '' AND $option->in_stock <= 0)) {
						$html .= "<p class='pg-out-of-stock'>".JText::_("PAGO_OUT_OF_STOCK")."</p>";
					} else {
						if($option->price_sign == 1){

							$optionPrice = Pago::get_instance( 'price' )->calculateAttributePrice( $option->price_type, $price, $option->price_sum );

							$html .= "<div class='pg-attr-price' id='opt_price_original_".$option->id."' originaloptprice='".$optionPrice."'>";
								$html .= Pago::get_instance( 'price' )->format( $optionPrice );
							$html .= "</div>";


							// disable attrubute qty
							// if($option->show_qty == 1){
							// 	$html .= "<div class=pg-qty-con>";
							// 		$html .= "<label for='pg-attr-opt-qty-".$option->id."' class='pg-attr-qty-label'>".JText::_('PAGO_ITEM_QTY')."</label>
							// 				 <input onkeyup='considerPrice();' type='text' size='1' class='pg-inputbox' value='1' name='attrib[qty][".$attribute->id."][".$option->id."]' id='pg-attr-opt-qty-".$option->id."' />";
							// 		$html .= " <span class='pg-qty-control' attr=''>
							// 				 	<a href='javascript:void(0);' class='pg-qty-up' onclick='qtyChange(this,".$option->id.");'></a>
							// 				 	<a href='javascript:void(0);' class='pg-qty-down' onclick='qtyChange(this,".$option->id.");'></a>
							// 				 </span>";
							// 	$html .= "</div>";
							// }
						}
					}
					$html .= "<div stlye='clear:both; float:none;'></div>";
					$html .= "</div>";
					// if( $option->images ) {
					// 	$html .= "<div class='pg_attribute_option_gallery pg-item-images pg-image-thumbnails'>";
					// 		$html .= template_functions::attribute_option_gallery($option);
					// 	$html .= "</div>";
					// }
					$html .= "<div stlye='clear:both; float:none;'></div>";
				$html .= "</div>";
			}
		}

		return $html;
	}

	public function attribute_option_gallery($option){
		$html = '';
		$path = JURI::root() .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'attr_opt' .DIRECTORY_SEPARATOR. $option->id .DIRECTORY_SEPARATOR;

		$html .= "<ul id='mycarousel_opt_".$option->id."'  class='jcarousel-skin-tango pg-gallery-thumbs'>";
		$html .= "<script>jQuery(document).ready(function() {
    				jQuery('#mycarousel_opt_".$option->id."').jcarousel({

					    });
					});</script>";
		foreach ($option->images as $image) {
			$alt = '';
			if(isset($image->content)){
				$alt = $image->content['alt'];
			}
			$html .= "<li>";
				$html .= "<a rel='nofollow' ><img rel=".$path.$image->img."  title='".$alt."' class='pg-thumbnail-img' src=".$path.$image->img." /></a>";
			$html .= "</li>";
		}
		$html .= "</ul>";
		return $html;

	}
	//Display Attributes
	public function format_attribute( $attribute, $show_name = true, $list = false, $no_title = false ) {

		if($show_name === true ) {
			$name = $attribute['name'];
			$html = '<label for="pg-attribute-' . $attribute['id'] . '">' . $name . '</label>';
		} else {
			$html = '';
		}

		switch($attribute['type']) {
			case 'multiselect':
				$html .= '<select id="pg-attribute-' . $attribute['id'] . '" name="attrib['.$attribute['id'].']">';
				$html .= '<option value="" selected="selected">Select ' . $name . '</option>';

				foreach( $attribute['options'] as $option ) {
					$html .= '<option id="pg-attr-option-' . $option['id'] . '" value="' . htmlentities($option['name']) . '">';
					$html .= $option['name'];
					$html .= '</option>';
				}
				$html .= '</select>';

			case 'select':

				if($attribute['pricing'] == 1) {
					$attr_ul_class = 'pg-pricing-attributes';
					$pricing = true;
				} else {
					$attr_ul_class = 'pg-attributes';
				}

				($list == true) ? $options = '<ul class="' . $attr_ul_class . ' clearfix">' : $options = '';

				if($show_name == true) {
					if($list == true) {
						$options .= '<li>' . $name . '</li>';
					} else {
						$options .= $name;
					}
				}

				(empty($pricing)) ? '' : $options .= '<li>Qty:</li>';

				$total_options = count($attribute['options']);
				$counter = 1;

				foreach($attribute['options'] as $option) {
					if(!empty($pricing)) {

						$session = JFactory::getSession();
						$state_abbr = $session->get( 'postalState', NULL );

						if( ($option['price'] == '0.00' || $option['price'] == '')
						&& $option['quantity'] > 0 ) {
							$class = ' not-available';
							$disabled = 'disabled="disabled"';
							$option_price = 'N/A in ' . $state_abbr;
						} elseif($option['quantity'] == 0 && $option['price'] == 0.00 ) {
							$class = ' outofstock';
							$disabled = 'disabled="disabled"';
							$option_price = 'Out of Stock';
						} else {
							$class = '';
							$disabled = '';
							$option_price = '$' . $option['price'];
						}
						$options .= '<li>
										<ul class="pricing-attribute' . $class . '">
											<li class="attribute-qty"><input type="text" name="attrib['.$attribute['id'].'][' . $option['id'] . '][qty]"' .
											' id="' . $option['id'] . '_qty" class="quantity" size="3" maxlength="4"' . $disabled . ' /></li>
											<li class="attribute-name">' . $option['name'] . '</li>
											<li class="attribute-price">' . $option_price . '</li>
											<input type="hidden" name="attrib['.$attribute['id'].']['.$option['id'].'][name]" value="'.htmlentities($option['name']).'" />
											<input type="hidden" name="attrib['.$attribute['id'].']['.$option['id'].'][price]" value="'.$option['price'].'" />
										</ul>
									</li>';
					} else {
						if($total_options == 1) {
							$comma = '';
						} elseif($counter < $total_options) {
							$counter++;
							$comma = ',&nbsp;';
						} elseif($counter == $total_options) {
							$comma = '';
						}

						if($list == true) {
							$options .= '<li>' . $option['name'] . $comma . '</li>';
						} else {
							$options .= '<span>' . $option['name'] . $comma . '</span>';
						}
					}
				}

				($list == true) ? $options .= '</ul>' : '';
				break;

			case 'textarea':
				foreach ( $attribute['options'] as $option ) {
					if( $no_title == true ) {
						$options = $option['value'];
					} else {
						$options = $name;
						$options .= '<p>' . $option['value'] . '</p>';
					}
				}
				break;

			case 'text':
			default:
				$options = '';
				foreach ( $attribute['options'] as $option ) {
					if( $no_title == true ) {
						$options .= $option['value'];
					} elseif(!empty($option['name']) && !empty($option['value'])) {
						$options .= '<tr>';
						$options .= '<td class="attribute-name">' . $option['name']
								 . ':</td><td class="attribute-value">' . $option['value'] . '</td>';
						$options .= '</tr>';
					} elseif(!empty($option['value'])) {
						$options .= $name;
						$options .= '<span>' . $option['value'] . '</span>';
					}
				}
				break;
		}
		return $html;
	}

	//Display Primary Item Image
	public function display_image($images, $size, $alt = '', $title = '', $attributes = '') {

		// Making sure that the array of images are objects, if not change to object
		// (encountered a problem with the objects getting converting to arrays during JSON encode
		//	or decode for the cart cookie)
		if( !empty ( $images ) ) {
			if( is_array( $images[0] ) ) {
				foreach( $images as $k => $image )
					$images[$k] = (object) $image;
			}

			foreach($images as $image) {
				if($image->default == 1 || $image->type == 'store_default') {
					($size == 'gallery') ? $image_url = '' : $image_url = PagoImageHandlerHelper::get_image_from_object( $image, $size, true );

					if( empty( $image->caption ) ) {
						$alt_tag = $alt;
						$title_tag = $title;
					} else {
						$alt_tag = $image->caption;
						$title_tag = $image->caption;
					}

					$primary = '';

					if( $image->type != 'store_default' && $size != 'gallery')
						$primary .= '<a id="pg-main-image" rel="nofollow" href="'
								 . JRoute::_( 'index.php?option=com_pago&view=item&layout=gallery&async=2&tmpl=component&id='
								 . $image->item_id ) . '" class="pg-quickview" title="' . $title_tag . '">';

					$primary .= '<img src="' . $image_url . '" ' . $attributes;

					if( is_array( $size ) ) {
						$dimensions = ' width="' . $size[0] . '" height="' . $size[1] . '"';
					} elseif ( $size == 'quickview' ) {
						$imagedata = PagoHelper::maybe_unserialize($image->file_meta);
						if( !( array_search( $size, array_keys( (array) $imagedata['sizes'] ) ) ) ) {
							$dimensions = ' width="' . $imagedata['width'] . '" height="' . $imagedata['height'] . '"';
						} else {
							$imagedata = $imagedata['sizes'][$size];
							$dimensions = ' width="' . $imagedata['width'] . '" height="' . $imagedata['height'] . '"';
						}
					} else {
						$dimensions = '';
					}

					$primary .= ' id="pg-imageid-' . $image->id . '" class="pg-' . $size
							 . '-img" title="' . $title_tag . '" alt="' . $alt_tag . '" ' . $dimensions . ' />';

					if( $image->type != 'store_default' && $size != 'gallery' )
						$primary .= '</a>';

					echo $primary;
					break;
				}
			}
		}
	}

	//Display Thumbnail Images
	public function list_images($images, $size, $config, $link_size, $alt = '', $title = '') {
		if($link_size == 'large') {
			$max = $config->get( 'img_thumb_amount_item', 4 );
		} elseif($link_size == 'medium') {
			$max = 3;
		}

		if(count( $images ) < $max ) {
			$max = count($images);
		}

		$thumbnails = "<ul class=\"pg-image-{$size}s\">";

		for ( $i = 0; $i < $max; $i++ ) {
			if( empty( $images[$i]->caption ) ) {
				$alt_tag = $alt;
				$title_tag = $title;
			} else {
				$alt_tag = $images[$i]->caption;
				$title_tag = $images[$i]->caption;
			}
			if($images[$i]->type == 'store_default') {
				return;
			}

			$image = PagoImageHandlerHelper::get_image_from_object( $images[$i], $size, true );
			$image_link = PagoImageHandlerHelper::get_image_from_object( $images[$i], $link_size, true );

			$thumbnails .= '<li><span>';
			$thumbnails .= '<a rel="nofollow" href="'
						. JRoute::_( 'index.php?option=com_pago&view=item&layout=gallery&async=2&tmpl=component&id='
						. $images[$i]->item_id ) . '" class="pg-gallery" title="' . $title_tag . '">';
			$thumbnails .= '<img src="' . $image . '"';
			$thumbnails .= "class=\"pg-$size-img\" id=\"pg-imageid-" . $images[$i]->id . "\" title=\""
						. $title_tag . "\" alt=\"" . $alt_tag . "\" rel=\"" . $image_link . "\" />";
			$thumbnails .= '</a></span></li>';
		}

		$thumbnails .= '</ul>';

		echo $thumbnails;
	}

	//Display Gallery Thumbnail Images
	public function list_gallery_images($images, $size, $type, $link_size) {
		$thumbnails = "<ul>";

		for ( $i = 0; $i < count($images); $i++ ) {

			if($images[$i]->type == $type) {

				$image = PagoImageHandlerHelper::get_image_from_object( $images[$i], $size, true );
				$image_link = PagoImageHandlerHelper::get_image_from_object( $images[$i], $link_size, true );

				$thumbnails .= '<li>';
				$thumbnails .= '<img src="' . $image . '"';
				$thumbnails .= "class=\"pg-$size-img\" id=\"pg-thumbimageid-" . $images[$i]->id . "\" title=\""
							. $images[$i]->caption . "\" alt=\"" . $images[$i]->caption . "\" rel=\"" . $image_link . "\" />";
				$thumbnails .= '</li>';
			}
		}

		$thumbnails .= '</ul>';

		echo $thumbnails;
	}

	public function item_categories($categories, $item) {
		$category_uls = $categories->get_cat_ul(array(
			'categories' => $item->categories,
			'depth' => 0,
			'show_unpublished' => false,
			'cache' => false,
			'get_item_count' => false,
			'no_link' => true,
			'ul' => array(
				'ul_class' => 'item-cat-ul',
				'li_class' => 'item-cat-li',
				'parent_class' => 'item-cat-parent',
				'published_class' => 'item-cat-published',
				'unpublished_class' => 'item-cat-unpublished',
			)
		));
		return $category_uls->ul;
	}

	public function todo($msg) {
		echo '<h1 class="todo">' . $msg . '</h1>';
	}

	public function truncate( $string, $limit, $stop = " ", $append = "..." ) {
		if( strlen( $string ) <= $limit )
			return $string;

		if( ( $stop = strpos( $string, $stop, $limit ) ) !== false ) {
			if( $stop < strlen( $string ) - 1 ) {
				$string = substr( $string, 0, $stop ) . $append;
			}
		}
		return $string;
	}

	public function order_status( $status ) {
		switch( $status ){
			/* pretty sure this isn't a status since I couldn't find anything about it in the backend code (pago_config.php) - ap
			case 'F':
				return JText::_( 'PAGO_ORDER_STATUS_COMPLETED' );
				break;*/
			case 'X':
				return JText::_( 'PAGO_ORDER_STATUS_CANCELLED' );
				break;
			case 'R':
				return JText::_( 'PAGO_ORDER_STATUS_REFUNDED' );
				break;
			case 'S':
				return JText::_( 'PAGO_ORDER_STATUS_SHIPPED' );
				break;
			case 'C':
				return JText::_( 'PAGO_ORDER_STATUS_CONFIRMED' );
				break;
			case 'P':
			default:
				return JText::_( 'PAGO_ORDER_STATUS_PENDING' );
				break;
		}
	}
}
?>
