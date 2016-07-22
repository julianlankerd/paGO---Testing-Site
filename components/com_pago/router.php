<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

/**
 * This will help to easily convert to the new routing that is being worked on in joomla.
 */
abstract class pago_route
{
	protected $segments = array();
	protected $db;

	public function __construct()
	{
		$this->db = JFactory::getDBO();
	}

	/**
	 * Functions to be implemented by children classes
	 */
	abstract function build( &$query );
	abstract function parse( $segments );

	protected function sef_url( $url )
	{
		if ( is_array( $url ) ) {
			$url2 = array();
			foreach ( (array) $url as $key => $seg ) {
				$url2[$key] = $this->sef_url( $seg );
			}
			$url = $url2;
			return (array) $url;
		}

		$url = utf8_decode( $url );
		$url = htmlentities( $url, ENT_COMPAT, 'UTF-8' );
		$url = preg_replace(
			'/&(..?)(acute|grave|slash|cedil|circ|ring|tilde|uml|lig);/',
			'$1',
			$url
		);
		$url = preg_replace( '/&[^;]*;/', '', $url );
		$url = preg_replace( '/\s-\s/', '-', $url );
		$url = preg_replace( '/[\s]+/', '-', $url );
		$url = preg_replace( '/[.:,;+&#\/*]/', '-', $url );
		$url = preg_replace( '/[^a-z0-9_-]/', '', strtolower( $url ) );
		$url = preg_replace( '/-+/', '-', $url );

		return $url;
	}

	protected function de_joomalize( $str )
	{
		return str_replace( ':', '-', $str );
	}
}

class route_category extends pago_route
{
	protected $id = 0;

	public function build( &$query )
	{
		if (  isset( $query['cid'] ) ) {
			$this->id = (int) $query['cid'];
		}

		// Can't route root category
		if ( $this->id < 1 ) {
		unset($query['view']); 
			return $this->segments;
		}

		$this->segments[] = $this->sef_url( JText::_( 'PAGO_URL_CATEGORY_SEGMENT' ) );
		$this->segments = array_merge(
		$this->segments,
			$this->get_category_structure( $this->id )
		);



		unset( $query['cid'] );
		unset( $query['view'] );

		return $this->segments;
	}

	public function parse( $segments )
	{
		// Remove the /PAGO_URL_CATEGORY_SEGMENT/ segment
		array_shift( $segments );

		$category_id = $this->parse_category_structure( $segments );

		$vars['view'] = 'category';
		$vars['cid'] = $category_id;

		return $vars;
	}

	/**
	* Gets the complete category structure from a category id
	*/
	protected function get_category_structure( $id )
	{
		$categories = Pago::get_instance( 'categoriesi' );

		// Add category in question
		foreach ( explode( '/', $categories->get( $id )->path ) as $value ) {
			$segments[] = $this->sef_url( $value );
		}

		return $segments;
	}

	/**
	* Will return the last category ID from a category structure
	*/
	protected function parse_category_structure( $segments )
	{
		foreach ( $segments as &$value ) {
			$value = $this->de_joomalize( $value );
		}

		return @Pago::get_instance( 'categoriesi' )
			->get_by_path( implode( '/', $segments ) )->id;
	}
}

/**
 * Just defining the front page even though everything is done just like category
 */
class route_frontpage extends route_category{}

/**
 * Base off of category route since items are shown inside of a category
 */
class route_item extends route_category
{
	protected $id = 0;

