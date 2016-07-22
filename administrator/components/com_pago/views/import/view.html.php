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

class PagoViewImport extends JViewLegacy
{

	/**
	 * Display the view
	 */

	public function display( $tpl = null )
	{
		// JToolBarHelper::custom('importpgdata', 'pago_import.png', JText::_('COM_PAGO_IMPORT'), JText::_('COM_PAGO_IMPORT'), false, false);
		$document = JFactory::getDocument();
		$model = $this->getModel('import');
		$task = JFactory::getApplication()->input->get('task');
		$layout = JFactory::getApplication()->input->get('layout');
		$records = JFactory::getApplication()->input->get('records');
		if(isset($records) && $records > 0)
		{
			$msg = JText::_('COM_PAGO_IMPORT_OK'). "" .$records." Record(s)";
			$this->assignRef( 'msg', $msg );
			JFactory::getApplication()->enqueueMessage($msg);
		}

		if ($layout == 'importrecord')
		{
			$this->setLayout($layout);
		}

		$task   = JFactory::getApplication()->input->get('task');
		$result = '';

		if ($task == 'importpgdata')
		{
			// Load the data to export
			$result = $this->get('Data');
		}
		
		$top_menu = array(
			array(
				'task'  => 'importpgdata', 
				'text'  => JTEXT::_('COM_PAGO_IMPORT'), 
				'class' => 'save pg-btn-medium pg-btn-dark pg-btn-green'
			)
		);
		
		$this->assignRef('top_menu', $top_menu);

		$this->result = $result;
		parent::display($tpl);
	}

}
