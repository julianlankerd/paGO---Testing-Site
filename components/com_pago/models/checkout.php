<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

jimport( 'joomla.application.component.model' );
jimport( 'joomla.filter.filterinput' );
//jimport( 'pago.address' );

class PagoModelCheckout extends JModelLegacy
{
	public function set_address( $data, $guest, $sameas, $save_address )
	{
		ob_clean();
		// just implementing the same as already in checkout
		$cart = Pago::get_instance( 'cart' )->get();
		if ( isset( $cart['user_data'] ) && !empty( $cart['user_data'] ) ) {
			$user_data = $cart['user_data'];
			foreach ( $user_data as $k => $udata ) {
				$user_data[$k] = (object) $udata;
			}
		} else {
			$user_data = array();
		}

		//for use in grabbing/adding addresses
		$user_model = JModelLegacy::getInstance( 'Customers', 'PagoModel' );


		$filter = new JFilterInput();
		if($sameas)
		{
			$k = 'b';
			$index = 1;
			$name = 'Billing';
		}

		if ( !empty( $data ) ) {
			foreach ( $data as $k => $address ) {
				if ( $k == 's' ) {
					$index = 0;
					$name = 'Shipping';
				} else if ( $k == 'b' ) {
					$index = 1;
					$name = 'Billing';
				}

				$address_id = 0;

				if ( isset( $address['id'] ) ) {
					$address_id = $filter->clean( $address['id'] );
				}

				$user_data[$index] = new stdClass;

				//is this a new address?
				if( $address_id == 0 ) {
					
					// we have to save for everything else to work. This needs to be fixed too much
					// assuming that only a logged in user can checkout.
					//if( !empty( $address['save'] ) )
					$new_address = true;

					// we only assume these are the field names for right now
					// assummed wrong...
					/*$user_data[$index]->company = $filter->clean( $address['company'] );
					$user_data[$index]->last_name = $filter->clean( $address['lastname'] );
					$user_data[$index]->first_name = $filter->clean( $address['firstname'] );
					$user_data[$index]->address_type = $k;
					$user_data[$index]->address_type_name = $name;
					$user_data[$index]->middle_name = '';
					$user_data[$index]->phone_1 = $filter->clean( $address['telephoneno'] );
					$user_data[$index]->phone_2 = '';
					$user_data[$index]->address_1 = $filter->clean( $address['address1'] );
					$user_data[$index]->address_2 = $filter->clean( $address['address2'] );
					$user_data[$index]->city = $filter->clean( $address['city'] );
					$user_data[$index]->fax = '';
					*/
					
					$user_data[$index]->company = $filter->clean( $address['company'] );
					$user_data[$index]->last_name = $filter->clean( $address['last_name'] );
					$user_data[$index]->first_name = $filter->clean( $address['first_name'] );
					$user_data[$index]->address_type = $k;
					$user_data[$index]->address_type_name = $name;
					$user_data[$index]->middle_name = '';
					$user_data[$index]->phone_1 = $filter->clean( $address['phone_1'] );
					$user_data[$index]->phone_2 = $filter->clean( $address['phone_2'] );;
					$user_data[$index]->address_1 = $filter->clean( $address['address_1'] );
					$user_data[$index]->address_2 = $filter->clean( $address['address_2'] );
					$user_data[$index]->city = $filter->clean( $address['city'] );
					$user_data[$index]->fax = '';
					
					$cart['user_email'] = $filter->clean( $address['user_email'] );
					
					$user_data[$index]->user_email = $filter->clean( $address['user_email'] );
					$user_data[$index]->country = $filter->clean( $address['country'] );
					$user_data[$index]->state = (isset($address['state'])) ? $filter->clean( $address['state'] ) : '';
					$user_data[$index]->zip = $filter->clean( $address['zip'] );

				//otherwise look for existing address
				} else {
					$saved_addresses = $user_model->get_user_addresses();

					foreach( $saved_addresses as $user_address ) {
						if( $user_address->id === $address_id ) {
							$address = (array) $user_address;
							break;
						}
					}
					//unset($address['id']);
					unset($address['user_id']);
					unset($address['cdate']);
					unset($address['mdate']);
					unset($address['perms']);

					$user_data[$index] = (object) $address;
					$user_data[$index]->address_type = $k;
					$user_data[$index]->address_type_name = $name;

					// save billing in cart
					$user_data[1] = clone $user_data[$index];
					$user_data[1]->address_type = 'b';
					$user_data[1]->address_type_name = 'Billing';
				}
			}
		}
		// just a simple copy over for now. probably should actually have this as a flag
		// TODO: Is this needed anymore - does not work.
		

		if ( $sameas === 'yes' && $index != 1 ) {
			$user_data[1] = clone $user_data[0];
			$user_data[1]->address_type = 'b';
			$user_data[1]->address_type_name = 'Billing';
			
		}
		
		
		$cart['user_data'] = $user_data;

		$user = JFactory::getUser();
		if($user->guest){
			$userId = 0;
		}else{
			$userId = $user->id;
		}

		// no error checking on set because no error checking in set function which is pretty bad
		Pago::get_instance( 'cookie' )->set( 'cart_'.$userId, $cart );
		// Update tax data based on selected Shipping/Billing Address
		Pago::get_instance( 'price' )->calculateTax( $cart );
		Pago::get_instance( 'price' )->calc_cart( $cart );
		// no error checking on set because no error checking in set function which is pretty bad
		Pago::get_instance( 'cookie' )->set( 'cart_'.$userId, $cart );
		Pago::get_instance( 'cart' )->set( $cart );
		// should not have to do this.... set should actually return false if an error happened
		$cart = Pago::get_instance( 'cookie' )->get( 'cart_'.$userId );
		if ( !isset( $cart['user_data'] ) || empty( $cart['user_data'] ) ){
			return false;
		}

		if ( !$guest && isset( $new_address ) && $save_address == 'yes' ) {
			$user_model->add_address( $user_data );
		}
		ob_clean();
		return true;
	}

