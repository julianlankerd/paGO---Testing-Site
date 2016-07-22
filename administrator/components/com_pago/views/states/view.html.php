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

/**
 */
class PagoViewStates extends JViewLegacy
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
			case 'form':
				$this->display_form();
				parent::display( $tpl );
				return;
		}
		$country_id = JFactory::getApplication()->input->get('filter_country_id',  0);
		$user_fields_model = JModelLegacy::getInstance( 'User_fields','PagoModel' );
		$countries = $user_fields_model->get_countries( 'country_id' );

		$this->countries = array();

		foreach ( $countries as $k => $v ) {
			$this->countries[] = array(
			'id' => $v,
			'name' => $k
			);
		}

		$this->items = $this->parse_items( $this->get( 'Items' ) );

		$this->pagination = $this->get( 'Pagination' );
		$this->state      = $this->get( 'State' );

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// $this->addToolbar();
		$this->assignRef( 'country_id', $country_id );
		
		$top_menu = array(
			array(
				'task' => 'publish',
				'text' => JText::_('PAGO_PUBLISH'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'unpublish',
				'text' => JText::_('PAGO_UNPUBLISH'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'add',
				'text' => JText::_('PAGO_NEW'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark pg-btn-green'
			),
			array(
				'task' => 'edit',
				'text' => JText::_('PAGO_EDIT'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'delete',
				'text' => JText::_('PAGO_DELETE'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'cancel',
				'text' => JText::_('PAGO_CANCEL'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			)
		);
		
		$this->assignRef('top_menu', $top_menu);
		
		parent::display($tpl);
	}

	function display_form()
	{
		// JToolBarHelper::save();
		// JToolBarHelper::apply();
		// $country_id = JFactory::getApplication()->input->get('filter_country_id',  0);
		// if ( JFactory::getApplication()->input->get('cid', array(0), 'array') )  {
		// 	JToolBarHelper::cancel();
		// } else {
		// 	JToolBarHelper::cancel( 'cancel', 'Close' );
		// }

		$cid = JFactory::getApplication()->input->get('cid',  0, 'array');
		$cid = (int)$cid[0];

		$item = JTable::getInstance( 'States', 'Table' );
		$item->load( $cid );

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		if( isset( $item->params ) ){
			$item_params = json_decode( $item->params );

			if( is_object( $item_params ) ){
			foreach( $item_params as $k=>$v){
				$item->$k = $v;
			}
			}
		}

		$bind_data = array(
			'params' => $item,
			'custom' => $item
		);

		Pago::load_helpers( 'pagoparameter' );

		$params = new PagoParameter( $bind_data,  $cmp_path . 'views/states/tmpl/fields.xml' );

		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

		$base_params = $params->render( 'params', 'params', '' );
		$custom_params = $params->render( 'custom', 'custom', '' );

		$this->assignRef( 'base_params',  $base_params );
		$this->assignRef( 'custom_params', $custom_params );
		$this->assignRef( 'item', $item );
		$this->assignRef( 'country_id', $country_id );
		
		$top_menu = array(
			array(
				'task' => 'save',
				'text' => JText::_('PAGO_SAVE_AND_CLOSE'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'apply',
				'text' => JText::_('PAGO_SAVE'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark pg-btn-green'
			),
			array(
				'task' => 'cancel',
				'text' => JText::_('PAGO_CANCEL'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			)
		);
		
		$this->assignRef('top_menu', $top_menu);
		
	}

	/**
	* Add the page title and toolbar.
	*
	* @since 1.6
	*/
	protected function addToolbar()
	{
		JToolBarHelper::publish();
		JToolBarHelper::unpublish();
		JToolBarHelper::addNew('add');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');
		JToolBarHelper::cancel( 'cancel', JText::_( 'PAGO_CANCEL' ) );
	}

	protected function parse_items( $items )
	{
		return $items;
	}
}
