<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldRadioBorder extends JFormField
{
    /**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'RadioBorder';

	function getInput()
	{
	    $attribs = '';
	    $class = 'radio border ';
	    
		if (!empty($this->style))
		    $attribs .= 'style="'.$this->style.'"';
	    
		if (!empty($this->element['class']))
		    $class .= $this->element['class'];
		    
		$i = 0;
		$options = '';
		foreach ($this->element->option as $option)
		{
		    $text = JText::_( $option );
		    $checked = ((string)$option['value'] == $this->value) ? 'checked' : '';
		    
		    $options .= <<<HTML
    		    <input type="radio" id="{$this->id}{$i}" name="{$this->name}" value="{$option['value']}" {$checked}>
    		    <label for="{$this->id}{$i}">{$text}</label>
HTML;
            ++$i;
		}
	    
	    return <<<HTML
			<fieldset {$attribs} id="{$this->id}" class="{$class}">
			    {$options}
			</fieldset>
HTML;
	}
}
