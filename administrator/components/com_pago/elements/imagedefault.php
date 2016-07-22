<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport( 'joomla.plugin.plugin');

class JFormFieldImageDefault extends JFormField
{
	protected $type = 'imagesizes';
	var $_default = '{"thumbnail":{"width":150,"height":150,"crop":1},"small":{"width":100,"height":100,"crop":1},"medium":{"width":250,"height":250,"crop":0},"large":{"width":706,"height":598,"crop":0}}';

	function getInput()
	{
		// we just use the fieldname because its a file upload and the params[xxx][xxxx] format
		// screws it up since its a single file upload
		$name = $this->fieldname;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$db  = JFactory::getDBO();
		$doc = JFactory::getDocument();

		$params = Pago::get_instance( 'config' )->get();
		$html   = '';

		Pago::load_helpers( 'imagehandler' );

		$default = PagoImageHandlerHelper::get_item_files( 0, false, array( 'store_default' ) );

		if ( isset( $default[0] ) ) {
			$html .= PagoImageHandlerHelper::get_image_from_object( $default[0], array( 40, 40 ), 'style="float:left;"');
		}
		$html .= '<input type="file" name="' .$name. '" style="margin-top:10px;" />';

		return $html;
	}
}
