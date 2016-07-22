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
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class PagoControllerUpload extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false)
	{
		jexit();
	}

	function __construct()
	{
		parent::__construct();
	}

	function upload()
	{
		$upload_type = JFactory::getApplication()->input->getWord( 'type' );
		$config = Pago::get_instance( 'config' )->get();
		$dispatcher = KDispatcher::getInstance();
		$variation_id = JFactory::getApplication()->input->getInt( 'variation_id' );
		//print_r($_REQUEST);die;
		Pago::load_helpers( 'imagehandler' );

		$app = JFactory::getApplication(); 
		if ( $app->isSite() ) {
			if ( 'user' != $upload_type ) {
				Pago::error( 500, JText::_( 'ONLY_USER_UPLOADS' ) );
			}
			if ( !$config->get( 'media.allow_user_uploaded' ) ) {
				Pago::error( 500, JText::_( 'USER_UPLOADS_UNAVAILABLE' ) );
			}

			$this->addModelPath(JPATH_ADMINISTRATOR . '/components/com_pago/models');
		}

		$model = $this->getModel( 'upload' );

		// Upload
		if ( $id = $model->handle_upload() ) {
			$layout = 'success_' . $upload_type;
			
			$dispatcher->trigger( 'file_uploader_success_layout', array( &$layout, $upload_type ) );
			if($upload_type == 'category')
			{
				$cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
				$this->deleteCatImages($id, $cid);
				$layout = "success_images";
			}
			else if($upload_type == 'download')
			{
				$layout = "success_files";
			}
			
			if($variation_id){
				$layout = "success_variation";
			}
			
			JFactory::getApplication()->input->set( 'layout', $layout );
		} else {
			JFactory::getApplication()->input->set( 'layout', 'failure' );
		}

		JFactory::getApplication()->input->set( 'view', 'upload' );

		parent::display();

		jexit();
	}

	function deleteCatImages($id, $cid) {
		//JFactory::getApplication()->input->set( 'id', $id );

		$model  = $this->getModel( 'file' );
		$file_id = $model->getCatFiles($id, $cid[0]);

		$success = $model->delete($file_id);
	}

}
?>
