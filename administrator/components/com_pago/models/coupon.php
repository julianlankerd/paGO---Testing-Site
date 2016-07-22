<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');


class PagoModelCoupon extends JModelLegacy
{
	var $_data;
	var $_order  = array();

	function __construct()
	{
		parent::__construct();

		$array = JFactory::getApplication()->input->get('cid',  0, 'array');
		$this->setId((int)$array[0]);
	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id        = $id;
		$this->_data    = null;
	}

	function &getData()
	{
		$this->_data = JTable::getInstance( 'coupon', 'table' );
		$this->_data->load( $this->_id );

		return $this->_data;
	}

	function store()
	{
		$row = $this->getTable();
		$data = JFactory::getApplication()->input->getArray($_POST);
		$data = $data['params'];

		$create = false;
		$id = $data['id'];

		if($id == 0){
			$data['created'] = date( 'Y-m-d H:i:s', time() );
		}
		$data['modified'] = date( 'Y-m-d H:i:s', time() );

		if(!isset($data['start'])){
			$data['start'] = date( 'Y-m-d H:i:s', time() );	
		}
		
		if(isset($data['end']) && strlen($data['end']) > 3){
			if($data['start'] > $data['end']){
				$return['status'] = 'fail'; 
				$return['message'] = JText::_( 'PAGO_COUPON_ERROR_START_DATE' );
				return $return;
			}
		}

		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->check()) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );

		$db = $row->getDBO();
		if( $insert_id = $db->insertid() ){
			$id 	= $db->insertid();
			$data['id'] = $id;
			$create = true;
		}else{
			$insert_id = $id;	
		}

		if ( $data['id'] > 0 ) {

			$db = JFactory::getDBO();

			$query = "DELETE FROM #__pago_coupon_assign WHERE coupon_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();

			$query = "DELETE FROM #__pago_coupon_categories WHERE coupon_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();

			$query = "DELETE FROM #__pago_coupon_groups WHERE coupon_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();

			$query = "DELETE FROM #__pago_coupon_rules WHERE coupon_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();

			$query = "DELETE FROM #__pago_coupon_events WHERE coupon_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();

			///////// coupon rule
			foreach ( $data['rules'] as $k => $v ) {
				$query = "INSERT INTO #__pago_coupon_rules VALUES ".
					"( '".$data['id']."', '".$k."', '{}', '".$v['discount'] ."', '".
					$v['is_percent']."')";
				$db->setQuery( $query );
				$db->query();
			}

			///////// coupon events
			
			$query = "INSERT INTO #__pago_coupon_events VALUES ".
				"( '".$data['id']."', '".$data['events']['available_type']."', '".$data['events']['available_condition']."', '".$data['events']['filter_sum']."')";
			$db->setQuery( $query );
			$db->query();

			///////// coupon assign
			if($data['assign']['type'] == 1){
				
				$query = "INSERT INTO #__pago_coupon_assign VALUES ".
					"( '".$data['id']."', '".$data['assign']['type']."', '".$data['assign']['assign_items']."', '".$data['assign']['assign_users']."')";
				$db->setQuery( $query );
				$db->query();
			}

			if($data['assign']['type'] == 2){

				if(isset($data['assign']['assign_category'])){
					// category
					$assign_category = array_unique( $data['assign']['assign_category'] );

					$cat_values = array();
					$new_cats = array();
						foreach( $assign_category as $cid ){
							$cat_values[] = '('.$cid.','.$id.')';
							$new_cats[] = $cid;
						}

					$query = "INSERT INTO #__pago_coupon_categories
						( category_id, coupon_id ) VALUES ". implode( ',', $cat_values );

					$this->_db->setQuery($query);
					$this->_db->query();
				}
				$query = "INSERT INTO #__pago_coupon_assign VALUES ".
					"( '".$data['id']."', '".$data['assign']['type']."', '".$data['assign']['assign_items']."', '".$data['assign']['assign_users']."')";
				$db->setQuery( $query );
				$db->query();
			}
			if($data['assign']['type'] == 3){
				if(isset($data['assign']['assign_groups'])){
					// groups
					$assign_groups = array_unique( $data['assign']['assign_groups'] );

					$groups_values = array();
					$new_groups = array();
						foreach( $assign_groups as $cid ){
							$groups_values[] = '('.$cid.','.$id.')';
							$new_groups[] = $cid;
						}

					$query = "INSERT INTO #__pago_coupon_groups
						( group_id, coupon_id ) VALUES ". implode( ',', $groups_values );

					$this->_db->setQuery($query);
					$this->_db->query();
				}

				$query = "INSERT INTO #__pago_coupon_assign VALUES ".
					"( '".$data['id']."', '".$data['assign']['type']."', '".$data['assign']['assign_items']."', '".$data['assign']['assign_users']."')";
				$db->setQuery( $query );
				$db->query();
			}
			if($data['assign']['type'] == 4){
				
				$query = "INSERT INTO #__pago_coupon_assign VALUES ".
					"( '".$data['id']."', '".$data['assign']['type']."', '".$data['assign']['assign_items']."', '".$data['assign']['assign_users']."')";
				$db->setQuery( $query );
				$db->query();
			}
			
			if($data['assign']['type'] == 5){
				
				$query = "INSERT INTO #__pago_coupon_assign VALUES ".
					"( '".$data['id']."', '".$data['assign']['type']."', '', '')";
				$db->setQuery( $query );
				$db->query();
			}

		}

		$return['status'] = 'success'; 
		$return['message'] = JText::_( 'PAGO_COUPON_SAVE' );
		$return['id'] = $insert_id; 
		return $return;
	}

	function verify( $code )
	{
		$db = JFactory::getDBO();
		$query = 'SELECT id,unlimited FROM #__pago_coupon WHERE published = 1 AND code =' . $db->Quote( $code );
		$db->setQuery( $query );
		$couponData = $db->loadObject();
	    
	    if(!$couponData) return false;
	    
		if($couponData->unlimited == '1')
		{
			return $couponData->id;
		}
		else
		{
			$query = 'SELECT id FROM #__pago_coupon WHERE published = 1 AND used < quantity '.
			'AND code =' . $db->Quote( $code );
			$db->setQuery( $query );
			
			return $db->loadResult();
		}
	}

	function get_rules( $coupon_id )
	{
		$db = JFactory::getDBO();

		$query = 'SELECT * FROM #__pago_coupon_rules where coupon_id = ' . $db->quote($coupon_id);
		$db->setQuery( $query );
		return $db->loadAssocList();
	}

	function get_events( $coupon_id )
	{
		$db = JFactory::getDBO();

		$query = 'SELECT * FROM #__pago_coupon_events where coupon_id = ' . $db->quote($coupon_id);
		$db->setQuery( $query );
		return $db->loadAssocList();
	}

	function get_assign( $coupon_id )
	{
		$db = JFactory::getDBO();

		$query = 'SELECT * FROM #__pago_coupon_assign where coupon_id = ' . $db->quote($coupon_id);
		$db->setQuery( $query );
		return $db->loadAssocList();
	}

	function get_assign_category( $coupon_id, $coupon_code=false )
	{
		$db = JFactory::getDBO();
		
		if($coupon_code){
			$query = '
			SELECT * FROM #__pago_coupon_assign 
				LEFT JOIN #__pago_coupon
					ON #__pago_coupon_assign.coupon_id = #__pago_coupon.id
				WHERE #__pago_coupon.code = ' . $db->quote($coupon_code);
			
			$db->setQuery( $query );
		
			return $db->loadAssoc();
		}
		
		$query = 'SELECT * FROM #__pago_coupon_assign where coupon_id = ' . $db->quote($coupon_id);
		
		$db->setQuery( $query );
		
		return $db->loadAssocList();
	}

	function get_rule_fields( $rule, $data = null )
	{
		$c = $this->load_rule_html( $rule, $data );

		if ( $c !== null ) {
			return $c;
		}

		return null;
	}

	protected function load_rule_html( $rule, $data = null )
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_pago/helpers/coupon_rules';
		$class = $rule . '_html';

		if ( file_exists( $path . '/' . $class . '.php' ) ) {
			include( $path . '/' . $class . '.php' );
			if ( $data === null ) {
				return new $class(
					array(
						'coupon_id' => '',
						'name' => $rule,
						'params' => '{}',
						'discount' => '',
						'is_percent' => ''
					)
				);
			} else {
				return new $class( $data );
			}
		}

		return null;
	}

	function increment_used( $code )
	{
		$db = JFactory::getDBO();

		$query = 'UPDATE #__pago_coupon set used = used +1 WHERE code = ' . $db->Quote( $code );

		$db->setQuery( $query );

		$db->query();
	}

	function assign_item_html($couponId){
		$html = '<div class="pg-row-item pg-col8 pg-assign-items">
					<div class="pg-col6">
						<ul class="coupon-assign-items">';

		$itemsUniqueId = array();
		$uniqueItems = array();

		if($couponId != 0){
			$model = JModelLegacy::getInstance( 'Coupon', 'PagoModel' );
			$assign = $model->get_assign($couponId);
			$itemModel = JModelLegacy::getInstance( 'Item', 'PagoModel' );
			if($assign){
				$itemsId = json_decode($assign['0']['assign_items']);
				if($itemsId){
					foreach ($itemsId as $value) {
						if(!in_array($value->id, $itemsUniqueId)){
							$itemsUniqueId[] = $value->id;
							$uniqueItems[] = $value;
							$item = $itemModel->getItemName($value->id);
							if ( $item ) {
								$html .= '<li class="itemAdded" id="'.$value->id.'">'.$item->name.'
											  <span title="Click to remove" class="coupon-remove-assign-item">x</span>
										  </li>';
							}
						}
					}
				}
			}
		}
		$html .= 		'</ul>
					</div>';
		$html .= '<div class="pg-col4">';
		$html .= '<div class="pg-relative">';
		$html .= 	'<input type="text"  id="coupon-assign-item-add" autocomplete="off" aria-autocomplete="list" aria-haspopup="true"></li>';
		$html .= 	'<input type="hidden" name="params[assign][assign_items]" id="params_assign_items" value=\''.json_encode($uniqueItems).'\' ></div>';
		$html .= '</div>';
		$html .= '</div>
				</div>';

		return $html;		
	}


	function assign_user_html($couponId){
		$html = '<div class="pg-row-item pg-col8 pg-assign-users">
					<div class="pg-row-item pg-col6">
						<ul class="coupon-assign-users">';

		$usersUniqueId = array();
		$uniqueUsers = array();

		if($couponId != 0){
			$model = JModelLegacy::getInstance( 'Coupon', 'PagoModel' );
			$assign = $this->get_assign($couponId);
			if($assign){
				$usersId = json_decode($assign['0']['assign_users']);
				if($usersId){
					foreach ($usersId as $value) {
						if(!in_array($value->id, $usersUniqueId)){
							$usersUniqueId[] = $value->id;
							$uniqueUsers[] = $value;
							$user = $this->getUserName($value->id);
							$html .= '<li class="userAdded" id="'.$value->id.'">'. $user .'
										  <span title="Click to remove" class="coupon-remove-assign-user">x</span>
									  </li>';
						}
					}
				}
			}
		}
		$html .= 		'</ul>
					</div>';
		$html .= '<div class="pg-col4">';
		$html .= 	'<input type="text"  id="coupon-assign-user-add" autocomplete="off" aria-autocomplete="list" aria-haspopup="true"></li>';
		$html .= 	'<input type="hidden" name="params[assign][assign_users]" id="params_assign_users" value=\''.json_encode($uniqueUsers).'\' ></div>';
		$html .= '</div>
				</div>';

		return $html;		
	}

	function assign_category_html($couponId){
		$html = '<div class="pg-row-item pg-col4 pg-assign-category">';
		
		$cat_table = JTable::getInstance( 'categoriesi', 'Table' );
		$cats = $cat_table->getTree( 1 );
		
		$ctrl = "params[assign][assign_category][]";
		$attribs = ' multiple="true"';

		$key = 'id';
		$val = 'name';

		$value = 0;
		
		$control_name = "params_assign_category";

		foreach($cats as $cat){
			$cat_name = str_repeat('_', (($cat->level) * 2) );

			$cat_name .= '['. $cat->level .']_ ' . $cat->name;
			$options[] = array(
				'id' => $cat->id,
				'name' => $cat_name
			);
		}

		if($couponId){
			//get our secondary cats
			$query = ' SELECT category_id FROM #__pago_coupon_categories '.
					'  WHERE coupon_id = '.$couponId;

			$secondary_categories = $this->_getList( $query );

			$cats = array();

			if( is_array( $secondary_categories ) )
			foreach ( $secondary_categories as $cat ) {
				$cats[] = $cat->category_id;
			}

			if ( !empty( $cats ) ) {
				$value = $cats;
			}
		}

		$html .= @JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name);

		$html .= '</div>';
		return $html;
	}
	function assign_groups_html($couponId){
		$html = '<div class="pg-row-item pg-col4 pg-assign-groups">';
		
		$groupModel = JModelLegacy::getInstance( 'groups', 'PagoModel' );
		$groups		=  $groupModel->getItems();
		
		$ctrl = "params[assign][assign_groups][]";
		$attribs = ' multiple="true"';

		$key = 'id';
		$val = 'name';

		$value = 0;
		
		$control_name = "params_assign_groups";

		foreach($groups as $group){
			// $name = str_repeat('_', (($cat->level) * 2) );

			// $cat_name .= '['. $cat->level .']_ ' . $cat->name;
			$options[] = array(
				'id' => $group->group_id,
				'name' => $group->name
			);
		}

		if($couponId){
			$query = ' SELECT group_id FROM #__pago_coupon_groups '.
					'  WHERE coupon_id = '.$couponId;

			$selected_groups = $this->_getList( $query );

			$groups = array();

			if( is_array( $selected_groups ) )
			foreach ( $selected_groups as $group ) {
				$groups[] = $group->group_id;
			}

			if ( !empty( $groups ) ) {
				$value = $groups;
			}
		}

		$html .= @JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name);

		$html .= '</div>';
		return $html;
	}
	public function search_user($word)
    {
        $query = "SELECT username as label,id as value FROM #__users WHERE name LIKE '%".$word."%' OR username LIKE '%".$word."%' OR email LIKE '%".$word."%'";

        $this->_db->setQuery( $query );
        $result = $this->_db->loadObjectList();
        return $result;
    }
    public function getUserName($id){
		$query = "SELECT username FROM #__users WHERE id = ".$id;

		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		return $result;
	}
}
