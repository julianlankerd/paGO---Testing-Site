<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) die;

jimport( 'joomla.application.component.controller' );

class PagoControllerApi extends JControllerLegacy
{
	private $output = [
		'code' => 400,
		'status' => 'Bad Request',
		'model' => 0
	];
	
	function display($cachable = false, $urlparams = array())
	{
		$input = json_decode(file_get_contents("php://input"), true);
		
		if(empty($input)) $output['status'] = 'malformed json input';
		
		$output = $this->output;
		$act = explode('.', @$input['act']);
		$model = @$act[0];
		$method = @$act[1];
		$dta = @$input['dta'];
		
		if(file_exists(JPATH_COMPONENT . '/api/'.$model.'.php'))
			require_once JPATH_COMPONENT . '/api/'.$model.'.php';
		
		$class = 'PagoApi'.$model;
		
		if(method_exists($class, $method)){
			$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');
			$output = $class::$method($dta);
		} else $output['status'] = 'act not found';
		
		echo json_encode([
			'meta' => [
				'code'=> $output['code'],
				'status' => $output['status'],
				'model_name' => $model,
				'method_name' => $method,
			],
			'response' => [ 
				$model => $output['model']	
			]
		]);
	}
	
	function call($request_method=false, $service=false, $payload=false, $exit=true){
		
		header('Content-Type: application/json');
		
		Pago::load_helpers( 'pagoparameter' );
		
		$input = new JInput;
		
		$params = Pago::get_instance('params')->params;
		$request_method or $request_method = $_SERVER['REQUEST_METHOD'];
		$service or $service = $_GET['service'];
		$payload or $payload = json_decode(file_get_contents("php://input"));
		
		//could be dealing with an image and it's associated bullshit
		$payload or $payload = json_decode($input->get('payload', '', 'post'));
		
		$is_multipart = false;
		
		if(!empty($_FILES)){
			foreach($_FILES as $name=>$data){
				$payload->$name = new CurlFile($data['tmp_name']);
				$is_multipart = true;
			}
		}
	
		if($service == 'wingman/subscriber' && $request_method == 'POST'){
			$seowingman_user = $this->get_seowingman_user();
			$payload->analyst_login = $seowingman_user['login'];
			$payload->analyst_password = $seowingman_user['password'];
		}
		
		$auto_id = false;
		
		if(isset($payload->id) && $payload->id == 'auto') $auto_id = true;
			
		if($auto_id){
			if(!$payload->id = $this->get_auto_id(md5($service)))
				unset($payload->id);
		}
		//$exit=1; //turn on and go to index.php?option=com_pago&view=payoptions to test call
		$domain = $params->get('pago_api.endpoint', 'https://api.pagocommerce.com') . '/';
		$version = $params->get('pago_api.version', 'v1') . '/';
		$testmode = $params->get('pago_api.testmode', 'v1');
		
		$url = $domain . $version . $service;
		
		$ch = curl_init();
		
		$header = [
			'api-domain: ' . $domain,
			'api-version: ' . $version,
			'api-testmode: ' . $testmode,
			'api-livemode: ' //. //'true' //this is for wingman
		];
		
		if(!$is_multipart){
			$header[] = 'Content-Type: application/json';
			$payload_data = json_encode($payload);
		} else {
			$payload_data = $payload;
		}
		
		if($request_method == 'PATCH'){
			if(isset($payload->id)){
				$url = $url . '/' . str_replace('/', '*', $payload->id);
			} elseif($id = $this->get_auto_id(md5($service))) {
				$url = $url . '/' . str_replace('/', '*', $id);
				$this->set_auto_id(md5($service), 0);
			}
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_data);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		}
		
		if($request_method == 'PUT'){
			if(isset($payload->id)){
				$url = $url . '/' . str_replace('/', '*', $payload->id);
			} elseif($id = $this->get_auto_id(md5($service))) {
				$url = $url . '/' . str_replace('/', '*', $id);
				$this->set_auto_id(md5($service), 0);
			}
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_data);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		}
		
		if($request_method == 'DELETE'){
			if(isset($payload->id)){
				$url = $url . '/' . str_replace('/', '*', $payload->id);
			} elseif($id = $this->get_auto_id(md5($service))) {
				$url = $url . '/' . str_replace('/', '*', $id);
				$this->set_auto_id(md5($service), 0);
			}
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_data);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		}
		
		if($request_method == 'GET'){
			if(isset($payload->id)){
				$url = $url . '/' . str_replace('/', '*', $payload->id);
			} elseif($id = $this->get_auto_id(md5($service))) {
				$url = $url . '/' . str_replace('/', '*', $id);
			}
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_data);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		}
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/../cacert.crt');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		
		if($request_method == 'POST'){
			curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_data);
		}
		
		if(!$output = curl_exec($ch)){
		    exit('API CURL ERROR: ' . curl_error($ch));
		}	
		
		curl_close($ch);
		
		$output_array = json_decode($output);
		
		if($auto_id) $this->set_auto_id(md5($service), @$output_array->id);
		
		if($exit)
			exit($output);
			
		return $output_array;
	}
	
	private function get_seowingman_user(){
		
		jimport( 'joomla.user.helper');
       	
		$password =  JUserHelper::genRandomPassword();
		
		$data = [
			"name" => 'SEO Wingman',
			"username" => 'seowingman',
			"password" => $password,
			"password2" => $password,
			"email" => 'info@corephp.com',
			"block" => 0,
			"groups" => [7]
		];
			
		$user = new JUser;
		
		if(JFactory::getUser('seowingman')->id){
			$user = JFactory::getUser('seowingman');
		}
		
		if(!$user->bind($data)) {
		      throw new Exception("Could not bind data. Error: " . $user->getError());
		}
		if (!$user->save()) {
		      throw new Exception("Could not save user. Error: " . $user->getError());
		}
		      
		return [
			'login' => 'seowingman',
			'password' => $password
		];
	}
	private function get_auto_id($name_space){
		
		if($name_space == md5('wingman/subscriptions') || $name_space == md5('wingman/s3'))
			$name_space = md5('wingman/subscriber');
			
		return Pago::get_instance('params')->get($name_space, 0);
	}
	
	private function set_auto_id($name_space, $id){
		
		Pago::get_instance('params')->set($name_space, $id);
	}
}
