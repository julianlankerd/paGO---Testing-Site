<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelItems extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'item.id',
				'sku', 'item.sku',
				'published', 'item.published',
				'name', 'item.name',
				'type', 'item.type',
				'primary_category', 'item.primary_category',
				'price', 'item.price',
				'created', 'item.created',
				'ordering', 'item.ordering',
				'item_ordering', 'item.item_ordering'
			);
		}

		parent::__construct($config);
	}

	public function getSecondary_categories()
	{
		$sql = "
		SELECT cats_items.item_id,group_concat(cats.name SEPARATOR ' | ') as names
			FROM #__pago_categories_items as cats_items
			LEFT JOIN #__pago_categoriesi AS cats ON cats_items.category_id = cats.id
				GROUP BY cats_items.item_id
		";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList( 'item_id' );
	}

	public function getCategory_list()
	{
		$cat_table = JTable::getInstance( 'categoriesi', 'Table' );
		$cats = $cat_table->getTree( 1 );
		foreach($cats as $cat)
		{
			$cat_name = str_repeat(' - ', ( ( $cat->level ) ) );
			$cat_name .= '' . $cat->name;
			$categoriesTree[] = array(
				'value' => $cat->id,
				'text' => $cat_name
			);
		}
		return $categoriesTree;
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
				'DISTINCT item.*'
			)
		);

		$query->from( '`#__pago_items` AS item' );


		// Join over the item type
		$query->select( 'types.name AS type_name' );

		$query->join(
			'LEFT',
			'`#__pago_item_types` AS types ON item.type = types.id'
		);

		// Join over the primary category
		$query->select( 'category.name AS primary_category_name' );

		$query->join(
			'LEFT',
			'`#__pago_categoriesi` AS category ON item.primary_category = category.id'
		);
		
		$query->join(
			'LEFT',
			'`#__pago_categories_items` AS catitems ON catitems.item_id = item.id'
		);
		
		$dispatcher = KDispatcher::getInstance();
		$dispatcher->trigger('backend_item_extra_fields_query', array($query));

		// Filter by search in title
		$search = $this->getState('filter.search');

		$query->where('item.visibility != 0');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('o.order_id = '.(int) $search);
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$custom_search_query = $dispatcher->trigger('backend_item_extra_fields_search', array( $search));
				
				if(isset($custom_search_query[0]) && $custom_search_query[0]!="")
				{
					$query->where('(item.name LIKE ' . $search . ' OR item.sku LIKE ' . $search . ' OR item.description LIKE ' . $search . ' OR item.content LIKE ' . $search . ' OR '.$cws_search_query[0].' )');
				}
				else
				{
					$query->where('(item.name LIKE ' . $search . ' OR item.sku LIKE ' . $search . ' OR item.description LIKE ' . $search . ' OR item.content LIKE ' . $search . ' )');
				}

			}
		}

		$filter_var = $this->getState('filter.primary_category');

		if (!empty($filter_var)) 
		{
			$cat_table = JTable::getInstance('categoriesi', 'Table');
			$cats = $cat_table->getTree($filter_var);
			
			foreach($cats as $cat)
			{
				$cat_id[] = $cat->id;
			}
			
			if (isset($cat_id) && count($cat_id) > 0)
			{
				$catids = implode(",", $cat_id);
				$query->where('catitems.category_id in (' . $catids . ')');
			}

		}


		$filter_var = $this->getState('filter.type');

		if (!empty($filter_var)) {
				$filter_var = $db->Quote($filter_var);
				$query->where('item.type = ' . $filter_var );
		}

		$filter_var = $this->getState('filter.published');

		if (!empty($filter_var) || $filter_var === '0' ) {
				$query->where('item.published = ' . $filter_var );
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->group('item.id');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.type');
		$id	.= ':'.$this->getState('filter.price_type');
		$id .= ':'.$this->getState('filter.primary_category');

		return parent::getStoreId($id);
	}

	public function getTable($type = 'Items', $prefix = 'PagoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$primary_category = $this->getUserStateFromRequest(
			$this->context.'.filter.primary_category',
			'filter_primary_category',
			''
		);
		$this->setState('filter.primary_category', $primary_category);

		$published = $this->getUserStateFromRequest(
			$this->context.'.filter.published',
			'filter_published',
			''
		);
		$this->setState('filter.published', $published);

		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '');
		$this->setState('filter.type', $type);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('item_ordering', 'asc');
	}

	function deleteItemsByCategory( $categoryId ) {
		$db	= $this->getDbo();

		$query = "SELECT `id`
			FROM #__pago_items
				WHERE `primary_category` = {$categoryId}";
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		if($items){
			$ids = array();
			foreach($items as $item){
				$ids[] = $item->id;
			}
			$this->deleteItems($ids);	
		}
		return true;
	}
	
	function deleteItems( $ids ) {

		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$db = JFactory::getDBO();

		$table = JTable::getInstance( 'Items', 'Table' );

		foreach( $ids as $item_id ){
			// delete item images
			$fielModel = JModelLegacy::getInstance( 'File', 'PagoModel' );
			$fielModel->delete_all( $item_id , array("images", "video", "download"));

			//delete item attrs
			$this->deleteItemsAttrs($item_id);

			//delete categories relations 
			$db->setQuery( "DELETE FROM #__pago_categories_items
			WHERE item_id = {$item_id}" );
			$db->query();

			//delete meta tag
			$db->setQuery( "DELETE FROM #__pago_meta_data
			WHERE id = {$item_id} AND type = 'item'" );
			$db->query();

			$table->delete( $item_id );
			$table->reset();
		}
	}

	function deleteItemsAttrs( $itemId ) {
		$db = JFactory::getDBO();	
		jimport('joomla.filesystem.folder');

		$query="SELECT id FROM #__pago_product_varation 
		WHERE item_id = {$itemId}";

		$this->_db->setQuery( $query );

		$variationImagesPaths = $this->_db->loadObjectList();
		
		if(count($variationImagesPaths)>0){
			foreach($variationImagesPaths as $variationImagesPath){
				$variationImgsPath=JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $variationImagesPath->id.DIRECTORY_SEPARATOR;
				if ( JFolder::exists( $variationImgsPath )){
					$ret = JFolder::delete($variationImgsPath);
				}
			}
		}

		//delete item arrts 
		$db->setQuery( "DELETE FROM #__pago_attr
			WHERE for_item = {$itemId}" );
		$db->query();

		//delete item varations
		$db->setQuery( "DELETE FROM #__pago_product_varation
			WHERE item_id = {$itemId}" );
		$db->query();

		//delete item attr opts
		$db->setQuery( "DELETE FROM #__pago_attr_opts
			WHERE for_item = {$itemId}" );
		$db->query();

		//delete item var_rel
		$db->setQuery( "DELETE FROM #__pago_product_varation_rel
			WHERE item_id = {$itemId}" );
		$db->query();

	}

	function sortItems($itemIds)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$itemIds = str_replace(',', '', $itemIds);

		$itemsIdByCat = array();

		$result = array();

		$i=1;
		
		foreach ($itemIds as $itemId) {			
			$sql = "UPDATE #__pago_items
			 			SET item_ordering = " . $i . " WHERE id = " . $itemId;

			$this->_db->setQuery( $sql );
			$asd = $this->_db->query();
			$i++;
		}
		
		foreach ($itemIds as $itemId) {			
			$query="SELECT primary_category FROM #__pago_items 
				WHERE id = " . $itemId;

			$this->_db->setQuery( $query );
			$result = $this->_db->loadObject();


			$itemsIdByCat[$result->primary_category][] = $itemId;
		}

		foreach ($itemsIdByCat as $catId => $itemId) {
			$i = 1;
			foreach ($itemsIdByCat[$catId]  as $itemId) {
			 	$sql = "UPDATE #__pago_items
			 			SET ordering = " . $i . " WHERE id = " . $itemId;

				$this->_db->setQuery( $sql );
				$asd = $this->_db->query();
				$i++;
			}
		}
		return true;
	}
}
