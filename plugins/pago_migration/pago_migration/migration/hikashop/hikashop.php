<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// Protect from unauthorized access
defined('_JEXEC') or die();
class PagoMigrationHikashop
{
    public function migrateUsers()
    {
        $db = JFactory::getDBO();
        $sel_hikashop_users = "SELECT * FROM #__hikashop_user as u LEFT JOIN #__users as ju ON u.user_cms_id = ju.id";
        $db->setQuery($sel_hikashop_users);
        $hikashop_users = $db->loadObjectList();
        $total_users = 0;
        foreach($hikashop_users as $hika_user)
        {
            // get User address Type
            $BillingAdded = true;
            $address_type = 'b';
            $address_type_name = 'Billing';

            $hikaName = explode(" " ,$hika_user->name);

            $row = JTable::getInstance( 'userinfo', 'Table' );
            $row->load();
            $row->user_id = $hika_user->user_cms_id;
            $row->address_type = $address_type;
            $row->address_type_name = $address_type_name;
            $row->company = '';
            $row->title = '';
            $row->last_name = $hikaName[1];
            $row->first_name = $hikaName[0];
            $row->middle_name = '';
            $row->phone_1 = '';
            $row->phone_2 = '';
            $row->fax = '';
            $row->address_1 = '';
            $row->address_2 = '';
            $row->city = '';
            $row->state = '';
            $row->country = '';
            $row->zip = '';
            $row->user_email = $hika_user->user_email;
            $row->cdate = '';
            $row->mdate = '';
            $row->perms = '';
            $row->store();

            $srow = JTable::getInstance( 'userinfo', 'Table' );

            $address_type = 's';
            $address_type_name = 'Shipping';
            $srow->load();
            $srow->user_id = $hika_user->user_cms_id;
            $srow->address_type = $address_type;
            $srow->address_type_name = $address_type_name;
            $srow->company = '';
            $srow->title = '';
            $srow->last_name = $hikaName[1];
            $srow->first_name = $hikaName[0];
            $srow->middle_name = '';
            $srow->phone_1 = '';
            $srow->phone_2 = '';
            $srow->fax = '';
            $srow->address_1 = '';
            $srow->address_2 = '';
            $srow->city = '';
            $srow->state = '';
            $srow->country = '';
            $srow->zip = '';
            $srow->user_email = $hika_user->user_email;
            $srow->cdate = '';
            $srow->mdate = '';
            $srow->perms = '';
            $srow->store();

            $total_users++;
        }

        return $total_users;
    }

    public function migrateOrders()
    {
        //  migrate orders
        $db = JFactory::getDBO();
        $sel_hikashop_orders = "SELECT * FROM #__hikashop_order as o";
        $db->setQuery($sel_hikashop_orders);
        $hikashop_orders = $db->loadObjectList();
        $total_orders = 0;
		
        foreach($hikashop_orders as $hikashop_order)
        {
            $hikashop_order_id = $hikashop_order->order_id;
            // Insert Pago Orders
            $pago_order_id = $this->insertPagoOrders($hikashop_order);
            $total_orders++;
            $this->insertPagoOrderAddress($hikashop_order, $pago_order_id);
            // End
            // Get Order Items
            $order_items = $this->getOrderItemDetail($hikashop_order_id);
            foreach($order_items as $order_item)
            {
                $this->insertPagoOrderItem($order_item, $pago_order_id);
            }
        }
        return $total_orders;
    }

