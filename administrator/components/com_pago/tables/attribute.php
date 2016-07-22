<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableAttribute extends JTable
{

	var $id         = null;
	var $name       = null;
	var $type       = null;
	var $order      = null;
	var $created    = null;
	var $modified   = null;
	var $pricing    = null;
	var $searchable = 1;
	var $showfront 	= 1;
	var $ordering   = null;
	var $attr_enable = 0;
	var $expiry_date = null;
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__pago_attr', 'id', $db);
    }

    /**
	 * Overloaded check function
	 */
	function check()
	{
		if ( trim( $this->name ) == '' ) {
			$this->setError( JText::_('PAGO_ATTRIBUTE_ERROR_NO_NAME') );
			return false;
		}

		// if ( empty( $this->alias ) ) {
		// 	$this->alias = $this->name;
		// }

		// $this->alias = JFilterOutput::stringURLSafe( $this->alias );

		// if ( trim( str_replace( '-', '', $this->alias ) ) == '' ) {
		// 	$datenow = JFactory::getDate();
		// 	$this->alias = $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
		// }

		return true;
	}
}
