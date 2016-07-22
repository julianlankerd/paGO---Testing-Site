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

class PagoViewAffiliates extends JViewLegacy
{
	function display(){

		$task_method = 'task_' . JFactory::getApplication()->input->getCmd('task');

		if( method_exists( $this, $task_method ) ){

			$this->app =& JFactory::getApplication();
			$this->$task_method();

			return;
		}

		JToolBarHelper::title( JText::_( 'PAGO_AFFILIATES_MANAGER' ), 'generic.png' );
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::addNewX();
        JToolBarHelper::editListX();
        JToolBarHelper::deleteList();

        // Get data from the model
        $attributes = $this->get( 'Data');

		$pagination = $this->get('pagination');

		$ordering = true;

		/* Call the state object */
		$state = $this->get( 'state' );

		/* Get the values from the state object that were inserted in the model's construct function */
		$lists['order_Dir'] = $state->get( 'filter_order_Dir' );
		$lists['order']     = $state->get( 'filter_order' );
		$lists['search']    = $state->get( 'filter_search' );

		$this->assignRef( 'lists', $lists );
        $this->assignRef( 'pagination', $pagination );
       	$this->assignRef( 'attributes', $attributes );
		$this->assignRef( 'ordering', $ordering );

        parent::display();
	}

	function task_publish()
	{
		$model = JModelLegacy::getInstance('Attributes','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'ARRAY' ) );
		$model->publish();

		$msg 	= JText::_('Attribute Published');
		$link 	= 'index.php?option=com_pago&view=attributes';

		$this->app->controller->setRedirect($link, $msg);
	}

	function task_unpublish()
	{
		$model = JModelLegacy::getInstance('Attributes','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'ARRAY' ) );
		$model->unpublish();

		$msg 	= JText::_('Attribute Unpublished');
		$link 	= 'index.php?option=com_pago&view=attributes';

		$this->app->controller->setRedirect($link, $msg);
	}

	function task_saveorder()
	{
		// TODO: needs to be fixed
		//JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid = JFactory::getApplication()->input->post->get( 'cid', array(), 'array' );

		$model = JModelLegacy::getInstance('Attribute','PagoModel');
		if ( $model->saveOrder( $cid ) ) {
			$msg = JText::_( 'ATTRIBUTES_ORDERED' );
		} else {
			$msg = $model->getError();
		}

		$this->app->controller->setRedirect( 'index.php?option=com_pago&view=attributes', $msg );
	}

	function task_orderup()
	{
		// TODO: needs to be fixed
		//JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid = JFactory::getApplication()->input->post->get( 'cid', array(), 'array' );

		if ( !isset( $cid[0] ) && !$cid[0] ) {
			$this->app->controller->setRedirect(
				'index.php?option=com_pago&view=attributes',
				JText::_( 'ATTRIBUTES_NOT_SELECTED' )
			);
			return false;
		}
		$id = (INT) $cid[0];

		$model = JModelLegacy::getInstance('Attribute','PagoModel');
		if ( $model->moveAttr( $id, -1 ) ) {
			$msg = JText::_( 'ATTRIBUTES_ORDERED_UP' );
		} else {
			$msg = $model->getError();
		}

		$this->app->controller->setRedirect( 'index.php?option=com_pago&view=attributes', $msg );
	}

	function task_orderdown()
	{
		// TODO: needs to be fixed
		//JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid = JFactory::getApplication()->input->post->get( 'cid', array(), 'array' );

		if ( !isset( $cid[0] ) && !$cid[0] ) {
			$this->app->controller->setRedirect(
				'index.php?option=com_pago&view=attributes',
				JText::_( 'ATTRIBUTES_NOT_SELECTED' )
			);
			return false;
		}
		$id = (INT) $cid[0];

		$model = JModelLegacy::getInstance('Attribute','PagoModel');
		if ( $model->moveAttr( $id, 1 ) ) {
			$msg = JText::_( 'ATTRIBUTES_ORDERED_DOWN' );
		} else {
			$msg = $model->getError();
		}

		$this->app->controller->setRedirect( 'index.php?option=com_pago&view=attributes', $msg );
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function task_edit()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);

		$view 	= $this->app->controller->getView('Attribute','html','PagoView');
		$model 	= JModelLegacy::getInstance('Attribute','PagoModel');

		$view->setModel($model, true);
		$view->setLayout('form');
		$view->display();
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function task_add()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);

		$view 	= $this->app->controller->getView('Attribute','html','PagoView');
		$model 	= JModelLegacy::getInstance('Attribute','PagoModel');

		$view->setModel($model, true);
		$view->setLayout('form');
		$view->display();
	}

	function task_save()
	{
		$model = JModelLegacy::getInstance('Attribute','PagoModel');

		if ($model->store()) {
			$msg = JText::_( 'Attribute Saved!' );
		} else {
			$msg = JText::_( 'Error Saving Attribute' );
		}
	 	$msg = JText::_( 'Attribute Saved!' );
		$link = 'index.php?option=com_pago&view=attributes';
		//die();
		$this->app->controller->setRedirect($link, $msg);
	}

	function task_apply()
	{
		$model = JModelLegacy::getInstance('Attribute','PagoModel');
		$id = $model->store();

		if (!$id) {
			$id = JFactory::getApplication()->input->get('id',  0, 'int');
		}

		$msg = JText::_( 'Attribute Saved!' );
		$link = 'index.php?option=com_pago&view=attribute&task=edit&layout=form&cid[]=' . $id;

		$this->app->controller->setRedirect($link, $msg);
	}

	function task_remove()
	{
		$model = JModelLegacy::getInstance('Attributes','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'ARRAY' ) );
		$model->remove();

		$msg 	= JText::_('Attribute Removed');
		$link 	= 'index.php?option=com_pago&view=attributes';
		$this->app->controller->setRedirect($link, $msg);
	}
}
