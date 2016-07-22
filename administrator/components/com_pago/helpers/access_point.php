<?php 
// defined('_JEXEC') or die;
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !isset( $_REQUEST['ac'] ) ) {
	die();
}

$JPATH_CORE = dirname(__FILE__) . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'..'.
	DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR;

$file = $JPATH_CORE .'..'. DIRECTORY_SEPARATOR . 'configuration.php';

//hacks for symlinked development environments
if(!file_exists($file)){
	$file = '/var/www/html/dev/configuration.php';
	$JPATH_CORE = '/var/www/html/dev/administrator/';
}

if(strstr(dirname(__FILE__), 'pago_vini')){
	$file = '/var/www/html/vini/configuration.php';
	$JPATH_CORE = '/var/www/html/vini/administrator/';
}

define('JPATH_CORE', $JPATH_CORE);

require_once( realpath( $file ) );

$config = new JConfig();
$name = md5( md5( $config->secret . 'administrator' ) );

$fileTypes = $_REQUEST['validFileType'];
$fileTypes = str_replace( "*", "",$fileTypes);
$fileTypes = str_replace( ".", "",$fileTypes);
$fileTypes = explode(";", $fileTypes);
$fileParts = pathinfo($_FILES['Filedata']['name']);

if($_FILES['Filedata']['name']!=""){
	if (!in_array(strtolower($fileParts['extension']), $fileTypes)) {	
		echo 'Invalid file type.';	
		exit();
	
	}
	else{
	
	$_COOKIE[$name] = $_REQUEST['ac'];
	unset ($_REQUEST['ac'] );

	$_REQUEST['tmpl']   = 'component';
	$_REQUEST['option'] = 'com_pago';
	$_REQUEST['task']   = 'upload';
	include( realpath( JPATH_CORE . 'index.php' ) );
}
}else{
	
	$_COOKIE[$name] = $_REQUEST['ac'];
	unset ($_REQUEST['ac'] );

	$_REQUEST['tmpl']   = 'component';
	$_REQUEST['option'] = 'com_pago';
	$_REQUEST['task']   = 'upload';
	include( realpath( JPATH_CORE . 'index.php' ) );
}
?>