<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableAttributes extends JTable
{

	var $id         = null;
	var $name       = null;
	var $type       = null;
	var $order      = null;
	var $created    = null;
	var $modified   = null;
	var $pricing    = null;
	var $searchable = null;
	var $ordering   = null;
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_attr', 'id', $db);
    }
}
