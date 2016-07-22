<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die();

class NavigationHelper
{
	/**
	 * Build urls for categories or items
	 *
	 *
	 * @param $type string category || item
	 * @param $id int cateogry id || item id
	 * @param $relative boolean If false, return absolute url, else return relative url. Default is true
	 * @param $extra array key=>value pairs of extra params to append to url
	 *
	 * @return $url string
	 */
	public function build_url( $type, $id, $relative = true, $extra = array(), $needItemId = true, $itemId = false )
	{
		$uri = JURI::getInstance();

		if (!($type == 'category' || $type == 'item') ) {
			return false;
		}

		$url = '';

		// build category or item url
		if ( $type === 'category' ) {
			$url .= 'index.php?option=com_pago&view=category&cid=' . (int) $id;
		} else if ( $type === 'item' ) {
			$url .= 'index.php?option=com_pago&view=item&id='. (int) $id;
		}

		// add extra params to the url
		if ( is_array( $extra ) && count( $extra ) > 0 ) {
			$url .= '&' . http_build_query( $extra );
		}

		// try to find item id not trying really hard
		$item_id = JFactory::getApplication()->input->getInt( 'Itemid', null );

		if ( $item_id !== null && $needItemId) {
			$url .= '&Itemid=' . (int) $item_id;
		}


		$url = JRoute::_($url);
		if($itemId  && $needItemId){
			if($itemId != substr($url, strpos($url, "Itemid=")+7)){
				return str_replace(substr($url, strpos($url, "Itemid=")+7),$itemId,$url);
			}

		}

		if(!$relative) $url= $uri->toString(array('scheme', 'host', 'port')) . $url;
		//$url = str_replace('&amp;', '&', $url);
		//echo $url;die;
		return $url;
	}

	public function build_hidden( $extra = array() )
	{
		$html = '<input type="hidden" name="option" value="com_pago" />';
	}

