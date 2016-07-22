<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery( 'jqueryui' );
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();
PagoHtml::addGlobalConfigVariablesInJs();

$d = JFactory::getDocument();
$CURRENCY_SYMBOL = CURRENCY_SYMBOL;

JHTML::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'javascript' .DS. 'orderjs.php');

PagoHtml::pago_top( $menu_items, 'tabs', $this->top_menu );
?>
<script>
function checkQuantity(id)
{
	var currentId = id.split("_");
	var currentQty = jQuery("#currentQty_"+currentId[1]).val();
	var updatedQty = jQuery("#quantity_"+currentId[1]).val();
	
	/*if(updatedQty >= currentQty)
	{
		alert("Quantity must be less than current quantity");
		return false;
	}
	else
	{
		 window.location.href = 'index.php?option=com_pago&view=ordersi&task=update_quantity&order_id=<?php echo $this->order['details']->order_id; ?>&updatedQty='+updatedQty+'&item_id='+currentId[1];
		 
	}*/
		
	window.location.href = 'index.php?option=com_pago&view=ordersi&task=update_quantity&order_id=<?php echo $this->order['details']->order_id; ?>&updatedQty='+updatedQty+'&item_id='+currentId[1];

}
var refund_prompt = function(id, order_id, order_log_id, amount){
	
	var value = prompt("<?php echo JText::_( 'PAGO_ORDER_REFUND_AMOUNT' ); ?>" + amount, amount);
    
    if (value != null) {
        window.location.href = 'index.php?option=com_pago&view=ordersi&task=charge_refund&id='
        +id+'&cid='+order_id+'&order_log_id='+order_log_id+'&amount='+amount+'&refund='+value;
    }
}
</script>
<div class="pg-content">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php PagoHtml::deploy_tabpanel( 'tabs' ); ?>
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li class="first pg-information">
						<a onClick="addTabPrefixInUrl(this);" href="#tabs-1">
							<span class="icon"></span><?php echo JText::_( 'PAGO_ORDER_TAB_INFORMATION' ); ?>
						</a>
					</li>
					<li class="pg-addresses">
						<a onClick="addTabPrefixInUrl(this);" href="#tabs-2">
							<span class="icon"></span><?php echo JText::_( 'PAGO_ORDER_TAB_ADDRESSES' ); ?>
						</a>
					</li>
					<li class="pg-items">
						<a onClick="addTabPrefixInUrl(this);" href="#tabs-3">
							<span class="icon"></span><?php echo JText::_( 'PAGO_ORDER_TAB_ITEMS' ); ?>
						</a>
					</li>
					<li class="pg-payment">
						<a onClick="addTabPrefixInUrl(this);" href="#tabs-4">
							<span class="icon"></span><?php echo JText::_( 'PAGO_ORDER_TAB_PAYMENT' ); ?>
						</a>
					</li>
					<li class="pg-history">
						<a onClick="addTabPrefixInUrl(this);" href="#tabs-5">
							<span class="icon"></span><?php echo JText::_( 'PAGO_ORDER_TAB_HISTORY' ); ?>
						</a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="tabs-content pg-pad-20 pg-white-bckg pg-border">
				<?php
					$buttons = array( array(
						'class' => 'pg-btn-small  pg-btn-red pg-btn-unpublish',
						'element_type' => 'button',
						'type' => 'task',
						'task' => 'cancel_order',
						'confirm' => 'true',
						'rel' => '{\'order_id\':' . $this->order_id . '}',
						'text' => JText::_ ( 'PAGO_ORDER_CANCEL' )
					) );
					
					$tracking_number = array();
					// trigger for  tracking number link
					if ($this->order['details']->tracking_number != "") {
						$shipping_method = explode("-", $this->order['details']->ship_method_id);
						
						$dispatcher = KDispatcher::getInstance();
						JPluginHelper::importPlugin('pago_shippers');
						
						$tracking_number = $dispatcher->trigger('generate_link', array(
							$shipping_method[0], 
							$this->order['details']->tracking_number
						));
					}
					
				?>
				
				<div id="tabs-1">
					<div class="pg-row">
						<div class="pg-col-6">
							<?php 
								echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_TITLE_INFORMATION' ), JText::_( 'PAGO_ORDER_TITLE_INFORMATION_DESC' ), null, null, null, null, $this->order['details']->order_status, false );
								echo $this->information;
								echo PagoHtml::module_bottom();
							?>
						</div>
						
						<div class="pg-col-6">
							<div class="pg-row">
						
						
						
								<?php
    								$has_subscription = false;
    								
    								foreach($this->items as $item){
    									if($item->price_type == 'subscription'){
    										$has_subscription = true;
    										$sub_payment_data = json_decode($item->sub_payment_data);
    										$cid = $sub_payment_data->metadata->order_id;
    										$customer_id = $sub_payment_data->customer;
    										break;
    									}
    								}
								?>
                								
								<?php if ($has_subscription): ?>
								<div class="pg-col-12 pg-mb-20">
									<?php echo PagoHtml::module_top( JText::_( 'COM_PAGO_ORDERSI_UPDATE_CREDITCARD' ), null, null, null, null, null, null, false ); ?>
									<div class="pg-tab-content pg-pad-20 pg-border">
										<div class="pg-row no-margin">
											
												
											
											<input type="hidden" name="pago_customer_id" value="<?php echo $customer_id ?>">
											
											<div class="pg-row">
                								<div class="pg-col-8">
                									<label for="credicard">Credit Card Number</label>
                									<input type="text" name="pago_customer_number" value="<?php //echo $c->get('number', '4111111111111111') ?>">
                								</div>
                								<div class="pg-col-4">
                									<label for="security">CVV</label>
                									<input type="text" name="pago_customer_cvc"  value="<?php //echo $c->get('cvc', '123') ?>">
                								</div>
                							</div>
                							<div class="pg-row  pg-mt-20">
                								<div class="pg-col-12"><label>Expiration Date</label></div>
                								<div class="pg-col-6">
                									<select name="pago_customer_exp_month" class="inputbox pg-left pg-mb-20" >

                                                        <?php $months = cal_info(0);
                                                        $selected = false;
                                                        foreach($months['months'] as $value=>$name):
                                                            //if($c->get('exp_month') == $value) $selected = 'selected="selected"';
                                                        ?>
                                                            <option <?php echo $selected ?> value="<?php echo $value ?>"><?php echo $name ?></option>
                                                        <?php
                                                            $selected = false;
                                                        endforeach ?>

                                                    </select>
                								</div>
                								
                								<div class="pg-col-6">
                									<select name="pago_customer_exp_year" class="inputbox pg-left pg-mb-20" >
                                                        <?php 
                                                        $curYear = date("Y");
                                                        $selected = false;
                                                        for ($x = $curYear; $x < $curYear + 15; $x++):
                                                            //if($c->get('exp_year') == $x) $selected = 'selected="selected"';
                                                        ?>
                                                            <option <?php echo $selected ?> value="<?php echo $x ?>"><?php echo $x ?></option>
                                                        <?php
                                                            $selected = false;
                                                        endfor ?>

                                                    </select>
                                                    
                                                    
											
                								</div>
                									
                								</div>
                							
                						
											
											
										</div>
									</div>
									<?php echo PagoHtml::module_bottom(); ?>
								</div>
								
								<?php endif ?>
								
								<?php if(!$this->item_shipping) : ?>
								
								<div class="pg-col-12 pg-mb-20">
									<?php 
										echo PagoHtml::module_top( JText::_( 'PAGO_ORDERS_SHIPPING_DETAILS' ), null, null, null, null, null, null, false );
										echo $this->shipping_details; 
										echo PagoHtml::module_bottom();
									?>
								</div>
								
								<?php else: ?>
								
								<div class="pg-col-12 pg-mb-20">
									<?php echo PagoHtml::module_top( JText::_( 'PAGO_ORDERS_SHIPPING_DETAILS' ), null, null, null, null, null, null, false ); ?>
									<div class="pg-tab-content pg-pad-20 pg-border">
										<div class="pg-row">
											<?php foreach($this->items as $item) : ?>
												<div class="pg-col-12">
													<span class="field-heading">
														<label><?php echo $item->name; ?></label>
													</span>
													<div>
														<?php 
															if($item->varation_id) :
																$productVariant = Pago::get_instance('orders')->getVariantDetails($item->varation_id, $item->id);
																
																if(count($productVariant) > 0) :
														?>
															<p><?php echo JText::_( 'PAGO_ORDER_ITEM_VARIATION' ) . $productVariant->name; ?></p>
															<p><?php echo JText::_( 'PAGO_ORDER_ITEM_ATTRIBUTES' ); ?></p>
															<?php echo pago_orders::getAttribHtml($item->attributes); ?>
														<?php
																endif;
															endif;
														?>
													</div>
												</div>
											<?php endforeach; ?>
										
											<?php if(count($tracking_number) > 0 && $tracking_number[0]!=""): ?>
											<div class="pg-col-12 pg-displayastext">
												<div class="pg-row-item-inner">
													<span class="field-heading">
														<label><?php echo JText::_('COM_PAGO_TRACKING_LINK'); ?></label>
													</span>
													<div><?php echo $tracking_number[0];?></div>
												</div>
											</div>
											<?php endif;?>
										</div>
									</div>
									<?php echo PagoHtml::module_bottom(); ?>
								</div>
								
								<?php endif; ?>
								
								<div class="pg-col-12">
									<?php echo PagoHtml::module_top( JText::_( 'COM_PAGO_ORDERSI_UPDATE_ORDER_STATUS' ), null, null, null, null, null, null, false ); ?>
									<div class="pg-tab-content pg-pad-20 pg-border">
										<div class="pg-row no-margin">
											<?php if($this->order['details']->ship_method_id != NULL): ?>
											
											<div class="pg-col-6">
												
												<span class="field-heading">
													<label id="tracking_number-lbl" for="tracking_number" class="hasTooltip" title="<?php echo JText::_('COM_PAGO_LBL_SHIPPING_TRACKING_NUMBER'); ?>"><?php echo JText::_('COM_PAGO_LBL_SHIPPING_TRACKING_NUMBER'); ?></label>
												</span>
												<div>
													<input type="text" name="tracking_number" id="tracking_number" value="<?php echo $this->order['details']->tracking_number;?>">
												</div>
												
											</div>
											
											<?php else: ?>
								
											<input type="hidden" name="tracking_number" value="">
								
											<?php endif; ?>
											
											<div class="pg-col-6">
												<span class="field-heading">
													<label><?php echo JTEXT::_("COM_PAGO_LBL_ORDER_STATUS"); ?></label>
												</span>
												<?php 
													$order_status_list = Pago::get_instance('orders')->get_all_order_status();
													$types[] = JHTML::_('select.option', '0', '- ' . JText::_('COM_PAGO_ORDESI_SELECT_STATUS_LBL') . ' -');
													$types = array_merge($types, $order_status_list );
													echo $mylist['statuslist'] = JHTML::_('select.genericlist', $types,'order_status', '', 'value', 'text', $this->order['details']->order_status);
												?>
											</div>
										</div>
									</div>
									<?php echo PagoHtml::module_bottom(); ?>
								</div>
								
							</div>
						</div>
						
					</div>
				</div>
				
				<div id="tabs-2">
					<div class="pg-row">
						<div class="pg-col-6">
							<?php 
								echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_ADDRESS_BILLING' ), null, null, null, null, null, null, false );
								echo $this->address_billing;
								echo PagoHtml::module_bottom();
							?>
						</div>
						<div class="pg-col-6">
							<?php 
								echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_ADDRESS_SHIPPING' ), null, null, null, null, null, null, false );
								echo $this->address_shipping;
								echo PagoHtml::module_bottom();
							?>
						</div>
					</div>
				</div>
				
				<div id="tabs-3">
					<div class="pg-row pg-mb-20">
						<div class="pg-col-12">
							<?php //echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_TITLE_ITEMS' ), null, null, null, null, null, null, false ); ?>
							
							<!--
							<span class="new-item-field">
								<select multiple="multiple" style="width:200px" id="pg_ordersi_item_c_name" name="pg_ordersi_item_c_name">
									<?php foreach ($this->Allitems as $aitem): ?>
										<option value="<?php echo $aitem->id?>"><?php echo $aitem->name ?></option>
									<?php endforeach; ?>
								</select>
								<input type="hidden" name="pg_ordersi_item_id1" id="pg_ordersi_item_id1" value="" >
							</span>
							-->

							<div id="pg-order_item_status_update_message"></div>
							
							<div class="pg-table-wrap">
								<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
									<thead>
										<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
											<!--
											<td class="pg-sku">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_('PAGO_ADDORDER_REMOVE_ITEM'); ?>
												</div>
											</td>
											-->
											<td class="pg-item-name">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_('PAGO_ADDORDER_ITEM_NAME'); ?>
												</div>
											</td>
											<td class="pg-item-name">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_('PAGO_ADDORDER_ITEM_PRICE_WITHOUT_TAX'); ?>
												</div>
											</td>
											<td class="pg-item-name">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_('PAGO_ADDORDER_ITEM_TAX'); ?>
												</div>
											</td>
											<td class="pg-item-name">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_('PAGO_ADDORDER_ITEM_QUANTITY'); ?>
												</div>
											</td>
											<td class="pg-item-name">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_('PAGO_ITEM_TOTAL_PRICE'); ?>
												</div>
											</td>
											<?php if($this->order['details']->ship_method_id == NULL): ?>
											<td class="pg-item-name">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_('COM_PAGO_ITEM_STATUS'); ?>
												</div>
											</td>
											<?php endif; ?>
										</tr>
									</thead>
									<tbody>
										
										<?php foreach( $this->items as $i=>$item ): $item=(object)$item; ?>
										
										<tr class="pg-table-content pg-row<?php echo $i % 2; ?>" id ="pg_order_item_<?php echo $item->id?>">
											
											<!--
											<td class="pg-remove">
												<a onclick="removeOrderItem(<?php echo $this->order_id?>, <?php echo $item->id?>);" title="<?php echo JText::_( 'PAGO_ORDER_ITEM_REMOVE' ) ?>"></a>
											</td>
											-->
											
											<td class="pg-item-name">
												<?php 
													echo $item->name;
													
													$attributes = '';
													if($item->attributes)
														echo $attributes = pago_orders::getAttribHtml($item->attributes);
												
													$subcr = json_decode($item->sub_payment_data);
													
													if(is_object($subcr))
														echo pago_orders::getSubscrHtml($subcr, $item->order_item_id);
												?>
												
											</td>
											
											<td class="pg-item-name"><?php echo Pago::get_instance( 'price' )->format($item->price, $this->order['details']->order_currency); ?></td>
											
											<td class="pg-item-name"><?php echo Pago::get_instance( 'price' )->format($item->order_item_tax, $this->order['details']->order_currency); ?></td>
											
											<td class="pg-item"><input id="quantity_<?php echo $item->id;?>" class="pg-inputbox" type="text" title="Quantity" size="1" maxlength="4" name="qty" value="<?php echo $item->qty ?>"><button id="upate_<?php echo $item->id;?>" type="button" onclick="checkQuantity(this.id);return false;" style="border: 0; background: transparent"><img src="<?php echo JURI::root();?>components/com_pago/templates/default/images/update.png" width="20" alt="submit" />
