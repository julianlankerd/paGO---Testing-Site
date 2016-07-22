<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of states.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoModelCurrencies extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'item.id',
				'name', 'item.name',
				'code', 'item.code',
				'sybmol', 'item.sybmol',
				'published', 'item.published',
				'default', 'item.default',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	public function getItems($published = true){
		$items = $this->getListQuery($published);
		return $items;
	}
	protected function getListQuery($published = true)
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		
		$query->from( '`#__pago_currency` AS item' );
		

		if(!$published){
			$query->where( "published = 1" );
		}

		$query->order($db->escape('`default` DESC,`id` ASC'));
		$db->setQuery($query);

		$items = $db->loadObjectList();

		return $items;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Currencies', $prefix = 'PagoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */

	public function delete($id){
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->delete($db->quoteName('#__pago_currency'));
		$query->where("`id` = {$id} AND `default` != 1");
		
		$db->setQuery($query);	
		
		$db->execute();
		
		return $id;
	}
	function makeDefault() {
		$id = JFactory::getApplication()->input->get( 'id', '', '' );

		$row = $this->getTable( 'currency', 'Table' );
		$row->load( $id );

        if ( !$row->id ) {
			$this->setError( JText::_( 'Select an currency to make it a default' ) );
			return false;
		}

		$row->default = 1;

		if ( !$row->store() ) {
			$this->setError( $row->getError() );
			return false;
		}

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query = "UPDATE #__pago_currency
			SET `default` = 0
				WHERE `id` != {$row->id}";
		$db->setQuery( $query );
		$db->query();

		// Write global config settings file
		PagoHelper::writeDefaultSettings();

		return true;
	}
	function store( $data = array() )
	{
		$db		= $this->getDbo();
		$row = $this->getTable( 'currency', 'Table' );

		if(isset($data['id'])){
			$row->load( $data['id'] );
		}
		if ( !$row->bind( $data ) ) {
			$this->setError( $row->getError() );
			return false;
		}
		if ( !$row->check() ) {
			$this->setError( $row->getError() );
			return false;
		}
		if ( !$row->store() ) {
			$this->setError( $row->getError() );
			return false;
		}

		return $row->id;
	}
	function getDefault(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__pago_currency where #__pago_currency.default = 1";
		$db->setQuery($sql);
		$currency = $db->loadObject();
		//var_dump($db);
		//die();
		return $currency;
	}
	function getCurrencyById($id){
		$db = JFactory::getDBO();
		$id = (int)$id;

		$sql = "SELECT * FROM #__pago_currency where `id` = ".$id;

		$db->setQuery($sql);
		$currency = $db->loadObject();
		
		return $currency;
	}
	function getCurrenciesFromXml(){
		$currenciesFile = file_get_contents('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml', true);
		$currenciesFileByArray = explode("currency='", $currenciesFile);
		$currencies = array();
		$currencies['EUR'] = 1;
		foreach ($currenciesFileByArray as $key => $value) {
			if($key == 0){
				continue;
			}
			$currencyArray = explode("'", $value);
			$currencies[$currencyArray[0]] = $currencyArray[2];	
		}
		return $currencies;
	}
	function getCurrenciesCodeFromXml()
	{
		$xml = simplexml_load_file( 'http://www.tm-xml.org/TM-XML/TM-XML_xml/ISOCurrencyCode.xml' );

		$currencies = array();
		foreach( $xml->Currency as $key => $value ) {
			$currency_key = (string)$value->CurrencyCode[0];
			$currency_name = (string)$value->CurrencyName[0];

			$currencies[$currency_key] = $currency_key;
		}

		return $currencies;
	}
	function getCurrenciesCource(){
		$db = JFactory::getDBO();
		$data = date("Y-m-d");

		$sql = "SELECT * FROM #__pago_currency_cource where `date` = '".$data."'";
		
		$db->setQuery($sql);
		$cource = $db->loadObject();

		if(!$cource){
			$cource = $this->getCurrenciesFromXml();
			$serialize = serialize($cource);
			
			$query = "INSERT INTO #__pago_currency_cource ( `date`, `cource` )".
				"VALUES ('{$data}', '{$serialize}')";
			$db->setQuery( $query );
			$db->query();
		}else{
			$cource = unserialize($cource->cource);
		}		
		return $cource;		
	}
}