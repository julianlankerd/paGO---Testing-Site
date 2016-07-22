<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class PagoViewReports extends JViewLegacy
{
	/**
	 * Display the view
	 */

	public function display( $tpl = null )
	{
		Pago::load_helpers( 'pagoparameter' );

		parent::display($tpl);
	}
	
	
	
}
