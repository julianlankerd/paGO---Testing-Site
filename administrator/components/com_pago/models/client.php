<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');


class PagoModelClient extends JModelLegacy
{
    var $_data;
 	var $_order  = array();

	function __construct()
	{
		parent::__construct();

		$array = JFactory::getApplication()->input->get('cid',  0, 'array');
		$this->setId((int)$array[0]);
	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id        = $id;
		$this->_data    = null;
	}

	function getData()
	{
		// Load the data]


		$this->cid = JFactory::getApplication()->input->get('cid', array(0), 'array' );
		$id = $this->cid[0];

		if (empty( $this->_data )) {

			$query = "SELECT * FROM  #__pago_user_info
					 			WHERE user_id = $id";

			$this->_db->setQuery( $query );
			//$this->_data = $this->_db->loadObject();

			$data = $this->_db->loadObjectList();

			$this->_data = $data[0];



			if(isset($data[1]))
			foreach($data[1] as $k=>$v){
				$this->_data->{"m_$k"} = $v;
			}

		}


		if (!$this->_data) {
			$this->_data = new stdClass();

			$this->_data->id = 0;
			$this->_data->user_id = 0;
			$this->_data->address_type = false;
			$this->_data->address_type_name = false;
			$this->_data->company = false;
			$this->_data->title = false;
			$this->_data->last_name = false;
			$this->_data->first_name = false;
			$this->_data->middle_name = false;
			$this->_data->phone_1 = false;
			$this->_data->phone_2 = false;
			$this->_data->fax = false;
			$this->_data->address_1 = false;
			$this->_data->address_2 = false;
			$this->_data->city = false;
			$this->_data->state = false;
			$this->_data->country = false;
			$this->_data->zip = false;
			$this->_data->user_email = false;
			$this->_data->cdate = date( 'Y-m-d H:i:s', time() );
			$this->_data->mdate = date( 'Y-m-d H:i:s', time() );
		}

		return $this->_data;
	}

	function store()
	{
		$error=false;

		$row = $this->getTable();

		$data = JFactory::getApplication()->input->getArray($_POST);
		$data = $data['params'];

		$create = false;
		$id = $data['id'];

		$user = $this->get_user( $data );



		if(!$user) return false;

		$data['user_id'] = $user->id;

		if($id == 0){
			$data['cdate'] = date( 'Y-m-d H:i:s', time() );
		}


		if (!$row->bind($data)) JError::raiseWarning( 500, $row->getError() ); $error=$row->getError();

		if (!$row->check()) JError::raiseWarning( 500, $row->getError() ); $error=$row->getError();

		if (!$row->store()) JError::raiseWarning( 500, $row->getError() ); $error=$row->getError();

		if($error){
			return false;
		}

		if($id == 0){
			$ulid = $user->id .'_b';
			$db = $row->_db;

			$query = "UPDATE #__pago_user_info SET id = '$ulid', address_type='b' WHERE id=0";
			$db->setQuery($query);
			$result = $db->query();
		}
		else {
			$data['id'] = $user->id .'_m';
		}

		//mailing address stuff
		$data['address_type'] = 'm';

		if($data[ 'm_address_1']){
			foreach( $data as $k=>$v ){
				if( isset( $data[ 'm_' . $k ] ) ){
					$data[$k] = $data[ 'm_' . $k ];
				}
			}
		}


		if (!$row->bind($data)) JError::raiseWarning( 500, $row->getError() ); $error=$row->getError();

		if (!$row->check()) JError::raiseWarning( 500, $row->getError() ); $error=$row->getError();

		if (!$row->store()) JError::raiseWarning( 500, $row->getError() ); $error=$row->getError();

		if($error){
			return false;
		}

		if($id == 0){
			$id = $user->id .'_m';
			$db = $row->_db;

			$query = "UPDATE #__pago_user_info SET id = '$id', address_type='m' WHERE id=0";
			$db->setQuery($query);
			$result = $db->query();
		}


		return $user->id;
	}

