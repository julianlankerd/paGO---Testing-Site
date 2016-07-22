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

/*$is_publish_filter = $this->state->get('filter.published');

if (!empty($is_publish_filter) || $is_publish_filter === '0' ) {
	print_r($this->items);die;
}*/
		
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
Pago::load_helpers('categories');
$catnip = new catnip('pago_categories', 'com_pago', 'category', 'cid', false);
require_once(JPATH_SITE.'/components/com_pago/helpers/navigation.php');
$nav = new NavigationHelper();
PagoHtml::pago_top( $menu_items,'',$this->top_menu );
?>
<script type="text/javascript">
jQuery(document).ready(function(){
		var form = jQuery('#adminForm');
		jQuery("#pg-button-search").on('click',function(){	
	    	if(jQuery("#filter_search").val()==""){
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
				alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_COMMENT');?>');
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
			<input class = "pg-left pg-mr-20" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('PAGO_SEARCH_IN_TITLE'); ?>" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light" type="submit" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<!--<div id="pg-button-clear" class="pg-button pg-button-grey pg-button-clear" tabindex="0"><div><button type="button" tabindex="-1" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button></div></div>-->
			<button id="pg-button-reset" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button"  onclick="document.getElementById('filter_search').value='';document.getElementById('filter_published').value='';this.form.submit();"><?php echo JText::_('PAGO_FILTER_RESET'); ?></button>
		</div>

		<div class="pg-filter-options">
			<div class = "pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>

			<div class="filter-published pg-filter-status pg-right pg-mr-20">
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
			<div class="clear"></div>
		</div>

		<div class="clear"></div>

	</fieldset>

	<?php
		$idSort = '';
		$publishedSort = '';
		$nameSort = '';
		$textSort = '';
		$createdSort = '';

		switch($listOrder){
			case 'id':
				$idSort = 'pg-sorted-'.$listDirn;
				break;
			case 'published':
				$publishedSort = 'pg-sorted-'.$listDirn;
				break;
			case 'name':
				$nameSort = 'pg-sorted-'.$listDirn;
				break;
			case 'text':
				$textSort = 'pg-sorted-'.$listDirn;
				break;
			case 'created':
				$createdSort = 'pg-sorted-'.$listDirn;
				break;
		}
	?>

<div class="pg-table-wrap">
	<div class = "pg-container-header">
		<?php echo JText::_( 'PAGO_COMMENTS_MANAGER' ); ?>
	</div>
	<div class = "pg-white-bckg pg-border pg-pad-20">
		<table class="pg-table pg-repeated-rows pg-items-manager" id="pg-items-manager">
			<thead>
				<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
					<td class="pg-checkbox">
						<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="pago_check_all(this, 'td.pg-checkbox input');" />
						<label for="checkall"></label>
					</td>

					<td class="pg-id <?php echo $idSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_COMMENTS_ID', 'id', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-published <?php echo $publishedSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_PUBLISHED', 'published', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-name <?php  echo $nameSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_COMMENTS_AUTHOR_NAME', 'name', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-comment <?php echo $textSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_COMMENTS', 'text', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-links">
						<?php
							echo JTEXT::_("COM_PAGO_FRONT_LINK");	
						?>
					</td>

					<td class="pg-created <?php echo $createdSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_ITEMS_CREATED', 'created', $listDirn, $listOrder); ?>
					</td>
				</tr>
			</thead>
			<tbody>
			<?php $i = 0; ?>
			<?php foreach ($this->comments as $comment) : ?>
				<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
					<td class="pg-checkbox">
						<?php echo JHtml::_('grid.id', $i, $comment->id); ?>
						<label for="cb<?php echo $i ?>"></label>
					</td>

					<td class="pg-id">
						<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=comments&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $comment->id); ?>">
							<?php echo $comment->id ?>
						</a>	
					</td>

					<td class="pg-published">
		                <?php echo PagoHelper::published( $comment, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="comments" rel="' .$comment->id. '"' ); ?>
					</td>

					<td class="pg-comment">
						<?php
						if($comment->author_id == 0){
	
							echo $comment->author_name;	
						}else{
							?> 
							
							<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=customers&task=edit&view=customers&user_id='.$comment->author_id); ?>">
							<?php echo $comment->name ?>
							</a>
							<?php
						
						} ?>
					</td>

					<td class="pg-comment">
						<!-- <a href="<?php echo JRoute::_('index.php?option=com_pago&controller=comments&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $comment->id); ?>">-->
							<?php echo $comment->text ?>
						<!-- </a> -->	
					</td>

					<td class="pg-links">
						<?php  $itemid = $nav -> getItemid($comment->item_id, $comment->primary_category); ?>
						<a href="<?php echo JURI::ROOT() . 'index.php?option=com_pago&view=item&id=' . $comment->item_id . '&cid=' . $comment->primary_category . '&Itemid=' . $itemid ?>"  target="_black"><?php echo $comment->item_name ?></a>
					</td>

					<td class="pg-created">
						<!--<?php echo $comment->created;?>-->
						<?php 
							$date = explode(" ", $comment->created);
							$time = $date[1];
							$date = $date[0];
						?>
						<div class = "product-created-date"><?php echo $date; ?></div>
						<div class = "product-reated-time"><?php echo $time; ?></div>
					</td>
				</tr>
				<?php $i++ ?>
				<?php if($comment->replays){?>
					<?php foreach ($comment->replays as $replay) : ?>
						<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
							<td class="pg-checkbox">
								<?php echo JHtml::_('grid.id', $i, $replay->id); ?>
								<label for="cb<?php echo $i ?>"></label>
							</td>
							<td class="pg-id">
								<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=comments&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $replay->id); ?>">
									<?php echo "|—".$replay->id ?>
								</a>	
							</td>

							<td class="pg-published">
				                <?php echo PagoHelper::published( $replay, $i, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="comments" rel="' .$replay->id. '"' ); ?>
							</td>

							<td class="pg-comment">
								<?php echo $replay->author_name;?>
							</td>

							<td class="pg-comment">
								<!-- <a href="<?php echo JRoute::_('index.php?option=com_pago&controller=comments&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $replay->id); ?>">-->
									<?php echo $replay->text ?>
								<!-- </a> -->	
							</td>

							<td class="pg-links">
								<?php  $itemid = $nav -> getItemid($comment->item_id, $comment->primary_category); ?>
								<?php if (empty($replay->primary_category)) $replay->primary_category = $comment->primary_category; ?>
								<a href="<?php echo JURI::ROOT() . 'index.php?option=com_pago&view=item&id=' . $replay->item_id . '&cid=' . $replay->primary_category . '&Itemid=' . $itemid ?>"  target="_black"><?php echo $comment->item_name ?></a>
							</td>

							<td class="pg-created">
								<!--<?php echo $replay->created;?>-->
								<?php 
									$date = explode(" ", $replay->created);
									$time = $date[1];
									$date = $date[0];
								?>
								<div class = "product-created-date"><?php echo $date; ?></div>
								<div class = "product-reated-time"><?php echo $time; ?></div>
							</td>
						</tr>
						<?php $i++ ?>
					<?php endforeach; ?>
				<?php } ?>
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
		<input type="hidden" name="controller" value="comments" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();