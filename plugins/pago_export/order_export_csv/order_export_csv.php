<?php

/**

 * @package paGO Commerce

 * @author 'corePHP', LLC

 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce

 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

**/

defined('_JEXEC') or die();

class plgPago_ExportOrder_Export_CSV extends JPlugin
{

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/* Gets called when a export button is clicked

	 *

	 * @param int $order_id The id of the new order

	 */

	public function on_order_export_csv( $context, $order_id )
	{
		$exportfilename = "pago_order_export.csv";
		$db = JFactory::getDBO();
		
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

		
		echo "Order id, Item Name, Item SKU, Item Price, Item Quanity,";
		echo "Biling Name, Billing Address, Billing phone1, Billing phone2, Billing City, Billing Zip,Billing state, Billing country,";
		echo "Shipping Name, Shipping Address, Shipping phone1, Shipping phone2, Shipping city, Shipping Zip, Shipping State, Shipping Country,";
		echo "cdate, Order subtotal, Order total, Order tax, Order shipping, Order discount, Order currency, Payment gateway, Order status";
		echo "\n";
			
			
		for ( $i = 0 ; $i < count ($order_id); $i++ ) 
		{
			
			if($order_id[$i]->order_id!="")
			{
				$order_info  = Pago::get_instance('orders')->get($order_id[$i]->order_id);
				
				
				for($g=0;$g<count($order_info['items']);$g++)
				{
					$fullname_billing = strtoupper($order_info['addresses']['billing']->first_name) . " " . strtoupper($order_info['addresses']['billing']->middle_name) . " " .strtoupper($order_info['addresses']['billing']->last_name);
				
				$fullname_shipping = strtoupper($order_info['addresses']['shipping']->first_name) . " " . strtoupper($order_info['addresses']['shipping']->middle_name) . " " . strtoupper($order_info['addresses']['shipping']->last_name);
				
				$query_ship_state = 'SELECT state_2_code FROM #__pago_country_state where state_name = "' . $order_info['addresses']['shipping']->state . '"';
				$shipping_state_name = $db->setQuery( $query_ship_state )->loadResult();
				
				$query_bil_state = 'SELECT state_2_code FROM #__pago_country_state where state_name = "' . $order_info['addresses']['billing']->state . '"';
				$billing_state_name = $db->setQuery( $query_bil_state )->loadResult();
										
					echo $order_id[$i]->order_id . ',';
					echo '"' . $order_info['items'][$g]->name . '","' . $order_info['items'][$g]->sku .'","'. $order_info['items'][$g]->price . '",'. $order_info['items'][$g]->qty .',';
					echo '"'. $fullname_billing . '","' . $order_info['addresses']['billing']->address_1 . '","' . $order_info['addresses']['billing']->phone_1 . '","' . $order_info['addresses']['billing']->phone_2 . '","' . $order_info['addresses']['billing']->city . '","' . $order_info['addresses']['billing']->zip . '","' . $billing_state_name  . '","' . $order_info['addresses']['billing']->country  . '",';
					echo '"'.$fullname_shipping.'","' . $order_info['addresses']['shipping']->address_1 . '","' . $order_info['addresses']['shipping']->phone_1 . '","' . $order_info['addresses']['shipping']->phone_2 . '","' . $order_info['addresses']['shipping']->city . '","' . $order_info['addresses']['shipping']->zip . '","' . $shipping_state_name  . '","' . $order_info['addresses']['shipping']->country  . '",';
					echo '"'. $order_info['details']->cdate . '","' . $order_info['details']->order_subtotal . '","' . $order_info['details']->order_total . '","' . $order_info['details']->order_tax . '","' . $order_info['details']->order_shipping  . '","' . $order_info['details']->order_discount  . '","' . $order_info['details']->order_currency  . '","' . $order_info['details']->payment_gateway  . '","' . $order_info['details']->order_status  . '",';
					echo "\r\n"; 
					
				}
			}			
		}
		
		exit();				

	}
}





