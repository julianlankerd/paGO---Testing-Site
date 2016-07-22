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

class PagoControllerCurrencies extends PagoController
{
	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );
		$this->registerTask( 'default', 'makeDefault' );
		$this->registerTask( 'unpublish', 'publish' );
	}
	function delete(){
		$id = JFactory::getApplication()->input->get( 'id', '', '' );
		$model     = JModelLegacy::getInstance('currencies', 'PagoModel');
		$result = $model->delete($id);
		echo $result;
		die();
	}
	function makeDefault()
	{
		$model = $this->getModel( 'currencies' );

		if ( $model->makeDefault() ) {
			$msg = 1;
		} else {
			$msg = JText::_( 'An error has occurred: '.$model->getError() );
		}

		echo $msg;
		jexit();
	}
	function publish()
	{
		$db  = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get( 'id', '', '' );

		$publish = ( $this->getTask() == 'publish' ? 1 : 0 );


		$query = 'UPDATE #__pago_currency SET published = ' . (int) $publish
			. ' WHERE id =  ' .$id. ' ';
		$db->setQuery( $query );

		if ( !$db->query() ) {
			JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
		}

		$query	= $db->getQuery(true);
		// Instantiate an article table object
		$query->select(' * ');
		$query->from( '`#__pago_currency` AS item' );
		$query->where("`id` = {$id}");
		$db->setQuery($query);

		$row = $db->loadObject();

		echo PagoHelper::published( $row, 0, 'tick.png',  'publish_x.png',
			'', ' class="publish-buttons" type="currency" rel="' .$row->id. '"' );
		jexit();
	}
	function add(){
		$data['name'] = JFactory::getApplication()->input->get( 'currencyName', '', '' );
		$data['code'] = JFactory::getApplication()->input->get( 'currencyCode', '', '' );
		$data['symbol'] = JFactory::getApplication()->input->get( 'currencySymbol', '', '' );

		$model = $this->getModel( 'currencies' );
		$id = $model->store($data);
		$return['error'] = 0;
		if($id){
			$return['id'] = $id;
		}else{
			$return['error'] = 1;
			$return['message'] = $model->getError();
		}
		echo json_encode($return);
		exit();
	}
	function getCurrencies(){
		$currentCurrenciesJson = JFactory::getApplication()->input->get( 'currentCurrenciesJson', '', '' );
		$currentCurrenciesArray = array();
		if(count($currentCurrenciesJson) > 0){
			$currentCurrencies = json_decode($currentCurrenciesJson);
		}
		$model = $this->getModel( 'currencies' );
		$currencies = $model->getCurrenciesCodeFromXml();
		foreach ($currencies as $key => $currency) {
			if (in_array($key, $currentCurrencies)) {
				unset($currencies[$key]);
			}
		}
		ksort( $currencies );
		echo json_encode($currencies);
		exit();
	}
}
