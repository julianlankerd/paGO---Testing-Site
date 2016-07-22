<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');
class JFormFieldItemlist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Itemlist';

	private function get_item_data()
	{
		// Get attribute data associated to items
		$db = Jfactory::getDBO();
		$query = "SELECT * FROM #__pago_items WHERE published = 1";
		$db->setQuery($query);
		$result = $db->LoadObjectList();

		return $result;
	}

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

		$items = $this->get_item_data();

		foreach ($items as $item)
		{
			$item_name = $item->name;
			$options[] = array(
				'id' => $item->id,
				'name' => $item_name
			);
		}

		if(!is_array($value))
		{
			$value = explode(",", $value);
		}
		$html = @JHTML::_('select.genericlist', $options, $ctrl, $attribs, $key, $val, $value, $control_name . $name);

		return $html;
	}
}
