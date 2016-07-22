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
$doc->addCustomTag('<meta property="og:title" content="'.$this->item->name.'" />');
$doc->addCustomTag('<meta property="og:type" content="product" />');
if ( isset( $this->images[0]->id) ) {
	$main_image_url = PagoImageHandlerHelper::get_image_from_object( $this->images[0], 'large', true,'',false );
	$doc->addCustomTag('<meta property="og:image" content="'.$main_image_url.'" />');
}
$doc->addCustomTag('<meta property="og:url" content="'.JFactory::getURI()->toString().'" />');
// Displays the full item detail view.
$this->load_header();
Pago::load_helpers( array( 'helper','attributes' ) );

$cat_id = JFactory::getApplication()->input->get('cid');
$extraId = array('cid' => $cat_id);

/* ******************************************************************* */
/* ******************************************************************* */
/* ******************************************************************* */


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

				$html .= "<div class='pg-attribute-product-container pg-product-field ".$isBorder." clearfix' type='".$attr_type[$attribute->type]."'>";
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
	$cid = JFactory::getApplication()->input->get( 'cid' );
	$item_id = JFactory::getApplication()->input->get( 'Itemid' );

	/* ******************************************************************* */
	/* ******************************************************************* */
	/* ******************************************************************* */
?>

<?php
PagoHtml::add_css( $_root . '/components/com_pago/templates/default/css/tango/skin.css' );
PagoHtml::add_js( $_root . '/components/com_pago/templates/default/js/jquery.jcarousel.js' );

$preselectedVarationId = PagoAttributesHelper::get_preselected_varation( $this->item->id );
//
?>
<script>
jQuery(document).ready(function(){
	considerPrice(0,<?php echo $this->item->id; ?>);
	getComments(<?php echo $this->item->id; ?>);
});
</script>

<?php // Revist price (possible snippet?)
	$productPriceObj = Pago::get_instance( 'price' )->getItemDisplayPrice($this->item);
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

<?php
if ( $this->item->availibility_options == 2){ ?>
<div class = "modal fade" aria-hidden="true" role="dialog" id="contact_info_modal">
	<div class="contact_info_title">
		<?php echo JTEXT::_('PAGO_COTNACT_INFO_TITLE'); ?>
		<a href = "javascript:void(0)" class="contact_info_modal_close"></a>
	</div>
	<div class="modal-body">
	</div>
</div>
<?php } ?>
<div class="modal fade" aria-hidden="true" role="dialog" id="guest-submision">
	<div class="modal-dialog">
    	<div class="modal-content">
    		<div class="modal-header">
        		<button type="button" class="close" style="margin: 0 22px 0 0" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		<h4 class="modal-title" id="myModalLabel"><?php echo JTEXT::_('PAGO_GUEST_SUBMITION_MODAL_TITLE'); ?></h4>
      		</div>
      		<div class="modal-body">
				<?php require PagoHelper::load_template( 'item', 'guest' );  ?>
			</div>
		</div>
	</div>
