<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is within the rest of the framework
defined ( 'JPATH_BASE' ) or die ();

/**
 * Renders a multiple item select element
 *
 */

class JFormFieldMultiselectgroups extends JFormField {
	/**
	 * Element name
	 *
	 * @access       protected
	 * @var          string
	 */
	protected $type = 'Multiselectgroups';

	function getInput() {

		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$cid = JFactory::getApplication()->input->get('cid',  0, 'array');
		$cid = (int)$cid[0];

		//$id  = $name;
		$id = 'grouplistgroups';
		//echo $id;die();
		$remote_url = $node['remote_url'];
		//echo($node['remote_url']);die();
		$doc = JFactory::getDocument();

		// Base name of the HTML control.
		$ctrl  = $name;

		// Construct the various argument calls that are supported.
		$attribs       = ' ';
		if ($v = $node['size']) {
				$attribs       .= 'size="'.$v.'"';
		}

		if ($v = $node['style']) {
				$attribs       .= 'style="'.$v.'"';
		}

		if ($v = $node['class']) {
				$attribs       .= 'class="'.$v.'"';
		} else {
				$attribs       .= 'class="inputbox"';
		}
		if ($m = $node['multiple'])
		{
				$attribs       .= ' multiple="multiple"';
				//$ctrl          .= '[]';
		}


		$db = JFactory::getDBO();

		$sql = "SELECT *
			FROM #__pago_groups as groups
					";

		$db->setQuery( $sql );
		$options_data = $db->loadAssocList();

		$key = 'group_id';
		$val = 'name';

		$options = array();

		//$options[]= array($key=> 856,$val => 'asdf');
		if(is_array($options_data))
		foreach ( $options_data as $option){
				$options[]= array($key=>$option['group_id'],$val =>$option['name']);
		}

		$description = '
			<div style="text-align:left">
				<h3>' . JText::_( 'Select Group(s) Membership' ) . '</h3>

			</div>';

		//print_r($attribs);die();
		//if($options){
				return $description . JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $name);
		//}

	}
}
