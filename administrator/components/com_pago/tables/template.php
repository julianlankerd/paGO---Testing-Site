<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableTemplate extends JTable
{

    var $pgtemplate_id = null;
    var $pgtemplate_name = null;
    var $pgtemplate_type= null;
	var $pgtemplate_body = null;
    var $pgtemplate_enable = 0;
    var $pgtemplate_parent_type = null;
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db )
    {
        parent::__construct('#__pago_view_templates', 'pgtemplate_id', $db);
    }
}
