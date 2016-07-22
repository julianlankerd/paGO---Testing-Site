<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableCustom_shipping_rules extends JTable
{

    var $rule_id = null;
    var $rule_name = null;
    var $order_total_start = null;
    var $order_total_end = null;
	var $weight_start = 0;
	var $weight_end = null;
    var $country = null;
    var $state = null;
    var $zipcode = null;
	var $category = null;
	var $items = null;
	var $shipping_price = null;
	var $priority = null;
	var $published = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db )
	{
        parent::__construct('#__pago_custom_shipping_rules', 'rule_id', $db);
    }

}
