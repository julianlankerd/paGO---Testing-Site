<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableProduct_Varation extends JTable
{

	var $id = null;
	var $item_id = null;
    var $name = null;
    var $price_type = null;
    var $price = null;
    var $sku = null;
    var $qty_limit = null;
    var $qty = null;
    var $published = null;
    var $expiry_date = null;
    var $var_enable  = 0;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_product_varation', 'id', $db);
    }
}
