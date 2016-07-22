<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerTax extends PagoController
{
	function __construct( $default = array() )
	{
		parent::__construct($default);
		
		$this->save2new = false;
		
		$this->redirect_to = 'index.php?' . http_build_query(array(
			'option' => JFactory::getApplication()->input->get('option'),
			'view' => JFactory::getApplication()->input->get('view'),
			'taxcid' => JFactory::getApplication()->input->getInt('taxcid'),
		));

		$this->registerTask('new', 'add');
	}


	function display( $cacheable = false, $urlparams = false )
	{
		parent::display($cacheable = false, $urlparams = false);
	}

	function publish() {
		$this->set_published(true);
		$this->setRedirect($this->redirect_to, JText::_('PAGO_TAX_PUBLISHED'));
	}

	function unpublish()
	{
		$this->set_published(false);
		$this->setRedirect($this->redirect_to, JText::_('PAGO_TAX_UNPUBLISHED'));
	}

	private function set_published( $state = true )
	{
		JTable::addIncludePath(JPATH_COMPONENT . '/tables');

		$table = JTable::getInstance('Tax', 'Table');
		if (!is_array(JFactory::getApplication()->input->get('cid', array(0), 'array')))
			$this->setRedirect($this->redirect_to, JText::_('PAGO_CID_MUST_BE_AN_ARRAY'));

		foreach (JFactory::getApplication()->input->get('cid', array(0), 'array') as $item_id)
		{
			$data = array(
				'pgtax_id' => $item_id,
				'pgtax_enable' => $state
			);

			if (!$table->bind($data))
			{
				JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}

			$table->store();
			$table->reset();
		}
	}

	function edit()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		JFactory::getApplication()->input->set('layout', 'form');
		parent::display();
	}

	function add()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		JFactory::getApplication()->input->set('layout', 'form');
		parent::display();
	}

	function save()
	{
		$model = JModelLegacy::getInstance('Tax_rate', 'PagoModel');
		$data = JFactory::getApplication()->input->getArray($_POST);
		
		$taxcid   = $data['params']['pgtax_class_id'];
		if(!$taxcid)
		{
			$taxcid = JFactory::getApplication()->input->get('taxcid', 0, 'int');
		}
		if ( $model->store() )
		{
			$msg = JText::_('PAGO_TAX_SAVED');
		}
		else
		{
			$msg = JText::_('PAGO_TAX_ERROR');
		}
	
		$link = 'index.php?option=com_pago&view=tax&taxcid='.$taxcid;
		
		if($this->save2new)
			$link = 'index.php?option=com_pago&view=tax&task=new&taxcid='.$taxcid;
		
		$this->setRedirect($link, $msg);
	}
	
	function save2new()
	{
		$this->save2new = true;
		$this->save();
	}
	
	function apply()
	{
		$model = JModelLegacy::getInstance('Tax_rate', 'PagoModel');
		$id = $model->store();
		$data = JFactory::getApplication()->input->getArray($_POST);
		$taxcid   = $data['params']['pgtax_class_id'];

		if (! $id)
		{
			$id = JFactory::getApplication()->input->get('id', 0, 'int');
		}

		if (!$taxcid)
		{
			$taxcid = JFactory::getApplication()->input->get('taxcid', 0, 'int');
		}

		$msg = JText::_('PAGO_TAX_SAVED');

		$link = 'index.php?option=com_pago&view=tax&task=edit&taxcid=' . $taxcid . '&cid[]=' . $id;
		$this->setRedirect($link, $msg);
	}

	function delete() {
		JTable::addIncludePath(JPATH_COMPONENT . '/tables');

		$db = JFactory::getDBO();

		$table = JTable::getInstance('Tax', 'Table');

		if (!is_array(JFactory::getApplication()->input->get('cid', array(0), 'array')))
		{
			$this->setRedirect($this->redirect_to, JText::_('PAGO_CID_MUST_BE_AN_ARRAY'));
		}

		foreach (JFactory::getApplication()->input->get('cid', array(0), 'array') as $item_id)
		{
			$table->delete($item_id);
			$table->reset();
		}

		$this->setRedirect($this->redirect_to, JText::_('PAGO_TAX_DELETED'));
	}

	function cancel()
	{
		$link = 'index.php?option=com_pago&view=tax_class';
		$this->setRedirect($link, $msg);
	}
}
?>
