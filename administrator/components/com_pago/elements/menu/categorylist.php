<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');


class JFormFieldCategorylist extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Categorylist';

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
		if ($v = $this->size) {
				$attribs       .= 'size="'.$v.'"';
		}
		if ($v = $this->style) {
				$attribs       .= 'style="'.$v.'"';
		}
		if ($v = $this->element['class'] ) {
				$attribs       .= 'class="'.$v.'"';
		} else {
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

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pago'.DS.'tables');
		$cat_table = JTable::getInstance('categoriesi', 'Table');
		$cats = $cat_table->getTree( 1 );

		if( empty( $cats ) && ( JFactory::getApplication()->input->get( 'view' ) != 'categories' ) ){

			$app = JFactory::getApplication();

			JError::raiseWarning( 100, JText::_( 'PAGO_YOU_MUST_CREATE_A_CATEGORY_FIRST_JIZZFACE' ) );

			$app->redirect( 'index.php?option=com_pago' );
		}

		foreach($cats as $cat){
			$cat_name = str_repeat(' -- ', ( ( $cat->level ) ) );

			//$cat_name .= '['. $cat->level .']_ ' . $cat->name;
			$cat_name .= '' . $cat->name;
			$options[] = array(
				'id' => $cat->id,
				'name' => $cat_name
			);
		}

		if(!is_array($value))
		{
			$value = explode(",", $value);
		}
		$html = @JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name.$name);

		return $html;
    }
}
