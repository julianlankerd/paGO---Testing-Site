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

class PagoViewExport extends JViewLegacy
{

	/**
	 * Display the view
	 */

	public function display( $tpl = null )
	{
		// JToolBarHelper::custom('exportpgdata', 'pago_export.png', JText::_('COM_PAGO_EXPORT'), JText::_('COM_PAGO_EXPORT'), false, false);
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		Pago::load_helpers('pagoparameter');
		$model = $this->getModel('export');
		$task = JFactory::getApplication()->input->get('task');
		if ($task == 'exportpgdata')
		{
			$this->get('Data');
		}
		
		$top_menu = array(
			array(
				'task' => 'exportpgdata',
				'text' => JText::_('COM_PAGO_EXPORT'),
				'class' => 'pg-btn-medium pg-btn-dark pg-btn-green'
			)	
		);

		$this->assignRef( 'top_menu',  $top_menu );

		parent::display($tpl);
	}

}
