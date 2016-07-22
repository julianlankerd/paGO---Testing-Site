<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
class modPagoTotalAvgHelper
{
	public static function averageData($sale, $startDate='', $endDate='')
	{
		// Module params
		
	
		if ($sale == 'days')
		{	
			$total_sales =modPagoTotalAvgHelper::ticker_format(Pago::get_instance('price')->format(modPagoTotalAvgHelper::total_sales('days', $startDate, $endDate)));
			$Total_sales_lbl ="Last 7 Days";
			$average_sale = modPagoTotalAvgHelper::ticker_format(Pago::get_instance('price')->format(modPagoTotalAvgHelper::average_sale('days', $startDate, $endDate)));
			$new_customers = modPagoTotalAvgHelper::ticker_format(modPagoTotalAvgHelper::new_customers('days', $startDate, $endDate));
			$abandoned = modPagoTotalAvgHelper::ticker_format(modPagoTotalAvgHelper::abandoned_carts('days', $startDate, $endDate));
		}
		elseif ($sale == 'months')
		{
			$total_sales = modPagoTotalAvgHelper::ticker_format(Pago::get_instance('price')->format(modPagoTotalAvgHelper::total_sales('months', $startDate, $endDate)));
			$Total_sales_lbl ="Last 30 Days";
			$average_sale = modPagoTotalAvgHelper::ticker_format(Pago::get_instance('price')->format(modPagoTotalAvgHelper::average_sale('months', $startDate, $endDate)));
			$new_customers = modPagoTotalAvgHelper::ticker_format(modPagoTotalAvgHelper::new_customers('months', $startDate, $endDate));
			$abandoned = modPagoTotalAvgHelper::ticker_format(modPagoTotalAvgHelper::abandoned_carts('months', $startDate, $endDate));
		}
		elseif ($sale == 'year')
		{
			$total_sales = modPagoTotalAvgHelper::ticker_format(Pago::get_instance('price')->format(modPagoTotalAvgHelper::total_sales('year', $startDate, $endDate)));
			$Total_sales_lbl ="Last Years";
			$average_sale = modPagoTotalAvgHelper::ticker_format(Pago::get_instance('price')->format(modPagoTotalAvgHelper::average_sale('year', $startDate, $endDate)));
			$new_customers = modPagoTotalAvgHelper::ticker_format(modPagoTotalAvgHelper::new_customers('year', $startDate, $endDate));
			$abandoned = modPagoTotalAvgHelper::ticker_format(modPagoTotalAvgHelper::abandoned_carts('year', $startDate, $endDate));
		}
		elseif ($sale == 'customdate')
		{
			$total_sales = modPagoTotalAvgHelper::ticker_format(Pago::get_instance('price')->format(modPagoTotalAvgHelper::total_sales('customdate', $startDate, $endDate)));	
			$Total_sales_lbl =$endDate . " To " . $startDate;	
			$average_sale = modPagoTotalAvgHelper::ticker_format(Pago::get_instance('price')->format(modPagoTotalAvgHelper::average_sale('customdate', $startDate, $endDate)));
			$new_customers = modPagoTotalAvgHelper::ticker_format(modPagoTotalAvgHelper::new_customers('customdate', $startDate, $endDate));
			$abandoned = modPagoTotalAvgHelper::ticker_format(modPagoTotalAvgHelper::abandoned_carts('customdate', $startDate, $endDate));
		}
		
		echo $total_sales . ":" . $Total_sales_lbl . ":" .$average_sale . ":" .$new_customers . ":" .$abandoned ;
		exit;
	}

