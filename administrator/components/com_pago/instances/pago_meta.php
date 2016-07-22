<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_meta
{
	private $types = array( 'item', 'category' );

	private $meta_keys = array(
		'html_title',
		'title',
		'author',
		'robots',
		'keywords',
		'description'
	);

	/**
	 * Add metadata for the specified object.
	 *
	 * @param string Type of object metadata is for (e.g. items, categories)
	 * @param int ID of the object metadata is for
	 * @param string Metadata key
	 * @param string Metadata value
	 * @return bool True on successful update, false on failure.
	 */
	public function add( $meta_type, $object_id, $meta_key, $meta_value )
	{
		if ( !$this->verify_type_key( $meta_type, $meta_key ) ) {
			return false;
		}
		if ( !$object_id = abs( intval( $object_id ) ) ) {
			return false;
		}

		if ( !$table = $this->_get_meta_table( $meta_type ) ) {
			return false;
		}

		$db    = JFactory::getDBO();
		$cache = Pago::get_instance( 'cache' );

		$_meta_value = $meta_value;

		$query = "INSERT INTO #__pago_{$table}
			( `id`, `type`, `{$meta_key}` )
			VALUES
			( $object_id, " .$db->Quote( $meta_type ). ", " .$db->Quote( $meta_value ). " )";
		$db->setQuery( $query );
		$db->query();

		$cache->delete( $object_id, $meta_type . '_meta' );

		$dispatcher = KDispatcher::getInstance();
		$dispatcher->do_action(
			"added_{$meta_type}_meta",
			array(
				$object_id,
				$meta_key,
				$_meta_value
			)
		);

		return true;
	}

	/**
	 * Update metadata for the specified object.  If there is no value for the object
	 * ID and metadata key, the metadata will be added.
	 *
	 * @param string Type of object metadata is for (e.g. items, categories)
	 * @param int ID of the object metadata is for
	 * @param string Metadata key
	 * @param string Metadata value
	 * @param string Optional.  If specified, only update existing metadata entries with
	 * 		the specified value. Otherwise, update all entries.
	 * @return bool True on successful update, false on failure.
	 */
	public function update( $meta_type, $object_id, $meta_key, $meta_value, $prev_value = '' )
	{
		if ( !$this->verify_type_key( $meta_type, $meta_key ) ) {
			return false;
		}

		if ( !$object_id = abs( intval( $object_id ) ) ) {
			return false;
		}

		if ( !$table = $this->_get_meta_table( $meta_type ) ) {
			return false;
		}

		$db    = JFactory::getDBO();
		$cache = Pago::get_instance( 'cache' );

		// Check to see if object already exists
		$query = "SELECT 1
			FROM #__pago_{$table}
				WHERE `id` = {$object_id} AND `type` = '{$meta_type}'";
		$db->setQuery( $query );
		if ( !$meta_id = $db->loadResult() ) {
			return $this->add( $meta_type, $object_id, $meta_key, $meta_value );
		}

		// Compare existing value to new value if no prev value given and the key exists only once.
		if ( empty( $prev_value ) ) {
			$old_value = $this->get( $meta_type, $object_id, $meta_key );
			if ( count( $old_value ) == 1 ) {
				if ( isset( $old_value[0] ) ) {
					if ( $old_value[0] === $meta_value )
						return false;
				}
			}
		}

		$query = "UPDATE #__pago_{$table}
			SET `{$meta_key}` = " .$db->Quote( $meta_value ). "
				WHERE `id` = {$object_id} AND `type` = '{$meta_type}'";

		$db->setQuery( $query );
		$db->query();

		$cache->delete( $object_id, $meta_type . '_meta' );

		$dispatcher = KDispatcher::getInstance();
		$dispatcher->do_action(
			"updated_{$meta_type}_meta",
			array(
				$object_id,
				$meta_key,
				$meta_value
			)
		);

		return true;
	}

	/**
	 * Delete metadata for the specified object.
	 *
	 * @param string Type of object metadata is for (e.g. items, categories)
	 * @param int ID of the object metadata is for
	 * @param string Metadata key
	 * @return bool True on successful delete, false on failure.
	 */
	public function delete( $meta_type, $object_id, $meta_key )
	{
		if ( !$this->verify_type_key( $meta_type, $meta_key ) ) {
			return false;
		}

		if ( !$object_id = abs( intval( $object_id ) ) ) {
			return false;
		}

		if ( !$table = $this->_get_meta_table( $meta_type ) ) {
			return false;
		}

		$db    = JFactory::getDBO();
		$cache = Pago::get_instance( 'cache' );

		$query = "UPDATE #__pago_{$table} SET `{$meta_key}` = ''
			WHERE `id` = {$object_id} AND `type` = '{$meta_type}'";
		$db->setQuery( $query );
		$db->query();
		$count = $db->getAffectedRows();

		if ( !$count ) {
			return false;
		}

		$cache->delete( $object_id, $meta_type . '_meta' );

		$dispatcher = KDispatcher::getInstance();
		$dispatcher->do_action(
			"deleted_{$meta_type}_meta",
			array(
				$meta_ids,
				$object_id,
				$meta_key,
				$meta_value
			)
		);

		return true;
	}

	/**
	 * Get the metadata for a specified object
	 *
	 * @param string Type of object metadata is for (e.g. items, categories)
	 * @param int ID of the object metadata is for
	 * @param string Metadata key. If not specified, retrieve all metadata for
	 * 		the specified object.
	 * @return string|array Single metadata value, or array of values
	 */
	public function get( $meta_type, $object_id, $meta_key = '', $single = false )
	{
		if ( !$this->verify_type_key( $meta_type, $meta_key ) ) {
			return false;
		}

		if ( !$object_id = abs( intval( $object_id ) ) ) {
			return false;
		}

		$cache = Pago::get_instance( 'cache' );

		$meta_cache = $cache->get( $object_id, $meta_type . '_meta' );

		if ( empty($meta_cache) ) {
			$this->update_meta_cache( $meta_type, $object_id );
			$meta_cache = $cache->get( $object_id, $meta_type . '_meta' );
		}

		if ( !$meta_key ) {
			return $meta_cache;
		}

		if ( isset( $meta_cache[$meta_key] ) ) {
			return $meta_cache[$meta_key];
		}

		return '';
	}

	/**
	 * Update the metadata cache for the specified objects.
	 *
	 * @param string Type of object metadata is for (e.g. items, categories)
	 * @param int|array Array or integer of object ID(s) to update cache for
	 * @return mixed Metadata cache for the specified objects, or false on failure.
	 */
	public function update_meta_cache( $meta_type, $object_ids )
	{
		if ( empty( $meta_type ) || empty( $object_ids ) ) {
			return false;
		}

		if ( !$table = $this->_get_meta_table( $meta_type ) ) {
			return false;
		}

		$db    = JFactory::getDBO();
		$cache = Pago::get_instance( 'cache' );

		// Get id column
		$column = $db->escape( $meta_type . '_id' );

		if ( !is_array( $object_ids ) ) {
			$object_ids = array( $object_ids );
		}

		JArrayHelper::toInteger( $object_ids, array( 0 ) );

		$cache_key = $meta_type . '_meta';
		$ids = array();
		foreach ( $object_ids as $id ) {
			if ( false === $cache->get( $id, $cache_key ) ) {
				$ids[] = $id;
			}
		}

		if ( empty( $ids ) ) {
			return false;
		}

		// Get meta info
		$_cache = array();
		$query = "SELECT *
			FROM #__pago_{$table}
				WHERE `id` IN ( " .join( ',', $ids ). " ) AND `type` = '{$meta_type}'";
		$db->setQuery( $query );
		$meta_list = $db->loadAssocList();

		if ( !empty( $meta_list ) ) {
			foreach ( $meta_list as $metarow ) {
				$mpid = (int) $metarow['id'];

				foreach( $this->meta_keys as $mkey ) {
					$_cache[$mpid][$mkey] = $metarow[$mkey];
				}
			}
		}

		foreach ( $ids as $id ) {
			if ( !isset( $_cache[$id] ) ) {
				$_cache[$id] = array();
			}
		}

		foreach ( array_keys( $_cache ) as $object ) {
			$cache->set( $object, $_cache[$object], $cache_key );
		}

		return $cache;
	}

	/**
	 * Retrieve the name of the metadata table for the specified type.
	 * There are no checks to ensure that the table exists, it is just assumed
	 *
	 * @param string $meta_type Type of object to get the meta table for
	 * @return string Metadata table name, or false if no type
	 */
	private function _get_meta_table( $type )
	{
		if ( !in_array( $type, $this->types ) ) {
			return false;
		}

		return 'meta_data';
	}

	/**
	 * Verify that meta_type and meta_key exists in arrays of valid inputs
	 *
	 * @param string $meta_type Type of object to get the meta data for
	 * @param string $meta_key Key to get/insert value of
	 * @return bool False if not True if verified
	 */
	private function verify_type_key( $type, $key )
	{
		if ( !in_array( (string)$type, $this->types ) ||
			 !in_array( (string)$key, $this->meta_keys ) ) {
			return false;
		}

		return true;
	}
}
