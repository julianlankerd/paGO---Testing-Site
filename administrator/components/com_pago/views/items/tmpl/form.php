<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery( 'jqueryui' );
PagoHtml::apply_layout_fixes();
PagoHtml::loadBootstrapModalForBackend();


JText::script('PAGO_ATTRIBUTE_OPTION_HAVE_VARIATION');
JText::script('PAGO_ATTRIBUTE_IS_REQUIRED');
JText::script('PAGO_ATTRIBUTE_OPTION_DELETE');
JText::script('PAGO_ATTRIBUTE_CUSTOM_DELETE');
JText::script('PAGO_ATTRIBUTE_HAVE_VARIATION');
JText::script('PAGO_ATTRIBUTE_CANT_BE_REQUIRED');
JText::script('PAGO_ATTRIBUTE_CHOOSE_REQUIRED');
//Joomla.JText.strings.PAGO_ATTRIBUTE_CHOOSE_REQUIRED

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::loadUploadifive();

$SIZEUNIT = SIZEUNIT;
$CURRENCY_SYMBOL = CURRENCY_SYMBOL;
$WEIGHTUNIT = WEIGHTUNIT;


$SIZEUNITKG = 'kg';
$SIZEUNITM = 'm';

PagoHtml::pago_top( $menu_items, 'tabs',$this->top_menu );
$doc = JFactory::getDocument();
$doc->addScriptDeclaration( "
	jQuery(document).ready(function(){
		if (location.hash) {
 		 setTimeout(function() { window.scrollTo(0, 0);}, 1);
		}
		jQuery('#params_price-lbl').append(' ($CURRENCY_SYMBOL)');
		jQuery('#params_weight-lbl').append(' ($WEIGHTUNIT)');
		jQuery('#params_length-lbl').append(' ($SIZEUNIT)');
		jQuery('#params_height-lbl').append(' ($SIZEUNIT)');
		jQuery('#params_width-lbl').append(' ($SIZEUNIT)');
		//changeMeasure();
		// jQuery('#params_discount_amount').keyup(function() {
		// 	jQuery( '#params_discount_amount' ).css('border','1px solid #ccc');
		// 	var discountAmount = jQuery( '#params_discount_amount' ).val();
		// 	if(discountAmount.length>0){
		// 		if(discountAmount.match(/^\d+$/)){
		// 			if(discountAmount<=0){
		// 				jQuery( '#params_discount_amount' ).css('border','1px solid red');
		// 			}
		// 		}else{
		// 			jQuery( '#params_discount_amount' ).css('border','1px solid red');
		// 		}
		// 	}
		// })

		if(jQuery( '#params_availibility_options option:selected').val()==1){
			jQuery('.details .pg-pgcalendar').hide();
		}

	});
	jQuery('#params_unit_of_measure').on('change',function() {
		changeMeasure();
	});
	function changeMeasure(){
		if(jQuery('#params_unit_of_measure').val() == 'imperial'){
			if(jQuery('#params_length-lbl span').length != 0){
				jQuery('#params_length-lbl span').html(' ($SIZEUNIT)');
			}else{
				jQuery('#params_length-lbl').append('<span> ($SIZEUNIT)</span>');
			}

			if(jQuery('#params_width-lbl span').length != 0){
				jQuery('#params_width-lbl span').html(' ($SIZEUNIT)');
			}else{
				jQuery('#params_width-lbl').append('<span> ($SIZEUNIT)</span>');
			}

			if(jQuery('#params_height-lbl span').length != 0){
				jQuery('#params_height-lbl span').html(' ($SIZEUNIT)');
			}else{
				jQuery('#params_height-lbl').append('<span> ($SIZEUNIT)</span>');
			}

			if(jQuery('#params_weight-lbl span').length != 0){
				jQuery('#params_weight-lbl span').html(' ($WEIGHTUNIT)');
			}else{
				jQuery('#params_weight-lbl').append('<span> ($WEIGHTUNIT)</span>');
			}
		}else{
			if(jQuery('#params_length-lbl span').length != 0){
				jQuery('#params_length-lbl span').html(' ($SIZEUNITM)');
			}else{
				jQuery('#params_length-lbl').append('<span> ($SIZEUNITM)</span>');
			}

			if(jQuery('#params_width-lbl span').length != 0){
				jQuery('#params_width-lbl span').html(' ($SIZEUNITM)');
			}else{
				jQuery('#params_width-lbl').append('<span> ($SIZEUNITM)</span>');
			}

			if(jQuery('#params_height-lbl span').length != 0){
				jQuery('#params_height-lbl span').html(' ($SIZEUNITM)');
			}else{
				jQuery('#params_height-lbl').append('<span> ($SIZEUNITM)</span>');
			}

			if(jQuery('#params_weight-lbl span').length != 0){
				jQuery('#params_weight-lbl span').html(' ($SIZEUNITKG)');
			}else{
				jQuery('#params_weight-lbl').append('<span> ($SIZEUNITKG)</span>');
			}
		}
	}

	" );
?>

<script type="text/javascript">

jQuery(document).ready(function(){

	jQuery('#params_price').bind('keypress', function(e) {
	
			var k = e.which;
			var ok = k == 46 ||  // .
					 k == 8 || // delete
					 k == 127 || // back space
					 k >= 48 && k <= 57; // 0-9
				
	
			if (!ok){
				e.preventDefault();
			}
	}); 
	
	jQuery('#params_discount_amount').bind('keypress', function(e) {
	
			var k = e.which;
			var ok = k == 46 ||  // .
					 k == 8 || // delete
					 k == 127 || // back space
					 k >= 48 && k <= 57; // 0-9
				
	
			if (!ok){
				e.preventDefault();
			}
	}); 
	
});


	Joomla.submitbutton = function( task )
	{
		jQuery.noConflict();
		
		if (task == 'cancel')
		{
			Joomla.submitform(task);
			return;
		}
	
		var name  = jQuery("#params_name");
		var price = jQuery("#params_price");
		var sku   = jQuery("#params_sku");
		var qty   = jQuery("#params_qty");
		var discountAmount = jQuery("#params_discount_amount");
		var error = 0;
		var reg = new RegExp('^[0-9]+$');
		
		var errors = [];
		
		if (price.val().length > 0) 
		{ 
			if (!price.val().match(/^[\d.]+$/)) 
			{
				errors.push({
					input: price, 
					message: "<?php echo JText::_( 'PAGO_ITEM_ERROR_NUMBERS_ONLY' ); ?>"
				});
			}
		} 
		else 
		{
			errors.push({
				input: price, 
				message: "<?php echo JText::_( 'PAGO_ITEM_ERROR_REQUIRED' ); ?>"
			});
		}	
		
		if (jQuery.trim(name.val()) == '')
		{
			errors.push({
				input: name, 
				message: "<?php echo JText::_( 'PAGO_ITEM_ERROR_REQUIRED' ); ?>"
			});
		}
		
		if (name.val().length > 255)
		{
			errors.push({
				input: name, 
				message: "<?php echo sprintf(JText::_( 'PAGO_ITEM_ERROR_NAME_MAX_SIZE' ), '255'); ?>"
			});
		}
		
		if(/^[a-zA-Z0-9- ]*$/.test(name.val()) == false) {
			errors.push({
			input: name, 
			message: "<?php echo sprintf(JText::_( 'PAGO_ITEM_ERROR_ILLEGAL_CHARACTERS' ), '255'); ?>"
		});
		}
		
		if(/^[a-zA-Z0-9- ]*$/.test(sku.val()) == false) {
			errors.push({
			input: sku, 
			message: "<?php echo sprintf(JText::_( 'PAGO_ITEM_ERROR_ILLEGAL_CHARACTERS' ), '255'); ?>"
		});
		}
		
		
		if (!reg.test(qty.val()) && qty.parent().parent().css('display') != 'none')
		{
			errors.push({
				input: qty, 
				message: "<?php echo JText::_( 'PAGO_ITEM_ERROR_NUMBERS_POSITIVE' ); ?>"
			});
		}
		
		if (discountAmount.val().length > 0)
		{
			if (discountAmount.val().match(/^[\d.]+$/))
			{
				if (discountAmount.val() < 0)
				{
					errors.push({
						input: discountAmount, 
						message: "<?php echo JText::_( 'PAGO_ITEM_ERROR_DISCOUNT_ZERO' ); ?>"
					});
				}
			}
			else
			{
				errors.push({
					input: discountAmount, 
					message: "<?php echo JText::_( 'PAGO_ITEM_ERROR_NUMBERS_ONLY' ); ?>"
				});
			}
		}
	
		if (errors.length > 0)
		{
			for (var i = 0; i < errors.length; i++) 
			{
				var error = errors[i];
			
				var label = document.createElement("label");
					label.setAttribute("for", error.input.attr("id"));
					label.setAttribute("class", "label-error");
					label.innerHTML = error.message;
				
				error.input.addClass("input-error");
				
				if (error.input.next().is("label.label-error"))
					continue;
					
				error.input.after(label).on("focus", function(){
					jQuery(this).removeClass("input-error").next("label.label-error").remove();
				});
			};
			
			jQuery("#tabs").tabs("option", "active", 0);
			
			return false;
		}
		
		jQuery('#selectedTab').val(jQuery('.ui-state-active a').attr('href'));
		Joomla.submitform(task);
	}
	
</script>
<div class = "pg-products-admin-form-container clearfix">
	<div class="pg-content">
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<input type='hidden' name="selectedTab" id='selectedTab' />
			<?php PagoHtml::deploy_tabpanel( 'tabs' ) ?>
			<div id="tabs">
				<div class="pg-tabs">
					<ul>
						<li class="first pg-information"><a onClick="addTabPrefixInUrl(this);" href="#tabs-1"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEM_TAB_INFORMATION' ); ?></a></li>
						<li class="pg-pricing"><a onClick="addTabPrefixInUrl(this);" href="#tabs-2"><span class="icon"></span><?php echo JText::_( 'PAGO_DISCOUNTS' ); ?></a></li>
						<li class="pg-product-view"><a onClick="addTabPrefixInUrl(this);" href="#tabs-3"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEMS_VIEW' ); ?></a></li>
						<li class="pg-attributes"><a onClick="addTabPrefixInUrl(this);" href="#tabs-4"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEM_TAB_ATTRIBUTES' ); ?></a></li>
						<li class="pg-attributes"><a onClick="addTabPrefixInUrl(this);" href="#tabs-5"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEM_TAB_SHIPPING' ); ?></a></li>
						<li class="pg-meta"><a onClick="addTabPrefixInUrl(this);" href="#tabs-6"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEM_TAB_METADATA' ); ?></a></li>
						<li class="pg-downloads"><a onClick="addTabPrefixInUrl(this);" href="#tabs-7"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEM_TAB_DOWNLOADS' ); ?></a></li>
						<li class="last pg-related"><a onClick="addTabPrefixInUrl(this);" href="#tabs-8"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEM_TAB_RELATED' ); ?></a></li>
						<!--<li class="pg-media"><a onClick="addTabPrefixInUrl(this);" href="#tabs-9"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEM_TAB_MEDIA' ); ?></a></li>-->
						<?php
							// Needs to be a filter, otherwise the $counter gets lost
							$counter = 10;
							$dispatcher->trigger( 'backend_item_tab_name', array( &$counter, $this->item ) );
						?>
					</ul>
					<div class="clear"></div>
				</div>

				<div class = "tabs-content pg-pad-20 pg-white-bckg pg-border ">
					<div id="tabs-1">
						<div class = "pg-row pg-mb-20">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_PRODUCT_DETAILS' ), null, null, null, 'pg-col-6', '', '', false ); ?>
								<?php echo $this->base_params; ?>
							<?php echo PagoHtml::module_bottom(false); ?>
							
							<div class="pg-col-6">
								<div class="pg-row">
									<?php echo PagoHtml::module_top( JText::_( 'PAGO_CATEGORIES' ), null, null, null, 'pg-col-12 pg-mb-20', '', '', false ); ?>
										<?php echo $this->category_params; ?>
									<?php echo PagoHtml::module_bottom(false); ?>
								</div>
							</div>
						</div>
							
						<div class = "pg-row pg-mb-20">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_TAX' ), null, null, null, 'pg-col-6 pg-mb-20', '', '', false ); ?>
								<?php echo $this->tax_params; ?>
							<?php echo PagoHtml::module_bottom(false); ?>
							
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_BUDGES' ), null, null, null, 'pg-col-6', '', '', false ); ?>
								<?php echo $this->badges_params; ?>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>

						<div class = "pg-row pg-mb-20">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_MEDIA' ), null, null, null, 'pg-col-12', '', '', false ); ?>
								<?php echo $this->images_params; ?>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>
							
						<div class = "pg-row">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_SHORT_DESCRIPTION' ), null, null, null, 'pg-col-6', '', '', false ); ?>
								<?php echo $this->short_desc_params; ?>
							<?php echo PagoHtml::module_bottom(); ?>

							<?php echo PagoHtml::module_top( JText::_( 'PAGO_LONG_DESCRIPTION' ), null, null, null, 'pg-col-6', '', '', false ); ?>
								<?php echo $this->long_desc_params; ?>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>
					</div>

					<div id="tabs-2">
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_PRODUCT_DETAILS' ), $this->item->name, null, null, null, '', '', '', false ); ?>
							<div class = "pg-pad-20 pg-border">
								<div class = "pg-row">
									<?php echo $this->discount_params; ?>
								</div>
							</div>
						<?php echo PagoHtml::module_bottom(false); ?>
					</div>
					
					<div id="tabs-3">
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_PRODUCT_VIEW' ), $this->item->name, null, null, null, '', '', '', false ); ?>
							<div class="pg-pad-20 pg-border" id="view_settings">
								<div class="pg-row">
									<?php echo $this->view_settings; ?>
								</div>
					<!--
								<?php echo $this->view_settings_sharings; ?>
					-->
							</div>
						<?php echo PagoHtml::module_bottom(false); ?>
					</div>

					<div id="tabs-4">
						<?php echo $this->attribute_params ?>
						<div class='attribute-lightbox'>
							<div class='lightbox-inner'>
								<div class='edit_image'><img id='edit_image' src='' /></div>
								<div class='lightbox-inputs'>
									<div><label>Title</label><input type='text' id='edit_title'/></div>
									<div><label>Alt</label><input type='text' id='edit_alt'/></div>
									<div><label>Description</label><textarea id='edit_desc' rows=5 cols=30></textarea></div>
									<div style="float:right;">
										<button type='button' class='lightbox-cancel'>Cancel</button>
										<button type='button' class='lightbox-save'>Save</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div id="tabs-5">
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_ITEM_TITLE_SHIPPING' ), $this->item->name, null, null, null, '', '', '', false ); ?>
						<div class = "pg-pad-20 pg-border">
							<div class = "pg-row">
								<div class = "pg-col-6">
									<?php echo $this->dimension_params; ?>
								</div>
								<div class = "pg-col-6">
									<?php echo $this->shipping_params; ?>
								</div>
							</div>
						</div>
						<?php echo PagoHtml::module_bottom(false); ?>
					</div>

					<div id="tabs-6">
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_ITEMS_TITLE_META_PARAMETERS' ), $this->item->name, null, null, null, '', '', '', true ); ?>
							<?php echo $this->meta_params; ?>
						<?php echo PagoHtml::module_bottom(false); ?>
					</div>
					
					<div id="tabs-7">
						<?php echo $this->downloadable_params; ?>
					</div>

					<div id="tabs-8">
					<?php echo PagoHtml::module_top( JText::_( 'PAGO_ITEM_TITLE_RELATED' ), $this->item->name, null, null, null, '', '', '', true ); ?>
						<div class = "pg-pad-20 pg-border">
							<div class = "pg-row">
								<div class = "pg-col-6">
									<?php echo $this->related_item_params; ?>
								</div>
								<div class = "pg-col-6">
									<?php echo $this->related_category_params; ?>
								</div>
							</div>
						</div>
						<?php echo PagoHtml::module_bottom(); ?>
					</div>

					<!--<div id="tabs-9">
						
					</div>-->

					<?php
					// Needs to be a filter, otherwise the $counter gets lost
					$counter = 10;
					$dispatcher->trigger( 'backend_item_tab_data', array( &$counter, $this->item ) );
					?>
				</div>
			</div>

			<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
			<input type="hidden" name="option" value="com_pago" />
			<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
			<input type="hidden" name="task" value="cancel" />
			<input type="hidden" name="view" value="items" />
			
			<div id="myModal" class="modal fade" role="dialog">
			  <div class="modal-dialog">
			
			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 class="modal-title"><?php echo JText::_('PAGO_ITEM_SUBSCR_TITLE') ?></h4>
			      	<p><?php echo JText::_('PAGO_ITEM_SUBSCR_DESC') ?></p>
			      </div>
			      <div class="modal-body pg-pad-20">
			      	<div id="subtabs">
						<div class="pg-tabs">
							<ul>
								<li><a href="#subtabs-1">Pricing</a></li>
								<li><a href="#subtabs-2">Interval</a></li>
								<li><a href="#subtabs-3">Trial</a></li>
							</ul>
							<div class="clear"></div>
						</div>
						<div id="subtabs-1">
							<?php echo $this->subscription_params_price; ?>
						</div>
						<div id="subtabs-2">
							<?php echo $this->subscription_params_interval; ?>
						</div>
						<div id="subtabs-3">
							<?php echo $this->subscription_params_trial; ?>
						</div>
					</div>
					<script>
						jQuery(function(){
							jQuery( '#subtabs' ).tabs();
						});
					</script>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_('PAGO_ITEM_SUBSCR_DONE') ?></button>
			      </div>
			    </div>
			
			  </div>
			</div>

			<?php echo JHTML::_( 'form.token' ); ?>
		</form>

	</div><!-- end pago content -->
</div>

<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();
