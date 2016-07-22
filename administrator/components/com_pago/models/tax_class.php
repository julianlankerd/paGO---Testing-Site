<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelTax_class extends JModelList
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

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'taxtmpl.*'
			)
		);

		$query->from('`#__pago_tax_class` AS taxtmpl');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	public function getTable($type = 'tax_class', $prefix = 'PagoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('pgtax_class_id', 'desc');
	}
}