	public function getItemid($prd_id, $cat_id, $language = "")
	{
		$db = JFactory::getDBO();
		
		$lang_query = "";
		
		if($language!="")
		{
			$lang_query = " AND language='".$language."' ";
		}

		if($cat_id)
		{
			
			
			$sql = "SELECT id, params FROM #__menu WHERE published=1 AND `link` LIKE '%com_pago%' AND (`link` LIKE '%view=category%' OR '%view=frontpage%') ".$lang_query." ORDER BY 'ordering'";
			$db->setQuery($sql);
			$catParams = $db->loadObjectList();
			
			if (count($catParams) > 0)
			{
				for ($f = 0; $f < count($catParams); $f++)
				{
					$catParamsArray = json_decode($catParams[$f]->params);
					
					if (isset($catParamsArray->cid) && count($catParamsArray->cid) > 0)
					{
						for ($r = 0; $r < count($catParamsArray->cid); $r++)
						{
							if ($catParamsArray->cid[$r] == $cat_id)
							{
								return $catParams[$f]->id;
							}
						}
					}
				}
			}
		
		
		}

		if( $prd_id ) {
			$sql = "SELECT category_id FROM #__pago_categories_items WHERE item_id = " . $prd_id;
			$db->setQuery($sql);
			$cats = $db->loadObjectList();

			for ($i = 0;$i < count($cats);$i++)
			{
				$cat = $cats[$i];
			    $sql = "SELECT id, params FROM #__menu WHERE published=1 AND `link` LIKE '%com_pago%' AND (`link` LIKE '%view=category%' OR '%view=frontpage%') ".$lang_query." ORDER BY 'ordering'";
				$db->setQuery($sql);
				$catParams = $db->loadObjectList();
				
				if (count($catParams) > 0)
				{
					for ($f = 0; $f < count($catParams); $f++)
					{
						$catParamsArray = json_decode($catParams[$f]->params);
						
						if (isset($catParamsArray->cid) && count($catParamsArray->cid) > 0)
						{
							for ($r = 0; $r < count($catParamsArray->cid); $r++)
							{
								if ($catParamsArray->cid[$r] == $cat->category_id)
								{
									return $catParams[$f]->id;
								}
							}
						}
					}
				}
			}
		}

		$sql = "SELECT id FROM #__menu WHERE published=1 AND `link` LIKE '%com_pago%' AND (`link` LIKE '%view=category%' OR '%view=frontpage%') ".$lang_query." ORDER BY 'ordering'";
		 $db->setQuery($sql);

		if ($Itemid = $db->loadResult())
		{
			return $Itemid;
		}

		$reqItemId = JFactory::getApplication()->input->get('Itemid');

		if($reqItemId)
		{
			return $reqItemId;
		}
		else
		{
			if($cat_id)
			{
				Pago::load_helpers( 'categories' );

				$catnip = new catnip( 'pago_categories', 'com_pago', 'category', 'cid', false );

				$parents = array();
				$catnip -> get_parent_category_ids($cat_id, $parents);

				if(count($parents) > 0)
				{
					for($s = 0; $s < count($parents); $s++)
					{
						$sql = "SELECT id, params FROM #__menu WHERE published=1 AND `link` LIKE '%com_pago%' AND (`link` LIKE '%view=category%' OR '%view=frontpage%')  ".$lang_query." ORDER BY 'ordering'";
						$db->setQuery($sql);
						$catParams = $db->loadObjectList();
						
						if (count($catParams) > 0)
						{
							for ($f = 0; $f < count($catParams); $f++)
							{
								$catParamsArray = json_decode($catParams[$f]->params);
								
								if (isset($catParamsArray->cid) && count($catParamsArray->cid) > 0)
								{
									for ($r = 0; $r < count($catParamsArray->cid); $r++)
									{
										if ($catParamsArray->cid[$r] == $parents[$s])
										{
											return $catParams[$f]->id;
										}
									}
								}
							}
						}
					}

					$sql = "SELECT id, params FROM #__menu WHERE published=1 AND `link` LIKE '%com_pago%' ".$lang_query." ORDER BY 'ordering'";
					$db->setQuery($sql);
					$pago_Params = $db->loadObjectList();
					if (count($pago_Params) > 0)
					{
						return $pago_Params[0]->id;
					}
				}
				else
				{
					$sql = "SELECT id, params FROM #__menu WHERE published=1 AND `link` LIKE '%com_pago%' ".$lang_query." ORDER BY 'ordering'";
					$db->setQuery($sql);
					$pago_Params = $db->loadObjectList();
					if (count($pago_Params) > 0)
					{
						return $pago_Params[0]->id;
					} 
					else 
					{
						$sql = "SELECT id FROM #__menu WHERE home = 1 ".$lang_query."";
						$db->setQuery($sql);
						$homeId = $db->loadResult();
						return $homeId;
					}
				}
			}
			else
			{
					$sql = "SELECT id, params FROM #__menu WHERE published=1 AND `link` LIKE '%com_pago%' ".$lang_query." ORDER BY 'ordering'";
					$db->setQuery($sql);
					$pago_Params = $db->loadObjectList();
					if (count($pago_Params) > 0)
					{
						return $pago_Params[0]->id;
					} 
					else 
					{
						$sql = "SELECT id FROM #__menu WHERE home = 1 ".$lang_query."";
						$db->setQuery($sql);
						$homeId = $db->loadResult();
						return $homeId;
					}

			}
		}
	}

	public function generateBreadcrumPath($cid)
	{
		$app     = JFactory::getApplication();
		$pathway       = $app->getPathway();
		$view          = JFactory::getApplication()->input->get('view');
		$Itemid        = JFactory::getApplication()->input->getInt('Itemid');
		$catid         = JFactory::getApplication()->input->get('cid');//, array(0), 'array'

		$id         = JFactory::getApplication()->input->getInt('id');
		$customPathWay = array();

		$pathArray = $pathway->getPathWay();
		$totalcount = count($pathArray);

		for ($j = 0; $j < $totalcount; $j++)
		{
			unset($pathArray[$j]);
		}

		$pathway->setPathWay($pathArray);

		switch ($view)
		{
			case "category":
			case "item":

				if ($cid != 0)
				{
					$categoryList= array_reverse($this->getCategoryNavList($cid));
					$customPathWay = array_merge($customPathWay, $this->getBreadcrumbPath($categoryList));
				}

				if($view == 'item' && $id != 0)
				{
					$itemDetail = $this->getItemDetail($id);
					$item  = new stdClass;
					$item->name      = $itemDetail->name;
					$item->link      = "";
					$customPathWay[] = $item;

				}
				break;
		}

		if (count($customPathWay) > 0)
		{
			$customPathWay[count($customPathWay) - 1]->link = '';

			for ($j = 0; $j < count($customPathWay); $j++)
			{
				$pathway->addItem($customPathWay[$j]->name, $customPathWay[$j]->link);
			}
		}

	}

