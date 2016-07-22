<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }
jimport('joomla.filesystem.file');
class PagoImageHelper
{
	static public function unique_filename( $dir, $filename )
	{
		$filename = strtolower( $filename );

		// Separate the filename into a name and extension
		$info = pathinfo( $filename );
		$ext = !empty( $info['extension']) ? $info['extension'] : '';
		$name = basename( $filename, ".{$ext}" );

		// Edge case: if file is named '.ext', treat as an empty name
		if ( $name === ".$ext" ) {
			$name = '';
		}

		// Increment the file number until we have a unique file to save in $dir
		$number = '';

		if ( !empty( $ext ) ) {
			$ext = strtolower( ".$ext" );
		}

		$filename = str_replace( $ext, '', $filename );
		// Strip % so the server doesn't try to decode entities.
		$filename = str_replace( '%', '',
			JFilterOutput::stringURLSafe( str_replace('_', '-', $filename) ) ) . $ext;

		while ( file_exists( $dir . "/$filename" ) ) {
			if ( '' == "$number$ext" ) {
				$filename = $filename . ++$number . $ext;
			} else {
				$filename = str_replace( "$number$ext", ++$number . $ext, $filename );
			}
		}

		return $filename;
	}

	static public function upload_dir( $dir = null )
	{
		jimport('joomla.filesystem.folder');
		
		$dir = rtrim( $dir, '/' );

		if ( !$dir ) {
			return array( 'error' => JText::_( 'No directory to create' ) );
		}

		if ( strpos( $dir, JPATH_ROOT ) === false ) {
			$dir = JPATH_ROOT . DS . $dir;
		}

		// Create subfolder and all parent folders
		if ( !JFolder::exists( $dir ) ) {
			// Create Folder
			if ( !JFolder::create( $dir, 0755 ) ) {
				$message = sprintf( JText::_( 'Unable to create directory %s. Is its parent
				 	directory writable by the server?' ), $dir );
				return array( 'error' => $message );
			}

			// Create index.html file
			$path = $dir.DS.'index.html';
			$con = '<html />';
			
			JFile::write( $path, $con );
		}

		// Set variables
		$baseurl = str_replace( array( 'components/com_pago/helpers/', 'administrator/' ), '',
		 	JURI::root() );
		$subdir  = trim( str_replace( array( JPATH_ROOT, DS ), array( '', '/' ), $dir ), '/' );
		$url     = $baseurl . $subdir;

		$uploads = array( 'path' => $dir, 'url' => $url, 'subdir' => $subdir,
			'basedir' => JPATH_ROOT, 'baseurl' => $baseurl, 'error' => false );


		return $uploads;
	}

	static public function handle_upload_error( &$file, $message )
	{
		return array( 'error' => $message );
	}

	/**
	 * Calculates the new dimentions for a downsampled image.
	 *
	 * Same as {@link shrink_dimensions()}, except the max parameters are
	 * optional. If either width or height are empty, no constraint is applied on
	 * that dimension.
	 *
	 * @since 2.5.0
	 *
	 * @param int $current_width Current width of the image.
	 * @param int $current_height Current height of the image.
	 * @param int $max_width Optional. Maximum wanted width.
	 * @param int $max_height Optional. Maximum wanted height.
	 * @return array First item is the width, the second item is the height.
	 */
	static public function constrain_dimensions( $current_width, $current_height, $max_width = 0,
		$max_height = 0 )
	{
		if ( !$max_width and !$max_height ) {
			return array( $current_width, $current_height );
		}

		$width_ratio = $height_ratio = 1.0;

		if ( $max_width > 0 && $current_width > $max_width ) {
			$width_ratio = $max_width / $current_width;
		}

		if ( $max_height > 0 && $current_height > $max_height ) {
			$height_ratio = $max_height / $current_height;
		}

		// the smaller ratio is the one we need to fit it to the constraining box
		$ratio = min( $width_ratio, $height_ratio );

		return array( intval( $current_width * $ratio ), intval( $current_height * $ratio ) );
	}

	/**
	 * Retrieve calculated resized dimensions for use in imagecopyresampled().
	 *
	 * Calculate dimensions and coordinates for a resized image that fits within a
	 * specified width and height. If $crop is true, the largest matching central
	 * portion of the image will be cropped out and resized to the required size.
	 *
	 * @param int $orig_w Original width.
	 * @param int $orig_h Original height.
	 * @param int $dest_w New width.
	 * @param int $dest_h New height.
	 * @param bool $crop Optional, default is false. Whether to crop image or resize.
	 * @return bool|array False, on failure. Returned array matches parameters for imagecopyresampled() PHP function.
	 */
	static public function image_resize_dimensions( $orig_w,
													$orig_h,
													$dest_w,
													$dest_h,
													$crop = false )
	{
		if ( $orig_w <= 0 || $orig_h <= 0 ) {
			return false;
		}

		// At least one of dest_w or dest_h must be specific
		if ( $dest_w <= 0 && $dest_h <= 0 ) {
			return false;
		}

		if ( $crop ) {
			// Crop the largest possible portion of the original image
			// that we can size to $dest_w x $dest_h
			$aspect_ratio = $orig_w / $orig_h;
			$new_w = min( $dest_w, $orig_w );
			$new_h = min( $dest_h, $orig_h );
			if ( !$new_w ) {
				$new_w = intval( $new_h * $aspect_ratio );
			}
			if ( !$new_h ) {
				$new_h = intval( $new_w / $aspect_ratio );
			}

			$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

			$crop_w = ceil( $new_w / $size_ratio );
			$crop_h = ceil( $new_h / $size_ratio );

			$s_x = floor( ( $orig_w - $crop_w ) / 2 );
			$s_y = floor( ( $orig_h - $crop_h ) / 2 );
		} else {
			// Don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
			$crop_w = $orig_w;
			$crop_h = $orig_h;

			$s_x = 0;
			$s_y = 0;

			list( $new_w, $new_h ) = PagoImageHandlerHelper::constrain_dimensions(
				$orig_w, $orig_h, $dest_w, $dest_h );
		}

		// if the resulting image would be the same size or larger we don't want to resize it
		if ( $new_w >= $orig_w && $new_h >= $orig_h ) {
			//return false;
		}

		// The return array matches the parameters to imagecopyresampled()
		// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
		return array( 0, 0, $s_x, $s_y, $new_w, $new_h, $crop_w, $crop_h );

	}

	/**
	 * Load an image from a string, if PHP supports it.
	 *
	 * @param string $file Filename of the image to load.
	 * @return resource The resulting image resource on success, Error string on failure.
	 */
	static public function load_image( $file )
	{
		if ( !file_exists( $file ) ) {
			return sprintf( JText::_( "File '%s' doesn't exist?" ), $file );
		}

		if ( !function_exists( 'imagecreatefromstring' ) ) {
			return JText::_( 'The GD image library is not installed.' );
		}

		// Set artificially high because GD uses uncompressed images in memory
		@ini_set( 'memory_limit', '256M' );
		$image = imagecreatefromstring( file_get_contents( $file ) );

		if ( !is_resource( $image ) ) {
			return sprintf( JText::_( "File '%s' is not an image." ), $file );
		}

		return $image;
	}

	static public function file_is_displayable_image( $path )
	{
		$info = @getimagesize( $path );

		if ( empty( $info ) ) {
			$result = false;
		} elseif ( !in_array( $info[2], array( IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG) ) ) {
			// only gif, jpeg and png images can reliably be displayed
			$result = false;
		} else {
			$result = true;
		}

		return $result;
	}

	/**
	 * Calculates the new dimentions for a downsampled image.
	 *
	 * @param int $width Current width of the image
	 * @param int $height Current height of the image
	 * @param int $wmax Maximum wanted width
	 * @param int $hmax Maximum wanted height
	 * @return mixed Array(height,width) of shrunk dimensions.
	 */
	static public function shrink_dimensions( $width, $height, $wmax = 128, $hmax = 96 )
	{
		return PagoImageHandlerHelper::constrain_dimensions( $width, $height, $wmax, $hmax );
	}

	/**
	 * Scale down an image to fit a particular size and save a new copy of the image.
	 *
	 * The PNG transparency will be preserved using the function, as well as the
	 * image type. If the file going in is PNG, then the resized image is going to
	 * be PNG. The only supported image types are PNG, GIF, and JPEG.
	 *
	 * @param string $file Image file path.
	 * @param int $max_w Maximum width to resize to.
	 * @param int $max_h Maximum height to resize to.
	 * @param bool $crop Optional. Whether to crop image or resize.
	 * @param string $suffix Optional. File Suffix.
	 * @param string $dest_path Optional. New image file path.
	 * @param int $jpeg_quality Optional, default is 90. Image quality percentage.
	 * @return mixed Error on failure. String with new destination path. Array of dimensions from {@link image_resize_dimensions()}
	 */
	static public function image_resize(    $file,
											$max_w,
											$max_h,
											$crop = false,
											$suffix = null,
											$dest_path = null,
											$jpeg_quality = 100 )
	{
		$image = PagoImageHandlerHelper::load_image( $file );
		if ( !is_resource( $image ) ) {
			return array( 'error' => 'error_loading_image: '. $image );
		}

		list( $orig_w, $orig_h, $orig_type ) = getimagesize( $file );
		$dims = PagoImageHandlerHelper::image_resize_dimensions( $orig_w, $orig_h, $max_w, $max_h,
			 $crop);

		if ( !$dims ) {
			return $dims;
		}

		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

		$newimage = imagecreatetruecolor( $dst_w, $dst_h );

		// Preserve PNG transparency
		if ( IMAGETYPE_PNG == $orig_type && function_exists( 'imagealphablending' )
			&& function_exists( 'imagesavealpha' )
		) {
			imagealphablending( $newimage, false );
			imagesavealpha( $newimage, true );
		}

		imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h,
			 $src_w, $src_h);

		// We don't need the original in memory anymore
		imagedestroy( $image );

		$config = Pago::get_instance( 'config' )->get();
		$images_add_suffix_image   = $config->get( 'media.images_add_suffix_image', 1 );
		// $suffix will be appended to the destination filename, just before the extension
		if ( !$suffix && $images_add_suffix_image) {
			$suffix = "{$dst_w}x{$dst_h}";
		}

		$info = pathinfo( $file );
		$dir = $info['dirname'];
		$ext = $info['extension'];
		$name = basename( $file, ".{$ext}" );

		if ( !is_null( $dest_path ) && $_dest_path = realpath( $dest_path ) ) {
			$dir = $_dest_path;
		}

		if ( !is_null( $dest_path ) ) {
			$dir = $dest_path;
		}
		PagoImageHandlerHelper::upload_dir($dir);
		

		$jconfig = new JConfig();
		$tempfilename = "{$jconfig->tmp_path}/{$name}-{$suffix}.{$ext}";
		$destfilename = "{$dir}/{$name}-{$suffix}.{$ext}";

		if ( $orig_type == IMAGETYPE_GIF ) {
			if ( !@imagegif( $newimage, $destfilename ) ) {
				$error = array(
					'error' => 'resize_path_invalid: ' . JText::_( 'Resize path invalid') );
			}
		} elseif ( $orig_type == IMAGETYPE_PNG ) {
			if ( !@imagepng( $newimage, $destfilename ) ) {
				$error = array(
					'error' => 'resize_path_invalid: ' . JText::_( 'Resize path invalid' ) );
			}
		} else {
			if($ext == 'jpeg'){
				$fileFormat = 'jpeg';
			}else{
				$fileFormat = 'jpg';
			}
			// All other formats are converted to jpg
			$destfilename = "{$dir}/{$name}-{$suffix}.{$fileFormat}";
			if ( !@imagejpeg( $newimage, $destfilename, $jpeg_quality ) ) {
				$error = array(
					'error' => 'resize_path_invalid: ' . JText::_( 'Resize path invalid' ) );
			}
		}

		if ( isset( $error ) ) {
			if ( $orig_type == IMAGETYPE_GIF ) {
				if ( !@imagegif( $newimage, $tempfilename ) ) {
					return array(
						'error' => 'resize_path_invalid: ' . JText::_( 'Resize path invalid' ) );
				}
			} elseif ( $orig_type == IMAGETYPE_PNG ) {
				if ( !@imagepng( $newimage, $tempfilename ) ) {
					return array(
						'error' => 'resize_path_invalid: ' . JText::_( 'Resize path invalid' ) );
				}
			} else {
				if($ext == 'jpeg'){
					$fileFormat = 'jpeg';
				}else{
					$fileFormat = 'jpg';
				}
				// All other formats are converted to jpg
				$destfilename = "{$dir}/{$name}-{$suffix}.{$fileFormat}";
				if ( !@imagejpeg( $newimage, $tempfilename, $jpeg_quality ) ) {
					return array(
						'error' => 'resize_path_invalid: ' . JText::_( 'Resize path invalid' ) );
				}
			}

			JFile::move( $tempfilename, $destfilename );
		}

		imagedestroy( $newimage );

		// Set correct file permissions
		$stat = stat( dirname( $destfilename ) );
		// Same permissions as parent folder, strip off the executable bits
		$perms = $stat['mode'] & 0000666;
		@chmod( $destfilename, $perms );

		return $destfilename;
	}

	/**
	 * Resize an image to make a thumbnail or intermediate size.
	 *
	 * The returned array has the file size, the image width, and image height. The
	 * filter 'image_make_intermediate_size' can be used to hook in and change the
	 * values of the returned array. The only parameter is the resized file path.
	 *
	 * @param string $file File path.
	 * @param int $width Image width.
	 * @param int $height Image height.
	 * @param bool $crop Optional, default is false. Whether to crop image to specified height and width or resize.
	 * @return bool|array False, if no image was created. Metadata array on success.
	 */
	static public function image_make_intermediate_size( $file, $width, $height, $crop = false, $suffix = false, $destination_path= null )
	{
		if ( $width || $height ) {
			$resized_file = PagoImageHandlerHelper::image_resize( $file, $width, $height, $crop, $suffix, $destination_path );
			
			if ( is_string( $resized_file ) && $resized_file
				&& $info = getimagesize( $resized_file )
			) {
				return array(
					'file' => basename( $resized_file ),
					'width' => $info[0],
					'height' => $info[1],
				);
			}
			
			return array('error'=>@$resized_file['error']);
		}

		return false;
	}

	/**
	 * Convert a fraction string to a decimal.
	 *
	 * @param string $str
	 * @return int|float
	 */
	static public function exif_frac2dec( $str )
	{
		@list( $n, $d ) = explode( '/', $str );
		if ( !empty( $d ) ) {
			return $n / $d;
		}

		return $str;
	}

	/**
	 * Convert the exif date format to a unix timestamp.
	 *
	 * @since 2.5.0
	 *
	 * @param string $str
	 * @return int
	 */
	static public function exif_date2ts( $str )
	{
		@list( $date, $time ) = explode( ' ', trim( $str ) );
		@list( $y, $m, $d ) = explode( ':', $date );

		return strtotime( "{$y}-{$m}-{$d} {$time}" );
	}

	/**
	 * Get extended image metadata, exif or iptc as available.
	 *
	 * Retrieves the EXIF metadata aperture, credit, camera, caption, copyright, iso
	 * created_timestamp, focal_length, shutter_speed, and title.
	 *
	 * The IPTC metadata that is retrieved is APP13, credit, byline, created date
	 * and time, caption, copyright, and title. Also includes FNumber, Model,
	 * DateTimeDigitized, FocalLength, ISOSpeedRatings, and ExposureTime.
	 *
	 * @todo Try other exif libraries if available.
	 *
	 * @param string $file
	 * @return bool|array False on failure. Image metadata array on success.
	 * Credit to wordpress.com for this function
	 */
	static public function read_image_metadata( $file )
	{
		if ( !file_exists( $file ) ) {
			return false;
		}

		list( ,, $sourceImageType ) = getimagesize( $file );

		// exif contains a bunch of data we'll probably never need formatted in ways
		// that are difficult to use. We'll normalize it and just extract the fields
		// that are likely to be useful.  Fractions and numbers are converted to
		// floats, dates to unix timestamps, and everything else to strings.
		$meta = array(
			'aperture'          => 0,
			'credit'            => '',
			'camera'            => '',
			'make'              => '',
			'caption'           => '',
			'created_timestamp' => 0,
			'copyright'         => '',
			'focal_length'      => 0,
			'iso'               => 0,
			'shutter_speed'     => 0,
			'title'             => '',
		);

		// Read iptc first, since it might contain data not available in exif such
		// as caption, description etc
		if ( is_callable( 'iptcparse' ) ) {
			getimagesize( $file, $info );
			if ( !empty( $info['APP13'] ) ) {
				$iptc = iptcparse( $info['APP13'] );

				if ( !empty( $iptc['2#110'][0] ) ) { // Credit
					$meta['credit'] = utf8_encode( trim( $iptc['2#110'][0] ) );
				} elseif ( !empty( $iptc['2#080'][0] ) ) { // byline
					$meta['credit'] = utf8_encode( trim( $iptc['2#080'][0] ) );
				}

				// Created date and time
				if ( !empty( $iptc['2#055'][0] ) && !empty( $iptc['2#060'][0] ) ) {
					$meta['created_timestamp'] = strtotime( $iptc['2#055'][0]
						. ' ' . $iptc['2#060'][0] );
				}
				if ( !empty( $iptc['2#120'][0] ) ) { // Caption
					$meta['caption'] = utf8_encode( trim( $iptc['2#120'][0] ) );
				}
				if ( !empty( $iptc['2#116'][0] ) ) { // Copyright
					$meta['copyright'] = utf8_encode( trim( $iptc['2#116'][0] ) );
				}
				if ( !empty( $iptc['2#005'][0] ) ) { // Title
					$meta['title'] = utf8_encode( trim( $iptc['2#005'][0] ) );
				}
			}
		}


		// Fetch additional info from exif if available
		if ( is_callable( 'exif_read_data' ) && in_array( $sourceImageType,
			array( IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM ) )
		) {
			$exif = @exif_read_data( $file );
			if ( !empty( $exif['FNumber'] ) ) {
				$meta['aperture']
					= round( PagoImageHandlerHelper::exif_frac2dec( $exif['FNumber'] ), 2 );
			}
			if ( !empty( $exif['Make'] ) ) {
				$meta['make'] = trim( $exif['Make'] );
			}
			if ( !empty( $exif['Model'] ) ) {
				$meta['camera'] = trim( $exif['Model'] );
			}
			if ( !empty( $exif['DateTimeDigitized'] ) ) {
				$meta['created_timestamp']
					= PagoImageHandlerHelper::exif_date2ts( $exif['DateTimeDigitized'] );
			}
			if ( !empty( $exif['FocalLength'] ) ) {
				$meta['focal_length']
					= PagoImageHandlerHelper::exif_frac2dec( $exif['FocalLength'] );
			}
			if ( !empty( $exif['ISOSpeedRatings'] ) ) {
				$meta['iso'] = $exif['ISOSpeedRatings'];
			}
			if ( !empty( $exif['ExposureTime'] ) ) {
				$meta['shutter_speed']
					= PagoImageHandlerHelper::exif_frac2dec( $exif['ExposureTime'] );
			}
			if ( !$meta['credit'] && !empty( $exif['OwnerName'] ) ) {
				$meta['credit'] = trim( $exif['OwnerName'] );
			}
		}

		return $meta;
	}

	static public function check_filetype( $filename, $mimes = null, $mime_check = true )
	{
		$type = false;
		$ext  = false;

		// If we don't want to do a mime check and just return the type and ext
		if ( false == $mime_check ) {
			$type = PagoImageHelper::__getMimeType( $filename );
			$ext  = strtolower( pathInfo( $filename, PATHINFO_EXTENSION ) );
		}
		// Accepted MIME types are set here as PCRE unless provided.
		$mimes = ( is_array( $mimes ) ) ? $mimes : PagoImageHelper::get_allowed_mime_types();

		foreach ( $mimes as $ext_preg => $mime_match ) {
			$ext_preg = '!\.(' . $ext_preg . ')$!i';
			if ( preg_match( $ext_preg, $filename, $ext_matches ) ) {
				$type = $mime_match;
				$ext = $ext_matches[1];
				break;
			}
		}

		return compact( 'ext', 'type' );
	}

	static public function get_allowed_mime_types()
	{
		static $mimes = false;

		if ( !$mimes ) {
			$dispatcher = KDispatcher::getInstance();

			// Accepted MIME types are set here as PCRE unless provided.
			$mimes = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png',
			'bmp' => 'image/bmp',
			'tif|tiff' => 'image/tiff',
			'ico' => 'image/x-icon',
			'asf|asx|wax|wmv|wmx' => 'video/asf',
			'avi' => 'video/avi',
			'divx' => 'video/divx',
			'flv' => 'video/x-flv',
			'mov|qt' => 'video/quicktime',
			'mpeg|mpg|mpe' => 'video/mpeg',
			'txt|asc|c|cc|h' => 'text/plain',
			'csv' => 'text/csv',
			'tsv' => 'text/tab-separated-values',
			'ics' => 'text/calendar',
			'rtx' => 'text/richtext',
			'css' => 'text/css',
			'htm|html' => 'text/html',
			'mp3|m4a|m4b' => 'audio/mpeg',
			'mp4|m4v' => 'video/mp4',
			'ra|ram' => 'audio/x-realaudio',
			'wav' => 'audio/wav',
			'ogg|oga' => 'audio/ogg',
			'ogv' => 'video/ogg',
			'mid|midi' => 'audio/midi',
			'wma' => 'audio/wma',
			'mka' => 'audio/x-matroska',
			'mkv' => 'video/x-matroska',
			'rtf' => 'application/rtf',
			'js' => 'application/javascript',
			'pdf' => 'application/pdf',
			'doc|docx' => 'application/msword',
			'pot|pps|ppt|pptx|ppam|pptm|sldm|ppsm|potm' => 'application/vnd.ms-powerpoint',
			'wri' => 'application/vnd.ms-write',
			'xla|xls|xlsx|xlt|xlw|xlam|xlsb|xlsm|xltm' => 'application/vnd.ms-excel',
			'mdb' => 'application/vnd.ms-access',
			'mpp' => 'application/vnd.ms-project',
			'docm|dotm' => 'application/vnd.ms-word',
			'pptx|sldx|ppsx|potx' => 'application/vnd.openxmlformats-officedocument.presentationml',
			'xlsx|xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
			'docx|dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml',
			'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
			'swf' => 'application/x-shockwave-flash',
			'class' => 'application/java',
			'tar' => 'application/x-tar',
			'zip' => 'application/zip',
			'gz|gzip' => 'application/x-gzip',
			'exe' => 'application/x-msdownload',
			// openoffice formats
			'odt' => 'application/vnd.oasis.opendocument.text',
			'odp' => 'application/vnd.oasis.opendocument.presentation',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
			'odg' => 'application/vnd.oasis.opendocument.graphics',
			'odc' => 'application/vnd.oasis.opendocument.chart',
			'odb' => 'application/vnd.oasis.opendocument.database',
			'odf' => 'application/vnd.oasis.opendocument.formula',
			// wordperfect formats
			'wp|wpd' => 'application/wordperfect',
			);

			$dispatcher->trigger( 'upload_mimes', array( &$mimes ) );
		}

		return $mimes;
	}

