<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableTaxrules extends JTable
{

 	var $id = null;
	var $name = null;
	var $applytoaddress = null;
	var $group_id = null;
	var $country_id = null;
	var $state_id = null;
	var $item_type = null;
	var $zip = null;
	var $tax = null;
	var $shipping_exempt = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_taxrules', 'id', $db);
    }
}
