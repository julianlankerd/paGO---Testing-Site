<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');


class JFormFieldCategorylistnotinherited extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Categorylistnotinherited';

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		// Base name of the HTML control.
		$ctrl  = $name;
		$view =  JFactory::getApplication()->input->get( 'view' );
		$itemId =  JFactory::getApplication()->input->get( 'cid', array(0), 'array()' );
	 	$itemId = $itemId[0];
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

		$module = false;
		if(isset($this->element['module'])){
			$module = true;
		}
		if ($module)
		{
			jimport( 'joomla.database.table' );
			JTable::addIncludePath( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'tables' );
		}

		$cat_table = JTable::getInstance( 'categoriesi', 'Table' );


		$cats = $cat_table->getTree( 1 );
		
		if( empty( $cats ) && ( JFactory::getApplication()->input->get( 'view' ) != 'categories' ) ){

			$app = JFactory::getApplication();

			JError::raiseWarning( 100, JText::_( 'PAGO_YOU_MUST_CREATE_A_CATEGORY_FIRST_JIZZFACE' ) );

			$app->redirect( 'index.php?option=com_pago' );
		}

		// $filterCategory = JRequest::getInt( 'filterCategory' );
		// if($filterCategory){
		// 	var_dump($filterCategory);
		// 	exit();
		// }
		foreach($cats as $cat){
			if($cat->inherit_parameters_from > 1){
				continue;
			}
			if($cat->id == $itemId && $view == 'categories')
			{
				continue;
			}
			$cat_name = str_repeat(' -- ', ( ( $cat->level ) ) );

			//$cat_name .= '['. $cat->level .']_ ' . $cat->name;
			$cat_name .= '' . $cat->name;
			if($cat->id == 1){
				$cat_name = '-';
			}
			$options[] = array(
				'id' => $cat->id,
				'name' => $cat_name
			);
		}

		if(!is_array($value))
		{
			$value = explode(",", $value);
		}


		$filterCategory = JFactory::getApplication()->input->getInt( 'filterCategory' );
		if($filterCategory && $control_name == 'params_primary_category'){
			$value[] = $filterCategory;  
		}
		$html = @JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name.$name);

		return $html;
    }
}
