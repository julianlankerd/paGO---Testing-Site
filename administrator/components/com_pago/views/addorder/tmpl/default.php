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

$CURRENCY_SYMBOL = CURRENCY_SYMBOL;

$users_l = json_encode($this->all_users);
$doc->addScriptDeclaration('var $USERS = '.$users_l.';');

PagoHtml::pago_top( $menu_items, 'tabs' );
$doc = JFactory::getDocument();
$doc->addScriptDeclaration( "
	jQuery(document).ready(function(){
		jQuery('#pg_addorder_order_subtotal_div').append(' ($CURRENCY_SYMBOL)');
		jQuery('#pg_addorder_order_tax_div').append(' ($CURRENCY_SYMBOL)');
		jQuery('#pg_addorder_order_total_div').append(' ($CURRENCY_SYMBOL)');
		jQuery('#pg_addorder_order_shipping_div').append(' ($CURRENCY_SYMBOL)');
	})
	" );
?>
<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	jQuery.noConflict();
	if(task == 'cancel'){
		Joomla.submitform(task);
		return;
	}
}
jQuery( document ).ready(function() {
		var userId = <?php echo $this->user_id?>;
		var addressId = <?php echo $this->address_id?>;
		var saddressId = <?php echo $this->saddress_id?>;
		getAddressInformation(userId, addressId, saddressId);
		getUserAddress('',userId, addressId, saddressId);
	});
</script>
<div class="select_user_popup" style="width:100%; height:100%; z-index: 150; display: none ">	
	<div class="existing_users_details_input">
		<i class="fa fa-search"></i>
			<input type="text" class="ui-autocomplete-input" id="users-list-add" name="users_list" autocomplete="off" aria-autocomplete="list" aria-haspopup="true">
		<i class="fa fa-times"></i>
	</div>
