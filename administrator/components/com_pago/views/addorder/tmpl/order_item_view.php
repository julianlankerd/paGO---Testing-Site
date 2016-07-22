<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
$_root = JURI::root( true );
$doc = JFactory::$document;


//$doc->addCustomTag('<meta property="og:title" content="'.$this->item->name.'" />');
//$doc->addCustomTag('<meta property="og:type" content="product" />');
// if ( isset( $this->images[0]->id) ) {
// 	$main_image_url = PagoImageHandlerHelper::get_image_from_object( $this->images[0], 'large', true,'',false );
// 	$doc->addCustomTag('<meta property="og:image" content="'.$main_image_url.'" />');
// }
// $doc->addCustomTag('<meta property="og:url" content="'.JFactory::getURI()->toString().'" />');
// Displays the full item detail view.
//$this->load_header();
Pago::load_helpers( array( 'attributes' ) );

//$cat_id = JFactory::getApplication()->input->get('cid');
//PagoHtml::thickbox();
$cid = $this->item->primary_category;

/* ******************************************************************* */
/* ******************************************************************* */
/* ******************************************************************* */


$item_id = JFactory::getApplication()->input->get( 'Itemid' );

/* ******************************************************************* */
/* ******************************************************************* */
/* ******************************************************************* */
?>

<?php
//PagoHtml::add_css( $_root . '/components/com_pago/templates/default/css/tango/skin.css' );
//PagoHtml::add_js( $_root . '/components/com_pago/templates/default/js/jquery.jcarousel.js' );

$preselectedVarationId = PagoAttributesHelper::get_preselected_varation( $this->item->id );

//
?>
<script>
jQuery(document).ready(function(){
	considerPrice(0,<?php echo $this->item->id; ?>);
	//getComments(<?php echo $this->item->id; ?>);
});
</script>

<?php // Revist price (possible snippet?)
	

	$productPriceObj = Pago::get_instance( 'price' )->getItemDisplayPrice_back($this->item);
	$allDownloads = PagoImageHandlerHelper::get_item_files( $this->item->id, true, 'download' );
?>

<?php
	$product_image = json_decode($this->viewSettings->product_view_settings_image_settings);
	if($product_image){
		$product_image_settings = '#pago #pg-product-view .pg-item-images-con{
			padding: '.$product_image->padding_top.'px '.$product_image->padding_right.'px '.$product_image->padding_bottom.'px '.$product_image->padding_left.'px;
			margin: '.$product_image->margin_top.'px '.$product_image->margin_right.'px '.$product_image->margin_bottom.'px '.$product_image->margin_left.'px;
			border-width: '.$product_image->border_top.'px '.$product_image->border_right.'px '.$product_image->border_bottom.'px '.$product_image->border_left.'px;
		}';

		$doc->addStyleDeclaration($product_image_settings);
	}
?>

