<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.view' );
class PagoViewWingman extends JViewLegacy
{
	function display( $tpl = null )
	{
		PagoHtml::add_css( JURI::root( true ) . '/administrator/components/com_pago/css/animate.css' );
		PagoHtml::add_css( JURI::root( true ) . '/administrator/components/com_pago/css/pago.wingman.css' );
		
		PagoHtml::add_js( 'https://js.stripe.com/v2/', true );
		PagoHtml::add_js( JURI::root( true ) . '/administrator/components/com_pago/javascript/wingman/vendors.js', true );
		PagoHtml::add_js( JURI::root( true ) . '/administrator/components/com_pago/javascript/wingman/wingman.js', true );
		
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		
		$jdoc = JFactory::getDocument();
		// $jdoc = $jdoc->setBase( JURI::root( true ) . '/administrator/components/com_pago/' );
		
		$b_data = array(
		// 'params' => $item
		);
		
		Pago::load_helpers( 'pagoparameter' );
		
		$params = new PagoParameter( $b_data,  $cmp_path . 'views/wingman/metadata.xml' );
		
		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );
		
		$base = $params->render( 'params', 'base', '', '', '', false, true, false );
		$this->assignRef('base', $base);
		
		$address = $params->render( 'params', 'address', '', '', '', false, true, false );
		$this->assignRef('address', $address);
		
		$cc = $params->render( 'params', 'cc', '', '', '', false, true, false );
		$this->assignRef('cc', $cc);
		
		$terms = $params->render( 'params', 'terms', '', '', '', false, true, false );
		$this->assignRef('terms', $terms);
		
		parent::display( $tpl );
	}
}