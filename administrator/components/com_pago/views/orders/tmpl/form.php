<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
PagoHtml::apply_layout_fixes();
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items );
?>
<form action="index.php" method="post" name="adminForm">
	<?php echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_DETAILS' ) ) ?>
	<?php echo $this->details ?>
	<?php echo $this->grouplist ?>
	<?php echo PagoHtml::module_bottom() ?>

	<?php echo PagoHtml::module_top( JText::_( 'PAGO_ADDRESS_DETAILS' ) ) ?>
	<?php echo $this->address_billing ?>
	<?php echo $this->address_shipping ?>
	<?php echo PagoHtml::module_bottom() ?>

	<?php echo PagoHtml::module_top( JText::_( 'PAGO_ITEMS' ) ) ?>
	<?php echo $this->items ?>
	<?php echo $this->shipping ?>
	<?php echo $this->invoice ?>
	<?php echo PagoHtml::module_bottom() ?>
	
	<?php echo PagoHtml::module_top( JText::_( 'PAGO_OTHER_ORDER_DETAILS' ) ) ?>
	<?php echo $this->customer_note ?>
	<?php echo $this->payment_gateways ?>
	<?php echo PagoHtml::module_bottom() ?>

	<input type="hidden" name="cid[]" value="<?php echo $this->order_id; ?>" />
	<input type="hidden" name="option" value="com_pago" />
	<input type="hidden" name="id" value="<?php echo $this->order_id; ?>" />
	<input type="hidden" name="user_id" value="<?php echo $this->user_id; ?>" />
	<input type="hidden" name="task" value="cancel" />
	<input type="hidden" name="view" value="orders" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();