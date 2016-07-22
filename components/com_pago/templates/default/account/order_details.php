<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
$addresses = $this->order['addresses'];
$details = $this->order['details'];
$items = $this->order['items'];
?>
<div id="pg-account-order-details">
	<span class="pg-account-order-label"><?php echo JText::_('PAGO_ACCOUNT_ORDER_NUMBER'); ?>:</span> <span class="pg-account-order-number"><?php echo $details->order_id; ?></span><br />
	<span class="pg-account-order-label"><?php echo JText::_('PAGO_ACCOUNT_ORDER_DATE'); ?>:</span> <span class="pg-account-order-date"><?php echo $details->cdate; ?></span>
	<div id="pg-account-order-addresses" class="row">
		<div id="pg-account-order-shipping" class = "col-sm-6">
			<div class = "pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_ACCOUNT_ORDER_SHIPTO'); ?></h3>
				<div>
					<div class="pg-account-order-address">
						<?php if (!empty($addresses['shipping']->first_name)) : ?>
							<div class = "pg-account-order-address-first-name">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_FIRST_NAME'); ?>: </span>
								<span><?php echo $addresses['shipping']->first_name; ?> </span>
							</div>
						<?php endif; ?>

						<?php if (!empty( $addresses['shipping']->middle_name )) : ?>
							<div class = "pg-account-order-address-middle-name">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_MIDDLE_NAME'); ?>: </span>
								<span><?php echo $addresses['shipping']->middle_name; ?> </span>
							</div>						
						<?php endif; ?>

						<?php if (!empty($addresses['shipping']->last_name)) : ?>
							<div class = "pg-account-order-address-last-name">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_LAST_NAME'); ?>: </span>
								<span><?php echo $addresses['shipping']->last_name; ?> </span>
							</div>						
						<?php endif; ?>

						<?php if (!empty($addresses['shipping']->company)) : ?>
							<div class = "pg-account-order-address-company">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_COMPANY'); ?>: </span>
								<span><?php echo $addresses['shipping']->company; ?> </span>
							</div>						
						<?php endif; ?>

						<?php if (!empty($addresses['shipping']->address_1)) : ?>
							<div class = "pg-account-order-address-street">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_ADDRESS'); ?>: </span>
								<span><?php echo $addresses['shipping']->address_1; ?> </span>
							</div>	
						<?php endif; ?>
						
						<?php if (!empty($addresses['shipping']->address_2)) : ?>
							<div class = "pg-account-order-address-street">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_ADDRESS2'); ?>: </span>
								<span><?php echo $addresses['shipping']->address_2; ?> </span>
							</div>	
						<?php endif; ?>
						
						<?php if (!empty($addresses['shipping']->city)) : ?>
							<div class = "pg-account-order-address-city">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_CITY'); ?>: </span>
								<span><?php echo $addresses['shipping']->city; ?> </span>
							</div>
						<?php endif; ?>

						<?php if (!empty($addresses['shipping']->state)) : ?>
							<div class = "pg-account-order-address-state">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_STATE'); ?>: </span>
								<span><?php echo $addresses['shipping']->state; ?> </span>
							</div>		
						<?php endif; ?>
						
						<?php if (!empty($addresses['shipping']->zip)) : ?>
							<div class = "pg-account-order-address-zip">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_ZIP_CODE'); ?>: </span>
								<span><?php echo $addresses['shipping']->zip; ?> </span>
							</div>	
						<?php endif; ?>

						<?php if (!empty( $addresses['shipping']->phone_1)) : ?>
							<div class = "pg-account-order-address-phone">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_PHONE'); ?>: </span>
								<span><?php echo $addresses['shipping']->phone_1; ?> </span>
							</div>							
						<?php endif; ?>

						<?php if (!empty( $addresses['shipping']->phone_2)) : ?>
							<div class = "pg-account-order-address-phone">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_PHONE2'); ?>: </span>
								<span><?php echo $addresses['shipping']->phone_2; ?> </span>
							</div>							
						<?php endif; ?>
					</div>
					<?php if (!empty($details->ship_method_id)) : ?>
						<div id="pg-account-order-shipping-method">
							<span class = "pg-address-field-name"><?php echo JText::_('PAGO_ACCOUNT_ORDER_SHIPPING_METHOD_LABEL'); ?></span>
							<span><?php echo $details->ship_method_id; ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div id="pg-account-order-billing" class = "col-sm-6">
			<div class = "pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_ACCOUNT_ORDER_BILLTO'); ?></h3>
				<div>
					<div class="pg-account-order-address">
						<?php if (!empty($addresses['billing']->first_name)) : ?>
							<div class = "pg-account-order-address-first-name">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_FIRST_NAME'); ?>: </span>
								<span><?php echo $addresses['billing']->first_name; ?> </span>
							</div>
						<?php endif; ?>

						<?php if (!empty( $addresses['billing']->middle_name )) : ?>
							<div class = "pg-account-order-address-middle-name">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_MIDDLE_NAME'); ?>: </span>
								<span><?php echo $addresses['billing']->middle_name; ?> </span>
							</div>						
						<?php endif; ?>

						<?php if (!empty($addresses['billing']->last_name)) : ?>
							<div class = "pg-account-order-address-last-name">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_LAST_NAME'); ?>: </span>
								<span><?php echo $addresses['billing']->last_name; ?> </span>
							</div>						
						<?php endif; ?>

						<?php if (!empty($addresses['billing']->company)) : ?>
							<div class = "pg-account-order-address-company">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_COMPANY'); ?>: </span>
								<span><?php echo $addresses['billing']->company; ?> </span>
							</div>						
						<?php endif; ?>

						<?php if (!empty($addresses['billing']->address_1)) : ?>
							<div class = "pg-account-order-address-street">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_ADDRESS'); ?>: </span>
								<span><?php echo $addresses['billing']->address_1; ?> </span>
							</div>	
						<?php endif; ?>
						
						<?php if (!empty($addresses['billing']->address_2)) : ?>
							<div class = "pg-account-order-address-street">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_ADDRESS2'); ?>: </span>
								<span><?php echo $addresses['billing']->address_2; ?> </span>
							</div>	
						<?php endif; ?>
						
						<?php if (!empty($addresses['billing']->city)) : ?>
							<div class = "pg-account-order-address-city">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_CITY'); ?>: </span>
								<span><?php echo $addresses['billing']->city; ?> </span>
							</div>
						<?php endif; ?>

						<?php if (!empty($addresses['billing']->state)) : ?>
							<div class = "pg-account-order-address-state">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_STATE'); ?>: </span>
								<span><?php echo $addresses['billing']->state; ?> </span>
							</div>		
						<?php endif; ?>
						
						<?php if (!empty($addresses['billing']->zip)) : ?>
							<div class = "pg-account-order-address-zip">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_ZIP_CODE'); ?>: </span>
								<span><?php echo $addresses['billing']->zip; ?> </span>
							</div>	
						<?php endif; ?>

						<?php if (!empty( $addresses['billing']->phone_1)) : ?>
							<div class = "pg-account-order-address-phone">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_PHONE'); ?>: </span>
								<span><?php echo $addresses['billing']->phone_1; ?> </span>
							</div>							
						<?php endif; ?>

						<?php if (!empty( $addresses['billing']->phone_2)) : ?>
							<div class = "pg-account-order-address-phone">
								<span class = "pg-address-field-name"><?php echo JTEXT::_('PAGO_PHONE2'); ?>: </span>
								<span><?php echo $addresses['billing']->phone_2; ?> </span>
							</div>							
						<?php endif; ?>
					</div>
					<?php if (!empty($details->payment_gateway)) : ?>
						<div id="pg-account-order-billing-method">
							<span class = "pg-address-field-name"><?php echo JText::_('PAGO_ACCOUNT_ORDER_BILLING_METHOD_LABEL'); ?>: </span>
							<span><?php
							if($details->payment_gateway == "banktransfer")
							{
								$dispatcher = KDispatcher::getInstance();
								JPluginHelper::importPlugin('pago_gateway');
								$plugin = JPluginHelper::getPlugin('pago_gateway',$details->payment_gateway);
								$pluginParams = new JRegistry(@$plugin->params);
								$details->payment_gateway = $details->payment_gateway . "<br/>" . JTEXT::_("COM_PAGO_BANK_INFORMATION") . $pluginParams->get('txtextra_info');
							}
							
							 echo $details->payment_gateway; ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	
	
	
	<?php
		$has_subscription = false;
		
		foreach($items as $item){
			if($item->price_type == 'subscription'){
				$has_subscription = true;
				$sub_payment_data = json_decode($item->sub_payment_data);
				$order_id = $sub_payment_data->metadata->order_id;
				$customer_id = $sub_payment_data->customer;
				break;
			}
		}
	?>
					
	<?php if ($has_subscription): ?>
								
		<div class = "pg-wrapper-container">
			<h3><?php echo JText::_('PAGO_ACCOUNT_ORDER_CCREDITCARD'); ?></h3>
				<div class="pg-account-order-address">
					
					
					<form action="index.php">
						<input type="hidden" name="option" value="com_pago">
						<input type="hidden" name="view" value="account">
						<input type="hidden" name="task" value="update_cc">
						<input type="hidden" name="order_id" value="<?php echo $order_id ?>">
						<input type="hidden" name="pago_customer_id" value="<?php echo $customer_id ?>">
						
						<div class="pg-row">
							<div class="pg-col-8">
								<label for="credicard"><?php echo JText::_('PAGO_ACCOUNT_ORDER_CCNUMBER'); ?></label>
								<input type="text" name="pago_customer_number" value="<?php //echo $c->get('number', '4111111111111111') ?>">
							</div>
							<div class="pg-col-4">
								<label for="security"><?php echo JText::_('PAGO_ACCOUNT_ORDER_CCCVC'); ?></label>
								<input type="text" name="pago_customer_cvc"  value="<?php //echo $c->get('cvc', '123') ?>">
							</div>
						</div>
						<div class="pg-row  pg-mt-20">
							<div class="pg-col-12"><label><?php echo JText::_('PAGO_ACCOUNT_ORDER_CCEXPDATE'); ?></label></div>
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
							
							<div class="pg-col-6 pg-mt-20 pg-right">
								<input value="<?php echo JText::_('PAGO_ORDERS_CCUPDATE'); ?>" name="<?php echo JText::_('PAGO_ORDERS_CCUPDATE'); ?>" type="submit" class="apply pg-btn-medium pg-btn-dark pg-btn-green" >
								
								</a>
							</div>
						
						</form>
					</div>
                							
                							
                							
			</div>
		</div>
	<?php endif ?>	
	
	<div id="pg-account-order-summary">
		<div class = "pg-wrapper-container">
			<h3><?php echo JText::_('PAGO_ACCOUNT_ORDER_SUMMARY_TITLE'); ?></h3>
			<div id="pg-account-order-items">
				<table>
					<thead>
						<tr>
							<th class="pg-account-order-item-qty"><?php echo JText::_('PAGO_ACCOUNT_ORDER_ITEMS_TABLE_QTY'); ?></th>
							<th class="pg-account-order-item-desc"><?php echo JText::_('PAGO_ACCOUNT_ORDER_ITEMS_TABLE_DESC'); ?></th>
							<th class="pg-account-order-item-price"><?php echo JText::_('PAGO_ACCOUNT_ORDER_ITEMS_TABLE_PRICE'); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="2" class="pg-account-order-label"><strong><?php echo JText::_('PAGO_ACCOUNT_ORDER_TOTAL_SUBTOTAL'); ?></strong></td>
							<td class="pg-account-order-subtotal">$<?php echo number_format( ( $details->order_subtotal ), 2 ) ?></td>
						</tr>							
						<tr>
							<td colspan="2" class="pg-account-order-label"><?php echo JText::_('PAGO_ACCOUNT_ORDER_TOTAL_TAX_TOTAL'); ?></td>
							<td class="pg-account-order-tax-total">$<?php echo number_format( ( $details->order_tax ), 2 ) ?></td>
						</tr>							
						<tr>
							<td colspan="2" class="pg-account-order-label"><?php echo JText::_('PAGO_ACCOUNT_ORDER_TOTAL_SHIPPING_TOTAL'); ?></td>
							<td class="pg-account-order-shipping-total">$<?php echo number_format( ( $details->order_shipping ), 2 ) ?></td>
						</tr>							
						<tr>
							<td colspan="2" class="pg-account-order-label"><?php echo JText::_('PAGO_ACCOUNT_ORDER_TOTAL_TOTAL'); ?></td>
							<td class="pg-account-order-total">$<?php echo number_format( ( $details->order_total ), 2 ) ?></td>
						</tr>
					</tfoot>							
					<tbody>
						<?php foreach( $items as $item ) : ?>
						<tr>
							<td class="pg-account-order-item-qty"><?php echo $item->qty ?></td>
							<td class="pg-account-order-item-desc">
								<?php 
									$subscr = json_decode($item->sub_payment_data);
													
									if(is_object($subscr)){
										
										 if(!$subscr->cancel_at_period_end): ?>
		
											<a class="pg-red-text-btn" 
												href="<?php echo JRoute::_('index.php?option=com_pago&view=account&task=subscr_cancel&id=' . $subscr->id . '&order_item_id=' . $item->order_item_id . '&customer=' . $subscr->customer . '&order_id='. $item->order_id )  ?>" 
												role="button">
												<?php echo JText::_('PAGO_ACCOUNT_ORDER_CANCEL_SUBSCR'); ?>
											</a>
												
										<?php else: ?>
												
											<a class="pg-green-text-btn" 
												href="<?php echo JRoute::_('index.php?option=com_pago&view=account&task=subscr_reinstate&id=' . $subscr->id . '&order_item_id=' . $item->order_item_id . '&customer=' . $subscr->customer . '&order_id='. $item->order_id ) ?>" 
												role="button">
												<?php echo JText::_('PAGO_ACCOUNT_ORDER_RE_SUBSCR'); ?>
											</a>
							
										<?php endif;
										
									}
								?>
								&nbsp;<a href="<?php echo $this->nav->build_url('item', $item->id, true, $item->primary_category) ?>"><?php echo $item->name; ?></a></td>
							<td class="pg-account-order-item-price">$<?php echo number_format( ( $item->price * $item->qty ), 2 ) ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php if ($details->tracking_number != ""):?>
				<table>
						<tr>
							<td><?php echo JText::_('PAGO_ACCOUNT_ORDER_TRACKING_LINK_TITLE')?>:
								<?php
								$tracking_number = array();
								// trigger for  tracking number link
								if ($details->tracking_number != "")
								{
									$shipping_method = explode("-", $details->ship_method_id);
									$dispatcher = KDispatcher::getInstance();
									JPluginHelper::importPlugin('pago_shippers');

									$tracking_number = $dispatcher->trigger('generate_link', array(
										$shipping_method[0],
										$details->tracking_number
									));

									echo $tracking_number[0];
								}

							?>
							</td>
						</tr>
				</table>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>