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

class PagoViewItems extends JViewLegacy
{
	function display()
	{
		$callback = JFactory::getApplication()->input->get( 'callback' );

		if( $callback ){
			JFactory::getSession()->set('callback', $callback, 'pago_order');
		} else {
			$callback = JFactory::getSession()->get('callback', false, 'pago_order');
		}

        // Get data from the model
        $items =& $this->get( 'GridData');

		$pagination =& $this->get('pagination');

		// Call the state object
		$state = $this->get( 'state' );

		// Get the values from the state object that were inserted in the model's construct function
		$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
		$lists['order']     = $state->get( 'filter_order' );
		$lists['search']    = $state->get( 'filter_search' );

		$this->assign( 'callback', $callback );
		$this->assignRef( 'lists', $lists );
        $this->assignRef( 'pagination', $pagination );
       	$this->assignRef( 'items', $items );

        parent::display( 'rawgrid' );
	}
}
