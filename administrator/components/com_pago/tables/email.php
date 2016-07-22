<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableEmail extends JTable
{

    var $pgemail_id = null;
    var $pgemail_name = null;
    var $pgemail_type= null;
	var $pgemail_body = null;
    var $pgemail_enable = 0;
    var $template_for = null;
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db )
    {
        parent::__construct('#__pago_mail_templates', 'pgemail_id', $db);
    }
}
