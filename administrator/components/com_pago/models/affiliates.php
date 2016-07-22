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


class PagoModelAffiliates extends JModelLegacy
{

	function __construct()
	{
		parent::__construct();
	}

	public function get_affiliate_id()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT id
					FROM #__pago_affiliates
						WHERE user_id = " . (int)$user->id;

		$db->setQuery( $sql );

		return $db->loadResult();
	}

	public function get_affiliate_data()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT *
					FROM #__pago_affiliates
						WHERE user_id = " . (int)$user->id;

		$db->setQuery( $sql );

		return $db->loadObject();
	}

	public function get_affiliate_sales()
	{
		$db   = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql  = "SELECT *
					FROM #__pago_affiliates_account
						WHERE user_id = " . (int)$user->id;

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}

	public function get_affiliate_signup_count()
	{
		$db = JFactory::getDBO();

		// Lets get the affiliate
		$affiliate_id = self::get_affiliate_id();

		$sql = "SELECT COUNT(id)
					FROM #__pago_affiliates_account AS aa
						WHERE aa.affiliate_id = " . (int)$affiliate_id;

		$db->setQuery( $sql );

		return $db->loadResult();
	}

	public function get_affiliate_referral_list()
	{
		$db = JFactory::getDBO();

		// Lets get the affiliate
		$affiliate_id = self::get_affiliate_id();

		$sql = "SELECT *
					FROM #__pago_affiliates_account AS aa
					LEFT JOIN #__pago_orders_items AS oi ON oi.order_id = aa.order_id
						WHERE aa.affiliate_id = " . (int)$affiliate_id;

		$db->setQuery( $sql );

		return $db->loadObjectList();
	}
}
