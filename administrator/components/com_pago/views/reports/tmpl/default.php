<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
PagoHtml::behaviour_jquery();
PagoHtml::behaviour_jqueryui();
$dispatcher = KDispatcher::getInstance();

PagoHtml::add_js( JURI::base() . 'components/com_pago/javascript/com_pago_config.js' );
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items, 'tabs', false );
?>
<div class="pg-content">
<div class="pg-container-header">Reports</div>
	<div class="pg-row">
                        
								
		<div class="pg-col-3">
			<div class="ui-box ui-box-enabled" id="PayPal_Pro-ui-box" style='min-height: 205px;'>
				<div style="
					 width: 70px; height: 70px; 
					 background-color: #7aa642; 
					 background-image: url('<?php echo JURI::root();?>administrator/components/com_pago/css/img-new/average-sales.png');
					 background-repeat: no-repeat;
					 background-position: center center;
				"></div>
				<hr class="ui-separator">
				<div class="pg-row" id="PayPal_Pro-ui-indicator">
					<div class="pg-col-6 ui-status-indicator">Purchased/Unpurchased Items</div>
					<div class="pg-col-6 text-right"><a href="index.php?option=com_pago&view=graph&type=purchase">Details</a>
					</div>
				</div>
			</div>
			
		</div>
                                                
		<div class="pg-col-3">
			<div class="ui-box ui-box-enabled" id="TwoCheckout-ui-box" style='min-height: 205px;'>
				<div style="
					 width: 70px; height: 70px; 
					 background-color: #7aa642; 
					 background-image: url('<?php echo JURI::root();?>administrator/components/com_pago/css/img-new/average-sales.png');
					 background-repeat: no-repeat;
					 background-position: center center;
				"></div>
				<hr class="ui-separator">
				<div class="pg-row" id="TwoCheckout-ui-indicator">
					<div class="pg-col-6 ui-status-indicator">Total Revenue</div>
					<div class="pg-col-6 text-right"><a href="index.php?option=com_pago&view=graph&type=revenue">Details</a>
					</div>
				</div>
			</div>
		</div>
                                                
		<div class="pg-col-3">
			<div class="ui-box ui-box-enabled" id="PayPal_Express-ui-box" style='min-height: 205px;'>
				<div style="
					 width: 70px; height: 70px; 
					 background-color: #7aa642; 
					 background-image: url('<?php echo JURI::root();?>administrator/components/com_pago/css/img-new/average-sales.png');
					 background-repeat: no-repeat;
					 background-position: center center;
				"></div>
				<hr class="ui-separator">
				<div class="pg-row" id="PayPal_Express-ui-indicator">
					<div class="pg-col-6 ui-status-indicator">Average Order Value</div>
					<div class="pg-col-6 text-right"><a href="index.php?option=com_pago&view=graph&type=avgord">Details</a>
					</div>
				</div>
			</div>
			
		</div>
                                                
		<div class="pg-col-3">
			<div class="ui-box ui-box-enabled" id="AuthorizeNet_AIM-ui-box" style='min-height: 205px;'>
				<div style="
					 width: 70px; height: 70px; 
					 background-color: #7aa642; 
					 background-image: url('<?php echo JURI::root();?>administrator/components/com_pago/css/img-new/average-sales.png');
					 background-repeat: no-repeat;
					 background-position: center center;
				"></div>
				<hr class="ui-separator">
				<div class="pg-row" id="AuthorizeNet_AIM-ui-indicator">
					<div class="pg-col-6 ui-status-indicator">Search Results</div>
					<div class="pg-col-6 text-right"><a href="index.php?option=com_pago&view=graph&type=search">Details</a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="pg-col-3">
			<div class="ui-box ui-box-enabled" id="AuthorizeNet_AIM-ui-box" style='min-height: 205px;'>
				<div style="
					 width: 70px; height: 70px; 
					 background-color: #7aa642; 
					 background-image: url('<?php echo JURI::root();?>administrator/components/com_pago/css/img-new/average-sales.png');
					 background-repeat: no-repeat;
					 background-position: center center;
				"></div>
				<hr class="ui-separator">
				<div class="pg-row" id="AuthorizeNet_AIM-ui-indicator">
					<div class="pg-col-6 ui-status-indicator">Abandoned carts</div>
					<div class="pg-col-6 text-right"><a href="index.php?option=com_pago&view=graph&type=cart">Details</a>
					</div>
				</div>
			</div>
			
		</div>
                                                
	</div>
</div>

<?php
 PagoHtml::pago_bottom();
