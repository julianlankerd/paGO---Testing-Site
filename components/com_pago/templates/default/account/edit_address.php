<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
$this->document->setTitle( JText::_('PAGO_EDIT_USER_DETAILS') ); ?>
<?php

	$addr_id = JFactory::getApplication()->input->get( 'addr_id' );
	$checkout = JFactory::getApplication()->input->get( 'checkout' );

	foreach( $this->addresses as $address_type => $addresses ) {
		foreach( $addresses as $address ) {
			if ( $address->id == $addr_id ) {
				unset( $this->addresses );
				$this->addresses[0] = $address;
				$this->address_type = $address_type;
			}
		}
	}

?>
<?php $this->load_header(); ?>

<div id="pg-account">
	<div id="pg-account-menu" class="pg-account-left">
		<?php echo $this->modules->render_position( 'pago_account_menu' ); ?>
		<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
	</div>
	<div id="pg-account-addresses" class="pg-wrapper-container clearfix">
		<h3><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_UPDATE_ADDRESS'); ?></h3>
		<div class = "row pg-form-container">
			<div class = "col-sm-6 col-sm-offset-3">
				<form id="pg-account-address-form" action="<?php echo JRoute::_('index.php'); ?>" method="POST">
					<div id="pg-account-address-shipping" class="pg-account-address">
						<div>
							<?php $this->prefix = $this->address_type; $this->preset_number = '0'; ?>
							<?php if ( $user_fields = PagoHelper::load_template( 'common', 'tmpl_user_fields' ) ) require $user_fields; ?>
							<input type="hidden" name="address[<?php echo $this->prefix; ?>][save]" value="save" />
							<input type="hidden" name="checkout" value="<?php echo $checkout; ?>" />
						</div>
					</div>
					<input type="hidden" name="address[<?php echo $this->prefix; ?>][id]" value="<?php echo $addr_id; ?>" />
					<input type="hidden" name="view" value="account" />
					<input type="hidden" name="controller" value="account" />
					<input type="hidden" name="task" value="update_address" />
					<?php echo JHTML::_( 'form.token' ) ?>
					<button type="submit" class="pg-button pg-green-text-btn"><?php echo JText::_('PAGO_UPDATE'); ?></button>
				</form>
				<form action="<?php echo JRoute::_('index.php'); ?>" method="GET">
					<input type="hidden" name="id" value="<?php echo $addr_id; ?>" />
					<input type="hidden" name="option" value="com_pago" />
					<input type="hidden" name="view" value="account" />
					<input type="hidden" name="task" value="delete_address" />
					<input type="hidden" name="checkout" value="<?php echo $checkout; ?>" />
					<?php echo JHTML::_( 'form.token' ) ?>
					<button type="submit" class="pg-button pg-gray-background-btn"><?php echo JText::_('PAGO_DELETE'); ?></button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php $this->load_footer(); ?>