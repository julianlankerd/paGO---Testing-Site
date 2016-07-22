<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

	//START STUFF FOR COUNTRY AND STATE DYNAMIC SELECTION - PROLLY BETTER TO PUT THIS SOMEWHERE ELSE...
	$guest = JFactory::getApplication()->input->getInt('guest');
	if($guest == "")
	{
		$guest = @$this->guest;
	}

	$doc = JFactory::getDocument();
	if($this->prefix == 's'){
		$js = 'jQuery(document).ready(function() {
				jQuery("#s_countystate").chained("#addressscountry");
			});
		';
	}
	else{
		$js = 'jQuery(document).ready(function() {
				jQuery("#b_countystate").chained("#addressbcountry");
			});
		';
	}
	
	// creates issue with country drop down */
	$doc->addScriptDeclaration( $js );

	//build countries dropdown
	$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );

	$countries = $user_fields_model->get_countries();

	$options = array();

	foreach($countries as $k=>$v){
		$options[] = array(
			'id' => $v,
			'name' => $k
		);
	}

	$value = 0;


	if ( !empty( $this->addresses[$this->preset_number]->country ) ){
		$value = $this->addresses[$this->preset_number]->country;
	}
	$options[0] = '';
	$selected_country = $value;

	$country_select = JHTML::_(
		'select.genericlist',
		$options, 'address['.$this->prefix . '][country]',
		'class="pg-dropdown required country" title="' . JText::_('PAGO_COUNTRY_REQUIRED') . '"  data-placeholder="'.JText::_("PAGO_PLEASE_SELECT_COUNTRY").'"',
		'id',
		'name',
		$value,
		'address['.$this->prefix . '][country]'
	);
	//get states - have to build it this way because joomla html
	//select.options does not allow for adding attributes to option
	//tag and we need this for the jquery chained selection

	$states = $user_fields_model->get_countries_states();


	$value = false;

	if ( !empty( $this->addresses[$this->preset_number]->state ) ){
		$value = $this->addresses[$this->preset_number]->state;
	}
	array_unshift($states['attribs'],'');
	ob_start();?>
	
	<select disabled="disabled" data-placeholder="<?php echo JText::_("PAGO_PLEASE_SELECT_STATE"); ?>" class="pg-dropdown countystate" id="<?php echo $this->prefix; ?>_countystate" name="address[<?php echo $this->prefix; ?>][countystate]">
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
			<option <?php echo $style; ?> <?php echo $selected ?> value="" <?php echo $class ?>></option>
		<?php }else{ ?>
			<option <?php echo $style; ?> <?php echo $selected ?> value="<?php echo $state ?>" <?php echo $class ?>><?php echo $state ?></option>
		<?php } ?>
	<?php endforeach ?>
	</select>
	<?php
	 $state_select = ob_get_clean();

	//END STUFF FOR COUNTRY AND STATE DYNAMIC SELECTION

?>

