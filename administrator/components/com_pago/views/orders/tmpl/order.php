<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
PagoHtml::behaviour_jquery( 'jqueryui' );
PagoHtml::apply_layout_fixes();
PagoHtml::uniform();
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items, 'tabs' );

?>

<div class="pg-content">
	<form action="index.php" method="post" name="adminForm">

		<?php PagoHtml::deploy_tabpanel( 'tabs' ); ?>
		<div id="tabs">

			<div class="pg-tabs">
				<ul>
					<li><a href="#tabs-1"><?php echo JText::_( 'PAGO_ORDER_DETAILS' ) ?></a></li>
					<li><a href="#tabs-2"><?php echo JText::_( 'PAGO_ORDER_ITEMS' ) ?></a></li>
					<li><a href="#tabs-3"><?php echo JText::_( 'PAGO_ORDER_SHIPPINGBILLING' ) ?></a></li>
					<?php
					// Needs to be a filter, otherwise the $counter gets lost
					//$counter = 5;
					//$dispatcher->trigger( 'backend_item_tab_name', array( &$counter ) );
					?>
				</ul>
				<div class="clear"></div>
			</div>

			<div id="tabs-1">
				<?php echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_DETAILS' ) ) ?>
				<?php echo $this->details ?>
				<?php echo PagoHtml::module_bottom() ?>
			</div>

			<div id="tabs-2">
				<?php echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_ITEMS' ) ) ?>
				<?php echo $this->items ?>
				<?php echo PagoHtml::module_bottom() ?>
			</div>

			<div id="tabs-3">

				<?php echo PagoHtml::module_top( JText::_( 'PAGO_PAYMENT_METHOD' ) ) ?>
				<?php
					//quick/ugly way to disable selection of pgate on an existing order
					if( !$this->order_id ){ 
						echo $this->payment_gateways ;
					} else {
						echo '<div style="display:none">' . $this->payment_gateways . '</div>';
					}
				?>
				<?php echo PagoHtml::module_bottom() ?>

				<?php echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_TOTALS' ) ) ?>
				<?php echo $this->order_tools; ?>
				<?php echo PagoHtml::module_bottom() ?>

				<?php echo PagoHtml::module_top( JText::_( 'PAGO_ORDER_SHIPPING' ) ) ?>
				<?php echo $this->shipping ?>
				<?php echo $this->customer_note ?>
				<?php echo PagoHtml::module_bottom() ?>

				<?php echo PagoHtml::module_top( JText::_( 'PAGO_ADDRESS_DETAILS' ) ) ?>
				<?php echo $this->address_billing ?>
				<?php echo $this->address_shipping ?>
				<?php echo PagoHtml::module_bottom() ?>
			</div>
			<!-- end #tabs-3 -->
			
			<?php
			// Needs to be a filter, otherwise the $counter gets lost
			//$counter = 5;
			//$dispatcher->trigger( 'backend_item_tab_data', array( &$counter, $this->item ) );
			?>
		</div>
		<!-- end #tabs -->

		<input type="hidden" name="cid[]" value="<?php echo $this->order_id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->order_id; ?>" />
		<input type="hidden" name="user_id" value="<?php echo $this->user_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="orders" />

		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
<!-- end pago content --> 
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();