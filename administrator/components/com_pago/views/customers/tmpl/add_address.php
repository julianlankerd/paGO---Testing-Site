<?php defined('_JEXEC') or die('Restricted access');
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
$user_address = JFactory::getApplication()->input->get('user_address', array(0), 'array');

$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );

$doc = JFactory::getDocument();

$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.chained.mini.js' );


$js = 'jQuery(document).ready(function() {
	jQuery("#countystate").chained("#addresscountry");
	});
';
//$doc->addScriptDeclaration( $js );

	$countries = $user_fields_model->get_countries();

	$options = array();



$options[0] = array(
	'id' => '',
	'name' => JText::_("PAGO_PLEASE_SELECT_COUNTRY"),
);

foreach($countries as $k=>$v){
	$options[] = array(
		'id' => $v,
		'name' => $k
	);
}

	$value = 0;


	if ( !empty( $user_address[0]['country'] )){
		$value = $user_address[0]['country'];
	}
	
	$selected_country = $value;

	$country_select = JHTML::_(
		'select.genericlist',
		$options, 'address[country]',
		'class="pg-dropdown required country" title="' . JText::_('PAGO_COUNTRY_REQUIRED') . '"  data-placeholder="'.JText::_("PAGO_PLEASE_SELECT_COUNTRY").'"',
		'id',
		'name',
		$value,
		'address[country]'
	);
	

	$states = $user_fields_model->get_countries_states();


	$value = false;

	if ( !empty( $user_address[0]['state'] ) ){
		$value = $user_address[0]['state'];
	}
	array_unshift($states['attribs'],'');
	ob_start();?>
	
	<select data-placeholder="<?php echo JText::_("PAGO_PLEASE_SELECT_STATE"); ?>" class="pg-dropdown countystate" id="countystate" name="address[state]">
	<?php foreach( $states['attribs'] as $state => $class ):
		if( $state == $value ){
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		} ?>
		<?php
			$style="";
			if($selected_country===0 || $class=="class=\"".$selected_country."\"") $style="style=\"display:none;\"";
		?>
		<?php if($state===0){ ?>
			<option <?php echo $selected ?> value="" <?php echo $class ?>></option>
		<?php }else{ ?>
			<option  <?php echo $selected ?> value="<?php echo $state ?>" <?php echo $class ?>><?php echo $state ?></option>
		<?php } ?>
	<?php endforeach ?>
	</select>
	<?php
	 $state_select = ob_get_clean();

	//END STUFF FOR COUNTRY AND STATE DYNAMIC SELECTION
