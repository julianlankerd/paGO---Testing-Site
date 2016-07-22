<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// Protect from unauthorized access
defined('_JEXEC') or die();
class PagoMigrationRedshop
{
    public function migrateUsers()
    {
        $db = JFactory::getDBO();
        $sel_redshop_users = "SELECT * FROM #__redshop_users_info as u group by user_id";
        $db->setQuery($sel_redshop_users);
        $redshop_users = $db->loadObjectList();
        $total_users = 0;
        foreach($redshop_users as $red_user)
        {
            // get single uer
            $sel_redshop_single_user = "SELECT * FROM #__redshop_users_info WHERE user_id =".$red_user->user_id;
            $db->setQuery($sel_redshop_single_user);
            $redshop_single_user = $db->loadObjectList();

            $BillingAdded = false;
            foreach($redshop_single_user as $red_single_user)
            {
                // get User address Type
                if($red_single_user->address_type == 'BT' && !$BillingAdded)
                {
                    $BillingAdded = true;
                    $address_type = 'b';
                    $address_type_name = 'Billing';
                }
                else
                {
                    $address_type = 's';
                    $address_type_name = 'Shipping';
                }
                $row = JTable::getInstance( 'userinfo', 'Table' );
                $row->load();
                $row->user_id = $red_single_user->user_id;
                $row->address_type = $address_type;
                $row->address_type_name = $address_type_name;
                $row->company = $red_single_user->company_name;
                $row->title = '';
                $row->last_name = $red_single_user->lastname;
                $row->first_name = $red_single_user->firstname;
                $row->middle_name = '';
                $row->phone_1 = $red_single_user->phone;
                $row->phone_2 = '';
                $row->fax = '';
                $row->address_1 = $red_single_user->address;
                $row->address_2 = '';
                $row->city = $red_single_user->city;
                $row->state = $red_single_user->state_code;
                $row->country = $red_single_user->country_code;
                $row->zip = $red_single_user->zipcode;
                $row->user_email = $red_single_user->user_email;
                $row->cdate = '';
                $row->mdate = '';
                $row->perms = '';
                $row->store();

                $total_users++;
            }
        }

        return $total_users;
    }

    public function migrateOrders()
    {
        //  migrate orders
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'order.php');
        $db = JFactory::getDBO();
        $sel_redshop_orders = "SELECT * FROM #__redshop_orders as o LEFT JOIN #__redshop_order_payment as op ON o.order_id = op.order_id";
        $db->setQuery($sel_redshop_orders);
        $redshop_orders = $db->loadObjectList();

