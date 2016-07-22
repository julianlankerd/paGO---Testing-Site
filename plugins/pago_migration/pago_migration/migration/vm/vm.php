<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// Protect from unauthorized access
defined('_JEXEC') or die();
class PagoMigrationVm
{
    public function migrateUsers()
    {
        $db = JFactory::getDBO();
        $sel_vm_users = "SELECT * FROM #__virtuemart_vmusers group by virtuemart_user_id";
        $db->setQuery($sel_vm_users);
        $virtuemart_users = $db->loadObjectList();
        $total_users = 0;
		
        foreach ($virtuemart_users as $vm_users)
        {
            // get single uer
            $sel_vm_single_user = "SELECT * FROM #__virtuemart_userinfos WHERE virtuemart_user_id =" . $vm_users->virtuemart_user_id;
            $db->setQuery($sel_vm_single_user);
            $virtuemart_single_user = $db->loadObjectList();
            $BillingAdded = false;
			
            foreach ($virtuemart_single_user as $vm_single_user)
            {
                // get User address Type
                if($vm_single_user->address_type == 'BT' && !$BillingAdded)
                {
                    $BillingAdded = true;
                    $address_type = 'b';
                    $address_type_name = 'Billing';
                }
                else
                {
                    $address_type = 'm';
                    $address_type_name = 'Shipping';
                }
				
                $row = JTable::getInstance('userinfo', 'Table');
                $row->load();
				$state = $this -> getVmState($vm_single_user->virtuemart_state_id);
				$country = $this -> getVmCountry($vm_single_user->virtuemart_country_id);
				$email = $this -> getUserEmail($vm_single_user->virtuemart_user_id);
                $row->user_id = $vm_single_user->virtuemart_user_id;
                $row->address_type = $address_type;
                $row->address_type_name = $address_type_name;
                $row->company = $vm_single_user->company;
                $row->title = $vm_single_user->title;
                $row->last_name = $vm_single_user->last_name;
                $row->first_name = $vm_single_user->first_name;
                $row->middle_name = $vm_single_user->middle_name;
                $row->phone_1 = $vm_single_user->phone_1;
                $row->phone_2 = $vm_single_user->phone_2;
                $row->fax = $vm_single_user->fax;
                $row->address_1 = $vm_single_user->address_1;
                $row->address_2 = $vm_single_user->address_2;
                $row->city = $vm_single_user->city;
                $row->state = $state;
                $row->country = $country;
                $row->zip = $vm_single_user->zip;
                $row->user_email = $email;
                $row->cdate = $vm_single_user->created_on;
                $row->mdate = $vm_single_user->modified_on;
                $row->perms = '';
                $row->store();

                $total_users++;
            }
        }

        return $total_users;
    }
	
	public function getVmState($stateId)
	{
		$db = JFactory::getDBO();
		$stateQue = "SELECT state_name FROM #__virtuemart_states WHERE virtuemart_state_id=" . $stateId;
		$db -> setQuery($stateQue);

		return $db -> loadResult();
	}

	public function getVmCountry($couId)
	{
		$db = JFactory::getDBO();
		$couQue = "SELECT country_2_code FROM #__virtuemart_countries WHERE virtuemart_country_id=" . $couId;
		$db -> setQuery($couQue);

		return $db -> loadResult();
	}
	
	public function getUserEmail($id)
	{	
		$db = JFactory::getDBO();
		$emailQue = "SELECT email FROM #__users WHERE id=" . $id;
		$db -> setQuery($emailQue);

		return $db -> loadResult();
	}

    public function migrateOrders()
    {
        //  migrate orders
        $db = JFactory::getDBO();
        $sel_virtuemart_orders = "SELECT * FROM #__virtuemart_orders as o";
        $db->setQuery($sel_virtuemart_orders);
        $virtuemart_orders = $db->loadObjectList();
        $total_orders = 0;
		
        foreach($virtuemart_orders as $vm_order)
        {
            $vm_order_id = $vm_order->virtuemart_order_id;
            // Insert Pago Orders
            $pago_order_id = $this->insertPagoOrders($vm_order);
            $total_orders++;
            $this->insertPagoOrderAddress($vm_order_id, $pago_order_id);
            // End
            // Get Order Items
            $order_items = $this->getVmOrderItemDetail($vm_order_id);

            foreach ($order_items as $order_item)
            {
                $this->insertPagoOrderItem($order_item, $pago_order_id, $vm_order);
            }
        }
        return $total_orders;
    }
	
