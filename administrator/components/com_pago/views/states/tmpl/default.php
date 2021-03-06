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

$user   = JFactory::getUser();
$userId   = $user->get('id');
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canOrder = $user->authorise('core.edit.state', 'com_banners.category');
$saveOrder  = $listOrder=='ordering';

PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items, null, $this->top_menu );

?>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get( '_name' ) ); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="no-margin pg-mb-20">
		<div class="filter-search">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?>" class="pg-left pg-mr-20"/>
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="submit" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button id="pg-button-reset" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button"  onclick=""><?php echo JText::_('PAGO_FILTER_RESET'); ?></button>
		</div>
		<div class="pg-filter-options">
			<div class="pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>
			<div class="filter-country pg-right pg-filter-country pg-mr-20">
				<select name="filter_country_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('PAGO_SEL_COUNTRY');?></option>      
					<?php echo JHtml::_('select.options', $this->countries, 'id', 'name', $this->state->get('filter.country_id'));?>
				</select>
			</div>
			<div class="filter-published pg-right pg-filter-publish pg-mr-20">
				<select name="filter_publish" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('PAGO_SEL_STATUS');?></option>     
					<?php 
					$options = array(
					array(
						'value' => 1,
						'text' => JText::_( 'PAGO_PUBLISHED' ),
						'disable' => 0
					),
					array(
						'value' => 0,
						'text' => JText::_( 'PAGO_UNPUBLISHED' ),
						'disable' => 0
					)
					);
					echo JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.publish'));?>
				</select>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>	
	</fieldset>
	
	<div class="pg-container-header">
		<?php echo JText::_( 'PAGO_STATE_MANAGER' ); ?>
	</div>
	
	<div class="pg-content">
		<div class="pg-border pg-white-bckg pg-pad-20">
			<div class="pg-table-wrap">
				<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
					<thead>
						<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
							<td class="pg-checkbox">
								<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'td.pg-checkbox input');" />
								<label for="checkall"></label>
							</td>
										 
										 <?php $col_title='PAGO_STATE_ID'; $col_name = 'state_id' ?>
							<td class="pg-<?php echo $col_name ?> <?php if ( $listOrder == $col_name ) { echo 'pg-currently-sorted'; } ?>">
								<div class="pg-sort-indicator-wrapper">
								<?php
									echo JHtml::_('grid.sort', $col_title, $col_name, $listDirn, $listOrder);
									if ( $listOrder == $col_name ) {
										echo '<span class="pg-sorted-' . $listDirn . '"></span>';
									}
								?>
								</div>
							</td>
											
											<?php $col_title='PAGO_STATE_NAME'; $col_name = 'state_name' ?>
							<td class="pg-<?php echo $col_name ?> <?php if ( $listOrder == $col_name ) { echo 'pg-currently-sorted'; } ?>">
								<div class="pg-sort-indicator-wrapper">
								<?php
									echo JHtml::_('grid.sort', $col_title, $col_name, $listDirn, $listOrder);
									if ( $listOrder == $col_name ) {
										echo '<span class="pg-sorted-' . $listDirn . '"></span>';
									}
								?>
								</div>
							</td>
											
											<?php $col_title='PAGO_COUNTRY_ID'; $col_name = 'zone_id' ?>
							<td class="pg-<?php echo $col_name ?> <?php if ( $listOrder == $col_name ) { echo 'pg-currently-sorted'; } ?>">
								<div class="pg-sort-indicator-wrapper">
								<?php
									echo JHtml::_('grid.sort', $col_title, $col_name, $listDirn, $listOrder);
									if ( $listOrder == $col_name ) {
										echo '<span class="pg-sorted-' . $listDirn . '"></span>';
									}
								?>
								</div>
							</td>
											
											<?php $col_title='PAGO_STATE_3_CODE'; $col_name = 'state_3_code' ?>
							<td class="pg-<?php echo $col_name ?> <?php if ( $listOrder == $col_name ) { echo 'pg-currently-sorted'; } ?>">
								<div class="pg-sort-indicator-wrapper">
								<?php
									echo JHtml::_('grid.sort', $col_title, $col_name, $listDirn, $listOrder);
									if ( $listOrder == $col_name ) {
										echo '<span class="pg-sorted-' . $listDirn . '"></span>';
									}
								?>
								</div>
							</td>
											
											<?php $col_title='PAGO_state_2_CODE'; $col_name = 'state_2_code' ?>
							<td class="pg-<?php echo $col_name ?> <?php if ( $listOrder == $col_name ) { echo 'pg-currently-sorted'; } ?>">
								<div class="pg-sort-indicator-wrapper">
								<?php
									echo JHtml::_('grid.sort', $col_title, $col_name, $listDirn, $listOrder);
									if ( $listOrder == $col_name ) {
										echo '<span class="pg-sorted-' . $listDirn . '"></span>';
									}
								?>
								</div>
							</td>
							
							<?php $col_title='PAGO_PUBLISH'; $col_name = 'publish' ?>
							<td class="pg-<?php echo $col_name ?>ed <?php if ( $listOrder == $col_name ) { echo 'pg-currently-sorted'; } ?>">
								<div class="pg-sort-indicator-wrapper">
								<?php
									echo JHtml::_('grid.sort', $col_title, $col_name, $listDirn, $listOrder);
									if ( $listOrder == $col_name ) {
										echo '<span class="pg-sorted-' . $listDirn . '"></span>';
									}
								?>
								</div>
							</td>			
								 
							
							<!--
										 [state_id] => 258
									[zone_id] => 12
									[state_name] => test2
									[state_3_code] => aaa
									[state_2_code] => bb
									[publish] => 1
									[params] => {"test":"SAD22","test2":"helloworld2"}-->
						</tr>
					</thead>
					<tbody>
					<?php foreach ($this->items as $i => $item) : 
								$ordering = ($listOrder == 'ordering');
					?>
						<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
							<td class="pg-checkbox">
								<?php echo JHtml::_('grid.id', $i, $item->state_id); ?>
								<label for="cb<?php echo $i ?>"></label>
							</td>
										
											<td class="pg-item-state_id">
								<?php echo $item->state_id ?>
							</td>
										 
							<td class="pg-state_name">
								<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=states&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $item->state_id); ?>">
									<?php echo $item->state_name; ?></a>
							</td>
											
											<td class="pg-zone_id">
								<?php echo $item->country_id ?>
							</td>
											
											<td class="pg-state_3_code">
								<?php echo $item->state_3_code ?>
							</td>
											
											 <td class="pg-state_2_code">
								<?php echo $item->state_2_code ?>
							</td>
							
							<td class="pg-published">
			                <?php 
			                	$item->published = $item->publish;
			                	
			                	echo PagoHelper::published( $item, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="states" rel="' .$item->country_id. '"' ); 
			                ?>
							</td>
							
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>	
		</div>
	</div>
	
	<div class="pg-pagination clear">
		<?php echo PagoHtml::pago_pagination($this->pagination); ?>
	</div>

	<div>
		<input type="hidden" name="task" value="" />
				<input type="hidden" name="controller" value="states" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_country_id" value="<?php echo $this->country_id ?>" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();
