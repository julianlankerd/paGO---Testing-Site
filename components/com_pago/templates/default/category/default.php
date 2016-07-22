<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>

<script type = "text/javascript">
	jQuery(document).ready(function(){
		jQuery('#pg-category-view .pg_attr_options[attrtype="0"][attrdisplaytype="0"] select').on('change', function(){
			var obj = jQuery(this).siblings('.chosen-container').find('.chosen-single>span');
			var obj_class = jQuery(this).find('option:selected').attr('rel');
			obj.attr('class','');
			obj.addClass('pg-color-'+obj_class);
		})
	})
</script>

<?php

$this->load_header();
Pago::load_helpers( array( 'helper','attributes' ) );

$children = $this->settingsCategory->get_children();

?>
<?php
	$doc = JFactory::$document;
	
	$doc->addScript("components/com_pago/templates/default/js/idangerous.swiper.js");

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
	$item_id = JFactory::getApplication()->input->get( 'Itemid' );
?>

<?php
	$lg = 12/$this->settingsCategory->product_grid_large;
	$md = 12/$this->settingsCategory->product_grid_medium;
	$sm = 12/$this->settingsCategory->product_grid_small;
	$xs = 12/$this->settingsCategory->product_grid_extra_small;

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

	$product_inner_classes .= 'col-xs-12 ';
?>

<?php

	$cat_image = json_decode($this->settingsCategory->category_settings_image_settings);
	
	$category_view_settings_image_settings = json_decode($this->settingsCategory->category_settings_product_image_settings);
	$category_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($category_view_settings_image_settings->image_size);
	$categoryImageMaxWidth = $this->get('config')->get('media')->image_sizes->$category_image_size_title->width; 
	$categoryImageMaxHeight = $this->get('config')->get('media')->image_sizes->$category_image_size_title->height; 

	$cat_image_settings = '#pago #pg-category-view .pg-category-wrapper{
		width: '.$categoryImageMaxWidth.'px;
		height: '.$categoryImageMaxHeight.'px;
	}';

	//$doc->addStyleDeclaration($cat_image_settings);

	$cat_product_image = json_decode($this->settingsCategory->category_settings_product_image_settings);

	$cat_product_image_settings = '#pago #pg-category-view .pg-category-product-image-block{
		padding: '.$cat_product_image->padding_top.'px '.$cat_product_image->padding_right.'px '.$cat_product_image->padding_bottom.'px '.$cat_product_image->padding_left.'px;
		margin: '.$cat_product_image->margin_top.'px '.$cat_product_image->margin_right.'px '.$cat_product_image->margin_bottom.'px '.$cat_product_image->margin_left.'px;
		border-width: '.$cat_product_image->border_top.'px '.$cat_product_image->border_right.'px '.$cat_product_image->border_bottom.'px '.$cat_product_image->border_left.'px;
	}';

	$doc->addStyleDeclaration($cat_product_image_settings);
	
	if(count($this->categories) > 1){
		$show_title = $this->menu_config->get('category_title');
		$show_count = $this->menu_config->get('category_item_count');
		$show_image = $this->menu_config->get('category_image');
		$show_desc = $this->menu_config->get('category_desc');
	} else {
		$show_title = $this->settingsCategory->category_settings_category_title;
		$show_count = $this->settingsCategory->category_settings_product_counter;
		$show_image = $this->settingsCategory->category_settings_category_image;
		$show_desc = $this->settingsCategory->category_settings_category_description;
	}
	
	$heading = '<h1>'.$this->menu_config->get('page_title').'</h1>';
	if ($this->menu_config->get('show_page_heading')){
		if (trim($this->menu_config->get('page_heading')) == ""){
			$heading = '';
		} else {
			$heading = '<h1>'.$this->menu_config->get('page_heading').'</h1>';
		}
	}
	
?>

