<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class PagoTableMcampaigns extends JTable
{
	public $id;
	public $listid;
	public $listname;
	public $name;
	public $subject;
	public $fromname;
	public $fromemail;
	public $reply;
	public $confirmation;
	public $creation_date;
	public $recipients;
	public $sent;
	public $cid;
	public $cdata;
	public $folderid;

	public function __construct( $db )
	{
		$db = JFactory::getDBO();

		$db->setQuery( "
		CREATE TABLE IF NOT EXISTS `#__pago_mcampaigns` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `listid` varchar(255) NOT NULL,
		  `listname` text NOT NULL,
		  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `subject` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `fromname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `fromemail` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `reply` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `confirmation` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		  `creation_date` int(22) NOT NULL,
		  `recipients` int(22) NOT NULL,
		  `sent` tinyint(4) NOT NULL,
		  `cid` varchar(255) NOT NULL,
		  `cdata` text NOT NULL,
		  `folderid` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

		$db->query();

		parent::__construct( '#__pago_mcampaigns', 'id', $db );
	}

	/**
	 * Override check and make sure we have a name and alias for the category
	 */
	public function check()
	{
		if ( trim( $this->name ) == '' ) {
			$this->setError( JText::_('PAGO_CATEGORY_ERROR_MUST_CONTAIN_NAME') );
			return false;
		}

		return true;
	}

	/**
	 * Override bind to make sure we save meta data
	 */
	public function bind( $array, $ignore = '' )
	{
		if ( isset( $array['meta'] ) ) {
			foreach ( $array['meta'] as $k => $meta ) {
				$array[$k] = $meta;
			}
		}
		return parent::bind( $array, $ignore );
	}

	/**
	 * Override store to make sure we have an unique alias and created modified times
	 */
	public function store( $updatenulls = false )
	{
		$user = JFactory::getUser();

		if ( $this->id ) {
			$this->modified_time = date( 'Y-m-d H:i:s', time() );
			$this->modified_user_id = $user->get( 'id' );
		} else {
			$this->created_time = date( 'Y-m-d H:i:s', time() );
			$this->created_user_id = $user->get( 'id' );
		}

		if ( !$this->unique_alias() ) {
			$this->setError( JText::_('PAGO_CATEGORY_ERROR_ALIAS_UNIQUE') );
			$datenow =& JFactory::getDate();
			$this->alias .= '-' . $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
		}
		return parent::store( $updatenulls );
	}

	public function create_alias()
	{
		$this->alias = trim( $this->alias );
		if ( empty ( $this->alias ) ) {
			$this->alias = $this->name;
		}

		// setup some common replacements so the alias makes some sense
		$pattern = array(
			'/&/',
			'/^\s/', // remove extra spaces at the begining
			'/-/',
			'/\\\/',
			'/\//'
		);
		$replace = array(
			'and',
			'',
			' '
		);

		$this->alias = preg_replace( $pattern, $replace, $this->alias );

		// now we can filter output we may want to do the filtering here since we are already doing
		// a preg_replace
		$this->alias = JFilterOutput::stringURLSafe( $this->alias );

		if ( trim( str_replace( '-', '', $this->alias ) ) == '' ) {
			$datenow =& JFactory::getDate();
			$this->alias = $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
		}

	}

	public function get_item_count()
	{
		$query = 'SELECT COUNT(DISTINCT item_ref.item_id) '.
			'FROM #__pago_categories_items as item_ref '.
			'LEFT JOIN #__pago_items as item ON item.id = item_ref.item_id '.
			'WHERE item.published = 1 AND category_id IN ( '.
			'SELECT cat.id FROM #__pago_categoriesi as cat '.
			'LEFT JOIN #__pago_categoriesi as parent on parent.id = '. (int) $this->id .
			' WHERE cat.lft BETWEEN parent.lft AND parent.rgt )';
		// make sure everything gets converted
		$query = preg_replace( '/#__/', $this->_db->getPrefix(), $query );
		$this->_db->setQuery( $query );
		$this->item_count = $this->_db->loadResult();
		$this->store();
	}

	public function unique_alias()
	{
		$query = 'SELECT id FROM ' . $this->_tbl . ' WHERE alias = \'' . $this->alias . '\'';
		$this->_db->setQuery( $query );

		$alias_id = $this->_db->loadResult();
		if ( $alias_id == $this->id || !$alias_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Check that we don't have any products attached to this category
	 * TODO: add ability to check for categories with items attached
	 */
	public function can_delete( $id )
	{
		if ( $id == 1 ) {
			return false;
		}
		return true;
	}
}
