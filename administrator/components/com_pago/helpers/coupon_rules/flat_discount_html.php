<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

include JPATH_ADMINISTRATOR . '/components/com_pago/helpers/coupon_rules/coupon_rule_html.php';

class flat_discount_html extends coupon_rule_html
{
	protected function get_title()
	{
		//return JText::_('PAGO_COUPON_FLAT_DISCOUNT');
	}

	protected function get_inputs()
	{
		$inputs = '<div class = "pg-col-6">';
		$inputs .= '<label for="">'.JText::_('PAGO_COUPON_FLAT_DISCOUNT_TYPE') .'</label>';
		$inputs .= '<select name="params[rules][' . $this->name . '][is_percent]">';
		if ( $this->is_percent ) {
			$inputs .= '<option value="0">'. JText::_('PAGO_COUPON_FLAT_DISCOUNT_AMOUNT') .
				'</option><option value="1" selected="selected">'.
				JText::_('PAGO_COUPON_FLAT_DISCOUNT_PERCENT') . '</option>';
		} else {
			$inputs .= '<option value="0" selected="selected">'.
				JText::_('PAGO_COUPON_FLAT_DISCOUNT_AMOUNT') . '</option>'.
				'<option value="1">'. JText::_('PAGO_COUPON_FLAT_DISCOUNT_PERCENT') .
				'</option>';
		}
		$inputs .= '</select>';
		$inputs .= '</div>';

		$inputs .= '<div class = "pg-col-6">';
		$inputs .= '<label for="">'.JText::_('PAGO_COUPON_FLAT_DISCOUNT_DISCOUNT').'</label>';
		if ( isset( $this->discount ) ) {
			$inputs .= '<input name="params[rules][' . $this->name .
				'][discount]" type="text" value="'. $this->discount .'"/>';
		} else {
			$inputs .= '<input name="params[rules][' . $this->name . '][discount]" type="text"/>';
		}
		$inputs .= '</div>';

		return $inputs;
	}
}
