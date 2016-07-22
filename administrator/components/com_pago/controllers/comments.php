<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerComments extends PagoController
{
	function __construct( $default = array() )
	{
		parent::__construct( $default );

		$this->redirect_to = 'index.php?' . http_build_query( array(
			'option'=> JFactory::getApplication()->input->get( 'option' ),
			'view'=> JFactory::getApplication()->input->get( 'view' )
		));
	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}

	function publish()
	{
		$this->set_published( true );

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_COMMENTS_PUBLISHED' ) );
	}

	function unpublish()
	{
		$this->set_published( false );

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_COMMENTS_UNPUBLISHED' ) );
	}

	function cancel()
	{
		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_COMMENTS_OPERATION' ) );
	}
	
	private function set_published( $state = true )
	{
		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$table = JTable::getInstance( 'Comments', 'Table' );

		if ( !is_array( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) )
			$this->setRedirect( $this->redirect_to,
				JText::_( 'PAGO_CID_MUST_BE_AN_ARRAY' ) );

		foreach ( JFactory::getApplication()->input->get( 'cid', 0, 'array') as $item_id ) {
			$data = array(
				'id' => $item_id,
				'published' => $state
			);

			if ( !$table->bind( $data ) ) {
				JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}

			$table->store();
			$table->reset();
		}
	}

	function edit()
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );

		JFactory::getApplication()->input->set( 'layout', 'form' );
		
		parent::display();
	}

	function save()
	{
		$model = JModelLegacy::getInstance( 'Comments', 'PagoModel' );
		if ( $model->store() ) {
			$msg = JText::_( 'PAGO_COMMENTS_SAVED' );
		} else {
			$msg = JText::_( 'PAGO_COMMENTS_ERROR' );
		}
		$this->setRedirect( $this->redirect_to, $msg );
	}

	function apply()
	{
		$model = JModelLegacy::getInstance( 'Comments', 'PagoModel' );
		$id = $model->store();

		if ( $id ) {
			$msg = JText::_( 'PAGO_COMMENTS_SAVED' );
		} else {
			$msg = JText::_( 'PAGO_COMMENTS_ERROR' );
		}
		
		if ( ! $id ) {
			$id = JFactory::getApplication()->input->get( 'id', 0, 'int' );
		}

		
		$link = 'index.php?option=com_pago&view=comments&task=edit&cid[]=' . $id;
		$this->setRedirect( $link, $msg );
	}

	function remove()
	{ 
		if( !is_array( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) )
			$this->setRedirect( $this->redirect_to,
				JText::_( 'PAGO_CID_MUST_BE_AN_ARRAY' ) );
	
		
		$model = JModelLegacy::getInstance( 'Comments', 'PagoModel' );
		$model->deleteItems( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) );

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_COMMENTS' ) );
	}
}
?>
