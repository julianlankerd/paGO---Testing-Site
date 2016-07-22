<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

// This is the first file of the checkout process
$j_user = JFactory::getUser();
$view = JFactory::getApplication()->input->get('view');


$this->document->addScriptDeclaration( '
	function showMe (it, box) {
	  var vis = (box.checked) ? "none" : "block";
	  document.getElementById(it).style.display = vis;
	}
' );

( $j_user->id ) ? $logged_in = 1 : $logged_in = 0;
$this->preset_number = 0;
$this->load_header();
?>
<div id="pg-checkout" class="pg-step1">

	<h1 class="pg-title">Checkout</h1>
	<?php if ( !$logged_in ) : ?>
    	<div id="pg-checkout-account-action" class="clearfix">
        	<?php template_functions::load_template('common', 'tmpl', 'login_register'); ?>
            <div id="pg-account-guest">
            	<h2><?php echo JText::_('PAGO_CHECKOUT_GUEST_CONTINUE'); ?></h2>
                <div>
                	<p><?php echo JText::_('PAGO_CHECKOUT_GUEST_CONTINUE_DESC'); ?></p>
                    <a href="<?php JRoute::_('index.php?option=com_pago&view=checkout'); ?>" class="pg-button"><?php echo JText::_('PAGO_CHECKOUT_GUEST_BUTTON'); ?></a>
               	</div>
            </div>
        </div>
    <?php endif; ?>
	<form action="<?php echo JRoute::_('index.php') ?>" method="post" id="checkout" class="pg-form">

		<?php
		if ( $logged_in ) {
			echo '<p>You are currently logged in as <strong>' . $this->user->username . '</strong>. You may <a href="' . JRoute::_('index.php?option=com_pago&view=account&layout=account_settings') . '">edit your account here</a>.';

		} else {
		// If not logged in then display the new and existing customer fields.
		?>
			<div class="outer column-2of2">
				<div class="inner-column">
					<fieldset class="checkout-create_login">
						<legend class="h3">New Customers</legend>
						<p>To register enter an account password and an email address to send the invoice too. If you register you can use the account area to view you account details. You are not required to register however to complete your order.</p>
						<p>To register just enter your information below and your account will be automatically created.</p>
						<div class="row required pg-text">
							<label for="username" id="username-label">Username <span class="required">(required)</span></label>
							<input id="username" name="username" type="text" class="required" title="Username. This is a required field" />
						</div>
						<div class="row required pg-text clear">
							<label for="email" id="EmailAddress">Email Address <span class="required">(required)</span></label>
							<input id="email" name="email" type="text" class="required" title="Email Address. This is a required field" />
						</div>
						<div class="row pg-password width-50">
							<label for="Password" id="Password">Password <span class="required">(required)</span></label>
							<input id="password" name="password" type="password" />
						</div>
						<div class="row pg-password width-50">
							<label for="confirm_password" id="Confirmpassword">Confirm password <span class="required">(required)</span></label>
							<input id="confirm_password" name="confirm_password" type="password" />
						</div>
					</fieldset><!-- end Create a login -->
				</div>
			</div>

		<?php } ?><!-- end if not logged in //-->

		<h2 class="pg-title clear">Billing and Delivery Details</h2>

		<div class="outer column-1of2 row-2columms">
			<div class="inner-column">
				<fieldset class="checkout-billing_address">
					<legend class="h3">Billing Address</legend>
					<div class="outer clear">
						<?php if ( $logged_in ) { ?>
							<div class="row pg-selectlist clear">
								<label for="sel_BillingAddressPreset" id="BillingAddressPreset">Choose from your saved addresses</label>
								<select id="sel_BillingAddressPreset" name="sel_BillingAddressPreset">
									<?php
									$i = 0;
									// Need to change $preset_number to chosen $i value to change form.
									foreach( $this->addresses as $address ) {
										echo '<option value="' . $i . '">' . $address->address_type_name . '</option>';
										$i++;
									} ?>
								</select>
							</div>
							<div class="row pg-text">
								<p><a href="#">create a new address</a>. How will we save a preset?</p>
							</div>
						<?php } ?>
					</div>
					<?php
					$this->prefix = 'b';
					template_functions::load_template( 'common', 'tmpl', 'user_fields' );
					?>
					<div class="row pg-checkbox clear">
						<input id="sameasbillingaddress" name="sameasbillingaddress" type="checkbox" checked="checked" title="Delivery address" />
						<span><label for="sameasbillingaddress" id="sameasbillingaddress">Shipping same as billing?</label></span>
					</div>
				</fieldset><!-- end Billing Address -->
			</div>
	</div>
		<div class="outer column-2of2 row-2columms">
			<div class="inner-column">
				<fieldset class="checkout-delivery_address">
					<legend class="h3">Delivery Address</legend>

					<br class="clear" />

					<?php if ( $logged_in ) { ?>
					<div class="row pg-selectlist clear">
						<label for="sel_DeliveryAddressPreset" id="DeliveryAddressPreset">Choose from your saved addresses</label>
						<select id="sel_DeliveryAddressPreset" name="sel_DeliveryAddressPreset">
							<?php
							$i = 0;
							// Need to change $preset_number to chosen $i value to change form.
							foreach ( $this->addresses as $address ) {
								echo '<option value="' . $i . '">' . $address->address_type_name . '</option>';
								$i++;
							} ?>
						</select>
					</div>
					<?php } ?>

					<br class="clear" />
					<?php
					$this->prefix = 'm';
					template_functions::load_template( 'common', 'tmpl', 'user_fields' );
					?>
				</fieldset><!-- end Delivery Address -->
			</div>
		</div>

		<div class="pg-submit">
			<span class="pg-button"><input type="submit" value="<?php echo JText::_( 'PAGO_CANCEL_CONTINUE_SHOPPING' ) ?>" /></span>
			<span class="pg-button"><input type="submit" value="Continue to Review Order" /></span>
		</div>
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="view" value="checkout" />
		<input type="hidden" name="step" value="shipping" />
		<?php echo JHTML::_( 'form.token' ) ?>
	</form>

</div><!-- end .pg-checkout -->
<?php echo $this->load_footer() ?>
