<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableLocations extends JTable
{

	var $country_id = null;
	var $zone_id = null;
	var $country_name = null;
	var $country_3_code = null;
	var $country_2_code = null;
	var $publish = null;
	var $params = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_country', 'country_id', $db);
    }
}
