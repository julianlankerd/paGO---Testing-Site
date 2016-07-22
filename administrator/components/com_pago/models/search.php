<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');

class PagoModelSearch  extends JModelLegacy
{
	var $_total = null;
	var $_pagination = null;

	public function search($searchQuery)
	{
		$db = JFactory::getDBO();
		$where = '';
		$query = "SELECT DISTINCT `items`.id AS item_id
		 		FROM #__pago_items AS items
		 		LEFT JOIN #__pago_product_varation as variation ON `variation`.item_id=items.id";
			
		$module = JModuleHelper::getModule('mod_pago_search');
		$searchParams = new JRegistry($module->params);
		$fuzzy_search_enable = (int) $searchParams['fuzzy_search_enable'];
		$sortByItem = str_replace("+", " ", JRequest::getVar('sortbyitem')); 
	
		if($sortByItem == NULL)
		{
			$sortByItem = "items.name ASC";
		}
			
				
		if (!empty($searchQuery))
		{
			$checkForExist = "SELECT id FROM #__pago_search_keywords WHERE pgkeyword='" . trim($searchQuery) . "'";
			$db->setQuery($checkForExist);
			$resultId = $db->loadResult();
			
			if(isset($resultId))
			{
				$setCount = "UPDATE #__pago_search_keywords SET count= count+1 WHERE id=" . $resultId;
				$db->setQuery($setCount);
				$db->Query();
			}
			else
			{
				$inseryKeyword = "INSERT INTO #__pago_search_keywords (pgkeyword, count) VALUES ('" . trim($searchQuery) . "', '1')";
				$db->setQuery($inseryKeyword);
				$db->Query();
			}
			
			$config = Pago::get_instance( 'config' )->get();
			$searchType = $config->get('search_product_settings.search_type');
			
			if($searchType)
			{
				//exact word start
				$searchWord = $searchQuery;			
				$itemNameQuery = '';
				$itemDescQuery = '';
				$itemContentQuery = ''; 
				$variationNmQuery = '';
				$attrOptsQuery = '';
				
				if($fuzzy_search_enable)
				{
					$words = array();
				
					for ($i = 0; $i < strlen($searchWord); $i++)
					{
						// insertions
						$words[] = substr($searchWord, 0, $i) . '_' . substr($searchWord, $i);
						// deletions
						$words[] = substr($searchWord, 0, $i) . substr($searchWord, $i + 1);
						// substitutions
						$words[] = substr($searchWord, 0, $i) . '_' . substr($searchWord, $i + 1);
					}
					// last insertion
					$words[] = $searchWord . '_'; 
					
					if (count($words) > 0)
					{
						for($g = 0; $g < count($words); $g++)
						{
							$varLimit = count($words)-1;
							$itemNameQuery .= "`items`.name LIKE '%{$words[$g]}%' OR ";
							$itemDescQuery .= "`items`.description LIKE '%{$words[$g]}%' OR ";
							$itemContentQuery .= "`items`.content LIKE '%{$words[$g]}%' OR ";
							
							if($g < $varLimit)
							{
								$variationNmQuery .= "`variation`.name LIKE '%{$words[$g]}%' OR ";
							}
							else
							{
								$variationNmQuery .= "`variation`.name LIKE '%{$words[$g]}%' ";
							}
							
							$attrOptsQuery .= "AND `attr_opts`.name LIKE '%{$words[$g]}%' !=0 ";
						}
					} 
				}
				else
				{
					$itemNameQuery .= "`items`.name LIKE '%{$searchWord}%' OR ";
					$itemDescQuery .= "`items`.description LIKE '%{$searchWord}%' OR ";
					$itemContentQuery .= "`items`.content LIKE '%{$searchWord}%' OR ";
					$variationNmQuery .= "`variation`.name LIKE '%{$searchWord}%' ";
					$attrOptsQuery .= "AND `attr_opts`.name LIKE '%{$searchWord}%' !=0 ";
				}
				
						$where[]= "(
				" . $itemNameQuery . "
				" . $itemDescQuery . " 
				" . $itemContentQuery . " 
				(" . $variationNmQuery . " 
					AND `variation`.published = 1 
					AND `variation`.var_enable = 1
				) 
				OR
				(SELECT count(*) FROM #__pago_product_varation_rel as var_rel LEFT JOIN #__pago_attr_opts as attr_opts ON `var_rel`.opt_id = `attr_opts`.id 
					WHERE `items`.id = `var_rel`.item_id
					AND `attr_opts`.opt_enable = 1
					AND `attr_opts`.published = 1
					AND `variation`.published = 1
					AND `variation`.var_enable = 1
					" . $attrOptsQuery . " )
				 )
				AND `items`.published = 1
				AND `items`.visibility = 1
				";
				//exact worde end
			
			}
			else
			{
				
				$searchQuery = explode(' ', trim($searchQuery));
				
				foreach($searchQuery as $searchWord){
				
				$itemNameQuery = '';
				$itemDescQuery = '';
				$itemContentQuery = ''; 
				$variationNmQuery = '';
				$attrOptsQuery = '';
				
				if($fuzzy_search_enable)
				{
					$words = array();
				
					for ($i = 0; $i < strlen($searchWord); $i++)
					{
						// insertions
						$words[] = substr($searchWord, 0, $i) . '_' . substr($searchWord, $i);
						// deletions
						$words[] = substr($searchWord, 0, $i) . substr($searchWord, $i + 1);
						// substitutions
						$words[] = substr($searchWord, 0, $i) . '_' . substr($searchWord, $i + 1);
					}
					// last insertion
					$words[] = $searchWord . '_'; 
					
					if (count($words) > 0)
					{
						for($g = 0; $g < count($words); $g++)
						{
							$varLimit = count($words)-1;
							$itemNameQuery .= "`items`.name LIKE '%{$words[$g]}%' OR ";
							$itemDescQuery .= "`items`.description LIKE '%{$words[$g]}%' OR ";
							$itemContentQuery .= "`items`.content LIKE '%{$words[$g]}%' OR ";
							
							if($g < $varLimit)
							{
								$variationNmQuery .= "`variation`.name LIKE '%{$words[$g]}%' OR ";
							}
							else
							{
								$variationNmQuery .= "`variation`.name LIKE '%{$words[$g]}%' ";
							}
							
							$attrOptsQuery .= "AND `attr_opts`.name LIKE '%{$words[$g]}%' !=0 ";
						}
					} 
				}
				else
				{
					$itemNameQuery .= "`items`.name LIKE '%{$searchWord}%' OR ";
					$itemDescQuery .= "`items`.description LIKE '%{$searchWord}%' OR ";
					$itemContentQuery .= "`items`.content LIKE '%{$searchWord}%' OR ";
					$variationNmQuery .= "`variation`.name LIKE '%{$searchWord}%' ";
					$attrOptsQuery .= "AND `attr_opts`.name LIKE '%{$searchWord}%' !=0 ";
				}
				
						$where[]= "(
				" . $itemNameQuery . "
				" . $itemDescQuery . " 
				" . $itemContentQuery . " 
				(" . $variationNmQuery . " 
					AND `variation`.published = 1 
					AND `variation`.var_enable = 1
				) 
				OR
				(SELECT count(*) FROM #__pago_product_varation_rel as var_rel LEFT JOIN #__pago_attr_opts as attr_opts ON `var_rel`.opt_id = `attr_opts`.id 
					WHERE `items`.id = `var_rel`.item_id
					AND `attr_opts`.opt_enable = 1
					AND `attr_opts`.published = 1
					AND `variation`.published = 1
					AND `variation`.var_enable = 1
					" . $attrOptsQuery . " )
				 )
				AND `items`.published = 1
				AND `items`.visibility = 1
				";
				}
				//each worde end
			}
			
