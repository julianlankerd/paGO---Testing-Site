<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

// bit masks for attributes
defined( 'KG_ATTR_ALL' ) or define(	'KG_ATTR_ALL', 0x01 );
defined( 'KG_ATTR_PRICE' ) or define( 'KG_ATTR_PRICE', 0x02 );
defined( 'KG_ATTR_SEARCH' ) or define( 'KG_ATTR_SEARCH', 0x04 );
defined( 'KG_ATTR_GLOBAL' ) or define( 'KG_ATTR_GLOBAL', 0x08 );
defined( 'KG_ATTR_DEFAULT' ) or define( 'KG_ATTR_DEFAULT', 0x10 );
defined( 'KG_ATTR_COMPARE' ) or define( 'KG_ATTR_COMPARE', 0x20 );
defined( 'KG_ATTR_SHOW_FRONT' ) or define( 'KG_ATTR_SHOW_FRONT', 0x40 );

class pago_attributes
{
	/*
	 * Get attributes
	 * @param $item_id int of item id or 0 for all
	 * @param $mask bit mask for different options can be OR
	 */
	public function get_price_attributes( $item_id = 0 )
	{
		return $this->get_attributes( $item_id, KG_ATTR_PRICE );
	}

	public function get_search_attributes( $item_id = 0 )
	{
		return $this->get_attributes( $item_id, KG_ATTR_SEARCH );
	}

	public function get_compare_attributes( $item_id = 0 )
	{
		return $this->get_attributes( $item_id, KG_ATTR_COMPARE );
	}

	public function get_front_attributes( $item_id = 0 )
	{
		return $this->get_attributes( $item_id, KG_ATTR_SHOW_FRONT );
	}

	private function search_attributes()
	{
	}

	public function get_attribute_options($attribute_id, $option_id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT item_attr.id AS attribute_id, item_attr.name AS attribute_name , attr_opts. *
				FROM #__pago_attr AS item_attr
					LEFT JOIN #__pago_attr_opts AS attr_opts
				   		ON item_attr.id = attr_opts.attr_id
				   			WHERE item_attr.id = " . $attribute_id . " and attr_opts.id = " . $option_id . "
								ORDER BY item_attr.id DESC LIMIT 0,10";

		$db->setQuery($query);
		$results = $db->loadObject();
		return $results;
	}
}

class pago_attribute
{
	public $id         = '';
	public $name       = '';
	public $pricing    = '';
	public $searchable = '';
	public $global     = '';
	public $default    = '';
	public $showfront  = '';
	public $compare    = '';
	public $type       = '';
	public $options    = array();

	public function __construct( $attrib, $options )
	{
		foreach( array_keys( $attrib ) as $key ) {
			$this->$key = $attrib[$key];
		}
	}

	public function get_html()
	{
	}
}
