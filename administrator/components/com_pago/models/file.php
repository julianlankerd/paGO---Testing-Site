<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.model' );

class PagoModelFile extends JModelLegacy
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

	function makeDefault() {
		$file = JFactory::getApplication()->input->getInt( 'id', 0 );
		$row  = $this->getTable( 'files', 'Table' );

		$row->load( $file );

		if ( !$row->id ) {
			$this->setError( JText::_( 'Select an image to make it a default' ) );
			return false;
		}

		$row->default = 1;

		if ( !$row->check() ) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		if ( !$row->store() ) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		if($row->type == 'images' || $row->type == 'video'){

			
			$query = "UPDATE #__pago_files
				SET `default` = 0
					WHERE `id` != {$row->id}
					AND `item_id` = {$row->item_id}
					AND (`type` = 'images' || `type` = 'video')";
		}else{
			$query = "UPDATE #__pago_files
				SET `default` = 0
					WHERE `id` != {$row->id}
					AND `item_id` = {$row->item_id}
					AND `type` = '{$row->type}'";	
		}
		$this->_db->setQuery( $query );
		$this->_db->query();

		return $row->id;
	}
	function removeDefault() {
		$file = JFactory::getApplication()->input->getInt( 'id', 0 );
		$row  = $this->getTable( 'files', 'Table' );

		$row->load( $file );

		if ( !$row->id ) {
			$this->setError( JText::_( 'Select an image to remove it a default' ) );
			return false;
		}

		$row->default = 0;

		if ( !$row->check() ) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		if ( !$row->store() ) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		if($row->type == 'images' || $row->type == 'video'){

			
			$query = "UPDATE #__pago_files
				SET `default` = 0
					WHERE `id` != {$row->id}
					AND `item_id` = {$row->item_id}
					AND (`type` = 'images' || `type` = 'video')";
		}else{
			$query = "UPDATE #__pago_files
				SET `default` = 0
					WHERE `id` != {$row->id}
					AND `item_id` = {$row->item_id}
					AND `type` = '{$row->type}'";	
		}
		$this->_db->setQuery( $query );
		$this->_db->query();

		return $row->id;
	}
	function copy($data = array(), $newItemId, $path){

		jimport( 'joomla.filesystem.file' );
		$oldFileName ='';
		$oldFileMeta ='';
		$newFileName ='';
		$oldItemId = $data['item_id'];

		$data['id'] = 0;
		$data['item_id'] = $newItemId;
		$data['title'] = str_replace('item-'.$oldItemId, 'item-'.$newItemId, $data['title']);
		$data['alias'] = str_replace('item-'.$oldItemId, 'item-'.$newItemId, $data['alias']);

		if($data['file_name'])
		{
			$oldFileName = $data['file_name'];
		}
		if($data['file_meta'])
		{
			$oldFileMeta = $data['file_meta'];
		}
		
		
		$data['file_name'] = str_replace('item-'.$oldItemId, 'item-'.$newItemId, $data['file_name']);
		$data['file_meta'] = str_replace('item-'.$oldItemId, 'item-'.$newItemId, $data['file_meta']);
		if($data['file_name'])
		{
			$newFileName = $data['file_name'];
		}
                
                $_image = new stdClass();

		$_image->file_meta = PagoHelper::maybe_unserialize( $oldFileMeta );

		// Copy original file
		$oldFile= $path .DS. $oldFileName;
		$newFile= $path .DS. $newFileName;
		JFile::copy($oldFile, $newFile);

		// Do all of the smaller sizes
		if(count($_image->file_meta['sizes'])> 0 )
		{
			foreach ( (array) $_image->file_meta['sizes'] as $_size ) {

				$ret = JFile::copy( $path .DS. $_size['file'],
					$path .DS. str_replace('item-'.$oldItemId, 'item-'.$newItemId, $_size['file']) );

				if ( $ret !== true ) {
					$errors[] = $ret;
				}
			}
		}
		

		return $this->store($data);
	}
	function store( $data = array() )
	{
		$row = $this->getTable( 'files', 'Table' );

		if ( !$data || empty( $data ) ) {
			$data = JFactory::getApplication()->input->getArray($_POST);
		}

		// Since most of the time we don't submit all of the data lets load it before.
		// This is normally not needed.
		$row->load( $data['id'] );

		// Wipe current fulltext
		if ( JFactory::getApplication()->input->getString( 'filetext' ) ) {
			$row->fulltext = '';
			JFactory::getApplication()->input->post->set( 'text',
				JFactory::getApplication()->input->post->get( 'filetext', '' ) );
		}

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
				= $row->getNextOrder( "`item_id` = {$row->item_id} AND `type` = '{$row->type}'" );
		}

		if ( !$row->check() ) {
			$this->setError( $row->getError() );
			return false;
		}

		if ( !$row->store() ) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		return $row->id;
	}

	function save_order( $_cids = array(), $item = null )
	{	

		$cid = array();

		if ( !empty( $_cids ) ) {
			$cid   = $_cids;
			$item  = $item;
		}
		$async = JFactory::getApplication()->input->getInt( 'async', '0' );
		$total = count( $cid );


		JArrayHelper::toInteger( $cid, array(0) );

		// Instantiate an article table object
		$row = JTable::getInstance( 'files', 'Table' );


		// Update the ordering for items in the cid array
		for ( $i = 0; $i < $total; $i++ )
		{
			$row->load( (int) $cid[$i] );
			if ( $row->ordering != $i ) {				
				$newOrdering  = $i;
				$query = "UPDATE #__pago_files
							SET `ordering` = {$newOrdering}
								WHERE `id` = {$row->id}";
				$this->_db->setQuery( $query );
				$this->_db->query();

				// $row->ordering = $i;
				// if ( !$row->store() ) {
				// 	JError::raiseError( 500, $row->getErrorMsg() );
				// 	return false;
				// }
			}
		}
		// //Reorder all
		// if ( $row->reorder( "item_id = {$item}" ) ) {
		// 	$msg 	= JText::_( 'New ordering saved' );
		// } else {
		// 	$msg 	= JText::_( 'Unable to save ordering: ' . $row->getError() );
		// }

		// If coming from another function
		if ( !empty( $_cids ) ) {
			return true;
		}
		if ( $async ) {
			echo $msg;
			jexit();
		} else {
			// $this->setRedirect( "index.php?option=pago&view=items&task=edit&cid[]={$item}", $msg );
		}
	}

	/**
	 * Deletes all images for a given item_id
	 *
	 * @param int Item ID. Expects it to already be an integer
	 * @return bool True on success.
	 */
	function delete_all( $item_id, $types )

	{
		if(!is_array($types)){
			$types = array($types);	
		}

		if($types){
            $where = '(';
            foreach ($types as $type){
                $where .= " type = '$type' OR";
            }
            $where = substr($where, 0, -2);
            $where .= ')';
		}else{
			$where = '1 = 1';	
		}

        $query = "SELECT `id`
		FROM #__pago_files
			WHERE `item_id` = {$item_id} and {$where}";

		$this->_db->setQuery( $query );
		$cids = $this->_db->loadColumn();

        return $this->delete( $cids );
	}

	function delete( $cids = array() )
	{
		jimport( 'joomla.filesystem.file' );

		$row  = $this->getTable( 'files', 'Table' );
		
		if ( empty( $cids ) ) {
			$cids = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		}

		JArrayHelper::toInteger( $cids, array(0) );

		foreach ( (array) $cids as $cid ) {
			$row->load( $cid );
			
			$this->deleteImages( $row );

			if ( !$row->delete( $cid ) ) {
				$this->setError( $row->getErrorMsg() );
				return false;
			}
		}

		return true;
	}

	function deleteImages( $row )
	{

		$dispatcher = KDispatcher::getInstance();
		$meta       = unserialize( @$row->file_meta );
		$params     = Pago::get_instance( 'config' )->get();

		$product = @PagoHelper::get_product( $row->item_id );
		$category = @Pago::get_instance( 'categoriesi' )->get( $product->primary_category );

		$path = trim( $params->get( $row->type . '_file_path', 'media' .DS. 'pago' ), DS );

		// Delete all intermediate sizes

		$dispatcher->trigger( 'image_path', array( $category->id, $row ) );
		
		if($row->file_name)
			$meta['sizes'][] = ['file' => $row->file_name];

		if ( isset( $meta['sizes'] ) && !empty( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $file ) {

				if($row->type == "images" || $row->type == "video" || $row->type == "download"){
					$file = JPATH_ROOT .DS. $path .DS. 'items'
						.DS. JFilterOutput::stringURLSafe( $category->id )
						.DS. $file['file'];
				}
				
				if($row->type == "variation"){
					$file = JPATH_ROOT .DS. $path .DS. 'product_variation'
						.DS. $row->provider
						.DS. $file['file'];
				}
				
				if($row->type == "category"){
					$file = JPATH_ROOT .DS. $path .DS. 'category'
						.DS. $row->item_id
						.DS. $file['file'];
				}	
				
				if(isset($file) && JFile::exists( $file ) ) {
					JFile::delete( $file );
				}
			}
		}

		// Delete original file
		$dispatcher->trigger( 'image_path', array( &$category->id, $row ) );

		if($row->type == "images" || $row->type == "video" || $row->type == "download"){
			$file = JPATH_ROOT .DS. $path .DS. 'items'
				.DS. JFilterOutput::stringURLSafe( $category->id )
				.DS. $row->file_name;
		}
		if($row->type == "category"){
			$file = JPATH_ROOT .DS. $path .DS. 'category'
				.DS. $row->item_id
				.DS. $row->file_name;
		}

		if (isset($file) && JFile::exists( $file ) ) {
			JFile::delete( $file );
		}

		return true;
	}
	function getVideoProviders()
	{
		$file = JPATH_COMPONENT . '/helpers/video_sources.php';
		jimport('joomla.filesystem.file');
		if (JFile::exists($file))
		{
			require $file;
			$thirdPartyProviders = array_slice($tagReplace, 40);
			$providersTmp = array_keys($thirdPartyProviders);
			$providers = array();
			foreach ($providersTmp as $providerTmp)
			{

				if (stristr($providerTmp, 'google|google.co.uk|google.com.au|google.de|google.es|google.fr|google.it|google.nl|google.pl') !== false)
				{
					$provider = 'google';
				}
				elseif (stristr($providerTmp, 'spike|ifilm') !== false)
				{
					$provider = 'spike';
				}
				else
				{
					$provider = $providerTmp;
				}
				$providers[] = $provider;
			}
			return $providers;
		}else{
			return array();	
		}
	}
	function getFileById($id){
		$id = (int)$id;

		if ( empty( $this->_data ) ) {
			$row = $this->getTable( 'files', 'Table' );
			$row->load( $id );

			$this->_data = $row;
		}

		return $this->_data;	
	}

	function getCatFiles($id, $cid){
		$query = "SELECT `id`
		FROM #__pago_files
			WHERE `id` != {$id} AND `item_id` = {$cid} and `type` = 'category'";

		$this->_db->setQuery( $query );
		$cids = $this->_db->loadColumn();
		return $cids;
	}
}
?>
