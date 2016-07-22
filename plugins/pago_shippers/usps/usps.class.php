<?php
defined('_JEXEC') or die ('Restricted access');
/**
 * @package		Pago Shipping Plugin
 * @author 		'corePHP' LLC.
 * @copyright 	(C) 2010- 'corePHP' LLC.
 * @license 	GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Support: http://support.corephp.com/
 */

class usps
{
	private $_request_xml = array ();

	private $_userId = '';
	
	private $_rateReply = '';


	public function __construct()
	{
		if (!extension_loaded('curl'))
		{
			throw new Exception(JText::_('PAGO_MISSING_CURL') . ' USPS.');
		}
	}
	
	public function get($property) {
		return json_decode(json_encode(simplexml_load_string($this->$property)));
	}
	
	/**
	 *
	 * Sets the required credentials for connecting
	 * @param array $cred
	 */
	public function setCredentials(array $cred)
	{
		$this->_userId = $cred ['userName'];
	}

	/**
	 *
	 * You can either get all the service type prices or pick an array of them.
	 * The service types must be the same values as what the shipper is returning
	 * @param array $serviceType
	 */
	public function getRates(array $serviceType = array(), $InternationalShiping)
	{
		$this->_sendGetRate($InternationalShiping);
		if(isset($this->_rateReply->error) && $this->_rateReply->error)
		{
			$rates = array ();
			$rates ['error'] = true;
			$rates ['error_message'] = $this->_rateReply->error_message;
			return  $rates;
		}


		$rates = $this->_parseRates($this->_rateReply,$InternationalShiping);

		if (empty($serviceType))
		{
			$ret = $rates;
		}
		else
		{
			$ret = array ();

			foreach ($rates as $k => $rate)
			{
				if (!in_array($rate ['matchname'], $serviceType))
				{
					unset ( $rates [$k] );
				}
			}
		}

		return $rates;
	}

	/**
	 *
	 * Returns an array like [serviceType] = price
	 * @param string $ret
	 */
	private function _parseRates($ret, $InternationalShiping)
	{
		$xml = @simplexml_load_string($ret);

		if (!is_a($xml, 'SimpleXMLElement'))
		{
			throw new Exception(JText::_('Bad XML from USPS.'));
		}

		if ($InternationalShiping)
		{
			$rates = array();
			foreach ( $xml as $package )
			{
				$services = $package->Service;
				$rate_price = 0;
				foreach ($services as $service)
				{
						$code = (string) $service->attributes()->ID;
						$name = str_replace("&lt;sup&gt;&amp;reg;&lt;/sup&gt;", "", (string) $service->SvcDescription);
						$name = str_replace("&lt;sup&gt;&amp;trade;&lt;/sup&gt;", "", $name);
						$name = str_replace("&lt;sup&gt;&#174;&lt;/sup&gt;", "", $name);
						$name = str_replace("&lt;sup&gt;&#8482;&lt;/sup&gt;", "", $name);

						if (!array_key_exists($code, $rates))
						{
							$rates [(string) $service->attributes()->ID] = array();
						}

						$rates [$code] ['code'] = $code;
						$rates [$code] ['matchname'] = $name;
						$rates [$code] ['name'] = $name;
						$rate_price += (float) $service->Postage;
						@$rates [$code] ['value'] = $rate_price;
				}
			}
		}
		else
		{
			$rates = array ();
			foreach ( $xml as $package )
			{
				foreach ( $package->Postage as $postage )
				{
					$code = (string) $postage->attributes()->CLASSID;
					$name = str_replace("&lt;sup&gt;&amp;trade;&lt;/sup&gt;", "", (string) $postage->MailService);
					$name = str_replace("&lt;sup&gt;&#174;&lt;/sup&gt;", "" , $name); // July 2013
					$name = str_replace("&lt;sup&gt;&#8482;&lt;/sup&gt;", "", $name); // July 2013

					$rates [$code] ['name'] = $name;
					$name = str_replace(" 1-Day", "", $name); // July 2013
					$name = str_replace(" 2-Day", "", $name); // July 2013
					$name = str_replace(" 3-Day", "", $name); // July 2013
					$name = str_replace(" Military", "", $name); // July 2013
					$name = str_replace(" DPO", "", $name); // July 2013

					if (!array_key_exists($code, $rates))
					{
						$rates [(string) $postage->attributes()->CLASSID] = array();
					}

					$rates [$code] ['code'] = $code;
					$rates [$code] ['matchname'] = $name;
					@$rates [$code] ['value'] += (float) $postage->Rate [0];
				}
			}
		}

		return $rates;
	}

