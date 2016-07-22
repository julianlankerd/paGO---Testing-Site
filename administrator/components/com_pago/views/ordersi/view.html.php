<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
* View class for a list of paGO orders.
*
* @package   Joomla.Administrator
* @subpackage  com_pago
* @since   2.5
*/
class PagoViewOrdersi extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	* Display the view
	*/

	public function display( $tpl = null )
	{

		switch( $this->_layout ){
			case 'form':
				$this->display_form();
				parent::display( $tpl );
				return;
		}

		$this->items = $this->parse_items( $this->get( 'Items' ) );
		$this->Allitems = $this->get( 'Allitems' );
		$this->pagination = $this->get( 'Pagination' );
		$this->state    = $this->get( 'State' );

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		//$this->addToolbar();

		////////// Our tool bar
		//$top_menu[] = array('task' => 'new', 'text' => JTEXT::_('PAGO_NEW'), 'class' => 'new pg-btn-medium pg-btn-green pg-btn-dark');
		$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'remove', 'text' => JTEXT::_('PAGO_DELETE'), 'class' => 'delete pg-btn-medium pg-btn-dark');
		if (JPluginHelper::isEnabled('pago_export', 'order_export_csv'))
		{
			$top_menu[] = array('task' => 'export_csv', 'text' => JTEXT::_('PAGO_EXPORT_CSV'), 'class' => 'export pg-btn-medium pg-btn-dark');
		}
		
		$top_menu[] = array('task' => 'print_orders', 'text' => JTEXT::_('PAGO_PRINT_ORDER'), 'class' => 'export pg-btn-medium pg-btn-dark');
		$this->assignRef( 'top_menu',  $top_menu );

		parent::display($tpl);
	}

	function display_form()
	{
		// JToolBarHelper::save();
		// JToolBarHelper::apply();

		// if ( JFactory::getApplication()->input->get('cid', array(), 'array' ))
		// {
		// 	// JToolBarHelper::cancel();
		// }
		// else
		// {
		// 	// JToolBarHelper::cancel('cancel', 'Close');
		// }

		$this->Allitems = $this->get( 'Allitems' );
		$cid = JFactory::getApplication()->input->get('cid', array(0), 'array');
		$cid = (int) $cid[0];
		$this->OrderLogs = $this->get('OrderLogs');
		$order 	= Pago::get_instance('orders')->get($cid);
		$CURRENCY_SYMBOL = CURRENCY_SYMBOL;
		//var_dump($order); exit();
		$order_status = Pago::get_instance('orders')->get_order_status($order['details']->order_status);
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		$shipment = $order['shipments'][0];
		$shipper = explode('-', $shipment['carrier']);
		isset( $shipper[0] ) or $shipper[0] = 'Unspecified';
		isset( $shipper[1] ) or $shipper[1] = 'Unspecified';

		$item_shipping = false;
		if ($shipment['carrier'] == "")
		{
			$item_shipping = true;
		}

		if ($order['details']->ip_address == '::1')
		{
			$order['details']->ip_address = JTEXT::_('COM_PAGO_BY_ADMIN');
		}

		$order['addresses']['billing']->region = $order['addresses']['billing']->state;
		$order['addresses']['shipping']->region = $order['addresses']['shipping']->state;

		$bind_data = array(
			'information' => array(
				'order_id' => $order['details']->order_id,
				'order_status' => $order_status,
				'customer_name' => $order['addresses']['billing']->first_name . ' ' . $order['addresses']['billing']->last_name,
				'order_date' => $order['details']->cdate,
				'ip_address' => $order['details']->ip_address,
				'order_shipping' => Pago::get_instance( 'price' )->format($order['details']->order_shipping, $order['details']->order_currency),
				'order_tax' => Pago::get_instance( 'price' )->format($order['details']->order_tax, $order['details']->order_currency),
				'order_discount' => Pago::get_instance( 'price' )->format($order['details']->order_discount+$order['details']->coupon_discount, $order['details']->order_currency),
				'order_total' => Pago::get_instance( 'price' )->format($order['details']->order_total, $order['details']->order_currency),
				'order_subtotal' => Pago::get_instance( 'price' )->format($order['details']->order_subtotal, $order['details']->order_currency)
			),
			'shipping_details' => array(
				'carrier' => $shipper[0],
				'method' => trim($shipper[1]),
				'shipping_total' => Pago::get_instance('price')->format($shipment['shipping_total'], $order['details']->order_currency)
			),
			'address_billing' => $order['addresses']['billing'],
			'address_shipping' => $order['addresses']['shipping'],
			'items' => $order['items']
		);
		$payment = array();

		if(isset($order['payment'][0]) and count($order['payment'][0]) > 0)
		{

			$payment = $order['payment'][0];
			$orderPyament[] = array(
							'date' => $payment->sdate,
							'txn_id' => $payment->txn_id,
							'payment_data' => $payment->payment_data,
							'status' => $payment->status,
							'amount' => $payment->payment,
							'payment_capture_status' => $payment->payment_capture_status,
							'isFraud' => $payment->isfraud,
							'fraudMessage' => $payment->fraud_message
						);
		}
		PagoHtml::add_js( JURI::root( true ) .'/administrator/components/com_pago/javascript/jquery-ui/js/jquery-ui-1.10.4.custom.min.js');

		PagoHtml::add_js( JURI::root( true ) . '/administrator/components/com_pago/javascript/jquery-ui/js/jquery.multiselect.min.js');

		PagoHtml::add_js( JURI::root( true ) . '/administrator/components/com_pago/javascript/jquery-ui/js/jquery.multiselect.filter.js');
		// PagoHtml::add_js( JURI::root( true ) . '/administrator/components/com_pago/javascript/com_pago_order.js');
		// PagoHtml::add_css( JURI::base( true ) . '/components/com_pago/css/jquery-ui.css' );
		PagoHtml::add_css( JURI::base( true ) . '/components/com_pago/css/jquery.multiselect.css' );
		PagoHtml::add_css( JURI::base( true ) . '/components/com_pago/css/jquery.multiselect.filter.css' );
		Pago::load_helpers('pagoparameter');

		$params = new PagoParameter($bind_data,  $cmp_path . 'views/ordersi/tmpl/fields.xml');

		JForm::addfieldpath(array($cmp_path . DS . 'elements'));

		$info = $params->render('information',
			'information',
			JText::_('PAGO_ORDERS_BASIC_DETAILS'),
			'default',
			null,
			false
		);
		$shipping = $params->render('shipping_details',
			'shipping_details',
			JText::_('PAGO_ORDERS_SHIPPING_DETAILS'),
			'default',
			null,
			false
		);
		$address_b = $params->render('address_billing',
			'address_billing',
			JText::_('PAGO_ORDERS_BILLING_DETAILS'),
			'default',
			null,
			false
		);
		$address_s = $params->render('address_shipping',
			'address_shipping',
			JText::_('PAGO_ORDERS_MAILING_DETAILS'),
			'default',
			null,
			false
		);

		$this->assignRef('information', $info);
		$this->assignRef('shipping_details', $shipping);
		$this->assignRef('item_shipping', $item_shipping);
		$this->assignRef('address_billing', $address_b);
		$this->assignRef('address_shipping', $address_s);

		$this->assignRef('items', $order['items']);
		$this->assignRef('payments', $orderPyament);
		$this->assignRef('order', $order);
		$this->assignRef('order_id', $cid);
		$this->assignRef('address_id', $order['addresses']['billing']->id);
		$this->assignRef('saddress_id', $order['addresses']['shipping']->id);
		$this->assignRef('user_id', $order['details']->user_id);


		//$this->assignRef( 'custom_params', $params->render( 'custom', 'custom' ) );
	//	$this->assignRef( 'item', $item );
	
		// Our tool bar
		$top_menu[] = array('task' => 'save', 'text' => JTEXT::_('PAGO_SAVE_AND_CLOSE'), 'class' => 'save pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'apply', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'apply pg-btn-medium pg-btn-dark pg-btn-green');
		$top_menu[] = array('task' => 'cancel', 'text' => JTEXT::_('PAGO_CANCEL'), 'class' => 'cancel pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'cancel_order', 'text' => JTEXT::_('PAGO_ORDER_CANCEL'), 'class' => 'pg-btn-medium pg-btn-dark pg-btn-red');

		$this->assignRef( 'top_menu',  $top_menu );
		
	}

	/**
	* Add the page title and toolbar.
	*
	* @since 1.6
	*/
	protected function addToolbar()
	{
		JToolBarHelper::addNew('new');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('Are you sure you want to delete this order?');
	}

	protected function parse_items( $items )
	{

		$this->order_status_options = Pago::get_instance('config')->get_order_status_options();

		if( is_array( $items ) ) {
			foreach( $items as $k => $item ) {

				$item->name = $item->first_name . ' ' . $item->last_name;

				if( !$item->order_status ){
					$item->order_status = 'P';
				}

				$item->order_id_text = $this->leading_zeros( $item->order_id, 5);

				$link =
					JRoute::_(
						'index.php?option=com_pago&view=orders&task=edit&cid[]='.
						$item->order_id
					);
				//$item->editlink = "<a href=\"{$link}\">{$leading_ord_id}</a>";

				$item->order_status = $this->order_status_options[ $item->order_status ];
				$item->mdate = date( 'r', strtotime( $item->mdate ) );
				$items[$k] = $item;
			}
		}

		return $items;
	}

	protected function leading_zeros( $value, $places )
	{

		$leading = 0;
		if(is_numeric($value)){
			for($x = 1; $x <= $places; $x++){
				$ceiling = pow(10, $x);
				if($value < $ceiling){
					$zeros = $places - $x;
					for($y = 1; $y <= $zeros; $y++){
						$leading .= "0";
					}
				$x = $places + 1;
				}
			}
			$output = $leading . $value;
		}
		else{
			$output = $value;
		}
		return $output;
	}
}
