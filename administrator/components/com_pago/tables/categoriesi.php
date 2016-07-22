<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

jimport( 'joomla.filter.filteroutput' );
jimport( 'joomla.database.tablenested' );

class TableCategoriesi extends JTableNested
{
	public $id;
	public $path;
	public $level;
	public $name;
	public $description;
	public $item_count;
	public $created_user_id;
	public $created_time;
	public $modified_user_id;
	public $modified_time;
	public $image;
	public $published = 1;
	public $featured;
	public $visibility;
	public $expiry_date;
	public $category_custom_layout;
	public $item_custom_layout;
	public $category_settings_category_title;
	public $category_settings_product_counter;
	public $category_settings_category_description;
	public $product_settings_product_title;
	public $product_settings_product_image;
	public $product_settings_link_to_product;
	public $product_settings_featured_badge;
	public $product_settings_quantity_in_stock;
	public $product_settings_short_desc;
	public $product_settings_short_desc_limit;
	public $product_settings_desc;
	public $product_settings_desc_limit;
	public $product_settings_sku;
	public $product_settings_price;
	public $product_settings_discounted_price;
	public $product_settings_attribute;
	public $product_settings_media;
	public $product_settings_downloads;
	public $product_settings_rating;
	public $product_settings_category;
	public $product_settings_read_more;
	public $product_settings_add_to_cart;
	public $product_settings_add_to_cart_qty;
	public $product_settings_fb;
	public $product_settings_tw;
	public $product_settings_pinterest;
	public $product_settings_google_plus;
	public $product_grid_extra_small;
	public $product_grid_small;
	public $product_grid_medium;
	public $product_grid_large;
	public $product_view_settings_product_title;
	public $product_view_settings_product_image;
	public $product_view_settings_featured_badge;
	public $product_view_settings_quantity_in_stock;
	public $product_view_settings_short_desc;
	public $product_view_settings_short_desc_limit;
	public $product_view_settings_desc;
	public $product_view_settings_desc_limit;
	public $product_view_settings_sku;
	public $product_view_settings_price;
	public $product_view_settings_discounted_price;
	public $product_view_settings_attribute;
	public $product_view_settings_media;
	public $product_view_settings_downloads;
	public $product_view_settings_rating;
	public $product_view_settings_category;
	public $product_view_settings_add_to_cart;
	public $product_view_settings_add_to_cart_qty;
	public $product_view_view_settings_product_review;
	public $product_view_view_settings_related_products;
	public $product_view_settings_related_num_of_products;
	public $product_view_settings_related_title;
	public $product_view_settings_related_category;
	public $product_view_settings_related_image;
	public $product_view_settings_related_short_text;
	public $product_view_settings_fb;
	public $product_view_settings_tw;
	public $product_view_settings_pinterest;
	public $product_view_settings_google_plus;
	public $category_settings_image_settings;
	public $category_settings_product_image_settings;
	public $product_view_settings_image_settings;
	public $category_settings_category_image;
	public $product_settings_link_on_product_image;
	public $product_settings_product_per_page;
	public $product_settings_product_title_limit;
	public $product_view_settings_product_title_limit;
	public $view_settings_product_image_zoom;

	public function __construct( $db )
	{
		parent::__construct( '#__pago_categoriesi', 'id', $db );
	}

	/**
	 * Override check and make sure we have a name and alias for the category
	 */
	public function check()
	{
		if ( trim( $this->name ) == '' ) {
			$this->setError( JText::_('PAGO_CATEGORY_ERROR_MUST_CONTAIN_NAME') );
			return false;
		}

		$this->create_alias();

		return true;
	}

	/**
	 * Override store to make sure we have an unique alias and created modified times
	 */
	public function store( $updatenulls = false )
	{
		$user = JFactory::getUser();

		if ( $this->id ) {
			$this->modified_time = date( 'Y-m-d H:i:s', time() );
			$this->modified_user_id = $user->get( 'id' );
		} else {
			$this->created_time = date( 'Y-m-d H:i:s', time() );
			$this->created_user_id = $user->get( 'id' );
		}

		return parent::store( $updatenulls );
	}