	public static function average($sale, $startDate, $endDate){
		// Module params
		$total_sales = self::ticker_format(Pago::get_instance('price')->format(self::total_sales('months', $startDate, $endDate)));
		$Total_sales_lbl =JText::_('PAGO_LAST_30_DAYS');
		
		//$total_sales_percent = self::total_sales_percent();
		$average_sale = self::ticker_format(Pago::get_instance('price')->format(self::average_sale('months', $startDate, $endDate)));
		//$average_percent = self::average_percent();
		$new_customers = self::ticker_format(self::new_customers('months', $startDate, $endDate));
		//$new_customers_percent = self::new_customers_percent();
		$abandoned = self::ticker_format(self::abandoned_carts('months', $startDate, $endDate));
		//$abandoned_percent = self::abandoned_carts_percent();
		
		
		?>

        <div class="pg-row pg-mb-20 pg-totals-averages">
            <div class="pg-col-3">
            	<div class = "pg-pad-20 pg-white-bckg pg-border pg-clear">
            		<div class = "total-sales-ico"></div>
            		<div class = "pg-totals-averages-info" id="total-recent-sales-div">
	                    <span class = "totals-averages-heading" id="total-recent-sales"><?php echo $total_sales; ?></span>
	                    <span class = "totals-averages-title"><?php echo JText::_('PAGO_TOTAL_RECENT_SALES'); ?></span>
	                    <span class = "totals-averages-last-days" id="total-recent-sales-lbl"><?php echo $Total_sales_lbl; ?></span>
                    </div>
					<div id="totalRecentSelloader" style="display:none;"><img src="<?php echo JURI::root() ?>components/com_pago/images/loadingAnimation.gif" /></div>

                    <!--<?php //if ($total_sales_percent):?>
                        <div class="pg-percentage">
                            <span class="pg-<?php //echo ($total_sales_percent < 100) ? 'down' : 'up';?> pg-percent"><?php //echo $total_sales_percent; ?>%</span>
                            <span class="pg-range"><?php //echo JText::_('PAGO_LAST_SEVEN_DAYS'); ?></span>
                        </div>
                    <?php //endif;?>-->
                </div>
            </div>

            <div class="pg-col-3">
            	<div class = "pg-pad-20 pg-white-bckg pg-border pg-clear">
            		<div class = "average-sales-ico"></div>
            		<div class = "pg-totals-averages-info">
	                    <span class = "totals-averages-heading" id="total-avg-sales"><?php echo $average_sale; ?></span>
	                    <span class = "totals-averages-title"><?php echo JText::_('PAGO_AVG_SALE_AMT'); ?></span>
	                    <span class = "totals-averages-last-days" id="total-avg-sales-lbl"><?php echo JText::_('PAGO_LAST_30_DAYS'); ?></span>
                    </div>
					<div id="totalAvgLoader" style="display:none;"><img src="<?php echo JURI::root() ?>components/com_pago/images/loadingAnimation.gif" /></div>
                   
                </div>
            </div>

            <div class="pg-col-3">
           		<div class = "pg-pad-20 pg-white-bckg pg-border pg-clear">
           			<div class = "new-customers-ico"></div>
           			<div class = "pg-totals-averages-info" id="new_customer-count-div">
	                    <span class = "totals-averages-heading" id="new_customer-count"><?php echo $new_customers; ?></span>
	                    <span class = "totals-averages-title"><?php echo JText::_('PAGO_NEW_CUSTOMERS'); ?></span>
	                    <span class = "totals-averages-last-days" id="new_customer-count-lbl"><?php echo JText::_('PAGO_LAST_30_DAYS'); ?></span>
                    </div>
                    <div id="newCustomerloader" style="display:none;"><img src="<?php echo JURI::root() ?>components/com_pago/images/loadingAnimation.gif" /></div>
                   
                </div>
            </div>

            <div class="pg-col-3">
            	<div class = "pg-pad-20 pg-white-bckg pg-border pg-clear">
            		<div class = "abandoned-carts-ico"></div>
            		<div class = "pg-totals-averages-info" id="abandoned-cart-avg-div">
	                    <span class = "totals-averages-heading" id="abandoned-cart-avg"><?php echo $abandoned; ?></span>
	                    <span class = "totals-averages-title"><?php echo JText::_('PAGO_ABANDONED_CARTS'); ?></span>
	                    <span class = "totals-averages-last-days" id="abandoned-cart_avg-lbl"><?php echo JText::_('PAGO_LAST_30_DAYS'); ?></span>
                    </div>
                    <div id="abandonedCartloader" style="display:none;"><img src="<?php echo JURI::root() ?>components/com_pago/images/loadingAnimation.gif" /></div>
                   
                </div>
            </div>
        </div>
	<?php

	
	}
	


