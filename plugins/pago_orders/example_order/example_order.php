<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die();

class plgPago_OrdersExample_Order extends JPlugin
{
	/* Gets called whan a new one-off order is placed
	 *
	 * @param int $order_id The id of the new order
	 */
	public function on_new_order( $order_id )
	{
	}

	/* Gets called when a new subscription order is placed
	 *
	 * @param int $order_id The id of the new order
	 */
	public function on_new_order_subscription( $order_id )
	{
	}
}
