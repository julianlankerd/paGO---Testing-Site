<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableTax_class extends JTable
{

    var $pgtax_class_id = null;
    var $pgtax_class_name = null;
    var $pgtax_class_enable = 0;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db )
    {
        parent::__construct('#__pago_tax_class', 'pgtax_class_id', $db);
    }
}
