<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.model' );
Pago::load_helpers( 'imagehandler' );

class PagoModelUpload extends JModelLegacy
{
	var $_data;
	var $_product;
	var $_category;
	var $_id;

	function __construct()
	{
		parent::__construct();
		$array = JFactory::getApplication()->input->get( 'id',  0, 'array' );
		$this->setId( $array[0] );
	}

	function setId( $id )
	{
		$this->_id = $id;
		$this->_data = null;
	}

	function getData()
	{
		$mainframe = JFactory::getApplication();

		$id = JFactory::getApplication()->input->get( 'inserted_id' );
		if ( !$id ) { return array(); }

		if ( empty( $this->_data ) ) {
			$query = $this->_buildQuery( $id );
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();

			// Unserialize meta
			if ( !empty( $this->_data ) ) {
				$this->_data->file_meta = unserialize( $this->_data->file_meta );
			}
		}

		return $this->_data;
	}

	function _buildQuery( $id )
	{
		$query = "SELECT c.*
					FROM #__pago_files AS c
						WHERE c.id = {$id}";
		return $query;
	}

	function getProduct()
	{
		if ( empty( $this->_product ) ) {
			$id = JFactory::getApplication()->input->getInt( 'item_id' );
			$this->_product = PagoHelper::get_product( $id );
		}

		return $this->_product;
	}

	function getCategory()
	{
		if ( empty( $this->_category ) ) {
			if(!$this->_product){
				$this->_category = Pago::get_instance( 'categoriesi' )->get( 1 );
			}else{
				$this->_category = Pago::get_instance( 'categoriesi' )->get( $this->_product->primary_category );
			}
		}

		return $this->_category;
	}

	function handle_upload()
	{
		
		//$variation_path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $varationId .DIRECTORY_SEPARATOR;
		
		$dispatcher = KDispatcher::getInstance();
		$params = Pago::get_instance( 'config' )->get();
		$variation_id = JFactory::getApplication()->input->getInt( 'variation_id' );//variation_image
		$upload_type = JFactory::getApplication()->input->getWord( 'type' );

		$path = trim(
			$params->get( 'media.'.$upload_type . '_file_path', 'media' .DS. 'pago' ), DS
		);
		$dispatcher->trigger( 'upload_file_path', array( &$path, $upload_type ) );

		if ( in_array( $upload_type, array( 'images', 'user', 'store_default', 'download', 'video' ) ) ) {

			$this->getProduct();
			$this->getCategory();
			if(!$this->_product){
				$item_id = JFactory::getApplication()->input->getInt( 'item_id' );
			}else{
				$item_id = $this->_product->id;
			}
			$path_extra = JFilterOutput::stringURLSafe( $this->_category->id );
			$path_extra = 'items' .DS. $path_extra;  
			
			if($variation_id){
				//$upload_type = 'variation';
				$path_extra = 'product_variation' .DS. $variation_id;
			} 
				 
			
		} elseif ($upload_type == "category") {
			// for category
			/*$item_id = JRequest::getInt( 'item_id', '' );
			$path_extra = JPath::clean( JFolder::makeSafe( JRequest::getString( 'path' ) ) );

			$dispatcher->trigger( 'file_uploader_get_vars',
				array( &$item_id, &$path_extra, $upload_type, 'abs_path' ) );

			$path_extra = 'category' .DS. $path_extra;*/
			$this->getProduct();
			$this->getCategory();
			$item_id = JFactory::getApplication()->input->get('item_id');
			$path_extra = JFilterOutput::stringURLSafe( $item_id );
			$path_extra = 'category' .DS. $path_extra;

		} else {
			// This is here for extendibility
			$item_id = JFactory::getApplication()->input->getInt( 'item_id', '' );
			$path_extra = JPath::clean( JFolder::makeSafe( JFactory::getApplication()->input->getString( 'path' ) ) );

			$dispatcher->trigger( 'file_uploader_get_vars',
				array( &$item_id, &$path_extra, $upload_type, 'abs_path' ) );
		}
		
		if ( !$path ) {
			JFactory::getApplication()->input->set( 'error', JText::_( 'Unexpectect Error' ) );
			return false;
		}


		// $uploadFiles = JFactory::getApplication()->input->files->get('upload', array(), 'array');
		$uploadFiles = JFactory::getApplication()->input->files->get('Filedata', array(), 'array');
		//$uploadFiles = $_FILES["Filedata"];
		//  added trigger to override extra path for image upload
		// update  extra path for image upload
		JPluginHelper::importPlugin( 'pago_products' );
		$result = array();
		$dispatcher->trigger(
			'image_upload_override',
			array( &$result, $this->_category, $path, $uploadFiles, $upload_type )
		);
		if(isset($result['path']) && $result['path'] != "")
		{
			$path_extra = $result['path'];
		}
		//end
		if(!count($uploadFiles)){
			$uploadFiles['name'] = 'noimage.jpg';
			$uploadFiles['type'] = 'application/octet-stream';
			$uploadFiles['tmp_name'] = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_pago'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'noimage.jpg';
			$file = PagoImageHandlerHelper::handle_upload(
			$uploadFiles,
			$path . DIRECTORY_SEPARATOR . trim($path_extra, DIRECTORY_SEPARATOR), $upload_type, $item_id,1);
		}else{
			$file = PagoImageHandlerHelper::handle_upload(
			$uploadFiles,
			$path . DIRECTORY_SEPARATOR . trim($path_extra, DIRECTORY_SEPARATOR), $upload_type, $item_id);
		}

		if ( isset( $file['error'] ) ) {
			JFactory::getApplication()->input->set( 'error', $file['error'] );
			return false;
		}

		return $this->add_file( $file, $upload_type, $item_id );
	}

