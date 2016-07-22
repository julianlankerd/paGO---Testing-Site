<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

/**
 * usage
$result = Pago::get_instance('transaction_email')->set((object)[
	
	'recipients' => [(object)[
		'template' => 'email_invoice',
		'type' => 'site',
		'name' => 'Adam Docherty',
		'email' => 'me@gmail.com'
	],(object)[
		'template' => 'email_invoice',
		'type' => 'admin',
		'name' => 'Super User',
		'email' => 'admin@corephp.com'
	]],
	
	'data' => (object)[
		'total' => 10.99,
		...
	]
	
])->send();
**/
	
defined('_JEXEC') or die('Restricted access');

/**
 * Pago transaction email class.
 * 
 * @author Adam Docherty 4 'corePHP', LLC
 *
 * @since  1.0
 */
class pago_transaction_email
{
	/**
	 * Method set email templatee to recipients.
	 *
	 * @param   array  $params  An array of input data.
	 *
	 * @return  self object
	 *
	 * @since   1.0
	 */
	public function set($params)
	{
		//populate class vars with params
		foreach($params as $name=>$value)
			$this->$name = $value;
		
		//each recipient needs template rendered 
		//and mail sent individually
		foreach($this->recipients as $recipient){
			
			//load our template which is created in backend admin
			$template = $this->get_template(
				$recipient->template, 
				$recipient->type
			);
			
			//set up our method name to check if custom map
			//exists for this template
			$map = $recipient->template;
			
			//if a map is found it will be processed to provide the 
			//correct template variables to the template
			if(method_exists($this, $map))
				$this->$map();
			
			//template name will be our email subject if subject empty
			if(!isset($recipient->subject))
				@$recipient->subject = $template->pgemail_name;
			
			//add the rendered invoice template to our object
			//it is now ready to be emailed
			//echo
			@$recipient->body = $this->render_template($template->pgemail_body);
		}
		
		//return self for object chaining
		return $this;
	}
	
	/**
	 * Method send transactional emails to recipients.
	 *
	 * @return  self object
	 *
	 * @since   1.0
	 */
	function send()
	{
		//get paGO backend store configuration
		$store_cfg 	= Pago::get_instance('config')->get();
		
		//set up from email and name from config params
		$from = [
			$store_cfg->get('general.store_email'), 
			$store_cfg->get('general.pago_store_name')
		];
		
		//print_r($this->recipients);die;
		
		//loop thru our recipients and send their transactional emails
		foreach($this->recipients as $recipient){
			
			$mailer = JFactory::getMailer();
			
			$mailer->isHTML();
			$mailer->addRecipient($recipient->email);
			$mailer->setSender($from);
			$mailer->setSubject($recipient->subject);
			$mailer->setBody($recipient->body);
			$mailer->send();
		}
		
		//return self for object chaining
		return $this;
	}
	
