<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();


class JFormFieldPgcalendar extends JFormField
{
	protected $type = 'Pgcalendar';


	protected function getInput()
	{
		// Initialize some field attributes.
		$format = $this->element['format'] ? (string) $this->element['format'] : 'yy-mm-dd';

		if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$this->value))
    	{
        	$this->value = explode(" ", $this->value);
        	$this->value = $this->value[0];
    	}

		if(!isset($this->value) || $this->value == '0000-00-00' )
		{
			$this->value = $this->element['default']; 	
		}
		// Build the attributes array.
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
			$attribs   .= 'class="' . $v . ' pg-calendar"';
		}
		else
		{
			$attribs       .= 'class="pg-calendar"';
		}

		if ($m = $this->multiple )
		{
			$attribs       .= ' multiple="true"';
		}

		if ($m = $this->disabled)
		{
			$attribs       .= ' disabled="disabled"';
		}
		
		if(@$this->readonly) $attribs  .= ' readonly ';

		// Handle the special case for "now".
		if (strtoupper($this->value) == 'NOW')
		{
			$this->value = date('Y-m-d');
		}

		$return = '<script>
				jQuery(function() {
					jQuery( "#'.$this->id.'" ).datepicker(
						{ 
							dateFormat: "'.$format.'", 
							firstDay: 1,
							prevText: "<span class = \"fa fa-chevron-left\"></span>",
							nextText: "<span class = \"fa fa-chevron-right\"></span>",
							showOtherMonths: "true"
						}
					);
				});
		   </script>';

		$return .= "<input ".$attribs." name='".$this->name."' value='".$this->value."' type='text' id='".$this->id."'>";
		return $return;		   
		//return JHtml::_('calendar', $this->value, $this->name, $this->id, $format, $attributes);
	}
}
