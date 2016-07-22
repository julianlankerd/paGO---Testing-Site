<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

define( 'PAGO_CMP_NAME', 'pago' );
define( 'PAGO_CMP_PATH', dirname( __FILE__ ) );
define( 'PAGO_HELPER_PATH', JPATH_ADMINISTRATOR . '/components/com_pago/helpers' );

//database logging
//to enable db logging create table:
/*
CREATE TABLE `jos_log_entries` (
  `priority` int(11) DEFAULT NULL,
  `message` varchar(512) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  KEY `idx_category_date_priority` (`category`,`date`,`priority`)
)

create log:
JLog::add('mylog', JLog::INFO, 'dblog');
*/
$tables = JFactory::getDbo()->getTableList();
$dbprefix = JFactory::getApplication()->getCfg('dbprefix');

if(in_array($dbprefix . 'log_entries', $tables)){
	JLog::addLogger(
	    array(
	        'logger' => 'database'
	    ),
	    JLog::ALL,
	    'dblog'
	);
}
		
// Always include pago helper
Pago::load_helpers( array( 'helper', 'pagohtml' ) );

// Require the base controller
require JPATH_COMPONENT . '/controller.php';

// Write some Global settings
$configpath = JPATH_COMPONENT_ADMINISTRATOR . '/helpers/pagoConfig.php';

if (!file_exists($configpath))
{
	PagoHelper::writeDefaultSettings();
}

require_once $configpath;

$user = JFactory::getUser();
$dispatcher = KDispatcher::getInstance();

$dispatcher->do_action( 'pago_init' );

$controller = null;

// use the view or specified controller to load the controller to use
// specified controller takes precedence over view
if ( ( $controller = JFactory::getApplication()->input->getCmd( 'controller' ) )
	|| ( $controller = JFactory::getApplication()->input->getCmd( 'view' ) ) ){

	if ( $controller == 'upload' ) {
		$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/upload.php';
	} else {
		$path = JPATH_COMPONENT .'/controllers/'.$controller.'.php';
	}

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

$dispatcher->do_action( 'pago_shutdown' );

// Check for asynchronous requests
if ( 2 === JFactory::getApplication()->input->getInt( 'async', 0 ) ) {
	echo $controller->getMessage();

	// Always kill it
	jexit();
}

// Redirect if set by the controller
$controller->redirect();
?>