<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_reports
{

	function __construct(){

		$this->db = JFactory::getDBO();
	}

	function get( $method, $params=false )
	{
		if( method_exists( $this, $method ) ){
			return $this->$method( $params );
		} else {
			JError::raiseWarning( 100, JText::_( 'REPORT METHOD NOT FOUND ' ) . $method );
		}
	}

	function top_products(){

		//$this->inject_random_dates();

		$query = "SELECT orders_items.item_id AS id, items.name AS name, SUM(orders_items.qty) AS total
					FROM #__pago_orders_items AS orders_items
						LEFT JOIN #__pago_items AS items
					   		ON orders_items.item_id = items.id
					   			GROUP BY orders_items.item_id
									ORDER BY total DESC LIMIT 0,10";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		return $results;
	}

	function topten_clients(){

		$query = "SELECT users.id, users.name, SUM(orders.order_subtotal) as total
					FROM #__pago_orders AS orders
						LEFT JOIN #__users AS users
					   		ON orders.user_id = users.id
					   			GROUP BY orders.user_id
									ORDER BY total DESC LIMIT 0,10";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		return $results;
	}

	function daily_income( $params ){

		$year = date('Y', strtotime( $params['start_date_value'] ) );
		$month = date('m', strtotime( $params['start_date_value'] ) );
		$day = date('d', strtotime( $params['start_date_value'] ) );

		$query = "SELECT * FROM #__pago_orders
					WHERE month( cdate ) = $month AND year( cdate ) = $year AND day( cdate ) = $day
						ORDER BY cdate";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		$return = array();

		for($m = 1;$m <= 24; $m++){

			$return[ $m ] = 0;
		}
//print_r($return);die();
		foreach( $results as $result ){

			$day = date('G', strtotime( $result->cdate ) );

			$amount = $return[ $day ] + $result->order_subtotal;

			//number_format($number, 2, '.', '')
			$return[ $day ] = number_format($amount, 2, '.', '');
		}
		//print_r($return);die();
		return $return;
	}

	function monthly_income( $params ){

		$year = date('Y', strtotime( $params['start_date_value'] ) );
		$month = date('m', strtotime( $params['start_date_value'] ) );

		$query = "SELECT * FROM #__pago_orders
					WHERE month( cdate ) = $month AND year( cdate ) = $year
						ORDER BY cdate";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		$return = array();

		for($m = 1;$m <= 31; $m++){
			$month = substr( date("F", mktime(0, 0, 0, $m) ) , 0, 3);

			$return[ $m ] = 0;
		}
//print_r($return);die();
		foreach( $results as $result ){

			$day = date('j', strtotime( $result->cdate ) );

			$amount = $return[ $day ] + $result->order_subtotal;

			//number_format($number, 2, '.', '')
			$return[ $day ] = number_format($amount, 2, '.', '');
		}

		return $return;
	}

	function annual_income( $params ){

		$year = date('Y', strtotime( $params['start_date_value'] ) );

		$query = "SELECT * FROM #__pago_orders
					WHERE year( cdate ) = $year
						ORDER BY cdate";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		$return = array();

		for($m = 1;$m <= 12; $m++){
			$month = substr( date("F", mktime(0, 0, 0, $m) ) , 0, 3);

			$return[ $month ] = 0;
		}

		foreach( $results as $result ){

			$month = substr( date('F', strtotime( $result->cdate ) ) , 0, 3);

			$return[ $month ] = $return[ $month ] + $result->order_subtotal;
		}

		//print_r($return);die();
		return $return;
	}

	function inject_random_dates(){
		$query = "SELECT * FROM #__pago_orders";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		foreach($results as $result){
			$time = rand( strtotime("Jan 01 2010"), strtotime("feb 28 2011") );
			$date = date('Y-m-d H:i:s', $time);

				$query = "UPDATE #__pago_orders SET cdate='{$date}', mdate='{$date}' WHERE order_id={$result->order_id}";
				$this->db->setQuery($query);
				$result = $this->db->query();

		}
	}


	function items_category( $params ){

		$query = "SELECT
					cats.name AS name,
					cats.id AS id
					FROM #__pago_categories_items AS cats_items
						LEFT JOIN #__pago_categories AS cats
							ON cats.id = cats_items.category_id";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		foreach( $results as $result ){
			if( !isset( $res_index[ $result->id ] ) ) $res_index[ $result->id ] = 0;

			$res_index[ $result->id ] = $res_index[ $result->id ] + 1;
		}

		foreach( $results as $result ){
			$result->item_count = $res_index[ $result->id ];
			$res_array[ $result->name ] = $result;
		}

		return $res_array;
	}
}