    public function insertPagoOrderAddress($hikashop_order, $pago_order_id)
    {
        $db = JFactory::getDBO();

        // Get billing address
        $hikashop_order_billing_address_id = $hikashop_order->order_billing_address_id;
        $sel_billing_address = "SELECT * FROM #__hikashop_address WHERE address_id=".$hikashop_order_billing_address_id;
        $db->setQuery($sel_billing_address);
        $billing_address = $db->loadObjectList();
        foreach($billing_address as $bill_addr)
        {
             // get joomla  user id from hikashop user_id
            $sel_joomla_users = "SELECT user_cms_id FROM #__hikashop_user WHERE user_id = ".$bill_addr->address_user_id;
            $db->setQuery($sel_joomla_users);
            $joomla_user_id = $db->loadResult();

            //Get country and state code
             $sel_country = "SELECT zone_code_3 FROM #__hikashop_zone WHERE zone_namekey = '" . $bill_addr->address_country . "' and zone_type ='country'";
            $db->setQuery($sel_country);
            $country_code = $db->loadResult();

            $sel_state = "SELECT zone_code_2 FROM #__hikashop_zone WHERE zone_namekey = '" . $bill_addr->address_state . "' and zone_type ='state'";
            $db->setQuery($sel_state);
            $state_code = $db->loadResult();

            $address_type = 'b';
            $address_type_name = 'Billing';

            $db->setQuery(
                "INSERT INTO #__pago_orders_addresses (
                    order_id,
                    user_id,
                    company,
                    title,
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
                    address_type_name,
                    cdate,
                    mdate)
                VALUES(
                    '{$pago_order_id}',
                    '{$joomla_user_id}',
                    '{$bill_addr->address_company}',
                    '{$bill_addr->address_title}',
                    '{$bill_addr->address_lastname}',
                    '{$bill_addr->address_firstname}',
                    '{$bill_addr->address_middle_name}',
                    '{$bill_addr->address_telephone}',
                    '{$bill_addr->address_telephone2}',
                    '{$bill_addr->address_stree1}',
                    '{$bill_addr->address_stree2}',
                    '{$bill_addr->address_city}',
                    '{$bill_addr->address_fax}',
                    '{}',
                    '{$country_code}',
                    '{$state_code}',
                    '{$bill_addr->address_post_code}',
                    '{$address_type}',
                    '{$address_type_name}',
                    '',
                    ''
                )" );
            $db->query();
        }

        // Get shipping address
        $hikashop_order_shipping_address_id = $hikashop_order->order_shipping_address_id;
        $sel_shipping_address = "SELECT * FROM #__hikashop_address WHERE address_id=".$hikashop_order_shipping_address_id;
        $db->setQuery($sel_shipping_address);
        $shipping_address = $db->loadObjectList();
        foreach($shipping_address as $ship_addr)
        {
             // get joomla  user id from hikashop user_id
            $sel_joomla_users = "SELECT user_cms_id FROM #__hikashop_user WHERE user_id = ".$ship_addr->address_user_id;
            $db->setQuery($sel_joomla_users);
            $joomla_user_id = $db->loadResult();

            //Get country and state code
            $sel_country = "SELECT zone_code_3 FROM #__hikashop_zone WHERE zone_namekey = '" . $ship_addr->address_country . "' and zone_type ='country'";
            $db->setQuery($sel_country);
            $country_code = $db->loadResult();

            $sel_state = "SELECT zone_code_2 FROM #__hikashop_zone WHERE zone_namekey = '" . $ship_addr->address_state . "' and zone_type ='state'";
            $db->setQuery($sel_state);
            $state_code = $db->loadResult();

            $address_type = 's';
            $address_type_name = 'Shipping';

            $db->setQuery(
                "INSERT INTO #__pago_orders_addresses (
                    order_id,
                    user_id,
                    company,
                    title,
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
                    address_type_name,
                    cdate,
                    mdate)
                VALUES(
                    '{$pago_order_id}',
                    '{$joomla_user_id}',
                    '{$ship_addr->address_company}',
                    '{$ship_addr->address_title}',
                    '{$ship_addr->address_lastname}',
                    '{$ship_addr->address_firstname}',
                    '{$ship_addr->address_middle_name}',
                    '{$ship_addr->address_telephone}',
                    '{$ship_addr->address_telephone2}',
                    '{$ship_addr->address_stree1}',
                    '{$ship_addr->address_stree2}',
                    '{$ship_addr->address_city}',
                    '{$ship_addr->address_fax}',
                    '{}',
                    '{$country_code}',
                    '{$state_code}',
                    '{$ship_addr->address_post_code}',
                    '{$address_type}',
                    '{$address_type_name}',
                    '',
                    ''
                )" );
            $db->query();
        }
    }

    public function getOrderItemDetail($hikashop_order_id)
    {
		$db = JFactory::getDBO();
        $sel_orders_items = "SELECT * FROM #__hikashop_order_product where order_id=".$hikashop_order_id;
        $db->setQuery($sel_orders_items);
        return $hikashop_order_items = $db->loadObjectList();
    }

    public function insertPagoOrderItem($order_item, $pago_order_id)
    {
        $data = array();
        $data['order_id'] = $pago_order_id;
        $data['item_id'] = $order_item->product_id;
        $data['qty'] = $order_item->order_product_quantity;
        $data['price'] = $order_item->order_product_price;
        $data['price_type'] = '';
        $data['attributes'] = '';
        $data['sub_recur'] = $item->sub_recur;
        $data['order_item_shipping'] = $order_item->order_product_shipping_price;
        $data['order_item_ship_method_id'] = $order_item->order_product_shipping_id;

        $oitable = JTable::getInstance( 'orders_items', 'Table' );
        $oitable->bind($data);
        $oitable->check();
        $oitable->store();
    }

