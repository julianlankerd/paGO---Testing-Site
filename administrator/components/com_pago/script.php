<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
* Script file of pago component
*/
class com_pagoInstallerScript
{
	/**
	* method to install the component
	*
	* @return void
	*/
	function install( $parent )
	{
		$mlds_path = $parent->getParent()->getPath('source') . "/libraries/";

		if (JFolder::exists($mlds_path . 'pago'))
		{
			$librariesPath = defined('JPATH_LIBRARIES') ? JPATH_LIBRARIES : JPATH_PLATFORM;
			JFolder::copy($mlds_path . 'pago', $librariesPath . DIRECTORY_SEPARATOR . 'pago', '', true);
			JFolder::delete($mlds_path . 'pago');
		}
	}

	/**
	* method to uninstall the component
	*
	* @return void
	*/
	function uninstall( $parent )
	{
		jimport('joomla.filesystem.file');

		$db 		= JFactory::getDbo();
		$installer 	= new JInstaller();
		$assets 	= json_decode(
			JFile::read( JPATH_ROOT . '/components/com_pago/assets.json' )
		);

		$list = false;

		$cfg = new JConfig();

		$config = Pago::get_instance('config')->get();
		$keep_db = $config->get('general.keep_drop_db_tables');

		if($keep_db != '1')
		{
			$db->setQuery( "SHOW TABLES LIKE '{$cfg->dbprefix}pago_%'" );
			$tables = $db->loadAssocList();

			foreach ( $tables as $table ) {
				$list .= '`' . array_pop( $table ) . '`, ';
			}

			$list = substr_replace( $list ,'' ,-2 );

			if ( !$list ) return false;

			$db->setQuery( "DROP TABLE {$list};" );
			$db->execute();
		}else{
			rename ( JPATH_ROOT . '/media/pago/' , JPATH_ROOT . '/media/pago_backup/' );
			mkdir ( JPATH_ROOT . '/media/pago/');
		}

		//delete menus
		if(is_array($parent->get( 'manifest' )->menus->menu)){
			foreach ( $parent->get( 'manifest' )->menus->menu as $menu ) {
				//$menu_type = $menu->getAttribute( 'menutype' );
				$menu_type = (string)$menu->attributes()->menutype;
				if ( $menu_type ) {
					$db->setQuery( "DELETE FROM #__menu WHERE menutype='{$menu_type}'" );
					$db->query();

					$db->setQuery( "DELETE FROM #__menu_types WHERE menutype='{$menu_type}'" );
					$db->query();
				}
			}
		}

		if ( !empty( $assets ) ) {
			foreach ( $assets as $asset ) {
				$query = $db->getQuery( true );

				$query->select( 'extension_id' )
						->from( '#__extensions' )
							->where( 'type=' . $db->quote( $asset->type ) )
							->where( 'element=' . $db->quote( $asset->element ) )
							->where( 'name=' . $db->quote( $asset->name ) );

				$db->setQuery( $query );

				$id = $db->loadResult();

				$installer->uninstall( $asset->type, $id );
			}
		}
	}

	/**
	* method to update the component
	*
	* @return void
	*/
	function update( $parent )
	{
	}

