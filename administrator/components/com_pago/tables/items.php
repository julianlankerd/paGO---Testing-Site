<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableItems extends JTable
{

    var $id = null;
    var $name = null;
    var $type = null;
    var $created = null;
	var $modified = null;
	var $published = 0;
	var $params = null;
	var $alias = null;
    var $primary_category = null;

    var $shipping_methods = null;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db )
	{
        parent::__construct('#__pago_items', 'id', $db);
    }
	
    public function search_item($word,$itemId = false, $existPrdId = false) 
    //public function search_item($word,$itemId = false, $catid = false,$existPrdId = false) 
    {
		$and = '';
		// if($catid != '' AND $catid != false)
		// {
		// 	$and = ' AND primary_category IN("'.$catid.'")';
		// }
		if($existPrdId != '' AND $existPrdId != false)
		{
			$and .= ' AND id NOT IN("'.$existPrdId.'")';
		}
		
        if($itemId)
		{
            $query = "SELECT name as label,id as value FROM " . $this->_tbl . " WHERE visibility != 0 AND published = 1 AND id != ".$itemId." AND name LIKE '%".$word."%'" . $and;
        }
        else
        {
            // if($catid != false){
            //     $query = "SELECT name as label,id as value FROM " . $this->_tbl . " WHERE visibility != 0 AND name LIKE '%".$word."%' AND primary_category IN('".$catid."')" . $and;
            // }else{
                $query = "SELECT name as label,id as value FROM " . $this->_tbl . " WHERE visibility != 0 AND published = 1 AND name LIKE '%".$word."%'";
            //}
        }

        
        $this->_db->setQuery( $query );
        $result = $this->_db->loadObjectList();
        return $result;
    }
}
