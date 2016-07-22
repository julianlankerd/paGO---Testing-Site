<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class PagoViewOrders extends JViewLegacy
{
	function display()
	{
		
		/*//Load Gateway Plugin
		JPluginHelper::importPlugin( 'pago_gateway', 'gateway' );
			   
		//Set manifest array
		$manifest = array();
			   
		JDispatcher::getInstance()
		   ->trigger( 'kg_load_manifest', array( &$manifest, 943 ) );
		
		print_r( $manifest );
		
		die();*/
		
		$gateway = JFactory::getApplication()->input->get( 'gateway' );
		$operation = JFactory::getApplication()->input->get( 'task' );
		$txn_id = JFactory::getApplication()->input->get( 'txn_id' );
		$sub_operation = JFactory::getApplication()->input->get( 'operation' );
		
		//Load plugin that houses KCommerce, the kc_initialise
		//event can be found here
		JPluginHelper::importPlugin( 'pago_gateway', 'gateway' );
		
		//trigger the initialise event, we use array_pop
		//because the trigger method returns an array
		//and since we are only expecting one response
		//all's cool
		$KGateway = array_pop( 
			//message the initialise event also passing the selected pgate
			JDispatcher::getInstance()->trigger( 'kg_initialise', array( 'operation', $gateway ) ) 
		)
		//note: object chaining ahead!
		//here we add an object to implement the KCommerce Observer layer
		->add_observer( Pago::get_instance( 'gateway_observer' ) )
		//now we set the checkout manifest
		->process_operation( $txn_id, $operation, $sub_operation );
		
		echo( $KGateway->last_response );
		
		return;
		
		
		/*$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin( 'pago_payment' );
		
		$dispatcher->trigger( JRequest::getVar( 'gateway' ) . '_controller' );*/
	}
}
