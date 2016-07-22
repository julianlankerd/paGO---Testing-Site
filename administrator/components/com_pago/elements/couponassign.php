<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldcouponassign extends JFormField
{
	protected $type = 'Couponassign';
	var $_data = array();

	protected function getInput()
	{

		return $this->html();
	}

	protected function html()
	{

		$model = JModelLegacy::getInstance( 'coupon', 'PagoModel' );
		
		PagoHtml::add_js( JURI::root( true ) .
			'/administrator/components/com_pago/javascript/jquery-ui/js/jquery-ui-1.10.4.custom.min.js');
		
		?>
			<div class="pg-rule-title"></div>
			<div class="pg-rule-inputs">
				<div class="pg-col4 pg-row-item">
					<div class="pg-row-item-inner">
						<label for=""><?php echo JText::_('PAGO_COUPON_ASSIGN_TYPE'); ?></label>
						<div class="selector">
							<select id="coupon-assign-type" name="params[assign][type]">
								<option value="0" ><?php echo JText::_('PAGO_COUPON_ASSIGN_TYPE_GLOBAL'); ?></option>
								<option value="1" ><?php echo JText::_('PAGO_COUPON_ASSIGN_TYPE_ITEMS'); ?></option>
								<option value="2" ><?php echo JText::_('PAGO_COUPON_ASSIGN_TYPE_CATEGORIES'); ?></option>
								<!-- <option value="3" ><?php echo JText::_('PAGO_COUPON_ASSIGN_TYPE_SHOPPING_GROUPS'); ?></option> -->
								<option value="4" ><?php echo JText::_('PAGO_COUPON_ASSIGN_TYPE_USERS'); ?></option>
								<!--<option value="5" ><?php echo JText::_('PAGO_COUPON_ASSIGN_TYPE_SHIPPING'); ?></option>-->
							</select>
						</div>
					</div>
				</div>
				<div id="coupon-assign-parameters">

				</div>
			</div>		
		<?php
		if($this->value){
			$doc = JFactory::$document;
			$script='
				jQuery(document).ready(function(){
					jQuery("select#coupon-assign-type").val(\''.$this->value['0']['type'].'\').change();
				});
			';
	        $doc->addScriptDeclaration($script);
    	}
	}
}
