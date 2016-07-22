<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * Pago  Component Pago  Model
 *
 * @author      notwebdesign
 * @package		Joomla
 * @subpackage	Pago
 * @since 1.5
 */
class PagoModelItemslist extends JModelLegacy
{
	var $_items = NULL;
	var $_item = NULL;
	var $_total = null;
	var $_pagination = null;

    /**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Sets limits for request
	 */
	function set_limits()
	{
		$app = JFactory::getApplication();

        // Get pagination request variables
       	$limit = $this->get_limit();
        $limitstart = JFactory::getApplication()->input->get( 'limitstart', 0, '', 'int' );


        $this->setState( 'limit', $limit );
        $this->setState( 'limitstart', $limitstart );
    }

	public function get( $property, $default = NULL )
	{
		if( $this->_item ) {
			return $this->_item;
		}

		$id         = $this->getState( 'id' );
		$dispatcher = KDispatcher::getInstance();

		// put query into an array for plugins to modify
		$sql = array();

		$sql['columns'] = array(
			'cats.id as cid',
			'cats.name as cname',
			'cats.path as path',
			'items.*',
		);

		$sql['from'] = array(
			'#__pago_categories_items AS cat_items_ref'
		);

		$sql['joins'] = array(
			'LEFT JOIN #__pago_categoriesi AS cats ON cats.id = cat_items_ref.category_id',
			'LEFT JOIN #__pago_items AS items ON cat_items_ref.item_id = items.id',
		);

		$sql['where'] = array(
			'cat_items_ref.item_id = '. $id
		);


		// Apply filters to sql
		$dispatcher->trigger( 'item_sql', array( &$sql ) );

		// join query together
		$query  = 'SELECT ' . implode( ', ', $sql['columns'] );
		$query .= ' FROM ' . implode( ', ', $sql['from'] );
		$query .= ' '. implode( ' ', $sql['joins'] );
		$query .= ' WHERE ' . implode( ' AND ', $sql['where'] );

		$dispatcher->do_action( 'item_query', array( $query ) );

		$this->_db->setQuery( $query );
		$data = $this->_db->loadObjectList();

		if ( !empty( $data ) ) {
			foreach( $data as $item ) {
				$categories[] = array(
					'id' => $item->cid,
					'name' => $item->cname,
					'path' => $item->path
				);
			}
		}

		$data = $data[0];

		$data->categories = array();

		if ( isset( $categories ) ) {
			$data->categories = $categories;
		}

		unset( $primary );
		unset( $categories );

		$this->_item = $data;

		return $this->_item;
	}

	function get_limit()
	{
		$cid = $this->getState( 'cid' );
		$app = JFactory::getApplication();
		$menu_config   = $app->getParams();
		$settingsCID = $menu_config->get('inherit_category');
		$limit = $app->input->get('limit');
	
		if($settingsCID=='0' || $settingsCID==''){
			if( !$cid ) 
				$cid = 1;
		}else
			$cid = $settingsCID;
		$cat_table = JTable::getInstance( 'categoriesi', 'Table' );
		$cats = $cat_table->getTree( $cid );

		$app = JFactory::getApplication();

        // Get pagination request variables
		if($limit)
		{
			return $limit;
		}
        else if($cats[0]->product_settings_product_per_page==0)
		{
        	return $app->getUserStateFromRequest( 'global.list.limit', 'limit',$app->getCfg( 'list_limit' ), 'int' );
		}
       	else
		{
       		return $cats[0]->product_settings_product_per_page;
		}
	}

