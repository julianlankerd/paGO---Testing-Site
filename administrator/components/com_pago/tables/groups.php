<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableGroups extends JTable
{
	public $group_id, $name, $description, $params, $isdefault, $created, $modified;

	/**
     * Constructor
     *
     * @param object Database connector object
     */
	function __construct( $db ) {
		parent::__construct('#__pago_groups', 'group_id', $db);
	}
}
