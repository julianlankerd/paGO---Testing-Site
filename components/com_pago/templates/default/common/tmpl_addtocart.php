<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); // no direct access ?>
<?php 
	$view = JFactory::getApplication()->input->get('view');
	$Itemid = JFactory::getApplication()->input->getInt('Itemid');

	if( isset( $this->item ) ) $item = $this->item;
?>
<div class="pg-item-addtocart">
<?php //check for child products (not implemented yet).
if(1 != 2) :
	if ($item->qty <= 0)
	{
		if ($item->availibility_options == 0)
		{
	 		echo "<div>" . JTEXT::_('PAGO_OUT_OF_STOCK') . "</div>";
		}
		elseif ($item->availibility_options == 1)
		{
	 		echo "<div>" . JTEXT::_('PAGO_NOT_AVAILABLE') . "</div>";
		}
		elseif ($item->availibility_options == 2)
		{
			Jhtml::_('behavior.modal');
			$cid = JFactory::getApplication()->input->get('cid');
			echo '<a href="'.JURI::root() . 'index.php?option=com_pago&view=contact_info&tmpl=component&cid=' . $cid . '&id=' . $item->id . '" class="modal" rel="{handler: \'iframe\', size: {x: 550, y: 400}}">' . JTEXT::_('COM_PAGO_CONTACT_FOR_MORE_INFO') . '</a>';
		}
		elseif ($item->availibility_options == 3 && strtotime($item->availibility_date) > strtotime(date("Y-m-d")))
		{
			echo "<div>" . JTEXT::_('PAGO_AVAILIBLITY_DATE') . " : " . $item->availibility_date . "</div>";
		}
		elseif($item->availibility_options == 4 && strtotime($item->availibility_date) != 0)
		{
			echo "<div class='pg-out-of-stock'>" . JTEXT::_('PAGO_OUT_OF_STOCK') . "</div>";
		}
		else
		{
			echo "<div>" . JTEXT::_('PAGO_NOT_AVAILABLE') . "</div>";
		}
	}
	else
	{
	?>
		<!-- <form name="addtocart" id="pg-addtocart" method="post" action="<?php //echo JRoute::_( 'index.php' ) ?>"> -->
		<?php if( $view == 'item' ) :
			$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
			$return = base64_encode( $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ); ?>
			<?php if( (isset( $item->qty ) && $item->qty > 0 ) ||  $item->availibility_options == 0 ) : ?>
			<div class=pg-qty-con>
			<label for="pg-item-opt-qty" class="pg-label"><?php echo JText::_('PAGO_ITEM_QTY'); ?></label>
			<input onkeyup='considerPrice();' type="text" size="1" class="pg-inputbox" name="qty" value='1' id='pg-item-opt-qty' />
				<span class='pg-qty-control' attr=''>
				 	<a href='javascript:void(0);' class='pg-qty-up' onclick="qtyChangeItem(this,<?php echo $item->id ?>);"></a>
				 	<a href='javascript:void(0);' class='pg-qty-down' onclick='qtyChangeItem(this,<?php echo $item->id ?>);'></a>
				 </span>
			</div>
			<?php endif; ?>
		<?php else :
			$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
			$return = base64_encode( $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ); ?>
		<?php endif; ?>
			<input type="hidden" name="id" value="<?php echo $item->id; ?>" />
			<input type="hidden" name="option" value="com_pago" />
			<input type="hidden" name="view" value="cart" />
			<input type="hidden" name="task" value="add" />
			<input type="hidden" name="return" value="<?php echo $return ?>" />
			<?php echo JHtml::_('form.token'); ?>
			<?php if( (isset( $item->qty ) && $item->qty > 0 ) ||  $item->availibility_options == 0 ) : ?>
			<button type="submit" class="pg-button pg-addtocart"><?php echo JText::_( 'PAGO_ADD_TO_CART' ); ?></button>
			<?php else: ?>
			<button type="submit" class="pg-button pg-addtocart pg-disabled" disabled="disabled"><?php echo JText::_( 'PAGO_ADD_TO_CART' ); ?></button>
			<?php endif; ?>
			
		<!-- </form> -->
		<?php
	}
		?>
<?php else : ?>
	<a class="pg-button" title="<?php echo JText::_( 'PAGO_VIEW_DETAILS' ); ?>" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=item&id=' . $item->id ); ?>">
		<?php echo JText::_( 'PAGO_VIEW_DETAILS' ); ?>
	</a>
<?php endif; ?>
</div>