	public static function abandoned_carts($sales, $startDate, $endDate)
	{
		$db = JFactory::getDBO();
				
		if($sales == "months")
		{
			$sql = "SELECT COUNT(DATE_FORMAT(cdate, '%m-%d-%Y')) as carts
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() + INTERVAL 1 DAY)
				AND (order_status = '' OR order_status = 'P')";
		}
		else if($sales == "days")
		{
			$sql = "SELECT COUNT(DATE_FORMAT(cdate, '%m-%d-%Y')) as carts
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() + INTERVAL 1 DAY)
				AND (order_status = '' OR order_status = 'P')";
		}
		else if($sales == "year")
		{
			$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
			$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
			$endyear = date("Y-m-d", $date_six_year_ago);
			$sql = "SELECT COUNT(DATE_FORMAT(cdate, '%m-%d-%Y')) as carts FROM #__pago_orders WHERE (cdate BETWEEN  '" . $endyear . "' AND '" . $startyear . "') AND (order_status = '' OR order_status = 'P')";
		}
		else if($sales == "customdate")
		{
			$sql = "SELECT COUNT(DATE_FORMAT(cdate, '%m-%d-%Y')) as carts FROM #__pago_orders WHERE (cdate BETWEEN  '" . $endDate . "' AND '" . $startDate . "') AND (order_status = '' OR order_status = 'P')";
		}

		$db->setQuery($sql);

		return $db->loadResult();
	}



	public static function abandoned_carts_percent()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT COUNT(DATE_FORMAT(cdate, '%m-%d-%Y')) as carts
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE() - INTERVAL 7 DAY)
				AND (order_status = '' OR order_status = 'P')";

		$db->setQuery($sql);
		$last_week = $db->loadResult();

		$this_week = self::abandoned_carts(7);

		if ($this_week == 0 || $last_week == 0)
		{
			return false;
		}

		$percent = $this_week / $last_week;
		$percent = number_format($percent * 100, 0);

		return $percent;
	}

	public static function new_customers($sales, $startDate, $endDate)
	{
		$db = JFactory::getDBO();

		// Find orders
		if($sales == "months")
		{
			$sql = "SELECT COUNT(DISTINCT o.user_id) as customers
				FROM #__pago_orders AS o
				LEFT JOIN #__users AS u ON o.user_id = u.id
				WHERE (u.registerDate BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()+ INTERVAL 1 DAY)
					AND (order_status = 'C')";
		}
		else if($sales == "days")
		{
			$sql = "SELECT COUNT(DISTINCT o.user_id) as customers
				FROM #__pago_orders AS o
				LEFT JOIN #__users AS u ON o.user_id = u.id
				WHERE (u.registerDate BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE()+ INTERVAL 1 DAY)
					AND (order_status = 'C')";
		}
		else if($sales == "year")
		{
			$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
			$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
			$endyear = date("Y-m-d", $date_six_year_ago);
			$sql = "SELECT COUNT(DISTINCT o.user_id) as customers FROM #__pago_orders AS o LEFT JOIN #__users AS u ON o.user_id = u.id WHERE (u.registerDate BETWEEN  '" . $endyear . "' AND '" . $startyear . "') AND (order_status = 'C')";
		}
		else if($sales == "customdate")
		{
			$sql = "SELECT COUNT(DISTINCT o.user_id) as customers FROM #__pago_orders AS o LEFT JOIN #__users AS u ON o.user_id = u.id WHERE (u.registerDate BETWEEN  '" . $endDate . "' AND '" . $startDate . "') AND (order_status = 'C')";
		}
		
		
		$db->setQuery($sql);

		return $db->loadResult();
	}

	public static function new_customers_percent()
	{
		$db = JFactory::getDBO();

		// Find orders
		$sql = "SELECT COUNT(DISTINCT o.user_id) as customers
			FROM #__pago_orders AS o
			LEFT JOIN #__users AS u ON o.user_id = u.id
			WHERE (u.registerDate BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE() - INTERVAL 7 DAY)
				AND (order_status = 'C')";
		$db->setQuery($sql);
		$last_week = $db->loadResult();

		$this_week = self::new_customers(7);

		if ($this_week == 0 || $last_week == 0)
		{
			return false;
		}

		$percent = $this_week / $last_week;
		$percent = number_format($percent * 100, 0);

		return $percent;
	}

	public static function average_percent()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT AVG( order_total ) as average
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE() - INTERVAL 7 DAY)
				AND (order_status = 'C')";

		$db->setQuery($sql);
		$last_week = $db->loadResult();

		$this_week = self::average_sale(7);

		if ( $this_week == 0 || $last_week == 0 )
		{
			return false;
		}

		$percent = $this_week / $last_week;
		$percent = number_format($percent * 100, 0);

		return $percent;
	}

	public static function average_sale($sales, $startDate, $endDate)
	{
		$db = JFactory::getDBO();

		if($sales == "months")
		{
			$sql = "SELECT AVG( order_total ) as average FROM #__pago_orders WHERE cdate > DATE_SUB( NOW( ) , INTERVAL 30 DAY) AND (order_status = 'C')";

		}
		else if($sales == "days")
		{
			$sql = "SELECT AVG( order_total ) as average FROM #__pago_orders WHERE cdate > DATE_SUB( NOW( ) , INTERVAL 7 DAY) AND (order_status = 'C')";
		}
		else if($sales == "year")
		{
			$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
			$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
			$endyear = date("Y-m-d", $date_six_year_ago);
			$sql = "SELECT AVG( order_total ) as average FROM #__pago_orders WHERE cdate BETWEEN '" . $endyear . "' AND '" . $startyear . "' AND (order_status = 'C')";
		}
		else if($sales == "customdate")
		{
			$sql = "SELECT AVG( order_total ) as average FROM #__pago_orders WHERE cdate BETWEEN '" . $endDate . "' AND '" . $startDate . "' AND (order_status = 'C')";
		}
		
		$db->setQuery($sql);
		$average = $db->loadResult();

		return $average;
	}

	public static function total_sales_percent()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT SUM(order_total) as total
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE() - INTERVAL 7 DAY)
				AND (order_status = 'C')";

		$db->setQuery($sql);
		$last_week = $db->loadResult();

		$this_week = self::total_sales(7);

		if ( $this_week == 0 || $last_week == 0 )
		{
			return false;
		}

		$percent = $this_week / $last_week;
		$percent = number_format($percent * 100, 0);

		return $percent;
	}

    public static function ticker_format( $number )
	{

		$numbers = str_split($number);
		$formatted = '<div class="pg-ticker">';

		foreach ($numbers as $tmp)
		{
			if (preg_match('/^[a-z0-9]+$/i', $tmp))
			{
				$formatted .= '<span><span class="pg-bevel"></span>' . $tmp . '</span>';
			}
			else
			{
				$formatted .= $tmp;
			}
		}

		$formatted .= '</div>';

		return $formatted;
	}

	public static function total_sales($sales, $startDate, $endDate)
	{
		$db = JFactory::getDBO();
		
		if($sales == "months")
		{
			$sql = "SELECT SUM( order_total ) AS total FROM #__pago_orders WHERE cdate > DATE_SUB( NOW( ) , INTERVAL 30 DAY) AND (order_status = 'C')";
		}
		else if($sales == "days")
		{
			$sql = "SELECT SUM( order_total ) AS total FROM #__pago_orders WHERE cdate > DATE_SUB( NOW( ) , INTERVAL 7 DAY) AND (order_status = 'C')";
		}
		else if($sales == "year")
		{
			$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
			$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
			$endyear = date("Y-m-d", $date_six_year_ago);
			$days = 7;
			$sql = "SELECT SUM( order_total ) as total FROM #__pago_orders WHERE cdate BETWEEN '" . $endyear . "' AND '" . $startyear . "' AND (order_status = 'C')";
		}
		else if($sales == "customdate")
		{
			$sql = "SELECT SUM( order_total ) as total FROM #__pago_orders WHERE cdate BETWEEN '" . $endDate . "' AND '" . $startDate . "' AND (order_status = 'C')";
		}


		$db->setQuery($sql);
		$total = $db->loadResult();

		return $total;
	}
}
