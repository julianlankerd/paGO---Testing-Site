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

PagoHtml::behaviour_jquery();
PagoHtml::behaviour_jqueryui();
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

$document = JFactory::getDocument();
$document->addScript("/administrator/components/com_pago/javascript/jquery-ui/js/sortablelist.js");
$document->addStyleSheet("/administrator/components/com_pago/javascript/jquery-ui/css/sortablelist.css");


JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items,'',$this->top_menu );

?>
<script type="text/javascript">
	var limitstart = <?php echo $this->pagination->limitstart; ?>;
	var ids = new Array();
	jQuery( "#pg-attributes-manager tbody" ).sortable({
	  stop: function( event, ui ) {
	  	var ids = new Array();
	  	jQuery.each(jQuery("#pg-attributes-manager tbody tr"), function () {
	  		ids.push(jQuery(this).attr('orderId'));
		});

		jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "saveorder",
					dataType: 'json',
					limitstart: limitstart,
					ids: ids,
				}),
				success: function( response ) {
					obj = JSON.parse(response);
					jQuery.each(obj, function( index, value ) {
						jQuery("#order-title_"+index).html(value);
					});
				}
			});
	   }
	});
jQuery(document).ready(function(){
	jQuery("#pg-button-search").on('click',function(){	
    	if(jQuery("#filter_search").val()==""){
    	
	    	return false;
	   	}
	});
	jQuery("#pg-button-clear").on('click',function(){	
    	if(jQuery("#filter_search").val()!="" ){
		  document.id('filter_search').value='';
		  this.form.submit();
	   	}else{
	    	return false;
	    }
	});
});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get( '_name' ) ); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class = "no-margin pg-mb-20">
		<div class="filter-search">
			<input class = "pg-left pg-mr-20" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?>" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="submit" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button id="pg-button-clear" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button" tabindex="-1" ><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="pg-filter-options">
			<div class = "pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>
		</div>

	</fieldset>

	<?php
		$orderSort = '';
		$nameSort = '';
		$typeSort = '';
		$createdSort = '';

		switch($listOrder){
			case 'ordering':
				$orderSort = 'pg-sorted-'.$listDirn;
				break;
			case 'name':
				$nameSort = 'pg-sorted-'.$listDirn;
				break;
			case 'type':
				$typeSort = 'pg-sorted-'.$listDirn;
				break;
			case 'created':
				$createdSort = 'pg-sorted-'.$listDirn;
				break;
		}
	?>

<div class="pg-table-wrap">
	<div class = "pg-container-header">
		<?php echo JText::_( 'PAGO_ATTRIBUTES_MANAGER' ); ?>
	</div>
	<div class = "pg-white-bckg pg-border pg-pad-20">
		<table class="pg-table pg-repeated-rows pg-attributes-manager" id="pg-attributes-manager">
			<thead>
				<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
					<td class="pg-checkbox">
						<input type="checkbox" name="checkall-toggle" id="checkall" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'td.pg-checkbox input');" />
						<label for="checkall"></label>
					</td>

					<td class="pg-sort1" style="width: 10%;">
						<div class="pg-sort-indicator-wrapper">
							<?php //echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder)
							echo JText::_('JGRID_HEADING_ORDERING');
							; ?>
						</div>
					</td>

					<td class="pg-item-name <?php echo $nameSort; ?>">
						<div class="pg-sort-indicator-wrapper">
							<?php echo JHtml::_('grid.sort', 'PAGO_ITEM_NAME', 'name', $listDirn, $listOrder); ?>
						</div>
					</td>

					<td class="pg-type <?php echo $typeSort; ?>">
						<div class="pg-sort-indicator-wrapper">
							<?php echo JHtml::_('grid.sort', 'PAGO_TYPE', 'type', $listDirn, $listOrder); ?>
						</div>
					</td>
					<td class="pg-created <?php echo $createdSort; ?>">
						<div class="pg-sort-indicator-wrapper">
							<?php echo JHtml::_('grid.sort', 'PAGO_CREATED', 'created', $listDirn, $listOrder); ?>
						</div>
					</td>
					<!-- <td class="pg-modified <?php //if ( $listOrder == "modified" ) { //echo 'pg-currently-sorted'; } ?>">
						<div class="pg-sort-indicator-wrapper">
						<?php
							//echo JHtml::_('grid.sort',  'PAGO_LAST_MODIFIED', 'modified', $listDirn, $listOrder);
							//if ( $listOrder == 'modified' ) {
								//echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							//}
						?>
						</div>
					</td> -->
				</tr>
			</thead>
			<tbody class="ui-sortable">
			<?php foreach ($this->items as $i => $item) :
						$ordering	= ($listOrder == 'ordering');
			?>
				<tr id="drag-item-<?php echo $item->id; ?>" class="pg-table-content pg-row<?php echo $i % 2; ?>" orderId="<?php echo $item->id; ?>" >
					<td class="pg-checkbox">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						<label for="cb<?php echo $i ?>"></label>
					</td>
					<td class="pg-sort" id="order-title_<?php echo $item->id; ?>">
						<span class="pg-sort-handle"  title="<?php echo JText::_('PAGO_CLICK_FOR_SORT');?>"></span>
						<input type="hidden" name="ordering" value="<?php echo $item->id; ?>" />					
					</td>
					<td class="pg-item-name">
						<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=attributes&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $item->id); ?>"><?php echo $item->name; ?></a>
					</td>
					<td class="pg-item-type">
						<?php echo JText::_( 'PAGO_ATTRIBUTE_TYPE_NAME_'.$item->type ); ?>
					</td>
					<td class="pg-created">
						<!--<?php echo $item->created;?>-->
						<div class = "product-created-date"><?php echo JHTML::_('date', $item->created , JText::_('Y-m-d')); ?></div>
						<div class = "product-reated-time"><?php echo JHTML::_('date', $item->created , JText::_('H:i:s')); ?></div>
					</td>
					<!-- <td class="pg-item-modified">
						<?php //echo $item->modified;?>
					</td> -->
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
		<input type="hidden" name="controller" value="attributes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">

	jQuery(document).ready(function(){
		jQuery('table.pg-attributes-manager tbody').sortable({
			handle: 'span.pg-sort-handle',
			opacity: 0.6,
			scroll: true,
			cursor: 'move',
			axis: 'y',
			start: function(event, ui) {
				jQuery('.ui-sortable-placeholder').html('<td colspan=\"10\"><div class=\"pg-placeholder\">Drop item row here.</div></td>');
			},
			stop: function(event, ui ) {
				var idArray = [];
					jQuery('.ui-sortable tr').each(function () {
					idArray.push(this.id);
				});

				jQuery.ajax({
					type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=attributes&task=attributeOrdering&id=' +idArray+ '&async=1',
				});
			}
		});
		
	})

</script>
<?php PagoHtml::pago_bottom();
