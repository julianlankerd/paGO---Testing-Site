<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

/**
 * Pago Cache
 *
 * This class provides methods for caching data for the time that the page loads,
 * all data is gone after PHP finishes. For storing file caches, use JCache
 *
 * The class is used to save trips to the database, stores all of the cache data in memory
 */
class pago_cache
{
	/**
	 * Holds the cached objects
	 */
	var $cache = array();

	/**
	 * Adds data to the cache if it doesn't already exist.
	 *
	 * @param int|string The id of the cache
	 * @param mixed The data to store in the cache
	 * @param string Where to group the cache contents
	 * @return bool False if cache ID and group already exists, true on success
	 */
	function add( $id, $data, $group = 'default' )
	{
		if ( empty ( $group ) ) {
			$group = 'default';
		}

		// If already exists return
		if ( false !== $this->get( $id, $group ) ) {
			return false;
		}

		return $this->set($id, $data, $group );
	}

	/**
	 * Remove the contents of the cache if it exists, use $force = true to always unset
	 *
	 * @param int|string The id of the cache
	 * @param string Where to group the cache contents
	 * @param bool Optional. Whether to force the unsetting of the cache
	 *		ID in the group
	 * @return bool False if the contents weren't deleted and true on success
	 */
	function delete( $id, $group = 'default', $force = false )
	{
		if ( empty( $group ) ) {
			$group = 'default';
		}

		// If doesn't exists and not force
		if ( !$force && false === $this->get( $id, $group ) ) {
			return false;
		}

		unset( $this->cache[$group][$id] );

		return true;
	}

	/**
	 * Clears the whole cache
	 *
	 * @return bool Always returns true
	 */
	function flush()
	{
		$this->cache = array();

		return true;
	}

	/**
	 * Returns the content of the cache if it exists
	 *
	 * @param int|string The id of the cache
	 * @param string Where to group the cache contents
	 * @return bool|mixed False on failure to retrieve contents or the cache
	 *		contents on success
	 */
	function get( $id, $group = 'default' )
	{
		if ( empty( $group ) ) {
			$group = 'default';
		}

		if ( isset( $this->cache[$group][$id] ) ) {
			if ( is_object( $this->cache[$group][$id] ) ) {
				return clone( $this->cache[$group][$id] );
			} else {
				return $this->cache[$group][$id];
			}
		}

		return false;
	}

	/**
	 * Replace the contents in the cache, if contents already exist
	 *
	 * @param int|string The id of the cache
	 * @param mixed The data to store in the cache
	 * @param string Where to group the cache contents
	 * @return bool False if not exists, true if contents were replaced
	 */
	function replace( $id, $data, $group = 'default' )
	{
		if ( empty( $group ) ) {
			$group = 'default';
		}

		// Can't replace what doesn't exist
		if ( false === $this->get( $id, $group ) ) {
			return false;
		}

		return $this->set( $id, $data, $group );
	}

	/**
	 * Set the data for a cache id
	 *
	 * The caches are grouped by the $group parameter and the $id. Group names should
	 * be chosen carefully to avoid conflicts, mostly by plugins outside of the Pago core.
	 *
	 * @param int|string The id of the cache
	 * @param mixed The data to store in the cache
	 * @param string Where to group the cache contents
	 * @return bool Always returns true
	 */
	function set( $id, $data, $group = 'default' )
	{
		if ( empty( $group ) ) {
			$group = 'default';
		}

		if ( null === $data ) {
			$data = '';
		}

		if ( is_object( $data ) ) {
			$data = clone( $data );
		}

		$this->cache[$group][$id] = $data;

		return true;
	}
}
