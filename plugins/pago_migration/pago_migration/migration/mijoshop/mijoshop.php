<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// Protect from unauthorized access
defined('_JEXEC') or die();
class PagoMigrationMijoshop
{
        public function migrateUsers()
    {
        $db = JFactory::getDBO();
        $sel_mijo_users = "SELECT * FROM #__mijoshop_customer order by customer_id";
        $db->setQuery($sel_mijo_users);
        $mijoshop_users = $db->loadObjectList();
        $total_users = 0;

        foreach ($mijoshop_users as $mijo_users)

        {
            // get single uer
            $sel_mijo_single_user = "SELECT * FROM #__mijoshop_address WHERE customer_id =" . $mijo_users->customer_id;
            $db->setQuery($sel_mijo_single_user);
            $mijoshop_single_user = $db->loadObjectList();
            $BillingAdded = false;

            foreach ($mijoshop_single_user as $mijo_single_user)

            {
                $this->insertAddress('b', 'Billing', $mijo_single_user, $mijo_users);
                $this->insertAddress('s', 'Shipping', $mijo_single_user, $mijo_users);
                $total_users++;
            }
        }

        return $total_users;
    }
    
    public function insertAddress($address_type, $address_type_name, $mijo_single_user, $mijo_users)
    {               
        $row = JTable::getInstance('userinfo', 'Table');
        $row->load();
        $state = $this -> getMijoState($mijo_single_user->zone_id);
        $country = $this -> getMijoCountry($mijo_single_user->country_id);
        $email = $mijo_users->email;
        $row->user_id = $this -> getJoomlaUserId($mijo_single_user->customer_id);
        $row->address_type = $address_type;
        $row->address_type_name = $address_type_name;
        $row->company = addslashes($mijo_single_user->company);
        $row->last_name = $mijo_single_user->lastname;
        $row->first_name = $mijo_single_user->firstname;
        $row->phone_1 = $mijo_users->email;
        $row->fax = $mijo_users->fax;
        $row->address_1 = $mijo_single_user->address_1;
        $row->address_2 = $mijo_single_user->address_2;
        $row->city = $mijo_single_user->city;
        $row->state = $state;
        $row->country = $country;
        $row->zip = $mijo_single_user->postcode;
        $row->user_email = $email;
        $row->cdate = $mijo_users->date_added;
        $row->perms = '';
        $row->store();
    }

    public function getMijoState($stateId)
    {
        $db = JFactory::getDBO();
        $stateQue = "SELECT name FROM #__mijoshop_zone WHERE zone_id=" . $stateId;
        $db -> setQuery($stateQue);

        return $db -> loadResult();
    }
    
    public function getMijoCountry($couId)
    {
        $db = JFactory::getDBO();
        $couQue = "SELECT iso_code_2 FROM #__mijoshop_country WHERE country_id=" . $couId;
        $db -> setQuery($couQue);

        return $db -> loadResult();
    }
    
    public function getJoomlaUserId($custId)
    {
        $db = JFactory::getDBO();
        $cusQue = "SELECT juser_id FROM #__mijoshop_juser_ocustomer_map WHERE ocustomer_id=" . $custId;
        $db -> setQuery($cusQue);

        return $db -> loadResult();
    }
    
    public function migrateOrders()
    {
        
           //  migrate orders
        $db = JFactory::getDBO();
        $sel_mijoshop_orders = "SELECT * FROM #__mijoshop_order";
        $db->setQuery($sel_mijoshop_orders);
        $mijoshop_orders = $db->loadObjectList();
        $total_orders = 0;
        
        foreach($mijoshop_orders as $mijo_order)
        {
            $mijo_order_id = $mijo_order->order_id;
            // Insert Pago Orders
            $pago_order_id = $this->insertPagoOrders($mijo_order);
            $total_orders++;
            $this->insertPagoOrderAddress($mijo_order_id, $pago_order_id, $mijo_order);
            // End
            // Get Order Items
            $order_items = $this->getMijoOrderItemDetail($mijo_order_id);

            foreach ($order_items as $order_item)
            {
                $this->insertPagoOrderItem($order_item, $pago_order_id, $mijo_order);
            }
        }
        return $total_orders;
    
    }
    
