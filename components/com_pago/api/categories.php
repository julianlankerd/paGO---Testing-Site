<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class PagoApiCategories
{
	static public function get($dta, $menu_config=false)
	{
		$code = 200;
	    $status = 'success';
		$categories = [];
		$items = [];
		$app = JFactory::getApplication();
		$input = $app->input;
		
		Pago::load_helpers(['categories', 'module', 'imagehandler']);
		
		if(empty($dta)){
			$id = 1;
			$dta = [
				['id'=>$id]
			];
		}
		
		foreach($dta as $cat){
			$category = Pago::get_instance('categoriesi')->get(
				$cat['id'],
				1,
				false,
				false,
				true
			);
			
			if($menu_config){
				if($menu_config->get('inherit_category'))
					$category->inherit_parameters_from
						= $menu_config->get('inherit_category');
			}
			
			$category->image_url = self::get_cat_image_url($category);
			$categories[] = $category;
		}
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => [
				'categories' => $categories,
				'items' => $items
			]
		];
	}
	
	static public function layout($dta)
	{
		$code = 200;
	    $status = 'success';
		$categories = [];
		$app = JFactory::getApplication();
		$input = $app->input;
		
		
		foreach($dta as $item){
			$menu_config = $app->getMenu()->getItem($item['id'])->params;
			$cids = $menu_config->get('cid');
			
			foreach($cids as $cid){
				$category = self::get(
					[['id'=>$cid]],
					$menu_config
				);
				
				$categories[] = $category['model']['categories'];
			}
			
			/*$categories = self::sort_cats(
				$menu_config->get('block_order_by'),
				$categories
			);*/
			
			$items = self::get_cat_items(
				$cids, 
				@$item['search'], 
				$menu_config->get('view_order_by'), 
				@$item['min_price'], 
				@$item['max_price'],
				@$item['limitstart']
			);
		}
		
		return [
			'code' => $code,
			'status' => $status,
			'model' => [
				'categories' => $categories,
				'items' => $items
			]
		];
	}
	
	static private function sort_cats($category_order_by, $categories)
	{
		switch ($category_order_by){
			case 'latest' :
					usort($categories, [__CLASS__, 'category_sort_by_latest_first']);
					break;
			case 'oldest' :
					usort($categories, [__CLASS__, 'category_sort_by_latest_last']);
					break;
			case 'name' :
					usort($categories, [__CLASS__, 'category_sort_by_name']);
					break;
			case 'random' :
					shuffle($categories);
					break;
			default :
					usort($categories, [__CLASS__, 'category_sort_by_latest_first']);
		}
		
		return $categories;
	}
	
	static private function get_cat_items($cids, $search, $product_order_id, $min_price, $max_price, $limitstart)
	{
		JFactory::getApplication()->input->set('limitstart', $limitstart);
		
		$items_model = JModelLegacy::getInstance( 'Itemslist', 'PagoModel' );

		$items_model->setState( 'cid', $cids );
		$items_model->setState( 'search', ['name' => $search]);
		$items_model->setState( 'product_order_by', $product_order_id);
		
		$items      = $items_model->get_list(0, $min_price, $max_price);
		$pagination = $items_model->getPagination();
		$limit = $items_model->get_limit();
		
		return [
			'pagination' => $pagination,
			'list' => $items
		];
	}
	
	static private function get_cat_image_url($category)
	{
		if($category->inherit_parameters_from > 1){
			// It is set from category to inherit parameters from other category
			$category_inherited_from = Pago::get_instance( 'categoriesi' )
				->get( $category->inherit_parameters_from, 1, false, false, true);
				
			$category = Pago::get_instance( 'categoriesi' )
				->inherit_parameters_from_to($category_inherited_from, $category);
		}
	
		$category_settings_image_settings = json_decode(
			$category->category_settings_image_settings
		);
		
		$category_image_size_title = Pago::get_instance( 'config' )
			->getSizeByNumber($category_settings_image_settings->image_size);
	
		$image_url = '';
		$image_obj = PagoImageHandlerHelper::get_item_files(
			$category->id, 
			true, 
			'category'
		);
		
		if(isset($image_obj[0])){
			$image_url = PagoImageHandlerHelper::get_image_from_object(
				$image_obj[0],
				$category_image_size_title,
				true
			);
		}
		
		return $image_url;
	}
	
	static private function category_sort_by_latest_last( $a, $b ) {
		$a = $a[0];
		$b = $b[0];
		return strtotime($a->created_time) == strtotime($b->created_time) ? 0 : ( strtotime($a->created_time) > strtotime($b->created_time) ) ? 1 : -1;
	}
		
	static private function category_sort_by_latest_first( $a, $b ) {
		$a = $a[0];
		$b = $b[0];
		return strtotime($a->created_time) == strtotime($b->created_time) ? 0 : ( strtotime($a->created_time) > strtotime($b->created_time) ) ? -1 : 1;
	}
	
	static private function category_sort_by_name( $a, $b ) {
		$a = $a[0];
		$b = $b[0];
		return strtotime($a->name) == strtotime($b->name) ? 0 : ( strtotime($a->name) > strtotime($b->name) ) ? -1 : 1;
	}
}
