<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

class PagoControllerAccount extends PagoController
{
	public function register()
	{
		JModelLegacy::addIncludePath( JPATH_SITE . '/components/com_users/models/' );
		$j_user = JModelLegacy::getInstance( 'registration', 'UsersModel' );
		$model = JModelLegacy::getInstance( 'customers', 'PagoModel' );
		$data = JFactory::getApplication()->input->getArray($_POST);
		$app = JFactory::getApplication();

		// pull in com_users language file to use the registration
		$lang = JFactory::getLanguage();
		$ext = 'com_users';
		$base_dir = JPATH_SITE;
		$lang->load( $ext, $base_dir );
		$usersConfig = JComponentHelper::getParams('com_users');
		$useractivation = $usersConfig->get('useractivation');
		if($useractivation == 1)
		{
			$msg = JTEXT::_("PAGO_SELF_ACTTVATION");
		}
		if($useractivation == 2)
		{
			$msg = JTEXT::_("PAGO_CONTACT_ADMIN_FOR_ACTIVATION");
		}
		$redirect = JFactory::getApplication()->input->get( 'redirect', null );
		if ( $redirect == null )
		{
			$redirect = "index.php?option=com_pago&view=account";
		}

		if ( $j_user->register( $data ) )
		{
			if($useractivation == 1 ||$useractivation == 2)
			{
				$this->setRedirect( $redirect, $msg );
			}
			else
			{
				 $app->login( array(
					'username' => $data['username'],
					'password' => $data['password']
				) );
				$this->setRedirect( $redirect, $msg );
			}
		}
		else {
			$errors = $j_user->getErrors();
			$app->enqueueMessage( $errors[0], 'error' );
			$this->setRedirect( 'index.php?option=com_pago&view=account&layout=register');
		}
	}

	public function uploadAvatar(){
		$user = JFactory::getUser();
 	    $return = array();

		if (!$user->guest) {
			if (!empty($_FILES) && file($_FILES['Filedata']['tmp_name'])) {
				$tempFile   = $_FILES['Filedata']['tmp_name'];
				$filename = $_FILES['Filedata']['name'];
				
				jimport( 'joomla.filesystem.folder' );
				
				$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'users';
				if(!JFolder::exists($path)){
					mkdir($path, 0755, true);
				}

				$allowTypes = array('jpg','png');
				$file_extn = explode(".", strtolower($filename));
				$file_size = filesize ( $tempFile );
				
				if (in_array($file_extn[1], $allowTypes)) {
					if($file_size <=  5000000){
						$checkPath = JPATH_ROOT . '/media/pago/users/'.$user->id.'.jpg';
						if(file_exists($checkPath)){
							unlink($checkPath);	
						}
						$checkPath = JPATH_ROOT . '/media/pago/users/'.$user->id.'.png';
						if(file_exists($checkPath)){
							unlink($checkPath);	
						}
						move_uploaded_file($tempFile,$path.DIRECTORY_SEPARATOR.$user->id.'.'.$file_extn[1]);
						//$return['message'] = JText::_( 'PAGO_ACCOUNT_AVATAR_DONE' );
						$imgPath = JURI::root( true ) .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'users';
						$return['filePath'] = $imgPath.DIRECTORY_SEPARATOR.$user->id.'.'.$file_extn[1];
						$return['status'] = 1;
					}else{
						$return['message'] = JText::_( 'PAGO_ACCOUNT_AVATAR_SIZE_LIMIT' );
						$return['status'] = 0;
					}
				}else{
					$return['message'] =  JText::_( 'PAGO_ACCOUNT_AVATAR_WRONG_TYPE' );
					$return['status'] = 0;
				}
			}
		}
		$return =  json_encode($return);
		echo $return;
		exit();	
	}
	public function removeAvatar(){
		$user = JFactory::getUser();
 	    $return = array();
 	    $config = Pago::get_instance('config')->get();
		$pago_theme   = $config->get( 'template.pago_theme', 'default' );
		if (!$user->guest) {
			$userId = $user->id;
			$checkPath = JPATH_ROOT . '/media/pago/users/'.$userId.'.jpg';
			if(file_exists($checkPath)){
				unlink($checkPath);	
			}
			$checkPath = JPATH_ROOT . '/media/pago/users/'.$userId.'.png';
			if(file_exists($checkPath)){
				unlink($checkPath);	
			}
		}
		$result = array();
		$result['avatar'] = JURI::root( true ). '/components/com_pago/templates/'.$pago_theme.'/images/no-image.png';
		echo json_encode($result);
		exit();
	}
	
