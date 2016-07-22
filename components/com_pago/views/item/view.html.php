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
class PagoViewItem extends PagoView
{
	function display( $tpl = null )
	{
		// Helpers
		Pago::load_helpers( array( 'categories', 'imagehandler', 'pagohtml' ) ); //, 'attributes'

		// Initialize some variables
		$document = JFactory::getDocument();
		$app      = JFactory::getApplication();
		$pathway  = $app->getPathWay();
		$filter   = JFilterInput::getInstance();
		$user = JFactory::getUser();

		// Set view from template switcher
		$catnip = new catnip( 'pago_categories', 'com_pago', 'category', 'cid', false );

		$id          = $filter->clean( $_REQUEST['id'], 'INT' );
		$items_model = JModelLegacy::getInstance( 'Itemslist', 'PagoModel' );

		$items_model->setState( 'id', $id );
        $item = $items_model->get(false,false);

        if ($item->published == 0)
        {
            JError::raiseError(404, sprintf(JText::_('PAGO_PRODUCT_NOT_EXIST'), $item->name, $item->sku));
        }

		// Get all images attached to product
		$images = PagoImageHandlerHelper::get_item_files( $item->id, true, array( 'images','video' ) );
		if ( $this->config->get( 'allow_user_uploaded', 1 ) ) {
			$user_images = PagoImageHandlerHelper::get_item_files( $item->id, true,
				array( 'user' ) );
			$this->assignRef( 'user_images', $user_images );
		}
		$category = Pago::get_instance( 'categoriesi' )->get( $item->primary_category);
		
		if($category->inherit_parameters_from > 1){
			// It is set from category to inherit parameters from other category
			$category_inherited_from = Pago::get_instance( 'categoriesi' )->get( $category->inherit_parameters_from, 1, false, false, true);
			$settingsCategory = Pago::get_instance( 'categoriesi' )->inherit_parameters_from_to( $category_inherited_from, $category);
		} else {
			// no parameter inheritance, just get the category settings
			$settingsCategory = $category;
		}

		$viewSettings = $this->viewSettings($item,$settingsCategory);


		if(($viewSettings->product_view_settings_short_desc == 1) || ($viewSettings->product_view_settings_desc == 1) || ($viewSettings->product_view_settings_product_title == 1)){
            if($viewSettings->product_view_settings_short_desc == 1){
                $item->description = TruncateHTML::truncateWords( $item->description, $viewSettings->product_view_settings_short_desc_limit, '...');
            }
            if($viewSettings->product_view_settings_desc == 1){
                $item->content = TruncateHTML::truncateWords( $item->content, $viewSettings->product_view_settings_desc_limit, '...');
            }
            if($viewSettings->product_view_settings_product_title == 1){
                $item->name = TruncateHTML::truncateWords( $item->name, $viewSettings->product_view_settings_product_title_limit, '...');
            }
        }
        
        if($viewSettings->product_view_settings_related_products == 1){
            $relatedProducts = $items_model->getRelatedProduct($item,$viewSettings->product_view_settings_related_num_of_products);
        }
        /// get comments
        $commentsModel = JModelLegacy::getInstance( 'Comments', 'PagoModel' );
        $commentsCount = $commentsModel->getCommentsCount($item->id);

		JLoader::register( 'NavigationHelper', JPATH_COMPONENT .'/helpers/navigation.php');
		$nav = new NavigationHelper();

		// Create Title and Pathway data
		$title = false;
		$cid = JFactory::getApplication()->input->get('cid');
		$nav -> generateBreadcrumPath($cid);
		// Create title cat_name | item_name
		$title .= html_entity_decode($category->name) . ' | ' . html_entity_decode($item->name);
		$this->set_metadata( 'item', $id, $title );

		// Set Custom layout
		$layout = Pago::get_instance('categoriesi')->get_custom_layout($item->id, 'item', 'item');

        if ($layout != "")
        {
            $this->set_theme($layout);
		}
        $this->set('item', $layout);
        
		$this->assignRef( 'nav', $nav );
        $this->assignRef( 'item', $item );
        $this->assignRef( 'commentsCount', $commentsCount );
        $this->assignRef( 'images', $images );
		$this->assignRef( 'document', $document );
		$this->assignRef( 'category', $category );
        $this->assignRef( 'viewSettings', $viewSettings );
		$this->assignRef( 'relatedProducts', $relatedProducts );
		$this->assign( 'option', 'com_pago' );
		$this->assign( 'view', 'category' );
		$this->assignRef( 'user', $user );

        parent::display( $tpl );
    }
    public function viewSettings($item,$cat){
    	$view_setting = new stdClass();

    	if($item->view_settings_product_title == 2){
    		$view_setting->product_view_settings_product_title	= $cat->product_view_settings_product_title;
    	}else{
    		$view_setting->product_view_settings_product_title  = $item->view_settings_product_title;
    	}

        if($item->view_settings_title_limit_inherit == 0){
            $view_setting->product_view_settings_product_title_limit  = $cat->product_view_settings_product_title_limit;
        }else{
            $view_setting->product_view_settings_product_title_limit  = $item->view_settings_title_limit;
        }

		if($item->view_settings_product_image == 2){
    		$view_setting->product_view_settings_product_image	= $cat->product_view_settings_product_image;
    	}else{
    		$view_setting->product_view_settings_product_image  = $item->view_settings_product_image;
    	}

    	if($item->view_settings_featured_badge == 2){
    		$view_setting->product_view_settings_featured_badge	= $cat->product_view_settings_featured_badge;
    	}else{
    		$view_setting->product_view_settings_featured_badge  = $item->view_settings_featured_badge;
    	}

    	if($item->view_settings_quantity_in_stock == 2){
    		$view_setting->product_view_settings_quantity_in_stock	= $cat->product_view_settings_quantity_in_stock;
    	}else{
    		$view_setting->product_view_settings_quantity_in_stock  = $item->view_settings_quantity_in_stock;
    	}

    	if($item->view_settings_short_desc == 2){
    		$view_setting->product_view_settings_short_desc			= $cat->product_view_settings_short_desc;
    		$view_setting->product_view_settings_short_desc_limit	= $cat->product_view_settings_short_desc_limit;
    	}else{
    		$view_setting->product_view_settings_short_desc		 	= $item->view_settings_short_desc;
    		$view_setting->product_view_settings_short_desc_limit   = $item->view_settings_short_desc_limit;
    	}

    	if($item->view_settings_desc == 2){
    		$view_setting->product_view_settings_desc			= $cat->product_view_settings_desc;
    		$view_setting->product_view_settings_desc_limit		= $cat->product_view_settings_desc_limit;
    	}else{
    		$view_setting->product_view_settings_desc		 	= $item->view_settings_desc;
    		$view_setting->product_view_settings_desc_limit   	= $item->view_settings_desc_limit;
    	}

    	if($item->view_settings_sku == 2){
    		$view_setting->product_view_settings_sku	= $cat->product_view_settings_sku;
    	}else{
    		$view_setting->product_view_settings_sku  = $item->view_settings_sku;
    	}

		if($item->view_settings_price == 2){
    		$view_setting->product_view_settings_price	= $cat->product_view_settings_price;
    	}else{
    		$view_setting->product_view_settings_price  = $item->view_settings_price;
    	}

    	if($item->view_settings_discounted_price == 2){
    		$view_setting->product_view_settings_discounted_price	= $cat->product_view_settings_discounted_price;
    	}else{
    		$view_setting->product_view_settings_discounted_price  = $item->view_settings_discounted_price;
    	}

    	if($item->view_settings_attribute == 2){
    		$view_setting->product_view_settings_attribute	= $cat->product_view_settings_attribute;
    	}else{
    		$view_setting->product_view_settings_attribute  = $item->view_settings_attribute;
    	}

		if($item->view_settings_media == 2){
    		$view_setting->product_view_settings_media	= $cat->product_view_settings_media;
    	}else{
    		$view_setting->product_view_settings_media  = $item->view_settings_media;
    	}

		if($item->view_settings_downloads == 2){
    		$view_setting->product_view_settings_downloads	= $cat->product_view_settings_downloads;
    	}else{
    		$view_setting->product_view_settings_downloads  = $item->view_settings_downloads;
    	}

		if($item->view_settings_rating == 2){
    		$view_setting->product_view_settings_rating	= $cat->product_view_settings_rating;
    	}else{
    		$view_setting->product_view_settings_rating  = $item->view_settings_rating;
    	}

		if($item->view_settings_category == 2){
    		$view_setting->product_view_settings_category	= $cat->product_view_settings_category;
    	}else{
    		$view_setting->product_view_settings_category  = $item->view_settings_category;
    	}

		if($item->view_settings_add_to_cart == 2){
    		$view_setting->product_view_settings_add_to_cart	= $cat->product_view_settings_add_to_cart;
    	}else{
    		$view_setting->product_view_settings_add_to_cart  = $item->view_settings_add_to_cart;
    	}

		if($item->view_settings_add_to_cart_qty == 2){
    		$view_setting->product_view_settings_add_to_cart_qty	= $cat->product_view_settings_add_to_cart_qty;
    	}else{
    		$view_setting->product_view_settings_add_to_cart_qty  = $item->view_settings_add_to_cart_qty;
    	}

    	if($item->view_settings_product_review == 2){
    		$view_setting->product_view_settings_product_review	= $cat->product_view_settings_product_review;
    	}else{
    		$view_setting->product_view_settings_product_review  = $item->view_settings_product_review;
    	}

		if($item->view_settings_related_products == 2){
            $view_setting->product_view_settings_related_num_of_products    = $cat->product_view_settings_related_num_of_products;
            $view_setting->product_view_settings_related_products   = $cat->product_view_settings_related_products;
        }else{
            $view_setting->product_view_settings_related_products  = $item->view_settings_related_products;
            $view_setting->product_view_settings_related_num_of_products  = $item->view_settings_related_num_of_products;
    	}

    	if($item->view_settings_add_to_cart == 2){
    		$view_setting->product_settings_add_to_cart	= $cat->product_settings_add_to_cart;
    	}else{
    		$view_setting->product_settings_add_to_cart  = $item->view_settings_add_to_cart;
    	}

    	if($item->view_settings_add_to_cart_qty == 2){
    		$view_setting->product_settings_add_to_cart_qty	= $cat->product_settings_add_to_cart_qty;
    	}else{
    		$view_setting->product_settings_add_to_cart_qty  = $item->view_settings_add_to_cart_qty;
    	}

    	if($item->view_settings_fb == 2){
    		$view_setting->product_view_settings_fb	= $cat->product_view_settings_fb;
    	}else{
    		$view_setting->product_view_settings_fb  = $item->view_settings_fb;
    	}

    	if($item->view_settings_tw == 2){
    		$view_setting->product_view_settings_tw	= $cat->product_view_settings_tw;
    	}else{
    		$view_setting->product_view_settings_tw  = $item->view_settings_tw;
    	}

    	if($item->view_settings_pinterest == 2){
    		$view_setting->product_view_settings_pinterest	= $cat->product_view_settings_pinterest;
    	}else{
    		$view_setting->product_view_settings_pinterest  = $item->view_settings_pinterest;
    	}

    	if($item->view_settings_google_plus == 2){
    		$view_setting->product_view_settings_google_plus	= $cat->product_view_settings_google_plus;
    	}else{
    		$view_setting->product_view_settings_google_plus  = $item->view_settings_google_plus;
    	}

		if($item->view_settings_image_settings_show == 2){
    		$view_setting->product_view_settings_image_settings	= $cat->product_view_settings_image_settings;
    	}else{
    		$view_setting->product_view_settings_image_settings  = $item->view_settings_image_settings;
    	}
        if($item->view_settings_product_image_zoom == 0){
            $view_setting->product_view_settings_image_zoom = $cat->product_view_settings_product_image_zoom;
        }else{
            $view_setting->product_view_settings_image_zoom  = $item->view_settings_product_image_zoom;
        }
		return $view_setting;
    }
}
?>
