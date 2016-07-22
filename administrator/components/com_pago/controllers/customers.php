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

class PagoControllerCustomers extends PagoController
{
	/**
	* Custom Constructor
	*/
	private $_view = 'customers';

	function __construct( $default = array() )
	{
		parent::__construct( $default );

		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$this->table = JTable::getInstance( 'Customers', 'Table' );

		$this->redirect_to = 'index.php?' . http_build_query( array(
			'option'=> JFactory::getApplication()->input->get( 'option' ),
			'view'=> JFactory::getApplication()->input->get( 'view' )
		));
	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}

	function edit()
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		JFactory::getApplication()->input->set( 'layout', 'edit_address' );
		parent::display();
	}

	function add()
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );

		JFactory::getApplication()->input->set( 'layout', 'form' );

		parent::display();
	}
	
	function cancel()

	{
		$app = JFactory::getApplication();
		$app->redirect( "index.php?option=com_pago&view=customers");
	}


	function save()
	{
		$app = JFactory::getApplication();

		//$id = $this->store();

		JFactory::getApplication()->input->set( 'layout', 'default' );

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_SUCCESS_APPLIED_PARAMS' ) );
	}

	function apply()
	{
		$app = JFactory::getApplication();

		$id = $this->store();

		$app->enqueueMessage( JText::_( 'PAGO_SUCCESS_APPLIED_PARAMS' ) );
		$app->redirect( "index.php?option=com_pago&view=customers&layout=form&cid[]=" . $id );
	}

	function saveUser(){
		$user = JFactory::getApplication()->input->get('jform', '', 'array');
		$joomlaUserId = Pago::get_instance('users')->saveJoomlaUser($user);
		echo json_encode($joomlaUserId);exit();
	}

	function store()
	{
		$db = JFactory::getDBO();
		$groups = JFactory::getApplication()->input->get( 'grouplist' );
		$groups = $groups['groups'];

		$jinput = JFactory::getApplication()->input;
		$billing_address = $jinput->get('address_billing', '', 'array');
		$mailing_address = $jinput->get('address_shipping', '', 'array');
		$address_mailing_same_as_billing = $jinput->get('address_mailing_same_as_billing', '', 'int');

		if($address_mailing_same_as_billing)
		{
			$shipping_address = $billing_address;
			$shipping_address['address_type'] = 's';
			$shipping_address['address_type_name'] = 'shipping';
		}

		$pagoUserId = $jinput->get('id', '', 'int');
		$joomlaUserId = $jinput->get('jid', '', 'int');
		if($joomlaUserId)
		{
			$joomlaUserId = $joomlaUserId;
		}
		else if(!$pagoUserId)
		{
			$userId = 0;
			if(!$userId)
			{
				$joomlaUserId = Pago::get_instance('users')->saveJoomlaUser($billing_address);
			}
		}
		else
		{
			$joomlaUserId = $billing_address['user_id'];
		}


		if($joomlaUserId)
		{
			// Insert Pago Users
			$billing_address['user_id'] = $joomlaUserId;
			$shipping_address['user_id'] = $joomlaUserId;
			$billingAddressId = Pago::get_instance('users')->saveUserAddress('b', $billing_address);

			if ($billingAddressId)
			{
				$shippingAddressId = Pago::get_instance('users')->saveUserAddress('s', $shipping_address);
			}
		}

		if($joomlaUserId)
		{
			$db->setQuery( "DELETE FROM #__pago_groups_users WHERE user_id = ".(int)$joomlaUserId."" );
			$db->query();



			if ( !empty($groups) ) {
				foreach( $groups as $group_id ) {
					$db->setQuery( "INSERT INTO #__pago_groups_users SET group_id = ".
						(int) $group_id .", user_id = ". (int)$joomlaUserId );
					$db->query();
				}
			}
		}

		if ( !JFactory::getApplication()->input->get( 'id' ) )
		{

			return $shippingAddressId;

		}
		return JFactory::getApplication()->input->get( 'id' );
	}

	function remove()
	{
		$db = JFactory::getDBO();

		$model = JModelLegacy::getInstance('Customers','PagoModel');

		if ( !is_array( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) )
			$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_CID_MUST_BE_AN_ARRAY' ) );

		foreach ( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) as $item_id ) {
			
			$this->table->delete($item_id);
			$this->table->reset();
			$sql = "SELECT id FROM #__pago_user_info WHERE id=".$item_id;
			$db->setQuery( $sql );
			$userIdExist = $db->loadResult();

			if ($userIdExist) 
			{
				JFactory::getApplication()->input->set( 'id', $userIdExist);
				$model->delete_address();
			}

		}

		$this->setRedirect( $this->redirect_to, JText::_( 'PAGO_ITEMS_DELETED' ) );
	}

	function checkUserExist()
	{
		ob_clean();
		$db = JFactory::getDBO();
		$jinput = JFactory::getApplication()->input;
		$email = $jinput->get('email', '', 'string');

		$sql= "SELECT name,email from #__users where email='".$email."'";
		$db->setQuery( $sql );
		$userData = $db->loadAssocList();
		if(isset($userData[0]['name']) && isset($userData[0]['email'])){
			echo 0;exit;
		}
		else
		{
			echo 1;exit();
		}

	}

	function getJoomlaUserInfo()
	{
		ob_clean();
		$db = JFactory::getDBO();
		$jinput = JFactory::getApplication()->input;
		$userid = $jinput->get('userid', '0', 'INT');

		$sql= "SELECT name,email from #__users where id=".$userid;
		$db->setQuery( $sql );
		$userData = $db->loadAssocList();
		if(isset($userData[0]['name']) && isset($userData[0]['email'])){
			echo $name = $userData[0]['name']."_".$userData[0]['email'];exit;
		}
		else
		{
			echo $name = " _ ";exit();
		}

	}
	function getCustomerAccount(){
		$db = JFactory::getDBO();
		$user_info = '';
		$user_address = '';
		JFactory::getApplication()->input->set( 'layout', 'caccount' );
		$userId = JFactory::getApplication()->input->get('userId', 0, 'int');

		if($userId){
			$sql= "SELECT * from #__users where id=".$userId;
			$db->setQuery( $sql );
			$user_info = $db->loadAssocList();

			$sql= "SELECT * from #__pago_user_info where user_id=".$userId;
			$db->setQuery( $sql );
			$user_address = $db->loadObjectList();
		}

		JFactory::getApplication()->input->set( 'user_data', $user_info );
		JFactory::getApplication()->input->set( 'user_address', $user_address );
		parent::display();
		exit();
	}

	function getAddAddress(){
		JFactory::getApplication()->input->set( 'layout', 'add_address' );
		
		if(JFactory::getApplication()->input->get('addr_id')){
			$addr_id = JFactory::getApplication()->input->get('addr_id');

			$db = JFactory::getDBO();
			$sql= "SELECT * from #__pago_user_info where id=".$addr_id;
			$db->setQuery( $sql );
			$user_address = $db->loadAssocList();
			JFactory::getApplication()->input->set( 'user_address', $user_address );
		}
		parent::display();
		exit();
	}

	function storeAddress(){
		$db = JFactory::getDBO();
		$address_data = JFactory::getApplication()->input->get('address', array(0), 'array');
		$AddressId = '';
		$address = array();
		$cdate =  date("Y-m-d H:i:s");

		$address['id'] = isset($address_data['id']) ? $address_data['id'] : "";
		
		$address['user_id'] = $address_data['user_id'];
		$address['company'] = $address_data['company'];
		$address['title'] = $address_data['company'];
		$address['last_name'] = $address_data['lastname'];
		$address['first_name'] = $address_data['firstname'];
		$address['middle_name'] = "";
		$address['phone_1'] = $address_data['telephoneno'];
		$address['phone_2'] = "";
		$address['address_1'] = $address_data['address1'];
		$address['address_2'] = $address_data['address2'];
		$address['city'] = $address_data['city'];
		$address['fax'] = "";
		$address['user_email'] = $address_data['email'];
		$address['country'] = $address_data['country'];
		$address['state'] = $address_data['state'];
		$address['zip'] = $address_data['postcodezip'];
		$address['cdate'] = $cdate;
		$address['mdate'] = $cdate;

		if($address_data['addr_type'] == 'b'){
			$AddressId = Pago::get_instance('users')->saveUserAddress('b', $address);
		}elseif ($address_data['addr_type'] == 's') {
			$AddressId = Pago::get_instance('users')->saveUserAddress('s', $address);
		}
		
		echo json_encode($address['user_id']);
		exit();
	}

	function romoveAddress(){
		$model = JModelLegacy::getInstance('Customers','PagoModel');
		echo $model->delete_address();
		exit();
	}
}
