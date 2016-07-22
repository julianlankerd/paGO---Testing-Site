<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class PagoViewCoupons extends JViewLegacy
{
	function display( $tpl = null )
	{
		$this->toolbars();

		if ( $this->_layout == 'default' ) {
			$items = $this->get( 'Data');

			$pagination = $this->get('pagination');

			$state = $this->get( 'state' );

			$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
			$lists['order']     = $state->get( 'filter_order' );
			$lists['search']     = $state->get( 'filter_search' );

			$this->assignRef( 'lists', $lists );
			$this->assignRef( 'pagination', $pagination );
			$this->assignRef( 'items', $items );
		}

		if ( $this->_layout == 'form' ) {
			$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

			$model = JModelLegacy::getInstance( 'coupon', 'PagoModel' );
			$item = $model->getData();
			$item->rules = $model->get_rules( $item->id );
			$item->events = $model->get_events( $item->id );
			$item->assign = $model->get_assign( $item->id );

			$b_data = array(
				'params' => $item
			);

			Pago::load_helpers( 'pagoparameter' );

			$params = new PagoParameter( $b_data,  $cmp_path . 'views/coupons/metadata.xml' );

			JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

			//if ( !$shipping_params = $params->render( 'params', 'shipping', JText::_( 'PAGO_ITEMS_TITLE_SHIPPING_PARAMETERS' ), 'shipping-parameters', '', false, true, true ) )
			if( !$base_params = $params->render( 'params', 'base', '', 'coupons-base-parameters', '', false, true, false ) ){
				$base_params = 'No Base Parameters found';
			}

			if( !$rule_params = $params->render( 'params', 'rules', '', 'coupons-rules-parameters no-margin', '', false, true, false, false ) ){
				$rule_params = 'No Rule Parameters found';
			}

			if( !$event_params = $params->render( 'params', 'events', '', 'coupons-events-parameters no-margin', '', false, true, false, false ) ){
				$event_params = 'No Event Parameters found';
			}

			if( !$assign_params = $params->render( 'params', 'assign', '', 'coupons-assign-parameters', '', false, true, false, false ) ){
				$assign_params = 'No Assign Parameters found';
			}

			$this->assignRef('base_params', $base_params);
			$this->assignRef('rule_params', $rule_params);
			$this->assignRef('event_params', $event_params);
			$this->assignRef('assign_params', $assign_params);
			$this->assignRef('cats', $cats);
			$this->assignRef('item', $item);
		}
		parent::display();
	}

	function toolbars()
	{
		if ( $this->_layout == 'default' ) {
			/*JToolBarHelper::title( JText::_( 'PAGO_COUPONS_MANAGER' ), 'generic.png' );
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::addNew();
			JToolBarHelper::editList();
			JToolBarHelper::deleteList();*/

			$top_menu[] = array('task' => 'publish', 'text' => JTEXT::_('PAGO_PUBLISHED'), 'class' => 'publish pg-btn-medium pg-btn-dark');
			$top_menu[] = array('task' => 'unpublish', 'text' => JTEXT::_('PAGO_UNPUBLISH'), 'class' => 'unpublish pg-btn-medium pg-btn-dark');
			$top_menu[] = array('task' => 'add', 'text' => JTEXT::_('PAGO_NEW'), 'class' => 'new pg-btn-medium pg-btn-green pg-btn-dark');
			$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
			$top_menu[] = array('task' => 'remove', 'text' => JTEXT::_('PAGO_DELETE'), 'class' => 'delete pg-btn-medium pg-btn-dark');

			$this->assignRef( 'top_menu',  $top_menu );

		}
		if ( $this->_layout == 'form' ) {
			$text = JFactory::getApplication()->input->get('cid', array(0), 'array') ? JText::_( 'Edit' ) : JText::_( 'New' );
			JToolBarHelper::title(
				JText::_( 'PAGO_COUPONS_MANAGER' ).': <small><small>[ ' . $text.' ]</small></small>'
			);

			$top_menu[] = array('task' => 'save', 'text' => JTEXT::_('PAGO_SAVE_AND_CLOSE'), 'class' => 'publish pg-btn-medium pg-btn-dark');
			$top_menu[] = array('task' => 'apply', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'unpublish pg-btn-medium pg-btn-dark');
			$top_menu[] = array('task' => 'cancel', 'text' => JTEXT::_('PAGO_CANCEL'), 'class' => 'new pg-btn-medium pg-btn-dark');

			$this->assignRef( 'top_menu',  $top_menu );

			/*JToolBarHelper::save();
			JToolBarHelper::apply();

			if ( JFactory::getApplication()->input->get('cid', array(0), 'array') )  {
				JToolBarHelper::cancel();
			} else {
				JToolBarHelper::cancel( 'cancel', 'Close' );
			}*/
		}
	}
}