</button><input type="hidden" name="currentQty" value="<?php echo $item->qty ?>" id="currentQty_<?php echo $item->id;?>"  /></td>
											
											<td class="pg-item-price"><?php echo Pago::get_instance( 'price' )->format((($item->price + $item->order_item_tax) * $item->qty), $this->order['details']->order_currency); ?></td>
											
											<?php 
												if ($this->order['details']->ship_method_id == NULL):
												
													$item_tracking_number = array();
													// trigger for  tracking number link
													if ($item->tracking_number != "") {
														$item_shipping_method = explode("-", $item->order_item_ship_method_id);
														
														$dispatcher = KDispatcher::getInstance();
														JPluginHelper::importPlugin('pago_shippers');
														$item_tracking_number = $dispatcher->trigger(
															'generate_link',
															array($item_shipping_method[0], $item->tracking_number)
														);
													}
											?>
											
											<td class='pg-item-status'>
												
											<?php if (count($item_tracking_number) > 0 && $item_tracking_number[0]!=""): ?>
											
												<div class="pg-row-item  pg-col-auto pg-displayastext">
													<div class="pg-row-item-inner">
														 <?php echo JText::_('COM_PAGO_TRACKING_LINK'); ?> - <?php echo $item_tracking_number[0];?>
													</div>
													<input type='hidden' name='tracking_number<?php echo $i; ?>' value=''>
												</div>
										 	
										 	<?php 
										 		else:
										 	?>
										 		<span class="field-heading">
										 			<label><?php echo JText::_('COM_PAGO_TRACKING_NO'); ?></label>
										 		</span>
										 		<div>
										 			<input type='text' name='tracking_number".$i."' value=''>
										 		</div>
											<?php
												endif;
											?>
											
												<input type='hidden' name='order_item_id".$i."' value='" . $item->id . "'>
											
											<?php
												$item_order_status_list = Pago::get_instance('orders')->get_all_order_status();
												$item_types[] = JHTML::_('select.option', '0', '- ' . JText::_('COM_PAGO_ORDESI_SELECT_STATUS_LBL') . ' -');
												$types = array_merge($item_types, $item_order_status_list );
												$item_mylist['statuslist'] = JHTML::_('select.genericlist', $types,'item_order_status'.$i, '', 'value', 'text', $item->order_item_status);
											?>
												<div class='pg-row-item  pg-col-auto pg-displayastext no-margin'>
													<span class="field-heading">
														<label><?php echo JText::_('PAGO_ORDER_ITEM_STATUS'); ?></label>
													</span>
													<div>
														<?php echo $item_mylist['statuslist']; ?>
													</div>
												</div>
												<!--
												<div class='pg-row-item-inner'>
													<div id='pg-button-update' class='pg-button pg-button-grey pg-button-update' tabindex='0'>
														<div>
															<button type='button' tabindex='-1' onclick='updateOrderSatus(this.form.item_order_status<?php echo $i;?>.value, this.form.tracking_number<?php echo $i;?>.value, this.form.order_item_id<?php echo $i;?>.value);'><?php echo JText::_('COM_PAGO_UPDATE') ?></button>
														</div>
													</div>
												</div>
												-->
											
											</td>
											
											<?php endif; ?>
											
										</tr>
										
										<?php endforeach; ?>
										
									</tbody>
								</table>
							</div>
				
							<!--
							<table>
								<tr class="pg-sub-heading">
									<td colspan="6">
										<?php echo JText::_('PAGO_ITEMS') ?>
									<div class="pg-title-button-wrap pg-sub-title-button-wrap">
										<a href="javascript:void(0);"  class="pg-title-button pg-button-add pg-sub-title-button" rel="">
											<span class="icon-32-add pg-sub-title-button" id="pg-ordersi_item_save_btn" title="Save"></span>
											Save
										</a>
									</div>
									</td>
								</tr>
								<tr class="pg-sub-heading new-item-con" id="order_item_tr1">
									<td>
										<span class="new-currency-field">
											<div id="pg_ordersi_item_remove1"></div>
										</span>
									</td>
									
									<td>
										<span class="new-item-field"><input type='text' id='pg_ordersi_item_name1' name='pg_ordersi_item_name1' value="" readonly='true' />
											<input type="hidden" name="pg_ordersi_item_id1" id="pg_ordersi_item_id1" value="" variationId="0" >
											<input type="hidden" name="pg_ordersi_item_variation_id1" id="pg_ordersi_item_variation_id1" value="" >
										</span>
									</td>
									<td>
										<span class="new-item-field">
											<input type="text" id="pg_ordersi_item_price_without_tax1" name="pg_ordersi_item_price_without_tax1" value=""  readonly="true"/>
										</span>
									</td>
									<td>
										<span class="new-item-field">
											<input type="text" id="pg_ordersi_item_tax1" name="pg_ordersi_item_tax1" value="" readonly="true"/>
											<input type="hidden" id="pg_ordersi_item_with_tax1" name="pg_ordersi_item_with_tax1" value="0"  readonly="true"/>
											<input type="hidden" id="pg_ordersi_item_tax_rate1" name="pg_ordersi_item_tax_rate1" value="0"  readonly="true"/>
											<input type="hidden" id="pg_ordersi_apply_tax_on_shipping1" name="pg_ordersi_apply_tax_on_shipping1" value="0"  readonly="true"/>
											<input type="hidden" id="pg_ordersi_item_free_shipping1" name="pg_ordersi_item_free_shipping1" value="0"  readonly="true"/>
											<input type="hidden" id="pg_ordersi_item_shipping_tax1" name="pg_ordersi_item_shipping_tax1" value="0"  readonly="true"/>
										</span>
									</td>
									<td>
										<span class="new-item-field">
											<input type="text" id="pg_ordersi_item_quantity1" name="pg_ordersi_item_quantity1" value=""  onKeyup=updateOrderTotalO(1) />
											<input type="hidden" name="pg_ordersi_item_qty1" id="pg_ordersi_item_qty1" value="" >
										</span>
									</td>
									<td>
										<span class="new-item-field">
											<input type="text" id="pg_ordersi_item_total_price1" name="pg_ordersi_item_total_price1" value="" readonly="true"/>
										</span>
									</td>
								</tr>
							</table>
							-->
							
							<div class="itemDetails ordersi"></div>
							
							<?php //echo PagoHtml::module_bottom(); ?>
						</div>
					</div>
				</div>
				
				<div id="tabs-4">
					<div class="pg-row">
						<?php if($this->payments[0]['isFraud']): ?>
							
						<div class="pg-col-8 pg-mb-20">
							<div class="pg-info-con">
								<img src="components/com_pago/css/img/pg-info.png" class="pg-info-con-image">
								<span class="pg-info-con-text"><strong><?php echo JText::_ ( 'COM_PAGO_ORDERSI_FRAUD_ORDER_DATA' ); ?></strong> <?php echo $this->payments[0]['fraudMessage']; ?></span>
								<div>
									<button type="button" tabindex="-1" class="pg-btn pg-btn-medium pg-btn-light pg-btn-info-con" onclick="checkOrderwithMaxmind(this.form.pg_ordersi_order_id.value);"><?php echo JText::_('COM_PAGO_ORDERSI_CHECK_MAXMIND'); ?></button>
								</div>
							</div>
						</div>
							
						<?php endif; ?>
							
						<div class="pg-col-12">
							<div class="pg-table-wrap">
								<table class="pg-table pg-repeated-rows">
									<thead>
										<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
											<td class="">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_PAYMENT_TXN_ID'); ?>
												</div>
											</td>
											<td class="pg-sku ">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_PAYMENT_DATE'); ?>
												</div>
											</td>
											<td class="pg-item-name ">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_PAYMENT_DESCRIPTION'); ?>
												</div>
											</td>
											<td class="pg-item-name ">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_PAYMENT_AMOUNT'); ?>
												</div>
											</td>
											<td class="">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_PAYMENT_REFUNDED'); ?>
												</div>
											</td>
											<td class="pg-type ">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_AORDER_PAYMENT_ACTION'); ?>
												</div>
											</td>
										</tr>
									</thead>
									<tbody>
										<?php 
											$initial_payment = (object)$this->payments[0];
											
											if (isset($initial_payment->txn_id)): 
										?>
										<tr class="pg-table-content">
											<td>
												<?php echo $initial_payment->txn_id ?>
											</td>
											<td>
												<?php echo $initial_payment->date ?>
											</td>
											<td>
												<?php echo $initial_payment->payment_data ?>
											</td>
											<td>
												<?php echo Pago::get_instance( 'price' )->format($initial_payment->amount, $this->order['details']->order_currency); ?>
											</td>
											<td>
												<div id="pg-order_refundtotal"><?php echo Pago::get_instance( 'price' )->format($this->order['details']->order_refundtotal, $this->order['details']->order_currency); ?></div>
											</td>
											<td>
												<?php if($this->order['details']->payment_gateway == 'paypalexpress'): ?>	
													<input value="<?php echo ( $initial_payment->amount - $this->order['details']->order_refundtotal ) ?>" type="text" name="refund_partial<?php echo $this->order_id ?>" id="refund_partial<?php echo $this->order_id ?>"/>
												
													<button type="button" class="pg-btn pg-btn-medium pg-btn-light" confirm="true" onclick="refundOrderPayment();">
														<?php echo JText::_( 'PAGO_ORDER_REFUND_PAYMENT' ) ?>
													</button>
												<?php else: ?>
														<?php //echo JText::_( 'PAGO_ORDER_REFUND_NOT_POSSIBLE' ) ?>
												<?php endif ?>
											</td>
										</tr>
										<?php else: ?>
										<tr>
											<td colspan="6"><?php echo JText::_( 'PAGO_ORDER_NO_PAYMENTS' ) ?></td>
										</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				
				<div id="tabs-5">
					<div class="pg-row">
						<div class="pg-col-12">
							<div class="pg-table-wrap">
								<table class="pg-table pg-repeated-rows" id="">
									<thead>
										<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
											<td class="">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_LOG_DATE'); ?>
												</div>
											</td>
											<td class="pg-sku ">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_LOG_STATUS'); ?>
												</div>
											</td>
											<td class="pg-item-name ">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_LOG_DESCRIPTION'); ?>
												</div>
											</td>
											<td class="pg-item-name ">
												<div class="pg-sort-indicator-wrapper">
													<?php echo JText::_( 'PAGO_ORDER_LOG_ACTION'); ?>
												</div>
											</td>
										</tr>
									</thead>
									<tbody>
										<?php foreach($this->OrderLogs as $ordLogs): 
											$data = json_decode($ordLogs->action);
										?>
										<tr class="pg-table-content">
											<td ><?php echo $ordLogs->date; ?></td>
											<td ><?php echo $ordLogs->order_status; ?></td>
											<td ><?php echo @$data->id . '<br>' . $ordLogs->description ?></td>
											<td >
												<?php if(!is_object($data)): ?>
													<?php echo $ordLogs->action; ?>
												<?php else: ?>
													<?php echo $data->type; ?>
													
													<?php if(($data->type == 'charge.succeeded') && ($ordLogs->order_status != 'Refunded')): ?>
														
														<a  class="pg-btn-medium pg-btn-light pg-btn-red"
															href="javascript:;"
															onclick="return refund_prompt('<?php echo @$data->id ?>', '<?php echo $this->order['details']->order_id ?>', '<?php echo $ordLogs->order_log_id ?>', '<?php echo @$data->amount ?>')"
															role="button">
															<?php echo JText::_('PAGO_ORDER_REFUND_PAYMENT') ?>
														</a>
													<?php elseif(($data->type == 'charge.refunded') && ($data->balance > 0) && !strstr($ordLogs->description, 'Actioned')): ?>
													
														<a  class="pg-btn-medium pg-btn-light pg-btn-red"
															href="javascript:;"
															onclick="return refund_prompt('<?php echo @$data->id ?>', '<?php echo $this->order['details']->order_id ?>', '<?php echo $ordLogs->order_log_id ?>', '<?php echo @$data->balance ?>')"
															role="button">
															<?php echo JText::_('PAGO_ORDER_REFUND_PAYMENT') ?>
														</a>
													
													<?php endif ?>
													
												<?php endif ?>
												
											</td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				
			</div>
			<table class="pg-table pg-repeated-rows pg-items-manager">
					<tbody>
					<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="70%">&nbsp;</td>
						<td class="pg-item-name" >
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ADDORDER_ORDER_SUBTOTAL'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_subtotal_div">
								<?php echo Pago::get_instance( 'price' )->format($this->order['details']->order_subtotal, $this->order['details']->order_currency); ?>
							</div>
							<input type="hidden"  name="pg_addorder_order_subtotal" value="0" id="pg_addorder_order_subtotal" >
						</td>
					</tr>
						<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="70%">&nbsp;</td>
						<td class="pg-item-name" >
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ADDORDER_ORDER_TAX'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_tax_div">
								<?php echo Pago::get_instance( 'price' )->format($this->order['details']->order_tax, $this->order['details']->order_currency); ?>
							</div>
							<input type="hidden"  name="pg_addorder_order_tax" value="0" id="pg_addorder_order_tax" >
						</td>
					</tr>
					<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="70%">&nbsp;</td>
						<td class="pg-item-name" >
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ORDER_DISCOUNT'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_discount_div">
								<?php echo Pago::get_instance( 'price' )->format($this->order['details']->order_discount +$this->order['details']->coupon_discount, $this->order['details']->order_currency); ?>
							</div>
							<input type="hidden"  name="pg_addorder_order_discount" value="0" id="pg_addorder_order_discount" >
						</td>
					</tr>
					</tr>
						<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="70%">&nbsp;</td>
						<td class="pg-item-name" >
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ADDORDER_ORDER_SHIPPING'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_shipping_div">
								<?php echo Pago::get_instance( 'price' )->format($this->order['details']->order_shipping, $this->order['details']->order_currency); ?>
							</div>
							<input type="hidden"  name="pg_addorder_order_shipping" value="0" id="pg_addorder_order_shipping" >
							<input type="hidden"  name="pg_addorder_order_shipping_old" value="0" id="pg_addorder_order_shipping_old" >
							<input type="hidden"  name="pg_addorder_order_shipping_name" value="0" id="pg_addorder_order_shipping_name" >
							
						</td>
					</tr>
						<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="70%">&nbsp;</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ADDORDER_ORDER_TOTAL'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_total_div">
								<?php echo Pago::get_instance( 'price' )->format($this->order['details']->order_total, $this->order['details']->order_currency); ?>
							</div>
							<input type="hidden"  name="pg_addorder_order_total" value="0" id="pg_addorder_order_total" >
						</td>
					</tr>
					</tbody>
				</table>
		</div>
		<input type="hidden" name="cid[]" value="<?php echo $this->order_id; ?>" />
		<input type="hidden" name="rel" value="" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->order_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="ordersi" />
		<input type="hidden" name="controller" value="ordersi" />
		<input type="hidden" id ="pg_ordersi_order_id" name="pg_ordersi_order_id" value="<?php echo $this->order_id; ?>" />
		<input type="hidden" name="pg-filter_user" id="pg-filter_user" value="<?php echo $this->user_id; ?>" />
		<input type="hidden" name="pg-billing_address" id="pg-billing_address" value="<?php echo $this->address_id; ?>" />
		<input type="hidden" name="pg-shipping_address" id="pg-shipping_address" value="<?php echo $this->saddress_id; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
<!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();
?>