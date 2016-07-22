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
 * Methods supporting a list of categories.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoModelCategories extends JModelList
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
				'lft', 'item.lft',
				'published', 'item.published',
				'featured', 'item.featured',
				'description', 'item.description',
				'modified_time', 'item.modified_time',
				'created_time', 'item.created_time'
			);
		}

		parent::__construct($config);
	}

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

	public function getCategory_list(){

		$this->_db->setQuery( "SELECT category.id AS `value`, category.name AS `text`
			FROM #__pago_categoriesi AS category
			WHERE `visibility` = 1
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

		$query->from( '`#__pago_categoriesi` AS item' );


		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				//$query->where('o.order_id = '.(int) $search);
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(item.name LIKE '.$search.'
									OR item.id = '.$db->Quote( $this->getState('filter.search') ).')
				');

			}
		}

		$filter_var = $this->getState('filter.primary_category');

		if (!empty($filter_var)) {
				$query->where('item.primary_category = ' . $filter_var );
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

		$filter_var = $this->getState('filter.price_type');

		if (!empty($filter_var)) {
				$filter_var = $db->Quote($filter_var);
				$query->where('item.price_type = ' . $filter_var );
		}

		/* this is needed because root should never be shown or able to be deleted
		 *  or else you wont be able to add new categories
		 */
		$query->where( 'item.id > 1 AND item.visibility = 1' );

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
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.type');
		$id	.= ':'.$this->getState('filter.price_type');
		$id .= ':'.$this->getState('filter.primary_category');

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
		parent::populateState('lft', 'asc');
	}

	//DEPRECIATED
	public function getGroup()
	{
		$cid = $this->getState('cid');

		$item = new stdClass;

		$item->group_id = 0;

		if( $cid ){
			$sql = "SELECT *
						FROM {$this->_table}
							WHERE $this->_table_key=$cid";

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

	public function getData()
	{
		if( $this->_items ) return $this->_items;

		$db = $this->_db;

		$page = $this->getState('page');
		$rows = $this->getState('rows');
		$sidx = $this->getState('sidx');
		$sord = $this->getState('sord');

		if( $sidx == 'order' || ( $sidx == 'order' && !$this->getState('_search') ) ){
			$this->_order = " ORDER BY lft {$sord}";
		} else {
			$this->_order = " ORDER BY {$sidx} {$sord}";
		}

		$start = ( $rows * $page ) - $rows;

		$where = array();

		// don't show root category AND not visibility category
		$where[] = 'parent_id != 0 AND visibility != 0';

		if ( $this->getState('_search') ) {
			$where[] = $this->get_search_query();
		}

		$where 	= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$sql = "SELECT SQL_CALC_FOUND_ROWS {$this->_table}.*
					FROM {$this->_table}
							{$where} {$this->_order}";

		$this->_items = $this->_getList( $sql, $start, $rows );


		$db->setQuery( 'SELECT FOUND_ROWS()' );
		$this->_total = $db->loadResult();

		$this->setState( 'total', $this->_total );
		$this->setState( 'totalpages', ceil( $this->_total/$rows ) );

		return $this->_items;
	}
	
	function copy_cat()
	{
		jimport('joomla.filesystem.file');
		Pago::load_helpers('imagehandler');
		$cids  = $this->getState('cid');
		$where = 'cat.id=' . array_shift($cids);

		if ( !empty( $cids)) 
		{
			foreach( $cids as $cid)
			{
				$where .= ' OR cat.id=' . $cid;
			}
		}

		$query = "SELECT cat.* FROM #__pago_categoriesi as cat WHERE $where";
		$this->_db->setQuery( $query );
		$cats = $this->_db->loadAssocList();
		$created_ids = array();

		foreach( $cats as $cat )
		{
		
			$preCid = $cat['id'];
			$cat['id'] = 0;
			$cat['name'] = $cat['name'] . ' ' . JText::_('copy');
			$cat['published'] = 0;
			$cat['alias'] = str_replace(" ", "-", $cat['name']);
			
			JFactory::getApplication()->input->post->set( 'params', $cat );
			$table = JTable::getInstance( 'categoriesi', 'Table' );
			$timestamp = time() + 86400;
			$cat['expiry_date'] = $timestamp;
			$table->setLocation( $cat['parent_id'], 'last-child');
			
			
			if (!$table->bind($cat))
			{
				return JError::raiseWarning(20, $table->getError());
			}
			
			if (!$table->store()) 
			{
				return JError::raiseWarning(20, $table->getError());
			}
			
			if ( !$table->rebuildPath( $table->id ) ) 
			{
				return JError::raiseWarning( 20, $table->getError() );
			}

			if ( !$table->rebuild( $table->id, $table->lft, $table->level, $table->path ) ) 
			{
				return JError::raiseWarning( 20, $table->getError() );
			}
			
			$qu = "SELECT LAST_INSERT_ID()";
			$this->_db->setQuery($qu);
			$latestCatId = $this->_db->loadResult();
			
			$prevCatMeta = "SELECT * FROM #__pago_meta_data WHERE type='category' AND id='" . $preCid . "'";
			$this->_db->setQuery($prevCatMeta);
			$prevCatMetaData = $this->_db->loadObjectList();
			
			if(count($prevCatMetaData) > 0)
			{
				$querymetaInsert = "INSERT INTO #__pago_meta_data"
					. "(`id`, `type`, `html_title`, `title`, `author`, `robots`, `keywords`, `description`) "
					. "VALUES ('" . $latestCatId . "', 'category', " . $this->_db->Quote($prevCatMetaData[0]->html_title) . ", " . $this->_db->Quote($prevCatMetaData[0]->title) . ", " . $this->_db->Quote($prevCatMetaData[0]->author) . ", " . $this->_db->Quote($prevCatMetaData[0]->robots) . ", " . $this->_db->Quote($prevCatMetaData[0]->keywords) . ", " . $this->_db->Quote($prevCatMetaData[0]->description) . ")";
				$this->_db->setQuery($querymetaInsert);
				$this->_db->Query();
			}


			
			// get item images
		 	$query = "SELECT * FROM #__pago_files as images
								WHERE type = 'category' AND item_id = {$preCid}";
			$this->_db->setQuery( $query );
			$images = $this->_db->loadAssocList();
			
			//$images[0]['item_id'] = $latestCatId;
			$model = JModelLegacy::getInstance( 'file', 'PagoModel');

			foreach ($images as $id => $file) 
			{
				$sdir = JPATH_SITE . "/media/pago/category/" . $preCid . "/";
				$src_folder = $sdir . $file['file_name'];
				if(is_file($src_folder))
				{
					if (!is_dir(JPATH_SITE . "/media/pago/category/" . $latestCatId))
					{
						mkdir(JPATH_SITE . "/media/pago/category/" . $latestCatId, 0755);
					}
					$dest_folder = JPATH_SITE . "/media/pago/category/" . $latestCatId . "/";
					
					
					$src = $sdir;
					$dst = $dest_folder;
					$dir = opendir($src); 
					while(false !== ( $newfile = readdir($dir)) ) { 
						if (( $newfile != '.' ) && ( $newfile != '..' )) { 
							
								copy($src . '/' . $newfile,$dst . '/' . $newfile); 
							
						} 
					} 
					closedir($dir); 

					// TO store file in Database
					$file['id'] = 0;
					$file['item_id'] = $latestCatId;
					$row = JTable::getInstance( 'files', 'Table' );
					if (!$row->bind($file))
					{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}

					//PagoHelper::saveContentPrep($row);
					if (!$row->id)
					{
						$row->ordering = $row->getNextOrder("`item_id` = {$row->item_id} AND `type` = '$row->type'");
					}

					if ( !$row->store() )
					{
						$this->setError($row->getError());
						return false;
					}

					
				}
				
			}
		}
		
		Pago::get_instance( 'categories' )->clear_cache();
	}

	public function saveorder($idArray = null, $lft_array = null)
	{

		// Get an instance of the table object.
		$table = JTable::getInstance( 'categoriesi', 'Table' );

		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());
			return false;
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
}
