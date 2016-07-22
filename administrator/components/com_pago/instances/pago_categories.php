<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_categories
{
	var $_config;
	var $_ini;

	function get( $params ){

		jimport('joomla.cache.cache');
		jimport('joomla.cache.callback');

		$cache = JCache::getInstance( 'callback', array(
			//this will be the folder name of your cache
			//inside the "cachebase" folder
			'defaultgroup' => 'com_pago_categories', ///
			'cachebase' => JPATH_SITE . '/cache/',
			//how long to store the data in cache (seconds
			'lifetime' => ( 5*60*60 ), // hours to seconds
			'language' => 'en-GB',
			'storage' => 'file'
		));

		//$cache->clean( 'com_pago_categories' );

		$cache->setCaching( $params['cache'] );

		$cache_id = md5( 'category' . $params['id'] );

		$return = $cache->get( array( $this, '_get' ), array( $params ), $cache_id );

		return $return;
	}

	function _get(  $params ){

		extract( $params );

		Pago::load_helpers( 'categories' );

		$catnip = new catnip( 'pago_categories', 'com_pago', 'category', 'cid', false );

		$catnip->get_all_data = true;

		$tree = $catnip->get_tree()->get_menu( true );


		$dom = qp( $tree );

		$selector = "li[node={$id}]";

		if( $id == '1' ){
			$category = $catnip->get(1);

			$dom->find('ul:first')->wrapAll('<li depth="0" class="node _published parent" id="node1" node="1"></li>');
			$dom->top()->find('li:first')->wrapAll('<ul></ul>');

		} else {
			$category = json_decode( $dom->find( "li[node={$id}]" )->find( 'pre' )->innerHtml() );
		}

		$category->description = html_entity_decode( $category->description );

		$item_count = $this->get_item_count( $category->id );
		$category->item_count = $item_count['item_count'];
		$category->total_item_count = $item_count['total_item_count'];

		$children = false;

		foreach( $dom->top()->find( $selector )->children( 'ul' )->children('li') as $child ){
			$data = json_decode( $child->find( 'pre' )->innerHtml() );

			$item_count = $this->get_item_count( $data->id );
			$data->item_count = $item_count['item_count'];
			$data->total_item_count = $item_count['total_item_count'];

			$data->description = html_entity_decode( $data->description );

			if( $data ) $children[] = $data;
		}

		$siblings_prev = false;

		foreach( $dom->top()->find( $selector )->prevAll( 'li' ) as $sibling ){
			$data = json_decode( $sibling->find( 'pre' )->innerHtml() );

			$item_count = $this->get_item_count( $data->id );
			$data->item_count = $item_count['item_count'];
			$data->total_item_count = $item_count['total_item_count'];
					$data->description = html_entity_decode( $data->description );
			if( $data ) $siblings_prev[] = $data;
		}

		$siblings_next = false;

		foreach( $dom->top()->find( $selector )->nextAll( 'li' ) as $sibling ){
			$data = json_decode( $sibling->find( 'pre' )->innerHtml() );

			$item_count = $this->get_item_count( $data->id );
			$data->item_count = $item_count['item_count'];
			$data->total_item_count = $item_count['total_item_count'];
			$data->description = html_entity_decode( $data->description );
			if( $data ) $siblings_next[] = $data;
		}

		$parent = false;
		$parents = false;
		if( $dom->top()->find( $selector )->parent()->parent()->attr( 'id' ) ){
			$data = json_decode( $dom->top()->find( $selector )->parent()->parent()->find( 'pre' )->innerHtml() );

			$item_count = $this->get_item_count( $data->id );
			$data->item_count = $item_count['item_count'];
			$data->total_item_count = $item_count['total_item_count'];
			$data->description = html_entity_decode( $data->description );
			$parent = $data;

			$parent_path = explode( '.', $category->path );

			array_pop( $parent_path );
			array_pop( $parent_path );
			array_pop( $parent_path );
			array_shift( $parent_path );

			$parents = false;

			if( !empty( $parent_path ) ){
				foreach( $parent_path as $id ){
					$data = json_decode( $dom->top()->find( "li[node={$id}]" )->find( 'pre' )->innerHtml() );

					if( $data ){
						$item_count = $this->get_item_count( $data->id );
						$data->item_count = $item_count['item_count'];
						$data->total_item_count = $item_count['total_item_count'];
							$data->description = html_entity_decode( $data->description );
						$parents[] = $data;
					}
				}
			}
		}

		$ul = false;

		if( isset( $params['ul'] ) ){
			$catnip = new catnip( 'pago_categories', 'com_pago', 'category', 'cid', false );

			$catnip->show_unpublished = $params['show_unpublished'];

			$ul = $catnip->get_tree( $params['id'], $params['depth'], 0,  $params['get_item_count'], $params['no_link'] )->get_menu(  );

			$ul_params = $params['ul'];

			if( isset( $ul_params['add_all_link'] ) && $ul_params['add_all_link'] ){
				$dom = qp( $ul );

				foreach( $dom->find('ul:first')->children() as $li ){

					$class = $li->attr('class');
					$id = 'all_' . $li->attr('id');
					$class = str_replace( '_parent', '', $class );
					$class = str_replace( 'parent', '', $class );
					$a = $li->find('a:first');
					$name = $a->text();
					$a = str_replace( $name, 'All ' . $name, $a->html() );

					$li->parent()->find('ul:first')->prepend('<li id="'.$id.'" class="'.$class.'">'.$a.'</li>');
				}

				$ul = $dom->top()->find('body')->innerHtml();
			}

			$ul = str_replace(
				array( '_ul_class', '_li_class', '_published', '_unpublished', '_parent' ),
				array(
					$ul_params['ul_class'],
					$ul_params['li_class'],
					$ul_params['published_class'],
					$ul_params['unpublished_class'],
					$ul_params['parent_class'] ),
				$ul
			);
		}

		$return = new stdClass();

		$return->category = $category;
		$return->children = $children;
		$return->siblings_prev = $siblings_prev;
		$return->siblings_next = $siblings_next;
		$return->parent = $parent;
		$return->parents = $parents;
		$return->ul = $ul;

		return $return;
	}

	function get_cat_ul( $params ){

		$cats_index = array();

		foreach( $params['categories'] as $cat ){
			$cats_index[ $cat['id'] ] = true;
		}

		Pago::load_helpers( 'categories' );

		$catnip = new catnip( 'pago_categories', 'com_pago', 'category', 'cid', false );

		//$tree = $catnip->get_tree( )->get_menu( true );

		$tree = $catnip->get_tree( 1, false, false, $params['get_item_count'], $params['no_link'] )->get_menu( true );

		$dom = qp( $tree );

		foreach( $dom->find('li') as $li ){

			$has_child = false;

			$node_id = $li->attr( 'node' );

			if( !isset( $cats_index[ $node_id ] ) ){
				$li->remove();
			}
		}

		foreach( $dom->top()->find('ul') as $ul ){
			if( $ul->innerHtml() == '' ){
				$ul->remove();
			}
		}

		$uls = array();

		$ul_params = $params['ul'];

		foreach( $dom->top()->find('ul:first')->children() as $li ){
			$ul = '<ul class="'.$ul_params['ul_class'].'">' . $li->html() . '</ul>';

			$ul = str_replace(
				array( '_ul_class', '_li_class', '_published', '_unpublished', '_parent' ),
				array(
					$ul_params['ul_class'],
					$ul_params['li_class'],
					$ul_params['published_class'],
					$ul_params['unpublished_class'],
					$ul_params['parent_class'] ),
				$ul
			);

			$uls[] = $ul;
		}

		$ul = $dom->top()->find('body')->innerHtml();



		$ul = str_replace(
			array( '_ul_class', '_li_class', '_published', '_unpublished', '_parent' ),
			array(
				$ul_params['ul_class'],
				$ul_params['li_class'],
				$ul_params['published_class'],
				$ul_params['unpublished_class'],
				$ul_params['parent_class'] ),
			$ul
		);

		$return = new stdClass();

		$return->ul = $ul;
		$return->uls = $uls;

		return $return;
	}

	function get_item_count( $id=false ){

		if( !$id ) return false;

		$db = JFactory::getDBO();

		$sql = "SELECT COUNT(category_id) as count FROM #__pago_categories_items
				WHERE category_id = {$id}";

		$db->setQuery( $sql );

		$return['item_count'] = $db->loadObject()->count;

		$regexp = '\\\.' . $id . '\\\.';

		$sql = "SELECT COUNT(category_id) as count FROM #__pago_categories_items
			LEFT JOIN #__pago_categories
			ON #__pago_categories.id = #__pago_categories_items.category_id
			WHERE (path REGEXP '{$regexp}')";

		$db->setQuery( $sql );

		$return['total_item_count'] = $return['item_count'] + $db->loadObject()->count;

		return $return;
	}

	function clear_cache(){
		jimport('joomla.cache.cache');
		jimport('joomla.cache.callback');

		$cache = JCache::getInstance( 'callback', array(
			//this will be the folder name of your cache
			//inside the "cachebase" folder
			'defaultgroup' => 'com_pago_categories', ///
			'cachebase' => JPATH_SITE . '/cache/',
			//how long to store the data in cache (seconds
			'lifetime' => ( 5*60*60 ), // hours to seconds
			'language' => 'en-GB',
			'storage' => 'file'
		));

		$cache->clean( 'com_pago_categories' );
	}
}