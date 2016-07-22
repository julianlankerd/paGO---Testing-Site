<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }
/**
 * @version		$Id: view.html.php 1 2009-10-27 20:56:04Z rafael $
 * @package		Pago
 * @copyright	Copyright (C) 2009 'corePHP' / corephp.com. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 */

jimport( 'joomla.application.component.view' );

class PagoViewcontact_info extends JViewLegacy
{
	function display( $tpl = null )
	{
		// Add jQuery
		$_root = JURI::root(true);
		PagoHtml::add_js($_root . '/components/com_pago/templates/default/js/jquery.validate.min.js');
		parent::display($tpl);
	}
}
?>