<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Pago  Helper
 *
 * @package Joomla
 * @subpackage Pago
 * @since 1.5
 */
class PagoUpgrader
{
	/**
	 * Create config database
	 *
	 * @since 1.0
	 **/
	static public function create_config()
	{
		$db = JFactory::getDBO();
		$query = "
		CREATE TABLE IF NOT EXISTS `#__pago_config` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`modified` datetime NOT NULL,
			`modified_by` int(11) unsigned NOT NULL DEFAULT '0',
			`name` varchar(255) NOT NULL,
			`params` text NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery( $query );
		$db->query();

		$datenow = &JFactory::getDate();
		$db_time = $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
		$query = "INSERT INTO #__pago_config
			( `modified`, `name`, `params`)
			VALUES
			( '{$db_time}', 'dbversion', '1000');";
		$db->setQuery( $query );
		$db->query();
	}

	/**
	 * Move the `params` to the items_meta table
	 **/
	static public function upgrade_1035()
	{
		$db = JFactory::getDBO();
		$meta = Pago::get_instance( 'meta' );

		$query = "SELECT `id`, `params`
			FROM #__pago_items
				WHERE `params` != ''";
		$db->setQuery( $query );
		$list = $db->loadObjectList();

		foreach ( $list as $row ) {
			$params = new JParameter( $row->params );

			foreach ( $params->_registry['_default']['data'] as $key => $value ) {
				$meta->add( 'items', $row->id, $key, $value, true );
			}

			$query = "UPDATE #__pago_items SET `params` = '' WHERE `id` = {$row->id}";
			$db->setQuery( $query );
			$db->query();
		}
	}

	/**
	 * Move the metatags to the items_meta table
	 **/
	static public function upgrade_1039()
	{
		$db = JFactory::getDBO();
		$meta = Pago::get_instance( 'meta' );

		$query = "SELECT `id`, `meta_html_title`, `meta_tag_title`, `meta_tag_author`,
			`meta_tag_robots`, `meta_tag_keywords`, `meta_tag_description`
				FROM #__pago_items";
		$db->setQuery( $query );
		$list = $db->loadObjectList();

		foreach ( $list as $row ) {
			if ( !empty( $row->meta_html_title ) ) {
				$meta->add( 'items', $row->id, 'meta_html_title', $row->meta_html_title, true );
			}

			if ( !empty( $row->meta_tag_title ) ) {
				$meta->add( 'items', $row->id, 'meta_tag_title', $row->meta_tag_title, true );
			}

			if ( !empty( $row->meta_tag_author ) ) {
				$meta->add( 'items', $row->id, 'meta_tag_author', $row->meta_tag_author, true );
			}

			if ( !empty( $row->meta_tag_robots ) ) {
				$meta->add( 'items', $row->id, 'meta_tag_robots', $row->meta_tag_robots, true );
			}

			if ( !empty( $row->meta_tag_keywords ) ) {
				$meta->add( 'items', $row->id, 'meta_tag_keywords', $row->meta_tag_keywords, true );
			}

			if ( !empty( $row->meta_tag_description ) ) {
				$meta->add( 'items', $row->id, 'meta_tag_description', $row->meta_tag_description,
					true );
			}
		}
	}

	/**
	 * Import old categories to new categories table
	 */
	static public function upgrade_1057()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * from #__pago_categories ORDER BY path, n_order';
		$db->setQuery( $query );
		$old_categories = $db->loadObjectList();

		JTable::addIncludePath( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_pago'.DIRECTORY_SEPARATOR.'tables');
		$table = JTable::getInstance( 'categoriesi', 'Table' );
		$old_ids = array();

		foreach ( $old_categories as $cat ) {
			if ( $cat->path == '.' ) {
				continue; // don't need root
			}
			$path = explode( '.', $cat->path );

			if ( $path[ count($path) - 2 ] == 1 ) {
				$parent_id = 1;
			} else {
				$parent_id = $old_ids[$path[ count($path) - 2 ]];
			}

			$data['id'] = 0;
			$data['parent_id'] = $parent_id;
			$data['name'] = $cat->name;
			$data['description'] = $cat->description;
			$data['alias'] = $cat->alias;
			$data['meta_html_title'] = $cat->meta_html_title;
			$data['meta_tag_title'] = $cat->meta_tag_title;
			$data['meta_tag_author'] = $cat->meta_tag_author;
			$data['meta_tag_robots'] = $cat->meta_tag_robots;
			$data['meta_tag_keywords'] = $cat->meta_tag_keywords;
			$data['meta_tag_description'] = $cat->meta_tag_description;
			$data['published'] = $cat->published;

			$table->setLocation( $data['parent_id'], 'last-child');

			if ( !$table->bind( $data ) ) {
				return JError::raiseWarning( 20, $table->getError() );
			}
			$table->create_alias();

			if ( !$table->check() ) {
				return JError::raiseWarning( 20, $table->getError() );
			}

			if ( !$table->store() ) {
				return JError::raiseWarning( 20, $table->getError() );
			}

			if ( !$table->rebuildPath( $table->id ) ) {
				return JError::raiseWarning( 20, $table->getError() );
			}

			if ( !$table->rebuild( $table->id, $table->lft, $table->level, $table->path ) ) {
				return JError::raiseWarning( 20, $table->getError() );
			}

			$old_ids[$cat->id] = $table->id;

			$update_items = 'UPDATE #__pago_items SET primary_category = ' . $table->id .
				' WHERE primary_category = ' . $cat->id;
			$update_cat_items = 'UPDATE #__pago_items SET category_id = ' . $table->id .
				' WHERE category_id = ' . $cat->id;
			$db->setQuery( $update_items );
			$db->query();
			$db->setQuery( $update_cat_items );
			$db->query();

			$table->get_item_count();

			$table->reset();
		}
	}

