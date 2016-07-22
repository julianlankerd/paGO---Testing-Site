<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelGraph extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// List state information.
		parent::populateState('count', 'desc');
	}

	public function getPurchasedItems()
	{
		$db = Jfactory::getDBO();
		$query = "SELECT *
			FROM (
				SELECT i.id, i.name,o.price, SUM( o.qty ) AS quantity
				FROM #__pago_items AS i
				LEFT JOIN #__pago_orders_items AS o ON i.id = o.item_id
				LEFT JOIN #__pago_orders AS os ON os.order_id = o.order_id
				WHERE os.order_status='C' GROUP BY i.id
			) AS q
			WHERE q.quantity IS NOT NULL
			ORDER BY q.quantity DESC
			LIMIT 0,10";
		$db->setQuery($query);
		$items = $db->LoadObjectList();

		return $items;
	}
	
	public function getUnpurchasedItems($purchasedItem)
	{
		$itemdIds = array();
		
		for($p = 0; $p < count($purchasedItem); $p++)
		{
			$itemdIds[] =  $purchasedItem[$p]->id;
		}
		
		$itemidStr = implode("','", $itemdIds);
		$db = Jfactory::getDBO();
		$query = "SELECT i.id, i.name,i.price
				FROM #__pago_items AS i
				WHERE i.id NOT IN('" . $itemidStr . "')
			ORDER BY i.id DESC
			LIMIT 0,10";
		$db->setQuery($query);
		$items = $db->LoadObjectList();
		
		return $items;
	}
	
	public function getRevenueDetails($revenue_start_date, $revenue_end_date)
	{

		$db = Jfactory::getDBO();
		$query = "SELECT sum(order_total) as total,sum(order_subtotal) as subtotal FROM #__pago_orders WHERE cdate BETWEEN '" . $revenue_start_date . "' AND '" . $revenue_end_date . "' AND (order_status = 'C')";
		$db->setQuery($query);
		$items = $db->LoadObjectList();
		
		return $items;
	}
	
	public function getOrderAverages($revenue_start_date, $revenue_end_date)
	{

		$db = Jfactory::getDBO();
		$query = "SELECT AVG(order_total) as total,AVG(order_subtotal) as subtotal FROM #__pago_orders WHERE cdate BETWEEN '" . $revenue_start_date . "' AND '" . $revenue_end_date . "' AND (order_status = 'C')";
		$db->setQuery($query);
		$items = $db->LoadObjectList();
		
		return $items;
	}
	
	public function getKeywords()
	{
		//$orderDirn	= $this->state->get('list.direction');
		$orderDirn	=JFactory::getApplication()->input->get('filter_order_Dir');
		
		if(!$orderDirn)
		{
			$orderDirn = 'desc';
		}
		
		$db = Jfactory::getDBO();
		$query = "SELECT * FROM #__pago_search_keywords WHERE 1=1 ORDER BY count " . $orderDirn . " LIMIT 0,50";
		$db->setQuery($query);
		$keywords = $db->LoadObjectList();

		return $keywords;
	}
	
	public function exportData($exportVar, $total, $subtotal, $startdt, $enddt)
	{


		/* Set the oago export filename */
		$exportfilename = 'pago_' . $exportVar . '.csv';

		/* Start output to the browser */
		if (preg_match('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "Opera";
		}
		elseif (preg_match('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "IE";
		}
		else
		{
			$UserBrowser = '';
		}

		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';

		/* Clean the buffer */
		ob_clean();

		header('Content-Type: ' . $mime_type);
		header('Content-Encoding: UTF-8');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

		if ($UserBrowser == 'IE')
		{
			header('Content-Disposition: inline; filename="' . $exportfilename . '"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			header('Content-Disposition: attachment; filename="' . $exportfilename . '"');
			header('Pragma: no-cache');
		}

		switch ($exportVar)
		{
			case 'purchaseItem':
				$this->loadPurchasedItems();
				break;
			case 'unpurchaseItem':
				$this->loadUnPurchasedItems();
				break;
			case 'revenue':
				$this->loadRevenue($total, $subtotal, $startdt, $enddt);
				break;
			case 'avgorder':
				$this->loadAverageOrder($total, $subtotal, $startdt, $enddt);
				break;
			case 'keywords':
				$this->loadKeywords();
				break;
				
			case 'abandoned':
				$this->loadAbandonedCart();
				break;
		}

		exit;
	}
	
	public function loadAbandonedCart()
	{
		$items = $this->getAbandonedCart();
		echo "ID,Cart Amount, Date, User Id, User Email";
		echo "\n";
		$inc = 0;
		
		for($p = 0; $p < count($items); $p++)
		{ 
			$data = json_decode( $items[$p]->data, true );
			$email ='';
			
			if($items[$p]->user_id !=0)
			{
				$email = $this->getEmail($items[$p]->user_id);
			}
		
			if(array_key_exists('cart_0', $data))
			{
				
				if((array_key_exists('cart_' . $items[$p]->user_id, $data)) && (count($data['cart_' . $items[$p]->user_id]['items']) == 0))				
				{
					continue;
				}
				
				if(array_key_exists('cart_' . $items[$p]->user_id, $data))
				{
					$price = Pago::get_instance( 'price' )->format($data['cart_' . $items[$p]->user_id]['total']);
				}
				else
				{
					$price = Pago::get_instance( 'price' )->format($data['cart_0']['total']);
				}
				
				$temp = true;
				echo ++$inc . ',' . $price . ',"' . $items[$p]->created . '",' . $items[$p]->user_id . ',"' . $email . '"';
			} 	
		}
	}
	
	private function loadKeywords()
	{
			$keywords = $this->getKeywords();
			echo "ID,Keyword, count";
			echo "\n";
			
			 for($p = 0; $p < count($keywords); $p++)
			 {
				echo $p+1 . ',"' . $keywords[$p]->pgkeyword . '",' . $keywords[$p]->count;
				echo "\n";
			}
	}
	
	private function loadAverageOrder($total, $subtotal, $startdt, $enddt)
	{
			echo "Start Date, End Date, Average Order Total, Average Order Subtotal";
			echo "\n";
			$total = Pago::get_instance( 'price' )->format($total);
			$subtotal = Pago::get_instance( 'price' )->format($subtotal);
			$total = str_replace(',', '', $total);
			$subtotal = str_replace(',', '', $subtotal);
			echo $startdt . ',' . $enddt . ',' . $total . ',' . $subtotal;
	}
	
	private function loadRevenue($total, $subtotal, $startdt, $enddt)
	{
			echo "Start Date, End Date, Revenue Total, Revenue Subtotal";
			echo "\n";
			$total = Pago::get_instance( 'price' )->format($total);
			$subtotal = Pago::get_instance( 'price' )->format($subtotal);
			$total = str_replace(',', '', $total);
			$subtotal = str_replace(',', '', $subtotal);
			echo $startdt . ',' . $enddt . ',' . $total . ',' . $subtotal;
	}


	private function loadUnPurchasedItems()
	{

		$purchasedItem = $this->getPurchasedItems();
		$unpurchasedItems = $this->getUnpurchasedItems($purchasedItem);

		if(count($unpurchasedItems) > 0)
		{
			echo "ID,Item Name, Item Price";
			echo "\n";
			
			 for($p = 0; $p < count($unpurchasedItems); $p++)
			 {
			 	$price = Pago::get_instance( 'price' )->format($unpurchasedItems[$p]->price);
				echo $p+1 . ',"' . $unpurchasedItems[$p]->name . '",' . $price;
				echo "\n";
			}
		}
	}
	
	private function loadPurchasedItems()
	{
		$purchasedItems = $this->getPurchasedItems();

		if(count($purchasedItems) > 0)
		{
			echo "ID,Item Name, Item Price, No. of Purchased";
			echo "\n";
			
			 for($p = 0; $p < count($purchasedItems); $p++)
			 {
			 	$price = Pago::get_instance( 'price' )->format($purchasedItems[$p]->price);
				echo $p+1 . ',"' . $purchasedItems[$p]->name . '",' . $price  . ',' . $purchasedItems[$p]->quantity;
				echo "\n";
			}
		}
	}
	
	
	public function getAbandonedCart()
	{
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__pago_cookie WHERE 1=1 ORDER BY created DESC";
		$db->setQuery( $query );
		$data = $db->loadObjectList();
		return $data;
	}
	
	public function getEmail($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT email FROM #__users WHERE id=" . $id;
		$db->setQuery($query);
		$data = $db->loadResult();
		return $data;
	}
}
