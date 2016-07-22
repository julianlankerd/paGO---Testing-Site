<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelGroups extends JModelList
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
				'group_id', 'item.group_id',
				'name', 'item.name',
				'description', 'item.description',
				'isdefault', 'item.isdefault',
				'created', 'item.created',
				'modified', 'item.modified'
			);
		}

		parent::__construct($config);
	}
	// Why are there category queries in the groups model?
	public function getSecondary_categories(){

		$sql = "
		SELECT cats_items.item_id,group_concat(cats.name SEPARATOR ' | ') as names
			FROM #__pago_categories_items as cats_items
			LEFT JOIN #__pago_categoriesi AS cats ON cats_items.category_id = cats.id
				GROUP BY cats_items.item_id
		";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList( 'item_id' );
	}

	// Why are there category queries in the groups model?
	public function getCategory_list(){

		$this->_db->setQuery( "SELECT category.id AS `value`, category.name AS `text`
			FROM #__pago_categoriesi AS category
				ORDER BY `name`" );

		return $this->_db->loadAssocList();
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

		$query->from( '`#__pago_groups` AS item' );

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(item.name LIKE '.$search.'
									OR item.group_id = '.$db->Quote( $this->getState('filter.search') ).')
				');
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
	public function getTable($type = 'Groups', $prefix = 'PagoTable', $config = array())
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
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('group_id', 'desc');
	}

	function getGroup()
	{
		$cid = $this->getState('cid');

		$item = new stdClass;

		$item->group_id = 0;

		if( $cid ){
			$sql = "SELECT *
						FROM #__pago_groups
							WHERE group_id=$cid";

			$item = $this->_getList( $sql, 0, 1 );
			$item = $item[0];

			$sql = "SELECT *
						FROM #__pago_groups_users
							WHERE group_id={$cid}";

			$members_data = $this->_getList( $sql, 0, 1000 );

			$members = false;

			if(is_array($members_data))
			foreach( $members_data as $member ){
				$members[] = $member->user_id;
			}

			$item->members = $members;
		}

		return $item;
	}

	function getMemberlist( $query )
	{
		if(!$query) return false;

		$db = JFactory::getDBO();
		$query = "SELECT id, username FROM #__users WHERE name LIKE '{$query}%' ORDER BY username";

		$db->setQuery($query);
		$result = $db->loadAssocList( 'id' );

		if( !empty( $result ) ){
			 foreach( $result as $k=>$v ){
				 $prepped[ $k ] = $v[ 'username' ];
			 }

			 return $prepped;
		}

		return false;
	}
}