	public function update_account($noredirect=false)
	{
		// Check for request forgeries
		JSession::getFormToken() or jexit( 'Invalid Token' );

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance( 'Customers','PagoModel' );
		$return = true;
		$msg = '';
		$result = $model->update_account();
		
		// if ($result === 'big_file') {
		// 	$msg = JText::_( 'PAGO_ACCOUNT_AVATAR_SIZE_LIMIT' );
		// 	$return = false;
		// } elseif ($result === 'wrong_ex') {
		// 	$msg = JText::_( 'PAGO_ACCOUNT_AVATAR_WRONG_TYPE' );
		// 	$return = false;
		// }elseif ($result) {
		// 	$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_MESSAGE' );
		// }else{
		// 	$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_FAILURE' );
		// 	$return = false;
		// }
		
		$success = 1;
		
		if ($result) {
			$msg .= JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_MESSAGE' );
		}else{
			$msg .= JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_FAILURE' );
			$return = false;
			$success = 0;
		}
		
		if($return){
			$post = JFactory::getApplication()->input->getArray($_POST);
			if ( $post['txt_NewPassword'] )
			{
				$result = $model->update_password();
				if ($result) {
					$msg .="</br>". JText::_( 'Password Successfully Updated' );
				} else {
					$msg .="</br>".  JText::_( 'Error Updating Password. Please review settings' );
					$success = 0;
				}
			}
		}
		
		if($noredirect){
			return [
				'success' => $success,
				'message' => str_replace('</br>', ' - ', $msg)
			];
		}
		
		$link = 'index.php?option=com_pago&view=account&layout=account_settings';

		return $this->setRedirect( $link, $msg );
	}

	public function update_primary_address()
	{
		// Check for request forgeries
		JSession::getFormToken() or jexit( 'Invalid Token' );

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance( 'Customers','PagoModel' );

		if ($model->update_primary_address()) {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_MESSAGE' );
			$link = 'index.php?option=com_pago&view=account&layout=account_settings';
		} else {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_FAILURE' );
			$link = 'index.php?option=com_pago&view=account&layout=account_settings';
		}

		return $this->setRedirect( $link, $msg );
	}

	public function add_address()
	{
		// Check for request forgeries
		JSession::getFormToken() or jexit( 'Invalid Token' );

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model   = JModelLegacy::getInstance( 'Checkout','PagoModel' );

		$guest   = JFactory::getApplication()->input->getInt( 'guest', 0 );
		$same_as = JFactory::getApplication()->input->get( 'sameasshipping', 'no' );
		$data    = JFactory::getApplication()->input->get( 'address', array(), 'array' );
		
		
		if (array_key_exists('b', $data)) {
			if(strlen($data["b"]['postcodezip'])>6 || strlen($data["b"]['postcodezip'])<4 ){
				$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_ZIP_ERROR_MESSAGE' );
				$link = 'index.php?option=com_pago&view=account';
				return $this->setRedirect( $link, $msg );
			}
			if(strlen($data["b"]['telephoneno'])>15 || strlen($data["b"]['telephoneno'])<3 ){
				$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_PHONE_ERROR_MESSAGE' );
				$link = 'index.php?option=com_pago&view=account';
				return $this->setRedirect( $link, $msg );
			}
		}
		if (array_key_exists('s', $data)) {
			if(strlen($data["s"]['postcodezip'])>6 || strlen($data["s"]['postcodezip'])<4 ){
				$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_ZIP_ERROR_MESSAGE' );
				$link = 'index.php?option=com_pago&view=account';
				return $this->setRedirect( $link, $msg );
			}
			if(strlen($data["s"]['telephoneno'])>15 || strlen($data["s"]['telephoneno'])<3 ){
				$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_PHONE_ERROR_MESSAGE' );
				$link = 'index.php?option=com_pago&view=account';
				return $this->setRedirect( $link, $msg );
			}
		}

		if ( $model->set_address( $data, $guest, $same_as,'yes' ) ) {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_MESSAGE' );
			$link = 'index.php?option=com_pago&view=account';
		} else {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_FAILURE' );
			$link = 'index.php?option=com_pago&view=account';
		}

		return $this->setRedirect( $link, $msg );
	}

