<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiPaygates
{
	static public function get($dta)
	{
		$code = 400;
		$status = 'failure - undefined error...';
		
		$config = Pago::get_instance('config')->get('global');
		
		require_once( JPATH_COMPONENT.DS.'controllers'.DS.'checkout'.'.php' );
		$checkout = new PagoControllerCheckout;
		
		$payment_options = $checkout->billing(true);
		//print_r($payment_options);
		
		if(!empty($payment_options))
			$status = 'failure - no applicable shipping options';
		
		foreach($payment_options as $k=>$po){
			
			if(strstr($po['logo'], '/payum')) continue;
			
			$po['logo'] = JURI::base() 
				. "plugins/pago_gateway/{$k}/icon.jpg";
				
			$payment_options[$k] = $po;
				
		}
		
		return [
			'code' => 200,
			'status' => 'success - paygates loaded',
			'model' => @$payment_options	
		];
	}
}