	public function build( &$query )
	{
		$menu = JFactory::getApplication()->getMenu();

		if ( isset( $query['id'] ) ) {
			$this->id = (int) $query['id'];
		}

		if ( isset( $query['cid'] ) ) {
				$cid = (int) $query['cid'];
		}



		$_query = "SELECT `alias`, `primary_category`, `id`
			FROM #__pago_items
				WHERE `id` = {$this->id}";

		$this->db->setQuery( $_query );
		$_result = $this->db->loadAssoc();


		if ( isset( $query['Itemid'] ) && !empty( $query['Itemid'] ) ) {
			$itemid = $query['Itemid'];
		} else {
			$itemid = 0;
		}

		$menuId = $this->get_category_menu_item( $_result['primary_category'] );

		if ( !$menuId ) {
			$menuItem = $menu->getActive();
			if ( !isset( $menuItem->query['cid'] ) || (isset( $menuItem->query['cid'] ) &&
				$menuItem->query['cid'] !== $_result['primary_category'] ))
			{
				// find primary menu item for categories
				$menuId = $this->get_parent_category_menu_item( $_result['primary_category'] );
				if ( $menuId ) {
					$menuItem = $menu->getItem( $menuId );
				}
			}
		} else {
			$menuItem = $menu->getItem( $menuId );
		}

		//$menuItem was throwing a notice: Trying to get property of non-object
		//in certain instances so added type check and dummy class if non exists
		if( !is_object( $menuItem ) ){
			$menuItem = (object)array(
				'id' => -1,
				'query' => array( 'cid' => -1 )
			);
		}

		if(!isset($menuItem->query['cid']))
		{
			if (isset($cid))
			{
				$menuItem->query['cid'] = $cid;
			}
			else
			{
				$menuItem->query['cid'] = $_result['primary_category'];
			}
		}
		$query['Itemid'] = $menuItem->id;


		if($query['view'] == 'item')
		{
			require_once(JPATH_SITE.'/components/com_pago/helpers/navigation.php');
			$nav = new NavigationHelper();
			$lang = '';
			if( isset( $query['lang'] ) ) {
				$lang = $query['lang'];
			}
			$query['Itemid'] = $nav->getItemid($_result['id'], $_result['primary_category'], $lang);
		}

		$reqCid = JFactory::getApplication()->input->get('cid');

		if (isset($cid))
		{
			$parseId = $cid;
		}
		else if(isset($reqCid))
		{
			$parseId = $reqCid;
		}
		else
		{
			$parseId = $_result['primary_category'];
		}
		$this->segments = array_merge($this->segments,$this->get_category_structure($parseId));

		$this->segments[] = $this->sef_url( JText::_( 'PAGO_URL_ITEM_SEGMENT' ) );
		$this->segments[] = $this->sef_url( $_result['alias'] );

		unset( $query['id'] );
		unset( $query['view'] );
		unset( $query['cid'] );

		return $this->segments;
	}

	public function parse( $segments )
	{
		// Remove the previously set item segment
		array_shift( $segments );

		$item_name = $this->de_joomalize( array_pop( $segments ) );

		// Remove the /PAGO_URL_ITEM_SEGMENT/ segment
		array_pop( $segments );

		$category_id = (int) $this->parse_itenm_category_structure( $item_name );

		$query = "SELECT `id`
			FROM #__pago_items
			WHERE ";

		if ( $category_id ) {
			$query .= "`primary_category` = {$category_id} AND ";
		}

		$query .= "`alias` = " . $this->db->Quote( $item_name );

		$this->db->setQuery( $query );
		$id = $this->db->loadResult();
		$category_id = $this->parse_category_structure($segments);

		$vars['view'] = 'item';
		$vars['id'] = $id;
		$vars['cid'] = $category_id;

		return $vars;
	}

	/**
	* Will return the last category ID from a category structure
	*/
	protected function parse_itenm_category_structure( $item_alias )
	{

		$query = "SELECT `primary_category`
		FROM #__pago_items
		WHERE ";
		$query .= "`alias` = " . $this->db->Quote( $item_alias );
		$this->db->setQuery( $query );
		return $id = $this->db->loadResult();
	}



	private function get_category_menu_item( $cat_id )
	{
		// get the primary category for item menu id
		$query = 'SELECT menu_id from #__pago_menu WHERE cat_id = ' . (int) $cat_id;

		$this->db->setQuery( $query );
		return $this->db->loadResult();
	}

	private function get_parent_category_menu_item( $cat_id )
	{
		$query = 'SELECT parent_id FROM #__pago_categoriesi WHERE id = ' . (int) $cat_id;

		$this->db->setQuery( $query );
		$parent = $this->db->loadResult();

		if ( !$parent ) {
			return false;
		}
		$parent_menu = $this->get_category_menu_item( $parent );
		if ( !$parent_menu ) {
			return $this->get_parent_category_menu_item( $parent );
		}

		return $parent_menu;
	}
}

/**
 * Account view route
 */
class route_account extends route_default
{
	protected $addr_id = 0;

	public function build( &$query )
	{

		$menu = JFactory::getApplication()->getMenu();
		$this->layout = '';
		$this->addr_id = 0;
		$this->checkout = 0;


		if ( isset( $query['addr_id'] ) ) {
			$this->addr_id = (int) $query['addr_id'];
		}

		if ( isset( $query['checkout'] ) ) {
				$this->checkout = (int) $query['checkout'];
		}

		if ( isset( $query['layout'] ) ) {
			$this->layout = $query['layout'];
		}


		if ( isset( $query['Itemid'] ) && !empty( $query['Itemid'] ) ) {
			$itemid = $query['Itemid'];
		} else {
			$itemid = 0;
		}


		$this->segments[] = $this->sef_url(
			JText::_( 'PAGO_URL_' . strtoupper( $query['view'] ) . '_SEGMENT' ) );


		if ( $this->layout ) {
			$this->segments[] = $this->layout;
			unset( $query['layout'] );
		}

		if ( $this->checkout ) {
			$this->segments[] = $this->checkout;
			unset( $query['checkout'] );
		}

		if ( $this->addr_id ) {
			$this->segments[] = $this->addr_id;
			unset( $query['addr_id'] );
		}


		unset( $query['view'] );

		return $this->segments;

	}

