<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');


class PagoModelAccount extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();
	}

	// Frontend stuff
	public function get_user()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT ku.*, u.*
					FROM #__pago_user_info AS ku
					LEFT JOIN #__users AS u ON u.id = ku.user_id
						WHERE ku.user_id = $user->id";

		$db->setQuery( $sql );

		return $db->loadObject();
	}

	public function get_user_addresses()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT *
					FROM #__pago_user_info
						WHERE user_id = $user->id";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_recent_orders_status()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT *
					FROM #__pago_orders
						WHERE user_id = $user->id
							ORDER BY cdate DESC
								LIMIT 5";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_recently_purchased_products()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT oi.*, i.*
					FROM #__pago_orders	AS o
					LEFT JOIN #__pago_orders_items AS oi ON oi.order_id = o.order_id
					LEFT JOIN #__pago_items AS i ON i.id = oi.item_id
						WHERE o.user_id = $user->id
							ORDER BY o.cdate DESC
								LIMIT 5";

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function update_account()
	{
		$post = JFactory::getApplication()->input->getArray($_POST);
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "UPDATE #__users SET email = '" .$post['new_email']. "' WHERE id='" .$post['user_id']. "'";
		$db->setQuery($sql);
		$result = $db->query();

		$sql = "UPDATE #__pago_user_info SET user_email = '" .$post['new_email']. "' WHERE user_id = '" .$post['user_id']. "'";
		$db->setQuery( $sql );
		$result = $db->query();

		return $result;
	}

	public function update_primary_address()
	{
		$post = JFactory::getApplication()->input->getArray($_POST);
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		if(!$post) return;

		$sql = "SELECT id
					FROM #__pago_user_info
					WHERE user_id = '" .$post['user_id']. "'
						AND address_type = 'p'";
		$db->setQuery( $sql );
		$id = $db->loadResult();

		$sql = "UPDATE #__pago_user_info
					SET first_name = '" .$post['first_name']. "',
						middle_name = '" .$post['middle_name']. "',
						last_name = '" .$post['last_name']. "',
						company = '" .$post['company']. "',
						address_1 = '" .$post['address_1']. "',
						address_2 = '" .$post['address_2']. "',
						city = '" .$post['city']. "',
						state = '" .$post['state']. "',
						zip = '" .$post['zip']. "',
						phone_1 = '" .$post['phone_1']. "',
						phone_2 = '" .$post['phone_2']. "'
						WHERE id = $id";
		$db->setQuery( $sql );
		$result = $db->query();

		return $result;
	}

	public function add_address( $user_data = null )
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();
		$insert = '';

		if ( $user_data === null ) {
			$post = JFactory::getApplication()->input->getArray($_POST);

			if(!$post) return;

			if( $post['address_type'] == 'p' ) {
				// We have a new primary address
				$sql = "SELECT id
							FROM #__pago_user_info
							WHERE user_id = '" .$post['user_id']. "'
								AND address_type = 'p'";
				$db->setQuery( $sql );
				$id = $db->loadResult();

				// If there is no id - lets skip this query
				if( $id ) {
					// Lets update the old primary address to just billing
					$sql = "UPDATE #__pago_user_info
								SET address_type_name = '',
									address_type = ''
									WHERE id = " .$id;
					$db->setQuery( $sql );
					$update = $db->query();
				}

				unset( $post['address_type_name'] );
				$post['address_type_name'] = 'Primary';
			}

			$insert = "(
							" .$post['user_id']. ",
							'" .$post['address_type']. "',
							'" .$post['address_type_name']. "',
							'" .$post['company']. "',
							'" .$post['last_name']. "',
							'" .$post['first_name']. "',
							'" .$post['middle_name']. "',
							'" .$post['phone_1']. "',
							'" .$post['phone_2']. "',
							'" .$post['address_1']. "',
							'" .$post['address_2']. "',
							'" .$post['city']. "',
							'" .$post['state']. "',
							'" .$post['zip']. "'
						)";
		} else {
			$insert = '';
			$user_id = $user->get( 'id' );
			foreach ( $user_data as $data ) {
				if ( !empty( $insert ) ) {
					$insert .= ',';
				}
				$insert .= "(
							" .$user_id. ",
							'" .$data->address_type. "',
							'" .$data->address_type_name. "',
							'" .$data->company. "',
							'" .$data->last_name. "',
							'" .$data->first_name. "',
							'" .$data->middle_name. "',
							'" .$data->phone_1. "',
							'" .$data->phone_2. "',
							'" .$data->address_1. "',
							'" .$data->address_2. "',
							'" .$data->city. "',
							'" .$data->state. "',
							'" .$data->zip. "'
						)";
			}

		}
		$sql = "INSERT INTO #__pago_user_info
			( user_id,address_type,address_type_name,company,last_name,first_name,
				middle_name,phone_1,phone_2,address_1,address_2,city,state,zip) VALUES ";
		$sql .= $insert;

		$db->setQuery( $sql );

		if (!$result = $db->query()){
			echo $db->stderr();
			return false;
		}

		return true;
	}

	public function update_address()
	{
		$post = JFactory::getApplication()->input->getArray($_POST);
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		if(!$post) return;

		if( $post['address_type'] == 'p' ) {
			// We have a new primary address
			$sql = "SELECT id
						FROM #__pago_user_info
						WHERE user_id = '" .$post['user_id']. "'
							AND address_type = 'p'";
			$db->setQuery( $sql );
			$id = $db->loadResult();

			// If there is no id - lets skip this query
			if( $id ) {
				// Lets update the old primary address to just billing
				$sql = "UPDATE #__pago_user_info
							SET address_type_name = '',
								address_type = 'b'
								WHERE id = " .$id;
				$db->setQuery( $sql );
				$update = $db->query();
			}
		}

		$address_type_name = '';
		if( $post['address_type'] == 's' ) {
			$address_type_name = 'Shipping';
		}

		if( $post['address_type'] == 'b' ) {
			$address_type_name = 'Billing';
		}

		if( $post['address_type'] == 'p' ) {
			$address_type_name = 'Primary';
		}

		$sql = "UPDATE #__pago_user_info
					SET	address_type = " . $db->Quote( $post['address_type'] ) . ",
						address_type_name = " . $db->Quote( $address_type_name ) . ",
						company = " . $db->Quote( $post['company'] ) . ",
						last_name = " . $db->Quote( $post['last_name'] ) . ",
						first_name = " . $db->Quote( $post['first_name'] ) . ",
						middle_name = " . $db->Quote( $post['middle_name'] ) . ",
						phone_1 = " . $db->Quote( $post['phone_1'] ) . ",
						phone_2 = " . $db->Quote( $post['phone_2'] ) . ",
						address_1 = " . $db->Quote( $post['address_1'] ) . ",
						address_2 = " . $db->Quote( $post['address_2'] ) . ",
						city = " . $db->Quote( $post['city'] ) . ",
						state = " . $db->Quote( $post['state'] ) . ",
						zip = " . $db->Quote( $post['zip'] ) . "
							WHERE id = " . $db->Quote( $post['id'] );
		$db->setQuery( $sql );

		if (!$result = $db->query()){
			echo $db->stderr();
			return false;
		}

		return true;
	}

	public function delete_address()
	{
		$id = JFactory::getApplication()->input->getInt( 'id' );
		$db = JFactory::getDBO();

		$sql = "DELETE FROM #__pago_user_info WHERE id = " .$id;
		$db->setQuery( $sql );

		if (!$result = $db->query()){
			echo $db->stderr();
			return false;
		}

		return true;
	}

	private function reset_old_address()
	{
	}

	public function update_password()
	{
		$mainframe = JFactory::getApplication();
		jimport('joomla.user.helper');

		$post = JFactory::getApplication()->input->getArray($_POST);

		// Make sure that we have a pasword
		if ( ! $post['txt_NewPassword'] )
		{
			$this->setError( JText::_( 'MUST_SUPPLY_PASSWORD' ) );
			return false;
		}

		// Verify that the passwords match
		if ( $post['txt_NewPassword'] != $post['txt_ConfirmPassword'] )
		{
			$this->setError( JText::_( 'PASSWORDS_DO_NOT_MATCH_LOW' ) );
			return false;
		}

		// Get the necessary variables
		$db			= JFactory::getDBO();
		$u			= JFactory::getUser();
		$salt		= JUserHelper::genRandomPassword( 32 );
		$crypt		= JUserHelper::getCryptedPassword( $post['txt_NewPassword'], $salt );
		$password	= $crypt.':'.$salt;

		// Get the user object
		$user = new JUser( $u->get( 'id' ) );

		// Fire the onBeforeStoreUser trigger
		JPluginHelper::importPlugin( 'user' );
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onBeforeStoreUser', array( $user->getProperties(), false ) );

		// Build the query
		$query 	= 'UPDATE #__users'
				. ' SET password = ' . $db->Quote( $password )
				. ' , activation = ""'
				. ' WHERE id = ' . (int) $u->get( 'id' )
				. ' AND block = 0';

		$db->setQuery( $query );

		// Save the password
		if ( !$result = $db->query() )
		{
			$this->setError( JText::_( 'DATABASE_ERROR' ) );
			return false;
		}

		// Update the user object with the new values.
		$user->password			= $post['txt_NewPassword'];
		$user->activation		= '';
		$user->password_clear	= $post['txt_ConfirmPassword'];

		// Fire the onAfterStoreUser trigger
		$dispatcher->trigger( 'onAfterStoreUser', array( $user->getProperties(), false, $result, $this->getError() ) );

		return true;
	}
}
