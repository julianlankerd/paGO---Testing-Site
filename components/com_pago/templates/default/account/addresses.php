<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
$this->document->setTitle( 'Addresses' ); ?>

<?php $this->load_header(); ?>

<div id="pg-account">
	<div id="pg-account-menu" class="pg-account-left">
		<?php echo $this->modules->render_position( 'pago_account_menu' ); ?>
		<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
	</div>

	<div id="pg-account-addresses" class="pg-account-right clearfix">

		<?php if( !$this->user_info && !$this->addresses ) : ?>
			<h2><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_UPDATE_ACCOUNT_TITLE'); ?></h2>
			<p><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_UPDATE_ACCOUNT_DESC'); ?></p>
			<?php if ( $addressTemplate = PagoHelper::load_template( 'account', 'add_address' ) ) require $addressTemplate; ?>

		<?php else : ?>
			<?php foreach( $this->addresses as $type => $addresses ) :
				switch( $type ) {
					case 'b':
						$html_id = 'billing';
						$lang = 'BILLING';
						break;

					default: case 'm':
						$html_id = 'shipping';
						$lang = 'SHIPPING';
						break;
				}
			?>
			<h2><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES'); ?></h2>
			<div id="pg-account-addresses-<?php echo $html_id; ?>" class="pg-account-addresses">
				<h3><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_' . $lang . '_ADDRESSES'); ?></h3>
				<div>
					<?php foreach( $addresses as $user_address ) : ?>
					<div class="pg-account-<?php echo $html_id; ?>-address">
						<fieldset class="pg-fieldset pg-<?php echo $html_id; ?>-address-fieldset">
							<legend class="pg-legend">
								<a href="<?php echo JRoute::_('index.php?option=com_pago&view=account&layout=edit_address&addr_id=' . (int) $user_address->id) ; ?>" class="pg-edit-link">
									<?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_EDIT_ADDRESS'); ?>
								</a>
								&nbsp;&nbsp;
								<a href ="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&controller=account&task=delete_address&' . JSession::getFormToken() . '=1&id=' . (int) $user_address->id ); ?>" class="pg-delete-link">
									<?php echo JText::_( 'PAGO_ACCOUNT_ADDRESSES_DELETE_ADDRESS' ); ?>
								</a>
							</legend>
							<ul class="pg-<?php echo $html_id; ?>-address">
								<li class="pg-<?php echo $html_id; ?>-address-name">
									<span class="pg-<?php echo $html_id; ?>-address-first-name"><?php echo $user_address->first_name; ?></span> <span class="pg-<?php echo $html_id; ?>-address-last-name"><?php echo $user_address->last_name; ?></span>
								</li>
								<li class="pg-<?php echo $html_id; ?>-address-street">
									<?php echo $user_address->address_1; ?>
								</li>
								<?php if( !empty( $user_address->address_2 ) ) : ?>
									<li class="pg-<?php echo $html_id; ?>-address-street">
										<?php echo $user_address->address_2; ?>
									</li>
								<?php endif; ?>
								<li>
									<span class="pg-<?php echo $html_id; ?>-address-city"><?php echo $user_address->city; ?></span>, <span class="pg-<?php echo $html_id; ?>-address-state"><?php echo $user_address->state;?></span> <span class="pg-<?php echo $html_id; ?>-address-zip"><?php echo $user_address->zip; ?></span>
								</li>
								<li class="pg-<?php echo $html_id; ?>-address-country">
									<?php echo $user_address->country; ?>
								</li>
								<li class="pg-<?php echo $html_id; ?>-address-phone">
									<?php echo $user_address->phone_1; ?>
								</li>
							</ul>
						</fieldset>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
			<a href="<?php echo JRoute::_('index.php?option=com_pago&view=account&layout=add_address'); ?>" class="pg-button">+ <?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_ADD_ADDRESS'); ?></a>
		<?php endif; ?>
	</div>
</div>
<?php $this->load_footer();