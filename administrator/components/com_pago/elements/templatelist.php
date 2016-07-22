<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');


class JFormFieldTemplatelist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Templatelist';

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		// Base name of the HTML control.
		$ctrl  = $name;

		// Construct the various argument calls that are supported.
		$attribs   = ' ';

		if ($v = $this->size)
		{
			$attribs       .= 'size="' . $v . '"';
		}

		if ($v = $this->style)
		{
			$attribs       .= 'style="' . $v . '"';
		}

		if ($v = $this->element['class'] )
		{
			$attribs       .= 'class="' . $v . '"';
		}
		else
		{
			$attribs       .= 'class="inputbox"';
		}

		if ($m = $this->multiple )
		{
				$attribs       .= ' multiple="true"';
		}

		if ($m = $this->disabled)
		{
				$attribs       .= ' disabled="disabled"';
		}

		$key = 'id';
		$val = 'name';

		$options[0]['id'] = "None";
		$options[0]['name'] = JText::_('PAGO_SELECT');
		$options[1]['id'] = "order_receipt";
		$options[1]['name'] = JText::_('PAGO_ORDER_RECEIPT_TEMPLATE');
		

		if (!is_array($value))
		{
			$value = explode(",", $value);
		}

		$html = @JHTML::_('select . genericlist', $options, $ctrl, $attribs, $key, $val, $value, $control_name . $name);

		return $html;
    }
}
