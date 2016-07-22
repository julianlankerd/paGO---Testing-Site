<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

$config = Pago::get_instance('config')->get();

require_once ( dirname( __FILE__ ) .DS. 'helper.php' );
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

$start_level 	= trim( $params->get( 'start_level', 1 ) );
$depth			= trim( $params->get( 'depth', 999 ) );

PagoHtml::loadFonts();

$pago_template = pago::get_instance('template');

require JModuleHelper::getLayoutPath('mod_pago_categories', $params->get('layout', 'default'));