</div>
<div class="pg-content">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php PagoHtml::deploy_tabpanel( 'tabs' ) ?>
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li class="first pg-information"><a href="#tabs-1"><span class="icon"></span><?php echo JText::_('PAGO_ORDER_TAB_INFORMATION'); ?></a></li>
					<?php if($this->user_id) : ?>
					<li class="pg-attributes"><a href="#tabs-2"><span class="icon"></span><?php echo JText::_('PAGO_ORDER_TAB_ITEMS'); ?></a></li>
					<li class="pg-meta"><a href="#tabs-3"><span class="icon"></span><?php echo JText::_('PAGO_ORDER_TAB_SHIPPING'); ?></a></li>
					<li class="pg-meta"><a href="#tabs-4"><span class="icon"></span><?php echo JText::_('PAGO_ORDER_TAB_PAYMENT'); ?></a></li>
					<li class="pg-media"><a href="#tabs-5"><span class="icon"></span><?php echo JText::_('PAGO_ORDER_TAB_HISTORY'); ?></a></li>
					<?php endif; ?>
				</ul>
				<div class="clear"></div>
			</div>
			<?php
			$payment_plugins = PagoHelper::get_all_plugins('pago_gateway', 1);
			$shipping_plugins = PagoHelper::get_all_plugins('pago_shippers', 1);

			if (count($payment_plugins) <= 0 || count($shipping_plugins) <= 0)
			{
			?>
				<div id="tabs-1">
					<?php echo PagoHtml::module_top( JText::_( 'PAGO_COMING_SOON' ), false ) ?>
					<table border="0" cellspacing="0" cellpadding="0" class="adminlist">
					<tbody>
						<?php if(count($payment_plugins) <= 0) : ?>
							<tr>
								<td width="100" align="right">
								<?php echo "Please set atleast one Payment Methods from below Available Payment Methods." ?>
								<?php $avail_payment_plugins = PagoHelper::get_all_plugins('pago_gateway', 2);
										foreach($avail_payment_plugins as $key => $payment)
										{
												echo "<br/>";
												echo JText::_($payment->name);
										}

								?>
								</td>
							</tr>
						<?php endif; ?>
						<?php if(count($shipping_plugins) <= 0) : ?>
							<tr>
								<td width="100" align="right">
								<?php echo "Please set atleast one Shipping Methods from below Available Shipping Methods." ?>
								<?php $avail_shipping_plugins = PagoHelper::get_all_plugins('pago_shippers', 2 );
										foreach($avail_shipping_plugins as $key => $shipping)
										{
												echo "<br/>";
												echo JText::_($shipping->name);
										}

								?>
								</td>
							</tr>
						<?php endif; ?>
					</table>
					<?php echo PagoHtml::module_bottom() ?>
				</div>
			<?php
			}
			else
			{ ?>
			<div id="tabs-1">

			<?php echo PagoHtml::module_top(JText::_('PAGO_ORDER_TITLE_INFORMATION'), JText::_('PAGO_ORDER_TITLE_INFORMATION_DESC'), null, null, null, null, JText::_('PAGO_NEW_ORDER')) ?>
				<table border="0" cellspacing="0" cellpadding="0" class="adminlist">
					<tbody>
						<tr>
							<td width="100" align="right">
								<?php //echo JText::_('COM_PAGO_SELECT_USER'); ?>
								<button class="open_select_user_popup">Select User</button>
							</td>
							<td  style="display:none">

								<select name="filter_user" id="pg-filter_user" class="inputbox" onChange="getAddressInformation(this.value,0,0);">
									<option value="0"><?php echo JText::_('COM_PAGO_SELECT_USER');?></option>
									<?php echo JHtml::_('select.options', $this->users, 'value', 'text', $this->user_id);?>
								</select>
							</td>
							<td width="50" >
								<input type="checkbox" name="address_mailing_same_as_billing" id ="address_mailing_same_as_billing" value="1">
								<label for="address_mailing_same_as_billing"></label>
							</td>
							<td width="300" align="right">
								<?php echo JText::_('COM_PAGO_SHIPPING_SAME_AS_BILLING'); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</tbody>
		</table>
		<div class="pg-split-table">
			<div class="pg-col6">
				<div id="new-pago-user-div" style="font-size:16px;color:red;">
				</div>
				<div id="Billingaddress" >
				</div>
				<div id="addBillingAddress" class="after-js-change">
					<table class="pg-table pg-first">
						<tbody>
							<?php echo $this->address_billing ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="pg-col6">
				<div id="Shippingaddress">
				</div>
				<div id="addShippingaddress" class="after-js-change" >
				<table class="pg-table pg-last">
					<tbody>
						<?php echo $this->address_shipping ?>
					</tbody>
				</table>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="pg-split-table">
			<input type="button" id="savebtn" name="savebtn" value="<?php echo JText::_('PAGO_ADDORDER_NEXT_STEP'); ?>" />
		</div>
		<table class="pg-table">
			<tbody>
			<?php echo PagoHtml::module_bottom() ?>
			</div>
			<?php } ?>
			<?php if($this->user_id) : ?>
			<div id="tabs-2"> <?php echo PagoHtml::module_top(JText::_('PAGO_ADDORDER_TITLE_ITEMS'), false ) ?>
				
				UNDER CONSTRUCTION please not open ticket
				<select style="width:200px" id="pg_addorder_item_c_name1" name="pg_addorder_item_c_name1" class="order_item_select">
					<?php
					foreach ($this->items as $item)
					{
					?>
						<option value="<?php echo $item->id?>"><?php echo $item->name ?></option>
					<?php
					}
					?>
				</select>

				
				<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
					<thead>
						<tr class="pg-sub-heading">
							<td class="pg-sku">
								<div class="pg-sort-indicator-wrapper">
									<?php //echo JText::_('PAGO_ADDORDER_REMOVE_ITEM'); ?>
								</div>
							</td>
							<td class="pg-sku">
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
						</tr>
					</thead>
					<tbody>
					<tr class="pg-sub-heading new-item-con" id="order_item_tr1">
						<td>
							<span class="new-currency-field">
								<div id="pg_addorder_item_remove1"></div>
							</span>
						</td>
						<td>
							<span class="new-item-field"><input type='text' id='pg_addorder_item_name1' name='pg_addorder_item_name1' value="" readonly='true' />
								<input type="hidden" name="pg_addorder_item_id1" id="pg_addorder_item_id1" value="" variationId="0" >
								<input type="hidden" name="pg_addorder_item_variation_id1" id="pg_addorder_item_variation_id1" value="" >
							</span>
						</td>
						<td>

							<span class="new-item-field">
								<input type="text" id="pg_addorder_item_price_without_tax1" name="pg_addorder_item_price_without_tax1" value=""  readonly="true"/>
							</span>
						</td>
						<td>
							<span class="new-item-field">
								<input type="text" id="pg_addorder_item_tax1" name="pg_addorder_item_tax1" value="" readonly="true"/>
								<input type="hidden" id="pg_addorder_item_with_tax1" name="pg_addorder_item_with_tax1" value="0"  readonly="true"/>
								<input type="hidden" id="pg_addorder_item_tax_rate1" name="pg_addorder_item_tax_rate1" value="0"  readonly="true"/>
								<input type="hidden" id="pg_addorder_apply_tax_on_shipping1" name="pg_addorder_apply_tax_on_shipping1" value="0"  readonly="true"/>
								<input type="hidden" id="pg_addorder_item_free_shipping1" name="pg_addorder_item_free_shipping1" value="0"  readonly="true"/>
								<input type="hidden" id="pg_addorder_item_shipping_tax1" name="pg_addorder_item_shipping_tax1" value="0"  readonly="true"/>
							</span>
						</td>
						<td>
							<span class="new-item-field">
								<input type="text" id="pg_addorder_item_quantity1" name="pg_addorder_item_quantity1" value="" />
								<input type="hidden" name="pg_addorder_item_qty1" id="pg_addorder_item_qty1" value="" >
							</span>
						</td>
						<td>
							<span class="new-item-field">
								<input type="text" id="pg_addorder_item_total_price1" name="pg_addorder_item_total_price1" value="" readonly="true"/>
							</span>
						</td>
					</tr>
				</tbody>
				</table>
				<div class="itemDetails"></div>
				<?php echo PagoHtml::module_bottom() ?>
			</div>

			<div id="tabs-3">
				<?php echo PagoHtml::module_top(JText::_('PAGO_ADDORDER_SHIPPING_METHODS'), false) ?>
				<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
					<tr>
						<td>
						<div id="pg-shipping_options">
						 <?php echo JText::_('COM_PAGO_ADDORDER_SHIPPING_WAIT_MESSAGE'); ?>
						</div>
					</td>
					</tr>
				</table>
				<?php echo PagoHtml::module_bottom() ?>
			</div>
			<div id="tabs-4">
				<?php echo PagoHtml::module_top( JText::_( 'PAGO_ADDORDER_PAYMENT' ), false ) ?>
				<table>
					<tr>
						<td>
						<div id="pg-payment-options">
							<?php
							// Payment options trigger
							$dispatcher = KDispatcher::getInstance();
							JPluginHelper::importPlugin('pago_gateway');
							$payment_options = array();
							$dispatcher->trigger(
								'payment_set_options',
								array( &$payment_options, "" , "" )
							);
							?>

						<div id="pg-payment-methods">
                            <h4 class="pg-title">Payment Methods</h4>
                            <ul class="pg-payment-methods">
                            <?php
								$payCounter      = 0;
								$payCheckedStyle = "";
								$CardStyle = "style = 'display:none'";
                            	foreach( $payment_options as $gateway => $gateway_option ) : 
									if(!$payCounter)
									{
										$plugin = JPluginHelper::getPlugin('pago_gateway', $gateway);
									 	$pluginParams = new JRegistry($plugin->params);
									 	$credit_card = $pluginParams->get('creditcard', '0');
									 	$payCheckedStyle = "checked = checked";
										if($credit_card)
										{
											$CardStyle = "";
										}
									}
                        		?>
                                <li class="pg-payment-method">
                                    <input <?php echo $payCheckedStyle; ?> id="pg-<?php echo $gateway; ?>" type="radio" class="pg-radio" name="payment_option" value="<?php echo $gateway; ?>" onChange="getCreditcardForm(this.value);" />
                                    <label for="pg-<?php echo $gateway; ?>"></label>
                                    <?php echo $gateway_option['name']; ?>
                                </li>
                            <?php
							$payCounter++;
							$payCheckedStyle = "";
                            endforeach; ?>
                            </ul>
                            <div class="pg-checkout-payment-method">
                        	<table class="pg-table">
                        		<tr>
                        			<td>
									<div id="creditCardForm" <?php echo $CardStyle;?> >
										<fieldset class="pg-fieldset">
										<legend class="pg-legend">Credit/debit card payment options</legend>
											<table class="pg-table">
											<tr class="pg-table-content">
												<td colspan="2">
											Note that billing address details are used for Card Verification routines 
											so your billing address should reflect that of the card holder.
												</td>
											</tr>
											<tr class="pg-table-content">
												<td>
													<label for="pg-checkout-cc-number" class="pg-label">Credit/debit card number</label>
												</td>
												<td width="70%">
													<input id="pg-checkout-cc-number" type="text" name="cc_cardNumber" value="" class="pg-inputbox required creditcard" autocomplete="off"/>
													<label for="pg-checkout-cc-number"></label>
												</td>
											</tr>
											<tr class="pg-table-content">
												<td>
													<label for="pg-checkout-cc-expire-month" class="pg--label">Expiry date month</label>
												</td>
												<td width="70%">
													<select id="pg-checkout-cc-expire-month" name="cc_expirationDateMonth" class="pg-selectbox creditcardmonth">
													<?php for ($m = 1; $m <= 12; $m++):
													$s = mktime( 0, 0, 0, 0 + $m, 1, date( "y" ) );
													$selected = false;
													if( str_pad( $m, 2, '0', STR_PAD_LEFT ) == JFactory::getApplication()->input->get( 'sel_Expirydatemonth' ) ){
													    $selected = 'selected="selected"';
													} ?>
													<option <?php echo $selected ?> value="<?php echo date("m", $s) ?>"><?php echo JText::_( date("F", $s) ) ?></option>
													<?php endfor ?>
													</select>
												</td>
											</tr>
											<tr class="pg-table-content">
												<td>
													<label for="pg-checkout-cc-expire-year" class="pg-label">Expiry date year</label>
												</td>
												<td width="70%">
													<select id="pg-checkout-cc-expire-year" name="cc_expirationDateYear" class="pg-selectbox creditcardmonth">
													<?php for ($i = 0; $i <= 10; $i++):
													$year = date( 'Y', strtotime( "now +{$i} years" ) );
													$selected = false;
													if( $year == JFactory::getApplication()->input->get( 'sel_Expirydateyear' ) ){
													    $selected = 'selected="selected"';
													} ?>
													<option <?php echo $selected ?> value="<?php echo $year ?>"><?php echo $year ?></option>                            
													<?php endfor ?>
													</select>
												</td>
											</tr>
											<tr class="pg-table-content">
												<td>
													<label for="pg-checkout-cc-cv2code" class="pg-label">CV2 (3 digit security code on back of card)</label>
												</td>
												<td width="70%">
													<input id="" type="password" name="cc_cv2code" value="" autocomplete="off" maxlength="3" class="pg-inputbox required"/>
													<label for="pg-checkout-cc-cv2code"></label>
												</td>
											</tr>
										</fieldset>
									</div>
									</td>
								</tr>
							</table>
                            </div>
                        </div>
                        </div>
                    	</td>
                	</tr>
            	</table>
				<?php echo PagoHtml::module_bottom() ?>
			</div>
			<div id="tabs-5">
				<?php echo PagoHtml::module_top( JText::_( 'PAGO_COMING_SOON' ), false ) ?>
				<?php //echo $this->images_params ?>
				<?php echo PagoHtml::module_bottom() ?>
			</div>
			<?php endif ; ?>
			<table class="pg-table pg-repeated-rows pg-items-manager">
					<tbody>
					<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="80%">&nbsp;</td>
						<td class="pg-item-name" >
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ADDORDER_ORDER_SUBTOTAL'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_subtotal_div">
								0
							</div>
							<input type="hidden"  name="pg_addorder_order_subtotal" value="0" id="pg_addorder_order_subtotal" >
						</td>
					</tr>
						<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="80%">&nbsp;</td>
						<td class="pg-item-name" >
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ADDORDER_ORDER_TAX'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_tax_div">
								0
							</div>
							<input type="hidden"  name="pg_addorder_order_tax" value="0" id="pg_addorder_order_tax" >
						</td>
					</tr>
					</tr>
						<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="80%">&nbsp;</td>
						<td class="pg-item-name" >
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ADDORDER_ORDER_SHIPPING'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_shipping_div">
								0
							</div>
							<input type="hidden"  name="pg_addorder_order_shipping" value="0" id="pg_addorder_order_shipping" >
							<input type="hidden"  name="pg_addorder_order_shipping_old" value="0" id="pg_addorder_order_shipping_old" >
							<input type="hidden"  name="pg_addorder_order_shipping_name" value="0" id="pg_addorder_order_shipping_name" >
							
						</td>
					</tr>
						<tr class="pg-sub-heading new-item-con">
						<td colspan="4" align="right" width="80%">&nbsp;</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper">
								<?php echo JText::_( 'PAGO_ADDORDER_ORDER_TOTAL'); ?>
							</div>
						</td>
						<td class="pg-item-name">
							<div class="pg-sort-indicator-wrapper" id="pg_addorder_order_total_div">
								0
							</div>
							<input type="hidden"  name="pg_addorder_order_total" value="0" id="pg_addorder_order_total" >
						</td>
					</tr>
					</tbody>
				</table>
		</div>
		<input type="hidden" name="user_id" value="<?php echo $this->user_id; ?>" />
		<input type="hidden" name="rel" value="" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="address_id" value="<?php echo $this->address_id; ?>" />
		<input type="hidden" name="saddress_id" value="<?php echo $this->saddress_id; ?>" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="view" value="addorder" />
		<input type="hidden" name="controller" value="addorder" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>

