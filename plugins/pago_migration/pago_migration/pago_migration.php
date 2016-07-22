<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die();

class plgPago_MigrationPago_migration extends JPlugin
{

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin('pago_migration', 'pago_migration');
		$this->_params = new JRegistry($this->_plugin->params);

		// Hooks
		KDispatcher::add_filter('on_select_migration', array($this, 'on_select_migration'));
	}

	public function on_select_migration( $extension )
	{
		require_once ( JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'pago_migration' . DIRECTORY_SEPARATOR . 'pago_migration' . DIRECTORY_SEPARATOR . 'migration/' .$extension.'/'.$extension.'.php');

		if($extension == 'redshop')
		{
			$pagoMigrationClass = new PagoMigrationRedshop();
		}
		elseif($extension == 'vm')
		{
			$pagoMigrationClass = new PagoMigrationVm();
		}
		elseif($extension == 'vm15')
		{
			$pagoMigrationClass = new PagoMigrationVm15();
		}
		elseif($extension == 'mijoshop')
		{
			$pagoMigrationClass = new PagoMigrationMijoshop();
		}
		elseif($extension == 'hikashop')
		{
			$pagoMigrationClass = new PagoMigrationHikashop();
		}


		$total_category_items = $pagoMigrationClass->migrateCategories(); 
		$total_users = $pagoMigrationClass->migrateUsers();
		$total_orders = $pagoMigrationClass->migrateOrders();

		return $total_category_items."_".$total_users."_".$total_orders;

	}
}

