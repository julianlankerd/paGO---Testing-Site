<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access'); ?>
<div class="pg-content">
<form action="index.php" method="post" name="adminForm">
<div id="editcell">
<?php
$content = '<div class="pg-order-search left">';
$content .= '<input type="text" name="search" id="search" value="' .
	htmlspecialchars($this->lists['search']) .
	'" class="text_area" onchange="document.adminForm.submit();" />';
$content .= '<label for="search">' . JText::_( 'Search for Name' ) .
	'</label>';
$content .= '</div>';
echo PagoHtml::module_top( $content, 'search' );

/*
				<?php echo JText::_( 'Filter' ); ?>:
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
 */
?>
	<div class="pg-module-content">
    <table class="pg-table pg-order-table">
    <thead>
        <tr>
            <th width="20">
                <input type="checkbox" id="checkall" name="toggle" value="" onclick="checkAll(<?php echo count( $this->attributes ); ?>);" />
                <label for="checkall"></label>
            </th>
            <th width="40%">

                <?php echo JHTML::_( 'grid.sort', JText::_( 'Name' ), 'name', $this->lists['order_Dir'], $this->lists['order']); ?>

            </th>
             <th>
                <?php echo JHTML::_( 'grid.sort', JText::_( 'Type' ), 'type', $this->lists['order_Dir'], $this->lists['order']); ?>
            </th>
             <th>
				<?php echo JHTML::_('grid.sort',   'Order by', 'ordering', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				<?php if ($this->ordering) echo JHTML::_('grid.order',  $this->attributes ); ?>
            </th>
            <th>
            <?php echo JHTML::_( 'grid.sort', JText::_( 'Creation Date' ), 'created', $this->lists['order_Dir'], $this->lists['order']); ?>

            </th>
            <th>
             <?php echo JHTML::_( 'grid.sort', JText::_( 'Last Modify Date' ), 'modified', $this->lists['order_Dir'], $this->lists['order']); ?>

            </th>
        </tr>
    </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->attributes ); $i < $n; $i++)
    {
        $row = $this->attributes[$i];
		$checked    = JHTML::_( 'grid.id', $i, $row->id );

        ?>
        <tr class="<?php echo "row$k"; echo ( $i&1 ) ? ' odd' : ' even'; ?>">
        	<td>
				<?php echo $checked; ?>
            </td>

            <td>
            	<?php
				$link = JRoute::_( 'index.php?option=com_pago&view=attributes&task=edit&cid[]='. $row->id );
				?>
                <a href="<?php echo $link ?>"><?php echo $row->name; ?></a>
            </td>
            <td>
                <?php echo $row->type; ?>
            </td>
			<td class="order" nowrap="nowrap">
				<span><?php echo $this->pagination->orderUpIcon( $i, ( $i != 0  ), 'orderup', 'Move Up', $this->ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n, ( $i != $n-1 ), 'orderdown', 'Move Down', $this->ordering ); ?></span>
				<?php $disabled = $this->ordering ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
			</td>
            <td>
                <?php echo $row->created; ?>
            </td>
            <td>
                <?php echo $row->modified; ?>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>

    <tfoot>
			<tr>
				<td colspan="15">
					 <? echo $this->pagination->getListFooter() ?></td>
			</tr>
			</tfoot>

    </table>
	</div> <!-- end pg-module-content -->
	<?php echo PagoHtml::module_bottom() ?>

</div>

<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

<input type="hidden" name="option" value="com_pago" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="attributes" />

</form>
</div>

