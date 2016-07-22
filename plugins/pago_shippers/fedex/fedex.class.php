<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
/**
 * @package		Pago Shipping Plugin
 * @author 		'corePHP' LLC.
 * @copyright 	(C) 2010- 'corePHP' LLC.
 * @license 	GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Support: http://support.corephp.com/
 */

class fedex {
	private $_request;
	private $_response;
	private $_rateReply;

	public function __construct()
	{

		if (!extension_loaded('soap'))
		{
			throw new Exception(JText::_('PAGO_MISSING_SOAP') . ' FedEx.');
		}

		$this->_loadRequest();
	}

	public function __set($name, $var)
	{
		$this->_request [$name] = $var;
	}

	public function __get($key) {
		return $this->_request [$key];
	}

	/**
	 *
	 * Sets the required credentials for connecting
	 * @param array $cred
	 */
	public function setCredentials(array $cred) {
		$this->_request ['WebAuthenticationDetail'] ['UserCredential'] ['Key'] = $cred ['Key'];
		$this->_request ['WebAuthenticationDetail'] ['UserCredential'] ['Password'] = $cred ['Password'];
		$this->_request ['ClientDetail'] ['AccountNumber'] = $cred ['AccountNumber'];
		$this->_request ['ClientDetail'] ['MeterNumber'] = $cred ['MeterNumber'];
	}

	/**
	 *
	 * Enter description here ...
	 * @param array $serviceType
	 */
	public function getRates(array $serviceType = array(),$test_mode = 0) {
		$this->_sendGetRate ($test_mode);
		if(isset($this->_rateReply->error))
		{
			$rates = array ();
			$rates ['error'] = true;
			$rates ['error_message'] = $this->_rateReply->error_message;
			return  $rates;
		}


		$rates = $this->_parseRates ();

		if (! empty ( $serviceType )) {
			$ret = array ();
			foreach ( $rates as $rateServiceType => $rate ) {
				if (! in_array ( $rateServiceType, $serviceType )) {
					unset ( $rates [$rateServiceType] );
				}
			}
		}
		return $rates;
	}

	private function _parseRates() {
		$rates = array ();
		foreach ( $this->_rateReply as $rate ) {
			if (! array_key_exists ( $rate->ServiceType, $rates )) {
				$rates [$rate->ServiceType] = array ();
			}
			$rates [$rate->ServiceType] ['code'] = $rate->ServiceType;
			$rates [$rate->ServiceType] ['name'] = $this->_getServiceName ( $rate->ServiceType );
			@$rates [$rate->ServiceType] ['value'] += ( float ) $rate->RatedShipmentDetails [0]->ShipmentRateDetail->TotalNetCharge->Amount;
		}
		return $rates;
	}

	public function setShipper(array $address) {
		$this->_request ['RequestedShipment'] ['Shipper'] ['Address'] = $address;
	}

	public function setRecipient(array $address) {
		$this->_request ['RequestedShipment'] ['Recipient'] ['Address'] = $address;
	}

	/**
	 * Package needs to be a minimum of weight and weight unit
	 * ['Weight']['Value'] = '2.0';
	 * ['Weight']['Units'] = 'LB';
	 *
	 * @param array $package
	 */
	public function setPackage(array $package)
	{
		$package['Weight']['Value'] = intval($package['Weight']['Value']);
		$package['Weight']['Value'] = 0 < $package ['Weight'] ['Value'] ? $package ['Weight'] ['Value'] : 1;
		$this->_request ['RequestedShipment'] ['RequestedPackageLineItems'] [] = $package;
		$this->_request ['RequestedShipment'] ['PackageCount'] = count($this->_request ['RequestedShipment'] ['RequestedPackageLineItems']);
	}

