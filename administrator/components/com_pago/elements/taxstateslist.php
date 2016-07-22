<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldTaxstateslist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Taxstateslist';

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
	
		$html="";
		$html='<select id="params_pgtax_stateparamspgtax_state" name="params[pgtax_state]" class="inputbox" >';
		$html.="<option value=''>". JText::_("PAGO_PLEASE_SELECT_STATE")."</option>";

		foreach ($countries['attribs'] as $k =>$v){
			$v=str_replace('"', '', $v);
			$v=str_replace('class', '', $v);
			$v=str_replace('=', '', $v);

			$html.="<option value='".$k."'";
			if($value==$k){
				$html.="selected='selected'";
			};
			$html.=" class='".$v."'>".$k."</option>";
		}
	
		$html.="</select>";


		return $html;
	}
}
