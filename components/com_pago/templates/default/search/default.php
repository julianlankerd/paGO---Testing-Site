<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
$this->load_header();
Pago::load_helpers( array( 'helper','attributes' ) );
?>
<div class="pagoSearch">
<form name="pg-search" class="pg-search-form" method="get" action="<?php echo JRoute::_( 'index.php' ) ?>">
	<input type="hidden" name="option" value="com_pago" />
	<input type="hidden" name="view" value="<?php echo JTEXT::_('PAGO_SEARCH'); ?>" />
	<?php
		$Itemid = JFactory::getApplication()->input->get->get( 'Itemid'); 
		if($Itemid)
		{
			?>
				<input type="hidden" name="Itemid" value="<?php echo $_REQUEST['Itemid'];?>" />
		<?php } 
	?>
	<div class="pg-search-bar">
	<input type="text" name="search_query" class="search_query_input" value="<?php echo $this->searchQuery ?  $this->searchQuery : ''; ?>" placeholder="search"/>
	<input type="submit" value="<?php echo JTEXT::_('PAGO_SEARCH'); ?>" class = "pg-gray-background-btn pago-btn-search"/>
	</div>
	<?php
		$sortOrder = $this->nav->getSortByProductsList();
		 $sortByItem = JRequest::getVar('sortbyitem');
		echo "<div class='sortByInput'><label style='vertical-align: bottom;'>".JTEXT::_('PAGO_SORT_BY')." :  </label>&nbsp;" . JHtml::_('select.genericlist',$sortOrder,'sortbyitem','class="inputbox pg-select" id="pg-sort" size="1" onChange="this.form.submit();"','value','text',$sortByItem) . "</div>";	
	?>
</form>
<div class="clear"></div>
</div>