</div>
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
<div id = "pg-product-view">
	<div id="pg-product-<?php echo $this->item->id; ?>" selectedVaration="0" itemId="<?php echo $this->item->id; ?>" class="pg-product-id-<?php echo $this->item->id; ?> clearfix product-container view-item">
		<div id="pg-product-synopsis" class="clearfix">
			<div class = "row">		
				<div class = "col-sm-8">	
					<div class = "pg-item-view-left-container">	
						<?php if (count($this->images) > 1 && ($this->viewSettings->product_view_settings_media || $this->viewSettings->product_view_settings_product_image) ||
						(count($this->images) == 1 && $this->viewSettings->product_view_settings_product_image)) : ?>
							<div id="pg-product-images">
								<?php
								$dateNow =  date( "Y-m-d");
								 if ($this->item->featured && $this->item->featured_start_date <= $dateNow && $this->item->featured_end_date >= $dateNow) : ?>
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
										<div class="pg-item-images-con">
											<?php
												$product_view_settings_image_settings = json_decode($this->viewSettings->product_view_settings_image_settings);
												$product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($product_view_settings_image_settings->image_size);
												
												$productImageMaxWidth = $this->get('config')->get('media')->image_sizes->$product_image_size_title->width; 
												$productImageMaxHeight = $this->get('config')->get('media')->image_sizes->$product_image_size_title->height; 
												
												if($this->images[0]->type == "images"){
													
													if ($this->viewSettings->product_view_settings_product_image){
										        		$main_image_url_rel = PagoImageHandlerHelper::get_image_from_object( $this->images[0], $product_image_size_title, true );
										        		echo "<img title='{$this->images[0]->title}' alt='{$this->images[0]->title}' class='pg-main-image' src='".$main_image_url_rel."' id='pg-imageid-".$this->images[0]->id."' >";
										        	}
										        	else{
										        		$main_image_url_rel = PagoImageHandlerHelper::get_image_from_object( $this->images[1], $product_image_size_title, true );
										        		echo "<img class='pg-main-image' src='".$main_image_url_rel."' id='pg-imageid-".$this->images[1]->id."' >";
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
										                <li style="list-style: none;" class = "swiper-slide">
										                	<img <?php echo $this->images[$i]->type == 'video' ? 'imageType="video" videoId="'.$this->images[$i]->id.'"' : 'imageType="images"';?>  fullurl="<?php echo $image; ?>" class="changeAttributeSelect" type='item' itemId="<?php echo $this->item->id; ?>" title="<?php echo $this->images[$i]->title; ?>" alt="<?php echo $this->images[$i]->title; ?>" src="<?php echo $imageThumb; ?>" >
										                </li>
										    			<?php
										            }
													if( empty( $product_image_size_title ) ) {
														$product_image_size_title = '-1';
													}
												 	$varations = template_functions::get_varations( $this->item->id,$product_image_size_title );

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
						<!-- no item image but have varation image START // ADD ANI -->
						<?php 
						$product_view_settings_image_settings = json_decode($this->viewSettings->product_view_settings_image_settings);
						$product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($product_view_settings_image_settings->image_size);
					

												$productImageMaxWidth = $this->get('config')->get('media')->image_sizes->$product_image_size_title->width; 
												$productImageMaxHeight = $this->get('config')->get('media')->image_sizes->$product_image_size_title->height; 
												
						$varations = template_functions::get_varations( $this->item->id,$product_image_size_title );

						if( count($this->images) < 1  && $varations['images']): 
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
							<div id="pg-product-images">
								
								<div class = "pg-gallery-con-container">
									<div class = "pg-gallery-con">
										<div class="pg-item-images-con">
											<img class='' src='' id="var_main_image" >
											<input type="hidden" name="pg-image-size" id="pg-image-size" value="<?php echo $product_image_size_title; ?>"/>
											
										</div>
									</div>
								</div>
							
								<div id="pg-item-images-add-con-main" class="pg-item-images-add-con-main" <?php echo $display_media ?>>
									<div id="pg-item-images-add-con" class = "pg-thumbnail-swiper-container">
											<ul class="pg-image-thumbnails swiper-wrapper">
												<?php
												
													if ($this->viewSettings->product_view_settings_product_image){
														$start = 0;
													}
													else{
														$start = 1;
													}
										          
													if( empty( $product_image_size_title ) ) {
														$product_image_size_title = '-1';
													}
												 	
												 		echo $varations['images'];
												
												 ?>
											</ul>
									</div>
									<a href = "javascript:void(0)" class = "thumbnail-swiper-prev"></a>
									<a href = "javascript:void(0)" class = "thumbnail-swiper-next"></a>
								</div>
							</div>
							<div class = "pg-gallery-con-container">
									<div class = "pg-gallery-con">
										<div class="pg-item-images-con">
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
							</div>	
						<?php endif; ?>
						<?php if( count($this->images) < 1  && !$varations['images']): ?>
						<div class = "pg-gallery-con-container">
									<div class = "pg-gallery-con">
										<div class="pg-item-images-con">
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
							</div>	
						<?php endif; ?>
						<!-- no item image but have varation image END // ADD ANI -->
						
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
						

						<?php
							$class = '';
							$is_social = '';
							$is_rate = '';

							if ($this->viewSettings->product_view_settings_fb ||
							$this->viewSettings->product_view_settings_tw ||
							$this->viewSettings->product_view_settings_pinterest ||
							$this->viewSettings->product_view_settings_google_plus) {
								$is_social = '1';
								$class .= '-share';
							}
							if ($this->viewSettings->product_view_settings_rating){
								$is_rate = '1';
								$class .= '-rate';
							}
						?>

						<?php if ($is_social || $is_rate) :?>
							<?php $social_icons_count = 0; ?>
							<div class = "pg-product<?php echo $class;?>-block clearfix">
								<div class = "row clearfix">
									<?php if ($is_social) :?>
										<!-- SHARING -->
										<?php
											if ($this->viewSettings->product_view_settings_fb)
												$social_icons_count++;

											if ($this->viewSettings->product_view_settings_tw)
												$social_icons_count++;

											if ($this->viewSettings->product_view_settings_pinterest)
												$social_icons_count++;

											if ($this->viewSettings->product_view_settings_google_plus)
												$social_icons_count++;
										?>
										<?php
											$product_rating_size = '';

											if ($is_rate){
												$product_rating_size = "col-lg-6";
											}
											else{
												$product_rating_size = "col-lg-12";
											}
											
											$facebook_share_url = rawurlencode($this->nav->build_url('item', $this->item->id, false, $extraId));
											$facebook_share_url = 'http://www.facebook.com/sharer.php?u=' . $facebook_share_url;
										?>
										
										<div class = "<?php echo $product_rating_size; ?>">
											<ul class="pg-product-sharing social_icons_<?php echo $social_icons_count; ?>">
												<?php if ($this->viewSettings->product_view_settings_fb) : ?>
													<li class = "pg-product-sharing-facebook">
														<a target="_blank" href = "<?php echo $facebook_share_url; ?>" class="fa fa-facebook"></a>
													</li>
												<?php endif;?>

												<?php if ($this->viewSettings->product_view_settings_google_plus) : ?>
													<li class = "pg-product-sharing-google-plus">
														<a target="_blank" href = "http://plus.google.com/share?url=<?php echo $this->nav->build_url('item', $this->item->id, false, $extraId); ?>" class="fa fa-google-plus"></a>
													</li>
												<?php endif;?>

												<?php if ($this->viewSettings->product_view_settings_pinterest) : ?>
													<?php if( isset( $this->images[0] ) ) :	?>
														<?php 
															$product_view_settings_image_settings = json_decode($this->viewSettings->product_view_settings_image_settings);
															$product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($product_view_settings_image_settings->image_size);
														 ?>
													 <?php endif;?>

													<li class = "pg-product-sharing-pinterest">
														<a target="_blank" href = "http://pinterest.com/pin/create/button/?url=<?php echo $this->nav->build_url('item', $this->item->id, false, $extraId); ?>&media=<?php echo @$main_image_url; ?>&description=<?php echo $this->item->name; ?>" class="fa fa-pinterest"></a>
													</li>
												<?php endif;?>

												<?php if ($this->viewSettings->product_view_settings_tw) : ?>
													<li class = "pg-product-sharing-twitter">
														<a target="_blank" href = "http://twitter.com/intent/tweet?text=<?php echo $this->item->name; ?>&url=<?php echo $this->nav->build_url('item', $this->item->id, false, $extraId); ?>" class="fa fa-twitter"></a>
													</li>
												<?php endif;?>
											</ul>
										</div>
									<?php endif; ?>

									<?php if ($is_rate) :?>
										<!-- RATING -->
										<?php
											$product_sharing_size = '';
											if ($social_icons_count > 0 ){
												$product_sharing_size = "col-lg-6";
											}
											else{
												$product_sharing_size = "col-lg-12";
											}
										?>
										<div class = "<?php echo $product_sharing_size; ?>">
											<div class="pg-product-rate" item_id="<?php echo $this->item->id;?>">
												<span><?php echo JTEXT::_('PAGO_RATE');?></span>
												<ul <?php echo $this->user->guest ? '':''?>><!-- class="rated" -->
													<li <?php echo $this->item->rating > 0 ? 'class="rated_star"':''?>><a rating="1" href = "javascript: void(0)"></a></li>
													<li <?php echo $this->item->rating > 1 ? 'class="rated_star"':''?>><a rating="2" href = "javascript: void(0)"></a></li>
													<li <?php echo $this->item->rating > 2 ? 'class="rated_star"':''?>><a rating="3" href = "javascript: void(0)"></a></li>
													<li <?php echo $this->item->rating > 3 ? 'class="rated_star"':''?>><a rating="4" href = "javascript: void(0)"></a></li>
													<li <?php echo $this->item->rating > 4 ? 'class="rated_star"':''?>><a rating="5" href = "javascript: void(0)"></a></li>
												</ul>
												<div class="pg-product-rate-result"></div>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php 
						$config = Pago::get_instance( 'config' )->get('global');
						$show_comments = $config->get('comments.show_comments');
						$guest_comment = $config->get('comments.comment_guest_submition');
						//$replay_comment = $config->get('comments.comment_replay');

						if($show_comments == 1){ ?>
							<div class="pg-comments-container">
								<div class="pg-comments-con">
									<!-- show comment by ajax -->
								</div>
								<div class="clearfix"></div>
								<?php if($guest_comment == 1){ ?>
									<div class="pg-add-comment-container">
										<?php 
											$commentName = $this->user->guest ? 'guest' : 'member';
										?>
										<?php $avatar = PagoHelper::getAvatar(); ?>
										<form name="addComment" id="pg-addComment" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
											<span class="pg_user_image pg-comment-author-image">
												<img src="<?php echo $avatar['avatarPath']; ?>">
											</span>
											<textarea rows="1" name="comment_message" placeholder = "<?php echo JTEXT::_('PAGO_COMMENT_WRITE_MESSAGE')?>"></textarea><br/>
											<input type="hidden"  name="comment_parentId" value="0" >

												
											<input type="hidden" name="comment_name" value='<?php echo $this->user->guest ? '':$this->user->name?>'>	
											<input type="hidden" name="comment_email" value='<?php echo $this->user->guest ? '':$this->user->email?>'>	
											<input type="hidden" name="comment_web_site">
											
											<input type="button" class="addCommentBtn pg-green-text-btn <?php echo $commentName;?>" name="addCommentBtn" value="<?php echo JTEXT::_('PAGO_COMMENT_POST_REVIEW');?>" >
										</form>
									</div>
									<div class="clearfix"></div>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
					<?php if( !empty( $this->item->content ) &&  $this->viewSettings->product_view_settings_desc) : ?>
								<!-- Long description -->
								<div class="pg-product-long-desc pg-product-field">
									<?php echo html_entity_decode($this->item->content); ?>
								</div>
							<?php endif; ?>
					<?php if($config->get('comments.comment_moderation')==1):?>
						<div class="pg-notification-message pg-notification"></div>
					<?php endif;?>
				</div>

				<div class = "col-sm-4">
					<form name="addtocart" id="pg-addtocart" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
						<div class = "pg-item-view-right-container">	
							<?php if ($this->viewSettings->product_view_settings_price || $this->viewSettings->product_view_settings_discounted_price) : ?>
								<!-- PRICE -->
								<div class="pg-product-price pg-product-field">
									<?php
										if ($this->viewSettings->product_view_settings_price && $this->viewSettings->product_view_settings_discounted_price){
											if ($productPriceObj->old_price && $productPriceObj->old_price > 0)
											{
												echo '<div class = "pg-product-old-price"><strike>'.Pago::get_instance('price')->format($productPriceObj->old_price).'</strike><span class = "pg-product-price-separator"> /&nbsp; </span></div>';
											echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
											}
											else
											{
												echo '<div class = "pg-product-real-price">'.JText::_("PAGO_ITEM_IS_FREE").'</div>';
											}
										}
										elseif($this->viewSettings->product_view_settings_price && !$this->viewSettings->product_view_settings_discounted_price){
											if ($productPriceObj->old_price){
												if($productPriceObj->old_price > 0)
												{
													echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
												}
												else
												{
													echo '<div class = "pg-product-real-price">'.JText::_("PAGO_ITEM_IS_FREE").'</div>';
												}
											}
											else{
												if($productPriceObj->item_price_including_tax > 0)
												{
													echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
												}
												else
												{
													echo '<div class = "pg-product-real-price">'.JText::_("PAGO_ITEM_IS_FREE").'</div>';
												}
												
											}
										}
										elseif(!$this->viewSettings->product_view_settings_price && $this->viewSettings->product_view_settings_discounted_price){
											if($productPriceObj->item_price_including_tax > 0)
												{
													echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
												}
												else
												{
													echo '<div class = "pg-product-real-price">'.JText::_("PAGO_ITEM_IS_FREE").'</div>';
												}
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
							
							<!-- subscription /components/com_pago/templates/default/category/default.php -->
							<?php if ($this->item->price_type == 'subscription') : ?>
										<!-- SKU -->
								<div class="pg-category-product-subscription">
									<span> <?php echo JText::_( 'PAGO_PRODUCT_SUBSCRIPTION' ); ?></span>:
									<span class="category_product_subscription_period">
										<?php echo Pago::get_instance('price')->format($this->item->subscr_price) ?> each <?php echo $this->item->subscr_init_price; ?> <?php echo $this->item->sub_recur; ?></span>
								</div>
								
								<?php if ($this->item->subscr_start_num) : ?>
									<span> <?php echo JText::_( 'PAGO_PRODUCT_SUBSCRIPTION_TRIAL' ); ?></span>:
									<span class="category_product_subscription_trial">
										<?php echo $this->item->subscr_start_num ?> <?php echo $this->item->subscr_start_type ?> for <?php echo Pago::get_instance('price')->format($this->item->price) ?></span>
								<?php endif; ?>
								
							<?php endif; ?>
							
							<?php		
								$product_view_settings_image_settings = json_decode($this->viewSettings->product_view_settings_image_settings);
								$product_image_size_title = Pago::get_instance( 'config' )->getSizeByNumber($product_view_settings_image_settings->image_size);
								$varations = template_functions::get_varations( $this->item->id,$product_image_size_title );
								$showAttribute = false;
							?>
							<?php if($this->viewSettings->product_view_settings_attribute) : ?>
								<?php $showAttribute = true; ?>	
							<?php endif; ?>
							<!-- Attributes -->
							<div class="<?php echo $showAttribute ? "":"hiddenAttribute"; ?> pg-product-attributes has-border-top pg-product-field">
								<?php
									echo display_attribute( $this->item ); 
								?>
							</div>
							<?php echo "<input type='hidden' id='item_varations_".$this->item->id."' value='".$varations['jsonVarations']."'>"; ?>
							<!-- ********************* -->
							<!-- QUANTITY OUT OF STOCK -->
							<!-- ********************* -->

							<?php if($this->viewSettings->product_view_settings_downloads) : ?>
								<!-- Downloads -->
								
								<?php 
								
									$downloadsActive = false;
									
									foreach ($allDownloads as $productDownloads){
										if($productDownloads->access != 2) continue;
										
										$downloadsActive = true;
									} 
								?>
								
								<?php if ($allDownloads && $downloadsActive) : ?>
									<div class="pg-product-downloads-block pg-product-field">
										<a href = "javascript:void(0)">
											<span><?php echo JTEXT::_("PAGO_PRODUCT_DOWNLOADS"); ?></span>
											<span class="donwload-plus-minus"></span>
										</a>
										<ul class="pg-product-downloads">
											<?php foreach ($allDownloads as $productDownloads) {
											
											$link = 'index.php?option=com_pago&controller=item&task=downloadFiles&fileid=' . $productDownloads->id;
											 if($productDownloads->access == 2): ?>
													<li>
														<a class = "download-text" href="<?php echo $link; ?>"><?php echo $productDownloads->title; ?></a>
														<a class = "download-ico" href="<?php echo $link; ?>"></a>
													</li>
												<?php endif ?>
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
												//PagoHtml::thickbox();
												$cid = JFactory::getApplication()->input->get('cid', array(0), 'array');
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
													<?php //if($this->viewSettings->product_view_settings_add_to_cart_qty) : ?>
														<?php
															/* QUANTITY */
															$return = base64_encode( JURI::current() );
															if( (isset( $product->qty ) && $product->qty > 0 ) ||  $product->availibility_options == 0 ) : ?>
																<div class="pg-product-qty-con pg-product-field">
																	<?php if($this->viewSettings->product_view_settings_add_to_cart_qty) : ?>
																	<label for="pg-item-opt-qty" class="pg-label"><?php echo JText::_('PAGO_ITEM_QTY'); ?></label>
																	<?php endif; ?>
																	<?php if($this->viewSettings->product_view_settings_add_to_cart_qty) : ?>
																	<input onkeyup='considerPrice(0,<?php echo $product->id; ?>);' type="text" size="1" class="pg-inputbox pg-item-opt-qty" name="qty" value='1' />
																	<?php else: ?>
																	<input onkeyup='considerPrice(0,<?php echo $product->id; ?>);' type="hidden" size="1" class="pg-inputbox pg-item-opt-qty" name="qty" value='1' />
																	<?php endif; ?>
																</div>
															<?php endif;
														?>
													<?php //endif; ?>

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
																			if($productPriceObj->item_price_including_tax > 0)
																			{
																				echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
																			}
																			else
																			{
																				echo '<div class = "pg-product-real-price">'.JText::_("PAGO_ITEM_IS_FREE").'</div>';
																			}
																			
																		}
																		elseif($this->viewSettings->product_view_settings_price && !$this->viewSettings->product_view_settings_discounted_price){
																			if($productPriceObj->old_price > 0)
																			{
																				echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->old_price).'</div>';
																			}
																			else
																			{
																				echo '<div class = "pg-product-real-price">'.JText::_("PAGO_ITEM_IS_FREE").'</div>';
																			}
																		}
																		elseif(!$this->viewSettings->product_view_settings_price && $this->viewSettings->product_view_settings_discounted_price){
																			if($productPriceObj->item_price_including_tax > 0)
																			{
																				echo '<div class = "pg-product-real-price">'.Pago::get_instance('price')->format($productPriceObj->item_price_including_tax).'</div>';
																			}
																			else
																			{
																				echo '<div class = "pg-product-real-price">'.JText::_("PAGO_ITEM_IS_FREE").'</div>';
																			}
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
							<?php endif; ?>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php if ($this->relatedProducts) : ?>
		<span class = "pg-related-products-title"><?php echo JTEXT::_('PAGO_RELATED_PRODUCTS_TITLE')?></span>
		<div class = "pg-related-products">
			<div class = "pg-related-products-swiper-cantainer">
			<div class = "swiper-wrapper">
				<?php foreach ($this->relatedProducts as $relatedProduct) : ?>
					<?php
						$relatedProductPriceObj = Pago::get_instance( 'price' )->getItemDisplayPrice($relatedProduct);
						$_img_obj = (object) array(
							'id'        		=> $relatedProduct->file_id,
							'title'     		=> 'View Details for ' . html_entity_decode($relatedProduct->name), //$product->file_title,
							'alias'     		=> $relatedProduct->file_alias,
							'caption'   		=> $relatedProduct->file_caption,
							'item_id'   		=> $relatedProduct->id,
							'type'      		=> $relatedProduct->file_type,
							'file_name' 		=> $relatedProduct->file_file_name,
							'file_meta' 		=> $relatedProduct->file_file_meta,
							'primary_category' 	=> $relatedProduct->primary_category
						);

						$image = PagoImageHandlerHelper::get_image_from_object( $_img_obj, $product_image_size_title, false );
						$extraId = array('cid' => $relatedProduct->primary_category);
					?>
					<div class = "swiper-slide">
						<div class="pg-related-product">
							<div class = "pg-related-product-image"><?php echo $image; ?></div>
							<a href="<?php echo $this->nav->build_url('item', $relatedProduct->id, true, $extraId) ?>" class = "pg-related-product-overlay">
								<div class = "pg-related-product-info">
									<div class = "pg-related-product-name"><?php echo $relatedProduct->name; ?></div>
									<div class = "pg-related-product-price"><?php echo Pago::get_instance('price')->format($relatedProductPriceObj->item_price_including_tax); ?></div>
								</div>
							</a>
						</div>	
					</div>		
				<?php endforeach; ?>
			</div>
			</div>
			
			<a href = "javascript:void(0)" class = "pg-related-product-slider-prev"></a>
			<a href = "javascript:void(0)" class = "pg-related-product-slider-next"></a>
		</div>
	<?php endif;?>
</div>

<?php $this->load_footer();
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
<?php 

if (count($this->images) > 1 && ($this->viewSettings->product_view_settings_media || $this->viewSettings->product_view_settings_product_image) ||
			(count($this->images) == 1 && $this->viewSettings->product_view_settings_product_image)
			|| (count($this->images) < 1  &&  $varations['images']))  : ?>
<script type = "text/javascript">
	var thumbnailSwiper;
	var thumbnailImageWidth = jQuery('#pg-item-images-add-con li img').width()+10;
	var thumbnailImageHeight = jQuery('#pg-item-images-add-con li img').height();

	var thumbnailBottom = parseInt(jQuery('#pago #pg-product-view #pg-item-images-add-con-main').css('bottom'));
	
	var imageCount = jQuery('#pg-item-images-add-con li').length;
	if(imageCount==1){
		jQuery('#pg-item-images-add-con').hide();
	}
	var timeOut = '';

	function initThumbnailSlider(){
		if (jQuery('.pg-thumbnail-swiper-container').length){
			var thumbnailContainerWidth = jQuery('#pg-item-images-add-con').width();
			var thumbnailSlidesCount = Math.floor(thumbnailContainerWidth/thumbnailImageWidth);

			if (imageCount * thumbnailImageWidth > thumbnailContainerWidth){
				jQuery('#pg-product-images .thumbnail-swiper-prev').css('display', 'block');
				jQuery('#pg-product-images .thumbnail-swiper-next').css('display', 'block');
			}
			else{
				jQuery('#pg-product-images .thumbnail-swiper-prev').css('display', 'none');
				jQuery('#pg-product-images .thumbnail-swiper-next').css('display', 'none');	
			}

			thumbnailSwiper = new Swiper('#pg-product-images .pg-thumbnail-swiper-container',{
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
		});
		

		jQuery(document).on('click', '#pg-product-images .thumbnail-swiper-prev', function(e){
			e.preventDefault();
			
			thumbnailSwiper.swipePrev();
			jQuery('#pg-product-images .thumbnail-swiper-prev').css('display', 'block');
			jQuery('#pg-product-images .thumbnail-swiper-next').css('display', 'block');
			if(jQuery("#pg-product-images .swiper-wrapper li").first().hasClass("swiper-slide-visible")){
				
				jQuery('#pg-product-images .thumbnail-swiper-prev').css('display', 'none');
			}
			if(jQuery("#pg-product-images .swiper-wrapper li").last().hasClass("swiper-slide-visible")){
				
				jQuery('#pg-product-images .thumbnail-swiper-next').css('display', 'none');
			}

		})

		jQuery(document).on('click', '#pg-product-images .thumbnail-swiper-next', function(e){
			e.preventDefault();
			thumbnailSwiper.swipeNext();
			jQuery('#pg-product-images .thumbnail-swiper-prev').css('display', 'block');
				jQuery('#pg-product-images .thumbnail-swiper-next').css('display', 'block');
			if(jQuery("#pg-product-images .swiper-wrapper li").first().hasClass("swiper-slide-visible")){
				
				jQuery('#pg-product-images .thumbnail-swiper-prev').css('display', 'none');
			}
			if(jQuery("#pg-product-images .swiper-wrapper li").last().hasClass("swiper-slide-visible")){
				
				jQuery('#pg-product-images .thumbnail-swiper-next').css('display', 'none');
			}
		})

	})

	jQuery(window).load(function(){

	initThumbnailSlider();
		if(jQuery("#pg-product-images .swiper-wrapper li").first().hasClass("swiper-slide-visible")){
			jQuery('#pg-product-images .thumbnail-swiper-prev').css('display', 'none');
			
		}
		if(jQuery("#pg-product-images .swiper-wrapper li").last().hasClass("swiper-slide-visible")){
			
			jQuery('#pg-product-images .thumbnail-swiper-next').css('display', 'none');
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
<script>

jQuery(window).load(function(){
		if (jQuery('.pg-related-products').length){

			var relatedContainerWidth = jQuery(".pg-related-products-swiper-cantainer").width();
			var relatedImageCount = jQuery('.pg-related-products-swiper-cantainer .swiper-slide').length ;
			var relatedImageWidth = jQuery('.pg-related-products-swiper-cantainer .swiper-slide').width();
	
			if (relatedImageCount * relatedImageWidth > relatedContainerWidth){
				jQuery('.pg-related-product-slider-prev').css('display', 'block');
				jQuery('.pg-related-product-slider-next').css('display', 'block');
			}
			else{
				jQuery('.pg-related-product-slider-prev').css('display', 'none');
				jQuery('.pg-related-product-slider-next').css('display', 'none');	
			}




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

			if(jQuery(".swiper-wrapper .swiper-slide").first().hasClass("swiper-slide-visible")){
				
				jQuery('.pg-related-product-slider-prev').css('display', 'none');

			}
			if(jQuery(".swiper-wrapper .swiper-slide").last().hasClass("swiper-slide-visible")){
				
				jQuery('.pg-related-product-slider-next').css('display', 'none');
			}

			jQuery(document).on('click', '.pg-related-product-slider-prev', function(e){
				e.preventDefault();
				mySwiper.swipePrev();
			
				jQuery('.pg-related-product-slider-prev').css('display', 'block');
				jQuery('.pg-related-product-slider-next').css('display', 'block');
				if(jQuery(".pg-related-products-swiper-cantainer .swiper-wrapper .swiper-slide").first().hasClass("swiper-slide-visible")){
					
					jQuery('.pg-related-product-slider-prev').css('display', 'none');

				}
				if(jQuery(".pg-related-products-swiper-cantainer .swiper-wrapper .swiper-slide").last().hasClass("swiper-slide-visible")){
					
					jQuery('.pg-related-product-slider-next').css('display', 'none');
				}
			})

			jQuery(document).on('click', '.pg-related-product-slider-next', function(e){
				e.preventDefault();
				mySwiper.swipeNext();
				
				jQuery('.pg-related-product-slider-prev').css('display', 'block');
				jQuery('.pg-related-product-slider-next').css('display', 'block');
				if(jQuery(".pg-related-products-swiper-cantainer .swiper-wrapper .swiper-slide").first().hasClass("swiper-slide-visible")){
					
					jQuery('.pg-related-product-slider-prev').css('display', 'none');
				}
				if(jQuery(".pg-related-products-swiper-cantainer .swiper-wrapper .swiper-slide").last().hasClass("swiper-slide-visible")){
			
					jQuery('.pg-related-product-slider-next').css('display', 'none');
				}
			})
		}
	})
	
</script>

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