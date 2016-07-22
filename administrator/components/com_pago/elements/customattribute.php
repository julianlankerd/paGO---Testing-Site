<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldCustomAttribute extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'CustomAttribute';
	var $_data = array();

	protected function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$attr = $node->attributes();
		$this->attr_name = $attr['name'];
		$this->callback = $attr['callback'];
		$this->getData();
		return $this->html( $name, $value, $node );
	}

	function getToolbar()
	{
		$bar = new JToolBar( $this->attr_name . '-Toolbar' );

		$array = JFactory::getApplication()->input->get( 'cid',  0, 'array' );
		$product_id = (int) $array[0];

		echo PagoHelper::addCustomButton( 'Add ' . ucfirst( $this->callback ),
			"return jQuery.customAttribute('addField');",
			'new', '#', 'toolbar', '', $bar );

		echo PagoHelper::addCustomButton(JText::_( 'PAGO_DELETE'),
			"javascript:delete_files('."  .$this->callback. "-checkboxes');",
				'delete', 'javascript:void(0);', 'toolbar', '', $bar);

		return $bar->render();
	}

	function getData()
	{
		if ( empty( $this->_data ) ) {
			$array = JFactory::getApplication()->input->get( 'cid',  0, 'array' );
			$item_id = (int) $array[0];

			$db = JFactory::getDBO();
			$attribute_model = JModelLegacy::getInstance('Attribute', 'PagoModel');

			if($item_id){	
				$query = 'SELECT `id`,`primary_category`,`price` FROM #__pago_items WHERE id = ' . $item_id;
				$db->setQuery( $query );
				$item = $db->loadObject();
				$this->_data['item'] =  $item;
				$this->_data['attribute'] =  $attribute_model->get_custom_attributes($item_id);
			}
		}

		return $this->_data;
	}

	function html( $name, $value, $node )
	{
		$_name = 'custom_attr';
		PagoHtml::behaviour_jquery();

		$doc = JFactory::getDocument();

		//$script  = 'var uploadifivePath="'. JURI::root() ."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_pago".DIRECTORY_SEPARATOR."javascript".DIRECTORY_SEPARATOR."uploadify".DIRECTORY_SEPARATOR."uploadifive.php".'";';
		$script  = 'uploadifivePath="index.php?option=com_pago&controller=attributes&task=uploadfile";';
		$script .= 'var JPATH_ROOT="'. JPATH_ROOT .'";';
		$script .= 'var JURI="'.JURI::root( true ) .'/'.'"';


		$doc->addScriptDeclaration( $script );
			

		// PagoHtml::add_js( JURI::base( true )
		// 	. '/components/com_pago/javascript/uploadify/jquery.uploadifive.js' );

		// PagoHtml::add_css( JURI::base( true )
		// 	. '/components/com_pago/javascript/uploadify/uploadifive.css' );

		PagoHtml::add_js( JURI::base( true )
			. '/components/com_pago/javascript/com_pago_custom_attribute.js' );

		PagoHtml::add_js( JURI::base( true )
			. '/components/com_pago/javascript/jscolor/jscolor.js' );
		
		ob_start();
		
		//exit();
	?>
	<?php
		//$attribute_model = JModel::getInstance('Attribute', 'PagoModel');
		//$attributeHtml = $attribute_model->get_custom_attribute_html($this->_data);
		//echo $attributeHtml;
		if(isset($this->_data['attribute'])){
			$attributeData = $this->_data['attribute'];
		}else{
			$attributeData = false;	
		}
		if(isset($this->_data['item'])){
			$item = $this->_data['item'];
		}else{
			$item = false;	
		}
		$attribute_model = JModelLegacy::getInstance('Attribute', 'PagoModel');
		// var_dump($this->getToolbar());
		// exit();
	?>
	<div class="clear"></div>
	<div id="sub-tabs" class="sub-tab"> <!-- tab start -->
		<div class="pg-tabs"> <!-- pg-tabs start -->
			<ul class="cutom_attibute_tabs pg-mb-20 clearfix" id="cutom_attibute_tabs">
				<li class="first active"><a tab="ca_config" href="javascript:void(0);"><?php echo JText::_('PAGO_CUSTOM_ATTRIBUTES_TAB_CONFIG') ?></a></li>
				<li class="last"><a tab="ca_manager" href="javascript:void(0);"><?php echo JText::_('PAGO_CUSTOM_ATTRIBUTES_TAB_MANAGER') ?></a></li>
			</ul>
			<div class="tab-content">
			 	<div class="tab-pane active" id="ca_config"><!-- tab1 start -->
				 	<!-- add attribute start -->
					<div class="add_custom_attribute_con">
						<a href="javascript:void(0);" onclick="showAddAttribute(<?php echo $item->id;?>);" class="pg-btn-large pg-btn-light pg-btn-green pg-btn-add pg-right" data-toggle="modal" data-target="#addAttribute">
							<?php echo JText::_( "PAGO_CUSTOM_ATTRIBUTES_ADD_ATTRIBUTE" ); ?>
						</a>
						<div class="clear"></div>
						<div class="add_custom_attribute_container modal fade modal-sm" id="addAttribute"></div>
					</div>
					<div class="clear"></div>
					<!-- add attribute end -->		
					<div class="pg-table-wrap"> <!-- pg-table-wrap start -->
					<?php if($attributeData) { ?>
						<?php foreach ($attributeData as $attribute) { ?>
							<?php
								$attributeTitle = $attribute_model->get_custom_attribute_title($attribute,$item->id);
								echo $attributeTitle;
							?>
							<div class = "pg-pad-20 pg-border">
								<table id="attr_table_<?php echo $attribute->id;?>" class="pg-table pg-custom-attr-table pg-repeated-rows">
								<?php 
									$attributeThead = $attribute_model->get_custom_attribute_thead($attribute,$item->id);
									echo $attributeThead;

									$attributeTbody = $attribute_model->get_custom_attribute_tbody($attribute,$item);
									echo '<tbody>';
										echo $attributeTbody;	
									echo '</tbody>';
								?>
									</tbody>
								</table>
							</div>
						<?php } ?>
					<?php } ?>
					</div><!-- pg-table-wrap end -->
					<!-- add attribute value start -->
					<div class="add_attribute_value_con">
						<div class="add_attribute_value_container modal fade modal-sm" id="addAttributeValue"></div>
					</div>
					<div class="clear"></div>
					<!-- add attribute value end -->
				</div><!-- tab1 end -->
				<div class="tab-pane" id="ca_manager">
					<div class="clear"></div>
					<div class="pg-info-con">
						<img src="components/com_pago/css/img/pg-info.png" /><span class="pg-info-con-text"><?php echo JText::_( "PAGO_CUSTOM_ATTRIBUTES_MANAGER_INFO" ); ?></span>	
					</div>
					<div class="add_product_varation_con">
						<a href="javascript:void(0);" onclick="showAddProductVariation(<?php echo $item->id;?>);" class="pg-btn-large pg-btn-light pg-btn-green pg-btn-add pg-right" data-toggle="modal" data-target="#addProductVariation">
							<?php echo JText::_( "PAGO_CUSTOM_ATTRIBUTES_ADD_PRODUCT_VARATION" ); ?>
						</a>
						<div class="clear"></div>
						<div class="add_product_varation_container modal modal-sm fade" id="addProductVariation"></div>
					</div>
					<div class="clear"></div>
					<?php
						$productVarationList = $attribute_model->get_product_varation_list($item);
						echo $productVarationList; 
					?>
				</div>
			</div>
		</div><!-- pg-tabs end -->
	</div><!-- tab end -->
	<?php
		$return = ob_get_contents();
		ob_end_clean();
			$doc->addScriptDeclaration( "jQuery(document).ready(function(){
				bind_publish_attr_buttons();
				bind_publish_varation_buttons();
				bind_preselected_varation_buttons();
			})
			" );
		return $return;
	}
}