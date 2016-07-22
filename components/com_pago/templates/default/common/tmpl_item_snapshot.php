<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$store_default_image = $config->get( 'store_default_image', array() );
?>
<div class="<?php echo $params->get( 'moduleclass_sfx' ); ?>">
	<ul>
	<?php
	foreach ( $items as $item ) {
		if ( $params->get( 'show_image', 1 ) ) {
			$no_image        = empty( $item->file_file_meta );
			$item_image_size = $config->get( 'image_size_category', 'medium' );
		?>
			<li>
				<?php if ( $no_image ) {} else {
					// Get object ready!
					if ( !empty( $item->file_file_meta ) ) {
						$_img_obj = (object) array(
							'id'        => $item->file_id,
							'title'     => $item->file_title,
							'alias'     => $item->file_alias,
							'caption'   => $item->file_caption,
							'product'   => $item->id,
							'type'      => $item->file_type,
							'file_name' => $item->file_file_name,
							'file_meta' => $item->file_file_meta
							);
					} else { // Use store default
						$_img_obj = $store_default_image;
					}
					echo PagoImageHandlerHelper::get_image_from_object( $_img_obj,
						 $item_image_size );
				}
			}

			if( $params->get( 'show_name', 1 ) ) {
			?>
				<li>
					<a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=item&id='
						. $item->id ); ?>">
						<?php 
							if ( $params->get( 'truncate', 1 ) ) {
								echo substr( 
									$item->name, 
									0, 
									$params->get( 'truncate_amount', 16 ) ); 
							} else {
								echo $item->name;
							}
							?>
				</li>
			<?php
			}

			if( $params->get( 'show_price', 1 ) ) {
			?>
				<li>
					<?php echo $item->price; ?>
				</li>
			<?php
			}

			if( $params->get( 'show_add_to_cart', 1 ) ) {
			?>
				<li>
					<a class="pg-button" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=cart&task=add&return=true&id=' . $item->id ); ?>">Add to Cart</a>
				</li>
			<?php
			}
		} ?>
	</ul>
</div>