<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelTax extends JModelList
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$taxcid = JFactory::getApplication()->input->getInt('taxcid');

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'taxtmpl.*'
			)
		);

		$filter_var = $this->getState('filter.pgtax_class_id');

		if ($taxcid)
		{
			$filter_var = $taxcid;
		}

		if (!empty($filter_var))
		{
			$query->where('taxtmpl.pgtax_class_id = ' . $filter_var);
		}

		$query->from('`#__pago_tax_rates` AS taxtmpl');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	public function getTaxclasses()
	{
		$this->_db->setQuery("SELECT `pgtax_class_id` AS `value`, `pgtax_class_name` AS `text`
			FROM #__pago_tax_class
				ORDER BY `pgtax_class_id`");

		return $this->_db->loadAssocList();
	}

	public function getTable($type = 'tax', $prefix = 'PagoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		$taxcid = JFactory::getApplication()->input->getInt('taxcid');
		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		$pgtax_class_id = $this->getUserStateFromRequest(
			$this->context . '.filter.pgtax_class_id',
			'filter_pgtax_class_id',
			''
		);
		if ($taxcid)
		{
			$pgtax_class_id = $taxcid;
		}
		$this->setState('filter.pgtax_class_id', $pgtax_class_id);

		// List state information.
		parent::populateState('pgtax_id', 'desc');
	}
}
