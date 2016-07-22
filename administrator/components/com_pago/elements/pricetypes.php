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

jimport( 'joomla.plugin.plugin');

class JFormFieldPricetypes extends JFormField
{

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$doc = JFactory::getDocument();

		$doc->addScriptDeclaration("

			jQuery(document).ready(function(){

				var value = '{$value}';

				if( value == 'subscription' ) {
					jQuery('#pg-edit-pricetype2').fadeIn();
				}

				jQuery('#paramsprice_type').change(function() {

					jQuery('#subscr_fieldset').fadeOut();
					jQuery('#pg-edit-pricetype2').fadeOut();

					var selection = jQuery(this).find('option:selected').attr('value');

					switch(selection)
					{
						case 'subscription':
							jQuery('#pg-edit-pricetype2').fadeIn();
						break;
					}
				});

			});

		");

		$ctrl = $name;

		## An array of $key=>$value pairs ##
		$price_types = array(
			'one_off' => JText::_( 'PAGO_SEL_ONE_OFF' ),
			'subscription' => JText::_( 'PAGO_SEL_SUBSCRIPTION' )
		);

		## Initialize array to store dropdown options ##
		$options = array();

		foreach( $price_types as $key=>$val ){
			## Create $value ##
			$options[] = JHTML::_('select.option', $key, $val);
		}

		$html = '<div style="position:relative" class="clear"><div style="position:absolute;top:-27px;left:100px" class="clear">';
		$html .= '<a style="display:none;color:#fff" id="pg-edit-pricetype2" class="label label-success" data-toggle="modal" data-target="#myModal">' . JText::_( 'PAGO_SUBSCRIPTION_OPTIONS' ) . '</a>';
		$html .= '</div></div>';
		
		$html .=  JHTML::_('select.genericlist', $options, $ctrl, false, 'value', 'text', $value, $name);
		
		return $html;
	}
}
