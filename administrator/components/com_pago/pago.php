
<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
* Define constants for all pages
*/
define( 'PAGO_CMP_NAME', 'pago' );
define( 'COM_PAGO_DIR', 'images/pago/' );
define( 'COM_PAGO_BASE', JPATH_ROOT .DS. COM_PAGO_DIR );
define( 'COM_PAGO_BASEURL', JURI::root() . str_replace( DS, '/', COM_PAGO_DIR ) );

require JPATH_COMPONENT . '/controller.php' ;

require( JPATH_COMPONENT . '/helpers/helper.php' );

// TODO: remove before going live
PagoHelper::db_check();

PagoHelper::plugin_check();

// Write global config settings file
PagoHelper::writeDefaultSettings();
$configpath = JPATH_COMPONENT . '/helpers/pagoConfig.php';
require_once $configpath;

$controller = null;

// use the view or specified controller to load the controller to use
// specified controller takes precedence over view
if ( ( $controller = JFactory::getApplication()->input->getCmd( 'controller' ) )
	|| ( $controller = JFactory::getApplication()->input->getCmd( 'view' ) ) ) {

	$path = JPATH_COMPONENT . "/controllers/{$controller}.php";

	if ( !file_exists( $path ) ) {
		// if no controller use default
		$controller = null;
	} else {
		require $path;
	}

}

// Create the controller
$classname = 'PagoController';

if ( $controller !== null ) {
	$classname .= $controller;
}

if ( !class_exists( $classname ) ) {
	JError::raiseWarning( 22, JText::_( 'PAGO_CONTROLLER_CLASS_NOT_FOUND' ) );
	return;
}

$controller = new $classname();
$controller->execute( JFactory::getApplication()->input->getCmd( 'task', 'display' ) );
// Redirect if set by the controller
$controller->redirect();