	public function create_alias()
	{
		$this->alias = trim( $this->alias );
		if ( empty ( $this->alias ) ) {
			$this->alias = $this->name;
		}

		// setup some common replacements so the alias makes some sense
		$pattern = array(
			'/&/',
			'/^\s/', // remove extra spaces at the begining
			'/-/',
			'/\\\/',
			'/\//'
		);
		$replace = array(
			'and',
			'',
			' '
		);

		$this->alias = preg_replace( $pattern, $replace, $this->alias );

		// now we can filter output we may want to do the filtering here since we are already doing
		// a preg_replace
		$this->alias = JFilterOutput::stringURLSafe( $this->alias );

		if ( trim( str_replace( '-', '', $this->alias ) ) == '' || !$this->unique_alias() ) {
			$datenow = JFactory::getDate();
			$this->alias .= $datenow->Format( "Y-m-d-H-M-S" );
		}

	}

	public function get_item_count()
	{
		$query = 'SELECT COUNT(DISTINCT item_ref.item_id) '.
			'FROM #__pago_categories_items as item_ref '.
			'LEFT JOIN #__pago_items as item ON item.id = item_ref.item_id '.
			'WHERE item.published = 1 AND category_id IN ( '.
			'SELECT cat.id FROM #__pago_categoriesi as cat '.
			'LEFT JOIN #__pago_categoriesi as parent on parent.id = '. (int) $this->id .
			' WHERE cat.lft BETWEEN parent.lft AND parent.rgt )';
		// make sure everything gets converted
		$query = preg_replace( '/#__/', $this->_db->getPrefix(), $query );
		$this->_db->setQuery( $query );
		$this->item_count = $this->_db->loadResult();
		$this->store();
	}

	public function unique_alias()
	{
		$query = 'SELECT id FROM ' . $this->_tbl . ' WHERE alias = \'' . $this->alias . '\'';
		$this->_db->setQuery( $query );

		$alias_id = $this->_db->loadResult();
		if ( $alias_id == $this->id || !$alias_id ) {
			return true;
		}

		return false;
	}
	/**
	 * Check that we don't have any products attached to this category
	 * TODO: add ability to check for categories with items attached
	 */
	public function can_delete( $id )
	{
		if ( $id == 1 ) {
			return false;
		}
		return true;
	}

	public function getTree($pk = null, $diagnostic = false)
	{
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;
 
        // Get the node and children as a tree.
        $select = ($diagnostic) ? 'SELECT n.'.$k.', n.parent_id, n.level, n.lft, n.rgt' : 'SELECT n.*';
        $this->_db->setQuery(
                $select .
                ' FROM `'.$this->_tbl.'` AS n, `'.$this->_tbl.'` AS p' .
                ' WHERE n.lft BETWEEN p.lft AND p.rgt AND n.visibility != 0' .
                ' AND p.'.$k.' = '.(int) $pk .
                ' ORDER BY n.lft'
        );
        $tree = $this->_db->loadObjectList();
 
        // Check for a database error.
        if ($this->_db->getErrorNum()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
        }
 
        return $tree;
	}
	public function getTreeWithoutParent($pk = null, $diagnostic = false)
	{
        // Initialise variables.
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;
 
        // Get the node and children as a tree.
        $select = ($diagnostic) ? 'SELECT n.'.$k.', n.parent_id, n.level, n.lft, n.rgt' : 'SELECT n.*';
        $this->_db->setQuery(
                $select .
                ' FROM `'.$this->_tbl.'` AS n, `'.$this->_tbl.'` AS p' .
                ' WHERE n.lft BETWEEN p.lft AND p.rgt AND n.visibility != 0 AND n.parent_id != 0' .
                ' AND p.'.$k.' = '.(int) $pk .
                ' ORDER BY n.lft'
        );
        $tree = $this->_db->loadObjectList();
 
        // Check for a database error.
        if ($this->_db->getErrorNum()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
        }
 
        return $tree;
	}

	public function deleteRelations($catId){

		// delete categories item relations
		$sql = "DELETE FROM #__pago_categories_items WHERE category_id = " .$catId;
		$this->_db->setQuery( $sql );

		$this->_db->query();

		// delete categories attr relations
		$sql = "DELETE FROM #__pago_attr_categories WHERE category_id = " .$catId;
		$this->_db->setQuery( $sql );

		$this->_db->query();

		// delete categories coupon relations
		$sql = "DELETE FROM #__pago_coupon_categories WHERE category_id = " .$catId;
		$this->_db->setQuery( $sql );

		$this->_db->query();
	}
}
