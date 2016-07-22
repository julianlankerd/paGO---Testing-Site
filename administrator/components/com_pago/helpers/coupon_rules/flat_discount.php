<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

include JPATH_ADMINISTRATOR . '/components/com_pago/helpers/coupon_rules/coupon_rule.php';

class flat_discount extends coupon_rule
{
	public function process( &$cart, $coupon_assign_type )
	{
		$subtotal = $cart['subtotal'];

		$apply_coupon = $this->apply_coupon_rules($subtotal, $cart, $coupon_assign_type);
		if(!$apply_coupon)
		{
			return;
		}

		if ( !$this->is_percent )
		{
			// If we are a full amount make sure our cart total is greater then the amount
			// We are applying discount on subtotal so we need to use subtotal here instaed of total
			if ( $cart['subtotal'] < $this->discount )
			{
				return;
			}

			// Required percent rate to apply on tax
			$percent_rate = ($this->discount * 100) / $subtotal;
			$discount = array(
				'amount' => $this->discount,
				'total' => $this->discount,
				'is_percent' => $this->is_percent,
				'percent_rate' => $percent_rate
			);

			return $discount;
		}

		$dis_total = ($subtotal) * ($this->discount / 100);
		$dis_amount = $this->discount * 100;
		$discount = array(
			'amount' => $dis_total,
			'total' => $dis_total,
			'is_percent' => $this->is_percent,
			'percent_rate' => $this->discount
		);

		return $discount;
	}

	function apply_coupon_rules(&$subtotal, $cart, $coupon_assign_type)
	{

		$db = JFactory::getDBO();
		$total = 0;
		$coupon_id = $coupon_assign_type['coupon_id'];

		if($coupon_assign_type['type'] == 1)
		{
			//get assigend category
			foreach($cart['items'] as $item)
			{
				$added_item = false;
				$item_id = $item->id;

				// get categories of item
				$query = "SELECT assign_items FROM #__pago_coupon_assign WHERE coupon_id ='".$coupon_id."'";
				$db->setQuery($query);
				$assign_items = $db->loadResult();

				$assign_items = json_decode($assign_items);

				foreach($assign_items as $coupon_item)
				{
					if(($coupon_item->id == $item_id) && !$added_item)
					{
						$total += ($item->item_price_excluding_tax *  $item->cart_qty);
						$added_item = true;
					}
				}
			}

			$subtotal = $total;

			if($subtotal > 0)
			{
				return true;
			}

		}

		else if($coupon_assign_type['type'] == 2)
		{

			//get assigend category
			foreach($cart['items'] as $item)
			{

				$added_item = false;
				$item_id = $item->id;

				// get categories of item
				$query = "SELECT category_id FROM #__pago_categories_items WHERE item_id ='".$item_id."'";
				$db->setQuery($query);
				$item_categories = $db->loadObjectList();

				foreach($item_categories as $category)
				{
					$sql = "SELECT category_id FROM #__pago_coupon_categories WHERE category_id ='".$category->category_id."' AND coupon_id ='".$coupon_id."'";
					$db->setQuery($sql);
					$coupon_category_id = $db->loadResult();

					if($coupon_category_id && !$added_item)
					{
						$total += ($item->item_price_excluding_tax *  $item->cart_qty);
						$added_item = true;
					}
				}
			}

			$subtotal = $total;
			if($subtotal > 0)
			{
				return true;
			}
		}
		else if($coupon_assign_type['type'] == 4)
		{
			$user = JFactory::getUser();
		 	$user_id = $user->id;
			// get users for this coupon
			$query = "SELECT assign_users FROM #__pago_coupon_assign WHERE coupon_id ='".$coupon_id."'";
			$db->setQuery($query);
			$assign_users = $db->loadResult();
			$assign_users = json_decode($assign_users);

			$valid_coupon = false;
			foreach($assign_users as $coupon_user)
			{
				if($coupon_user->id == $user_id)
				{
					$valid_coupon = true;
				}
			}

			return $valid_coupon;
		}
		else
		{
			return true;
		}

	}
}
