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
 * View class for a list of Orders.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoViewOrders extends JViewLegacy
{
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	
	public function display( $tpl = null )
	{		
		
		if ( $this->_layout == 'form' ) {
			return $this->display_order();
		}
		
		// Initialise variables.
		$this->order_status_options = Pago::get_instance('config')->get_order_status_options();
		$this->categories	= $this->get( 'CategoryOrders' );
		$this->items		= $this->parse_items( $this->get( 'Items' ) );
		$this->pagination	= $this->get( 'Pagination' );
		$this->state		= $this->get( 'State' );
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		require_once JPATH_COMPONENT .'/../com_banners/models/fields/bannerclient.php';
		parent::display($tpl);
	}
		
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/../com_banners/helpers/banners.php';
		
		$canDo	= BannersHelper::getActions($this->state->get('filter.category_id'));
		$user	= JFactory::getUser();
		//JToolBarHelper::title(JText::_('COM_BANNERS_MANAGER_BANNERS'), 'banners.png');
		//if (count($user->getAuthorisedCategories('com_banners', 'core.create')) > 0) {
			JToolBarHelper::addNew('new');
		//}

		if (($canDo->get('core.edit'))) {
			JToolBarHelper::editList('edit');
		}

		/*if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'banners.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {*/
			JToolBarHelper::deleteList('delete');
			JToolBarHelper::divider();
		//}	

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_pago');
			JToolBarHelper::divider();
		}
		JToolBarHelper::help('JHELP_COMPONENTS_BANNERS_BANNERS');
	}
	
	protected function parse_items( $items ){
		
		
		
		if( is_array( $items ) ) {
			foreach( $items as $k => $item ) {
				
				$item->name = $item->first_name . ' ' . $item->last_name;
				
				if( !$item->order_status ){
					$item->order_status = 'P';
				}

				$leading_ord_id = $this->leading_zeros($item->order_id, 5);
				
				$link =
					JRoute::_(
						'index.php?option=com_pago&view=orders&task=edit&cid[]='.
						$item->order_id
					);
				$item->editlink = "<a href=\"{$link}\">{$leading_ord_id}</a>";
				
				$item->order_status = $this->order_status_options[ $item->order_status ];
				$item->mdate = date( 'r', strtotime( $item->mdate ) );
				$items[$k] = $item;
			}
		}
		
		return $items;
	}
	
	protected function leading_zeros($value, $places){

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
	
	function display_order(){
		
		$this->setLayout( 'order' );
		
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$mainframe = JFactory::getApplication();

		$state = $this->get( 'state' );
		$pago_config = Pago::get_instance( 'config' );
		
		$cid = JFactory::getApplication()->input->get( 'cid',  0, 'array' );
		$state->set( 'order_id', (int) $cid[0] );
		
		if( JFactory::getApplication()->input->get( 'task' ) != 'new' ) {
			JToolBarHelper::custom(
				'invoice',
				'invoice.png',
				'invoice.png',
				JText::_('Invoice'),
				false,
				false
			);
		}
		
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		
		$order = (array)$this->get('order');
		
		foreach( (array)$order['details'] as $k=>$v ){
			if( !strstr($k, '*') ){
				$order['basic'][ $k ] = $v;
			}
		}
		
		$order['details'] = $order['basic'];
		unset($order['basic']);
		//grab transaction tools from payment gateway plugin
		//$dispatcher = JDispatcher::getInstance();

		//JPluginHelper::importPlugin( 'pago_payment' );
		
		$order_tools = $this->get_order_tools( $order['details']['payment_gateway'], $order['details']['ipn_dump'] );
	
		
		//$dispatcher->trigger( $order['details']->payment_gateway . '_get_order_tools', array( &$order_tools, $order ) );
		
		$elements_xml = $cmp_path . 'views/orders/elements.xml';

		// PagoParameter Overrides JParameter render to put pago class names in html
		Pago::load_helpers( 'pagoparameter' );
		
		foreach( $order['items'] as $item ){
			unset($item->content);
			unset($item->description);
			unset($item->sub_payment_data);
		}

		$itemslist = json_encode( $order['items'] );
		
		$bind_data['details'] = (array)$order['details'];
		$bind_data['details']['itemslist'] = $itemslist;
		$bind_data['payment_gateways'] = (array)$order['details'];
		$bind_data['customer_note'] = (array)$order['details'];
		$bind_data['invoice'] = (array)$order['details'];
		$bind_data['shipping'] = (array)$order['details'];
		$bind_data['discounts'] = (array)$order['details'];
		$bind_data['grouplist'] = (array)$order['details'];
		$bind_data['attributes'] = (array)$order['details'];
		$bind_data['items'] = (array)$bind_data['details'];
		$bind_data['address_billing'] = (array)$order['addresses']['billing'];
		$bind_data['address_shipping'] = (array)$order['addresses']['shipping'];
		
		
		$params = new PagoParameter( $bind_data, $elements_xml );
		
		JForm::addfieldpath( array( $cmp_path . '/elements' ) );		
		
		$this->assign( 'order_tools', $order_tools );
		//
		//control / fieldset
		$this->assign( 'order_id', (int)$cid[0] );
		$this->assign( 'user_id', $order['details']['user_id'] );
		$this->assign( 'details', $params->render( 'details', 'details' ) );
		$this->assign( 'shipping', $params->render( 'shipping', 'shipping' ) );
		$this->assign( 'discounts', $params->render( 'discounts', 'discounts' ) );
		$this->assign( 'customer_note', $params->render( 'details', 'customer_note' ) );
		$this->assign( 'invoice', $params->render( 'details', 'invoice' ) );
		$this->assign( 'payment_gateways', $params->render( 'details', 'payment_gateways' ) );
		$this->assign( 'grouplist', $params->render( 'grouplist', 'grouplist' ) );
		$this->assign( 'attributes', $params->render( 'attributes', 'attributes' ) );
		$this->assign( 'items', $params->render( 'items', 'items' ) );
		$this->assign(
			'address_billing',
			$params->render( 'address_billing', 'address_billing' )
		);
		$this->assign(
			'address_shipping',
			$params->render( 'address_shipping', 'address_shipping' )
		);
		
		parent::display();
	}
	
	function get_order_tools( $gateway, $txn_id ){
		
		$tmpl_uri = JURI::base() . 'components/com_pago/views/orders/tmpl/';
		
		ob_start();

		include( dirname( __FILE__ ) . '/tmpl/order_tools.php' );
		
		$order_tools = ob_get_clean();
		
		return $order_tools;
	}
}
