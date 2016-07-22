<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport( 'joomla.plugin.plugin');

class JFormFieldPluginselect extends JFormField
{
	protected $type = 'pluginselect';

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$db  = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get( 'id', 0 );

		if(!$id){
			$id = JFactory::getApplication()->input->get( 'cid', 0 );
			$id = $id[0];
		}

		$ctrl  = $name;
		$plugin_dir = JPATH_PLUGINS . '/'.$node->attributes( 'plugin_group' ).'/';

		if( !is_dir( $plugin_dir ) ) {

			$plugin_install_dir = JPATH_SITE . '/administrator/components/com_pago/plugins/'.$node->attributes( 'plugin_group' ).'/';
			$this->install_plugins( $plugin_install_dir );
		}

		$files = scandir( $plugin_dir );

		if(is_array($files)){
			foreach($files as $file){
				if(strstr($file, '.xml')){
					$view = str_replace('.xml', '', $file);

					if(!$value){
						$value = $view;
					}

					$options[] = JHTML::_('select.option', $view, $view);
					//$options[] = JHTML::_('select.option', 'test', 'test');
				}
			}
		}

		$html = JHTML::_('select.genericlist', $options, $ctrl, false, 'value', 'text', $value, $control_name.$name );

		$url = 'index.php?option=com_pago&view=pluginselect&format=raw&plugin_group='
		.$node->attributes( 'plugin_group' ).'&params_group='.$node->attributes( 'params_group' ).'&plugin=';

		$html .= "
			<script>


				//var myAjax = new Ajax(url, {method: 'get'}).request();
				$('$control_name$name').addEvent( 'change' , function(event){

						$('plugin_params').innerHTML = '<img src=\"assets/images/ajax-loader.gif\" />';
						var plugin = $('$control_name$name').value;
						//var id = $id;

						//var url = window.location.href + '&plugin_view=mod_mainmenu&plugin='  + plugin;
						var url = '$url'  + plugin;

						//accordion.display(3);
						new Ajax(url,{
							method: 'get',
							evalScripts: true,
							onSuccess: function(req){
								$('plugin_params').innerHTML = req;

								//accordion.display(3);
							}
						}).request();
				 } );

			</script>
		";

		$sql = "SELECT params FROM #__modules WHERE id = '$id'";

		$db->setQuery($sql);
		$row = $db->loadObject();

		$params = false;

		if( isset( $row->params ) ){
			$params = $row->params;
		}

		$lang =& JFactory::getLanguage();

		$lang->load( 'plg_'.$node->attributes( 'plugin_group' ).'_' . $value, JPATH_ADMINISTRATOR );

		$params = new JParameter( $params, JApplicationHelper::getPath( 'plg_xml', $node->attributes( 'plugin_group' ) . '/' . $value ), 'plugin' );

		$params->addElementPath( array(
			//JPATH_PLUGINS . DS . 'system' . DS .  'jent' . DS . 'elements',
			JPATH_PLUGINS . DS . $node->attributes( 'plugin_group' ) . DS .  $value . DS . 'elements'
			)
		);

		$html .= '<div id="plugin_params" style="margin:4px 0px 0px 0px;border:1px solid #ccc">';
		$html .= $params->render('params', $node->attributes( 'params_group' ));
		$html .= '</div>';

		return $html;
	}

	//install all plugins in a given directory
	private function install_plugins( $plugin_install_dir ){

		$db  = JFactory::getDBO();

		jimport('joomla.installer.installer');
		$installer = & JInstaller::getInstance();

		$plugins = scandir($plugin_install_dir);

		if( is_array( $plugins ) ){
			foreach( $plugins as $plugin_name ){
				if( !strstr( $plugin_name, '.' ) ){

					$plugin = $plugin_install_dir . $plugin_name;

					if( !$installer->install( $plugin ) ){

						$error = 'Error: Failed to install ' . $plugin_name;

					} else {

						$sql = "UPDATE #__plugins
									SET published = 1
										WHERE element = '$plugin_name'";

						$db->setQuery( $sql );
						$db->query();
					}
				}
			}
		} else {
			return 'No Plugins Found in Directory';
		}

		return $error;
	}
}
