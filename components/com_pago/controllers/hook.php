<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.controller' );

class PagoControllerHook extends JControllerLegacy
{
    
    function display($cachable = false, $urlparams = array())
	{
		require_once( JPATH_SITE.'/components/com_pago/controllers/api.php' );
		
		$params = Pago::get_instance('params')->params;
		$livemode = $params->get('payoptions.livemode');
		$input = json_decode(file_get_contents("php://input"));
		$api = new PagoControllerApi;
		
		if(!is_object($input)) exit();
		
		//$event = $input;
		
		//this call is for security as we cannot trust the initial data
		//input from the wild. So we just take the event id and call our
		//api again to make sure the event is "real"
		$event = $api->call('GET', 'relay', (object)[
		    'id'=>$input->id, 
		    //'id'=>'evt_17nIP2EIuy0vVjzFMug3DmrE',
		    'user_id'=>$input->user_id, 
		    'livemode'=>$livemode
		], false);
		
		if(!isset($event->type)) exit();
		
		$dispatcher = KDispatcher::getInstance();
		
		JPluginHelper::importPlugin('pago_gateway');
		
		$dispatcher->trigger('pago.hook.'.$event->type, [$event]);
		
		exit('success');
	}
	
	function test(){
		
		//get paGO backend store configuration
		$store_cfg 	= Pago::get_instance('config')->get();
		$order 	= Pago::get_instance('orders')->get(190);
		$recipient = $order['addresses']['billing'];
		
		$recipient->name = $recipient->first_name . ' ' . $recipient->last_name;
		
		//send transactional emails to user and store email
		$result = Pago::get_instance('transaction_email')->set((object)[
			'recipients' => [(object)[
				'template' => 'email_update_order_status',
				'type' => 'site',
				'name' => $recipient->name,
				'email' => $recipient->user_email
			],(object)[
				'template' => 'email_update_order_status',
				'type' => 'admin',
				'name' => $store_cfg->get('general.pago_store_name'),
				'email' => $store_cfg->get('general.store_email')
			]],
			'data' => $order
		]);
		//->send();
		
		exit('asdf');
	}
}