			$where = implode('OR', $where);
		}
		else
		{
			$where = "1=1";
		}
		
		JPluginHelper::importPlugin('search');
		$dispatcher = JEventDispatcher::getInstance();
		$results = $dispatcher->trigger('onPagoSearch', array());
		$resultsKeywords = $dispatcher->trigger('onPagoSearchKeyword', array());
		$resultsPremimumItems = $dispatcher->trigger('onPagoSearchPremiumItems', array());
		$resultsDownloads = $dispatcher->trigger('onPagoSearchMostDownloadable', array());

		
		if(count($results) > 0)
		{
			$query .= $results[0][0];
			$where .= $results[0][1];
		}
		
		if(count($resultsKeywords) > 0)
		{
			$query .= $resultsKeywords[0][0];
			$where .= $resultsKeywords[0][1];
		}
		
		if(count($resultsPremimumItems) > 0)
		{
			$query .= $resultsPremimumItems[0][0];
			$where .= $resultsPremimumItems[0][1];
		}

		if(count($resultsDownloads) > 0)
		{
			$query .= $resultsDownloads[0][0];
			$where .= $resultsDownloads[0][1];
		}


	    $query.=" WHERE ".$where." ORDER BY '" . $sortByItem . "'";
		$db->setQuery($query);
		$itemsId = $db->loadObjectList();
		$items_model = JModelLegacy::getInstance( 'Itemslist', 'PagoModel' );

		$this->_total = count($itemsId);
		
		if($itemsId){
			$itemIdsSql  = ' AND items.`id` IN (';
			$orderSql  = '(`items`.id, ';
    		foreach ($itemsId as $itemId) {
    			$itemIdsSql .= $itemId->item_id.',';
    			$orderSql .= $itemId->item_id.',';
    		}
        	$itemIdsSql = substr($itemIdsSql, 0, -1);
        	$orderSql = substr($orderSql, 0, -1);
			$itemIdsSql .= ') ';
			$orderSql .= ') ';
	
			$this->set_limits();
			
			$sql = "SELECT items.*, (
										SELECT  files.`id` AS file_id
										FROM #__pago_files as files
										WHERE
										(files.`published` = 1 OR files.`published` is null ) AND 
										(files.`type` = 'images' OR files.`type` is null ) AND 
										(files.`default` = 1 OR files.`default` is null ) AND
										files.`item_id` = `items`.id LIMIT 1
									) as file_id
							,category.`name` AS category_name
					FROM #__pago_items as items
					LEFT JOIN #__pago_categoriesi as category
					ON (category.`id` = items.`primary_category`)
					WHERE items.`visibility` = 1
					AND items.`published` = 1
					{$itemIdsSql} ORDER BY " . $sortByItem . " LIMIT {$this->getState( 'limitstart' )},{$this->get_limit()}";
			$db->setQuery( $sql );
			//ORDER BY FIELD {$orderSql}
			$items = $db->loadObjectList();
	
			return $items;
		}else{
			return false;
		}
	}
	function getPagination()
	{	
		if ( empty( $this->_pagination ) ) {

			// Call set limits
			$this->set_limits();

			
			jimport( 'joomla.html.pagination' );
			$this->_pagination = new JPagination( $this->_total, $this->getState( 'limitstart' ), $this->get_limit() );
		}
		return $this->_pagination;
	}
	function set_limits()
	{
		$app = JFactory::getApplication();

        // Get pagination request variables
       	$limit = $this->get_limit();
        $limitstart = JFactory::getApplication()->input->get( 'limitstart', 0, '', 'int' );


        $this->setState( 'limit', $limit );
        $this->setState( 'limitstart', $limitstart );
    }
    function get_limit()
	{
		$config = Pago::get_instance( 'config' )->get();
		$perPage = $config->get('search_product_settings.product_settings_product_per_page');
		

		return $perPage;
	}
}