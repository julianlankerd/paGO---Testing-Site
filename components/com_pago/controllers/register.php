<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') )  die( 'Direct Access to this location is not allowed.' ); 

class PagoControllerRegister extends PagoController
{
	public function register(){
		// Check for request forgeries.
		if(!JSession::checkToken()){
			jexit(JText::_('PAGO_INVALID_TOKEN'));
		}
		// Get the user data.
		$errors = '';
		$requestData = JFactory::getApplication()->input->post->get('jform', array(), 'array');
		
		$config = Pago::get_instance('config')->get('global');
		$security_level = $config->get( 'checkout.checkout_register_security', 0 );
		
		if($security_level == 1){
			$recaptcha_field = JFactory::getApplication()->input->post->get('recaptcha_response_field');
			
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$res = $dispatcher->trigger('onCheckAnswer',$recaptcha_field);
			
			if(!$res[0]){
				$errors['captcha'] = JText::_('PAGO_CAPTCHA_MISMATCH');
			}
		}
		
		if($security_level == 2){
			
			if(JFactory::getApplication()->input->post->get('hp')){
				exit;
			}
			
			JPluginHelper::importPlugin('pago_security');
			$dispatcher = JDispatcher::getInstance();
			
			$res = $dispatcher->trigger('is_spam', array(
				$requestData['name1'], 
				$requestData['email1'], 
				0, 
				0
			));
			
			if(!$res[0]){
				$errors['akismet'] = JText::_('PAGO_FLAGGED_SPAM');
			}
		}
		
		if($requestData['name1']==''){
			$errors['name1'] = JText::_('PAGO_NAME_IS_REQUIRED');
		}
		if($requestData['email1']==''){
			$errors['email'] = JText::_('PAGO_EMAIL_IS_REQUIRED');
		}else{
			if(!filter_var($requestData['email1'], FILTER_VALIDATE_EMAIL)){
				$errors['email'] = JText::_('PAGO_NOT_VALID_EMAIL');
			}else{
				if($this->checkEmailExists($requestData['email1'])){
					$errors['email'] = JText::_('PAGO_EMAIL_ALREADY_USED');
				}
			}
		}
		if($requestData['password1']==''){
			$errors['password'] = JText::_('PAGO_PASSWORD_IS_REQUIRED');
		}else if($requestData['password1']!=$requestData['password2']){
			$errors['password'] = JText::_('PAGO_PASSWORD_DONOT_MATCH');
		}elseif($requestData['password1']==$requestData['password2'] && strlen($requestData['password2'])<4){
			$errors['password'] = JText::_('PAGO_PASSWORD_LENGTH');
		}
		if($errors!=''){
			jexit(json_encode($errors));
		}
		
		if($security_level != 3){
			$reg_response = $this->auto_register(
				$requestData['name1'], 
				$requestData['email1'],
				$requestData['email1'], 
				$requestData['password1']
			);
			
			jexit('refresh');
		}
		
		// Initialise variables.
		$app	= JFactory::getApplication();
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_users/models');
		$model = JModelLegacy::getInstance( 'Registration', 'UsersModel' );
		JForm::addFormPath(JPATH_SITE . '/components/com_users/models/forms');
		JForm::addFieldPath(JPATH_SITE . '/components/com_users/models/fields');



		// Validate the posted data.
		$form	= $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();
			if(count($errors)){
				$errors['unknown'] = JText::_('PAGO_UNKNOWN_ERROR');
				jexit(json_encode($errors));
			}
		}
		
		JPluginHelper::importPlugin('pago_orders');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAfterRegistration',array($requestData['email1'], $requestData['name1']));
		
		jexit('1');
	}
	public function checkEmailExists($email){
		$db = JFactory::getDBO();
		$sql = "SELECT id FROM #__users WHERE `email`='".$email."'";
		$db->setQuery($sql);
		$db->query();
		if($db->getNumRows()>0){
			return true;
		}
		return false;
	}
	
	function auto_register($name, $username, $email, $password){
		
	    $juser = JFactory::getUser($username);
	
	    $password = $password;
	
	    //we don't want to overrite if user already exists for Joomla!
	    if($juser->id) return;
	
	    jimport('joomla.application.component.helper');
	
	    $config = JComponentHelper::getParams('com_users');
	
	    // Default to Registered.
	    $defaultUserGroup = $config->get('new_usertype', 2);
	
	    $juser = JUser::getInstance();
	
	    $data = array(
	        'name' => $name,
	        'username' => $username,
	        'email' => $email,
	        'groups' => array($defaultUserGroup),
	        'password' => $password,
	        'password2' => $password
	    );
	
	    if(!$juser->bind($data)){
	        //print('Could not bind data. Error: '.$juser->getError());return;
	    }
	    if(!$juser->save()){
	        //print('Could not save user. Error: '.$juser->getError());return;
	    }
	    
	    $user = JFactory::getUser();
        $user->load($juser->id);
        
        if(!$user->id > 0) {
            return;
        }

        // Allow a page to redirect the user to
        /*$redirect = $this->params->get('redirect');
        if($redirect > 0) {
            $redirect = JRoute::_('index.php?Itemid='.$redirect);
        } else {
            $redirect = null;
        }*/

		$redirect = null;
		
        // Construct the options
        $options = array();
        $options['remember'] = true;
        $options['return'] = $redirect;
        $options['action'] = 'core.login.site';

        // Construct a response
        jimport('joomla.user.authentication');
        JPluginHelper::importPlugin('authentication');
        JPluginHelper::importPlugin('user');
        $authenticate = JAuthentication::getInstance();

        // Construct the response-object
        $response = new JAuthenticationResponse;
        $response->type = 'Joomla';
        $response->email = $user->email;
        $response->fullname = $user->name;
        $response->username = $user->username;
        $response->password = $user->username;
        $response->language = $user->getParam('language');
        $response->status = JAuthentication::STATUS_SUCCESS;
        $response->error_message = null;

        // Authorise this response
        $authorisations = $authenticate->authorise($response, $options);

        // Run the login-event
        $results = JFactory::getApplication()->triggerEvent('onUserLogin', array((array) $response, $options));
	}
}