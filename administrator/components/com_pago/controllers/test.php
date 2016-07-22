<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerTest extends PagoController
{
	function __construct( $default = array() )
	{
		parent::__construct( $default );

		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$this->redirect_to = 'index.php?' . http_build_query( array(
			'option'=> JFactory::getApplication()->input->get( 'option' ),
			'view'=> JFactory::getApplication()->input->get( 'view' )
		));

		$this->registerTask( 'new', 'add' );
	}

	function test()
	{
		echo 'hi';
	}

	function unpublish()
	{
		$this->set_published( false );

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_ITEMS_UNPUBLISHED' ) );
	}

	private function set_published( $state = true )
	{
		if( !is_array( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) )
			$this->setRedirect( $this->redirect_to,
				JText::_( 'PAGO_CID_MUST_BE_AN_ARRAY' ) );

		foreach ( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) as $item_id ) {
			$table = JTable::getInstance( 'States', 'Table' );

			$data = array(
				'state_id' => $item_id,
				'publish' => $state
			);

			if ( !$table->bind( $data ) ) {
				JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}

			$table->store();
		}
	}

	function setdefault()
	{
		$group_id = JFactory::getApplication()->input->get( 'group_id' );

		$db = JFactory::getDBO();
		$db->setQuery( "UPDATE #__pago_groups SET isdefault=0" );
		$db->query();
		$db->setQuery( "UPDATE #__pago_groups SET isdefault=1 WHERE group_id=$group_id" );
		$db->query();

		$this->setRedirect( $this->redirect_to, JText::_( 'Successfully Set Default' ) );
	}

	function remove()
	{
		$table = JTable::getInstance( 'States', 'Table' );

		if ( !is_array( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) )
			$this->setRedirect( $this->redirect_to,
				JText::_( 'PAGO_CID_MUST_BE_AN_ARRAY' ) );

		foreach ( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) as $item_id ) {
			$table->delete( $item_id );
			$table->reset();
		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_DELETED_RECORD' ) );
	}

	function cancel()
	{
		$country_id =  JFactory::getApplication()->input->get( 'country_id' );
		if($country_id)
		{
			$this->setRedirect( 'index.php?option=com_pago&view=states&filter_country_id='.$country_id,
			JText::_( 'PAGO_OPERATION_CANCELED' ) );
		}
		else
		{
			$this->setRedirect( 'index.php?option=com_pago&view=locations', JText::_( 'PAGO_OPERATION_CANCELED' ) );
		}
	}

	function add()
	{
		$filter_country_id =  JFactory::getApplication()->input->get( 'filter_country_id' );
		$this->setRedirect( 'index.php?option=com_pago&view=states&layout=form&filter_country_id='.$filter_country_id
		// ,JText::_( 'PAGO_ADD_RECORD' ) 
		);
	}

	function edit()
	{
		$cid =  JFactory::getApplication()->input->get( 'cid' );

		$this->setRedirect( 'index.php?option=com_pago&view=states&layout=form&cid[]=' . $cid[0],
			JText::_( 'PAGO_EDIT_RECORD' ) );
	}

	function apply()
	{
		$cid = $this->store();

		if( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) $cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );

		$msg = JText::_( 'Successfully Applied Parameters' );
		$this->setRedirect( 'index.php?option=com_pago&view=states&layout=form&cid[]=' . $cid,
			$msg );
	}

	function save()
	{
		$this->store();

		$country_id =  JFactory::getApplication()->input->get( 'country_id' );
		if($country_id)
		{
			$this->setRedirect( 'index.php?option=com_pago&view=states&filter_country_id='.$country_id,
			JText::_( 'PAGO_OPERATION_CANCELED' ) );
		}
		else
		{	
			$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_SUCCESSFULLY_SAVED' ) );
		}
	}

	function store()
	{
		$row = JTable::getInstance( 'States', 'Table' );

		$data 	= JFactory::getApplication()->input->get( 'params' );
		$data['params'] = json_encode( JFactory::getApplication()->input->get( 'custom' ) );

		$error = false;

		if ( !$row->bind( $data ) ) $error = true;
		if ( !$row->store() ) $error = true;

		if ( $error ) {
			JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			return false;
		}

		$_tbl_key = $row->get( '_tbl_key' );

		return $row->$_tbl_key;
	}
}
