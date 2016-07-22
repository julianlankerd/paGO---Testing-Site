<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');


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

<div class="pg-main" id="pago-wingman-app" data-ng-cloak>
	<div class="pg-content">
		<div class="pg-row pg-mb-20">
			<div data-ng-view data-ng-cloak data-ng-show="ready" data-onload="$root.viewLoaded()" autoscroll="true"></div>
		</div>
	</div>
</div>

<?php PagoHtml::pago_bottom();