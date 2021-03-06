<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_banners.category');
/*
Pago::load_helpers( 'pagoparameter' );
$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
$params = new JParameter( false, $cmp_path . 'views/emails/metadata.xml' );
$params->addElementPath( array( $cmp_path . 'elements' ) );
print_r($params);exit;*/
$saveOrder	= $listOrder == 'ordering';
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';

PagoHtml::pago_top($menu_items, '', $this->top_menu);

?>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get('_name')); ?>" method="post" name="adminForm" id="adminForm">
	
	<fieldset id="filter-bar" class = "no-margin pg-mb-20">
		<div class="filter-search pg-left">
			<input class = "pg-left pg-mr-20" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('PAGO_SEARCH'); ?>" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			<button id="pg-button-clear" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button" ><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="pg-filter-options">
			<div class = "pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>
		</div>
	</fieldset>
	
	<?php echo PagoHtml::module_top( JText::_( 'PAGO_CUSTOM_EMAIL_TEMPLATES' ), null, null, null, null, null, null, false ); ?>
	
	<div class="pg-white-bckg pg-border pg-pad-20">
		<div class="pg-table-wrap">
			<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
				<thead>
					<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
						<td class="pg-checkbox">
							<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'td.pg-checkbox input');" />
							<label for="checkall"></label>
						</td>
						<td class="pg-mailname
						<?php
						if ($listOrder == 'pgemail_name')
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort', 'PAGO_EMAIL_TEMPLATE_NAME', 'pgemail_name', $listDirn, $listOrder);
	
								if ($listOrder == 'pgemail_name')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-price
						<?php
						if ( $listOrder == 'pgemail_type' )
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort',  'PAGO_EMAIL_TYPE', 'pgemail_type', $listDirn, $listOrder);
	
								if ($listOrder == 'pgemail_type')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-price
						<?php
						if ( $listOrder == 'template_for' )
						{
							echo 'pg-currently-sorted';
						}
						?>
						">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort',  'PAGO_EMAIL_TEMPLATE_FOR_LBL', 'template_for', $listDirn, $listOrder);
	
								if ($listOrder == 'template_for')
								{
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-published
						<?php
						if ( $listOrder == 'pgemail_enable' )
						{
							echo 'pg-currently-sorted';
						}
						?>
							">
							<div class="pg-published">
								<div class="pg-sort-indicator-wrapper">
								<?php
									echo JHtml::_('grid.sort', 'PAGO_EMAIL_PUBLISHED', 'pgemail_enable', $listDirn, $listOrder);

									if ($listOrder == 'pgemail_enable')
									{
										echo '<span class="pg-sorted-' . $listDirn . '"></span>';
									}
								?>
								</div>
							</div>
						</td>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
						<td class="pg-checkbox">
							<?php echo JHtml::_('grid.id', $i, $item->pgemail_id); ?>
							<label for="cb<?php echo $i ?>"></label>
						</td>
						<td class="pg-item-mailname">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=emails&task=edit&view=' . $this->get('_name') . '&cid[]='.(int) $item->pgemail_id); ?>">
								<?php echo $item->pgemail_name; ?></a>
						</td>
						<td class="pg-item-price">
							<?php echo $item->pgemail_type;?>
						</td>
						<td class="pg-item-price">
							<?php echo $item->template_for;?>
						</td>
		                <td class="pg-published">
			                <?php 
								$item->id = $item->pgemail_id;
								$item->published = $item->pgemail_enable;
								echo PagoHelper::published( $item, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="items" rel="' .$item->id. '"' ); 
							?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="pg-pagination">
		<?php echo PagoHtml::pago_pagination($this->pagination); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="emails" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();