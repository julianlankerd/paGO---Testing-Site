<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
class modPagoOverviewHelper
{
	public static function GetOverviewChart($params)
	{
		// Module params
		$moduleclass_sfx = $params->get('moduleclass_sfx', '');
		$doc = JFactory::getDocument();
		$doc->addScript('https://www.google.com/jsapi');
	
		// Order Day Chart
		$ord_array = self::get_latest_order_months();
		$order_chart = self::get_chart_for_months($ord_array);
		$chart_script = self::get_chart_script($order_chart);
		?>

		<div class = "pg-dashboard-charts-container">
			<div class = "pg-container-header">
				<?php echo JTEXT::_('PAGO_SALES_CHARTS');?>
			<!--	<div class = "pg-right no-margin">
					<select name="select_sale" id="select_sale" style="width:100px;">
						<option value="days">7 <?php echo JTEXT::_('PAGO_DASHBOARD_DAYS'); ?></option>
						<option value="months"><?php echo JTEXT::_('PAGO_MONTHLY'); ?></option>
						<option value="year"><?php echo JTEXT::_('PAGO_YEARLY'); ?></option>
					</select>
				</div> -->
			</div>
			
			<div id="pago_chart_overview" class="pago_chart_overview pg-border pg-white-bckg"></div>
			<div id="chartOverviewLoader" style="display:none;"><img src="<?php echo JURI::root() ?>components/com_pago/images/loadingAnimation.gif" /></div>
		</div>
<?php
	}

	// Get Chart Script
	public static function get_chart_script($order_chart)
	{
		$doc = JFactory::getDocument();
		$scripts = "
				if(typeof google !== 'undefined') google.load('visualization', '1', {packages:['corechart']});
					  google.setOnLoadCallback(pago_chart_overview);
					  function pago_chart_overview() {
						var data = google.visualization.arrayToDataTable([" . trim($order_chart, ",") . "]);
						var options = {width: '100%',height: 400,title: 'Sales Chart'};
						var chart = new google.visualization.AreaChart(document.getElementById('pago_chart_overview'));
						chart.draw(data, options);
						if (jQuery('.pg-dashboard-charts-container .pg-container-header + div').height() > jQuery('.pg-dashboard-best-selling-container .pg-container-header + div').height()){
							jQuery('.pg-dashboard-best-selling-container .pg-container-header + div').css('height', jQuery('.pg-dashboard-charts-container .pg-container-header + div').height()+parseInt(2));
						}
						else{
							jQuery('.pg-dashboard-charts-container .pg-container-header + div').css('height', jQuery('.pg-dashboard-best-selling-container .pg-container-header + div').height()+parseInt(42));	
						}
					}
				";
		$doc->addScriptDeclaration($scripts);
	}

	// Get Recent Orders For Days
	public static function get_latest_order_days()
	{
		$rightnow = date('Y-m-d', strtotime('+1 days'));
		$sub7days = date('Y-m-d', strtotime('-6 days'));
		$db = JFactory::getDBO();
		$ord_query = "SELECT order_id,FROM_UNIXTIME(unix_timestamp(cdate),'%Y-%m-%d') AS ord_date,SUM(order_total) AS order_total FROM #__pago_orders WHERE cdate BETWEEN '" . $sub7days . "' AND '" . $rightnow . "' AND order_status <> 'A' GROUP BY ord_date ORDER BY order_id DESC";
		$db->setQuery($ord_query);

		return $ord_array = $db->loadObjectList();

	}
	
	// Get Chart Array for Days
	public static function get_chart_for_days($ord_array)
	{
		$rightnow = date('Y-m-d', strtotime('+1 days'));
		$sub7days = date('Y-m-d', strtotime('-6 days'));
		$order_chart = '';

		$days = 7;
		$format = 'm-d';
		$m = date("m");
		$de = date("d");
		$y = date("Y");
		$dateArray = array();
		$dateChart = '';

		for ($i = 0; $i <= $days - 1; $i++)
		{
			$dateArray[] = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
		}

		$cur_date_array = array_reverse($dateArray);

		for ($j = 0;$j < count($cur_date_array);$j++)
		{
			if ($j == 0)
			{
				$order_chart .= "['Date: ', 'Sales']";
			}

			$dateChart = $cur_date_array[$j];
			$priceChart = '0';

			for ($k = 0;$k < count($ord_array);$k++)
			{
				if ($cur_date_array[$j] == date('m-d', strtotime($ord_array[$k] -> ord_date)))
				{
					$priceChart = $ord_array[$k]->order_total . " ";
				}
			}

			$order_chart .= ",['" . $dateChart . "', " . $priceChart . "]";
		}
		// ['Date: ', 'Sales'],['10-23', 0],['10-24', 0],['10-25', 0],['10-26', 100.00000 ],['10-27', 0],['10-28', 660.00000 ],['10-29', 0]

		return $order_chart;
	}

