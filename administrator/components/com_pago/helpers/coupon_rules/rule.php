<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

abstract class rule
{
	protected $coupon_id;
	protected $name;
	protected $params;
	protected $discount;
	protected $is_percent;

	public function __construct( $data = null )
	{
		if ( $data === null ) {
			return;
		}

		$this->unserialize( $data );
	}

	/**
	 * Unserialize data from database into rule object.
	 * Giving an array or object with keys/variables defined for:
	 *   coupon_id, name, params, discount, is_percent
	 * Assign the values to the corresponding object values
	 *
	 * @param mixed $data
	 * @return bool
	 */
	public function unserialize( $data )
	{
		if ( !is_array($data) && !is_object($data) ) {
			return false;
		}

		if ( is_array( $data ) ) {
			$this->coupon_id = $data['coupon_id'];
			$this->name = $data['name'];
			$this->params = json_decode($data['params']);
			$this->discount = $data['discount'];
			$this->is_percent = $data['is_percent'];
		} else if ( is_object( $data ) ) {
			$this->coupon_id = $data->coupon_id;
			$this->name = $data->name;
			$this->params = unserialize($data->params);
			$this->discount = $data->discount;
			$this->is_percent = $data->is_percent;
		}

		return true;
	}

	/**
	 * Serializes rule object to be stored into a database.
	 * Return an array with keys:
	 *   coupon_id, name, params, discount, is_percent
	 *
	 * @return array
	 */
	public function serialize()
	{
		$s = array();
		$s['coupon_id'] = $this->coupon_id;
		$s['name'] = $this->name;
		$s['params'] = json_decode($this->params);
		$s['discount'] = $this->discount;
		$s['is_percent'] = $this->is_percent;

		return $s;
	}
}
