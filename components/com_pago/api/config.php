<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiConfig
{
	static public function get($dta)
	{
		$config = Pago::get_instance('config')->get('global');
		
		if(!empty($dta)){
			$model = [];
			foreach($dta as $i){
				
				if($i['type']== 'language'){
					$model[$i['type']] = self::get_lang();
				} else {
					$model[$i['type']] = $config->get($i['type']);
				}
			}
		}
		
		return [
			'code' => 200,
			'status' => 'success - retrieved config data',
			'model' => [@$model]	
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
