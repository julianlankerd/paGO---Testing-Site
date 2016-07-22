<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldDisplayastext extends JFormField
{
   protected $type = 'Displayastext';

   protected function getInput()
	{
		 /*
		 $name = $this->name;
         $value = $this->value;
         $node = $this->node;
         $control_name = $this->control_name;
		*/

		return $this->value;
    }
}