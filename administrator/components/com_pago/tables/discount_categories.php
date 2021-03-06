<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class TableDiscount_categories extends JTable
{
	var $id  					= null;
	var $discount_rule_id 	 	= null;
	var $category_id     		= null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
	function __construct( $db )
	{
        parent::__construct('#__pago_discount_categories', 'id', $db);
    }
}