?>
<script src="<?php echo JURI::root(true); ?>/components/com_pago/javascript/jquery.chained.mini.js"></script>
<div id="pg-account-addresses" class="pg-wrapper-container pg-account-addresses clearfix">
		<h3></h3>
		<div class="marg_padd"></div>
		<div>
			<div>
				<form id="pg-account-address-form" action="<?php echo JRoute::_('index.php'); ?>" method="POST">
					<div id="pg-account-address-shipping" class="pg-account-address" style="margin-left: 20px;">	
						<div id="pg-system-messages"></div>
						<div class="add_address_div">
							<div>
								<label for="company" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_COMPANY_NAME' );?> (optional)</label>
							</div>
							<input id="company" name="address[company]" type="text" value="<?php if($user_address[0]['company']) echo $user_address[0]['company'] ?>" class="pg-inputbox" />
						</div>
						<div class="add_address_div">
							<div>
								<label for="firstname" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_FIRST_NAME' );?> <span class="required">(required)</span></label>
							</div>
							<input id="firstname" name="address[firstname]" type="text" class="pg-inputbox required" value="<?php if($user_address[0]['first_name']) echo $user_address[0]['first_name'] ?>" placeholder="John" />
						</div>
						<div class="add_address_div">
							<div>
								<label for="lastname" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_LAST_NAME' );?> <span class="required">(required)</span></label>
							</div>
							<input id="lastname" name="address[lastname]" type="text" class="pg-inputbox required" value="<?php if($user_address[0]['last_name']) echo $user_address[0]['last_name'] ?>" placeholder="Doe" />
						</div>
						<div class="add_address_div">
							<div>
								<label for="address1" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ADDRESS_1' );?> <span class="required">(required)</span></label>
							</div>
							<input id="address1" name="address[address1]" type="text" class="pg-inputbox required" value="<?php if($user_address[0]['address_1']) echo $user_address[0]['address_1'] ?>" placeholder="123 Apple St." />
						</div>
						<div class="add_address_div">
							<div>
								<label for="address2" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ADDRESS_2' );?> (optional)</label>
							</div>
							<input id="address2" name="address[address2]" type="text" value="<?php if($user_address[0]['address_2']) echo $user_address[0]['address_2'] ?>" class="pg-inputbox" /><br/>
						</div>
						<div class="add_address_div">
							<div>
								<label for="city" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_CITY' );?> <span class="required">(required)</span></label>
							</div>
							<input id="city" name="address[city]" type="text" class="pg-inputbox required" value="<?php if($user_address[0]['city']) echo $user_address[0]['city'] ?>" placeholder="Plainsville" />
						</div>
						<div class="add_address_div">
							<div>
								<label for="country" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_COUNTRY' );?> <span class="required">(required)</span></label>
							</div>
							<?php echo $country_select ?>
						</div>
						<div class="add_address_div">
							<div>
								<label for="countystate" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_STATE' );?></label>
							</div>
							<?php echo $state_select ?>
						</div>
					    <div class="add_address_div">
							<div>
					    		<label for="postcodezip" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ZIP' );?> <span class="required">(required)</span></label>
							</div>
							<input id="postcodezip" name="address[postcodezip]" type="text" class="pg-inputbox required" value="<?php if($user_address[0]['zip']) echo $user_address[0]['zip'] ?>" placeholder="49001" />
						</div>
						<div class="add_address_div">
							<div>
								<label for="telephoneno" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_PHONE' );?> <span class="required">(required)</span></label>
							</div>
							<input id="telephoneno" name="address[telephoneno]" type="text" class="pg-inputbox required " value="<?php if($user_address[0]['phone_1']) echo $user_address[0]['phone_1'] ?>" placeholder="(555) 555-5555" />
					    </div>
					    <div class="add_address_div">
							<div>
					    		<label class = "pg-label"><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_INFO_CONTACT_TITLE'); ?></label>
					    	</div>
					    	<p style="width: 90%"><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_INFO_CONTACT_DESC'); ?></p>
					    </div>
					    <div class="add_address_div">
							<div>
					    		<label for="pg-email" class="pg-label">Email Address <span>(required)</span></label>
							</div>
							<input id="pg-email" name="address[email]" type="text" class="pg-inputbox required" value="<?php if($user_address[0]['user_email']) echo $user_address[0]['user_email'] ?>" />
						</div>
						<br />
						<input type="hidden" name="address[user_id]" value="<?php if($user_address[0]['user_id']) echo $user_address[0]['user_id'] ?>" class="user_id"/>
						<input type="hidden" name="address[addr_type]" value="<?php if($user_address[0]['address_type']) echo $user_address[0]['address_type'] ?>" class="addr_type"/>
						<?php if($user_address[0]['id']){?> <input type="hidden" name="address[id]" value="<?php echo $user_address[0]['id'] ?>"/>  <?php } ?>
						<input type="hidden" name="option" value="com_pago" />
						<input type="hidden" name="view" value="customers" />
						<input type="hidden" name="task" value="storeAddress" /> 
					
						<?php echo JHTML::_( 'form.token' ) ?>
						<div class="add_address_button">
							<button type="button" class="pg-button pg-green-text-btn add_save_btn pull-left" style="margin-right:10px;"><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_SAVE_ACCOUNT_BUTTON'); ?></button>
							<button type="button" class="pg-button pg-gray-background-btn add_cancel_btn pull-left"><?php echo JText::_('PAGO_CANCEL_BUTTON'); ?></a></button>
						</div>
					</div>
				</form>
			</div>
		</div>
