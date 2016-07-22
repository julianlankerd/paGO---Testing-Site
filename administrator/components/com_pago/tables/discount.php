<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class TableDiscount extends JTable
{
	var $id  					= null;
	var $rule_name     	 		= null;
	var $start_date     		= null;
	var $end_date  				= null;
	var $max_use_per_user 		= null;
	var $discount_type	 		= null;
	var $discount_amount 		= null;
	var $discount_event 		= null;
	var $discount_filter 		= null;
	var $discount_filter_value 	= null;
	var $published			 	= null;
	var $priority			 	= null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
	function __construct( $db )
	{
        parent::__construct('#__pago_discount_rules', 'id', $db);
    }
}
