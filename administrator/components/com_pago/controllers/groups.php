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

class PagoControllerGroups extends PagoController
{
	function __construct( $default = array() )
	{
		parent::__construct( $default );

		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$this->redirect_to = 'index.php?' . http_build_query( array(
			'option'=> JFactory::getApplication()->input->get( 'option' ),
			'view'=> JFactory::getApplication()->input->get( 'view' )
		));
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
		$table = JTable::getInstance( 'Groups', 'Table' );

		if ( !is_array( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) )
			$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_CID_MUST_BE_AN_ARRAY' ) );

		foreach ( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) as $item_id ) {
			$table->delete( $item_id );
			$table->reset();
		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_ITEMS_DELETED' ) );
	}

	function cancel()
	{
		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_OPERATION_CANCELED' ) );
	}

	function add()
	{
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function edit()
	{
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function apply()
	{
		$cid = $this->store();

		if( JFactory::getApplication()->input->get( 'group_id' ) ) $cid = JFactory::getApplication()->input->get( 'group_id' );

		$msg = JText::_( 'Successfully Applied Parameters' );
		$this->setRedirect(
			'index.php?option=com_pago&view=groups&layout=form&cid[]=' .$cid,
			$msg
		);
	}

	function save()
	{
		$this->store();

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_SUCCESSFULLY_SAVED' ) );
	}

	function store()
	{
		$table = JTable::getInstance( 'Groups', 'Table' );

		$data 		= JFactory::getApplication()->input->get( 'base' );
		$group_id 	= JFactory::getApplication()->input->get( 'group_id' );
		$members 	= JFactory::getApplication()->input->get( 'memberlist' );
		$members 	= $members['members'];
		$params 	= json_encode( JFactory::getApplication()->input->get( 'custom' ) );

		$db = $table->get( '_db' );

		if ( !$group_id ) {
			$data['created'] = date( 'Y-m-d H:i:s', time() );
			$data['modified'] = date( 'Y-m-d H:i:s', time() );
		}

		$data['group_id'] = $group_id;
		$data['params'] = $params;

		$table->bind( $data );
		$table->store();

		if ( !$group_id ) {
			$group_id = $db->insertid();
		}

		$db->setQuery( "DELETE FROM #__pago_groups_users WHERE group_id = {$group_id}" );
		$db->query();

		if ( !empty($members) ) {
			foreach( $members as $user_id ){
				$db->setQuery(
					"INSERT INTO #__pago_groups_users
						SET group_id = {$group_id}, user_id = {$user_id}"
				);
				$db->query();
			}
		}

		return $group_id;
	}

	function memberslist()
	{
		$query = JFactory::getApplication()->input->getString( 'q' );
		$model = JModelLegacy::getInstance( 'groups', 'PagoModel' );

		$list = $model->getMemberlist( $query );
		if ( !$list ) {
			die();
		}

		$ini = Pago::get_instance( 'config' )->encode_ini( $list );
		echo $ini; die();
	}
}
