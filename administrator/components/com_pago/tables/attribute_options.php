<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableAttribute_Options extends JTable
{

	var $id = null;
	var $attr_id = null;
	var $type = null;
    var $name = null;
    var $color = null;
    var $size = null;
    var $size_type = null;
    var $ordering = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_attr_opts', 'id', $db);
    }
}
