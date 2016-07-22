<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableShippingrule extends JTable
{

    var $rule_id = null;
    var $rule_name = null;
    var $category= null;
	var $items = null;
    var $country = null;
    var $state = null;
    var $zipcode = null;
	var $order_total_start = 0;
	var $order_total_end = 0;
	var $weight_start = 0;
    var $weight_end = 0;
    var $shipping_price = 0;
    var $published = 0;
    var $priority = 0;
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
