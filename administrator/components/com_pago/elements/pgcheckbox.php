<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');
class JFormFieldpgcheckbox extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'pgcheckbox';

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$Checked = $this->value == '1' ? ' checked="checked" ' : '';
		$Val = $this->value == 1 ? 1 : 0;

		// Base name of the HTML control.
		$ctrl  = $name;
		$html = '<fieldset id="params_product_settings_pg" class="pg-checkbox">';
		$html .= '<input type="checkbox" checked="checked" value="'.$Val.'" class="hiddenCheck" name="'.$name.'" />';
		$html .= '<input type="checkbox" '.$Checked.' value="'.$Val.'" id="'.$this->id.'" />';
		$html .= '<label for="'.$this->id.'"></label>';
		$html .= '</fieldset>';
		return $html;
	}
}
