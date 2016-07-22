<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.controller' );
jimport( 'joomla.database.table' );
JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/tables' );

class PagoControllerFiles extends PagoController
{
	function __construct()
	{
		parent::__construct();
	}

	function display()
	{
		parent::display();
	}

	function tree()
	{
		$op = JFactory::getApplication()->input->get( 'operation' );

		switch ( $op ) {
			case 'add_file':
				$return = $this->add_file( JFactory::getApplication()->input->getString( 'path' ),
					JFactory::getApplication()->input->getString( 'file' ) );
				break;

			case 'create_node':
				$return = $this->add_folder( JFactory::getApplication()->input->getString( 'path' ),
					JFactory::getApplication()->input->getString( 'title' ) );
				break;

			case 'remove_node':
				$return = $this->remove( JFactory::getApplication()->input->getString( 'path' ),
					JFactory::getApplication()->input->getString( 'type' ),
					JFactory::getApplication()->input->getString( 'file' ) );
				break;

			case 'get_children':
			default:
				$return = $this->get_children_by_path( JFactory::getApplication()->input->getString( 'path' ) );
				break;
		}

		echo json_encode( $return );
		jexit();
	}

	/**
	* Function adds a file that is already on the filesystem
	* but not in the database.
	*
	* @param string The relative path to the file from the files absolute path
	* @param string The name of the file
	* @return object Contains a success or failure status
	*/
	function add_file( $path, $file )
	{
		$dispatcher = KDispatcher::getInstance();
		$base_path = PagoHelper::get_files_base_path();
		$_path = JPath::clean( JFolder::makeSafe( $path ) );
		$_file = JPath::clean( $file, '' );
		$model = $this->getModel( 'upload' );

		// Set upload type to be used in filters
		JFactory::getApplication()->input->get( 'type', 'files' );

		$full_path = $base_path . $_path . "/" . $_file;
		if ( !file_exists( $full_path ) ) {
			return array( (object) array( 'status' => 0 ) );
		}

		// If you want to override the mime types
		$mimes = false;
		$mime_check = true;

		// A correct MIME type will pass this test
		$dispatcher->trigger( 'upload_check_filetype',
			array( &$mimes, &$mime_check, 'files' ) );
		$filetype = PagoImageHandlerHelper::check_filetype( $full_path, $mimes, $mime_check );

		$file_data = array( 'file' => $full_path, 'file_name' => $_file, 'url' => '',
			'type' => $filetype['type'] );

		if ( $model->add_file( $file_data, 'files', 0 ) ) {
			return array( (object) array( 'status' => 1 ) );
		} else {
			return array( (object) array( 'status' => 0 ) );
		}
	}

	function add_folder( $path, $folder )
	{
		$config = Pago::get_instance( 'config' )->get();
		$return = array();

		$_path = JPath::clean( JFolder::makeSafe( $path ) );
		$_folder = JPath::clean( JFolder::makeSafe( $folder ) );
		if ( $_folder != $folder ) {
			return array( (object) array( 'status' => 0 ) );
		}

		$base_path = PagoHelper::get_files_base_path() . '/';

		$path = $base_path . $_path . '/' . $folder;

		if ( JFolder::create( $path ) ) {
			$return[] = (object) array(
				'attr' => (object) array(
					'path' => str_replace( $base_path, '', $path ),
					'rel' => 'folder',
					'id' => str_replace( array( $base_path, '/', '\\'), '', $path )
					),
				'data' => $folder,
				'state' => empty( $folders ) ? '' : 'closed',
				'status' => 1
				);
		} else {
			$return[] = (object) array( 'status' => 0 );
		}

		return $return;
	}

	function remove( $path, $type, $file )
	{
		$base_path = PagoHelper::get_files_base_path() . DS;
		$_path = JPath::clean( JFolder::makeSafe( $path ) );
		$model = $this->getModel( 'files' );

		if ( 'file' == $type ) {
			$ret = true;
			$file = JPath::clean( $file, '' );
			$_file = $base_path . $_path . '/' . $file;

			if ( file_exists( $_file ) ) {
				$ret = JFile::delete( $_file );
			}

			if ( $ret ) {
				$model->delete_file_by_path( $file, $_path );
			}

			return array( (object) array( 'status' => ( $ret ) ? 1 : 0 ) );
		}

		$_folders = JFolder::folders( $base_path . $_path );
		$_files   = PagoHelper::get_files_in_dir( $base_path . $_path );

		if ( !empty( $_folders ) || !empty( $_files ) ) {
			return array( (object) array(
				'status' => 0, 'msg' => 'Please make sure that the folder is empty.' ) );
		}

		$ret = JFolder::delete( $base_path . $_path );

		return array( (object) array( 'status' => ( $ret ) ? 1 : 0 ) );
	}

	function get_children_by_path( $path )
	{
		$base_path = PagoHelper::get_files_base_path() . DS;
		$_path = JPath::clean( JFolder::makeSafe( $path ) );
		$model = $this->getModel( 'files' );
		$return = array();

		if ( 'root' == $_path ) {
			$_folders = JFolder::folders( $base_path );
			$_files   = PagoHelper::get_files_in_dir( $base_path );
			$return[] = (object) array(
				'attr' => (object) array(
					'path' => '/',
					'rel' => 'root',
					'id' => 'root'
					),
				'data' => 'Root',
				'state' => ( empty( $_folders ) && empty( $_files ) ) ? '' : 'closed'
				);

			return $return;
		}

		$path = $base_path;
		if ( '/' != $_path ) {
			$path .= ltrim( $_path, '/\\' );
		}

		// Get files in folder
		$files = PagoHelper::get_files_in_dir( $path );
		foreach ( $files as $file ) {
			$__file = $model->get_file_by_path( $file, $_path );
			$return[] = (object) array(
				'attr' => (object) array(
					'path' => $_path,
					'rel' => 'file',
					'id' => $file,
					'class' => ( isset( $__file->id ) ? 'file-exists' : 'new-file' )
					),
				'data' => $file,
				'state' => ''
				);
		}

		// Get folders in folder
		$folders = JFolder::folders( $path );
		foreach ( $folders as $folder ) {
			$_folders = JFolder::folders($path . '/' . $folder);
			$_files   = PagoHelper::get_files_in_dir($path . '/' . $folder);
			$return[] = (object) array(
				'attr' => (object) array(
					'path' => str_replace(rtrim($base_path, '/'), '', $path . '/' . $folder),
					'rel' => 'folder',
					'id' => str_replace(array($base_path, '/', '\\'), '', $path . '/' . $folder)
					),
				'data' => $folder,
				'state' => ( empty( $_folders ) && empty( $_files ) ) ? '' : 'closed'
				);
		}

		return $return;
	}
}