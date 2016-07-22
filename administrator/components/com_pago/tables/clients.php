<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableClients extends JTable
{

	var $id = null;
	var $user_id = null;
	var $address_type = null;
	var $address_type_name = null;
	var $company = null;
	var $title = null;
	var $last_name = null;
	var $first_name = null;
	var $middle_name = null;
	var $phone_1 = null;
	var $phone_2 = null;
	var $fax = null;
	var $address_1 = null;
	var $address_2 = null;
	var $city = null;
	var $state = null;
	var $country = null;
	var $zip = null;
	var $user_email = null;
	var $cdate = null;
	var $mdate = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_user_info', 'id', $db);
    }
}