	public function getVmOrderItemDetail($vm_order_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT oi.* FROM #__virtuemart_order_items AS oi WHERE oi.virtuemart_order_id=" . $vm_order_id;
		$db -> setQuery($sql);

		return $db->loadObjectList();
	}
	
	public function getVmCurrency($order_currency_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT currency_code_3 FROM #__virtuemart_currencies WHERE virtuemart_currency_id='" . $order_currency_id . "'";
		$db -> setQuery($sql);

		return $db->loadObjectList();
	}

    public function insertPagoOrders($vm_order)
    {
        $db = JFactory::getDBO();
        $otable = JTable::getInstance( 'orders', 'Table' );
        $otable->order_id = $vm_order->virtuemart_order_id;
        $otable->user_id = $vm_order->virtuemart_user_id;
		$otable->vendor_id = $vm_order->virtuemart_vendor_id;
        $otable->order_number = $vm_order->order_number;
        $otable->payment_gateway = $this->getPaymentMethod($vm_order->virtuemart_paymentmethod_id);
        $otable->order_total = $vm_order->order_total;
        $otable->order_subtotal = $vm_order->order_subtotal;
        $otable->order_tax = $vm_order->order_tax;
		$otable->coupon_code = $vm_order->coupon_code;
		$otable->coupon_discount = $vm_order->coupon_discount;
        $otable->order_shipping = $vm_order->order_shipment;
		$otable->ship_method_id = $vm_order->virtuemart_shipmentmethod_id;
        $otable->order_shipping_tax = $vm_order->order_shipment_tax;
        $otable->coupon_discount = $vm_order->coupon_discount;
        $otable->order_discount = $vm_order->order_discount;
	    $otable->order_currency = $this->getVmCurrency($vm_order->order_currency);
        $otable->cdate = $vm_order->created_on;
        $otable->mdate = $vm_order->modified_on;
        $otable->order_status = $vm_order->order_status;
        $otable->customer_note = $vm_order->customer_note;
        $otable->ip_address = $vm_order->ip_address;
		$otable->user_email = $this->getUserEmail($vm_order->virtuemart_user_id);
      
        $order = $db->insertObject('#__pago_orders', $otable, 'order_id');

        $order_id = $otable->order_id;
       // $cardnumber = base64_decode($vm_order->order_payment_number);
		$paymentData = $this -> getPaymentMethod($vm_order->virtuemart_paymentmethod_id);
		$payment_capture_status = '';
		
		if ($vm_order->order_status == 'C')
		{
			$payment_capture_status = "Captured";
		}

        // insert order Payment data
        $db->setQuery("
            INSERT INTO #__pago_orders_sub_payments
                SET
                    order_id = {$order_id},
                    item_id = {$order_id},
                    txn_id = '',
                    payment = '{$paymentData}',
                    status = '{$vm_order->order_status}',
                    payment_data = '{$paymentData}',
                    card_number = '',
                    payment_capture_status = '{$payment_capture_status}',
                    isfraud = 0,
                    fraud_message = ''
        ");

        $db->query();

        return $order_id;
    }
	
	public function getPaymentMethod($virtuemart_paymentmethod_id)
	{
	   $db = JFactory::getDBO();
       $payment = "SELECT payment_element FROM #__virtuemart_paymentmethods WHERE virtuemart_paymentmethod_id=" . $virtuemart_paymentmethod_id;
       $db->setQuery($payment);

        return $db->loadResult();
	}

    public function insertPagoOrderAddress($vm_order_id, $pago_order_id)
    {
        $db = JFactory::getDBO();
        $sel_vm_order_users = "SELECT * FROM #__virtuemart_order_userinfos as ou WHERE virtuemart_order_id=".$vm_order_id;
        $db->setQuery($sel_vm_order_users);
        $vm_order_users = $db->loadObjectList();

        foreach ($vm_order_users as $vm_order_user)
        {
             // get User address Type
            if($vm_order_user->address_type == 'BT')
            {
                $address_type = 'b';
                $address_type_name = 'Billing';
            }
            else
            {
                $address_type = 's';
                $address_type_name = 'Shipping';
            }
            
			$state = $this -> getVmState($vm_order_user->virtuemart_state_id);
			$country =$this -> getVmCountry($vm_order_user->virtuemart_country_id);
			$ordAdd =  "INSERT INTO #__pago_orders_addresses (order_id, user_id, company, last_name, first_name, middle_name, phone_1, phone_2, address_1, address_2, city, fax, user_email, country, state, zip, address_type, cdate, mdate) VALUES(
                    '".$pago_order_id."',
                    '".$vm_order_user->virtuemart_user_id."',
                    '".$vm_order_user->company."',
                    '".$vm_order_user->last_name."',
                    '".$vm_order_user->first_name."',
                    '".$vm_order_user->middle_name."',
                    '".$vm_order_user->phone_1."',
                    '".$vm_order_user->phone_2."',
                    '".$vm_order_user->address_1."',
                    '".$vm_order_user->address_2."',
                    '".$vm_order_user->city."',
                     '".$vm_order_user->fax."',
                    '".$vm_order_user->email."',
                    '".$country."',
                    '".$state."',
                    '".$vm_order_user->zip."',
                    '".$address_type."',
                    '".$vm_order_user->created_on."',
                    '".$vm_order_user->modified_on."'
                )";

            $db->setQuery($ordAdd);
            $db->query();
        }
    }

