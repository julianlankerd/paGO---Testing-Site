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

	if($this->item) $item = $this->item;
?>
<div id="pg-quick-addtocart">
	<a class="pg-item-name" href="<?php echo JRoute::_('index.php?option=com_pago&view=item&id=' . $item->id); ?>"><?php echo $item->name; ?></a>
	<?php if($item->price) { ?>
		<?php if(isset($item->stock)) :
				if($item->stock < 1) : ?>
			<div class="pg-out-of-stock"><?php echo JText::_('PAGO_OUT_OF_STOCK'); ?></div>
		<?php endif; endif; ?>
		<div class="pg-item-price"><?php echo $item->price; ?></div>
		<form name="addtocart" id="pg-addtocart" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
			<label for="quantity"><?php echo JText::_('PAGO_QUANTITY'); ?></label>
			<input type="text" size="1" name="quantity" id="pg-quantity" class="pg-text-box" value="" />
			<input type="hidden" name="id" value="<?php echo $item->id; ?>" />
			<input type="hidden" name="option" value="com_pago" />
			<input type="hidden" name="view" value="cart" />
			<input type="hidden" name="task" value="add" />
			<?php
				if( $view == 'item' )
					$return = base64_encode('index.php?option=com_pago&view=item&id='. $item->id . '&Itemid=' . $Itemid );
				else
					$return = base64_encode('index.php?option=com_pago&view=category&cid='. $item->primary_category . '&Itemid=' . $Itemid );
			?>
			<input type="hidden" name="return" value="<?php echo $return ?>" />
			<?php echo JHtml::_('form.token'); ?>
			<button type="submit" class="pg-button pg-addtocart"><?php echo JText::_( 'PAGO_ADD_TO_CART' ); ?></button>
		</form>
	<?php } else { ?>
	<p class="purchasing-soon"><?php echo JText::_('PAGO_PURCHASING_AVAILABLE_SOON'); ?></p>
<?php } ?>
	<br class="clear" />
</div>
<?php die(); ?>
