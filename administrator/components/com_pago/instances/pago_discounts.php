<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class pago_discounts
{
	public function __construct()
	{
		$this->config = Pago::get_instance( 'config' )->get();
	}

	public function getDiscountRule($cart)
	{
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/models' );
		$this->discount_model = JModelLegacy::getInstance( 'discount', 'PagoModel' );
		$discountsRules = $this->discount_model->getDiscountRule( $cart );
		if(count($discountsRules) > 0)
		{
			return $discountsRules[0];
		}
		
	}
}