    public function insertPagoOrderItem($order_item, $pago_order_id, $vm_order)
    {
        $data = array();
        $data['order_id'] = $pago_order_id;
        $data['item_id'] = $order_item->virtuemart_product_id;
        $data['qty'] = $order_item->product_quantity;
        $data['price'] = $order_item->product_item_price;
        $data['price_type'] = '';
        $data['attributes'] =  $order_item->product_attribute;
       // $data['sub_recur'] = $item->sub_recur;
        $data['order_item_shipping'] = $vm_order->order_shipment;
        $data['order_item_ship_method_id'] = $vm_order->virtuemart_shipmentmethod_id;

        $oitable = JTable::getInstance( 'orders_items', 'Table' );
        $oitable->bind($data);
        $oitable->check();
        $oitable->store();
    }

    public function migrateCategories()
    {
        //  migrate categories
        $db = JFactory::getDBO();
        $sel_virtuemart_categories = "SELECT * FROM #__virtuemart_categories as c LEFT JOIN #__virtuemart_category_categories as cx ON c.virtuemart_category_id = cx.category_child_id LEFT JOIN #__virtuemart_categories_en_gb as ceg ON c.virtuemart_category_id = ceg.virtuemart_category_id WHERE cx.category_parent_id = 0";
        $db->setQuery($sel_virtuemart_categories);
        $virtuemart_categories = $db->loadObjectList();

        $total_categories = 0;
        $total_items = 0;
        foreach($virtuemart_categories as $vm_category)
        {
            $parent_category_id = 1;
			$level = 1;
            $category_id = $vm_category->virtuemart_category_id;

            // Insert main category
            $insertedCategoryId = $this->insertPagoCategory($vm_category, $parent_category_id, 1);
            $total_categories++;
            $main_migrated_total_items = $this->migrateItems($category_id,$insertedCategoryId);
            $total_items = $total_items + $main_migrated_total_items;

            $sel_vm_sub_categories = "SELECT * FROM #__virtuemart_category_categories where category_parent_id!= 0 and category_parent_id =".$category_id;
            $db->setQuery($sel_vm_sub_categories);
            $virtuemart_sub_categories = $db->loadObjectList();
			$level++;

           foreach($virtuemart_sub_categories as $sub_category)
            {
                $subcategory_id = $sub_category->category_child_id;

                $sel_vm_redshop_category = "SELECT * FROM #__virtuemart_categories AS vc LEFT JOIN #__virtuemart_categories_en_gb as vceg ON vc.virtuemart_category_id = vceg.virtuemart_category_id  where vc.virtuemart_category_id =" . $subcategory_id;
                $db->setQuery($sel_vm_redshop_category);
                $vm_sub_category = $db->loadObjectList();

                $insertedCategoryId =  $this->insertPagoCategory($vm_sub_category[0], $insertedCategoryId, $level);
                $total_categories++;
                $sub_migrated_total_items = $this->migrateItems($subcategory_id,$insertedCategoryId);
                $total_items = $total_items + $sub_migrated_total_items;
            }
        }

        return $total_categories."_".$total_items;
    }