<div id = "pg-product-view">
	<div id="pg-product-<?php echo $this->item->id; ?>" selectedVaration="0" itemId="<?php echo $this->item->id; ?>" class="pg-product-id-<?php echo $this->item->id; ?> clearfix product-container view-item">
		<div id="pg-product-synopsis" class="clearfix">
			<div class = "row" style="margin-left:0 !important;">		
				<div class = "col-sm-4" style="float:left;width:45%;">	
					<div class = "pg-item-view-left-container">	
						<?php if (count($this->images) > 1 && ($this->viewSettings->product_view_settings_media || $this->viewSettings->product_view_settings_product_image) ||
						(count($this->images) == 1 && $this->viewSettings->product_view_settings_product_image)) : ?>
							<div id="pg-product-images">
								<?php if ($this->item->featured) : ?>
									<!-- Feature -->
									<div class="pg-product-featured">
										<span class = "fa fa-star"></span>
									</div>
								<?php endif; ?>

								<?php if ($this->item->show_new && PagoHelper::item_is_new( $this->item->id )) : ?>
									<!-- New -->
									<span class="pg-product-new">
										<?php echo JTEXT::_('PAGO_PRODUCT_NEW')?>
									</span>
								<?php endif; ?>

								<div class = "pg-gallery-con-container">
									<div class = "pg-gallery-con">
										<div class="pg-item-images-con" style="height: 350px;owerflow:hidden;">
											<?php
												$product_view_settings_image_settings = json_decode($this->viewSettings->product_view_settings_image_settings);
												$product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($product_view_settings_image_settings->image_size);
												

												$productImageMaxWidth = $this->get('config')->get('media')->image_sizes->$product_image_size_title->width; 
												$productImageMaxHeight = $this->get('config')->get('media')->image_sizes->$product_image_size_title->height; 
												
												if($this->images[0]->type == "images"){
													if ($this->viewSettings->product_view_settings_product_image){
										        		$main_image_url_rel = PagoImageHandlerHelper::get_image_from_object( $this->images[0], $product_image_size_title, true );
										        		echo "<img  style='max-height:350px;'  class='pg-main-image' src='".$main_image_url_rel."' id='pg-imageid-".$this->images[0]->id."' >";
										        	}
										        	else{
										        		$main_image_url_rel = PagoImageHandlerHelper::get_image_from_object( $this->images[1], $product_image_size_title, true );
										        		echo "<img  style='max-height:350px;' class='pg-main-image' src='".$main_image_url_rel."' id='pg-imageid-".$this->images[1]->id."' >";
										        	}
												}else{
													echo "<img class='pg-main-image' src='' >";	
												}
												
											?>
											<input type="hidden" name="pg-image-size" id="pg-image-size" value="<?php echo $product_image_size_title; ?>"/>
											<div class="pg-item-video">
												<?php
												if($this->images[0]->type == "video" && !$preselectedVarationId){
													$file = JPATH_SITE.'/administrator/components/com_pago/helpers/video_sources.php';
													jimport('joomla.filesystem.file');
													if (JFile::exists($file))
													{
														require $file;
													}
													$videoEmbed = $tagReplace[$this->images[0]->provider];
													$videoEmbed = str_replace("{SOURCE}", $this->images[0]->video_key, $videoEmbed);
													echo $videoEmbed; 
												}
												?>
											</div>
										</div>
									</div>
								</div>
								<?php if (count($this->images) > 0 ) : ?>
								<?php
										$thumbImageWidth = $this->config->get('media')->image_sizes->thumbnail->width;
										$thumbImageHeight = $this->config->get('media')->image_sizes->thumbnail->height;

										$thumbImageSize = '#pago #pg-product-view #pg-item-images-add-con .pg-image-thumbnails li img{
											width:'.$thumbImageWidth.'px;
											height: '.$thumbImageHeight.'px;
										}';
										$display_media = '';
										if(!$this->viewSettings->product_view_settings_media || !$this->viewSettings->product_view_settings_product_image)
										{
											$display_media = 'style="display:none"';
										}
										$doc->addStyleDeclaration($thumbImageSize);
									?>
									<div id="pg-item-images-add-con-main" class="pg-item-images-add-con-main" <?php echo $display_media ?>>
										<div id="pg-item-images-add-con" class = "pg-thumbnail-swiper-container">
											<ul class="pg-image-thumbnails swiper-wrapper">
												<?php
													$item_count = count($this->images);
													if ($this->viewSettings->product_view_settings_product_image){
														$start = 0;
													}
													else{
														$start = 1;
													}
										            for ($i = $start; $i < $item_count; $i++) {
										                $image = PagoImageHandlerHelper::get_image_from_object( $this->images[$i], $product_image_size_title, true);
										                $imageThumb = PagoImageHandlerHelper::get_image_from_object( $this->images[$i], "thumbnail", true);
							                			?>
										                <li style="list-style: none;float:left;" class = "swiper-slide">
										                	<img <?php echo $this->images[$i]->type == 'video' ? 'imageType="video" videoId="'.$this->images[$i]->id.'"' : 'imageType="images"';?>  fullurl="<?php echo $image; ?>" class="changeAttributeSelect" type='item' itemId="<?php echo $this->item->id; ?>" src="<?php echo $imageThumb; ?>" >
										                </li>
										    			<?php
										            }
													if( empty( $product_image_size_title ) ) {
														$product_image_size_title = '-1';
													}
												 	$varations = PagoAttributesHelper::get_varations( $this->item->id,$product_image_size_title );
												 	if($varations['images'])
												 	{
												 		echo $varations['images'];
												 	}
												 ?>
											</ul>
										</div>
										<a href = "javascript:void(0)" class = "thumbnail-swiper-prev"></a>
										<a href = "javascript:void(0)" class = "thumbnail-swiper-next"></a>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						
					</div>
				</div>

				<div class = "col-sm-4"  style="float:left;width:45%;padding-left: 30px;">
					<div class="clearfix"></div>
						<?php if ($this->viewSettings->product_view_settings_product_title) : ?>
							<!-- TITLE -->
							<div class="pg-product-title">
								<h1>
									<?php echo html_entity_decode($this->item->name); ?>
								</h1>
							</div>
						<?php endif; ?>
						<?php if ($this->viewSettings->product_view_settings_sku) : ?>
							<!-- SKU -->
							<div class="pg-product-sku">
								<span><?php echo JText::_( 'PAGO_PRODUCT_SKU' ); ?></span>:
								<span class="product_sku_code"><?php echo $this->item->sku; ?></span>
							</div>
						<?php endif; ?>

					<form name="addtocart" id="pg-addtocart" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
						<div class = "pg-item-view-right-container">	
							<?php if ($this->viewSettings->product_view_settings_price || $this->viewSettings->product_view_settings_discounted_price) : ?>
								<!-- PRICE -->
								<div class="pg-product-price pg-product-field">
									<?php
										if ($this->viewSettings->product_view_settings_price && $this->viewSettings->product_view_settings_discounted_price){
											if ($productPriceObj->old_price)
												echo '<div class = "pg-product-old-price"><strike>'.Pago::get_instance('price')->format($productPriceObj->old_price).'</strike><span class = "pg-product-price-separator"> /&nbsp; </span></div>';
											echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
										}
										elseif($this->viewSettings->product_view_settings_price && !$this->viewSettings->product_view_settings_discounted_price){
											if ($productPriceObj->old_price){
												echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
											}
											else{
												echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
											}
										}
										elseif(!$this->viewSettings->product_view_settings_price && $this->viewSettings->product_view_settings_discounted_price){
											echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
										}
									?>
								</div>
							<?php endif; ?>

							<?php if( $this->item->description && $this->viewSettings->product_view_settings_short_desc) : ?>
								<!-- Short description -->
								<?php 
									$isBorder = '';
									if( !empty( $this->item->content ) &&  $this->viewSettings->product_view_settings_desc){
										$isBorder = 'has-border-bottom';
									}
								?>
								<div class="pg-product-short-desc pg-product-field <?php echo $isBorder; ?> ">
									<?php echo html_entity_decode($this->item->description); ?>
								</div>
							<?php endif; ?>

							<?php if( !empty( $this->item->content ) &&  $this->viewSettings->product_view_settings_desc) : ?>
								<!-- Long description -->
								<div class="pg-product-long-desc pg-product-field">
									<?php echo html_entity_decode($this->item->content); ?>
								</div>
							<?php endif; ?>

							<?php if ($this->viewSettings->product_view_settings_category || $this->viewSettings->product_view_settings_quantity_in_stock) : ?>
								<div class = "pg-product-category-stock-block pg-product-field has-border-top">
									<?php if ($this->viewSettings->product_view_settings_category) : ?>
									<!-- Category -->
										<div class="pg-product-category">
											<span><?php echo JTEXT::_('PAGO_CATEGORY_PRODUCT_CATEGORIES'); ?></span>
											<span><?php echo $this->category->name; ?></span>
										</div>
									<?php endif; ?>

									<?php if ($this->viewSettings->product_view_settings_quantity_in_stock) : ?>
									<!-- Quantity in stock -->
										<div class="pg-product-stock">
											<span><?php echo JTEXT::_('PAGO_CATEGORY_PRODUCT_STOCK'); ?></span>
											<span><?php 
											if($this->item->qty_limit=='0')
												echo $this->item->qty; 
											else
												echo JText::_('PAGO_UNLIMITED'); 
											?></span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							
							<?php
								$product_view_settings_image_settings = json_decode($this->viewSettings->product_view_settings_image_settings);
								$product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($product_view_settings_image_settings->image_size);
								$varations = PagoAttributesHelper::get_varations( $this->item->id,$product_image_size_title );
								$showAttribute = false;
							?>
							<?php if($this->viewSettings->product_view_settings_attribute) : ?>
								<?php $showAttribute = true; ?>	
							<?php endif; ?>
							<!-- Attributes -->
							<div class="<?php echo $showAttribute ? "":"hiddenAttribute"; ?> pg-product-attributes has-border-top pg-product-field">
								<?php
									echo PagoAttributesHelper::display_attribute( $this->item ); 
								?>
							</div>

							<?php echo "<input type='hidden' id='item_varations_".$this->item->id."' value='".$varations['jsonVarations']."'>"; ?>
							<!-- ********************* -->
							<!-- QUANTITY OUT OF STOCK -->
							<!-- ********************* -->

							<?php if($this->viewSettings->product_view_settings_downloads) : ?>
								<!-- Downloads -->
								<?php if ($allDownloads) : ?>
									<div class="pg-product-downloads-block pg-product-field">
										<a href = "javascript:void(0)">
											<span><?php echo JTEXT::_("PAGO_PRODUCT_DOWNLOADS"); ?></span>
											<span class="donwload-plus-minus"></span>
										</a>
										<ul class="pg-product-downloads">
											<?php foreach ($allDownloads as $productDownloads) { ?>
												<?php
													$link = JURI::ROOT().'media/pago/items/'.$cid.'/'.$productDownloads->file_name;
												?>
												<li>
													<a class = "download-text" href="<?php echo $link; ?>"><?php echo $productDownloads->title; ?></a>
													<a class = "download-ico" href="<?php echo $link; ?>"></a>
												</li>
											<?php }?>
										</ul>
									</div>
								<?php endif; ?>
							<?php endif;?>

							<?php //if( !empty($this->item->qty) && $this->item->qty <= 0) : ?>
								<!-- <p class="pg-out-of-stock"><?php //echo JText::_('PAGO_OUT_OF_STOCK'); ?></p> -->
							<?php //endif; ?>

							<?php if($this->viewSettings->product_view_settings_add_to_cart) : ?>
								<!-- ADD TO CART -->
									<?php defined('_JEXEC') or die('Restricted access'); // no direct access ?>
									<?php
										$view = JFactory::getApplication()->input->get('view');
										$Itemid = JFactory::getApplication()->input->getInt('Itemid');

										if(isset($this->item))
											$product = $this->item;
									?>
									<div class="pg-product-addtocart clearfix">
										<?php //check for child products (not implemented yet).
											if(1 != 2) :
											if ($product->availibility_options == 2)
											{
												echo '<a rel="" class="pg-title-button pg-button-new pg-green-text-btn" data-toggle="modal" data-target="#contact_info_modal" onclick="pull_upload_contctInfo('.$this->category->id.','. $product->id.',false);" href="javascript:void(0);">' . JTEXT::_('COM_PAGO_CONTACT_FOR_MORE_INFO') . '</a>';
											}else{
												if ($product->qty_limit==0 && $product->qty <= 0)
												{
													echo '<div class = "pg-green-background-btn">';

													if ($product->availibility_options == 0)
													{
														echo "<div>" . JTEXT::_('PAGO_OUT_OF_STOCK') . "</div>";
													}
													elseif ($product->availibility_options == 1)
													{
														echo "<div>" . JTEXT::_('PAGO_NOT_AVAILABLE') . "</div>";
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
													echo '</div>';
												}
												else{
													?>
													<!-- <form name="addtocart" id="pg-addtocart" method="post" action="<?php //echo JRoute::_( 'index.php' ) ?>"> -->
													<?php if($this->viewSettings->product_view_settings_add_to_cart_qty) : ?>
														<?php
															/* QUANTITY */
															$return = base64_encode( JURI::current() );
															if( (isset( $product->qty ) && $product->qty > 0 ) ||  $product->availibility_options == 0 ) : ?>
																<div class="pg-product-qty-con pg-product-field">
																	<label for="pg-item-opt-qty" class="pg-label"><?php echo JText::_('PAGO_ITEM_QTY'); ?></label>
																	<input onkeyup='considerPrice(0,<?php echo $product->id; ?>);' type="text" size="1" class="pg-inputbox pg-item-opt-qty" name="qty" value='1' />
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

													<?php $isPrice = '';?>

													<?php if( (isset( $product->qty ) && $product->qty > 0 ) ||  $product->availibility_options == 0 ) : ?>
														<div class = "pg-product-field">
															<?php if ($this->viewSettings->product_view_settings_price || $this->viewSettings->product_view_settings_discounted_price) : ?>													
																<!-- PRICE -->
																<?php 
																	$isPrice = 'price-on';																

																?>
																<div class="pg-addtocart-product-price">
																	<?php
																		if ($this->viewSettings->product_view_settings_price && $this->viewSettings->product_view_settings_discounted_price){
																			echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
																		}
																		elseif($this->viewSettings->product_view_settings_price && !$this->viewSettings->product_view_settings_discounted_price){
																			echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
																		}
																		elseif(!$this->viewSettings->product_view_settings_price && $this->viewSettings->product_view_settings_discounted_price){
																			echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
																		}
																	?>
																</div>
															<?php endif; ?>
															<button type="submit" class="pg-button pg-addtocart pg-green-background-btn <?php echo $isPrice; ?>"><?php echo JText::_( 'PAGO_ADD_TO_CART' ); ?></button>
														</div>
													<?php else: ?>
														<button type="submit" class="pg-button pg-addtocart pg-disabled" disabled="disabled"><?php echo JText::_( 'PAGO_ADD_TO_CART' ); ?></button>
													<?php endif; ?>
													<!-- </form> -->
													<?php
												}
											}
											?>
											<?php else : ?>
												<a class="pg-button" title="<?php echo JText::_( 'PAGO_VIEW_DETAILS' ); ?>" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=item&id=' . $item->id ); ?>">
													<?php echo JText::_( 'PAGO_VIEW_DETAILS' ); ?>
												</a>
											<?php endif; ?>
										<div class="pg-addtocart-success-block">
											<div class="pg-addtocart-success-text">
												<?php 
													//echo JTEXT::_('PAGO_PRODUCT_ADD_SUCCESS_ADDED').' '.$product->name.' '.JTEXT::_('PAGO_PRODUCT_ADD_SUCCESS_TO_CART'); 
												?>
											</div>
											<!-- <a href = "<?php echo JRoute::_('index.php?option=com_pago&view=cart'); ?>">
												<?php echo JTEXT::_('PAGO_PRODUCT_ADD_SUCCESS_VIEW_CART'); ?>
											</a> -->
											<div class="pg-addtocart-success-block-close"></div>
										</div>
									</div>
							<?php endif; ?>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php //$this->load_footer();
//jimport('joomla.application.module.helper');
//$module = JModuleHelper::getModule('mod_pago_item_suggestions','Pago Item Suggetion');

// if(count($module)){
//    echo JModuleHelper::renderModule($module);
// }
?>
<?php

if($preselectedVarationId){
	$attributeModel = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
	$varationAttribute = $attributeModel->get_product_varation_attribute( $preselectedVarationId->id, true);
	$preSelectedAttributeID = $varationAttribute[0]->attribute->id;
	$preSelectedOptionID = $varationAttribute[0]->option->id;
	?>
 		<script type = "text/javascript">
 			preselectVaration(<?php echo $preselectedVarationId->id;?>,<?php echo $this->item->id;?>,false,<?php echo $preSelectedAttributeID;?>,<?php echo $preSelectedOptionID;?>);
 		</script>
	<?php
}
?>
<?php if (count($this->images) > 1 && ($this->viewSettings->product_view_settings_media || $this->viewSettings->product_view_settings_product_image) ||
			(count($this->images) == 1 && $this->viewSettings->product_view_settings_product_image)) : ?>
<script type = "text/javascript">
	var thumbnailSwiper;
	var thumbnailImageWidth = jQuery('#pg-item-images-add-con li img').width()+10;
	var thumbnailImageHeight = jQuery('#pg-item-images-add-con li img').height();

	var thumbnailBottom = parseInt(jQuery('#pago #pg-product-view #pg-item-images-add-con-main').css('bottom'));
	
	var imageCount = jQuery('#pg-item-images-add-con li').length;

	var timeOut = '';

	function initThumbnailSlider(){
		if (jQuery('.pg-thumbnail-swiper-container').length){
			var thumbnailContainerWidth = jQuery('#pg-item-images-add-con').width();
			var thumbnailSlidesCount = Math.floor(thumbnailContainerWidth/thumbnailImageWidth);

			if (imageCount * thumbnailImageWidth > thumbnailContainerWidth){
				jQuery('.thumbnail-swiper-prev').css('display', 'block');
				jQuery('.thumbnail-swiper-next').css('display', 'block');
			}
			else{
				jQuery('.thumbnail-swiper-prev').css('display', 'none');
				jQuery('.thumbnail-swiper-next').css('display', 'none');	
			}

			thumbnailSwiper = new Swiper('.pg-thumbnail-swiper-container',{
			    slidesPerView: thumbnailSlidesCount,
			    paginationClickable: true,
			    keyboardControl: false,
			    simulateTouch: false,
			    resizeReInit: true,
			    calculateHeight: true,
			});
		}
	}

	var imageMaxWidth = <?php echo $productImageMaxWidth;?>;
	var imageMaxHeight = <?php echo $productImageMaxHeight;?>;

	jQuery(document).ready(function(){		
		var imageContainerWidth = jQuery('#pg-product-images').width();
		var imageContainerHeight = imageMaxHeight/(imageMaxWidth/imageContainerWidth);
		
		if (imageContainerHeight < imageContainerWidth / 3)
			imageContainerHeight = imageContainerWidth = 3;
			
		jQuery('#pg-product-images').css('height', imageContainerHeight);
		jQuery('#pg-product-images img').css('max-height', imageContainerHeight);

		jQuery('#pg-product-images .pg-item-video iframe').css('width', imageContainerWidth);
		jQuery('#pg-product-images .pg-item-video iframe').css('height', imageContainerHeight-thumbnailImageHeight-2*thumbnailBottom);
		jQuery('#pg-product-images .pg-item-video iframe').css('margin-top', -(thumbnailImageHeight+2*thumbnailBottom));

		jQuery('#pg-product-view .pg_attr_options[attrtype="0"][attrdisplaytype="0"] select').on('change', function(){
			var obj = jQuery(this).siblings('.chosen-container').find('.chosen-single>span');
			var obj_class = jQuery(this).find('option:selected').attr('rel');
			obj.attr('class','');
			obj.addClass('pg-color-'+obj_class);
		})

		jQuery(document).on('click', '.thumbnail-swiper-prev', function(e){
			e.preventDefault();
			thumbnailSwiper.swipePrev();
		})

		jQuery(document).on('click', '.thumbnail-swiper-next', function(e){
			e.preventDefault();
			thumbnailSwiper.swipeNext();
		})
	})

	jQuery(window).load(function(){
		initThumbnailSlider();

		if (jQuery('.pg-related-products').length){
			var containerWidth = 0;
			var productWidth = 0;
			var slidePerView = 0;

			containerWidth = jQuery('.pg-related-products-swiper-cantainer').width();
			productWidth = parseInt(jQuery('.pg-related-product').width())+parseInt(jQuery('.pg-related-product').css('margin-right'))+parseInt(jQuery('.pg-related-product').css('margin-left'));

			slidePerView = Math.floor(containerWidth/productWidth);

			if (slidePerView == 0){
				slidePerView = 1;
			}

			var mySwiper = new Swiper('.pg-related-products-swiper-cantainer',{
			    slidesPerView: slidePerView,
			    paginationClickable: true,
			    keyboardControl: true,
			    simulateTouch: false,
			    resizeReInit: true,
			    calculateHeight: true,
			    //cssWidthAndHeight: true,
			    //loop: true,
			});

			jQuery(window).resize(function(){
				containerWidth = jQuery('.pg-related-products-swiper-cantainer').width();
				productWidth = parseInt(jQuery('.pg-related-product').width())+parseInt(jQuery('.pg-related-product').css('margin-right'))+parseInt(jQuery('.pg-related-product').css('margin-left'));

				slidePerView = Math.floor(containerWidth/productWidth);

				if (slidePerView == 0){
					slidePerView = 1;
				}

				mySwiper.params.slidesPerView = slidePerView;
			})

			jQuery(document).on('click', '.pg-related-product-slider-prev', function(e){
				e.preventDefault();
				mySwiper.swipePrev();
			})

			jQuery(document).on('click', '.pg-related-product-slider-next', function(e){
				e.preventDefault();
				mySwiper.swipeNext();
			})
		}
	})
	jQuery(window).resize(function(){
		var addCommentContainerWidth = parseInt(jQuery('.pg-add-comment-container').width());
		var addCommentAvatarWidth = 62;

		jQuery('.pg-add-comment-container textarea').css('width', (addCommentContainerWidth-addCommentAvatarWidth)+'px');
		
		clearTimeout(timeOut);
		timeOut = setTimeout(function(){
			initThumbnailSlider();
		}, 200);

		var imageContainerWidth = jQuery('#pg-product-images').width();
		var imageContainerHeight = imageMaxHeight/(imageMaxWidth/imageContainerWidth);
		
		if (imageContainerHeight < imageContainerWidth / 3)
			imageContainerHeight = imageContainerWidth = 3;
			
		jQuery('#pg-product-images').css('height', imageContainerHeight);
		jQuery('#pg-product-images img').css('max-height', imageContainerHeight);

		jQuery('#pg-product-images .pg-item-video iframe').css('width', imageContainerWidth);
		jQuery('#pg-product-images .pg-item-video iframe').css('height', imageContainerHeight-thumbnailImageHeight-2*thumbnailBottom);
		jQuery('#pg-product-images .pg-item-video iframe').css('margin-top', -(thumbnailImageHeight+2*thumbnailBottom));
	})
</script>
<?php endif;?>
<?php
	$addComment = JFactory::getApplication()->input->get('addComment');
	if($addComment == 1){
		?>
			<script type = "text/javascript">
				var addCommentCon = jQuery('.pg-add-comment-container').offset().top;
				jQuery('html, body').animate({scrollTop:addCommentCon}, 'slow');
			</script>
		<?php
	}
?>
<script type = "text/javascript">
	jQuery(document).ready(function(){
		var addCommentContainerWidth = parseInt(jQuery('.pg-add-comment-container').width());
		var addCommentAvatarImageWidth = parseInt(jQuery('.pg-add-comment-container .pg-comment-author-image').width());
		var addCommentAvatarImageBorderLeft = parseInt(jQuery('.pg-add-comment-container .pg-comment-author-image').css('border-left-width'));
		var addCommentAvatarImageBorderRight = parseInt(jQuery('.pg-add-comment-container .pg-comment-author-image').css('border-right-width'));
		var addCommentAvatarImageMargin = parseInt(jQuery('.pg-add-comment-container .pg-comment-author-image').css('margin-right'));

		jQuery('.pg-add-comment-container textarea').css('width', addCommentContainerWidth-addCommentAvatarImageWidth-addCommentAvatarImageMargin-addCommentAvatarImageBorderLeft-addCommentAvatarImageBorderRight-1);
	})
</script>