<form type_of_address="<?php echo $this->prefix; ?>" class="pg-add-address-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="add_ship_form">
	<div class="pg-checkout-shipping-address-fields">
		<label for="<?php echo $this->prefix; ?>_company" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_COMPANY_NAME' );?> (optional)</label>
		<input id="<?php echo $this->prefix; ?>_company" name="address[<?php echo $this->prefix; ?>][company]" type="text" value="<?php if ( !empty( $this->addresses[$this->preset_number]->company ) ) { echo $this->addresses[$this->preset_number]->company; } ?>" class="pg-inputbox" />
		<label for="<?php echo $this->prefix; ?>_firstname" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_FIRST_NAME' );?> <span class="required">(required)</span></label>
		<input id="<?php echo $this->prefix; ?>_firstname" name="address[<?php echo $this->prefix; ?>][firstname]" type="text" class="pg-inputbox required" value="<?php if ( !empty( $this->addresses[$this->preset_number]->first_name ) ) { echo $this->addresses[$this->preset_number]->first_name; } ?>" placeholder="John" />
		<label for="<?php echo $this->prefix; ?>_lastname" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_LAST_NAME' );?> <span class="required">(required)</span></label>
		<input id="<?php echo $this->prefix; ?>_lastname" name="address[<?php echo $this->prefix; ?>][lastname]" type="text" class="pg-inputbox required" value="<?php if ( !empty( $this->addresses[$this->preset_number]->last_name ) ) { echo $this->addresses[$this->preset_number]->last_name; } ?>" placeholder="Doe" />
		<label for="<?php echo $this->prefix; ?>_address1" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ADDRESS_1' );?> <span class="required">(required)</span></label>
		<input id="<?php echo $this->prefix; ?>_address1" name="address[<?php echo $this->prefix; ?>][address1]" type="text" class="pg-inputbox required" value="<?php if ( !empty( $this->addresses[$this->preset_number]->address_1 ) ) { echo $this->addresses[$this->preset_number]->address_1; } ?>" placeholder="123 Apple St." />
		<label for="<?php echo $this->prefix; ?>_address2" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ADDRESS_2' );?> (optional)</label>
		<input id="<?php echo $this->prefix; ?>_address2" name="address[<?php echo $this->prefix; ?>][address2]" type="text" value="<?php if ( !empty( $this->addresses[$this->preset_number]->address_2 ) ) { echo $this->addresses[$this->preset_number]->address_2; } ?>" class="pg-inputbox" /><br/>
		<label for="<?php echo $this->prefix; ?>_city" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_CITY' );?> <span class="required">(required)</span></label>
		<input id="<?php echo $this->prefix; ?>_city" name="address[<?php echo $this->prefix; ?>][city]" type="text" class="pg-inputbox required" value="<?php if ( !empty( $this->addresses[$this->preset_number]->city ) ) { echo $this->addresses[$this->preset_number]->city; } ?>" placeholder="Plainsville" />
		<label for="<?php echo $this->prefix; ?>_country" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_COUNTRY' );?> <span class="required">(required)</span></label>
		<?php echo $country_select ?>
		<div class="shiping_states">
			<label for="<?php echo $this->prefix; ?>_countystate" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_STATE' );?></label>
			<?php echo $state_select ?>
		</div>
		<?php /*<input id="<?php echo $this->prefix; ?>_countystate" name="address[<?php echo $this->prefix; ?>][countystate]" class="pg-inputbox required" value="<?php if( !empty ( $this->addresses[$this->preset_number]->state ) ) { echo $this->addresses[$this->preset_number]->state; } ?>" placeholder="Michigan" /> */ ?>
		<label for="<?php echo $this->prefix; ?>_postcodezip" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ZIP' );?> <span class="required">(required)</span></label>
		<input id="<?php echo $this->prefix; ?>_postcodezip" name="address[<?php echo $this->prefix; ?>][postcodezip]" type="text" class="pg-inputbox required" value="<?php if ( !empty( $this->addresses[$this->preset_number]->zip ) ) { echo $this->addresses[$this->preset_number]->zip; } ?>" placeholder="49001" />
		<label for="<?php echo $this->prefix; ?>_telephoneno" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_PHONE' );?> <span class="required">(required)</span></label>
		<input id="<?php echo $this->prefix; ?>_telephoneno" name="address[<?php echo $this->prefix; ?>][telephoneno]" type="text" class="pg-inputbox required phoneUS" value="<?php if ( !empty( $this->addresses[$this->preset_number]->phone_1 ) ) { echo $this->addresses[$this->preset_number]->phone_1; } ?>" placeholder="(555) 555-5555" />
		<h4><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_INFO_CONTACT_TITLE'); ?></h4>
		<p><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_INFO_CONTACT_DESC'); ?></p>
		<label for="pg-email" class="pg-label">Email Address <span>(required)</span></label>
		<input id="pg-email" name="address[<?php echo $this->prefix; ?>][email]" type="text" class="pg-inputbox required" value="<?php if ( !empty( $this->addresses[$this->preset_number]->user_email ) ) { echo $this->addresses[$this->preset_number]->user_email; } ?>" />
    	<?php if($this->prefix == 's'){ ?>
    		<input type="hidden" name="same_as_shipping_hidden" id="same_as_shipping_hidden" value="1" >
			<input type="checkbox" class="pg-checkbox sameasshipping" id="same-shiping-address" name="sameasshipping" value="yes" checked="checked" />
	    	<label for="same-shiping-address" class="pg-label">
	    		<?php echo JText::_('PAGO_ACCOUNT_REGISTER_SAME_AS_SHIPPING'); ?>
	    	</label>
    	<?php } ?>
    	<?php if($this->prefix != 's'){ ?>
    	<input checked="checked" style="display:none;" type="radio" name="address[b][id]" value="0" class="pg-radiobutton required" />
    	<?php } ?>
		<!-- <input type="hidden" name="option" value="com_pago" /> -->
	    <!-- <input type="hidden" name="view" value="checkout" /> -->
	    <!-- <input type="hidden" name="task" value="set_address" /> -->
	    <input type="hidden" name="guest" value="<?php echo $guest ?>">
	    <?php echo JHTML::_( 'form.token' ) ?>
	</div>
</form>
<script>
jQuery(document).ready(function(){
	jQuery("#addressscountry").change(function(){
		jQuery("#s_countystate").prop("selectedIndex",0);
	});
	jQuery("#addressbcountry").change(function(){
		jQuery("#b_countystate").prop("selectedIndex",0);
	});
	jQuery("#pago select").chosen({disable_search_threshold: 10});
	//jQuery("#b_countystate").chained("#addressbcountry");
});
</script>