<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

/**
 * Pago Cookie
 *
 * This class provides methods for storing persistant user data in a cookie
 */

/*
EXAMPLE:

Pago::get_instance( 'cookie' )->get( 'myname', 'default value' );
Pago::get_instance( 'cookie' )->set( 'myname', 'set value' );
*/
class pago_cookie
{
	/**
	 * Holds the cookie id
	 */
	private $cookie_id = false;

	/**
	 * Holds the cookie data
	 */
	private $data = false;

	/**
	 * constructor sets up the cookie and data
	 *
	 */
	function __construct()
	{
		$this->del_cookie();
		$this->cookie_id or $this->set_cookie_id();
		$this->data or $this->set_data();
	}

	/**
	 * Gets cookie data
	 *
	 * @param string name of data dimension
	 * @param string|array default data to return if none exists
	 *
	 * @return object
	 */
	public function get( $name, $default = null )
	{
		if ( isset( $this->data[$name] ) ) {
			if ( $default !== null && empty( $this->data[$name] ) ) {
				$this->set( $name, $default );
			}
		} else {
			$this->set( $name, $default );
		}

		return $this->data[ $name ];
	}

	/**
	 * Sets cookie data
	 *
	 * @param string name of data dimension
	 * @param string|array value of data
	 *
	 * @return object
	 */
	public function set( $name, $value )
	{
		$db = JFactory::getDBO();

		$this->data[ $name ] = $value;

		$data = json_encode( $this->data );

		$user = JFactory::getUser();

		$data = $this->merge_cookie($this->data, $user->id);
		
		
		$query = "UPDATE #__pago_cookie
					SET data=" . $db->Quote( $data, true )
					. ", user_id=" . $db->Quote( $user->id, true ) .
					" WHERE id='{$this->cookie_id}'";

		$db->setQuery( $query );
		$db->query();

		return $this->data;
	}

	/**
	 * Sets cookie id
	 */
	private function set_cookie_id()
	{
		if( isset( $_COOKIE[ 'pago_id' ] ) ){
			$this->cookie_id = $_COOKIE[ 'pago_id' ];
			return;
		}

		$this->set_cookie();
	}

	/**
	 * Sets cookie
	 */
	private function set_cookie(){

		$this->cookie_id = md5( time() . uniqid ( rand(), true ) );

		$created = date( 'Y-m-d H:i:s', time() );

		$db = JFactory::getDBO();

		$user = JFactory::getUser();

		$query = "INSERT INTO #__pago_cookie SET
					id='{$this->cookie_id}',
					user_id=" . $db->Quote( $user->id, true ) .", created='{$created}'";

		$db->setQuery( $query );
		$db->query();

		//setcookie ( 'pago_id', $this->cookie_id, strtotime( 'now + 1 year' ) );
		setcookie ( 'pago_id', $this->cookie_id, strtotime( 'now + 5 day' ) ,'/' );
	}

	/**
	 * Sets data
	 */
	private function set_data()
	{
		$db = JFactory::getDBO();

		$query = "SELECT data FROM #__pago_cookie
					WHERE id='{$this->cookie_id}'";

		$db->setQuery( $query );

		$data = $db->loadObject();

		if( !empty( $data ) ){
			$this->data = json_decode( $data->data, true );
		} else {
			$created = date( 'Y-m-d H:i:s', time() );
			$user = JFactory::getUser();

			$query = "INSERT INTO #__pago_cookie SET
					id='{$this->cookie_id}',
					user_id=". $db->Quote( $user->id, true ) . ", created='{$created}'";

			$db->setQuery( $query );
			$db->query();
		}
	}

	/**
	 * Deletes cookie
	 */
	private function del_cookie()
	{
		$db = JFactory::getDBO();
		//setcookie ( 'pago_id', 0, strtotime( 'now - 5 day' ));
		$days_ago = date('Y-m-d H:i:s', strtotime('-5 days', time()) );

		$del_cookie = "DELETE FROM #__pago_cookie WHERE created < '".$days_ago."'";
		$db->setQuery( $del_cookie );
		$db->query();
	}

	public function merge_cookie($data, $userid){
		if($userid != 0){

			$session = JFactory::getSession();
			$cookie_check  = $session->get('cookie_check');
			
			if(isset($data['cart_0']['items'])){
				if(isset($data['cart_'.$userid]['items'])){
					if($cookie_check=="" || count($data['cart_'.$userid]['items']) == 0)
					{
						$addItems = array();
						foreach($data['cart_0']['items'] as $gitem){
						
							$equlas = false;

							foreach($data['cart_'.$userid]['items'] as $item){
								if(is_array($item)){
									if($item['id'] == $gitem['id']){
										if($item['varationId'] == $gitem['varationId']){
											$equlas = true;
											break;
										}
									}
								}
								else{
									if($item->id == $gitem['id']){
										if($item->varationId == $gitem['varationId']){
											$equlas = true;
											break;
										}
									}
								}
							}

							if(!$equlas){
								$addItems[] = $gitem;
							}
						}
	
						$session->set('cookie_check', $userid);

						foreach ($addItems as $gitem) {
							JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
							$model = JModelLegacy::getInstance('Attribute','PagoModel');
							$varation = $model->get_product_varations_by_id($gitem['varationId'], true);

							if($varation && $varation->attributes){
								$attributes = array();
								foreach ($varation->attributes as $attribute) {

									$attributes[$attribute->attribute->id] = $attribute->option->id; 	
								}
							}
							else{
								$attributes = array();
							}

							$aaa = Pago::get_instance('cart')->add( $gitem['id'], $gitem['cart_qty'], $attributes, $gitem['varationId'] );
						}
					}
				}
				else{
					$data['cart_'.$userid] = $data['cart_0'];
				}
				
			}
		}
		return json_encode($data);
	}

	public function del_guest_item($itemid, $varId){
		$user = JFactory::getUser();
		if($user->id != 0){
			if ( $varId != 0 ) {
			
				foreach($this->data['cart_0']['items'] as $key => $gitem){
					if($gitem['id'] == $itemid && $gitem['varationId'] == $varId){
						unset($this->data['cart_0']['items'][$key]);
					}
				}
			}
			else{
				
				foreach($this->data['cart_0']['items'] as $key => $gitem){
					if($gitem['id'] == $itemid && $gitem['varationId'] == 0){
						unset($this->data['cart_0']['items'][$key]);
					}
				}
			}
			if(empty($this->data['cart_0']['items'])){
				$this->set('cart_0', null);
			}
			else{
				$this->set('cart_0', $this->data['cart_0']);
			}
		}
	}
}
