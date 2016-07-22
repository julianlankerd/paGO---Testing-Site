<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class pago_coupons
{
	protected $code;
	protected $coupon_model;
	protected $rules = array();

	/**
	 * Set the code and verify that its valid before we try and process the rules.
	 *
	 * @params string $code coupon code
	 * @return bool
	 */
	public function set_code( $code )
	{
		$c_id = $this->verify_code( $code );
		if ( !$c_id ) {
			return false;
		}

		$this->code = $code;

		if ( !$this->get_rules( $c_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the rules associated with the coupon code
	 *
	 * @params string|null $code Coupon code or empty to use the code assigned by set_code()
	 * @return array
	 */
	private function get_rules( $c_id )
	{
		$rules = $this->coupon_model->get_rules( $c_id );

		if ( is_array( $rules ) ) {
			foreach ( $rules as $rule ) {
				$r = $this->load_rule( $rule );
				if ( !$r ) {
					continue;
				}
				$this->rules[] = $r;
			}
		}

		return true;
	}

	/**
	 * Instantiate rule classes to be used to process the coupon
	 *
	 * @params array $data assoc array of rule data
	 * @return object
	 */
	private function load_rule( $data )
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_pago/helpers/coupon_rules/';

		$class_name = $data['name'];

		if ( !file_exists( $path . $class_name . '.php' ) ) {
			return false;
		}

		include $path . $class_name . '.php';

		if ( !class_exists( $class_name ) ) {
			return false;
		}

		$rule = new $class_name( $data );

		return $rule;

	}

	/**
	 * Verify that the coupon code actually exists in the DB.
	 *
	 * @params string $code coupon code
	 * @return bool
	 */
	public function verify_code( $code )
	{
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/models' );
		$this->coupon_model = JModelLegacy::getInstance( 'coupon', 'PagoModel' );

		return $this->coupon_model->verify( $code );
	}

	/**
	 * Process the coupon against the cart to calculate discount
	 *
	 * @params array $cart Cart
	 * @return array $cart
	 */
	public function process( $cart, $coupon_assign_type)
	{
		$discounts = array();
		foreach ( $this->rules as $rule ) {
			$discount = $rule->process( $cart, $coupon_assign_type);
			if($discount)
			{
				$valid_coupon = true;
				$discounts[] = $discount;
			}
		}

		if($valid_coupon)
		{
			return $discounts;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Increment code use
	 */
	public function incr_use()
	{
		$this->coupon_model->increment_used( $this->code );

	}
}