	public function update_address()
	{
		// Check for request forgeries
		JSession::getFormToken() or jexit( 'Invalid Token' );
		$checkout = JFactory::getApplication()->input->get( 'checkout' );
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance( 'Customers','PagoModel' );

		$email = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/"; 
		

		$post = JFactory::getApplication()->input->getArray($_POST);

		foreach ( $post['address'] as $k => $data ) {
			if(!preg_match($email, $post['address'][$k]['email']) || !preg_match('/^[0-9\s(-)\s+\s-]*$/', $post['address'][$k]['telephoneno']) || !preg_match('/^[a-zA-Z]*$/', $post['address'][$k]['firstname']) || !preg_match('/^[a-zA-Z]*$/', $post['address'][$k]['lastname']) || !preg_match('/^[a-zA-Z]*$/',$post['address'][$k]['city']) || !preg_match('/^[0-9]*$/',$post['address'][$k]['postcodezip'])){
				$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_FAILURE' );
				if($checkout)
				{
					$link = 'index.php?option=com_pago&view=checkout';
				}
				else
				{
					// $link = 'index.php?option=com_pago&view=account&layout=addresses';
					$link = 'index.php?option=com_pago&view=account';
				}

				return $this->setRedirect( $link, $msg );
			}
					if(strlen($post['address'][$k]['postcodezip'])>6 || strlen($post['address'][$k]['postcodezip'])<4){
						$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_ZIP_ERROR_MESSAGE' );
						$link = 'index.php?option=com_pago&view=account';
						return $this->setRedirect( $link, $msg );
					}
					if(strlen($post['address'][$k]['telephoneno'])>15 || strlen($post['address'][$k]['telephoneno'])<3){
							$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_PHONE_ERROR_MESSAGE' );
						$link = 'index.php?option=com_pago&view=account';
						return $this->setRedirect( $link, $msg );
					}
		}

		if ($model->update_address()) {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_MESSAGE' );
		} else {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_FAILURE' );
		}
		if($checkout)
		{
			$link = 'index.php?option=com_pago&view=checkout';
		}
		else
		{
			// $link = 'index.php?option=com_pago&view=account&layout=addresses';
			$link = 'index.php?option=com_pago&view=account';
		}

		return $this->setRedirect( $link, $msg );
	}

	public function delete_address()
	{
		// Check for request forgeries
		JSession::getFormToken( 'get' ) or jexit( 'Invalid Token' );
		$checkout = JFactory::getApplication()->input->get( 'checkout' );
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance( 'Customers','PagoModel' );

		if ($model->delete_address()) {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_DELETE_MESSAGE' );
		} else {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_UPDATE_FAILURE' );
		}
		if($checkout)
		{
			$link = 'index.php?option=com_pago&view=checkout';
		}
		else
		{
			$link = 'index.php?option=com_pago&view=account';
		}

		return $this->setRedirect( $link, $msg );
	}

	public function update_password()
	{
		// Check for request forgeries
		JSession::getFormToken() or jexit( 'Invalid Token' );

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance( 'Customers','PagoModel' );

		if ($model->update_password()) {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_PASSWORD_MESSAGE' );
			$link = 'index.php?option=com_pago&view=account&layout=account_settings';
		} else {
			$msg = JText::_( 'PAGO_ACCOUNT_ADDRESS_PASSWORD_CHANGE_FAILURE' );
			$link = 'index.php?option=com_pago&view=account&layout=account_settings';
		}

		return $this->setRedirect( $link, $msg );
	}
	public function rate(){
		$user = JFactory::getUser();
		if (!$user->guest) {
			$itemId = JFactory::getApplication()->input->get( 'itemId' );
			$rating = JFactory::getApplication()->input->get( 'rating' );

			JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
			$itemModel = JModelLegacy::getInstance( 'Item','PagoModel' );
			$rate = $itemModel->rate($user->id,$itemId,$rating);
			if($rate){
				$return['status'] = 2;	
				$return['message'] = JTEXT::_("PAGO_RATE_THANKS_FOR_VOTING");	
				$return['rate'] = $rate;	
			}else{
				$return['status'] = 1;
				$return['message'] = JTEXT::_("PAGO_RATE_VOTED");
			}
		}else{
			$return['status'] = 0;
			$return['message'] = JTEXT::_("PAGO_RATE_LOGIN_TO_VOTE");
		}

		$return = json_encode($return);
		echo $return;
		exit();
	}
	
	public function getUsernameFromEmail(){
		$email = JFactory::getApplication()->input->getString('email');
		$db = JFactory::getDBO();
		$query = ('select username from #__users where email = "'.$email.'"');
		$db->setQuery($query);
		$res = $db->loadResult();
		
		if(is_null($res))
		{
			$res="NULL";
		}
		
		if($res != "")
		{
			$query = ('select * from #__users where email = "' . $email . '"');
			$db->setQuery($query);
			$joomUser = $db->loadObjectList();
			$joomUser = $joomUser[0];
			//Get PAgo USER

			$sql = "SELECT id
					FROM #__pago_user_info
						WHERE user_id = '".$joomUser->id."'";

			$db->setQuery( $sql );
			$pagoUser = $db->loadResult();

			if(!$pagoUser)
			{
				$name = explode(" ", $joomUser->name);
				$address = array();
				$cdate =  date("Y-m-d H:i:s");
				$address['id'] = "";
				$address['user_id'] = $joomUser->id;
				$address['company'] = '';
				$address['title'] = '';
				$address['last_name'] = @$name[1];
				$address['first_name'] = @$name[0];
				$address['middle_name'] = "";
				$address['phone_1'] = "";
				$address['phone_2'] = "";
				$address['address_1'] = "";
				$address['address_2'] = "";
				$address['city'] = "";
				$address['fax'] = "";
				$address['user_email'] = $joomUser->email;
				$address['country'] = '';
				$address['state'] = "";
				$address['zip'] = "";
				$address['cdate'] = $cdate;
				$address['mdate'] = $cdate;
				// Insert pago user

				$addressId = Pago::get_instance( 'users' )->saveUserAddress('s', $address );
				$addressId = Pago::get_instance( 'users' )->saveUserAddress('b', $address );
				// End
			}
		}

		echo $res;
		exit;
	}

