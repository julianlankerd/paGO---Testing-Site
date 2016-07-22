<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableOrders extends JTable
{

	var $order_id            = null;
	var $user_id             = null;
	var $user_email          = null;
	var $vendor_id           = null;
	var $order_number        = null;
	var $user_info_id        = null;
	var $payment_gateway     = null;
	var $order_total         = null;
	var $order_subtotal      = null;
	var $order_refundtotal   = null;
	var $order_tax           = null;
	var $order_tax_details   = null;
	var $order_shipping      = null;
	var $order_shipping_tax  = null;
	var $coupon_discount     = null;
	var $coupon_code         = null;
	var $order_discount      = null;
	var $order_currency      = null;
	var $order_status        = null;
	var $cdate               = null;
	var $mdate               = null;
	var $ship_method_id      = null;
	var $customer_note       = null;
	var $ip_address          = null;
	var $ipn_dump            = null;
	var $payment_message     = null;

	/**
     * Constructor
     *
     * @param object Database connector object
     */
	function __construct( $db )
	{
		parent::__construct('#__pago_orders', 'order_id', $db);
	}

	/**
	 * @overload
	 *
	 */
	function load( $oid = null, $reset = true )
	{
		if ( parent::load( $oid, $reset ) ) {
			$user = JFactory::getUser( $this->user_id );
			$this->user_name = $user->name;

			$this->cdate = strtotime( $this->cdate );
			$this->mdate = strtotime( $this->mdate );
			$this->cdate = date( 'd/m/Y H:i:s', $this->cdate );
			$this->mdate = date( 'd/m/Y H:i:s', $this->mdate );

			return true;
		} else {
			$this->cdate = date( 'Y-m-d H:i:s', time() );
			$this->mdate = date( 'Y-m-d H:i:s', time() );
		}

		return false;
	}
}
