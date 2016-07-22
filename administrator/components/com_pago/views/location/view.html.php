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

class PagoViewLocation extends JViewLegacy
{
	function display(){

		$this->run_task();

		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();

		Pago::load_helpers( 'pagoparameter' );

		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/tables' );

		$row = JTable::getInstance( 'locations', 'Table' );

		$row->load( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) );

		$params = json_decode( $row->params, true );

		if( is_array( $params ) )
		$row = array_merge( (array)$row, $params );

		$bind_data = array(
			'params' => $row,
			'custom' => $row
		);

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

		$this->assign( 'id', JFactory::getApplication()->input->get( 'cid' ) );
		$this->assign( 'params', new PagoParameter( $bind_data,  dirname( __FILE__ ) . '/params.xml' ) );

		parent::display();
	}

	function task_save(){
		$app = JFactory::getApplication();
		if( $this->store() ) $app->enqueueMessage( JText::_( 'Successfully Saved Changes' ) );
 		$app->redirect( "index.php?option=com_pago&view=locations" );
	}

	function task_apply(){
		$app = JFactory::getApplication();
		if( $this->store() ) $app->enqueueMessage( JText::_( 'Successfully Applied Changes' ) );
 		$app->redirect( "index.php?option=com_pago&view={$this->_name}&layout=form&cid=" . JFactory::getApplication()->input->get( 'id' ) );
	}

	function task_cancel(){
		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::_( 'Operation Cancelled' ) );
 		$app->redirect( "index.php?option=com_pago&view=locations" );
	}

	function store(){

		$app 	= JFactory::getApplication();

		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/tables' );

		$row = JTable::getInstance( 'locations', 'Table' );

		$data 	= JFactory::getApplication()->input->get( 'params' );
		$data['params'] = json_encode( JFactory::getApplication()->input->get( 'custom' ) );

		if( !$row->bind( $data ) ) $error = true;
		if( !$row->store() ) $error = true;

		if( $error ){
			JError::raiseWarning( 500, $row->getError() );
			return false;
		}

		return true;
	}

	function run_task(){

		$task_method = 'task_' . JFactory::getApplication()->input->get( 'task' );

		if ( method_exists( $this, $task_method ) ) {
            $this->$task_method();
        } elseif( JFactory::getApplication()->input->get( 'task' ) ) {
			JError::raiseWarning( 500, JText::_( 'Task Not Found: ' . JFactory::getApplication()->input->get( 'task' ) ) );
        	$app = JFactory::getApplication();
 			$app->redirect( "index.php?option=com_pago&view={$this->_name}&layout=form" );
		}
	}
}