<script type = "text/javascript">
	jQuery(document).ready(function(){
		/*jQuery(".pago-btn-search").on("click",function(){
	      if(jQuery(".search_query_input").val()==""){
	       return false;
	      }
	    })*/
		jQuery('#pg-category-view .pg_attr_options[attrtype="0"][attrdisplaytype="0"] select').on('change', function(){
			var obj = jQuery(this).siblings('.chosen-container').find('.chosen-single>span');
			var obj_class = jQuery(this).find('option:selected').attr('rel');
			obj.attr('class','');
			obj.addClass('pg-color-'+obj_class);
		})

		String.prototype.escapeHTML = function() {
        return this.replace(/&/g, "&amp;")
                   .replace(/</g, "&lt;")
                   .replace(/>/g, "&gt;")
                   .replace(/"/g, "&quot;")
                   .replace(/'/g, "&#039;");
    	}
    	jQuery('.pg-search-form').submit(function() {
    		var val = jQuery('.search_query_input').val().escapeHTML();
    		jQuery('.search_query_input').val(val);
    		return true;
    	});

	})
</script>


<?php
	$doc = JFactory::$document;

	function display_attribute( $item ) {
		Pago::load_helpers( array( 'attributes' ) );

		$attributes = PagoAttributesHelper::get_item_attributes( $item );
		$removeDefault = false;
		$html = '';

		$attr_type=array('color','size','material','custom');
		$size_type=array(
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_US'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_USL'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_UK'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_EUROPE'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_ITALY'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_AUSTRALISA'),
			JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_JAPAN')
			);

		if ( $attributes ) {
			foreach ($attributes as $attribute) {
				if(isset($attribute->options)){
					$isBorder = 'has-border-bottom';
					if ($attribute->display_type == 0){
						$isBorder = '';
					}
					$isColorList = '';
					if ($attribute->display_type == 1 && $attribute->type==0){
						$isColorList = 'color-list';
					}
					$html .= "<div class='pg-attribute-product-container clearfix pg-cat-product-field ".$isBorder."' type='".$attr_type[$attribute->type]."'>";
					//if($attribute->type=='1') $attribute->name.=$size_type[$attribute->size];
					$html .= '<div class = "pg-attr-'.$attr_type[$attribute->type].'"><label class="pg-attribute-label '.$isColorList.'" for="pg-attribute-' . $attribute->id . '">' . $attribute->name . ':</label></div>';
					if( $attribute->options ) {
						$html .= "<div class='pg_attr_options pg_attr_". $attribute->id ."' attr_id='".$attribute->id."' attrType='".$attribute->type."' attrDisplayType='".$attribute->display_type."'>";

						switch ($attribute->display_type) {
							case '0': //dropdown
								if($attribute->type==0){
									$doc = JFactory::$document;
									$style_colors = '';
									foreach ($attribute->options as $option) {
										$style_colors .= '.pg-color-'.$option->name.':after{
											background-color:'.$option->color.';
										}';
									}
									$doc->addStyleDeclaration($style_colors);
								}
								$html .= "<select name='attrib[".$attribute->id."]' onchange='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",\"\",".$item->id.")'>";
								if($attribute->required != 1){
									$html .= "<option value='0' selected = 'selected' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list pg-".$attr_type[$attribute->type]."-none' rel = 'none'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</option>";
								}
								foreach ($attribute->options as $option) {
									$html .= "<option value='".$option->id."' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list pg-".$attr_type[$attribute->type]."-".$option->name." attr_option_". $option->id ."' attr_option='".$option->id."' rel='".$option->name."'>".$option->name."</option>";
								}
								$html .= "</select>";
							break;
							case '1': //List
								foreach ($attribute->options as $option) {
									$preValue = 0;
									$custom_style='';
									if($attribute->type==0){
										$custom_style = "style='background-color:". $option->color."'";
									}
									$required = "";
									if($attribute->required == 1){
										$required = "required='1'";
									}

									$html .= "<input class='attr_input attr_option_". $option->id ."' opt_id='". $option->id ."' type='hidden' name='attrib[".$attribute->id."][".$option->id."][selected]' value='".$preValue."' />";
									$html .= "<span ".$required." title=".$option->name." onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_attribute_option pg_".$attr_type[$attribute->type]."_option_list attr_option_". $option->id ."' attr_option='".$option->id."' ".$custom_style." >";
									if($attribute->type==0) $html .= "</span>";
									else $html .= $option->name."</span>";	
								}
							break;
							case '2':

								if($attribute->required != 1){
									$html .= "<input name='attrib[".$attribute->id."]' type='radio' title=".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')." value='0' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class='attr_radio attr_option_0' >";
									if($attribute->type==0){
										$html .= "<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class='pg_".$attr_type[$attribute->type]."_option_list pg_".$attr_type[$attribute->type]."_radio pg-".$attr_type[$attribute->type]."-none'></span>";
										$html .= "<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class = 'pg-attr-".$attr_type[$attribute->type]."-radio-none-name'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
									}else{
										$html .= "<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",0,".$item->id.");' class = 'pg_".$attr_type[$attribute->type]."_option_list pg-attr-".$attr_type[$attribute->type]."-radio-none-name'>".JText::_('PAGO_ATTRIBUTE_OPT_VALUES_NONE')."</span>";
									}
								}
								foreach ($attribute->options as $option) {
									$custom_style='';
									if($attribute->type==0){
										$custom_style = "style='background-color:". $option->color."'";
									}
									$html .= "<input name='attrib[".$attribute->id."]' type='radio' title=".$option->name." value='".$option->id."' onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_attribute_option attr_radio attr_option_". $option->id ."' >";
									if($attribute->type==0){
										$html .="<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class='pg_".$attr_type[$attribute->type]."_option_list' ".$custom_style."></span>";
										$html .="<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class = 'pg-attr-".$attr_type[$attribute->type]."-radio-name'>".$option->name."</span>";
									}else{
										$html .= "<span onClick='show_attr_option_form(".$attribute->type.",".$attribute->id.",".$attribute->display_type.",".$option->id.",".$item->id.");' class = 'pg_".$attr_type[$attribute->type]."_option_list pg-attr-".$attr_type[$attribute->type]."-radio-name'>".$option->name."</span>";
									}
								}
							break;
						}

						$html .= "</div>";
					}
					$html.="</div>";
				}
			}
		}
		return $html;
	}
	defined('_JEXEC') or die('Restricted access');
	$cid = JFactory::getApplication()->input->get('cid');
	$item_id = JFactory::getApplication()->input->get( 'Itemid' );
?>

