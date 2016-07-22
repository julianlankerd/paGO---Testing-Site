<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
/**
 * File that includes default filters for Pago
 */

/**
 * Function to store the pago image sizes
 */
defined('_JEXEC') or die;
KDispatcher::add_filter( 'pre_save_pago_config', 'save_pago_config_image_sizes' );
function save_pago_config_image_sizes( &$params )
{
	// make sure when saving global image sizes that the defaults are set
	$image_sizes = array();


	foreach ( (array) $params['media']['image_sizes'] as $_size ) {
		if ( 4 == count( $_size ) ) {
			$image_sizes[$_size[0]] = array(
				'width'  => $_size[1],
				'height' => $_size[2],
				'crop'   => $_size[3]
			);
		}
	}

	$defaults = array(
		'thumbnail' => array(
			'width' => 50,
			'height' => 50,
			'crop' => 1
		),
		'medium' => array(
			'width' => 150,
			'height' => 150,
			'crop' => 1
		),
		'large' => array(
			'width' => 350,
			'height' => 350,
			'crop' => 0
		)
	);

	$params['media']['image_sizes'] = array_merge( $defaults, $image_sizes );

	return $params;
}

/**
 * Function to store the pago file custom metadata
 */
//KDispatcher::add_filter( 'pre_save_pago_config', 'save_pago_config_filemeta' );
function save_pago_config_filemeta( $params )
{
	foreach ( (array) $params['media']['files_meta'] as $meta ) {
		if ( trim( $meta[0] ) ) {
			$files_meta[trim( $meta[0] )] = trim( $meta[1] );
		}
	}

	$params['media']['files_meta'] = $files_meta;

	return $params;
}

/**
 * Function to store the default store-wide image
 */
KDispatcher::add_filter( 'pre_save_pago_config', 'save_pago_config_default_image' );
function save_pago_config_default_image( $params )
{
	Pago::load_helpers( 'imagehandler' );
	$config = Pago::get_instance( 'config' )->get();

	$_default = PagoImageHandlerHelper::get_item_files( 0, false, array( 'store_default' ) );

	$uploaded = JFactory::getApplication()->input->get( 'store_default_image', array(), 'files', 'array' );

	// check for error and that it isn't set to 0
	if ( isset($uploaded['error']) && $uploaded['error'] !== 0 ) {
		return $params;
	}

	$path = trim( $config->get( 'media.images_file_path', 'media/pago' ), DIRECTORY_SEPARATOR );
	$file = PagoImageHandlerHelper::handle_upload( $uploaded, $path .DIRECTORY_SEPARATOR. 'default', 'default' );

	if ( isset( $file['error'] ) ) {
		return $params;
	}

	$url       = $file['url'];
	$type      = $file['type'];
	$file_name = $file['file_name'];
	$file      = $file['file'];
	$title     = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
	$content   = '';
	$file_meta = array();

	// Prepare array to insert into db
	$data = array(
		'title'      => $title,
		'caption'    => '',
		'item_id'    => 0,
		'published'  => 1,
		'access'     => 0,
		'file_name'  => $file_name,
		'type'       => 'store_default',
		'mime_type'  => $type,
		'file_meta'  => array( 'file_meta' => $file_meta )
	);

	// Create thumbnails and other meta data
	PagoImageHandlerHelper::generate_image_metadata( $file, $data, $config );

	// Serialize meta data for storage
	$data['file_meta'] = serialize( $data['file_meta'] );

	// Save the data
	$model_upload = JModelLegacy::getInstance( 'upload', 'PagoModel' );
	$id = $model_upload->store( $data );

	if ( !$id ) {
		JError::raiseWarning( 100, 'There was an error uploading the default image: '
			. $model->getError() );
	} else {
		// Delete old default image
		if ( isset( $_default[0] ) && !empty( $_default[0] ) ) {
			$model_file = JModelLegacy::getInstance( 'file', 'PagoModel' );
			$model_file->delete( array( $_default[0]->id ) );
		}
	}

	return $params;
}

/**
 * Helper for the image path URL so that it doesn't use 'store_default' instead of 'default'
 */
KDispatcher::add_filter( 'image_path', 'image_path_store_default' );
function image_path_store_default( $current_name, $row )
{
	if ( 'store_default' != $row->type ) {
		return;
	}

	$current_name = 'default';
}
KDispatcher::add_filter( 'image_path', 'image_path_category' );
function image_path_category( $current_name, $row )
{
	
	if ( 'category' != $row->type ) {
		return;
	}

	$category = Pago::get_instance( 'categoriesi' )->get( $row->item_id );
	if($category){
		$current_name = JFilterOutput::stringURLSafe( $category->id );
	}
}
KDispatcher::add_filter( 'upload_file_path', 'files_upload_file_path' );
function files_upload_file_path( $path, $type )
{
	if ( 'files' != $type ) {
		return;
	}

	$path = PagoHelper::get_files_base_path();
}

/**
 * Helper for the image path URL so that it doesn't use 'store_default' instead of 'default'
 * 
 * @param object JParameter object use $config->def( name, value ) to define to set default value
 */
