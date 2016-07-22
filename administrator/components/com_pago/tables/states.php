<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableStates extends JTable
{

	var $state_id = null;
	var $country_id = null;
	var $state_name = null;
	var $state_3_code = null;
	var $state_2_code = null;
	var $publish = null;
	var $params = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_country_state', 'state_id', $db);
    }
}
