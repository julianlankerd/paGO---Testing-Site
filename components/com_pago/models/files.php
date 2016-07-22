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
	var $_product;

	function store( $data = array() )
	{
		$row =& $this->getTable( 'files', 'Table' );

		if ( !$data || empty( $data ) ) {
			$data = JFactory::getApplication()->input->getArray($_POST);
		}

		// Since most of the time we don't submit all of the data lets load it before.
		// This is normally not needed.
		$row->load( intval( $data['id'] ) );

		// Since we are in the front-end we must make sure that only the owner can make edits
		$juser = JFactory::getUser();
		if ( $row->created_by != $juser->get( 'id' ) ) {
			$this->setError( JText::_( 'NOT_ALLOWED_TO_ACCESS_RESOURCE' ) );
			return false;
		}

		// ALWAYS set access and published when in front-end
		$row->access    = 0;
		$row->published = 1;

		// Wipe current fulltext
		if ( JFactory::getApplication()->input->getString( 'filetext' ) ) {
			$row->fulltext = '';
			JFactory::getApplication()->input->set( 'text',
				JFactory::getApplication()->input->post->get( 'filetext', '') );
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
				= $row->getNextOrder( "`product` = {$row->product} AND `type` = '$row->type'" );
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

	function delete( $cids = array() )
	{
		jimport( 'joomla.filesystem.file' );

		$row   =& $this->getTable( 'files', 'Table' );
		$juser = JFactory::getUser();

		if ( empty( $cids ) ) {
			$cids = JFactory::getApplication()->input->get( 'image', array(0), 'array' );
		}
		JArrayHelper::toInteger( $cids, array(0) );

		foreach ( (array) $cids as $cid ) {
			$row->load( $cid );

			// Since we are in the front-end we must make sure that only the owner can make edits
			if ( $row->created_by != $juser->get( 'id' ) ) {
				$this->setError( JText::_( 'NOT_ALLOWED_TO_ACCESS_RESOURCE' ) );
				return false;
			}

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
		$meta   = unserialize( @$row->file_meta );
		$params = Pago::get_instance( 'config' )->get();

		$product = PagoHelper::get_product( $row->product );
		$category = Pago::get_instance( 'categoriesi' )->get( $product->primary_category );
		
		$path = trim( $params->get( $row->type . '_file_path', 'media/pago' ), '/' );

		// Delete all intermediate sizes
		if ( isset( $meta['sizes'] ) && !empty( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $file ) {
				$file = JPATH_ROOT .'/'. $path
					.'/'. JFilterOutput::stringURLSafe( $category->name ) .'/'. $file['file'];
				if( JFile::exists( $file ) ) {
					JFile::delete( $file );
				}
			}
		}

		// Delete original file
		$file = JPATH_ROOT .'/'. $path .'/'.
		 	JFilterOutput::stringURLSafe( $category->name ) .'/'. $row->file_name;
		if ( JFile::exists( $file ) ) {
			JFile::delete( $file );
		}
	}
}
?>
