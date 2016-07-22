<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/kview.php';
/**
 * HTML View class for the Pago  component
 */
class PagoViewCategory extends PagoView
{
	function display( $tpl = null )
	{
		// Helpers
		Pago::load_helpers( array( 'categories', 'module', 'imagehandler' ) );

		// Initialize some variables
		$document	   = JFactory::getDocument();
		$session       = JFactory::getSession();
		$app 		   = JFactory::getApplication();
		$filter        = JFilterInput::getInstance();
		$search        = JFactory::getApplication()->input->get( 'search', array(), 'array' );
		$menu_config   = $app->getParams();
		$user = JFactory::getUser();
		
		function category_sort_by_latest_last( $a, $b ) {
			return strtotime($a->created_time) == strtotime($b->created_time) ? 0 : ( strtotime($a->created_time) > strtotime($b->created_time) ) ? 1 : -1;
		}
		
		function category_sort_by_latest_first( $a, $b ) {
			return strtotime($a->created_time) == strtotime($b->created_time) ? 0 : ( strtotime($a->created_time) > strtotime($b->created_time) ) ? -1 : 1;
		}
		
		function category_sort_by_name( $a, $b ) {
			return strcmp($a->name, $b->name);
		}
		
		$product_order_by = $menu_config->get('view_order_by');
		$category_order_by = $menu_config->get('block_order_by');


		$cid = $menu_config->get('cid');


		// Check $cid from config menu
		$categorySlider = true;
		$categories = false;

		if(is_array($cid)){
			if (count($cid) > 1){
				if($menu_config->get('inherit_category') == 0){
					$settings_cid = 1;
				} else {
					$settings_cid = $menu_config->get('inherit_category');
				}
				$categories = array();
				foreach($cid as $one_cid){
					$cat = Pago::get_instance( 'categoriesi' )->get( $one_cid, 1, false, false, true);
					if($cat){
						$categories[] = $cat;
					}
					
					//print_r($cid);die;
				}
			
			} else if(count($cid) == 0)
			{
				// if count of cid is zero
				$cid = 0;
				if($menu_config->get('inherit_category') == 0){
					$settings_cid = 1;
				} else {
					$settings_cid = $menu_config->get('inherit_category');
				}
				$cat = Pago::get_instance( 'categoriesi' )->get( $settings_cid, 1, false, false, true);
				if($cat){
					$categories = array($cat);
				}
			}
			else
			{
				// If 1 category specified, use it's category parameters
				if($menu_config->get('inherit_category') == 0){
					$settings_cid = $cid[0];
				} else {
					$settings_cid = $menu_config->get('inherit_category');
				}

				$cat = Pago::get_instance( 'categoriesi' )->get( $settings_cid, 1, false, false, true);
				if($cat){
					$categories = array($cat);
				}
			}
		// If cid not specified, show all items with parent category parameters
		} else {
			$cid = 0;
			$menu_settings_cid = $menu_config->get('inherit_category');
			
			if(isset($menu_settings_cid))
			{
				$settings_cid = $menu_settings_cid;
			}
			else
			{
				$settings_cid = 1;
			}
			
			$cat = Pago::get_instance( 'categoriesi' )->get( $settings_cid, 1, false, false, true);
			if($cat){
				$categories = array($cat);
			}
		}
//print_r($cat->get_children());die;
		if(JFactory::getApplication()->input->get('cid')){
			$categorySlider = false;
			$cid = JFactory::getApplication()->input->get('cid');
			$settings_cid = $cid;
			$cat = Pago::get_instance( 'categoriesi' )->get( $settings_cid, 1, false, false, true);
			if($cat){
				$categories = array($cat);
			}
		}
		
		$settingsCategory = Pago::get_instance( 'categoriesi' )->get( $settings_cid, 1, false, false, true);
		

		if($settingsCategory->inherit_parameters_from > 1){
			// It is set from category to inherit parameters from other category
			$category_inherited_from = Pago::get_instance( 'categoriesi' )->get( $settingsCategory->inherit_parameters_from, 1, false, false, true);
			$settingsCategory = Pago::get_instance( 'categoriesi' )->inherit_parameters_from_to( $category_inherited_from, $settingsCategory);
		}
		
		switch ($category_order_by){
			case 'latest' :
					usort($categories, 'category_sort_by_latest_first');
					break;
			case 'oldest' :
					usort($categories, 'category_sort_by_latest_last');
					break;
			case 'name' :
					usort($categories, 'category_sort_by_name');
					break;
			case 'random' :
					shuffle($categories);
					break;
			default :
					usort($categories, 'category_sort_by_latest_first');
		}
		
		$category_settings_image_settings = json_decode($settingsCategory->category_settings_image_settings);
		$category_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($category_settings_image_settings->image_size);
		
		// Get images for category
		//if($menu_config->get('category_block') && $menu_config->get('category_image')){
			foreach($categories as &$category){
				$image_url = '';
				$image_obj = PagoImageHandlerHelper::get_item_files($category->id, true, 'category');
				if(isset($image_obj[0])){
					$image_url = PagoImageHandlerHelper::get_image_from_object( $image_obj[0], $category_image_size_title, true );
				}
				$category->image_url = $image_url;
			}
		//}
		
		// Sort categories based on backend value
		usort( $categories, 'category_sort_by_name' );

		if ( !$settingsCategory ) {
			JError::raiseWarning( 20, 'Category does not exist or is not published.' );
			return false;
		}

		$session->set( 'referer_cid', $cid, 'pago_cart' );

		$items_model = JModelLegacy::getInstance( 'Itemslist', 'PagoModel' );

		$items_model->setState( 'cid', $cid );
		$items_model->setState( 'search', $search );
		$items_model->setState( 'product_order_by', $product_order_by );

		// Get data from the model
		$minPrice              = JFactory::getApplication()->input->getFloat('minPrice');
		$maxPrice              = JFactory::getApplication()->input->getFloat('maxPrice');
				$dispatcher = KDispatcher::getInstance();

		$layout = Pago::get_instance('categoriesi')->get_custom_layout($settings_cid, 'category', 'category');


		JPluginHelper::importPlugin('pago_products');

		$dispatcher->trigger('override_category_items', array(&$items, $cid, $minPrice, $maxPrice, $settingsCategory, $layout, $menu_config));

		

		if(!count($items))
		{
			// Get data from the model
			$items      = $items_model->get_list(0, $minPrice, $maxPrice);
			
			if(($settingsCategory->product_settings_short_desc == 1) || ($settingsCategory->product_settings_desc == 1) || ($settingsCategory->product_settings_product_title == 1))
			{
				if($items)
				{
					foreach ($items as $key => $item) {
					
						if($settingsCategory->product_settings_short_desc == 1){
							$item->description = TruncateHTML::truncateWords( $item->description, $settingsCategory->product_settings_short_desc_limit, '...');
						}

						if($settingsCategory->product_settings_desc == 1){
							$item->content = TruncateHTML::truncateWords( $item->content, $settingsCategory->product_settings_desc_limit, '...');
						}

						if($settingsCategory->product_settings_product_title == 1){
							$item->name = TruncateHTML::truncateWords( $item->name, $settingsCategory->product_settings_product_title_limit, '...');
						}
					}
				}
			}
		}

		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin('search');
		/*	JPluginHelper::importPlugin('pago_products');
		$dispatcher->trigger('override_category_items', array(&$items)); */

		$pagination = $items_model->getPagination();
		$limit = $items_model->get_limit();

		
		JLoader::register( 'NavigationHelper', JPATH_COMPONENT .'/helpers/navigation.php');

		$nav = new NavigationHelper();

		$title = false;
		if($menu_config->get('page_title') && JFactory::getApplication()->input->get('cid')=="")
            $title .=  html_entity_decode($menu_config->get('page_title'));
        else
            $title .=  html_entity_decode($categories[0]->name);
		$this->set_metadata( 'category', $categories[0]->id, $title );

		$this->assign( 'option', 'com_pago' );
		$this->assign( 'view',   'category'  );
		$nav->generateBreadcrumPath($categories[0]->id);

		// Set Custom layout
		$layout = Pago::get_instance('categoriesi')->get_custom_layout($settings_cid, 'category', 'category');

		if ($layout != "")
        {
            $this->set_theme($layout);
		}
        $this->set('category', $layout);
	
		$this->assign( 'children', $cat->get_children() );
		$this->assignRef( 'nav', $nav );
    	$this->assignRef( 'items',       $items );
    	$this->assignRef( 'categorySlider',       $categorySlider );
    	$this->assignRef( 'pagination',  $pagination );
		$this->assignRef( 'limit',  $limit );
		$this->assignRef( 'params',      $params );
		$this->assignRef( 'cid',         $cid );
		$this->assignRef( 'document',    $document );
		$this->assignRef( 'categories',    $categories );
		$this->assignRef( 'settingsCategory',    $settingsCategory );
		$this->assignRef( 'menu_config', $menu_config );
		//$this->assignRef( 'image', $image );
		$this->assignRef( 'user', $user );

        parent::display($tpl);
    }
}
?>