    public function getMijoOrderItemDetail($mijo_order_id)
    {
        $db = JFactory::getDBO();
        $sql = "SELECT * FROM #__mijoshop_order_product WHERE order_id=" . $mijo_order_id;
        $db -> setQuery($sql);

        return $db->loadObjectList();
    }

     public function insertPagoOrders($mijo_order)
    {
        $db = JFactory::getDBO();
        $otable = JTable::getInstance( 'orders', 'Table' );
        $otable->order_id = $mijo_order->order_id;
        $otable->user_id = $this->getJoomlaUserId($mijo_order->customer_id);
        $otable->payment_gateway = $mijo_order->payment_method;
        $otable->order_total = $this->getOrderCalcDetail($mijo_order->order_id, "total");
        $otable->order_subtotal = $this->getOrderCalcDetail($mijo_order->order_id, "sub_total");
        $otable->order_tax = $this->getOrderCalcDetail($mijo_order->order_id, "tax");;
        //$otable->coupon_code = $mijo_order->coupon_code;
        $otable->coupon_discount = $this->getOrderCalcDetail($mijo_order->order_id, "coupon");
        $otable->order_shipping = $this->getOrderCalcDetail($mijo_order->order_id, "shipping");
        //$otable->ship_method_id = $mijo_order->virtuemart_shipmentmethod_id;
        //$otable->order_shipping_tax = $mijo_order->order_shipment_tax;
        //$otable->order_discount = $mijo_order->order_discount;
        $otable->order_currency = $mijo_order-> currency_code;
        $otable->cdate = $mijo_order->date_added;
        $otable->mdate = $mijo_order->date_modified;
        $otable->order_status = $this->getMijoOrderStatus($mijo_order->order_status_id);
        $otable->customer_note = $mijo_order->comment;
        $otable->ip_address = $mijo_order->ip;
        $otable->user_email = $mijo_order->email;
      
        $order = $db->insertObject('#__pago_orders', $otable, 'order_id');

        $order_id = $otable->order_id;
       // $cardnumber = base64_decode($mijo_order->order_payment_number);
        $payment_capture_status = '';
        
        if ($otable->order_status == 'Complete')
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
                    payment = '{$otable->order_total}',
                    status = '{$otable->order_status}',
                    payment_data = '{$mijo_order->payment_method}',
                    card_number = '',
                    payment_capture_status = '{$payment_capture_status}',
                    isfraud = 0,
                    fraud_message = ''
        ");

        $db->query();

        return $order_id;
    }
    
    public function getMijoOrderStatus($order_status_id)
    {
        $db = JFactory::getDBO();
        $cusQue = "SELECT name FROM #__mijoshop_order_status WHERE order_status_id='" . $order_status_id . "'";
        $db -> setQuery($cusQue);

        return $db -> loadResult();
    }
    
    
    public function getOrderCalcDetail($order_id, $code)
    {
        $db = JFactory::getDBO();
        $cusQue = "SELECT value FROM #__mijoshop_order_total WHERE order_id='" . $order_id . "' AND code='" . $code . "'" ;
        $db -> setQuery($cusQue);

        return $db -> loadResult();
    }