	public function checkEmail(){
		$post = JFactory::getApplication()->input->getArray($_POST);
		$email = $post['email'];
		$userId = $post['userId'];
		$db = JFactory::getDBO();
		$query = ('select username from #__users where email = "'.$email.'" AND id <> '.$userId);
		$db->setQuery($query);
		$res = $db->loadResult();
		if(is_null($res)){
			$res="NULL";
		}
		echo $res;
		exit;
	}
	
	function update_cc()
	{
		$jinput = JFactory::getApplication()->input;
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		$order_id = $jinput->get('order_id', '', 'int');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$payload = (object)[
			'livemode' => $livemode,
			'id' => $jinput->get('pago_customer_id', '', 'string'),
			'account' => $account,
			'number' => $jinput->get('pago_customer_number', '', 'string'),
			'cvc' => $jinput->get('pago_customer_cvc', '', 'string'),
			'exp_month' => $jinput->get('pago_customer_exp_month', '', 'string'),
			'exp_year' => $jinput->get('pago_customer_exp_year', '', 'string')
		];
		
		//print_r($payload);die;
		
		$api = new PagoControllerApi;
		$res = $api->call('PUT', 'subscr', $payload, false);
		
		if(@$res->id){
	       	JFactory::getApplication()->enqueueMessage(JText::_('PAGO_ORDERI_CARDSUCCESS'), 'message');
		} else {
			JFactory::getApplication()->enqueueMessage($res->detail, 'warning');
		}
		
		$this->setRedirect( 
			JRoute::_('index.php?option=com_pago&view=account&layout=order_receipt&status=true&order_id='.$order_id, false)
		);
	}
	
	function subscr_cancel(){
		
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', 0, 'int');
		$sid = $jinput->get('id', 0, 'string');
		$order_item_id = $jinput->get('order_item_id', 0, 'int');
		$customer = $jinput->get('customer', 0, 'string');
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$payload = (object)[
			'id' => $sid, 
			'livemode' => $livemode,
			'customer' => $customer,
			'account' => $account
		];
		
		$api = new PagoControllerApi;
		$res = $api->call('DELETE', 'subscr', $payload, false);
		
		if(@$res->id){
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');
	    	$order_item = JTable::getInstance('Orders_items', 'Table');
	       	
	       	$data = [
	       		'order_item_id' => $order_item_id,
	       		'sub_payment_data' => json_encode($res)
	       	];
	       	
	       	$order_item->bind($data);
	       	
	       	$order_item->store();
	       	
	       	JFactory::getApplication()->enqueueMessage(JText::_('PAGO_SUBSCR_CANCELED'), 'notice');
		}
		
 		$this->setRedirect( 
			JRoute::_('index.php?option=com_pago&view=account&layout=order_receipt&status=true&order_id='.$order_id, false)
		);
		
		return;
	}
	
	function subscr_reinstate(){
		
		$jinput = JFactory::getApplication()->input;
		$order_id = $jinput->get('order_id', 0, 'int');
		$sid = $jinput->get('id', 0, 'string');
		$order_item_id = $jinput->get('order_item_id', 0, 'int');
		$customer = $jinput->get('customer', 0, 'string');
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$account = $params->get('payoptions.live_recipient_id');
		
		if(!$livemode) $account = $params->get('payoptions.test_recipient_id');
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$payload = (object)[
			'id' => $sid, 
			'customer' => $customer,
			'livemode' => $livemode,
			'account' => $account
		];
		
		$api = new PagoControllerApi;
		$res = $api->call('POST', 'subscr', $payload, false);
		
		if(@$res->id){
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');
	    	$order_item = JTable::getInstance('Orders_items', 'Table');
	       	
	       	$data = [
	       		'order_item_id' => $order_item_id,
	       		'sub_payment_data' => json_encode($res)
	       	];
	       	
	       	$order_item->bind($data);
	       	
	       	$order_item->store();
	       	
	       	JFactory::getApplication()->enqueueMessage(JText::_('PAGO_SUBSCR_REINSTATED'), 'notice');
		}
		
 		$this->setRedirect( 
			JRoute::_('index.php?option=com_pago&view=account&layout=order_receipt&status=true&order_id='.$order_id, false)
		);
		
		return;
	}
}