	/**
	* Get MIME type for file
	*
	* @internal Used to get mime types
	* @param string &$file File path
	* @return string
	*/
	public static function __getMimeType( &$file )
	{
		$type = false;
		// Fileinfo documentation says fileinfo_open() will use the
		// MAGIC env var for the magic file
		if ( extension_loaded( 'fileinfo') && isset( $_ENV['MAGIC'] ) &&
			( $finfo = finfo_open( FILEINFO_MIME, $_ENV['MAGIC'] ) ) !== false ) {
			if ( ( $type = finfo_file( $finfo, $file ) ) !== false ) {
				// Remove the charset and grab the last content-type
				$type = explode( ' ', str_replace( '; charset=', ';charset=', $type ) );
				$type = array_pop( $type );
				$type = explode( ';', $type );
				$type = trim( array_shift( $type ) );
			}
			finfo_close( $finfo );

		// If anyone is still using mime_content_type()
		}// elseif (function_exists('mime_content_type'))
			// $type = trim(mime_content_type($file));

		if ( $type !== false && strlen( $type ) > 0 && $type != 'text/plain' ) return $type;

		// Otherwise do it the old fashioned way
		static $exts = array(
			'3gp' => 'video/3gpp',
			'ai' => 'application/postscript',
			'aif' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'asc' => 'text/plain',
			'atom' => 'application/atom+xml',
			'au' => 'audio/basic',
			'avi' => 'video/x-msvideo',
			'bcpio' => 'application/x-bcpio',
			'bin' => 'application/octet-stream',
			'bmp' => 'image/bmp',
			'cdf' => 'application/x-netcdf',
			'cgm' => 'image/cgm',
			'class' => 'application/octet-stream',
			'cpio' => 'application/x-cpio',
			'cpt' => 'application/mac-compactpro',
			'csh' => 'application/x-csh',
			'css' => 'text/css',
			'dcr' => 'application/x-director',
			'dif' => 'video/x-dv',
			'dir' => 'application/x-director',
			'djv' => 'image/vnd.djvu',
			'djvu' => 'image/vnd.djvu',
			'dll' => 'application/octet-stream',
			'dmg' => 'application/octet-stream',
			'dms' => 'application/octet-stream',
			'doc' => 'application/msword',
			'docx' => 'application/msword',
			'dtd' => 'application/xml-dtd',
			'dv' => 'video/x-dv',
			'dvi' => 'application/x-dvi',
			'dxr' => 'application/x-director',
			'eps' => 'application/postscript',
			'etx' => 'text/x-setext',
			'exe' => 'application/octet-stream',
			'ez' => 'application/andrew-inset',
			'flv' => 'video/x-flv',
			'gif' => 'image/gif',
			'gram' => 'application/srgs',
			'grxml' => 'application/srgs+xml',
			'gtar' => 'application/x-gtar',
			'gz' => 'application/x-gzip',
			'hdf' => 'application/x-hdf',
			'hqx' => 'application/mac-binhex40',
			'htm' => 'text/html',
			'html' => 'text/html',
			'ice' => 'x-conference/x-cooltalk',
			'ico' => 'image/x-icon',
			'ics' => 'text/calendar',
			'ief' => 'image/ief',
			'ifb' => 'text/calendar',
			'iges' => 'model/iges',
			'igs' => 'model/iges',
			'jnlp' => 'application/x-java-jnlp-file',
			'jp2' => 'image/jp2',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'js' => 'application/x-javascript',
			'kar' => 'audio/midi',
			'latex' => 'application/x-latex',
			'lha' => 'application/octet-stream',
			'lzh' => 'application/octet-stream',
			'm3u' => 'audio/x-mpegurl',
			'm4a' => 'audio/mp4a-latm',
			'm4p' => 'audio/mp4a-latm',
			'm4u' => 'video/vnd.mpegurl',
			'm4v' => 'video/x-m4v',
			'mac' => 'image/x-macpaint',
			'man' => 'application/x-troff-man',
			'mathml' => 'application/mathml+xml',
			'me' => 'application/x-troff-me',
			'mesh' => 'model/mesh',
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'mif' => 'application/vnd.mif',
			'mov' => 'video/quicktime',
			'movie' => 'video/x-sgi-movie',
			'mp2' => 'audio/mpeg',
			'mp3' => 'audio/mpeg',
			'mp4' => 'video/mp4',
			'mpe' => 'video/mpeg',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpga' => 'audio/mpeg',
			'ms' => 'application/x-troff-ms',
			'msh' => 'model/mesh',
			'mxu' => 'video/vnd.mpegurl',
			'nc' => 'application/x-netcdf',
			'oda' => 'application/oda',
			'ogg' => 'application/ogg',
			'ogv' => 'video/ogv',
			'pbm' => 'image/x-portable-bitmap',
			'pct' => 'image/pict',
			'pdb' => 'chemical/x-pdb',
			'pdf' => 'application/pdf',
			'pgm' => 'image/x-portable-graymap',
			'pgn' => 'application/x-chess-pgn',
			'pic' => 'image/pict',
			'pict' => 'image/pict',
			'png' => 'image/png',
			'pnm' => 'image/x-portable-anymap',
			'pnt' => 'image/x-macpaint',
			'pntg' => 'image/x-macpaint',
			'ppm' => 'image/x-portable-pixmap',
			'ppt' => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.ms-powerpoint',
			'ps' => 'application/postscript',
			'qt' => 'video/quicktime',
			'qti' => 'image/x-quicktime',
			'qtif' => 'image/x-quicktime',
			'ra' => 'audio/x-pn-realaudio',
			'ram' => 'audio/x-pn-realaudio',
			'ras' => 'image/x-cmu-raster',
			'rdf' => 'application/rdf+xml',
			'rgb' => 'image/x-rgb',
			'rm' => 'application/vnd.rn-realmedia',
			'roff' => 'application/x-troff',
			'rtf' => 'text/rtf',
			'rtx' => 'text/richtext',
			'sgm' => 'text/sgml',
			'sgml' => 'text/sgml',
			'sh' => 'application/x-sh',
			'shar' => 'application/x-shar',
			'silo' => 'model/mesh',
			'sit' => 'application/x-stuffit',
			'skd' => 'application/x-koan',
			'skm' => 'application/x-koan',
			'skp' => 'application/x-koan',
			'skt' => 'application/x-koan',
			'smi' => 'application/smil',
			'smil' => 'application/smil',
			'snd' => 'audio/basic',
			'so' => 'application/octet-stream',
			'spl' => 'application/x-futuresplash',
			'src' => 'application/x-wais-source',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc' => 'application/x-sv4crc',
			'svg' => 'image/svg+xml',
			'swf' => 'application/x-shockwave-flash',
			't' => 'application/x-troff',
			'tar' => 'application/x-tar',
			'tcl' => 'application/x-tcl',
			'tex' => 'application/x-tex',
			'texi' => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'tr' => 'application/x-troff',
			'tsv' => 'text/tab-separated-values',
			'txt' => 'text/plain',
			'ustar' => 'application/x-ustar',
			'vcd' => 'application/x-cdlink',
			'vrml' => 'model/vrml',
			'vxml' => 'application/voicexml+xml',
			'wav' => 'audio/x-wav',
			'wbmp' => 'image/vnd.wap.wbmp',
			'wbxml' => 'application/vnd.wap.wbxml',
			'webm' => 'video/webm',
			'wml' => 'text/vnd.wap.wml',
			'wmlc' => 'application/vnd.wap.wmlc',
			'wmls' => 'text/vnd.wap.wmlscript',
			'wmlsc' => 'application/vnd.wap.wmlscriptc',
			'wmv' => 'video/x-ms-wmv',
			'wrl' => 'model/vrml',
			'xbm' => 'image/x-xbitmap',
			'xht' => 'application/xhtml+xml',
			'xhtml' => 'application/xhtml+xml',
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.ms-excel',
			'xml' => 'application/xml',
			'xpm' => 'image/x-xpixmap',
			'xsl' => 'application/xml',
			'xslt' => 'application/xslt+xml',
			'xul' => 'application/vnd.mozilla.xul+xml',
			'xwd' => 'image/x-xwindowdump',
			'xyz' => 'chemical/x-xyz',
			'zip' => 'application/zip',
		);

		$ext = strtolower( pathInfo( $file, PATHINFO_EXTENSION ) );
		return isset( $exts[$ext] ) ? $exts[$ext] : 'application/octet-stream';
	}

