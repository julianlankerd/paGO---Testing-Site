<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !isset( $_REQUEST['ac'] ) ) {
	die();
}

define( 'JPATH_CORE', dirname(__FILE__) . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'..'. 
	DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR );

require_once realpath( JPATH_CORE . 'configuration.php' );
$config = new JConfig();
$name = md5( md5( $config->secret . 'site' ) );
$_COOKIE[$name] = $_REQUEST['ac'];
unset( $_REQUEST['ac'] );

$_REQUEST['tmpl']   = 'component';
$_REQUEST['option'] = 'com_pago';
$_REQUEST['task']   = 'upload';
$_REQUEST['async']  = 1;

include realpath( JPATH_CORE . 'index.php' );
?>