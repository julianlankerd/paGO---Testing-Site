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
	function multisave()
	{
		$model = $this->getModel( 'files' );
		$images = JFactory::getApplication()->input->get( 'files', array(), 'array' );

		foreach ( $images as $id => $image ) {
			// Prepare variables for later
			$image['id'] = $id;

			JFactory::getApplication()->input->set( 'text', $image['text'] );

			if ( $id = $model->store( $image ) ) {
				$msg = JText::_( 'PAGO_UPLOAD_SUCCESSFULLY_SAVED' );
			} else {
				$msg = JText::_( 'PAGO_ERROR_OCCURRED' ) . $model->getError();
			}
		}

		$script = '<script type="text/javascript">setTimeout("loc = (window.dialogArguments || opener || parent || top).location; loc.hash = \'#tab=user_images\'; loc.reload()", 1000);</script>';

		jexit( 'Saved' . $script );
	}

	function remove()
	{
		$model  = $this->getModel( 'files' );
		$async  = JFactory::getApplication()->input->getInt( 'async', 0 );
		$silent = JFactory::getApplication()->input->getInt( 'silent', 1 );

		if ( $model->delete() ) {
			$cids = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );

			$msg = JText::_( 'PAGO_UPLOAD_SUCCESSFULLY_DELETED' );
			if ( count( $cids ) > 1 ) {
				$msg = JText::_( 'PAGO_UPLOAD_SUCCESSFULLY_DELETED' );
			}

			if ( $async && $silent ) {
				$msg = 1;
			}
		} else {
			$id	 = JFactory::getApplication()->input->getInt('id');
			$msg = JText::_( 'PAGO_ERROR_OCCURRED' ) . $model->getError();
		}

		// If coming from ajax request
		if ( $async ) {
			echo $msg;
			jexit();
		}

		$this->setRedirect( 'index.php?option=com_pago&view=item&id='
			. JFactory::getApplication()->input->getInt( 'product' ), $msg );
	}
	public function getVideo(){
		$videoId = JFactory::getApplication()->input->get( 'videoId' );

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('File','PagoModel');

		$video = $model->getFileById($videoId);
		if($video){
			$file = JPATH_SITE.'/administrator/components/com_pago/helpers/video_sources.php';
			jimport('joomla.filesystem.file');
			if (JFile::exists($file))
			{
				require $file;
			}
			
			$videoEmbed = $tagReplace[$video->provider];
			$videoEmbed = str_replace("{SOURCE}", $video->video_key, $videoEmbed);
			$return['videoEmbed'] = $videoEmbed;
			$return = json_encode($return);
			echo $return;
			exit();
		}
	}
}
?>