	/**
	 * Method to load email template from database.
	 *
	 * @param   array  $params  An array of input data.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	private function get_template($template, $type)
	{
		//templates are created in the backend admin and stored
		//in the db pago_mail_templates table
		$db = JFactory::getDBO();
			
		$db->setQuery("
			SELECT pgemail_name, pgemail_body
				FROM `#__pago_mail_templates` 
					WHERE pgemail_enable = 1
						AND pgemail_type = {$db->quote($template)}
						AND template_for = {$db->quote($type)}
		");

		return $db->loadObject();
	}
	
		/**
	 * Method to render data to email template using Tinybutstrong class.
	 *
	 * @param   string  $template  the template file html from database.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	private function render_template($template)
	{
		//load our template engine - Tinybutstrong
		JLoader::register('clsTinyButStrong', dirname(__FILE__) . '/../helpers/tbs.php');
		
		//need to alter the template data to TBS format, this is for backwards 
		//compatibility so we can use existing templates
		$template = str_replace('{', '{onshow.', $template);
		
		//set some default options for TBS
		$tbs = new clsTinyButStrong([
			'chr_open' => '{', 
			'chr_close' => '}'
		]);
		
		//supress template errors
		$tbs->NoErr = true;
		
		//load our template string to TBS
		$tbs->Source = $template;
		
		//map our data parameter to TBS
		foreach($this->data as $name=>$value)	
			$tbs->VarRef[$name] = $value;
		
		//render the template
		$tbs->LoadTemplate(null);
		$tbs->Show(TBS_NOTHING);
		
		return $tbs->Source;
	}
	
		/**
	 * Method to convert status abbreviation to text.
	 *
	 * @param   string  $status  the status abbreviation.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	private function get_order_status($status)
	{
		//map of paGO status text
		$map = [	
			'P' => JText::_('PAGO_ORDER_STATUS_PENDING'),
			'C' => JText::_('PAGO_ORDER_STATUS_COMPLETED'),
			'X' => JText::_('PAGO_ORDER_STATUS_CANCELLED'),
			'R' => JText::_('PAGO_ORDER_STATUS_REFUNDED'),
			'S' => JText::_('PAGO_ORDER_STATUS_SHIPPED'),
			'PA' => JText::_('PAGO_ORDER_STATUS_AUTHORIZED_ONLY')
		];
		
		if(isset($map[$status])) return $map[$status];
			
		return Text::_('PAGO_ORDER_STATUS_PENDING');
	}
	
	/**
	 * Method to do custom stuff on specific transaction template.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function email_invoice_failed()
	{
		$this->map_legacy_templates();
	}
	
		/**
	 * Method to do custom stuff on specific transaction template.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function email_invoice()
	{
		$this->map_legacy_templates();
	}
	
		/**
	 * Method to do custom stuff on specific transaction template.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function email_update_order_status()
	{
		$this->map_legacy_templates();
	}
	
		/**
	 * Method to map old template tag vars to input data paramter.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function map_legacy_templates()
	{
		//maps our data parameter to the template structure / tags
		//template tag variable naming should reflect that of database
		//field names and should not be altered otherwise you have to 
		//have a messy map routine like below! ie if a database field is named 
		//"order_subtotal" don't name the template variable something different
		//like "ordersubtotal" keep variable names consistent!!!
		
		$order = $this->data;
		$order['payment'] = $order['payment'][0];
		
		$exclude = array_flip([
			'ID',
			'ORDER_ID',
			'ADDRESS_TYPE',
			'ADDRESS_TYPE_NAME',
			'CDATE',
			'MDATE',
			'PERMS'
		]);
		
		$item_name = '';
		$item_quantity = '';
		$item_sku = '';
		$item_price = '';
		
		foreach($order['items'] as $item){
			$item_name .= "{$item->name}<br>";
			$item_quantity .= "{$item->qty}<br>";
			$item_sku .= "{$item->sku}<br>";
			$item_price .= Pago::get_instance('price')->format($item->price) . "<br>";
		}
		
		$billingHtml = '';
		
		foreach($order['addresses']['billing'] as $name=>$value){
			if($value && !isset($exclude[strtoupper($name)]))
			$billingHtml .= '<br><b>' . JText::_('PAGO_' . strtoupper($name)) . '</b>: ' . $value;
		}
		
		$mailingHtml = '';
		
		foreach($order['addresses']['shipping'] as $name=>$value){
			if($value && !isset($exclude[strtoupper($name)]))
			$mailingHtml .= '<br><b>' . JText::_('PAGO_' . strtoupper($name)) . '</b>: ' . $value;
		}
		
		$this->data = array_merge([
			'orderid' => $order['details']->order_id,
			'user_email' => $order['details']->user_email,
			'ordertotal' => Pago::get_instance('price')->format($order['details']->order_total),
			'ordersubtotal' => Pago::get_instance('price')->format($order['details']->order_subtotal),
			'orderrefundtotal' => Pago::get_instance('price')->format($order['details']->order_refundtotal),
			'ordertax' => Pago::get_instance('price')->format($order['details']->order_tax),
			'ordertax_details' => $order['details']->order_tax_details,
			'order_shipping' => Pago::get_instance('price')->format($order['details']->order_shipping),
			'order_shippingtax' => Pago::get_instance('price')->format($order['details']->order_shipping_tax),
			'order_coupon_disc' => Pago::get_instance('price')->format($order['details']->coupon_discount),
			'order_coupon_code' => $order['details']->coupon_code,
			'order_discount' => Pago::get_instance('price')->format($order['details']->order_discount),
			'ordercurrency' => $order['details']->order_currency,
			'orderstatus' => $this->get_order_status($order['details']->order_status),
			'order_cadte' => $orderDate = date("m/d/Y", strtotime(str_replace ( "/" , "-" ,  $order['details']->cdate))),
			'order_detail_link' => '',
			'item_name' => $item_name,
			'item_quantity' => $item_quantity,
			'item_sku' => $item_sku,
			'item_price' => $item_price,
			'billingaddress' => $billingHtml,
			'mailingaddress' => $mailingHtml,
			'order_item_ship_method_lbl' => JTEXT::_('PAGO_ORDER_ITEM_SHIP_METHOD_LBL'),
			'order_item_ship_method' => $item->order_item_ship_method_id,
			'order_subtotal' => Pago::get_instance('price')->format($order['details']->order_subtotal),
			'payment_lbl' => JTEXT::_('PAGO_PAYMENT_LBL'),
			'paymentmethod' => $order['details']->payment_gateway,
			'order_payment_msg_lbl' => JTEXT::_('PAGO_PAYMENT_METHOD_MSG_LBL'),
			'order_payment_msg' => $order['payment']->payment_data  . '<br>' .  $order['payment']->txn_id,
			'order_shipmethod_lbl' => JTEXT::_('PAGO_SHIP_METHOD_LBL'),
			'order_shipping_method' => $order['details']->ship_method_id,
			'order_customernote_lbl' => JTEXT::_('PAGO_CUST_NOTE_LBL'),
			'order_customernote' => $order['details']->customer_note,
			'order_receipt_lbl' => JTEXT::_('COM_PAGO_ORDER_RECEIPT_LBL'),
			'customer_information_lbl' => JTEXT::_('COM_PAGO_CUSTOMER_INFORMATION_LBL'),
			'order_items_lbl' => JTEXT::_('COM_PAGO_ORDER_ITEMS_LBL'),
			'ordertotal_lbl' => JTEXT::_('PAGO_ORD_TOTAL_LBL'),
			
			'orderrefundtotal_lbl' => JTEXT::_('PAGO_ORDREFUND_TOTAL_LBL'),
			
			'order_subtotal_lbl' => JTEXT::_('PAGO_ORD_SUBTOTAL_LBL'),
			'order_tax_lbl' => JTEXT::_('PAGO_ORD_TAX_LBL'),
			'order_tax_details_lbl' => JTEXT::_('PAGO_ORD_TAX_DETAILS_LBL'),
			'order_ship_lbl' => JTEXT::_('PAGO_ORD_SHIP_LBL'),
			'order_ship_tax_lbl' => JTEXT::_('PAGO_SHIP_TAX_DETAILS_LBL'),
			'order_coupon_disc_lbl' => JTEXT::_('PAGO_COUPON_DISC_LBL'),
			'order_disc_lbl' => JTEXT::_('PAGO_COP_DISC_LBL'),
			'order_couponcode_lbl' => JTEXT::_('PAGO_COP_CODE_LBL'),
			'order_currency_lbl' => JTEXT::_('PAGO_CURRENCY_LBL'),
			'order_status_lbl' => JTEXT::_('PAGO_STATUS_LBL'),
			'order_cdate_lbl' => JTEXT::_('PAGO_CDATE_LBL'),
			'order_billing_add_lbl' => JTEXT::_('PAGO_BILLING_ADD_LBL'),
			'order_mailing_add_lbl' => JTEXT::_('PAGO_MAILING_ADD_LBL'),
			'orderid_lbl' => JTEXT::_('PAGO_MAILING_ORDIID_LBL'),
			'item_name_lbl' => JTEXT::_('PAGO_ITEM_NAME_LBL'),
			'item_quantity_lbl' => JTEXT::_('PAGO_ITEM_QTY_LBL'),
			'item_sku_lbl' => JTEXT::_('PAGO_ITEM_SKU_LBL'),
			'item_price_lbl' => JTEXT::_('PAGO_ITEM_PRICE_LBL'),
			'item_item_ship_method_lbl' => JTEXT::_('PAGO_ITEM_SHIP_METHOD_LBL'),
			'order_detail_link_lbl' => JTEXT::_('PAGO_ORDER_DETAIL_LINK_LBL')
		
		], $this->data);
	}
}
