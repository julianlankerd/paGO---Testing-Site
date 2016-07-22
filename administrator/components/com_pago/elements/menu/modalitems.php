<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldModalItems extends JFormField
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        protected $type = 'ModalItems';

        protected function getInput()
		{
			
			// Load the javascript
			JHtml::_('behavior.framework');
			JHtml::_('behavior.modal', 'a.modal');
			//JHtml::_('bootstrap.tooltip');

			// Build the script.
			$script = array();
			$script[] = '	function jSelectChart_'.$this->id.'(id, name, object) {';
			$script[] = '		document.id("'.$this->id.'_id").value = id;';
			$script[] = '		document.id("'.$this->id.'_name").value = name;';
			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			// Get the title of the linked chart
			$db = JFactory::getDBO();
			$db->setQuery(
				'SELECT name' .
				' FROM #__pago_items' .
				' WHERE id = '.(int) $this->value
			);

			try
			{
				$title = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage);
			}

			if (empty($title))
			{
				$title = JText::_('PAGO_ITEM_MENU_LABEL_DEFAULT');
			}

			$link = 'index.php?option=com_pago&amp;view=items&amp;layout=modal&amp;tmpl=component&amp;function=jSelectChart_'.$this->id;

			if (isset($this->element['language']))
			{
				$link .= '&amp;forcedLanguage='.$this->element['language'];
			}

			$html = "\n".'<div class="input-append"><input type="text" class="input-medium" id="'.$this->id.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /><a class="modal btn" title="Select"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-address hasTooltip" title="Select"></i> '.JText::_('JSELECT').'</a></div>'."\n";
			// The active contact id field.
			if (0 == (int) $this->value)
			{
				$value = '';
			}
			else
			{
				$value = (int) $this->value;
			}

			// class='required' for client side validation
			$class = '';
			if ($this->required)
			{
				$class = ' class="required modal-value"';
			}

			$html .= '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

			return $html;
        }
}
