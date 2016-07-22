<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class TableCoupon_rules extends JTable
{
	var $coupon_id  = null;
	var $name       = null;
	var $params     = null;
	var $discount   = null;
	var $is_percent = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
	function __construct( $db )
	{
        parent::__construct('#__pago_coupon_rules', 'coupon_id', $db);
    }
}
