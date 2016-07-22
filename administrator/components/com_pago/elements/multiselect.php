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

class JFormFieldMultiselect extends JFormField {
	/**
	 * Element name
	 *
	 * @access       protected
	 * @var          string
	 */
	protected $type = 'Multiselect';

	function getInput() {

		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$cid = JFactory::getApplication()->input->get('cid',  0, 'array');
		$cid = (int)$cid[0];

		$id  = str_replace( '_', '', $control_name );

		$remote_url = $node['remote_url'];

		$doc = JFactory::getDocument();

		$base_uri =  JURI::base() . 'components/com_pago/javascript/multiselect/';

		$doc->addScript( $base_uri . 'js/plugins/localisation/jquery.localisation-min.js' );
		$doc->addScript( $base_uri . 'js/plugins/tmpl/jquery.tmpl.1.1.1.js' );
		$doc->addScript( $base_uri . 'js/plugins/blockUI/jquery.blockUI.js' );
		$doc->addScript( $base_uri . 'js/ui.multiselect.js' );

		$doc->addStyleSheet( $base_uri . 'css/ui.multiselect.css' );

		$doc->addStyleDeclaration("
			.ui-multiselect li {  text-align: left;}
		");

		$doc->addScriptDeclaration("

			jQuery(function(){
				//jQuery.localise('ui.multiselect', {/*language: 'es',/* */ path: '{$base_uri}js/locale/'});

				// local
				jQuery('#$id').multiselect({
					remoteUrl: '$remote_url'
				});
			});

		");


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
			FROM #__pago_groups_users
				LEFT JOIN #__users
					ON #__users.id = #__pago_groups_users.user_id
				WHERE group_id={$cid}";
		$db->setQuery( $sql );
		$options_data = $db->loadAssocList();

		$key = 'user_id';
		$val = 'username';

		$options = array();

		//$options[]= array($key=> 856,$val => 'asdf');
		if(is_array($options_data))
		foreach ( $options_data as $option){
				$options[]= array($key=>$option['user_id'],$val =>$option['username']);
		}

		$description = '
			<div style="text-align:left">
				<h3>' . JText::_( 'Select Group Members' ) . '</h3>
				' . JText::_( 'Start typing username in textfield' ) . '
			</div>';
		//if($options){
				return $description . JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $name);
		//}

	}
}