<?php

	$lg = 12/$this->config->get('search_product_grid_settings.product_grid_large');
	$md = 12/$this->config->get('search_product_grid_settings.product_grid_medium');
	$sm = 12/$this->config->get('search_product_grid_settings.product_grid_small');
	$xs = 12/$this->config->get('search_product_grid_settings.product_grid_extra_small');

	$product_large_view = 'col-lg-'.$lg;
	$product_medium_view = 'col-md-'.$md;
	$product_small_view = 'col-sm-'.$sm;
	$product_extra_small_view = 'col-xs-'.$xs;

	$product_classes = $product_large_view.' '.$product_medium_view.' '.$product_small_view.' '.$product_extra_small_view.' ';

	$product_inner_classes = '';
	if($lg != '12'){
		$product_inner_classes .= 'col-lg-12 ';
	}
	else{
		$product_inner_classes .= 'col-lg-4 ';
	}

	if($md != '12'){
		$product_inner_classes .= 'col-md-12 ';
	}
	else{
		$product_inner_classes .= 'col-md-4 ';
	}

	if($sm != '12'){
		$product_inner_classes .= 'col-sm-12 ';
	}
	else{
		$product_inner_classes .= 'col-sm-4 ';
	}

	if($xs != '12'){
		$product_inner_classes .= 'col-xs-12 ';
	}
	else{
		$product_inner_classes .= 'col-xs-4 ';
	}
?>

<div id = "pg-category-view">
	<?php if( !empty( $this->items ) ) : ?>
		<?php $count = 1; ?>
		<div id="pg-products">
			<?php $search_product_image_settings = json_decode($this->config->get('search_product_grid_settings.category_settings_product_image_settings'));
				  $product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($search_product_image_settings->image_size);

			?>
			<?php foreach( $this->items as $product ) : ?>
				<?php
					$cat_id = $product->primary_category;
					$extraId = array('cid' => $cat_id);

					$_img_obj = PagoImageHandlerHelper::getImageById($product->file_id);
					$pin_url = PagoImageHandlerHelper::get_image_from_object( $_img_obj, $product_image_size_title, true, '' , false );
					$image = PagoImageHandlerHelper::get_image_from_object( $_img_obj, $product_image_size_title, false );
					$allMedia = PagoImageHandlerHelper::get_item_files( $product->id, true, array( 'images' ) );
					$allDownloads = PagoImageHandlerHelper::get_item_files( $product->id, true, 'download' );

					$mediaImages = false;
					if($allMedia){
						foreach ($allMedia as $media) {
							$mediaImages[] = PagoImageHandlerHelper::get_image_from_object( $media, $product_image_size_title, false );
						}
					}
				?>
				<div class = "<?php echo $product_classes; ?>">
					<div id="pg-product-<?php echo $product->id; ?>" itemId="<?php echo $product->id; ?>" class="product-cell product-container">
					<form name="addtocart" id="pg-addtocart<?php echo $count; ?>" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
						<div class = "product-block clearfix">
							<div class = "<?php echo $product_inner_classes; ?>">
								<?php if(!(($image && $this->config->get('search_product_settings.product_settings_product_image')) || ($this->config->get('search_product_settings.product_settings_media') && $allMedia))) : ?>
									<?php if ($product->show_new ||  $this->config->get('search_product_settings.product_settings_featured_badge')) : ?>
										<div class = "pg-category-product-header-margin"></div>
											<?php if ($product->show_new && PagoHelper::item_is_new( $product->id )) : ?>
												<!-- New -->
												<span class="pg-product-new">
													<?php echo JTEXT::_('PAGO_PRODUCT_NEW')?>
												</span>
											<?php endif; ?>

											<?php 
											$dateNow =  date( "Y-m-d");
											if ($this->config->get('search_product_settings.product_settings_featured_badge') && $product->featured && $product->featured_start_date <= $dateNow && $product->featured_end_date >= $dateNow) : ?>
												<div class="pg-product-featured">
													<span class = "fa fa-star"></span>
												</div>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ($this->config->get('search_product_settings.product_settings_product_title')) : ?>
								<!-- Title -->
								<div class="pg-category-product-title pg-cat-product-field">
									<?php if ($this->config->get('search_product_settings.product_settings_link_to_product')) : ?>
										<a href="<?php echo $this->nav->build_url('item', $product->id, true, $extraId) ?>">
											<?php echo $product->name;?>
										</a>
									<?php else : ?>
										<span><?php echo $product->name;?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							

							<?php if(($image && $this->config->get('search_product_settings.product_settings_product_image')) || ($this->config->get('search_product_settings.product_settings_media') && $allMedia)) : ?>
								<!-- Image -->
								<div class = "pg-category-product-image">
									<div class = "pg-category-product-image-block">
										<?php if($image && $this->config->get('search_product_settings.product_settings_product_image')) : ?>
											<?php if ($this->config->get('search_product_settings.product_settings_link_on_product_image')) : ?>
												<a href="<?php echo $this->nav->build_url('item', $product->id, true, $extraId) ?>">
													<?php echo $image; ?>
													<?php if ($this->config->get('search_product_settings.product_settings_media') && $allMedia) : ?>
														<?php
															foreach($mediaImages as $index => $mediaImage){
																if($index != 0){
																	echo $mediaImage;
																}
															}
														?>
													<?php endif; ?>
												</a>
											<?php else : ?>
												<div>
													<?php echo $image; ?>
													<?php if ($this->config->get('search_product_settings.product_settings_media') && $allMedia) : ?>
														<?php
															foreach($mediaImages as $index => $mediaImage){
																if($index != 0){
																	echo $mediaImage;
																}
															}
														?>
													<?php endif; ?>
												</div>
											<?php endif; ?>
										<?php else : ?>
											<div>
												<?php
													foreach($mediaImages as $index => $mediaImage){
														if($index != 0){
															echo $mediaImage;
														}
													}
												?>
											</div>
										<?php endif; ?>
