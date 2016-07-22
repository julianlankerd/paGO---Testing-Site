<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of states.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoModelItem_types extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'item.id',
				'name', 'item.name',
				'published', 'item.published',
				'default', 'item.default',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	public function getItems(){
		$items = $this->getListQuery();
		return $items;
	}
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		
		$query->from( '`#__pago_item_types` AS item' );
		
		$query->order($db->escape('`default` DESC,`id` ASC'));
		$db->setQuery($query);

		$items = $db->loadObjectList();

		return $items;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'ItemTypes', $prefix = 'PagoTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */

	public function delete($id){
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->delete($db->quoteName('#__pago_item_types'));
		//$query->where("`id` = {$id} AND `default` != 1");
		$query->where("`id` = {$id}");
		
		$db->setQuery($query);	
		
		$db->execute();
		
		return $id;
	}
	// function makeDefault() {
	// 	$id = JFactory::getApplication()->input->get( 'id', '', '' );

	// 	$row = $this->getTable( 'item_types', 'Table' );
	// 	$row->load( $id );

 //        if ( !$row->id ) {
	// 		$this->setError( JText::_( 'Select an type to make it a default' ) );
	// 		return false;
	// 	}

	// 	$row->default = 1;

	// 	if ( !$row->store() ) {
	// 		$this->setError( $row->getError() );
	// 		return false;
	// 	}

	// 	$db		= $this->getDbo();
	// 	$query	= $db->getQuery(true);

	// 	$query = "UPDATE #__pago_item_types
	// 		SET `default` = 0
	// 			WHERE `id` != {$row->id}";
	// 	$db->setQuery( $query );
	// 	$db->query();

	// 	return true;
	// }
	function store( $data = array() )
	{
		$db		= $this->getDbo();
		$row = $this->getTable( 'item_types', 'Table' );

		if(isset($data['id'])){
			$row->load( $data['id'] );
		}
		if ( !$row->bind( $data ) ) {
			$this->setError( $row->getError() );
			return false;
		}
		if ( !$row->check() ) {
			$this->setError( $row->getError() );
			return false;
		}
		if ( !$row->store() ) {
			$this->setError( $row->getError() );
			return false;
		}

		return $row->id;
	}
	// function getDefault(){
	// 	$db = JFactory::getDBO();

	// 	$sql = "SELECT * FROM #__pago_item_types where `default` = 1";

	// 	$db->setQuery($sql);
	// 	$type = $db->loadObject();
		
	// 	return $type;
	// }

	function getTagName($id){
		$this->_db->setQuery( "SELECT `name` FROM #__pago_item_types
			WHERE id = {$id}" );
		$this->_db->query();
		$item = $this->_db->loadObject();

		if( !$item ) {
			return false;
		}
		return $item;
	}
}