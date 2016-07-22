<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

class pago_categoriesi
{
	protected $_nodes = array();
	protected $db     = null;
	protected $tbl    = null;
	protected $params = array();

	public function __construct()
	{
		$this->db = JFactory::getDBO();
		$this->tbl = '#__pago_categoriesi';
	}

	/**
	 * Get category by id
	 *
	 * @param int default 1
	 * @param int depth of children to get default 1. 0 for infinite
	 * @param boolean force reload of node from DB
	 * @param boolean output object list from DB instead of a pago_category_node object
	 *
	 * @return pago_category_node
	 */
	public function get( $id = 1, $depth = 1, $force_load = false, $object_list = false, $published = false)
	{
		
		// check to make sure that 0 or root actually mean 1
		if ( $id == 'root' || $id == 0 ) {
			$id = 1;
		}

		if ( $object_list ) {
			return $this->_load( $id, $depth, $object_list );
		}

		if ( !isset( $this->_nodes[$id] ) || $force_load ) {
			$this->_load( $id, $depth, $object_list, $published);
		}

		if ( isset( $this->_nodes[$id] ) ) {
			return $this->_nodes[$id];
		}

		return false;
	}

	/**
	 * Get category by path
	 *
	 * @param string path of category to get
	 * @param int depth of children to get default 1. 0 for infinite
	 * @param boolean force reload of node from DB
	 * @param boolean output object list from DB instead of a pago_category_node object
	 *
	 * @return pago_category_node
	 */
	public function get_by_path( $path, $depth = 1, $force_load = false, $object_list = false )
	{
		$query = 'SELECT id FROM ' . $this->tbl . ' WHERE published = 1 AND visibility = 1 AND path = ' . $this->db->quote( $path );

		$this->db->setQuery( $query );
		$id = $this->db->loadResult();

		if($id){
			if ( $object_list ) {
				return $this->_load( $id, $depth, $object_list, true );
			}

			if ( !isset( $this->_nodes[$id] ) || $force_load  ) {
				$this->_load( $id, $depth );
			}

			if ( isset( $this->_nodes[$id] ) ) {
				return $this->_nodes[$id];
			}
		}
		return false;
	}

	/**
	 * Get category by alias
	 *
	 * @param string alias of category to get
	 * @param int depth of children to get default 1. 0 for infinite
	 * @param boolean force reload of node from DB
	 * @param boolean output object list from DB instead of a pago_category_node object
	 *
	 * @return pago_category_node
	 */
	public function get_by_alias( $alias, $depth = 1, $force_load = false, $object_list = false )
	{
		$query = 'SELECT id FROM ' . $this->tbl . ' WHERE alias = ' . $this->db->quote( $alias );
		$this->db->setQuery( $query );
		$id = $this->db->loadResult();

		if ( $object_list ) {
			return $this->_load( $id, $depth, $object_list );
		}

		if ( !isset( $this->_nodes[$id] ) || $force_load  ) {
			$this->_load( $id, $depth );
		}

		if ( isset( $this->_nodes[$id] ) ) {
			return $this->_nodes[$id];
		}

		return false;
	}