	static public function ext2type( $ext )
	{
		$dispatcher = KDispatcher::getInstance();
		$ext2type = array(
			'audio'       => array( 'aac', 'ac3',  'aif',  'aiff', 'm3a',  'm4a',   'm4b', 'mka', 'mp1', 'mp2',  'mp3', 'ogg', 'oga', 'ram', 'wav', 'wma' ),
			'video'       => array( 'asf', 'avi',  'divx', 'dv',   'flv',  'm4v',   'mkv', 'mov', 'mp4', 'mpeg', 'mpg', 'mpv', 'ogm', 'ogv', 'qt',  'rm', 'vob', 'wmv' ),
			'document'    => array( 'doc', 'docx', 'docm', 'dotm', 'odt',  'pages', 'pdf', 'rtf', 'wp',  'wpd' ),
			'spreadsheet' => array( 'numbers',     'ods',  'xls',  'xlsx', 'xlsb',  'xlsm' ),
			'interactive' => array( 'key', 'ppt',  'pptx', 'pptm', 'odp',  'swf' ),
			'text'        => array( 'asc', 'csv',  'tsv',  'txt' ),
			'archive'     => array( 'bz2', 'cab',  'dmg',  'gz',   'rar',  'sea',   'sit', 'sqx', 'tar', 'tgz',  'zip' ),
			'code'        => array( 'css', 'htm',  'html', 'php',  'js' ),
			'image'       => array( 'jpg', 'jpeg', 'jpe', 'png', 'gif' )
			);

		$dispatcher->trigger( 'ext2type', array( &$ext2type ) );

		foreach ( $ext2type as $type => $exts ) {
			if ( in_array( $ext, $exts ) ) {
				return $type;
			}
		}

		return 'other';
	}

