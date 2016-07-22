<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class PagoViewInstallation extends JViewLegacy
{
	public $_layout = 'default';

	public $_module_positions = array(
		'pago_menu',
		'pago_toolbar',
		'pago_frontpage_topleft',
		'pago_frontpage_topright',
		'pago_frontpage_body',
		'pago_category_frontpage_topleft',
		'pago_category_frontpage_topright',
		'pago_category_frontpage_body',
		'pago_checkout_login',
		'pago_checkout_register'
	);

	private $_session = false;
	private $_task_request = false;
	private $_helper = false;

	public function display()
	{


		if( !function_exists( 'qp' ) ){
			//CANNOT USE PAGO CLASS HERE BECAUSE INSTALLATION WILL NOT WORK!
			//Pago::load_helpers( 'QueryPath' . DS . 'QueryPath' );
			require( JPATH_COMPONENT . '/helpers/QueryPath/QueryPath.php' );
		}

		//CANNOT USE PAGO CLASS HERE BECAUSE INSTALLATION WILL NOT WORK!
		//Pago::load_helpers( 'installation' );
		require( JPATH_COMPONENT . '/helpers/installation.php' );

		$this->_helper = new pago_helper_installer( $this->_module_positions  );
		$this->_task_request =& JFactory::getApplication()->input->getCmd('task');

		$task_method = false;

		if( $this->_task_request ){
			$task_method = 'task_' . $this->_task_request;
		}

		if( method_exists( $this, $task_method ) ){

			$link = 'index.php?option=com_pago';

			$msg = $this->$task_method();
			$app =& JFactory::getApplication();
			return $app->controller->setRedirect($link, $msg);
		}

		$this->_helper->install();

		$app = JFactory::getApplication();

		$app->redirect( 'index.php?option=com_pago', JText::_('Installation Complete') );

		$e =& JError::getErrors();

		if( $this->_helper->_fatal_error ){
			JError::raiseWarning( 0, JText::_( 'PAGO_RESOLVE_ERRORS' ) );
			$this->assignRef( 'exceptions', $e );
		}

		$this->assign( 'disable_continue', false );
		$this->assign( 'base_extensions', $this->_helper->_base_extensions );

		$this->setLayout( $this->_layout );

		return $this->loadTemplate();
	}

	private function task_install_extension(){
		$params = JComponentHelper::getParams( 'com_pago' );

		if(!$params->get('base_ext_tools', 1)){
			JError::raiseWarning( 0, JText::_( 'PAGO_BASE_EXTENSION_UPGRADE_DISABLED' ) );
			return false;
		}

		$extension = JFactory::getApplication()->input->getCmd('extension');

		if( $this->_helper->install_extension( $extension ) ){
			return JText::_( 'PAGO_SUCCESS_EXTENSION_INSTALL' ) .': ' . $extension;
		}

		return false;
	}
}
?>