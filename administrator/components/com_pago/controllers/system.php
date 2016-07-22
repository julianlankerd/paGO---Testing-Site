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

class PagoControllerSystem extends PagoController
{
	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );
		$this->registerTask( 'apply', 'save' );
	}
	function save()
	{
		$error = 1;
		
		$this->save_params();
		
		// save currencies
		$model = $this->getModel( 'currencies' );
		$currencies = JFactory::getApplication()->input->get( 'currencies', array(), 'array' );

		foreach ( $currencies as $id => $currency ) {
			// Prepare variables for later
			$currency['id'] = $id;

			if ( $id = $model->store( $currency ) ) {
				$msg = JText::_( 'PAGO_SUCCESS_SAVED' );
			} else {
				$error = JText::_( 'An error has occurred: '.$model->getError() );
			}
		}

		// save item types
		$model = $this->getModel( 'item_types' );
		$itemTypes = JFactory::getApplication()->input->get( 'itemTypes', array(), 'array' );

		foreach ( $itemTypes as $id => $itemType ) {
			// Prepare variables for later
			$itemType['id'] = $id;

			if ( $id = $model->store( $itemType ) ) {
				$msg = JText::_( 'PAGO_SUCCESS_SAVED' );
			} else {
				$error = JText::_( 'An error has occurred: '.$model->getError() );
			}
		}

		if($error != 1){
			$msg = $error;
		}
		$link 	= 'index.php?option=com_pago&view=system';

		$this->setRedirect($link, $msg);
	}
	
	function save_params()
	{
		$paramGroup = JFactory::getApplication()->input->get( 'params', [], 'array' );
		
		foreach($paramGroup as $groupName=>$params){
			foreach($params as $name=>$value){
				Pago::get_instance('params')->set(
					$groupName.'.'.$name, 
					$value
				);
			}
		}
	}
}
