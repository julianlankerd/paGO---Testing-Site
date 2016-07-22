<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerOrders extends PagoController
{
	private $_view = 'Orders';

	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );
		$this->registerTask( 'new', 'new_order' );
		$this->registerTask( 'remove', 'delete' );
	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}

	function new_order()
	{
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function invoice()
	{
		JFactory::getApplication()->input->set( 'layout', 'invoice' );
		parent::display();
	}

	function grid_edit()
	{
		$operation = JFactory::getApplication()->input->get( 'oper' );

		switch ( JFactory::getApplication()->input->get( 'oper' ) ) {
			case 'del':
				$this->delete();
			break;
			case 'edit':
				$this->save_status();
			break;
		}
	}

	function save_status()
	{
		$order_status = JFactory::getApplication()->input->get( 'order_status' );
		$id = JFactory::getApplication()->input->get( 'id' );

		$db = JFactory::getDBO();

		$db->setQuery("
			UPDATE #__pago_orders SET `order_status` = '$order_status'
			WHERE `order_id` = $id;
		");

		$db->query();
	}

	function delete()
	{
		if ( !is_array( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) ) ) return;

		$where = false;

		foreach ( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) as $id ) {
			$where .= " `order_id`=$id OR";
		}

		$where = substr_replace( $where, '', -3 );

		$db = JFactory::getDBO();

		$db->setQuery( "DELETE FROM #__pago_orders WHERE $where" );
		$db->query();

		$db->setQuery( "DELETE FROM #__pago_orders_items WHERE $where" );
		$db->query();

		$msg = JText::_( 'Successfully Deleted Order' );
		$this->setRedirect(
			'index.php?option=com_pago&view=orders2',
			$msg
		);
	}

	function edit()
	{
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function save()
	{
		$ship_method = JFactory::getApplication()->input->get( 'shipping' );

		$order_id = $this->store();

		if( !$ship_method['ship_method_id'] ){
			//print_r($_REQUEST);die();
			$msg = JText::_( 'PLEASE SELECT SHIPPING OPTIONS' );
			$this->setRedirect(
				'index.php?option=com_pago&view=orders&task=edit&cid[]=' . $order_id,
				$msg
			);
			return;
		}

		$msg = JText::_( 'Successfully Saved Parameters' );
		$this->setRedirect( 'index.php?option=com_pago&view=orders', $msg );
	}

	function apply()
	{
		$order_id = $this->store();

		$msg = JText::_( 'Successfully Applied Parameters' );
		$this->setRedirect(
			'index.php?option=com_pago&view=orders&task=edit&cid[]=' . $order_id,
			$msg
		);
	}

	function cancel()
	{
		$msg = JText::_( 'Canceled Operation' );
		$this->setRedirect( 'index.php?option=com_pago&view=orders', $msg );
	}

	function store()
	{
		$order_id = JFactory::getApplication()->input->get('id');
		$details = JFactory::getApplication()->input->get( 'details' );
		$items = JFactory::getApplication()->input->get( 'params' );
		$ship_method = JFactory::getApplication()->input->get( 'shipping' );
		$discounts = JFactory::getApplication()->input->get( 'discounts' );
		$address_billing = JFactory::getApplication()->input->get( 'address_billing' );
		$address_shipping = JFactory::getApplication()->input->get( 'address_shipping' );

		$groups = JFactory::getApplication()->input->get( 'grouplist' );
		$groups = $groups['groups'];

		$user_id = $details['user_id'];

		$items = json_decode( $items['itemslist'], true );
		$ship_method = $ship_method['ship_method_id'];

		//$details['order_discount'] = $discounts['order_discount'];

		$details['order_subtotal'] = 0;

		foreach ( $items as $item ) {
			$details['order_subtotal'] =
				$details['order_subtotal'] + ( $item['qty'] * $item['price'] );
		}

		$details['order_subtotal'] = $details['order_subtotal'] - $details['order_discount'];

		$details['order_shipping'] = false;

		if ( $ship_method ) {
			$details['order_shipping'] = array_pop( explode('|', $ship_method) );
		}

		$details['order_total'] = $details['order_shipping'] + $details['order_subtotal'];

		if ( $order_id ) {
			$details['order_id'] = $order_id;
			//2011-01-26 16:10:48
		} else {
			$details['cdate'] = date( 'Y-m-d H:i:s', time() );
		}

		$details['ship_method_id'] = $ship_method;

		$set = false;

		foreach ( $details as $name => $value ) {
			$set .= "$name='$value',";
		}

		$set = substr_replace( $set, '', -1 );

		$db = JFactory::getDBO();

		$db->setQuery("
			INSERT INTO #__pago_orders SET $set
			ON DUPLICATE KEY UPDATE $set
		");

		$db->query();

		$insertid = $db->insertid();
		if ( $insertid ) {
			$order_id = $insertid;
		}

		$db->setQuery("
			DELETE FROM #__pago_orders_items WHERE order_id=$order_id;
		");

		$db->query();

		$set = false;

		foreach ( $items as $item ) {
			$set .= '('. $order_id . ',' . $item['id'] . ',' . $item['qty'] . ',"' .
				$item['price'] .'"),';
		}

		$set = substr_replace( $set, '', -1 );

		$db->setQuery("
			INSERT INTO #__pago_orders_items ( order_id, item_id, qty, price )
			VALUES $set;
		");

		$db->query();

		//addresses
		$db->setQuery("
			DELETE FROM #__pago_orders_addresses  WHERE order_id=$order_id;
		");

		$db->query();

		//billing address
		$set = "order_id={$order_id},user_id={$user_id},address_type='b',";

		foreach ( $address_billing as $name => $value ) {
			$set .= "$name='$value',";
		}

		$set = substr_replace( $set, '', -1 );

		$db = JFactory::getDBO();

		$db->setQuery("
			INSERT INTO #__pago_orders_addresses SET $set
		");

		$db->query();

		//mailing address
		$set = "order_id={$order_id},user_id={$user_id},address_type='m',";

		foreach ( $address_shipping as $name => $value ) {
			$set .= "$name='$value',";
		}

		$set = substr_replace( $set, '', -1 );

		$db = JFactory::getDBO();

		$db->setQuery("
			INSERT INTO #__pago_orders_addresses SET $set
		");

		$db->query();

		$db->setQuery( "DELETE FROM #__pago_groups_users WHERE user_id = {$user_id}" );
		$db->query();

		if ( !empty( $groups ) ) {
			foreach ( $groups as $group_id ) {
				$db->setQuery(
					"INSERT INTO #__pago_groups_users SET group_id = {$group_id},".
					"user_id = {$user_id}"
				);
				$db->query();
			}
		}

		return $order_id;
	}

	function getRecentOrdersList($ordersCount=5){
		$ordersCount = $_POST['ordersCount'];
		$itemModel = JModelLegacy::getInstance( 'item', 'PagoModel' );
		$attributeModel = JModelLegacy::getInstance( 'attribute', 'PagoModel' );
		Pago::load_helpers( 'imagehandler' );
		$db = JFactory::getDBO();
		$sql = "SELECT DISTINCT  o.*, u.first_name, u.last_name, i.item_id,i.varation_id
			FROM #__pago_orders AS o
			LEFT JOIN #__pago_orders_addresses AS u ON o.order_id = u.order_id
			LEFT JOIN #__pago_orders_items AS i ON o.order_id=i.order_id 
			ORDER BY o.order_id DESC LIMIT ".$ordersCount;

		$db->setQuery($sql);

		$orders = $db->loadObjectList();
		$arr=array();
		foreach($orders as $order){
			$order=(array)$order;
			$order['primary_category']=$itemModel->getItemPrimaryCat($order['item_id']);
			$order['name']=$itemModel->getItemName($order['item_id'])->name;

			$itemImage = PagoImageHandlerHelper::get_item_files($order['item_id']);

			if(count($itemImage)>0){
				$order['file_name']="<img src='".JURI::ROOT().'media/pago/items/'.$order['primary_category'].'/'.$itemImage[0]->file_name."'>";

			}
			
			
			 
			 if($order['varation_id']!=0){

			 	$order['file_name']=$attributeModel->getVarationImages($order['varation_id'],"-large");
			 	$order['name']=$attributeModel->getVarationName($order['varation_id']);

			 }
			 array_push ($arr,$order);
			
		}
		$orders = $arr;
		
		$html = "";

		foreach ($orders as $order){
			if(array_key_exists('file_name',$order)){
				if($order['file_name']!=""){
					$image = $order['file_name'] ;
				}else{
					$image = "<img src='".JURI::root() . "components/com_pago/images/category-noimage.jpg"."'>";
				}

			}else{

				$image = "<img src='".JURI::root() . "components/com_pago/images/category-noimage.jpg"."'>";
			}
			$html .= '<tr>';
			$html .= '<td style="padding: 0px;">'.$image.'</td>';
			$html .= '<td style="padding: 0px;">'.$order['name'].'</td>';
			$html .= '<td>'.$order['first_name'] . ' ' . $order['last_name'].'</td>';
			$html .= '<td>'.$order['cdate'].'</td>';
			$html .= '<td>'.Pago::get_instance('price')->format($order['order_total']).'</td>';
			
			$html .= '</tr>';
		}
		echo json_encode($html);
        exit();

	}
}
