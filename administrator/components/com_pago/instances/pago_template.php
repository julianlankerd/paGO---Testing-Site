<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class pago_template
{
	/**
	 * Find the path of the current theme
	 *
	 * returns an array of paths to the theme
	 * theme_path, theme_css_path, theme_functions_path
	 *
	 * @params $theme string
	 * @return array
	 */
	public function find_paths( $theme , $view = "")
	{
		if($theme == '0' || $theme == '')
		{
			$theme = 'default';
		}

		$app = JFactory::getApplication(0);
		if ( $app->isAdmin() ) {
			$db = JFactory::getDBO();
			$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
			$db->setQuery($query);
			$joomla_theme = $db->loadResult();
		} else {
			$joomla_theme = $app->getTemplate();
		}

		$paths = array(
			'component' => array(
				'full' => JPATH_SITE .'/components/com_pago/templates/',
				'url' => JURI::base(true) . '/components/com_pago/templates/'
			),
			'joverride' => array(
				'full' => JPATH_SITE . '/templates/' . $joomla_theme . '/html/com_pago/',
				'url' => JURI::base(true) . '/templates/' . $joomla_theme .'/html/com_pago/'
			)
		);

		$return_paths = array();
		
		$config       = Pago::get_instance( 'config' )->get();
		$default_pago_theme   = 'default';
		foreach ( $paths as $path ) {
			$tpath = $path['full'] . $theme . '/';
			if ( file_exists( $tpath ) && is_dir( $tpath ) ) {
				if(file_exists( $tpath.$view ) && is_dir( $tpath ))
				{
					$return_paths[] = $tpath;
				}
				else
				{
					$return_paths[] = $path['full'].'default/';
				}
				$temStyle = $config->get( 'template.pago_theme_style', 0 );
				if($temStyle == 0){
					if(!file_exists($tpath . 'css/'))
					{
						$return_paths[] = $path['full'] .'default/css/pago.css';
						$return_paths[] = $path['url'] .'default/css/pago.css';
					}
					else
					{
						$return_paths[] = $tpath . 'css/pago.css';
						$return_paths[] = $path['url'] . $theme .'/css/pago.css';
					}
				}elseif($temStyle == 1){
					if(!file_exists($tpath . 'css/'))
					{
						$return_paths[] = $path['full'] .'default/css/pago-dark.css';
						$return_paths[] = $path['url'] .'default/css/pago-dark.css';
					}
					else
					{
						$return_paths[] = $tpath . 'css/pago-dark.css';
						$return_paths[] = $path['url'] . $theme .'/css/pago-dark.css';
					}
				}
				if(!file_exists($tpath . 'functions.php'))
				{
					$return_paths[] = $path['full'] .'default/functions.php';
				}
				else
				{
					$return_paths[] = $tpath . 'functions.php';
				}
				$return_paths[] = $path['full'];
				$return_paths[] = $path['url'];

				if(file_exists( $tpath.$view ) && is_dir( $tpath ))
				{
					$return_paths[] = $path['url']. $theme . '/';
				}
				else
				{
					$return_paths[] = $path['url'].'default/';
				}
				//$return_paths[] = $path['url'];
			}
		}
		
		return $return_paths;
	}
	
	public function getParentCategories($cat_id, $prev_array)
	{
		$db = JFactory::getDBO();
		$query = "SELECT id,parent_id FROM #__pago_categoriesi WHERE id=" . $cat_id;
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$parentCat = array();

		if (!empty($result[0]->parent_id))
		{
			$parentCat[] = $result[0]->parent_id;

			if (count($prev_array) > 0)
			{
				$parentCat = array_merge($parentCat, $prev_array);
			}

			return  $this -> getParentCategories($result[0]->parent_id, $parentCat);
		}
		else
		{
			return $prev_array;
		}
	}

	/**
	 * HTML output for category menu
	 *
	 * @param int category id to get tree of
	 * @param int
	 * @param array html markup parameters
	 */
	public function get_category_menu_tree( $id = 1, $depth = 1, $html_params = array() )
	{
		// TODO: add caching for query and final tree
		//JLoader::register('NavigationHelper', JPATH_COMPONENT . '/helpers/navigation.php');
		JLoader::register('NavigationHelper',JPATH_ROOT .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'navigation.php');
		
		$nav = new NavigationHelper;
		$results = Pago::get_instance( 'categoriesi' )->get( $id, $depth, false, true );
		$Itemid = JFactory::getApplication()->input->get('Itemid');

		$active_class =
			isset( $html_params['active_class'] ) ? $html_params['active_class'] : 'active';

		if ( JFactory::getApplication()->input->get('option', '', 'cmd') == 'com_pago' ) {
			$active_cid = JFactory::getApplication()->input->getInt( 'cid', 0 );
		}

		$active_item = null;

		if ( $active_cid != 0 ) {
			$active_item = Pago::get_instance( 'categoriesi' )->get( $active_cid );
		}

		$html = '';
		$final_level = 0;
		$prev_level = 0;
		$final_level;
		$prev_parent = 0;

		$html .= '<ul>';
		foreach ( $results as $node ) {
			// skip root node but still caclulate final level from it
			if ( $node->id === $id ) {
				$final_level = $node->level + $depth;
			}

			$style_class = '';
			$active_parent_cat[] = $active_cid;
			$parent_cats = $this->getParentCategories($active_cid, array());
			$parent_cats = array_merge($parent_cats, $active_parent_cat);

			if (!in_array($node->parent_id, $parent_cats))
			{
				$style_class = "class='pg_sub_category'";
			}

			if ( $node->id == 1 ) {
				continue;
			}

			if ( ($node->rgt - $node->lft) == 1 || $node->level == $final_level ) {
				$has_children = false;
			} else {
				$has_children = true;
			}

			if ( $node->level < $prev_level
					&& $prev_parent != $node->parent_id
					&& $node->id != $id ) {
				$html .= str_repeat( '</ul></li>', $prev_level - $node->level );
			}

			if ( $node->level < $prev_level
					&& $prev_parent == $node->parent_id
					&& $node->id != $id ) {
				$html .= '</ul></li>';
			}

			if ( $node->level > $prev_level && $node->id != $id && $prev_level != 0 ) {
				$html .= '<ul '.$style_class.'>';
			}

			$class = 'node';
			$node_id_selector = 'node' . $node->id;

			if ( $node->id == $active_cid ) {
				$class .= ' '. $active_class;
			}

			if ( $has_children ) {
				$current_parent = $node->id;
				$class .= ' _parent';
				if ( $active_item !== null ) {
					if ( ($active_item->lft < $node->rgt && $active_item->lft > $node->lft)
						&& $node->id != $active_cid ) {
						$class .= ' ' . $active_class;
					}
				}
			}

			$Itemid = $nav->getItemid(0, $node->id);
			$items_model = JModelLegacy::getInstance('Itemslist', 'PagoModel');
			$item_count = $items_model->getItemCount($node->id);
			$style = '';
			
			if($node->published == '0')// || $item_count <= 0
			{
				$style = "style=display:block";
			}
			
			$html .= '<li class="'. $class .'" id="'. $node_id_selector .'" '.$style.'>';
			$html .= '<a class="node" href="' . JRoute::_( 'index.php?option=com_pago&view=category&view=category&cid=' . $node->id ."&Itemid=" . $Itemid) . '">' . $node->name . '</a>';

			if ( !$has_children ) {
				$html .= '</li>';
			}

			$prev_level = $node->level;
			$prev_parent = $node->parent_id;
		}
		$html .= '</ul>';

		return $html;
	}
}