    public function insertPagoCategory($vm_category, $parent_category_id, $level=1)
    {

        $db = JFactory::getDBO();
        $table = JTable::getInstance( 'categoriesi', 'Table' );
        $catdata = array();
        $category_id = $vm_category->virtuemart_category_id;

        if ($category_id)
        {
            $catdata['id'] = '';
            $timestamp = time() + 86400;
            $table->setLocation( $parent_category_id, 'last-child');
            $catdata['created_time'] = date( 'Y-m-d H:i:s', time() );
        

            $catdata['alias'] = $vm_category->category_name;
            $catdata['expiry_date'] = $timestamp;
            $catdata['parent_id'] = $parent_category_id;
            $catdata['name'] = $vm_category->category_name;
            $catdata['visibility'] = 1;
            $catdata['published'] = $vm_category->published;
            $catdata['description'] = $vm_category->category_description;
            $catdata['item_count'] = 0;
            $catdata['created_user_id'] = $vm_category->created_by;
            $catdata['modified_user_id'] = $vm_category->modified_by;
            $catdata['modified_time'] = date( 'Y-m-d H:i:s', time() );
            $catdata['featured'] = 0;
            $catdata['category_settings_image_settings'] = '{"padding_left":"0","border_left":"1","margin_left":"0","margin_top":"0","border_top":"1","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"1","margin_right":"30","border_bottom":"1","margin_bottom":"20"}';
            $catdata['category_settings_product_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';
            $catdata['product_view_settings_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"3","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';

         //   $catdata = (object) $catdata;
            
            $table->bind( $catdata );
            $table->check();
            $table->store();
            $table->rebuildPath( $table->id );
            $table->rebuild( $table->id, $table->lft, $level, $table->path ); 
			
			 // Add category meta data
            $data['meta'] = array();
            $data['meta']['type'] = 'category';
            $data['meta']['title'] = $vm_category->category_name;
            $data['meta']['html_title'] = $vm_category->category_name;
            $data['meta']['author'] = $vm_category->metaauthor;
            $data['meta']['robots'] = $vm_category->metarobot;
            $data['meta']['keywords'] = $vm_category->metakey;
            $data['meta']['description'] = $vm_category->metadesc;
			
			 $meta = Pago::get_instance( 'meta' );

            foreach ( $data['meta'] as $key => $value ) {
                $meta->update( 'category', $table->id, $key, $value );
            }
			
			$this->insertCategoryImages($vm_category,$table->id);
            return $table->id; 

        }
    }

	 public function insertCategoryImages($vm_category , $pago_category_id)
    {
        $db = JFactory::getDBO();
        $itemCatId = $categoryId = $pago_category_id;
        jimport('joomla.filesystem.file');
        Pago::load_helpers('imagehandler');
        $dispatcher = KDispatcher::getInstance();
        $params = Pago::get_instance('config')->get();

        $category_images = array();
        //$category_images[] = $redshop_category->category_full_image;

        // get redshop additional media
      $qu = "SELECT m.file_title,m.file_url FROM #__virtuemart_medias AS m LEFT JOIN #__virtuemart_category_medias AS cm ON m.virtuemart_media_id=cm.virtuemart_media_id WHERE cm.virtuemart_category_id='" . $vm_category->virtuemart_category_id . "' AND m.file_type='category' ORDER BY cm.ordering";
        $db->setQuery($qu);
        $additional_medias = $db->loadObjectList();
		
        foreach($additional_medias as $additional_media)
        {
			$img = explode('category/', $additional_media->file_url);
            $category_images[] = $img[1];
        }
		
        $img_count = count($category_images);

        // Check for image count loop start
        if ($img_count > 0)
        {
            // Image check for loop start
            foreach($category_images as $category_image)
            {
                $title =  pathinfo($category_image,PATHINFO_FILENAME);
                $content = $title;
                $src_folder = JPATH_SITE . "/images/stories/virtuemart/category/" . $category_image;

                if(is_file($src_folder))
                {
                    if (!is_dir(JPATH_SITE . "/media/pago/category/" . $itemCatId))
                    {
                        mkdir(JPATH_SITE . "/media/pago/category/" . $itemCatId, 0755);
                    }
                    $uploads = PagoImageHandlerHelper::upload_dir( "/media/pago/category/" . $itemCatId );
                    $name_prefix = '';
                    $filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix.$category_image );
                    $dest_folder = JPATH_SITE . "/media/pago/category/" . $itemCatId . "/" . $filename;
                    $files[] = $filename;

                    copy($src_folder, $dest_folder);
                }
				

                // Files For Loop start
                for ($s = 0; $s < count($files); $s++)
                {
                    // Check for matching Image
                    if ($files[$s] != "" && $files[$s] == $filename)
                    {
                        $item_id = $itemCatId;
                        $org_file = JPATH_SITE . "/media/pago/category/" . $itemCatId . "/" . $files[$s];

                        $existing_image = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $itemCatId . "' AND `title`='".$filename."' and type='category'";
                        $db->setQuery($existing_image);
                        $existing_imageRs = $db->loadResult();

                        if($existing_imageRs == 0 )
                        {

                            $qu_img = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $itemCatId . "' AND `default`=1";
                            $db->setQuery($qu_img);
                            $dflImg = $db->loadResult();
                            $default = 0;

                            if ($dflImg == 0)
                            {
                                $default = 1;
                            }
                            $mimes = false;
                            $mime_check = true;
                            $filetype = PagoImageHandlerHelper::check_filetype( $files[$s], $mimes, $mime_check );
                            extract( $filetype );

                            $url = JURI::root()."/media/pago/category/" . $categoryId . "/" . $files[$s]; 
                            $file = array( 'file' => $org_file, 'file_name' => $files[$s], 'url' => $url,'type' => $type );

                            $url       = $file['url'];
                            $type      = $file['type'];
                            $file_name = $file['file_name'];
                            $file      = $file['file'];
                            $title     = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
                            //$content   = '';
                            $file_meta = array();

                            if ( $file_meta = @PagoImageHandlerHelper::read_image_metadata( $file ) ) {
                                if ( trim( $file_meta['title'] ) ) {
                                    $title = $file_meta['title'];
                                }
                                if ( trim( $file_meta['caption'] ) ) {
                                    $content = $file_meta['caption'];
                                }
                            }
                            $data = array(
                                'title'     => $title,
                                'caption'   => $content,
                                'alias'     => $title,
                                'item_id'   => $item_id,
                                'default'   => $default,
                                'published' => 1,
                                'file_name' => $file_name,
                                'type'      => 'category',
                                'mime_type' => $type,
                                'file_meta' => array( 'file_meta' => $file_meta ),
                                'created_time' => date( 'Y-m-d H:i:s', time() ),
                                'modified_time' => date( 'Y-m-d H:i:s', time() )
                            );


                            PagoImageHandlerHelper::generate_image_metadata( $file, $data, $params );

                            // Serialize meta data for storage
                            $data['file_meta'] = serialize( $data['file_meta'] );

                            $dispatcher->trigger('files_upload_before_store', array($data));
                             $row = JTable::getInstance( 'files', 'Table' );

                            if (!$row->bind($data))
                            {
                                $db->getErrorMsg();
                                $this->setError($this->_db->getErrorMsg());
                                return false;
                            }

                            PagoHelper::saveContentPrep($row);

                            // Quick and ugly fix
                            if ($row->introtext && !$row->fulltext )
                            {
                                $row->fulltext = $row->introtext;
                            }
                            elseif ( $row->introtext && $row->fulltext )
                            {
                                $row->fulltext = $row->introtext . ' ' . $row->fulltext;
                            }

                            unset( $row->introtext );

                            if (!$row->id)
                            {
                                $row->ordering = $row->getNextOrder("`item_id` = {$row->item_id} AND `type` = '$row->type'");
                            }
                            if ( !$row->store() )
                            {
                                $this->setError($row->getError());
                                return false;
                            }
                        }
                    }
                    // Check for matching Image loop end
                }
                // Image check for loop end
            }
            // Check for image count loop end
        } 

    }

    public function migrateItems($redshop_category_id,$pago_category_id)
    {
        $db = JFactory::getDBO();
        $sel_virtuemart_items = "SELECT * FROM #__virtuemart_products as p LEFT JOIN #__virtuemart_product_categories as px ON p.virtuemart_product_id = px.virtuemart_product_id  LEFT JOIN #__virtuemart_products_en_gb as peg ON p.virtuemart_product_id = peg.virtuemart_product_id WHERE px.virtuemart_category_id = ".$redshop_category_id;
        $db->setQuery($sel_virtuemart_items);
        $virtuemart_items = $db->loadObjectList();

        $added_virtuemart_items = array();
        $migrated_total_items = 0;
		
        foreach($virtuemart_items as $vm_item)
        {
            if(!array_key_exists($vm_item->virtuemart_product_id, $added_virtuemart_items))
            {
                // get redshop item stock
                $q_stock = "SELECT product_in_stock FROM `#__virtuemart_products` WHERE virtuemart_product_id = '" . $vm_item->virtuemart_product_id . "'";
                $db->setQuery($q_stock);
                $stocks = $db->loadResult();

                $vm_item->quantity = $stocks;
                
                $insertedItemId = $this->insertPagoItem($vm_item, $pago_category_id);
                $migrated_total_items++;
                $added_virtuemart_items[$vm_item->virtuemart_product_id] = $insertedItemId;

            }
            else
            {
                // add item in category Item table
                // get Pago item id
                $itemId = $added_redshop_pago_items[$vm_item->virtuemart_product_id];
                $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $itemId . "')";
                $db->setQuery($query_par_cat);
                $db->Query();
            }

        }

        return $migrated_total_items;

    }

    public function insertPagoItem($vm_item, $pago_category_id)
    {
        $db = JFactory::getDBO();
        $row = JTable::getInstance( 'items', 'Table' );
        $row->id = $vm_item->virtuemart_product_id;
        $row->sku = $vm_item->product_sku;
		$price = $this->getProductPrice($vm_item->virtuemart_product_id);
        $vm_item->product_name = utf8_encode($vm_item->product_name);
        $vm_item->product_s_desc = utf8_encode($vm_item->product_s_desc);
        $vm_item->product_desc = utf8_encode($vm_item->product_desc);
        $row->name = html_entity_decode(htmlspecialchars($vm_item->product_name));
        $row->qty = $vm_item->product_in_stock;
        $row->type = '';
        $row->published = $vm_item->published;
		if(isset($price -> product_price))
        $row->price = $price -> product_price;
        $row->tax_exempt = 0;
        $row->primary_category = $pago_category_id;
        $row->free_shipping = 0;
        $row->shipping_methods = '';
        $row->pgtax_class_id = '';
        $row->visibility = 1;
        $row->description = html_entity_decode($vm_item->product_s_desc);
        $row->content = html_entity_decode($vm_item->product_desc);
        $row->unit_of_measure = '';
        $row->height = $vm_item->product_height;
        $row->width = $vm_item->product_width;
        $row->length = $vm_item->product_length;
        $row->weight = $vm_item->product_weight;
        $row->access = 1;
		if(isset($price -> product_override_price))
        $row->discount_amount = $price->product_override_price;
        $row->discount_type = 0;
        $row->disc_start_date = '';
        $row->disc_end_date = '';
        $row->apply_discount = '';

        $row->created = date( 'Y-m-d H:i:s', time() );
        // set alias
        $row->alias = $vm_item->product_name;
        if($row->alias == '')
        {
            $row->alias = $row->name;
        }
        $row->alias = JFilterOutput::stringURLSafe( $row->alias );
        if ( trim( str_replace( '-', '', $row->alias ) ) == '' ) {
            $datenow = JFactory::getDate();
            $row->alias = $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
        }
        // Insert record
        $ret = $db->insertObject('#__pago_items', $row, 'id');

      
        $itemId = $row->id;

        // added data
        // Insert record
        $query = "INSERT IGNORE INTO #__pago_items_data "
            . "(`item_id`, `unit_of_measure`, `length`, `width`, `height`, `weight`) "
            . "VALUES ('" . $itemId . "', '', '" . $vm_item->product_length . "', '" . $vm_item->product_width . "', '" . $vm_item->product_height . "', '" . $vm_item->product_weight . "')";

        $db->setQuery($query);
        $db->Query();
        // add item in category Item table
        $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $itemId . "')";
        $db->setQuery($query_par_cat);
        $db->Query();
		
		 // Add item meta data
        $data['meta'] = array();
        $data['meta']['type'] = 'item';
        $data['meta']['title'] = html_entity_decode(htmlspecialchars($vm_item->product_name));
        $data['meta']['html_title'] = html_entity_decode(htmlspecialchars($vm_item->product_name));
        $data['meta']['author'] = $vm_item->metaauthor;
        $data['meta']['robots'] = $vm_item->metarobot;
        $data['meta']['keywords'] = $vm_item->metakey;
        $data['meta']['description'] = $vm_item->metadesc;

        $meta = Pago::get_instance( 'meta' );

        foreach ( $data['meta'] as $key => $value ) {
            $meta->update( 'item', $itemId, $key, $value );
        }

		
		$this->insertItemImages($vm_item, $itemId);
		$this->insertItemAttributes($vm_item, $itemId);
		  
        return $itemId;
    }
	
	public function getProductPrice($prd_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT product_price,override,product_override_price,product_tax_id,product_discount_id FROM #__virtuemart_product_prices WHERE virtuemart_product_id=" . $prd_id;
		$db -> setQuery($sql);
		return $db -> loadObject();
	}
	
	public function insertItemImages($vm_item , $pago_item_id)
    {
        $db = JFactory::getDBO();
        $qu = "SELECT primary_category FROM #__pago_items WHERE `id`='" . $pago_item_id . "' ";
        $db->setQuery($qu);
        $itemCatId = $db->loadResult();
        jimport('joomla.filesystem.file');
        Pago::load_helpers('imagehandler');
        $dispatcher = KDispatcher::getInstance();
        $params = Pago::get_instance('config')->get();

        // redshop main image
        $pago_images = array();
       // $pago_images[] = $vm_item->product_full_image;

        // get redshop additional media
        $qu = "SELECT m.file_title,m.file_url FROM #__virtuemart_medias AS m LEFT JOIN #__virtuemart_product_medias AS pm ON m.virtuemart_media_id=pm.virtuemart_media_id WHERE pm.virtuemart_product_id='" . $pago_item_id . "' AND m.file_type='product' ORDER BY pm.ordering";
        $db->setQuery($qu);
        $additional_medias = $db->loadObjectList();
        foreach($additional_medias as $additional_media)
        {
			$img = explode('product/', $additional_media->file_url);
            $pago_images[] = $img[1];
        }


        $img_count = count($pago_images);
        if ($img_count > 0)
        {
            foreach($pago_images as $pago_image)
            {
                $title =  pathinfo($pago_image,PATHINFO_FILENAME);
                $content = $title;
                $src_folder = JPATH_SITE . "/images/stories/virtuemart/product/" . $pago_image;

                if(is_file($src_folder))
                {
                    if (!is_dir(JPATH_SITE . "/media/pago/items/" . $itemCatId))
                    {
                        mkdir(JPATH_SITE . "/media/pago/items/" . $itemCatId, 0755);
                    }
                    $uploads = PagoImageHandlerHelper::upload_dir( "/media/pago/items/" . $itemCatId );
                    $name_prefix = 'item-' .$pago_item_id. '-';
                    $filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix.$pago_image );
                    $dest_folder = JPATH_SITE . "/media/pago/items/" . $itemCatId . "/" . $filename;
                    $files[] = $filename;

                    copy($src_folder, $dest_folder);
                }

                // Files For Loop start
                for ($s = 0; $s < count($files); $s++)
                {
                    // Check for matching Image
                    if ($files[$s] != "" && $files[$s] == $filename)
                    {
                        $item_id = $pago_item_id;
                        $org_file = JPATH_SITE . "/media/pago/items/" . $itemCatId . "/" . $files[$s];

                        $existing_image = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $pago_item_id . "' AND `title`='".$filename."'";
                        $db->setQuery($existing_image);
                        $existing_imageRs = $db->loadResult();

                        if($existing_imageRs == 0)
                        {
                            $qu_img = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $pago_item_id . "' AND `default`=1";
                            $db->setQuery($qu_img);
                            $dflImg = $db->loadResult();
                            $default = 0;

                            if ($dflImg == 0)
                            {
                                $default = 1;
                            }
                            $mimes = false;
                            $mime_check = true;
                            $filetype = PagoImageHandlerHelper::check_filetype( $files[$s], $mimes, $mime_check );
                            extract( $filetype );

                            $url = JURI::root()."/media/pago/items/" . $itemCatId . "/" . $files[$s];
                            $file = array( 'file' => $org_file, 'file_name' => $files[$s], 'url' => $url,'type' => $type );

                            $url       = $file['url'];
                            $type      = $file['type'];
                            $file_name = $file['file_name'];
                            $file      = $file['file'];
                            $title     = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
                            //$content   = '';
                            $file_meta = array();

                            if ( $file_meta = @PagoImageHandlerHelper::read_image_metadata( $file ) ) {
                                if ( trim( $file_meta['title'] ) ) {
                                    $title = $file_meta['title'];
                                }
                                if ( trim( $file_meta['caption'] ) ) {
                                    $content = $file_meta['caption'];
                                }
                            }

                            $data = array(
                                'title'     => $title,
                                'caption'   => $content,
                                'alias'     => $title,
                                'item_id'   => $pago_item_id,
                                'default'   => $default,
                                'published' => 1,
                                'file_name' => $file_name,
                                'type'      => 'images',
                                'mime_type' => $type,
                                'file_meta' => array( 'file_meta' => $file_meta ),
                                'created_time' => date( 'Y-m-d H:i:s', time() ),
                                'modified_time' => date( 'Y-m-d H:i:s', time() )
                            );

                            PagoImageHandlerHelper::generate_image_metadata( $file, $data, $params );

                            // Serialize meta data for storage
                            $data['file_meta'] = serialize( $data['file_meta'] );
                            $dispatcher->trigger('files_upload_before_store', array($data));
                            $row = JTable::getInstance( 'files', 'Table' );
                            if (!$row->bind($data))
                            {
                                $this->setError($this->_db->getErrorMsg());
                                return false;
                            }

                            PagoHelper::saveContentPrep($row);

                            // Quick and ugly fix
                            if ($row->introtext && !$row->fulltext )
                            {
                                $row->fulltext = $row->introtext;
                            }
                            elseif ( $row->introtext && $row->fulltext )                             {
                                $row->fulltext = $row->introtext . ' ' . $row->fulltext;
                            }

                            unset( $row->introtext );

                            if (!$row->id)
                            {
                                $row->ordering = $row->getNextOrder("`item_id` = {$row->item_id} AND `type` = '$row->type'");
                            }

                            if ( !$row->store() )
                            {
                                $this->setError($row->getError());
                                return false;
                            }
                        }
                    }
                // Check for matching Image loop end
                }
            }
        }
    }
	