	public function getItemDetail($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT id,name FROM #__pago_items WHERE id = " . (int) $id . "";
		$db->setQuery($query);
		$res = $db->loadObject();

		return $res;
	}

	public function getCategoryNavList($cid)
	{
		static $i = 0;
		static $categoryList= array();

		$category_parent_id = $this->getParentCategory($cid);
		$catItemid = $this ->getItemid(0, $cid);
		$catDetail = $this->getCatDetails($cid);

		if (count($catDetail) > 0)
		{
			if ($catItemid != "")
			{
				$Itemid = $catItemid;
			}
			else
			{
				$Itemid = JFactory::getApplication()->input->get('Itemid');
			}

			$categoryList[$i]['cid']   = $catDetail->id;
			$categoryList[$i]['category_name'] = $catDetail->name;
			$categoryList[$i]['catItemid']     = $Itemid;
		}

		if ($category_parent_id != 0)
		{
			$i++;
			array_merge($categoryList, $this->getCategoryNavList($category_parent_id));
		}

		return $categoryList;
	}

	public function getCatDetails($cid)
	{
		$db = JFactory::getDBO();
		$query = "SELECT id,name FROM #__pago_categoriesi WHERE id = " . (int) $cid . " AND parent_id!=0 ";
		$db->setQuery($query);
		$res = $db->loadObject();

		return $res;
	}

	public function getParentCategory($id = 0)
	{
		$db = JFactory::getDBO();
		$query = "SELECT parent_id FROM #__pago_categoriesi WHERE id = " . (int) $id . " ";
		$db->setQuery($query);
		$res = $db->loadResult();

		return $res;
	}

	public function getBreadcrumbPath($category = array())
	{
		$pathway_items = array();

		for ($i = 0; $i < count($category); $i++)
		{
			$item            = new stdClass;
			$item->name      = $category[$i]['category_name'];
			$item->link      = JRoute::_('index.php?option=com_pago&view=category&cid=' . $category[$i]['cid'] . '&Itemid=' . $category[$i]['catItemid']);
			$pathway_items[] = $item;
		}

		return $pathway_items;
	}

	public function getSortByList()
	{
		$sort_data           = array();
		$sort_data[0] = new stdClass;
		$sort_data[0]->value = "alpha";
		$sort_data[0]->text  = JText::_('COM_PAGO_ALPHA');

		$sort_data[1] = new stdClass;
		$sort_data[1]->value = "price-min";
		$sort_data[1]->text  = JText::_('COM_PAGO_PRICE_MIN');

		$sort_data[2] = new stdClass;
		$sort_data[2]->value = "price-max";
		$sort_data[2]->text  = JText::_('COM_PAGO_PRICE_MAX');

		$sort_data[3] = new stdClass;
		$sort_data[3]->value = "featured";
		$sort_data[3]->text  = JText::_('COM_PAGO_FEATURED');

		$sort_data[4] = new stdClass;
		$sort_data[4]->value = "latest";
		$sort_data[4]->text  = JText::_('COM_PAGO_LATEST');

		return $sort_data;
	}

	public function getMediaInfo($itemid)
	{
		$db = JFactory::getDBO();
		$query = "SELECT id,title,alias,item_id,type,file_name,access FROM #__pago_files WHERE item_id=" . $itemid . " AND type='download'";
		$db->setQuery($query);
		return $downloadRes = $db->loadObjectList();
	}
	
	public function getSortByProductsList()
	{
		$sort_data           = array();
		
		$sort_data[0] = new stdClass;
		$sort_data[0]->value = urlencode("items.name ASC");
		$sort_data[0]->text  = JText::_('COM_PAGO_NAME_ASC');

		$sort_data[1] = new stdClass;
		$sort_data[1]->value = urlencode("items.id DESC");
		$sort_data[1]->text  = JText::_('COM_PAGO_NEWEST');

		$sort_data[2] = new stdClass;
		$sort_data[2]->value = urlencode("items.price ASC");
		$sort_data[2]->text  = JText::_('COM_PAGO_PRICE_MIN');
		
		$sort_data[3] = new stdClass;
		$sort_data[3]->value = urlencode("items.price DESC");
		$sort_data[3]->text  = JText::_('COM_PAGO_PRICE_MAX');

		$sort_data[4] = new stdClass;
		$sort_data[4]->value = urlencode("items.ordering ASC");
		$sort_data[4]->text  = JText::_('COM_PAGO_ORDERING');

		return $sort_data;
	}

}