<div class="modal fade" aria-hidden="true" role="dialog" id="login-modal">
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
<div id = "pg-category-view">
	<?php 
		if ($this->categorySlider && $this->menu_config->get('show_page_heading')){
			echo $heading;
		}
	?>
	<div class = "modal fade" aria-hidden="true" role="dialog" id="contact_info_modal">
		<div class="contact_info_title">
			<?php echo JTEXT::_('PAGO_COTNACT_INFO_TITLE'); ?>
			<a href = "javascript:void(0)" class="contact_info_modal_close"></a>
		</div>
		<div class="modal-body">
		</div>
	</div>

	<?php if( $this->menu_config->get('category_block') && $this->categorySlider) : ?>
		<div id="pg-categories">
			<div class="pg-category-info swiper-wrapper clearfix">
				<?php

					$itemId = JFactory::getApplication()->input->get('Itemid');

					foreach($this->categories as $category){
						$catLink = JRoute::_( 'index.php?option=com_pago&view=category&cid='.$category->id.'&Itemid='.$itemId);							
				?>
					<div class="swiper-slide">
						<div class="pg-category-wrapper">
							<?php if ($show_image) :
								$img_source = $category->image_url ? $category->image_url : JURI::root() . 'components/com_pago/images/category-noimage.jpg';

								echo '<div class="pg-category-image">';
								echo "<a href='".$catLink."'><img class='pg-large-img pg-main-image' src='".$img_source."'></a></div>";
							endif; 
								echo "<a href='".$catLink."' class='pg-category-title'><h3>".$category->name;
								if ($show_count) : ?>
									<span>(<?php echo $category->item_count; ?>)</span>
								<?php endif; ?>
								<?php echo '</h3></a>';?>

						</div>	
					</div>
				<?php
					}
				?>
			</div>
		</div>
		<div class="pg-categories-pagination"></div>
	<?php endif; ?>

	<?php if ((JFactory::getApplication()->input->get('cid') || $this->cid)  && !is_array($this->cid)) : ?>
		<div class="pg-cur-category-info clearfix">
			<div class="pg-cur-category-header">
				<?php if ($this->settingsCategory->category_settings_category_title) : ?>
					<div class="pg-cur-category-title">
						<h1>
							<?php echo $this->categories[0]->name; ?>
							<?php if ($this->settingsCategory->category_settings_product_counter) : ?>

								<span class = "pg-cur-category-products-count">(<?php echo $this->categories[0]->item_count; ?>)</span>
							<?php endif; ?>
						</h1>
					</div>
				<?php endif; ?>
				<?php if(!empty($children) && $this->menu_config->get('show_subcats')): ?>
					<ul class="breadcrumb">
						<li class="active"><?php echo JText::_('PAGO_SUBCATEGORIES') ?>: &nbsp;</li>
						<?php if(!empty($children)) foreach($children as $child): ?>
						<li>
							<a class="pg-button" title="<?php echo $child->name ?>" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=category&cid=' . $child->id ); ?>">
								<?php echo $child->name ?>
							</a>
						</li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
			</div>
			<?php if($this->settingsCategory->category_settings_category_image &&  $this->categories[0]->image_url || $this->settingsCategory->category_settings_category_description && $this->categories[0]->description): ?>
				<div class="pg-current-category-info clearfix">
			<?php endif; ?>
				<?php if ($this->settingsCategory->category_settings_category_image &&  $this->categories[0]->image_url ) : ?>
					<div class="pg-cur-category-image">
						<img src="<?php echo $this->categories[0]->image_url; ?>">
					</div>
				<?php endif; ?>

				<?php if ($this->settingsCategory->category_settings_category_description && $this->categories[0]->description) : ?>
					<div class="pg-cur-category-description">
						<?php echo $this->categories[0]->description; ?>
					</div>
				<?php endif; ?>	

			<?php if($this->settingsCategory->category_settings_category_image &&  $this->categories[0]->image_url || $this->settingsCategory->category_settings_category_description && $this->categories[0]->description): ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if( !empty( $this->items ) && ($this->menu_config->get('category_view'))) : ?>
		<?php $count = 1; ?>
		<div id="pg-products" class="row">
			<?php 
				$category_settings_product_image_settings = json_decode($this->settingsCategory->category_settings_product_image_settings);
				$product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($category_settings_product_image_settings->image_size);
			?>
			<?php foreach( $this->items as $product ) : ?>
			
			
				<?php
    				
					$extraId = array('cid' => $product->primary_category);

					$_img_obj = (object) array(
						'id'        		=> $product->file_id,
						'title'     		=> 'View Details for ' . html_entity_decode($product->name), //$product->file_title,
						'alias'     		=> $product->file_alias,
						'caption'   		=> $product->file_caption,
						'item_id'   		=> $product->id,
						'type'      		=> $product->file_type,
						'file_name' 		=> $product->file_file_name,
						'file_meta' 		=> $product->file_file_meta,
						'primary_category' 	=> $product->primary_category
					);
					$pin_url = PagoImageHandlerHelper::get_image_from_object( $_img_obj, $product_image_size_title, true, '' , false );
					$image = PagoImageHandlerHelper::get_image_from_object( $_img_obj, $product_image_size_title, false );
					$allMedia = PagoImageHandlerHelper::get_item_files( $product->id, true, array( 'images' ) );
					$allDownloads = PagoImageHandlerHelper::get_item_files( $product->id, true, 'download' );
					$allVarations = PagoAttributesHelper::get_all_varation( $product->id );

					$preselectVaration = false;
					$preselectedVarationId = PagoAttributesHelper::get_preselected_varation( $product->id );

					if($preselectedVarationId){
						$preselectVaration = template_functions::get_varation( $preselectedVarationId->id,$product_image_size_title );
					}

					$mediaImages = false;
					if($allMedia){
						foreach ($allMedia as $media) {
							$mediaImages[] = PagoImageHandlerHelper::get_image_from_object( $media, $product_image_size_title, false );
						}
					}

					$varationImages = false;
					if($allVarations){
						foreach ($allVarations as $varation) {
							$Fullvaration = template_functions::get_varation( $varation->id, $product_image_size_title);
							$varationImages[] = $Fullvaration['images'];
						}
					}
				?>
				<div class = "<?php echo $product_classes; ?>">
					<div id="pg-product-<?php echo $product->id; ?>" itemId="<?php echo $product->id; ?>" class="product-cell product-container view-category">
					<form name="addtocart" id="pg-addtocart<?php echo $count; ?>" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
					<div class = "product-block clearfix">
						<div class = "<?php echo $product_inner_classes; ?>">
							<?php if(!(($image && $this->settingsCategory->product_settings_product_image) || ($this->settingsCategory->product_settings_media && $allMedia))) : ?>
								<?php if ($product->show_new || $this->settingsCategory->product_settings_featured_badge) : ?>
									<div class = "pg-category-product-header-margin"></div>
									<?php if ($product->show_new && PagoHelper::item_is_new( $product->id )) : ?>
										<!-- New -->
										<span class="pg-product-new">
											<?php echo JTEXT::_('PAGO_PRODUCT_NEW')?>
										</span>
									<?php endif; ?>

									
									<?php 
									$dateNow =  date( "Y-m-d");

									if ($this->settingsCategory->product_settings_featured_badge && $product->featured && $product->featured_start_date <= $dateNow && $product->featured_end_date >= $dateNow) : ?>
										<!-- Feature -->
										<div class="pg-product-featured">
											<span class = "fa fa-star"></span>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ($this->settingsCategory->product_settings_product_title) : ?>
								<!-- Title -->
								<div class="pg-category-product-title pg-cat-product-field">
									<?php if ($this->settingsCategory->product_settings_link_to_product) : ?>
										<a href="<?php echo $this->nav->build_url('item', $product->id, true, $extraId) ?>">
											<?php echo $product->name;?>
										</a>
									<?php else : ?>
										<span><?php echo $product->name;?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if(($image && $this->settingsCategory->product_settings_product_image) || ($this->settingsCategory->product_settings_media && $allMedia)) : ?>
								<!-- Image -->
								<div class = "pg-category-product-image pg-cat-product-field">
									<div class = "pg-category-product-image-block">
										<?php $haveImage = false; ?>

										<?php if($image && $this->settingsCategory->product_settings_product_image) : ?>
											<?php $haveImage = true; ?>
										<?php endif; ?>	

										<?php if ($this->settingsCategory->product_settings_link_on_product_image) : ?>
											<a href="<?php echo $this->nav->build_url('item', $product->id, true, $extraId) ?>">
										<?php else : ?>
											<div>
										<?php endif; ?>
											
											<?php
												if($preselectVaration){
													if($preselectVaration && $preselectVaration['varation']->default ==  1){
														if($haveImage){
															echo $image;
														}

														$ExistmediaImages= false;
															
														if ($this->settingsCategory->product_settings_media && $allMedia){
															foreach($mediaImages as $index => $mediaImage){
																if($index != 0){
																	echo $mediaImage;
																	$ExistmediaImages= true;
																} 
															}
														}

														if(!$ExistmediaImages && $this->settingsCategory->product_settings_media && $allVarations){
															foreach($varationImages as $index => $mediaImage){
																if($index != 0){
																	$ExistmediaImages= true;
																	echo $mediaImage;
																}
															}
														}
													}

													else{
														if($preselectVaration['images']){
															echo $preselectVaration['images'];
															$ExistmediaImages= false;
															if ($this->settingsCategory->product_settings_media && $allMedia){
																foreach($mediaImages as $index => $mediaImage){
																	if($index != 0){
																		$ExistmediaImages= true;
																		echo $mediaImage;
																	}
																}
															}

															if(!$ExistmediaImages && $this->settingsCategory->product_settings_media && $allVarations)
															{
																foreach($varationImages as $index => $mediaImage){
																	if($index != 0){
																		$ExistmediaImages= true;
																		echo $mediaImage;
																	}
																}
															}
														}

														else{
															if($haveImage && $image){
																echo $image;
															}
															else
															{

																$size = PagoImageHandlerHelper::getSizeByName($product_image_size_title);
																$imgStyle = '';
																if($size->crop == 0){
																	$imgStyle = '';
																}
																else{
																	$imgStyle = 'style="max-width:100%;max-height:'.$size->height.'px"';
																}
																echo '<img '.$imgStyle.' src='.JURI::root() . 'components/com_pago/images/noimage.jpg>'; 
															}
														}
														
													}
												}

												else{
													if($haveImage){
														echo $image;
													}

													$ExistmediaImages= false;
													if ($this->settingsCategory->product_settings_media && $allMedia){
														foreach($mediaImages as $index => $mediaImage){
															if($index != 0){
																$ExistmediaImages= true;
																echo $mediaImage;
															}
														}
													}

													if(!$ExistmediaImages && $this->settingsCategory->product_settings_media && $allVarations){
														foreach($varationImages as $index => $mediaImage){
															if($index != 0){
																$ExistmediaImages= true;
																echo $mediaImage;
															}
														}
													}
												}
											?>
										<?php if ($this->settingsCategory->product_settings_link_on_product_image) : ?>
											</a>
										<?php else : ?>
											</div>
										<?php endif; ?>
										
										<?php 
										$dateNow =  date( "Y-m-d");
										if ($this->settingsCategory->product_settings_featured_badge && $product->featured && $product->featured_start_date <= $dateNow && $product->featured_end_date >= $dateNow) : ?>
											<!-- Feature -->
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
								<div class = "pg-category-product-image pg-cat-product-field">
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

							<?php if ($this->settingsCategory->product_settings_sku || $this->settingsCategory->product_settings_price || $this->settingsCategory->product_settings_discounted_price) : ?>
								<div class = "pg-category-product-info clearfix pg-cat-product-field">
									<?php if ($this->settingsCategory->product_settings_sku) : ?>
										<!-- SKU -->
										<div class="pg-category-product-sku">
											<span> <?php echo JText::_( 'PAGO_PRODUCT_SKU' ); ?></span>:
											<span class="category_product_sku_code"><?php echo $product->sku; ?></span>
										</div>
									<?php endif; ?>

									<?php if ($this->settingsCategory->product_settings_price || $this->settingsCategory->product_settings_discounted_price) : ?>
										<!-- Price -->
										<div class="pg-category-product-price">
											<?php
												$productPriceObj = Pago::get_instance('price')->getItemDisplayPrice($product);

												if ($this->settingsCategory->product_settings_price && $this->settingsCategory->product_settings_discounted_price){
													if ($productPriceObj->old_price)
														echo '<div class = "pg-category-product-old-price"><strike>'.Pago::get_instance('price')->format($productPriceObj->old_price).'</strike><span class = "pg-category-product-price-separator"> /&nbsp; </span></div>';
													echo '<div class = "pg-category-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
												}
												elseif($this->settingsCategory->product_settings_price && !$this->settingsCategory->product_settings_discounted_price){
													if ($productPriceObj->old_price){
														echo '<div class = "pg-category-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
													}
													else{
														echo '<div class = "pg-category-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
													}
												}
												elseif(!$this->settingsCategory->product_settings_price && $this->settingsCategory->product_settings_discounted_price){
													echo '<div class = "pg-category-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
												}
											?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ($this->settingsCategory->product_settings_category || $this->settingsCategory->product_settings_quantity_in_stock) : ?>
								<?php if ($this->settingsCategory->product_settings_category) : ?>
									<!-- Category -->
									<div class="pg-category-product-category pg-cat-product-field">
										<span><?php echo JTEXT::_('PAGO_CATEGORY_PRODUCT_CATEGORIES'); ?></span>
										<span><?php echo $product->category->name; ?></span>
									</div>
								<?php endif; ?>

								<?php if ($this->settingsCategory->product_settings_quantity_in_stock) : ?>
									<!-- Quantity in stock -->
									<div class="pg-category-product-stock pg-cat-product-field">
										<span><?php echo JTEXT::_('PAGO_CATEGORY_PRODUCT_STOCK'); ?></span>
										<span><?php 
										if($product->qty_limit=='0')
											echo $product->qty; 
										else
											echo JText::_('PAGO_UNLIMITED'); 
										?></span>
									</div>
								<?php endif; ?>
							<?php endif; ?>
    				
    						<!-- subscription /components/com_pago/templates/default/category/default.php -->
							<?php if ($product->price_type == 'subscription') : ?>
										<!-- SKU -->
								<div class="pg-category-product-subscription">
									<span> <?php echo JText::_( 'PAGO_PRODUCT_SUBSCRIPTION' ); ?></span>:
									<span class="category_product_subscription_period">
										<?php echo Pago::get_instance('price')->format($product->subscr_price) ?> each <?php echo $product->subscr_init_price; ?> <?php echo $product->sub_recur; ?></span>
								</div>
								
								<?php if ($product->subscr_start_num) : ?>
									<span> <?php echo JText::_( 'PAGO_PRODUCT_SUBSCRIPTION_TRIAL' ); ?></span>:
									<span class="category_product_subscription_trial">
										<?php echo $product->subscr_start_num ?> <?php echo $product->subscr_start_type ?> for <?php echo Pago::get_instance('price')->format($product->price) ?></span>
								<?php endif; ?>
								
							<?php endif; ?>
							
							<?php if ($this->settingsCategory->product_settings_fb ||
							$this->settingsCategory->product_settings_tw ||
							$this->settingsCategory->product_settings_pinterest ||
							$this->settingsCategory->product_settings_google_plus) :?>

								<!-- Sharing -->
								<?php
									$social_icons_count = 0;

									if ($this->settingsCategory->product_settings_fb)
										$social_icons_count++;

									if ($this->settingsCategory->product_settings_tw)
										$social_icons_count++;

									if ($this->settingsCategory->product_settings_pinterest)
										$social_icons_count++;

									if ($this->settingsCategory->product_settings_google_plus)
										$social_icons_count++;
								?>
								<div class = "pg-category-product-sharing pg-cat-product-field has-border-top has-border-bottom">
									<ul class="pg-product-sharing social_icons_<?php echo $social_icons_count; ?>">
										<?php if ($this->settingsCategory->product_settings_fb) : ?>
											<li class = "pg-product-sharing-facebook">
												<a target="_blank" href = "http://www.facebook.com/sharer.php?u=<?php echo $this->nav->build_url('item', $product->id, false, $extraId); ?>" class="fa fa-facebook"></a>
											</li>
										<?php endif;?>

										<?php if ($this->settingsCategory->product_settings_google_plus) : ?>
											<li class = "pg-product-sharing-google-plus">
												<a target="_blank" href = "http://plus.google.com/share?url=<?php echo $this->nav->build_url('item', $product->id, false, $extraId); ?>" class="fa fa-google-plus"></a>
											</li>
										<?php endif;?>

										<?php if ($this->settingsCategory->product_settings_pinterest) : ?>
											<li class = "pg-product-sharing-pinterest">
												<a target="_blank" href = "http://pinterest.com/pin/create/button/?url=<?php echo $this->nav->build_url('item', $product->id, false, $extraId); ?>&media=<?php echo $pin_url;?>&description=<?php echo $product->name; ?>" class="fa fa-pinterest"></a>
											</li>
										<?php endif;?>

										<?php if ($this->settingsCategory->product_settings_tw) : ?>
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
								if ($this->settingsCategory->product_settings_product_title){
									$isMarginTop = 'has-margin-top';
								}
							?>

							<?php if ($this->settingsCategory->product_settings_rating) :?>
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

							<?php if($this->settingsCategory->product_settings_short_desc) : ?>
								<?php if($product->description) : ?>
									<?php 
										$isBorder = '';
										if($this->settingsCategory->product_settings_desc && $product->content){
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

							<?php if($this->settingsCategory->product_settings_desc && $product->content) : ?>
								<!-- Long description -->
								<div class="pg-category-product-long-desc pg-cat-product-field <?php echo $isMarginTop; ?>">
									<?php echo $product->content; ?>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif; ?>
							
							<?php if($this->settingsCategory->product_settings_downloads && $allDownloads) : ?>
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
								if ($this->settingsCategory->product_settings_product_title){
									$isMarginTop = 'has-margin-top';
								}
							?>
							<?php
								
								$attributes = PagoAttributesHelper::get_item_attributes( $product ); 
								$varations = template_functions::get_varations( $product->id);
								echo "<input type='hidden' id='item_varations_".$product->id."' value='".$varations['jsonVarations']."'>";
								$showAttribute = false;
							?>
							<?php if($this->settingsCategory->product_settings_attribute) : ?>
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

							<?php if($this->settingsCategory->product_settings_add_to_cart) : ?>
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
											if ($product->availibility_options == 2)
											{
												//PagoHtml::thickbox();
												
													$cid = $product->primary_category;
												
												echo '<a rel="" class="pg-title-button pg-button-new pg-green-text-btn" data-toggle="modal" data-target="#contact_info_modal" onclick="pull_upload_contctInfo('.$cid.','. $product->id.',false);" href="javascript:void(0);">' . JTEXT::_('COM_PAGO_CONTACT_FOR_MORE_INFO') . '</a>';
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
													<?php if($this->settingsCategory->product_settings_add_to_cart_qty) : ?>
														<?php
															/* QUANTITY */
															$return = base64_encode( JURI::current() );
															if( (isset( $product->qty ) && $product->qty > 0 ) ||  $product->availibility_options == 0 ) : ?>
																<div class="pg-category-product-qty-con pg-product-field">
																	<label for="pg-item-opt-qty" class="pg-label"><?php echo JText::_('PAGO_ITEM_QTY'); ?></label>
																	<input onkeyup='considerPrice(0,<?php echo $product->id; ?>);' type="text" size="1" maxlength="4" class="pg-inputbox pg-item-opt-qty" name="qty" value='1' />
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
															<?php if ($this->settingsCategory->product_settings_price || $this->settingsCategory->product_settings_discounted_price) : ?>													
																<!-- PRICE -->
																<?php 
																	$isPrice = 'price-on';																
																?>
																<div class="pg-addtocart-product-price">
																	<?php
																		if ($this->settingsCategory->product_settings_price && $this->settingsCategory->product_settings_discounted_price){
																			echo '<div class = "pg-addtocart-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
																		}
																		elseif($this->settingsCategory->product_settings_price && !$this->settingsCategory->product_settings_discounted_price){
																			echo '<div class = "pg-addtocart-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
																		}
																		elseif(!$this->settingsCategory->product_settings_price && $this->settingsCategory->product_settings_discounted_price){
																			echo '<div class = "pg-addtocart-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
																		}
																	?>
																</div>
															<?php endif; ?>
															<button type="submit" class="pg-button pg-addtocart pg-green-background-btn <?php echo $isPrice; ?>" <?php echo $product->jump_to_checkout ? 'data-jump-to-checkout="'. JRoute::_( JURI::base( true ) . '/index.php?option=com_pago&view=checkout' ) .'"' : '' ?>><?php echo JText::_( 'PAGO_ADD_TO_CART' ); ?></button>
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
										
										<a href = "<?php echo JRoute::_('index.php?option=com_pago&view=' . $linkVar); ?>" class = "<?php echo $isMarginTop; ?>">
											<?php echo JTEXT::_($langVar); ?>
										</a>
										<div class="pg-addtocart-success-block-close"></div>
									</div>
								</div>
								<?php $isMarginTop = ''; ?>
							<?php endif; ?>
							<?php
								if($preselectedVarationId){
									$attributeModel = JModelLegacy::getInstance( 'Attribute', 'PagoModel' );
									$varationAttribute = $attributeModel->get_product_varation_attribute( $preselectedVarationId->id, true);
									$preSelectedAttributeID = $varationAttribute[0]->attribute->id;
									$preSelectedOptionID = $varationAttribute[0]->option->id;
									?>
								 		<script type = "text/javascript">
								 			preselectVaration(<?php echo $preselectedVarationId->id;?>,<?php echo $product->id;?>,true,<?php echo $preSelectedAttributeID;?>,<?php echo $preSelectedOptionID;?>);
								 		</script>
									<?php
								}
							?>
							<!-- READ MORE -->
							<?php if($this->settingsCategory->product_settings_read_more) : ?>
								<div class = "pg-category-product-read-more pg-cat-product-field">
									<a class="pg-gray-background-btn pg-no-hover" href="<?php echo $this->nav->build_url('item', $product->id, true, $extraId) ?>">
										<?php echo JTEXT::_('PAGO_CATEGORY_PRODUCT_READ_MORE'); ?>
									</a>
								</div>
								<?php $isMarginTop = ''; ?>
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
<script>
	jQuery(document).ready(function(){
		<?php
			echo "var lg = ".(12/$lg).";";
			echo "var md = ".(12/$md).";";
			echo "var sm = ".(12/$sm).";";
			echo "var xs = 1;";
		?>
		if (jQuery(window).width() >= 1200){
			if (lg == 1){
				sliderViewCount = 2;
			}
			else{
				sliderViewCount = lg;
			}
		}
		else if (jQuery(window).width() >= 992 && jQuery(window).width() <= 1199){
			if (md == 1){
				sliderViewCount = 2;	
			}
			else{
				sliderViewCount = md;
			}
		}
		else if (jQuery(window).width() >= 768 && jQuery(window).width() <= 991){
			sliderViewCount = sm;
		}
		else if (jQuery(window).width() <= 767){
			sliderViewCount = xs;
		}
		var catSwiper = new Swiper('#pg-categories',{
		    slidesPerView: sliderViewCount,
		    pagination: '.pg-categories-pagination',
		    paginationClickable: true,
		    keyboardControl: true,
		    simulateTouch: false,
		    resizeReInit: true,
		    calculateHeight: true,
		    loop: false,
		}); 

		jQuery(window).resize(function(){
			if (jQuery(window).width() >= 1200){
				if (lg == 1){
					sliderViewCount = 2;
				}
				else{
					sliderViewCount = lg;
				}
			}
			else if (jQuery(window).width() >= 992 && jQuery(window).width() <= 1199){
				if (md == 1){
					sliderViewCount = 2;	
				}
				else{
					sliderViewCount = md;
				}
			}
			else if (jQuery(window).width() >= 768 && jQuery(window).width() <= 991){
				sliderViewCount = sm;
			}
			else if (jQuery(window).width() <= 767){
				sliderViewCount = xs;
			}
			catSwiper.params.slidesPerView = sliderViewCount;
			catSwiper.reInit();
		})
	})
</script>