	function get_list( $all = 0, $minPrice = 0, $maxPrice = 0 , $only_primary = 0)
	{
		if( $this->_items && !$all ) {
			return $this->_items;
		}

		$dispatcher = KDispatcher::getInstance();
		$user       = JFactory::getUser();
		$gid        = $user->get( 'aid', 0 );
		$cid        = $this->getState( 'cid' );
		$product_order_by = $this->getState( 'product_order_by' );
		
		if(is_array($cid)){
			foreach($cid as $k => $one_id){
				$one_id = (int)$one_id;

				$query = 'SELECT id FROM #__pago_categoriesi WHERE published = 1 AND visibility = 1 AND id = ' . $one_id;
				$this->_db->setQuery($query);
				if(!$this->_db->loadResult()){
					unset($cid[$k]);
				}
			}	
		}

		$show_all = true;
		$cids = '(';
		if(is_array($cid)){
			if(count($cid) > 0)
			{
				$show_all = false;
				foreach($cid as $one_id){
					$cids .= $one_id.",";
				}
				$cids = substr($cids, 0, -1);
				$cids .= ')';
			}
			else
			{
				$cids = '';
			}
			
		} else {
			if($cid != 0){
				$show_all = false;
			}
			$cids .= $cid.')';
		}
		
		$search     = $this->getState( 'search' );

		// split sql into chunks so it is easier to customize from plugin
		$sql['columns'] = array(
			'items.*',
			'files.`id` AS file_id',
			'files.`title` AS file_title',
			'files.`alias` AS file_alias',
			'files.`caption` AS file_caption',
			'files.`type` AS file_type',
			'files.`file_name` AS file_file_name',
			'files.`file_meta` AS file_file_meta'
		);
		$sql['from'] = array(
			'#__pago_items as items'
		);
		// check if we actually have a category id
		// 1 is root and 0 is unset. An AJAX script will only pass value 1
		if($only_primary){
			$sql['where'][] = 'items.`published` = 1 AND items.`visibility` = 1 AND items.`primary_category` in '.  $cids;
		}else if ( !$show_all ) {

			$sql['joins'][] = 'LEFT JOIN #__pago_categories_items as cat_items_ref ON '.
				'( cat_items_ref.`item_id` = items.`id` )';
			if($cids!='')
			{
				$sql['where'][] = 'cat_items_ref.`category_id` in '.  $cids;
			}
			$sql['where'][] = 'AND items.`published` = 1 AND items.`visibility` = 1';
		} else {
			$sql['joins'][] = 'LEFT JOIN #__pago_categoriesi as category ON ( items.`primary_category` = category.`id` )';
			$sql['where'][] = 'items.`published` = 1 AND items.`visibility` = 1 AND category.`published` = 1 AND category.`visibility` = 1';
		}
		$sql['joins'][] = 'LEFT JOIN #__pago_files as files ON '.
				'( files.`item_id` = items.`id` '.
				'AND files.`published` = 1 '.
				// This needs to get changed to use Joomla's ACL instead
				//'AND files.`access` <= '. (INT) $gid .' '.
				'AND files.`type` = \'images\' '.
				'AND files.`default` = 1)';


		if ( is_array( $search ) ) {
			if ( array_key_exists( 'name', $search ) ) {
				$search_value = $this->_db->escape( $search['name'] );
				$sql['where'][] = ' AND (items.name LIKE \'%'. $search_value .
				   	'%\' OR items.sku LIKE \'%'. $search_value . '%\' )';
				unset($search_value);
			}
		}
		$sql['group'] = array(
			'items.`id`'
		);

		switch ( $product_order_by ) {
			case 'latest':
				$sql['order'] = array('items.`created` DESC');
				break;
			case 'priceasc':
				$sql['order'] = array('items.`price` ASC');
				break;
			case 'pricedesc':
				$sql['order'] = array('items.`price` DESC');
				break;
			case 'random':
				$sql['order'] = array('RAND()');
				break;
			case 'name':
				$sql['order'] = array('items.`name` ASC');
				break;
			case 'ordering':
				$sql['order'] = array('items.`ordering` ASC');
				break;
			default:
				$sql['order'] = array('items.`created` DESC');
		}

		// Apply filters to sql
		$dispatcher->trigger( 'category_list_sql', array( &$sql ) );
		$join_field = '';
		$app = JFactory::getApplication();
		if ($app->isSite())
		{
			$join_field =" AND ((items.availibility_options  <> 4 OR (items.qty <> 0 OR items.availibility_date <> '0000-00-00')) AND (items.availibility_options  <> 1 OR (items.qty > 0)))";
			if($maxPrice != 0)
			{
				$join_field .="AND items.price >=" . $minPrice . " AND items.price <=" . $maxPrice;
			}	
		}
		
		$app 		   = JFactory::getApplication();
		$menu_config   = $app->getParams();
		$display_items = $menu_config->get('display_items'); // 1 :featured, 2 : on sale
		
		if($cid && !is_array($cid))
		{
			$settingsCategory = Pago::get_instance( 'categoriesi' )->get( $cid, 1, false, false, true);
			$display_items = $settingsCategory->category_view_display_items;
		}		
		
		if($display_items == '1')
		{
			$dateNow =  date( "Y-m-d");
			$sql['where'][] = 'AND items.`featured` = 1 AND items.`featured_start_date` <= "' . $dateNow . '" AND items.`featured_end_date` >= "' . $dateNow . '"';
		}
		else if($display_items == '2')
		{
			$sql['where'][] = 'AND items.`apply_discount` = 1 AND items.`disc_start_date` <= CURDATE() AND items.`disc_end_date` >= CURDATE()' ;
		}

		// build query after it has been passed to all the plugins
		$query = 'SELECT SQL_CALC_FOUND_ROWS ';
		$query .= implode( ', ', $sql['columns'] );
		$query .= ' FROM ' . implode( ', ', $sql['from'] );
		$query .= ' ' . implode( ' ', $sql['joins'] );
		$query .= ' WHERE ' . implode( ' ', $sql['where'] );
		$query .= $join_field;
		if ( !empty( $sql['group'] ) ) {
			$query .= ' GROUP BY ' . implode( ', ', $sql['group'] );
		}
		if ( !empty( $sql['order'] ) ) {
			$query .= ' ORDER BY ' . implode( ', ', $sql['order'] );
		}

		// Do action pre query
		$dispatcher->do_action( 'category_list_query', array( $query ) );

		if ( $all ) {
			$this->_db->setQuery( $query );
			return $this->_db->loadObjectList();
		}

		// Call set limits
		$this->set_limits();

		$this->_items = $this->_getList( $query, $this->getState( 'limitstart' ),
		 	 $this->get_limit() );

		$this->_db->setQuery('SELECT FOUND_ROWS();');
		$this->_total = $this->_db->loadResult();

		if($this->_items){
			foreach ($this->_items as $item) {
				$item->category = Pago::get_instance( 'categoriesi' )->get( $item->primary_category, 1, false, false, true);
			}
		}
		return $this->_items;
	}