	function add_file( $file, $upload_type, $item_id )
	{
		$dispatcher = KDispatcher::getInstance();
		$params = Pago::get_instance( 'config' )->get();
		$variation_id = JFactory::getApplication()->input->getInt( 'variation_id' );
		$data_type = $upload_type;
		
		if($variation_id) 
				$data_type = 'variation';
				
		$url       = $file['url'];
		$type      = $file['type'];
		$file_name = $file['file_name'];
		$file      = $file['file'];
		$title     = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
		$content   = '';
		$file_meta = array();


		if ( in_array( $upload_type, array( 'images', 'user' ) ) ) {
			// Use image exif/iptc data for title and caption defaults if possible
			if ( $file_meta = @PagoImageHandlerHelper::read_image_metadata( $file ) ) {
				if ( trim( $file_meta['title'] ) ) {
					$title = $file_meta['title'];
				}
				if ( trim( $file_meta['caption'] ) ) {
					$content = $file_meta['caption'];
				}
			}
		}

		// Prepare array to insert into db
		$data = array(
			'title'     => $title,
			'caption'   => $content,
			'item_id'   => $item_id,
			'default'   => 0,
			'published' => 'files' == $upload_type ? 1 : $params->get(
				'media.images_auto_publish', 1
			),
			'access'    => 'files' == $upload_type ? 0 : $params->get(
				'media.images_default_access', 0
			),
			'file_name' => $file_name,
			'type'      => $data_type,
			'mime_type' => $type,
			'file_meta' => array( 'file_meta' => $file_meta )
		);

		if ( in_array( $upload_type, array( 'images', 'user', 'category', 'video' ) ) ) {
			// Create thumbnails and other meta data
			PagoImageHandlerHelper::generate_image_metadata( $file, $data, $params );
			
			if(isset($data['file_meta']['sizes']['error'])){
				JFactory::getApplication()->input->set( 'error', $data['file_meta']['sizes']['error'] );
				return false;
			}
			
			// Serialize meta data for storage
			$data['file_meta'] = serialize( $data['file_meta'] );
		}
		if ( !empty( $item_id ) ) {
			$has_images = PagoImageHandlerHelper::get_item_files( $item_id, false,
				array(   'images' ,'video') );

			if ( $upload_type != 'download' && empty( $has_images )) {
				$data['default'] = 1;
			}
		}
		
		if($variation_id){
			$data['title'] = str_replace("item-{$item_id}-", "var-{$variation_id}-", $data['title']);
			$data['provider'] = $variation_id;
		}
		
		
		$dispatcher->trigger( 'files_upload_before_store', array( $data ) );

		// Save the data
		$id = $this->store( $data );

		if ( !$id ) {
			JFactory::getApplication()->input->set( 'error', $this->getError() );
			return false;
		} else {
			JFactory::getApplication()->input->set( 'inserted_id', $id );
			return $id;
		}
	}

	function store( $data )
	{
		$row = $this->getTable( 'files', 'Table' );

		if ( !$row->bind( $data ) ) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		// Prepare the content for saving to the database
		PagoHelper::saveContentPrep( $row );
		// Quick and ugly fix
		if ( $row->introtext && !$row->fulltext ) {
			$row->fulltext = $row->introtext;
		} elseif ( $row->introtext && $row->fulltext ) {
			$row->fulltext = $row->introtext .' '. $row->fulltext;
		}
		unset( $row->introtext );

		if ( !$row->id ) {
			$row->ordering
				= $row->getNextOrder( "`item_id` = {$row->item_id} AND `type` = '$row->type'" );
		}

		if ( !$row->check() ) {
			$this->setError( $row->getError() );
			return false;
		}

		if ( !$row->store() ) {
			$this->setError( $row->getError() );
			return false;
		}

		return $row->id;
	}
}
?>
