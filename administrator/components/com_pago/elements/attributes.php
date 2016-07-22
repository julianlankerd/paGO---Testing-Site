<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldAttributes extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Attributes';
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
			"return jQuery.attributeOpts('addField');",
			'new', '#', 'toolbar', '', $bar );

		return $bar->render();
	}

	function getData()
	{
		if ( empty( $this->_data ) ) {
			$array = JFactory::getApplication()->input->get( 'cid',  0, 'array' );
			$attribute_id = (int) $array[0];
			$db = JFactory::getDBO();
			$query = 'SELECT * FROM #__pago_attr_opts WHERE attr_id = ' . $attribute_id
				.' AND opt_enable = 1 AND for_item = 0 ORDER BY ordering';
			$db->setQuery( $query );
			$this->_data = $db->loadObjectList();
		}

		return $this->_data;
	}

	function html( $name, $value, $node )
	{
		PagoHtml::behaviour_jquery();

		$doc = JFactory::getDocument();

		$script  = 'var uploadifivePath="'. JURI::root() ."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_pago".DIRECTORY_SEPARATOR."javascript".DIRECTORY_SEPARATOR."uploadify".DIRECTORY_SEPARATOR."uploadifive.php".'";';
		$script  = 'uploadifivePath="index.php?option=com_pago&controller=attributes&task=uploadfile";';
		$script .= 'var JPATH_ROOT="'. JPATH_ROOT .'";';
		$script .= 'var JURI="'.JURI::root( true ) .'/'.'"';


		$doc->addScriptDeclaration( $script );
		
		// PagoHtml::add_js( JURI::base( true )
		// 	. '/components/com_pago/javascript/uploadify/jquery.uploadifive.js' );

		// PagoHtml::add_css( JURI::base( true )
		// 	. '/components/com_pago/javascript/uploadify/uploadifive.css' );
		PagoHtml::loadUploadifive();

		PagoHtml::add_js( JURI::base( true )
			. '/components/com_pago/javascript/com_pago_attribute.js' );

		PagoHtml::add_js( JURI::base( true )
			. '/components/com_pago/javascript/jscolor/jscolor.js' );
		

		$data_count = count( $this->_data );

		// ob_start();
		
		//exit();
	?>
	<!--
	<table id="pg-attribute-options" class="pg-table pg-repeated-rows">
		<tbody>
	-->
			<!-- start -->
			<?php
			if ( $data_count != 0 ) {
				$attribute_model = JModelLegacy::getInstance('Attribute', 'PagoModel');
				$attributeHtml = $attribute_model->get_attribute_html($this->_data);
				return $attributeHtml;
			}
			?>		
			<!-- end -->
	<!--
		</tbody>
	</table>
	-->
	<?php
		// $return = ob_get_contents();
		// ob_end_clean();
		// return $return;
		return '';
	}
}