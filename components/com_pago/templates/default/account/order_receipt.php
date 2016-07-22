<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

$this->document->setTitle( 'Order Receipt' );
$this->pathway->addItem( 'Order Receipt' , JRoute::_( '&view=account' ) );

$addresses = $this->order['addresses'];
$details = $this->order['details'];
$items = $this->order['items'];
$order_status = Pago::get_instance( 'orders' )->get_order_status( $this->order['details']->order_status ); ?>

<?php $this->load_header(); ?>

<div id="pg-account">
	<div id="pg-account-menu">
		<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
	</div>

	<div id="pg-account-order" class="clearfix">

		<?php if( JFactory::getApplication()->input->get( 'status' ) ) : ?>
		<h2><?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_TITLE'); ?></h2>

		<div id="pg-account-order-status" class="pg-account-order-status-<?php echo strtolower( $order_status ); ?>">
			<p class="pg-order-status-desc"><?php echo JText::_('PAGO_ACCOUNT_ORDER_STATUS_' . strtoupper( $order_status ) . '_DESC'); ?></p>
		</div>
		<?php else: ?>
		<h2><?php echo JText::_('PAGO_ACCOUNT_ORDER_INVOICE_TITLE'); ?></h2>
		<?php endif; ?>

		<?php if ( $order_details_template = PagoHelper::load_template( 'account', 'order_details' ) ) require $order_details_template; ?>

	</div>
</div>