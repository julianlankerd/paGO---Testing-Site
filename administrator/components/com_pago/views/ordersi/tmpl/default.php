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
$postArray = JFactory::getApplication()->input->getArray($_POST);

if(!isset($postArray['filter_order_status']))
{
	$postArray['filter_order_status'] = '';
}
JHtml::_('behavior.keepalive');

PagoHtml::apply_layout_fixes();
PagoHtml::uniform();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();

include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items,'',$this->top_menu );
?>
<div class="modal fade" aria-hidden="true" role="dialog" id="ord-date-modal">

	<form name="modalcal" action="" method="post" id="modalcal">
		<?php JHTML::_('behavior.calendar'); ?>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">
	<span><?php echo JText::_('PAGO_START_DATE'); ?></span> <span><?php echo JHTML::calendar(date("Y-m-d", strtotime("-1 week")),'startdate', 'startdt', '%Y-%m-%d',array('size'=>'8','maxlength'=>'10')); ?></span><br/><?php echo JText::_('PAGO_END_DATE'); ?> <?php echo JHTML::calendar(date("Y-m-d"),'enddate', 'enddt', '%Y-%m-%d',array('size'=>'8','maxlength'=>'10')); ?><br />
	
					<?php 
						$key = 'value';
						$val = 'prdname';		
					
						$db = JFactory::getDBO();
						$sql = "SELECT name AS prdname, id AS value FROM #__pago_items WHERE 1=1 and published=1 ORDER BY name ASC";		
						$db->setQuery($sql);
						$options = $db->loadObjectList();
						echo '<div>Select Products</div>';
						echo $html = @JHTML::_('select.genericlist', $options, 'products[]', 'class="inputbox" multiple="true"', $key, $val, 0);
						
					
					?>
					</h4><br /><br />
					   <button type="button" class="export pg-btn-medium" id="orderDateSet">Export</button>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="ordersi" />
		<input type="hidden" name="view" value="ordersi" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
<?php 
	include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'javascript' .DS. 'orderjs.php');
?>
<script language="javascript" type="text/javascript">