	/**
	 * Move all info from item_data table to item
	 */
	static public function upgrade_1065()
	{
		$db = JFactory::getDBO();

		$limit = 100;
		$limit_increment = 100;
		$limit_offset = 0;
		$update_query = array();

		// limit the number we do at a time incase there are a lot of records
		set_time_limit(0);
		$query = 'SELECT COUNT(*) FROM #__pago_items_data';
		$db->setQuery( $query );
		$count = $db->loadResult();

		for( $i = 0; $i <= $count; $i += $limit_increment ) {
			$query = 'SELECT * FROM #__pago_items_data LIMIT ' . $limit . ' offset '
				. $limit_offset;
			$db->setQuery( $query );
			$items_data = $db->loadObjectList();

			foreach( $items_data as $data ) {
				$update_query[] = 'UPDATE #__pago_items SET `content` = \'' . $data->content
					. '\', `unit_of_measure` = \'' . $data->unit_of_measure . '\', `height` = \''
					. $data->height . '\', `width` = \'' . $data->width .'\', `length` = \''
					. $data->length . '\', `weight` = \'' . $data->weight . '\' WHERE id = '
					. $data->item_id;
			}
			$db->setQuery( implode( ';', $update_query ) );
			$db->queryBatch();

			$update_query = array();
			$limit += $limit_increment;
		}

		flush();
	}

	/***
	 * Migrate all meta data to new meta_data table
	 */
	static public function upgrade_1074()
	{
		$db = JFactory::getDBO();

		$query = 'SELECT * from #__pago_items_meta order by items_id';

		$db->setQuery( $query );
		$items = $db->loadAssocList();

		$insert_sql = array();

		$temp_items = array();
		foreach ( $items as $item ) {
			$temp_items[$item['items_id']][$item['meta_key']] = $item['meta_value'];
		}

		foreach ( $temp_items as $k => $item ) {
			$insert_sql[] = "('".$k."','item','".$item['meta_html_title']."','".
				$item['meta_tag_title']."','".$item['meta_tag_author']."','".
				$item['meta_tag_robots']."','".$item['meta_tag_keywords']."','".
				$item['meta_tag_description']."')";
		}

		$query = 'SELECT `id`, `meta_html_title`, `meta_tag_title`, `meta_tag_author`,'.
			' `meta_tag_robots`, `meta_tag_keywords`, `meta_tag_description` '.
			'FROM #__pago_categoriesi';

		$db->setQuery( $query );

		$cats = $db->loadAssocList();

		foreach( $cats as $cat ) {
			$insert_sql[] = "('".$cat['id']."','category','".$cat['meta_html_title']."','".
				$cat['meta_tag_title']."','".$cat['meta_tag_author']."','".
				$cat['meta_tag_robots']."','".$cat['meta_tag_keywords']."','".
				$cat['meta_tag_description']."')";
		}

		$insert_query = 'INSERT INTO #__pago_meta_data '.
			'(`id`, `type`, `html_title`, `title`, `author`, `robots`, `keywords`, `description` )'.
			' VALUES '. implode( ',', $insert_sql );

		$db->setQuery( $insert_query );
		$db->query();
	}

	/**
	 * Hopefully this is only temporary
	 */
	static public function find_upgrades( $db_version )
	{
		jimport( 'joomla.filesystem.file' );
		$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'sql';

		$db = JFactory::getDBO();
		$no_more = false;
		$version_check = intval( $db_version ) + 1;

		while ( false == $no_more ) {
			$_file = $path .DIRECTORY_SEPARATOR. "pago_{$version_check}.sql";
			if ( file_exists( $_file ) ) {
				$contents = JFile::read( $_file );

				if ( method_exists( 'PagoUpgrader', 'upgrade_' . $version_check ) ) {
					$_method = 'upgrade_' . $version_check;
					PagoUpgrader::$_method();
				}

				$queries = $db->splitSql( $contents );
				foreach ( (array) $queries as $query ) {
					$db->setQuery( $query );
					$db->query();
				}

				$version_check++;
			} else {
				$no_more = true;
			}
		}

		return $version_check - 1;
	}
}

?>
