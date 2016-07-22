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

class PagoViewTemplates extends JViewLegacy
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
		switch ($this->_layout)
		{
			case 'form':
				$this->display_form();
				parent::display($tpl);

				return;
			case 'copy':
				$this->display_copy();
				parent::display($tpl);

				return;
		}

		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// $this->addToolbar();
		$this->items		= $this->parse_items($this->get('Items'));
		
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
				'task' => 'new',
				'text' => JText::_('PAGO_NEW'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark pg-btn-green'
			),
			array(
				'task' => 'edit',
				'text' => JText::_('PAGO_EDIT'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'remove',
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
		$cid = JFactory::getApplication()->input->get('cid', array(0), 'array');
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$model = JModelLegacy::getInstance('Template', 'PagoModel');

		Pago::load_helpers('pagoparameter');

		$model->setId($cid[0]);

		$item = $model->getData();

		// Toolbar

		$title = JText::_('PAGO_TEMPLATESS');
		if(isset($item->pgmail_name)){
			$desc = JText::_('PAGO_TEMPLATE_DESC') . $item->pgmail_name;
		}else{
			$desc = JText::_('PAGO_TEMPLATE_DESC');
		}
		$text = JFactory::getApplication()->input->get('cid', array(0), 'array') ? JText::_('PAGO_EDIT') : JText::_('PAGO_NEW');
		$title .= ': <small><small>[ ' . $text . ' ]</small></small>';

		// JToolBarHelper::title($title, 'generic.png');
		// JToolBarHelper::save();
		// JToolBarHelper::apply();

		// if (JFactory::getApplication()->input->get('cid', array(0), 'array'))
		// {
		// 	JToolBarHelper::cancel();
		// }
		// else
		// {
		// 	JToolBarHelper::cancel('cancel', JText::_('PAGO_CANCEL'));
		// }

		$item = (array) $item;

		foreach ((array) $item as $k => $v)
		{
			if (!strstr($k, '*'))
			{
				$item['basic'][ $k ] = $v;
			}
		}

		$item_bind = array(
			'params' => (array) $item['basic'],
			'base' => (array) $item['basic']
		);

		$item = (object) $item;

		// PagoParameter Overrides JParameter render to put pago class names in html
		$params = new PagoParameter($item_bind,  $cmp_path . 'views/templates/metadata.xml');
		
		JForm::addfieldpath( array(
			$cmp_path . DS . 'elements'
			)
		);

		if ( !$base_params = $params->render('params', 'base', ''))
			$base_params = JText::_('PAGO_TEMPLATE_ERROR_GENERAL_PARAMETERS');

		$this->assignRef('base_params',      			$base_params);
		$this->assignRef('item',             			$item);
		
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
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$user	= JFactory::getUser();

		JToolBarHelper::publish('publish');
		JToolBarHelper::unpublish('unpublish');
		JToolBarHelper::addNew('new');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');

	}

	protected function parse_items( $items )
	{
		return $items;
	}
}