</div>
<script>
jQuery(document).ready(function() {
	jQuery('.add_save_btn').click(function() {
		var form = jQuery(".pg-account-addresses form");
		var data = new Object();
		var error = '';
		var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		form.find('input').each(function(){
			data[jQuery(this).attr('name')] = jQuery(this).val();
		})
		form.find('select').each(function(){
			data[jQuery(this).attr('name')] = jQuery(this).val();
		})
		data['tmpl'] = 'component';
		data['view'] = 'customers';
		data['task'] = 'storeAddress';
		
		if(!data['address[email]']) {
			error += '<div>Email is required</div>';
			jQuery('#pg-email').css('border', '1px solid red');
		}
		else{
			if (!filter.test(data['address[email]'])){
				error +=  '<div>Email is wrong</div>';
				jQuery('#pg-email').css('border', '1px solid red');
			} 
		}

		if(!data['address[firstname]']){
			error += '<div>First name is required</div>';
			jQuery('#firstname').css('border', '1px solid red');
		} 

		if(!data['address[lastname]']){
			error += '<div>Last name is required</div>';
			jQuery('#lastname').css('border', '1px solid red');
		} 

		if(!data['address[address1]']){
			error += '<div>Address is required</div>';
			jQuery('#address1').css('border', '1px solid red');
		} 

		if(!data['address[city]']){
			error += '<div>City is required</div>';
			jQuery('#city').css('border', '1px solid red');
		} 

		if(data['address[country]'] == '0'){
			error += '<div>Country is required</div>';
			jQuery('#addresscountry').css('border', '1px solid red');
		} 

		if(!data['address[postcodezip]']){
			error += '<div>Zip is required</div>';
			jQuery('#postcodezip').css('border', '1px solid red');
		} 	

		if(!data['address[telephoneno]']){
			error += '<div>Phone is required</div>';
			jQuery('#telephoneno').css('border', '1px solid red');
		} 

		if(error != '')
		{
			alert("You have error in form !!");
			return false;
		}

		jQuery.ajax({
    		type: "POST",
    		url: 'index.php',
			data: data,
    		success: function(response){
     			if (response){
    				jQuery.ajax({
			    		type: "POST",
			    		url: 'index.php',
						data: 'option=com_pago&view=customers&task=getCustomerAccount&userId='+response+'&async=1',
			    		success: function(response){
			    			jQuery('#pg-account').html(response);
			    		}
					});
    			}
    		}
		});
	});

	jQuery('.add_cancel_btn').click(function() {
		jQuery.ajax({
    		type: "POST",
    		url: 'index.php',
			data: 'option=com_pago&view=customers&task=getCustomerAccount&userId='+jQuery('.user_id').val()+'&async=1',
    		success: function(response){
    			jQuery('#pg-account').html(response);
    		}
		});
	});

	jQuery("#countystate").chained("#addresscountry");
	jQuery("#countystate, #addresscountry").chosen({"disable_search": true,  "disable_search_threshold": 6});
	var PAGO_PLEASE_SELECT_STATE='<?php echo JText::_("PAGO_PLEASE_SELECT_STATE"); ?>';

	jQuery("#countystate option:first").text(PAGO_PLEASE_SELECT_STATE);
	jQuery("#countystate").trigger("chosen:updated");

	jQuery('#addresscountry').on('change', function() {
		jQuery('#countystate').parent().addClass("disabled");
		jQuery("#countystate option:first").text(PAGO_PLEASE_SELECT_STATE);
		jQuery("#countystate").trigger("chosen:updated");
		if(jQuery('#countystate').is('[disabled=disabled]')){
		}else{
			jQuery('#countystate').parent().removeClass("disabled");
		}
		return false;
	});

});
</script>