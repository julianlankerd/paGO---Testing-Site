<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 
defined('_JEXEC') or die('Restricted access'); 

$this->document->setTitle( 'Order History' ); 
$this->pathway->addItem( 'Order History' , JRoute::_( '&view=account' ) );

$version = new JVersion();
$class = '';

JText::script('PAGO_ACCOUNT_ORDER_HISTORY_DATE_REQUIRED');
JText::script('PAGO_ACCOUNT_ORDER_HISTORY_DATE_ERROR');

if($version->RELEASE < 3){
	$class = 'class = "pg-account-order-history-dates"';
}
?>

<?php $this->load_header(); ?>

<div id="pg-account">
	<div id="pg-account-menu" class="pg-account-left">
		<?php echo $this->modules->render_position( 'pago_account_menu' ); ?>
		<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
	</div>

	<div id="pg-account-order">
		<div class = "pg-account-title"><?php echo JText::_('PAGO_ACCOUNT_ORDER_HISTORY_TITLE'); ?></div>
		<p><?php echo JText::_('PAGO_ACCOUNT_ORDER_HISTORY_DESC'); ?></p>

		<div id="pg-account-order-history">
			<div id="pg-system-messages"></div>
			<div id="pg-account-order-history-header">
				<form action="<?php echo JRoute::_('index.php?layout=order_history&view=account'); ?>" method="POST" id='pg-order-form'>
					<?php
					/*
					<div id="pg-account-order-history-search">
						<label for="pg-order-status-searchbox" class="pg-label"><?php echo JText::_('PAGO_ACCOUNT_ORDER_NUMBER'); ?>:</label>
						<input id="pg-order-history-searchbox" type="text" name="history_search" class="pg-inputbox pg-searchbox" onfocus="if (this.value=='Search...') this.value='';" onblur="if (this.value=='') this.value='Search...';" value="Search..." />
					</div>
					*/
					?>
					<span class="label-order"><?php echo JText::_('PAGO_ACCOUNT_ORDER_HISTORY_DATE_RANGE'); ?>:</span>
					<div id="pg-account-order-history-dates" <?php echo $class; ?>>
						<span class="pg-calendar"><?php echo JHTML::calendar( isset($this->startdate) ? $this->startdate : '', 'startdate', 'startdate', '%Y-%m-%d' ); ?></span>
						<span class="pg-calendar"><?php echo JHTML::calendar( isset($this->enddate) ? $this->enddate : '', 'enddate', 'enddate', '%Y-%m-%d' ); ?></span>		
					</div>
					<button class="pg-searchbutton"><?php echo JText::_('PAGO_ACCOUNT_ORDER_GO_BUTTON'); ?></button>
					<a href="<?php echo JRoute::_('index.php?option=com_pago&view=account&layout=order_history&all=1'); ?>" class="pg-account-order-show-all"><?php echo JText::_('PAGO_ACCOUNT_ORDER_SHOW_ALL'); ?></a>
				</form>
			</div>
			<div id="pg-account-order-history-recent" class="pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_ACCOUNT_ORDER_HISTORY_ORDERS_TITLE'); ?></h3>
				<div>
					<?php if ( $recent_table = PagoHelper::load_template( 'account', 'order_recent_table' ) ) require $recent_table; ?>
				</div>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery('.pg-calendar button').click(function () {
		jQuery('.calendar').last().children(':first').children(':first').children(':first').children(':first').remove();
		jQuery('.calendar').last().children(':first').children(':first').children(':first').children(':first').attr('colspan', '7');
	});

});
</script>
