<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldattributeassign extends JFormField
{
	protected $type = 'Attributeassign';
	var $_data = array();

	protected function getInput()
	{

		return $this->html();
	}

	protected function html()
	{
		$model = JModelLegacy::getInstance( 'attribute', 'PagoModel' );

		?>
			<div class="pg-rule-title"></div>
			<div class="pg-rule-inputs">
				<div class="pg-col4 pg-row-item">
					<div class="pg-row-item-inner">
						<!--<label for=""><?php echo JText::_('PAGO_ATTRIBUTE_ASSIGN_TYPE'); ?></label>-->
						<div class="selector">
							<select id="attribute-assign-type" name="params[assign][assign_type]">
								<option value="0" ><?php echo JText::_('PAGO_ATTRIBUTE_ASSIGN_TYPE_GLOBAL'); ?></option>
								<option value="1" ><?php echo JText::_('PAGO_ATTRIBUTE_ASSIGN_TYPE_ITEMS'); ?></option>
								<option value="2" ><?php echo JText::_('PAGO_ATTRIBUTE_ASSIGN_TYPE_CATEGORIES'); ?></option>
							</select>
						</div>
					</div>
				</div>
				<div id="attribute-assign-parameters">

				</div>
			</div>		
		<?php
		if($this->value){
			$doc = JFactory::$document;
			$script='
				jQuery(document).ready(function(){
					jQuery("select#attribute-assign-type").val(\''.$this->value['0']['assign_type'].'\').change();
				});
			';
	        $doc->addScriptDeclaration($script);
    	}
	}
}
