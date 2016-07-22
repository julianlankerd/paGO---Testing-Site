<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerPromos extends PagoController
{
	private $_view = 'promos';

	public function __construct( $default = array() )
	{
		parent::__construct( $default );
	}

	public function display()
	{
		parent::display();
	}

	function publish()
	{
		$model = JModelLegacy::getInstance('promos','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'ARRAY' ) );
		$model->publish();

		$msg 	= JText::_('PROMO_PUBLISHED');
		$link 	= 'index.php?option=com_pago&view=promos';

		$this->app->controller->setRedirect($link, $msg);
	}

	function unpublish()
	{
		$model = JModelLegacy::getInstance('promos','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'ARRAY' ) );
		$model->unpublish();

		$msg 	= JText::_('PROMO_UNPUBLISHED');
		$link 	= 'index.php?option=com_pago&view=promos';

		$this->app->controller->setRedirect($link, $msg);
	}

	function edit()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function add()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function save()
	{
		$model = JModelLegacy::getInstance('promos','PagoModel');

		if ($model->store()) {
			$msg = JText::_( 'PROMO_SAVED' );
		} else {
			$msg = JText::_( 'PROMO_SAVE_ERROR' );
		}
		$msg = JText::_( 'PROMO_SAVED' );
		$link = 'index.php?option=com_pago&view=promos';
		//die();
		$this->app->controller->setRedirect($link, $msg);
	}

	function apply()
	{
		$model = JModelLegacy::getInstance('promos','PagoModel');
		$id = $model->store();

		if (!$id) {
			$id = JFactory::getApplication()->input->getInt('id',  0);
		}

		$msg = JText::_( 'PROMO_SAVED' );
		$link = 'index.php?option=com_pago&view=promos&task=edit&cid[]=' . $id;

		$this->app->controller->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = JModelLegacy::getInstance('promos','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'ARRAY' ) );
		$model->remove();

		$msg 	= JText::_('PROMO_REMOVED');
		$link 	= 'index.php?option=com_pago&view=promos';
		$this->app->controller->setRedirect($link, $msg);
	}
}
