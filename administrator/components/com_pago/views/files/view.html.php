<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.view' );

class PagoViewFiles extends JViewLegacy
{
	function display( $tpl = null )
	{
		$db       = JFactory::getDBO();
		$document = JFactory::getDocument();
		$config   = Pago::get_instance( 'config' )->get();

		$this->toolbar();

		PagoHtml::jstree( array( 'cookie', 'hotkeys' ) );
		PagoHtml::thickbox();
		PagoHtml::add_js( JURI::root(true)
			. '/administrator/components/com_pago/javascript/com_pago.js' );

		$this->assignRef( 'config', $config );

		parent::display( $tpl );
	}

	function toolbar()
	{
		if ( 'files' != JFactory::getApplication()->input->get( 'view' ) ) {
			return;
		}
	}
}
?>