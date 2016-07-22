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

class PagoControllerPayoptions extends PagoController
{
	/**
	* Custom Constructor
	*/
	private $_view = 'payoptions';

	function __construct( $default = array() )
	{
		parent::__construct( $default );
	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}

	function store_recipient_id()
	{
		$input = JFactory::getApplication()->input;

		if(!$input->get( 'livemode' ) ) {
			Pago::get_instance( 'params' )->set(
				'payoptions.test_recipient_id',
				$input->get( 'recipient_id' )
			);
		} else {
			Pago::get_instance('params')->set(
				'payoptions.live_recipient_id',
				$input->get( 'recipient_id' )
			);
		}

		Pago::get_instance('params')->set(
			'payoptions.livemode',
			$input->get( 'livemode' )
		);

		Pago::get_instance('params')->set(
			'payoptions.active',
			$input->get( 'active' )
		);

		/*Pago::get_instance('params')->set(
			'payoptions.bank_account_id',
			$_POST['bank_account_id']
		);*/
		Pago::get_instance('params')->set(
			'payoptions.account_number',
			$input->get( 'account_number' )
		);

		exit(true);
	}

	function delete_recipient_id()
	{
		$input = JFactory::getApplication()->input;

		if(!$input->get( 'livemode' ) || ( $input->get( 'livemode' ) === 'undefined' ) ){
			Pago::get_instance('params')->set(
				'payoptions.test_recipient_id',
				''
			);
		} else {
			Pago::get_instance('params')->set(
				'payoptions.live_recipient_id',
				''
			);
		}

		Pago::get_instance('params')->set(
			'payoptions.recipient_id',
			''
		);

		exit('true');
	}

	function delete_customer_id()
	{
		exit(
			Pago::get_instance('params')->set(
				'payoptions.customer_id',
				''
			)
		);
	}

	function store_customer_id()
	{
		$input = JFactory::getApplication()->input;
		Pago::get_instance('params')->set(
			'payoptions.customer_id',
			$input->get( 'id' )
		);

		exit(true);
	}

	function save_pg_config()
	{
		$id = $_POST['id'];

		unset($_POST['id']);
		//print_r($_POST);
		foreach($_POST as $name=>$value){
			Pago::get_instance('params')->set(
				'paygates.'.$id.'.'.$name,
				$value
			);
		}

		exit(true);
	}
}
