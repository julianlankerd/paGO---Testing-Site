<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');


class JFormFieldItemTags extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'ItemTags';

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

		$model = JModelLegacy::getInstance( 'Item_types', 'PagoModel' );
		$tagsId = json_decode($value);
		$tagsUniqueId = array();
		$uniqueTags = array();

		$html = '<div class="pg-row-item pg-relateditems"><ul class="pg-item-tags">';
			if($tagsId){
				foreach ($tagsId as $value) {
					if(!in_array($value->id, $tagsUniqueId)){
						$tagsUniqueId[] = $value->id;
						$uniqueTags[] = $value;
						$tag = $model->getTagName($value->id);
						$html .= '<li class="tagAdded" id="'.$value->id.'">'.$tag->name.'
									  <span title="Click to remove tag" class="item-tag-remove">x</span>
								  </li>';
					}
				}
			}
		$html .= '</ul></div>';

		$html .='<span class="field-heading"><label id="params_item_custom_layout-lbl" for="item-tag-add" title="">'.JText::_('PAGO_ITEM_TYPE_ADD_TITLE').'</label></span>';
		$html .='<input type="text"  '.$attribs.' id="item-tag-add" autocomplete="off" aria-autocomplete="list" aria-haspopup="true"></li>';
		$html .= '<input type="hidden" name="'.$name.'" id="'.$control_name.'" value=\''.json_encode($uniqueTags).'\' >';
		return $html;
    }
}
