<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class PagoViewPluginselect extends JViewLegacy
{
	function display()
	{		
		$plugin = JFactory::getApplication()->input->get->get('plugin', false, 'WORD');
		$plugin_group = JFactory::getApplication()->input->get->get('plugin_group', false, 'WORD');
		$params_group = JFactory::getApplication()->input->get->get('params_group', false, 'WORD');
		
		$lang =& JFactory::getLanguage();
		
		$lang->load( 'plg_'.$plugin_group.'_' . $plugin, JPATH_ADMINISTRATOR );
		
		$params = new JParameter( false, JApplicationHelper::getPath( 'plg_xml', $plugin_group . '/' . $plugin ), 'plugin' );
		
		$params->addElementPath( array(
			//JPATH_PLUGINS . DS . 'system' . DS .  'jent' . DS . 'elements',
			JPATH_PLUGINS . DS . $plugin_group . DS .  $plugin . DS . 'elements'
			) 
		);
		
		//$html .= '<div id="plugin_params" style="margin:4px 0px 0px 0px;border:1px solid #ccc">';
		echo $params->render('params', $params_group );
		
	}
}
?>