<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

include JPATH_ADMINISTRATOR . '/components/com_pago/helpers/coupon_rules/rule.php';

abstract class coupon_rule_html extends rule
{
	public function get_html_fields()
	{
		$title = '<div class="pg-rule-title">' . $this->get_title() . '</div>';
		$input = '<div class="pg-rule-inputs pg-row">' . $this->get_inputs() . '</div>';

		return $title . $input;
	}

	abstract protected function get_title();
	abstract protected function get_inputs();
}