<?php								$dateNow =  date( "Y-m-d");
										if ($this->config->get('search_product_settings.product_settings_featured_badge') && $product->featured && $product->featured_start_date <= $dateNow && $product->featured_end_date >= $dateNow) : ?>											<!-- Feature -->
											<div class="pg-product-featured">
												<span class = "fa fa-star"></span>
											</div>
										<?php endif; ?>

										<?php if ($product->show_new && PagoHelper::item_is_new( $product->id )) : ?>
										<!-- New -->
										<span class="pg-product-new">
											<?php echo JTEXT::_('PAGO_PRODUCT_NEW')?>
										</span>
									<?php endif; ?>
									</div>
								</div>
								<?php else: ?>
								<div class = "pg-category-product-image">
									<div class = "pg-category-product-image-block">
										<?php $size = PagoImageHandlerHelper::getSizeByName($product_image_size_title);
											$imgStyle = '';
											if($size->crop == 0){
												$imgStyle = '';
											}
											else{
												$imgStyle = 'style="max-width:100%;max-height:'.$size->height.'px"';
											}
											echo '<img '.$imgStyle.' src='.JURI::root() . 'components/com_pago/images/noimage.jpg>';  ?>
									
									</div>
								</div>
							<?php endif; ?>

							<?php if ($this->config->get('search_product_settings.product_settings_price') || $this->config->get('search_product_settings.product_settings_discounted_price') || $this->config->get('search_product_settings.product_settings_sku')) : ?>
								<div class = "pg-category-product-info clearfix pg-cat-product-field">
									<?php if ($this->config->get('search_product_settings.product_settings_sku')) : ?>
										<!-- SKU -->
										<div class="pg-category-product-sku">
											<span> <?php echo JText::_( 'PAGO_PRODUCT_SKU' ); ?></span>:
											<span class="category_product_sku_code"><?php echo $product->sku; ?></span>
										</div>
									<?php endif; ?>

									<?php if ($this->config->get('search_product_settings.product_settings_price') || $this->config->get('search_product_settings.product_settings_discounted_price')) : ?>
										<!-- Price -->
										<div class="pg-category-product-price">
											<?php
												$productPriceObj = Pago::get_instance('price')->getItemDisplayPrice($product);

												if ($this->config->get('search_product_settings.product_settings_price') && $this->config->get('search_product_settings.product_settings_discounted_price')){
													if ($productPriceObj->old_price)
														echo '<div class = "pg-category-product-old-price"><strike>'.Pago::get_instance('price')->format($productPriceObj->old_price).'</strike><span class = "pg-category-product-price-separator"> /&nbsp; </span></div>';
													echo '<div class = "pg-category-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
												}
												elseif($this->config->get('search_product_settings.product_settings_price') && !$this->config->get('search_product_settings.product_settings_discounted_price')){
													if ($productPriceObj->old_price){
														echo '<div class = "pg-category-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
													}
													else{
														echo '<div class = "pg-category-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
													}
												}
												elseif(!$this->config->get('search_product_settings.product_settings_price') && $this->config->get('search_product_settings.product_settings_discounted_price')){
													echo '<div class = "pg-category-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
												}
											?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ($this->config->get('search_product_settings.product_settings_category')) : ?>
								<!-- Category -->
								<div class="pg-category-product-category pg-cat-product-field">
									<span><?php echo JTEXT::_('PAGO_CATEGORY_PRODUCT_CATEGORIES'); ?></span>
									<span><?php echo $product->category_name; ?></span>
								</div>
							<?php endif; ?>


							<?php if ($this->config->get('search_product_settings.product_settings_quantity_in_stock')) : ?>
								<!-- Quantity in stock -->
								<div class="pg-category-product-stock pg-cat-product-field">
									<span><?php echo JTEXT::_('PAGO_CATEGORY_PRODUCT_STOCK'); ?></span>
									<span><?php echo $product->qty; ?></span>
								</div>
							<?php endif; ?>

							<?php if ($this->config->get('search_product_social_settings.product_settings_fb') ||
							$this->config->get('search_product_social_settings.product_settings_tw') ||
							$this->config->get('search_product_social_settings.product_settings_pinterest') ||
							$this->config->get('search_product_social_settings.product_settings_google_plus')) :?>

								<!-- Sharing -->
								<?php
									$social_icons_count = 0;

									if ($this->config->get('search_product_social_settings.product_settings_fb'))
										$social_icons_count++;

									if ($this->config->get('search_product_social_settings.product_settings_tw'))
										$social_icons_count++;

									if ($this->config->get('search_product_social_settings.product_settings_pinterest'))
										$social_icons_count++;

									if ($this->config->get('search_product_social_settings.product_settings_google_plus'))
										$social_icons_count++;
								?>
								<div class = "pg-category-product-sharing pg-cat-product-field has-border-top has-border-bottom">
									<ul class="pg-product-sharing social_icons_<?php echo $social_icons_count; ?>">
										<?php if ($this->config->get('search_product_social_settings.product_settings_fb')) : ?>
											<li class = "pg-product-sharing-facebook">
												<a target="_blank" href = "http://www.facebook.com/sharer.php?u=<?php echo $this->nav->build_url('item', $product->id, false, $extraId); ?>" class="fa fa-facebook"></a>
											</li>
										<?php endif;?>

										<?php if ($this->config->get('search_product_social_settings.product_settings_google_plus')) : ?>
											<li class = "pg-product-sharing-google-plus">
												<a target="_blank" href = "http://plus.google.com/share?url=<?php echo $this->nav->build_url('item', $product->id, false, $extraId); ?>" class="fa fa-google-plus"></a>
											</li>
										<?php endif;?>

										<?php if ($this->config->get('search_product_social_settings.product_settings_pinterest')) : ?>
											<li class = "pg-product-sharing-pinterest">
												<a target="_blank" href = "http://pinterest.com/pin/create/button/?url=<?php echo $this->nav->build_url('item', $product->id, false, $extraId); ?>&media=<?php echo $pin_url;?>&description=<?php echo $product->name; ?>" class="fa fa-pinterest"></a>
											</li>
										<?php endif;?>

										<?php if ($this->config->get('search_product_social_settings.product_settings_tw')) : ?>
											<li class = "pg-product-sharing-twitter">
												<a target="_blank" href = "http://twitter.com/intent/tweet?text=<?php echo $product->name; ?>&url=<?php echo $this->nav->build_url('item', $product->id, false, $extraId); ?>" class="fa fa-twitter"></a>
											</li>
										<?php endif;?>
									</ul>
								</div>
							<?php endif; ?>
						</div>

						<div class="<?php echo $product_inner_classes; ?>">
							<?php 
								$isMarginTop = '';
								if ($this->config->get('search_product_settings.product_settings_product_title')){
									$isMarginTop = 'has-margin-top';
								}
							?>

							<?php if ($this->config->get('search_product_settings.product_settings_rating')) :?>
								<!-- RATING -->
								<div class="pg-product-rate pg-cat-product-field has-border-top has-border-bottom <?php echo $isMarginTop; ?>" item_id="<?php echo $product->id;?>">
									<span><?php echo JTEXT::_('PAGO_RATE');?></span>
									<ul <?php echo $this->user->guest ? '':''?>><!-- class="rated" -->
										<li <?php echo $product->rating > 0 ? 'class="rated_star"':''?>><a rating="1" href = "javascript:void(0)"></a></li>
										<li <?php echo $product->rating > 1 ? 'class="rated_star"':''?>><a rating="2" href = "javascript:void(0)"></a></li>
										<li <?php echo $product->rating > 2 ? 'class="rated_star"':''?>><a rating="3" href = "javascript:void(0)"></a></li>
										<li <?php echo $product->rating > 3 ? 'class="rated_star"':''?>><a rating="4" href = "javascript:void(0)"></a></li>
										<li <?php echo $product->rating > 4 ? 'class="rated_star"':''?>><a rating="5" href = "javascript:void(0)"></a></li>
									</ul>
									<div class="pg-product-rate-result"></div>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif; ?>

							<?php if($this->config->get('search_product_settings.product_settings_short_desc')) : ?>
								<?php if($product->description) : ?>
									<?php 
										$isBorder = '';
										if($this->config->get('search_product_settings.product_settings_desc') && $product->content){
											$isBorder = 'has-border-bottom';
										}
									?>

									<!-- Short description -->
									<div class="pg-category-product-short-desc pg-cat-product-field <?php echo $isBorder.' '.$isMarginTop; ?>">
										<?php echo $product->description; ?>
									</div>
									<?php $isMarginTop = ''; ?>
								<?php endif; ?>
							<?php endif; ?>

							<?php if($this->config->get('search_product_settings.product_settings_desc') && $product->content) : ?>
								<!-- Long description -->
								<div class="pg-category-product-long-desc pg-cat-product-field <?php echo $isMarginTop; ?>">
									<?php echo $product->content; ?>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif; ?>
							
							<?php if($this->config->get('search_product_settings.product_settings_downloads') && $allDownloads) : ?>
								<!-- Downloads -->
								<div class="pg-product-downloads-block pg-cat-product-field <?php echo $isMarginTop; ?>">
									<a href = "javascript:void(0)">
										<span><?php echo JTEXT::_("PAGO_PRODUCT_DOWNLOADS"); ?></span>
										<span class = "donwload-plus-minus"></span>
									</a>
									<ul class="pg-product-downloads">
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
						<div class = "<?php echo $product_inner_classes; ?> ">
							<?php 
								$isMarginTop = '';
								if ($this->config->get('search_product_settings.product_settings_product_title')){
									$isMarginTop = 'has-margin-top';
								}
							?>

							<?php 
								$attributes = PagoAttributesHelper::get_item_attributes( $product ); 
								$varations = template_functions::get_varations( $product->id);
								echo "<input type='hidden' id='item_varations_".$product->id."' value='".$varations['jsonVarations']."'>";
								$showAttribute = false;
							?>
							<?php if($this->config->get('search_product_settings.product_settings_attribute')) : ?>
								<?php $showAttribute = true; ?>	
							<?php endif; ?>
							<?php if ($attributes) : ?>
								<!-- Attributes -->
								<div class="<?php echo $showAttribute ? "":"hiddenAttribute"; ?> pg-product-attributes pg-cat-product-field <?php echo $isMarginTop; ?>">
									<?php
										echo display_attribute( $product );
									?>
								</div>
								<?php if($showAttribute){
									$isMarginTop = '';	
								}
								?>
							<?php endif; ?>

							<?php if($this->config->get('search_product_settings.product_settings_add_to_cart')) : ?>
								<!-- ADD TO CART -->
									<?php defined('_JEXEC') or die('Restricted access'); // no direct access ?>
									<?php
										$view = JFactory::getApplication()->input->get('view');
										$Itemid = JFactory::getApplication()->input->getInt('Itemid');

										if(isset($this->item))
											$product = $this->item;
									?>
									<div class="pg-category-product-addtocart <?php echo $isMarginTop; ?>">
										<?php //check for child products (not implemented yet).
											if(1 != 2) :
												if ($product->qty!='' && $product->qty <= 0)
												{
													if ($product->availibility_options == 0)
													{
														echo "<div>" . JTEXT::_('PAGO_OUT_OF_STOCK') . "</div>";
													}
													elseif ($product->availibility_options == 1)
													{
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
													<?php if($this->config->get('search_product_settings.product_settings_add_to_cart_qty')) : ?>
														<?php
															/* QUANTITY */

															$return = base64_encode( JURI::current() );
															if( (isset( $product->qty ) && $product->qty > 0 ) ||  $product->availibility_options == 0 ) : ?>
																<div class="pg-category-product-qty-con pg-cat-product-field">
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
															<?php if ($this->config->get('search_product_settings.product_settings_price') || $this->config->get('search_product_settings.product_settings_discounted_price')) : ?>
																<!-- Price -->
																<?php 
																	$isPrice = 'price-on';																
																?>
																<div class="pg-addtocart-product-price">
																	<?php
																		if ($this->config->get('search_product_settings.product_settings_price') && $this->config->get('search_product_settings.product_settings_discounted_price')){
																			echo '<div class = "pg-addtocart-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
																		}
																		elseif($this->config->get('search_product_settings.product_settings_price') && !$this->config->get('search_product_settings.product_settings_discounted_price')){
																			echo '<div class = "pg-addtocart-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
																		}
																		elseif(!$this->config->get('search_product_settings.product_settings_price') && $this->config->get('search_product_settings.product_settings_discounted_price')){
																			echo '<div class = "pg-addtocart-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
																		}
																	?>
																</div>
															<?php endif; ?>
															<button type="submit" class="pg-button pg-addtocart pg-green-background-btn <?php echo $isPrice; ?>"><?php echo JText::_( 'PAGO_ADD_TO_CART' ); ?></button>
														</div>
													<?php else: ?>
														<button type="submit" class="pg-button pg-addtocart pg-green-background-btn pg-disabled" disabled="disabled"><?php echo JText::_( 'PAGO_ADD_TO_CART' ); ?></button>
													<?php endif; ?>
													<?php
												}
											?>
											<?php else : ?>
												<a class="pg-button" title="<?php echo JText::_( 'PAGO_VIEW_DETAILS' ); ?>" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=item&id=' . $product->id ); ?>">
													<?php echo JText::_( 'PAGO_VIEW_DETAILS' ); ?>
												</a>
											<?php endif; ?>
										<div class="pg-addtocart-success-block">
											<div class="pg-addtocart-success-text">
												<?php 
													//echo JTEXT::_('PAGO_PRODUCT_ADD_SUCCESS_ADDED').' '.$product->name.' '.JTEXT::_('PAGO_PRODUCT_ADD_SUCCESS_TO_CART'); 
												?>
											</div>
											<?php
											 	 $nav = new NavigationHelper();
												$navItemid = $Itemid = $nav->getItemid($product->id, $product->primary_category);
												
												if(empty($Itemid))
												{
												 $Itemid = JFactory::getApplication()->input->get('Itemid');
												}   
											   ?>
												   <?php 
												$config = Pago::get_instance('config')->get('global');
												$skip_cart_page = $config->get('checkout.skip_cart_page');
												
												if($skip_cart_page == 1)
												{
													$linkVar = 'checkout';
													$langVar = 'PAGO_PRODUCT_GO_TO_CHECKOUT';
												}
												else
												{
													$linkVar = 'cart';
													$langVar = 'PAGO_PRODUCT_ADD_SUCCESS_VIEW_CART';
												}
												?>
										
										<a href = "<?php echo JRoute::_('index.php?option=com_pago&view='.$linkVar.'&Itemid=' . $Itemid); ?>" class = "<?php echo $isMarginTop; ?>">
											<?php echo JTEXT::_($langVar); ?>
										</a>
											
											<div class="pg-addtocart-success-block-close"></div>
										</div>
									</div>
									<?php $isMarginTop = ''; ?>
							<?php endif; ?>
							<?php
								$preselectedVarationId = PagoAttributesHelper::get_preselected_varation( $product->id );
								if($preselectedVarationId){
									?>
								 		<script type = "text/javascript">
								 			preselectVaration(<?php echo $preselectedVarationId->id;?>,<?php echo $product->id;?>,true);
								 		</script>
									<?php
								}
							?>
							<!-- READ MORE -->
							<?php if($this->config->get('search_product_settings.product_settings_read_more')) : ?>
								<div class = "pg-category-product-read-more pg-cat-product-field">
									<a class="pg-gray-background-btn pg-no-hover" href="<?php echo $this->nav->build_url('item', $product->id, true, $extraId) ?>">
										<?php echo JTEXT::_('PAGO_CATEGORY_PRODUCT_READ_MORE'); ?>
									</a>
								</div>
							<?php endif; ?>
						</div>
					</div>
					</div>
				</div>
				<?php $count++; ?>
			</form>
			<?php endforeach;?>
			<div class="pg-pagination"><?php echo $this->pagination->getPagesLinks();  ?></div>
		</div>
		<?php endif;?>
</div>
</div>
</div>