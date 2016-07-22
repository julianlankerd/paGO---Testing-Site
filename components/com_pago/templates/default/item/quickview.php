<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<div id="pg-quickview" class="pg-item-id-<?php echo $this->item->id; ?> clearfix">
	<div id="pg-item-synopsis" class="clearfix">
		<div id="pg-item-images" class="pg-item-left">
			<?php template_functions::display_image($this->images, 'small', $this->item->name, $this->item->name); ?>
			<?php template_functions::list_images($this->images, 'thumbnail', $this->config, 'large', $this->item->name, $this->item->name); ?>
		</div>
		<div id="pg-item-details" class="pg-item-right">
			<div class="pg-item-name">
				<a href="<?php echo $this->nav->build_url( 'item', $this->item->id ) ?>" title="<?php echo $this->item->name; ?>">
					<?php echo $this->item->name; ?>
				</a>
			</div>
			<div class="pg-item-price">
				<?php echo Pago::get_instance( 'price' )->format($this->item->price); ?>
			</div>
			<form name="addtocart" id="pg-addtocart" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
				<?php echo template_functions::load_template('common', 'tmpl', 'addtocart'); ?>
			</form>
		</div>
	</div>
	<div id="pg-item-full-details">
		<?php echo $this->item->description; ?>
	</div>
	<a href="<?php echo $this->nav->build_url( 'item', $this->item->id ) ?>" class="pg-button"><?php echo JText::_('PAGO_QUICKVIEW_ITEM_DETAILS'); ?></a>
</div>
<?php die(); ?>
