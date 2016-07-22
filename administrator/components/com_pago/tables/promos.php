<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TablePromos extends JTable
{
	var $id           = null;
	var $product_id   = null;
	var $start        = null;
	var $end          = null;
	var $codition     = null;
	var $quantity_min = null;
	var $quantity_max = null;
	var $user_type    = null;
	var $value        = null;
	var $amount       = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_promos', 'id', $db);
    }
}