    public function insertPagoOrderAddress($mijo_order_id, $pago_order_id, $mijo_order)
    {

        $db = JFactory::getDBO();
        //Payment address
        $state = $this -> getMijoState($mijo_order->payment_zone_id);
        $country = $this -> getMijoCountry($mijo_order->payment_country_id);
         $user_id = $this -> getJoomlaUserId($mijo_order->customer_id);
        $ordAdd =  "INSERT INTO #__pago_orders_addresses (order_id, user_id, company, last_name, first_name, phone_1, address_1, address_2, city, fax, user_email, country, state, zip, address_type, cdate, mdate) VALUES(
                '".$pago_order_id."',
                '".$mijo_order->customer_id."',
                '".addslashes($mijo_order->payment_company)."',
                '".addslashes($mijo_order->payment_lastname)."',
                '".addslashes($mijo_order->payment_firstname)."',
                '".$mijo_order->telephone."',
                '".addslashes($mijo_order->payment_address_1)."',
                '".addslashes($mijo_order->payment_address_2)."',
                '".addslashes($mijo_order->payment_city)."',
                 '".$mijo_order->fax."',
                '".$mijo_order->email."',
                '".$country."',
                '".addslashes($state)."',
                '".$mijo_order->payment_postcode."',
                'b',
                '".$mijo_order->date_added."',
                '".$mijo_order->date_modified."'
            )";

        $db->setQuery($ordAdd);
        $db->query();
        
