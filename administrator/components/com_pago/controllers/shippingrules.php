<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerShippingrules extends PagoController
{
	function __construct( $default = array() )
	{
		parent::__construct($default);

		$this->redirect_to = 'index.php?' . http_build_query(array(
			'option' => JFactory::getApplication()->input->get('option'),
			'view' => JFactory::getApplication()->input->get('view')
		));

		$this->registerTask('new', 'add');
	}

	

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display($cacheable = false, $urlparams = false);
	}

	function publish()
	{
		$this->set_published(true);
		$this->setRedirect($this->redirect_to, JText::_('PAGO_SHIPPING_RULE_PUBLISHED'));
	}

	function unpublish()
	{
		$this->set_published(false);
		$this->setRedirect($this->redirect_to, JText::_('PAGO_SHIPPING_RULE_UNPUBLISHED'));
	}

	private function set_published( $state = true )
	{
		JTable::addIncludePath(JPATH_COMPONENT . '/tables');

		$table = JTable::getInstance('Shippingrule', 'Table');

		if (!is_array(JFactory::getApplication()->input->get('cid', array(0), 'array')))
			$this->setRedirect($this->redirect_to, JText::_('PAGO_CID_MUST_BE_AN_ARRAY'));

		foreach (JFactory::getApplication()->input->get('cid', array(0), 'array') as $item_id)
		{
			$data = array(
				'id' => $item_id,
				'published' => $state
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

	function save($copy=false)
	{
		$model = JModelLegacy::getInstance('Shippingrule', 'PagoModel');

		if ( $model->store($copy) )
		{
			$msg = JText::_('PAGO_SHIPPING_RULE_SAVED');
		}
		else
		{
			$msg = JText::_('PAGO_SHIPPING_RULE_ERROR');
		}

		$msg = JText::_('PAGO_SHIPPING_RULE_SAVED');
		$this->setRedirect($this->redirect_to, $msg);
	}
	
	function save2new(){
		
		$this->redirect_to = 'index.php?option=com_pago&view=shippingrules&task=new';
		
		$this->save();
	}
	
	function save2copy(){
		$this->save(true);
	}
	
	function apply()
	{
		$model = JModelLegacy::getInstance('Shippingrule', 'PagoModel');
		$id = $model->store();

		if (! $id)
		{
			$id = JFactory::getApplication()->input->getInt('id', 0);
		}

		$msg = JText::_('PAGO_SHIPPING_RULE_SAVED');

		$link = 'index.php?option=com_pago&view=shippingrules&task=edit&cid[]=' . $id;
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		JTable::addIncludePath(JPATH_COMPONENT . '/tables');

		$db = JFactory::getDBO();

		$table = JTable::getInstance('Shippingrule', 'Table');

		if (!is_array(JFactory::getApplication()->input->get('cid', array(0), 'array')))
		{
			$this->setRedirect($this->redirect_to, JText::_('PAGO_CID_MUST_BE_AN_ARRAY'));
		}

		foreach (JFactory::getApplication()->input->get('cid', array(0), 'array') as $item_id)
		{
			$table->delete($item_id);
			$table->reset();
		}

		$this->setRedirect($this->redirect_to, JText::_('PAGO_SHIPPING_RULE_DELETED'));
	}
}
?>
