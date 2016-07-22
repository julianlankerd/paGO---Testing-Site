<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JFormFieldCategorylayoutlist extends JFormField
{
	protected $type = 'categorylayoutlist';

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;
		$config = Pago::get_instance( 'config' )->get('global');
		$pago_theme = $config->get('template.pago_theme', 'default');

		jimport( 'joomla.filesystem.folder' );

		// get overrides from component
		$customLayoutFolderPath    = JPATH_SITE . DS . 'components' . DS . 'com_pago' . DS . 'templates' . DS;
		$componentFolders = JFolder::folders( $customLayoutFolderPath );
		foreach ($componentFolders as $key => $componentFolder) {
			$customLayoutPath    = JPATH_SITE . DS . 'components' . DS . 'com_pago' . DS . 'templates' . DS . $componentFolder . DS . 'category' . DS. 'default.php';

			if(!JFile::exists( $customLayoutPath ) || $componentFolder == $pago_theme){
				unset($componentFolders[$key]);
			}
		}

		$db = JFactory::getDBO();
		// get overrides from component
		$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
		$db->setQuery($query);
		$joomla_theme = $db->loadResult();

		$templatesFolders = array();
		if($joomla_theme){
			$templatesFolderPath    = JPATH_SITE . DS . 'templates' . DS . $joomla_theme . DS . 'html' . DS . 'com_pago' . DS;
			if(JFolder::exists( $templatesFolderPath )){
				$templatesFolders = JFolder::folders( $templatesFolderPath );
				foreach ($templatesFolders as $key => $templatesFolder) {
					$templatesLayoutPath    = JPATH_SITE . DS . 'templates' . DS . $joomla_theme . DS . 'html' . DS . 'com_pago' . DS . $templatesFolder . DS . 'category' . DS .'default.php';
					if(!JFile::exists( $templatesLayoutPath )){
					 	unset($templatesFolders[$key]);
					}
				}
			}
		}
		//merging select option in the select box
		//$options[] = JHTML::_( 'select.option', '0' , JText::_('COM_PAGO_SELECT'));
		$foldersC = $componentFolders;
		$options[] = JHTML::_( 'select.option', '0' , JText::_('COM_PAGO_DEFAULT_TEMPLATE'));
		foreach ( $foldersC as $folder ) {
			$options[] = JHTML::_( 'select.option', $folder, 'component / '.$folder );
		}

		$foldersT = $templatesFolders;
		foreach ( $foldersT as $folder ) {
			$options[] = JHTML::_( 'select.option', $folder, 'templates / '.$folder );
		}
		
		// $config = Pago::get_instance( 'config' )->get('global');
		// $pago_theme = $config->get('template.pago_theme', 'default');

		// jimport( 'joomla.filesystem.folder' );

		// $customLayoutPath    = JPATH_SITE . DS . 'components' . DS . 'com_pago' . DS . 'templates' . DS . $pago_theme . DS . 'category'.DS. 'overrides';
		// $componentFolders = JFolder::files( $customLayoutPath );
		// $db               = JFactory::getDBO();


		// $folders = $componentFolders;

		// $options = array();

		// //merging select option in the select box
		// $options[] = JHTML::_( 'select.option', '0' , JText::_('COM_PAGO_SELECT'));
		// $options[] = JHTML::_( 'select.option', '1' , JText::_('COM_PAGO_DEFAULT_TEMPLATE'));
		// foreach ( $folders as $folder ) {
		// 	$options[] = JHTML::_( 'select.option', $folder, $folder );
		// }

		return JHTML::_(
			'select.genericlist',
			$options,
			$name,
			'class="inputbox"',
			'value',
			'text',
			$value,
			$control_name . $name
		);
	}
}