        //shipping address
        $ship_state = $this -> getMijoState($mijo_order->shipping_zone_id);
        $ship_country = $this -> getMijoCountry($mijo_order->shipping_country_id);
        $ordShipAdd =  "INSERT INTO #__pago_orders_addresses (order_id, user_id, company, last_name, first_name, phone_1, address_1, address_2, city, fax, user_email, country, state, zip, address_type, cdate, mdate) VALUES(
                '".$pago_order_id."',
                '".$mijo_order->customer_id."',
                '".addslashes($mijo_order->shipping_company)."',
                '".addslashes($mijo_order->shipping_lastname)."',
                '".addslashes($mijo_order->shipping_firstname)."',
                '".$mijo_order->telephone."',
                '".addslashes($mijo_order->shipping_address_1)."',
                '".addslashes($mijo_order->shipping_address_2)."',
                '".addslashes($mijo_order->shipping_city)."',
                 '".$mijo_order->fax."',
                '".$mijo_order->email."',
                '".$ship_country."',
                '".addslashes($ship_state)."',
                '".$mijo_order->shipping_postcode."',
                'm',
                '".$mijo_order->date_added."',
                '".$mijo_order->date_modified."'
            )";

        $db->setQuery($ordShipAdd);
        $db->query();
       
    
    }

    public function insertPagoOrderItem($order_item, $pago_order_id)
    {
        $data = array();
        $data['order_id'] = $pago_order_id;
        $data['item_id'] = $order_item->product_id;
        $data['qty'] = $order_item->quantity;
        $data['price'] = $order_item->price;
        $data['price_type'] = '';
       // $data['attributes'] =  $order_item->product_attribute;
       // $data['order_item_shipping'] = $order_item->order_shipment;
        //$data['order_item_ship_method_id'] = $order_item->virtuemart_shipmentmethod_id;

        $oitable = JTable::getInstance( 'orders_items', 'Table' );
        $oitable->bind($data);
        $oitable->check();
        $oitable->store();
    }

    public function migrateCategories()
    {
        // migrate categories
        $db = JFactory::getDBO();
        $sel_mijoshop_categories = "SELECT * FROM #__mijoshop_category AS c LEFT JOIN #__mijoshop_category_description AS md ON c.category_id=md.category_id WHERE c.parent_id = 0 order by c.category_id";
        $db->setQuery($sel_mijoshop_categories);
        $mijoshop_categories = $db->loadObjectList();

        $total_categories = 0;
        $total_items = 0;
        foreach($mijoshop_categories as $mijo_category)
        {
            $parent_category_id = 1;
            $category_id = $mijo_category->category_id;

            // Insert main category
            $insertedCategoryId = $this->insertPagoCategory($mijo_category, $parent_category_id);
            $total_categories++;
            $main_migrated_total_items = $this->migrateItems($category_id,$insertedCategoryId);
            $total_items = $total_items + $main_migrated_total_items;

            $sel_mijoshop_sub_categories = "SELECT category_id FROM #__mijoshop_category where parent_id!= 0 and parent_id =".$category_id." order by category_id ";
            $db->setQuery($sel_mijoshop_sub_categories);
            $redshop_sub_categories = $db->loadObjectList();

            foreach($redshop_sub_categories as $sub_category)
            {
                $subcategory_id = $sub_category->category_id;

                $sel_sub_mijoshop_category = "SELECT * FROM #__mijoshop_category AS c LEFT JOIN #__mijoshop_category_description AS md ON c.category_id=md.category_id WHERE  c.category_id ='".$subcategory_id."'";
                $db->setQuery($sel_sub_mijoshop_category);
                $mijoshop_sub_category = $db->loadObjectList();

                $insertedSubCategoryId =  $this->insertPagoCategory($mijoshop_sub_category[0], $insertedCategoryId);
                $total_categories++;
                $sub_migrated_total_items = $this->migrateItems($subcategory_id,$insertedSubCategoryId);
                $total_items = $total_items + $sub_migrated_total_items;
            }
        }

        return $total_categories."_".$total_items;
    }


    public function insertPagoCategory($mijo_category, $parent_category_id)
    {

        $db = JFactory::getDBO();
        $table = JTable::getInstance( 'categoriesi', 'Table' );
        $catdata = array();
        $category_id = $mijo_category->category_id;

        if ($category_id)
        {
            $catdata['id'] = '';
            $timestamp = time() + 86400;
            $table->setLocation( $parent_category_id, 'last-child');
            $catdata['created_time'] = date( 'Y-m-d H:i:s', time() );
        

            $catdata['alias'] = $mijo_category->name;
            $catdata['expiry_date'] = $timestamp;
            $catdata['parent_id'] = $parent_category_id;
            $catdata['name'] = $mijo_category->name;
            $catdata['visibility'] = 1;
            $catdata['published'] = $mijo_category->status;
            $catdata['description'] = $mijo_category->description;
            $catdata['item_count'] = 0;
            $catdata['created_user_id'] = 0;
            $catdata['modified_user_id'] = 0;
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
            $table->rebuild( $table->id, $table->lft, $table->level, $table->path );
            
             // Add category meta data
            $data['meta'] = array();
            $data['meta']['type'] = 'category';
            $data['meta']['title'] = $mijo_category->name;
            $data['meta']['html_title'] = $mijo_category->name;
            $data['meta']['author'] = 'category';
            $data['meta']['robots'] = 'category';
            $data['meta']['keywords'] = $mijo_category->meta_keyword;
            $data['meta']['description'] = $mijo_category->meta_description;
            
             $meta = Pago::get_instance( 'meta' );

            foreach ( $data['meta'] as $key => $value ) {
                $meta->update( 'category', $table->id, $key, $value );
            }

            $this->insertCategoryImages($mijo_category,$table->id);
            return $table->id; 

        }
    }

    public function insertCategoryImages($mijoshop_category , $pago_category_id)
    {
        $db = JFactory::getDBO();
        $itemCatId = $pago_category_id;
        jimport('joomla.filesystem.file');
        Pago::load_helpers('imagehandler');
        $dispatcher = KDispatcher::getInstance();
        $params = Pago::get_instance('config')->get();

        $category_images = array();
        $category_images[] = $mijoshop_category->image;

        // get redshop additional media
      /*  $qu = "SELECT media_name FROM #__redshop_media WHERE `media_section` = 'product' AND `section_id`='" . $pago_item_id . "' ";
        $db->setQuery($qu);
        $additional_medias = $db->loadObjectList();
        foreach($additional_medias as $additional_media)
        {
            $pago_images[] = $additional_media->media_name;
        } */
        $img_count = count($category_images);

        // Check for image count loop start
        if ($img_count > 0)
        {
            // Image check for loop start
            foreach($category_images as $category_image)
            {
                $title =  pathinfo($category_image,PATHINFO_FILENAME);
                $files = array();
                $content = $title;
                $src_folder = JPATH_SITE . "/components/com_mijoshop/opencart/image/" . $category_image;

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
                if(count($files) > 0)
                {
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
    
                                $url = JURI::root()."/media/pago/category/" . $itemCatId . "/" . $files[$s]; 
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
                }
                // Image check for loop end
            }
            // Check for image count loop end
        } 

    }

    public function migrateItems($mijoshop_category_id,$pago_category_id)
    {
        
        $db = JFactory::getDBO();
        $sel_mijoshop_items = "SELECT * FROM #__mijoshop_product_to_category WHERE category_id = ".$mijoshop_category_id;
        $db->setQuery($sel_mijoshop_items);
        $mijoshop_items = $db->loadObjectList();

        $added_mijoshop_items = array();
       
        $migrated_total_items = 0;
        foreach($mijoshop_items as $mijo_item)
        {
            
            $item_exists = "SELECT * FROM #__pago_items WHERE id = ".$mijo_item->product_id;
            $db->setQuery($item_exists);
            $exists_item_id = $db->loadResult();

            if(!$exists_item_id)
            {
                 $q_stock = "SELECT mp.*,md.*,pd.*,pd.price as disc_price,mp.price as prd_price,mp.product_id as prd_id,mp.quantity as prd_quantity FROM #__mijoshop_product AS mp LEFT JOIN #__mijoshop_product_description AS md ON mp.product_id=md.product_id LEFT JOIN #__mijoshop_product_discount AS pd ON mp.product_id=pd.product_id WHERE mp.product_id = '" . $mijo_item->product_id . "'";
                $db->setQuery($q_stock);
                $mijo_item_details = $db->loadObjectList();

                $insertedItemId = $this->insertPagoItem($mijo_item_details[0], $pago_category_id);
                $migrated_total_items++;
                $added_mijoshop_items[$mijo_item->product_id] = $insertedItemId;

            }
            else
            {
                // add item in category Item table
                // get Pago item id
                $itemId = $exists_item_id;
                $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $itemId . "')";
                $db->setQuery($query_par_cat);
                $db->Query();
            }

        }

        return $migrated_total_items;
    }

    public function insertPagoItem($mijo_item, $pago_category_id)
    {
        
        $db = JFactory::getDBO();
        $row = JTable::getInstance( 'items', 'Table' );
        $row->id = $mijo_item->prd_id;
        $row->sku = $mijo_item->model;
        $mijo_item->product_name = utf8_encode($mijo_item->name);
        $mijo_item->description = utf8_encode($mijo_item->description);
        $row->name = html_entity_decode(htmlspecialchars($mijo_item->name));
        $row->qty = $mijo_item->prd_quantity;
        $row->type = '';
        $row->published = $mijo_item->status;
        $row->price = $mijo_item->prd_price;
        $row->tax_exempt = 0;
        $row->primary_category = $pago_category_id;
        $row->free_shipping = 0;
        $row->shipping_methods = '';
        $row->pgtax_class_id = '';
        $row->visibility = 1;
        $row->description = html_entity_decode($mijo_item->description);
        $row->unit_of_measure = '';
        $row->height = $mijo_item->height;
        $row->width = $mijo_item->width;
        $row->length = $mijo_item->length;
        $row->weight = $mijo_item->weight;
        $row->access = 1;
        $row->discount_amount = $mijo_item->disc_price;
        $row->discount_type = 0;
        $row->disc_start_date = $mijo_item->date_start;
        $row->disc_end_date = $mijo_item->date_end;
        
        if($mijo_item->product_discount_id)
        {
            $row->apply_discount = 1;
        }

        $row->created = date( 'Y-m-d H:i:s', time() );
        // set alias
        $row->alias = $mijo_item->name;
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
            . "VALUES ('" . $itemId . "', '', '" . $mijo_item->length . "', '" . $mijo_item->width . "', '" . $mijo_item->height . "', '" . $mijo_item->weight . "')";

        $db->setQuery($query);
        $db->Query();
        // add item in category Item table
        $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $itemId . "')";
        $db->setQuery($query_par_cat);
        $db->Query();
        
         // Add item meta data
        $data['meta'] = array();
        $data['meta']['type'] = 'item';
        $data['meta']['title'] = html_entity_decode(htmlspecialchars($mijo_item->name));
        $data['meta']['html_title'] = html_entity_decode(htmlspecialchars($mijo_item->name));
        $data['meta']['author'] = 'item';
        $data['meta']['robots'] = 'item';
        $data['meta']['keywords'] = $mijo_item->meta_keyword;
        $data['meta']['description'] = $mijo_item->meta_description;

        $meta = Pago::get_instance( 'meta' );

        foreach ( $data['meta'] as $key => $value ) {
            $meta->update( 'item', $itemId, $key, $value );
        }

        // Insert Images
        $this->insertItemImages($mijo_item , $itemId);
        // Insert Attributes
        $this->insertItemAttributes($mijo_item , $itemId);

        return $itemId;
    
    }

    function insertItemAttributes($mijo_item , $pago_item_id)
    {
        
        // Select redshop ttributes
        $db = JFactory::getDBO();
        $select_vm_attrs = "SELECT po.option_id,mo.type,od.name FROM #__mijoshop_product_option AS po LEFT JOIN #__mijoshop_option AS mo ON po.option_id=mo.option_id LEFT JOIN #__mijoshop_option_description AS od ON od.option_id=po.option_id WHERE po.product_id='" . $mijo_item->product_id . "'";
        $db->setQuery($select_vm_attrs);
        $mijo_attributes = $db->loadObjectList();
        
        foreach($mijo_attributes as $mijo_attrib)
        {

            $row = JTable::getInstance( 'attribute', 'Table' );
            

            $data = array(
                    'name'     => $mijo_attrib->name,
                    'type'   => 3,
                    'alias'     => $mijo_attrib->name,
                    'visible'   => 1,
                    'preselected' => 1,
                    'for_item' => $pago_item_id,
                    'attr_enable'      => 1,
                    'showfront' => 1
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
           $select_redshop_attr_vals = "SELECT ov.*,od.* FROM #__mijoshop_product_option_value AS ov LEFT JOIN #__mijoshop_option_value_description AS od ON ov.option_value_id=od.option_value_id  WHERE ov.product_id='" . $mijo_item->product_id . "' AND ov.option_id= '" . $mijo_attrib->option_id . "'";
            $db->setQuery($select_redshop_attr_vals);
            $mijo_attributes_vals = $db->loadObjectList();
            
            foreach($mijo_attributes_vals as $attr_val)
            {
                    $attrVal = JTable::getInstance( 'Attribute_Options', 'Table' );
                    $data = array(
                        'attr_id'     => $attribute_id,
                        'name'     => $attr_val->name,
                        'price_sign'   => '1',
                        'price_type'   => 0,
                        'price_sum' => $attr_val->price,
                        'for_item' => $pago_item_id,
                        'in_stock'      => 10,
                        'opt_enable' => 1,
                        'published' => 1,
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

    function insertItemImages($mijo_item , $pago_item_id)
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
        $qu = "SELECT image FROM #__mijoshop_product WHERE product_id='" . $pago_item_id . "'";
        $db->setQuery($qu);
        $additional_medias = $db->loadObjectList();
        foreach($additional_medias as $additional_media)
        {
            $img = explode('data/', $additional_media->image);
            $pago_images[] = $img[1];
        }

        
        $img_count = count($pago_images);
        if ($img_count > 0)
        {
            foreach($pago_images as $pago_image)
            {
                $title =  pathinfo($pago_image,PATHINFO_FILENAME);
                $content = $title;
                $src_folder = JPATH_SITE . "/components/com_mijoshop/opencart/image/data/" . $pago_image;

               // if(is_file($src_folder))
              //  {
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
              //  }

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
}
