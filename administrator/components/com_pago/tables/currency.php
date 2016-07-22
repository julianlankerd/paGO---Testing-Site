<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableCurrency extends JTable
{
	public $id                = null;
	public $name              = '';
	public $code              = '';
	public $symbol            = '';
	public $published         = null;
	public $default		      = null;


	/**
	* Constructor
	*
	* @param object Database connector object
	*/
	function __construct( $db )
	{
		parent::__construct( '#__pago_currency', 'id', $db );
	}

	/**
	 * Overloaded check function
	 */
	function check()
	{
		if ( trim( $this->name ) == '' ) {
			$this->setError( JText::_('PAGO_CURRENCY_ERROR_NO_NAME') );
			return false;
		}
		if ( trim( $this->code ) == '' ) {
			$this->setError( JText::_('PAGO_CURRENCY_ERROR_NO_CODE') );
			return false;
		}
		return true;
	}
}
