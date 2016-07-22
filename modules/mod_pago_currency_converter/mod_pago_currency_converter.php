<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$doc = JFactory::getDocument();

$config = Pago::get_instance('config')->get();
$pago_theme   = $config->get( 'template.pago_theme', 'default' );
// Include the syndicate functions only once
Pago::load_helpers( array( 'helper','pagohtml', 'imagehandler' ) );


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

PagoHtml::loadChosen();
PagoHtml::loadFonts();

$doc->addScript(JURI::base() . 'modules/'.$module->module.'/tmpl/js/script.js');

$configpath = JPATH_ADMINISTRATOR . '/components/com_pago/helpers/pagoConfig.php';
require_once $configpath;

JFactory::getLanguage()->load('com_yourcomponentname');

$currenciesModel = JModelLegacy::getInstance('currencies', 'PagoModel');

$newCurrencyId = JFactory::getApplication()->input->getInt('currencyChanger');
if($newCurrencyId){
	Pago::get_instance( 'cookie' )->set( 'current_currency', $newCurrencyId );	
}
		
if(!$currenciesModel){
	return;
}
$currencies = $currenciesModel->getItems(false);


$currentCurrencyId = Pago::get_instance( 'cookie' )->get( 'current_currency');

if(!$currentCurrencyId){
	$defaultCurrency = $currenciesModel->getDefault();
	$currentCurrencyId = $defaultCurrency->id;
	Pago::get_instance( 'cookie' )->set( 'current_currency', $currentCurrencyId );
}

require JModuleHelper::getLayoutPath('mod_pago_currency_converter', $params->get('layout', 'default'));