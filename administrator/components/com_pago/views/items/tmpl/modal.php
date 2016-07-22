<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
error_reporting(0);

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

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
Pago::load_helpers('categories');
$catnip = new catnip('pago_categories', 'com_pago', 'category', 'cid', false);
require_once(JPATH_SITE.'/components/com_pago/helpers/navigation.php');
$nav = new NavigationHelper();


$input     = JFactory::getApplication()->input;
$function  = $input->getCmd('function', 'jSelectChart_jform_request_id');

$doc = JFactory::getDocument();
$style = '.pg-sidebar {'
        . 'display:none;'
        . '}'
        . '.pg-header {'
        . 'display:none;'
        . '}'
        . '#pago .pg-main {'
        . 'margin-left:0;'
        . '}';  
$doc->addStyleDeclaration( $style );
$doc->addStyleSheet( JURI::root() . 'administrator' .DS. 'components' .DS. 'com_pago' .DS. 'css' .DS. 'menu-picker.css' );


?>
<script type="text/javascript">
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

<div id="pago">
	<form action="<?php echo JRoute::_('index.php?option=com_pago&tmpl=component&view=items&layout=modal' ); ?>" method="post" name="adminForm" id="adminForm" class="menuItemModal">
		<fieldset id="filter-bar">
			
			<div class="filter-search fltlft">
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('PAGO_SEARCH_IN_TITLE'); ?>" />
				<button class="btn btn-primary" type="submit" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button class="btn btn-warning" type="button"  onclick="document.id('filter_search').value='';document.getElementById('filter_primary_category').value='';document.getElementById('filter_type').value='';document.getElementById('filter_published').value='';this.form.submit();"><?php echo JText::_('PAGO_FILTER_RESET'); ?></button>
			</div>

			<div class="pg-filter-options">

				<div class="filter-published fltrt pg-filter-status">
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

				<div class="filter-published fltrt pg-filter-product-type">
					<select name="filter_type" class="inputbox" onchange="this.form.submit()" id="filter_type">
						<option value=""><?php echo JText::_('PAGO_SEL_PRODUCT_TYPE');?></option>
						<?php
						$options = array(
						array(
							'value' => 'tangible',
							'text' => JText::_( 'PAGO_SEL_TANGIBLE' ),
							'disable' => 0
						),
						array(
							'value' => 'intangible',
							'text' => JText::_( 'PAGO_SEL_INTANGIBLE' ),
							'disable' => 0
						)
						);
						echo JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.type'));?>
					</select>
				</div>

				 <div class="filter-primary_category fltrt pg-filter-primary-category">
					<select name="filter_primary_category" class="inputbox" onchange="this.form.submit()" id="filter_primary_category">
						<option value=""><?php echo JText::_('PAGO_SEL_PRIMARY_CATEGORY');?></option>
						<?php echo JHtml::_('select.options', $this->categories, 'value', 'text', $this->state->get('filter.primary_category'));?>
					</select>
				</div>
				
			</div>

			<div class="clear"></div>

		</fieldset>

	<h1 class="pg-background-color">
		<?php echo JText::_( 'PAGO_ITEMS_MANAGER' ); ?>
	</h1>

	<div class="pg-table-wrap">
		<table id="pg-items-manager" class="table table-striped">
			<thead>
				<tr>
					<td>
						<?php
							echo JHtml::_('grid.sort', 'PAGO_SKU', 'sku', $listDirn, $listOrder);
							if ( $listOrder == 'sku' ) {
								echo $listDirn;
							}
						?>
					</td>
					<td>
						<?php
							echo JHtml::_('grid.sort', 'PAGO_ITEMS_ITEM_NAME', 'name', $listDirn, $listOrder);
							if ( $listOrder == 'name' ) {
								echo $listDirn;
							}
						?>
					</td>
					<td>
						<?php
							echo JHtml::_('grid.sort', 'PAGO_ITEMS_CATEGORY', 'primary_category', $listDirn, $listOrder);
							if ( $listOrder == 'primary_category' ) {
								echo $listDirn;
							}
						?>
					</td>
					<td>
						<?php
							echo JHtml::_('grid.sort', 'PAGO_ITEMS_TYPE', 'type', $listDirn, $listOrder);
							if ( $listOrder == 'type' ) {
								echo $listDirn;
							}
						?>
					</td>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
			if($item->published)
			{
			 ?>
				<tr>
		
					<td>
						<?php echo $item->sku;?>
					</td>
					<td>
						<a href="javascript:void(0);" class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');">
							<?php echo $item->name; ?>
						</a>
					</td>
					<td>
						<?php $result = $catnip -> get_parent_category_tree($item->primary_category); ?>
					</td>
					<td>
						<?php echo $item->type_name ?>
					</td>
				</tr>
				<?php
				}
				 endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="pg-pagination-wrap">
		<?php echo PagoHtml::pago_pagination($this->pagination); ?>
		<div class="limitbox">
			<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
		</div>
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
</div>