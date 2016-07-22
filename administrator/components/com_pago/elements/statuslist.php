<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldStatuslist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Statuslist';

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		//print_r($this->);die();

		// Base name of the HTML control.
		$ctrl  = $name;

		// Construct the various argument calls that are supported.
		$attribs       = ' ';
		if ($v = $node->attributes( 'size' )) {
				$attribs       .= 'size="'.$v.'"';
		}
		if ($v = $node->attributes( 'style' )) {
				$attribs       .= 'style="'.$v.'"';
		}
		if ($v = $node->attributes( 'class' )) {
				$attribs       .= 'class="'.$v.'"';
		} else {
				$attribs       .= 'class="inputbox"';
		}
		if ($m = $node->attributes( 'multiple' ))
		{
				$attribs       .= ' multiple="multiple"';
				$ctrl          .= '[]';
		}
		if ($m = $node->attributes( 'disabled' ))
		{
				$attribs       .= ' disabled="disabled"';
		}

		$key = 'id';
		$val = 'name';

		$status_options = Pago::get_instance('config')->get_order_status_options();

		$options = array();

		foreach($status_options as $k=>$v){
			$options[] = array(
				'id' => $k,
				'name' => $v
			);
		}

		$html = JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name.$name);

		return $html;
    }
}
