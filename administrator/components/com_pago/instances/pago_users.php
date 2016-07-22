<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_users
{
	public function get( $user_id = false )
	{
		if( !$user_id ){
			$user_id = JFactory::getUser()->id;
		}

		// if we don't have a user_id then we are doing a guest checkout
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/models' );

		$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );
		$user_fields_model->setState( 'user_id', $user_id );

		$user_data = $user_fields_model->get_data();

		if( (int)$user_id === 0 ) {
			$cart = Pago::get_instance( 'cart' )->get();
			$user_data = $cart['user_data'];

			$user_data[0] = (array)$user_data[0];
			if ( isset( $user_data[1] ) ) {
				$user_data[1] = (array)$user_data[1];
			}

			$user_data[0]['country_id'] =
				$user_fields_model->get_country_id( $user_data[0]['country'], '2code' );

			$user_data[0]['state_id'] = $user_fields_model->get_state_id(
				$user_data[0]['country_id'],
				$user_data[0]['state'],
				'name'
			);
			if ( isset( $user_data[1] ) ) {
				$user_data[1]['country_id'] =
					$user_fields_model->get_country_id( $user_data[1]['country'], '2code' );
				$user_data[1]['state_id'] = $user_fields_model->get_state_id(
					$user_data[1]['country_id'],
					$user_data[1]['state'],
					'name'
				);
			}
		}

		if ( !isset( $user_data[0] ) ) {
			return array( 'shipping' => new stdClass(), 'billing' => new stdClass() );
		}
		// we need to make sure they are an object this type of stuff would be better handled in a
		// standard object :(
		$data['shipping'] = (object)$user_data[0];
		$data['billing'] = (object)$user_data[0];

		if( isset( $user_data[1] ) ){
			$data['billing'] = (object)$user_data[1];
		}

		if ( isset( $user_data['groups'] ) ) {
			$data['groups'] = $user_data['groups'];
		} else {
			$data['groups'] = array();
		}

		return $data;
	}

	public function getAllUsers($allField = false)
	{
		$db   = JFactory::getDBO();
		$sql = '';
		if(!$allField){
			$sql = "SELECT *
					FROM #__users WHERE name !='Super User'";
		}
		else{
			$sql = "SELECT name as label,id as value
					FROM #__users WHERE name !='Super User'";
		}

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function getPagoUsers($id = false)
	{
		$db   = JFactory::getDBO();
		$sql = '';
		$data = array();
		$users = array();

		if(!$id){
			$sql = "SELECT DISTINCT user_id FROM #__pago_user_info";
			$db->setQuery( $sql );
			$data = $db->loadAssocList();
		}
		else{
			$sql = "SELECT user_id FROM #__pago_user_info WHERE user_id=$id LIMIT 0,1";
			$db->setQuery( $sql );
			$data = $db->loadAssocList();
		}

		foreach ($data as $value) {
			$sql = "SELECT * 
					FROM #__users WHERE name != 'Super User' AND id =".$value['user_id'];
			$db->setQuery( $sql );
			$user = $db->loadAssocList();
			if(isset($user[0])){	
				$user[0]['value'] = $user[0]['id'];
				$user[0]['text'] = $user[0]['name'];
				$users[] = $user[0];
			}
		}

		return $users;
	}

	public function get_user_addresses($userid)
	{
		$db   = JFactory::getDBO();
		$sql = "SELECT *
					FROM #__pago_user_info
						WHERE user_id = '".$userid."'";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_user_billing_addresses($userid)
	{
		$db   = JFactory::getDBO();
		$sql = "SELECT *
					FROM #__pago_user_info
						WHERE address_type = 'b' and user_id = '".$userid."'";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_user_shipping_addresses($userid)
	{
		$db   = JFactory::getDBO();
		$sql = "SELECT *
					FROM #__pago_user_info
						WHERE address_type = 's' and user_id = '".$userid."'";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_shipping_addresses($addressid)
	{
		$db   = JFactory::getDBO();
		$sql = "SELECT *
					FROM #__pago_user_info
						WHERE address_type = 's' and id = '".$addressid."'";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}


	public function saveUserAddress($address_type, $address)
	{
		require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'tables' . DS . 'userinfo.php');
		$db   = JFactory::getDBO();

		if ( $address_type == 's' )
		{
			$index = 0;
			$name = 'Shipping';
		}
		elseif ( $address_type == 'b' )
		{
			$index = 1;
			$name = 'Billing';
		}
		
		$user_data = JTable::getInstance( 'Userinfo', 'Table' );
		if(isset($address['id']))
		{
			$user_data->load($address['id']);
			$user_data->id = $address['id'];
		}
		else
		{
			$user_data->load();
		}

		$user_data->user_id = @$address['user_id'];
		$user_data->company = @$address['company'];
		$user_data->title = @$address['title'];
		$user_data->last_name = @$address['last_name'];
		$user_data->first_name = @$address['first_name'];
		$user_data->address_type = @$address_type;
		$user_data->address_type_name = @$name;
		$user_data->middle_name = @$address['middle_name'];
		$user_data->phone_1 = @$address['phone_1'];
		$user_data->phone_2 = @$address['phone_2'];
		$user_data->address_1 = @$address['address_1'];
		$user_data->address_2 = @$address['address_2'];
		$user_data->city = @$address['city'];
		$user_data->fax = @$address['fax'];
		$user_data->user_email = @$address['user_email'];
		$user_data->country = @$address['country'];
		$user_data->state = (isset($address['state'])) ? $address['state'] : '';
		$user_data->zip = @$address['zip'];
		$user_data->cdate = (isset($address['cdate'])) ? $address['cdate'] : '';
		$user_data->mdate = (isset($address['mdate'])) ? $address['mdate'] : '';


		if (!$user_data->store())
		{
			echo $row->getError();
			return false;
		}

		$insertedId = $user_data->id;

		return $insertedId;
	}

	public function deleteUserAddress($id)
	{
		require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'tables' . DS . 'userinfo.php');
		$db = JFactory::getDbo();

		$sql = "SELECT user_id 
					FROM #__pago_user_info
							WHERE id = " . (int)$id;
		$db->setQuery( $sql );
		$user_id = $db->loadResult();
		
		if(JFactory::getUser()->id != $user_id)
			return false;
		
		$user_data = JTable::getInstance( 'Userinfo', 'Table' );
		
		return $user_data->delete($id);
	}
	
	public function saveJoomlaUser($userData)
	{
		// Store User in Joomla users database
		$data['username'] = $userData['email'];
		$data['password'] = $userData['password1'];
		$data['password1'] = $userData['password2'];
		$data['name'] = $userData['name'];
		$data['email'] = $userData['email'];
		$data['email1'] = $userData['email'];
		$data['block'] = 0;
		$data['groups'] = array("Registered" => "2");

		if (trim($data['username']) == "")
		{
			$error = JError::raiseWarning(0, JText::_('EMPTY_USERNAME'));
			return false;
		}

		// Get required system objects
		$user = new JUser();

		if (!$user->bind($data))
		{
			$error = JError::raiseError(500, $user->getError());
			return false;
		}

		$newId = $user->get('id');

		if ($user->get('block') && $newId == $me->id && !$me->block)
		{
			$error = JError::raiseWarning(0, JText::_('YOU_CANNOT_BLOCK_YOURSELF'));
			return false;
		}

		if (!$user->save())
		{
			JError::raiseWarning('', JText::_($user->getError()));
			return false;
			//return $this->setRedirect($this->redirect_to, false);
		}

		return $user->id;
	}

	public function get_order_user_address($user_id, $address_id, $order_id)
	{
		$db   = JFactory::getDBO();
		$sql = "SELECT *
					FROM #__pago_orders_addresses
						WHERE user_id = $user_id and order_id = $order_id and id =$address_id";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_all_order_of_user($user_id)
	{
		$db   = JFactory::getDBO();
		$sql = "SELECT *
					FROM #__pago_orders
						WHERE user_id = $user_id and order_status = 'C'";

		$db->setQuery( $sql );
		$total_orders = count($db->loadObjectList());
		return $total_orders;
	}

}
