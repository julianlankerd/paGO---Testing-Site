<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper' );
jimport( 'joomla.application.component.model' );


class PagoModelUser_fields extends JModelLegacy
{
    /**
     * Hellos data array
     *
     * @var array
     */
    var $_fields = null;
	var $_fields_values = null;
	var $_countries = null;
	var $_countries_states = null;

    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */

	function __construct()
	{
		parent::__construct();
    }

 	function get_fields()
    {
		if( $this->_fields ) return $this->_fields;

		$sql = "SELECT *
				FROM #__pago_userfield
				WHERE published = 1
				ORDER BY ordering";

		$this->_fields = $this->_getList( $sql );

		return $this->_fields;
	}

	function get_fields_values()
    {
		if( $this->_fields_values ) return $this->_fields_values;

		$sql = "SELECT *
				FROM #__pago_userfield_values
				ORDER BY ordering";

		$values = $this->_getList( $sql );

		foreach($values as $value){
			$this->_fields_values[ $value->fieldid ][ $value->fieldtitle ] = $value->fieldvalue;
		}

		return $this->_fields_values;
	}

	function get_countries( $key=false )
    {
		$sql = "SELECT *
				FROM #__pago_country where publish = 1";

		$values = $this->_getList( $sql );

		if( $key ){
			foreach($values as $value){
				$this->_countries[ $value->country_name ] = $value->country_id;
			}

			return $this->_countries;
		}

		$get_codes = false;

		if( $get_codes ){
			foreach($values as $value){
				$this->_countries[ $value->country_name ] = $value->country_name;
			}
		} else {
			foreach($values as $value){
				$this->_countries[ $value->country_name ] = $value->country_2_code;
			}
		}

		return $this->_countries;
	}

	function get_countries_states($countryCode = false)
    {
		if( $this->_countries_states ) return $this->_countries_states;

		$sql = "SELECT state.*, country.country_name, country.country_2_code
				FROM #__pago_country_state as state
					LEFT JOIN #__pago_country as country
						ON country.country_id = state.country_id WHERE state.publish = 1";

		if($countryCode){
			$countryId = $this->get_country_id($countryCode,"2code");				
			$sql .=	" AND state.country_id = {$countryId}";
		}

		$values = $this->_getList( $sql );
		foreach($values as $value){
			$this->_countries_states['attribs'][ $value->state_name ] = 'class="'.$value->country_2_code.'"';
			$this->_countries_states['options'][ $value->state_name ] = $value->state_name;
		}
		
		return $this->_countries_states;
	}

	function get_states( $country_2_code )
    {
		if( $this->_countries_states ) return $this->_countries_states;

		$sql = "SELECT state.*, country.country_name, country.country_2_code
				FROM #__pago_country_state as state
					LEFT JOIN #__pago_country as country
						ON country.country_id = state.country_id
							WHERE country.country_2_code = '{$country_2_code}' AND state.publish = 1";


		$this->_db->setQuery( $sql );

		$values = $this->_db->loadAssocList( 'state_2_code' );

		$return = array();

		foreach($values as $k=>$value){
			$return[ $k ] = $value['state_name'];
		}

		return $return;
	}

	function get_data(){

		$db = $this->_db;
		$user_id = $this->getState( 'user_id' );

		if ( $user_id ) {
			$sql = "
				SELECT 	userinfo.*,
						country.country_id, country.country_name, country.country_3_code, country.country_2_code, country.params AS country_params,
						state.state_id, state.state_name, state.state_3_code, state.state_2_code, state.params AS state_params
					FROM #__pago_user_info as userinfo
						LEFT JOIN #__pago_country as country
							ON country.country_2_code = userinfo.country
						LEFT JOIN #__pago_country_state as state
							ON state.state_name = userinfo.state
								AND country.country_id = state.country_id
					WHERE userinfo.user_id = $user_id
						ORDER BY userinfo.address_type DESC
			";

			$db->setQuery( $sql );

			$user_data = $db->loadObjectList();

			$sql = "
				SELECT * FROM #__pago_groups_users
					WHERE user_id = $user_id
			";

			$db->setQuery( $sql );

			$user_data['groups'] = $db->loadObjectList('group_id');

			return $user_data;
		}

		// get from session if no user_id for guest checkouts
		$session = JFactory::getSession();
		$user_data = $session->get('user_data', array(), 'pago_cart');

		return $user_data;
	}

	function store()
    {

		$db = $this->_db;
		$params = $this->getState( 'params' );
		$m_params = $this->getState( 'm_params' );
		$user_id = $this->getState( 'user_id' );
		$columns = false;
		$values = false;
		$set = false;

		foreach($params as $column=>$value){
			$set .="`$column`='$value',";
		}

		$m_set = $set;

		if(!empty($m_params)){
			$m_set = false;
			foreach($m_params as $column=>$value){
				$m_set .="`$column`='$value',";
			}
		}

		$set = substr( $set, 0, -1 );
		$m_set = substr( $m_set, 0, -1 );

		$id = $user_id . '_b';
		$db->setQuery("
			INSERT INTO #__pago_user_info
				SET `id`='$id',`address_type`='b',`user_id`=$user_id, $set
				ON DUPLICATE KEY UPDATE $set
		");

		$db->query();

		$id = $user_id . '_m';
		$db->setQuery("
			INSERT INTO #__pago_user_info
				SET `id`='$id',`address_type`='m',`user_id`=$user_id, $m_set
				ON DUPLICATE KEY UPDATE $m_set
		");

		$db->query();

		if( $db->getErrorMsg() ){
			JError::raiseWarning( 500, $db->getErrorMsg() );
			return false;
		}

		return true;
	}

	function get_country_id( $country, $type = 'name' )
	{
		$db = JFactory::getDBO();

		switch( $type ) {
			case 'name':
			default:
				$where = 'WHERE country_name = ' . $db->Quote( $country, false );
				break;
			case '2code':
				$where = 'WHERE country_2_code = ' . $db->Quote( $country, false );
				break;
			case '3code':
				$where = 'WHERE country_3_code = ' . $db->Quote( $country, false );
				break;
		}

		$query = 'SELECT country_id FROM #__pago_country ' . $where;
		$id = $db->setQuery( $query )->loadResult();
		if ( $id ) {
			return $id;
		} else {
			return false;
		}
	}

	function get_state_id ( $country_id, $state, $type = 'name' )
	{
		$db = JFactory::getDBO();

		switch( $type ) {
			case 'name':
			default:
				$where = 'WHERE state_name = ' . $db->Quote( $state, false );
				break;
			case '2code':
				$where = 'WHERE state_2_code = ' . $db->Quote( $state, false );
				break;
			case '3code':
				$where = 'WHERE state_3_code = ' . $db->Quote( $state, false );
				break;
		}

		$query = 'SELECT state_id FROM #__pago_country_state ' . $where . ' AND country_id = '
			. (int) $country_id;
		$id = $db->setQuery( $query )->loadResult();
		if ( $id ) {
			return $id;
		} else {
			return false;
		}
	}
}
