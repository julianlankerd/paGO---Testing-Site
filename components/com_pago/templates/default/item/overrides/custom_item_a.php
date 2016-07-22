<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
$_root = JURI::root( true );

// Displays the full item detail view.
$this->load_header(); 


PagoHtml::add_css( $_root . '/components/com_pago/templates/default/css/tango/skin.css' );
PagoHtml::add_js( $_root . '/components/com_pago/templates/default/js/jquery.jcarousel.js' );
?>
<script>
jQuery(document).ready(function(){
	considerPrice();
});
</script>
<div id="pg-item" class="pg-item-id-<?php echo $this->item->id; ?> clearfix">
	<div id="pg-item-synopsis" class="clearfix">
		<?php if (!empty( $this->images))
		{
		?>
			<div id="pg-item-images" class="pg-item-left">
				<?php PagoImageHelper::display_image($this->images, 'large', $this->item->name, $this->item->name); ?>
				<?php PagoImageHelper::list_images($this->images, 'thumbnail', $this->config, 'large', $this->item->name, $this->item->name); ?>
			</div>
		<?php
		}
		?>
		<div id="pg-item-details" style="padding-left:412px;" class="pg-item-right">
			<div class="pg-item-name">
				<h1><?php echo html_entity_decode($this->item->name); ?></h1><span class="pg-item-sku"><span><?php echo JText::_( 'PAGO_ITEM_SKU' ); ?></span>: <?php echo $this->item->sku; ?></span>
			</div>
			<?php if( !isset($this->item->qty) || $this->item->qty <= 0) : ?>
			<p class="pg-out-of-stock"><?php echo JText::_('PAGO_OUT_OF_STOCK'); ?></p>
			<?php endif; ?>
			<?php // Revist price (possible snippet?)
				$itemPriceObj = Pago::get_instance( 'price' )->getItemDisplayPrice($this->item);
			?>
			<div class="pg-item-price" id="pg-item-original-price" originalprice="<?php echo $itemPriceObj->item_price_excluding_tax; ?>" ><?php echo Pago::get_instance( 'price' )->format($itemPriceObj->item_price_excluding_tax); ?></div>
			
			<form name="addtocart" id="pg-addtocart" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
				<?php if ( $addtocart = PagoHelper::load_template( 'common', 'tmpl_addtocart' ) ) require $addtocart; ?>
				
				<div class="pg-item-desc">
					<?php echo html_entity_decode($this->item->description); ?>
				</div>

				<div class="pg-item-attributes">
					<?php echo template_functions::display_attribute( $this->item ); ?>
				</div>
			</form>
		</div>
	</div>
	<?php if( !empty( $this->item->content ) ) : ?>
	<div id="pg-item-full-details">
		<h2><?php echo JText::_( 'PAGO_ITEM_FULL_DETAILS' ); ?></h2>
		<?php echo html_entity_decode($this->item->content); ?>
	</div>
	<?php endif; ?>
</div>
<?php $this->load_footer() ?>