	/**
	 * Check that a category exists
	 *
	 * @param int
	 *
	 * @return boolean True|False
	 */
	public function exists( $id )
	{
		// check if we already have the category so we don't have to do a query
		if ( isset( $this->_nodes[$id] ) ) {
			return true;
		}

		// since we haven't processed it before lets run a query to check if it exists
		$this->db = JFactory::getDBO();
		$this->db->setQuery( 'SELECT 1 FROM ' . $this->tbl . ' WHERE id = ' . (int) $id );

		if ( $this->loadResult() ) {
			return true;
		}

		return false;
	}
	
	
		/**
	 * Replace category parametrs from the first category to the second one
	 *
	 * @param cat_from - category from which to get parameters
	 * @param cat_to - category to which the parameters will be set
	 *
	 * @return category with replaced parameters
	 */
	function inherit_parameters_from_to($cat_from, $cat_to)
	{
		$cat_to->category_custom_layout = $cat_from->category_custom_layout;
		$cat_to->item_custom_layout = $cat_from->item_custom_layout;
		$cat_to->truncate_desc = $cat_from->truncate_desc;
		$cat_to->category_settings_category_title = $cat_from->category_settings_category_title;
		$cat_to->category_settings_product_counter = $cat_from->category_settings_product_counter;
		$cat_to->category_settings_category_description = $cat_from->category_settings_category_description;
		$cat_to->product_settings_product_title = $cat_from->product_settings_product_title;
		$cat_to->product_settings_product_image = $cat_from->product_settings_product_image;
		$cat_to->product_settings_link_to_product = $cat_from->product_settings_link_to_product;
		$cat_to->product_settings_featured_badge = $cat_from->product_settings_featured_badge;
		$cat_to->product_settings_quantity_in_stock = $cat_from->product_settings_quantity_in_stock;
		$cat_to->product_settings_short_desc = $cat_from->product_settings_short_desc;
		$cat_to->product_settings_short_desc_limit = $cat_from->product_settings_short_desc_limit;
		$cat_to->product_settings_desc = $cat_from->product_settings_desc;
		$cat_to->product_settings_desc_limit = $cat_from->product_settings_desc_limit;
		$cat_to->product_settings_sku = $cat_from->product_settings_sku;
		$cat_to->product_settings_price = $cat_from->product_settings_price;
		$cat_to->product_settings_discounted_price = $cat_from->product_settings_discounted_price;
		$cat_to->product_settings_attribute = $cat_from->product_settings_attribute;
		$cat_to->product_settings_media = $cat_from->product_settings_media;
		$cat_to->product_settings_downloads = $cat_from->product_settings_downloads;
		$cat_to->product_settings_rating = $cat_from->product_settings_rating;
		$cat_to->product_settings_category = $cat_from->product_settings_category;
		$cat_to->product_settings_read_more = $cat_from->product_settings_read_more;
		$cat_to->product_settings_add_to_cart = $cat_from->product_settings_add_to_cart;
		$cat_to->product_settings_add_to_cart_qty = $cat_from->product_settings_add_to_cart_qty;
		$cat_to->product_settings_fb = $cat_from->product_settings_fb;
		$cat_to->product_settings_tw = $cat_from->product_settings_tw;
		$cat_to->product_settings_pinterest = $cat_from->product_settings_pinterest;
		$cat_to->product_settings_google_plus = $cat_from->product_settings_google_plus;
		$cat_to->product_grid_extra_small = $cat_from->product_grid_extra_small;
		$cat_to->product_grid_small = $cat_from->product_grid_small;
		$cat_to->product_grid_medium = $cat_from->product_grid_medium;
		$cat_to->product_grid_large = $cat_from->product_grid_large;
		$cat_to->product_view_settings_product_title = $cat_from->product_view_settings_product_title;
		$cat_to->product_view_settings_product_image = $cat_from->product_view_settings_product_image;
		$cat_to->product_view_settings_featured_badge = $cat_from->product_view_settings_featured_badge;
		$cat_to->product_view_settings_quantity_in_stock = $cat_from->product_view_settings_quantity_in_stock;
		$cat_to->product_view_settings_short_desc = $cat_from->product_view_settings_short_desc;
		$cat_to->product_view_settings_short_desc_limit = $cat_from->product_view_settings_short_desc_limit;
		$cat_to->product_view_settings_desc = $cat_from->product_view_settings_desc;
		$cat_to->product_view_settings_desc_limit = $cat_from->product_view_settings_desc_limit;
		$cat_to->product_view_settings_sku = $cat_from->product_view_settings_sku;
		$cat_to->product_view_settings_price = $cat_from->product_view_settings_price;
		$cat_to->product_view_settings_discounted_price = $cat_from->product_view_settings_discounted_price;
		$cat_to->product_view_settings_attribute = $cat_from->product_view_settings_attribute;
		$cat_to->product_view_settings_media = $cat_from->product_view_settings_media;
		$cat_to->product_view_settings_downloads = $cat_from->product_view_settings_downloads;
		$cat_to->product_view_settings_rating = $cat_from->product_view_settings_rating;
		$cat_to->product_view_settings_category = $cat_from->product_view_settings_category;
		$cat_to->product_view_settings_add_to_cart = $cat_from->product_view_settings_add_to_cart;
		$cat_to->product_view_settings_add_to_cart_qty = $cat_from->product_view_settings_add_to_cart_qty;
		$cat_to->product_view_settings_product_review = $cat_from->product_view_settings_product_review;
		$cat_to->product_view_settings_related_products = $cat_from->product_view_settings_related_products;
		$cat_to->product_view_settings_fb = $cat_from->product_view_settings_fb;
		$cat_to->product_view_settings_tw = $cat_from->product_view_settings_tw;
		$cat_to->product_view_settings_pinterest = $cat_from->product_view_settings_pinterest;
		$cat_to->product_view_settings_google_plus = $cat_from->product_view_settings_google_plus;
		$cat_to->product_view_settings_related_num_of_products = $cat_from->product_view_settings_related_num_of_products;
		$cat_to->product_view_settings_related_title = $cat_from->product_view_settings_related_title;
		$cat_to->product_view_settings_related_category = $cat_from->product_view_settings_related_category;
		$cat_to->product_view_settings_related_image = $cat_from->product_view_settings_related_image;
		$cat_to->product_view_settings_related_short_text = $cat_from->product_view_settings_related_short_text;
		$cat_to->category_settings_image_settings = $cat_from->category_settings_image_settings;
		$cat_to->category_settings_product_image_settings = $cat_from->category_settings_product_image_settings;
		$cat_to->product_view_settings_image_settings = $cat_from->product_view_settings_image_settings;
		$cat_to->category_settings_category_image = $cat_from->category_settings_category_image;
		$cat_to->product_settings_link_on_product_image = $cat_from->product_settings_link_on_product_image;
		$cat_to->product_settings_product_per_page = $cat_from->product_settings_product_per_page;
		$cat_to->product_settings_product_title_limit = $cat_from->product_settings_product_title_limit;
		$cat_to->product_view_settings_product_title_limit = $cat_from->product_view_settings_product_title_limit;
		$cat_to->product_view_settings_product_image_zoom = $cat_from->product_view_settings_product_image_zoom;
		return $cat_to;
	}