	//Display Primary Item Image
	static public function display_image($images, $size, $alt = '', $title = '', $attributes = '')
	{
		// Making sure that the array of images are objects, if not change to object
		// (encountered a problem with the objects getting converting to arrays during JSON encode
		//	or decode for the cart cookie)
		if( !empty ( $images ) ) {
			if( is_array( $images[0] ) ) {
				foreach( $images as $k => $image )
					$images[$k] = (object) $image;
			}

			foreach($images as $image) {

				if($image->default == 1 || $image->type == 'store_default') {
					($size == 'gallery') ? $image_url = '' : $image_url = PagoImageHandlerHelper::get_image_from_object( $image, $size, true );

					if( empty( $image->caption ) ) {
						$alt_tag = $alt;
						$title_tag = $title;
					} else {
						$alt_tag = $image->caption;
						$title_tag = $image->caption;
					}

					$primary = '<img src="' . $image_url . '" ' . $attributes;

					if( is_array( $size ) ) {
						$dimensions = ' width="' . $size[0] . '" height="' . $size[1] . '"';
					} elseif ( $size == 'quickview' ) {
						$imagedata = PagoHelper::maybe_unserialize($image->file_meta);
						if( !( array_search( $size, array_keys( (array) $imagedata['sizes'] ) ) ) ) {
							$dimensions = ' width="' . $imagedata['width'] . '" height="' . $imagedata['height'] . '"';
						} else {
							$imagedata = $imagedata['sizes'][$size];
							$dimensions = ' width="' . $imagedata['width'] . '" height="' . $imagedata['height'] . '"';
						}
					} else {
						$dimensions = '';
					}

					$primary .= ' id="pg-imageid-' . $image->id . '" class="pg-' . $size
							 . '-img" title="' . $title_tag . '" alt="' . $alt_tag . '" ' . $dimensions . ' />';

					if( $image->type != 'store_default' && $size != 'gallery' )
						$primary .= '</a>';

					echo $primary;
					break;
				}
			}
		}
	}

