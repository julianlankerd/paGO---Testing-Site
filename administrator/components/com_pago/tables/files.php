<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

class TableFiles extends JTable
{
	var $id               = 0;
	var $title            = null;
	var $alias            = null;
	var $item_id          = null;
	var $published        = 0;
	var $ordering         = null;
	var $access           = null;
	var $default          = 0;
	var $created_time     = '0000-00-00 00:00:00';
	var $created_by       = null;
	var $modified_time    = '0000-00-00 00:00:00';
	var $modified_by      = null;
	var $caption          = null;
	var $fulltext         = null;
	var $type             = null;
	var $file_name        = null;
	var $mime_type        = null;
	var $file_meta        = null;
	var $hits             = null;
	var $metadata         = null;

	function TableFiles( $db )
	{
		parent::__construct( '#__pago_files', 'id', $db );
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
		$dispatcher = KDispatcher::getInstance();
		$createdate = JFactory::getDate();
		$version 	= new JVersion();

		if($version->RELEASE >= 3){
			$createdate = JDate::getInstance('now',  $config->get( 'offset' ));
		}else{
			$createdate->setOffset( $config->getValue( 'config.offset' ) );
		}

		$this->modified_time = $createdate->toSql();
		$this->modified_by   = $user->id;

		// Insert
		if ( !$this->id ) {
			$this->created_time  = $createdate->toSql();
			//$this->created_time  = $createdate->toMySQL();
			$this->created_by    = $user->id;
			$this->modified_time = $this->_db->getNullDate();
			$this->modified_by   = 0;
		}

		if ( empty( $this->title ) ) {
			$this->setError( JText::_( 'File must have a title' ) );
			return false;
		}

		if ( empty( $this->alias ) ) {
			$this->alias = $this->title;
		}
		$this->alias = JFilterOutput::stringURLSafe( $this->alias );

		if ( trim( str_replace( '-', '', $this->alias ) ) == '' ) {
			$datenow =& JFactory::getDate();
			$this->alias = $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
		}

		$required_types = array( 'images', 'category' );
		$dispatcher->trigger( 'files_type_require_item_id', array( &$required_types ) );
		if ( empty( $this->item_id ) && in_array( $this->type, $required_types ) ) {
			$this->setError( JText::_( 'File must have an item_id' ) );
			return false;
		}

		if ( trim( str_replace( '&nbsp;', '', $this->fulltext ) ) == '') {
			$this->fulltext = '';
		}

		// json encode metadata
		$this->metadata = json_encode( $this->metadata );

		return true;
	}
}
?>