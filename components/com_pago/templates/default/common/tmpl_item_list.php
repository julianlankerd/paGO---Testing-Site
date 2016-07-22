<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 

$store_default_image = $this->config->get( 'store_default_image', array() );

template_functions::equal_heights();
$this->document->addScriptDeclaration( "
	jQuery(window).load(function() {
		jQuery('.equalize').equalize();
		jQuery('.equalize img').vAlign();
	});
	jQuery(window).resize(function() {
		jQuery('.equalize img').vAlign();
	});
" );
?>

<div id="wrap-list-item" class="outer clearfix">
	<div id="list-item" class="inner clearfix">
		<?php
		$image_sizes = json_decode( $this->config->get( 'image_sizes' ), true );

		$column = 1;
		$count  = 1;
		$position = '';
		$item_image_size = $this->config->get( 'image_size_category', 'medium' );
		$item_image_style = "style=\"width: {$image_sizes[$item_image_size]['width']}px; height: {$image_sizes[$item_image_size]['height']}px;\"";
		$item_equalize_style = "style=\"height: {$image_sizes[$item_image_size]['height']}px;\"";
		foreach ( $this->items as $item ) {
			$item_position = '';
			$no_image = empty( $item->file_file_meta ) && empty( $store_default_image );

			if ( $column == 1 ) {
				echo '<ul class="item-row row-' . $count . ' items-'
					. $this->tmpl_params->get( 'prod_per_row', 4 ) .'">';
				$item_position = ' first';
			}
			if ( ( $column == $this->tmpl_params->get( 'prod_per_row', 4 ) ) || ( end( $item ) ) ) {
				$item_position .= ' last';
			}

		?>
		<li class="outer wrap-pg-item item-id-<?php echo $item->id; echo $item_position; ?>">
			<div class="inner pg-item">
				<div class="outer wrap-pg-thumb <?php if ( $no_image ) { echo 'no-image'; } ?>">
					<a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=item&id=' . $item->id ); ?>" class="image-link">
						<div class="inner pg-thumb equalize">
							<?php if ( $no_image ) { ?>
								<span>No Image Available</span>
							<?php } else {
								if ( !empty( $item->file_file_meta ) ) {
									// Get object ready!
									$_img_obj = (object) array(
										'id'               => $item->file_id,
										'title'            => $item->file_title,
										'alias'            => $item->file_alias,
										'caption'          => $item->file_caption,
										'item_id'          => $item->id,
										'primary_category' => $item->primary_category,
										'type'             => $item->file_type,
										'file_name'        => $item->file_file_name,
										'file_meta'        => $item->file_file_meta
										);
								} else { // Use store default
									$_img_obj = $store_default_image;
								}
								echo PagoImageHandlerHelper::get_image_from_object( $_img_obj,
									 $item_image_size );
							} ?>
						</div>
					</a>
				</div>

	        	<h3 class="pg-name"><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=item&id=' . $item->id ); ?>"><?php echo substr( $item->name, 0, 16 ) ?></a></h3>

	        	<p class="pg-type"><?php echo $item->type; ?></p>
	        	<p class="pg-price"><?php $item->currency?><?php echo number_format( $item->price,
		 			2, '.', '' ) ?></p>
	        	<p class="pg-buy-now"><a href="<?php echo JRoute::_(
		 			'index.php?option=com_pago&view=cart&task=add&return=true&id=' . $item->id );
		 			?>">Add to Cart</a></p>
			</div>
		</li>

		<?php
			if ( $column == $this->tmpl_params->get( 'prod_per_row', 4 ) ) { // If the number of items per row has been reached
				echo "</ul>\n";
				$column = 1;
				$count++;
			}
			else {
				$column++;
			}
		} // end foreach

		if ( $column != 1 ) {
			echo "</ul>\n";
		}
		?>
	</div><!-- end #item_list //-->
</div><!-- end #wrap-item_list //-->