	/**
	* method to run before an install/update/uninstall method
	*
	* @return void
	*/
	function preflight( $type, $parent )
	{
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight( $type, $parent )
	{
		$assets = array();

		if ( in_array( $type, array( 'install', 'update' ) ) ) {
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			jimport('joomla.installer.installer');

			if ( !$this->install_modules( $type, $parent, $assets ) ) {
				JError::raiseWarning( 21, JText::_( 'COM_PAGO_ERROR_INSTALLING_MODULES' ) );
			}

			//Remove all paGO module instances
			if ( $type != 'update' ) {
				$db = JFactory::getDBO();
				$query = 'DELETE FROM #__modules WHERE `module` LIKE "%mod_pago%" and client_id=0';
				$db->setQuery( $query );
				if ( !$db->query() ) {
					JError::raiseWarning( 1, JText::_( 'COM_PAGO_ERROR_REMOVING_BLANK_MODULES' ) );
					return false;
				}
			}

			if ( !$this->install_plugins( $type, $parent, $assets ) ) {
				JError::raiseWarning( 21, JText::_( 'COM_PAGO_ERROR_INSTALLING_PLUGINS' ) );
			}
		}

		// We save the modules and plugins installed so we can uninstall them if required
		$assets_file = JPATH_ROOT . '/components/com_pago/assets.json';
		$assets_encoded = json_encode( $assets );
		JFile::write( $assets_file,  $assets_encoded);

		if ( $type != 'update' ) {
			$this->setup_modules( $parent );
			$this->setup_menus( $parent );
			$this->activate_modules();
		}
		// Upload default Image
		/*$default_image_path = JPATH_ROOT . '/components/com_pago/images/noimage.jpg';
		require_once( JPATH_ROOT.'/administrator/components/com_pago/helpers/helper.php' );
		$PagoHelper = new PagoHelper();
		$PagoHelper->uplaodDefaultImage($default_image_path); */
		echo JTEXT::_("COM_PAGO_INSTALL_SUCCESS");
	}

	function install_modules( $type, $parent, &$assets )
	{
		// Get an installer instance
		$installer = new JInstaller(); // Cannot use the instance that is already created, no!
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$mlds_path = $parent->getParent()->getPath('source') . '/admin/extensions/modules';
		$returns = array();

		if ( !JFolder::exists( $mlds_path ) ) {
			return true;
		}

		// Loop through modules
		$modules = JFolder::folders( $mlds_path );
		foreach ( $modules as $module ) {
			$m_dir = $mlds_path .'/'. $module .'/';

			// Install the package
			if ( !$installer->install( $m_dir ) ) {
				// There was an error installing the package
				JError::raiseWarning( 21, JTEXT::sprintf(
					'COM_PAGO_MODULE_INSTALL_ERROR', $module ) );
				$returns[] = false;
			} else {
				// Package installed sucessfully
				//$app->enqueueMessage( JTEXT::sprintf('COM_PAGO_MODULE_INSTALL_SUCCESS', $module ) );
				$returns[] = true;

				$manifest = $installer->manifest;
				$ext_type = (string) $manifest->attributes()->type;

				if ( count ( $manifest->files->children() ) ) {
					foreach ( $manifest->files->children() as $file ) {
						if ( $file->attributes()->$ext_type ) {
							$element = (string) $file->attributes()->$ext_type;
							break;
						}
					}
				}

				$assets[] = array(
					'name' => (string) $manifest->name,
					'type' => $ext_type,
					'element' => $element
				);
			}
		}

		return !in_array( false, $returns, true );
	}

	function install_plugins( $type, $parent, &$assets )
	{
		// Get an installer instance
		$installer = new JInstaller(); // Cannot use the instance that is already created, no!
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$plgs_path = $parent->getParent()->getPath('source') . '/admin/extensions/plugins';
		$returns = array();
		$enable = array();
		$auto_enable = array(
			'pago_migration/pago_migration', 'pago_gateway/pago', 'system/pago'
		);

		if ( !JFolder::exists( $plgs_path ) ) {
			return true;
		}

		// Loop through plugin types
		$plg_types = JFolder::folders( $plgs_path );
		foreach ( $plg_types as $plg_type ) {
			if($plg_type == "pago_migration"){
				$plugin = $plg_type;
				$p_dir = $plgs_path .'/'. $plg_type .'/'. $plugin .'/';

				// Install the package
				if ( !$installer->install( $p_dir ) ) {
					// There was an error installing the package
					JError::raiseWarning( 21, JTEXT::sprintf(
						'COM_PAGO_PLUGIN_INSTALL_ERROR', $plg_type) );
					$returns[] = false;
				} else {
					// Package installed sucessfully
					//$app->enqueueMessage( JTEXT::sprintf('COM_PAGO_PLUGIN_INSTALL_SUCCESS', $plg_type . '/' . $plugin ) );
					$returns[] = true;

					$manifest = $installer->manifest;
					$ext_type = (string) $manifest->attributes()->type;

					if ( count ( $manifest->files->children() ) ) {
						foreach ( $manifest->files->children() as $file ) {
							if ( $file->attributes()->$ext_type ) {
								$element = (string) $file->attributes()->$ext_type;
								break;
							}
						}
					}

					$assets[] = array(
						'name' => (string) $manifest->name,
						'type' => $ext_type,
						'element' => $element
					);

					// Maybe auto enable?
					if ( 'install' == $type && in_array( $plg_type.'/'.$plugin, $auto_enable ) ) {
						$enable[] = "(`folder` = '{$plg_type}' AND `element` = '{$plugin}')";
					}
				}

			}else{
				// Loop through plugins
				$plugins = JFolder::folders( $plgs_path .'/'. $plg_type );
				foreach ( $plugins as $plugin ) {
					$p_dir = $plgs_path .'/'. $plg_type .'/'. $plugin .'/';

					// Install the package
					if ( !$installer->install( $p_dir ) ) {
						// There was an error installing the package
						JError::raiseWarning( 21, JTEXT::sprintf(
							'COM_PAGO_PLUGIN_INSTALL_ERROR', $plg_type . '/' . $plugin ) );
						$returns[] = false;
					} else {
						// Package installed sucessfully
						//$app->enqueueMessage( JTEXT::sprintf('COM_PAGO_PLUGIN_INSTALL_SUCCESS', $plg_type . '/' . $plugin ) );
						$returns[] = true;

						$manifest = $installer->manifest;
						$ext_type = (string) $manifest->attributes()->type;

						if ( count ( $manifest->files->children() ) ) {
							foreach ( $manifest->files->children() as $file ) {
								if ( $file->attributes()->$ext_type ) {
									$element = (string) $file->attributes()->$ext_type;
									break;
								}
							}
						}

						$assets[] = array(
							'name' => (string) $manifest->name,
							'type' => $ext_type,
							'element' => $element
						);

						// Maybe auto enable?
						if ( 'install' == $type && in_array( $plg_type.'/'.$plugin, $auto_enable ) ) {
							$enable[] = "(`folder` = '{$plg_type}' AND `element` = '{$plugin}')";
						}
					}
				}
			}
		}

		// Run query
		if ( !empty( $enable ) ) {
			$db->setQuery( "UPDATE #__extensions
				SET `enabled` = 1
					WHERE ( " . implode( ' OR ', $enable ) . " ) AND `type` = 'plugin'" );

			if ( !$db->query() ) {
				JError::raiseWarning( 1, JText::_( 'COM_PAGO_ERROR_ENABLING_PLUGINS' ) );
				return false;
			}
		}

		return !in_array( false, $returns, true );
	}

	private function setup_modules( $parent )
	{
		foreach ( $parent->get( 'manifest' )->modules->module as $module ) {

			$table = JTable::getInstance( 'Module', 'JTable' );

			// $bind_data = array(
			// 	'title' => $module->getAttribute( 'title' ),
			// 	'note' => $module->getAttribute( 'note' ),
			// 	'content' => '',
			// 	'ordering' => 1,
			// 	'position' => $module->getAttribute( 'position' ),
			// 	'checked_out' => 0,
			// 	'checked_out_time' => '0000-00-00 00:00:00',
			// 	'publish_up' => '0000-00-00 00:00:00',
			// 	'publish_down' => '0000-00-00 00:00:00',
			// 	'published' => 1,
			// 	'module' => $module->getAttribute( 'module' ),
			// 	'access' => 1,
			// 	'showtitle' => 1,
			// 	'params' => '',
			// 	'client_id' => $module->getAttribute( 'client' ),
			// 	'language' => '*',
			// );
			$bind_data = array(
				'title' => (string)$module->attributes()->title,//getAttribute( 'title' ),
				'note' => (string)$module->attributes()->note,
				'content' => '',
				'ordering' => 1,
				'position' => (string)$module->attributes()->position,
				'checked_out' => 0,
				'checked_out_time' => '0000-00-00 00:00:00',
				'publish_up' => '0000-00-00 00:00:00',
				'publish_down' => '0000-00-00 00:00:00',
				'published' => 1,
				'module' => (string)$module->attributes()->module,
				'access' => 1,
				'showtitle' => 1,
				'params' => '',
				'client_id' => (string)$module->attributes()->client,
				'language' => '*',
			);

			if ( !$table->bind( $bind_data ) || !$table->check() || !$table->store() ) {
				JError::raiseWarning( 0, $table->getError() );
			}
		}
	}

	private function activate_modules(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id', 'module'));
		$query->from('#__modules');
		$query->where('`module` LIKE \'%mod_pago%\'');
		$query->order('ordering ASC');

		$db->setQuery($query);

		$modules = $db->loadObjectList();

		//add all paGO modules to all pages
		if ($modules) {
			$db = JFactory::getDbo();
			foreach ($modules as $module){
				$query = $db->getQuery(true);

				$columns = array('moduleid', 'menuid');

				$values = array($module->id, '0');

				$query
					->insert($db->quoteName('#__modules_menu'))
					->columns($db->quoteName($columns))
					->values(implode(',', $values));
				$db->setQuery( $query );
				if ( !$db->query() ) {
					JError::raiseWarning( 1, JText::_( 'COM_PAGO_ERROR_ENABLING_MODULES_ALL' ) );
					return false;
				}
			}
		}

		//Publish all paGO modules
		$query = 'UPDATE #__modules SET `published` = 1 WHERE `module` LIKE "%mod_pago%"';
		$db->setQuery( $query );
		if ( !$db->query() ) {
			JError::raiseWarning( 1, JText::_( 'COM_PAGO_ERROR_PUBLISHING_MODULES' ) );
			return false;
		}

	}

	private function setup_menus( $parent )
	{
		if($parent->get( 'manifest' )->menus->menu){
			foreach ( $parent->get( 'manifest' )->menus->menu as $menu ) {
				$menu_type_table = JTable::getInstance( 'MenuType', 'JTable' );

				// $menutype = $menu->getAttribute( 'menutype' );
				// $menutitle = $menu->getAttribute( 'title' );
				// $position = $menu->getAttribute( 'position' );

				$menutype = (string)$menu->attributes()->menutype;
				$menutitle = (string)$menu->attributes()->title;
				$position = (string)$menu->attributes()->position;

				// $bind_data = array(
				// 	'menutype' => $menu->getAttribute( 'menutype' ),
				// 	'title' => $menu->getAttribute( 'title' ),
				// 	'description' => $menu->getAttribute( 'description' )
				// );
				$bind_data = array(
					'menutype' => (string)$menu->attributes()->menutype,
					'title' => (string)$menu->attributes()->title,
					'description' => (string)$menu->attributes()->description
				);

				if ( !$menu_type_table->bind( $bind_data ) ||
					!$menu_type_table->check() ||
					!$menu_type_table->store()
				) {
					JError::raiseWarning( 0, $menu_type_table->getError() );
				}

				foreach ( $menu->item as $item ) {
					$menu_table  = JTable::getInstance( 'Menu', 'JTable' );
					$component_id = 0;

					if ( (string)$item->attributes()->type == 'component' ) {
					//if ( $item->getAttribute( 'type' ) == 'component' ) {
						$db = $menu_type_table->get( '_db' );
						$db->setQuery( "SELECT extension_id
							FROM #__extensions
								WHERE name = 'com_pago'" );
						$component_id = $db->loadObject()->extension_id;
					}

					// $bind_data = array(
					// 	'menutype' => $menu->getAttribute( 'menutype' ),
					// 	'title' => $item->getAttribute( 'title' ),
					// 	'note' => $item->getAttribute( 'note' ),
					// 	'path' => $item->getAttribute( 'path' ),
					// 	'link' => $item->getAttribute( 'link' ),
					// 	'type' => $item->getAttribute( 'type' ),
					// 	'published' => 1,
					// 	'component_id' => $component_id,
					// 	'ordering' => 0,
					// 	'checked_out' => 0,
					// 	'checked_out_time' => '0000-00-00 00:00:00',
					// 	'browserNav' => 0,
					// 	'access' => 1,
					// 	'img' => '',
					// 	'template_style_id' => 0,
					// 	'params' => '',
					// 	'home' => 0,
					// 	'language' => '*',
					// 	'client_id' => 0,
					// );
					$bind_data = array(
						'menutype' => (string)$menu->attributes()->menutype,
						'title' => (string)$item->attributes()->title,
						'note' => (string)$item->attributes()->note,
						'path' => (string)$item->attributes()->path,
						'link' => (string)$item->attributes()->link,
						'type' => (string)$item->attributes()->type,
						'published' => 1,
						'component_id' => $component_id,
						'ordering' => 0,
						'checked_out' => 0,
						'checked_out_time' => '0000-00-00 00:00:00',
						'browserNav' => 0,
						'access' => 1,
						'img' => '',
						'template_style_id' => 0,
						'params' => '',
						'home' => 0,
						'language' => '*',
						'client_id' => 0,
					);

					if ( !$menu_table->bind( $bind_data ) ||
						!$menu_table->check() ||
						!$menu_table->store()
					) {
						JError::raiseWarning( 0, $menu_table->getError() );
					}

					$db = $menu_table->get( '_db' );
					$menu_id = $db->insertid();
					//$title = $item->getAttribute( 'title' );
					//$level = $item->getAttribute( 'level' );

					$title = (string)$item->attributes()->title;
					$level = (string)$item->attributes()->level;

					if ( !$level ) $level = 1;

					$db->setQuery( "UPDATE #__menu
						SET level={$level}
							WHERE menutype='{$menutype}'
								AND title='{$title}'" );
					$db->query();
				}

				$mod_table = JTable::getInstance( 'Module', 'JTable' );

				$bind_data = array(
					'title' => $menutitle,
					'note' => '',
					'content' => '',
					'ordering' => 1,
					'position' => $position,
					'checked_out' => 0,
					'checked_out_time' => '0000-00-00 00:00:00',
					'publish_up' => '0000-00-00 00:00:00',
					'publish_down' => '0000-00-00 00:00:00',
					'published' => 1,
					'module' => 'mod_menu',
					'access' => 1,
					'showtitle' => 1,
					'params' => '{"menutype":"'.$menutype.'","startLevel":"0","endLevel":"0",'.
						'"showAllChildren":"0","tag_id":"","class_sfx":"","window_open":"",'.
						'"layout":"","moduleclass_sfx":"_menu","cache":"1","cache_time":"900",'.
						'"cachemode":"itemid"}',
					'client_id' => 0,
					'language' => '*',
				);

				if ( !$mod_table->bind( $bind_data ) || !$mod_table->check() || !$mod_table->store() )
						JError::raiseWarning( 0, $mod_table->getError() );
			}
		}
	}
}
