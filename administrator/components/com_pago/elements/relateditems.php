<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');


class JFormFieldRelatedItems extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'RelatedItems';

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
				$attribs       .= 'class="'.$v.' ui-autocomplete-input"';
		} else {
				$attribs       .= 'class="inputbox ui-autocomplete-input"';
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

		$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );
		$itemsId = json_decode($value);
		$itemsUniqueId = array();
		$uniqueItems = array();
		
		$html ='<input type="text"  '.$attribs.' id="related-item-add" autocomplete="off" aria-autocomplete="list" aria-haspopup="true"></li>';
		
		$html .= '<span class="field-heading"><label>'.JTEXT::_('PAGO_ITEM_RELATED_CHOSEN').'</label></span>';
		$html .= '<div class="pg-relateditems"><ul class="pg-related-items">';
			if($itemsId){
				foreach ($itemsId as $value) {
					if(!in_array($value->id, $itemsUniqueId)){
						$itemsUniqueId[] = $value->id;
						$uniqueItems[] = $value;
						$item = $model->getItemName($value->id);
						$html .= '<li class="itemAdded" id="'.$value->id.'">'.$item->name.'
									  <span title="'.JTEXT::_("PAGO_REMOVE_RELATED_PRODUCT").'" class="related-item-remove fa fa-times"></span>
								  </li>';
					}		  	
				}	
			}
		$html .= '</ul></div>';
		
		$html .= '<input type="hidden" name="'.$name.'" id="'.$control_name.'" value=\''.json_encode($uniqueItems).'\' >';		
		return $html;
    }
}
