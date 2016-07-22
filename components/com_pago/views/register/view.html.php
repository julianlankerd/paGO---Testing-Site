<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/kview.php';
/**
 * HTML View class for the Pago  component
 */
class PagoViewRegister extends PagoView
{
	function display( $tpl = null )
	{
		$user       = JFactory::getUser();
		if( !$user->guest ) {
			JFactory::getApplication()
			->redirect( 'index.php?option=com_pago&view=account' );
		}
		// Helpers
		// Set view from template switcher
		$config = Pago::get_instance( 'config' )->get();
        $layout = $config->get( 'account.register_custom_layout' );
        
        if(!$layout) $layout = 'default'; 
        
        if($layout != "")
        {
            $this->set_theme($layout);
        }
        $this->set( 'register' );
        parent::display( $tpl );
    }
}
?>
