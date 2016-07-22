<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of states.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoModelStates extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'state_id', 'item.state_id',
				'country_id', 'item.country_id',
				'state_name', 'item.state_name',
				'state_3_code', 'item.state_3_code',
				'state_2_code', 'item.state_2_code',
				'publish', 'item.publish'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'*'
			)
		);
		
		$query->from( '`#__pago_country_state` AS item' );

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(item.state_name LIKE '.$search.'
									OR item.state_id = '.$db->Quote( $this->getState('filter.search') ).')
				');
		}

		$filter_var = $this->getState('filter.publish');

		if (!empty($filter_var) || $filter_var === '0' ) {
				$query->where('item.publish = ' . $filter_var );
		}

		$filter_var = $this->getState('filter.country_id');

		if (!empty($filter_var) ) {
				$query->where('item.country_id = ' . $filter_var );
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		/*$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.type');
		$id	.= ':'.$this->getState('filter.price_type');
		$id .= ':'.$this->getState('filter.primary_category');*/

		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'States', $prefix = 'PagoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$filter = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $filter);

		$filter = $this->getUserStateFromRequest($this->context.'.filter.publish', 'filter_publish');
		$this->setState('filter.publish', $filter);

		$filter = $this->getUserStateFromRequest($this->context.'.filter.country_id', 'filter_country_id');
		$this->setState('filter.country_id', $filter);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('state_id', 'desc');
	}
}