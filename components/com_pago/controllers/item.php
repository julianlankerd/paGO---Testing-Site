<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

class PagoControllerItem extends PagoController
{
	public function getDefaultImage(){
		$item_id = JFactory::getApplication()->input->get( 'itemId' );
		Pago::load_helpers( array( 'categories', 'imagehandler', 'pagohtml' ) );
		$return['imagePath'] = false;
		$return['status'] = "fail";
		$images = PagoImageHandlerHelper::get_item_files( $item_id, true, array( 'images' ) );
		$main_image_url = PagoImageHandlerHelper::get_image_from_object( $images[0], 'large', true,'',false );
		if($main_image_url) {
			$return['imagePath'] = $main_image_url;
			$return['status'] = "success";
		}
		$return = json_encode($return);
		echo $return;
		jexit();
	}

	function get_items()
	{
		ob_clean();
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/models/');
		$model = JModelLegacy::getInstance('item', 'PagoModel');
		$word = JFactory::getApplication()->input->get('search_query', null, 'string');
		$items = $model->get_search_items($word);
		echo json_encode($items);
        exit();
	}
	
	public function downloadFiles()
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/models/');
		$fileid = JFactory::getApplication()->input->get('fileid');
		$model = JModelLegacy::getInstance('item', 'PagoModel');
		$fileDetails = $model->getFileDetails($fileid);
		$filenm = JFactory::getApplication()->input->get( 'filenm' );
		$filename = JURI::BASE().'media/pago/items/' . $fileDetails[0]['primary_category'] . '/' . $fileDetails[0]['file_name'];
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=" . $fileDetails[0]['file_name']);
		header("Content-Transfer-Encoding: binary");
		readfile($filename); 
		exit;
	}
}