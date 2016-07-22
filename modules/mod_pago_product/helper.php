<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/ 
// no direct access
defined('_JEXEC') or die('Restricted access');
if(!class_exists('template_functions'))
{
	require_once (JPATH_SITE .'/components/com_pago/templates/default/functions.php');
}
class mod_pago_product_helper
{
    public static function getItems( $params )
	{
		$db = JFactory::getDBO();
		
		$qty = '';
		
		$order_by = $params->get( 'order_by');

        if ($order_by == 1) {
            $order = " items.created asc ";
        } 
        if ($order_by == 2) {
            $order = " items.created desc ";
        } 
        if ($order_by == 4) {
            $order = " items.title ASC ";
        }
        if ($order_by == 5) {
            $order = " items.title DESC ";
        } 
        if ($order_by == 8) {
            $order = " items.featured DESC ";
        } 
        if ($order_by == 10) {
            $order = " items.rating DESC ";
        } 
        if ($order_by == 11) {
            $order = " items.modified DESC ";
        } 
        if ($order_by == 12) {
            $order = " RAND() ";
        }

	  if($order_by == 9)
        {
        	$items = self :: getMostSoldItem($params);
        	if(count($items) > 0)
        	{
        		return $items;
        	}
        	else
        	{
        		$order = " RAND() ";
        	}
        }

        $category = $params->get( 'category' );
        $categoryIds = '';

        if($category != 1){
        	if($params->get('category_selector')){
        		$cats = array();

        		if($params->get('product_settings_show_child_item')){
        			foreach ($params->get('category_selector') as $cat) {
	        			if(false == in_array($cat, $cats)) {
			                array_push( $cats, $cat );
			            }
	        			$cats = self::getAllCategoryIds($cat,$cats);
	        		}
        		}else{
        			$cats = $params->get('category_selector');
        		}
        		
    			if($cats){
	    			$categoryIds = ' AND items.`primary_category` IN (';
	        		foreach ($cats as $cat) {
	        			$categoryIds .= $cat.',';
	        		}
		        	$categoryIds = substr($categoryIds, 0, -1);	
	    			$categoryIds .= ') ';
    			}
        	}
        }
        
        $product_show = $params->get('product_show');
        $where = '';
		$dateNow =  date( "Y-m-d");
		
        if($product_show == 2){ // FEATURED + NEW
        	$where = ' AND (items.`featured` = 1 || (items.`show_new` = 1 AND items.`until_new_date` >= "'.$dateNow.'")) ';
        }elseif($product_show == 3){ //FEATURED
        	$where = ' AND items.`featured` = 1 AND items.`featured_start_date` <= "' . $dateNow . '" AND items.`featured_end_date` >= "' . $dateNow . '"';

        }elseif($product_show == 4){ //NEW
        	$where = ' AND (items.`show_new` = 1 AND items.`until_new_date` >= "'.$dateNow.'") ';
        }
        

		$sql = "SELECT items.*, 
			category.`name` AS category_name
				FROM #__pago_items as items 
					LEFT JOIN #__pago_categoriesi as category
					ON (category.`id` = items.`primary_category`)
						WHERE items.`visibility` = 1 
							{$qty}
							AND items.`published` = 1
							{$where}
							{$categoryIds}
								ORDER BY {$order}
								LIMIT " . $params->get('limit', 5);

		$db->setQuery( $sql );
		$items = $db->loadObjectList();

		return $items;
	}

	public static function getMostSoldItem($params)
	{
		$db 	= JFactory::getDBO();
		$sql 	= "SELECT count( * ) , item_id, i.*
				FROM `#__pago_orders_items` AS oi
				LEFT JOIN #__pago_orders AS o ON oi.order_id = o.order_id
				LEFT JOIN #__pago_items AS i ON oi.item_id = i.id
				WHERE o.order_status = 'C'
				AND i.published = '1'
				AND i.visibility = '1'
				GROUP BY item_id
				ORDER BY count( * ) DESC 
				LIMIT " . $params->get('limit', 5);
				
		$db->setQuery( $sql ); 
		$items  = $db->loadObjectList();

		return $items;
	}

