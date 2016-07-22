<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldHtmlarea extends JFormField
{
    /**
    * Element name
    *
    * @access       protected
    * @var          string
    */
    protected $type = 'htmlarea';

    function getInput()
    {
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		   $editor = JFactory::getEditor();
		$ctrl = $name;

		if ( $height = $node->attributes( 'height' ) ){}else $height = '70';

		return $editor->display( $ctrl,  $value, '100%', $height, '75', '20' ) ;
    }
}
