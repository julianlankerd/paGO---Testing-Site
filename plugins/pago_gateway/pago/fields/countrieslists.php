<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldCountrieslists extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Countrieslists';

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

		if ($v = $this->size)
		{
				$attribs       .= 'size="' . $v . '"';
		}

		if ($v = $this->style)
		{
				$attribs       .= 'style="' . $v . '"';
		}

		if ($v = $this->element['class'])
		{
				$attribs       .= 'class = "' . $v . '"';
		}
		else
		{
				$attribs       .= 'class = "inputbox"';
		}

		if ($m = $this->multiple)
		{
				$attribs       .= ' multiple = "true"';
		}

		if ($m = $this->disabled)
		{
				$attribs       .= ' disabled = "disabled"';
		}

		$key = 'id';
		$val = 'name';

		$countries = [
			'AU' => 'Australia',
			'AT' => '*Austria',
			'BE' => '*Belgium',
			'CA' => 'Canada',
			'DK' => 'Denmark',
			'FI' => 'Finland',
			'FR' => '*France',
			'DE' => '*Germany',
			'IE' => 'Ireland',
			'IT' => '*Italy',
			'JP' => '**Japan',
			'LU' => '*Luxembourg',
			'MX' => '**Mexico',
			'NL' => '*The Netherlands',
			'NO' => 'Norway',
			'SG' => '**Singapore',
			'ES' => '*Spain',
			'SE' => 'Sweden',
			'CH' => '**Switzerland',
			'GB' => 'United Kingdom',
			'US' => 'United States',
		];
		
		$countries = array_flip($countries);
		
		$options = array();

		foreach ($countries as $k => $v)
		{
			$options[] = array(
				'id' => $v,
				'name' => $k
			);
		}

		if(!is_array($value))
		{
			$value = explode(",", $value);
		}

		$html = JHTML::_('select.genericlist', $options, $ctrl, $attribs, $key, $val, $value, $this->id);

		return $html;
	}
}
