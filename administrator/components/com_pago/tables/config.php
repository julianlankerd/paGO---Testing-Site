<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

class JTableConfig extends JTable
{
	var $id          = 0;
	var $modified    = '0000-00-00 00:00:00';
	var $modified_by = null;
	var $name        = null;
	var $params      = null;

	function __construct( $db, $key = 'id'  )
	{
        parent::__construct('#__pago_config', $key, $db);
    }

	/**
	 * Check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		$user       = JFactory::getUser();
		$config     = JFactory::getConfig();
		$createdate = JFactory::getDate();
		$version 	= new JVersion();
		
		if($version->RELEASE >= 3){
			$createdate = JDate::getInstance('now',  $config->get( 'offset' ));
		}else{
			$createdate->setOffset( $config->getValue( 'config.offset' ) );	
		}
		
		$this->modified    = $createdate->toSql();
		$this->modified_by = $user->id;

		return true;
	}

	/**
	* Overloaded bind function
	*
	* @access public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	function bind( $array, $ignore = '' )
	{
		if ( is_array( $array['params'] ) ) {
			$registry = new JRegistry();
			$registry->loadArray( $array['params'] );
			$array['params'] = $registry->toString();
		}

		return parent::bind( $array, $ignore );
	}
}
?>