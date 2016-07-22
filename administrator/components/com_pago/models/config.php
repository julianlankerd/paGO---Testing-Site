<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');

class PagoModelConfig extends JModelLegacy
{
	protected $config;

	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

	/**
	 * Fetch config data from database
	 *
	 * Passing string to namespace will get config for said namespace default is global namespace
	 * Passing null to group will pull all groups for namespace or else passing a group name will
	 * get the namespace/group config options
	 * Passing true to $force_reload will force it to get most current config from database default
	 * is false and to pull from cache if available
	 *
	 * @params $namespace string default global
	 * @params $group string default null
	 * @params $force_reload boolean default false
	 * @return $config object
	 */
	public function get_config( $namespace = 'global', $group = null, $force_reload = false )
	{
		if ( isset( $this->config[$namespace] ) && !$force_reload ) {
			return $this->config[$namespace];
		}

		$this->config[$namespace] = array();

		$where = ' `namespace` = ' . $this->_db->Quote( $namespace, false);
		if ( $group !== null ) {
			$where .= ' AND `group` = ' . $this->_db->Quote($group, false);
		}
		$query = 'SELECT * FROM #__pago_params WHERE ' . $where;
		$this->_db->setQuery( $query );
		$results = $this->_db->loadAssocList();

		// if empty result just return an empty array
		if( empty( $results ) ) {
			return array();
		}

		foreach ( $results as $result ) {
			if ( $result['serialized'] ) {
				if ( !isset( $this->config[$namespace][$result['group']] ) ) {
					$this->config[$namespace][$result['group']] = new stdClass;
				}
				$this->config[$namespace][$result['group']]->{$result['name']} =
					unserialize( $result['value'] );

				continue;
			}

			if ( !isset( $this->config[$namespace][$result['group']] ) ) {
				$this->config[$namespace][$result['group']] = new stdClass;
			}
			$this->config[$namespace][$result['group']]->{$result['name']} = $result['value'];
		}

		if ( $group !== null ) {
			return $this->config[$namespace][$group];
		}
		return $this->config[$namespace];
	}

	/**
	 * Save config to database
	 *
	 * Saves config to database in key -> value pairs
	 * serializes objects and arrays that are values before saving
	 *
	 * @params $config mixed array|object
	 * @params $namespace string default global
	 * @params $group boolean default true
	 * @return boolean true|false
	 */
	public function save_config( $config, $namespace = 'global', $group = true )
	{
		$set_stmt = array();
		foreach ( $config as $name=>$value ) {
			if ( $group && is_array( $value ) ) {
				foreach ( $value as $oname => $ovalue ) {
					if ($oname == 'image_sizes')
					{
						$imagesizes = array();

						foreach ($ovalue as $isizeName => $isize)
						{

							$find = array("'",'"','.','/','?','`',"\\"," ");
							$isizeName=str_replace($find,"",$isizeName);

							$imagesizes[$isizeName]['width'] = $isize['width'];
							$imagesizes[$isizeName]['height'] = $isize['height'];
							$imagesizes[$isizeName]['crop'] = $isize['crop'];
						}

						$set_stmt[] = '( ' . $this->_db->Quote($oname, true) . ', ' .
						$this->serialize_data($imagesizes ) .
						$this->_db->Quote($namespace, true) . ', '.
						$this->_db->Quote( $name, true ) . ')';
					}
					else
					{
						$set_stmt[] = '( '. $this->_db->Quote($oname, true).', '.
						$this->serialize_data( $ovalue ) .
						$this->_db->Quote($namespace, true) . ', '.
						$this->_db->Quote( $name, true ) . ')';
					}
				}
				continue;
			}

			$set_stmt[] = '( '. $this->_db->Quote( $name, true ) .', '.
				$this->serialize_data( $value ) .
				$this->_db->Quote( $namespace, true) .', \'\')';
		}
		// insert or update if name and namespace is currently in the DB
		$query = 'INSERT INTO #__pago_params '.
			'(`name`, `value`, `serialized`, `namespace`, `group`) VALUES '.
			implode( ',',  $set_stmt ) . ' ON DUPLICATE KEY UPDATE `value` = VALUES(value),'.
			' `serialized` = VALUES(serialized)';
		$this->_db->setQuery( $query );

		// if legacy error handling is active
		if ( JError::$legacy ) {
			if ( !$this->_db->query() ) {
				return false;
			}
		} else {
			// just let joomla catch the exception if there is one
			$this->_db->query();
		}

		return true;
	}

	private function serialize_data( $value )
	{
		if ( is_object( $value ) || is_array( $value ) ) {
			return $this->_db->Quote( serialize($value), true ).', 1, ';
		} else {
			return $this->_db->Quote($value, true) .', 0, ';
		}
	}

	function getDeafultImage()
	{
		$ImageName = "";
		$query = "SELECT *
			FROM #__pago_files
				WHERE `type` = 'store_default'
				 LIMIT 1";
		$this->_db->setQuery( $query );
		$default = $this->_db->loadObject();
		if(count($default) > 0)
		{
			$ImageName  = $default->file_name;
		}
		return $ImageName;
	}
}
