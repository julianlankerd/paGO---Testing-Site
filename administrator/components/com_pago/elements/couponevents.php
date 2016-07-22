<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldCouponevents extends JFormField
{
	protected $type = 'Couponevents';
	var $_data = array();

	protected function getInput()
	{

		return $this->html();
	}

	protected function html()
	{

		$model = JModelLegacy::getInstance( 'coupon', 'PagoModel' );

		?>
			<div class="pg-rule-title"></div>
			<div class="pg-rule-inputs pg-row">
				<div class="pg-col-4 pg-row-item">
					<label for=""><?php echo JText::_('PAGO_COUPON_EVENTS_AVAILABLE'); ?></label>
					<div class="selector">
						<select id="coupon-events-available-type" name="params[events][available_type]">
							<option value="0" <?php if($this->value && $this->value['0']['available_type'] == 0){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_COUPON_EVENTS_ALWAYS_AVAILABLE'); ?></option>
							<option value="1" <?php if($this->value && $this->value['0']['available_type'] == 1){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_COUPON_EVENTS_AVAILABLE_AFTER'); ?></option>
						</select>
					</div>
				</div>
				<div class = "pg-col-8">
					<div id="coupon-events-filter" <?php if(!$this->value || ($this->value && $this->value['0']['available_type'] == 0)){echo 'style="display:none"';} ?>>
						<div class = "pg-row">
							<div class="pg-col-6 pg-row-item">
								<label for=""><?php echo JText::_('PAGO_COUPON_EVENTS_FILTER'); ?></label>
								<div class="selector">
									<select id="coupon-events-available-condition" name="params[events][available_condition]">
										<option value="0" <?php if($this->value && $this->value['0']['available_condition'] == 0){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_COUPON_EVENTS_FILTER_QUANTITY'); ?></option>
										<option value="1" <?php if($this->value && $this->value['0']['available_condition'] == 1){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_COUPON_EVENTS_FILTER_AMOUNT'); ?></option>
									</select>
								</div>
							</div>
							<div class="pg-col-6 pg-row-item">
								<div class = "pg-row">
									<div class = "pg-col-9">
										<label for=""><?php echo JText::_('PAGO_COUPON_EVENTS_FILTER_SUM'); ?></label>
										<input type="text" <?php if($this->value){echo 'value="'. $this->value['0']['filter_sum'] .'"';} ?> name="params[events][filter_sum]" >
									</div>

									<div class = "pg-col-3">
										<div <?php if($this->value && $this->value['0']['available_condition'] == 1){echo 'style="display:none"';} ?> id="event-available-count-item" class="event-available-count">
											<label for=""><?php echo JText::_('PAGO_COUPON_EVENTS_FILTER_TYPE_ITEM'); ?></label>
										</div>		
					
										<div <?php if(!$this->value || ($this->value && $this->value['0']['available_condition'] == 0)){echo 'style="display:none"';} ?> id="event-available-count-amout" class="event-available-count">
											<label for="">
											<?php 
												$currenciesModel = JModelLegacy::getInstance('currencies', 'PagoModel');
												$defaultCurrency = $currenciesModel->getDefault();
												echo $defaultCurrency->symbol;
											?>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="pg-col1 pg-row-item">
							
						</div>
					</div>
				</div>
			</div>		
		<?php
		if($this->value){
			$doc = JFactory::$document;
			$script='
				jQuery(document).ready(function(){
					jQuery("select#coupon-assign-type").val(\''.$this->value['0']['available_type'].'\').change();
				});
			';
	        $doc->addScriptDeclaration($script);
    	}
	}
}