	public function set_shipping( $carrier_option )
	{
		$shipper = array();
		$config = Pago::get_instance('config')->get();
		$cart = Pago::get_instance('cart')->get();

		if ($config->get('checkout.shipping_type'))
		{
			$shippingvalue = 0;

			foreach ($carrier_option as $product => $carrier_option)
			{
				if ($carrier_option)
				{
					$carrier_option = explode('|', $carrier_option);
					$shipper = array(
						'code' => $carrier_option[1],
						'value' => $carrier_option[3],
						'name' => $carrier_option[0] . ' - ' . $carrier_option[2],
					);
				}
				elseif ($config->get('checkout.skip_shipping'))
				{
					$shipper = array(
						'code' => '1',
						'value' => '0.00',
						'name' => 'none'
					);
				}
				else
				{
					JError::raiseWarning(500, JText::_('PAGO_CARRIER_OPTION_NOT_FOUND'));

					return false;
				}

				if (empty($shipper))
				{
					JError::raiseWarning(500, JText::_('PAGO_SHIPPER_ERROR'));

					return false;
				}

				$cart['items'][$product]->carrier = $shipper;
				$shippingvalue += $shipper['value'];
			}

			$cart['shipping'] = $shippingvalue;
			$cart['shipping_excluding_tax'] = $shippingvalue;
		}
		else
		{
			if ( $carrier_option )
			{
				$carrier_option = explode('|', $carrier_option);

				$shipper = array(
					'code' => $carrier_option[1],
					'value' => $carrier_option[3],
					'name' => $carrier_option[0] . ' - ' . $carrier_option[2],
				);
			}
			elseif ($config->get('checkout.skip_shipping'))
			{
				$shipper = array(
					'code' => '1',
					'value' => '0.00',
					'name' => 'none'
				);
			}
			else
			{
				JError::raiseWarning(500, JText::_('PAGO_CARRIER_OPTION_NOT_FOUND'));

				return false;
			}

			if (empty($shipper))
			{
				JError::raiseWarning(500, JText::_('PAGO_SHIPPER_ERROR'));

				return false;
			}

			$cart['carrier'] = $shipper;
			$cart['shipping'] = $shipper['value'];
			$cart['shipping_excluding_tax'] = $shipper['value'];
		}

		
		$user = JFactory::getUser();
		if($user->guest){
			$userId = 0;
		}else{
			$userId = $user->id;
		}

		Pago::get_instance('cookie')->set('cart_'.$userId, $cart);

		return true;
	}

	public function deplete_inventory( $order_id )
	{
		$db = JFactory::getDBO();

		$query = 'SELECT item_id, qty FROM #__pago_orders_items WHERE order_id = '
			. (int) $order_id;

		$db->setQuery( $query );

		$items = $db->loadAssocList();

		foreach ( $items as $item ) {
			$update_queries[] = 'UPDATE #__pago_items SET qty = qty-' . (int)$item['qty']
				. ' WHERE id = ' . (int)$item['item_id'];
		}

		foreach( $update_queries as $update ) {
			$db->setQuery($update);
			$db->query();
		}
	}

	public function get_order_receipt_template( )
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__pago_view_templates WHERE pgtemplate_parent_section ="order" AND pgtemplate_type="order_receipt" ';
		$db->setQuery($query);
		$items = $db->loadObject();

		return $items;
	}
}