	//Display Thumbnail Images
	static public function list_images($images, $size, $config, $link_size, $alt = '', $title = '')
	{
		if($link_size == 'large') {
			$max = $config->get( 'img_thumb_amount_item', 4 );
		} elseif($link_size == 'medium') {
			$max = 3;
		}

		if(count( $images ) < $max ) {
			$max = count($images);
		}

		$thumbnails = "<ul class=\"pg-image-{$size}s\">";

		for ( $i = 0; $i < $max; $i++ ) {
			if( empty( $images[$i]->caption ) ) {
				$alt_tag = $alt;
				$title_tag = $title;
			} else {
				$alt_tag = $images[$i]->caption;
				$title_tag = $images[$i]->caption;
			}
			if($images[$i]->type == 'store_default') {
				return;
			}

			$image = PagoImageHandlerHelper::get_image_from_object( $images[$i], $size, true );
			$image_link = PagoImageHandlerHelper::get_image_from_object( $images[$i], $link_size, true );

			$thumbnails .= '<li><span>';
			$thumbnails .= '<a rel="nofollow" href="'
						. JRoute::_( 'index.php?option=com_pago&view=item&layout=gallery&async=2&tmpl=component&id='
						. $images[$i]->item_id ) . '" class="pg-gallery" title="' . $title_tag . '">';
			$thumbnails .= '<img src="' . $image . '"';
			$thumbnails .= "class=\"pg-$size-img\" id=\"pg-imageid-" . $images[$i]->id . "\" title=\""
						. $title_tag . "\" alt=\"" . $alt_tag . "\" rel=\"" . $image_link . "\" />";
			$thumbnails .= '</a></span></li>';
		}

		$thumbnails .= '</ul>';

		echo $thumbnails;
	}

}
?>
