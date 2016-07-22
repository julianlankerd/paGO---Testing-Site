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

class PagoViewPayoptions extends JViewLegacy
{
	/**
	 * Display the view
	 */

	public function display( $tpl = null )
	{
		Pago::load_helpers( 'pagoparameter' );
		
		$params = Pago::get_instance('params')->params;
		
		$active = $params->get('payoptions.active');
		$livemode = $params->get('payoptions.livemode');
		$recipient_live_id = $params->get('payoptions.live_recipient_id');
		$recipient_test_id = $params->get('payoptions.test_recipient_id');
		
		if($recipient_test_id) 
			$params = $this->set_params($params, $recipient_test_id);
			
		if($recipient_live_id) 
			$params = $this->set_params($params, $recipient_live_id, '_live');
			
		$params = $this->set_customer_params($params);
		
		$params->set('payoptions_livetoggle.active', @$active);
		$params->set('payoptions_livetoggle.livemode', @$livemode);
		
		$bind_data = [
			'params' => $params
		];
		
		$params = new PagoParameter( $bind_data,  dirname( __FILE__ ) . '/params.xml' );
		
		$this->assignRef('params', $params);
		$this->assign('paygates', $params->data['params']->get('paygates'));
		$this->assign('customer', $params->data['params']->get('customer'));

		parent::display($tpl);
	}
	
	function set_params($params, $recipient_id, $postfix=false){
		
		$payload = (object)[
			'id'=> $recipient_id,
			'livemode' => 0
		];
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$api = new PagoControllerApi;
		$recipient = $api->call('GET', 'recipients', $payload, false);
		
		$bank_account = @$recipient->bank_accounts->data[0];
		
		$bank_account_number = 'xxxxxxxx'.@$bank_account->last4;
		$legal_entity = @$recipient->legal_entity;
		$address = @$legal_entity->address;

		$dob = false;

		if($legal_entity){
			$dob = $legal_entity->dob;
			$dob = $dob->day . '-' . $dob->month . '-' . $dob->year;
		}
        
        $country = @$recipient->country;
        
        !$legal_entity->business_tax_id_provided  or  $legal_entity->business_tax_id = 'xxxxxxxxx';
        !$legal_entity->personal_id_number_provided  or  $legal_entity->personal_id_number = 'xxxxxxxxx';
           
		$params->set('payoptions'.$postfix.'.recipient_id', @$recipient_id);
		$params->set('payoptions_verification'.$postfix.'.legal_entity_type', @$legal_entity->type);
		$params->set('payoptions_verification'.$postfix.'.legal_entity_first_name', @$legal_entity->first_name);
		$params->set('payoptions_verification'.$postfix.'.legal_entity_last_name', @$legal_entity->last_name);
		$params->set('payoptions_verification'.$postfix.'.legal_entity_dob', @$dob);
		
		$params->set('payoptions_verification'.$postfix.'.legal_entity_address_line1', @$address->line1);
		$params->set('payoptions_verification'.$postfix.'.legal_entity_address_city', @$address->city);
		$params->set('payoptions_verification'.$postfix.'.legal_entity_address_state', @$address->state);
		$params->set('payoptions_verification'.$postfix.'.legal_entity_address_postal_code', @$address->postal_code);
		
		$params->set('payoptions_verification'.$postfix.'.'.$country.'_legal_entity_tax_id', @$legal_entity->tax_id);
		$params->set('payoptions_verification'.$postfix.'.'.$country.'_legal_entity_personal_id_number', @$legal_entity->personal_id_number);
		$params->set('payoptions_verification'.$postfix.'.'.$country.'_legal_entity_business_name', @$legal_entity->business_name);
		$params->set('payoptions_verification'.$postfix.'.'.$country.'_legal_entity_business_tax_id', @$legal_entity->business_tax_id);
				
		$params->set('payoptions'.$postfix.'.email', @$recipient->email);
		$params->set('payoptions'.$postfix.'.country', @$recipient->country);
		$params->set('payoptions_banking'.$postfix.'.bank_account_country', @$bank_account->country);
		$params->set('payoptions_banking'.$postfix.'.bank_account_currency', @$bank_account->currency);
		$params->set('payoptions_banking'.$postfix.'.bank_account_number', $bank_account_number);
		$params->set('payoptions_banking'.$postfix.'.bank_account_routing_number', @$bank_account->routing_number);
		
		return $params;
	}
	
	function set_customer_params($params){
		
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		$api = new PagoControllerApi;
		
		$customer_id = Pago::get_instance('params')->get('payoptions.customer_id');
		
		//paygates call
		$payload = (object)[
			'id'=> $customer_id . 'pago'
		];
		
		$paygates = $api->call('GET', 'payum', $payload, false);
		$paygates = @$paygates->_embedded->paygates;
		
		if(!is_array($paygates)) $paygates = [];
		
		
		foreach($paygates as $k=>$pg){
			
			Pago::get_instance('params')->set(
        		'paygates.'.$pg->id.'.data', $pg
        	);
        	
			$fields = (object)[];
			$fields->active = 'bool';
			$fields->testMode = 'bool';
			
			foreach($pg->fields as $name=>$value){
				$fields->$name = $value;
			}
			
			$pg->fields = $fields;
			
			foreach($pg->fields as $name=>$value){
				
				if(is_array($value)){
					foreach($value as $k1=>$val){
						if($val == Pago::get_instance('params')->get('paygates.'.$pg->id.'.'.$name)){
							$value[$k1] = '*'.$val;
						}
						$pg->fields->$name = $value;
					}
                } elseif($value == 'bool'){ 
                	$pg->fields->$name = (bool)Pago::get_instance('params')->get(
                		'paygates.'.$pg->id.'.'.$name, (bool)$value
                	);
                } else {
                	$pg->fields->$name = Pago::get_instance('params')->get(
                		'paygates.'.$pg->id.'.'.$name, (string)$value
                	);
                }
			}
                	
			$paygates[$k] = $pg;
		}
		
		$params->set('paygates', @$paygates);
		
		if($customer_id){
			//customer call
			$payload = (object)[
				'id'=> $customer_id
			];
			
			$customer = $api->call('GET', 'customers', $payload, false);
			
			$c = $customer;
			$cc = @$c->sources->data[0];
			$carray = [
				'id' => @$c->id,
				'name' => @$c->metadata->name,
				'email' => @$c->metadata->email,
				'phone' => @$c->metadata->phone,
			];
			
			if($cc){
				$carray = array_merge([
					'number' => 'xxxxxxxxxxxxx'.$cc->last4,
					'cvc' => 'xxx',
					'exp_month' => $cc->exp_month,
					'exp_year' => $cc->exp_year,
				],$carray);	
			}
		} else { $carray = []; }
		
		$customer = new JRegistry;
		$customer->loadArray($carray);
		
		$params->set('customer', $customer);
		
		return $params;
	}
}