	function get_item( $all = 0 )
	{
		if( $this->_items && !$all ) {
			return $this->_items;
		}

		$dispatcher = KDispatcher::getInstance();
		$user       = JFactory::getUser();
		$gid        = $user->get( 'aid', 0 );
		$cid        = $this->getState( 'cid' );
		$search     = $this->getState( 'search' );

		// split sql into chunks so it is easier to customize from plugin
		$sql['columns'] = array(
			'items.*',
			'files.`id` AS file_id',
			'files.`title` AS file_title',
			'files.`alias` AS file_alias',
			'files.`caption` AS file_caption',
			'files.`type` AS file_type',
			'files.`file_name` AS file_file_name',
			'files.`file_meta` AS file_file_meta'
		);
		$sql['from'] = array(
			'#__pago_items as items'
		);
		// check if we actually have a category id
		// 1 is root and 0 is unset. An AJAX script will only pass value 1
		if ( $cid > 1 ) {
			$sql['joins'][] = 'LEFT JOIN #__pago_categories_items as cat_items_ref ON '.
				'( cat_items_ref.`item_id` = items.`id` )';
			$sql['where'][] = 'cat_items_ref.`category_id` = '. (INT) $cid;
			$sql['where'][] = 'AND items.`published` = 1';
		} else {
			$sql['where'][] = 'items.`published` = 1';
		}
		$sql['joins'][] = 'LEFT JOIN #__pago_files as files ON '.
				'( files.`item_id` = items.`id` '.
				'AND files.`published` = 1 '.
				// This needs to get changed to use Joomla's ACL instead
				//'AND files.`access` <= '. (INT) $gid .' '.
				'AND files.`type` = \'images\' '.
				'AND files.`default` = 1)';


		if ( is_array( $search ) ) {
			if ( array_key_exists( 'name', $search ) ) {
				$search_value = $this->_db->escape( $search['name'] );
				$sql['where'][] = ' AND (items.name LIKE \'%'. $search_value .
				   	'%\' OR items.sku LIKE \'%'. $search_value . '%\' )';
				unset($search_value);
			}
		}
		$sql['group'] = array(
			'items.`id`'
		);

		switch ( JFactory::getApplication()->input->get( 'sortby' ) ) {
			case 'price-min':
				$sql['order'] = array('items.`price` ASC');
				break;
			case 'price-max':
				$sql['order'] = array('items.`price` DESC');
				break;
			case 'featured':
				$sql['where'][] = 'AND items.`featured` = 1';
				break;
			case 'latest':
				$sql['order'] = array('items.`created` DESC');
				break;
			default:
				$sql['order'] = array('items.`name` ASC');
		}


		// Apply filters to sql
		$dispatcher->trigger( 'category_list_sql', array( &$sql ) );
		$join_field = '';
		$app = JFactory::getApplication();
		if ($app->isSite())
		{
			$join_field =" AND ((items.availibility_options  <> 4 OR (items.qty <> 0 OR items.availibility_date <> '0000-00-00')) AND (items.availibility_options  <> 1 OR (items.qty > 0)))";
		}

		// build query after it has been passed to all the plugins
		$query = 'SELECT SQL_CALC_FOUND_ROWS ';
		$query .= implode( ', ', $sql['columns'] );
		$query .= ' FROM ' . implode( ', ', $sql['from'] );
		$query .= ' ' . implode( ' ', $sql['joins'] );
		$query .= ' WHERE ' . implode( ' ', $sql['where'] );
		$query .= $join_field;
		if ( !empty( $sql['group'] ) ) {
			$query .= ' GROUP BY ' . implode( ', ', $sql['group'] );
		}
		if ( !empty( $sql['order'] ) ) {
			$query .= ' ORDER BY ' . implode( ', ', $sql['order'] );
		}

		// Do action pre query
		$dispatcher->do_action( 'category_list_query', array( $query ) );

		if ( $all ) {
			$this->_db->setQuery( $query );
			return $this->_db->loadObjectList();
		}

		// Call set limits
		$this->set_limits();

		$this->_items = $this->_getList( $query, $this->getState( 'limitstart' ),
		 	 $this->get_limit() );

		$this->_db->setQuery('SELECT FOUND_ROWS();');
		$this->_total = $this->_db->loadResult();

		if($this->_items){
			foreach ($this->_items as $item) {
				$item->category = Pago::get_instance( 'categoriesi' )->get( $item->primary_category, 1, false, false, true);
			}
		}
		return $this->_items;
	}

