<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiAddresses
{
	static public function get($dta)
	{
		$code = 200;
		$status = 'success - got user addresses';
		
		if(JFactory::getUser()->get('guest'))
			return [
				'code' => 400,
				'status' => 'failure - user not logged in',
				'model' => [@$model]	
			];
		
		$user_model = JModelLegacy::getInstance( 'Customers', 'PagoModel' );
		$addresses  = $user_model->get_user_addresses();	
		
		if(!empty($addresses)){
			/*$model = [];
			$code = 200;
			$status = 'success - got user addresses';
			
			foreach($addresses as $address){
				$model[$address->address_type][] = $address;
			}*/
		} else {
			$code = 200;
			$status = 'success - but no user addresses found lalala';
		}
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => @$addresses	
		];
	}
	
	static public function apply($dta)
	{
		if(JFactory::getUser()->get('guest'))
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
		
		$addr_model = JModelLegacy::getInstance( 'Customers', 'PagoModel' );
		$user_id = JFactory::getUser()->get('id');
		
		foreach($dta as $k=>$addr){
			$addr['user_id'] = $user_id;
			$dta[$k]['id'] = Pago::get_instance('users')
				->saveUserAddress($addr['address_type'], $addr);
		}
		
		return [
			'code' => 200,
			'status' => 'success - applied address',
			'model' => [@$dta]	
		];
	}
	
	static public function del($dta)
	{
		$code = 200;
		$status = 'success - successfully deleted address(es)';
		
		if(empty($dta))
			return [
				'code' => 400,
				'status' => 'failure - empty payload',
				'model' => [@$dta]	
			];
		
		foreach($dta as $item){
			if(!Pago::get_instance('users')
				->deleteUserAddress($item['id'])){
				
				$code = 400;
				$status = 'failure';
			}
		}
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => [@$dta]	
		];
	}
}
