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
$saveOrder	= $listOrder=='item_ordering';

PagoHtml::uniform();
PagoHtml::apply_layout_fixes();
PagoHtml::thickbox();

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
Pago::load_helpers('categories');
$catnip = new catnip('pago_categories', 'com_pago', 'category', 'cid', false);
require_once(JPATH_SITE.'/components/com_pago/helpers/navigation.php');
$nav = new NavigationHelper();
PagoHtml::pago_top( $menu_items,'',$this->top_menu );
$saveOrderingUrl = 'index.php?option=com_pago&task=saveOrderAjax&tmpl=component';
PagoHtml::sortable( 'pg-items-manager', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);

?>
<script type="text/javascript">
jQuery(document).ready(function(){
		jQuery("#pg-button-search").on('click',function(){	
	    	if(jQuery("#filter_search").val()==""){
		    	return false;
		   	}
		});
		jQuery("#pg-button-reset").on('click',function(){	
	    	//if(jQuery("#filter_search").val()!="" || jQuery("#filter_primary_category").val()!="" || jQuery("#filter_type").val()!="" || jQuery("#filter_published").val()!=""){
	    		document.id('filter_search').value='';
			   	document.getElementById('filter_primary_category').value='';
			   	//document.getElementById('filter_type').value='';
			   	document.getElementById('filter_published').value='';
			   	this.form.submit();
		   	//}else{
		    //	return false;
		    //}
		});
});


Joomla.submitbutton = function (pressbutton) 
{
	submitbutton(pressbutton);
}

function tb_remove() 
{
	window.location.href = "index.php?option=com_pago&view=items";
}

