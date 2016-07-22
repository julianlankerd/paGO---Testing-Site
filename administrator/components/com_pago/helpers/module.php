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
 * Wrapper for JModuleHelper to provide a class to pass to views instead of
 * assuming template developers will know about JModuleHelper and use the static methods
 */
class PagoHelperModule
{
	function __construct()
	{
	}

	function get_position( $position=false )
	{
		return JModuleHelper::getModules( $position );
	}

	function render_position( $position=false )
	{
		$modules = JModuleHelper::getModules( $position );
		if ( count( $modules ) == 0 ) {
			return false;
		} else {
			foreach ( $modules as $module ) {
				echo JModuleHelper::renderModule( $module );
			}
		}
	}

	function get_module( $name )
	{
		if ( JModuleHelper::isEnabled( $name ) ) {
			echo JModuleHelper::renderModule( $name );
		}
	}

	function module_enabled( $module )
	{
		return JModuleHelper::isEnabled( $module );
	}
}
?>
