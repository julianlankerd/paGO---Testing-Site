<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableCategories extends JTable
{
	//public $group_id, $name, $description, $params, $isdefault, $created, $modified;
	public $id, $path, $n_order, $name, $description, $thumb_image, $full_image, $created, $modified, $published, $params;
	/**
     * Constructor
     *
     * @param object Database connector object
     */
	function __construct( $db ) {
		parent::__construct('#__pago_categories', 'id', $db);
	}

	/**
	 * Overloaded check function
	 */
	function check()
	{
		if ( empty( $this->alias ) ) {
			$this->alias = $this->name;
		}
		$this->alias = JFilterOutput::stringURLSafe( $this->alias );

		if ( trim( str_replace( '-', '', $this->alias ) ) == '' ) {
			$datenow = JFactory::getDate();
			$this->alias = $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
		}

		return true;
	}
}
