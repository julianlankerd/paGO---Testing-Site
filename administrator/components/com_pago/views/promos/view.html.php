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

class PagoViewPromos extends JViewLegacy
{
	function display()
	{
		$this->toolbars();
		if ( $this->_layout == 'default' ) {
			
			
			$items =& $this->get( 'Data');

			$pagination =& $this->get('pagination');

			$state =& $this->get( 'state' );

			$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
			$lists['order']     = $state->get( 'filter_order' );
			$lists['search']     = $state->get( 'filter_search' );

			$this->assignRef( 'lists', $lists );
			$this->assignRef( 'pagination', $pagination );
			
			
			
			
			
			$this->assignRef( 'items', $items );
		}

		if ( $this->_layout == 'form' ) {
			$item =& $this->get('Data');

			$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
			
			/*$bind_data = array(
			'base' => $item,
			'custom' => $item,
			'memberlist' => $item
		);
	
		Pago::load_helpers( 'pagoparameter' );

		$params = new PagoParameter( $bind_data,  $cmp_path . 'views/groups/elements.xml' );

		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );*/
			$ini = Pago::get_instance( 'config' )->ini_encode( $item );
			
			$params = new JParameter( $ini,  $cmp_path . 'views/promos/metadata.xml' );

			$params->addElementPath( array( $cmp_path . DS . 'elements' ) );

			if( !$base_params = $params->render( 'params', 'base' ) ){
				$base_params = 'No Base Parameters found';
			}

			if( !$condition_params = $params->render( 'params', 'condition' ) ){
				$condition_params = false;
			}

			if( !$product_params = $params->render( 'params', 'products' ) ){
				$product_params = false;
			}

			$this->assignRef('base_params', $base_params);
			$this->assignRef('condition_params', $condition_params);
			$this->assignRef('product_params', $product_params);
			$this->assignRef('cats', $cats);
			$this->assignRef('item', $item);
		}

		parent::display();
	}

	function toolbars()
	{
		if ( $this->_layout == 'default' ) {
			JToolBarHelper::title( JText::_( 'PAGO_PROMO_MANAGER' ), 'generic.png' );
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::addNewX();
			JToolBarHelper::editListX();
			JToolBarHelper::deleteList();
		}
		if ( $this->_layout == 'form' ) {
			$text = JFactory::getApplication()->input->get('cid', array(0), 'array') ? JText::_( 'Edit' ) : JText::_( 'New' );
			JToolBarHelper::title(
				JText::_( 'PAGO_PROMO_MANAGER' ).': <small><small>[ ' . $text.' ]</small></small>'
			);
			JToolBarHelper::save();
			JToolBarHelper::apply();

			if ( JFactory::getApplication()->input->get('cid', array(0), 'array') )  {
				JToolBarHelper::cancel();
			} else {
				JToolBarHelper::cancel( 'cancel', 'Close' );
			}
		}
	}
}
