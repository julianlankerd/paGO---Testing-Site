<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerConfig extends PagoController
{
	/**
	* Custom Constructor
	*/
	function __construct( $default = array() )
	{
		parent::__construct( $default );
		$this->registerTask( 'save', 'save' );
	}

	function apply()
	{
		$params     = JFactory::getApplication()->input->post->get( 'params', array(), 'array');
		$theme      = JFactory::getApplication()->input->post->get( 'theme', array(), 'array');
		$db         = JFactory::getDBO();
		$dispatcher = KDispatcher::getInstance();
		$config     = JModelLegacy::getInstance('config', 'PagoModel');

		if ( !empty( $params ) ) {
			$dispatcher->trigger( 'pre_save_pago_config', array( &$params ) );

			if ( $config->save_config( $params ) ) {
				$msg = JText::_( 'PAGO_CONFIG_SAVE' );
			} else {
				$msg = JText::_( 'PAGO_CONFIG_NOT_SAVE' );
			}
		}

		if ( !empty( $theme ) ) {
			$dispatcher->trigger( 'pre_save_pago_theme_config', array( &$theme ) );

			$global = Pago::get_instance('config')->get( 'global', null, true );
			if ( !$config->save_config( $theme, $global->get('template.pago_theme') ) ) {
				$msg = JText::_( 'PAGO_CONFIG_THEME_NOT_SAVE' );
			}
		}

		$this->setRedirect( 'index.php?option=com_pago&view=config', $msg );
	}
	function save()
	{
		$params = JFactory::getApplication()->input->get( 'params', array(), 'array' );
		$theme      = JFactory::getApplication()->input->get( 'params', array(), 'array' );
		$dispatcher = KDispatcher::getInstance();

		$model = JModelLegacy::getInstance( 'Config', 'PagoModel' );

		if ( !empty( $params ) ) {
			$dispatcher->trigger( 'pre_save_pago_config', array( &$params ) );

			if ( $model->save_config($params) ) {
				$msg = JText::_( 'PAGO_CONFIG_SAVE' );
			} else {
				$msg = JText::_( 'PAGO_CONFIG_NOT_SAVE' );
			}
		}

		if ( !empty( $theme ) ) {
			$dispatcher->trigger( 'pre_save_pago_theme_config', array( &$theme ) );

			$global = Pago::get_instance('config')->get( 'global', null, true );
			if ( !$model->save_config( $theme, $global->get('template.pago_theme') ) ) {
				$msg = JText::_( 'PAGO_CONFIG_THEME_NOT_SAVE' );
			}
		}

		$link 	= 'index.php?option=com_pago&view=config';

		$this->setRedirect($link, $msg);
	}
	function loadConfig()
	{
		// load in and render config
		$theme = JFactory::getApplication()->input->get( 'theme', 'default' );
		$cmp_path = JPATH_ADMINISTRATOR . '/components/com_pago/';
		$template = Pago::get_instance( 'template' );
		list( $theme_path, , , ) = $template->find_paths( $theme );

		Pago::load_helpers( 'pagoparameter' );

		$template_params = new PagoParameter(
			array(
				'theme' => Pago::get_instance( 'config' )->get( $theme )
			),
			$theme_path . '/config.xml'
		);

		JForm::addfieldpath(array($cmp_path . '/elements'));
		echo $template_params->render_config( 'theme', 'options' );
		die();
	}
	function loadState(){
		$countryCode = JFactory::getApplication()->input->get( 'countryCode', '', '' );
		//echo $countryCode;
		$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );

		$countryCodes = explode(',', $countryCode);
		$statesHtml = '';		

		foreach ($countryCodes as $country) {
			$states = $user_fields_model->get_countries_states($country);
		
			if($states){
				 foreach( $states['attribs'] as $state=>$class ){
				 	$statesHtml .= '<option value="'.$state.'"'.$class.'>'.$state.'</option>';
				 }
			}			
		}

		echo json_encode($statesHtml);
		die();
	}
}
