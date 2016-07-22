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
 * Methods supporting a list of attributes.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoModelAttributes extends JModelList
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
				'id', 'item.id',
				'name', 'item.name',
				'type', 'item.type',
				'created', 'item.created',
				'modified', 'item.modified',
				'ordering', 'item.ordering'
			);
		}

		parent::__construct($config);
	}

	public function getSecondary_categories(){
		exit(); // not used
		$sql = "
		SELECT cats_items.item_id,group_concat(cats.name SEPARATOR ' | ') as names
			FROM #__pago_categories_items as cats_items
			LEFT JOIN #__pago_categoriesi AS cats ON cats_items.category_id = cats.id
				GROUP BY cats_items.item_id
		";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList( 'item_id' );
	}

	public function getCategory_list(){
		exit(); // not used
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

		$query->from( '`#__pago_attr` AS item' );

		// Filter by search in title
		$search = $this->getState('filter.search');
		$query->where('item.attr_enable = 1');
		$query->where('item.for_item = 0');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('o.order_id = '.(int) $search);
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(item.name LIKE '.$search.'
									OR item.id = '.$db->Quote( $this->getState('filter.search') ).')
				');

			}
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
	public function getTable($type = 'Items', $prefix = 'PagoTable', $config = array())
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

		$primary_category = $this->getUserStateFromRequest($this->context.'.filter.primary_category', 'filter_primary_category', '');
		$this->setState('filter.primary_category', $primary_category);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '');
		$this->setState('filter.type', $type);

		$price_type = $this->getUserStateFromRequest($this->context.'.filter.price_type', 'filter_price_type', '');
		$this->setState('filter.price_type', $price_type);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('ordering', 'asc');
	}

	public function remove(){
		if ( $this->getState('cid') ) {
			
			$db		= $this->getDbo();
			$deletedIds = array();

			foreach ( $this->getState('cid') as $id ) {
				
				$query	= $db->getQuery(true);
				
				$query->delete($db->quoteName('#__pago_attr'));
				$query->where("`id` = {$id}");
				
				$db->setQuery($query);	
				
				if ( $db->execute() ) {
					
					// delete attr options
					$model = JModelLegacy::getInstance('Attribute','PagoModel');
					$model->deleteAttrOpt($id);
					$model->deleteAttrRel($id);
				}
			}
			return true;
		}
		return false;
	}
	
	public function checkCustomAttrReq($attrId){
		$db = JFactory::getDBO();
		$required = '';
			
		$query = "SELECT required FROM #__pago_attr WHERE #__pago_attr.id = ".$attrId;
		$db->setQuery( $query );
		$required = $db->loadAssocList();
			
		return $required;
	}
	
	public function checkAttrRequiredAlert($attrId){
		$db = JFactory::getDBO();
			
		$query = "SELECT id FROM #__pago_attr_opts WHERE #__pago_attr_opts.attr_id = ".$attrId;
		$db->setQuery( $query );
		$option = $db->loadAssocList();
		if(!$option){
			return true;
		}
		else{
			return false;
		}		
	}
	
	function sortAttributes($itemIds)
	{
		$db = JFactory::getDbo();
		$itemIds = str_replace(',', '', $itemIds);
		$i=1;
		
		foreach ($itemIds as $itemId) 
		{			
			$sql = "UPDATE #__pago_attr SET ordering = " . $i . " WHERE id = " . $itemId;
			$db->setQuery( $sql );
			$asd = $db->query();
			$i++;
		}		

 	}
	
	function sortAttributesOptions($itemIds)
	{
		$db = JFactory::getDbo();
		$i=1;
		
		foreach ($itemIds as $itemId) 
		{			
			$sql = "UPDATE #__pago_attr_opts SET ordering = " . $i . " WHERE id = " . $itemId;
			$db->setQuery( $sql );
			$asd = $db->query();
			$i++;
		}		

 	}
	
	// public function removeCustomAttr($attrId){	
	// 		$db		= $this->getDbo();
	// 		$deletedIds = array();

	// 		foreach ( $this->getState('cid') as $id ) {
				
	// 			$query	= $db->getQuery(true);
				
	// 			$query->delete($db->quoteName('#__pago_attr'));
	// 			$query->where("`id` = {$id}");
				
	// 			$db->setQuery($query);	
				
	// 			if ( $db->execute() ) {
					
	// 				// delete attr options
	// 				$model = JModel::getInstance('Attribute','PagoModel');
	// 				$model->deleteAttrOpt($id);
	// 			}
	// 		}
	// 		return true;
	// 	}
	// 	return false;
	// }
}