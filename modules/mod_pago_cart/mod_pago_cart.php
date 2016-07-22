<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
$doc = JFactory::getDocument();
$config = Pago::get_instance('config')->get();

Pago::load_helpers( array( 'pagohtml') );

if($params->get('use_default_css',1) == 1){
	$temStyle = $config->get( 'template.pago_theme_style', 0 );
	if($temStyle == 0){
		$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/style.css');
	}elseif($temStyle == 1){
		$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/style-dark.css');
	}
}

if($params->get('use_default_jquery',0) == 1){
	PagoHtml::loadJquery();
}

$doc->addScript(JURI::root() . 'modules/mod_'.$module->name.'/js/script.js');

$configpath = JPATH_ADMINISTRATOR . '/components/com_pago/helpers/pagoConfig.php';
require_once $configpath;

$cart_items 	= Pago::get_instance('cart')->get('items');
$cart_total		= Pago::get_instance('cart')->get('format.total');
$cart_quantity 	= Pago::get_instance('cart')->get('total_qty');
$price_format 	= Pago::get_instance('price')->format($cart_total);
$price_format	= Pago::get_instance( 'price' )->removeNulls($price_format);

PagoHtml::loadFonts();

($cart_quantity == 1) ? $cart_quantity_text = JText::_('PAGO_MINI_CART_ITEM') :
$cart_quantity_text = JText::_('PAGO_MINI_CART_ITEMS');

require JModuleHelper::getLayoutPath('mod_pago_cart', $params->get('layout', 'default'));
