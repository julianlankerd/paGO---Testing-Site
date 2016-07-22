<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldStateslist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Stateslist';

	function getInput()
	{

		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		// Base name of the HTML control.
		$ctrl  = $name;

		// Construct the various argument calls that are supported.
		$attribs       = ' ';

		if ($v = $this->size){
			$attribs .= 'size="' . $v . '"';
		}

		if ($v = $this->style){
			$attribs .= 'style="' . $v . '"';
		}

		if ($v = $this->element['class']){
			$attribs .= 'class = "' . $v . '"';
		}
		else{
			$attribs .= 'class = "inputbox"';
		}

		if ($m = $this->multiple){
			$attribs .= ' multiple = "true"';
		}

		if ($m = $this->disabled){
			$attribs .= ' disabled = "disabled"';
		}

		$key = 'id';
		$val = 'name';

		$user_fields_model = JModelLegacy::getInstance('User_fields', 'PagoModel');
		$countries = $user_fields_model->get_countries_states();
		$countries = $countries['options'];
		$options = array();

		foreach ($countries as $k => $v){
			$options[] = array(
				'id' => $v,
				'name' => $k
			);
		}

		if (!is_array($value)){
			$value = explode(",", $value);
		}

		$html = JHTML::_('select.genericlist', $options, $ctrl, $attribs, $key, $val, $value, $control_name . $name);

		return $html;
	}
}
