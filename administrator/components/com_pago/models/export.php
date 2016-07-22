<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelExport extends JModelList
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
		$exportVar = JFactory::getApplication()->input->get('export');

		if (!$exportVar)
		{
			$app = JFactory::getApplication();
			$app->Redirect("index.php?option=com_pago&view=export", JText::_("PAGO_PLEASE_SELECT_SECTION"));
		}

		/* Set the oago export filename */
		$exportfilename = 'pago_' . $exportVar . '.csv';

		/* Start output to the browser */
		if (preg_match('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "Opera";
		}
		elseif (preg_match('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		{
			$UserBrowser = "IE";
		}
		else
		{
			$UserBrowser = '';
		}

		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';

		/* Clean the buffer */
		ob_clean();

		header('Content-Type: ' . $mime_type);
		header('Content-Encoding: UTF-8');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

		if ($UserBrowser == 'IE')
		{
			header('Content-Disposition: inline; filename="' . $exportfilename . '"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			header('Content-Disposition: attachment; filename="' . $exportfilename . '"');
			header('Pragma: no-cache');
		}

		switch ($exportVar)
		{
			case 'categories':
				$this->loadPagoCategories();
				break;
			case 'items':
				$this->loadPagoItems();
				break;
			case 'customers':
				$this->loadPagoCustomers();
				break;
		}

		exit;
	}

	private function loadPagoItems()
	{
		$dispatcher = KDispatcher::getInstance();
		$cat_table = JTable::getInstance( 'categoriesi', 'Table' );
		$cat_array = JFactory::getApplication()->input->get('catname', '' , 'array');
		$cat_value = '';
		$where = '';

		if(is_array($cat_array) && count($cat_array) > 0)
		{
			for ($y = 0; $y < count($cat_array); $y++)
			{
				$childCats = $cat_table->getTree( $cat_array[$y] );
				foreach($childCats as $childCat)
				{
					$childCategories[] = $childCat->id;
				}
			}

			$finalCategories =  array_merge($cat_array, $childCategories);

			$cat_value = implode(",", $finalCategories);
		}

		$db = JFactory::getDbo();

		if ($cat_value != '')
		{
			$prd = "SELECT i.* FROM #__pago_items as i LEFT JOIN #__pago_categories_items as ci ON i.id = ci.item_id WHERE ci.category_id in (" . $cat_value . ") ORDER BY i.id";
		}
		else
		{
			$prd = "SELECT i.* FROM #__pago_items as i " . $where . "ORDER BY i.id";
		}

		$db->setQuery($prd);
		if (!($res = $db->LoadObjectList()))
		{
			return null;
		}

		

		$i = 0;

		if (count($res) > 0)
		{
			for ($k = 0; $k < count($res); $k++)
			{
				$sql = "SHOW TABLES LIKE '%items_cws_fields_values'";
				$db -> setQuery($sql);
				$is_exist = $db -> LoadResult();

				if ($k == 0)
				{
					echo "SKU,Item Name,Quantity,Published,Price,Tax Exempt,Shipping-Available-Date,Shipping-Free-Shipping,Shipping-Method,Shipping-Tax-Class,Item-Display,Category-Primary,Category-Secondary,Short Description,Long Description,Dimensions-Weight-Type,Dimensions-Length,Dimensions-Width,Dimensions-Height,Dimensions-Weight";

					if ($is_exist)
					{
						$custom_extra_fields_labels = $dispatcher->trigger('backend_item_extra_fields_label_export', array());
						echo $custom_extra_fields_labels[0];
					}
					echo ",Parent SKU,Attribute1Name,Attribute1Value,Attribute2Name,Attribute2Value,Attribute3Name,Attribute3Value,Attribute4Name,Attribute4Value,Attribute5Name,Attribute5Value,Attribute6Name,Attribute6Value,Images";

					echo "\r\n";
				}
				
				$cat_tree = '';
				$primary_cat = $this->getPrimaryCatTree($res[$k]->primary_category, $cat_tree);
				$primary_cat_arr = explode("#", $primary_cat);
				$val = array_pop($primary_cat_arr);
				$cat_tree_pri = implode("#", array_reverse($primary_cat_arr));
				$secondary_cat = $this->getSecondaryCatTree($res[$k]->id);
				$images = $this->getItemImages($res[$k]->id); 

				$content = str_replace("\n", "" , $res[$k]->content);
				$content = str_replace("\r", "" , $content);
				$content = str_replace('"', "'", $content);
				
				$description = str_replace("\n", "" , $res[$k]->description);
				$description = str_replace("\r", "" , $description);
				$description = str_replace('"', "'", $description);

				echo $res[$k]->sku . ',"' . $res[$k]->name . '",' . $res[$k]->qty . ',' . $res[$k]->published . ','. $res[$k]->price . ',' . $res[$k]->tax_exempt . ',,' . $res[$k]->free_shipping . ','. $res[$k]->shipping_methods . ',' . $res[$k]->pgtax_class_id . ',' . $res[$k]->visibility . ',"'. $cat_tree_pri .'","' . $secondary_cat .'","' . trim(preg_replace('/\s\s+/', ' ', $description)) . '","' . trim(preg_replace('/\s\s+/', ' ', $content)) . '",' . $res[$k]->unit_of_measure . ',' . $res[$k]->length . ',' . $res[$k]->width . ',' . $res[$k]->height . ',' . $res[$k]->weight . '"';
				
				
				//Extra Field Export Start
				$dispatcher->trigger('backend_item_extra_fields_export', array($is_exist, $res[$k]->sku));
				//Extra Field Export End
				
				// Attribute Start
				echo ",";
				$this -> exportItemAttribute($res[$k]->id);
				echo ',"' . $images.'"';
				echo "\n";
				// Attribute end
				// Variation Start
				$combinationRows = $this -> getAttribVarRow($res[$k]->id, $res[$k]->sku);
				
				if(count($combinationRows > 0))
				{
					for($g = 0; $g < count($combinationRows); $g++)
					{
						echo $combinationRows[$g]->sku . ',' . $res[$k]->name . ',' . $res[$k]->qty . ',' . $res[$k]->type . ',' . $res[$k]->published . ','. $combinationRows[$g]->price . ',' . $res[$k]->tax_exempt . ',,' . $res[$k]->free_shipping . ','. $res[$k]->shipping_methods . ',' . $res[$k]->pgtax_class_id . ',' . $res[$k]->visibility . ',"'. $cat_tree_pri .'","' . $secondary_cat .'","' . trim(preg_replace('/\s\s+/', ' ', $description)) . '","' . trim(preg_replace('/\s\s+/', ' ', $content)) . '",' . $res[$k]->unit_of_measure . ',' . $res[$k]->length . ',' . $res[$k]->width . ',' . $res[$k]->height . ',' . $res[$k]->weight . '"';
				
						//Extra Field Export Start
						$dispatcher = KDispatcher::getInstance();
						$dispatcher->trigger('backend_item_extra_fields_export', array($is_exist, $res[$k]->sku));
						//Extra Field Export End
						
						echo ',' .  $combinationRows[$g]->sku;
						$comboRow = $combinationRows[$g];
						$attribCombo = $this -> getAttribVarComboRow($comboRow);
						$this ->exportAttributeCombo($attribCombo);
						$varImages = $this ->exportAttributeComboImg($comboRow);
						echo ',"' . $varImages.'"';
						echo "\n";
					}
				}
				// Variation End
			}
		}

	}

	private function exportAttributeComboImg($comboRow)
	{
		$dir =  JPATH_ROOT . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'pago' . DIRECTORY_SEPARATOR . 'product_variation' . DIRECTORY_SEPARATOR . $comboRow->id;
		$img_str = '';
		
		if(is_dir($dir))
		{
		   $cdir = scandir($dir);
		   $result = array();
		   foreach ($cdir as $key => $value)
		   {
			  if (!in_array($value,array(".","..")))
			  {
				 if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
				 {
					$result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
				 }
				 else if($value!="." && $value!=".." && !is_dir($dir.DIRECTORY_SEPARATOR .$value) && $value!='fields.ini' && $value!='index.html' && !strpos($value, "-"))
				 {
					$result[] = $value;
				 }
			  }
		   }
		   
		   	if(count($result) > 0)
			{
			   for($j = 0;$j < count($result); $j++)
			   {
			   		$cap = explode(".", $result[$j]);
					$img_str .= "/," . $result[$j]  . "," . $cap[0] ;
					
					if($j < count($result)-1)
					{
					 $img_str .= "|";
					}
				}
			}
		}
		
		return $img_str;
	}
	
	private function exportAttributeCombo($attribCombo)
	{
		$db = JFactory::getDBO();

		for($k = 0; $k < 6 ; $k++)
		{
			$attrNameStr = ''; 
			$attType = '';
			
			
			if(count($attribCombo) > $k)
			{
				$attribArr = $attribCombo[$k];
				$attribOpts = "SELECT at.name AS attr_name,ao.type,ao.name as attr_value FROM #__pago_attr as at LEFT JOIN #__pago_attr_opts AS ao ON ao.attr_id=at.id WHERE ao.attr_id=" . $attribCombo[$k]->attr_id . " AND ao.id=" . $attribCombo[$k]->opt_id;
				$db -> setQuery($attribOpts);
				$attribOptsVals = $db->loadObject();//echo '<pre/>';print_r($attribOptsVals);exit;
				$attType = $this -> getAttributeType($attribOptsVals -> type);
				$attrNameStr = $attribOptsVals -> attr_name; 
				$attrValStr = '';
				$attrValStr = $attribOptsVals -> attr_value . "|";
				$finalAttrName = $attrNameStr . "|" . $attType;
				echo ',"' . $finalAttrName . '","' . $attrValStr .'"';	
			}
			else
			{
				echo ',,';
			}	
			
		}
		
	}
	
		
	private function getAttribVarComboRow($comboRow)
	{
		$db = JFactory::getDbo();
		$sql = "SELECT v.id ,pvr.attr_id,pvr.opt_id FROM #__pago_product_varation AS v LEFT JOIN #__pago_product_varation_rel AS pvr ON v.id=pvr.varation_id WHERE v.item_id='" . 	$comboRow->item_id ."' AND pvr.varation_id='" . $comboRow->id . "'";
		$db -> setQuery($sql);
		$varResult = $db -> loadObjectList();
		return $varResult;
		
	}
	
	private function getAttribVarRow($id, $sku)
	{
		$db = JFactory::getDbo();
		$sql = "SELECT id ,item_id,sku, name, price FROM #__pago_product_varation WHERE item_id='" . $id ."'" ;
		$db -> setQuery($sql);
		$varResult = $db -> loadObjectList();
		return $varResult;
	}
	
	private function exportItemAttribute($id)
	{
		$db = JFactory::getDBO();
		$attrib = "SELECT pa.name AS attr_name,pa.type,pa.id FROM #__pago_attr AS pa WHERE pa.for_item=" . $id;
		$db -> setQuery($attrib);
		$attribArray = $db->loadObjectList();
		
		for($k = 0; $k < 6 ; $k++)
		{
			$attrNameStr = ''; 
			$attType = '';
			//echo count($attribArray)." ";
			
			if(count($attribArray) > $k)
			{
				//echo $k."inloop<br/>";
				$attribArr = $attribArray[$k];
				$attribVal = "SELECT ao.name AS attr_value,ao.color,ao.price_sum FROM #__pago_attr_opts AS ao WHERE ao.attr_id=" . $attribArr->id;
				$db -> setQuery($attribVal);
				$attribVal = $db->loadObjectList();
				$attType = $this -> getAttributeType($attribArray[$k] -> type);
				$attrNameStr = $attribArray[$k] -> attr_name; 
				$attrValStr = '';
				
				for($s = 0; $s < count($attribVal); $s++)
				{
					$attrValStr .= $attribVal[$s] -> attr_value . "|" . str_replace("#", "", $attribVal[$s] -> color) . "|" . $attribVal[$s] -> price_sum;
					
					if($s < count($attribVal)-1)
					{
						$attrValStr .= "##";
					}
				}

				echo "," . $attrNameStr . "|" . $attType . "," . $attrValStr;
			}
			else
			{
				echo ",,";
			}	
		}
		
	}
	
	private function getAttributeType($attType)
	{
		if($attType == 0)
		{
			$attTypeVal = 'color';
		}
		
		if($attType == 1)
		{
			$attTypeVal = 'size';
		}
		
		if($attType == 2)
		{
			$attTypeVal = 'material';
		}
		
		if($attType == 3)
		{
			$attTypeVal = 'custom';
		}
		
		return $attTypeVal;
	}

	private function getSecondaryCatTree($id)
	{
		$db = JFactory::getDBO();
		$cat = "SELECT category_id FROM #__pago_categories_items where item_id=" . $id;
		$db->setQuery($cat);
		$res = $db->loadColumn();
		$cat_tree_secondary = '';

		if (count($res) > 0)
		{
			for ($i = 0; $i < count($res); $i++)
			{
				$tree_res = $this->getPrimaryCatTree($res[$i]);
				$sec_cat_arr = explode("#", $tree_res);
				$val = array_pop($sec_cat_arr);
				$cat_tree_secondary .= implode("#", array_reverse($sec_cat_arr));

				if ($i != count($res) - 1)
				{
					$cat_tree_secondary .= "|";
				}
			}
		}

		return $cat_tree_secondary;
	}
	
	private function getPrimaryCatTree($id,$cat_tree='')
	{
		$db = JFactory::getDBO();
		$cat = "SELECT id,parent_id,name FROM #__pago_categoriesi WHERE id=" . $id;
		$db->setQuery($cat);
		$res = $db->loadObjectList();
		$cat_tree = '';

		if (count($res) > 0)
		{
			$cat_tree .= $res[0]->name . "#";
			$cat_tree .= $this -> getPrimaryCatTree($res[0]->parent_id, $cat_tree);
		}
		else
		{
			return false;
		}

		return $cat_tree;
	}

	private function loadPagoCustomers()
	{
		$db = JFactory::getDbo();
		$cat = "SELECT ui.* FROM #__pago_user_info AS ui ORDER BY ui.id";
		$db->setQuery($cat);

		if (!($cus_res = $db->LoadObjectList()))
		{
			return null;
		}

		$i = 0;

		if (count($cus_res) > 0)
		{
			for ($k = 0; $k < count($cus_res); $k++)
			{
				$custrow = $cus_res[$k];
				$custrow = (array) $custrow;
				$custfields = count($custrow);

				if ($i == 0)
				{
					foreach ($custrow as $cid => $cvalue)
					{
						echo '"' . str_replace('"', '""', $cid) . '"';

						if ($i < ($custfields - 1))
						{
							echo ',';
						}

						$i++;
					}

					echo "\r\n";
				}

				$i = 0;

				foreach ($custrow as $cid => $cvalue)
				{
					$cvalue = str_replace("\n", "", $cvalue);
					$cvalue = str_replace("\r", "", $cvalue);
					echo '"' . str_replace('"', '""', $cvalue) . '"';

					if ($i < ($custfields - 1))
					{
						echo ',';
					}

					$i++;
				}

				echo "\r\n";
			}

			if (is_resource($cus_res))
			{
				mysql_free_result($cus_res);
			}
		}
	}

	private function loadPagoCategories()
	{
		$db = JFactory::getDbo();
		$cat = "SELECT c.* FROM #__pago_categoriesi c ORDER BY c.id";
		$db->setQuery($cat);

		if (!($res = $db->LoadObjectList()))
		{
			return null;
		}

		$i = 0;

		if (count($res) > 0)
		{
			for ($k = 0; $k < count($res); $k++)
			{
				$catrow = $res[$k];
				$catrow = (array) $catrow;
				$catfields = count($catrow);

				if ($i == 0)
				{
					foreach ($catrow as $cid => $cvalue)
					{
						echo '"' . str_replace('"', '""', $cid) . '"';

						if ($i < ($catfields - 1))
						{
							echo ',';
						}

						$i++;
					}

					echo "\r\n";
				}

				$i = 0;

				foreach ($catrow as $cid => $cvalue)
				{
					$cvalue = str_replace("\n", "", $cvalue);
					$cvalue = str_replace("\r", "", $cvalue);
					echo '"' . str_replace('"', '""', $cvalue) . '"';

					if ($i < ($catfields - 1))
					{
						echo ',';
					}

					$i++;
				}

				echo "\r\n";
			}

			if (is_resource($res))
			{
				mysql_free_result($res);
			}
		}
	}
	
    private function getItemImages($id)
	{
		$db = JFactory::getDBO();
		$img = "SELECT * FROM #__pago_files where item_id='" . $id . "' and type = 'images' ORDER BY id ASC";
		$db->setQuery($img);
		$img_res = $db->loadObjectList();
		$img_str = '';
		
		if(count($img_res) > 0)
		{
		   for($j = 0;$j < count($img_res); $j++)
		   {
				//$img_str .= "/," . $img_res[$j] -> file_name . "," . $img_res[$j] -> caption;
				$img_str .= $img_res[$j]->file_name;
				if($j < count($img_res)-1)
				{
				 $img_str .= "|";
				}
		 	}
		}

	  return $img_str; 
	}
}
