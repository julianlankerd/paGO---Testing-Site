<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.controller' );

class PagoControllerMigrate extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();
	}

	function migrateData()
	{
		ob_clean();
		$model = $this->getModel('Migrate','PagoModel');
		$jinput = JFactory::getApplication()->input;
		$extension = $jinput->get('extension', '0', 'CHAR');
		$migrated_data = $model->migrateData($extension);
		echo $migrated_data[0];
		exit;
	}
}
?>
