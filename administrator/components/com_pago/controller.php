<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

//CANNOT USE PAGO CLASS HERE BECAUSE INSTALLATION WILL NOT WORK!
//Pago::load_helpers( 'pagohtml' );
require( JPATH_COMPONENT . '/helpers/pagohtml.php' );

/**
* Pago  Controller
*
* @package Joomla
* @subpackage Pago
*/
class PagoController extends JControllerLegacy
{
	/**
	* Constructor
	* @access private
	* @subpackage Pago
	*/
	public function __construct()
	{
		$trigger = JFactory::getApplication()->input->get( 'trigger' );

		if ( $trigger ) {
			$trigger = explode( '.', $trigger );

			JPluginHelper::importPlugin( $trigger[0] );

			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger( $trigger[1], array() );

			unset( $trigger );
		}

		parent::__construct();
	}

	public function display( $cacheable = false, $urlparams = false )
	{
		//inject an ajax call on every admin page to run our
		//cron job. Check the method cron in this class
		JFactory::getDocument()->addScriptDeclaration("
			jQuery(document).ready(function () {
				jQuery.get('index.php?option=com_pago&task=cron');
			});
		");
		
		$view = JFactory::getApplication()->input->get( 'view', '');

		if ( JFactory::getApplication()->input->getCmd( 'view' ) == '' ) {
			$view  = $this->getView  ( 'default', 'html'  );
			$ordersi = $this->getModel ( 'Dashboard','PagoModel' );
			$view->setModel( $ordersi );
			JFactory::getApplication()->input->set( 'view', 'default' );
		}
		$version = new JVersion();
		
		if($version->RELEASE >= 3){
			JHtml::_('jquery.framework');
		}
		$app = JFactory::getApplication();
		$app->controller = $this;

		if($version->RELEASE < 3){
			PagoHtml::behaviour_jquery();
		 }
		 PagoHtml::behaviour_jqueryui();
		//comment out to us the universal wrapper below
		return parent::display();
		//wrap output in pago wrapper if there is no format specified

		if( JFactory::getApplication()->input->get( 'format' ) ){
			parent::display();
		} else {			
			ob_start();

				//should we be adding all this in EVERY SINGLE TEMPLATE
				//I think not...

				include( JPATH_ADMINISTRATOR . '/components/com_pago/helpers/menu_config.php');
				PagoHtml::apply_layout_fixes();

				PagoHtml::pago_top( $menu_items );
					parent::display();
				PagoHtml::pago_bottom();

			echo ob_get_clean();
		}
		if($version->RELEASE >= 3){
			PagoHtml::behaviour_jquery();
			PagoHtml::behaviour_jqueryui();
		}
	}
	
	public function cron()
	{
		$params = Pago::get_instance('params');
		$locked = $params->get('cron.lock', 0);
		
		$unlock_after = 500; //seconds
		
		//to reduce overhead cron will only run once until complete
		//if there is an issue the cron will automatically unlock after
		//specified time
		if($locked && $locked > time() - $unlock_after) 
			exit('Cron in operation ' . $params->get('cron.error', 0));
		
		//set an error which will be cleared after all events are triggered
		//this will show up if an issue with specific plugin is jamming our
		//cron operation
		$params->set('cron.error', 'error');
		
		//allow 3rd party plugins to hook into cron
		//note standard cron operations will run in 
		//the pago system plugin onCron method
		JPluginHelper::importPlugin('pago_cron');
		
		KDispatcher::getInstance()->trigger('onCron');
		
		$params->set('cron.error', '');
		$params->set('cron.lock', time());
		
		exit('Cron completed');
	}
}
?>