submitbutton = function (pressbutton)
{
	var form = document.adminForm;

	if (pressbutton) 
	{
		if (pressbutton == 'MassMove')
		{
			var checkboxes = document.getElementsByName('cid[]');
			var vals = "";
			for (var i=0, n=checkboxes.length;i<n;i++) {
			  if (checkboxes[i].checked) 
			  {
			 	 vals += ","+checkboxes[i].value;
			  }
			}
			if (vals) vals = vals.substring(1);
			// Start height/width override
				jQuery(function (a) {
				tb_position = function () {
					if (!tb_avoid_resize) {
						var b = a("#TB_window"),
							g = 440,
							c = 400,
							d = 870 < g ? 870 : g;
						b.size() && (b.width(d - 50).height(c - 45), a("#TB_iframeContent").width(d - 50).height(c - 75), b.css({
							"margin-left": "-" + parseInt((d - 50) / 2, 10) + "px"
						}), "undefined" != typeof document.body.style.maxWidth && b.css({
							top: "20px",
							"margin-top": "0"
						}), a("#TB_title").css({
							"background-color": "#222",
							color: "#cfcfcf"
						}));
						
					}
				};
				});
			//end
			pull_upload_form_massmove(vals);
	 	}
		else
		{
			if(pressbutton == 'publish' || pressbutton == 'unpublish' || pressbutton == 'remove' || pressbutton == 'copy' || pressbutton == 'edit'){
				if (form.boxchecked.value == 0)
				{
					alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_ITEMS');?>');
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
}

function AssignCategory()
{
	var form = document.adminForm;
	var catVal = document.getElementById('assign_primary_category').value;

	if (form.boxchecked.value == 0 || catVal == '')
	{

		if (form.boxchecked.value == 0)
		{
			alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_ITEMS');?>');
			return false;
		}
		 
	 	if(catVal == '')
		{
			alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_CATEGORY');?>');
			return false;
		}
	 }
	 else
	 {
	 	form.task.value = 'AssignCategory';
		form.submit();
	 }
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get( '_name' ) ); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class = "no-margin pg-mb-20">
		<div class="filter-search">
			<input class = "pg-left pg-mr-20" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('PAGO_SEARCH_IN_TITLE'); ?>" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="submit" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<!--<div id="pg-button-clear" class="pg-button pg-button-grey pg-button-clear" tabindex="0"><div><button type="button" tabindex="-1" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button></div></div>-->
			<button id="pg-button-reset" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button"  onclick=""><?php echo JText::_('PAGO_FILTER_RESET'); ?></button>

		</div>

		<div class="pg-filter-options">
			<div class = "pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>

			<div class="filter-published pg-right pg-filter-status pg-mr-20">
				<select name="filter_published" class="inputbox" onchange="this.form.submit()" id="filter_published">
					<option value=""><?php echo JText::_('PAGO_SEL_STATUS');?></option>
					<?php
					$options = array(
					array(
						'value' => 1,
						'text' => JText::_( 'PAGO_SEL_PUBLISHED' ),
						'disable' => 0
					),
					array(
						'value' => 0,
						'text' => JText::_( 'PAGO_SEL_UNPUBLISHED' ),
						'disable' => 0
					)
					);
					echo JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.published'));?>
				</select>
			</div>

			 <div class="filter-primary_category pg-right pg-mr-20 pg-filter-primary-category">
				<select name="filter_primary_category" class="inputbox" onchange="this.form.submit()" id="filter_primary_category">
					<option value=""><?php echo JText::_('PAGO_SEL_PRIMARY_CATEGORY');?></option>
					<?php echo JHtml::_('select.options', $this->categories, 'value', 'text', $this->state->get('filter.primary_category'));?>
				</select>
			</div>
			<div class="clear"></div>
		</div>

		<div class="clear"></div>

	</fieldset>

	<?php
		$orderSort = '';
		$skuSort = '';
		$publishSort = '';
		$nameSort = '';
		$categorySort = '';
		$createdSort = '';
		$priceSort = '';

		switch($listOrder){
			case 'item_ordering':
				$orderSort = 'pg-sorted-'.$listDirn;
				break;
			case 'sku':
				$skuSort =  'pg-sorted-'.$listDirn;
				break;
			case 'published':
				$publishSort = 'pg-sorted-'.$listDirn;
				break;
			case 'name':
				$nameSort = 'pg-sorted-'.$listDirn;
				break;
			case 'primary_category':
				$categorySort = 'pg-sorted-'.$listDirn;
				break;
			case 'created':
				$createdSort = 'pg-sorted-'.$listDirn;
				break;
			case 'price':
				$priceSort = 'pg-sorted-'.$listDirn;
				break;
		}
	?>
					
<div class="pg-products-manager-container">
	<div class = "pg-container-header">
		<?php echo JText::_( 'PAGO_ITEMS_MANAGER' ); ?>
	</div>
	<div class = "pg-white-bckg pg-border pg-repeated-rows pg-pad-20">
		<table class="pg-table pg-items-manager" id="pg-items-manager">
			<thead>
				<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
					<td class="pg-checkbox">
						<input type="checkbox" name="checkall-toggle" id="checkall" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'td.pg-checkbox input');" />
						<label for="checkall"></label>
					</td>

					<td class="pg-sort <?php echo $orderSort; ?>">
						<?php echo JHtml::_('grid.sort', '', 'item_ordering', $listDirn, $listOrder); ?>
					</td>

					

					<td class="pg-name <?php echo $nameSort; ?>">
						<div class="pg-sort-indicator-wrapper">
							<?php echo JHtml::_('grid.sort', 'PAGO_ITEMS_ITEM_NAME', 'name', $listDirn, $listOrder); ?>
						</div>
					</td>

					<!--<td class="pg-links">
						<div class="pg-sort-indicator-wrapper">
						<?php
							echo JTEXT::_("COM_PAGO_FRONT_LINK");	
						?>
						</div>
					</td>-->

					<td class="pg-category <?php echo $categorySort; ?>">
						<div class="pg-sort-indicator-wrapper">
							<?php echo JHtml::_('grid.sort', 'PAGO_ITEMS_CATEGORY', 'primary_category', $listDirn, $listOrder); ?>
						</div>
					</td>
					
					<td class="pg-sku <?php echo $skuSort; ?>">
						<div class="pg-sort-indicator-wrapper">
							<?php echo JHtml::_('grid.sort', 'PAGO_SKU', 'sku', $listDirn, $listOrder);	?>
						</div>
					</td>
					
					<!--<td class="pg-type <?php if ( $listOrder == 'type' ) { echo 'pg-currently-sorted'; } ?>">
						<div class="pg-sort-indicator-wrapper">
						<?php
							echo JHtml::_('grid.sort', 'PAGO_ITEMS_TYPE', 'type', $listDirn, $listOrder);
							if ( $listOrder == 'type' ) {
								echo '<span class="pg-sorted-' . $listDirn . '"></span>';
							}
						?>
						</div>
					</td>-->

					
					<td class="pg-price <?php echo $priceSort; ?>">
						<div class="pg-sort-indicator-wrapper">
							<?php echo JHtml::_('grid.sort',  'PAGO_ITEMS_PRICE', 'price', $listDirn, $listOrder); ?>
						</div>
					</td>
					
					<td class="pg-created <?php echo $createdSort; ?>">
						<div class="pg-sort-indicator-wrapper">
							<?php echo JHtml::_('grid.sort', 'PAGO_ITEMS_CREATED', 'created', $listDirn, $listOrder); ?>
						</div>
					</td>
					
					<td class="pg-published <?php echo $publishSort; ?>">
						<div class="pg-published">
							<div class="pg-sort-indicator-wrapper">
								<?php echo JHtml::_('grid.sort', 'PAGO_PUBLISHED', 'published', $listDirn, $listOrder); ?>
							</div>
						</div>
					</td>
				</tr>
			</thead>

			<tbody>
			<?php foreach ($this->items as $i => $item) :
			$orderkey = array_search($item->id, $this->ordering);
			 ?>
				<!--<tr id="drag-item-<?php echo $item->id; ?>" class="pg-table-content pg-row<?php echo $i % 2; ?>">-->
				<tr class="pg-table-content pg-row<?php echo $i % 2; ?>" item-id="<?php echo $item->id; ?>" sortable-group-id="1">
					<td class="pg-checkbox">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						<label for="cb<?php echo $i ?>"></label>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" />
					</td>

				<!--	<td class="pg-sort">
						<span class="pg-sort-handle"  title="<?php echo JText::_('PAGO_CLICK_FOR_SORT');?>"></span>
						<input type="hidden" name="params[images_ordering][]" value="<?php echo $item->id; ?>" />
					</td>-->
					
					<td class="pg-sort">
							<div class="pg-sort">
								<span></span>
								<?php //echo $item->order; ?>
							</div>
						</td>

					

					<td class="pg-item-name">
						<div>
							<span>
							<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=items&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $item->id); ?>"><?php echo $item->name; ?></a>
							</span>
						<?php  $itemid = $nav -> getItemid($item->id, $item->primary_category); ?>
						<div style="float:right;"><a href="<?php echo JURI::ROOT() . 'index.php?option=com_pago&view=item&id=' . $item->id . '&cid=' . $item->primary_category . '&Itemid=' . $itemid ?>"  target="_black"><button type="button" class="btn btn-default btn-lg">
						 <span class="glyphicon glyphicon-open" aria-hidden="true"></span> View
						</button></a></div></div>
					</td>

					<!--<td class="pg-links">
						<?php  $itemid = $nav -> getItemid($item->id, $item->primary_category); ?>
						<a href="<?php echo JURI::ROOT() . 'index.php?option=com_pago&view=item&id=' . $item->id . '&cid=' . $item->primary_category . '&Itemid=' . $itemid ?>"  target="_black"><?php echo JTEXT::_("COM_PAGO_FRONT_LINK_URL"); ?></a>
					</td>-->

					<td class="pg-item-category">
						<span><?php $result = $catnip -> get_parent_category_tree($item->primary_category); ?></span>
					</td>
					
					<td class="pg-sku">
						<span><?php echo $item->sku;?></span>
					</td>
					
					<!--<td class="pg-item-type">
						<?php echo $item->type_name ?>
					</td>-->

					

					<td class="pg-price">
						<span><?php echo Pago::get_instance( 'price' )->format($item->price);?></span>
					</td>
					
					<td class="pg-created">
						<!--<?php echo $item->created;?>-->
						<?php 
							$date = explode(" ", $item->created);
							$time = $date[1];
							$date = $date[0];
						?>
						<div class = "product-created-date"><?php echo $date; ?></div>
						<div class = "product-reated-time"><?php echo $time; ?></div>
					</td>
					 <td class="pg-published">
		                <?php echo PagoHelper::published( $item, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="items" rel="' .$item->id. '"' ); ?>
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
		<input type="hidden" name="controller" value="items" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<script type="text/javascript">
<?php if($listOrder == "item_ordering"): ?>

/*	jQuery(document).ready(function(){
		jQuery('table.pg-items-manager tbody').sortable({
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
					data: 'option=com_pago&controller=items&task=ItemOrdering&id=' +idArray+ '&async=1',
				});
			}
		});
		
	})*/
<?php else: ?>
jQuery(document).ready(function(){
//	jQuery('td.pg-sort>a').trigger('mouseenter');
})
<?php endif ?>
</script>

<?php PagoHtml::pago_bottom();