	function get_user( $kdata ){



		if( $kdata['user_id'] ){
			$user = JFactory::getUser( $kdata['user_id'] );


			return $user;
		} else {
			$user = JFactory::getUser( 0 );
		}


		//print_r($user);die();

		// get the ACL
		$acl = JFactory::getACL();

		/* get the com_user params */
		 jimport('joomla.user.helper');
		jimport('joomla.application.component.helper'); // include libraries/application/component/helper.php
		$usersParams = &JComponentHelper::getParams( 'com_users' ); // load the Params

		// "generate" a new JUser Object
		//$user = JFactory::getUser(0); // it's important to set the "0" otherwise your admin user information will be loaded

		$data = array(); // array for all user settings

		// get the default usertype
		$usertype = $usersParams->get( 'new_usertype' );
		if (!$usertype) {
			$usertype = 'Registered';
		}

		// set up the "main" user information

		$data['name'] = $kdata['first_name'].' '.$kdata['last_name']; // add first- and lastname
		$data['username'] = $kdata['username']; // add username
		$data['email'] = $kdata['user_email']; // add email
		$data['gid'] = $acl->get_group_id( '', $usertype, 'ARO' );  // generate the gid from the usertype

		/* no need to add the usertype, it will be generated automaticaly from the gid */

		$password = JUserHelper::genRandomPassword();

		$data['password'] = $password; // set the password
		$data['password2'] = $password; // confirm the password
		$data['sendEmail'] = 1; // should the user receive system mails?

		/* Now we can decide, if the user will need an activation */

		//$useractivation = $usersParams->get( 'useractivation' ); // in this example, we load the config-setting
		$useractivation = false;

		if ($useractivation == 1) { // yeah we want an activation

			jimport('joomla.user.helper'); // include libraries/user/helper.php
			$data['block'] = 1; // block the User
			$data['activation'] =JUtility::getHash( JUserHelper::genRandomPassword() ); // set activation hash (don't forget to send an activation email)

		}
		else { // no we need no activation

			$data['block'] = 0; // don't block the user

		}

		if (!$user->bind($data)) { // now bind the data to the JUser Object, if it not works....
			JError::raiseWarning('', JText::_( $user->getError())); // ...raise an Warning
			return false; // if you're in a method/function return false
		}

		if (!$user->save()) { // if the user is NOT saved...
			JError::raiseWarning('', JText::_( $user->getError())); // ...raise an Warning
			return false; // if you're in a method/function return false
		}


		//send password to user
		$mail = JFactory::getMailer();

		$mail->to = array();
		$mail->isHTML(true);

		$conf =& JFactory::getConfig();

		//$email_body = $params->get( 'transaction_completed' );
		$email_body = 'username:' . $kdata['username'] . ' password:' . $password;

		//$email_body = str_replace( '{first_name}', $user_data->first_name, $email_body);
		//$email_body = str_replace( '{last_name}', $user_data->last_name, $email_body);
		//$email_body = str_replace( '{receipt}', $this->get_receipt($tpl), $email_body);

		$mail->setBody( $email_body );

		$mail->setSubject("Account Created " . $kdata['username'] );
		//$mail->setSender( $params->get( 'store_email' ) );
		$mail->setSender( 'adam.docherty@gmail.com' );

		//$mail->addRecipient( 'adam.docherty@gmail.com' );
		$mail->addRecipient( $kdata['user_email'] );

		if($conf->get('mailer') == 'smtp'){
			$mail->useSMTP(
				$conf->get('smtpauth'),
				$conf->get('smtphost'),
				$conf->get('smtpuser'),
				$conf->get('smtppass'),
				$conf->get('smtpsecure'),
				$conf->get('smtpport')
			);
		}

		if($mail->Send() === true){

		}

		return $user; // else return the new JUser object
	}
}