	// Get Recent Orders For Months
	public static function get_latest_order_months()
	{

		$startdate = date("Y-m-d", strtotime(date("Y") . "-1-01"));
	    $enddate = date("Y-m-d", strtotime(date("Y") . "-12-31"));
		$db = JFactory::getDBO();
		$ord_query = "SELECT order_id,FROM_UNIXTIME(unix_timestamp(cdate),'%Y-%m-%d') AS ord_date,SUM(order_total) AS order_total FROM #__pago_orders WHERE cdate BETWEEN '" . $startdate . "' AND '" . $enddate . "' AND order_status <> 'A' GROUP BY MONTH(ord_date) ORDER BY order_id DESC";
		$db->setQuery($ord_query);

		return $ord_array = $db->loadObjectList();

	}

	// Get Chart Array for Months
	public static function get_chart_for_months($ord_array)
	{
		$order_chart = '';
		$days = 7;
		$format = 'm-d';
		$m = date("m");
		$de = date("d");
		$y = date("Y");
		$monthArray = array();
		$dateChart = '';
		$startdate = date("Y-m-d", strtotime(date("Y") . "-1-01"));

		for ($m = 1; $m <= 12; $m++)
		{
    		$monthArray[] = date('y-m',  mktime(0, 0, 0, $m, 1, date('Y')));
     	}

		$monthArray = array_reverse($monthArray);

		for ($j = 0;$j < count($monthArray);$j++)
		{
			if ($j == 0)
			{
				$order_chart .= "['Months: ', 'Sales']";
			}

			$dateChart = $monthArray[$j];
			$priceChart = '0';

			for ($k = 0;$k < count($ord_array);$k++)
			{
				if ($monthArray[$j] == date('y-m', strtotime($ord_array[$k] -> ord_date)))
				{
					$priceChart = $ord_array[$k]->order_total . " ";
				}
			}

			$order_chart .= ",['" . $dateChart . "', " . $priceChart . "]";
		}

		return $order_chart;

	}

	// Get Recent Orders For Years
	public static function get_latest_order_years()
	{
		$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
		$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
	    $endyear = date("Y-m-d", $date_six_year_ago);
		$db = JFactory::getDBO();
		$ord_query = "SELECT order_id,FROM_UNIXTIME(unix_timestamp(cdate),'%Y-%m-%d') AS ord_date,SUM(order_total) AS order_total FROM #__pago_orders WHERE cdate BETWEEN '" . $endyear . "' AND '" . $startyear . "' AND order_status <> 'A' GROUP BY YEAR(ord_date) ORDER BY order_id DESC";
		$db->setQuery($ord_query);

		return $ord_array = $db->loadObjectList();

	}

	// Get Chart Array for Years
	public static function get_chart_for_years($ord_array)
	{
		$order_chart = '';
		$yearArray = array();
		$dateChart = '';
		$startdate = date("Y-m-d", strtotime(date("Y") . "-1-01"));
		$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
		$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
	    $endyear = date("Y-m-d", $date_six_year_ago);

		for ($y = 0; $y <= 6; $y++)
		{
    		$date_year_ago = strtotime("-" . $y . "year", strtotime(date("Y")));
	    	$yearArray[] = date("Y", $date_year_ago);
     	}

		$yearArray = array_reverse($yearArray);

		for ($j = 0;$j < count($yearArray);$j++)
		{
			if ($j == 0)
			{
				$order_chart .= "['Years: ', 'Sales']";
			}

			$dateChart = $yearArray[$j];
			$priceChart = '0';

			for ($k = 0;$k < count($ord_array);$k++)
			{
				if ($yearArray[$j] == date('Y', strtotime($ord_array[$k] -> ord_date)))
				{
					$priceChart = $ord_array[$k]->order_total . " ";
				}
			}

			$order_chart .= ",['" . $dateChart . "', " . $priceChart . "]";
		}

		return $order_chart;

	}
	
	public static function get_latest_order_custom_dates($start_date, $end_date, $daysDiff)
	{
		
		$end_date = date('Y-m-d 12:12:12',strtotime($end_date));
		
		if($daysDiff <= 7)
		{
			$groupBy = "ord_date";	
		}
		else if($daysDiff > 7 && $daysDiff <= 365)
		{
			$groupBy = "MONTH(ord_date)";
		}
		else if($daysDiff > 365)
		{
			$groupBy = "YEAR(ord_date)";
		}
		
		$db = JFactory::getDBO();
		 $ord_query = "SELECT order_id,FROM_UNIXTIME(unix_timestamp(cdate),'%Y-%m-%d') AS ord_date,SUM(order_total) AS order_total FROM #__pago_orders WHERE cdate BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND order_status <> 'A' GROUP BY " . $groupBy . " ORDER BY order_id DESC";
		$db->setQuery($ord_query);

		return $ord_array = $db->loadObjectList();

	}
	
