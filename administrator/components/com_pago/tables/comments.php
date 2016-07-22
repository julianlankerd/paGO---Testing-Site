<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableComments extends JTable
{

    var $id = null;
    var $item_id = null;
    var $parent_id = null;
    var $created = null;
	var $published = 0;
	var $text = null;
    var $author_id = null;
    var $author_name = null;
    var $author_email = null;
	var $author_web_site = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db )
	{
        parent::__construct('#__pago_comments', 'id', $db);
    }

}
