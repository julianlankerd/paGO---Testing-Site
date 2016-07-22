<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');


class JFormFieldEmaillist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Emaillist';

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

		$options[0]['id'] = "0";
		$options[0]['name'] = JText::_('PAGO_SELECT');
		$options[1]['id'] = "email_update_order_status";
		$options[1]['name'] = JText::_('PAGO_EMAIL_UPDATE_ORDER_STATUS');
		$options[2]['id'] = "email_invoice";
		$options[2]['name'] = JText::_('PAGO_EMAIL_INVOICE');
		$options[3]['id'] = "fraud_order_email";
		$options[3]['name'] = JText::_('PAGO_FRAUD_ORDER_EMAIL');


		if (!is_array($value))
		{
			$value = explode(",", $value);
		}

		$html = @JHTML::_('select . genericlist', $options, $ctrl, $attribs, $key, $val, $value, $control_name . $name);

		return $html;
    }
}
