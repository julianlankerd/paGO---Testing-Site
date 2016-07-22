<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
* View class for a list of paGO orders.
*
* @package   Joomla.Administrator
* @subpackage  com_pago
* @since   2.5
*/
class PagoViewAddorder extends JViewLegacy
{

	/**
	* Display the view
	*/

	public function order_item_view( $tpl = null ){
		// Helpers
		Pago::load_helpers( array( 'imagehandler' ) ); 
		//Pago::load_helpers( array( 'categories', 'imagehandler', 'pagohtml' ) ); 

		
		// Initialize some variables
		$document = JFactory::getDocument();
		$app      = JFactory::getApplication();
		//$pathway  = $app->getPathWay();
		
		$filter   = JFilterInput::getInstance();
		$user = JFactory::getUser();

		// Set view from template switcher
		//$catnip = new catnip( 'pago_categories', 'com_pago', 'category', 'cid', false );

		$id          = $filter->clean( JFactory::getApplication()->input->get('Itemid'), 'INT' );
		$user_id = JFactory::getApplication()->input->get('user_id');
		$config = Pago::get_instance('config')->get('global');

		$items_model = JModelLegacy::getInstance( 'Itemslist', 'PagoModel' );

		$items_model->setState( 'id', $id );
        $item = $items_model->get(false,false);

		// Get all images attached to product
		$images = PagoImageHandlerHelper::get_item_files( $item->id, true, array( 'images','video' ) );
		// if ( $this->config->get( 'allow_user_uploaded', 1 ) ) {
		// 	$user_images = PagoImageHandlerHelper::get_item_files( $item->id, true,
		// 		array( 'user' ) );
		// 	$this->assignRef( 'user_images', $user_images );
		// }
		

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
        
        // if($viewSettings->product_view_settings_related_products == 1){
        //     $relatedProducts = $items_model->getRelatedProduct($item,$viewSettings->product_view_settings_related_num_of_products);
        // }
       
        /// get comments
        //$commentsModel = JModelLegacy::getInstance( 'Comments', 'PagoModel' );
        //$commentsCount = $commentsModel->getCommentsCount($item->id);

		//JLoader::register( 'NavigationHelper', JPATH_COMPONENT .'/helpers/navigation.php');
		//$nav = new NavigationHelper();

		// Create Title and Pathway data
		//$title = false;
		//$cid = JFactory::getApplication()->input->get('cid');
		//$nav -> generateBreadcrumPath($cid);
		// Create title cat_name | item_name
		//$title .= html_entity_decode($category->name) . ' | ' . html_entity_decode($item->name);
		//$this->set_metadata( 'item', $id, $title );

		// Set Custom layout
		// $layout = Pago::get_instance('categoriesi')->get_custom_layout($item->id, 'item', 'item');

  //       if ($layout != "")
  //       {
  //           $this->set_theme($layout);
		// }
  //       $this->set('item');


		//$this->assignRef( 'nav', $nav );
        $this->assignRef( 'item', $item );
        $this->assignRef( 'config', $config );
       // $this->assignRef( 'commentsCount', $commentsCount );
        $this->assignRef( 'images', $images );
		$this->assignRef( 'document', $document );
		$this->assignRef( 'category', $category );
        $this->assignRef( 'viewSettings', $viewSettings );
		//$this->assignRef( 'relatedProducts', $relatedProducts );
		$this->assign( 'option', 'com_pago' );
		$this->assign( 'view', 'category' );
		//$this->assignRef( 'user', $user );

        parent::display( $tpl );
	}
	public function display( $tpl = null )
	{
		switch( $this->_layout ){
			case 'order_item_view': $this->order_item_view(); return;
		}

		$cid = JFactory::getApplication()->input->get('cid',  0, 'array');
		$cid = (int) $cid[0];
		//$this->users = $this->get( 'Users' );
		$this->items = $this->get( 'Items' );
		$this->Allitems = $this->items;
		$this->users = Pago::get_instance( 'users' )->getPagoUsers();

		// Load language file
   		$payment_lang_list = PagoHelper::get_all_plugins('pago_gateway', 2);
   		$shipping_lang_list = PagoHelper::get_all_plugins('pago_shippers', 2);
   		$language = JFactory::getLanguage();
   		$base_dir =  JPATH_ADMINISTRATOR;
   		$language_tag = $language->getTag();

   		for($l=0;$l<count($payment_lang_list);$l++)
   		{
			$extension = 'plg_pago_gateway_'.$payment_lang_list[$l]->element;
			$language->load($extension, $base_dir, $language_tag, true);
   		}

   		for($l=0;$l<count($shipping_lang_list);$l++)
   		{
			$extension_ship = 'plg_pago_shippers_'.$shipping_lang_list[$l]->element;
			$language->load($extension_ship, $base_dir, $language_tag, true);
   		}
		//End

		$order 	= Pago::get_instance('orders')->get($cid);
		$user_id = JFactory::getApplication()->input->get('user_id', 0);
		$address_id = JFactory::getApplication()->input->get('address_id', 0);
		$saddress_id = JFactory::getApplication()->input->get('saddress_id', 0);
		$shipment = $order['shipments'][0];
		$shipper = explode('-', $shipment['carrier']);
		isset( $shipper[0] ) or $shipper[0] = 'Unspecified';
		isset( $shipper[1] ) or $shipper[1] = 'Unspecified';

		$bind_data = array(
			'information' => array(
				'order_id' => $order['details']->order_id,
				'order_status' => '',
				'customer_name' => '',
				'order_date' => $order['details']->cdate,
			),
			'shipping_details' => array(
				'carrier' => $shipper[0],
				'method' => trim($shipper[1]),
				'shipping_total' => Pago::get_instance('price')->format($shipment['shipping_total'])
			),
			'address_billing' => $order['addresses']['billing'],
			'address_shipping' => $order['addresses']['shipping'],
			'items' => $order['items']
		);
		PagoHtml::behaviour_jqueryui();
		PagoHtml::behaviour_jquery_validator();
		
		
		
		PagoHtml::add_js( JURI::root( true ) .
    			'/administrator/components/com_pago/javascript/jquery-ui/js/jquery.multiselect.min.js');

		PagoHtml::add_js( JURI::root( true ) .
    			'/administrator/components/com_pago/javascript/jquery-ui/js/jquery.multiselect.filter.js');
		//PagoHtml::add_js( JURI::root( true ) .
    			//'/administrator/components/com_pago/javascript/com_pago_order.js');

		// PagoHtml::add_js( JURI::root( true ) .
  //   			'/administrator/components/com_pago/javascript/view_item.js');


		PagoHtml::add_css( JURI::base( true )
			. '/components/com_pago/css/jquery-ui.css' );
		PagoHtml::add_css( JURI::base( true )
			. '/components/com_pago/css/jquery.multiselect.css' );
		PagoHtml::add_css( JURI::base( true )
			. '/components/com_pago/css/jquery.multiselect.filter.css' );

		Pago::load_helpers('pagoparameter');
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$params = new PagoParameter($bind_data,  $cmp_path . 'views/addorder/tmpl/fields.xml');

		JForm::addfieldpath(array($cmp_path . DS . 'elements'));

		$info = $params->render('information',
			'information',
			JText::_('PAGO_ORDERS_BASIC_DETAILS')
		);
		$shipping = $params->render('shipping_details',
			'shipping_details',
			JText::_('PAGO_ORDERS_SHIPPING_DETAILS')
		);
		$address_b = $params->render('address_billing',
			'address_billing',
			JText::_('PAGO_ORDERS_BILLING_DETAILS')
		);
		$address_s = $params->render('address_shipping',
			'address_shipping',
			JText::_('PAGO_ORDERS_MAILING_DETAILS')
		);

		$all_users = Pago::get_instance( 'users' )->getAllUsers(true);
        	$this->assign( 'all_users', $all_users);

		$this->assignRef('information', $info);
		$this->assignRef('address_billing', $address_b);
		$this->assignRef('address_shipping', $address_s);
		$this->assignRef('user_id', $user_id);
		$this->assignRef('address_id', $address_id);
		$this->assignRef('saddress_id', $saddress_id);

		if ($user_id)
		{
			JToolBarHelper::save();
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	* Add the page title and toolbar.
	*
	* @since 1.6
	*/
	protected function addToolbar()
	{
		JToolBarHelper::cancel();
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
