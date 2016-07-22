<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );

$Itemid = $config->get('checkout.pago_cart_itemid');
if (empty($Itemid))
{
	$Itemid = JFactory::getApplication()->input->getInt('Itemid');
}
?>

<?php
	$module_mode = $params->get('cart_size', 1);
	$cart_price = $params->get('cart_price', 0);
	$link_to_cart = $params->get('link_to_cart', 0);
?>
<?php if($link_to_cart == 1){?> <a href="<?php echo JRoute::_('index.php?option=com_pago&view=cart'); ?>" class="link-to-cart-a"><?php } ?> 
<div moduleId="<?php echo $module->id; ?>" class="pg-cart-container pg-module<?php echo $params->get( 'moduleclass_sfx' ) ?> pg-main-container">
	<?php if ($module_mode == 0) : ?>
		<div class="pg-view-cart-mode2 <?php if($link_to_cart == 1){?>link <?php } ?>">
		<?php if ($cart_price == 1) { ?><span class="pg-cart-total pg-view-cart-mode2-price"><?php echo $price_format;?></span><?php } ?>
		<span class="pg-cart-quantity"><?php echo $cart_quantity; ?></span>

	<?php elseif ($module_mode == 2): ?>

		<div class="pg-view-cart-mode2 fixed-top-left <?php if($link_to_cart == 1){?>link <?php } ?>"> 
		<?php if ($cart_price == 1) { ?><span class="pg-cart-total fixed-top-left-price"><?php echo $price_format;?></span><?php } ?>
		<span class="pg-cart-quantity"><?php echo $cart_quantity; ?></span>

	<?php elseif ($module_mode == 3): ?>
		

		<div class="pg-view-cart-mode2 fixed-top-right <?php if($link_to_cart == 1){?>link <?php } ?>"> 
		<?php if ($cart_price == 1) { ?><span class="pg-cart-total fixed-top-right-price"><?php echo $price_format;?></span><?php } ?>
		<span class="pg-cart-quantity"><?php echo $cart_quantity; ?></span>


	<?php else : ?>
		<div class="pg-view-cart  <?php if($link_to_cart == 1){?>link <?php } ?>">
	<?php endif; ?>
		<div class="pg-view-cart-inner">
			<?php if ($params->get('cart_title_show')){ ?>
				<?php 
					if ($params->get('cart_title') == ''){
						$title = $module->title;
					}
					else{
						$title = $params->get('cart_title');
					}
				?>
				<div class="pg-view-cart-header"><?php echo $title; ?></div>
			<?php } ?>

			<div class = "pg-quick-cart-mode2">
							
			</div>
			
			<?php if($module_mode == 1) { ?>
			<div class="pg-view-cart-content <?php if ($cart_price == 0) { ?>pg-view-cart-content-price <?php }?>"  >
		    	<span class="pg-cart-quantity"><?php echo $cart_quantity; ?></span>
		    	<span>&nbsp;<?php echo $cart_quantity_text; ?></span>
	    		<span class = "pg-quick-cart-total"><?php echo JTEXT::_('PAGO_QUICK_CART_ITEM_TOTAL'); ?></span>
		    	<?php if ($cart_price == 1) { ?>/
		    	<span class="pg-cart-total"><?php echo $price_format; ?></span><?php } ?>
			</div>
			<?php } ?>
		</div>
    </div>
</div>
<?php if($link_to_cart == 1){?></a><?php } ?>