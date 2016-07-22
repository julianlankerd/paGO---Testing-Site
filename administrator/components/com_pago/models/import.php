<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelImport extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getData()
	{
		
		ob_clean();
		$post = JFactory::getApplication()->input->getArray($_POST);
		$files = $_FILES; 
		$files = $files[$post['task'] . $post['import']];
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		if (isset($post['task']) && isset($post['import']))
		{
			if ($files['name'] == "")
			{
				return JText::_('COM_PAGO_PLEASE_SELECT_FILE');
			}

			$ext = strtolower(JFile::getExt($files['name']));

			if ($ext != 'csv')
			{
				return JText::_('COM_PAGO_FILE_EXTENSION_WRONG');
			}
		}
		else
		{
			if (!isset($post['import']))
			{
				return JText::_('COM_PAGO_PLEASE_SELECT_SECTION');
			}
		}

		$src = $files['tmp_name'];
		$dest = JPATH_ROOT . '/media/pago/pgimportcsv/' . $post['import'] . '/' . $files['name'];
		$file_upload = JFile::upload($src, $dest);
		$session->clear('ImportPost');
		$session->clear('Importfile');
		$session->clear('Importfilename');
		$session->set('ImportPgPost', $post);
		$session->set('ImportPgfile', $files);
		$session->set('ImportPgfilename', $files['name']);

		$app->Redirect('index.php?option=com_pago&view=import&layout=importrecord');

		return;
	}



	public function importpgdata()
	{
		ob_clean();
		$session = JFactory::getSession();

		/* Get all posted data */

		$new_line = JFactory::getApplication()->input->get('new_line');

		if ($new_line == "")
		{
			$new_line = 0;
		}

		$post = $session->get('ImportPgPost');

		$files = $session->get('ImportPgfile');
		$file_name = $session->get('ImportPgfilename');

		/* Load the table model */
		switch ($post['import'])
		{
			case 'items':
				$row = $this->getTable('item');
				break;
		}

		$line = 1;
		$headers = array();
		$correctlines = 0;
		$sep = ',';
		$records = 0;
		$db = JFactory::getDBO();
		$handle = fopen(JPATH_ROOT . '/media/pago/pgimportcsv/' . $post['import'] . '/' . $file_name, "r");
		
		if (!is_dir(JPATH_SITE . "/media/pago/import_log"))
		{
			mkdir(JPATH_SITE . "/media/pago/import_log", 0755);
				
		}
		
		$t = time();
		$log_file = "import_log_" . $t . ".txt";
		$logfile = fopen(JPATH_SITE . "/media/pago/import_log/" . $log_file ,"wb");

		while (($data = fgetcsv($handle, 0, $sep, '"')) !== false)
		{
			if ($records <= 100 )
			{
				if ($line == 1)
				{
					foreach ($data as $key => $name)
					{
						/* Set the column headers */
						$headers[$key] = $name;
					}
				}
				else
				{
					if ($line > $new_line)
					{
							$rawdata = array();

							foreach ($data as $key => $name)
							{
								$rawdata[$headers[$key]] = $name;
							}

							if(count($rawdata) > 0)
							{
								if ($post['import'] == 'items')
								{
									$dispatcher = JEventDispatcher::getInstance();
									JPluginHelper::importPlugin('pago_products');
									$dispatcher->trigger('override_product_sku', array(&$rawdata));
									
									if($rawdata['Parent SKU']!="")
									{
										$this->importAttributeCombo($post, $rawdata);
							  			$records++;
									}
									else if($rawdata['SKU']!="")
									{
										$this->importProductData($post, $rawdata, $logfile);
							  			$records++;
									}
									else
									{
										fclose($handle);
										$text_print = "`_`" . $records . "`_`" . $records . "";
										ob_clean();
										echo $text_print;
										exit;
									}
									$dispatcher = KDispatcher::getInstance();
									JPluginHelper::importPlugin( 'pago_products' );
									$dispatcher->trigger('onAddBundledPorduct', array($rawdata));
								}
								
								if ($post['import'] == 'customers')
								{
									$this->importCustomerData($post, $rawdata);
								  	$records++;
								}
								
								if ($post['import'] == 'categories')
								{
									if($this->importCategoryData($post, $rawdata))
									{
										$records++;
									}
								  	
								}
							}
							else
							{
								fclose($handle);
								$text_print = "`_`" . $records . "`_`" . $records . "";
								ob_clean();
								echo $text_print;
								exit;
							}

							
					}
				}

				$line++;
			}
			else
			{
				$blank = "";
				$text_print = $line . "`_`" . $blank . "";
				ob_clean();
				echo  $text_print;
				exit;

			}
		}
		
		fclose($logfile);
		fclose($handle);
		$text_print = "`_`" . $records . "`_`" . $records . "";
		ob_clean();
		echo $text_print;
		exit;
	}
	
	public function importCustomerData($post,$rawdata)
	{
		$db = JFactory::getDBO();

		$q = "SELECT * FROM `#__users` "
								."WHERE `email` = '".trim($rawdata['user_email'])."' ";
		$this->_db->setQuery($q);
		$joomusers = $this->_db->loadObject();

		if (count($joomusers)==0){
			$user_id = 0;
		}else{
			$user_id = $joomusers->id;
		}

		if($user_id)
		{
			$query = "SELECT id FROM #__pago_user_info WHERE user_id='".$user_id."' AND address_type = '".$rawdata['address_type']."'";
			$db->setQuery($query);
			$pago_user_id = $db->loadResult();
		}
		else
		{
			// add user in Joomla

			$date = time();
			$name =$rawdata['first_name']." ".$rawdata['last_name']; 

			$data = array(
              "id" => 0,
              "name"=> $name,
              "username"=> trim($rawdata['username']),
              "password"=> $rawdata['password'],
              "password2"=> $rawdata['password'],
              "email"=> trim($rawdata['user_email']),
              "block"=>0,
              "registerDate" =>date("Y-m-d H:i:s", $date),
              "groups" => array('2' => '2'),
              "sendEmail" =>0,
            );

            $user = clone(JFactory::getUser());

            // If user registration is not allowed, show 403 not authorized.
            $usersConfig = JComponentHelper::getParams( 'com_users' );
            $usersConfig->set('allowUserRegistration' , 1);
            $user->bind($data);
            $user->save();
            $user_id = $user->id;
            $pago_user_id = 0;
		}

		if ($pago_user_id)
		{
			$mdate = time();
			$row = $this->getTable('userinfo');
			$row->load($pago_user_id);
			$row->user_id = $user_id;
			$row->address_type = $rawdata['address_type'];
			$row->address_type_name = $rawdata['address_type_name'];
			$row->company = $rawdata['company'];
			$row->title = $rawdata['title'];
			$row->last_name = $rawdata['last_name'];
			$row->first_name = $rawdata['first_name'];
			$row->middle_name = $rawdata['middle_name'];
			$row->phone_1 = $rawdata['phone_1'];
			$row->phone_2 = $rawdata['phone_2'];
			$row->fax = $rawdata['fax'];
			$row->address_1 = $rawdata['address_1'];
			$row->address_2 = $rawdata['address_2'];
			$row->city = $rawdata['city'];
			$row->state = $rawdata['state'];
			$row->country = $rawdata['country'];
			$row->zip = $rawdata['zip'];
			$row->user_email = $rawdata['user_email'];
			$row->mdate = $mdate;
			$row->perms = $rawdata['perms'];
			
			if(!$row->store())
			{
				return JText::_('COM_PAGO_ERROR_DURING_IMPORT');
			}
		}
		else
		{
			$mdate = time();
			$cdate = time();
			$row = $this->getTable('userinfo');
			$row->load();
			$row->user_id = $user_id;
			$row->address_type = $rawdata['address_type'];
			$row->address_type_name = $rawdata['address_type_name'];
			$row->company = $rawdata['company'];
			$row->title = $rawdata['title'];
			$row->last_name = $rawdata['last_name'];
			$row->first_name = $rawdata['first_name'];
			$row->middle_name = $rawdata['middle_name'];
			$row->phone_1 = $rawdata['phone_1'];
			$row->phone_2 = $rawdata['phone_2'];
			$row->fax = $rawdata['fax'];
			$row->address_1 = $rawdata['address_1'];
			$row->address_2 = $rawdata['address_2'];
			$row->city = $rawdata['city'];
			$row->state = $rawdata['state'];
			$row->country = $rawdata['country'];
			$row->zip = $rawdata['zip'];
			$row->user_email = $rawdata['user_email'];
			$row->cdate = $cdate;
			$row->mdate = $mdate;
			$row->perms = $rawdata['perms'];
			
			if (!$row->store())
			{
				return JText::_('COM_PAGO_ERROR_DURING_IMPORT');
			}
		}

		
	}
	
	public function importCategoryData($post,$rawdata)
	{
		if(trim($rawdata['name']) != '')
		{
		
			$db = JFactory::getDBO();
			$cat_id = 0;
			if($rawdata['id'])
			{
				$query = "SELECT id FROM #__pago_categoriesi WHERE id='".$rawdata['id']."'";
				$db->setQuery($query);
				$cat_id = $db->loadResult();
			}
			

			$parent_category = $rawdata['parent_category'];
			
			// get parent category id
			$parent_cat_id = 0;
			if($parent_category != "")
			{
				$query = "SELECT id FROM #__pago_categoriesi WHERE name='".$parent_category."'";
				$db->setQuery($query);
				$parent_cat_id = $db->loadResult();
			}

			$table = JTable::getInstance( 'categoriesi', 'Table' );
			$catdata = array();

			if ($cat_id)
			{
				$catdata['id'] = $cat_id;
			}
			else
			{
				$timestamp = time() + 86400;
				$table->setLocation( $parent_cat_id, 'last-child');
				$catdata['created_time'] = date( 'Y-m-d H:i:s', time() );
			}

			$catdata['alias'] = $rawdata['alias'];
			$catdata['expiry_date'] = $timestamp;
			$catdata['parent_id'] = $parent_cat_id;
			$catdata['name'] = $rawdata['name'];
			$catdata['visibility'] = $rawdata['visibility'];
			$catdata['published'] = $rawdata['published'];
			$catdata['description'] = $rawdata['description'];
			$catdata['item_count'] = $rawdata['item_count'];
			$catdata['created_user_id'] = $rawdata['created_user_id'];
			$catdata['modified_user_id'] = $rawdata['modified_user_id'];
			$catdata['modified_time'] = date( 'Y-m-d H:i:s', time() );
			$catdata['featured'] = $rawdata['featured'];
			$catdata['access'] = $rawdata['access'];
			$catdata['category_settings_image_settings'] = '{"padding_left":"0","border_left":"1","margin_left":"0","margin_top":"0","border_top":"1","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"1","margin_right":"30","border_bottom":"1","margin_bottom":"20"}';
            $catdata['category_settings_product_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';
            $catdata['product_view_settings_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"3","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';
            $catdata['category_settings_category_title'] = 1;
            $catdata['category_settings_product_counter'] = 0;
            $catdata['category_settings_category_description'] = 1;
            $catdata['product_settings_product_title'] = 1;
            $catdata['product_settings_product_image'] = 1;
            $catdata['product_settings_link_to_product'] = 1;
            $catdata['product_settings_featured_badge'] = 0;
            $catdata['product_settings_quantity_in_stock'] = 0;
            $catdata['product_settings_short_desc'] = 1;
            $catdata['product_settings_desc'] = 0;
            $catdata['product_settings_sku'] = 1;
            $catdata['product_settings_price'] = 1;
            $catdata['product_settings_media'] = 1;
            $catdata['product_settings_rating'] = 0;
            $catdata['product_settings_read_more'] = 1;
            $catdata['product_settings_discounted_price'] = 1;
            $catdata['product_settings_attribute'] = 0;
            $catdata['product_settings_downloads'] = 0;
            $catdata['product_settings_add_to_cart_qty'] = 1;
            $catdata['product_view_settings_price'] = 0;
            $catdata['product_view_settings_discounted_price'] = 1;
            $catdata['product_view_settings_attribute'] =0;
            $catdata['product_view_settings_add_to_cart_qty'] = 0;

			$table->bind( $catdata );
			if ($catdata['alias'] == '')
			{
				$table->check();
			}

			if (!$table->store())
			{
				return JText::_('COM_PAGO_ERROR_DURING_IMPORT');
			}

			$table->rebuildPath( $table->id );
			$table->rebuild( $table->id, $table->lft, $table->level, $table->path );

			$this -> ImportCategoryImages($table->id, $rawdata['images']);
			return true;

		}
		return false;
	}
	
	public function setItemLogError($logfile, $msg)
	{
		fwrite($logfile,$msg);
		fputs($logfile, "\r\n");
	
	}

	public function importProductData($post,$rawdata,$logfile)
	{
		$db = JFactory::getDBO();
		$sku = $rawdata['SKU'];
		$query = "SELECT id FROM #__pago_items WHERE sku = '" . $sku . "'";
		$db->setQuery($query);
		$itemId = $db->loadResult();
		$row = $this->getTable('items');
		
		if($itemId)
		{
			$row->load($itemId);
		}
		if($rawdata['SKU'] == '')
		{
			$randomString = rand(100000, 99999999);
			$rawdata['SKU'] = $randomString;
		}
		$row->sku = $rawdata['SKU'];
		$rawdata['Item Name'] = utf8_encode($rawdata['Item Name']);
		$rawdata['Short Description'] = utf8_encode($rawdata['Short Description']);
		$rawdata['Long Description'] = utf8_encode($rawdata['Long Description']);
		$row->name = $rawdata['Item Name'];
		$row->qty = $rawdata['Quantity'];
		//$row->type = $rawdata['Type'];
		$row->published = $rawdata['Published'];
		$row->price = $rawdata['Price'];
		$row->tax_exempt = $rawdata['Tax Exempt'];
		$row->primary_category = '1';
		$row->free_shipping = $rawdata['Shipping-Free-Shipping'];
		$row->shipping_methods = $rawdata['Shipping-Method'];
		$row->pgtax_class_id = $rawdata['Shipping-Tax-Class'];
		$row->visibility = $rawdata['Item-Display'];
		$row->description = html_entity_decode($rawdata['Short Description']);
		$row->content = html_entity_decode($rawdata['Long Description']);
		$row->unit_of_measure = $rawdata['Dimensions-Weight-Type'];
		$row->height = $rawdata['Dimensions-Length'];
		$row->width = $rawdata['Dimensions-Width'];
		$row->length = $rawdata['Dimensions-Height'];
		$row->weight = $rawdata['Dimensions-Weight'];
	
		if ($itemId)
		{
			// Update record

			$row->id = $itemId;

			if (!$row->store())
			{
				$error = $rawdata['SKU'] . " - SKU Edit Failed";//$row->getError();
				$this->setItemLogError($logfile, $error);
				return JText::_('COM_PAGO_ERROR_DURING_IMPORT');
			}
		}
		else
		{
			$row->created = date( 'Y-m-d H:i:s', time() );
			// set alias
			$row->alias = $row->name;
			$row->alias = JFilterOutput::stringURLSafe( $row->alias );

			if ( trim( str_replace( '-', '', $row->alias ) ) == '' ) {
				$datenow = JFactory::getDate();
				$row->alias = $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
			}

			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('pago_products');
			$dispatcher->trigger('override_sentence', array(&$row));

			// Insert record
			$ret = $this->_db->insertObject('#__pago_items', $row, 'id');

			if (!$ret)
			{
				$error = $rawdata['SKU'] . " - SKU ADD Failed";//$row->getError();
				$this->setItemLogError($logfile, $error);
				return JText::_('COM_PAGO_ERROR_DURING_IMPORT');
			}

			$qu = "SELECT LAST_INSERT_ID()";
			$db->setQuery($qu);
			$itemId = $db->loadResult();
		}

		if ($itemId)
		{
			// trigger for additional fields import (if any)
			$dispatcher = KDispatcher::getInstance();
			JPluginHelper::importPlugin( 'pago_products' );
			$dispatcher->trigger('onImportAdditionalFields', array($itemId, $rawdata));
			// End
			//Attribute Import start

			if($rawdata['Parent SKU'] == '')
			{
				if($rawdata['Attribute1Name'] != '')
				{
					$Attribute1Name = $rawdata['Attribute1Name'];
					$attribArray = $this -> setAttributeName($Attribute1Name, $itemId);	
					
					if($rawdata['Attribute1Value'] != '')
					{
						$attribValArr = explode('##', $rawdata['Attribute1Value']);
						$this -> setAttributeValue($attribValArr, $itemId, $attribArray);		
					}
				}
				
				if($rawdata['Attribute2Name'] != '')
				{
					$Attribute1Name = $rawdata['Attribute2Name'];
					$attribArray = $this -> setAttributeName($Attribute1Name, $itemId);	
					
					if($rawdata['Attribute2Value'] != '')
					{
						$attribValArr = explode('##', $rawdata['Attribute2Value']);
						$this -> setAttributeValue($attribValArr, $itemId, $attribArray);		
					}
				}
				
				if($rawdata['Attribute3Name'] != '')
				{
					$Attribute1Name = $rawdata['Attribute3Name'];
					$attribArray = $this -> setAttributeName($Attribute1Name, $itemId);	
					
					if($rawdata['Attribute3Value'] != '')
					{
						$attribValArr = explode('##', $rawdata['Attribute3Value']);
						$this -> setAttributeValue($attribValArr, $itemId, $attribArray);		
					}
				}
				
				if($rawdata['Attribute4Name'] != '')
				{
					$Attribute1Name = $rawdata['Attribute4Name'];
					$attribArray = $this -> setAttributeName($Attribute1Name, $itemId);	
					
					if($rawdata['Attribute4Value'] != '')
					{
						$attribValArr = explode('##', $rawdata['Attribute4Value']);
						$this -> setAttributeValue($attribValArr, $itemId, $attribArray);		
					}
				}
				
				if($rawdata['Attribute5Name'] != '')
				{
					$Attribute1Name = $rawdata['Attribute5Name'];
					$attribArray = $this -> setAttributeName($Attribute1Name, $itemId);	
					
					if($rawdata['Attribute5Value'] != '')
					{
						$attribValArr = explode('##', $rawdata['Attribute5Value']);
						$this -> setAttributeValue($attribValArr, $itemId, $attribArray);		
					}
				}
				
				if($rawdata['Attribute6Name'] != '')
				{
					$Attribute1Name = $rawdata['Attribute6Name'];
					$attribArray = $this -> setAttributeName($Attribute1Name, $itemId);	
					
					if($rawdata['Attribute6Value'] != '')
					{
						$attribValArr = explode('##', $rawdata['Attribute6Value']);
						$this -> setAttributeValue($attribValArr, $itemId, $attribArray);		
					}
				}
			}
			//Attribute Import End
		
			$dispatcher = KDispatcher::getInstance();
			$dispatcher->trigger('backend_item_extra_fields_import', array( &$itemId, $rawdata));
			$unit_of_measure = $rawdata['Dimensions-Weight-Type'];
			$length = $rawdata['Dimensions-Length'];
			$width = $rawdata['Dimensions-Width'];
			$height = $rawdata['Dimensions-Height'];
			$weight = $rawdata['Dimensions-Weight'];

			// Check for item data table
			$query = "SELECT item_id FROM #__pago_items_data WHERE item_id = '" . $itemId . "'";
			$db->setQuery($query);
			$itemAva = $db->loadResult();

			if ($itemAva)
			{
				$query = "UPDATE #__pago_items_data SET unit_of_measure='" . $unit_of_measure . "', "
				. "length = '" . $length . "', "
				. "width = '" . $width . "', "
				. "height = '" . $height . "', "
				. "weight = '" . $weight . "' WHERE `item_id`='" . $itemAva . "' ";
			}
			else
			{
				// Insert record
				$query = "INSERT IGNORE INTO #__pago_items_data "
					. "(`item_id`, `unit_of_measure`, `length`, `width`, `height`, `weight`) "
					. "VALUES ('" . $itemId . "', '" . $unit_of_measure . "', '" . $length . "', '" . $width . "', '" . $height . "', '" . $weight . "')";
			}

			$db->setQuery($query);
			$db->Query();

			// Check for metadta
			$query_meta = "SELECT id FROM #__pago_meta_data WHERE id = '" . $itemId . "' AND type='item'";
			$db->setQuery($query_meta);
			$itemMetaAva = $db->loadResult();
			$title = @$rawdata['Meta-Title'];
			$author = @$rawdata['Meta-Author'];
			$robots = @$rawdata['Meta-Robots'];

			if ($itemMetaAva)
			{
				$query_meta = "UPDATE #__pago_meta_data SET title='" . $title . "', "
				. "author = '" . $author . "', "
				. "robots = 'index, follow' WHERE `id`='" . $itemMetaAva . "' AND type='item'";
			}
			else
			{
				// Insert record
				$query_meta = "INSERT INTO #__pago_meta_data "
					. "(`id`, `type`, `title`, `author`, `robots`) "
					. "VALUES ('" . $itemId . "', 'item', '" . $title . "', '" . $author . "', 'index, follow')";
			}

			$db->setQuery($query_meta);
			$db->Query();

			// Check for Primary category
			$primary_category = $rawdata['Category-Primary'];
			$secondary_category = $rawdata['Category-Secondary'];
			$primary_category_array = explode("|", $primary_category);
			$pri_cat_count = count($primary_category_array);

			if ($pri_cat_count > 0)
			{
				for ($g = 0; $g < $pri_cat_count; $g++)
				{
					$cat_tree_name = $primary_category_array[$g];
					$cat_tree_array_count = 0;

					if ($cat_tree_name != '')
					{
						$cat_tree_array = explode("#", $cat_tree_name);
						$cat_tree_array_count = count($cat_tree_array);
					}

					// For images
					$cat_tree_image_name = @$primary_category_images_array[$g];
					$cat_tree_image_array_count = 0;

					if ($cat_tree_image_name != '')
					{
						$cat_tree_image_array = explode("#", $cat_tree_image_name);
						$cat_tree_image_array_count = count($cat_tree_image_array);
					}

					$parent_id = 0;
					$level = 0;
					$prd_cat_id = 0;

					if ($cat_tree_array_count > 0)
					{
						for ($b = 0;$b < $cat_tree_array_count; $b++)
						{
							$cat_name = $cat_tree_array[$b];
							if($cat_name != "" && $cat_name != '"')
							{
								$and = '';

								if ($level != 0)
								{
									$and = " AND parent_id=" . $parent_id;
								}

							 	$query_catcheck = "SELECT id FROM #__pago_categoriesi WHERE name  = '" . $cat_name . "'" . $and;
								$db->setQuery($query_catcheck);
								$cat_id = $db->loadResult();
								if ($cat_id == '')
								{
									//	$catdata = $this->getTable('categoriesi');
									$table = JTable::getInstance( 'categoriesi', 'Table' );
									$timestamp = time() + 86400;
									$table->setLocation( $parent_id, 'last-child');

									$catdata = array();
									$catdata['expiry_date'] = $timestamp;
									$catdata['parent_id'] = $parent_id;
									$catdata['name'] = $cat_name;
									$catdata['level'] = $level;
									$catdata['visibility'] = 1;
									$catdata['published'] = 1;
									$catdata['access'] = 1;
									$catdata['category_settings_image_settings'] = '{"padding_left":"0","border_left":"1","margin_left":"0","margin_top":"0","border_top":"1","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"1","margin_right":"30","border_bottom":"1","margin_bottom":"20"}';
						            $catdata['category_settings_product_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';
						            $catdata['product_view_settings_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"3","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';
						            $catdata['category_settings_category_title'] = 1;
						            $catdata['category_settings_product_counter'] = 0;
						            $catdata['category_settings_category_description'] = 1;
						            $catdata['product_settings_product_title'] = 1;
						            $catdata['product_settings_product_image'] = 1;
						            $catdata['product_settings_link_to_product'] = 1;
						            $catdata['product_settings_featured_badge'] = 0;
						            $catdata['product_settings_quantity_in_stock'] = 0;
						            $catdata['product_settings_short_desc'] = 1;
						            $catdata['product_settings_desc'] = 0;
						            $catdata['product_settings_sku'] = 1;
						            $catdata['product_settings_price'] = 1;
						            $catdata['product_settings_media'] = 1;
						            $catdata['product_settings_rating'] = 0;
						            $catdata['product_settings_read_more'] = 1;
						            $catdata['product_settings_discounted_price'] = 1;
						            $catdata['product_settings_attribute'] = 0;
						            $catdata['product_settings_downloads'] = 0;
						            $catdata['product_settings_add_to_cart_qty'] = 1;
						            $catdata['product_view_settings_price'] = 0;
						            $catdata['product_view_settings_discounted_price'] = 1;
						            $catdata['product_view_settings_attribute'] =0;
						            $catdata['product_view_settings_add_to_cart_qty'] = 0;

									$table->bind( $catdata );
									if ($catdata['alias'] == '')
									{
										$table->check();
									}
									$table->store();
									$table->rebuildPath( $table->id );
									$table->rebuild( $table->id, $table->lft, $table->level, $table->path );

									$qcat = "SELECT LAST_INSERT_ID()";
									$db->setQuery($qcat);
									$parent_id = $prd_cat_id = $db->loadResult();
									$this -> ImportCategoryImages($prd_cat_id, $cat_tree_image_array[$b]);
									
									// End
									
								}
								else
								{
									$prd_cat_id = $parent_id = $cat_id;
								}

								$level++;
							}
							
						}
					}


					if ($prd_cat_id != 0)
					{
						$query_prd_cat = "UPDATE #__pago_items SET primary_category='" . $prd_cat_id . "'  WHERE `sku`='" . $rawdata['SKU'] . "' ";
						$db->setQuery($query_prd_cat);
						$db->Query();
						
						$qu = "SELECT item_id FROM #__pago_categories_items WHERE category_id='" . $prd_cat_id . "'  AND `item_id`='" . $itemId . "' ";
						$db->setQuery($qu);
						$ava_par_id = $db->loadResult();

						if (!$ava_par_id)
						{
							$query_par_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $prd_cat_id . "', '" . $itemId . "')";
							$db->setQuery($query_par_cat);
							$db->Query();
						}
					}
				}
			}

			// Primary Category End

			// Secondary Category Start
			$secondary_category = $rawdata['Category-Secondary'];
			$secondary_category_array = explode("|", $secondary_category);
			$sec_cat_count = count($secondary_category_array);

			if ($sec_cat_count > 0)
			{
				for ($g = 0; $g < $sec_cat_count; $g++)
				{
					$sec_cat_tree_name = $secondary_category_array[$g];
					$sec_cat_tree_array_count = 0;

					if ($sec_cat_tree_name != '')
					{
						$sec_cat_tree_array = explode("#", $sec_cat_tree_name);
						$sec_cat_tree_array_count = count($sec_cat_tree_array);
					}

					$parent_id = 0;
					$level = 0;
					$sec_prd_cat_id = 0;

					if ($sec_cat_tree_array_count > 0)
					{
						for ($b = 0; $b < $sec_cat_tree_array_count; $b++)
						{
							$cat_name = $sec_cat_tree_array[$b];
							if($cat_name!="" && $cat_name != '"')
							{
								$and = '';

								if ($level != 0)
								{
									$and = " AND parent_id=" . $parent_id;
								}

								$query_catcheck = "SELECT id FROM #__pago_categoriesi WHERE name  = '" . $cat_name . "'" . $and;
								$db->setQuery($query_catcheck);
								$cat_id = $db->loadResult();

								if ($cat_id == '')
								{
									//	$catdata = $this->getTable('categoriesi');
									$stable = JTable::getInstance( 'categoriesi', 'Table' );
									$timestamp = time() + 86400;
									$stable->setLocation( $parent_id, 'last-child');

									$scatdata = array();
									$scatdata['expiry_date'] = $timestamp;
									$scatdata['parent_id'] = $parent_id;
									$scatdata['name'] = $cat_name;
									$scatdata['level'] = $level;
									$scatdata['visibility'] = 1;
									$scatdata['published'] = 1;
									$scatdata['access'] = 1;
									$scatdata['category_settings_image_settings'] = '{"padding_left":"0","border_left":"1","margin_left":"0","margin_top":"0","border_top":"1","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"1","margin_right":"30","border_bottom":"1","margin_bottom":"20"}';
						            $scatdata['category_settings_product_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';
						            $scatdata['product_view_settings_image_settings'] = '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"3","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}';
						            $scatdata['category_settings_category_title'] = 1;
						            $scatdata['category_settings_product_counter'] = 0;
						            $scatdata['category_settings_category_description'] = 1;
						            $scatdata['product_settings_product_title'] = 1;
						            $scatdata['product_settings_product_image'] = 1;
						            $scatdata['product_settings_link_to_product'] = 1;
						            $scatdata['product_settings_featured_badge'] = 0;
						            $scatdata['product_settings_quantity_in_stock'] = 0;
						            $scatdata['product_settings_short_desc'] = 1;
						            $scatdata['product_settings_desc'] = 0;
						            $scatdata['product_settings_price'] = 1;
						            $scatdata['product_settings_sku'] = 1;
						            $scatdata['product_settings_media'] = 1;
						            $scatdata['product_settings_rating'] = 0;
						            $scatdata['product_settings_read_more'] = 1;
						            $scatdata['product_settings_discounted_price'] = 0;
						            $scatdata['product_settings_attribute'] = 0;
						            $scatdata['product_settings_downloads'] = 0;
						            $scatdata['product_settings_add_to_cart_qty'] = 0;
						            $scatdata['product_view_settings_price'] = 0;
						            $scatdata['product_view_settings_discounted_price'] = 1;
						            $scatdata['product_view_settings_attribute'] =0;
						            $scatdata['product_view_settings_add_to_cart_qty'] = 1;

									$stable->bind( $scatdata );
									if ($scatdata['alias'] == '')
									{
										$stable->check();
									}
									$stable->store();
									$stable->rebuildPath( $stable->id );
									$stable->rebuild( $stable->id, $stable->lft, $stable->level, $stable->path );

									

									$sqcat = "SELECT LAST_INSERT_ID()";
									$db->setQuery($sqcat);
									$parent_id = $prd_cat_id = $db->loadResult();

									//End
									
								}
								else
								{
									$sec_prd_cat_id = $parent_id = $cat_id;
								}

								$level++;
							}
							
						}
					}

					if ($sec_prd_cat_id != 0)
					{
						$qu = "SELECT item_id FROM #__pago_categories_items WHERE category_id='" . $sec_prd_cat_id . "'  AND `item_id`='" . $itemId . "' ";
						$db->setQuery($qu);
						$ava_id = $db->loadResult();

						if (!$ava_id)
						{
							$query_sec_cat = "INSERT INTO #__pago_categories_items (`category_id`, `item_id`) VALUES ('" . $sec_prd_cat_id . "', '" . $itemId . "')";
							$db->setQuery($query_sec_cat);
							$db->Query();
						}
					}
				}
			}

			// Secondary Category End
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('pago_products');
			$dispatcher->trigger('getImageNamesOnNumber', array(&$rawdata));
			
			// Image Import start
			if(isset($rawdata['Images']) && $rawdata['Images']!= '')
			{
				$this->ImportItemImages($itemId, @$rawdata['Images']);
			}
			if(isset($rawdata['External_image_url']) && $rawdata['External_image_url']!= '')
			{
				$this->ImportItemImages($itemId, $rawdata['External_image_url'], true);
			}
			// Image Import End
			// import Downlaodable file
			$this->importDownloadFiles($itemId, $rawdata['download_files']);
		}
	}


	// Download 
    function importDownloadFiles($itemId , $download_files)
    {
       $db = JFactory::getDBO();

       
        $qu = "SELECT primary_category FROM #__pago_items WHERE `id`='" . $itemId . "' ";
        $db->setQuery($qu);
        $itemCatId = $db->loadResult();
        jimport('joomla.filesystem.file');
        Pago::load_helpers('imagehandler');
        $dispatcher = KDispatcher::getInstance();
        $params = Pago::get_instance('config')->get();

		if($download_files != "")
		{
			$total_files = explode("|", $download_files);
			$files_count = count($total_files);
			if($files_count <= 0)
			{
				$total_files[] = $download_files;
				$files_count = count($total_files);
			}
		}



		// Check for image count loop start
		if ($files_count > 0)
		{
			// Image check for loop start
			for ($l = 0; $l < $files_count; $l++)
			{
				$title =  pathinfo($total_files[$l],PATHINFO_FILENAME);
                $content = $title;
            	$dir = JPATH_ROOT . "/tmp/pago_images/" . $total_files[$l];
            	$filename = $total_files[$l];
				$src_folder = $dir;
				$uploadFiles['name'] = $filename;

				$path = trim(
					$params->get( 'media.user_file_path', 'media' .DS. 'pago' ), DS
				);

				$path_extra = 'items/'.$itemCatId;
				$result = array();
				if (!is_dir(JPATH_SITE .$path))
				{
					mkdir(JPATH_SITE ."/".$path."/".$path_extra, 0755);
				}

				$uploads = PagoImageHandlerHelper::upload_dir( $path );
				$config = Pago::get_instance( 'config' )->get();
				$name_prefix = 'item-' .$itemId. '-';
				$filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix.$filename );
				$dest_folder = JPATH_SITE ."/". $path ."/".$path_extra."/".$filename;
				$files[] = $filename;
				copy($src_folder, $dest_folder);
				// Files For Loop start

				for ($s = 0; $s < count($files); $s++)
				{

					// Check for matching Image 
					if ($files[$s] != "" && $files[$s] == $filename)
					{
						$item_id = $itemId;
						$org_file = JPATH_SITE ."/". $path ."/".$path_extra."/" . $files[$s];
						$existing_image = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $item_id . "' AND `title`='".$filename."'";
						$db->setQuery($existing_image);
						$existing_imageRs = $db->loadResult();
						if($existing_imageRs == 0)
						{

							$qu_img = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $item_id . "' AND `default`=1";
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

						// Set variables


							$url = JURI::root()."/". $path ."/".$path_extra."/" . $files[$s]; 
							$file = array( 'file' => $org_file, 'file_name' => $files[$s], 'url' => $url,'type' => $type );
							$url       = $file['url'];
							$type      = $file['type'];
							$file_name = $file['file_name'];
							$file      = $file['file'];
							$title     = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
							//$content   = '';
							$file_meta = array();
							// Use image exif/iptc data for title and caption defaults if possible
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
								'alias'     => $title,
								'caption'   => $content,
								'item_id'   => $item_id,
								'default'   => $default,
								'published' => 1,
								'file_name' => $file_name,
								'type'      => 'download',
								'mime_type' => $type,
								'file_meta' => array( 'file_meta' => $file_meta )
							);

							// Create thumbnails and other meta data
							PagoImageHandlerHelper::generate_image_metadata( $file, $data, $params );

							// Serialize meta data for storage
							$data['file_meta'] = serialize( $data['file_meta'] );
							$dispatcher->trigger('files_upload_before_store', array($data));
							$row = $this->getTable('files');

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
							elseif ( $row->introtext && $row->fulltext ) 
							{
								$row->fulltext = $row->introtext . ' ' . $row->fulltext;
							}
							unset( $row->introtext );

							if (!$row->id)
							{
								$row->ordering = $row->getNextOrder("`item_id` = {$item_id} AND `type` = '$row->type'");
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
	
	
	public function importAttributeCombo($post, $rawdata)
	{
		$db = JFactory::getDbo();
		$sql = "SELECT id FROM #__pago_items WHERE sku='".$rawdata['Parent SKU']."'";
		$db -> setQuery($sql);
		$prdItemId = $db -> LoadResult();
		$attribVarName = array();

		if($prdItemId)
		{
			$attribVarName = array();
			if($rawdata['Attribute1Name'] != '')
			{
				$Attribute1Name = $rawdata['Attribute1Name'];
				$name = explode ("|", $rawdata['Attribute1Value']);
				
				$idArr = $this -> getAttribId($Attribute1Name, $prdItemId, $name[0]);
				$attribId = $idArr[0] -> attrib_id;
				$optId = $idArr[0] -> opt_id;
				if($attribId)
				{
					$attribVarName[] = $name[0];
					$variantItemId = $this -> setAttribCombo($attribId, $prdItemId, $rawdata, $name[0], $optId);	
				}
			}
			
			if($rawdata['Attribute2Name'] != '')
			{
				$Attribute1Name = $rawdata['Attribute2Name'];
				$name = explode ("|", $rawdata['Attribute2Value']);
				$idArr = $this -> getAttribId($Attribute1Name, $prdItemId, $name[0]);
				$attribId = $idArr[0] -> attrib_id;
				$optId = $idArr[0] -> opt_id;
				
				if($attribId)
				{
					$attribVarName[] = $name[0];
					$variantItemId = $this -> setAttribCombo($attribId, $prdItemId, $rawdata, $name[0], $optId);	
				}
			}
			
			if($rawdata['Attribute3Name'] != '')
			{
				$Attribute1Name = $rawdata['Attribute3Name'];
				$name = explode ("|",$rawdata['Attribute3Value']);
				$idArr = $this -> getAttribId($Attribute1Name, $prdItemId, $name[0]);
				$attribId = $idArr[0] -> attrib_id;
				$optId = $idArr[0] -> opt_id;
				
				if($attribId)
				{
					$attribVarName[] = $name[0];
					$variantItemId = $this -> setAttribCombo($attribId, $prdItemId, $rawdata, $name[0], $optId);	
				}
			}
			
			if($rawdata['Attribute4Name'] != '')
			{
				$Attribute1Name = $rawdata['Attribute4Name'];
				$name = explode ("|",$rawdata['Attribute4Value']);
				$idArr = $this -> getAttribId($Attribute1Name, $prdItemId, $name[0]);
				$attribId = $idArr[0] -> attrib_id;
				$optId = $idArr[0] -> opt_id;
				
				if($attribId)
				{
					$attribVarName[] = $name[0];
					$variantItemId = $this -> setAttribCombo($attribId, $prdItemId, $rawdata, $name[0], $optId);	
				}
			}
			
			if($rawdata['Attribute5Name'] != '')
			{
				$Attribute1Name = $rawdata['Attribute5Name'];
				$name = explode ("|",$rawdata['Attribute5Value']);
				$idArr = $this -> getAttribId($Attribute1Name, $prdItemId, $name[0]);
				$attribId = $idArr[0] -> attrib_id;
				$optId = $idArr[0] -> opt_id;
				
				if($attribId)
				{
					$attribVarName[] = $name[0];
					$variantItemId = $this -> setAttribCombo($attribId, $prdItemId, $rawdata, $name[0], $optId);	
				}
			}
			
			if($rawdata['Attribute6Name'] != '')
			{
				$Attribute1Name = $rawdata['Attribute6Name'];
				$name = explode ("|",$rawdata['Attribute6Value']);
				$idArr = $this -> getAttribId($Attribute1Name, $prdItemId, $name[0]);
				$attribId = $idArr[0] -> attrib_id;
				$optId = $idArr[0] -> opt_id;
				
				if($attribId)
				{
					$attribVarName[] = $name[0];
					$variantItemId = $this -> setAttribCombo($attribId, $prdItemId, $rawdata, $name[0], $optId);	
				}
			}
			
			if(count($attribVarName) > 0)
			{
				$attribVarName = implode(" ", $attribVarName);
				
				$db = JFactory::getDbo();
				$queryVari = "UPDATE #__pago_product_varation SET name='" . $attribVarName . "' WHERE `id`='" . $variantItemId . "' ";
				$db->setQuery($queryVari);
				$db->Query();

				if($variantItemId)
				{
					$this->importVarinatImages($prdItemId, $variantItemId, $rawdata['Images']);
				}
			}
		}
	}
	
	public function setAttribCombo($attribId, $prdItemId, $rawdata, $name, $optId)
	{
		$db = JFactory::getDbo();
		$sql = "SELECT id FROM #__pago_product_varation WHERE sku='" . 	$rawdata['SKU'] ."'" ;
		$db -> setQuery($sql);
		$variantItemId = $db -> LoadResult();

		if($variantItemId)
		{
			$queryVari = "UPDATE #__pago_product_varation SET price='" . $rawdata['Price'] . "', qty='" . $rawdata['Quantity'] . "'  WHERE `id`='" . $variantItemId . "' ";
			$db->setQuery($queryVari);
			$db->Query();
			
			$sql = "SELECT varation_id FROM #__pago_product_varation_rel WHERE varation_id='" . 	$variantItemId . "' AND item_id='" . $prdItemId . "' AND attr_id='" . $attribId . "' AND opt_id=" .  $optId;
			$db -> setQuery($sql);
			$checkVar = $db -> LoadResult();
			
			if($checkVar)
			{
				$queryOpt = "UPDATE #__pago_product_varation_rel SET item_id='" . $prdItemId . "', attr_id='" . $attribId . "', opt_id='" . $optId . "'  WHERE `varation_id`='" . $checkVar . "' ";
				$db->setQuery($queryOpt);
				$db->Query();
			}
			else
			{
				$queryOpt = "INSERT INTO #__pago_product_varation_rel (`varation_id`, `item_id`, `attr_id`, `opt_id`) VALUES ('" . $variantItemId . "', '" .$prdItemId . "', '" . $attribId . "', '" . $optId . "')";
				 $db->setQuery($queryOpt);
				 $db->Query(); 
			}
			
		
		}
		else
		{
			 $queryVari = "INSERT INTO #__pago_product_varation (`item_id`, `name`, `price`, `qty`, `sku`, `var_enable`, `published`) VALUES ('" . $prdItemId . "', '" .$name . "', '" . $rawdata['Price'] . "', '" . $rawdata['Quantity'] . "', '" . $rawdata['SKU'] . "', '1', '1')";
			 $db->setQuery($queryVari);
			 $db->Query(); 
			 
			 $qu = "SELECT LAST_INSERT_ID()";
			 $db->setQuery($qu);
			 $variantItemId = $varId = $db->loadResult();
			 
			 $queryVari = "INSERT INTO #__pago_product_varation_rel (`varation_id`, `item_id`, `attr_id`, `opt_id`) VALUES ('" . $varId . "', '" .$prdItemId . "', '" . $attribId . "', '" . $optId . "')";
			 $db->setQuery($queryVari);
			 $db->Query(); 
		}
		
		return $variantItemId;
	}
	
	public function getAttribId($attribName, $itemId, $attVal)
	{
		
		$attribName = explode('|', $attribName);
		$attName = $attribName[0];
		$attType = $attribName[1]; 
		
		
		if($attType != '')
		{
			if($attType == 'color')
			{
				$attTypeVal = 0;
			}
			
			if($attType == 'size')
			{
				$attTypeVal = 1;
			}
			
			if($attType == 'material')
			{
				$attTypeVal = 2;
			}
			
			if($attType == 'custom')
			{
				$attTypeVal = 3;
			}
		}
		else
		{
			$attType == 'custom';
			$attTypeVal = 3;
		}
		
		$db = JFactory::getDbo();
		$sql = "SELECT at.id As attrib_id, ao.id AS opt_id FROM #__pago_attr As at LEFT JOIN #__pago_attr_opts as ao ON at.id=ao.attr_id WHERE at.name='" . 	$attName . "' AND at.type='" . $attTypeVal . "' AND at.for_item='" . $itemId . "' AND ao.name='" . $attVal . "'" ;
		$db -> setQuery($sql);
		$idArr = $db -> loadObjectList();

		if(count($idArr) > 0)
		{
			$for_item =$idArr[0]->for_item;
			$attr_id =$idArr[0]->attrib_id;

			if(!$for_item)
			{
				// insert item attr assign table
				// check if item is assigned to ietm or not
				$sel_attr_assign = "SELECT assign_items FROM #__pago_attr_assign WHERE attribut_id ='".$attr_id."' and assign_type=1";
				$db -> setQuery($sel_attr_assign);
				$idItems = $db -> loadObjectList();
				if(count($idItems) > 0 )
				{
					foreach($idItems as $idItem)
					{
						$assignItems = json_decode($idItem->assign_items);
						$assignItems[count($assignItems)]->id =$itemId;
						$uniqueAssignItems = array_unique( $assignItems, SORT_REGULAR );
						$encoded_assing_items = json_encode($uniqueAssignItems); 
						$queryVari = "UPDATE #__pago_attr_assign SET assign_items ='". $encoded_assing_items . "' WHERE attribut_id ='".$attr_id."' and assign_type=1";
					 	$db->setQuery($queryVari);
					 	$db->Query();
					 	return $idArr;
					}
				}
				else
				{
					// insert 
					$assignItems = array();
					$assignItems[0]->id =$itemId;
					$encoded_assing_items = json_encode($assignItems);

					$query_attrib = "INSERT INTO #__pago_attr_assign (`attribut_id`, `assign_type`, `assign_items`) VALUES ('" . $attr_id . "', '1', '" . $encoded_assing_items . "')";
					$db->setQuery($query_attrib);
					$db->Query();

					return $idArr;
				}
			}

		}

		
		
	}
	
	public function setAttributeValue($attribValArr, $itemId, $attribArray)
	{
		$attribType = $attribArray[1];
		$attribId = $attribArray[0];

		for($d = 0;$d < count($attribValArr); $d++)
		{
			$attribVal = explode('|', $attribValArr[$d]);
			$att_name = '';
			$att_color = '';
			$att_price = '';
			
			if($attribVal[0] != '')
			{
				$att_name = stripslashes($attribVal[0]);
			}
			
			if($attribVal[1] != '')
			{
				$att_color = "#".$attribVal[1];
			}
			
			if($attribVal[2] != '')
			{
				$att_price = (float)$attribVal[2];
			}
			
			$db = JFactory::getDbo();
			$sql = "SELECT id FROM #__pago_attr_opts WHERE name='" . 	$att_name . "' AND type='" . $attribType . "' AND for_item='" . $itemId . "' AND attr_id=" .  $attribId;
			$db -> setQuery($sql);
			$attribOptId = $db -> LoadResult();
			
			if(empty($attribOptId))
			{	
				 $query_attrib = "INSERT INTO #__pago_attr_opts (`name`, `type`, `for_item`, `color`, `price_sum`, attr_id) VALUES ('" . $att_name . "', '" . $attribType . "', '" . $itemId . "', '" . $att_color . "', '" . $att_price . "', '" . $attribId . "')";
				$db->setQuery($query_attrib);
				$db->Query();
			}
			else
			{
				$query_attrib = "UPDATE #__pago_attr_opts SET color='" . $att_color . "', price_sum='" . $att_price . "'  WHERE `id`='" . $attribOptId . "' ";
				$db->setQuery($query_attrib);
				$db->Query();
			}
			
		}
	}
	
	public function setAttributeName($attribName, $itemId)
	{
		$attribName = explode('|', $attribName);
		$attName = addslashes($attribName[0]);
		$attType = addslashes($attribName[1]);
		
		if($attType != '')
		{
			if($attType == 'color')
			{
				$attTypeVal = 0;
			}
			
			if($attType == 'size')
			{
				$attTypeVal = 1;
			}
			
			if($attType == 'material')
			{
				$attTypeVal = 2;
			}
			
			if($attType == 'custom')
			{
				$attTypeVal = 3;
			}
		}
		else
		{
			$attType = 'custom';
			$attTypeVal = 3;
		}
		
		$db = JFactory::getDbo();
		$sql = "SELECT id FROM #__pago_attr WHERE name='" . 	$attName . "' AND type='" . $attTypeVal . "' AND for_item=" . $itemId ;
		$db -> setQuery($sql);
		$attribId = $db -> LoadResult();
		
		if(empty($attribId))
		{	
			$query_attrib = "INSERT INTO #__pago_attr (`name`, `type`, `for_item`) VALUES ('" . $attName . "', '" . $attTypeVal . "', '" . $itemId . "')";
			$db->setQuery($query_attrib);
			$db->Query();
			$qu = "SELECT LAST_INSERT_ID()";
			$db->setQuery($qu);
			$attribId = $db->loadResult();
		}
		$attrib = array();
		$attrib[] = $attribId;
		$attrib[] = $attTypeVal;
		return $attrib;
	}

	public function importVarinatImages($itemId, $variantItemId,$csvimg)
	{
		jimport('joomla.filesystem.file');
		Pago::load_helpers('imagehandler');
		$params = Pago::get_instance('config')->get();
		$total_imgs = explode("|", $csvimg);
		$img_count = count($total_imgs);
		// Check for image count loop start
		if ($img_count > 0)
		{
			// Image check for loop start
			for ($l = 0; $l < $img_count; $l++)
			{
				$csv_img_name = explode(",", $total_imgs[$l]);
				$title = '';
				$content = '';

				if ($csv_img_name[1] != '')
				{
					$title = addslashes($csv_img_name[1]);
					//generate image name
					$ext = end(explode('.', $title));
				    $ext = substr(strrchr($title, '.'), 1);
				    $ext = substr($title, strrpos($title, '.') + 1);
				    $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $title);

					$arr = explode(' ',microtime());
					$newfilename = $arr[0] + $arr[1]+rand(1,1000);
					$newfilename =	str_replace('.','',$newfilename);
					$newfilename = $newfilename.".".$ext;
				}

				if ($csv_img_name[2] != '')
				{
					$content = addslashes($csv_img_name[2]);
				}
				
				$dir = JPATH_ROOT . "/tmp/pago_images/" . $csv_img_name[0];
				$src_folder = $dir . "/" . $csv_img_name[1];
				if(is_file($src_folder))
				{
					if (!is_dir(JPATH_SITE . "/media/pago/product_variation/" . $variantItemId))
					{
						mkdir(JPATH_SITE . "/media/pago/product_variation/" . $variantItemId, 0755);
					}
					
					$dest_folder = JPATH_SITE . "/media/pago/product_variation/" . $variantItemId . "/" . $newfilename;
					if(copy($src_folder, $dest_folder))
					{
						PagoImageHandlerHelper::generate_attribute_image($dest_folder);
					}
					
				}
			}
		}
	}

	public function ImportItemImages($itemId, $csvimg, $external_URL= false)
	{
		$db = JFactory::getDBO();
		$qu = "SELECT primary_category FROM #__pago_items WHERE `id`='" . $itemId . "' ";
		$db->setQuery($qu);
	    $itemCatId = $db->loadResult();
	    $category = Pago::get_instance( 'categoriesi' )->get( $itemCatId );

		jimport('joomla.filesystem.file');
		Pago::load_helpers('imagehandler');
		$dispatcher = KDispatcher::getInstance();
		$params = Pago::get_instance('config')->get();
		if($csvimg != "")
		{
			$total_imgs = explode("|", $csvimg);
			$img_count = count($total_imgs);
			if($img_count <= 0)
			{
				$total_imgs[] = $csvimg;
				$img_count = count($total_imgs);
			}
		}
		
		
		// Check for image count loop start
		if ($img_count > 0)
		{
			// Image check for loop start
			for ($l = 0; $l < $img_count; $l++)
			{
				$title =  pathinfo($total_imgs[$l],PATHINFO_FILENAME);
                $content = $title;
				
                if($external_URL)
                {
                	$dir = $total_imgs[$l];
                	$filename= $file = basename($total_imgs[$l]);
                }
                else
                {
                	$dir = JPATH_ROOT . "/tmp/pago_images/" . $total_imgs[$l];
                	$filename = $total_imgs[$l];
                }
				
				$src_folder = $dir;
				$uploadFiles['name'] = $filename;
			
				$path = trim(
					$params->get( 'media.images_file_path', 'media' .DS. 'pago' ), DS
				);
				$path_extra = 'items/'.$itemCatId;
				JPluginHelper::importPlugin( 'pago_products' );

				if($src_folder)
				{
					$result = array();
					$dispatcher->trigger(
						'image_upload_override',
						array( &$result, $category, '', $uploadFiles ,'images')
					);
					if(isset($result['path']) && $result['path'] != "")
					{
						$path_extra = $result['path'];
					}

					if (!is_dir(JPATH_SITE .$path))
					{
						mkdir(JPATH_SITE ."/".$path."/".$path_extra, 0755);
					}

					$uploads = PagoImageHandlerHelper::upload_dir( $path );
					$config = Pago::get_instance( 'config' )->get();
					$images_use_unique_name   = $config->get( 'media.images_use_unique_name', 1 );
					$images_add_suffix_image   = $config->get( 'media.images_add_suffix_image', 1 );

					$name_prefix = '';

					if($images_add_suffix_image)
					{
						$name_prefix = 'item-' .$itemId. '-';
					}
					if($images_use_unique_name)
					{
						$filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix.$filename );
					}
					else
					{
						$filename = $filename;
					}
					$dest_folder = JPATH_SITE ."/". $path ."/".$path_extra."/".$filename;
					$files[] = $filename;
					
					copy($src_folder, $dest_folder);
				}

				// Files For Loop start
				for ($s = 0; $s < count($files); $s++)
				{
					// Check for matching Image 
					if ($files[$s] != "" && $files[$s] == $filename)
					{
						$item_id = $itemId;
						$org_file = JPATH_SITE ."/". $path ."/".$path_extra."/" . $files[$s];

						$existing_image = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $item_id . "' AND `title`='".$filename."'";
						$db->setQuery($existing_image);
						$existing_imageRs = $db->loadResult();

						if($existing_imageRs == 0)
						{
							$qu_img = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $item_id . "' AND `default`=1";
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

							// Set variables
							
							$url = JURI::root()."/". $path ."/".$path_extra."/" . $files[$s]; 
							$file = array( 'file' => $org_file, 'file_name' => $files[$s], 'url' => $url,'type' => $type );

							$url       = $file['url'];
							$type      = $file['type'];
							$file_name = $file['file_name'];
							$file      = $file['file'];
							$title     = preg_replace( '/\.[^.]+$/', '', basename( $file ) );
							//$content   = '';
							$file_meta = array();

							// Use image exif/iptc data for title and caption defaults if possible
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
								'alias'     => $title,
								'caption'   => $content,
								'item_id'   => $item_id,
								'default'   => $default,
								'published' => 1,
								'file_name' => $file_name,
								'type'      => 'images',
								'mime_type' => $type,
								'file_meta' => array( 'file_meta' => $file_meta )
							);

							// Create thumbnails and other meta data
							PagoImageHandlerHelper::generate_image_metadata( $file, $data, $params );

							// Serialize meta data for storage
							$data['file_meta'] = serialize( $data['file_meta'] );

							$dispatcher->trigger('files_upload_before_store', array($data));
							$row = $this->getTable('files');

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

	public function ImportCategoryImages($categoryId, $csvimg)
	{
		$db = JFactory::getDBO();
	    $itemCatId = $categoryId;
		jimport('joomla.filesystem.file');
		Pago::load_helpers('imagehandler');
		$dispatcher = KDispatcher::getInstance();
		$params = Pago::get_instance('config')->get();
		if($csvimg != "")
		{
			$total_imgs = explode("|", $csvimg);
			$img_count = count($total_imgs);
			if($img_count <= 0)
			{
				$total_imgs[] = $csvimg;
				$img_count = count($total_imgs);
			}
		}
		
		// Check for image count loop start
		if ($img_count > 0)
		{
			// Image check for loop start
			for ($l = 0; $l < $img_count; $l++)
			{
				$title = pathinfo($total_imgs[$l],PATHINFO_FILENAME);
				$content = $title;

				$filename = $total_imgs[$l];
				$dir = JPATH_ROOT . "/tmp/pago_images/" . $total_imgs[$l];
				$src_folder = $dir;
				if(is_file($src_folder))
				{
					if (!is_dir(JPATH_SITE . "/media/pago/category/" . $categoryId))
					{
						mkdir(JPATH_SITE . "/media/pago/category/" . $categoryId, 0755);
					}
					$uploads = PagoImageHandlerHelper::upload_dir( "/media/pago/category/" . $categoryId );
					$name_prefix = '';
					$filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix.$filename);
					$dest_folder = JPATH_SITE . "/media/pago/category/" . $categoryId . "/" . $filename;
					$files[] = $filename;

					copy($src_folder, $dest_folder);

					
				}
				
				
				// Files For Loop start
				for ($s = 0; $s < count($files); $s++)
				{
					// Check for matching Image 
					
					if ($files[$s] != "" && $files[$s] == $filename)
					{
						$item_id = $categoryId;
						$org_file = JPATH_SITE . "/media/pago/category/" . $categoryId . "/" . $files[$s];
						
						$existing_image = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $categoryId . "' AND `title`='".$filename."' and type='category'";
						$db->setQuery($existing_image);
						$existing_imageRs = $db->loadResult();
						
					
						if($existing_imageRs == 0 )
						{

							$qu_img = "SELECT count(*) FROM #__pago_files WHERE `item_id`='" . $categoryId . "' AND `default`=1";
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

						//	if ( in_array( $upload_type, array( 'images', 'user' ) ) ) {
								// Use image exif/iptc data for title and caption defaults if possible
								if ( $file_meta = @PagoImageHandlerHelper::read_image_metadata( $file ) ) {
									if ( trim( $file_meta['title'] ) ) {
										$title = $file_meta['title'];
									}
									if ( trim( $file_meta['caption'] ) ) {
										$content = $file_meta['caption'];
									}
								}
						//	}
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

							//if ( in_array( $upload_type, array( 'images', 'user', 'category' ) ) ) {
								// Create thumbnails and other meta data
								PagoImageHandlerHelper::generate_image_metadata( $file, $data, $params );

								// Serialize meta data for storage
								$data['file_meta'] = serialize( $data['file_meta'] );
						//	}

						
							$dispatcher->trigger('files_upload_before_store', array($data));
							$row = $this->getTable('files');

							if (!$row->bind($data))
							{
								$this->_db->getErrorMsg(); 
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
}
