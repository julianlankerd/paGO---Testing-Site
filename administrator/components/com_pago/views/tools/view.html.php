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

class PagoViewTools extends JViewLegacy
{
	/**
	 * Stack to hold default buttons
	 *
	 * @since	1.6
	 */
	public $buttons = array();
	
	public function display(){		
		
		if( $plugin = JFactory::getApplication()->input->get( 'plugin' ) ){
			
			JPluginHelper::importPlugin( 'pago_tools', $plugin ); 
			
			JDispatcher::getInstance()->trigger( 'onPagoToolsView', array( $this, $plugin ) );
			
			return parent::display();
		}
		
		$this->getButtons();
		
		parent::display();
	}
	
	public function button( $button )
	{
		ob_start();
		
		require dirname(__FILE__) . '/tmpl/default_button.php';
		
		$html = ob_get_clean();
		
		return $html;
	}

	public function getButtons()
	{
		JPluginHelper::importPlugin( 'pago_tools' ); 

		$dispatcher = JDispatcher::getInstance();
		
		foreach( $dispatcher->get( '_observers' ) as $observer ){
			
			$observer_type = $observer->get ('_type' );
			
			if( isset( $observer_type ) && $observer_type == 'pago_tools' ){
				$this->buttons[] = array(
					'link' => JRoute::_('index.php?option=com_pago&view=tools&plugin=' . $observer->get( '_name' ) ),
					'image' => JURI::root() . 'plugins/pago_tools/' . $observer->get( '_name' ) . '/app/icon.png',
					'text' => $observer->get( '_text' ),
					'access' => array('core.manage', 'com_content', 'core.create', 'com_content', )
				);
			}
		}
		
		return $this->buttons;
	}
}