KDispatcher::add_filter( 'pago_configuration', 'add_default_image_to_config' );
function add_default_image_to_config( $config, $namespace )
{
	if ( $namespace !== 'global' ) {
		return $config;
	}

	$db = JFactory::getDBO();

	$query = "SELECT *
		FROM #__pago_files
			WHERE `type` = 'store_default'
			 LIMIT 1";
	$db->setQuery( $query );
	$default = $db->loadObject();

	$config->set( 'media.store_default_image', $default );
	return $config;
}

/**
 * Filter for category list attributes search
 * 
 * @param array Contains the sql query
 */
KDispatcher::add_filter( 'category_list_sql', 'attribute_search_category_list_query' );
function attribute_search_category_list_query( $sql )
{
	$search = JFactory::getApplication()->input->get( 'search', array() );

	// Stop if nothing there
	if ( empty( $search ) ) {
		return $sql;
	}

	// Stop if the array we need isn't there
	if ( !array_key_exists( 'attributes', $search ) ) {
		return $sql;
	}

	$sub_query['from'] = array();
	$sub_query['where'] = array();
	$i = 0;
	$attr_count = count( $search['attributes'] );

	foreach ( $search['attributes'] as $attribute ) {
		if ( $i == 0 ) {
			$sub_query['from'][] = 'FROM jos_pago_items_attr AS attr';
			$sub_query['where'][] = 'attr.attr_opt_id = '. (INT) $attribute;
		} else {
			$sub_query['from'][] = 'LEFT JOIN jos_pago_items_attr AS attr'.
				$i . ' ON attr'.$i.'.item_id = attr.item_id';
			$sub_query['where'][] = 'attr'.$i.'.attr_opt_id = '. (INT) $attribute;
		}
		$i++;
	}

	$sql['where'][] = ' AND items.id IN (SELECT attr.item_id '.
		implode( ' ', $sub_query['from'] ).' WHERE '. implode( ' AND ', $sub_query['where'] ).
		' )';

	return $sql;
}

/**
 * File uploader get variables will get the $item_id and the $path for this specific upload type
 * 
 * @param string Should be empty coming in, but needs to be set
 * @param string Should be empty coming in, but needs to be set
 * @param string The type of upload. In this case 'category'
 */
KDispatcher::add_filter( 'file_uploader_get_vars', 'fileup_category_image_get_vars' );
function fileup_category_image_get_vars( $item_id, $path, $upload_type )
{
	if ( 'category' != $upload_type ) {
		return;
	}
	//$category = Pago::get_instance( 'categoriesi' )->get( JRequest::getInt( 'item_id' ) );
	//$item_id  = $category->id;
	$path     = JFilterOutput::stringURLSafe( JFactory::getApplication()->input->getInt( 'item_id' ) );
}

KDispatcher::add_filter( 'file_uploader_success_layout', 'fileup_category_success_layout' );
function fileup_category_success_layout( $layout, $upload_type )
{	
	if ( 'category' != $upload_type ) {
		return;
	}

	$layout = 'success_images';
}

KDispatcher::add_filter( 'files_upload_before_store', 'fileup_files_before_store' );
function fileup_files_before_store( $data )
{	
	$dispatcher = KDispatcher::getInstance();
	$params = Pago::get_instance( 'config' )->get();

	$upload_type = JFactory::getApplication()->input->getWord( 'type' );

	if ( 'files' != $upload_type ) {
		return;
	}

	$path = trim( $params->get( $upload_type . '_file_path', 'media' .DIRECTORY_SEPARATOR. 'pago' ), DIRECTORY_SEPARATOR );
	$dispatcher->trigger( 'upload_file_path', array( &$path, $upload_type ) );

	$path_extra = JPath::clean( JFolder::makeSafe( JFactory::getApplication()->input->getString( 'path' ) ) );
	$dispatcher->trigger( 'file_uploader_get_vars',
		array( &$data['item_id'], &$path_extra, $upload_type, 'abs_path' ) );

	if ( !file_exists( $path .DIRECTORY_SEPARATOR. trim( $path_extra, DIRECTORY_SEPARATOR ) .DIRECTORY_SEPARATOR. $data['file_name'] ) ) {
		$data = array();
		return false;
	}

	// Make sure this is an array
	$data['file_meta'] = (array) PagoHelper::maybe_unserialize( $data['file_meta'] );

	$data['file_meta']['file_path'] = $path_extra;
	$data['file_meta'] = serialize( $data['file_meta'] );
}

KDispatcher::add_filter( 'uploader_allowed_extensions', 'uploader_allowed_extensions_files' );
function uploader_allowed_extensions_files( $allowed, $type )
{	
	if ( 'files' == $type ) {
		$allowed = '*.*';
	}
}

KDispatcher::add_filter( 'upload_check_filetype', 'upload_check_filetype_images' );
function upload_check_filetype_images( $mimes, $mime_check, $type )
{
	if ( in_array( $type, array( 'images', 'category', 'store_default' ) ) ) {
		$mimes = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png'
			);
	}
}
?>
