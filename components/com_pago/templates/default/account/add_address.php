<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if( 'add_address' == JFactory::getApplication()->input->get('layout') ) : unset($this->addresses);?>

<?php $this->load_header(); ?>
<?php $attrType=JFactory::getApplication()->input->get('addr_type'); ?>
<div id="pg-account">
	<div id="pg-account-menu" class="pg-account-left">
		<?php echo $this->modules->render_position( 'pago_account_menu' ); ?>
		<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
	</div>

	<div id="pg-account-addresses" class="pg-account-right clearfix">
		
<?php endif; ?>
<div id="pg-account-addresses" class="pg-wrapper-container clearfix">
	<h3>
		<?php 
		if($attrType=='s'){
			echo JText::_('PAGO_ACCOUNT_ADDRESSES_SHIPPING_TITLE');
		}elseif($attrType=='b'){
			echo JText::_('PAGO_ACCOUNT_ADDRESSES_BILLING_TITLE');
		} ?>
	</h3>
	<div class="row pg-form-container">
<div class="col-sm-6 col-sm-offset-3">

		<form id="pg-account-address-form" action="<?php echo JRoute::_('index.php'); ?>" method="POST">
			<?php if($attrType=="s"){ ?>
			<div id="pg-account-address-shipping" class="pg-account-address">
				
				<div>
					
					<?php $this->prefix = 's' ; $this->preset_number = '0'; ?>
					<?php if ( $user_fields = PagoHelper::load_template( 'common', 'tmpl_user_fields' ) ) require $user_fields; ?>
					
					<input type="hidden" name="address[<?php echo $this->prefix; ?>][save]" value="save" />
					<br />

					<!-- <label for="sameasshipping" class="pg-label">
						<strong><?php echo JText::_('PAGO_ACCOUNT_REGISTER_SAME_AS_SHIPPING'); ?></strong>
					</label><input type="radio" value="yes" name="sameasshipping" class="pg-radiobutton"<?php echo ( !isset( $this->addresses['b'] ) ) ? ' checked="checked"' : ''; ?> /><?php echo JText::_('Yes'); ?> <input type="radio" value="no" name="sameasshipping"<?php echo ( isset( $this->addresses['b'] ) ) ? ' checked="checked"' : ''; ?> class="pg-radiobutton" /><?php echo JText::_('No'); ?>
				 -->
				</div>
			</div>
			<?php }elseif($attrType=="b"){ ?>
			
			<div id="pg-account-address-billing" class="pg-account-address">
				
				<div>
					
					<?php $this->prefix = 'b'; $this->preset_number = '0'; ?>
					<?php if ( $user_fields = PagoHelper::load_template( 'common', 'tmpl_user_fields' ) ) require $user_fields; ?>
					
					<input type="hidden" name="address[<?php echo $this->prefix; ?>][save]" value="save" />
				</div>
			</div>
			<?php }?>
			<input type="hidden" name="option" value="com_pago" />
			<input type="hidden" name="view" value="account" />
			<input type="hidden" name="task" value="add_address" /> 
			
			<?php echo JHTML::_( 'form.token' ) ?>

			<button type="submit" class="pg-button pg-green-text-btn add_save_btn pull-left" style="margin-right:10px;"><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_SAVE_ACCOUNT_BUTTON'); ?></button>
			
			<a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account'); ?>" class="pg-button pg-gray-background-btn add_cancel_btn pull-left"><?php echo JText::_('PAGO_CANCEL_BUTTON'); ?></a>

		</form>
	</div>
	</div>
	</div>
<?php if( 'add_address' == JFactory::getApplication()->input->get('layout') ) : ?>
	</div>
</div>
<?php $this->load_footer(); ?>
<?php endif; ?>
