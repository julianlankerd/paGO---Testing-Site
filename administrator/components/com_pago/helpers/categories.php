<?php defined('_JEXEC') or die;
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

/**
 * Catnip Class
 */
class catnip
{
    var $id_varname, $get_all_data, $show_unpublished;

    function __construct( $table = 'pago_categories', $option = 'com_pago', $view = 'category',
 		$id_varname = 'cid', $active_node = false )
    {
        $this->table = $table;
        $this->option = $option;
        $this->view = $view;
        $this->id_varname = $id_varname;
        $this->active_node = $active_node;
    }

    public function get( $id = false, $get_item_count = false )
    {
        if ( ! $id )
            return false;

        $db = & JFactory::getDBO();

        $sql = "SELECT * FROM #__$this->table
				WHERE id = $id
					ORDER BY path, n_order";

        $db->setQuery( $sql );

		$cat = $db->loadObject();

		if( $cat && $get_item_count ){

			$sql = "SELECT COUNT(category_id) as count FROM #__pago_categories_items
				WHERE category_id = $cat->id";

			$db->setQuery( $sql );

			$cat->item_count = $db->loadObject()->count;

			 $regexp = '\\\.' . $cat->id . '\\\.';

			$sql = "SELECT COUNT(category_id) as count FROM #__pago_categories_items
				LEFT JOIN #__pago_categories
				ON #__pago_categories.id = #__pago_categories_items.category_id
				WHERE (path REGEXP '{$regexp}')";

			$db->setQuery( $sql );

			$cat->total_item_count = $db->loadObject()->count;
		}

		return $cat;
    }

    public function get_from_path( $path )
    {


		$cats = explode( '.', $path );

        $data = NULL;

        if ( is_array( $cats ) ) {
            array_shift( $cats );
            array_pop( $cats );

            $where = false;

            foreach ( $cats as $id ) {
                $where .= ' id=' . $id . ' OR';
            }

			if( $where ){
            	$where = substr_replace( $where, '', - 3 );
				$where = 'WHERE ' . $where;


				$db = & JFactory::getDBO();

				$sql = "SELECT id, name FROM #__$this->table
							$where
								ORDER BY path, n_order";

				$db->setQuery( $sql );

				$data = $db->loadObjectList();

			}
        }

        return $data;
    }

    public function get_tree( $root = 1, $level = false, $remove_unpublished=false, $show_item_count=false, $no_link=false )
    {
        $this->level = $level;
        $this->root = $root;
		$this->show_item_count = $show_item_count;
		$this->no_link = $no_link;
		//$this->get_all_data = $get_all_data;

        $db = & JFactory::getDBO();

        if ( $level ) {
            $iroot = '\\\.' . $root . '\\\.';
            $regexp = $iroot . '$|';

            for ( $i = 2; $i <= $level; $i += 1 ) {
                $regexp .= $iroot . str_repeat( '[[:digit:]]{1,}\\\.', $i - 1 ) . '$|';
            }

            $regexp = substr_replace( $regexp, '', - 1 );
        } else {
            $regexp = '\\\.' . $root . '\\\.';
        }

		$and = false;

		if( $remove_unpublished ) $and = 'AND `published` = 1';
		//published
        $sql = "SELECT * FROM #__$this->table
					WHERE (path REGEXP '$regexp') $and
							ORDER BY path, n_order";

        $db->setQuery( $sql );

        $this->tree = $db->loadObjectList();

//print_r($this->tree);die();
        return $this;
    }

    public function get_menu( $show_unpublished = false )
    {
        if ( ! function_exists( 'qp' ) ) {
            require_once (dirname( __FILE__ ) . '/QueryPath/QueryPath.php');
        }

        $nodes = array();

        foreach ( $this->tree as $k => $item ) {

            $item->path = $item->path . $item->id . '.';

            if ( ! $show_unpublished ) {
                $pattern = '/.*\.' . $this->root . '\./';

                $item->path = preg_replace( $pattern, '.', $item->path );
            }

            $path = explode( '.', $item->path );

            array_shift( $path );
            array_pop( $path );

            if ( $show_unpublished ) {
                array_shift( $path );
            }

            eval( '$nodes[' . implode( '][', $path ) . ']=array();' );

            $item->parent = array_pop( $path );
            $this->menu_tree[$item->id] = $item;
        }

        $this->menu_tree[1]->id = 1;
        $this->menu_tree[1]->name = 'root';
        $this->menu_tree[1]->published = 1;

        $this->draw_ul( $nodes, $show_unpublished );

        return $this->menu_xml;
    }

