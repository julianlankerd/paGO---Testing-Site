<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>

<?php
	defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<?php
	Pago::load_helpers( array( 'attributes' ) );

	$lg = $mod_pago_view_setting->product_grid_large;
	$md = $mod_pago_view_setting->product_grid_medium;
	$sm = $mod_pago_view_setting->product_grid_small;

	$mod_pago_grid_class = 'pg-lg-'.(12/$lg).' pg-md-'.(12/$md).' pg-sm-'.(12/$sm);

	if ($mod_pago_view_setting->product_settings_view_mode){
		$product_slide_mode = "pg-mod-product-horizontal-slide";
	}
	else{
		$product_slide_mode = "pg-mod-product-vertical-slide";
	}

	$product_image = json_decode($mod_pago_view_setting->product_image_settings);
	$product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($product_image->image_size);

	$product_image_settings = '.pg-mod-products .pg-mod-product-image-block{
		padding: '.$product_image->padding_top.'px '.$product_image->padding_right.'px '.$product_image->padding_bottom.'px '.$product_image->padding_left.'px;
		margin: '.$product_image->margin_top.'px '.$product_image->margin_right.'px '.$product_image->margin_bottom.'px '.$product_image->margin_left.'px;
		border-width: '.$product_image->border_top.'px '.$product_image->border_right.'px '.$product_image->border_bottom.'px '.$product_image->border_left.'px;
	}';

	$doc->addStyleDeclaration($product_image_settings);
?>
<div id="pago">
<div class="modal fade" aria-hidden="true" role="dialog" id="mod-login-modal">
	<div class="modal-dialog">
    	<div class="modal-content">
    		<div class="modal-header">
    			<button type="button" class="close" style="margin: 0 22px 0 0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel"><?php echo JTEXT::_('PAGO_GUEST_LOGIN_MODAL_TITLE'); ?></h4>
      		</div>
      		<div class="modal-body">
				<?php require PagoHelper::load_template( 'item', 'login' );  ?>
			</div>
		</div>
	</div>
