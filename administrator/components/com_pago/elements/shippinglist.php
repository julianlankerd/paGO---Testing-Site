<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');
class JFormFieldShippinglist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Shippinglist';

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

		if ($v = $this->element['class'] )
		{
			$attribs   .= 'class="' . $v . '"';
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
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage();
		$query = 'SELECT element,name FROM #__extensions WHERE type=' . $db->Quote('plugin') . ' AND folder=' . $db->Quote('pago_shippers') . ' AND enabled=1 ';
		$db->setQuery($query);
		$elements = $db->loadObjectList();

		$options = array();
		foreach($elements as $element)
		{
			$source = JPATH_PLUGINS . '/pago_shippers/' . $element->element;
			$extension = 'plg_pago_shippers_' . $element->element;
				$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load($extension . '.sys', $source, null, false, false)
			||	$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);
			$plg_name = JText::_($element->name);
			$options[] = array(
				'id' => $element->element,
				'name' => $plg_name
			);
		}

		$temps = array('0' => JText::_('PAGO_ALLOW_ALL'));
		$options = array_merge($temps, $options);

		if(!is_array($value))
		{
			$value = explode(",", $value);
		}

		$html = @JHTML::_('select.genericlist', $options, $ctrl, $attribs, $key, $val, $value, $control_name . $name);

		return $html;
    }

}