    public function insertPagoOrders($hikashop_order)
    {
        $db = JFactory::getDBO();
        $otable = JTable::getInstance( 'orders', 'Table' );

        // get joomla  user id from hikashop user_id
        $sel_joomla_users = "SELECT user_cms_id FROM #__hikashop_user WHERE user_id = '" . $hikashop_order->order_user_id . "'";
        $db->setQuery($sel_joomla_users);
        $joomla_user_id = $db->loadResult();

        // End
        $otable->order_id = $hikashop_order->order_id;
        $otable->user_id = $joomla_user_id;
        $otable->order_number = $hikashop_order->order_number;
        $otable->payment_gateway = $hikashop_order->order_payment_method;
        $otable->order_total = $hikashop_order->order_full_price;
        $otable->order_subtotal = ($hikashop_order->order_full_price -$hikashop_order->order_shipping_price -$hikashop_order->order_shipping_tax) ;
       // $otable->order_tax = $hikashop_order->order_tax;
        $otable->order_shipping = $hikashop_order->order_shipping_price;
        $otable->order_shipping_tax = $hikashop_order->order_shipping_tax;
        $otable->order_discount = $hikashop_order->order_discount_price;
        $otable->cdate = date("Y-m-d H:i:s", $hikashop_order->order_created);
        $otable->mdate = date("Y-m-d H:i:s", $hikashop_order->order_modified);
        $otable->order_status = $hikashop_order->order_status;
        $otable->ip_address = $hikashop_order->order_ip;
      
        $order = $db->insertObject('#__pago_orders', $otable, 'order_id');

        $order_id = $otable->order_id;
        $cardnumber = '';

        // insert order Payment data
        $db->setQuery("
            INSERT INTO #__pago_orders_sub_payments
                SET
                    order_id = {$order_id},
                    item_id = {$order_id},
                    txn_id = '{$hikashop_order->order_invoice_id}',
                    payment = '{$hikashop_order->order_full_price}',
                    status = '{$hikashop_order->order_status}',
                    payment_data = '{$hikashop_order->order_payment_method}',
                    card_number = '',
                    payment_capture_status = '',
                    isfraud = 0,
                    fraud_message = ''
        ");

        $db->query();

        return $order_id;
    }

    public function migrateCategories()
    {
        //  migrate categories
        $db = JFactory::getDBO();
        $sel_hikashop_categories = "SELECT * FROM #__hikashop_category as c  WHERE c.category_type ='product' and  category_parent_id = 1";
        $db->setQuery($sel_hikashop_categories);
        $hikashop_categories = $db->loadObjectList();

        $total_categories = 0;
        $total_items = 0;

        foreach($hikashop_categories as $hika_category)
        {
            $parent_category_id = $hika_category->category_parent_id;
            $category_id = $hika_category->category_id;
            // Insert main category
            $insertedCategoryId = $this->insertPagoCategory($hika_category, $parent_category_id);
            $total_categories++;
            $migrated_total_items = $this->migrateItems($category_id,$insertedCategoryId);
            $total_items = $total_items + $migrated_total_items;

            $sel_hikashop_sub_categories = "SELECT * FROM #__hikashop_category where category_parent_id!= 0 and category_parent_id =".$category_id;
            $db->setQuery($sel_hikashop_sub_categories);
            $hikashop_sub_categories = $db->loadObjectList();

            foreach($hikashop_sub_categories as $sub_category)
            {
                $subcategory_id = $sub_category->category_id;
                $insertedCategoryId =  $this->insertPagoCategory($sub_category, $insertedCategoryId);
                $total_categories++;
                $sub_migrated_total_items = $this->migrateItems($subcategory_id,$insertedCategoryId);
                $total_items = $total_items + $sub_migrated_total_items;
            }
        }
        return $total_categories."_".$total_items;
    }


    public function insertPagoCategory($hika_category, $parent_category_id)
    {
        $db = JFactory::getDBO();
        $table = JTable::getInstance( 'categoriesi', 'Table' );
        $catdata = array();
        $category_id = $hika_category->category_id;
        if($category_id)
        {
            $timestamp = time() + 86400;

            // Insert Category
            $catdata['id'] = '';
            $timestamp = time() + 86400;
            $table->setLocation( $parent_category_id, 'last-child');
            $catdata['created_time'] = date( 'Y-m-d H:i:s', time() );

            $catdata['alias'] = $hika_category->category_name;
            $catdata['expiry_date'] = $timestamp;
            $catdata['parent_id'] = $parent_category_id;
            $catdata['name'] = $hika_category->category_name;
            $catdata['visibility'] = 1;
            $catdata['published'] = $hika_category->category_published;
            $catdata['description'] = '';
            $catdata['item_count'] = 0;
            $catdata['created_user_id'] = 0;
            $catdata['modified_user_id'] = 0;
            $catdata['modified_time'] = date( 'Y-m-d H:i:s', time() );
            $catdata['featured'] = 0;
            $catdata['category_settings_image_settings'] = '{"padding_left":"0","border_left":"1","margin_left":"0","margin_top":"0","border_top":"1","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"1","margin_right":"30","border_bottom":"1","margin_bottom":"20"}';
            $catdata['category_settings_product_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';
            $catdata['product_view_settings_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"3","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';

            $table->bind( $catdata );
            $table->check();
            $table->store();
            $table->rebuildPath( $table->id );
            $table->rebuild( $table->id, $table->lft, $table->level, $table->path );
            // End Insert Category

            // Add category meta data
            $data['meta'] = array();
            $data['meta']['type'] = 'category';
            $data['meta']['title'] = $hika_category->category_page_title;
            $data['meta']['html_title'] = $hika_category->category_page_title;
            $data['meta']['author'] = 'category';
            $data['meta']['robots'] = 'category';
            $data['meta']['keywords'] = $hika_category->category_keywords;
            $data['meta']['description'] = $hika_category->category_meta_description;

            $meta = Pago::get_instance( 'meta' );

            foreach ( $data['meta'] as $key => $value ) {
                $meta->update( 'category', $table->id, $key, $value );
            }

            // add category images
            $this->insertCategoryImages($hika_category,$table->id);

            return $table->id;
        }
    }

    public function insertCategoryImages($hika_category , $pago_category_id)
    {
        $db = JFactory::getDBO();
        $itemCatId = $pago_category_id;
        jimport('joomla.filesystem.file');
        Pago::load_helpers('imagehandler');
        $dispatcher = KDispatcher::getInstance();
        $params = Pago::get_instance('config')->get();

         // get hikashop additional media
        $qu = "SELECT * FROM #__hikashop_file WHERE `file_type` = 'category' AND `file_ref_id`='" . $hika_category->category_id . "' order by file_ordering asc";
        $db->setQuery($qu);
        $hikashop_medias = $db->loadObjectList();

        $img_count = count($hikashop_medias);
        if ($img_count > 0)
        {
            foreach($hikashop_medias as $pago_image)
            {
                $title =  $pago_image->file_name;
                $content = $pago_image->file_description;
                $src_folder = JPATH_SITE . "/media/com_hikashop/upload/" . $pago_image->file_path;

                if(is_file($src_folder))
                {
                    if (!is_dir(JPATH_SITE . "/media/pago/category/" . $itemCatId))
                    {
                        mkdir(JPATH_SITE . "/media/pago/category/" . $itemCatId, 0755);
                    }
                    $uploads = PagoImageHandlerHelper::upload_dir( "/media/pago/category/" . $itemCatId );
                    $name_prefix = '';
                    $filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix.$pago_image->file_path );
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
                // Image check for loop end
            }
            // Check for image count loop end
        } 

    }

    public function migrateItems($hikashop_category_id,$pago_category_id)
    {
        $db = JFactory::getDBO();
        $sel_hikashop_items = "SELECT * FROM #__hikashop_product as p LEFT JOIN #__hikashop_product_category as px ON p.product_id = px.product_id WHERE px.category_id = ".$hikashop_category_id;
        $db->setQuery($sel_hikashop_items);
        $hikashop_items = $db->loadObjectList();

        $migrated_total_items = 0;
        foreach($hikashop_items as $hika_item)
        {
            $item_exists = "SELECT * FROM #__pago_items WHERE id = ".$hika_item->product_id;
            $db->setQuery($item_exists);
            $exists_item_id = $db->loadResult();

            if(!$exists_item_id)
            {
                $insertedItemId = $this->insertPagoItem($hika_item, $pago_category_id);
                $migrated_total_items++;
            }
            else
            {
                // add item in category Item table
                // get Pago item id
                $itemId = $hika_item->product_id;
                $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $itemId . "')";
                $db->setQuery($query_par_cat);
                $db->Query();
            }

        }

        return $migrated_total_items;

    }

    public function insertPagoItem($hika_item, $pago_category_id)
    {
        $db = JFactory::getDBO();
        // Get Hikashop profuct price
        $item_exists = "SELECT * FROM #__hikashop_price WHERE price_product_id = '".$hika_item->product_id."' order by price_min_quantity asc";
        $db->setQuery($item_exists);
        $product_price = $db->loadObjectList();

        $row = JTable::getInstance( 'items', 'Table' );
        $row->id = $hika_item->product_id;
        $row->sku = $hika_item->product_code;
        $hika_item->product_name = utf8_encode($hika_item->product_name);
        $hika_item->product_description = utf8_encode($hika_item->product_description);
        $row->name = html_entity_decode(htmlspecialchars($hika_item->product_name));
        $row->qty = $hika_item->product_quantity;
        $row->type = $hika_item->product_type;
        $row->published = $hika_item->product_published;
        $row->price = $product_price[0]->price_value;
        $row->tax_exempt = 0;
        $row->primary_category = $pago_category_id;
        $row->free_shipping = 0;
        $row->shipping_methods = '';
        $row->pgtax_class_id = '';
        $row->visibility = $hika_item->product_published;
        $row->description = '';
        $row->content = html_entity_decode($hika_item->product_description);
        $row->unit_of_measure = '';
        $row->height = $hika_item->product_height;
        $row->width = $hika_item->product_width;
        $row->length = $hika_item->product_length;
        $row->weight = $hika_item->product_weight;
        $row->access = 1;
        $row->discount_amount = 0;
        $row->discount_type = 0;
        $row->disc_start_date = '';
        $row->disc_end_date = '';
        $row->apply_discount = 0;

        $row->created = date( 'Y-m-d H:i:s', time() );
        // set alias
        $row->alias = $hika_item->product_alias;
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
            . "VALUES ('" . $itemId . "', '', '" . $hika_item->product_length . "', '" . $hika_item->product_width . "', '" . $hika_item->product_height . "', '" . $hika_item->product_weight . "')";

        $db->setQuery($query);
        $db->Query();
        // add item in category Item table
        $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $itemId . "')";
        $db->setQuery($query_par_cat);
        $db->Query();

        // Add item meta data
        $data['meta'] = array();
        $data['meta']['type'] = 'item';
        $data['meta']['title'] = $hika_item->product_page_title;
        $data['meta']['html_title'] = $hika_item->product_page_title;
        $data['meta']['author'] = 'item';
        $data['meta']['robots'] = 'item';
        $data['meta']['keywords'] = $hika_item->product_keywords;
        $data['meta']['description'] = $hika_item->product_meta_description;

        $meta = Pago::get_instance( 'meta' );

        foreach ( $data['meta'] as $key => $value ) {
            $meta->update( 'item', $itemId, $key, $value );
        }


        // Insert Images
        $this->insertItemImages($hika_item , $itemId);
        //$this->insertItemAttributes($hika_item , $itemId);
        return $itemId;
    }

    function insertItemImages($hika_item , $pago_item_id)
    {
        $db = JFactory::getDBO();
        $qu = "SELECT primary_category FROM #__pago_items WHERE `id`='" . $pago_item_id . "' ";
        $db->setQuery($qu);
        $itemCatId = $db->loadResult();

        jimport('joomla.filesystem.file');
        Pago::load_helpers('imagehandler');
        $dispatcher = KDispatcher::getInstance();
        $params = Pago::get_instance('config')->get();

        // get hikashop additional media
        $qu = "SELECT * FROM #__hikashop_file WHERE `file_type` = 'product' AND `file_ref_id`='" . $hika_item->product_id . "' order by file_ordering asc";
        $db->setQuery($qu);
        $hikashop_medias = $db->loadObjectList();

        $img_count = count($hikashop_medias);
        if ($img_count > 0)
        {
            foreach($hikashop_medias as $pago_image)
            {
                $title =  $pago_image->file_name;
                $content = $pago_image->file_description;
                $src_folder = JPATH_SITE . "/media/com_hikashop/upload/" . $pago_image->file_path;

                if(is_file($src_folder))
                {
                    if (!is_dir(JPATH_SITE . "/media/pago/items/" . $itemCatId))
                    {
                        mkdir(JPATH_SITE . "/media/pago/items/" . $itemCatId, 0755);
                    }
                    $uploads = PagoImageHandlerHelper::upload_dir( "/media/pago/items/" . $itemCatId );
                    $name_prefix = 'item-' .$pago_item_id. '-';
                    $filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix.$pago_image->file_path );
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