	// Creates a new package xml
	// We cannot build without ZipOrigination and ZipDestination
	public function setPackage(array $package)
	{

		if ($package['ZipOrigination'] == "" || $package['ZipDestination'] == "")
		{
			throw new Exception(JText::_('You must have a source and destination zip.'));
		}

		$xml = new SimpleXMLElement('<Package></Package>');
		$xml->addChild('Service', $package['Service']);
		$xml->addChild('ZipOrigination', $package['ZipOrigination']);
		$xml->addChild('ZipDestination', $package['ZipDestination']);
		$xml->addChild('Pounds', $package['Pounds']);
		$xml->addChild('Ounces', $package['Ounces']);
		$xml->addChild('Container', $package['Container']);
		$xml->addChild('Size', $package['Size']);
		$xml->addChild('Width', $package['Width']);
		$xml->addChild('Length', $package['Length']);
		$xml->addChild('Height', $package['Height']);
		$xml->addChild('Girth', $package['Girth']);
		$xml->addChild('Machinable', $package['Machinable']);
		$this->_request_xml [] = $xml;
	}

	public function setIntlPackage(array $package)
	{
		if ($package['Country'] == "" )
		{
			throw new Exception(JText::_('You must have a destination Country.'));
		}

		$xml = new SimpleXMLElement('<Package></Package>');
		$xml->addChild('Pounds', $package['Pounds']);
		$xml->addChild('Ounces', $package['Ounces']);
		$xml->addChild('Machinable', $package['Machinable']);
		$xml->addChild('MailType', $package['MailType']);
		$xml->addChild('ValueOfContents', $package['ValueOfContents']);
		$xml->addChild('Country', $package['Country']);
		$xml->addChild('Container', $package['Container']);
		$xml->addChild('Size', $package['Size']);
		$xml->addChild('Width', $package['Width']);
		$xml->addChild('Length', $package['Length']);
		$xml->addChild('Height', $package['Height']);
		$xml->addChild('Girth', $package['Girth']);
		$this->_request_xml [] = $xml;
	}

	private function _sendGetRate($InternationalShiping)
	{
		$this->_buildRequest($InternationalShiping);
		$ch = curl_init("http://production.shippingapis.com/ShippingAPI.dll");

		if ($InternationalShiping)
		{
			$postData = "API=IntlRateV2&XML=" . str_replace("\n", '', $this->_request);
		}
		else
		{
			$postData = "API=RateV4&XML=" . str_replace("\n", '', $this->_request);
		}

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		$this->_rateReply = curl_exec($ch);

		// Connection error to USPS
		if (0 < curl_errno($ch))
		{
			$error_message = JText::_('We have a connection problem with USPS: ' . curl_error($ch));
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

	private function _buildRequest($InternationalShiping)
	{
		$i = 1;
		$doc = new DOMDocument('1.0');

		if ($InternationalShiping)
		{
			$doc->loadXML("<IntlRateV2Request USERID='{$this->_userId}'></IntlRateV2Request>");
		}
		else
		{
			$doc->loadXML("<RateV4Request USERID='{$this->_userId}'></RateV4Request>");
		}

		foreach ($this->_request_xml as $package)
		{
			$package->addAttribute('ID', $i++);
			$domnode = dom_import_simplexml($package);
			$domnode = $doc->importNode($domnode, true);
			$doc->firstChild->appendChild($domnode);
		}

		$this->_request = $doc->saveXML();
	}
}