	/**
	 *  Load categories based off id or params to get categories
	 *
	 *  @param int
	 *  @param int
	 *  @param boolean True to load output in object list from DB. False to load output in
	 *  pago_cateogry_node object
	 *
	 *  @return void
	 */
	protected function _load( $id, $depth = 1, $object_list = false, $published = false )
	{
		if ( !isset( $id ) ) {
			return false;
		}
		if ( $id == 'root' || $id == 0 ) {
			$cid = '1';
		} else {
			$cid = $id;
		}


		$query = 'SELECT n.* FROM ' . $this->tbl . ' as n, ' . $this->tbl . ' as p';

		$where = 'n.lft BETWEEN p.lft AND p.rgt';

		if ( !empty( $this->params ) ) {
			if ( (!isset( $this->params['published'] ) ||  $this->params['published'] == 1) && $published ) {
				$where .= ' AND n.published = 1 AND p.published = 1';
			}

			if ( isset( $this->params['alias'] ) ) {
				$where .= ' AND p.alias = ' . $this->db->quote( (string) $this->params['alias'] );
			} else if ( isset( $this->params['name_like'] ) ) {
				$where .= ' AND p.name LIKE ' .
					$db->quote( '%' . (string) $this->params['name_like'] . '%' );
			} else {
				$where .= ' AND p.id = ' . (int) $cid;
			}

		} else {
				$where .= ' AND p.id = ' . (int) $cid;
			if($published){
				$where .= ' AND n.published = 1 AND p.published = 1';
			}
		}
		$where .= ' AND n.visibility != 0 AND n.visibility != 0';
		
		$order = 'n.lft';

		if ( isset( $this->params['orderby'] ) ) {
			$order .= ' ' . (string) $this->params['orderby'];
		}

		if ( isset( $this->params['direction'] ) ) {
			$order .= ' ' . $this->params['direction'];
		} else {
			$order .= ' ASC';
		}

		if ( $depth ) {
			$where .= ' AND n.level <= p.level + ' . (int) $depth;
		}

		$query .= ' WHERE '. $where . ' ORDER BY ' . $order;

		

		$this->db->setQuery( $query );

		$results = $this->db->loadObjectList();


		if ( $object_list ) {
			return $results;
		}

		$childrenLoaded = false;

		if (count($results)) {
			foreach($results as $result)
			{
				if ( !isset( $this->_nodes[$result->id] ) ) {
					$this->_nodes[$result->id] = new pago_category_node( $result, $depth );

					if ( $result->id != $id && isset( $this->_nodes[$result->parent_id] ) ) {
						$this->_nodes[$result->parent_id]->add_child( $this->_nodes[$result->id] );
					}
				} else if ( $result->id == $id ) {
					$this->_nodes[$result->id] = new pago_category_node( $result, $depth );

					if ( $result->id != $id && isset( $this->_nodes[$result->parent_id] ) ) {
						$this->_nodes[$result->parent_id]->add_child( $this->_nodes[$result->id] );
					}
				}
			}
		} else {
			$this->_nodes[$id] = null;
		}
		unset( $results );
	}

