<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

Pago::load_helpers( 'imagehelper' );

class PagoImageHandlerHelper extends PagoImageHelper
{
	/**
	 * Gets all of files for an item
	 *
	 * @since 1.0
	 *
	 * @param int The item id in question
	 * @param bool If we want to filter published and access for the query
	 * @param array The types of files to get. Ex. array( 'images', 'user' )
	 * @return object Contains all images for item
	 **/
	static public function get_item_files(  $item_id,
											$filter = true,
											$types = array( 'images', 'user', 'category', 'video' ),
											$variation_id = false)
	{

		$db     = JFactory::getDBO();
		$config = Pago::get_instance( 'config' )->get();

		$where = '';
		if ( !is_array( $types ) && empty( $types ) ) {
			$types = array( 'images', 'user', 'category', 'video' );
		}else{
			if(!is_array($types)){
				$types = array($types);
			}
		}

		$where .= ' AND (';

		foreach ( $types as $type ) {
			$where .= "f.`type` = " . $db->Quote( $type ) . ' OR ';
		}
		$where = substr( $where, 0, -4 );
		$where .= ')';

		if ( $filter ) {
			// This needs to get changed to use Joomla's ACL instead
			// $user = & JFactory::getUser();
			// $gid  = $user->get( 'aid', 0 );

			// $where .= ' AND f.`access` <= ' . (int) $gid;
			$where .= ' AND f.`published` = 1';
		}

		$item_id = intval( $item_id );

		if($type == 'category')
		{
			$query = "SELECT f.*, f.`item_id` as `primary_category` FROM #__pago_files AS f WHERE f.`item_id` = {$item_id}
				    {$where}
				    ORDER BY f.`default` DESC, f.`ordering` ASC";
		}
		elseif($type == 'variation')
		{
			$query = "SELECT f.*
				FROM #__pago_files AS f
				
					WHERE f.`provider` = {$variation_id}
				
					ORDER BY f.`default` DESC, f.`ordering` ASC";
		}
		else
		{
			$query = "SELECT f.*, i.`primary_category`
				FROM #__pago_files AS f
				LEFT JOIN #__pago_items AS i ON ( i.id = f.item_id )
					WHERE f.`item_id` = {$item_id}
					{$where}
					ORDER BY f.`default` DESC, f.`ordering` ASC";
		}
		
		$db->setQuery( $query );
		$files = $db->loadObjectList();
		
		if($type != 'variation' && $type != 'video')
			foreach ($files as $f => $file) {
				if(!PagoImageHandlerHelper::checkImageExist($file)){
					unset($files[$f]);
				}
			}
	
		if($files){
			$tempArray = array();
			$i = 0;
			foreach ($files as $file) {
				$tempArray[$i] = $file;
				$i++;
			}
			$files = $tempArray;
		}
		if ( empty( $files ) && in_array( 'images', $types ) ) {
			$files = $config->get( 'media.store_default_image', array() );
			if ( !empty( $files ) ) {
				$files = array( $files );
			}
		}


		return $files;
	}

	static function getImageById($id){
		if(!$id){
			return false;
		}
		$db = JFactory::getDBO();
		$query = "SELECT p.*, f.`id` AS file_id, f.`title` AS file_title, f.`alias` AS file_alias,
		 f.`caption` AS file_caption, f.`type` AS file_type, f.`file_name` AS file_file_name,
		 f.`file_meta` AS file_file_meta
		FROM #__pago_files as f
		LEFT JOIN #__pago_items as p ON f.item_id = p.id
		WHERE f.id = '".$id."'";
		$db->setQuery($query);
		$product = $db->loadObject();
		$img =(object) array(
					'id'        		=> $product->file_id,
					'title'     		=> 'View Details for ' . html_entity_decode($product->name), //$product->file_title,
					'alias'     		=> $product->file_alias,
					'caption'   		=> $product->file_caption,
					'item_id'   		=> $product->id,
					'type'      		=> $product->file_type,
					'file_name' 		=> $product->file_file_name,
					'file_meta' 		=> $product->file_file_meta,
					'primary_category' 	=> $product->primary_category
				);
		return $img;
	}
	// static public function get_item_default_file($item_id){

