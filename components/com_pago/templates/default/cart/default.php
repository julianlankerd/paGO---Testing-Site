<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php $this->load_header(); ?>
<?php 
	$config = Pago::get_instance('config')->get();
	$checkout_continue_shopping_link = $config->get('checkout.checkout_continue_shopping_link');
	$shipping_plugins = PagoHelper::get_all_plugins('pago_shippers', 1);
	$imageColumn = false;
	if(isset($this->cart['items'])){
		foreach($this->cart['items'] as $key => $product){
			if ( $product->images ){
				$imageColumn = true;
				break;
			}
		}
	}
?>
<div id="pg-cart" class="clearfix">
	<div id="pg-cart-header" class="clearfix">
		<div class="pg-title pg-cart-title">
			<h1><?php echo JText::_('PAGO_CART_TITLE'); ?></h1>
		</div>
	</div>
	<?php 
		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$attributeModel = JModelLegacy::getInstance('Attribute','PagoModel');
	?>
	<?php if($this->cart['total_qty'] > 0): ?>
		<?php
			$Productid = $config->get('checkout.pago_cart_itemid');

			if (empty($Productid)){
				$Productid = JFactory::getApplication()->input->get('Itemid');
			}
			$itemidstr = '';
			if(isset($Itemid)){
				$itemidstr = '&itemid='.$Itemid;
			}
		?>
		
		<div id="pg-cart-contents">
			<div id="pg-cart-discount-message"><?php echo $this->cart['discount_message'];?></div>
			<div id="pg-cart-table" class="pg-table">

				<div class="pg-cart-labels">
					<div class="pg-cart-product-image"><?php echo JText::_('PAGO_CART_PRODUCT'); ?></div>
					<div class="pg-cart-product-desc"><?php echo JText::_('PAGO_CART_PRODUCT_DESC'); ?></div>
					<div class="pg-cart-product-quantity"><?php echo JText::_('PAGO_CART_QTY'); ?></div>
					<div class="pg-cart-product-price"><?php echo JText::_('PAGO_CART_PRICE'); ?></div>
					<div class="pg-cart-product-total"><?php echo JText::_('PAGO_CART_TOTAL'); ?></div>
					<div class="pg-cart-product-remove"><?php echo JText::_('PAGO_CART_REMOVE'); ?></div>
				</div>

				<div class="pg-cart-products">
				<?php foreach($this->cart['items'] as $key => $product): ?>
					<div class="pg-cart-product">
						<div class="pg-cart-product-image">
							<?php if ( $product->images ) : ?>
								<?php 
									$cart_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($config->get('cart.cart_image_size'));
									if(isset($product->varationId) && $product->varationId){
										
										$noImage = false;

										$image = $attributeModel->getPhotoByType($product->varationId,'product_variation',$cart_image_size_title);
										
										$defVar = $attributeModel->checkDefaultVariation($product->varationId);
										
										if($defVar){
											$images = PagoImageHandlerHelper::get_item_files( $product->id, true, array( 'images' ) );
											if($images){
												$image = PagoImageHandlerHelper::get_image_from_object( $images[0], $cart_image_size_title, true );
											


											}else{
												$image = false;	
											}
										}
										
										
										if(!$image){
											$image = $attributeModel->getSameVarationImagePath($product->varationId,$cart_image_size_title);	
											

											if(!$image){
												$image = false;
											}	
										}

										if(!$image)
										{
											$cart_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($config->get('cart.cart_image_size'));
									
											$images = PagoImageHandlerHelper::get_item_files( $product->id, true, array( 'images' ) );
											if($images)
											{
												$image = PagoImageHandlerHelper::get_image_from_object( $images[0], $cart_image_size_title, true );
											}
											else
											{
												$image = false;	
												$noImage = true;
											} 
										}

										if($noImage)
										{
											$size = PagoImageHandlerHelper::getSizeByName($cart_image_size_title);
											$imgStyle = '';
											if($size->crop == 0){
												$imgStyle = 'style="max-width:'.$size->width.'px"';
											}else{
												$imgStyle = 'style="max-width:100%;max-height:'.$size->height.'px"';
											}
											echo '<img '.$imgStyle.' src='.JURI::root() . 'components/com_pago/images/noimage.jpg>';
										}else{
											echo '<img src='.$image .'>';
										}

									}else{
										PagoImageHelper::display_image($product->images, $cart_image_size_title, $product->name, $product->name);
									}
								?>
							<?php else: ?>
								<?php 
								$cart_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($config->get('cart.cart_image_size'));
									
								$images = PagoImageHandlerHelper::get_item_files( $product->id, true, array( 'images' ) );?>
								<?php if($images){
									$image = PagoImageHandlerHelper::get_image_from_object( $images[0], $cart_image_size_title, true );
									}else{
										$image = false;	
									} ?>

								<?php 
								if(!$image)
								{
									$cart_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($config->get('cart.cart_image_size'));
									$size = PagoImageHandlerHelper::getSizeByName($cart_image_size_title);
									$imgStyle = '';
									if($size->crop == 0){
										$imgStyle = 'style="max-width:'.$size->width.'px"';
									}else{
										$imgStyle = 'style="max-width:100%;max-height:'.$size->height.'px"';
									}
									 echo '<img '.$imgStyle.' src='.JURI::root() . 'components/com_pago/images/noimage.jpg>'; 
								}
								else
								{
									echo '<img src='.$image .'>';
								} ?>
							<?php endif; ?>
						</div>
						<div class="pg-cart-product-name">
							<?php
								$Productid = $this->nav->getItemid($product->id, $product->cid);
								$link = JRoute::_('index.php?option=com_pago&view=item&id=' . $product->id . '&cid=' . $product->cid .'&Itemid=' . $Productid);
							?>

							<a href="<?php echo $link; ?>"><?php echo $product->name; ?></a>						
							<span class="pg-product-sku"><span><?php echo JText::_( 'PAGO_PRODUCT_SKU' ); ?></span>:<?php echo $product->sku; ?></span>
					
							<?php 
								if(isset($product->attrsVal)){
									echo "<div class='pg-cart-attributes'>";							
										foreach ($product->attrsVal as $attrVal) {
											if($attrVal){ // if show in cart set none
												echo "<div class='pg-cart-attribute'>";							
												echo "<span class='pg-cart-attribute-name'>". $attrVal['attribute']->name .": ";
												// if(strlen($attrVal['attribute_option']->sku) > 0){
												// 	echo " <span class='pg-cart-attribute-sku'>(Sku: ".$attrVal['attribute_option']->sku.") <span>";	
												// }
												echo "</span>";
												echo "<div class='pg-cart-attribute-value'>";
												switch ($attrVal['attribute']->type) {
													case '0':
														echo $attrVal['attribute_option']->name."<span class='pg_color_option_form' style='background-color:". $attrVal['attribute_option']->color ."'></span>";
													break;
													case '1':
														echo $attrVal['attribute_option']->name . " " . $attrVal['attribute_option']->size." (";
														switch ($attrVal['attribute_option']->size_type) {
															case '0':
																echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US');
															break;
															case '1':
																echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL');
															break;
															case '2':
																echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK');
															break;
															case '3':
																echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE');
															break;
															case '4':
																echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY');
															break;
															case '5':
																echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA');
															break;
															case '6':
																echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN');
															break;
														}
														echo ")";	
													break;
													case '2':
														echo $attrVal['attribute_option']->name;
													break;
													case '3':
														echo $attrVal['attribute_option']->name;
													break;		
												}
											echo "</div>";
											echo "</div>";
										}
									}
								echo "</div>";
								} 
							?>
						</div>
						<div class="pg-cart-product-qty">
							<form class="pg-cart-actions" method="post" action="<?php echo JRoute::_('index.php') ?>">
								<input type="hidden" value="com_pago" name="option">
								<input type="hidden" value="cart" name="view">
								<input type="hidden" value="update" name="task">
								<input type="hidden" value="id_<?php echo $key ?>" name="id">
								<input type="hidden" value="<?php echo $product->price ?>" name="price_each">
								<input type="hidden" value="<?php if ($product->price == 0){echo "0";} else{ echo $product->subtotal; }?>" name="current_subtotal">
								<input type="hidden" value="<?php echo $this->cart['format']['subtotal'] ?>" name="subtotal">
								<input type="hidden" value="<?php echo $this->cart['format']['total'] ?>" name="total">
								<input type="text" value="<?php echo $product->cart_qty ?>" name="qty" maxlength="4" size="1" class="pg-inputbox" title="<?php echo JText::_('PAGO_CART_UPDATE_IPUTBOX_TITLE'); ?>">
								<button class="pg-cart-update" type="submit"></button>
								<?php echo JHTML::_( 'form.token' ) ?>
							</form>
							<div class="pg-cart-qty-update-message-wrapper">
								<div class="pg-cart-qty-update-message">
									<div class="pg-addtocart-success-block-close">
									</div>
								</div>
							</div>
						</div>
						<div class="pg-cart-product-price"><?php echo Pago::get_instance( 'price' )->format($product->price); ?></div>
						<div class="pg-cart-product-total">
							<?php 
								if ($product->price == 0){
									echo Pago::get_instance( 'price' )->format(0);
								}
								else{
									echo Pago::get_instance( 'price' )->format($product->subtotal);
								}
							?>
						</div>
						<div class = "pg-cart-product-remove">
							<form class="pg-cart-actions" name="delete" method="post" action="<?php echo JRoute::_('index.php') ?>">
								<input type="hidden" value="com_pago" name="option">
								<input type="hidden" value="cart" name="view">
								<input type="hidden" value="delete" name="task">
								<input type="hidden" value="id_<?php echo $key ?>" name="id">
								<button class="pg-cart-remove" type="submit"></button>
								<?php echo JHTML::_( 'form.token' ) ?>
							</form>
						</div>
					</div>
				<?php endforeach ?>
				</div>

				<div class="pg-cart-overview">
					<div class="pg-cart-totals pg-cart-subtotal">
						<div><span><?php echo JText::_('PAGO_CART_SUBTOTAL'); ?></span></div>
						<div class="pg-cart-total"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['subtotal']) ?></div>
					</div>
					<?php if ( isset($this->cart['format']['discount']) && $this->cart['format']['discount'] !== '0') :	?>
						<div class="pg-cart-totals pg-cart-discount">
							<div><span><?php echo JText::_('PAGO_CART_DISCOUNT'); ?></span></div>
							<div class="pg-cart-total"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['discount']) ?></div>
						</div>
					<?php endif; ?>
					<div class="pg-cart-totals pg-cart-shipping-total">
						<div><span><?php echo JText::_('PAGO_CART_SHIPPING_TOTAL'); ?></span></div>
						<div class="pg-cart-total"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['shipping']) ?></div>
					</div>
					<?php if(isset($promo_code)) : ?>
						<div class="pg-cart-totals pg-cart-promo-total">
							<div><span><?php echo (isset($promo_codes)) ? $promo_codes : '' ?></span></div>
							<div class="pg-cart-total"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['promos']) ?></div>
						</div>
					<?php endif; ?>
					<div class="pg-cart-totals pg-cart-tax-total">
						<div><span><?php echo JText::_('PAGO_CART_TAX_TOTAL'); ?></span></div>
						<div class="pg-cart-total"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['tax']) ?></div>
					</div>
					<div class="pg-cart-totals pg-cart-grand-total">
						<div><span><?php echo JText::_('PAGO_CART_TOTAL'); ?></span></div>
						<div class="pg-cart-total"><?php echo Pago::get_instance( 'price' )->format($this->cart['format']['total']) ?></div>
					</div>
				</div>


			</div>
		</div>

		<div id="pg-cart-footer" class="clearfix">
			<div id="pg-cart-promo-estimate">
				<div id="pg-cart-promo-code">
					<?php if ( !isset($this->cart['coupon']['code'] ) ): ?>
						<form name="pg-promo-codes" method="post" action="<?php echo JRoute::_('index.php' ) ?>">
							<label for="pg-promo-code"><?php echo JText::_('PAGO_CART_PROMO_CODE'); ?>:</label>
							<input width="10" type="text" class="pg-inputbox" maxlength="30" id="pg-promo-code" name="couponcode" />
							<input type="hidden" value="com_pago" name="option" />
							<input type="hidden" name="view" value="cart" />
							<input type="hidden" name="task" value="coupon">
							<button class = "pg-gray-background-btn pg-no-hover" id="applyPromoCode" type="submit"><?php echo JText::_('PAGO_CART_SUBMIT_PROMO'); ?></button>
							<?php echo JHTML::_( 'form.token' ) ?>
						</form>
					<?php endif; ?>
				</div>
				<div id="pg-cart-estimate">
					<?php if(!isset($zip) && count($shipping_plugins) > 0 && !$config->get('checkout.skip_shipping') ) : ?>
						<form name="pg-estimate-zip" method="post" action="<?php echo JRoute::_('index.php'); ?>">
							<label for="zip_code"><?php echo JText::_('PAGO_CART_ENTER_ZIP'); ?></label>
							<input type="text" name="zip_code" id="pg-zip-code" class="pg-inputbox pg-cart-zip-code" />
							<input type="hidden" value="com_pago" name="option" />
							<input type="hidden" name="view" value="cart" />
							<input type="hidden" name="task" value="shippingEstimation" />
							<button type="button" id="pg-cart-shipping-estimation-button" class="pg-gray-background-btn pg-cart-shipping-estimation-button pg-no-hover"><?php echo JText::_('PAGO_CART_SUBMIT_ZIP'); ?></button>
						</form>
						<div id="pg-cart-shipping-estimation-content"></div>
					<?php else : ?>
						<?php //echo $shipping_methods; ?>
						<!--<form name="pg-shipping-methods" method="post" action="<?php echo JRoute::_('index.php'); ?>">
							<label for="shipping_method"><?php echo JText::_('PAGO_CART_SELECT_SHIPPING_METHOD'); ?></label>
							<select name="shipping_method" id="pg-shipping-methods">
								<option name="ups_ground" value="ups_ground">UPS Ground - $9.99</option>
								<option name="ups_3day" value="ups_3day">UPS 3-day Select - $12.99</option>
								<option name="ups_nextday" value="ups_nextday">UPS Next Day - $49.99</option>
							</select>
							<label for="zip_code"><?php echo JText::_('PAGO_CART_ENTER_ZIP'); ?></label>
							<input type="text" name="zip_code" id="pg-zip-code" class="pg-inputbox pg-cart-zip-code" />
							<input type="hidden" value="com_pago" name="option" />
							<input type="hidden" name="view" value="cart" />
							<button type="submit"><?php echo JText::_('PAGO_CART_SUBMIT_SHIPPING_METHOD'); ?></button>
						</form>-->
					<?php endif; ?>
				</div>
			</div>

			<div class="pg-cart-express-checkout">
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
			<div class="pg-cart-checkout clearfix">
			<?php
				if(isset($checkout_continue_shopping_link))
				{
					$clink = $checkout_continue_shopping_link;
				}
				else
				{
					$clink =  JURI::root();
				}
				
				 ?>
				<a href="<?php echo $clink; ?>" title="<?php echo JText::_('PAGO_CART_CONTINUE_SHOPPING'); ?>" class="pg-continue-shopping pg-gray-text-btn pg-no-hover"><?php echo JText::_('PAGO_CART_CONTINUE_SHOPPING'); ?></a>
				<a href="<?php echo JRoute::_('index.php?option=com_pago&view=cart&task=clear'.$itemidstr); ?>" title="<?php echo JText::_('PAGO_CART_EMPTY_CART'); ?>" class="pg-empty-cart pg-gray-background-btn pg-no-hover"><?php echo JText::_('PAGO_CART_EMPTY_CART'); ?></a>
				<a href="<?php echo JRoute::_('index.php?option=com_pago&view=checkout'.$itemidstr); ?>" title="<?php echo JText::_('PAGO_CART_CHECKOUT'); ?>" class="pg-checkout-link pg-green-background-btn pg-no-hover"><?php echo JText::_('PAGO_CART_CHECKOUT'); ?></a>
			</div>
		</div>
	<?php else: ?>
		<div id="pg-cart-contents" class="pg-cart-empty">
			<p><?php echo JText::_('PAGO_CART_EMPTY'); ?></p>
		</div>
		<div id="pg-cart-footer" class="pg-cart-empty clearfix">
			<div class="pg-cart-checkout">
			<?php
				if(isset($checkout_continue_shopping_link))
				{
					$clink = $checkout_continue_shopping_link;
				}
				else
				{
					$clink =  JURI::root();
				}
				?>
				<a href="<?php echo $clink ?>" title="<?php echo JText::_('PAGO_CART_CONTINUE_SHOPPING'); ?>" class="pg-continue-shopping pg-gray-text-btn pg-no-hover"><?php echo JText::_('PAGO_CART_CONTINUE_SHOPPING'); ?></a>
				<span class="pg-checkout-link pg-green-background-btn pg-no-hover "><?php echo JText::_('PAGO_CART_CHECKOUT'); ?></span>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php $this->load_footer(); ?>
