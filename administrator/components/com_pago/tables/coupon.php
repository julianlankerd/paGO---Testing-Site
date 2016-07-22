<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableCoupon extends JTable
{
	var $id        = null;
	var $name      = null;
	var $code      = null;
	var $start     = null;
	var $end       = null;
	var $created   = null;
	var $modified  = null;
	var $published = 0;
	var $quantity  = 0;
	var $used      = 0;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
	function __construct( $db )
	{
        parent::__construct('#__pago_coupon', 'id', $db);
    }
}
