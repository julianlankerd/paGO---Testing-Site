<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined('_JEXEC') or die('Restricted access');

class mod_pago_category_helper
{
	public static function getParentCategories($cat_id, $prev_array)
	{
		$result = array();
		if( $cat_id ) {
			$db = JFactory::getDBO();
			$query = "SELECT id,parent_id FROM #__pago_categoriesi WHERE id=" . $cat_id;
			$db->setQuery($query);
			$result = $db->loadObjectList();
			$parentCat = array();
		}

		if (!empty($result[0]->parent_id))
		{
			$parentCat[] = $result[0]->parent_id;

			if (count($prev_array) > 0)
			{
				$parentCat = array_merge($parentCat, $prev_array);
			}

			return  self::getParentCategories($result[0]->parent_id, $parentCat);
		}
		else
		{
			return $prev_array;
		}
	}
}
?>