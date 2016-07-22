<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="pg-mod-categories<?php echo $params->get( 'moduleclass_sfx' ) ?> pg-main-container">
	<?php 
		JLoader::register('NavigationHelper',JPATH_ROOT .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_pago' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'navigation.php');
		
		$nav = new NavigationHelper;
		$results = Pago::get_instance( 'categoriesi' )->get( $start_level, $depth, false, true );
		$Itemid = JFactory::getApplication()->input->getInt('Itemid');
		$exclude_categories 	= $params->get( 'exclude_categories', array() );
		$active_class =
			isset( $html_params['active_class'] ) ? $html_params['active_class'] : 'active';

		$active_cid = false;
		if ( JFactory::getApplication()->input->getCmd('option') == 'com_pago' ) {
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
		$isCategories = false;

		foreach ( $results as $node ) {
			if(in_array($node->id, $exclude_categories))
			{
				continue;
			}		
			
			// skip root node but still caclulate final level from it
			if ( $node->id === $start_level ) {
				$final_level = $node->level + $depth;
			}

			$current_category = '';
			if ($node->id == $active_cid){
				$current_category = "pg-active-category";
			}

			$style_class = '';
			$active_parent_cat[] = $active_cid;
			$parent_cats = mod_pago_category_helper::getParentCategories($active_cid, array());
			$parent_cats = array_merge($parent_cats, $active_parent_cat);

			if (!in_array($node->parent_id, $parent_cats)){
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
					&& $node->id != $start_level ) {
				$html .= str_repeat( '</ul></li>', $prev_level - $node->level );
			}

			if ( $node->level < $prev_level
					&& $prev_parent == $node->parent_id
					&& $node->id != $start_level ) {
				$html .= '</ul></li>';
			}

			if ( $node->level > $prev_level && $node->id != $start_level && $prev_level != 0 ) {
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
			if($Itemid) $Itemid = "&Itemid=" . $Itemid;
			$items_model = JModelLegacy::getInstance('Itemslist', 'PagoModel');
			
			
			if($node->published != '0'){// || $item_count <= 0
				$html .= '<li class="'. $class .' '.$current_category.'" id="'. $node_id_selector .'">';
				$html .= '<a class="node" href="' . JRoute::_( 'index.php?option=com_pago&view=category&view=category&cid=' . $node->id .$Itemid) . '">' . $node->name;
				
				if($params->get( 'show_item_count', 0 ) == 1){
					$item_count = $items_model->getItemCount($node->id,true);
					$html .= '('.$item_count.')';
				}
				
				$html .= '</a>';
				$isCategories = true;

				if ( !$has_children ) {
					$html .= '</li>';
				}
			}

			$prev_level = $node->level;
			$prev_parent = $node->parent_id;
		}
		$html .= '</ul>';
		if (!$isCategories){
			$html .= '<span>'.JTEXT::_("MOD_PAGO_CATEGORY_ADD_CATEGORY").'</span>';
		}
		echo $html;
	?>
</div>
