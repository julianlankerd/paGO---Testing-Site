<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiAccount
{
	static public function get($dta)
	{
		$code = 200;
		$status = 'success - got user addresses';
		$account = [];
		
		if(JFactory::getUser()->get('guest'))
			return [
				'code' => 400,
				'status' => 'failure - user not logged in',
				'model' => [@$model]	
			];
		
		$user_model = JModelLegacy::getInstance( 'Customers', 'PagoModel' );
		
		$days = 15;
		
		$account['user_info'] = $user_model->get_user();
		$account['recent_orders'] = $user_model->get_recent_orders_status($days);
		$account['recent_products'] = $user_model->get_recently_purchased_products();
		$account['addresses']  = $user_model->get_user_addresses();	
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => @$account	
		];
	}
	
	static public function apply($dta)
	{
		$user = JFactory::getUser();
		
		if($user->get('guest'))
			return [
				'code' => 400,
				'status' => 'failure - user not logged in',
				'model' => [@$model]	
			];
		
		if(empty($dta))
			return [
				'code' => 400,
				'status' => 'failure - empty payload',
				'model' => [@$model]	
			];
		
		$dta = $dta[0];
		
		if(!isset($dta['email1'])){
			$dta['email1'] = $user->email;
			$dta['email2'] = $user->email;
		}
		
		$jinput = JFactory::getApplication()->input;
		
		$jinput->set('name', $dta['name']);
		$jinput->set('user_email', $dta['email1']);
		$jinput->set('new_email', $dta['email2']);
		
		if(isset($dta['password1']) && isset($dta['password1'])){
			$jinput->set('txt_NewPassword', $dta['password1']);
			$jinput->set('txt_ConfirmPassword', $dta['password2']);
		}
		
		require_once( JPATH_COMPONENT.DS.'controllers'.DS.'account'.'.php' );
		
		$account = new PagoControllerAccount;
		$response = $account->update_account(true);
		
		if(!$response['success']) $code = 400;
		
		$account = self::get([]);
		
		return [
			'code' => $code,
			'status' => $response['message'],
			'model' => $account['model']	
		];
	}
}
