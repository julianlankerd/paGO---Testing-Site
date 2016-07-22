<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// No direct access.
defined('_JEXEC') or die;
?>
<?php
JFactory::getApplication()->getUserStateFromRequest('com_plugins.plugins.filter.folder', 'filter_folder', null, 'cmd');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

//$this->pagination = $model->get('pagination');
//print_r($this->pagination);
$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state',	'com_plugins');
$saveOrder	= $listOrder == 'ordering';

PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items,'',$this->top_menu );
$doc = JFactory::getDocument();
if(isset($this->message)){
	if($this->messageType = "message"){
		$doc->addScriptDeclaration( "
			jQuery(document).ready(function(){
				jQuery('#system-message-container').html('<dl id=\'system-message\'><dt class=\'message\'>Message</dt><dd class=\'message message\'><ul><li>".$this->message."</li></ul></dd></dl>');
			})
		");
	}else{
		$doc->addScriptDeclaration( "
			jQuery(document).ready(function(){
				jQuery('#system-message-container').html('<dl id=\'system-message\'><dt class=\'error\'>Error</dt><dd class=\'error message\'><ul><li>".$this->message."</li></ul></dd></dl>');
			})
		");	
	}
}
?>
<script type="text/javascript">
Joomla.submitbutton = function (pressbutton) 
{
	submitbutton(pressbutton);
}
submitbutton = function (pressbutton)
{
	var form = document.adminForm;

	if (pressbutton) 
	{
		if(pressbutton == 'publish' || pressbutton == 'unpublish' || pressbutton == 'remove' || pressbutton == 'copy' || pressbutton == 'edit')
		{
			if (form.boxchecked.value == 0)
			{
				alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_PLUGIN');?>');
				return false;
			}
			else
			{
				form.task.value = pressbutton;
				form.submit();
			}
		}
		else
		{
			form.task.value = pressbutton;
		 	try
		 	{
				form.onsubmit();
			}
			catch (e)
			{
			}
			form.submit();
		}
	}
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=plugins'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="pg-plugin-manager no-margin pg-mb-20">
		<div class = "filter-options">
			<div class="filter-select pg-right">
				<div class = "filter-access pg-left pg-mr-20">
					<select name="filter_access" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
					</select>
				</div>

				<div class = "filter-gateway pg-left pg-mr-20">
					<select name="filter_folder" class="inputbox" onchange="this.form.submit()">
						<!-- <option value=""><?php echo JText::_('COM_PLUGINS_OPTION_FOLDER');?></option> -->
						<?php echo JHtml::_('select.options', $this->folder_options, 'value', 'text', $this->state->get('filter.folder'));?>
					</select>
				</div>

				<div class = "filter-status pg-left">
					<select name="filter_state" class="inputbox pg-left pg-mb-20" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
						<?php echo JHtml::_('select.options', $this->stateOptions, 'value', 'text', $this->state->get('filter.state'), true);?>
					</select>
				</div>
			</div>
		</div>
	</fieldset>
	
	<div class="pg-table-wrap">
		<div class = "pg-container-header">
			<?php echo JText::_( 'PAGO_PLUGINS_TITLE_MANAGER' ); ?>
		</div>

		<div class = "pg-white-bckg pg-border pg-pad-20">
			<table id="pg-plugin-manager" class="pg-table pg-plugin-manager">
				<thead>
					<tr class="pg-sub-heading pg-multiple-headings">
						<td class="pg-checkbox">
							<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							<label for="checkall"></label>
						</td>
						<td class="pg-plugin-name">
							<?php echo JHtml::_('grid.sort', 'COM_PLUGINS_NAME_HEADING', 'name', $listDirn, $listOrder); ?>
						</td>
						<td class="pg-published">
							<?php echo JHtml::_('grid.sort', 'JENABLED', 'enabled', $listDirn, $listOrder); ?>
						</td>
						<!-- <td class="pg-sort">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
							<?php if ($canOrder && $saveOrder) :?>
								<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'plugins.saveorder'); ?>
							<?php endif; ?>
						</td> -->
						<!-- <td class="pg-plugin-type">
							<?php echo JHtml::_('grid.sort', 'COM_PLUGINS_FOLDER_HEADING', 'folder', $listDirn, $listOrder); ?>
						</td> -->
						<td class="pg-plugin-element">
							<?php echo JHtml::_('grid.sort', 'COM_PLUGINS_ELEMENT_HEADING', 'element', $listDirn, $listOrder); ?>
						</td>
		                <td class="pg-access-small">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
						</td>
						<!-- <td class="pg-id">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'extension_id', $listDirn, $listOrder); ?>
						</td> -->
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering	= ($listOrder == 'ordering');
					$canEdit	= $user->authorise('core.edit',			'com_plugins');
					$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
					$canChange	= $user->authorise('core.edit.state',	'com_plugins') && $canCheckin;
					?>
					<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
						<td class="pg-checkbox">
							<?php echo JHtml::_('grid.id', $i, $item->extension_id); ?>
							<label for="cb<?php echo $i ?>"></label>
						</td>
						<td class="pg-plugin-name">
							<?php if ($item->checked_out) : ?>
								<?php echo PagoHelper::checkedout($item, $i, $item->editor, $item->checked_out_time, '', $canCheckin); ?>
								<?php // echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, '', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=plugins&task=edit&cid[]='.(int) $item->extension_id); ?>">
									<?php echo $item->name; ?></a>
							<?php else : ?>
									<?php echo $item->name; ?>
							<?php endif; ?>
						</td>
						<td class="pg-published">
							<?php 
								$item->published = $item->enabled;
								$item->id = $item->extension_id;
								echo PagoHelper::published( $item, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="extension" rel="' .$item->id. '"' ); 
							?>
						</td>
						<!--
						<td class="pg-published">
							<?php echo JHtml::_('jgrid.published', $item->enabled, $i, '', $canChange); ?>
						</td>
						-->
						<!-- <td class="pg-sort">
							<?php if ($canChange) : ?>
								<?php if ($saveOrder) :?>
									<?php if ($listDirn == 'asc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->folder == $item->folder), 'plugins.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->folder == $item->folder), 'plugins.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
									<?php elseif ($listDirn == 'desc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->folder == $item->folder), 'plugins.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->folder == $item->folder), 'plugins.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
									<?php endif; ?>
								<?php endif; ?>
								<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
								<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
							<?php else : ?>
								<?php echo $item->ordering; ?>
							<?php endif; ?>
						</td> -->
						<!-- <td class="pg-plugin-type">
							<?php echo $this->escape($item->folder);?>
						</td> -->
						<td class="pg-plugin-element">
							<?php echo $this->escape($item->element);?>
						</td>
						<td class="pg-access-small">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						<!-- <td class="pg-id">
							<?php echo (int) $item->extension_id;?>
						</td> -->
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<!-- <div class="pg-pagination">
		<?php echo $this->pagination->getListFooter(); ?>
	</div> -->

    	<input type="hidden" name="option" value="com_pago" />
    	<input type="hidden" name="controller" value="plugins" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
</form>
