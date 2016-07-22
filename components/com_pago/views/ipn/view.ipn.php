<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
//Pago::load_helpers( 'price' );

class PagoViewIpn extends JViewLegacy
{
	function display($tpl = null) {

		$gateway = JFactory::getApplication()->input->get( 'gateway' );

		//file_put_contents( dirname(__FILE__) . '/dump.txt', print_r($_REQUEST, true), FILE_APPEND );

		//Load plugin that houses KCommerce, the kc_initialise
		//event can be found here
		JPluginHelper::importPlugin( 'pago_gateway', 'gateway' );

		//trigger the initialise event, we use array_pop
		//because the trigger method returns an array
		//and since we are only expecting one response
		//all's cool
		$KGateway = array_pop(
			//message the initialise event also passing the selected pgate
			JDispatcher::getInstance()->trigger( 'kg_initialise', array( 'notification', $gateway ) )
		)
		//note: object chaining ahead!
		//here we add an object to implement the KCommerce Observer layer
		->add_observer( Pago::get_instance( 'gateway_observer' ) )
		//now we set the checkout manifest
		->process_notification();

		return;












		JPluginHelper::importPlugin( 'pago_payment' );

		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( JFactory::getApplication()->input->get( 'gateway' ) . '_ipn' );

		JFactory::getApplication()
			->redirect( JURI::root() . '?option=com_pago&view=checkout&step=completed&order_id=' . JFactory::getApplication()->input->get( 'order_id' ) );

		return;


		//this stuff is depreciated but leave it for reference when creating the email notification plugin


		$this->gateway = $gateway;
		$orders_model = JModelLegacy::getInstance( 'Orders','PagoModel' );

		$orders_model->setState( 'order_id', $order_id );
		$order = $orders_model->getOrder();

		$this->order = $order;

		//if( !$order || !$gateway ) return false;

		JPluginHelper::importPlugin( 'pago_payment', $gateway );

		JDispatcher::getInstance()->trigger( $gateway . '_ipn' );
		JDispatcher::getInstance()->trigger( 'pago_order_status_update' );

		$params = &JComponentHelper::getParams( 'com_pago' );

		$user = JFactory::getUser( $order->user_id );
		$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );
		$user_fields_model->setState( 'user_id', $order->user_id );
		$user_data = $user_fields_model->get_data();
		$user_data = $user_data[1];

		//file_put_contents( dirname(__FILE__) . '/udata.txt', print_r($user_data, true));

		$mail = JFactory::getMailer();

		$mail->to = array();
		$mail->isHTML(true);

		$config = Pago::get_instance( 'config' )->get();

		$email_body = $config->get( 'transaction_completed' );

		if(!$email_body){
			$email_body = '{first_name} {last_name} {receipt}';
		}

		$email_body = str_replace( '{first_name}', $user_data->first_name, $email_body);
		$email_body = str_replace( '{last_name}', $user_data->last_name, $email_body);
		$email_body = str_replace( '{receipt}', $this->get_receipt($tpl), $email_body);

		$mail->setBody( $email_body );

		$mail->setSubject("order: " . $order_id );
		$mail->setSender( $config->get( 'store_email' ) );

		$mail->addRecipient($user_data->user_email );

		if($conf->getValue('config.mailer') == 'smtp'){
			$mail->useSMTP(
				$conf->getValue('config.smtpauth'),
				$conf->getValue('config.smtphost'),
				$conf->getValue('config.smtpuser'),
				$conf->getValue('config.smtppass'),
				$conf->getValue('config.smtpsecure'),
				$conf->getValue('config.smtpport')
			);
		}

		if($mail->Send() === true){

		}

		JFactory::getApplication()->input->set( array(
			'order_id' => $order_id
		), 'POST' );

		//file_put_contents( dirname(__FILE__) . '/ipn_log.txt', print_r($_REQUEST, true));
		$orders_model->store();

		/*JRequest::set( array(
			'user_id' => 62,
			'vendor_id' => false,
			'order_number' => false,
			'user_info_id' => false,
			'order_total' => false,
			'order_subtotal' => false,
			'order_tax' => false,
			'order_tax_details' => false,
			'order_shipping' => false,
			'order_shipping_tax' => false,
			'coupon_discount' => false,
			'coupon_code' => false,
			'order_discount' => false,
			'order_currency' => false,
			'order_status' => false,
			'cdate' => false,
			'mdate' => false,
			'ship_method_id' => false,
			'customer_note' => false,
			'ip_address' => false,
			'ipn_dump' => 	print_r( $ipn_data, true )
		), 'POST' );*/

		if( 'gcheckout' == $gateway ) die();

		echo '<script type="text/javascript">
		<!--
		window.location = "'.JURI::root() . '?option=com_pago&view=checkout&step=completed"
		//-->
		</script>
		';

		die();
    }

	function get_receipt( $tpl ){
		$this->addTemplatePath( JPATH_ROOT . '/components/com_pago/templates/default/ipn' );

		$order_id = JFactory::getApplication()->input->get( 'order_id' );

		$price_helper = new PagoHelperPrice;

		$orders_items_model = JModelLegacy::getInstance( 'Orders_items','PagoModel' );

		$cart = $orders_items_model->get_order_items( $order_id );
		$shipper = array();

		$shipper['value'] = $this->order->order_shipping;
		$shipper['name'] = $this->order->ship_method_id;

		$price_helper->set_amounts( $cart, $shipper );

		$this->assign( 'payment_option', $this->gateway );
		$this->assignRef( 'cart', $cart );
		$this->assignRef( 'price_helper', $price_helper );
		$this->assignRef( 'sub_total_price', $price_helper->subtotal );
		$this->assignRef( 'total_price', $price_helper->total );
		$this->assignRef( 'shipping', $shipper );
		$this->assignRef( 'order', $this->order );

		$this->setLayout( 'receipt' );

		ob_start();
        parent::display( $tpl );

		return ob_get_clean();
	}
}
