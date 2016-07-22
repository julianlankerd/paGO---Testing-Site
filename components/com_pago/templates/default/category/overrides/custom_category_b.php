<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
$cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
$item_id = JFactory::getApplication()->input->get( 'Itemid' ); ?>

<?php $this->load_header(); ?>

<a name="grid"></a><a name="list"></a>

<?php if ( $categoriesLayout = PagoHelper::load_template( 'common', 'tmpl_categories' ) ) require $categoriesLayout; ?>
<?php if ( isset( $_REQUEST['cid'] ) ) : ?>
	<?php if( !empty( $this->items ) ) : ?>
	<?php $count = 1; ?>

	<div class="pg-grid-list">
		<ul>
			<li><a href="#grid" id="pg-grid-view" class="active"><?php echo JText::_('PAGO_GRID_VIEW'); ?></a></li>
			<li><a href="#list" id="pg-list-view"><?php echo JText::_('PAGO_LIST_VIEW'); ?></a></li>
		</ul>
	</div>
	<?php if ( $pagination = PagoHelper::load_template( 'common', 'tmpl_pagination' ) ) require $pagination; ?>

	<div id="pg-items" class="grid-4">
		<div class="item-grid clearfix">
		<?php foreach( $this->items as $item ) :
			$_img_obj = (object) array(
				'id'        		=> $item->file_id,
				'title'     		=> 'View Details for ' . html_entity_decode($item->name), //$item->file_title,
				'alias'     		=> $item->file_alias,
				'caption'   		=> $item->file_caption,
				'item_id'   		=> $item->id,
				'type'      		=> $item->file_type,
				'file_name' 		=> $item->file_file_name,
				'file_meta' 		=> $item->file_file_meta,
				'primary_category' 	=> $item->primary_category
			);
			$image = PagoImageHandlerHelper::get_image_from_object( $_img_obj, 'medium', false ); ?>
			<div id="cell-item-<?php echo $count; ?>" class="item-cell">
				<div class="pg-item clearfix">
					<?php if( $image ) : ?>
					<div class="pg-item-image">
						<a class="pg-quickview" href="<?php echo $this->nav->build_url('item', $item->id, true, array('layout'=>'quickview','tmpl'=>'component', 'async' => 2) );?>" title="<?php echo JText::_('PAGO_QUICKVIEW'); ?>">
							<?php echo $image; ?>
							<div><p><?php echo JText::_('PAGO_QUICKVIEW'); ?></p></div>
						</a>
					</div>
				<?php endif; ?>
					<div class="pg-item-text">
						<div class="pg-item-name">
							<a href="<?php echo $this->nav->build_url( 'item', $item->id ) ?>">
								<?php echo html_entity_decode($item->name); ?>
							</a>
						</div>
						<?php if( $item->description ) : ?>
							<div class="pg-item-short-desc"><?php echo template_functions::truncate($item->description, 100); ?></div>
						<?php endif; ?>
					</div>
					<div class="pg-item-action">
						<?php // Revist price (possible snippet?)
							$itemPriceObj = Pago::get_instance('price')->getItemDisplayPrice($item);
						?>
						<div class="pg-item-price"><?php echo Pago::get_instance('price')->format($itemPriceObj->item_price_including_tax); ?></div>
						<?php if ( $path = PagoHelper::load_template( 'common', 'tmpl_cataddtocart' ) ) require $path; ?>
					</div>
				</div>
			</div>
			<?php $count++; ?>
		<?php endforeach; ?>
		</div>
	</div>
	<?php if ( $pagination = PagoHelper::load_template( 'common', 'tmpl_pagination' ) ) require $pagination; ?>
	<?php else : ?>
	<div class="pg-alert">
		<?php echo JText::_('NO_RECORDS_FOUND') ?> <a href="#" onclick="back()"><?php echo JText::_('BACK') ?></a>
	</div>
	<?php endif; ?>
<?php endif; ?>
<?php $this->load_footer(); ?>
