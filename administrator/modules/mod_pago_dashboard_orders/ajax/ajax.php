<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
//defined('_JEXEC') or die;
$my_path = dirname(__FILE__);

if ( file_exists($my_path . "/../../../../configuration.php"))
{
    $absolute_path = dirname($my_path . "/../../../../configuration.php");
    require_once $my_path . "/../../../../configuration.php";
}
elseif(file_exists("/var/www/html/dev/configuration.php")){
	$absolute_path = dirname("/var/www/html/dev/configuration.php");
    require_once "/var/www/html/dev/configuration.php";
}
else
{
    die( "Joomla Configuration File not found!" );
}

$absolute_path = realpath($absolute_path);

// Set flag that this is a parent file
define('_JEXEC', 1);
define('JPATH_BASE', $absolute_path);
define('DS', DIRECTORY_SEPARATOR);

require_once  JPATH_BASE . DS . 'includes' . DS . 'defines.php';
require_once JPATH_BASE . DS . 'includes' . DS . 'framework.php';
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';
 
// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;
jimport( 'joomla.environment.request' );
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();


define( 'PAGO_CMP_NAME', 'pago' );
define( 'COM_PAGO_DIR', 'images/pago/' );
define( 'COM_PAGO_BASE', JPATH_ROOT .DS. COM_PAGO_DIR );
define( 'COM_PAGO_BASEURL', JURI::root() . str_replace( DS, '/', COM_PAGO_DIR ) );



require( JPATH_BASE . '/administrator/components/com_pago/helpers/helper.php' );
require( JPATH_BASE . '/administrator/components/com_pago/instances/pago_price.php' );

JDEBUG ? $_PROFILER->mark('afterLoad') : null;
require_once JPATH_ADMINISTRATOR . DS . 'modules' . DS . 'mod_pago_dashboard_orders' . DS . 'helper.php';
JPluginHelper::importPlugin( 'system' ); 
$get = JRequest::get('post');
$selected = $get['selected_sale'];

//$doc = JFactory::getDocument();

if ($selected == 'days' || $selected == 'months' || $selected == 'year')
{
	modPagoOrderHelper::latestOrdersAvg($selected);
}
elseif ($selected == 'customdate')
{
	$sale_start_date = $get['sale_start_date'];
	$sale_end_date = $get['sale_end_date'];
	modPagoOrderHelper::latestOrdersAvg($selected, $sale_start_date, $sale_end_date);
}
	
exit;


?>
