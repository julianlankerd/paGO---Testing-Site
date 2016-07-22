<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableTax extends JTable
{
    var $pgtax_id = null;
    var $pgtax_rate_name = null;
    var $pgtax_class_id = 0;
    var $pgtax_country = null;
    var $pgtax_state= null;
    var $pgzip_from = null;
    var $pgzip_to = null;
    var $pgtax_enable = 0;
    var $pgtax_rate = null;
    var $pgtax_apply_on_shipping = 0;
    var $priority = 0;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db )
    {
        parent::__construct('#__pago_tax_rates', 'pgtax_id', $db);
    }
}