	public function get_custom_layout( $id , $section='category', $layout='category')
	{
		if ($section == 'category')
		{
			$category = $this->get($id);

			if ($layout == 'category')
			{
				$matched_layout = $category->category_custom_layout;
			}
			else
			{
				$matched_layout = $category->item_custom_layout;
			}

			if ($matched_layout != "" && $matched_layout != '0' && $matched_layout != '1')
			{
				$layout = str_replace('.php', '', $matched_layout);

				return $layout;
			}
			else if ($matched_layout == 1)
			{
				return;
			}
			else
			{
				$parent_id = $category->parent_id;

				if ($parent_id)
				{
					$layout = $this->get_parent_layout($parent_id, $layout);

					return $layout;
				}
			}
		}

		if ($section == 'item' )
		{

			$item = PagoHelper::get_product($id);

			if ($item->item_custom_layout_inherit == 2)
			{

				$layout = $this->get_custom_layout($item->primary_category, 'category', $layout);
				return $layout;
			}
			else if($item->item_custom_layout != "" && $item->item_custom_layout != '0' && $item->item_custom_layout != '1')
			{
				$layout = str_replace('.php', '', $item->item_custom_layout);
				return $layout;

			}
			else
			{
				return;
			}
		}
	}

	public function get_parent_layout( $parent_id, $layout ='category' )
	{
		$pcategory = $this->get($parent_id);

		if ($layout == 'category')
		{
			$matched_layout = $pcategory->category_custom_layout;
		}
		else
		{
			$matched_layout = $pcategory->item_custom_layout;
		}

		if ($matched_layout != "" && $matched_layout != '0' && $matched_layout != '1')
		{
			$layout = str_replace('.php', '', $matched_layout);

			return $layout;
		}
		else if($matched_layout == 1)
		{
			return;
		}
		else
		{
			if ($pcategory->parent_id)
			{
				$parent_id = $pcategory->parent_id;
				$layout = $this->get_parent_layout($parent_id, $layout);

				return $layout;
			}
			else
			{
				return;
			}
		}
	}

