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

class PagoViewAttributes extends JViewLegacy
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
		}

		$this->items		= $this->parse_items( $this->get( 'Items' ) );
		$this->pagination	= $this->get( 'Pagination' );
		$this->state		= $this->get( 'State' );

		// Check for errors.
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

	function display_form()
	{
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$model = JModelLegacy::getInstance( 'attribute', 'PagoModel' );
		$attribute = $model->getData();
		$attribute->assign = $model->get_assign( $attribute->id );
		

		Pago::load_helpers( 'pagoparameter' );

		$bind_data = array(
			'params' => (array)$attribute
		);

		$params = new PagoParameter(
			$bind_data,
			$cmp_path . 'views/attributes/metadata.xml'
		);

		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

		if( !$base_params =
			$params->render(
				'params',
				'base',
				'' // JText::_( 'PAGO_ATTRIBUTE_PROPERTIES' ),
				// 'pg-attributes-base-params-options'
			)
		){
			$base_params = 'No Base Parameters found';
		}
		if( !$display_options =
			$params->render(
				'params',
				'display_options',
				'' // JText::_( 'PAGO_ATTRIBUTE_DISPLAY_PROPERTIES' ),
				// 'pg-attributes-display-properties'
			)
		){
			$display_options = 'No Base Parameters found';
		}
		if( !$values =
			$params->render(
				'params',
				'values',
				JText::_( 'PAGO_ATTRIBUTE_VALUES' ),
				'pg-inline-repeatable-rows',
				'', // PagoHelper::addCustomButton( JText::_( 'PAGO_ADD' ),
				false, // "return jQuery.attributeOpts('addField');",
				false // 'add', 'javascript:void(0);', 'toolbar', '' ) .
				// PagoHelper::addCustomButton( JText::_( 'PAGO_DELETE' ) ,
				// "return jQuery.attributeOpts( 'delField');",
				// 'delete', 'javascript:void(0);', 'toolbar', '' )
			)
		) {
				$values = 'No Extra Parameters found';
		}
		if( !$assignments =
			$params->render(
				'params',
				'assignments',
				'' // JText::_( 'PAGO_ATTRIBUTE_ASSIGNMENTS' ),
				// 'pg-attributes-assignments'
			)
		){
			$display_options = 'No Base Parameters found';
		}

		// $text = JFactory::getApplication()->input->get('cid', array(0), 'array') ? JText::_( 'Edit' ) : JText::_( 'New' );
		// JToolBarHelper::title(
		// 	JText::_( 'PAGO_ATTRIBUTES_MANAGER' ).
		// 	': <small><small>[ ' . $text.' ]</small></small>'
		// );

		$top_menu[] = array('task' => 'save', 'text' => JTEXT::_('PAGO_SAVE_AND_CLOSE'), 'class' => 'save pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'apply', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'apply pg-btn-medium pg-btn-dark pg-btn-green');
		$top_menu[] = array('task' => 'cancel', 'text' => JTEXT::_('PAGO_CANCEL'), 'class' => 'cancel pg-btn-medium pg-btn-dark');

		$this->assignRef( 'top_menu',  $top_menu );

		/*JToolBarHelper::save();
		JToolBarHelper::apply();

		if ( JFactory::getApplication()->input->get('cid', array(0), 'array') )  {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}*/

		$this->assignRef('assignments', $assignments);
		$this->assignRef('base_params', $base_params);
		$this->assignRef('display_options', $display_options);
		$this->assignRef('values', $values);
		$this->assignRef('attribute', $attribute);
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

	protected function parse_items( $items )
	{
		return $items;
	}
}