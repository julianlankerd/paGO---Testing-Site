<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_banners.category');
$saveOrder	= $listOrder=='ordering';

PagoHtml::uniform();
PagoHtml::apply_layout_fixes();
PagoHtml::pago_truncate_description();

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items );

?>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get( '_name' ) ); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?>" />
			<div id="pg-button-search" class="pg-button pg-button-grey pg-button-search" tabindex="0"><div><button type="submit" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button></div></div>
			<div id="pg-button-clear" class="pg-button pg-button-grey pg-button-clear" tabindex="0"><div><button type="button" tabindex="-1" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button></div></div>
		</div>

		<div class="pg-filter-options">

			<div class="clear"></div>
		</div>

		<div class="clear"></div>

	</fieldset>

<div class="pg-table-wrap">
	<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
		<thead>
			<tr class="pg-main-heading">
				<td colspan="8">
					<div class="pg-background-color">
						<?php echo JText::_( 'PAGO_GROUPS_MANAGER' ); ?>
					</div>
				</td>
			</tr>
			<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
				<td class="pg-checkbox">
					<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'td.pg-checkbox input');" />
					<label for="checkall"></label>
				</td>

				<td class="pg-id <?php if ( $listOrder == 'group_id' ) { echo 'pg-currently-sorted'; } ?>">
					<div class="pg-sort-indicator-wrapper">
					<?php
						echo JHtml::_('grid.sort', 'PAGO_GROUP_ID', 'group_id', $listDirn, $listOrder);
						if ( $listOrder == 'group_id' ) {
							echo '<span class="pg-sorted-' . $listDirn . '"></span>';
						}
					?>
					</div>
				</td>

				<td class="pg-name <?php if ( $listOrder == 'name' ) { echo 'pg-currently-sorted'; } ?>">
					<div class="pg-sort-indicator-wrapper">
					<?php
						echo JHtml::_('grid.sort', 'PAGO_GROUP_NAME', 'name', $listDirn, $listOrder);
						if ( $listOrder == 'name' ) {
							echo '<span class="pg-sorted-' . $listDirn . '"></span>';
						}
					?>
					</div>
				</td>
				<td class="pg-description <?php if ( $listOrder == 'description' ) { echo 'pg-currently-sorted'; } ?>">
					<div class="pg-sort-indicator-wrapper">
					<?php
						echo JHtml::_('grid.sort', 'PAGO_GROUP_DESCRIPTION', 'description', $listDirn, $listOrder);
						if ( $listOrder == 'description' ) {
							echo '<span class="pg-sorted-' . $listDirn . '"></span>';
						}
					?>
					</div>
				</td>
				<td class="pg-is-default <?php if ( $listOrder == 'isdefault' ) { echo 'pg-currently-sorted'; } ?>">
					<div class="pg-sort-indicator-wrapper">
					<?php
						echo JHtml::_('grid.sort', 'PAGO_DEFAULT', 'isdefault', $listDirn, $listOrder);
						if ( $listOrder == 'isdefault' ) {
							echo '<span class="pg-sorted-' . $listDirn . '"></span>';
						}
					?>
					</div>
				</td>
				<td class="pg-created <?php if ( $listOrder == 'created' ) { echo 'pg-currently-sorted'; } ?>">
					<div class="pg-sort-indicator-wrapper">
					<?php
						echo JHtml::_('grid.sort', 'PAGO_CREATED', 'created', $listDirn, $listOrder);
						if ( $listOrder == 'created' ) {
							echo '<span class="pg-sorted-' . $listDirn . '"></span>';
						}
					?>
					</div>
				</td>
				<td class="pg-modified <?php if ( $listOrder == 'modified' ) { echo 'pg-currently-sorted'; } ?>">
					<div class="pg-sort-indicator-wrapper">
					<?php
						echo JHtml::_('grid.sort',  'PAGO_LAST_MODIFIED', 'modified', $listDirn, $listOrder);
						if ( $listOrder == 'modified' ) {
							echo '<span class="pg-sorted-' . $listDirn . '"></span>';
						}
					?>
					</div>
				</td>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
					$ordering	= ($listOrder == 'ordering');
		?>
			<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
				<td class="pg-checkbox">
					<?php echo JHtml::_('grid.id', $i, $item->group_id); ?>
					<label for="cb<?php echo $i ?>"></label>
				</td>

				<td class="pg-item-group_id">
					<?php echo $item->group_id ?>
				</td>

				<td class="pg-item-name">
					<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=groups&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $item->group_id); ?>"><?php echo $item->name; ?></a>
				</td>
				<td class="pg-description">
					<?php
						if ( $item->description ) {
							$max_desc_length = 60;
							$category_desc_length = strlen($item->description);
							if ( $category_desc_length > $max_desc_length ) {
								$html_output  = '<span class="pg-description-read-more">' . JText::_( 'PAGO_READ_MORE' ) . '</span>';
								$html_output .= '<span class="pg-short-description">' . substr($item->description, 0, $max_desc_length) . '</span>';
								$html_output .= '<span class="pg-short-description-ellipsis">...</span>';
								$html_output .= '<span class="pg-long-description">' .  substr($item->description, $max_desc_length) . '</span>';
								echo $html_output;
							} else {
								echo $item->description;
							}
						}
					?>
				</td>
				<td class="pg-item-isdefault">
					<?php if( $item->isdefault ): ?>
						<img src="<?php echo JURI::root() ?>/components/com_pago/templates/default/images/icons/star-24.png" />
					<?php else: ?>
						<a href="index.php?option=com_pago&view=groups&controller=groups&task=setdefault&group_id=<?php echo $item->group_id ?>">
							<img src="<?php echo JURI::root() ?>/components/com_pago/templates/default/images/icons/star-inactive-24.png" />
						</a>
					<?php endif ?>
				</td>

				<td class="pg-item-created">
					<?php echo $item->created;?>
				</td>
				<td class="pg-item-modified">
					<?php echo $item->modified;?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<div class="pg-pagination">
	<?php echo PagoHtml::pago_pagination($this->pagination); ?>
	<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
</div>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="groups" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();