	// 	$item_id = intval( $item_id );

	// 	$db     = JFactory::getDBO();
	// 	$config = Pago::get_instance( 'config' )->get();
	// 	$where .= "";

	// 	$query = "SELECT f.*, i.`primary_category`
	// 			FROM #__pago_files AS f
	// 			LEFT JOIN #__pago_items AS i ON ( i.id = f.item_id )
	// 				WHERE f.`item_id` = {$item_id} AND f.`type` = images AND f.`published` = 1
	// 				ORDER BY f.`default` DESC limit 1";

	// 	$db->setQuery( $query );
	// 	$file = $db->loadObjectList();
	// }
	static public function get_image_from_object(   $row,
													$size = 'thumbnail',
													$no_html = false,
													$attr = '',
													$relative = true,
													$image_tag = true )
	{


		$url = JURI::root( true ).'/';
		if(!$relative){
			$url = JURI::root();
		}
		static $categories = array();
		static $records = array();
		$extra_path = '';

		$config = Pago::get_instance( 'config' )->get();
		$dispatcher = KDispatcher::getInstance();

		// Quick hack for displaying default image if no image exists
		// when passing an object with no file_meta, mostly due to the way
		// images are handled with the category item list.  Revisit later...

		if( empty( $row->file_meta ) )
			$row = $config->get( 'media.store_default_image', array() );

		if( !empty( $row ) ) {
			$imagedata = PagoHelper::maybe_unserialize( $row->file_meta );


			$_size = $size;
			if ( is_array( $_size ) ) {
				$_size = implode( '.', $_size );
			}

			if ( isset( $records[$_size][$row->id] ) ) {
				$file = $records[$_size][$row->id];
			} else {
				if ( $size == 'full') {
					$file['file'] = $row->file_name;
					$file['width'] = $imagedata['width'];
					$file['height'] = $imagedata['height'];

				} else {

					$image_sizes = $config->get( 'media.image_sizes' );
					if ( !( $file = PagoImageHandlerHelper::find_size( $row, $size ) ) ) {
						foreach( $image_sizes as $img_size) {
							// Loop through image sizes comparing it to current image's dimensions
							if($img_size->width == $imagedata['width']
								&& $img_size->height == $imagedata['height']
							) {
								// If dimensions match a config size, use those dimensions
								$file['file'] = $row->file_name;
								$file['width'] = $imagedata['width'];
								$file['height'] = $imagedata['height'];
								break;
							}


						}
						if ( empty( $file ) ) {
							$sizes = array_keys( (array) $image_sizes );

							// Lets make sure that there could be a larger image size
							if ( ( $k = array_search( $size, $sizes ) ) && $k + 1 != count( $sizes ) ) {
								// Grab the next largest image
								$file = @PagoImageHandlerHelper::find_size( $row, $size );
							} else {
								// At least display the original
								if ( is_array( $row->file_meta ) ) {
									 $row->file_meta = $row->file_meta;
								} else {
									 $row->file_meta = PagoHelper::maybe_unserialize( $row->file_meta );
								}
								$file['file'] = $row->file_name;
								$file['width'] = $row->file_meta['width'];
								$file['height'] = $row->file_meta['height'];
							}
						}
					}
				}
				// trigger to override image extra path
				JPluginHelper::importPlugin( 'pago_products' );
				$dispatcher->trigger( 'image_path', array( &$extra_path, $row, $size , $file['file'] , $row->type) );
				


				if ( !$extra_path ) {
					if( isset( $row->item_id ) && $row->item_id >0  ){
						$extra_path = @JFilterOutput::stringURLSafe( $row->primary_category );
						if($row->type == "images"){
							$extra_path = 'items' . '/' . $extra_path;
						}
						if($row->type == "video"){
							$extra_path = 'items' . '/' . $extra_path;
						}
						if($row->type == "variation"){
							$extra_path = 'product_variation' . '/' . $row->provider;
						}
					}

					if($row->type == "category"){
						$extra_path = JFilterOutput::stringURLSafe( $row->item_id );
						$extra_path = 'category' . '/' . $extra_path;
					}

					if($row->type == "store_default"){
						$extra_path = 'default' . '/' . $extra_path;
					}
				}
				
				$file['url'] = $url .
					trim( $config->get('media.'. $row->type . '_url_path', 'media/pago' ), '/' )
					.'/'. $extra_path .'/'. $file['file'];
				$file['image_path'] = JPATH_ROOT.DS .
					trim( $config->get('media.'. $row->type . '_url_path', 'media/pago' ), '/' )
					.'/'. $extra_path .'/'. $file['file'];
				$records[$_size][$row->id] = $file;
			}
		}
		if ( $no_html ) {
			if( isset( $file['url'] ) ) {
				return $file['url'];
			} else {
				return;
			}
		}

		if ( isset( $row->caption ) && !$row->caption ) {
			$row->caption = $row->title;
		}

		if ( isset( $file['width'] ) ) {
			$attr .= ' width="'.$file['width'].'"';
		}

		if ( isset( $file['height'] ) ) {
			$attr .= ' height="'.$file['height'].'"';
		}

		$caption = '';
		$tile = '';
		if ( isset( $row->caption ) ) {
			$row->caption = str_replace( '"', '\'', $row->caption );
			$caption = 'alt="' . $row->caption . '"';
		} else {
			$caption = 'alt=""';
		}

		if( isset( $row->title ) ) {
			$row->title = str_replace( '"', '\'', $row->title );
			$title = 'title="' . $row->title . '"';
		} else {
			$title = 'title=""';
		}

		if( isset( $file['url'] ) && JFile::exists($file['image_path'])  ) {


			if ($image_tag){
				$img = '<img src="' .$file['url']. '" ' . $caption . ' ' . $title . ' ' .$attr. ' />';
			}
			else{
				$img = $file['url'];
			}
		} else {
			$img = '';
		}

		return $img;
	}

