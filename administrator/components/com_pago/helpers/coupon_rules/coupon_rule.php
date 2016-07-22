<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

include JPATH_ADMINISTRATOR . '/components/com_pago/helpers/coupon_rules/rule.php';

abstract class coupon_rule extends rule
{
	/**
	 * Must be implemented
	 * Giving a cart object check if the rule applies to the cart
	 *
	 * @param array &$cart refrence to cart object
	 * @return int
	 */
	abstract public function process( &$cart ,$coupon_assign_type);
}