	public static function get_chart_for_custom_days($ord_array,$sale_start_date, $sale_end_date, $days)
	{
		$rightnow = $sale_end_date; // date('Y-m-d', strtotime('+1 days')); $xmasDay = new DateTime($sale_end_date. '+ 1 day');
		$sub7days = $sale_start_date; //date('Y-m-d', strtotime('-6 days'));
		$order_chart = '';

		$format = 'm-d';
		$m =  date("m", strtotime($sale_end_date));
		$de = date("d", strtotime($sale_end_date));
		$y = date("Y", strtotime($sale_end_date));
		$dateArray = array();
		$dateChart = '';

		for ($i = 0; $i <= $days ; $i++)
		{
			$dateArray[] = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
		}

		$cur_date_array = $dateArray;

		for ($j = 0;$j < count($cur_date_array);$j++)
		{
			if ($j == 0)
			{
				$order_chart .= "['Date: ', 'Sales']";
			}

			$dateChart = $cur_date_array[$j];
			$priceChart = '0';

			for ($k = 0;$k < count($ord_array);$k++)
			{
				if ($cur_date_array[$j] == date('m-d', strtotime($ord_array[$k] -> ord_date)))	
				{
					$priceChart = $ord_array[$k]->order_total . " ";
				}
			}

			 $order_chart .= ",['" . $dateChart . "', " . $priceChart . "]";
		}
		// ['Date: ', 'Sales'],['10-23', 0],['10-24', 0],['10-25', 0],['10-26', 100.00000 ],['10-27', 0],['10-28', 660.00000 ],['10-29', 0]

		return $order_chart;
	}
	
	public static function get_chart_for_custom_months($ord_array,$sale_start_date, $sale_end_date, $months)
	{
		$order_chart = '';
		$days = 7;
		$format = 'm-d';
		$mo= date("n", strtotime($sale_start_date));
		$moTo= date("n", strtotime($sale_end_date));
		$de = date("d", strtotime($sale_start_date));
		$y = date("Y", strtotime($sale_start_date));
		$monthArray = array();
		$dateChart = '';
		$startdate = $sale_start_date;//date("Y-m-d", strtotime(date("Y") . "-1-01"));

		for ($m = $mo; $m <= $moTo; $m++)
		{
    		$monthArray[] = date('y-m',  mktime(0, 0, 0, $m, 1, $y));
     	}

		//$monthArray = array_reverse($monthArray);

		for ($j = 0;$j < count($monthArray);$j++)
		{
			if ($j == 0)
			{
				$order_chart .= "['Months: ', 'Sales']";
			}

			$dateChart = $monthArray[$j];
			$priceChart = '0';

			for ($k = 0;$k < count($ord_array);$k++)
			{
				if ($monthArray[$j] == date('y-m', strtotime($ord_array[$k] -> ord_date)))
				{
					$priceChart = $ord_array[$k]->order_total . " ";
				}
			}

			$order_chart .= ",['" . $dateChart . "', " . $priceChart . "]";
		}

		return $order_chart;

	}
	
	public static function get_chart_for_custom_year($ord_array,$sale_start_date, $sale_end_date)
	{
		$order_chart = '';
		$yearArray = array();
		$dateChart = '';
		$startdate = $sale_start_date; // date("Y-m-d", strtotime(date("Y") . "-1-01"));
		//$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
		$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
	    $endyear = $sale_end_date;
		$startYear = date("Y", strtotime($sale_start_date));
		$endYear = date("Y", strtotime($sale_end_date));

		for ($y = $startYear; $y <= $endYear; $y++)
		{
    		//$date_year_ago = strtotime("-" . $y . "year", strtotime(date("Y")));
	    	$yearArray[] = $y;
     	}

		//$yearArray = array_reverse($yearArray);

		for ($j = 0;$j < count($yearArray);$j++)
		{
			if ($j == 0)
			{
				$order_chart .= "['Years: ', 'Sales']";
			}

			$dateChart = $yearArray[$j];
			$priceChart = '0';

			for ($k = 0;$k < count($ord_array);$k++)
			{
				if ($yearArray[$j] == date('Y', strtotime($ord_array[$k] -> ord_date)))
				{
					$priceChart = $ord_array[$k]->order_total . " ";
				}
			}

			$order_chart .= ",['" . $dateChart . "', " . $priceChart . "]";
		}

		return $order_chart;

	}


}
?>
