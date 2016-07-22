<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableOrders_items extends JTable
{
	var $order_item_id       = null;
	var $order_id            = null;
	var $item_id             = null;
	var $qty          = null;
	var $price           = null;
	var $price_type        = null;
	var $sub_recur        = null;
	var $sub_status     = null;
	var $sub_payment_data         = null;
	var $attributes      = null;
	var $order_item_tax = null;
	var $order_item_shipping   = null;
	var $order_item_shipping_tax = null;
	var $order_item_ship_method_id           = null;
	var $varation_id = null;

	/**
     * Constructor
     *
     * @param object Database connector object
     */
	function __construct( $db )
	{
		parent::__construct('#__pago_orders_items', 'order_item_id', $db);
	}

}
