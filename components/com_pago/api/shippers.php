<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiShippers
{
	static public function get($dta)
	{
		$code = 200;
		$status = 'success';
		$config = Pago::get_instance('config')->get('global');
		
		if($config->get('checkout.skip_shipping'))
			return [
				'code' => 400,
				'status' => 'failure - config set to skip shipping prra',
				'model' => [@$model]	
			];
		
		require_once( JPATH_COMPONENT.DS.'controllers'.DS.'checkout'.'.php' );
		
		$checkout = new PagoControllerCheckout;
		$shipping_options = $checkout->get_shipping_methods(true);
		
		foreach($shipping_options as $k=>$option){
			if(empty($shipping_options[$k]) || isset($shipping_options[$k]['error'])){
				$code = 400;
				$status = 'failure - one or more shippers have error';
			}
			
			$shipping_options[$k] = array_values($option);	
		}
		
		if(empty($shipping_options)){
			$code = 400;
			$status = 'failure - no applicable shipping options';
		}
		
		foreach($shipping_options as $k=>$carrier){
			foreach($carrier as $k2=>$option){
				if ( !isset($shipping_options[$k][$k2]['value']) )
					continue;
				
				$value = $shipping_options[$k][$k2]['value'];
				$shipping_options[$k][$k2]['format_value']
					= Pago::get_instance('price')->format($value);
			}
		}
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => @$shipping_options	
		];
	}
	
	static private function get_lang()
	{
		//overzealous joomla lang object protected vars are belong to me
		$lang = (array)JFactory::getLanguage();
		
		foreach($lang as $k=>$v){
			if(strstr($k, 'strings')){
				$lang = $v;
				break;
			} 
		}
		
		foreach($lang as $k=>$v){
			if(!strstr($k, 'PAGO')) unset($lang[$k]);
		}
		
		return $lang;
	}
}
