<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>

<?php
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('var $PRODUCT_NOT_EXIST = "'.JTEXT::_("MOD_PAGO_PRODUCT_NOT_EXIST").'"');
$config = Pago::get_instance('config')->get();

// Include the syndicate functions only once
require_once ( dirname( __FILE__ ) .DS. 'helper.php' );
Pago::load_helpers( array( 'helper','pagohtml', 'imagehandler','attributes' ) );
JLoader::register('NavigationHelper',JPATH_ROOT .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'navigation.php');
if($params->get('pago_config_load_use_font_awesome',1) == 1){
	PagoHtml::add_css("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");
}
if($params->get('use_default_jquery',0) == 1){
	PagoHtml::loadJquery();
}
PagoHtml::loadBootstrapCss();
$version = new JVersion();
if($version->RELEASE < 3){
	PagoHtml::loadBootstrapJs();
}
PagoHtml::loadChosen(false);
PagoHtml::loadFonts();

if($params->get('use_default_css',1) == 1){
	$temStyle = $config->get( 'template.pago_theme_style', 0 );
	if($temStyle == 0){
		$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/style.css');
		$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/chosen.css');
	}elseif($temStyle == 1){
		$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/style-dark.css');
		$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/chosen-dark.css');
	}
	$doc->addStyleSheet(JURI::base() . 'modules/'.$module->module.'/tmpl/css/idangerous.swiper.css');
}

$doc->addScript(JURI::base() . 'modules/'.$module->module.'/tmpl/js/script.js');
$doc->addScript(JURI::base() . 'modules/'.$module->module.'/tmpl/js/idangerous.swiper.js');

$user = JFactory::getUser();


$config = Pago::get_instance( 'config' )->get();
$items  = mod_pago_product_helper::getItems( $params );
$nav = new NavigationHelper();
$item_id = JFactory::getApplication()->input->getInt( 'Itemid' );


$mod_pago_view_setting  = mod_pago_product_helper::getViewSettings( $params );

if($items){
	foreach ($items as $item) {
		if(($mod_pago_view_setting->product_settings_short_desc == 1) || ($mod_pago_view_setting->product_settings_desc == 1) || ($mod_pago_view_setting->product_settings_product_title == 1)){

		    if($mod_pago_view_setting->product_settings_short_desc == 1){
		        $item->description = TruncateHTML::truncateWords( $item->description, $mod_pago_view_setting->product_settings_short_desc_limit, '...');
		    }
		    if($mod_pago_view_setting->product_settings_desc == 1){
		        $item->content = TruncateHTML::truncateWords( $item->content, $mod_pago_view_setting->product_settings_desc_limit, '...');
		    }
		    if($mod_pago_view_setting->product_settings_product_title == 1){
		        $item->name = TruncateHTML::truncateWords( $item->name, $mod_pago_view_setting->product_settings_product_title_limit, '...');
		    }
		}
	}
}

require JModuleHelper::getLayoutPath('mod_pago_product', $params->get('layout', 'default'));