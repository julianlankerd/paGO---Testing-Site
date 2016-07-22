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

class PagoViewCustomer extends JViewLegacy
{
	function display(){

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		$this->run_task();

		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();

		Pago::load_helpers( 'pagoparameter' );

		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pago'.DS.'tables' );

		$row = JTable::getInstance( 'Userinfo', 'Table' );

		$row->load( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) );

		$user_info = array( 'groups'=>array(), 'billing'=>0, 'shipping'=>0 );

		if( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) || JFactory::getApplication()->input->get( 'copy' ) ){
			$user_info = Pago::get_instance( 'users' )->get( $row->user_id );
		}

		$db = JFactory::getDBO();

		$sql = "SELECT groups.group_id
			FROM #__pago_groups_users as groups_users

					LEFT JOIN #__pago_groups as groups
					ON groups_users.group_id = groups.group_id

				WHERE groups_users.user_id={$row->user_id}";

		$db->setQuery( $sql );


		$user_info['groups']['groups'] = $db->loadResultArray();


		//$ini = $this->ini_encode( $user_info['groups'] );

		$bind_data = array(
			'grouplist' => $user_info['groups'],
			'address_billing' => $user_info['billing'],
			'address_shipping' => $user_info['shipping']
		);

		$params = new PagoParameter( $bind_data,  dirname( __FILE__ ) . '/params.xml' );

		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

		$this->assign( 'grouplist', $params->render( 'grouplist', 'grouplist' ) );
		$this->assign( 'address_billing', $params->render( 'address_billing', 'address_billing' ) );
		$this->assign( 'address_shipping', $params->render( 'address_shipping', 'address_shipping' ) );

		if( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) && !JFactory::getApplication()->input->get( 'copy' ) ){
			$this->assign( 'user', JFactory::getUser( $row->user_id ) );
		}

		if( JFactory::getApplication()->input->get( 'copy' ) ){
			JFactory::getApplication()->input->set( 'cid', false );
		}

		//$this->assign( 'details', $params->render( 'details', 'details' ) );

		$this->assign( 'id', JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) );

		parent::display();
	}

	function task_delete(){

		$app = JFactory::getApplication();

		$app->enqueueMessage( JText::_( 'Successfully Deleted Records' ) );
 		$app->redirect( "index.php?option=com_pago&view=customers" );
	}

	function task_save(){

		$app = JFactory::getApplication();

		$id = $this->store();

		$app->enqueueMessage( JText::_( 'Successfully Saved Changes' ) );
 		$app->redirect( "index.php?option=com_pago&view=customers" );
	}

	function task_apply(){

		$app = JFactory::getApplication();

		$id = $this->store();

		$app->enqueueMessage( JText::_( 'Successfully Applied Changes' ) );
 		$app->redirect( "index.php?option=com_pago&view=customer&layout=form&cid=" . $id );
	}

	function task_cancel(){
		$app = JFactory::getApplication();
		$app->enqueueMessage( JText::_( 'Operation Cancelled' ) );
 		$app->redirect( "index.php?option=com_pago&view=customers" );
	}

	function store(){

		$groups = JFactory::getApplication()->input->get( 'grouplist' );
		$groups = $groups['groups'];

		$addy_billing = JFactory::getApplication()->input->get( 'address_billing' );
		$addy_shipping = JFactory::getApplication()->input->get( 'address_shipping' );

		if( !$addy_shipping['first_name'] ){
			$addy_shipping = $addy_billing;
		}

		if( !JFactory::getApplication()->input->get( 'id' ) ){

			$user_exists = JFactory::getUser();

			unset( $addy_billing['id'] );
			unset( $addy_shipping['id'] );

			$details = JFactory::getApplication()->input->get( 'details' );
			$user_id = $details['user_id'];

			$user_exists = Pago::get_instance( 'users' )->get( $user_id );

			if( is_object( $user_exists['billing'] ) ){
				JError::raiseWarning( 500, JText::_( 'Joomla username is already associated with a Pago Customer' ) );
				$app = JFactory::getApplication();
				$app->redirect( "index.php?option=com_pago&view=customer&layout=form" );
				return;
			}

			$addy_billing['user_id'] = $user_id;
			$addy_shipping['user_id'] = $user_id;

		} elseif( $addy_billing['id'] == $addy_shipping['id'] ) {
			unset( $addy_shipping['id'] );
		}

		$user_id = $addy_billing['user_id'];

		$addy_billing['address_type '] = 'b';
		$addy_shipping['address_type '] = 's';

		$set_addy_billing = false;
		$set_addy_shipping = false;

		foreach( $addy_billing as $name=>$value ){
			$set_addy_billing .= "$name='$value',";
		}

		foreach( $addy_shipping as $name=>$value ){
			$set_addy_shipping .= "$name='$value',";
		}

		$set_addy_billing = substr_replace( $set_addy_billing, '', -1 );
		$set_addy_shipping = substr_replace( $set_addy_shipping, '', -1 );

		//echo $set_addy_billing.$set_addy_shipping;die();
		$db = JFactory::getDBO();

		$db->setQuery("
			INSERT INTO #__pago_user_info SET $set_addy_billing
			ON DUPLICATE KEY UPDATE $set_addy_billing
		");

		$db->query();

		$insertid = $db->insertid();

		$db->setQuery("
			INSERT INTO #__pago_user_info SET $set_addy_mailing
			ON DUPLICATE KEY UPDATE $set_addy_mailing
		");

		$db->query();

		$db->setQuery( "DELETE FROM #__pago_groups_users WHERE user_id = {$user_id}" );
		$db->query();

		if( !empty($groups) ){
			foreach( $groups as $group_id ){
				$db->setQuery( "INSERT INTO #__pago_groups_users SET group_id = {$group_id}, user_id = {$user_id}" );
				$db->query();
			}
		}

		if( !JFactory::getApplication()->input->get( 'id' ) ){
			return $insertid;
		}

		return JFactory::getApplication()->input->get( 'id' );
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

	function ini_encode( $item ){

		$ini = false;

		if(!empty($item))
		foreach ( $item as $key => $val ) {
			if( $key[0] != '_' )
				if ( is_array( $val ) ) {
					$list_items = false;
					foreach ( (array)$val as $l_item ) {
						$list_items .= $l_item.'|';
					}
					$list_items = substr( $list_items, 0, -1 );
					$ini .= "$key=$list_items\n";
				} else {
					$ini .= "$key=$val\n";
				}
		}

		return $ini;
	}
}