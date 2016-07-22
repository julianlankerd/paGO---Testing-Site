<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');

class PagoControllerInstall extends PagoController
{
	function display()
	{
		$mainframe = JFactory::getApplication();

		//$model	= &$this->getModel( 'Install' );

		$lang = JFactory::getLanguage();
		$lang->load( 'com_installer', JPATH_ADMINISTRATOR );

		JLoader::import( 'install', JPATH_ADMINISTRATOR . '/components/com_installer/models' );

		$model = JModelLegacy::getInstance('Install','InstallerModel');

		$model->setState( 'install.directory', $mainframe->getCfg( 'config.tmp_path' ));

		$view	= &$this->getView( 'Install');

		$ftp = JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		$view->setModel( $model, true );
		$view->display();
	}

	function doInstall()
	{
		$lang = JFactory::getLanguage();
		$lang->load( 'com_installer', JPATH_ADMINISTRATOR );

		// Check for request forgeries
		JSession::getFormToken() or jexit( 'Invalid Token' );

		JLoader::import( 'install', JPATH_ADMINISTRATOR . '/components/com_installer/models' );

		$model = JModelLegacy::getInstance('Install','InstallerModel');

		$view	= &$this->getView( 'Install' );

		$ftp = JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		if ( $model->install() ) {
			$cache = &JFactory::getCache('mod_menu');
			$cache->clean();
		}

		$view->setModel( $model, true );
		$view->display();
	}

	function manage()
	{
		$jinput = JFactory::getApplication()->input;
		$type	= $jinput->getWord('type', 'components');
		//$type	= JRequest::getWord('type', 'components');
		$model	= $this->getModel( $type );
		$view	= $this->getView( $type );

		$ftp = JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		$view->setModel( $model, true );
		$view->display();
	}

	function enable()
	{
		// Check for request forgeries
		JSession::getFormToken( 'request' ) or jexit( 'Invalid Token' );

		$jinput = JFactory::getApplication()->input;
		$type	= $jinput->getWord('type', 'components');
		$model	= $this->getModel( $type );
		$view	= $this->getView( $type );

		$ftp = JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		if ( method_exists( $model, 'enable' ) ) {
			$eid = $jinput->get('eid', array(), 'array');
			JArrayHelper::toInteger($eid, array());
			$model->enable($eid);
		}

		$view->setModel( $model, true );
		$view->display();
	}

	function disable()
	{
		// Check for request forgeries
		JSession::getFormToken( 'request' ) or jexit( 'Invalid Token' );

		$jinput = JFactory::getApplication()->input;
		$type	= $jinput->getWord('type', 'components');
		$model	= $this->getModel( $type );
		$view	= $this->getView( $type );

		$ftp = JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		if ( method_exists($model, 'disable') ) {
			$eid = $jinput->get('eid', array(), 'array');
			JArrayHelper::toInteger($eid, array());
			$model->disable($eid);
		}

		$view->setModel( $model, true );
		$view->display();
	}

	function remove()
	{
		// Check for request forgeries
		JSession::getFormToken() or jexit( 'Invalid Token' );

		$jinput = JFactory::getApplication()->input;
		$type	= $jinput->getWord('type', 'components');
		$model	= $this->getModel( $type );
		$view	= $this->getView( $type );

		$ftp = JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);

		$eid = $jinput->get('eid', array(), 'array');

		// Update to handle components radio box
		// Checks there is only one extensions, we're uninstalling components
		// and then checks that the zero numbered item is set (shouldn't be a zero
		// if the eid is set to the proper format)
		if( ( count( $eid ) == 1 ) &&
			( $type == 'components' ) &&
			( isset( $eid[0] ) ) )
			$eid = array($eid[0] => 0);

		JArrayHelper::toInteger($eid, array());
		$result = $model->remove($eid);

		$view->setModel( $model, true );
		$view->display();
	}
}