        $order_functions = new order_functions();
        $total_orders = 0;
        foreach($redshop_orders as $redshop_order)
        {
            $redshop_order_id = $redshop_order->order_id;
            // Insert Pago Orders
            $pago_order_id = $this->insertPagoOrders($redshop_order);
            $total_orders++;
            $this->insertPagoOrderAddress($redshop_order_id, $pago_order_id);
            // End
            // Get Order Items
            $order_items = $order_functions->getOrderItemDetail($redshop_order_id);
            foreach($order_items as $order_item)
            {
                $this->insertPagoOrderItem($order_item, $pago_order_id);
            }
        }
        return $total_orders;
    }

    public function insertPagoOrders($redshop_order)
    {
        $db = JFactory::getDBO();
        $otable = JTable::getInstance( 'orders', 'Table' );
        $redshop_order->payment_method_class = str_replace('rs_payment_', '', $redshop_order->payment_method_class);
        $otable->order_id = $redshop_order->order_id;
        $otable->user_id = $redshop_order->user_id;
        $otable->order_number = $redshop_order->order_number;
        $otable->payment_gateway = $redshop_order->payment_method_class;
        $otable->order_total = $redshop_order->order_total;
        $otable->order_subtotal = $redshop_order->order_subtotal;
        $otable->order_tax = $redshop_order->order_tax;
        $otable->order_shipping = $redshop_order->order_shipping;
        $otable->order_shipping_tax = $redshop_order->order_shipping_tax;
        $otable->coupon_discount = $redshop_order->coupon_discount;
        $otable->order_discount = $redshop_order->order_discount;
        $otable->cdate = date("Y-m-d H:i:s", $redshop_order->cdate);
        $otable->mdate = date("Y-m-d H:i:s", $redshop_order->mdate);
        $otable->order_status = $redshop_order->order_status;
        $otable->customer_note = $redshop_order->customer_note;
        $otable->ip_address = $redshop_order->ip_address;
      
        $order = $db->insertObject('#__pago_orders', $otable, 'order_id');

        $order_id = $otable->order_id;
        $cardnumber = base64_decode($redshop_order->order_payment_number);

        // insert order Payment data
        $db->setQuery("
            INSERT INTO #__pago_orders_sub_payments
                SET
                    order_id = {$order_id},
                    item_id = {$order_id},
                    txn_id = '{$redshop_order->order_payment_trans_id}',
                    payment = '{$redshop_order->order_payment_amount}',
                    status = '{$redshop_order->order_status}',
                    payment_data = '{$redshop_order->payment_method_class}',
                    card_number = '{$cardnumber}',
                    payment_capture_status = '{$redshop_order->authorize_status}',
                    isfraud = 0,
                    fraud_message = ''
        ");

        $db->query();

        return $order_id;
    }

    public function insertPagoOrderAddress($redshop_order_id, $pago_order_id)
    {
        $db = JFactory::getDBO();
        $sel_redshop_order_users = "SELECT * FROM #__redshop_order_users_info as ou WHERE order_id=".$redshop_order_id;
        $db->setQuery($sel_redshop_order_users);
        $redshop_order_users = $db->loadObjectList();
        foreach($redshop_order_users as $redshop_order_user)
        {
             // get User address Type
            if($redshop_order_user->address_type == 'BT')
            {
                $address_type = 'b';
                $address_type_name = 'Billing';
            }
            else
            {
                $address_type = 's';
                $address_type_name = 'Shipping';
            }

            $db->setQuery(
                "INSERT INTO #__pago_orders_addresses (
                    order_id,
                    user_id,
                    company,
                    last_name,
                    first_name,
                    middle_name,
                    phone_1,
                    phone_2,
                    address_1,
                    address_2,
                    city,
                    fax,
                    user_email,
                    country,
                    state,
                    zip,
                    address_type,
                    cdate,
                    mdate)
                VALUES(
                    '{$pago_order_id}',
                    '{$redshop_order_user->user_id}',
                    '{$redshop_order_user->company_name}',
                    '{$redshop_order_user->lastname}',
                    '{$redshop_order_user->firstname}',
                    '',
                    '{$redshop_order_user->phone}',
                    '',
                    '{$redshop_order_user->address}',
                    '',
                    '{$redshop_order_user->city}',
                    '',
                    '{$redshop_order_user->user_email}',
                    '{$redshop_order_user->country_code}',
                    '{$redshop_order_user->state_code}',
                    '{$redshop_order_user->zipcode}',
                    '{$address_type}',
                    '',
                    ''
                )" );
            $db->query();
        }
    }

    public function insertPagoOrderItem($order_item, $pago_order_id)
    {
        $data = array();
        $data['order_id'] = $pago_order_id;
        $data['item_id'] = $order_item->product_id;
        $data['qty'] = $order_item->product_quantity;
        $data['price'] = $order_item->product_item_price;
        $data['price_type'] = '';
        $data['attributes'] = '';
        $data['sub_recur'] = $item->sub_recur;
        $data['order_item_shipping'] = '';
        $data['order_item_ship_method_id'] = '';

        $oitable = JTable::getInstance( 'orders_items', 'Table' );
        $oitable->bind($data);
        $oitable->check();
        $oitable->store();
    }

    public function migrateCategories()
    {
        //  migrate categories
        $db = JFactory::getDBO();
        $sel_redshop_categories = "SELECT * FROM #__redshop_category as c LEFT JOIN #__redshop_category_xref as cx ON c.category_id = cx.category_child_id WHERE cx.category_parent_id = 0";
        $db->setQuery($sel_redshop_categories);
        $redshop_categories = $db->loadObjectList();

        $total_categories = 0;
        $total_items = 0;
        foreach($redshop_categories as $red_category)
        {
            $parent_category_id = 1;
            $category_id = $red_category->category_id;

            // Insert main category
            $insertedCategoryId = $this->insertPagoCategory($red_category, $parent_category_id);
            $total_categories++;
            $main_migrated_total_items = $this->migrateItems($category_id,$insertedCategoryId);
            $total_items = $total_items + $main_migrated_total_items;

            $sel_redshop_sub_categories = "SELECT * FROM #__redshop_category_xref where category_parent_id!= 0 and category_parent_id =".$category_id;
            $db->setQuery($sel_redshop_sub_categories);
            $redshop_sub_categories = $db->loadObjectList();

            foreach($redshop_sub_categories as $sub_category)
            {
                $subcategory_id = $sub_category->category_child_id;

                $sel_sub_redshop_category = "SELECT * FROM #__redshop_category where category_id =".$subcategory_id;
                $db->setQuery($sel_sub_redshop_category);
                $redshop_sub_category = $db->loadObjectList();

                $insertedCategoryId =  $this->insertPagoCategory($redshop_sub_category[0], $insertedCategoryId);
                $total_categories++;
                $sub_migrated_total_items = $this->migrateItems($subcategory_id,$insertedCategoryId);
                $total_items = $total_items + $sub_migrated_total_items;
            }
        }

        return $total_categories."_".$total_items;
    }


    public function insertPagoCategory($red_category, $parent_category_id)
    {

        $db = JFactory::getDBO();
        $table = JTable::getInstance( 'categoriesi', 'Table' );
        $catdata = array();
        $category_id = $red_category->category_id;

        if ($category_id)
        {
            $catdata['id'] = '';
            $timestamp = time() + 86400;
            $table->setLocation( $parent_category_id, 'last-child');
            $catdata['created_time'] = date( 'Y-m-d H:i:s', time() );
        

            $catdata['alias'] = $red_category->category_name;
            $catdata['expiry_date'] = $timestamp;
            $catdata['parent_id'] = $parent_category_id;
            $catdata['name'] = $red_category->category_name;
            $catdata['visibility'] = 1;
            $catdata['published'] = $red_category->published;
            $catdata['description'] = $red_category->category_description;
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

            $this->insertCategoryImages($red_category,$table->id);
            return $table->id; 

        }
    }

    public function insertCategoryImages($redshop_category , $pago_category_id)
    {
        $db = JFactory::getDBO();
        $itemCatId = $pago_category_id;
        jimport('joomla.filesystem.file');
        Pago::load_helpers('imagehandler');
        $dispatcher = KDispatcher::getInstance();
        $params = Pago::get_instance('config')->get();

        $category_images = array();
        $category_images[] = $redshop_category->category_full_image;

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
                $content = $title;
                $src_folder = JPATH_SITE . "/components/com_redshop/assets/images/category/" . $category_image;

                if(is_file($src_folder))
                {
                    if (!is_dir(JPATH_SITE . "/media/pago/category/" . $itemCatId))
                    {
                        mkdir(JPATH_SITE . "/media/pago/category/" . $itemCatId, 0755);
                    }
                    $uploads = PagoImageHandlerHelper::upload_dir( "/media/pago/category/" . $itemCatId );
                    $name_prefix = '';
                    $filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix.$pago_image );
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
        $sel_redshop_items = "SELECT * FROM #__redshop_product as p LEFT JOIN #__redshop_product_category_xref as px ON p.product_id = px.product_id WHERE px.category_id = ".$redshop_category_id;
        $db->setQuery($sel_redshop_items);
        $redshop_items = $db->loadObjectList();

        $added_redshop_items = array();
       
        $migrated_total_items = 0;
        foreach($redshop_items as $red_item)
        {
            $item_exists = "SELECT * FROM #__pago_items WHERE id = ".$red_item->product_id;
            $db->setQuery($item_exists);
            $exists_item_id = $db->loadResult();

            if(!$exists_item_id)
            {
                // get redshop item stock
                $q_stock = "SELECT SUM(quantity) as total_stock FROM `#__redshop_product_stockroom_xref` WHERE product_id = '" . $product_id . "'";
                $db->setQuery($q_stock);
                $stocks = $db->loadResult();

                $red_item->quantity = $stocks;
                
                $insertedItemId = $this->insertPagoItem($red_item, $pago_category_id);
                $migrated_total_items++;
                $added_redshop_items[$red_item->product_id] = $insertedItemId;

            }
            else
            {
                // add item in category Item table
                // get Pago item id
                $itemId = $added_redshop_pago_items[$red_item->product_id];
                $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $itemId . "')";
                $db->setQuery($query_par_cat);
                $db->Query();
            }

        }

        return $migrated_total_items;

    }

    public function insertPagoItem($red_item, $pago_category_id)
    {
        $db = JFactory::getDBO();
        $row = JTable::getInstance( 'items', 'Table' );
        $row->id = $red_item->product_id;
        $row->sku = $red_item->product_number;
        $red_item->product_name = utf8_encode($red_item->product_name);
        $red_item->product_s_desc = utf8_encode($red_item->product_s_desc);
        $red_item->product_desc = utf8_encode($red_item->product_desc);
        $row->name = html_entity_decode(htmlspecialchars($red_item->product_name));
        $row->qty = $red_item->quantity;
        $row->type = '';
        $row->published = $red_item->published;
        $row->price = $red_item->product_price;
        $row->tax_exempt = 0;
        $row->primary_category = $pago_category_id;
        $row->free_shipping = 0;
        $row->shipping_methods = '';
        $row->pgtax_class_id = '';
        $row->visibility = 1;
        $row->description = html_entity_decode($red_item->product_s_desc);
        $row->content = html_entity_decode($red_item->product_desc);
        $row->unit_of_measure = '';
        $row->height = $red_item->product_height;
        $row->width = $red_item->product_width;
        $row->length = $red_item->product_length;
        $row->weight = $red_item->weight;
        $row->access = 1;
        $row->discount_amount = ( $red_item->product_price - $red_item->discount_price );
        $row->discount_type = 0;
        $row->disc_start_date = $red_item->discount_stratdate;
        $row->disc_end_date = $red_item->discount_enddate;
        $row->apply_discount = $red_item->product_on_sale;

        $row->created = date( 'Y-m-d H:i:s', time() );
        // set alias
        $row->alias = $red_item->product_name;
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
            . "VALUES ('" . $itemId . "', '', '" . $red_item->product_length . "', '" . $red_item->product_width . "', '" . $red_item->product_height . "', '" . $red_item->product_weight . "')";

        $db->setQuery($query);
        $db->Query();
        // add item in category Item table
        $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $itemId . "')";
        $db->setQuery($query_par_cat);
        $db->Query();

        // Insert Images
        $this->insertItemImages($red_item , $itemId);
        // Insert Attributes
        $this->insertItemAttributes($red_item , $itemId);

        return $itemId;
    }

    function insertItemAttributes($red_item , $pago_item_id)
    {
        // Select redshop ttributes
        $db = JFactory::getDBO();
        $select_redshop_attrs = "SELECT * FROM #__redshop_product_attribute WHERE `product_id`='" . $pago_item_id . "' ";
        $db->setQuery($select_redshop_attrs);
        $redshop_attributes = $db->loadObjectList();
        if(count($redshop_attributes) > 0)
        {
            foreach($redshop_attributes as $redshop_attribute)
            {

                $row = JTable::getInstance( 'attribute', 'Table' );
                if($redshop_attribute->display_type == 'dropdown')
                {
                    $display_type = 0;
                }else if($redshop_attribute->display_type == 'radio')
                {
                    $display_type = 2;
                }

                $data = array(
                        'name'     => $redshop_attribute->attribute_name,
                        'type'   => 3,
                        'alias'     => $redshop_attribute->attribute_name,
                        'required'   => $redshop_attribute->attribute_required,
                        'visible'   => $redshop_attribute->attribute_published,
                        'preselected' => 1,
                        'for_item' => $pago_item_id,
                        'attr_enable'      => 1,
                        'showfront' => 1,
                        'display_type' => $display_type,
                    );

                if (!$row->bind($data))
                {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
                if ( !$row->store() )
                {
                    $this->setError($row->getError());
                    return false;
                }
                $attribute_id = $redshop_attribute->attribute_id;
                $pago_attribute_id = $row->id;

                // Insert attribute values
                // Select redshop attributes values
                $select_redshop_attr_vals = "SELECT * FROM #__redshop_product_attribute_property WHERE `attribute_id`='" . $attribute_id . "' ";
                $db->setQuery($select_redshop_attr_vals);
                $redshop_attributes_vals = $db->loadObjectList();
                if(count($redshop_attributes_vals) > 0 )
                {
                    foreach($redshop_attributes_vals as $attr_val)
                    {

                        // get item sttribute stock from redshop
                        $q_attr_stock = "SELECT SUM(quantity) as total_stock FROM `#__redshop_product_attribute_stockroom_xref` WHERE section_id = '" . $attr_val->property_id . "' and section ='property'";
                        $db->setQuery($q_attr_stock);
                        $attr_stocks = $db->loadResult();
                        if($attr_val->oprand == "+")
                        {
                            $price_sign= 1;
                        }
                        else
                        {
                            $price_sign= 0;
                        }
                        $attrVal = JTable::getInstance( 'Attribute_Options', 'Table' );
                        $data = array(
                            'attr_id'     => $pago_attribute_id,
                            'name'     => $attr_val->property_name,
                            'type'   => $row->type,
                            'price_sign'   => $price_sign,
                            'price_type'   => 0,
                            'price_sum' => $attr_val->property_price,
                            'for_item' => $pago_item_id,
                            'in_stock'      => $attr_stocks,
                            'opt_enable' => 1,
                            'published' => $attr_val->property_published,
                        );
                        if (!$attrVal->bind($data))
                        {
                            $this->setError($db->getErrorMsg());
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
        
    }

    function insertItemImages($red_item , $pago_item_id)
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
        $pago_images[] = $red_item->product_full_image;

        // get redshop additional media
        $qu = "SELECT media_name FROM #__redshop_media WHERE `media_section` = 'product' AND `section_id`='" . $pago_item_id . "' ";
        $db->setQuery($qu);
        $additional_medias = $db->loadObjectList();
        foreach($additional_medias as $additional_media)
        {
            $pago_images[] = $additional_media->media_name;
        }


        $img_count = count($pago_images);
        if ($img_count > 0)
        {
            foreach($pago_images as $pago_image)
            {
                $title =  pathinfo($pago_image,PATHINFO_FILENAME);
                $content = $title;
                $src_folder = JPATH_SITE . "/components/com_redshop/assets/images/product/" . $pago_image;

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
}