	public static function getViewSettings( $params ){
		$mod_pago_view_setting = new stdClass();
		
		if($params->get('product_settings_inherit_settings')){
			$db = JFactory::getDBO();
			$inherit_category = $params->get('inherit_category');

		    $sql = 'select * from #__pago_categoriesi where visibility = 1 AND id='.$inherit_category;

		    $db->setQuery( $sql );

		    $cat = $db->loadObject();
				
		    if($cat){
			if ($cat->inherit_parameters_from > 1){
				$sql = 'select * from #__pago_categoriesi where visibility = 1 AND id='.$cat->inherit_parameters_from;
				$db->setQuery( $sql );
				$cat = $db->loadObject();
			}
		    $mod_pago_view_setting->product_settings_product_title 			= $cat->product_settings_product_title;
				$mod_pago_view_setting->product_settings_link_to_product		= $cat->product_settings_link_to_product;
				$mod_pago_view_setting->product_settings_product_image 			= $cat->product_settings_product_image;
				$mod_pago_view_setting->product_settings_link_on_product_image  = $cat->product_settings_link_on_product_image;
				$mod_pago_view_setting->product_settings_featured_badge 		= $cat->product_settings_featured_badge;
				$mod_pago_view_setting->product_settings_quantity_in_stock 		= $cat->product_settings_quantity_in_stock;
				$mod_pago_view_setting->product_settings_short_desc 			= $cat->product_settings_short_desc;
				$mod_pago_view_setting->product_settings_short_desc_limit 		= $cat->product_settings_short_desc_limit;
				$mod_pago_view_setting->product_settings_desc 					= $cat->product_settings_desc;
				$mod_pago_view_setting->product_settings_desc_limit 			= $cat->product_settings_desc_limit;
				$mod_pago_view_setting->product_settings_sku 					= $cat->product_settings_sku;
				$mod_pago_view_setting->product_settings_price 					= $cat->product_settings_price;
				$mod_pago_view_setting->product_settings_discounted_price 		= $cat->product_settings_discounted_price;
				$mod_pago_view_setting->product_settings_attribute 				= $cat->product_settings_attribute;
				$mod_pago_view_setting->product_settings_media 					= $cat->product_settings_media;
				$mod_pago_view_setting->product_settings_downloads 				= $cat->product_settings_downloads;
				$mod_pago_view_setting->product_settings_rating 				= $cat->product_settings_rating;
				$mod_pago_view_setting->product_settings_category				= $cat->product_settings_category;
				$mod_pago_view_setting->product_settings_read_more 				= $cat->product_settings_read_more;
				$mod_pago_view_setting->product_settings_add_to_cart 			= $cat->product_settings_add_to_cart;
				$mod_pago_view_setting->product_settings_add_to_cart_qty 		= $cat->product_settings_add_to_cart_qty;
				$mod_pago_view_setting->product_settings_fb 					= $cat->product_settings_fb;
				$mod_pago_view_setting->product_settings_tw 					= $cat->product_settings_tw;
				$mod_pago_view_setting->product_settings_pinterest 				= $cat->product_settings_pinterest;
				$mod_pago_view_setting->product_settings_google_plus 			= $cat->product_settings_google_plus;
				$mod_pago_view_setting->product_settings_product_title 			= $cat->product_settings_product_title;
				$mod_pago_view_setting->product_image_settings 					= $cat->product_view_settings_image_settings;	
				$mod_pago_view_setting->product_settings_product_title_limit 	= $cat->product_settings_product_title_limit;	
		    }
		}else{

			$mod_pago_view_setting->product_settings_product_title 			= $params->get('product_settings_product_title');
			$mod_pago_view_setting->product_settings_product_title_limit	= $params->get('product_settings_product_title_limit');
			$mod_pago_view_setting->product_settings_link_to_product		= $params->get('product_settings_link_to_product');
			$mod_pago_view_setting->product_settings_product_image 			= $params->get('product_settings_product_image');
			$mod_pago_view_setting->product_settings_link_on_product_image  = $params->get('product_settings_link_on_product_image');
			$mod_pago_view_setting->product_settings_featured_badge 		= $params->get('product_settings_featured_badge');
			$mod_pago_view_setting->product_settings_quantity_in_stock 		= $params->get('product_settings_quantity_in_stock');
			$mod_pago_view_setting->product_settings_short_desc 			= $params->get('product_settings_short_desc');
			$mod_pago_view_setting->product_settings_short_desc_limit 		= $params->get('product_settings_short_desc_limit');
			$mod_pago_view_setting->product_settings_desc 					= $params->get('product_settings_desc');
			$mod_pago_view_setting->product_settings_desc_limit 			= $params->get('product_settings_desc_limit');
			$mod_pago_view_setting->product_settings_sku 					= $params->get('product_settings_sku');
			$mod_pago_view_setting->product_settings_price 					= $params->get('product_settings_price');
			$mod_pago_view_setting->product_settings_discounted_price 		= $params->get('product_settings_discounted_price');
			$mod_pago_view_setting->product_settings_attribute 				= $params->get('product_settings_attribute');
			$mod_pago_view_setting->product_settings_media 					= $params->get('product_settings_media');
			$mod_pago_view_setting->product_settings_downloads 				= $params->get('product_settings_downloads');
			$mod_pago_view_setting->product_settings_rating 				= $params->get('product_settings_rating');
			$mod_pago_view_setting->product_settings_category				= $params->get('product_settings_category');
			$mod_pago_view_setting->product_settings_read_more 				= $params->get('product_settings_read_more');
			$mod_pago_view_setting->product_settings_add_to_cart 			= $params->get('product_settings_add_to_cart');
			$mod_pago_view_setting->product_settings_add_to_cart_qty 		= $params->get('product_settings_add_to_cart_qty');
			$mod_pago_view_setting->product_settings_fb 					= $params->get('product_settings_fb');
			$mod_pago_view_setting->product_settings_tw 					= $params->get('product_settings_tw');
			$mod_pago_view_setting->product_settings_pinterest 				= $params->get('product_settings_pinterest');
			$mod_pago_view_setting->product_settings_google_plus 			= $params->get('product_settings_google_plus');
			$mod_pago_view_setting->product_settings_product_title 			= $params->get('product_settings_product_title');
			// $mod_pago_view_setting->product_grid_extra_small 				= $params->get('product_grid_extra_small');
			// $mod_pago_view_setting->product_grid_small 						= $params->get('product_grid_small');
			// $mod_pago_view_setting->product_grid_medium 					= $params->get('product_grid_medium');
			// $mod_pago_view_setting->product_grid_large 						= $params->get('product_grid_large');
			$mod_pago_view_setting->product_image_settings 					= $params->get('product_image_settings');
		}

		$mod_pago_view_setting->product_grid_extra_small 				= $params->get('product_grid_extra_small');
		$mod_pago_view_setting->product_grid_small 						= $params->get('product_grid_small');
		$mod_pago_view_setting->product_grid_medium 					= $params->get('product_grid_medium');
		$mod_pago_view_setting->product_grid_large 						= $params->get('product_grid_large');

		$mod_pago_view_setting->product_settings_view_mode = $params->get('product_settings_view_mode');

		return $mod_pago_view_setting;
	}
	static public function getAllCategoryIds( $category_id, &$found = array() ){
		$db = JFactory::getDBO();
	    $sql = 'select `id` from #__pago_categoriesi where visibility = 1 AND parent_id='.$category_id;
	    $db->setQuery( $sql );
	    $cats = $db->loadObjectList();
	    if($cats) {
	        foreach ($cats as $cat) {
	            if(false == in_array($cat->id, $found)) {
	                array_push( $found, $cat->id );
	            }
	            self::getAllCategoryIds($cat->id, $found);
	        }
	    }
	    return $found;
	}
	static public function product_display_attribute( $item ) {
		Pago::load_helpers( array( 'attributes' ) );

		$attributes = PagoAttributesHelper::get_item_attributes( $item );
		$removeDefault = false;
		$html = '';

		$attr_type=array('color','size','material','custom');
		$size_type=array(
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN')
			);
		if ( $attributes ) {
			foreach ($attributes as $attribute) {
				if(isset($attribute->options)){
					$isBorder = 'has-border-bottom';
					if ($attribute->display_type == 0){
						$isBorder = '';
					}
					$isColorList = '';
					if ($attribute->display_type == 1 && $attribute->type==0){
						$isColorList = 'color-list';
					}
					$html .= "<div class='pg-attribute-product-container pg-mod-product-field ".$isBorder." clearfix' type='".$attr_type[$attribute->type]."'>";

						//if($attribute->type=='1') $attribute->name.=$size_type[$attribute->size];
						$html .= '<div class = "pg-attr-'.$attr_type[$attribute->type].'"><label class="pg-attribute-label '.$isColorList.'" for="pg-attribute-' . $attribute->id . '">' . $attribute->name . ':</label></div>';
						if( $attribute->options ) {
							$html .= "<div class='pg_attr_options pg_attr_". $attribute->id ."' attr_id='".$attribute->id."' attrType='".$attribute->type."' attrDisplayType='".$attribute->display_type."'>";

							switch ($attribute->display_type) {
								case '0': //dropdown
									if($attribute->type==0){
										$doc = JFactory::$document;
										$style_colors = '';
										foreach ($attribute->options as $option) {
											$style_colors .= '.pg-color-'.$option->name.':after{
												background-color:'.$option->color.';
											}';
										}
										$doc->addStyleDeclaration($style_colors);
									}

									$html .= "<select name='attrib[".$attribute->id."]' onchange='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",\"\",".$item->id.")'>";
									if($attribute->required != 1){
										$html .= "<option value='0' selected = 'selected' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list pg-".$attr_type[$attribute->type]."-none' rel = 'none'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</option>";
									}
									foreach ($attribute->options as $option) {
										$html .= "<option value='".$option->id."' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list pg-".$attr_type[$attribute->type]."-".$option->name." attr_option_". $option->id ."' attr_option='".$option->id."' rel='".$option->name."'>".$option->name."</option>";
									}
									$html .= "</select>";
								break;
								case '1': //List
									foreach ($attribute->options as $option) {
										$preValue = 0;
										$custom_style='';
										if($attribute->type==0){
											$custom_style = "style='background-color:". $option->color."'";
										}
										$required = "";
									if($attribute->required == 1){
										$required = "required='1'";
									}

										$html .= "<input class='attr_input attr_option_". $option->id ."' opt_id='". $option->id ."' type='hidden' name='attrib[".$attribute->id."][".$option->id."][selected]' value='".$preValue."' />";
										$html .= "<span ".$required." title=".$option->name." onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list attr_option_". $option->id ."' attr_option='".$option->id."' ".$custom_style." >";
										if($attribute->type==0) $html .= "</span>";
										else $html .= $option->name."</span>";	
									}
								break;
								case '2':
									if($attribute->required != 1){
										$html .= "<input name='attrib[".$attribute->id."]' type='radio' title=".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')." value='0' onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class='attr_radio attr_option_0' >";
										if($attribute->type==0){
											$html .= "<span onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class='pg_".$attr_type[$attribute->type]."_option_list pg_".$attr_type[$attribute->type]."_radio pg-".$attr_type[$attribute->type]."-none'></span>";
											$html .= "<span onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class = 'pg-attr-".$attr_type[$attribute->type]."-radio-none-name'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
										}else{
											$html .= "<span onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class = 'pg_".$attr_type[$attribute->type]."_option_list pg-attr-".$attr_type[$attribute->type]."-radio-none-name'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
										}
									}
									foreach ($attribute->options as $option) {
										$custom_style='';
										if($attribute->type==0){
											$custom_style = "style='background-color:". $option->color."'";
										}
										$html .= "<input name='attrib[".$attribute->id."]' type='radio' title=".$option->name." value='".$option->id."' onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_attribute_option attr_radio attr_option_". $option->id ."' >";
										if($attribute->type==0){
											$html .="<span onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_".$attr_type[$attribute->type]."_option_list' ".$custom_style."></span>";
											$html .="<span onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class = 'pg-attr-".$attr_type[$attribute->type]."-radio-name'>".$option->name."</span>";
										}else{
											$html .= "<span onClick='mod_show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class = 'pg_".$attr_type[$attribute->type]."_option_list pg-attr-".$attr_type[$attribute->type]."-radio-name'>".$option->name."</span>";
										}
									}
								break;
							}

							$html .= "</div>";
						}
					$html.="</div>";
				}
			}
		}
		return $html;
	}

}
?>
