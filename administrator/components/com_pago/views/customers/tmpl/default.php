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

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items,'',$this->top_menu );

?>
    <script type="text/javascript">
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
		<div class="filter-search pg-left">
			<input class = "pg-left pg-mr-20" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BANNERS_SEARCH_IN_TITLE'); ?>" />
			<button id="pg-button-search" class="pg-button-search pg-mr-20 pg-left pg-btn-medium pg-btn-light"  type="submit" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button id="pg-button-clear" class="pg-button-clear pg-left pg-btn-medium pg-btn-light" type="button" tabindex="-1"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="pg-filter-options">
			<div class = "pg-limit-box pg-right">
				<?php echo PagoHtml::pago_limitBox($this->pagination); ?>
			</div>

			<?php
				$filter_name = 'city'
			?>

	        <div class="filter-<?php echo $filter_name ?> pg-filter-<?php echo $filter_name ?> pg-right pg-mr-20">
				<select name="filter_<?php echo $filter_name ?>" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('PAGO_SEL_' . strtoupper( $filter_name ) );?></option>
					<?php echo JHtml::_('select.options', $this->cities, 'value', 'text', $this->state->get('filter.' . $filter_name));?>
				</select>
			</div>

			<?php
				$filter_name = 'state'
			?>

	        <div class="filter-<?php echo $filter_name ?> pg-filter-<?php echo $filter_name ?> pg-right pg-mr-20">
				<select name="filter_<?php echo $filter_name ?>" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('PAGO_SEL_' . strtoupper( $filter_name ) );?></option>
					<?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.' . $filter_name));?>
				</select>
			</div>

            <?php
				$filter_name = 'country'
			?>

	        <div class="filter-<?php echo $filter_name ?> pg-filter-<?php echo $filter_name ?> pg-right pg-mr-20">
				<select name="filter_<?php echo $filter_name ?>" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('PAGO_SEL_' . strtoupper( $filter_name ) );?></option>
					<?php echo JHtml::_('select.options', $this->countries, 'value', 'text', $this->state->get('filter.' . $filter_name));?>
				</select>
			</div>

			<div class="clear"></div>
		</div>

		<div class="clear"></div>

	</fieldset>

<?php
	$idSort = '';
	$customerNameSort = '';
	$userNameSort = '';
	$userEmailSort = '';

	switch($listOrder){
		case 'id':
			$idSort = 'pg-sorted-'.$listDirn;
			break;
		case 'name':
			$customerNameSort = 'pg-sorted-'.$listDirn;
			break;
		case 'username':
			$userNameSort = 'pg-sorted-'.$listDirn;
			break;
		case 'email':
			$userEmailSort = 'pg-sorted-'.$listDirn;
			break;
	}
?>	

<div class="pg-table-wrap">
	<div class = "pg-container-header">
		<?php echo JText::_( 'PAGO_CUSTOMERS_MANAGER' ); ?>
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
						<?php echo JHtml::_('grid.sort', 'PAGO_' . strtoupper( 'user_id' ), 'id', $listDirn, $listOrder); ?>
					</td>

	                <td class="kg_th <?php echo $customerNameSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_' . strtoupper( 'order_customer_name' ), 'name', $listDirn, $listOrder); ?>
					</td>

	                <td class="kg_th <?php echo $userNameSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_' . strtoupper( 'user_name' ), 'username', $listDirn, $listOrder); ?>
					</td>

	                <td class="kg_th <?php echo $userEmailSort; ?>">
						<?php echo JHtml::_('grid.sort', 'PAGO_' . strtoupper( 'user_email' ), 'email', $listDirn, $listOrder); ?>
					</td>


					<!--
	                    ID
	                    Last Name
	                    First Name
	                    Phone
	                    Email
	                    State
	                    Country
					[id] => 12246
	                [user_id] => 42
	                [last_name] => Docherty
	                [first_name] => Adam
	                [phone_1] => (234) 2355678
	                [city] => Beverly Hills
	                [state] => California
	                [country] => US
	                [zip] => 90210
	                [user_email] =>
	                [cdate] =>

	                [perms] => shopper -->
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<tr class="pg-table-content pg-row<?php echo $i % 2; ?>">
					<td class="pg-checkbox">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						<label for="cb<?php echo $i ?>"></label>
					</td>

	                 <td class="pg-id">
						<?php echo $item->user_id;?>
					</td>

	                <td class="pg-item-name">
						<a href="<?php echo JRoute::_('index.php?option=com_pago&controller=customers&task=edit&view=' . $this->get( '_name' ) . '&user_id='.(int) $item->user_id); ?>"><?php echo $item->name; ?></a>
					</td>

					<td class="pg-item-user_name">
						<?php echo $item->username;?>
					</td>

	                 <td class="pg-item-user_email">
						<?php echo $item->user_email;?>
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
        <input type="hidden" name="controller" value="customers" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php PagoHtml::pago_bottom();
