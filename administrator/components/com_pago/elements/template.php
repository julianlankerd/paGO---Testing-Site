<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JFormFieldTemplate extends JFormField
{
	protected $type = 'template';

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		jimport( 'joomla.filesystem.folder' );

		$componentPath    = JPATH_SITE .DS. 'components' .DS. 'com_pago' .DS. 'templates';
		$componentFolders = JFolder::folders( $componentPath );
		$db               = JFactory::getDBO();

		$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();

		if ( JFolder::exists( JPATH_SITE .DS. 'templates' .DS. $defaultemplate
				.DS. 'html' .DS. 'com_pago' .DS. 'templates' ) ) {
			$templatePath = JPATH_SITE .DS. 'templates' .DS. $defaultemplate .DS. 'html'
				.DS. 'com_pago' .DS. 'templates';
		} else {
			$templatePath = JPATH_SITE .DS. 'templates' .DS. $defaultemplate .DS. 'html'
				.DS. 'com_pago';
		}

		if ( JFolder::exists( $templatePath ) ) {
			$templateFolders = JFolder::folders( $templatePath );
			$folders         = @array_merge( $templateFolders, $componentFolders );
			$folders         = @array_unique( $folders );
		} else {
			$folders = $componentFolders;
		}

		$options = array();
		foreach ( $folders as $folder ) {
			$options[] = JHTML::_( 'select.option', $folder, $folder );
		}

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
