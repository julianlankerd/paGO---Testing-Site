<?php defined('_JEXEC') or die( 'Restricted access' );
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!

include( JPATH_ADMINISTRATOR . '/components/com_pago/helpers/ShippingCalculator.php');
jimport( 'joomla.application.component.controller' );

class PagoControllerAddorder extends PagoController
{
	private $_view = 'Addorder';

	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );

		$jinput = JFactory::getApplication()->input;

		$this->redirect_to = 'index.php?' . http_build_query( array(
			'option'=> $jinput->get('option'),
			'view'=> $jinput->get('option')
		));
		$this->rel = json_decode( str_replace( "'", '"', $jinput->get('rel') ) );
	}

	function saveOrderUser()
	{
		$jinput = JFactory::getApplication()->input;

		$userId = $jinput->get('filter_user', '0', 'int');
		$billing_address = $jinput->get('address_billing', '', 'array');
		$shipping_address = $jinput->get('address_shipping', '', 'array');
		$address_mailing_same_as_billing = $jinput->get('address_mailing_same_as_billing', '', 'int');

		if($address_mailing_same_as_billing)
		{
			$shipping_address = $billing_address;
			$shipping_address['address_type'] = 's';
			$shipping_address['address_type_name'] = 'shipping';
		}


		if (!$userId)
		{
			// Store User in Joomla
			$user_id = Pago::get_instance('users')->saveJoomlaUser($billing_address);

			// Store User in Pago users database
			$billing_address['user_id'] = $user_id;
			$shipping_address['user_id'] = $user_id;

			$billingAddressId = Pago::get_instance('users')->saveUserAddress('b', $billing_address);

			if ($billingAddressId)
			{
				$shippingAddressId = Pago::get_instance('users')->saveUserAddress('s', $shipping_address);
			}
		}
		else
		{
			// check if user is pago user or not
			$userInfor = Pago::get_instance('users')->get_user_addresses($userid);
			if(count($userInfor) > 0)
			{
				$billingAddress = $jinput->get('address', '', 'array');
				$billingAddressId = $billingAddress['b']['id'];
				$shippingAddressId = $billingAddress['m']['id'];

				if ($address_mailing_same_as_billing)
				{
					$shippingAddressId = $billingAddressId;
				}
			}
			else
			{
				$userId = $userId;
				$billingAddress = $jinput->get('address', '', 'array');
				$billingAddressId = $billingAddress['b']['id'];
				$shippingAddressId = $billingAddress['m']['id'];

				if ($address_mailing_same_as_billing)
				{
					$shippingAddressId = $billingAddressId;
				}
			}
		}
		$this->setRedirect('index.php?option=com_pago&view=addorder&user_id=' . $userId . '&address_id=' . $billingAddressId . '&saddress_id=' . $shippingAddressId . '#tabs-2');
	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}


	function save()
	{
		$model = JModelLegacy::getInstance('Addorder','PagoModel');
		$config = Pago::get_instance('config')->get('global');

		$jinput = JFactory::getApplication()->input;
		$data = JFactory::getApplication()->input->getArray($_POST);
	

		$userId = $jinput->get('filter_user', '0', 'int');
		$addressid = $jinput->get('address_id', '0', 'INT');
		$saddressid = $jinput->get('saddress_id', '0', 'INT');
		$total = $jinput->get('pg_addorder_order_total', '0', 'float');
		$subtotal = $jinput->get('pg_addorder_order_subtotal', '0', 'float');
		$tax = $jinput->get('pg_addorder_order_tax', '0', 'float');
		$shipping = $jinput->get('pg_addorder_order_shipping', '0', 'float');
		$jinput->get('pg_addorder_order_shipping_name', '0', 'string');
		$payment_gateway = $jinput->get('payment_option', '0', 'string');

		// Get Shipping Data
		if ($config->get('checkout.shipping_type'))
		{
			$ship_method_id = "";
		}
		else
		{
			$ship_method_id = $jinput->get('carrier_option', '0', 'string');
		}

		$saved_addresses = Pago::get_instance('users')->get_user_addresses($userId);
		$user_info  = array();

		foreach ( $saved_addresses as $user_address )
		{
			if ( $user_address->id == $addressid )
			{
				$user_info['billing'] = (array) $user_address;
				$user_info['billing']['address_type'] = 'b';
				$user_info['billing']['address_type_name'] = 'billing';
				break;
			}
		}

		foreach ( $saved_addresses as $user_address )
		{
			if ( $user_address->id == $saddressid )
			{
				$user_info['shipping'] = (array) $user_address;
				$user_info['shipping']['address_type'] = 's';
				$user_info['shipping']['address_type_name'] = 'shipping';
				break;
			}
		}

		$orderData = array(
				'user_id' => $userId,
				'vendor_id' => false,
				'order_number' => false,
				'user_info_id' => false,
				'payment_gateway' => $payment_gateway,
				'order_total' => $total,
				'order_subtotal' => $subtotal,
				'order_tax' => $tax,
				'order_tax_details' => false,
				'order_shipping' => $shipping,
				'order_shipping_tax' => false,
				'coupon_discount' => 0,
				'coupon_code' => "",
				'order_discount' => false,
				'order_currency' => CURRENCY_CODE,
				'order_status' => false,
				'cdate' => false,
				'mdate' => false,
				'ship_method_id' => $ship_method_id,
				'customer_note' => false,
				'ip_address' => $_SERVER["REMOTE_ADDR"]
			);

		$order_id = $model->store($orderData);

		if ($order_id)
		{
			// Stroe Order Items
			$order_item = $this->getOrderItemData($data);

			if (is_array($data['carrier_option']) && $config->get('checkout.shipping_type'))
			{
				foreach ($data['carrier_option'] as $item => $shipping)
				{
					foreach ($order_item as $orderItem)
					{
						if ($orderItem->item_id == $item)
						{
							$orderItem->ship_method_id = $shipping;
						}
					}
				}
			}

			$model->storeOrderItems($order_id, $order_item);

			// Store Order addresses
			$model->storeOrderAdresses($order_id, $user_info);

			// Do payment
			// Get Payment Data
			$cc_expirationDateMonth = $jinput->get('cc_expirationDateMonth', '0', '');
			$cc_expirationDateYear = $jinput->get('cc_expirationDateYear', '0', '');
			$cc_cardNumber = $jinput->get('cc_cardNumber', '0', '');
			$cc_cv2code = $jinput->get('cc_cv2code', '0', '');

			$cc_expirationDate = '01-' . $jinput->get('cc_expirationDateMonth') . '-' . $jinput->get('cc_expirationDateYear');
			$cc = array(
					'cardNumber' => $cc_cardNumber,
					'expirationDate' => date('Y-m', strtotime($cc_expirationDate)),
					'cv2' => $cc_cv2code
					);
			$order 	= Pago::get_instance('orders')->get($order_id);
			$order['PaymentInformation'] = $cc;
			$successMessage = $model->DoOffilineOrderPayment($order);

			// Update Stock of items sold
			//$checkout_model = JModel::getInstance('checkout', 'PagoModel');
			//$checkout_model->deplete_inventory($order_id);
		}

		$this->setRedirect('index.php?option=com_pago&view=ordersi', $successMessage);

	}

	function getOrderItemData($post)
	{
		$orderItem = array();
		$i = 0;

		foreach ($post as $key => $value)
		{
			if (!strcmp("pg_addorder_item_id", substr($key, 0, 19)) && strlen($key) < 23)
			{
				$orderItem[$i]->item_id = $value;
			}

			if (!strcmp("pg_addorder_item_variation_id", substr($key, 0, 29)) && strlen($key) < 33)
			{
				$orderItem[$i]->varation_id = $value;
			}

			if (!strcmp("pg_addorder_item_price_without_tax", substr($key, 0, 34)))
			{
				$orderItem[$i]->item_price_without_tax = $value;
			}

			if (!strcmp("pg_addorder_item_tax", substr($key, 0, 20)) && strlen($key) < 23)
			{
				$orderItem[$i]->item_tax = $value;
			}

			if (!strcmp("pg_addorder_item_with_tax", substr($key, 0, 26)))
			{
				$orderItem[$i]->item_price_with_tax = $value;
			}

			if (!strcmp("pg_addorder_item_shipping_tax", substr($key, 0, 29)))
			{
				$orderItem[$i]->item_shipping_tax = $value;
			}

			if (!strcmp("pg_addorder_item_qty", substr($key, 0, 20)))
			{
				$orderItem[$i]->quantity = $value;
				$i++;
			}
		}

		return $orderItem;
	}

	function cancel()
	{
		$this->setRedirect('index.php?option=com_pago&view=ordersi', JText::_('PAGO_ADDORDER_ORDER_CANCELED'));
	}


	function getUserAddress()
	{
		ob_clean();
		$jinput = JFactory::getApplication()->input;
		$userid = $jinput->get('userid', '0', 'INT');
		$addressid = $jinput->get('addressId', '0', 'INT');
		$saddressid = $jinput->get('saddressId', '0', 'INT');

		if ($userid)
		{
			$baddress = "";

			if ($addressid)
			{
				$saved_addresses = Pago::get_instance('users')->get_user_addresses($userid);

				foreach ( $saved_addresses as $user_address )
				{
					if ( $user_address->id == $addressid )
					{
						$address = (array) $user_address;
						unset($address['id']);
						unset($address['user_id']);
						unset($address['cdate']);
						unset($address['mdate']);
						unset($address['perms']);
						break;
					}
				}

				$baddress = implode(":", $address);
			}

			$saddress = "";

			if ($saddressid)
			{
				if ($addressid == $saddressid)
				{
					$saddress = $baddress;
				}
				else
				{
					$saved_addresses = Pago::get_instance('users')->get_user_addresses($userid);
					foreach ( $saved_addresses as $user_address )
					{
						if ( $user_address->id == $saddressid )
						{
							$address1 = (array) $user_address;
							unset($address1['id']);
							unset($address1['user_id']);
							unset($address1['cdate']);
							unset($address1['mdate']);
							unset($address1['perms']);
							break;
						}
					}

					$saddress = implode(":", $address1);
				}
			}

			$addresses = $baddress . '####' . $saddress;
		}
		else
		{
			$addresses = "";
		}

		echo $addresses;
		exit;
	}

	function getAddressInformation()
	{
		ob_clean();
		$db		= Jfactory::getDBO();
		$jinput = JFactory::getApplication()->input;
		$userid = $jinput->get('userid', '0', 'INT');
		$addressid = $jinput->get('addressId', '0', 'INT');
		$saddressid = $jinput->get('saddressId', '0', 'INT');

		if ($userid)
		{
			$userInfor = Pago::get_instance('users')->get_user_addresses($userid);
			$userInforB = Pago::get_instance('users')->get_user_billing_addresses($userid);
			$userInforS = Pago::get_instance('users')->get_user_shipping_addresses($userid);
			if(count($userInfor) > 0 )
			{
				ob_clean();
				$bresponse = "";
				$options = "";
				foreach( $userInfor as $user_address ) :
					$selected = "";

					if ($user_address->id == $addressid)
					{
							$selected = "selected";
					}

					$options .= '<option value="'.$user_address->id.'" '.$selected.'>'.$user_address->address_1.'/'.$user_address->city.'/'.$user_address->state.'/'.$user_address->country.'</option>';
				endforeach;
				$bresponse .='<div class="pg-checkout-billing-address">
	                			<fieldset class="pg-fieldset pg-billing-address-fieldset">
	                    			<legend class="pg-legend"> 
	                    				<label for="pg-billing-address-'.$user_address->id.'" class="pg-label">
	                            			'.JText::_('PAGO_ADDORDER_SELECT_BILLING_ADDRESS').'
	                        			</label>
	                    			</legend>';
				$bresponse .= ' <ul class="pg-billing-address">
								<li class="pg-billing-address">
									<select name="address[b][id]" class="pg-radiobutton required" id="pg-billing_address">
										<option>--Add New User --</option>
										'.$options.'
									</select>
								</li>
							</ul>';
				$bresponse .= '
							</fieldset>
	            			</div>';

				$mresponse = "";
				$moptions = "";
				foreach( $userInfor as $user_address ) :
					$selected = "";
					if ($user_address->id == $saddressid)
					{
							$selected = "selected";
					}

					$moptions .= '<option value="'.$user_address->id.'" '.$selected.'>'.$user_address->address_1.'/'.$user_address->city.'/'.$user_address->state.'/'.$user_address->country.'</option>';
				endforeach;
				$mresponse .='<div class="pg-checkout-shipping-address">
	                			<fieldset class="pg-fieldset pg-shipping-address-fieldset">
	                    			<legend class="pg-legend"> 
	                    				<label for="pg-shipping-address-'.$user_address->id.'" class="pg-label">
	                            			'.JText::_('PAGO_ADDORDER_SELECT_SHIPPING_ADDRESS').'
	                        			</label>
	                    			</legend>';
				$mresponse .= ' <ul class="pg-shipping-address">
								<li class="pg-shipping-address">
									<select name="address[m][id]" class="pg-radiobutton required" id="pg-shipping_address">
										<option>--Add New User --</option>
										'.$moptions.'
									</select>
								</li>
							</ul>';
				$mresponse .= '
							</fieldset>
	            			</div>';
	   			$response = "1####".$bresponse."####".$mresponse;
   			}
   			else
			{
				$sql= "SELECT name,email from #__users where id=".$userid;
				$db->setQuery( $sql );
				$userData = $db->loadAssocList();
				$name = $userData[0]['name']."_".$userData[0]['email'];//exit;
				$response = "0####This user is not a pago user, so please add address and click on next button####".$name;
			}
   		}
   		else
   		{
   			$response = "";
   		}
       	echo  $response; exit;

	}

	function getItemDetail()
	{
		ob_clean();
		$jinput = JFactory::getApplication()->input;
		$item_id = $jinput->get('item_id', '0', 'INT');
		$user_id = $jinput->get('user_id', '0', 'INT');
		$addressid = $jinput->get('addressId', '0', 'INT');
		$saddressid = $jinput->get('saddressId', '0', 'INT');
		$varId = $jinput->get('varId', '0', 'INT');
		$qty =$jinput->get('qty', '1', 'INT');
		
		$item = PagoHelper::get_product($item_id);
		$name = $item->name;
		$price = $item->price;

		if($varId != 0){
			$model = JModelLegacy::getInstance('Attribute','PagoModel');
			$variation = $model->get_product_varations_by_id($varId);
			$name = $variation->name;
			$price = $variation->price;
		}
		
		
		$userData = Pago::get_instance('price')->getUserAddressInfoForTax($user_id, $addressid, $saddressid);
		$itemTax = Pago::get_instance('price')->getProductTax($item, $userData);

		$itemTaxAmount = $itemTax ['item_tax'];
		$resultItem = array();
		$resultItem [] = $name;
		$resultItem [] = $price;
		$resultItem [] = $itemTaxAmount;
		$resultItem [] = $price + $itemTaxAmount;
		$resultItem [] = $qty;
		$resultItem [] = $itemTax ['item_tax_rate'];
		$resultItem [] = $itemTax['apply_tax_on_shipping'];
		$resultItem [] = $item->free_shipping;

		$resultItem = implode("##", $resultItem);
		echo $resultItem; exit;

	}

	function getItemDetails(){
		$model = $this->getModel("Addorder");

		$jinput = JFactory::getApplication()->input;
		
		$item_id = $jinput->get('item_id', '0', 'INT');
		$order_id = $jinput->get('order_id', '0', 'INT');
		$user_id = $jinput->get('user_id', '0', 'INT');
		$addressid = $jinput->get('addressId', '0', 'INT');
		$saddressid = $jinput->get('saddressId', '0', 'INT');
		
	
		//$defaultImage=$model->getDefaultImage($item_id);
		
		//$item = PagoHelper::get_product($item_id);
		//$userData = Pago::get_instance('price')->getUserAddressInfoForTax($user_id, $addressid, $saddressid, $order_id);
		//$itemTax = Pago::get_instance('price')->getProductTax($item, $userData);

		//$itemTaxAmount = $itemTax ['item_tax'];
		JFactory::getApplication()->input->set( 'layout', 'order_item_view' );

		JFactory::getApplication()->input->set('Itemid', $item_id);
		JFactory::getApplication()->input->set('user_id', $user_id);
		JFactory::getApplication()->input->set('addressid', $addressid);
		JFactory::getApplication()->input->set('saddressid', $saddressid);
		//JFactory::getApplication()->input->set('defaultImage', $defaultImage);

		parent::display();
		exit;
	}

	public function considerPrice(){

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$selected_attributes = JFactory::getApplication()->input->get( 'selected_attributes', '', '' );
		$itemQty = JFactory::getApplication()->input->get( 'itemQty', '', '' );
		$changePhotoOptionId = JFactory::getApplication()->input->get( 'changePhotoOptionId', '', '' );


		$return = $model->considerPrice($itemId,$selected_attributes,$itemQty);
		$return['price'] = Pago::get_instance( 'price' )->format( $return['price'] );		

		$return = json_encode($return);
		echo $return;
		exit();
	}
	public function checkVarationExist(){
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$selected_attributes = JFactory::getApplication()->input->get( 'selected_attributes', '', '' );

		$varation = $model->get_varation_if_exist($selected_attributes,$itemId);
		if($varation){
			$return['success'] = "success";
			$return['varationId'] = $varation->id;
		}else{
			$return['success'] = "fail";
		}
		$return = json_encode($return);
		echo $return;
		exit();
	}
	public function getVaration(){
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		$varationId = JFactory::getApplication()->input->get( 'varationId', '', '' );
		$varation = $model->get_product_varations_by_id($varationId, true);
		$return = false;
		if($varation && $varation->attributes){
			$attributes = array();
			foreach ($varation->attributes as $attribute) {
				$attributes[$attribute->attribute->id] = $attribute->option->id; 	
			}
			if(!empty($attributes)){
				$return = json_encode($attributes);
			}
		}
		echo $return;
		exit();
	}
	public function getDefaultVaration(){
		Pago::load_helpers( array( 'attributes' ) );

		$itemId = JFactory::getApplication()->input->get( 'itemId', '', '' );
		$itemId = (int)$itemId;

		$defaultVarationId = PagoAttributesHelper::get_default_varation($itemId);
		if($defaultVarationId){
			$return['status'] = 'success';
			$return['defaultVarationId'] = $defaultVarationId->id;
		}else{
			$return['status'] = 'fail';
		}
		$return = json_encode($return);
		echo $return;
		exit();
	}
	public function getVideo(){

		$videoId = JFactory::getApplication()->input->get( 'videoId' );

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('File','PagoModel');

		$video = $model->getFileById($videoId);

		if($video){
			$file = JPATH_SITE.'/administrator/components/com_pago/helpers/video_sources.php';
			jimport('joomla.filesystem.file');
			if (JFile::exists($file))
			{
				require $file;
			}
			
			$videoEmbed = $tagReplace[$video->provider];
			$videoEmbed = str_replace("{SOURCE}", $video->video_key, $videoEmbed);
			$return['videoEmbed'] = $videoEmbed;
			$return = json_encode($return);
			echo $return;
			exit();
		}
	}
	function displayShippingOptions()
	{
		ob_clean();
		$ShippingCalculator = new ShippingCalculator;
		$items = json_decode(JFactory::getApplication()->input->get('items', '', 'string'));
		$items = $items->itemsArray;
		$saddress_id = JFactory::getApplication()->input->get('saddress_id', '', 'int');
		$user_id = JFactory::getApplication()->input->get('user_id', '', 'int');
		$shippingOptions = $ShippingCalculator->displayShippingOptions($items, $user_id, $saddress_id);
		echo $shippingOptions;exit;
	}

	function checkCreditCard()
	{
		ob_clean();
		jimport('joomla.html.parameter');
		$jinput = JFactory::getApplication()->input;
		$payment = $jinput->get('payment');
	  	$plugin = JPluginHelper::getPlugin('pago_gateway', $payment);
	 	$pluginParams = new JRegistry($plugin->params);
	 	echo $pluginParams->get('creditcard', '0');
	 	exit;
	}
}