	static public function checkImageExist($image){
		$url = JPATH_ROOT.DS;
		$config = Pago::get_instance( 'config' )->get();
		$dispatcher = KDispatcher::getInstance();
		$extra_path = "";
		// trigger for override extra path
		JPluginHelper::importPlugin( 'pago_products' );
		$dispatcher->trigger( 'image_path', array( &$extra_path, $image, "large" , $image->file_name, $image->type) );

		$id=array();
		if($image->type == 'video' && $image->video_key==""){
			array_push($id,$image->id);

		}
		if(!empty($id)){
			$files_model = JModelLegacy::getInstance('File', 'PagoModel');

			$files_model->delete($id);
			return false;
		}
		if($image->type == 'images' || $image->type == 'download' || $image->type == 'video'){
			if($extra_path!="")
			{
				$filePath = $url.trim( $config->get('media.'. $image->type . '_url_path', 'media/pago' ), '/' ).DS.$extra_path.DS.$image->file_name;
			}
			else
			{
				$filePath = $url.trim( $config->get('media.'. $image->type . '_url_path', 'media/pago' ), '/' ).DS.'items'.DS.$image->primary_category.DS.$image->file_name;
			}
			if(JFile::exists($filePath)){
				return true;
			}
			else{
				return false;
			}
		}
		if($image->type == 'category'){

			if($extra_path!="")
			{
				$filePath = $url.trim( $config->get('media.'. $image->type . '_url_path', 'media/pago' ), '/' ).DS.$extra_path.DS.$image->file_name;
			}
			else
			{
				$filePath = $url.trim( $config->get('media.'. $image->type . '_url_path', 'media/pago' ), '/' ).DS.'category'.DS.$image->item_id.DS.$image->file_name;
			}

			if(JFile::exists($filePath)){
				return true;
			}
			else{
				return false;
			}
		}
	}
	static public function find_size( $row, $size )
	{
		if ( is_array( $row->file_meta ) ) {
			$imagedata = $row->file_meta;
		} else {
			$imagedata = PagoHelper::maybe_unserialize( $row->file_meta );
		}
		$areas = array();

		if(!is_array( $size ) && !empty( $imagedata['sizes'] ))
		{

			$original_size = $size;
			$config = Pago::get_instance( 'config' )->get();

			$image_sizes = $config->get( 'media.image_sizes' );
			$size  = (array) $image_sizes->$size;


			foreach ( $imagedata['sizes'] as $_size => $data ) {
				// Already cropped to width or height; so use this size

				if ( ( $data['width'] == $size['width'] && $data['height'] <= $size['height'] )
					|| ( $data['height'] == $size['height'] && $data['width'] <= $size['width'] )
				) {
					$file = $data['file'];
					list( $width, $height ) = PagoImageHandlerHelper::image_constrain_size(
						$data['width'], $data['height'], $original_size );

					return compact( 'file', 'width', 'height' );
				}

				// Add to lookup table: area => size
				$areas[$data['width'] * $data['height']] = $_size;
			}

			if ( !$size || !empty( $areas ) ) {
				// Find for the smallest image not smaller than the desired size
				ksort( $areas );
				foreach ( $areas as $_size ) {
					$data = $imagedata['sizes'][$_size];
					if ( $data['width'] >= $size['width'] || $data['height'] >= $size['height'] ) {
						$file = $data['file'];
						list( $width, $height ) = PagoImageHandlerHelper::image_constrain_size(
							 $data['width'], $data['height'], $original_size );

						return compact( 'file', 'width', 'height' );
					}
				}
			}
			$size = $original_size;
		}

		// Get the best one for a specified set of dimensions
		if ( is_array( $size ) && !empty( $imagedata['sizes'] ) ) {
			foreach ( $imagedata['sizes'] as $_size => $data ) {
				// Already cropped to width or height; so use this size
				if ( ( $data['width'] == $size['width'] && $data['height'] <= $size['height'] )
					|| ( $data['height'] == $size['height'] && $data['width'] <= $size['width'] )
				) {
					$file = $data['file'];
					list( $width, $height ) = PagoImageHandlerHelper::image_constrain_size(
						$data['width'], $data['height'], $size );

					return compact( 'file', 'width', 'height' );
				}

				// Add to lookup table: area => size
				$areas[$data['width'] * $data['height']] = $_size;
			}

			if ( !$size || !empty( $areas ) ) {
				// Find for the smallest image not smaller than the desired size
				ksort( $areas );
				foreach ( $areas as $_size ) {
					$data = $imagedata['sizes'][$_size];
					if ( $data['width'] >= $size['width'] || $data['height'] >= $size['height'] ) {
						$file = $data['file'];
						list( $width, $height ) = PagoImageHandlerHelper::image_constrain_size(
							 $data['width'], $data['height'], $size );

						return compact( 'file', 'width', 'height' );
					}
				}
			}
		}

		if ( is_array( $size ) || empty( $size ) || empty( $imagedata['sizes'][$size] ) ) {
			return false;
		}

		$data = $imagedata['sizes'][$size];

		return $data;
	}

