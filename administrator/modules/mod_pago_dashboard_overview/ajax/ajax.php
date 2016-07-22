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

JDEBUG ? $_PROFILER->mark('afterLoad') : null;
require_once JPATH_ADMINISTRATOR . DS . 'modules' . DS . 'mod_pago_dashboard_overview' . DS . 'helper.php';
$get = JRequest::get('post');
$selected = isset( $get['selected_sale'] ) ? $get['selected_sale'] : '';
$sale_start_date = isset( $get['sale_start_date'] ) ? $get['sale_start_date'] : '';
$sale_end_date = isset( $get['sale_end_date'] ) ? $get['sale_end_date'] : '';

//$doc = JFactory::getDocument();
$order_chart = '';
if ($selected == 'days')
{
	$res = modPagoOverviewHelper::get_latest_order_days();
	$order_chart = modPagoOverviewHelper::get_chart_for_days($res);
}
elseif ($selected == 'months')
{
	// Order Month Chart
	$ord_array_month = modPagoOverviewHelper::get_latest_order_months();
	$order_chart = modPagoOverviewHelper::get_chart_for_months($ord_array_month);

}
elseif ($selected == 'year')
{
	// Order Year Chart
	$ord_array_year = modPagoOverviewHelper::get_latest_order_years();
	$order_chart = modPagoOverviewHelper::get_chart_for_years($ord_array_year);

}
elseif ($selected == 'customdate')
{
	// Order Year Chart
	$date1 = new DateTime($sale_end_date);
	$date2 = new DateTime($sale_start_date);
	$interval = $date1->diff($date2); 
	$ord_array_year = modPagoOverviewHelper::get_latest_order_custom_dates($sale_start_date, $sale_end_date, $interval->days);
	
	if($interval->days <= 7)
	{
		$order_chart = modPagoOverviewHelper::get_chart_for_custom_days($ord_array_year,$sale_start_date, $sale_end_date, $interval->d);
	}
	else if($interval->days > 7 && $interval->days <= 365)
	{
		$order_chart = modPagoOverviewHelper::get_chart_for_custom_months($ord_array_year,$sale_start_date, $sale_end_date, $interval->m);
	}
	else if($interval->days > 365)
	{
		$order_chart = modPagoOverviewHelper::get_chart_for_custom_year($ord_array_year,$sale_start_date, $sale_end_date);
	}

}

echo '['.$order_chart.']';
	exit;


?>
