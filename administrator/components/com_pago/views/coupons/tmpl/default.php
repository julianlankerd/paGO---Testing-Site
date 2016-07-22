<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

$listOrder = $this->lists['order'];
$listDirn = $this->lists['order_Dir'];

PagoHtml::uniform();

PagoHtml::apply_layout_fixes();
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR . '/components/com_pago/helpers/menu_config.php' );
PagoHtml::pago_top( $menu_items,'',$this->top_menu );

?>
<script type="text/javascript">
jQuery(document).ready(function(){
		var form = jQuery('#adminForm');
		jQuery("#pg-button-search").on('click',function(){	
	    	if(jQuery("#search").val()==""){
		    	return false;
		   	}
		   	form.submit();
		});
});
Joomla.submitbutton = function (pressbutton) 
{
	submitbutton(pressbutton);
}

submitbutton = function (pressbutton)
{
	var form = document.adminForm;

	if (pressbutton) 
	{
		if(pressbutton == 'publish' || pressbutton == 'unpublish' || pressbutton == 'remove' || pressbutton == 'edit'){
			if (form.boxchecked.value == 0)
			{
				alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_COUPON');?>');
				return false;
			}
			else
			{
				form.task.value = pressbutton;
				form.submit();
			}
		}
		else{
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
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="editcell">
	<fieldset id="filter-bar" class = "no-margin pg-mb-20">
		<div class="filter-search">
			<input class = "pg-left pg-mr-20" type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="button"><?php echo JText::_( 'Go' ); ?></button>
			<button id="pg-button-clear" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button"  onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</div>

		<div class="pg-filter-options">
			<div class = "pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>
		</div>
	</fieldset>

	<?php
		$idSort = '';
		$nameSort = '';
		$codeSort = '';
		$publishedSort = '';
		$saleStartSort = '';
		$saleEndSort = '';

		switch($listOrder){
			case 'id':
				$idSort = 'pg-sorted-'.$listDirn;
				break;
			case 'name':
				$nameSort = 'pg-sorted-'.$listDirn;
				break;
			case 'code':
				$codeSort = 'pg-sorted-'.$listDirn;
				break;
			case 'published':
				$publishedSort = 'pg-sorted-'.$listDirn;
				break;
			case 'start':
				$saleStartSort = 'pg-sorted-'.$listDirn;
				break;
			case 'end':
				$saleEndSort = 'pg-sorted-'.$listDirn;
				break;
		}
	?>

	<div class="pg-table-wrap">
		<div class = "pg-container-header">
			<?php echo JText::_( 'PAGO_COUPONS_MANAGER' ); ?>
		</div>
		<div class = "pg-white-bckg pg-border pg-pad-20">
			<table class="pg-table pg-repeated-rows pg-coupons-manager" id="pg-coupons-manager">
				<thead>
					<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
						<td class="pg-checkbox">
							<input type="checkbox" id="checkall" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
							<label for="checkall"></label>
						</td>

						<td class="pg-id <?php echo $idSort; ?>">
							<?php echo JHTML::_( 'grid.sort', JText::_( 'PAGO_COUPON_ID' ), 'id', $listDirn, $listOrder); ?>
						</td>

						<td class="pg-coupon-name <?php echo $nameSort;  ?>">
							<?php echo JHTML::_( 'grid.sort', JText::_( 'PAGO_COUPON_NAME' ), 'name', $listDirn, $listOrder); ?>
						</td>

						<td class="pg-coupon-code <?php echo $codeSort; ?>">
							<?php echo JHTML::_( 'grid.sort', JText::_( 'PAGO_COUPON_CODE' ), 'code', $listDirn, $listOrder); ?>
						</td>

						<td class="pg-published <?php echo $publishedSort; ?>">
							<?php echo JHTML::_( 'grid.sort', JText::_( 'PAGO_PUBLISHED' ), 'published', $listDirn, $listOrder); ?>
						</td>

						<td class="pg-created <?php echo $saleStartSort; ?>">
							<?php echo JHTML::_( 'grid.sort', JText::_( 'PAGO_COUPON_START_DATE' ), 'start', $listDirn, $listOrder); ?>
						</td>

						<td class="pg-created <?php echo $saleEndSort; ?>">
							<?php echo JHTML::_( 'grid.sort', JText::_( 'PAGO_COUPON_END_DATE' ), 'end', $listDirn, $listOrder); ?>
						</td>
					</tr>
				</thead>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = $this->items[$i];
				$checked	= JHTML::_( 'grid.id', $i, $row->id );

				?>
				<tr class="pg-table-content pg-<?php echo "row$k"; ?>">
					<td class="pg-checkbox">
						<?php echo $checked; ?>
						<label for="cb<?php echo $i ?>"></label>
					</td>
					<td class="pg-id">
						<?php echo $row->id; ?>
					</td>
					<td class="pg-coupon-name">
						<?php
						$link = JRoute::_( 'index.php?option=com_pago&view=coupons&task=edit&cid[]='. $row->id );
						?>
						<a href="<?php echo $link ?>"><?php echo $row->name; ?></a>
					</td>
					<td class="pg-coupon-code">
						<?php
						$link = JRoute::_( 'index.php?option=com_pago&view=coupons&task=edit&cid[]='. $row->id );
						?>
						<a href="<?php echo $link ?>"><?php echo $row->code; ?></a>
					</td>
					<td class="pg-published">
						<?php echo PagoHelper::published( $row, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="coupons" rel="' .$row->id. '"' ); ?>
					</td>
					<td class="pg-created">
						<?php 
							$date = explode(" ", $row->start);
							$time = $date[1];
							$date = $date[0];
						?>
						<div class = "product-created-date"><?php echo $date; ?></div>
						<div class = "product-reated-time"><?php echo $time; ?></div>
					</td>
					<td class="pg-created">
						<?php 
							$date = explode(" ", $row->end);
							$time = $date[1];
							$date = $date[0];
						?>
						<div class = "product-created-date"><?php echo $date; ?></div>
						<div class = "product-reated-time"><?php echo $time; ?></div>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>
		</div>
	</div>
</div>

<div class="pg-pagination">
	<?php echo PagoHtml::pago_pagination($this->pagination); ?>
</div>

<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

<input type="hidden" name="option" value="com_pago" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="coupons" />

</form>

<?php PagoHtml::pago_bottom(); ?>
