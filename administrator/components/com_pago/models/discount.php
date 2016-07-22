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

class PagoModelDiscount extends JModelLegacy
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
		$this->_data = JTable::getInstance( 'discount', 'table' );
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
		
		if($id == 0)
		{
			$data['created'] = date( 'Y-m-d');
		}
		$data['modified'] = date( 'Y-m-d');

		if(!isset($data['start_date']))
		{
			$data['start_date'] = date('Y-m-d');	
		}
		
		if(isset($data['end_date']) && strlen($data['end_date']) > 3)
		{
			if($data['start_date'] > $data['end_date'])
			{
				$return['status'] = 'fail'; 
				$return['message'] = JText::_( 'PAGO_DISCOUNT_ERROR_START_DATE' );
				return $return;
			}
		}

		if( $data['discount_event'] == '1' || $data['discount_event'] == '2'  || $data['discount_event'] == '5' )  // changed by hir
		{
			$data['discount_filter'] = $data['discount_filter'];
		} 
		else 
		{
			$data['discount_filter'] = $data['discount_filter_cat'];
		}

		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->check()) return JError::raiseWarning( 500, $row->getError() );
		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );

		$db = $row->getDBO();
		if( $insert_id = $db->insertid() )
		{
			$id 	= $db->insertid();
			$data['id'] = $id;
			$create = true;
		}
		else
		{
			$insert_id = $id;	
		}

		if ( $data['id'] > 0 ) 
		{
			$old_item_array = $this->get_assign_item_column($id);
			
			$old_category_array = $this->get_assign_category_column($id);
			
			$db = JFactory::getDBO();
			$query = "DELETE FROM #__pago_discount_items WHERE discount_rule_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();
			
			$query = "DELETE FROM #__pago_discount_categories WHERE discount_rule_id = " . $data['id'];
			$db->setQuery( $query );
			$db->query();

			if(isset($data['assign']['item_id']))
			{
				$assign_item = json_decode ( $data['assign']['item_id'], true );
				$new_item_array = array();

				foreach( $assign_item as $cid )
				{
					$query = "INSERT INTO #__pago_discount_items VALUES ".
					"('', '".$id."', '".$cid['id']."')";
					$db->setQuery( $query );
					$db->query();

					if($data['discount_event']!= '4')
					{
						$query_update_item = "UPDATE #__pago_items set disc_start_date='".$data['start_date']."', disc_end_date='".$data['end_date']."', apply_discount=1, discount_amount='".$data['discount_amount']."', discount_type='".$data['discount_type']."' where id='".$cid['id']."'";
						$db->setQuery( $query_update_item );
						$db->query();
						$new_item_array[] = $cid['id'];
					}
				}
				if($data['discount_event']!= '4')
				{
					$arr_items_diff = array_diff($old_item_array, $new_item_array);
					foreach( $arr_items_diff as $item_old_id )
					{
						$query_update_item = "UPDATE #__pago_items set apply_discount=0 where id='".$item_old_id."'";
						$db->setQuery( $query_update_item );
						$db->query();
					}
				}
			}

			if(isset($data['assign']['assign_category']))
			{
				// category
				$assign_category = array_unique( $data['assign']['assign_category'] );
				$new_category_array = array();

				foreach( $assign_category as $cid )
				{
					$query = "INSERT INTO #__pago_discount_categories VALUES ".
					"('', '".$id."', '".$cid."')";
					$db->setQuery( $query );
					$db->query();

					$query_category_items = "SELECT item_id as id FROM #__pago_categories_items where category_id  = '".$cid."'";
					$db->setQuery( $query_category_items );
					$total_category_items = $db->loadColumn();

					foreach( $total_category_items as $itemid )
					{
						$query_update_item = "UPDATE #__pago_items set disc_start_date='".$data['start_date']."', disc_end_date='".$data['end_date']."', apply_discount=1, discount_amount='".$data['discount_amount']."', discount_type='".$data['discount_type']."' where id='".$itemid."'";
						$db->setQuery( $query_update_item );
						$db->query();
					}

					$new_category_array[] = $cid;
				}

				$arr_category_diff = array_diff($old_category_array, $new_category_array);

				foreach( $arr_category_diff as $category_old_id )
				{
					$query_category_items = "SELECT item_id as id FROM #__pago_categories_items where category_id  = '".$category_old_id."'";
					$db->setQuery( $query_category_items );
					$total_category_items = $db->loadColumn();

					foreach( $total_category_items as $itemid )
					{
						$query_update_item = "UPDATE #__pago_items set apply_discount=0 where id='".$itemid."'";
						$db->setQuery( $query_update_item );
						$db->query();
					}
				}
			}
		}
		
		$return['status'] = 'success'; 
		$return['message'] = JText::_( 'PAGO_DISCOUNT_SAVE' );
		$return['id'] = $insert_id; 
		return $return;
	}

	function get_events( $id )
	{
		$db = JFactory::getDBO();

		$query = 'SELECT * FROM #__pago_discount_rules where id = ' . $db->quote($id);
		$db->setQuery( $query );
		return $db->loadAssocList();
	}
	
	function get_assign( $item_id )
	{
		$db = JFactory::getDBO();

		$query = 'SELECT item_id as id FROM #__pago_discount_items where discount_rule_id = ' . $db->quote($item_id);
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

	function assign_item_html($discountId)
	{
	
		$html = '<div class="pg-row-item pg-col10 pg-assign-items">
					<div class="pg-col10">
						<div>'.JText::_('PAGO_DISCOUNT_SELECTED_ITEMS').'</div>
						<ul class="coupon-assign-items">';

		$itemsUniqueId = array();
		$uniqueItems = array();

		if($discountId != 0)
		{
			$model = JModelLegacy::getInstance( 'Discount', 'PagoModel' );
			$assign = $model->get_assign($discountId);
			
			$itemModel = JModelLegacy::getInstance( 'Item', 'PagoModel' );
			if($assign)
			{
			
				$itemsId = json_decode($assign['0']['id']);
				
				if($itemsId)
				{
					foreach ($assign as $value) 
					{
						if(!in_array($value['id'], $itemsUniqueId))
						{
							$itemsUniqueId[] = $value['id'];
							$uniqueItems[] = $value;
							$item = $itemModel->getItemName($value['id']);
							if ( $item ) 
							{
								$html .= '<li class="itemAdded" id="'.$value['id'].'">'.$item->name.'
											  <span title="Click to remove" class="discount-remove-assign-item">x</span>
										  </li>';
							}
						}
					}
				}
			}
		}
		$html .= 		'</ul>
					</div>';
		$html .= '<div class="pg-col10">';
		$html .= '<div>'.JText::_('COM_PAGO_PLEASE_SELECT_ITEMS').'</div>';
		$html .= '<div class="pg-relative">';
		$html .= 	'<input type="text"  id="discount-assign-item-add" autocomplete="off" aria-autocomplete="list" aria-haspopup="true" placeholder="Please Select Product"></li>';
		$html .= 	'<input type="hidden" name="params[assign][item_id]" id="params_assign_item_id" value=\''.json_encode($uniqueItems).'\' ></div>';
		$html .= '</div>';
		$html .= '</div>
				</div>';

		return $html;		
	}
	
	
	function assign_category_html($discountId)
	{
		$html = '<div class="pg-row-item pg-col4 pg-assign-category">';
		
		$cat_table = JTable::getInstance( 'categoriesi', 'Table' );
		$cats = $cat_table->getTree( 1 );
		
		$ctrl = "params[assign][assign_category][]";
		$attribs = ' multiple="true"';

		$key = 'id';
		$val = 'name';

		$value = 0;
		
		$control_name = "params_assign_category";

		foreach($cats as $cat)
		{
			$cat_name = str_repeat('_', (($cat->level) * 2) );

			$cat_name .= '['. $cat->level .']_ ' . $cat->name;
			$options[] = array(
				'id' => $cat->id,
				'name' => $cat_name
			);
		}

		if($discountId)
		{
			//get our secondary cats
			$query = ' SELECT category_id FROM #__pago_discount_categories '.
					'  WHERE discount_rule_id = '.$discountId;

			$secondary_categories = $this->_getList( $query );

			$cats = array();

			if( is_array( $secondary_categories ) )
			foreach ( $secondary_categories as $cat ) 
			{
				$cats[] = $cat->category_id;
			}

			if ( !empty( $cats ) ) 
			{
				$value = $cats;
			}
		}

		$html .= @JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name);

		$html .= '</div>';
		return $html;
	}
	
	function get_assign_item_column( $item_id )
	{
		$db = JFactory::getDBO();

		$query = 'SELECT item_id as id FROM #__pago_discount_items where discount_rule_id = ' . $db->quote($item_id);
		$db->setQuery( $query );
		
		return $db->loadColumn();
	}
	
	function get_assign_category_column( $cat_id )
	{
		$db = JFactory::getDBO();

		$query = 'SELECT category_id as id FROM #__pago_discount_categories where discount_rule_id = ' . $db->quote($cat_id);
		$db->setQuery( $query );
		
		return $db->loadColumn();
	}
	
	public function getDiscountRule($cart)
	{
		$db = JFactory::getDBO();
		$todate = date("Y-m-d");
		$sql = "SELECT * from `#__pago_discount_rules` WHERE `start_date` < '".$todate."' and `end_date` > '".$todate."' and `published` = 1 order by priority";
		$db->setQuery($sql);
		$discountRules = $db->loadObjectList();
		$TaxAfterDiscount = 1;
		if($TaxAfterDiscount)
		{
			$order_total = $cart['subtotal'];
		}
		else
		{
			$order_total = $cart['total'];
		}
		$discounts = array();
		$i=0;
		foreach($discountRules  as $discountRule)
		{
			//discount_event => 1: After purchase certain
			//                  2: On selected Items
			//                  3: On selected Categories

			//discount_type => 0: fix amount
			//                 1: percent
			//

			//discount_filter => 0: quantity
			//                   1: amount
			//                   6: number of orders
			//
			$discount_type = $discountRule->discount_type;
			$discount_amount = $discountRule->discount_amount;
			$start_date = $discountRule->start_date;
			$end_date = $discountRule->end_date;
			switch ($discountRule->discount_event)
			{
				case '1':
						switch($discountRule->discount_filter)
					  	{
				  			case '0':
					  			if($cart)
					  			{

					  				$order_qty = 0;
						  			foreach ( $cart['items'] as $item ) {
										$order_qty += $item->cart_qty;
									}
						  			
						  			$required_qty = $discountRule->discount_filter_value;
						  			if($order_qty >= $required_qty)
						  			{
						  				if($discount_type)
						  				{
						  					$discounted_amount = ($discount_amount * $order_total)/100;
						  					$discounted_percent = $discount_amount;
						  				}
						  				else
						  				{
						  					$discounted_amount = $discount_amount;
						  					$discounted_percent = ($discounted_amount * 100) / $order_total;
						  				}
						  				$discounts[$i]['discount_priority'] = $discountRule->priority;
						  				$discounts[$i]['discount_rule_id'] = $discountRule->id;
						  				$discounts[$i]['discount_rule_name'] = $discountRule->rule_name;
						  				$discounts[$i]['discount_amount'] = $discounted_amount;
						  				$discounts[$i]['discount_percent'] = $discounted_percent;
						  				$discounts[$i]['discount_message'] = $discountRule->discount_message;
						  				$i++;
						  			}
					  			}
				  				continue;
					  		case '1':
							  	if($cart)
							  	{
							  		$required_total = $discountRule->discount_filter_value;
						  			if($order_total >= $required_total)
						  			{
						  				if($discount_type)
						  				{
						  					$discounted_amount = ($discount_amount * $order_total)/100;
						  					$discounted_percent = $discount_amount;
						  				}
						  				else
						  				{
						  					$discounted_amount = $discount_amount;
						  					$discounted_percent = ($discounted_amount * 100) / $order_total;
						  				}
										$discounts[$i]['discount_priority'] = $discountRule->priority;
						  				$discounts[$i]['discount_rule_id'] = $discountRule->id;
						  				$discounts[$i]['discount_rule_name'] = $discountRule->rule_name;
						  				$discounts[$i]['discount_amount'] = $discounted_amount;
						  				$discounts[$i]['discount_percent'] = $discounted_percent;
						  				$discounts[$i]['discount_message'] = $discountRule->discount_message;
						  				$i++;
						  			}
							  	}
					  			//$i++;
					  			continue;
			  				case '6':
				  				$user = JFactory::getUser();
								$user_id = $user->id;
					  			if($user_id)
					  			{
					  				$total_order = Pago::get_instance('users')->get_all_order_of_user($user_id);
					  				$current_order = $total_order + 1;
					  				$required_order = $discountRule->discount_filter_value;
					  				if($current_order > $required_order)
					  				{
					  					if($discount_type)
						  				{
						  					$discounted_amount = ($discount_amount * $order_total)/100;
						  					$discounted_percent = $discount_amount;
						  				}
						  				else
						  				{
						  					$discounted_amount = $discount_amount;
						  					$discounted_percent = ($discounted_amount * 100) / $order_total;
						  				}
						  				$discounts[$i]['discount_priority'] = $discountRule->priority;
						  				$discounts[$i]['discount_rule_id'] = $discountRule->id;
						  				$discounts[$i]['discount_rule_name'] = $discountRule->rule_name;
						  				$discounts[$i]['discount_amount'] = $discounted_amount;
						  				$discounts[$i]['discount_percent'] = $discounted_percent;
						  				$discounts[$i]['discount_message'] = $discountRule->discount_message;
					  					$i++;
					  				}
					  			}
					  			//$i++;
					  			continue;
					  	}
				continue;
				case '4':
						if($cart)
						{
							// get combination items
							$combination_items = $this->get_assign_item_column($discountRule->id);
							if(count($combination_items)> 0)
							{
								foreach($combination_items as $key => $citem)
								{
									foreach ( $cart['items'] as $item )
									{
										if($citem == $item->id)
										{
											unset($combination_items[$key]);
										}
									}
								}
								if(count($combination_items) == 0)
								{
									if($discount_type)
					  				{
					  					$discounted_amount = ($discount_amount * $order_total)/100;
					  					$discounted_percent = $discount_amount;
					  				}
					  				else
					  				{
					  					$discounted_amount = $discount_amount;
					  					$discounted_percent = ($discounted_amount * 100) / $order_total;
					  				}
									$discounts[$i]['discount_priority'] = $discountRule->priority;
					  				$discounts[$i]['discount_rule_id'] = $discountRule->id;
					  				$discounts[$i]['discount_rule_name'] = $discountRule->rule_name;
					  				$discounts[$i]['discount_amount'] = $discounted_amount;
					  				$discounts[$i]['discount_percent'] = $discounted_percent;
					  				$discounts[$i]['discount_message'] = $discountRule->discount_message;
				  					$i++;
								}
							}
							
						}
				case '5' :
						if($cart)
						{
							$dispatcher = KDispatcher::getInstance();
							$customFieldDiscount = $dispatcher->trigger( 'onCustomFieldDiscount', array(&$cart, $discountRule, $i) );
							if($customFieldDiscount)
							{
								$discounts[$i]['discount_priority'] = $discountRule->priority;
				  				$discounts[$i]['discount_rule_id'] = $discountRule->id;
				  				$discounts[$i]['discount_rule_name'] = $discountRule->rule_name;
				  				$discounts[$i]['discount_amount'] = 0;
				  				$discounts[$i]['discount_percent'] = 0;
				  				$discounts[$i]['discount_message'] = $discountRule->discount_message;
								$i++;
							}


						}
			}
		}


		return $discounts;
	}
}