	private function _loadRequest()
	{
		$this->_request ['TransactionDetail'] ['CustomerTransactionId'] = ' *** Rate Request v14 using PHP ***';
		$this->_request ['Version'] = array ('ServiceId' => 'crs', 'Major' => '14', 'Intermediate' => '0', 'Minor' => '0' );
		$this->_request ['ReturnTransitAndCommit'] = true;
		$this->_request ['RequestedShipment'] ['DropoffType'] = 'REGULAR_PICKUP';
		$this->_request ['RequestedShipment'] ['ShipTimestamp'] = date('c');
		//$this->_request ['RequestedShipment'] ['ServiceType'] = 'INTERNATIONAL_PRIORITY';
		$this->_request ['RequestedShipment'] ['PackagingType'] = 'YOUR_PACKAGING';
		$this->_request ['RequestedShipment'] ['TotalInsuredValue'] = array ('Ammount' => 0, 'Currency' => 'USD' );
		$this->_request ['RequestedShipment'] ['Shipper'] ['Address'] = array ();
		$this->_request ['RequestedShipment'] ['Recipient'] ['Address'] = array ();
		$this->_request ['RequestedShipment'] ['RateRequestTypes'] = 'LIST';
		$this->_request ['RequestedShipment'] ['PackageCount'] = '';
		$this->_request ['RequestedShipment'] ['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
		$this->_request ['RequestedShipment'] ['RequestedPackageLineItems'] = array ();
	}

	private function _sendGetRate($test_mode)
	{
		ini_set ( "soap.wsdl_cache_enabled", "0" );
		$pathToWsdl = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'RateService_v14.wsdl';
		if($test_mode)
		{
			$pathToWsdl = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'RateServiceTest_v14.wsdl';
		}



		$client = new SoapClient ( $pathToWsdl, array ('trace' => 1 ) );

		try {
			$this->_response = $client->getRates ( $this->_request );
		} catch ( SoapFault $e ) {
			$error_message =  'FEDEX had a SOAP fault. ' . $e->getMessage ();
			$this->_rateReply = new stdClass();
			$this->_rateReply->error_message = $error_message;
			$this->_rateReply->error = true;
		}

		if ($this->_response->HighestSeverity == 'ERROR' || $this->_response->HighestSeverity == 'FAILURE') {
			//JError::raiseNotice ( 'FEDEX3', 'FEDEX3: ' . $this->_response->Notifications->Message );
			if(count($this->_response->Notifications) > 1)
			{
				$error_message = $this->_response->Notifications[0]->Message;
			}
			else
			{
				$error_message = $this->_response->Notifications->Message;
			}

			$this->_rateReply = new stdClass();
			$this->_rateReply->error_message = $error_message;
			$this->_rateReply->error = true;


		} else {
			$this->_rateReply = $this->_response->RateReplyDetails;
		}
	}

	private function _getServiceName($code)
	{
		$services ['EUROPE_FIRST_INTERNATIONAL_PRIORITY'] = 'International First®';
		$services ['FEDEX_1_DAY_FREIGHT'] = 'FEDEX_1_DAY_FREIGHT';
		$services ['FEDEX_2_DAY'] = '2Day®';
		$services ['FEDEX_2_DAY_FREIGHT'] = 'FEDEX_2_DAY_FREIGHT';
		$services ['FEDEX_3_DAY_FREIGHT'] = 'FEDEX_3_DAY_FREIGHT';
		$services ['FEDEX_EXPRESS_SAVER'] = 'Express Saver®';
		$services ['FEDEX_FREIGHT'] = 'FEDEX_FREIGHT';
		$services ['FEDEX_GROUND'] = 'Ground';
		$services ['FEDEX_NATIONAL_FREIGHT'] = 'FEDEX_NATIONAL_FREIGHT';
		$services ['FIRST_OVERNIGHT'] = 'First Overnight®';
		$services ['GROUND_HOME_DELIVERY'] = 'Home Delivery®';
		$services ['INTERNATIONAL_ECONOMY'] = 'International Economy®';
		$services ['INTERNATIONAL_ECONOMY_FREIGHT'] = 'INTERNATIONAL_ECONOMY_FREIGHT';
		$services ['INTERNATIONAL_FIRST'] = 'International First';
		$services ['INTERNATIONAL_GROUND'] = 'International Ground';
		$services ['INTERNATIONAL_PRIORITY'] = 'International Priority';
		$services ['INTERNATIONAL_PRIORITY_FREIGHT'] = 'INTERNATIONAL_PRIORITY_FREIGHT';
		$services ['PRIORITY_OVERNIGHT'] = 'Priority Overnight®';
		$services ['SMART_POST'] = 'SmartPost®';
		$services ['STANDARD_OVERNIGHT'] = 'Standard Overnight®';

		if (! array_key_exists ( $code, $services )) {
			return $code;
		}

		return $services [$code];
	}

}