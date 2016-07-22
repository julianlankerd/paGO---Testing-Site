<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiStates
{
	static public function get($dta)
	{
		$code = 200;
		$status = 'success';
		$model = JModelLegacy::getInstance( 'User_fields','PagoModel' )
						->get_countries_states();
						
		return [
			'code' => $code,
			'status' => $status,
			'model' => $model	
		];
	}
}
