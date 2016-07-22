<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldDiscountevents extends JFormField
{
	protected $type = 'Discountevents';
	var $_data = array();

	protected function getInput()
	{
		return $this->html();
	}

	protected function html()
	{
		$model = JModelLegacy::getInstance( 'discount', 'PagoModel' );
		PagoHtml::add_js( JURI::root( true ) .
			'/administrator/components/com_pago/javascript/jquery-ui/js/jquery-ui-1.10.4.custom.min.js');
			PagoHtml::add_js( JURI::root( true ) .
			'/administrator/components/com_pago/javascript/com_pago_discount_rule.js');
		?>
		
			<div class="pg-rule-title"></div>
			<div class="pg-rule-inputs pg-row">
 				<div class="pg-col-4 pg-row-item">
					<label for=""><?php echo JText::_('PAGO_DISCOUNT_EVENTS_AVAILABLE'); ?></label>
					<div class="selector">
						<select id="discount-event-type" name="params[discount_event]">
							<option value="1" <?php if($this->value && $this->value['0']['discount_event'] == 1){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_EVENTS_AVAILABLE_AFTER'); ?></option>
                            <option value="4" <?php if($this->value && $this->value['0']['discount_event'] == 4){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_EVENTS_ON_COMBINED_ITEMS'); ?></option>
                            <option value="2" <?php if($this->value && $this->value['0']['discount_event'] == 2){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_EVENTS_ON_SELECTED_ITEMS'); ?></option>
                            <option value="3" <?php if($this->value && $this->value['0']['discount_event'] == 3){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_EVENTS_ON_SELECTED_CATEGORIES'); ?></option>
							<option value="5" <?php if($this->value && $this->value['0']['discount_event'] == 5){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_ON_SELECT_CUSTOM_FIELDS'); ?></option> <!-- changed by hir -->
						</select>
					</div>
				</div>
				<?php if(!$this->value || ($this->value && $this->value['0']['discount_event'] == 1 )) : ?>
				<div class = "pg-col-8">
					<div id="discount-events-filter">
						<div class = "pg-row">
							<div class="pg-col-6 pg-row-item">
								<label for=""><?php echo JText::_('PAGO_DISCOUNT_EVENTS_FILTER'); ?></label>
								<div class="selector">
									<select id="discount-events-available-condition" name="params[discount_filter]">
										<option value="0" <?php if($this->value && $this->value['0']['discount_filter'] == 0){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_EVENTS_FILTER_QUANTITY'); ?></option>
										<option value="1" <?php if($this->value && $this->value['0']['discount_filter'] == 1){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_EVENTS_FILTER_AMOUNT'); ?></option>
										<option value="6" <?php if($this->value && $this->value['0']['discount_filter'] == 6){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_EVENTS_FILTER_ORDERS'); ?></option>
									</select>
								</div>
							</div>
							<div class="pg-col-6 pg-row-item">
								<div class = "pg-row">
									<div class = "pg-col-9">
										<label for=""><?php echo JText::_('PAGO_DISCOUNT_EVENTS_FILTER_SUM'); ?></label>
										<input type="text" <?php if($this->value){echo 'value="'. $this->value['0']['discount_filter_value'] .'"';} ?> name="params[discount_filter_value]" >
									</div>

									<div class = "pg-col-3">
										<div <?php if($this->value && ($this->value['0']['discount_filter'] == 1 || $this->value['0']['discount_filter'] == 6)){echo 'style="display:none"';} ?> id="event-available-count-item" class="event-available-count">
											<label for=""><?php echo JText::_('PAGO_DISCOUNT_EVENTS_FILTER_TYPE_ITEM'); ?></label>
										</div>		
					
										<div <?php if(!$this->value || ($this->value && ($this->value['0']['discount_filter'] == 0  || $this->value['0']['discount_filter'] == 6))){echo 'style="display:none"';} ?> id="event-available-count-amout" class="event-available-count">
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
					</div>
				</div>
				<?php endif; ?>
				<?php if(!$this->value || ($this->value && $this->value['0']['discount_event'] == 2 )) : ?>
                <div class = "pg-col-8">
					<div id="discount-events-item-filter" >
						<div class = "pg-row">
                            <div class="pg-col-6 pg-row-item">
								<label for=""><?php echo JText::_('PAGO_DISCOUNT_EVENTS_FILTER'); ?></label>
								<div class="selector">
									<select id="discount-events-available-items" name="params[discount_filter]">
										<option value="2" <?php if($this->value && $this->value['0']['discount_filter'] == 2){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_ALL'); ?></option>
										<option value="3" <?php if($this->value && $this->value['0']['discount_filter'] == 3){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_SELECTED_ITEMS'); ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="pg-col-6 pg-row-item">
							<div class="pg-rule-inputs">
                                <div class="pg-col4 pg-row-item">
                       			 
                    			</div>
                                <div id="discount-assign-parameters">
                                </div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php if(!$this->value || ($this->value && $this->value['0']['discount_event'] == 3 )) : ?>
                
                <div class = "pg-col-8">
					<div id="discount-events-category-filter" <?php if(!$this->value || ($this->value && ($this->value['0']['discount_event'] == 1 || $this->value['0']['discount_event'] == 2))){echo 'style="display:none"';} ?>>
						<div class = "pg-row">
                            <div class="pg-col-6 pg-row-item">
								<label for=""><?php echo JText::_('PAGO_DISCOUNT_EVENTS_FILTER'); ?></label>
								<div class="selector">
									<select id="discount-events-available-categories" name="params[discount_filter_cat]">
										<option value="4" <?php if($this->value && $this->value['0']['discount_filter'] == 4){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_ALL'); ?></option>
										<option value="5" <?php if($this->value && $this->value['0']['discount_filter'] == 5){echo 'selected="selected"';} ?> ><?php echo JText::_('PAGO_DISCOUNT_SELECTED_CATEGORIES'); ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="pg-col-6 pg-row-item">
							<div class="pg-rule-inputs">
                                <div class="pg-col4 pg-row-item">
                       			 
                    			</div>
                                <div id="discount-assign-parameters-cat">
                                </div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php
					$dispatcher = KDispatcher::getInstance();
					$dispatcher->trigger('backend_disocunt_extrafield_display', array( $this->value));
				?>
				
				<?php if(!$this->value || ($this->value && $this->value['0']['discount_event'] == 4 )) : ?>
                <div class = "pg-col-8">
					
						<div class="pg-col-6 pg-row-item">
							<div class="pg-rule-inputs">
                                <div class="pg-col4 pg-row-item">

                    			</div>
                                <div id="discount-combine-items">
                                </div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>		
		<?php
		if($this->value){
			$doc = JFactory::$document;
			$script='
				jQuery(document).ready(function(){
					jQuery("select#discount-events-available-items").val(\''.$this->value['0']['discount_filter'].'\').change();
					jQuery("select#discount-events-available-categories").val(\''.$this->value['0']['discount_filter'].'\').change();
					jQuery("select#discount-event-type").val(\''.$this->value['0']['discount_event'].'\').change();
					jQuery("select#discount-custom-available-condition").val(\''.$this->value['0']['discount_filter'].'\').change(); 
				});
			';/* changed by hir last script line*/
	        $doc->addScriptDeclaration($script);
    	}
	}
}    
