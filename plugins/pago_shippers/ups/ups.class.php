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

class ups
{
	private $_packages = array ();
	private $_request = '';
	private $_accessReq = '';
	private $_rateReply = '';

	public function __construct() {

		if (! extension_loaded('curl'))
		{
			throw new Exception('UPS1', JText::_('PAGO_MISSING_CURL') . ' UPS.');
		}

		$this->_loadRequest();
	}
	
	public function get($property) {
		return json_decode(json_encode($this->$property));
	}
	
	/**
	 *
	 * Creates the main xml request
	 */

	private function _loadRequest()
	{
		$this->_request = new SimpleXMLElement('<RatingServiceSelectionRequest></RatingServiceSelectionRequest>');
		$this->_request->addChild('Shipment');
		$this->_request->addChild('PickupType');
		$this->_request->PickupType->addChild('Code', '01');
		$request = $this->_request->addChild('Request');
		$request->addChild('RequestAction', 'Rate');
		$request->addChild('RequestOption', 'shop');
	}

	/**
	 *
	 * Sets the required credentials for connecting
	 * @param array $cred
	 */

	public function setCredentials(array $cred)
	{
		$accessReq = new SimpleXMLElement('<AccessRequest></AccessRequest>');
		$accessReq->addChild('AccessLicenseNumber', $cred ['AccessLicenseNumber']);
		$accessReq->addChild('UserId', $cred ['UserId']);
		$accessReq->addChild('Password', $cred ['Password']);
		$this->_accessReq = $accessReq;
		$this->_testmode = $cred ['testmode'];
	}

	/**
	 *
	 * You can either get all the service type prices or pick an array of them.
	 * The service types must be the same values as what the shipper is returning
	 * @param array $serviceType
	 */

	public function getRates(array $serviceType = array())
	{
		$this->_sendGetRate();
		$rates = $this->_parseRates();

		if (! empty($serviceType))
		{
			foreach ( $rates as $rateServiceType => $v )
			{
				if (! in_array($rateServiceType, $serviceType))
				{
					unset ( $rates [$rateServiceType] );
				}
			}
		}

		return $rates;

	}

	/**
	 *
	 * Returns an array like [serviceType] = price
	 * @param  $rates
	 */

	private function _parseRates()
	{
		$rates = array ();
		$this->_rateReply = simplexml_load_string($this->_rateReply);

		if (0 == $this->_rateReply->Response->ResponseStatusCode)
		{
			$error_message = $this->_rateReply->Response->ResponseStatusDescription;
			$rates = array ();
			$rates ['error'] = true;
			$rates ['error_message'] = $error_message;
			return  $rates;
		}

		foreach ( $this->_rateReply->RatedShipment as $shipment )
		{
			$rates [(string) $shipment->Service->Code] ['code'] = (string) $shipment->Service->Code;
			$rates [(string) $shipment->Service->Code] ['name'] = $this->_getServiceName((string) $shipment->Service->Code); // Ups doesn't send it's common name
			$rates [(string) $shipment->Service->Code] ['value'] = (float) $shipment->TransportationCharges->MonetaryValue;
		}

		return $rates;

	}

	public function setShipper(array $address)
	{
		$shipAdd = $this->_request->Shipment->addChild('Shipper')->addChild('Address');
		$shipAdd->addChild('PostalCode', $address ['PostalCode']);
		$shipAdd->addChild('CountryCode', $address ['CountryCode']);
	}

	public function setRecipient(array $address)
	{
		$shipAdd = $this->_request->Shipment->addChild('ShipTo')->addChild('Address');
		$shipAdd->addChild('PostalCode', $address['PostalCode']);
		$shipAdd->addChild('CountryCode', $address ['CountryCode']);
		$shipAdd->addChild('ResidentialAddressIndicator', '');
	}

	// Creates a new package xml

	public function setPackage(array $package)
	{

		$package ['Weight'] ['Value'] = intval($package ['Weight'] ['Value']);
		$package ['Weight'] ['Value'] = 0 < $package ['Weight'] ['Value'] ? $package ['Weight'] ['Value'] : 1;
		$shipment = $this->_request->Shipment;
		$xmlPackage = $shipment->addChild('Package');
		$xmlPackage->addChild('PackagingType')->addChild('Code', '02');

		// Dimensions
		$packageDimension = $xmlPackage->addChild('Dimensions');
		$DimensionUnitOfMeasurement = $packageDimension->addChild('UnitOfMeasurement');
		$DimensionUnitOfMeasurement->addChild('code', $package ['Dimensions'] ['Units']);
		$packageDimension->addChild('Width', $package ['Dimensions'] ['Width']);
		$packageDimension->addChild('Height', $package ['Dimensions'] ['Height']);
		$packageDimension->addChild('Length', $package ['Dimensions'] ['Length']);

		// End

		// PackageWeight
		$packageWeight = $xmlPackage->addChild('PackageWeight');
		$UnitOfMeasurement = $packageWeight->addChild('UnitOfMeasurement');
		$UnitOfMeasurement->addChild('code', $package ['Weight'] ['Units']);
		$packageWeight->addChild('Weight', $package ['Weight'] ['Value']);

		// End
	}

	private function _sendGetRate()
	{
		$this->_buildRequest();

		if ($this->_testmode)
		{
			$ch = curl_init("https://www.ups.com/ups.app/xml/Rate");
		}
		else
		{
			$ch = curl_init("https://www.ups.com/ups.app/xml/Rate");
		}

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_request);
		$this->_rateReply = curl_exec($ch);

		// Connection error to USPS

		if (0 < curl_errno($ch))
		{
			$error_message = JText::_('We have a connection problem with UPS: ' . curl_error($ch));
			$this->_rateReply->error_message = $error_message;
			$this->_rateReply->error = true;
		}

		// USPS didn't know what we sent to them

		if (200 !== curl_getinfo($ch, CURLINFO_HTTP_CODE))
		{
			$error_message =  $this->_rateReply;
			$this->_rateReply = new stdClass();
			$this->_rateReply->error_message = $error_message;
			$this->_rateReply->error = true;
		}
	}

	private function _buildRequest()
	{
		$this->_request = $this->_accessReq->asXML() . $this->_request->asXML();
	}

	private function _getServiceName($code)
	{
		$services ['14'] = 'Next Day Air® Early A.M. SM';
		$services ['01'] = 'Next Day Air®';
		$services ['65'] = 'Saver';
		$services ['59'] = 'Second Day Air A.M.®';
		$services ['02'] = 'Second Day Air®';
		$services ['12'] = 'Three-Day Select®';
		$services ['13'] = 'Next Day Air Saver®';
		$services ['03'] = 'Ground';
		$services ['07'] = 'Worldwide ExpressSM';
		$services ['08'] = 'Worldwide ExpeditedSM';
		$services ['11'] = 'Standard';
		$services ['54'] = 'Worldwide Express PlusSM';
		$services ['13'] = 'Saver SM';

		if (! array_key_exists($code, $services))
		{

			return 'Unknown Service Name';
		}

		return $services [$code];
	}
}