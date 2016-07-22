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

/**
* Plugins Component Controller
*
* @package		Joomla
* @subpackage	Plugins
* @since 1.5
*/
class PagoControllerPlugins extends PagoController
{
	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'unpublish', 	'publish');
		$this->registerTask( 'edit' , 		'edit' );
		$this->registerTask( 'add' , 		'display' );
		$this->registerTask( 'orderup'   , 	'order' );
		$this->registerTask( 'orderdown' , 	'order' );

		$this->registerTask( 'accesspublic' 	, 	'access' );
		$this->registerTask( 'accessregistered'  , 	'access' );
		$this->registerTask( 'accessspecial' 	, 	'access' );

	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display();
	}

	// function add()
	// {

	// 	parent::display();
	// }

	function edit()
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		JFactory::getApplication()->input->set( 'layout', 'form'  );
		JFactory::getApplication()->input->set( 'view', 'plugins' );

		parent::display();
	}

	// function save()
	// {
	// 	// Check for request forgeries

	// 	$db   = JFactory::getDBO();
	// 	$row  = JTable::getInstance('plugin');
	// 	$task = $this->getTask();

	// 	$client = JRequest::getWord( 'filter_client', 'site' );
	// 	$post = JFactory::getApplication()->input->getArray($_POST);

	// 	if ( !$row->bind($post) ) {
	// 	}
	// 	if ( !$row->check() ) {
	// 	}
	// 	if ( !$row->store() ) {
	// 	}
	// 	$row->checkin();

	// 	if ( $client == 'admin' ) {
	// 		$where = "client_id=1";
	// 	} else {
	// 		$where = "client_id=0";
	// 	}

	// 	$row->reorder( 'folder = '.$db->Quote($row->folder).
	// 		' AND ordering > -10000 AND ordering < 10000 AND ( '.$where.' )' );

	// 	switch ( $task ) {
	// 		case 'apply':
	// 			$msg = JText::sprintf( 'Successfully Saved changes to Plugin', $row->name );
	// 			$this->setRedirect( 'index.php?option=com_pago&controller=plugins&view=plugin&'.
	// 				'client='. $client .'&task=edit&cid[]='. $row->id, $msg );
	// 			break;

	// 		case 'save':
	// 		default:
	// 			$msg = JText::sprintf( 'Successfully Saved Plugin', $row->name );
	// 			$this->setRedirect( 'index.php?option=com_pago&controller=plugins&view=plugins&'.
	// 				'client='. $client, $msg );
	// 			break;
	// 	}
	// }

	function publish()
	{
		// Check for request forgeries
		JSession::getFormToken() or jexit( 'Invalid Token' );

		$db		 = JFactory::getDBO();
		$user	 = JFactory::getUser();
		$cid     = JFactory::getApplication()->input->post->get( 'cid', array(0), 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$publish = ( $this->getTask() == 'publish' ? 1 : 0 );
		$client  = JFactory::getApplication()->input->getWord( 'filter_client', 'site' );

		if ( count( $cid ) < 1 ) {
			$action = $publish ? JText::_( 'publish' ) : JText::_( 'unpublish' );
			JFactory::getApplication()->enqueueMessage('Select a plugin to ' . $action, 'error');

		}

		$cids = implode( ',', $cid );

		$query = 'UPDATE #__extensions SET enabled = '.(int) $publish
			. ' WHERE extension_id IN ( '.$cids.' )'
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id').' ))'
			;
		$db->setQuery( $query );
		if ( !$db->query() ) {
			JError::raiseError( 500, $db->getErrorMsg() );
		}

		if ( count( $cid ) > 0 ) {
			$this->checkin($cid,false);
		}

		$this->setRedirect( 'index.php?option=com_pago&controller=plugins&view=plugins');
	}

	// function cancel()
	// {
	// 	// Check for request forgeries

	// 	$db  = JFactory::getDBO();
	// 	$row = JTable::getInstance('plugin');
	// 	$post = JFactory::getApplication()->input->getArray($_POST);
	// 	$row->bind($post);
	// 	$row->checkin();

	// 	$this->setRedirect( JRoute::_( 'index.php?option=com_pago&controller=plugins&view='.
	// 		'plugins&client='. $client, false ) );
	// }

	// function order()
	// {
	// 	// Check for request forgeries

	// 	$db     = JFactory::getDBO();

	// 	JArrayHelper::toInteger($cid, array(0));

	// 	$uid    = $cid[0];
	// 	$inc    = ( $this->getTask() == 'orderup' ? -1 : 1 );
	// 	$client = JRequest::getWord( 'filter_client', 'site' );


	// 	// Currently Unsupported
	// 	if ( $client == 'admin' ) {
	// 		$where = "client_id = 1";
	// 	} else {
	// 		$where = "client_id = 0";
	// 	}
	// 	$row = JTable::getInstance('plugin');
	// 	$row->load( $uid );
	// 	$row->move(
	// 		$inc,
	// 		'folder='.$db->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND ('.
	// 			$where.')'
	// 	);

	// 	$this->setRedirect( 'index.php?option=com_pago&controller=plugins&view=plugins' );
	// }

	// function access()
	// {
	// 	// Check for request forgeries

	// 	$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
	// 	JArrayHelper::toInteger($cid, array(0));

	// 	$uid    = $cid[0];
	// 	$access = $this->getTask();

	// 	$db = JFactory::getDBO();
	// 	switch ( $access ) {
	// 		case 'accesspublic':
	// 			$access = 0;
	// 			break;

	// 		case 'accessregistered':
	// 			$access = 1;
	// 			break;

	// 		case 'accessspecial':
	// 			$access = 2;
	// 			break;
	// 	}

	// 	$row = JTable::getInstance('plugin');
	// 	$row->load( $uid );
	// 	$row->access = $access;

	// 	if ( !$row->check() ) {
	// 		return $row->getError();
	// 	}
	// 	if ( !$row->store() ) {
	// 		return $row->getError();
	// 	}

	// 	$this->setRedirect( 'index.php?option=com_pago&controller=plugins&view=plugins' );
	// }

	// function saveorder()
	// {
	// 	// Check for request forgeries

	// 	JArrayHelper::toInteger($cid, array(0));

	// 	$db			= JFactory::getDBO();
	// 	$total		= count( $cid );
	// 	JArrayHelper::toInteger($order, array(0));

	// 	JArrayHelper::toInteger($cid, array(0));

	// 	$row 		=& JTable::getInstance('plugin');
	// 	$conditions = array();

	// 	// update ordering values
	// 	for ( $i=0; $i < $total; $i++ ) {
	// 		$row->load( (int) $cid[$i] );
	// 		if ( $row->ordering != $order[$i] ) {
	// 			$row->ordering = $order[$i];
	// 			if ( !$row->store() ) {
	// 				JError::raiseError(500, $db->getErrorMsg() );
	// 			}
	// 			// remember to updateOrder this group
	// 			$condition = 'folder = '.$db->Quote($row->folder).
	// 				' AND ordering > -10000 AND ordering < 10000 AND client_id = ' .
	// 				(int) $row->client_id;
	// 			$found = false;
	// 			foreach ( $conditions as $cond ) {
	// 				if ( $cond[1] == $condition ) {
	// 					$found = true;
	// 					break;
	// 				}
	// 			}
	// 			if ( !$found ) $conditions[] = array($row->id, $condition);
	// 		}
	// 	}

	// 	// execute updateOrder for each group
	// 	foreach ( $conditions as $cond ) {
	// 		$row->load( $cond[0] );
	// 		$row->reorder( $cond[1] );
	// 	}

	// 	$msg 	= JText::_( 'New ordering saved' );
	// 	$this->setRedirect( 'index.phpoption=com_pago&controller=plugins&view=plugins', $msg );
	// }
	public function checkin($cid = false,$redirect = true){
		
		$db		 = JFactory::getDBO();
		if(!$cid){
			$cid     = JFactory::getApplication()->input->post->get( 'cid', array(0), 'array' );
			JArrayHelper::toInteger($cid, array(0));
		}

		if ( count( $cid ) > 0 ) {
			if(!is_array($cid)){
				$cid = array($cid);	
			}
			foreach ($cid as $exId) {
				$exId = (int)$exId;
				$query = 'UPDATE #__extensions SET checked_out = 0 WHERE extension_id = '.$exId;
				$db->setQuery( $query );
				$db->query();
			}
		}

		if($redirect){
			$this->setRedirect( 'index.php?option=com_pago&controller=plugins&view=plugins');
		}else{
			return true;
		}
	}

	public function checkinExtension(){
		$db = JFactory::getDBO();
		
		$extensionId = JFactory::getApplication()->input->post->getInt('extension_id');

		$this->checkin(array($extensionId),false);

		$return['status'] = 'success';
		echo json_encode($return);
        exit();
	}
}