	function getDefaultCategoryMedia( $catgeory_id )
	{
		// split sql into chunks so it is easier to customize from plugin
		$sql['columns'] = array(
			'files.`id` AS file_id',
			'files.`title` AS file_title',
			'files.`alias` AS file_alias',
			'files.`caption` AS file_caption',
			'files.`type` AS file_type',
			'files.`file_name` AS file_file_name',
			'files.`file_meta` AS file_file_meta'
		);
		$sql['from'] = array(
			'#__pago_files as files'
		);

		$sql['where'][] = 'files.`default` = 1';
		$sql['where'][] = 'AND files.`published` = 1';
		$sql['where'][] = 'AND files.`type` = "category"';
		$sql['where'][] = 'AND files.`item_id` = '.$catgeory_id;

		// build query after it has been passed to all the plugins
		$query = 'SELECT SQL_CALC_FOUND_ROWS ';
		$query .= implode( ', ', $sql['columns'] );
		$query .= ' FROM ' . implode( ', ', $sql['from'] );
		$query .= ' WHERE ' . implode( ' ', $sql['where'] );


		$this->db->setQuery( $query );
		$catData = $this->db->loadObjectList();

		return $catData;
	}
}

class pago_category_node
{
	public $id                   = null;
	public $parent_id            = null;
	public $lft                  = null;
	public $rgt                  = null;
	public $level                = null;
	public $path                 = null;
	public $name                 = null;
	public $alias                = null;
	public $description          = null;
	public $featured             = null;
	public $item_count           = null;
	public $meta_html_title      = null;
	public $meta_tag_title       = null;
	public $meta_tag_author      = null;
	public $meta_tag_robots      = null;
	public $meta_tag_keywords    = null;
	public $meta_tag_description = null;
	public $created_user_id      = null;
	public $created_time         = null;
	public $modified_user_id     = null;
	public $modified_time        = null;
	public $published            = null;
	public $access               = null;

	protected $_parent           = null;
	protected $_children         = array();
	protected $_left_sibling     = null;
	protected $_right_sibling    = null;
	protected $_path             = array();
	protected $_depth            = null;

	function __construct( $category = null, $depth = 1 )
	{
		if ( $category && is_object( $category ) ) {
			foreach( $category as $k => $v ) {
				$this->$k = $v;
			}

			$this->_depth = $depth;

			return true;
		}

		return false;
	}

	/**
	 * Set parent of category
	 */
	function set_parent( &$parent )
	{
		if ($parent instanceof pago_category_node || !is_null($parent)) {
			if (!is_null($this->_parent)) {
				$key = array_search($this, $this->_parent->_children);
				unset($this->_parent->_children[$key]);
			}

			if (!is_null($parent)) {
				$parent->_children[] = & $this;
			}

			$this->_parent = & $parent;

			$this->_path = $parent->get_path();
			$this->_path[] = $this->id.':'.$this->alias;

			if (count($parent->_children) > 1) {
				end($parent->_children);
				$this->_left_sibling = prev($parent->_children);
				$this->_left_sibling->_right_sibling = &$this;
			}
		}

	}

	function add_child( &$child )
	{
		if ( $child instanceof pago_category_node ) {
			$child->set_parent( $this );
		}
	}

	function remove_child( $id )
	{
		foreach( $this->_children as $k => $child ) {
			if ( $child->id == $id ) {
				unset( $this->_children[$k] );
			}
		}
	}

	function get_parent()
	{
		return $this->_parent;
	}

	function get_children()
	{
		return $this->_children;
	}

	function get_path()
	{
		return $this->_path;
	}

	function get_next_sibling()
	{
		if ( isset( $this->_left_sibling ) ) {
			return $this->_left_sibling;
		}

		return false;
	}

	function get_prev_sibling()
	{
		if ( isset( $this->_right_sibling ) ) {
			return $this->_right_sibling;
		}

		return false;
	}

	function has_children()
	{
		if ( count( $this->_children ) ) {
			return true;
		}

		return false;
	}

	function has_parent()
	{
		if ( isset($this->_parent) && $this->_parent instanceof pago_category_node ) {
			return true;
		}

		return false;
	}

	function get_created_user()
	{
		return JFactory::getUser( $this->create_user_id );
	}

	function get_modified_user()
	{
		return JFactory::getUser( $this->modified_user_id );
	}
}
