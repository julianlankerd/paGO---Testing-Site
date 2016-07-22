<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');


//START STUFF FOR COUNTRY AND STATE DYNAMIC SELECTION - PROLLY BETTER TO PUT THIS SOMEWHERE ELSE...

$doc = JFactory::getDocument();

$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.chained.mini.js' );


$js = 'jQuery(document).ready(function() {
	jQuery("#'. $this->prefix .'_countystate").chained("#address'. $this->prefix .'country");
	});
';
$doc->addScriptDeclaration( $js );

//build countries dropdown
$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );

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


	if ( !empty( $this->addresses[$this->preset_number]->country ) ){
		$value = $this->addresses[$this->preset_number]->country;
	}

	$selected_country = $value;

	$country_select = JHTML::_(
		'select.genericlist',
		$options, 'address['.$this->prefix . '][country]',
		'required class="pg-dropdown  country" title="' . JText::_('PAGO_COUNTRY_REQUIRED') . '"  data-placeholder="'.JText::_("PAGO_PLEASE_SELECT_COUNTRY").'"',
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
	
	<select data-placeholder="<?php echo JText::_("PAGO_PLEASE_SELECT_STATE"); ?>" class="pg-dropdown countystate" id="<?php echo $this->prefix; ?>_countystate" name="address[<?php echo $this->prefix; ?>][countystate]">
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

<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_company" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_COMPANY_NAME' );?> (optional)</label>
	<input id="<?php echo $this->prefix; ?>_company" name="address[<?php echo $this->prefix; ?>][company]" type="text" value="<?php if ( !empty( $this->addresses[$this->preset_number]->company ) ) { echo $this->addresses[$this->preset_number]->company; } ?>" class="pg-inputbox" />
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_firstname" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_FIRST_NAME' );?> <span class="required">(required)</span></label>
	<input id="<?php echo $this->prefix; ?>_firstname" name="address[<?php echo $this->prefix; ?>][firstname]" type="text" class="pg-inputbox" required value="<?php if ( !empty( $this->addresses[$this->preset_number]->first_name ) ) { echo $this->addresses[$this->preset_number]->first_name; } ?>" placeholder="John" />
	
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_lastname" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_LAST_NAME' );?> <span class="required">(required)</span></label>
	<input id="<?php echo $this->prefix; ?>_lastname" name="address[<?php echo $this->prefix; ?>][lastname]" type="text" class="pg-inputbox " required value="<?php if ( !empty( $this->addresses[$this->preset_number]->last_name ) ) { echo $this->addresses[$this->preset_number]->last_name; } ?>" placeholder="Doe" />
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_address1" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ADDRESS1' );?> <span class="required">(required)</span></label>
	<input id="<?php echo $this->prefix; ?>_address1" name="address[<?php echo $this->prefix; ?>][address1]" type="text" class="pg-inputbox " required value="<?php if ( !empty( $this->addresses[$this->preset_number]->address_1 ) ) { echo $this->addresses[$this->preset_number]->address_1; } ?>" placeholder="123 Apple St." />
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_address2" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ADDRESS_2' );?> (optional)</label>
	<input id="<?php echo $this->prefix; ?>_address2" name="address[<?php echo $this->prefix; ?>][address2]" type="text" value="<?php if ( !empty( $this->addresses[$this->preset_number]->address_2 ) ) { echo $this->addresses[$this->preset_number]->address_2; } ?>" class="pg-inputbox" /><br/>
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_city" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_CITY' );?> <span class="required">(required)</span></label>
	<input id="<?php echo $this->prefix; ?>_city" name="address[<?php echo $this->prefix; ?>][city]" type="text" class="pg-inputbox " required value="<?php if ( !empty( $this->addresses[$this->preset_number]->city ) ) { echo $this->addresses[$this->preset_number]->city; } ?>" placeholder="Plainsville" />
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_country" class="pg-label" ><?php echo JText::_( 'PAGO_SHOPPER_FORM_COUNTRY' );?> <span class="required">(required)</span></label>
	<?php echo $country_select ?>
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_countystate" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_STATE' );?></label>
	<?php echo $state_select ?>
	<?php /*<input id="<?php echo $this->prefix; ?>_countystate" name="address[<?php echo $this->prefix; ?>][countystate]" class="pg-inputbox required" value="<?php if( !empty ( $this->addresses[$this->preset_number]->state ) ) { echo $this->addresses[$this->preset_number]->state; } ?>" placeholder="Michigan" /> */ ?>
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_postcodezip" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_ZIP' );?> <span class="required">(required)</span></label>
	<input id="<?php echo $this->prefix; ?>_postcodezip" name="address[<?php echo $this->prefix; ?>][postcodezip]" type="text" class="pg-inputbox " required value="<?php if ( !empty( $this->addresses[$this->preset_number]->zip ) ) { echo $this->addresses[$this->preset_number]->zip; } ?>" placeholder="49001" />
</div>
<div class="pg_address_fields">
	<label for="<?php echo $this->prefix; ?>_telephoneno" class="pg-label"><?php echo JText::_( 'PAGO_SHOPPER_FORM_PHONE' );?> <span class="required">(required)</span></label>
	<input id="<?php echo $this->prefix; ?>_telephoneno" name="address[<?php echo $this->prefix; ?>][telephoneno]" type="text" class="pg-inputbox  " required value="<?php if ( !empty( $this->addresses[$this->preset_number]->phone_1 ) ) { echo $this->addresses[$this->preset_number]->phone_1; } ?>" placeholder="(555) 555-5555" />
</div>
<div class="pg_address_fields">
	<label class = "pg-label"><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_INFO_CONTACT_TITLE'); ?></label>
	<p><?php echo JText::_('PAGO_CHECKOUT_SHIPPING_INFO_CONTACT_DESC'); ?></p>
</div>
<div class="pg_address_fields">
	<label for="pg-email" class="pg-label">Email Address <span>(required)</span></label>
	<input id="pg-email" name="address[<?php echo $this->prefix; ?>][email]" type="text" class="pg-inputbox " required value="<?php if ( !empty( $this->addresses[$this->preset_number]->user_email ) ) { echo $this->addresses[$this->preset_number]->user_email; } ?>" />
</div>