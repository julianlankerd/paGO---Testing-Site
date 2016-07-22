<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableItem_types extends JTable
{
	public $id                = null;
	public $name              = '';
	public $published         = null;
	public $default		      = null;


	/**
	* Constructor
	*
	* @param object Database connector object
	*/
	function __construct( $db )
	{
		parent::__construct( '#__pago_item_types', 'id', $db );
	}

	/**
	 * Overloaded check function
	 */
	function check()
	{
		if ( trim( $this->name ) == '' ) {
			$this->setError( JText::_('PAGO_ITEM_TYPES_ERROR_NO_NAME') );
			return false;
		}
		return true;
	}
	public function search_tag($word)
    {
        $query = "SELECT name as label,id as value FROM " . $this->_tbl . " WHERE name LIKE '%".$word."%'";

        $this->_db->setQuery( $query );
        $result = $this->_db->loadObjectList();
        return $result;
    }
}