</div>
</div>
<div class="pg-mod-products pg-module<?php echo $params->get( 'moduleclass_sfx'); ?> <?php echo $product_slide_mode.' '.$mod_pago_grid_class; ?> pg-main-container">
	<?php if ($product_slide_mode == 'pg-mod-product-vertical-slide'): ?>
		<a href = "javascript:void(0)" class = "pg-mod-product-slider-prev fa fa-chevron-up"></a>
		<a href = "javascript:void(0)" class = "pg-mod-product-slider-next fa fa-chevron-down"></a>

		<div class = "pg-mod-product-vertical-slider-pagination-block">
			<div class="pg-mod-product-slider-pagination"></div>
		</div>
	<?php endif; ?>
	<div class = "swiper-container">
		<div class = "swiper-wrapper">
			<?php $count = 1; ?>
			<?php foreach( $items as $product ) : ?>
				<?php
					$productPriceObj = Pago::get_instance( 'price' )->getItemDisplayPrice($product);
					$extraId = array('cid' => $product->primary_category);
					$allDownloads = PagoImageHandlerHelper::get_item_files( $product->id, true, 'download' );

					$allMedia = PagoImageHandlerHelper::get_item_files( $product->id, true, array( 'images' ) );
					$mediaImages = false;
					$image = '';
					$pin_url= '';

					if(count($allMedia)){
						$_img_obj = (object) array(
							'id'        		=> $allMedia[0]->id,
							'title'     		=> 'View Details for ' . html_entity_decode($product->name), //$product->file_title,
							'alias'     		=> $allMedia[0]->alias,
							'caption'   		=> $allMedia[0]->caption,
							'item_id'   		=> $product->id,
							'type'      		=> $allMedia[0]->type,
							'file_name' 		=> $allMedia[0]->file_name,
							'file_meta' 		=> $allMedia[0]->file_meta,
							'primary_category' 	=> $product->primary_category
						);
						$pin_url = PagoImageHandlerHelper::get_image_from_object( $_img_obj, $product_image_size_title, true, '' , false );
						$image = PagoImageHandlerHelper::get_image_from_object( $_img_obj, $product_image_size_title, false );

						if($allMedia){
							foreach ($allMedia as $media) {
								$mediaImages[] = PagoImageHandlerHelper::get_image_from_object( $media, $product_image_size_title, false );
							}
						}
					}
					$preselectVaration = false;
					$preselectedVarationId = PagoAttributesHelper::get_preselected_varation( $product->id );

					if($preselectedVarationId){
						$preselectVaration = template_functions::get_varation( $preselectedVarationId->id,$product_image_size_title );
					}
				?>
				<div class = "swiper-slide">
					<div class = "pg-mod-product product-container clearfix view-product-module" itemid="<?php echo $product->id;?>">
					<form name="addtocart" id="pg-addtocart<?php echo $product->id; ?>" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
						<div class = "pg-mod-product-col">
							<?php if(!(($image && $mod_pago_view_setting->product_settings_product_image) || ($mod_pago_view_setting->product_settings_media && $allMedia))) : ?>
								<?php if ($mod_pago_view_setting->product_settings_featured_badge && $product->featured) : ?>
									<!-- Feature -->
									<div class="pg-mod-product-featured"></div>
								<?php endif; ?>

								<?php if ($product->show_new && PagoHelper::item_is_new( $product->id )) : ?>
									<!-- New -->
									<span class="pg-mod-product-new">
										<?php echo JTEXT::_('MOD_PAGO_PRODUCT_NEW')?>
									</span>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ($mod_pago_view_setting->product_settings_product_title) : ?>
								<!-- Product Title -->
								<div class="pg-mod-product-title pg-mod-product-field">
									<?php if ($mod_pago_view_setting->product_settings_link_to_product) : ?>
										<a href="<?php echo $nav->build_url('item', $product->id, true, $extraId, true,$params->get( 'set_itemid')) ?>">
											<?php echo $product->name; ?>
										</a>
									<?php else: ?>
										<span>
											<?php echo $product->name; ?>
										</span>
									<?php endif;?>
								</div>
							<?php endif; ?>

							<?php if($mod_pago_view_setting->product_settings_product_image || $mod_pago_view_setting->product_settings_media) : ?>
								<div class = "pg-mod-product-image pg-mod-product-field">
									<div class = "pg-mod-product-image-block">
										<?php if ($image || $allMedia) : ?>
											<?php
												$haveImage = false;
												if($image && $mod_pago_view_setting->product_settings_product_image){
													$haveImage = true;
												}
											?>

											<?php if ($mod_pago_view_setting->product_settings_link_on_product_image) : ?>
												<a href="<?php echo $nav->build_url('item', $product->id, true, $extraId, true,$params->get( 'set_itemid')) ?>">
											<?php else : ?>
												<div>
											<?php endif; ?>

											<?php
												if($preselectVaration){
													if($preselectVaration && $preselectVaration['varation']->default ==  1){
														if($haveImage){
															echo $image;
														}
													}

													else{
														if($preselectVaration['images']){
															echo $preselectVaration['images'];
														}

														else{
															$size = PagoImageHandlerHelper::getSizeByName($product_image_size_title);
															$imgStyle = '';

															if($size->crop == 0){
																$imgStyle = 'style="max-width:'.$size->width.'px"';
															}

															else{
																$imgStyle = 'style="max-width:100%;max-height:'.$size->height.'px"';
															}

															echo '<img '.$imgStyle.' src='.JURI::root() . 'components/com_pago/images/noimage.jpg>';
														}
													}
												}
												else{
													if($haveImage){
														echo $image;
													}

													if ($mod_pago_view_setting->product_settings_media && $allMedia){
														if (!$image && count($allMedia) < 1){
															echo '<img src='.JURI::root() . 'components/com_pago/images/noimage.jpg>';
														}
														else{
															foreach($mediaImages as $index => $mediaImage){
																if($index != 0){
																	echo $mediaImage;
																}
															}
														}
													}
												}
											?>

											<?php if ($mod_pago_view_setting->product_settings_link_on_product_image) : ?>
												</a>
											<?php else : ?>
												</div>
											<?php endif; ?>
										<?php else : ?>
											<?php
												$size = PagoImageHandlerHelper::getSizeByName($product_image_size_title);
												$imgStyle = '';

												if($size->crop == 0){
													$imgStyle = 'style="max-width:'.$size->width.'px"';
												}

												else{
													$imgStyle = 'style="max-width:100%;max-height:'.$size->height.'px"';
												}
												echo '<img '.$imgStyle.' src='.JURI::root() . 'components/com_pago/images/noimage.jpg>';
											?>
										<?php endif; ?>

										<?php if ($mod_pago_view_setting->product_settings_featured_badge && $product->featured) : ?>
											<!-- Feature -->
											<div class="pg-mod-product-featured"></div>
										<?php endif; ?>

										<?php if ($product->show_new && PagoHelper::item_is_new( $product->id )) : ?>
											<!-- New -->
											<span class="pg-mod-product-new">
												<?php echo JTEXT::_('MOD_PAGO_PRODUCT_NEW')?>
											</span>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>

							<?php if ($mod_pago_view_setting->product_settings_sku || $mod_pago_view_setting->product_settings_price || $mod_pago_view_setting->product_settings_discounted_price) : ?>
								<div class = "pg-mod-product-info clearfix pg-mod-product-field">
									<?php if ($mod_pago_view_setting->product_settings_sku) : ?>
										<!-- Product SKU -->
										<div class="pg-mod-product-sku">
											<span> <?php echo JText::_( 'MOD_PAGO_PRODUCT_SKU' ); ?></span>
											<span class = "pg-mod-product-sku-code"><?php echo $product->sku;?>
										</div>
									<?php endif; ?>

									<?php if ($mod_pago_view_setting->product_settings_price || $mod_pago_view_setting->product_settings_discounted_price): ?>
										<!-- Product Price -->
										<div class="pg-mod-product-price">
											<?php
												if ($mod_pago_view_setting->product_settings_price && $mod_pago_view_setting->product_settings_discounted_price){
													if ($productPriceObj->old_price)
														echo '<div class = "pg-mod-product-old-price"><strike>'.Pago::get_instance('price')->format($productPriceObj->old_price).'</strike><span class = "pg-mod-product-price-separator"> /&nbsp; </span></div>';
													echo '<div class = "pg-mod-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
												}
												elseif($mod_pago_view_setting->product_settings_price && !$mod_pago_view_setting->product_settings_discounted_price){
													if ($productPriceObj->old_price){
														echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
													}
													else{
														echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
													}
												}
												elseif(!$mod_pago_view_setting->product_settings_price && $mod_pago_view_setting->product_settings_discounted_price){
													echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
												}
											?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ($mod_pago_view_setting->product_settings_category) : ?>
							<!-- Category -->
								<div class="pg-mod-product-category pg-mod-product-field">
									<span><?php echo JTEXT::_('MOD_PAGO_PRODUCT_CATEGORIES'); ?></span>
									<span><?php echo $product->category_name?></span>
								</div>
							<?php endif; ?>

							<?php if ($mod_pago_view_setting->product_settings_quantity_in_stock) : ?>
							<!-- Quantity in stock -->
								<div class="pg-mod-product-stock pg-mod-product-field">
									<span><?php echo JTEXT::_('MOD_PAGO_PRODUCT_STOCK'); ?></span>
									<span><?php
										if($product->qty_limit=='0')
											echo $product->qty;
										else
											echo JText::_('MOD_PAGO_PRODUCT_UNLIMITED');
									?></span>
								</div>
							<?php endif; ?>

							<?php if ($mod_pago_view_setting->product_settings_fb ||
							$mod_pago_view_setting->product_settings_tw ||
							$mod_pago_view_setting->product_settings_pinterest ||
							$mod_pago_view_setting->product_settings_google_plus) :?>
								<div class = "pg-mod-product-share-block clearfix">
									<?php
										$social_icons_count = 0;

										if ($mod_pago_view_setting->product_settings_fb)
											$social_icons_count++;

										if ($mod_pago_view_setting->product_settings_tw)
											$social_icons_count++;

										if ($mod_pago_view_setting->product_settings_pinterest)
											$social_icons_count++;

										if ($mod_pago_view_setting->product_settings_google_plus)
											$social_icons_count++;
									?>

									<div class = "pg-mod-product-sharing pg-mod-product-field has-border-top has-border-bottom">
										<ul class="social_icons_<?php echo $social_icons_count; ?>">
											<?php if ($mod_pago_view_setting->product_settings_fb) : ?>
												<li class = "pg-mod-product-sharing-facebook">
													<a target="_blank" href = "http://www.facebook.com/sharer.php?u=<?php echo $nav->build_url('item', $product->id, false, $extraId,true,$params->get( 'set_itemid')); ?>" class="fa fa-facebook"></a>
												</li>
											<?php endif;?>

											<?php if ($mod_pago_view_setting->product_settings_google_plus) : ?>
												<li class = "pg-mod-product-sharing-google-plus">
													<a target="_blank" href = "http://plus.google.com/share?url=<?php echo $nav->build_url('item', $product->id, false, $extraId,true,$params->get( 'set_itemid')); ?>" class="fa fa-google-plus"></a>
												</li>
											<?php endif;?>

											<?php if ($mod_pago_view_setting->product_settings_pinterest) : ?>
												<li class = "pg-mod-product-sharing-pinterest">
													<a target="_blank" href = "http://pinterest.com/pin/create/button/?url=<?php echo $nav->build_url('item', $product->id, false, $extraId,true,$params->get( 'set_itemid')); ?>&media=<?php echo $pin_url;?>&description=<?php echo $product->name; ?>" class="fa fa-pinterest"></a>
												</li>
											<?php endif;?>

											<?php if ($mod_pago_view_setting->product_settings_tw) : ?>
												<li class = "pg-mod-product-sharing-twitter">
													<a target="_blank" href = "http://twitter.com/intent/tweet?text=<?php echo $product->name; ?>&url=<?php echo $nav->build_url('item', $product->id, false, $extraId,true,$params->get( 'set_itemid')); ?>" class="fa fa-twitter"></a>
												</li>
											<?php endif;?>
										</ul>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<div class = "pg-mod-product-col">
							<?php
								$isMarginTop = '';
								if ($mod_pago_view_setting->product_settings_product_title){
									$isMarginTop = 'has-margin-top';
								}
							?>
							<?php if ($mod_pago_view_setting->product_settings_rating) :?>
								<!-- RATING -->
								<div class="pg-mod-product-rate pg-mod-product-field has-border-top has-border-bottom <?php echo $isMarginTop; ?>" item_id="<?php echo $product->id;?>">
									<span><?php echo JTEXT::_('MOD_PAGO_PRODUCT_RATE');?></span>
									<ul <?php echo $user->guest ? '':''?>><!-- class="rated" -->
										<li <?php echo $product->rating > 0 ? 'class="rated_star"':''?>><a rating="1" href = "javascript:void(0)"></a></li>
										<li <?php echo $product->rating > 1 ? 'class="rated_star"':''?>><a rating="2" href = "javascript:void(0)"></a></li>
										<li <?php echo $product->rating > 2 ? 'class="rated_star"':''?>><a rating="3" href = "javascript:void(0)"></a></li>
										<li <?php echo $product->rating > 3 ? 'class="rated_star"':''?>><a rating="4" href = "javascript:void(0)"></a></li>
										<li <?php echo $product->rating > 4 ? 'class="rated_star"':''?>><a rating="5" href = "javascript:void(0)"></a></li>
									</ul>
									<div class="pg-mod-product-rate-result"></div>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif; ?>

							<?php if( $product->description && $mod_pago_view_setting->product_settings_short_desc) : ?>
								<?php
									$isBorder = '';
									if($mod_pago_view_setting->product_settings_desc && $product->content){
										$isBorder = 'has-border-bottom';
									}
								?>
								<!-- Short description -->
								<div class="pg-mod-product-short-desc pg-mod-product-field <?php echo $isBorder.' '.$isMarginTop; ?>">
									<?php echo $product->description; ?>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif; ?>

							<?php if( $product->content && $mod_pago_view_setting->product_settings_desc) : ?>
								<!-- Long description -->
								<div class="pg-mod-product-long-desc pg-mod-product-field <?php echo $isMarginTop; ?>">
									<?php echo $product->content; ?>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif; ?>

							<?php if($mod_pago_view_setting->product_settings_downloads && $allDownloads) : ?>
								<!-- Downloads -->
								<div class="pg-mod-product-downloads-block pg-mod-product-field <?php echo $isMarginTop; ?>">
									<a href = "javascript:void(0)">
										<span><?php echo JTEXT::_("MOD_PAGO_PRODUCT_DOWNLOADS"); ?></span>
										<span class = "donwload-plus-minus"></span>
									</a>
									<ul class="pg-mod-product-downloads">
										<?php foreach ($allDownloads as $productDownloads) { ?>
											<?php
												$link = JURI::ROOT().'media/pago/items/'.$product->primary_category.'/'.$productDownloads->file_name;
											?>
											<li>
												<a class = "download-text" href="<?php echo $link; ?>"><?php echo $productDownloads->title; ?></a>
												<a class = "download-ico" href="<?php echo $link; ?>"></a>
											</li>
										<?php }?>
									</ul>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif;?>
						</div>

						<div class = "pg-mod-product-col">
							<?php
								$isMarginTop = '';
								if ($mod_pago_view_setting->product_settings_product_title){
									$isMarginTop = 'has-margin-top';
								}
							?>
							<?php
								$attributes = PagoAttributesHelper::get_item_attributes( $product );
								$varations = template_functions::get_varations( $product->id);
								echo "<input type='hidden' id='item_varations_".$product->id."' value='".$varations['jsonVarations']."'>";
								$showAttribute = false;
							?>
							<?php if($mod_pago_view_setting->product_settings_attribute) : ?>
								<?php $showAttribute = true; ?>
							<?php endif; ?>
								<!-- Attributes -->
								<?php if ($attributes && $showAttribute) : ?>
									<div class="<?php echo $showAttribute ? "":"hiddenAttribute"; ?> pg-mod-product-attributes pg-mod-product-field <?php echo $isMarginTop; ?>">
										<?php
											echo mod_pago_product_helper::product_display_attribute( $product );
										?>
									</div>
									<?php
										if($showAttribute){
											$isMarginTop = '';
										}
									?>
								<?php endif; ?>

							<?php if($mod_pago_view_setting->product_settings_add_to_cart) : ?>
								<!-- ADD TO CART -->
								<?php defined('_JEXEC') or die('Restricted access'); // no direct access ?>
								<?php
									$view = JFactory::getApplication()->input->get('view');
									$Itemid = JFactory::getApplication()->input->getInt('Itemid');
								?>
								<div class="pg-mod-product-addtocart <?php echo $isMarginTop; ?>">
									<!--check for child products (not implemented yet).-->
									<?php if(1 != 2) :
										if ($product->qty_limit ==0 && $product->qty <= 0 && $product->availibility_options != 0){
											if ($product->availibility_options == 1){
													echo "<div>" . JTEXT::_('PAGO_NOT_AVAILABLE') . "</div>";
											}
											elseif ($product->availibility_options == 2){
												Jhtml::_('behavior.modal');
												$cid = JFactory::getApplication()->input->get('cid');
												echo '<a href="'.JURI::root() . 'index.php?option=com_pago&view=contact_info&tmpl=component&cid=' . $cid . '&id=' . $product->id . '" class="modal" rel="{handler: \'iframe\', size: {x: 550, y: 400}}">' . JTEXT::_('COM_PAGO_CONTACT_FOR_MORE_INFO') . '</a>';
											}
											elseif ($product->availibility_options == 3 && strtotime($product->availibility_date) > strtotime(date("Y-m-d"))){
												echo "<div>" . JTEXT::_('PAGO_AVAILIBLITY_DATE') . " : " . $product->availibility_date . "</div>";
											}
											elseif($product->availibility_options == 4 && strtotime($product->availibility_date) != 0){
												echo "<div class='pg-out-of-stock'>" . JTEXT::_('PAGO_OUT_OF_STOCK') . "</div>";
											}
											else{
												echo "<div>" . JTEXT::_('PAGO_NOT_AVAILABLE') . "</div>";
											}
										}
										else{
											?>
											<!-- <form name="addtocart" id="pg-addtocart" method="post" action="<?php //echo JRoute::_( 'index.php' ) ?>"> -->
											<?php if($mod_pago_view_setting->product_settings_add_to_cart_qty) : ?>
												<?php
													/* QUANTITY */

													$return = base64_encode( JURI::current() );
													if( (isset( $product->qty ) && $product->qty > 0 ) ||  $product->availibility_options == 0 ) : ?>
														<div class="pg-mod-product-qty-con pg-mod-product-field">
															<label for="pg-item-opt-qty" class="pg-label"><?php echo JText::_('MOD_PAGO_PRODUCT_QTY'); ?></label>
															<input onkeyup='considerPrice(0,<?php echo $product->id; ?>);' maxlength="4" type="text" size="1" class="pg-inputbox pg-item-opt-qty" name="qty" value='1' />
														</div>
													<?php endif;
												?>
											<?php endif; ?>

											<input type="hidden" name="id" value="<?php echo $product->id; ?>" />
											<input type="hidden" name="option" value="com_pago" />
											<input type="hidden" name="view" value="cart" />
											<input type="hidden" name="task" value="add" />
											<input type="hidden" name="return" value="<?php echo $return ?>" />
											<?php echo JHtml::_('form.token'); ?>
											<?php
										}
									?>
									<?php else : ?>
										<a class="pg-button" title="<?php echo JText::_( 'PAGO_VIEW_DETAILS' ); ?>" href="<?php echo $nav->build_url('item', $product->id, true, $extraId, true,$params->get( 'set_itemid')) ?>">
											<?php echo JText::_( 'PAGO_VIEW_DETAILS' ); ?>
										</a>
									<?php endif; ?>

									<?php $isPrice = ''; ?>

									<?php if ($mod_pago_view_setting->product_settings_price
										|| $mod_pago_view_setting->product_settings_discounted_price
										|| isset( $product->qty ) && $product->qty > 0 ||
										$product->availibility_options == 0): ?>

										<div class = "pg-mod-product-field">
											<?php if ($mod_pago_view_setting->product_settings_price || $mod_pago_view_setting->product_settings_discounted_price): ?>
												<!-- Product Price -->
												<?php
													$isPrice = 'price-on';
													$isDiscounted = '';

													if ($productPriceObj->old_price)
														$isDiscounted = 'discounted';
												?>
												<div class="pg-mod-product-addtocart-price <?php echo $isDiscounted; ?>">
													<?php
														if ($mod_pago_view_setting->product_settings_price && $mod_pago_view_setting->product_settings_discounted_price){
															echo '<div class = "pg-mod-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
														}
														elseif($mod_pago_view_setting->product_settings_price && !$mod_pago_view_setting->product_settings_discounted_price){
															echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
														}
														elseif(!$mod_pago_view_setting->product_settings_price && $mod_pago_view_setting->product_settings_discounted_price){
															echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
														}
													?>
												</div>
											<?php endif; ?>

											<?php if( (isset( $product->qty ) && $product->qty > 0 ) ||  $product->availibility_options == 0 ) : ?>
												<button type="submit" class="pg-button pg-addtocart pg-addtocart-mod-products pg-green-background-btn <?php echo $isPrice; ?>" <?php echo $product->jump_to_checkout ? 'data-jump-to-checkout="'. JRoute::_( JURI::base( true ) . '/index.php?option=com_pago&view=checkout' ) .'"' : '' ?>><?php echo JText::_( 'MOD_PAGO_ADD_TO_CART' ); ?></button>
											<?php else: ?>
												<button type="submit" class="pg-button pg-addtocart pg-addtocart-mod-products pg-green-background-btn pg-disabled <?php echo $isPrice; ?>" disabled="disabled"><?php echo JText::_( 'MOD_PAGO_ADD_TO_CART' ); ?></button>
											<?php endif; ?>
										</div>
									<?php endif; ?>

									<div class="pg-addtocart-success-block">
										<div class="pg-addtocart-success-text">
											<?php
												//echo JTEXT::_('PAGO_PRODUCT_ADD_SUCCESS_ADDED').' '.$product->name.' '.JTEXT::_('PAGO_PRODUCT_ADD_SUCCESS_TO_CART');
											?>
										</div>
										<?php 
										$config = Pago::get_instance('config')->get('global');
										$skip_cart_page = $config->get('checkout.skip_cart_page');
										
										if($skip_cart_page == 1)
										{
											$linkVar = 'checkout';
											$langVar = 'MOD_PAGO_PRODUCT_GO_TO_CHECKOUT';
										}
										else
										{
											$linkVar = 'cart';
											$langVar = 'MOD_PAGO_PRODUCT_ADD_SUCCESS_VIEW_CART';
										}
										?>
										
										<a href = "<?php echo JRoute::_('index.php?option=com_pago&view='.$linkVar); ?>" class = "<?php echo $isMarginTop; ?>">
											<?php echo JTEXT::_($langVar); ?>
										</a>
										
										<div class="pg-addtocart-success-block-close"></div>
									</div>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif; ?>
							<?php
								if($preselectedVarationId){
									?>
								 		<script type = "text/javascript">
								 			mod_preselectVaration(<?php echo $preselectedVarationId->id;?>,<?php echo $product->id;?>,true);
								 		</script>
									<?php
								}
							?>
							<?php if($mod_pago_view_setting->product_settings_read_more) : ?>
								<!-- READ MORE -->
								<div class = "pg-mod-product-read-more pg-mod-product-field">
									<a class="pg-gray-background-btn" href="<?php echo $nav->build_url('item', $product->id, true, $extraId, true,$params->get( 'set_itemid')) ?>">
										<?php echo JTEXT::_('MOD_PAGO_PRODUCT_READ_MORE'); ?>
									</a>
								</div>
							<?php endif; ?>
							<?php $isMarginTop = ''; ?>
						</div>
					</form>
					</div>
				</div>
				<?php $count ++; ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<?php if ($product_slide_mode == 'pg-mod-product-horizontal-slide'): ?>
	<div class = "pg-mod-product-horizontal-slider-pagination-block">
		<a href = "javascript:void(0)" class = "pg-mod-product-slider-prev fa fa-chevron-left"></a>
		<div class="pg-mod-product-slider-pagination"></div>
		<a href = "javascript:void(0)" class = "pg-mod-product-slider-next fa fa-chevron-right"></a>
	</div>
<?php endif; ?>

<script type = "text/javascript">
	<?php
		echo "var lg = ".$lg.";";
		echo "var md = ".$md.";";
		echo "var sm = ".$sm.";";
		echo "var xs = 1;";

		if ($product_slide_mode == 'pg-mod-product-vertical-slide'){
			echo "var slideType = 'vertical';";
		}
		else{
			echo "var slideType = 'horizontal';";
		}
	?>
</script>
