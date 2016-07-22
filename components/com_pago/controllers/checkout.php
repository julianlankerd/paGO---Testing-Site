<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }
jimport( 'pago.order' );

class PagoControllerCheckout extends PagoController
{
	/**
	 * @override execute to become the entry point for the checkout state machine
	 */
	public function execute( $task )
	{
		$config = Pago::get_instance('config')->get('global');

		$this->task = $task;
		$this->prev_task = Pago::get_instance( 'cookie' )->get( 'checkout_previous_step', null );
		$save_address = Pago::get_instance( 'cookie' )->get( 'address_saved', 0 );
		$shipping_set = Pago::get_instance( 'cookie' )->get( 'shipping_set', 0 );
		$processed = Pago::get_instance( 'cookie' )->get( 'processed', 0 );
		
		
		$version = new JVersion();
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('var $JOOMLA_VERSION = "'.$version->RELEASE.'"');
		
		
		/*
		 * TODO: implement order class and save it with the cookie instance
		 * this way we have a sane type hintable object
		 */

		$user = JFactory::getUser();
		
		if($user->guest)
			JFactory::getApplication()->input->set( 'guest', 1 );
			
		$guest = JFactory::getApplication()->input->getInt( 'guest', 0 );
		
		$complete = JFactory::getApplication()->input->getInt( 'complete', 0 );
		
		if($complete) $guest=1;
		
		// see if we have a valid user. can be guest or actual user
		if ( ($guest && $user->guest) && !$config->get('checkout.force_checkout_register', 1) ) {
			$valid_user = true;
		}else if ( !$guest && $user->id ) {
			$valid_user = true;
		} else {
			$valid_user = false;
		}
		if($task == 'ipn'){
			$this->ipn();
			return;
		}

		if($task == 'get_address_form'){
			$this->get_address_form();
			return;
		}
		if($task == 'get_register_address_form'){
			$this->get_register_address_form();
			return;
		}

		if($task == 'set_shipping_methods'){
			$this->set_shipping_methods();
			return;
		}

		if ( $this->task == 'checkCreditCard' && $valid_user && $shipping_set) {
			// we have filled out billing info and need to set it
			$this->checkCreditCard();
			return;
		}

		if ( $this->task == 'process' && $valid_user && $shipping_set) {
			// we have filled out billing info and need to set it
			$this->process();
			return;
		}

		if ( $this->task == 'complete' && $valid_user && $shipping_set && $processed) {
			// we have filled out billing info and need to set it
			$this->complete();
			return;
		}

		if ( $this->task == 'calc_tax') {
			$this->calc_tax();
			return;
		}

		if ( $this->task == 'shipping_formatt') {
			$this->shipping_formatt();
			return;
		}

		$doTask = null;

		// TODO look into making sure url is always set correct as a post can cause you to start all
		//	 over if you refresh because the url doesn't have the correct params

		/* Start finite state machine
		 */

		if ( $this->task == 'display' && !$valid_user ) {
			// we haven't logged in or started checkout as guest
			$doTask = $this->task;
		}
		if ( $this->task == 'display' && $valid_user ) {
			// we are logged in and starting checkout go straight to addresses
			$doTask = 'address';
		}
		if ( $this->task == 'address' && $valid_user ) {
			// we are being sent to the address task to begin with and a valid user
			$doTask = $this->task;
		}
		if ( $this->task == 'address' && $this->prev_task == 'display' && $valid_user ) {
			// either logged in or continued as guest from display
			$doTask = $this->task;
		}
		if ( $this->task == 'set_address' && $this->prev_task == 'address' && $valid_user ) {
			// save addresses
			$doTask = $this->task;
		}
		if ( $this->task == 'address'
				&& $this->prev_task == 'set_address'
				&& $valid_user
				&& !$save_address ) {
			// entered address didn't validate or save
			$doTask = $this->task;
		}
		if ( $this->task == 'shipping'
				&& $this->prev_task == 'shipping'
				&& $valid_user
				&& $save_address ) {
			// more then likely a page refresh
			$doTask = $this->task;
		}

		if ( $this->task == 'shipping'
				&& $this->prev_task == 'set_address'
				&& $valid_user
				&& $save_address ) {
			// entered or selected address info now go to shipping
			$doTask = $this->task;
		}
		if ( $this->task == 'set_shipping' && $this->prev_task == 'shipping' && $valid_user ) {
			// user selected a shipping option now try to set it
			$doTask = $this->task;
		}
		if ( $this->task == 'shipping'
				&& $this->prev_task == 'set_shipping'
				&& $valid_user
				&& !$shipping_set ) {
			// shipping option not selected or error for some reason
			$doTask = $this->task;
		}

		if ( $this->task == 'shipping'
				&& $this->prev_task == 'set_address'
				&& $config->get('checkout.skip_shipping')
				&& $save_address
				&& $valid_user ) {
			// skip_shipping is set in config so must mean no shipping is needed
			$doTask = 'set_shipping';
		}
		if ( $this->task == 'billing'
				&& $this->prev_task == 'set_shipping'
				&& $valid_user
				&& $shipping_set ) {
			// selected shipping now go to billing
			$doTask = $this->task;
		}

		if ( $this->task == 'checkCreditCard' && $valid_user && $shipping_set )
		{
			// Show/hide creditcard form
			$doTask = $this->task;

		}

		if ( $this->task == 'billing'
				&& $this->prev_task == 'billing'
				&& $valid_user
				&& $shipping_set ) {
			// more then likely a page refresh
			$doTask = $this->task;
		}
		if ( $this->task == 'process' && $this->prev_task == 'billing' && $valid_user ) {
			// we have filled out billing info and need to set it
			$doTask = $this->task;
		}

		if ( $this->task == 'process' && $this->prev_task == 'checkCreditCard' && $valid_user )
		{
			// Save addresses
			$doTask = $this->task;
		}

		if ( $this->task == 'process' && $this->prev_task == 'terms' && $valid_user )
		{
			// Save addresses
			$doTask = $this->task;
		}
		if ( $this->task == 'set_address' && $this->prev_task == 'display' && $valid_user )
		{
			// Save addresses
			$doTask = $this->task;
		}

		if ( $this->task == 'set_address' && $this->prev_task == 'set_address' && $valid_user ) {
			// save addresses
			$doTask = $this->task;
		}

		if ( $this->task == 'set_address' && $this->prev_task == 'billing' && $valid_user ) {
			// save addresses
			$doTask = $this->task;
		}

		if ( $this->task == 'terms' && ($this->prev_task == 'billing' || $this->prev_task == 'checkCreditCard') && $valid_user ) {
			// save addresses
			$doTask = $this->task;
		}

		if ( $this->task == 'billing'
				&& $this->prev_task == 'set_address'
				&& $valid_user
				&& !$save_address ) {
			// tried to set billing and something happened
			$doTask = $this->task;
		}
		if ( $this->task == 'express_checkout' && !$valid_user ) {
			$doTask = 'display';
		}
		if ( $this->task == 'express_checkout' && $valid_user ) {
			$doTask = 'process';
		}

		if ( $this->task == 'process'
				&& $this->prev_task == 'set_address'
				&& $valid_user
				&& $save_address ) {
			// we have set a billing address now lets process the cc
			$doTask = $this->task;
		}
		if ( $this->task == 'billing'
				&& $this->prev_task == 'process'
				&& $valid_user
				&& !$processed ){
			// somethign happened while we processed the payment go back to billing
			$doTask = $this->task;
		}
		if ( $this->task == 'complete'
				&& $this->prev_task == 'process'
				&& $valid_user
				&& $processed ) {
			// completed billing
			$doTask = $this->task;
		}

		if ( $this->task == 'complete'
				&& $this->prev_task == 'ipn'
				&& $valid_user  && $processed) {
			// completed billing
			$doTask = $this->task;
		}

		if ( $this->task == 'ipn'
				&& $this->prev_task == 'process'
				&& $valid_user) {
			// completed billing
			$doTask = $this->task;
		}

		if ( $this->task == 'printorder' ) {
			// request to print order
			$doTask = $this->task;
		}

		// sets default
		if ( $doTask === null && !$valid_user ) {
			$doTask = 'display';
		}
		if ( $doTask === null && $valid_user ) {
			$doTask = 'address';
		}
		
		//with the new checkout we will have a clear query var
		//which should force successful checkout to receipt
		if(JFactory::getApplication()->input->getInt( 'clear', 0 )){
			$doTask = 'complete';
		}
		
		// End state machine
		// we make sure the task is allowed via acl
		
		if ( $this->authorise( $doTask ) ) {
			Pago::get_instance( 'cookie' )->set( 'checkout_previous_step', $doTask );
			/* TODO implement passing the order class to the different states
			$retval = $this->$doTask( $order );
			Pago::get_instance( 'cookie' )->set( 'order', $order );
			*/
			
			$retval = $this->$doTask();
			
			return $retval;
		} else {
			return JError::raiseError( 403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN') );
		}
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function display( $cachable = false, $urlparams = false )
	{
		$view = $this->getView( 'checkout', 'html' );
		$view->setLayout( 'default' );
		$view->display();
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function terms( $cachable = false, $urlparams = false )
	{
		$view = $this->getView( 'checkout', 'html' );
		$view->setLayout( 'terms' );
		$view->display();
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function address()
	{
		$guest = JFactory::getApplication()->input->getInt( 'guest', 0 );
		$dispatcher = KDispatcher::getInstance();
		$user = JFactory::getUser();
		$view = $this->getView('checkout', 'html');
		$cart = Pago::get_instance( 'cart' )->get();
		if($cart)
		{
			Pago::get_instance( 'price' )->calculateTax($cart);
			Pago::get_instance( 'price' )->calc_cart($cart);
			//Pago::get_instance( 'cart' )->set( $cart );
			// calculate realtime discount
			Pago::get_instance( 'price' )->calculateDiscount($cart);
			Pago::get_instance( 'price' )->calc_cart($cart);
			Pago::get_instance( 'cart' )->set( $cart );
		}
		$cart = Pago::get_instance( 'cart' )->get();
		$saved_addresses = array();
		$addresses = array();
		if ( !$guest && !$user->guest ) {
			$user_model = JModelLegacy::getInstance( 'Customers', 'PagoModel' );
			$saved_addresses  = $user_model->get_user_shipping_addresses();
		}
		// get addresses incase it was saved
		if ( isset( $cart['user_data'] ) && !$guest && !$user->guest ) {
			$addresses = $cart['user_data'];
			// TODO implement standard class so i don't have to do this :(
			foreach( $addresses as $k => $address ) {
				$addresses[$k] = (object) $address;
			}
		}

		// last chance for using an express payment gateway if one is enabled
		JPluginHelper::importPlugin( 'pago_gateway' );
		$express_payment_options = array();
		$dispatcher->trigger(
			'express_payment_set_options',
			array( &$express_payment_options )
		);
		
		$view->assignRef( 'guest', $guest );
		$view->assignRef( 'express_payment_options', $express_payment_options );
		$view->assignRef( 'saved_addresses', $saved_addresses );
		$view->assignRef( 'addresses', $addresses );
		$view->setLayout('address');
		$view->display();
		return $this;
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function set_address()
	{
		
		$model = $this->getModel('checkout');
		$jinput = JFactory::getApplication()->input;
		$call = $jinput->get('call', 'post', 'string');
		$guest = $jinput->get('guest', '0', 'int');
		$same_as = $jinput->get('sameasshipping', 'no', 'string');
	 	$save_address = $jinput->get('save_address', 'no', 'string');
		$data = $jinput->get('address', '', 'array');
		$nextTask =  $jinput->get('nextTask', '', 'string');

		if ( $model->set_address( $data, $guest, $same_as, $save_address ) ) {

			Pago::get_instance( 'cookie' )->set( 'address_saved', 1 );
			ob_clean();
			$return['status'] = "success";
			if($nextTask == 'shipping' && $call == 'ajax')
			{
				$this->get_shipping_methods();
			}
		} else {
			Pago::get_instance( 'cookie' )->set( 'address_saved', 0 );
			ob_clean();
			$return['status'] = "error";
			// $task = $this->prev_task;
		}
		
		//here we remove the carrier / shipper if the user is
		//changing address
		$cart = Pago::get_instance('cart')->get();
		
		$cart['carrier'] = $shipper;
		$cart['shipping'] = $shipper['value'];
		$cart['shipping_excluding_tax'] = $shipper['value'];
		
		$user = JFactory::getUser();
		if($user->guest){
			$userId = 0;
		}else{
			$userId = $user->id;
		}

		Pago::get_instance('cookie')->set('cart_'.$userId, $cart);
		Pago::get_instance( 'price' )->calc_cart( $cart );
		Pago::get_instance( 'cart' )->set( $cart );
		
		if($call == 'ajax'){
			ob_clean();
			echo json_encode($return);
			exit();
		}elseif($call == 'api'){
			ob_clean();
			return $return;
		}else{
			$task = 'shipping';
			$this->execute( $task );
			return $this;
		}
	}

	public function get_register_address_form(){
		ob_clean();
		$prefix = JFactory::getApplication()->input->get( 'prefix', 's' );
		$preset_number = JFactory::getApplication()->input->get( 'preset_number', 1 );
		$guest = JFactory::getApplication()->input->get( 'guest', 0 );
		$user =  JFactory::getUser();

		$this->prefix = $prefix; 
		$this->preset_number = $preset_number;
		$this->guest = $guest;
		$saved_addresses = array();
		$addresses = array();
		if ( !$guest && !$user->guest )
		{

			$user_model = JModelLegacy::getInstance( 'Customers', 'PagoModel' );
			$saved_addresses  = $user_model->get_user_billing_addresses();
		}
		$this->saved_addresses = $saved_addresses;

		$add_address = PagoHelper::load_template( 'common', 'tmpl_billing_address' );
		$return = array();

		ob_start();
			require $add_address;
			$return['formHtml'] = ob_get_contents();
		ob_end_clean();

		$return['status'] = "success";
		ob_clean();
		echo json_encode($return);
		exit();
	}

	public function get_address_form(){
		$prefix = JFactory::getApplication()->input->get( 'prefix', 's' );
		$preset_number = JFactory::getApplication()->input->get( 'preset_number', 1 );

		$this->prefix = $prefix;
		$this->preset_number = $preset_number;
		$add_address = PagoHelper::load_template( 'common', 'tmpl_add_address' );
		$return = array();

		ob_start();
			require $add_address;
			$return['formHtml'] = ob_get_contents();
		ob_end_clean();

		$return['status'] = "success";
		ob_clean();
		echo json_encode($return);
		exit();
	}

	public function get_shipping_methods($array_output=false){
		ob_clean();
		
		$dispatcher = KDispatcher::getInstance();
		$config = Pago::get_instance('config')->get('global');
		$cart = Pago::get_instance('cart')->get();
		$guest = JFactory::getApplication()->input->getInt('guest', '0');

		// We will assume the first index is the shipping because that is how set_address works
		$shipping_address = (object) @$cart['user_data'][0];
		$billing_address = (object) @$cart['user_data'][1];
		// Shipping options trigger
		
		$shipping_options = array();

		if($config->get('checkout.skip_shipping'))
		{
			$this->set_shipping_methods(true, $guest);
			exit;
		}
		
		//further planning needs to be done to implement
		//item based shipping correctly
		/*if ($config->get('checkout.shipping_type'))
		{

				$cartItemcount = 0;

				foreach ($cart['items'] as $item)
				{
						// Shipping options trigger
						$shipping_option = array();
						$tempCart = $cart;
						unset($tempCart['items']);
						$tempCart['items'][$cartItemcount] = $item;

						JPluginHelper::importPlugin('pago_shippers');
						$dispatcher->trigger(
							'set_shipping_options',
							array( &$shipping_option, $tempCart, $shipping_address)
						);
						
						if(count($shipping_option) > 0)
						{
							$shipping_options[$cartItemcount] = $shipping_option;
						}
						
						$cartItemcount = $cartItemcount + 1;
				}
		}
		else
		{ */
			
		//check each item for free shipping
		//only free shipping if all items have 
		//free shipping
		$freeshipping = true;

		foreach ($cart['items'] as $item)
		{
			if(!$item->free_shipping)
				$freeshipping = false;
		}

		if($freeshipping){
			return [
				'FREE' => [
					[
						'code' => 0,
						'name' => 'Free Shipping',
						'value' => 0
					]
				]
			];
		}
		
		// Shipping options trigger
		JPluginHelper::importPlugin('pago_shippers');
		$dispatcher->trigger(
			'set_shipping_options',
			array( &$shipping_options, $cart, $shipping_address)
		);

		// If we have empty shipping options then get flat rate.
		foreach($shipping_options as $k=>$op)
			if(empty($op))
				unset($shipping_options[$k]);
				
		if (empty($shipping_options))
		{
			$dispatcher->trigger(
				'set_shipping_options_empty',
				array( &$shipping_options, $cart, $shipping_address )
			);
		}
		
		if($array_output){
			return $shipping_options;
		}
		
		if(count($shipping_options) <= 0)
		{
			$this->set_shipping_methods(true, $guest);
			exit;
		}
		$this->shipping_address = $shipping_address;
		$this->billing_address= $billing_address;
		$this->shipping_options=$shipping_options;
		$this->productBasedShipping=$config->get('checkout.shipping_type');
		$this->cart=$cart;

		$shipping_method = PagoHelper::load_template( 'checkout', 'shipping' );

		$return = array();

		ob_start();
			require $shipping_method;
			$return['formHtml'] = ob_get_contents();

		ob_end_clean();
		$return['shipping'] = "yes";
		$return['payment'] = "no";
		$return['status'] = "success";
		ob_clean();
		echo json_encode($return);
		exit();
	}

	public function set_shipping_methods($no_shipping = false, $guest = 0){
		ob_clean();
		$model = $this->getModel('checkout');
		$dispatcher = KDispatcher::getInstance();
		$data = JFactory::getApplication()->input->get('carrier_option','', 'RAW');
		$reqguest = JFactory::getApplication()->input->get('guest');
		if($reqguest == "")
		{
			$guest = $guest;
		}
		else
		{
			$guest = $reqguest;
		}


		if($no_shipping)
		{
			Pago::get_instance( 'cookie' )->set( 'shipping_set', 1 );
			//$task = 'billing';
			$this->set_billing_form($guest);
		}
		else
		{
			if ( $model->set_shipping( $data ) )
			{
				Pago::get_instance( 'cookie' )->set( 'shipping_set', 1 );
				//$task = 'billing';
				$this->set_billing_form($guest);
			} else {
				Pago::get_instance( 'cookie' )->set( 'shipping_set', 0 );
				//$this->get_shipping_methods();
				$return['status'] = "error";
			}
		}
		$return = array();
		ob_clean();
		echo json_encode($return);
		exit();
	}

	public function set_billing_form($guest)
	{
		$dispatcher = KDispatcher::getInstance();
		$cart = Pago::get_instance( 'cart' )->get();
		$user_data = $cart['user_data'];

		// payment options trigger
		JPluginHelper::importPlugin( 'pago_gateway' );
		$payment_options = array();
		$dispatcher->trigger(
			'payment_set_options',
			array( &$payment_options, &$cart, &$user_data )
		);

		Pago::get_instance( 'price' )->calc_cart( $cart );

		Pago::get_instance( 'cart' )->set( $cart );

		$cart = Pago::get_instance( 'cart' )->get();

		$shipper = @$cart['carrier'];
		$user_data[0] = (object) $user_data[0];
		$user_data[1] = (object) $user_data[1];

		$this->billing_address = $user_data[1];
		$this->shipping_address = $user_data[0];
		$this->payment_options = $payment_options;
		$this->cart = $cart;
		$this->shipper = $shipper;
		$this->guest = $guest;
		
		Pago::get_instance( 'cookie' )->set( 'checkout_previous_step', 'billing' );

		$billing_method = PagoHelper::load_template( 'checkout', 'billing' );
		$return = array();

		ob_start();
			require $billing_method;
			$return['formHtml'] = ob_get_contents();
		ob_end_clean();
		$return['payment'] = "yes";
		$return['shipping'] = "no";
		$return['status'] = "success";
		ob_clean();
		echo json_encode($return);
		exit();
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function shipping()
	{
		$dispatcher = KDispatcher::getInstance();
		$config = Pago::get_instance('config')->get('global');
		$cart = Pago::get_instance('cart')->get();
		$guest = JFactory::getApplication()->input->getInt('guest', '0');

		// We will assume the first index is the shipping because that is how set_address works
		$shipping_address = (object) $cart['user_data'][0];
		$billing_address = (object) $cart['user_data'][1];
		// Shipping options trigger
		JPluginHelper::importPlugin('pago_shippers');
		$shipping_options = array();

		if ($config->get('checkout.shipping_type'))
		{
				$cartItemcount = 0;

				foreach ($cart['items'] as $item)
				{
						// Shipping options trigger
						$shipping_option = array();
						$tempCart = $cart;
						unset($tempCart['items']);
						$tempCart['items'][$cartItemcount] = $item;

						JPluginHelper::importPlugin('pago_shippers');
						$dispatcher->trigger(
							'set_shipping_options',
							array( &$shipping_option, $tempCart, $shipping_address)
						);

						$shipping_options[$cartItemcount] = $shipping_option;
						$cartItemcount = $cartItemcount + 1;
				}
		}
		else
		{
			// Shipping options trigger
			JPluginHelper::importPlugin('pago_shippers');
			$dispatcher->trigger(
				'set_shipping_options',
				array( &$shipping_options, $cart, $shipping_address)
			);

			// If we have empty shipping options then get flat rate.

			if (empty($shipping_options))
			{
				$dispatcher->trigger(
					'set_shipping_options_empty',
					array( &$shipping_options, $cart, $shipping_address )
				);
			}
		}

		$view = $this->getView('checkout', 'html');
		$view->setLayout('shipping');
		$view->assignRef('shipping_address', $shipping_address);
		$view->assignRef('billing_address', $billing_address);
		$view->assignRef('shipping_options', $shipping_options);
		$view->assignRef('productBasedShipping', $config->get('checkout.shipping_type'));
		$view->assignRef('cart', $cart);
		$view->display();

		return $this;
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function set_shipping()
	{
		$guest = JFactory::getApplication()->input->getInt( 'guest', 0 );
		$model = $this->getModel('checkout');
		$post = JFactory::getApplication()->input->getArray($_POST);
		$data = $post['carrier_option'];

		$skip_shipping = $post['skip_shipping'];
		if($skip_shipping)
		{
			Pago::get_instance( 'cookie' )->set( 'shipping_set', 1 );
			$task = 'billing';
		}
		else
		{
			if ( $model->set_shipping( $data ) ) 
			{
				Pago::get_instance( 'cookie' )->set( 'shipping_set', 1 );
				$task = 'billing';
			} 
			else 
			{
				Pago::get_instance( 'cookie' )->set( 'shipping_set', 0 );
				$task = 'shipping';
			}
		}

		$this->execute( $task );
		return $this;
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function billing($array_output=false)
	{
		$view = $this->getView('checkout', 'html');
		// show cart, shipping, payment
		$dispatcher = KDispatcher::getInstance();
		$cart = Pago::get_instance( 'cart' )->get();
		$user_data = (isset($cart['user_data'])) ? $cart['user_data'] : [];

		// payment options trigger
		JPluginHelper::importPlugin( 'pago_gateway' );
		$payment_options = array();
		$dispatcher->trigger(
			'payment_set_options',
			array( &$payment_options, &$cart, &$user_data )
		);
		
		//flag if payment option needs to capture cc details
		if($array_output){
			jimport('joomla.html.parameter');
			
			foreach($payment_options as $payment => $v){
				
				//check if new payment system
				if(strstr($payment, 'pago_')){
					$id = str_replace('pago_', '', $payment);
					$pluginParams = Pago::get_instance('params')->get('paygates.'.$id);
					$payment_options[$payment]['cc_form'] = $pluginParams->data->creditcard;
				} else {
					$plugin = JPluginHelper::getPlugin('pago_gateway', $payment);
					$pluginParams = new JRegistry($plugin->params);
					$payment_options[$payment]['cc_form'] = $pluginParams->get('creditcard', '0');
				}
			}
			
			return $payment_options;
		} 
		
		Pago::get_instance( 'price' )->calc_cart( $cart );
		Pago::get_instance( 'cart' )->set( $cart );

		$cart = Pago::get_instance( 'cart' )->get();
		$shipper = @$cart['carrier'];

		$view->assign( 'cc_options', true );

		$user_data[0] = (object) $user_data[0];
		$user_data[1] = (object) $user_data[1];
		$view->assignRef( 'billing_address', $user_data[1] );
		$view->assignRef( 'shipping_address', $user_data[0] );
		$view->assignRef( 'payment_options', $payment_options );
		$view->assignRef( 'cart', $cart );
		$view->assignRef( 'shipper', $shipper );
		$view->setLayout( 'billing' );
		$view->display();
		return $this;
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function process()
	{
		//added to pass proper output to Internal API
		$defer_redirect = JFactory::getApplication()->input->get('defer_redirect', 0);
		
		$session 	= JFactory::getSession();
		$gateway 	= JFactory::getApplication()->input->get( 'payment_option', 0 );
		$nopayment 	= JFactory::getApplication()->input->get( 'nopayment', 0 );
		$cc_expirationDate = '01-' . JFactory::getApplication()->input->get('cc_expirationDateMonth') . '-' . JFactory::getApplication()->input->get('cc_expirationDateYear');
		$cc = array(
				'cardNumber' => JFactory::getApplication()->input->get('cc_cardNumber'),
				'expirationDate' => date('Y-m', strtotime($cc_expirationDate)),
				'cv2' => JFactory::getApplication()->input->get('cc_cv2code')
				);
				
		//here is a check if a gateway has been specified
		//if not we assume the credit card form and Authnet
		//as the gateway
		//in the future will need someway to define what is
		//set as default CC form processor
		if( !$gateway ){
			$gateway = 'paypal_express';
			JFactory::getApplication()->input->get( 'payment_option', 'paypal_express' );
		}

		$user_id    = JFactory::getUser()->id;
		$session->set( 'payment_option', $gateway, 'pago_cart' );
		
		$order_id 	= $session->get( 'order_id', 0, 'pago_cart' );
		
		//for internal API
		if($defer_redirect) $order_id = false;
						
		$cart 		= Pago::get_instance( 'cart' )->get();
		$user_info['shipping'] = (object)@$cart['user_data'][0];
		$user_info['billing'] = (object)@$cart['user_data'][1];
		$shipper 	= @$cart['carrier'];

		$guest = JFactory::getApplication()->input->getInt( 'guest', 0 );
		if ( $guest && JFactory::getUser()->guest ) {
			$user_info['billing']->user_id = 0;
		}

		if($nopayment)
		{
			$gateway 	= "Free of Cost";
		}

		if ( !$order_id ) {
			
			$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');
			
			$currenciesModel = JModelLegacy::getInstance('currencies', 'PagoModel');
			$currentCurrencyId = Pago::get_instance( 'cookie' )->get( 'current_currency');
			
			if($currentCurrencyId){
				$currentCurrency = $currenciesModel->getCurrencyById($currentCurrencyId);
				$currentCurrency = $currentCurrency->code;
			} else {
				$currentCurrency = $currenciesModel->getDefault()->code;
			}
			
			$orderData = array();
			$orderData['user_id'] = $user_id;
			$orderData['vendor_id'] = false;
			$orderData['order_number'] = false;
			$orderData['user_info_id'] = false;
			$orderData['payment_gateway'] = JText::_('PAGO_PG_'.strtoupper($gateway));
			$orderData['order_total'] = $cart['total'];
			$orderData['order_subtotal'] = $cart['subtotal'];
			$orderData['order_tax'] = $cart['tax'];
			$orderData['order_tax_details'] = false;
			$orderData['order_shipping'] = $cart['shipping'];
			$orderData['order_shipping_tax'] = false;
			$orderData['coupon_discount'] = @$cart['coupon']['discounts'][0]['total'];
			$orderData['coupon_code'] = @$cart['coupon']['code'];
			$orderData['order_discount'] = $cart['discount'];
			$orderData['order_currency'] = $currentCurrency;
			$orderData['order_status'] = false;
			$orderData['cdate'] = false;
			$orderData['mdate'] = false;
			$orderData['ship_method_id'] = $shipper['name'];
			$orderData['customer_note'] = false;
			$orderData['ip_address'] =  $_SERVER["REMOTE_ADDR"];

			//here we store the order
			$order_model = JModelLegacy::getInstance( 'Orders','PagoModel' );

			$state = $order_model->getState();

			$state->set('user_info', $user_info );

			//return the new order id for later use
			$order_id = $order_model->store($orderData);

			$orders_items_model = JModelLegacy::getInstance( 'Orders_items','PagoModel' );

			$orders_items_model->store( $order_id, $cart );
			$session->set( 'order_id', $order_id, 'pago_cart' );
		}

		$order 	= Pago::get_instance('orders')->get($order_id);

		// TODO Add Items to this Order array

		$order['PaymentInformation'] = $cc;

		if(!$nopayment)
		{
			$paymentResult = array();
			// Event can be found here
			$dispatcher = KDispatcher::getInstance();
			JPluginHelper::importPlugin('pago_gateway');
			
			//check if new payment system
			if(strstr($gateway, 'pago_')){
				$dispatcher->trigger(
					'onGatewayPayment',
					array( &$paymentResult, &$gateway, &$order )
				);
			} else {
				$dispatcher->trigger(
					'onPayment',
					array( &$paymentResult, &$gateway, &$order )
				);
			}
			
			foreach ( $paymentResult as $pgateway => $result)
			{
				if ($pgateway == $gateway)
				{
					if( $result->order_status == "P" && $gateway != "banktransfer" && !$result->redirectUrl)
					{
						Pago::get_instance( 'cookie' )->set( 'processed', 0 );

						$session->set('payment_option', '', 'pago_cart');
						Pago::get_instance('orders')->onOrderFail($order, $result);
						
						//for internal API
						if($defer_redirect) return $result;
						
						JError::raiseWarning(500, $result->message);
						$this->execute( 'address' );

						return;
					}
					Pago::get_instance('orders')->onOrderComplete($order, $result);
				}
			}
		}
		else
		{
			$result = new stdClass();
			$result->order_id = $order['details']->order_id;
			$result->order_status = "C";
			$result->order_payment_status = 'Paid';
			$result->payment_capture_status = 'Authorized';
			$result->payment_capture_status = 'Captured';
			$result->message = JText::_('COM_PAGO_ORDER_PLACED_SUCCESSFULLY');
			$result->txn_id = '';
			$result->isFraud  = false;
			$result->paymentGateway  = 'Free of Cost';
			$result->cardnumber  ='';
			$result->fraudMessage = '';
			Pago::get_instance('orders')->onOrderComplete($order, $result);
		}

		Pago::get_instance('cookie')->set('processed', 1);

		$checkout_model = JModelLegacy::getInstance('checkout', 'PagoModel');
		$checkout_model->deplete_inventory($order_id);

		if ( !empty( $cart['coupon'] ) )
		{
			if ( isset($cart['coupon']['code']))
			{
				$coupon = Pago::get_instance('coupons');
				$coupon->set_code($cart['coupon']['code']);
				$coupon->incr_use();
			}
		}
		
		//to be able to tell if we are going directly to the complete method
		//from an offsite payment like paypal and to initate the payment capture
		$this->fromProcess = true;
		
		$this->execute('complete');
		
		//for internal API
		if($defer_redirect) return $result;
		
		return;
	}

	function ipn()
	{
		$jinput = JFactory::getApplication()->input;
		$gateway = $jinput->get('gateway', '', 'string');

		// Event can be found here
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin('pago_gateway');
		$paymentResult = array();
		$dispatcher->trigger(
			'onAfterAuthorizePayment',
			array( &$paymentResult, &$gateway, &$jinput)
		);

		foreach ( $paymentResult as $pgateway => $result)
		{
			if ($pgateway == $gateway)
			{
				if( $result->order_status == "P" )
				{
					$order_id = $result->order_id;
					Pago::get_instance( 'cookie' )->set( 'processed', 0 );

					$session->set('payment_option', '', 'pago_cart');
					JError::raiseWarning(500, $result->message);
					$this->execute( 'billing' );

					return;
				}
				// Get Order Data
 				$order_id = $result->order_id;
				$order 	= Pago::get_instance('orders')->get($order_id);
				Pago::get_instance('orders')->onOrderComplete($order, $result);
				$successMessage = $result->message;
			}
		}
		Pago::get_instance('cookie')->set('processed', 1);

		$checkout_model = JModelLegacy::getInstance('checkout', 'PagoModel');
		$checkout_model->deplete_inventory($order_id);

		if (!empty( $cart['coupon']))
		{
			if (isset($cart['coupon']['code']))
			{
				$coupon = Pago::get_instance('coupons');
				$coupon->set_code($cart['coupon']['code']);
				$coupon->incr_use();
			}
		}

		// $this->execute('complete');
		$return = 'index.php?option=com_pago&view=checkout&task=complete';
		$this->setRedirect($return, $successMessage);

		return;
	}

	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function complete()
	{
		//added to detect Internal API
		$defer_redirect = JFactory::getApplication()->input->get('defer_redirect', 0);
		
		// Because instead of using cookie the order id is being set in the session?
		$session 	= JFactory::getSession();
		$order_id = $session->get('order_id', 0, 'pago_cart');

		if(!$order_id) exit('illegal operation');
		// Load Gateway Plugin
		JPluginHelper::importPlugin('pago_gateway', 'gateway');

		// Get Order Data
		$order 	= Pago::get_instance('orders')->get($order_id);
		// For Capture Payment
		$payment_gateway = $order['details']->payment_gateway;

		$capture_event ='';
		
		if($payment_gateway != "Free of Cost")
		{
			//check if new payment system
			if(strstr($payment_gateway, 'pago_')){
				
				if(!isset($this->fromProcess)){
					$dispatcher = KDispatcher::getInstance();
					JPluginHelper::importPlugin('pago_gateway');
					//$capture_event = "order_cmpl";
					//$order['details']->order_status = "C";
				}
				
			} else {
				// Event can be found here
				jimport('joomla.html.parameter');
				$dispatcher = KDispatcher::getInstance();
				JPluginHelper::importPlugin('pago_gateway');
				$plugin = JPluginHelper::getPlugin('pago_gateway', $payment_gateway);
				$pluginParams = new JRegistry(@$plugin->params);
				$capture_event = $pluginParams->get('capture_event', '0');
			}
		}

		// Get Receipt Template
		$model = $this->getModel('checkout');
		$receipt_tmpl = $model->get_order_receipt_template();

		if ($capture_event == "order_cmpl" && $order['details']->order_status == "C")
		{
			$captureResult = array();
			
			//check if new payment system
			if(strstr($payment_gateway, 'pago_')){
				/*$dispatcher->trigger(
					'onGatewayCompletePayment',
					array( &$captureResult, &$gateway, &$order )
				);*/
				
			} else {
				$dispatcher->trigger(
					'onCapturePayment',
					array( &$captureResult, &$payment_gateway, &$order)
				);
			}
			
			foreach ( $captureResult as $pgateway => $result)
			{
				if ($pgateway == $payment_gateway)
				{
					$message = $result->message;
					Pago::get_instance('orders')->onOrderCaptured($order, $result);
				}
			}
		}
		
		//we will only force cleaning the cart on completed page
		//this method is also used for appying the paygate so we 
		//can't always clear it
		if(JFactory::getApplication()->input->getInt( 'clear', 0 )){
			//don't clear cart in case of checkout issues
			if(!$defer_redirect) Pago::get_instance('cart')->clear();
		}
		//don't clear cart in case of checkout issues
		//if(!$defer_redirect) Pago::get_instance('cart')->clear();
		
		$view = $this->getView('checkout', 'html');
		$view->assignRef('receipt_tmpl', $receipt_tmpl);
		$view->assignRef('order', $order);
		$view->setLayout('complete');
		$view->display();
		return $this;
	}
	
	/* TODO take PagoOrder $order as an argument for type hinting which will help keep a sane
	 * object of the order
	 */
	public function printorder()
	{
		$order_id = JFactory::getApplication()->input->getInt( 'orderid', 0 );
		$order_model = JModelLegacy::getInstance( 'order', 'PagoModel' );
		$order_model->setState( 'order_id', $order_id );
		$order = $order_model->getOrder();
		foreach( $order['items'] as &$item ) {
			$item->attributes = json_decode( $item->attributes );
		}

		$user_shipping = $order['addresses']['shipping'];
		$user_billing = $order['addresses']['billing'];

		$this->assignRef( 'user_shipping', $user_shipping );
		$this->assignRef( 'user_billing', $user_billing );
		$this->assignRef( 'order_id', $order_id );
		$this->assignRef( 'order', $order );

		$this->setLayout( 'invoice' );
		parent::display( $tpl );
	}

	/* Check for the Creditcard Param */

	function checkCreditCard()
	{
		jimport('joomla.html.parameter');
		$payment = JFactory::getApplication()->input->get('payment');
		
		//check if new payment system
		if(strstr($payment, 'pago_')){
			$id = str_replace('pago_', '', $payment);
			$pluginParams = Pago::get_instance('params')->get('paygates.'.$id);
			$credit_card =  $pluginParams->data->creditcard;
		} else {
		  	$plugin = JPluginHelper::getPlugin('pago_gateway', $payment);
		 	$pluginParams = new JRegistry($plugin->params);
		 	$credit_card =  $pluginParams->get('creditcard', '0');
        }
        
	 	ob_clean();
	 	echo $credit_card;
	 	exit;
	}

	/* Calculate tax in checkout  */

	function calc_tax()
	{
		$cart = Pago::get_instance('cart')->get();
		$price = Pago::get_instance( 'price' );

		if(JFactory::getApplication()->input->get('data', null, "ARRAY") != null){
			$addressData = JFactory::getApplication()->input->get('data', array(0), "ARRAY");
			$price->calculateTaxWhenCheckout($cart, false, $addressData);
		}
		else{
			$addressID = JFactory::getApplication()->input->get('addressID');
			$price->calculateTaxWhenCheckout($cart, $addressID);
		}
		
		exit();
	}

	function shipping_formatt()
	{
		$cart = Pago::get_instance('cart')->get();

		$Shipping = Pago::get_instance( 'price' )->format($cart['format']['shipping']);
		$Total = Pago::get_instance( 'price' )->format($cart['format']['total']);
		$Tax = Pago::get_instance( 'price' )->format($cart['tax']);

		$shippingTotal = array('shipping' => $Shipping, 'total' => $Total, 'tax' => $Tax);

		echo json_encode($shippingTotal);
		exit();
	}

}