	public function parse( $segments )
	{
		$vars['view'] = $segments[0];
		if ( isset( $segments[1] ) ) {
			$vars['layout'] =  $segments[1];
		}

		if ( isset( $segments[2] ) ) {
			$vars['checkout'] = (int) $segments[2];
		}

		if ( isset( $segments[3] ) ) {
			$vars['addr_id'] = (int) $segments[3];
		}else{
			$vars['checkout'] = 0;
			if ( isset( $segments[2] ) ) {
				$vars['addr_id'] = (int) $segments[2];
			}
		}

		return $vars;
	}
}

class route_default extends pago_route
{
	public function build( &$query )
	{
		$_id = JFactory::getApplication()->input->getInt('cid', 0 );
		if ( !$_id ) {
			$_id = JFactory::getApplication()->input->getInt( 'id', 0 );
		}

		$this->segments[] = $this->sef_url(
			JText::_( 'PAGO_URL_' . strtoupper( $query['view'] ) . '_SEGMENT' ) );
		if ( $_id ) {
			$this->segments[] = $_id;
			unset( $query['id'] );
		}

		unset( $query['view'] );

		return $this->segments;
	}

	public function parse( $segments )
	{
		$vars['view'] = $segments[0];
		if ( isset( $segments[1] ) ) {
			$vars['id'] = (int) $segments[1];
		}

		return $vars;
	}
}

/*
 * Function to convert a system URL to a SEF URL
 */
function PagoBuildRoute( &$query )
{
	$language = JFactory::getLanguage();
	$segments = array();

	// Load language
	$language->load( 'com_pago' );

	/*
	// Add the task segment /task/TASK_NAME/
	if ( isset( $query['task'] ) ) {
		$segments[] = JText::_( 'PAGO_URL_TASK_SEGMENT' );
		$segments[] = $query['task'];
		unset( $query['task'] );
	}
	 */

	if ( isset( $query['view'] ) ) {
		$class = 'route_' . $query['view'];
		if ( class_exists( $class ) ) {
			$build = new $class();
		} else {
			$build = new route_default();
		}
		$segments = array_merge( $segments, $build->build( $query ) );
	}
	return $segments;
}

/*
 * Function to convert a SEF URL back to a system URL
 */
function PagoParseRoute( $segments )
{
	$db       = JFactory::getDBO();
	$language = JFactory::getLanguage();
	$vars     = array();

	// Load language
	$language->load( 'com_pago' );

	/*
	if ( JText::_( 'PAGO_URL_TASK_SEGMENT' ) == $segments[0] ) {
		// Remove /task/ segment
		array_shift( $segments );
		$vars['task'] = array_shift( $segments );
	}
	 */

	// Check for item path and get it ready for the upcoming switch
	if ( count( $segments ) >= 2 ) {
		if ( JText::_( 'PAGO_URL_ITEM_SEGMENT' ) == $segments[ count( $segments ) - 2] ) {
			array_unshift( $segments, JText::_( 'PAGO_URL_ITEM_SEGMENT' ) );
		}
	}

	switch( $segments[0] ) {
		// /PAGO_URL_CATEGORY_SEGMENT/categories_path_structure
		case JText::_( 'PAGO_URL_CATEGORY_SEGMENT' ):
			$parse = new route_category();
			$vars = $parse->parse( $segments );
			break;

		// /categories_path_structure/PAGO_URL_ITEM_SEGMENT/item_name
		case JText::_( 'PAGO_URL_ITEM_SEGMENT' ):
			$parse = new route_item();
			$vars = $parse->parse( $segments );
			break;

		// /account_path_structure/PAGO_URL_ACCOUNT_SEGMENT
		case JText::_( 'PAGO_URL_ACCOUNT_SEGMENT' ):
			$parse = new route_account();
			$vars = $parse->parse( $segments );
			break;

		// /account_path_structure/PAGO_URL_SEARCH_SEGMENT
		case JText::_( 'PAGO_URL_SEARCH_SEGMENT' ):
			$parse = new route_default();
			$vars = $parse->parse( $segments );
			break;

		default:
			$parse = new route_default();
			$vars = $parse->parse( $segments );
	}

	return $vars;
}
?>
