<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// Protect from unauthorized access
defined('_JEXEC') or die();
class PagoMigrationVm15
{
    public function migrateUsers()
    {
        $db = JFactory::getDBO();
        $sel_vm_users = "SELECT * FROM #__vm_user_info group by user_id";
        $db->setQuery($sel_vm_users);
        $virtuemart_users = $db->loadObjectList();
        $total_users = 0;
		
        foreach ($virtuemart_users as $vm_users)
        {
            // get single uer
            $sel_vm_single_user = "SELECT * FROM #__vm_user_info WHERE user_id =" . $vm_users->user_id;
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
                    $address_type = 's';
                    $address_type_name = 'Shipping';
                }
				
                $row = JTable::getInstance('userinfo', 'Table');
                $row->load();
                $row->user_id = $vm_single_user->user_id;
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
                $row->state = $vm_single_user->state;
                $row->country = $vm_single_user->country;
                $row->zip = $vm_single_user->zip;
                $row->user_email = $vm_single_user->user_email;
                $row->cdate =  date("Y-m-d h:m:s", $vm_single_user->cdate);
                $row->mdate = date("Y-m-d h:m:s",$vm_single_user->mdate);
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
        $db = JFactory::getDBO();
        $sel_virtuemart_orders = "SELECT * FROM #__vm_orders as o";
        $db->setQuery($sel_virtuemart_orders);
        $virtuemart_orders = $db->loadObjectList();
        $total_orders = 0;
		
        foreach($virtuemart_orders as $vm_order)
        {
            $vm_order_id = $vm_order->order_id;
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
		$sql = "SELECT oi.* FROM #__vm_order_item AS oi WHERE oi.order_id=" . $vm_order_id;
		$db -> setQuery($sql);

		return $db->loadObjectList();
	}
	
	public function getVmCurrency($order_currency_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT currency-code_3 FROM #__virtuemart_currencies WHERE virtuemart_currency_id=" . $order_currency_id;
		$db -> setQuery($sql);

		return $db->loadObjectList();
	}

    public function insertPagoOrders($vm_order)
    {
        $db = JFactory::getDBO();
        $otable = JTable::getInstance( 'orders', 'Table' );
        $otable->order_id = $vm_order->order_id;
        $otable->user_id = $vm_order->user_id;
		$otable->vendor_id = $vm_order->vendor_id;
        $otable->order_number = $vm_order->order_number;
        $otable->payment_gateway = $this->getPaymentMethod($vm_order->order_id);
        $otable->order_total = $vm_order->order_total;
        $otable->order_subtotal = $vm_order->order_subtotal;
        $otable->order_tax = $vm_order->order_tax;
		$otable->coupon_code = $vm_order->coupon_code;
		$otable->coupon_discount = $vm_order->coupon_discount;
        $otable->order_shipping = $vm_order->order_shipping;
		$otable->ship_method_id = $vm_order->ship_method_id;
        $otable->order_shipping_tax = $vm_order->order_shipping_tax;
        $otable->order_discount = $vm_order->order_discount;
	    $otable->order_currency = $vm_order->order_currency;
        $otable->cdate = date("Y-m-d h:m:s", $vm_order->cdate);
        $otable->mdate = date("Y-m-d h:m:s",$vm_order->mdate);
        $otable->order_status = $vm_order->order_status;
        $otable->customer_note = $vm_order->customer_note;
        $otable->ip_address = $vm_order->ip_address;
		$otable->user_email = $this->getUserEmail($vm_order->user_id);
      
        $order = $db->insertObject('#__pago_orders', $otable, 'order_id');

        $order_id = $otable->order_id;
       // $cardnumber = base64_decode($vm_order->order_payment_number);
		$paymentData = $this -> getPaymentData($vm_order->order_id);
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
                    txn_id = '{$paymentData->order_payment_trans_id}',
                    payment = '{$this->getPaymentMethod($vm_order->order_id)}',
                    status = '{$vm_order->order_status}',
                    payment_data = '{$this->getPaymentMethod($vm_order->order_id)}',
                    card_number = '',
                    payment_capture_status = '{$payment_capture_status}',
                    isfraud = 0,
                    fraud_message = ''
        ");

        $db->query();

        return $order_id;
    }
	
	public function getPaymentData($order_id)
	{
		$db = JFactory::getDBO();
       $email = "SELECT * FROM #__vm_order_payment WHERE  order_id=" . $order_id;
       $db->setQuery($email);

        return $db->loadObject();
	}
	
	public function getUserEmail($userId)
	{
	   $db = JFactory::getDBO();
       $email = "SELECT email FROM #__users WHERE id=" . $userId;
       $db->setQuery($email);

        return $db->loadResult();
	}
	
	public function getPaymentMethod($order_id)
	{
	   $db = JFactory::getDBO();
       $payment = "SELECT pm.payment_method_name FROM #__vm_payment_method AS pm LEFT JOIN #__vm_order_payment as op ON op.payment_method_id=pm.payment_method_id WHERE op.order_id=" . $order_id;
       $db->setQuery($payment);

        return $db->loadResult();
	}

    public function insertPagoOrderAddress($vm_order_id, $pago_order_id)
    {
        $db = JFactory::getDBO();
        $sel_vm_order_users = "SELECT * FROM #__vm_order_user_info as ou WHERE order_id=".$vm_order_id;
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
            
	
			$ordAdd =  'INSERT INTO #__pago_orders_addresses (order_id, user_id, company, last_name, first_name, middle_name, phone_1, phone_2, address_1, address_2, city, fax, user_email, country, state, zip, address_type) VALUES(
                    "' . $pago_order_id.'",
                    "' . $vm_order_user->user_id . '",
                    "' . utf8_encode($vm_order_user->company) . '",
                    "' . utf8_encode($vm_order_user->last_name) . '",
                    "' . utf8_encode($vm_order_user->first_name) . '",
                    "' . utf8_encode($vm_order_user->middle_name) . '",
                    "' . $vm_order_user->phone_1 . '",
                    "' . $vm_order_user->phone_2 . '",
                    "' .  $vm_order_user->address_1 . '",
                    "' . $vm_order_user->address_2 . '",
                    "' . $vm_order_user->city . '",
                    "' . $vm_order_user->fax . '",
                    "' . $vm_order_user->user_email . '",
                    "' . $vm_order_user->country . '",
                    "' . $vm_order_user->state . '",
                    "' . $vm_order_user->zip . '",
                    "' . $address_type . '"
                )';


            $db->setQuery($ordAdd);
            $db->query();
        }
    }

    public function insertPagoOrderItem($order_item, $pago_order_id, $vm_order)
    {
        $data = array();
        $data['order_id'] = $pago_order_id;
        $data['item_id'] = $order_item->product_id;
        $data['qty'] = $order_item->product_quantity;
        $data['price'] = $order_item->product_item_price;
        $data['price_type'] = '';
        $data['attributes'] =  $order_item->product_attribute;
       // $data['sub_recur'] = $item->sub_recur;
       // $data['order_item_shipping'] = $vm_order->order_shipment;
        //$data['order_item_ship_method_id'] = $vm_order->virtuemart_shipmentmethod_id;

        $oitable = JTable::getInstance( 'orders_items', 'Table' );
        $oitable->bind($data);
        $oitable->check();
        $oitable->store();
    }

    public function migrateCategories()
    {
		$counter = $this->migrateItemsWithoutCategory();
		$r = 0;
	
        //  migrate categories
        $db = JFactory::getDBO();
        $sel_virtuemart_categories = "SELECT * FROM #__vm_category as c LEFT JOIN #__vm_category_xref as cx ON c.category_id = cx.category_child_id WHERE cx.category_parent_id = 0";
        $db->setQuery($sel_virtuemart_categories);
        $virtuemart_categories = $db->loadObjectList();
        $total_categories = 0;
        $total_items = 0;

        foreach($virtuemart_categories as $vm_category)
        {
            $parent_category_id = 1;
			$level = 1;
            $category_id = $vm_category->category_id;

            // Insert main category
            $insertedCategoryId = $this->insertPagoCategory($vm_category, $parent_category_id, 1);
            $total_categories++;
            $main_migrated_total_items = $this->migrateItems($category_id,$insertedCategoryId);
            $total_items = $total_items + $main_migrated_total_items;
			
			if($r == 0)
			{
				$total_items = $total_items + $counter;
			}

			$r++;

            $sel_vm_sub_categories = "SELECT * FROM #__vm_category_xref where category_parent_id!= 0 and category_parent_id =" . $category_id;
            $db->setQuery($sel_vm_sub_categories);
            $virtuemart_sub_categories = $db->loadObjectList();
			$level++;

           foreach($virtuemart_sub_categories as $sub_category)
            {
                $subcategory_id = $sub_category->category_child_id;

                $sel_vm_category = "SELECT * FROM #__vm_category AS vc where vc.category_id =" . $subcategory_id;
                $db->setQuery($sel_vm_category);
                $vm_sub_category = $db->loadObjectList();

                $insertedCategoryId =  $this->insertPagoCategory($vm_sub_category[0], $insertedCategoryId, $level);
                $total_categories++;
                $sub_migrated_total_items = $this->migrateItems($subcategory_id,$insertedCategoryId);
                $total_items = $total_items + $sub_migrated_total_items;
            }
        }

        return $total_categories . "_" . $total_items;
    }
	
	public function migrateItemsWithoutCategory()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT distinct(pcx.product_id) FROM #__vm_product AS p LEFT JOIN jos_vm_product_category_xref AS pcx ON p.product_id=pcx.product_id";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		$productArray = array();
		
		if (count($result) > 0)
		{
			for ($s = 0; $s < count($result); $s++)
			{
				if ($result[$s]->product_id != '')
				{
					$productArray[] = $result[$s]->product_id;
				}
			}
			
			$itemsStr = implode("','", $productArray);
			
			$selItems = "SELECT product_id from #__vm_product WHERE product_id NOT IN('" . $itemsStr . "')";
			$db->setQuery($selItems);
			$resultItems = $db->loadObjectList();
			$counter = 0;
			
			if (count($resultItems) > 0)
			{
				for ($j=0; $j<count($resultItems); $j++)
				{	
					$sel_virtuemart_items = "SELECT * FROM #__vm_product  WHERE product_id = " . $resultItems[$j]->product_id; 
					$db->setQuery($sel_virtuemart_items);
					$vm_item = $db->loadObject();
					$this->insertPagoItem($vm_item, '1');
					$counter++;
					
				}
				
				return $counter;
			}
		}
	}


    public function insertPagoCategory($vm_category, $parent_category_id, $level=1)
    {

        $db = JFactory::getDBO();
        $table = JTable::getInstance( 'categoriesi', 'Table' );
        $catdata = array();
        $category_id = $vm_category->category_id;

        if ($category_id)
        {
            $catdata['id'] = '';
            $timestamp = time() + 86400;
            $table->setLocation( $parent_category_id, 'last-child');
            $catdata['created_time'] = date( 'Y-m-d H:i:s', time() );
			
			if($vm_category->category_publish == 'Y')
			{
				$published = '1';
			}
			else
			{
				$published = '0';
			}
        

            $catdata['alias'] = $vm_category->category_name;
            $catdata['expiry_date'] = $timestamp;
            $catdata['parent_id'] = $parent_category_id;
            $catdata['name'] = $vm_category->category_name;
            $catdata['visibility'] = 1;
            $catdata['published'] = $published;
            $catdata['description'] = $vm_category->category_description;
            $catdata['item_count'] = 0;
            $catdata['created_user_id'] = $vm_category->cdate;
            $catdata['modified_user_id'] = $vm_category->mdate;
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
			//$this->insertCategoryImages($vm_category,$table->id);
            return $table->id; 

        }
    }
	
	 public function insertCategoryImages($vm_category , $pago_category_id)
    {}

    public function migrateItems($vm_category_id,$pago_category_id)
    {
        $db = JFactory::getDBO();
        $sel_virtuemart_items = "SELECT * FROM #__vm_product as p LEFT JOIN #__vm_product_category_xref as px ON p.product_id = px.product_id WHERE px.category_id = ".$vm_category_id;
        $db->setQuery($sel_virtuemart_items);
        $virtuemart_items = $db->loadObjectList();

        $added_virtuemart_items = array();
        $migrated_total_items = 0;
		
        foreach($virtuemart_items as $vm_item)
        {
             $checkSku = "SELECT id FROM #__pago_items WHERE sku = '" . $vm_item->product_sku . "'";
        	 $db->setQuery($checkSku);
			 $isExistSku = $db->loadResult();

            if(!$isExistSku)
            {
                $vm_item->quantity = $vm_item->product_in_stock;
                $insertedItemId = $this->insertPagoItem($vm_item, $pago_category_id);
                $migrated_total_items++;
                $added_virtuemart_items[$vm_item->product_id] = $insertedItemId;
            }
            else
            {
                // add item in category Item table
                // get Pago item id
                $itemId = $added_virtuemart_items[$vm_item->product_id];
                $query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $pago_category_id . "', '" . $vm_item->product_id . "')";
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
        $row->id = $vm_item->product_id;
        $row->sku = $vm_item->product_sku;
		$price = $this->getProductPrice($vm_item->product_id);
		$discPrice = $this->getDiscPrice($vm_item->product_discount_id);
		
		if($vm_item->product_publish == 'Y')
		{
			$published = '1';
		}
		else
		{
			$published = '0';
		}
		
        $vm_item->product_name = utf8_encode($vm_item->product_name);
        $vm_item->product_s_desc = utf8_encode($vm_item->product_s_desc);
        $vm_item->product_desc = utf8_encode($vm_item->product_desc);
        $row->name = html_entity_decode(htmlspecialchars($vm_item->product_name));
        $row->qty = $vm_item->product_in_stock;
        $row->type = '';
        $row->published = $published;
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
		
		if(is_array($discPrice))
		{
			$row->discount_amount = $discPrice->amount;
			$row->discount_type = $discPrice->is_percent;
			$row->disc_start_date = date( 'Y-m-d', $discPrice->start_date);
			$row->disc_end_date = date( 'Y-m-d', $discPrice->end_date);
			$row->apply_discount = 1;
		}
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
		
		// $this->insertItemImages($vm_item , $itemId);//changed
		 $this->insertItemAttributes($vm_item , $itemId);//changed

        return $itemId;
    }
	public function getDiscPrice($discount_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT amount,is_percent,start_date,end_date FROM #__vm_product_discount WHERE discount_id=" . $discount_id;
		$db -> setQuery($sql);
		return $db -> loadObject();
	}
	
	public function getProductPrice($prd_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT product_price FROM #__vm_product_price WHERE product_id=" . $prd_id;
		$db -> setQuery($sql);
		return $db -> loadObject();
	}
	
	public function insertItemImages($vm_item , $pago_item_id)
    {}
	
	 function insertItemAttributes($vm_item , $pago_item_id)
    {
		
		$db = JFactory::getDBO();
      	$vm_attributes = explode(";",$vm_item->attribute);
	
        for($k=0; $k<count($vm_attributes); $k++ )
        {
		
			$vm_attrib = explode(",",$vm_attributes[$k]);
            $row = JTable::getInstance( 'attribute', 'Table' );
            

            $data = array(
                    'name'     => $vm_attrib[0],
                    'type'   => 3,
                    'alias'     => $vm_attrib[0],
                    'visible'   => 1,
                    'preselected' => 1,
                    'for_item' => $pago_item_id,
                    'attr_enable'      => 1,
                    'showfront' => 1,
                    'display_type' => 1,
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
			
            for($s=1; $s<count($vm_attrib); $s++)
            {
					$prop_array = explode('[',$vm_attrib[$s]);
					$price_arr = explode(']',$prop_array[1]);
					$price=  str_replace('=','',$price_arr[0]);
                    $attrVal = JTable::getInstance( 'Attribute_Options', 'Table' );
                    $data = array(
                        'attr_id'     => $attribute_id,
                        'name'     => $prop_array[0],
                        'type'   => '3',
                        'price_sign'   => '1',
                        'price_type'   => 0,
                        'price_sum' => $price,
                        'for_item' => $pago_item_id,
                        'in_stock'      => '10',
                        'opt_enable' => '1',
                        'published' => '1',
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
