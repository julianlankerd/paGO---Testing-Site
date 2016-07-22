<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class menu_ref
{
	protected $db;

	public function __construct()
	{
		$this->db = JFactory::getDBO();
	}

	public function check_cids( $cid )
	{
		$cids = $this->parse_cids( $cid );

		// check for existing ids
		$query = 'SELECT 1 FROM #__pago_menu WHERE menu_id ' . $cids;

		$this->db->setQuery( $query );

		return $this->db->loadResult();
	}

	public function parse_cids( $cid )
	{
		if ( count( $cid ) > 1 ) {
			foreach( $cid as $k=>$c ) {
				$cid[$k] = (int) $c;
			}

			return 'IN ('.implode(',', $cid ) .')';
		} else if ( count( $cid ) !== 0 ) {
			return '= '. (int) $cid[0];
		}
	}

	public function delete_menu_index( $menu_ids )
	{
		$menu_ids = $this->parse_cids( $menu_ids );

		$query = 'DELETE FROM #__pago_menu WHERE menu_id ' . $menu_ids;

		$this->db->setQuery( $query );
		$this->db->query();
	}

	public function update_menu_index( $menu_ids, $published )
	{
		$menu_ids = $this->parse_cids( $menu_ids );

		$query = 'UPDATE #__pago_menu set published = ' . $published;

		$query .= ' WHERE menu_id ' . $menu_ids;

		$this->db->setQuery( $query );
		$this->db->query();
	}

	public function add_menu_index( $cid, $menu_id, $published )
	{
		$query = 'INSERT INTO #__pago_menu (`cat_id`, `menu_id`, `published`) VALUES ('.
			$cid . ', ' . $menu_id . ', ' . $published . ' )';

		$this->db->setQuery( $query );
		$this->db->query();
	}

	public function set_empty_index()
	{
		$query = 'SELECT cat_id FROM #__pago_menu WHERE menu_id = 0';

		$this->db->setQuery( $query );
		$cids = $this->db->loadResultArray();

		if ( empty( $cids ) ) {
			return;
		}

		foreach ( $cids as $cid ) {
			$query = "SELECT id FROM #__menu WHERE published != -2 ".
				"AND link LIKE '%option=com_pago&view=category&cid=" . $cid . "%'";
			$this->db->setQuery( $query );

			$mid = $this->db->loadResult();

			if ( $mid == 0 ) {
				continue;
			}

			$query = "UPDATE #__pago_menu SET menu_id = " . $mid . " WHERE cat_id = " . $cid;
			$this->db->setQuery( $query );
			$this->db->query();
		}
	}
}
