<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 

$this->document->setTitle( 'Order Status' ); 
$this->pathway->addItem( 'Order Status' , JRoute::_( '&view=account' ) );
$ordId = JFactory::getApplication()->input->get('status_search');
 ?>

<?php $this->load_header(); ?>

<div id="pg-account">
	<div id="pg-account-menu" class="pg-account-left">
		<?php echo $this->modules->render_position( 'pago_account_menu' ); ?>
		<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
	</div>

	<div id="pg-account-order" class="pg-account-right clearfix">

		<h2><?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_TITLE'); ?></h2>
		<p><?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_DESC'); ?></p>

		<div id="pg-account-orders-status">
			<div id="pg-account-order-status-search">
				<form action="" method="POST">
					<label for="pg-order-status-searchbox" class="pg-label"><?php echo JText::_('PAGO_ACCOUNT_ORDER_NUMBER'); ?>:</label>
					<input id="pg-order-status-searchbox" type="text" name="status_search" class="pg-inputbox pg-searchbox" onfocus="if (this.value=='Search...') this.value='';" onblur="if (this.value=='') this.value='Search...';" value="<?php if($ordId) {echo $ordId;} else {echo "Search...";} ?>" />
					<!--<button class="pg-searchbutton"><?php echo JText::_('PAGO_ACCOUNT_ORDER_SEARCH_BUTTON'); ?></button>-->
				</form>
			</div>
			<div id="pg-account-orders-recent" class = "pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_ACCOUNT_ORDER_RECENT_ORDERS_TITLE'); ?></h3>
				<div>
					<?php if ( $recent_table = PagoHelper::load_template( 'account', 'order_recent_table' ) ) require $recent_table; ?>
				</div>
			</div>
			<div id="pg-account-order-status-descs" class = "pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_DESCRIPTIONS_TITLE'); ?></h3>
				<table>
					<tr>
						<td>
							<span class = "fa fa-refresh"></span>
						</td>
						<td>
							<span class = "pg-account-order-status-title"><?php echo JText::_('PAGO_ORDER_STATUS_PENDING'); ?></span>
						</td>
						<td>
							<span class = "pg-account-order-status-desc">
								<?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_PENDING_DESC'); ?>								
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class = "fa fa-star"></span>
						</td>
						<td>
							<span class = "pg-account-order-status-title"><?php echo JText::_('PAGO_ORDER_STATUS_CONFIRMED'); ?></span>
						</td>
						<td>
							<span class = "pg-account-order-status-desc">
								<?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_CONFIRMED_DESC'); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class = "fa fa-truck"></span>
						</td>
						<td>
							<span class = "pg-account-order-status-title"><?php echo JText::_('PAGO_ORDER_STATUS_SHIPPED'); ?></span>
						</td>
						<td>
							<span class = "pg-account-order-status-desc">
								<?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_SHIPPED_DESC'); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class = "fa fa-star-o"></span>
						</td>
						<td>
							<span class = "pg-account-order-status-title"><?php echo JText::_('PAGO_ORDER_STATUS_COMPLETED'); ?></span>
						</td>
						<td>
							<span class = "pg-account-order-status-desc">
								<?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_COMPLETED_DESC'); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class = "fa fa-ban"></span>
						</td>
						<td>
							<span class = "pg-account-order-status-title"><?php echo JText::_('PAGO_ORDER_STATUS_CANCELLED'); ?></span>
						</td>
						<td>
							<span class = "pg-account-order-status-desc">
								<?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_CANCELLED_DESC'); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class = "fa fa-rotate-left"></span>
						</td>
						<td>
							<span class = "pg-account-order-status-title"><?php echo JText::_('PAGO_ORDER_STATUS_REFUNDED'); ?></span>
						</td>
						<td>
							<span class = "pg-account-order-status-desc">
								<?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_REFUNDED_DESC'); ?>
							</span>
						</td>
					</tr>
				</table>
			</div>
		</div>

	</div>
</div>