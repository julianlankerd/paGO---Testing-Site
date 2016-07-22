<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class PagoViewCustomers extends JViewLegacy
{
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */

	public function display( $tpl = null )
	{

		switch( $this->_layout ){
			case 'form': $this->display_form(); parent::display( $tpl ); return;
			case 'edit_address': $this->display_editform(); parent::display( $tpl ); return;
		}


		$this->items		= $this->parse_items( $this->get( 'Items' ) );
		$this->pagination	= $this->get( 'Pagination' );
		$this->state		= $this->get( 'State' );
		$customers          = $this->getModel( 'Customers','' );
		//$this->users        = $this->parse_items($customers->getJUsers($this->pagination));

		$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );

		$this->countries = array_flip( $user_fields_model->get_countries() );

		if( $country = $this->state->get('filter.country') ){
			$this->states = $user_fields_model->get_countries_states( $country );
			$this->states = $this->states['options'];

			if( $state = $this->state->get('filter.state') ){
				$this->cities = $this->get( 'Cities' );
			}
		}

		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		//$this->addToolbar();

		////////// Our tool bar
		$top_menu[] = array('task' => 'add', 'text' => JTEXT::_('PAGO_NEW'), 'class' => 'new pg-btn-medium pg-btn-green pg-btn-dark');
		$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'remove', 'text' => JTEXT::_('PAGO_DELETE'), 'class' => 'delete pg-btn-medium pg-btn-dark');
		
		$this->assignRef( 'top_menu',  $top_menu );

		parent::display($tpl);
	}
	
	function display_editform(){
		JToolBarHelper::cancel();
	}

	function display_form(){

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		$top_menu[] = array('task' => 'save', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'new pg-btn-medium pg-btn-green pg-btn-dark');
        $this->assignRef( 'top_menu',  $top_menu );
        
		$JoomlaUsers = $this->get( 'Users' );
		Pago::load_helpers( 'pagoparameter' );
		
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pago'.DS.'tables' );

		$row = JTable::getInstance( 'Userinfo', 'Table' );

		$user_info = array( 'groups'=>array(), 'billing'=>0, 'shipping'=>0 );

		$cid = 0;

		if( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) || JFactory::getApplication()->input->get( 'copy' ) ){
			$cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
			$cid = $cid[0];

			$row->load( $cid );

			$user_info = Pago::get_instance( 'users' )->get( $row->user_id );
		}

		$db = JFactory::getDBO();

		$sql = "SELECT groups.group_id
			FROM #__pago_groups_users as groups_users

					LEFT JOIN #__pago_groups as groups
					ON groups_users.group_id = groups.group_id

				WHERE groups_users.user_id={$row->user_id}";

		$db->setQuery( $sql );


		$user_info['groups']['groups'] = $db->loadResultArray();

		//$ini = $this->ini_encode( $user_info['groups'] );

		// $bind_data = array(
		// 	'grouplist' => $user_info['groups'],
		// 	'address_billing' => $user_info['billing'],
		// 	'address_mailing' => $user_info['mailing'],
		// 	'details' => $user_info['billing'],
		// );

		//$params = new PagoParameter( $bind_data,  dirname( __FILE__ ) . '/params.xml' );

		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

		// $this->assign( 'grouplist', $params->render( 'grouplist', 'grouplist' ) );
		// $this->assign( 'address_billing', $params->render( 'address_billing', 'address_billing', JText::_( 'PAGO_CUSTOMERS_BILLING_ADDRESS' ) ) );
		// $this->assign( 'address_mailing', $params->render( 'address_mailing', 'address_mailing', JText::_( 'PAGO_CUSTOMERS_MAILING_ADDRESS' ) ) );

		if( JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) && !JFactory::getApplication()->input->get( 'copy' ) ){
			$this->assign( 'user', JFactory::getUser( $row->user_id ) );
		}

		if( JFactory::getApplication()->input->get( 'copy' ) ){
			JFactory::getApplication()->input->set( 'cid', false );
		}

		$all_users = Pago::get_instance( 'users' )->getAllUsers(true);

		//$this->assign( 'details', $params->render( 'details', 'details' ) );
		$this->assign( 'users', $JoomlaUsers );
		$this->assign( 'id', $cid );
		$this->assign( 'all_users', $all_users);

		$this->assign( 'user_id', $row->user_id );
	}

	function display_copy(){
		$cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );

		Pago::load_helpers( 'pagoparameter' );

		$cids = $cid[0];
		$cids = explode( ',', $cids );
		$model->setState( 'cid', $cids );

		$params = new JParameter( false, $cmp_path . 'views/item/metadata.xml' );

		$params->addElementPath( array( $cmp_path . DS . 'elements' ) );

		$this->assignRef( 'params', $params );
		$this->assign( 'cids', $cids );
		$this->assign( 'items', $model->get_items() );
		$this->assign( 'user_info', $user_info);

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::addNew('add');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');
	}

	protected function parse_items( $items ){

		return $items;
	}
}