	static public function image_constrain_size( $width, $height, $size = 'medium' )
	{
		$config = Pago::get_instance( 'config' )->get();
		$image_sizes = (array) $config->get( 'media.image_sizes' );

		if ( is_array( $size ) ) {
			$max_width = $size[0];
			$max_height = $size[1];
		} elseif ( $size == 'thumb' || $size == 'thumbnail' ) {
			$max_width = intval( $image_sizes['thumbnail']->width );
			$max_height = intval( $image_sizes['thumbnail']->height );

			// Last chance thumbnail size defaults
			if ( !$max_width && !$max_height ) {
				$max_width  = 50;
				$max_height = 50;
			}
		} elseif ( isset( $image_sizes[$size] ) ) {
			$max_width = intval( $image_sizes[$size]->width );
			$max_height = intval( $image_sizes[$size]->height );
		} else { // $size == 'full' has no constraint
			$max_width = $width;
			$max_height = $height;
		}

		return PagoImageHelper::constrain_dimensions( $width, $height, $max_width, $max_height );
	}
	static public function getSizeByName($size){
		$config = Pago::get_instance( 'config' )->get();
		$image_sizes = (array) $config->get( 'media.image_sizes' );
		return $image_sizes[$size];
	}
	/**
	 * Start-up function to handle an uploaded image
	 *
	 * @param string $file Filepath of the Attached image.
	 * @param string $destfolder Path to destination folder.
	 * @return mixed Metadata for attachment.
	 **/
	static public function handle_upload( &$file, $destfolder, $upload_type = null, $item_id = null,$noimage=0)
	{
		$mainframe = JFactory::getApplication();
		$config = Pago::get_instance( 'config' )->get();
		jimport( 'joomla.filesystem.file' );
		$variation_id = JFactory::getApplication()->input->getInt( 'variation_id' );
		$dispatcher = KDispatcher::getInstance();

		$time = gmdate( 'Y-m-d H:i:s', ( time() + ( $mainframe->getCfg('offset') * 3600 ) ) );

		// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
		$upload_error_strings = array( false,
			JText::_( "The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>." ),
			JText::_( "The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form." ),
			JText::_( "The uploaded file was only partially uploaded." ),
			JText::_( "No file was uploaded." ),
			'',
			JText::_( "Missing a temporary folder." ),
			JText::_( "Failed to write file to disk." ));

		// If you want to override the mime types
		$mimes = false;
		$mime_check = true;

		// Check for any errors
		if ( isset($file['error']) && $file['error'] > 0 ) {
			return PagoImageHandlerHelper::handle_upload_error( $file,
				$upload_error_strings[$file['error']] );
		}

		// A non-empty file will pass this test.
		if ( isset($file['size']) && !($file['size'] > 0 ) ) {
			return PagoImageHandlerHelper::handle_upload_error( $file, JText::_( 'File is empty.
			 	Please upload something more substantial.
				This error could also be caused by uploads being disabled in your php.ini.' ) );
		}

		// Check for file attacks
		if ( !@is_uploaded_file( $file['tmp_name'] ) && !$noimage) {
			return PagoImageHandlerHelper::handle_upload_error( $file,
				JText::_( 'Specified file failed upload test.' ) );
		}

		// A correct MIME type will pass this test
		$dispatcher->trigger( 'upload_check_filetype',
			array( &$mimes, &$mime_check, $upload_type ) );
		$filetype = PagoImageHandlerHelper::check_filetype( $file['name'], $mimes, $mime_check );

		extract( $filetype );

		if ( !$type || !$ext ) {
			return PagoImageHandlerHelper::handle_upload_error( $file,
				JText::_( 'File type does not meet security guidelines. Try another.' ) );
		}
		// A writable uploads dir will pass this test.
		if ( !( ( $uploads = PagoImageHandlerHelper::upload_dir( $destfolder ) )
			&& false === $uploads['error'] )
		) {

			return PagoImageHandlerHelper::handle_upload_error( $file, $uploads['error'] );
		}
		
		if($variation_id){
			//$upload_type = 'var';
		}
		
		$images_use_unique_name   = $config->get( 'media.images_use_unique_name', 1 );
		$images_add_suffix_image   = $config->get( 'media.images_add_suffix_image', 1 );
		$name_prefix = '';
		if($images_add_suffix_image)
		{
			if ( 'user' == $upload_type ) {
				$name_prefix = $upload_type . '-';
			}
			if ( 'images' == $upload_type ) {
				$name_prefix = 'item-' .$item_id. '-';
			}
			
			if ( $variation_id ) {
				$name_prefix = 'var-' .$variation_id. '-';
			}
			
			if ( 'video' == $upload_type ) {
				$name_prefix = 'item-' .$item_id. '-';
			}
		}
	
		if($images_use_unique_name)
		{
			$filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix
			. $file['name'] );
		}
		else
		{
			$filename = $file['name'];
		}
		
		if($variation_id){
			$filename = str_replace("item-{$item_id}-", "var-{$variation_id}-", $filename);
		}
		
		// Move the file to the uploads dir
		$new_file = $uploads['path'] . "/$filename";
		if( ($noimage && false === JFile::copy( $file['tmp_name'], $new_file )) || (!$noimage && false === JFile::upload( $file['tmp_name'], $new_file ) && !file_exists($new_file) ) ){
			return PagoImageHandlerHelper::handle_upload_error( $file, sprintf(
				JText::_('The uploaded file could not be moved to %s.' ), $uploads['path'] ) );
		}


		// Set correct file permissions
		$stat = stat( dirname( $new_file ) );
		$perms = $stat['mode'] & 0000666;
		@chmod( $new_file, $perms );

		// Compute the URL
		$url = $uploads['url'] . "/$filename";

		$return = array( 'file' => $new_file, 'file_name' => $filename, 'url' => $url,
			'type' => $type );

		return $return;
	}

	/**
	 * Start-up function to generate all thumbnails
	 *
	 * @param int $attachment_id Attachment Id to process.
	 * @param string $file Filepath of the Attached image.
	 * @return mixed Metadata for attachment.
	 **/
	static public function generate_image_metadata( $file, &$data, $config )
	{
		$metadata = array();

		if ( preg_match( '!^image/!', $data['mime_type'] )
			&& PagoImageHandlerHelper::file_is_displayable_image( $file )
		) {
			$full_path_file = $file;
			$imagesize = getimagesize( $full_path_file );
			$metadata['width'] = $imagesize[0];
			$metadata['height'] = $imagesize[1];

			list( $uwidth, $uheight ) = PagoImageHandlerHelper::shrink_dimensions(
				$metadata['width'], $metadata['height'] );
			$metadata['hwstring_small'] = "height='$uheight' width='$uwidth'";

			// Make thumbnails and other intermediate sizes
			$sizes = (array) $config->get( 'media.image_sizes' );
			$dispatcher = KDispatcher::getInstance();

			foreach ( $sizes as $size => $_sizes ) {
				JPluginHelper::importPlugin( 'pago_products' );
				$result = array();
				$dispatcher->trigger(
					'image_size_upload_override',
					array( &$result, $size, $data, $full_path_file )
				);
				$full_path_file_dest = null;
				
				if(isset($result['full_path_file']) && $result['full_path_file'] != "")
				{
					$full_path_file_dest = $result['full_path_file'];
				}
				$resized = PagoImageHandlerHelper::image_make_intermediate_size(
					$full_path_file,
					$_sizes->width,
					$_sizes->height,
					$_sizes->crop , false, $full_path_file_dest );

				if ( $resized && !isset( $resized['error'] ) ) {
					$metadata['sizes'][$size] = $resized;
				} else {
					$metadata['sizes']['error'] = JText::_( 'PAGO_RESIZE_ERROR_MAYBE_GD_LIB_NOT_INSTALLED' );
				}
			}
		}

		$metadata = $data['file_meta'] = array_merge( $metadata, $data['file_meta'] );

		return $metadata;
	}
	static public function generate_attribute_image($file){
		$config = Pago::get_instance( 'config' )->get();

		$metadata = array();

		$full_path_file = $file;
		$imagesize = getimagesize( $full_path_file );
		$metadata['width'] = $imagesize[0];
		$metadata['height'] = $imagesize[1];

		$sizes = (array) $config->get( 'media.image_sizes' );

		foreach ( $sizes as $size => $_sizes ) {
			$resized = PagoImageHandlerHelper::image_make_intermediate_size(
				$full_path_file,
				$_sizes->width,
				$_sizes->height,
				$_sizes->crop,
				$size);
		}
	}
}

?>