Joomla.submitbutton = function(pressbutton)
{
	submitbutton(pressbutton);
}
submitbutton = function(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton)
    {
    	form.task.value=pressbutton;
    }

    if(pressbutton == 'remove' ||  pressbutton == 'edit' ||  pressbutton == 'print_orders'){
				if (form.boxchecked.value == 0)
				{
					alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_ORDER');?>');
					return false;
				}
				else
				{
					form.task.value = pressbutton;
					form.submit();
				}
	}
	
	if(pressbutton == 'export_csv')
    {
   		var formcal = document.modalcal;
		
		jQuery('#ord-date-modal').modal();
		  jQuery('#orderDateSet').on('click', function(e){
				formcal.task.value = pressbutton;
				formcal.submit();
		  });
		  return true;
	}
	
	

	if(pressbutton=='new')
	{
		form.controller.value="addorder";
		form.view.value="addorder";
	}
	try
	{
		form.onsubmit();
	}
	catch(e){}

	form.submit();
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get( '_name' ) ); ?>" method="post" name="adminForm" id="adminForm">
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

			<div class="filter-select pg-right pg-mr-20">
				<select name="filter_order_status" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('PAGO_ORDERS_PAYMENT_STATUS');?></option>
					<?php echo JHtml::_('select.options', $this->order_status_options, 'value', 'text',  $postArray['filter_order_status']);?>
				</select>
			</div>
		</div>
	</fieldset>

	<div class = "pg-container-header">
		<?php echo JText::_( 'PAGO_ORDERS_MANAGER' ); ?>
	</div>

	<?php
		$idSort = '';
		$dateSort = '';
		$nameSort = '';
		$gatewaySort = '';
		$statusSort = '';
		$totalSort = '';

		switch($listOrder){
			case 'order_id':
				$idSort = 'pg-sorted-'.$listDirn;
				break;
			case 'cdate':
				$dateSort =  'pg-sorted-'.$listDirn;
				break;
			case 'first_name':
				$nameSort = 'pg-sorted-'.$listDirn;
				break;
			case 'payment_gateway':
				$gatewaySort = 'pg-sorted-'.$listDirn;
				break;
			case 'order_status':
				$statusSort = 'pg-sorted-'.$listDirn;
				break;
			case 'order_total':
				$totalSort = 'pg-sorted-'.$listDirn;
				break;
		}
	?>

	<div class="pg-white-bckg pg-border pg-pad-20">
		<table id="pg-orders-manager" class="pg-table pg-repeated-rows pg-orders-manager">
			<thead>
				<tr class="pg-sub-heading pg-multiple-headings pg-sortable-table">
					<td class="pg-checkbox">
						<input type="checkbox" id="checkall" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						<label for="checkall"></label>
					</td>

					<td class="pg-id <?php echo $idSort; ?>">
						<?php echo JHtml::_('grid.sort',  'PAGO_ID', 'order_id', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-created <?php echo $dateSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_START_DATE', 'cdate', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-user-name  <?php echo $nameSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_ORDER_CUSTOMER_NAME', 'first_name', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-payment-gateway <?php echo $gatewaySort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_ORDER_PAYMENT_GATEWAY', 'payment_gateway', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-status <?php echo $statusSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_ORDER_STATUS', 'order_status', $listDirn, $listOrder); ?>
					</td>

					<td class="pg-total <?php echo $totalSort; ?>">
						<?php echo JHtml::_('grid.sort',  'PAGO_ORDER_TOTAL', 'order_total', $listDirn, $listOrder); ?>
					</td>
				</tr>
			</thead>

			<tbody>
			<?php foreach ($this->items as $i => $item) :
					$order 	= Pago::get_instance('orders')->get($item->order_id);
					$payment = array();
					if(count($order['payment'])>0 )
					{
						$payment = $order['payment'][0];
					}
			?>
				<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
					<td class="pg-checkbox">
						<?php echo JHtml::_('grid.id', $i, $item->order_id); ?>
						<label for="cb<?php echo $i ?>"></label>
					</td>
					<td class="pg-id">
						<?php 
							if(count($payment)>0)						{
								if($payment->isfraud){
									echo "<img src='".JURI::base()."/components/com_pago/css/images/fraud.png'>";
								}
							}
						?>
						<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=ordersi&task=edit&view=' . $this->get( '_name' ) . '&cid[]='.(int) $item->order_id); ?>"><?php echo $this->escape($item->order_id_text); ?></a>
						<!-- <p class="smallsub"><?php echo JText::_( 'PAGO_ORDER_MODIFIED_DATE' ) . $item->mdate; ?></p> -->
					</td>

	                <td class="pg-created">
						<!--<?php echo $item->cdate ?>-->
						<?php 
							$date = explode(" ", $item->cdate);
							$time = $date[1];
							$date = $date[0];
						?>
						<div class = "product-created-date"><?php echo $date; ?></div>
						<div class = "product-reated-time"><?php echo $time; ?></div>
					</td>

					<td class="pg-user-name">
							<a href="<?php echo JRoute::_('index.php?option=com_pago&view=customers&layout=form&user_id=' . $item->user_id); ?>"><?php echo $item->name ?></a>
					</td>

					<td class="pg-payment-gateway">
						<?php echo $item->payment_gateway ?>
					</td>

					<td class="pg-status">
						<span title="<?php echo $item->order_status; ?>" class="pg-icon pg-<?php echo strtolower(preg_replace("/[^A-Za-z0-9]/", "-", $item->order_status)); ?>"><?php echo $item->order_status; ?></span>
					</td>

					<td class="pg-total">
						<?php echo Pago::get_instance( 'price' )->format($item->order_total,$item->order_currency);?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

	</div>

	<div class="pg-pagination">
		<?php echo PagoHtml::pago_pagination($this->pagination); ?>
	</div>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="ordersi" />
		<input type="hidden" name="view" value="ordersi" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();
