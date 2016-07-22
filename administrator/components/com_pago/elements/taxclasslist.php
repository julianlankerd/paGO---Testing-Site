<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');
class JFormFieldTaxclasslist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Taxclasslist';

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
		$query = 'SELECT pgtax_class_id,pgtax_class_name FROM #__pago_tax_class WHERE pgtax_class_enable=1 ';
		$db->setQuery($query);
		$taxClasses = $db->loadObjectList();

		$options = array();

		foreach ($taxClasses as $taxClass)
		{
			$options[] = array(
				'id' => $taxClass->pgtax_class_id,
				'name' => $taxClass->pgtax_class_name
			);
		}

		$temps = array('0' => JText::_('PAGO_DEFAULT_TAX_CLASS'));
		$options = array_merge($temps, $options);

		if (!is_array($value))
		{
			$value = explode(",", $value);
		}

		$html = @JHTML::_('select.genericlist', $options, $ctrl, $attribs, $key, $val, $value, $control_name . $name);

		return $html;
    }

}