    function draw_ul( $nodes, $show_unpublished = false )
    {
        if ( is_array( $nodes ) ) {

            $this->menu_xml .= '<ul class="_ul_class ">';

            foreach ( $nodes as $id => $node ) {
                if ( ! array_key_exists( $id, $this->menu_tree ) ) {
                    continue;
                }
                $category = $this->menu_tree[$id];

                if ( $category->published || $show_unpublished || $this->show_unpublished ) {
                    if ( ! empty( $node ) ) {
                        $this->menu_xml .= $this->draw_li( $category, 'parent _parent' );
                        $this->draw_ul( $node, $show_unpublished );
                    } else {
                        $this->menu_xml .= $this->draw_li( $category, false );
                    }

                    $this->menu_xml .= '</li>';
                }
            }

            $this->menu_xml .= '</ul>';
        }
    }

    function draw_li( $category, $is_parent = false )
    {
        $link = JRoute::_(
            'index.php?option=' . $this->option . '&view=' . $this->view . '&' . $this->id_varname .
                 '=' . $category->id );

		$item_count = false;
		if( $this->show_item_count ){
			$item_count = ' (' . $this->get_item_count( $category->id ) . ')';
		}

        $published = '_unpublished ';

        $depth = count( explode( '.', $category->path ) ) - 2;

        if ( $category->published ) {
            $published = '_published ';
        }

        $this->name_index[$category->id] = $category->name;

		if( $this->no_link ){
			$li_a_start = '<span>';
			$li_a_end = '</span>';
		} else {
			$li_a_start = '<a class="node" href="' . $link . '">';
			$li_a_end = '</a>';
		}

		if( $this->get_all_data ){

			$category->description = htmlentities( $category->description );

			$category->link = $link;
			$li = '<li depth="' . $depth . '" class="node _li_class ' . $published . $is_parent . '" id="node' .
             $category->id . '" node="' . $category->id . '">'
			 . $li_a_start . htmlentities( $category->name ) . $item_count . $li_a_end . '<pre style="display:none">' . json_encode( $category ) . '</pre>';
		} else {
	    $li = '<li depth="' . $depth . '" class="node _li_class ' . $published . $is_parent . '" id="node' .
             $category->id . '" node="' . $category->id . '">' . $li_a_start . htmlentities( $category->name ) . $item_count . $li_a_end;
		}

        return $li;
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

		return $return['total_item_count'];

		return $return;
	}

    public function get_ul()
    {
        $name_index = false;
        $request_id = false;

        if ( isset( $_REQUEST[$this->id_varname] ) ) {
            $request_id = $_REQUEST[$this->id_varname];
        }

        if ( ! function_exists( 'qp' ) ) {
            require_once (dirname( __FILE__ ) . '/QueryPath/QueryPath.php');
        }

        $this->tree or $this->get_tree();

        $dom = qp( QueryPath::HTML_STUB );

        $dom->find( 'body' )
            ->append( '<ul/>' );

        foreach ( $this->tree as $node ) {
            $name_index[$node->id] = $node->name;
            $this->name_index[$node->id] = $node->name;
        }

        foreach ( $this->tree as $node ) {

            $parents = explode( '.', $node->path );
            //there is always a blank first and last element so we get rid of
            array_shift( $parents );
            array_pop( $parents );

            $depth = count( $parents );

            if ( ! empty( $parents ) ) {
                $dom->top()
                    ->find( 'ul:first' );

                $active = false;
                $published = false;

                if ( $request_id == $node->id ) {
                    $active = ' active';
                    $active_path = $parents;
                }

                if ( $node->published ) {
                    $published = ' _published';
                } else {
                    $published = ' _unpublished';
                }

                foreach ( $parents as $parent_id ) {
                    if ( ! $dom->find( 'li[node=' . $parent_id . ']' )
                        ->hasClass( 'node' ) ) {

                        if ( isset( $name_index[$parent_id] ) ) {
                            $dom->end()
                                ->append(
                                '<li class="node" t="1" node="' . $parent_id . '">' .
                                     $name_index[$parent_id] . '</li>' );

                            $dom->top()
                                ->find( 'li[node=' . $parent_id . ']' );
                        } else {
                            $dom->end();
                        }
                    } else {
                        if ( ! $dom->hasClass( 'parent' ) ) {
                            $dom->addClass( 'parent' );
                        }

                        if ( $active && ! $dom->hasClass( 'active' ) ) {
                            //$dom->children('a:first')->addClass('active')->parent();
                        }
                    }
                }

                $link = JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view . '&' .
                         $this->id_varname . '=' . $node->id );

                //$name = false;


                if ( isset( $name_index[$node->id] ) ) {
                    $name = $name_index[$node->id];
                }

                $dom->append(
                    '<ul>
						<li class="node' . $active . $published . '" node="' . $node->id . '" id="node' . $node->id .
                         '" depth="' . $depth . '">
							<a class="node" href="' . $link . '">' . $name . '</a>
						</li>
					</ul>' )
                    ->end();
            }
        }

        $uls = $dom->top()
            ->find( 'ul' );

        //tidying up empty uls
        foreach ( $uls as $ul ) {

            if ( ! $ul->innerhtml() ) {
                $ul->remove();
            }
        }

        $list = $dom->top()
            ->find( 'ul:first' )
            ->html();

        //this looks after sibling Uls they need to amalgameted into
        //one - funnily enough easier to do outside querypath!
        $list = str_replace( '</ul><ul>', '', $list );
        $list = str_replace( '</ul><ul class="active">', '', $list );

        $list = str_replace( '</ul></ul>', '</ul>', $list );
        $list = str_replace( '<ul><ul>', '<ul>', $list );

        return $list;
    }

