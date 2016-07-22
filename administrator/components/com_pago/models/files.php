<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.model' );

class PagoModelFiles extends JModelLegacy
{
	var $_data;
	var $_total;
	var $_item_id;

	function __construct()
	{
		parent::__construct();
	}

	function getData()
	{
		$mainframe = JFactory::getApplication();

		if ( empty( $this->_data ) ) {
			$row = $this->getTable( 'files', 'Table' );
			$row->load( JFactory::getApplication()->input->getInt( 'id' ) );

			$this->_data = $row;
		}

		return $this->_data;
	}

	/**
	 * Returns the dtabase row for the file in question
	 * If the file is not found that means that we could add it to the db
	 *
	 * @param string The name of the file to find
	 * @param string The path to the file from the basepath
	 * @return mixed Object containing found file or an empty object if nothing was found
	 */
	function get_file_by_path( $name, $path )
	{
		$db = JFactory::getDBO();
		$base_path = PagoHelper::get_files_base_path();

		$query = "SELECT *
			FROM #__pago_files
				WHERE `type` = 'files'
				AND `file_name` = " . $db->Quote( $name );
		$db->setQuery( $query );
		$files = $db->loadObjectList();

		foreach ( $files as $file ) {
			$meta = PagoHelper::maybe_unserialize( $file->file_meta );
			if ( isset( $meta['file_path'] ) && $path == $meta['file_path'] ) {
				return $file;
			}
		}

		return new stdClass();
	}

	/**
	 * Deletes a file from the database depending on their name and path
	 *
	 * @param string The name of the file to find
	 * @param string The path to the file from the basepath
	 * @return bool True if success
	 */
	function delete_file_by_path( $name, $path )
	{
		$db = JFactory::getDBO();
		$file = $this->get_file_by_path( $name, $path );

		if ( !isset( $file->id ) ) {
			return false;
		}

		$query = "DELETE FROM #__pago_files
			WHERE `id` = {$file->id}";
		$db->setQuery( $query );
		$db->query();

		return !$db->getErrorNum();
	}
}
?>
