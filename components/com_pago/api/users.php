<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiUsers
{
	static public function register($dta)
	{
		$dta_login = $dta;
		
		$dta = array_pop($dta);
		$name = $dta['name'];
		$username = $dta['username'];
		$email = $dta['email'];
		$password = $dta['password'];
		$auto_login = $dta['auto_login'];
		
		$juser = JFactory::getUser($username);
	
	    //we don't want to overrite if user already exists for Joomla!
	    if($juser->id)
	    	return [
				'code' => 400,
				'status' => 'error - user already exists',
				'model' => [@$model]	
			];
	
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
	        $code = '400';
	        $status = $juser->getErrors();
	    }
	    if(!$juser->save()){
	        $code = '400';
	        $status = $juser->getErrors();
	    }
	    
	    $user = JFactory::getUser();
        $user->load($juser->id);

	  // plugin event after registration
			
	  JPluginHelper::importPlugin('pago_register');
	  $dispatcher = JEventDispatcher::getInstance();
	  $dispatcher->trigger('on_complete_registration', array());
        
        if($user->id > 0 && $auto_login) {
        	$dta_login[0]['remember'] = true;
            return self::login($dta_login);
        } elseif($user->id){
        	$code = '200';
	        $status = 'success - registered user';
	        $model = [
				'id' => $user->id,
				'name' => $user->name,
				'username' => $user->username,
				'email' => $user->email
			];
        }
        
        return [
			'code' => $code,
			'status' => $status,
			'model' => [@$model]	
		];
	}
	
	static public function get($dta)
	{
		$dta = false;
		$code = '400';
	    $status = 'user not found';
	    
		if(isset($dta['username'])){
			$user = JFactory::getUser($dta['username']);
			$status = 'success - got user by name';
		} else {
			$user = JFactory::getUser();
			$status = 'success - got logged in user';
		}
	    
	    if($user->id){
        	$code = '200';
	        $model = [
				'id' => $user->id,
				'name' => $user->name,
				'username' => $user->username,
				'email' => $user->email
			];
        } else $status = 'failure - user not logged in';
        
        return [
			'code' => $code,
			'status' => $status,
			'model' => [@$model]	
		];
		
		$app = JFactory::getApplication();
		$app->logout();
		
		return [
			'code' => 200,
			'status' => 'success - retrieved user data',
			'model' => [@$model]	
		];
	}
	
	static public function login($dta)
	{
		$app = JFactory::getApplication();
		$filter = JFilterInput::getInstance();
		$user = JFactory::getUser();
		$code = 400;
		$status = 'login failed';
		$dta = array_pop($dta);
		$password = isset( $dta['password'] ) ? $dta['password'] : '';
		
		$credentials = [
			'username' => $filter->clean($dta['username'], 'username'),
			'password' => $filter->clean($password, 'raw')
		];
		
		$options = [
			'remember' => $filter->clean($dta['remember'], 'bool')
		];
		
		if (true === $app->login($credentials, $options)){
			if ($options['remember'] == true){
				$app->setUserState('rememberLogin', true);
			}
			
			$code = 200;
			$status = 'success -  user logged in';
			
			$user = JFactory::getUser();
			
			$model = [
				'id' => $user->id,
				'name' => $user->name,
				'username' => $user->username,
				'email' => $user->email
			];
		}
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => [@$model]	
		];
	}
	
	static public function logout()
	{
		$app = JFactory::getApplication();
		$app->logout();
		
		return [
			'code' => 200,
			'status' => 'success - user logged out',
			'model' => [@$model]	
		];
	}
}