    public function get_indented()
    {
        if ( ! function_exists( 'qp' ) ) {
            require_once (dirname( __FILE__ ) . '/QueryPath/QueryPath.php');
        }

        $items = qp( $this->get_menu( true ) )->find( 'li' );
        $list = array();

        foreach ( $items as $item ) {

            $list[] = array(
                            'id' => $item->attr( 'node' ),
                            'name' => $this->name_index[$item->attr( 'node' )],
                            'depth' => $item->attr( 'depth' ),
            //'path'=>$item->path
            );
        }

        /*simply
		foreach($list as $cat){
			echo str_repeat('>', $cat['depth'] ).$cat['name'].'<br/>';
		}*/

        return $list;
    }

    public function getTreeCount()
    {
        @$this->tree or $this->get_tree();
        return count( $this->tree );
    }

	public function get_product_count( $cid ) {
		$db = & JFactory::getDBO();

		$sql = "SELECT COUNT(category_id)
					FROM #__pago_categories_items AS kci
					LEFT JOIN #__pago_items AS ki ON ki.id = kci.item_id
						WHERE kci.category_id = $cid AND ki.published = 1";

        $db->setQuery( $sql );
		$products = $db->loadResult();

        return $products;
	}

    public function getCatArray()
    {
        @$this->tree or $this->get_tree();
        $newArray = array();

        foreach ( $this->tree as $node ) {
            $parents = explode( '.', trim( $node->path, '.' ) );
            $tmpArray = array();
            $tmpArray[$node->id]['items'][] = $node;
            while ( ! empty( $parents ) ) {
                $tmpArray[array_pop( $parents )][key( $tmpArray )] = array_pop( $tmpArray );
            }
            if ( empty( $newArray ) ) {
                $newArray = $tmpArray;
            } else {
                $newArray = $this->_mergeRecursive( $newArray, $tmpArray );
            }
        }

        return current( $newArray );
    }

    private function _mergeRecursive( $array1, $array2 )
    {
        foreach ( $array2 as $key => $val ) {
            if ( 'items' == $key ) {
                $array1['items'][] = current( $val );
            } elseif ( is_array( $val ) ) {
                if ( ! array_key_exists( $key, $array1 ) ) {
                    $array1[$key] = array();
                }
                $array1[$key] = $this->_mergeRecursive( $array1[$key], $array2[$key] );
            }
        }

        return $array1;
    }
	
	public function get_parent_category_tree($cat_id)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT parent_id, name FROM #__pago_categoriesi WHERE id = ' . (int) $cat_id;
		$db->setQuery( $query );
		$parent = $db->loadObjectList();
		$childcat= '';
		
		if (!$parent[0]->parent_id) 
		{
			echo $parent[0]->name;
			return false;
		}
		
		$cattext = $parent[0]->name;
		$childcat = " => " . $cattext ;
		$parent_menu = $this->get_parent_category_tree($parent[0]->parent_id);
		echo $childcat;
	}
	
	public function get_parent_category_ids($cat_id, &$parents)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT parent_id FROM #__pago_categoriesi WHERE id = ' . (int) $cat_id;
		$db->setQuery( $query );
		$parentId = $db->loadResult();
		$childcat= '';
		
		if ($parentId != 0) 
		{
			$parents[] = $parentId; 
			$this->get_parent_category_ids($parentId, $parents);
		}
	}
	
    public function get_parent_category_names($cat_id, &$parents)
    {
    	$db = JFactory::getDBO();
		$query = 'SELECT parent_id, name FROM #__pago_categoriesi WHERE id = ' . (int) $cat_id;
        $db->setQuery( $query );
	    $parentId = $db->loadObjectList();
        $childcat= '';

        if ($parentId[0]->parent_id != 0)
        {
           $parents[] = $parentId[0]->name;
           $this->get_parent_category_names($parentId[0]->parent_id, $parents);
        }
   }
}
?>