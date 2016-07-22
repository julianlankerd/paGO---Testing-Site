<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.view' );

class PagoViewUpload extends JViewLegacy
{
	function display( $tpl = null )
	{
		$db  = JFactory::getDBO();
		$row = $this->get( 'Data' );
		$variation_id = JFactory::getApplication()->input->getInt( 'variation_id' );
		
		// If something went wrong with the upload
		if ( empty( $row ) ) {
			$upVar = JFactory::getApplication()->input->files->get( 'upload', array(), 'array' );
			$this->assignRef( 'upload',  $upVar);
			$this->setLayout('failure');
			parent::display( $tpl );
			return;
		}

		$itemId = false;
		if ( in_array( $row->type, array( 'images', 'variation', 'user', 'store_default', 'video' ) ) ) {
			$product  = $this->get( 'Product' );
			$itemId = $product->id;
			$category = $this->get( 'Category' );
			$path_extra = JFilterOutput::stringURLSafe( $category->id );
			$path_extra = 'items/' . $path_extra;
			
			if($variation_id) 
				$path_extra = 'product_variation' .DS. $variation_id;
				
		} elseif ($row->type == "category") {
			// For category
			JFactory::getApplication()->input->set( 'item_id', $row->item_id );
			$item_id = '';
			$path_extra = '';
			$dispatcher = KDispatcher::getInstance();
			$dispatcher->trigger( 'file_uploader_get_vars',
				array( &$item_id, &$path_extra, $row->type, 'url' ) );

			$cat_id = JFactory::getApplication()->input->get('item_id');
			$path_extra = 'category/' . $cat_id;
		} else {
			// This is here for extendibility
			JFactory::getApplication()->input->set( 'item_id', $row->item_id );
			$item_id = '';
			$path_extra = '';
			$dispatcher = KDispatcher::getInstance();
			$dispatcher->trigger( 'file_uploader_get_vars',
				array( &$item_id, &$path_extra, $row->type, 'url' ) );

		}

		$params = Pago::get_instance( 'config' )->get();

		// Build the necessary Lists
		$lists = array();
	
		// Get the state list
		$lists['published'] = JHtml::_( 'select.booleanlist', 'files[' . $row->id . '][published]',
		 	'class="inputbox"', $row->published );


		// Build the html select list for the group access
		$lists['access'] = JHtml::_('access.assetgrouplist', 'access', $row->access);
		$uploadFiles = JFactory::getApplication()->input->files->get('upload', array(), 'array');
		$this->assignRef('row', $row);
		$this->assignRef('params', $params);
		$this->assignRef('path_extra', $path_extra);
		$this->assignRef('itemId', $itemId);
		$this->assignRef('lists', $lists);
		$this->assignRef('upload', $uploadFiles);
		$this->assignRef('category', $category);

		parent::display($tpl);
	}
}
?>
