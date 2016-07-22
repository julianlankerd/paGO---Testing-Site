<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');


class PagoModelDashboard extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();
	}

	public function get_recent_orders()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT DISTINCT o.order_id, o.*, u.first_name, u.last_name
			FROM #__pago_orders AS o
			LEFT JOIN #__pago_orders_addresses AS u ON o.order_id = u.order_id  WHERE u.address_type ='b'
				AND o.order_status != 'A' ORDER BY o.order_id DESC LIMIT 5";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function new_customers( $days = 30 )
	{
		$db = JFactory::getDBO();

		//Find orders 
		$sql = "SELECT COUNT(DISTINCT o.user_id) as customers
			FROM #__pago_orders AS o
			LEFT JOIN #__users AS u ON o.user_id = u.id
			WHERE (u.registerDate BETWEEN CURDATE() - INTERVAL $days DAY AND CURDATE()) 
				AND (order_status = 'C')";

		$db->setQuery( $sql );
		return $db->loadResult();
	}

	public function new_customers_percent( )
	{
		$db = JFactory::getDBO();

		//Find orders 
		$sql = "SELECT COUNT(DISTINCT o.user_id) as customers
			FROM #__pago_orders AS o
			LEFT JOIN #__users AS u ON o.user_id = u.id
			WHERE (u.registerDate BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE() - INTERVAL 7 DAY) 
				AND (order_status = 'C')";

		$db->setQuery( $sql );
		$last_week = $db->loadResult();

		$this_week = $this->new_customers( 7 );

		if ($this_week == 0 || $last_week == 0){ return false;}

		$percent = $this_week / $last_week;
		$percent = number_format($percent * 100, 0);

		return $percent;
	}

	public function abandoned_carts( $days = 30 )
	{
		$db = JFactory::getDBO();

		$sql = "SELECT COUNT(DATE_FORMAT(cdate, '%m-%d-%Y')) as carts
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL $days DAY AND CURDATE()) 
				AND (order_status = '' OR order_status = 'P')";

		$db->setQuery( $sql );

		return $db->loadResult();
	}

	public function abandoned_carts_percent()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT COUNT(DATE_FORMAT(cdate, '%m-%d-%Y')) as carts
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE() - INTERVAL 7 DAY) 
				AND (order_status = '' OR order_status = 'P')";

		$db->setQuery( $sql );
		$last_week = $db->loadResult();

		$this_week = $this->abandoned_carts( 7 );

		if ($this_week == 0 || $last_week == 0){ return false;}

		$percent = $this_week / $last_week;
		$percent = number_format($percent * 100, 0);

		return $percent;
	}

	public function average_sale( $days = 30 )
	{
		$db = JFactory::getDBO();

		$sql = "SELECT AVG( order_total ) as average
			FROM #__pago_orders
			WHERE ( cdate BETWEEN CURDATE() - INTERVAL $days DAY AND CURDATE() ) 
				AND ( order_status = 'C' )";

		$db->setQuery( $sql );
		$average = $db->loadResult();
		
		return $average;
	}

	public function average_percent()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT AVG( order_total ) as average
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE() - INTERVAL 7 DAY) 
				AND (order_status = 'C')";

		$db->setQuery( $sql );
		$last_week = $db->loadResult();

		$this_week = $this->average_sale( 7 );

		if ( $this_week == 0 || $last_week == 0 ){ return false; }

		$percent = $this_week / $last_week;
		$percent = number_format($percent * 100, 0);

		return $percent;
	}

	public function total_sales( $days = 30 )
	{
		$db = JFactory::getDBO();

		$sql = "SELECT SUM( order_total ) as total
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL $days DAY AND CURDATE()) 
				AND (order_status = 'C')";

		$db->setQuery( $sql );
		$total = $db->loadResult();
		
		return $total;
	}

	public function total_sales_percent()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT SUM(order_total) as total
			FROM #__pago_orders
			WHERE (cdate BETWEEN CURDATE() - INTERVAL 14 DAY AND CURDATE() - INTERVAL 7 DAY) 
				AND (order_status = 'C')";

		$db->setQuery( $sql );
		$last_week = $db->loadResult();
		
		$this_week = $this->total_sales( 7 );

		if ( $this_week == 0 || $last_week == 0 ){ return false; }

		$percent = $this_week / $last_week;
		$percent = number_format($percent * 100, 0);

		return $percent;
	}

	public function best_selling_items()
	{
		$db = JFactory::getDBO();

		//this gathers all items in orders but we might
		//want to modify it to list only items that have actually been paid for
		$sql = "SELECT *
			FROM (
				SELECT i.id, i.name, SUM( o.qty ) AS quantity
				FROM #__pago_items AS i
				LEFT JOIN #__pago_orders_items AS o ON i.id = o.item_id
				GROUP BY i.id, i.name
			) AS q
			WHERE q.quantity IS NOT NULL
			ORDER BY q.quantity DESC
			LIMIT 5";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}
	public function removeExpiredData(){
		$db = JFactory::getDBO();

		$currentTimestamp = time();

		$sql = "SELECT `id` FROM `#__pago_categoriesi`
				WHERE `visibility` = 0 AND `expiry_date` < {$currentTimestamp} ";

		$db->setQuery( $sql );

		$expiredCategoriesi = $db->loadObjectList();

		$catTable = JTable::getInstance( 'categoriesi', 'Table' );
		$fileModel = JModelLegacy::getInstance( 'File', 'PagoModel' );
		$itemModel = JModelLegacy::getInstance( 'Items', 'PagoModel' );

		if ( !empty( $expiredCategoriesi ) ) {
			foreach ( $expiredCategoriesi as $exCategory ){
				if ( $catTable->can_delete( $exCategory->id ) ) {
					// delete category items
					$itemModel->deleteItemsByCategory($exCategory->id);

					//delete category images
					$fileModel->delete_all( $exCategory->id , "category");
					
					//delete category Relations
					$catTable->deleteRelations( $exCategory->id );

					$catTable->delete( $exCategory->id, true );
				}
			}
		}

		$sql = "SELECT `id` FROM `#__pago_items`
				WHERE `visibility` = 0 AND `expiry_date` < {$currentTimestamp} ";

		$db->setQuery( $sql );

		$expiredItems = $db->loadObjectList();

		if ( !empty( $expiredItems ) ) {
			$ids = array();
			foreach ( $expiredItems as $exItem ){
					$ids[] = $exItem->id;				
			}
			$itemModel->deleteItems($ids);
		}

	}

}