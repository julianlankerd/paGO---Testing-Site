<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php
$modulID = JFactory::getApplication()->input->getInt( 'modulID' );
$module = PagoHelper::getModuleById($modulID);

if($module->params){
	$cart_size = json_decode($module->params)->cart_size;
	$cart_price = json_decode($module->params)->cart_price;
}else{
	$cart_size=1;
	$cart_price=0;
}


?>
	<div class="pg-quick-cart-contents">
	<?php if($this->cart['total_qty'] > 0): ?>
		<table id="pg-quick-cart-table" width="100%" cellspacing="0" cellpadding="4" border="0">
			<thead>
				<tr>
					<?php if($cart_size==1){
						?>
						<th class="pg-cart-item" ><?php echo JText::_('PAGO_QUICK_CART_ITEM_SHORT_DESC'); ?></th>
						<?php
					} else{
						?>
						<th class="pg-cart-item" colspan="2"><?php echo JText::_('PAGO_QUICK_CART_ITEM_DESC'); ?></th>
						<?php
					}?>
					<th class="pg-cart-item-quantity"><?php echo JText::_('PAGO_QUICK_CART_ITEM_QTY'); ?></th>
					<!-- <th class="pg-cart-item-price"><?php echo JText::_('PAGO_QUICK_CART_ITEM_PRICE'); ?></th> -->
					<th class="pg-cart-item-total"><?php echo JText::_('PAGO_QUICK_CART_ITEM_TOTAL'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
					JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
					$attributeModel = JModelLegacy::getInstance('Attribute','PagoModel');
				?>
				<?php foreach($this->cart['items'] as $item): 
				$Itemid = $this->nav->getItemid($item->id, $item->cid);
							$link = JRoute::_('index.php?option=com_pago&view=item&id=' . $item->id . '&cid=' . $item->cid .'&Itemid=' . $Itemid);
							?>
				<tr class="pg-cart-item">
					<td class="pg-cart-item-image">
						<?php if( $item->images ) : ?>
							<?php
								$config = Pago::get_instance('config')->get();
								$mini_cart_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($config->get('cart.mini_cart_image_size',1));

							 ?>
							<a href="<?php echo $link; ?>">
								<?php
								if(isset($item->varationId) && $item->varationId){

									$noImage = false;

									$image = $attributeModel->getPhotoByType($item->varationId,'product_variation',$mini_cart_image_size_title);
									
												$defVar = $attributeModel->checkDefaultVariation($item->varationId);
												
												if($defVar){
													$images = PagoImageHandlerHelper::get_item_files( $item->id, true, array( 'images' ) );
													if($images){
														$image = PagoImageHandlerHelper::get_image_from_object( $images[0], $mini_cart_image_size_title, true );
													


													}else{
														$image = false;	
													}
												}
												

									if(!$image){
										$image = $attributeModel->getSameVarationImagePath($item->varationId,$mini_cart_image_size_title);	
										
										if(!$image){
											$image = JURI::root() . 'components/com_pago/images/noimage.jpg';	
											$noImage = true;
										}	
									}

									if($noImage)
									{
										echo '<img src='.JURI::root() . 'components/com_pago/images/noimage.jpg>';
									}else{
										echo '<img src='.$image .'>';
									}

								}else{
									PagoImageHelper::display_image($item->images, $mini_cart_image_size_title, $item->name, $item->name);
								}
								?>	
							</a>
						<?php endif; ?>
						<?php if($cart_size==1){
							?>
							<p><a href="<?php echo $link; ?>"><?php echo $item->name; ?></a></p>
							<?php
					} else{?>
					</td>

					<td class="pg-cart-item-name">
						<a href="<?php echo $link; ?>"><?php echo $item->name; ?></a>
					<?php } ?>
					</td>
					<td class="pg-cart-item-qty"><?php echo $item->cart_qty; ?></td>
					<!-- <td class="pg-cart-item-price"><?php echo Pago::get_instance( 'price' )->format($item->price); ?></td> -->
					<td class="pg-cart-item-total">
					<?php
					if(isset($item->subtotal)){
							$price = Pago::get_instance( 'price' )->format($item->subtotal);	

							echo Pago::get_instance( 'price' )->removeNulls($price);	
					}else{
							$price = Pago::get_instance( 'price' )->format(0);
							echo Pago::get_instance( 'price' )->removeNulls($price);	
					}
					?>
					</td>
				</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
	<?php 
	($this->cart["total_qty"] == 1) ? $cart_quantity_text = JText::_('PAGO_MINI_CART_ITEM') :
	$cart_quantity_text = JText::_('PAGO_MINI_CART_ITEMS');
	?>
	<?php if($cart_size != 1) { ?>
	<div class="pg-view-cart-content  "  >
		<span class="pg-cart-quantity"><?php echo $this->cart["total_qty"]; ?></span>
		<span>&nbsp;<?php echo $cart_quantity_text; ?></span>
		<span class = "pg-quick-cart-total"><?php echo JTEXT::_('PAGO_QUICK_CART_ITEM_TOTAL'); ?></span>
		<?php 
			$price = Pago::get_instance( 'price' )->format($this->cart['subtotal']);

			echo Pago::get_instance( 'price' )->removeNulls($price);?>
			
	</div> 
	<?php } ?>
 

	<div class="pg-quick-cart-footer">
		<!-- <div class="pg-cart-subtotal">
			<span><?php echo JText::_('PAGO_QUICK_CART_SUBTOTAL'); ?>:</span>
			<?php echo Pago::get_instance( 'price' )->format($this->cart['format']['subtotal']); ?>
		</div> -->
		<div class = "view-cart">
			<a href="<?php echo JRoute::_('index.php?option=com_pago&view=cart'); ?>" class = "pg-gray-background-btn"><?php echo JText::_('PAGO_QUICK_CART_VIEW_CART'); ?></a>
		</div>
		<div class = "checkout">
			<a href="<?php echo JRoute::_('index.php?option=com_pago&view=checkout'); ?>" class = "pg-green-text-btn"><?php echo JText::_('PAGO_QUICK_CART_CHECKOUT'); ?></a>
		</div>
		<?php
		if ( !empty( $this->express_payment_options ) ) {
			foreach( $this->express_payment_options as $pay_option => $pay_values ) {
?>
				<a href="<?php
				$user_tmp = JFactory::getUser();
				if ( $user_tmp->guest ) {
					echo JRoute::_('index.php?option=com_pago&view=checkout&task=express_checkout'.
						'&payment_option=' . $pay_option . '&guest=1');
				} else {
					echo JRoute::_('index.php?option=com_pago&view=checkout&task=express_checkout'.
						'&payment_option=' . $pay_option );
				}
				?>"><?php echo $pay_values['image'] ?></a>
	<?php
			}
		}
?>
	</div>

	<?php else: ?>
		<div class="pg-cart-empty"><?php echo JText::_('PAGO_QUICK_CART_EMPTY'); ?></div>
	</div>
	<div class="pg-quick-cart-footer">
		<a class="pg-gray-background-btn" href="<?php echo JRoute::_('index.php?option=com_pago&view=cart'); ?>"><?php echo JText::_('PAGO_QUICK_CART_VIEW_CART'); ?></a>
	</div>
	<?php endif ?>
