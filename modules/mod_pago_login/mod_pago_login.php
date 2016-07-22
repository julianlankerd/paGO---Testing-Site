

<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined('_JEXEC') or die;
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('var $MOD_PAGO_LOGIN_LOGIN_FAILED = "'.JTEXT::_("MOD_PAGO_LOGIN_LOGIN_FAILED").'"');
$config = Pago::get_instance('config')->get();
// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';


$type 	= modPagoLoginHelper::getType();
$return	= modPagoLoginHelper::getReturnURL($params, $type);

if($params->get('pago_config_load_use_font_awesome',1) == 1){

		echo '<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.0/css/font-awesome.css" rel="stylesheet">';
}
if($params->get('use_default_css',1) == 1){
	$temStyle = $config->get( 'template.pago_theme_style', 0 );
	if($temStyle == 0){
		$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/style.css');
	}elseif($temStyle == 1){
		$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/style-dark.css');
	}
}
Pago::load_helpers( array( 'pagohtml','helper') );

if($params->get('use_default_jquery',0) == 1){
	PagoHtml::loadJquery();
}
$doc->addScript(JURI::base() . 'modules/'.$module->module.'/tmpl/js/script.js');
$user = JFactory::getUser();

PagoHtml::loadFonts();

$language = JFactory::getLanguage();
$language->load('com_pago');

require JModuleHelper::getLayoutPath('mod_pago_login', $params->get('layout', 'default'));