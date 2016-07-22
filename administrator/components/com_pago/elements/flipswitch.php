<?php

/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldFlipSwitch extends JFormField
{
    /**
     * Flip Switch input
     *
     * @access       public
     * @var          string
     */ 

    protected $type = 'flipswitch';

    public function getInput()
    {
		$doc  = JFactory::getDocument();
		
		$options = $this->element->option;
		$values = [];
		foreach ($options as $opt) {
            $values[] = [
            	'label' => JText::_( $opt ),
            	'value' => (string) $opt['value']
            ];
		}
		
		$checked = ($this->value == $values[0]['value']) ? 'checked' : '';
		
		if (!isset($this->element['width']))
		    $this->element['width'] = '70px';
		
		$css = <<<CSS
#{$this->id}-parent .onoffswitch-inner:before {
    content: "{$values[0]['label']}";
}

#{$this->id}-parent .onoffswitch-inner:after {
    content: "{$values[1]['label']}";
}
CSS;
		$doc->addStyleDeclaration($css);
		
        $html = <<<HTML
        <div class="onoffswitch" id="{$this->id}-parent" style="width: {$this->element['width']};">
            <input type="hidden" name="{$this->name}" value="{$this->value}" data-values="{$values[0]['value']},{$values[1]['value']}"/>
			<input type="checkbox" class="onoffswitch-checkbox" id="{$this->id}" {$checked}>
			<label class="onoffswitch-label" for="{$this->id}">
				<span class="onoffswitch-inner"></span>
				<span class="onoffswitch-switch"></span>
			</label>
		</div>
HTML;
        
        return $html;
    }
}