	function getPagination()
	{
		// Load the content if it doesn't already exist
		if ( empty( $this->_pagination ) ) {

			// Call set limits
			$this->set_limits();

			jimport( 'joomla.html.pagination' );
			$this->_pagination = new JPagination( $this->_total, $this->getState( 'limitstart' ), $this->get_limit() );
		}
		return $this->_pagination;
	}

	function getItemCount($catid,$includeTabel=false)
	{
		if($includeTabel){
			jimport( 'joomla.database.table' );
			JTable::addIncludePath( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'tables' );
		}

		$cat_table = JTable::getInstance( 'categoriesi', 'Table' );

		$cats = $cat_table->getTree( $catid );

		if(is_array($cats) && count($cats)){
			foreach($cats as $cat)
			{
				$cat_id[] = $cat->id;
			}
			$catids = implode(",", $cat_id);

			$query = "SELECT count(*) FROM #__pago_categories_items AS ci LEFT JOIN #__pago_items as pi ON ci.item_id=pi.id WHERE ci.category_id in (" . $catids . ") AND pi.published=1";
			$this->_db->setQuery( $query );
			return $this->_db->loadResult();
		}
		return "";
	}
	public function getRelatedProduct($item,$limit=5){

		$relatedProduct = false;
		$relatedProductIds = array();
		$relatedProductIds = json_decode($item->related_items);

		$relatedCatIds = json_decode($item->related_category);
		
		$relatedCategory = false;

		if(is_array($relatedCatIds) && count($relatedCatIds) > 0){

			$relatedCatIdsSql = '`primary_category` IN (';
    		foreach ($relatedCatIds as $relatedCatId) {

    			$relatedCatIdsSql .= $relatedCatId->id.',';
    		}
        	$relatedCatIdsSql = substr($relatedCatIdsSql, 0, -1);
			$relatedCatIdsSql .= ') ';
			
			$catProductSql = "SELECT id FROM #__pago_items as items
						WHERE {$relatedCatIdsSql}
							AND `published` = 1
							AND `visibility` = 1";

			$this->_db->setQuery( $catProductSql );
			$catRelatedProducts = $this->_db->loadObjectList();
			if($catRelatedProducts){
				$relatedCategory = true;
				$catRelatedProductsIds = array();
				foreach ($catRelatedProducts as $catRelatedProdusct) {
					$catRelatedProductsIds[] = $catRelatedProdusct;
				}
			}
		}

		if($relatedCategory){
			if(is_array($relatedProductIds) && count($relatedProductIds) > 0){
				$relatedProductIds = array_merge($catRelatedProductsIds,$relatedProductIds);
			}else{
				$relatedProductIds = $catRelatedProductsIds; 	
			}
		}

		$relatedProductIdsSql = '';
		if(is_array($relatedProductIds) && count($relatedProductIds) > 0){

			$relatedProductIdsSql = ' AND items.`id` IN (';
    		foreach ($relatedProductIds as $relatedProductId) {

    			$relatedProductIdsSql .= $relatedProductId->id.',';
    		}
        	$relatedProductIdsSql = substr($relatedProductIdsSql, 0, -1);
			$relatedProductIdsSql .= ') ';
		}else{
			return false;
		}

		$sql = "SELECT items.*, files.`id` AS file_id,
			files.`title` AS file_title, files.`alias` AS file_alias,
			files.`caption` AS file_caption, files.`type` AS file_type,
			files.`file_name` AS file_file_name, files.`file_meta` AS file_file_meta,
			category.`name` AS category_name
				FROM #__pago_items as items
					LEFT JOIN #__pago_files as files
					ON (files.`item_id` = items.`id`)
					LEFT JOIN #__pago_categoriesi as category
					ON (category.`id` = items.`primary_category`)
						WHERE items.`visibility` = 1
							AND items.`published` = 1
							AND ( files.`published` = 1 OR files.`published` is null )
							AND ( files.`type` = 'images' OR files.`type` is null )
							AND ( files.`default` = 1 OR files.`default` is null )
							{$relatedProductIdsSql}
								ORDER BY RAND()
								LIMIT " . $limit;

			$this->_db->setQuery( $sql );
			$relatedProduct = $this->_db->loadObjectList();
		
		return $relatedProduct;
	}
}
?>
