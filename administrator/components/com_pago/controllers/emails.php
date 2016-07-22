<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerEmails extends PagoController
{
	function __construct( $default = array() )
	{
		parent::__construct($default);

		$this->redirect_to = 'index.php?' . http_build_query(array(
			'option' => JFactory::getApplication()->input->get('option'),
			'view' => JFactory::getApplication()->input->get('view')
		));

		$this->registerTask('new', 'add');
	}


	function display( $cacheable = false, $urlparams = false )
	{
		parent::display($cacheable = false, $urlparams = false);
	}
	
	function hints()
	{
		$type = JFactory::getApplication()->input->get('type', 0);
		
		$hints = PagoHtml::getMailTemplateHints($type);
		$hints = str_replace("\n\r", "\n", $hints);
		$hints = explode("\n", $hints);
		
		$hints_table = array();
		$key = null;
		
		foreach ($hints as $hint) {
			$hint = explode(',', $hint);
			
			if (!isset($hint[1])) {
				$key = $hint[0];
				continue;
			}
			
			$hints_table[$key][] = $hint;
		}
		
		foreach ($hints_table as $type => $hints) :
		?>
		
		<div class="pg-col-6">
			<div class="pg-table-wrap">
				<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
					<thead>
						<tr class="pg-main-heading pg-multiple-headings pg-sortable-table">
							<td colspan="2">
								<?php echo $type; ?>
							</td>
						</tr>
						<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
							<td>
								<?php echo JText::_('PAGO_CUSTOM_TEMPLATE_HINT_TAG'); ?>
							</td>
							<td>
								<?php echo JText::_('PAGO_CUSTOM_TEMPLATE_HINT_VALUE'); ?>
							</td>
						</tr>
					</thead>
					<tbody>
						
						<?php foreach ($hints as $hint) : ?>
						
						<tr>
							<td><?php echo $hint[0]; ?></td>
							<td><?php echo $hint[1]; ?></td>
						</tr>
						
						<?php endforeach; ?>
						
					</tbody>
				</table>
			</div>
		</div>
		
		<?php
			endforeach;
						
		exit;
	}
	
	function publish()
	{
		$this->set_published(true);
		$this->setRedirect($this->redirect_to, JText::_('PAGO_EMAILS_PUBLISHED'));
	}

	function unpublish()
	{
		$this->set_published(false);
		$this->setRedirect($this->redirect_to, JText::_('PAGO_EMAILS_UNPUBLISHED'));
	}

	private function set_published( $state = true )
	{
		JTable::addIncludePath(JPATH_COMPONENT . '/tables');

		$table = JTable::getInstance('Email', 'Table');
		if (!is_array(JFactory::getApplication()->input->get('cid', array(0), 'array')))
			$this->setRedirect($this->redirect_to, JText::_('PAGO_CID_MUST_BE_AN_ARRAY'));

		foreach (JFactory::getApplication()->input->get('cid') as $item_id)
		{
			$data = array(
				'pgemail_id' => $item_id,
				'pgemail_enable' => $state
			);

			if (!$table->bind($data))
			{
				JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
			}

			$table->store();
			$table->reset();
		}
	}

	function edit()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		JFactory::getApplication()->input->set('layout', 'form');
		parent::display();
	}

	function add()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		JFactory::getApplication()->input->set('layout', 'form');
		parent::display();
	}

	function save()
	{
		$model = JModelLegacy::getInstance('Email', 'PagoModel');

		if ( $model->store() )
		{
			$msg = JText::_('PAGO_EMAIL_SAVED');
		}
		else
		{
			$msg = JText::_('PAGO_EMAIL_ERROR');
		}
		
		$this->setRedirect($this->redirect_to, $msg);
	}

	function apply()
	{
		$model = JModelLegacy::getInstance('Email', 'PagoModel');
		$id = $model->store();

		if (! $id)
		{
			$id = JFactory::getApplication()->input->get('id', 0, 'int');
		}

		$msg = JText::_('PAGO_EMAILS_SAVED');

		$link = 'index.php?option=com_pago&view=emails&task=edit&cid[]=' . $id;
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		JTable::addIncludePath(JPATH_COMPONENT . '/tables');

		$db = JFactory::getDBO();

		$table = JTable::getInstance('Email', 'Table');

		if (!is_array(JFactory::getApplication()->input->get('cid', array(0), 'array')))
		{
			$this->setRedirect($this->redirect_to, JText::_('PAGO_CID_MUST_BE_AN_ARRAY'));
		}

		foreach (JFactory::getApplication()->input->get('cid', array(0), 'array') as $item_id)
		{
			$table->delete($item_id);
			$table->reset();
		}

		$this->setRedirect($this->redirect_to, JText::_('PAGO_EMAILS_DELETED'));
	}
}
?>
