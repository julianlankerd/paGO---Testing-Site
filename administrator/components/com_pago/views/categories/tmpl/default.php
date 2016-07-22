<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die;

$doc = JFactory::getDocument();

$doc->addScriptDeclaration("
	jQuery(function(){
		jQuery('li#toolbar-tree').children('a').css('outline-width', '0').css('outline-style', 'none')
		.attr('onClick', '')
		.addClass('modal')
		.attr('rel', '{handler: \'iframe\', size: {x: 790, y: 590}, onClose:function(){window.parent.location.reload()} }')
		.attr('href', 'index.php?option=com_pago&view=categories&controller=categories&task=tree&tmpl=component');
	});
");

$doc->addStyleDeclaration('
	.icon-32-tree {
		background-image: url('.JURI::base().'components/com_pago/css/images/tree.png);
	}
');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_banners.category');
$saveOrder	= $listOrder=='ordering';


JHtml::_('behavior.keepalive');

PagoHtml::apply_layout_fixes();
PagoHtml::uniform();
PagoHtml::pago_truncate_description();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

//PagoHtml::pago_top( $menu_items );
PagoHtml::pago_top( $menu_items,'',$this->top_menu );

// if ($saveOrder)
$saveOrderingUrl = 'index.php?option=com_pago&task=saveOrderAjax&tmpl=component';
	PagoHtml::sortable( 'pg-categories-manager', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
// }

?>
<script type="text/javascript">
jQuery(document).ready(function(){
		jQuery("#pg-button-search").on('click',function(){	
	    	if(jQuery("#filter_search").val()==""){
		    	return false;
		   	}
		});
		jQuery("#pg-button-clear").on('click',function(){
		
	    	if(jQuery("#filter_search").val()!="" || jQuery("#filter_published").val!=""){

			 	jQuery("#filter_search").val("");
			 	jQuery("#filter_published").val("");
			   
			  this.form.submit();
		   	}else{
		    	return false;
		    }
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
		
			if(pressbutton == 'publish' || pressbutton == 'unpublish' || pressbutton == 'remove' || pressbutton == 'copy' || pressbutton == 'edit'){
				if (form.boxchecked.value == 0)
				{
					alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_CATEGORY');?>');
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
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get( '_name' ) ); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class = "no-margin pg-mb-20">
		<div class="filter-search">
			<input class = "pg-left pg-mr-20" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button id="pg-button-clear" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="pg-filter-option">
			<div class = "pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>

	        <div class="filter-published pg-right pg-mr-20">
				<select name="filter_published" id="filter_published" class="inputbox" onchange="this.form.submit()">
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
					echo JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.published'));?>
				</select>
			</div>
		</div>
	</fieldset>

	<?php
		$idSort = '';
		$orderingSort = '';
		$publishSort = '';
		$nameSort = '';
		$descSort = '';

		switch($listOrder){
			case 'id':
				$idSort = 'pg-sorted-'.$listDirn;
				break;
			case 'lft':
				$orderingSort = 'pg-sorted-'.$listDirn;
				break;
			case 'published':
				$publishSort = 'pg-sorted-'.$listDirn;
				break;
			case 'name':
				$nameSort = 'pg-sorted-'.$listDirn;
				break;
			case 'description':
				$descSort = 'pg-sorted-'.$listDirn;
				break;
		}
	?>

	<div class="pg-table-wrap">
		<div class = "pg-container-header">
			<?php echo JText::_( 'PAGO_CATEGORIES_MANAGER' ); ?>
		</div>
		<div class = "pg-white-bckg pg-border pg-pad-20">
			<table class="pg-table pg-repeated-rows pg-categories-manager" id="pg-categories-manager">
				<thead>
					<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
						<td class="pg-checkbox">
							<input type="checkbox" name="checkall-toggle" id="checkall" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'tbody td.pg-checkbox input');" />
							<label for="checkall"></label>
						</td>

						<td class="pg-sort <?php echo $orderingSort; ?>">
							<?php echo JHtml::_('grid.sort', '', 'lft', $listDirn, $listOrder); ?>
						</td>

		                <td class="pg-id <?php echo $idSort; ?>">
							<?php echo JHtml::_('grid.sort', 'PAGO_ID', 'id', $listDirn, $listOrder); ?>
						</td>

						<td class="pg-published <?php echo $publishSort; ?>">
							<div class="pg-published">
								<?php echo JHtml::_('grid.sort', 'PAGO_PUBLISHED', 'published', $listDirn, $listOrder); ?>
							</div>
						</td>

						<td class="pg-category-name <?php echo $nameSort; ?>">
							<div class="pg-sort-indicator-wrapper">
								<?php echo JHtml::_('grid.sort', 'PAGO_CATEGORIES_CATEGORY_NAME', 'name', $listDirn, $listOrder); ?>
							</div>
						</td>
		                
		                <td class="pg-description <?php echo $descSort; ?>">
		                	<div class="pg-sort-indicator-wrapper">
								<?php echo JHtml::_('grid.sort', 'PAGO_CATEGORIES_CATEGORY_DESCRIPTION', 'description', $listDirn, $listOrder); ?>
							</div>
						</td>
		                
						<!-- <td class="pg-modified <?php if ( $listOrder == 'modified_time' ) { echo 'pg-currently-sorted'; } ?>">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort', 'PAGO_CATEGORIES_MODIFIED', 'modified_time', $listDirn, $listOrder);
								if ( $listOrder == 'modified_time' ) {
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td>
						<td class="pg-created <?php if ( $listOrder == 'created_time' ) { echo 'pg-currently-sorted'; } ?>">
							<div class="pg-sort-indicator-wrapper">
							<?php
								echo JHtml::_('grid.sort', 'PAGO_CREATED', 'created_time', $listDirn, $listOrder);
								if ( $listOrder == 'created_time' ) {
									echo '<span class="pg-sorted-' . $listDirn . '"></span>';
								}
							?>
							</div>
						</td> -->
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) : 


				$orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
				if ($item->level > 1)
					{
						$parentsSTRs = PagoHelper::getParentCategories($item->id);

						$parentsSTR = PagoHelper::getParents($parentsSTRs);
					}
					else
					{
						$parentsSTR = "";
					}
				?>
					<tr class="pg-table-content pg-row<?php echo $i % 2; ?>"
					 sortable-group-id="<?php echo $item->parent_id; ?>"
					 item-id="<?php echo $item->id; ?>"
					 parents="<?php print_r($parentsSTR);?>" 
					 level="<?php echo $item->level; ?>"
					 >
					
						<td class="pg-checkbox">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							<label for="cb<?php echo $i ?>"></label>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" />
						
						</td>

						<td class="pg-sort">
							<div class="pg-sort">
								<span></span>
								<?php //echo $item->order; ?>
							</div>
						</td>

		                <td class="pg-id">
							<?php echo $item->id;?>
						</td>

						<td class="pg-published">
		                	<?php echo PagoHelper::published( $item, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="categories" rel="' .$item->id. '"' ); ?>
						</td>

						<td class="pg-category-name">
							<?php echo $item->editlink; ?></a>
						</td>
		                
		                <td class="pg-description">
							<?php
								if ( $item->description ) {
									$max_desc_length = 60;
									$category_desc_length = strlen($item->description);
									if ( $category_desc_length > $max_desc_length ) {
										$html_output  = '<span class="pg-description-read-more">' . JText::_( 'PAGO_CATEGORIES_READ_MORE' ) . '</span>';
										$html_output .= '<span class="pg-short-description">' . substr(strip_tags($item->description), 0, $max_desc_length) . '</span>';
										$html_output .= '<span class="pg-short-description-ellipsis">...</span>';
										$html_output .= '<span class="pg-long-description">' .  substr(strip_tags($item->description), $max_desc_length) . '</span>';
										echo $html_output;
									} else {
										echo $item->description;
									}
								}
							?>
						</td>
						
						<!-- <td class="pg-modified-time">
							<?php echo $item->modified_time ?>
						</td>
						<td class="pg-created-time">
							<?php echo $item->created_time?>
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
        <input type="hidden" name="controller" value="categories" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();