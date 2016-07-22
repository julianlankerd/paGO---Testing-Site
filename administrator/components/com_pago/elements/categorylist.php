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

		$view =  JFactory::getApplication()->input->get( 'view' );
		$itemId =  JFactory::getApplication()->input->get( 'cid', array(0), 'array()' );
		$itemId = $itemId[0];

		$new = $this->element['new'];
		// Base name of the HTML control.
		
		if(!$new){
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

			foreach($cats as $cat){
				if($cat->id == $itemId && $view == 'categories')
				{
					continue;
				}
				$cat_name = str_repeat(' -- ', ( ( $cat->level ) ) );
				
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

			$filterCategory = JFactory::getApplication()->input->getInt( 'filterCategory' );

			if($filterCategory && $control_name == 'params_primary_category'){
				$value[] = $filterCategory;  
			}
			$html = @JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name.$name);

			return $html;
		}
		else{
			
			$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );
			$item = $model->getItem($itemId);

			$secondaryCat = $model->getItemSecondaryCat($itemId);

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
			$tree = [];
			foreach($cats as $cat){
				$tree[$cat->level][$cat->id] = array(
			 		'id'        => $cat->id,
			 		'name'      => $cat->name,
			 		'htmls'     => "<ul class='pg-sub-categories-list'>",
			 		'html'      => "",
			 		'htmle'     => "</ul>",
			 		'parent_id' => $cat->parent_id
			 	);
			}

			
			$parent_id = 0;

			for($i=count($tree)-1; $i>0; $i--){
				$parent = 0;
				$elements = '';
				$checked = '';
				$j = 1;
				foreach($tree[$i] as $t){
					if($parent !=0 && $parent != $t['parent_id']){
						
					}
					else{
						$parent = $t['parent_id'];
					}
					$elements = '';
					foreach($secondaryCat as $k => $v) {
	    				if($v['category_id'] == $t['id']){
	    					$checked = 'checked="checked"';
	    					break;
	    				}
	    				else{
	    					$checked = '';
	    				}
					}

					$class = $t['html'] != '' ? 'class="hasChild"' : '';

					$elements .= '<li id="sub-'.$j.'-'.$t['id'].'" '.$class.'>';
					$elements .= '<div class = "category-info ">';
					$elements .= '<span class="pg-checkbox"><input type="checkbox" '.$checked.' name="params[secondary_category][]" value="'.$t['id'].'" id="pg-sub-category_choice_'.$j.'-'.$t['id'].'">';
					$elements .= '<label for="pg-sub-category_choice_'.$j.'-'.$t['id'].'"></label>';
					$elements .= '</span>';

					if($t['id'] == $item->primary_category){ 
						$elements .= '<span class="pg-primary-category" id="'.$t['id'].'">'.$t['name'].'<i class="fa fa-star"></i>';
					}
					else{ 
						$elements .= '<span class="pg-primary-not-chosen" id="'.$t['id'].'">'.$t['name'].'<i class="fa fa-star-o"></i>';
					}
					
					$elements .= '</span>';
					$elements .= '</div>';
					$elements .= $t['htmls'].$t['html'].$t['htmle'];
					$elements .= '</li>';

					$tree[$i-1][$t['parent_id']]['html'] .= $elements;
					$checked = '';
				}
				$j++;
			}

			foreach($secondaryCat as $k => $v) {
				if($v['category_id'] == $tree[0][1]['id']){
					$checked = 'checked="checked"';
					break;
				}
				else{
					$checked = '';
				}
			}

			$class = $tree[0][1]['html'] != '' ? 'class="hasChild"' : '';

			$html = '<ul class="pg-categories-list"><li id="main-'.$tree[0][1]['id'].'" '.$class.'>';
			$html .= '<div class = "category-info ">';
			$html .= '<span class="pg-checkbox"><input type="checkbox" '.$checked.' name="params[secondary_category][]" value="'.$tree[0][1]['id'].'"  id="pg-category_choice_'.$tree[0][1]['id'].'">';
			$html .= '<label for="pg-category_choice_'.$tree[0][1]['id'].'"></label>';
			$html .= '</span>';
			if($tree[0][1]['id'] == $item->primary_category){ 
				$html .= '<span class="pg-primary-category" id="'.$tree[0][1]['id'].'">'.$tree[0][1]['name'].'<i class="fa fa-star"></i></span>';
			}
			else{ 
				$html .= '<span class="pg-primary-not-chosen" id="'.$tree[0][1]['id'].'">'.$tree[0][1]['name'].'<i class="fa fa-star-o"></i></span>';
			}
			$html .= '</div>';
			$html .= '<ul class="pg-sub-categories-list">'.$tree[0][1]['html'].'</ul>';
			$html .='</li></ul>';
			$html .= '<input type="hidden" class="primaryInput" name="params[primary_category]" value="'.$item->primary_category.'">';

			return $html;
		}
	}
}