	function insertItemAttributes($vm_item , $pago_item_id)
    {
        // Select redshop ttributes
		$db = JFactory::getDBO();
      	$select_vm_attrs = "SELECT DISTINCT(pc.virtuemart_custom_id),c.custom_title,c.published,c.is_list FROM #__virtuemart_product_customfields AS pc LEFT JOIN #__virtuemart_customs AS c ON c.virtuemart_custom_id=pc.virtuemart_custom_id WHERE pc.virtuemart_product_id='" . $vm_item->virtuemart_product_id . "' AND c.is_cart_attribute='1' ";
        $db->setQuery($select_vm_attrs);
        $vm_attributes = $db->loadObjectList();
		
        foreach($vm_attributes as $vm_attrib)
        {

            $row = JTable::getInstance( 'attribute', 'Table' );
            

            $data = array(
                    'name'     => $vm_attrib->custom_title,
                    'type'   => 3,
                    'alias'     => $vm_attrib->custom_title,
                    'visible'   => $vm_attrib->published,
                    'preselected' => 1,
                    'for_item' => $pago_item_id,
                    'attr_enable'      => 1,
                    'showfront' => 1,
                    'display_type' => $vm_attrib->is_list,
                );

            if (!$row->bind($data))
            {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }
			
            if ( !$row->store() )
            {
				return  $row->getError();
            }
            $attribute_id = $row->id;

            // Insert attribute values
            // Select redshop ttributes values
            $select_redshop_attr_vals = "SELECT * FROM #__virtuemart_product_customfields WHERE `virtuemart_custom_id`='" . $vm_attrib->virtuemart_custom_id . "' ";
            $db->setQuery($select_redshop_attr_vals);
            $vm_attributes_vals = $db->loadObjectList();
			
            foreach($vm_attributes_vals as $attr_val)
            {
                    $attrVal = JTable::getInstance( 'Attribute_Options', 'Table' );
                    $data = array(
                        'attr_id'     => $attribute_id,
                        'name'     => $attr_val->custom_value,
                        'type'   => $row->type,
                        'price_sign'   => '1',
                        'price_type'   => 0,
                        'price_sum' => $attr_val->custom_price,
                        'for_item' => $pago_item_id,
                        'in_stock'      => 10,
                        'opt_enable' => 1,
                        'published' => $attr_val->published,
                    );
                    if (!$attrVal->bind($data))
                    {
                        $this->setError($this->_db->getErrorMsg());
                        return false;
                    }
                    if ( !$attrVal->store() )
                    {
                        $this->setError($attrVal->getError());
                        return false;
                    }
            }
        }
    }
}