<script>
jQuery(document).ready(function(){


		jQuery(document).on("keyup","#users-list-add",function(){
			jQuery(this).autocomplete({
				source : $USERS,
		        messages: {
			        noResults: "",
			        results: function() {}
			    },
		    	minLength : 1,
		        select : function(event, ui) {
		        	jQuery('#pg-filter_user').val(ui.item.value);
		        	jQuery('.select_user_popup').css('display', 'none');
		        	getAddressInformation(ui.item.value,0,0);
		        	return false;
		        },
		    });
		});

	jQuery('.open_select_user_popup').click(function() {
		jQuery('.select_user_popup').css('display', 'block');
	});

	 jQuery("#adminForm").validate({
	
		rules: {
				
				"address_billing[company]": "required",
				"address_billing[first_name]": "required",
				"address_billing[last_name]": "required",
				"address_billing[address_1]": "required",
				"address_billing[city]": "required",
				"address_billing[region]": "required",
				"address_billing[country]": "required",
				"address_billing[zip]": "required",
				"address_billing[user_email]": "required email",
				"address_billing[phone_1]": "required",
				"address_mailing[company]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[company]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[first_name]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[last_name]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[address_1]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[city]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[region]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[country]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[zip]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}},
				"address_mailing[user_email]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}},email: true},
				"address_mailing[phone_1]": {
					required: function (){if(jQuery("#address_mailing_same_as_billing").is(":checked")) {return false;}else {  return true;}}}
				
			},
			messages: {
				"address_billing[company]": "Company Required",
				"address_billing[first_name]": "FirstName Required",
				"address_billing[last_name]": "Lastname Required",
				"address_billing[address_1]": "Address1 Required",
				"address_billing[city]": "City Required",
				"address_billing[region]": "Region Required",
				"address_billing[country]": "Country Required",
				"address_billing[zip]": "Zip Required",
				"address_billing[user_email]": "User Email Required",
				"address_billing[phone_1]": "Phone1 Required",
				"address_mailing[company]": "Company Required",
				"address_mailing[first_name]": "FirstName Required",
				"address_mailing[last_name]": "Lastname Required",
				"address_mailing[address_1]": "Address1 Required",
				"address_mailing[city]": "City Required",
				"address_mailing[region]": "Region Required",
				"address_mailing[country]": "Country Required",
				"address_mailing[zip]": "Zip Required",
				"address_mailing[user_email]": "User Email Required",
				"address_mailing[phone_1]": "Phone1 Required"
			}
			
	  });
		//if (jQuery('#creditCardForm').length){jQuery( "#creditCardForm" ).hide(); }
	jQuery.validator.setDefaults({  ignore: ":hidden"});
	Joomla.submitbutton = function(task)
	{
		jQuery.noConflict();
		if( jQuery('#creditCardForm').is(':visible') ) 
		{
			var year= jQuery("#pg-checkout-cc-expire-year").val();
			var month= jQuery("#pg-checkout-cc-expire-month").val();
			var credit= jQuery("#pg-checkout-cc-number").val();
			var cvv= jQuery("#pg-checkout-cc-cv2code").val();
			var minMonth = new Date().getMonth() + 1;
			var minYear = new Date().getFullYear();
			if(credit == '')
			{
				jQuery('#pg-checkout-cc-number').css('border','solid 1px #FF0000');
				return false;
			}
			if(cvv == '')
			{
				jQuery('#pg-checkout-cc-cv2code').css('border','solid 1px #FF0000');
				return false;
			}
			if ((credit!='' && cvv!='') &&((year > minYear) || ((year == minYear) && (month >= minMonth))))
			{
				Joomla.submitform(task);
			}
			else
			{
				
				jQuery('#error').css('display','block');
				return false;
			}
		}
		else
		{
			Joomla.submitform(task);
		}


		
	}
	
		
});
function getCreditcardForm(value)
{
	jQuery.ajax({
        type: "POST",
        url: 'index.php',
		data: 'option=com_pago&view=addorder&task=checkCreditCard&payment=' + value+ '&async=1',
        success: function(response){
              if(response==1)
			{
				jQuery( "#creditCardForm" ).show();
			}
			else
			{
				jQuery( "#creditCardForm" ).hide();
			}
        }
    });

}


</script>
<!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();
