<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Plugins component
 *
 * @static
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.0
 */
class PagoViewConfig extends JViewLegacy
{
	function display( $tpl = null )
	{

		// JToolBarHelper::apply();

		JHTML::_( 'script', 'com_pago.js', 'administrator/components/com_pago/javascript/', false );

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		$global_config = Pago::get_instance( 'config' )->get('global', null, true);
		$template = Pago::get_instance( 'template' );
		list( $theme_path, , , ) = $template->find_paths(
			$global_config->get( 'template.pago_theme','default' )
		);

		// PagoParameter Overrides JParameter render to put pago class names in html
		Pago::load_helpers( 'pagoparameter' );
		$bind_data = array(
			'params' => $global_config
		);
		$params = new PagoParameter( $bind_data,  dirname( __FILE__ ) . '/elements.xml' );

		if(!file_exists($theme_path . '/config.xml'))
		{
			$theme_path_sp = explode('/',$theme_path);
			array_pop($theme_path_sp);
			array_pop($theme_path_sp);
			$theme_path = implode('/',$theme_path_sp).'/default';
		}

		// template specific config
		$template_params = new PagoParameter(
			array(
				'theme' => Pago::get_instance('config')->get(
					$global_config->get('template.pago_theme')
				)
			),
			$theme_path . '/config.xml'
		);
		
		$imageName = $this->get('DeafultImage');
		
		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );
		
		$top_menu = array(
			array(
				'task'  => 'apply',
				'text'  => JText::_( 'PAGO_SAVE' ),
				'class' => 'apply pg-btn-medium pg-btn-dark pg-btn-green'
			)	
		);
		
		$this->assignRef( 'top_menu', $top_menu );

		$this->assignRef( 'params', $params );
		$this->assignRef( 'template_params', $template_params );
		$this->assignRef( 'defaultImage', $imageName );
		parent::display( $tpl );
	}
}
