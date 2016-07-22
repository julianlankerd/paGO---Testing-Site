<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/


defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

class PagoModelMigrate extends JModelList
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
		parent::__construct($config);
	}


	public function migrateData($extension)
	{
		ob_clean();
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// trgigger plugin for migrate
		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin( 'pago_migration' );
		$migrated_data = $dispatcher->trigger(
			'on_select_migration',
			array( $extension )
		);
		// End trigger
		return $migrated_data;
	}
	
	public function checkForMigratePlugin()
	{
		$db = JFactory::getDbo();
		$responseArray = array ();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'pago_migration'");
		return $pluginEnable = $db->loadResult();
	}
	
	public function checkForInstalledExtension($extension)
	{
		if($extension == 'redshop')
		{
			$componentName = "com_redshop";
		}
		else if($extension == 'hikashop')
		{
			$componentName = "com_hikashop";
		}
		else if($extension == 'mijoshop')
		{
			$componentName = "com_mijoshop";
		}
		else if($extension == 'vm')
		{
			$componentName = "com_virtuemart";
		}
		
		$db = JFactory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = '" . $componentName ."'");
		return $db->loadResult();
		
	}
}

