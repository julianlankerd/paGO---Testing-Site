<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');


$listOrder	= $listOrder;
$listDirn	= $listDirn;

PagoHtml::apply_layout_fixes();
PagoHtml::uniform();
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items );

?>
<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
			<div id="pg-button-search" class="pg-button pg-button-grey pg-button-search" tabindex="0"><div><button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button></div></div>
			<div id="pg-button-clear" class="pg-button pg-button-grey pg-button-clear" tabindex="0"><div><button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button></div></div>
		</div>
	</fieldset>


<div id="editcell">	
	<div class="pg-table-wrap">
		<table class="pg-table pg-repeated-rows pg-promo-manager" id="pg-promo-manager">
			<thead>
				<tr class="pg-main-heading">
					<td colspan="7">
						<div class="pg-background-color">
							<?php echo JText::_( 'PAGO_PROMOS_MANAGER' ); ?>
						</div>
					</td>
				</tr>
				<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
					<td class="pg-checkbox">
						<input type="checkbox" id="checkall" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
						<label for="checkall"></label>
					</td>
					<td class="pg-promo-id <?php if ( $listOrder == 'id' ) { echo 'pg-currently-sorted'; } ?>">
						<?php
							echo JHTML::_( 'grid.sort', JText::_( 'PAGO_PROMO_ID' ), 'id', $listDirn, $listOrder);
							if ( $listOrder == 'id' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
					</td>
					<td class="pg-promo-name <?php if ( $listOrder == 'name' ) { echo 'pg-currently-sorted'; } ?>">
						<?php
							echo JHTML::_( 'grid.sort', JText::_( 'PAGO_PROMO_NAME' ), 'name', $listDirn, $listOrder);
							if ( $listOrder == 'name' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
					</td>
					<td class="pg-published <?php if ( $listOrder == 'published' ) { echo 'pg-currently-sorted'; } ?>">
						<?php
							echo JHTML::_( 'grid.sort', JText::_( 'PAGO_PUBLISHED' ), 'published', $listDirn, $listOrder);
							if ( $listOrder == 'published' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
					</td>
					<td class="pg-promo-category <?php if ( $listOrder == 'category' ) { echo 'pg-currently-sorted'; } ?>">
						<?php
							echo JHTML::_( 'grid.sort', JText::_( 'PAGO_CATEGORY' ), 'category', $listDirn, $listOrder);
							if ( $listOrder == 'category' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
					</td>
					<td class="pg-promo-start-date <?php if ( $listOrder == 'sale_start' ) { echo 'pg-currently-sorted'; } ?>">
						<?php
							echo JHTML::_( 'grid.sort', JText::_( 'PAGO_PROMO_START_DATE' ), 'sale_start', $listDirn, $listOrder);
							if ( $listOrder == 'sale_start' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
					</td>
					<td class="pg-promo-end-date <?php if ( $listOrder == 'sale_end' ) { echo 'pg-currently-sorted'; } ?>">
						<?php
							echo JHTML::_( 'grid.sort', JText::_( 'PAGO_PROMO_END_DATE' ), 'sale_end', $listDirn, $listOrder);
							if ( $listOrder == 'sale_end' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
					</td>
				</tr>			
			</thead>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row =& $this->items[$i];
				$checked	= JHTML::_( 'grid.id', $i, $row->id );

				?>
				<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
					<td class="pg-checkbox">
						<?php echo $checked; ?>
						<label for="cb<?php echo $i ?>"></label>
					</td>

					<td class="pg-id">
						<?php echo $row->id; ?>
					</td>
					<td class="pg-promo-name">
						<?php  
						$link = JRoute::_( 'index.php?option=com_pago&view=sales&task=edit&cid[]='. $row->id );
						?>
						<a href="<?php echo $link ?>"><?php echo $row->name; ?></a>
					</td>
					<td class="pg-published">
						<?php if($row->published ): 
						$link = JRoute::_( 'index.php?option=com_pago&view=sales&task=unpublish&cid[]='. $row->id );
						?>
						
						 <a href="<?php echo $link ?>"><img src="images/tick.png" /></a>
						
						<?php else: 
						$link = JRoute::_( 'index.php?option=com_pago&view=sales&task=publish&cid[]='. $row->id );
						?>
						
						<a href="<?php echo $link ?>"><img src="images/publish_x.png" /></a>
						
						<?php endif ?>
					</td>
					<td class="pg-promo-category">
						<?php echo $row->category; ?>
					</td>
					<td class="pg-promo-state-date">
						<?php echo $row->sale_start; ?>
					</td>
					<td class="pg-promo-end-date">
						<?php echo $row->sale_end; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>	
		</table>
	</div>
	<div class="pg-pagination">
		<?php echo PagoHtml::pago_pagination($this->pagination); ?>
		<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
	</div>
</div>

<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

<input type="hidden" name="option" value="com_pago" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="sales" />
 
</form>

<?php PagoHtml::pago_bottom(); ?>