<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.view' );
class PagoViewUpload_Form extends JViewLegacy
{
	function display( $tpl = null )
	{
		// Set view from template switcher
		Pago::get_instance( 'template' )->set( $this, 'upload_form' );
		
		// Set helper information
		Pago::load_helpers( array( 'module' ) );
		
		$modules = new PagoHelperModule;

		// Add Styles/Scripts
		PagoHtml::loadJquery();
		PagoHtml::add_css( JURI::root( true ) . '/components/com_pago/css/styles.css' );
		PagoHtml::add_css( JURI::root( true ) . '/components/com_pago/css/uploadify.css' );
		PagoHtml::add_js( JURI::root( true ) . '/components/com_pago/javascript/swfobject.js',
			true );
		PagoHtml::add_js( JURI::root( true )
			. '/components/com_pago/javascript/jquery.uploadify.js' );
			
		$this->assignRef( 'modules', $modules );

		parent::display( $tpl );
	}
}
?>