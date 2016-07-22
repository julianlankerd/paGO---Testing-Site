<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.view' );

class PagoViewFile extends JViewLegacy
{
	function display( $tpl = null )
	{
		$layout = JFactory::getApplication()->input->get( 'layout', 'description' );
		$editor = JFactory::getEditor();
		$params = Pago::get_instance( 'config' )->get();
		$id     = JFactory::getApplication()->input->getInt( 'id' );

		if ( 'description' == $layout || $id ) {
			$row = $this->get( 'Data' );
		} elseif ( 'form' == $layout ) {
			$model = JModelLegacy::getInstance( 'files', 'PagoModel' );
			$row   = $model->get_file_by_path( JFactory::getApplication()->input->getString( 'file' ),
				JFactory::getApplication()->input->getString( 'path' ) );
		}

		// If something went wrong with the upload
		if ( empty( $row ) ) {
			echo JText::_( 'PAGO_FILE_DOESNT_EXIST' );
			return;
		}

		$this->assignRef( 'row', $row );
		$this->assignRef( 'params', $params );
		$this->assignRef( 'editor', $editor );

		parent::display( $tpl );
	}
}
?>