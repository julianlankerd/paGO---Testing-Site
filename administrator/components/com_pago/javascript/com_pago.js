// (function( $ ) {
// 	$.fn.GridBindCheck = function() {
// 		$(this).bind('click', function(event) {
// 			if ( $(event.target).attr("name") == "cid[]" ) {
// 				pago_highlight_row( event.target );
// 			}
// 		})
// 	}
// 	$.fn.ButtonBindKeypress = function() {
// 		$(this).bind('keypress', function(event) {
// 			if (event.which == 32 || event.which == 13) {
// 				$(this).find('button').trigger('click');
// 			}
// 		})
// 	}
// })(jQuery)
jQuery(document).ready(function(){
	
	jQuery("#params_disc_start_date").live('change',function(e){
		jQuery("#params_disc_end_date").val("");
		jQuery("#params_disc_end_date").prop('disabled',false);
		if(jQuery("#params_disc_start_date").val()==""){
			jQuery("#params_disc_end_date").val("");
			jQuery("#params_disc_end_date").prop('disabled',true);
		}else{
			jQuery("#params_disc_end_date").prop('disabled',false);
		}
		return true;
	});

	jQuery("#params_disc_end_date").live('change',function(e){
		var startYear = jQuery("#params_disc_start_date").val();
		var endYear = jQuery("#params_disc_end_date").val();
		if(endYear < startYear){
			jQuery("#params_disc_end_date").val("");
			jQuery("#params_disc_end_date").css("border","red 1px solid");
			return false;
		}else{
			jQuery("#params_disc_end_date").css("border","#ccc 1px solid");
		}
		return true;
	});
	jQuery("#params_start").live('change',function(e){
		jQuery("#params_end").val("");
		jQuery("#params_end").prop('disabled',false);
		if(jQuery("#params_start").val()==""){
			jQuery("#params_end").val("");
			jQuery("#params_end").prop('disabled',true);
		}else{
			jQuery("#params_end").prop('disabled',false);
		}
		return true;
	});

	jQuery("#params_end").live('change',function(e){
		var startYear = jQuery("#params_start").val();
		var endYear = jQuery("#params_end").val();
		var todaysDate = new Date();
		if(endYear < startYear){
			jQuery("#params_end").val("");
			jQuery("#params_end").css("border","red 1px solid");
			return false;
		}else{
			jQuery("#params_end").css("border","#ccc 1px solid");
		}
		return true;
	});
	

	jQuery(".details .pg-pgcalendar").hide();
	if(jQuery( "#params_availibility_options option:selected").val()==3){
		jQuery(".details .pg-pgcalendar").show();
	}
	jQuery( '#params_availibility_options' ).live('change',function(){
		jQuery(".details .pg-pgcalendar").hide();
		if(jQuery( "#params_availibility_options option:selected").val()==3){
			jQuery(".details .pg-pgcalendar").show();
		}
	});
	jQuery(document).on('keypress',".numeric, #params_view_settings_title_limit , .padding input, .border input, .margin input , #params_product_settings_product_title_limit,#params_product_settings_product_per_page, #params_product_settings_short_desc_limit,	#params_product_view_settings_product_title_limit, #params_product_view_settings_short_desc_limit,#params_product_view_settings_desc_limit, #params_product_view_settings_related_num_of_products",function (e) {
	
	    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
	               return false;
	    }
	});
	
	
	jQuery(document).on('click','#sub-tabs.sub-tab .category-view-tabs li',function(){
		if(!jQuery(this).hasClass('active')){//attr('tab')
			jQuery('#sub-tabs.sub-tab .category-view-tabs li').removeClass('active');
			jQuery('#sub-tabs.sub-tab .tab-content .tab-pane').removeClass('active');

			jQuery(this).addClass('active');
			tabId = jQuery('a',this).attr('tab');
			jQuery('#'+tabId).addClass('active');
		}
	})
	jQuery('#addProductVariation').on("change", function(){
		jQuery("select").chosen({"disable_search": true, "disable_search_threshold": 6});
	})

	jQuery(document).on('click','#sub-tabs.sub-tab .search-view-tabs li',function(){
		if(!jQuery(this).hasClass('active')){//attr('tab')
			jQuery('#sub-tabs.sub-tab .search-view-tabs li').removeClass('active');
			jQuery('#sub-tabs.sub-tab .tab-content .tab-pane').removeClass('active');

			jQuery(this).addClass('active');
			tabId = jQuery('a',this).attr('tab');
			jQuery('#'+tabId).addClass('active');
		}
	})
	
	jQuery(document).on('click','#pg-items-manager, #pg-categories-manager, #pg-orders-manager, #pg-plugin-manager',function(){	
		jQuery(this).bind('click', function(event) {
			if ( jQuery(event.target).attr("name") == "cid[]" ) {
				pago_highlight_row( event.target );
			}
		})
	})
	
	jQuery(document).on('keypress','#pg-button-search, #pg-button-clear',function(){	
		jQuery(this).bind('keypress', function(event) {
			if (event.which == 32 || event.which == 13) {
				jQuery(this).find('button').trigger('click');
			}
		})	
	})
	var bound=0;
	jQuery(document).on('change','#params_general_countryparamsgeneralcountry',function(){
		countryCode = jQuery('#params_general_countryparamsgeneralcountry').val();	
		jQuery.ajax({
        	type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=config&task=loadState&countryCode=' +countryCode+ '',
			dataType : 'json',
            success : function(data) {

            		jQuery('.pg-stateslist select').next("span").html('');
            		jQuery('.pg-stateslist select').html("");
            		jQuery('#params_general_countryparamsgeneralcountry').trigger('chosen:updated');
            		jQuery('.pg-stateslist select').trigger('chosen:updated');
            	if(data){
            		jQuery('.pg-stateslist select').append(data);	
            		jQuery(".pg-stateslist select").val(jQuery(".pg-stateslist select option:first").val());
            		jQuery('.pg-stateslist select').next("span").html(jQuery(".pg-stateslist select option:first").val());
            		jQuery('#params_general_countryparamsgeneralcountry').trigger('chosen:updated');
            		jQuery('.pg-stateslist select').trigger('chosen:updated');
				}
            }
        });
	})
	jQuery(document).on('change','#params_countryparamscountry',function(){
		var countryCode = '';
		jQuery(this).find('option:selected').each(function(){
			countryCode += jQuery(this).val()+',';	
		});

		countryCode = countryCode.substring(0, countryCode.length-1);

		jQuery.ajax({
        	type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=config&task=loadState&countryCode=' +countryCode+ '',
			dataType : 'json',
            success : function(data) {
        		jQuery('.pg-stateslist select').html("");
        		jQuery('.pg-stateslist select').trigger('chosen:updated');

            	if(data){
            		jQuery('.pg-stateslist select').append(data);	
					jQuery('.pg-stateslist select').trigger('chosen:updated');
				}
            }
        });
	})
	// select state for customer
	jQuery(document).on('change','#address_billing_countryaddress_billingcountry',function(){
		changeCustomerBillingAddress();
	})
	jQuery(document).on('change','#address_mailing_countryaddress_mailingcountry',function(){
		changeCustomerMailingAddress();
	})

	if(jQuery('#address_mailing_countryaddress_mailingcountry').length > 0){
		changeCustomerMailingAddress();
	}

	if(jQuery('#address_billing_countryaddress_billingcountry').length > 0){
		changeCustomerBillingAddress();
	}

	// if(jQuery('#params_pgtax_countryparamspgtax_country').length > 0){
	// 	changeTaxStates();
	// }
	// // change tax state based on country selection
	// jQuery(document).on('change','#params_pgtax_countryparamspgtax_country',function(){
	// 	changeTaxStates();
	// })
	
	jQuery(document).on('click','.related-item-remove',function(){
		deletedItem = jQuery(this).parent();
		related_items = jQuery('#params_related_items').val();
		related_items = JSON.parse(related_items);
		new_related_items= {};
		i=0;
		for (key in related_items) {
			if(related_items[key].id && related_items[key].id !=  deletedItem.attr("id")){
				new_related_items[i++] = related_items[key]; 	
			}
		}
		related_items = new_related_items;
		jQuery('#params_related_items').val(JSON.stringify(related_items));
		deletedItem.remove();	
	})
	jQuery(document).on('keyup','#related-item-add',function(){
		if(bound) return;
		bound=1;
		itemId = jQuery('input[name="id"]').val();
		if(!itemId){
			itemId = 0;
		}
		jQuery(this).autocomplete({
			 source : function(request, response) {
				  	var liIds;
					liIds = jQuery('.pg-related-category li').map(function(i,n) { return jQuery(n).attr('id');}).get().join('","');
				 	var PrdliIds;
				 	PrdliIds = jQuery('.pg-related-items li').map(function(i,n) { return jQuery(n).attr('id');}).get().join('","');
	            	jQuery.ajax({
		            	type: 'POST',
						url: 'index.php',
						data: 'option=com_pago&controller=items&task=related_items&q=' +request.term+ '&itemId=' +itemId+'&catid=' +liIds+'&existPrdId=' +PrdliIds+ '',
						dataType : 'json',
		                success : function(data) {
		                    jQuery('#related-item-add').removeClass('itemLoading');
		                    response(data, function(item) {
		                        return item;
		                    });
		                }
		            });
	       },
	       messages: {
		        noResults: 'No Result Found',
		        results: function() {}
		    },
	        minLength : 1,
	        select : function(event, ui) {
	            jQuery('<li class="itemAdded" id="' + ui.item.value + '">' + ui.item.label + '<span title="Click to remove related item" class="related-item-remove fa fa-times"></span></li>').appendTo('.pg-related-items');
	            this.value = '';
	            related_items = jQuery('#params_related_items').val();
				related_items = JSON.parse(related_items);
				len = getObjectArrayLength(related_items);
				
				related_items[len] = {};
				related_items[len].id = ui.item.value;
					
				jQuery('#params_related_items').val(JSON.stringify(related_items));
	            return false;
	        },
	        search : function(event, ui) {
	            jQuery('#related-item-add').addClass('itemLoading');
	        }
	    });
	});
	
	//category search start
	var bound_cat=0;
	jQuery(document).on('keyup','#related-category-add',function(){
		if(bound_cat) return;
		bound_cat=1;
		itemId = jQuery('input[name="id"]').val();
		
		if(!itemId){
			itemId = 0;
		
		}
		jQuery(this).autocomplete({
			 source : function(request, response) {
			 	var exCatIds;
				exCatIds = jQuery('.pg-related-category li').map(function(i,n) { return jQuery(n).attr('id');}).get().join('","');

	            jQuery.ajax({
	            	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=categories&task=related_category&q=' +request.term+ '&exCatIds=' +exCatIds+'&itemId=' +itemId+ '',
					dataType : 'json',
	                success : function(data) {
	                    jQuery('#related-category-add').removeClass('itemLoading');
	                    response(data, function(item) {
	                        return item;
	                    });
	                }
	            });
	       },
	        messages: {
		        noResults: 'No Result Found',
		        results: function() {}
		    },
	        minLength : 2,
	        select : function(event, ui) {
	            jQuery('<li class="categoryAdded" id="' + ui.item.value + '">' + ui.item.label + '<span title="Click to remove related category" class="related-category-remove fa fa-times"></span></li>').appendTo('.pg-related-category');
	            this.value = '';
	            related_items = jQuery('#params_related_category').val();
				related_items = JSON.parse(related_items);
				len = getObjectArrayLength(related_items);
				
				related_items[len] = {};
				related_items[len].id = ui.item.value;
				
				jQuery('#params_related_category').val(JSON.stringify(related_items));
	            return false;
	        },
	        search : function(event, ui) {
	            jQuery('#related-category-add').addClass('itemLoading');
	        }
	    });
	});
	jQuery(document).on('click','.related-category-remove',function(){
		deletedItem = jQuery(this).parent();
		
		related_items = jQuery('#params_related_category').val();
		related_items = JSON.parse(related_items);
		new_related_items= {};
		i=0;
		for (key in related_items) {
			if(related_items[key].id && related_items[key].id !=  deletedItem.attr("id")){
				new_related_items[i++] = related_items[key]; 	
			}
		}
		related_items = new_related_items;
		jQuery('#params_related_category').val(JSON.stringify(related_items));
		deletedItem.remove();	
	})	
		
	//category show hide
	jQuery(document).on('change','input[name="params[product_settings_short_desc]"]',function(){
		configCategoryShowHide();
	})
	jQuery(document).on('change','input[name="params[product_settings_desc]"]',function(){
		configCategoryShowHide();
	})
	jQuery(document).on('change','input[name="params[product_view_settings_short_desc]"]',function(){
		configCategoryShowHide();
	})
	jQuery(document).on('change','input[name="params[product_view_settings_desc]"]',function(){
		configCategoryShowHide();
	})
	jQuery(document).on('change','input[name="params[product_view_settings_related_products]"]',function(){
		configCategoryShowHide();
	})
	configCategoryShowHide();	
	//item show hide
	jQuery(document).on('change','input[name="params[show_new]"]',function(){
		configItemShowHide();
	})
	jQuery(document).on('change','input[name="params[view_settings_desc]"]',function(){
		configItemShowHide();
	})
	jQuery(document).on('change','input[name="params[view_settings_short_desc]"]',function(){
		configItemShowHide();
	})
	jQuery(document).on('change','input[name="params[product_settings_short_desc]"]',function(){
		configItemShowHide();
	})
	jQuery(document).on('change','input[name="params[view_settings_image_settings_show]"]',function(){
		configItemShowHide();
	})
	jQuery(document).on('change','input[name="params[view_settings_related_products]"]',function(){
		configItemShowHide();
	})
	jQuery(document).on('change','input[name="params[item_custom_layout_inherit]"]',function(){
		configItemShowHide();
	})
	jQuery(document).on('change','input[name="params[view_settings_title_limit_inherit]"]',function(){
		configItemShowHide();
	})
	configItemShowHide();

	jQuery( '#pg-customer-address_mailing_same_as_billing' ).live('change',function(){
		if( jQuery('#pg-customer-address_mailing_same_as_billing').is(':checked') ){
			jQuery( '#addShippingaddress').hide();
		}
		else{
			jQuery( '#addShippingaddress').show();
		}
	});

	jQuery(document).on('click', 'ul.pg-menu > li .chevron', function(){
		var obj = jQuery(this).parent('li');
		if (obj.hasClass('open')){
			obj.removeClass('open');
			obj.find('.pg-submenu-wrap').slideUp();
		}
		else{
			obj.addClass('open');
			obj.find('.pg-submenu-wrap').slideDown();
		}	
	})

	jQuery('.pg-submenu-wrap li.current').parents('li').addClass('current open');

	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}

	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}

	function eraseCookie(name) {
		createCookie(name,"",-1);
	}

	function closeSidebar(animate) {
		jQuery('.pg-sidebar-show-hide').removeClass('opened');
		jQuery('.pg-sidebar-show-hide').addClass('closed');
		jQuery('.pg-sidebar .pg-menu').addClass('closed');
		if (animate === true) {
			jQuery('.pg-sidebar').stop(true, false).animate({'width': '60px'}, 300);
			jQuery('.pg-main-container').stop(true, false).animate({'margin-left': '60px'}, 300);
		} else {
			jQuery('.pg-sidebar').css('width', '60px');
			jQuery('.pg-main-container').css('margin-left', '60px');
		}
	}

	function openSidebar(animate) {
		jQuery('.pg-sidebar-show-hide').addClass('opened');
		jQuery('.pg-sidebar-show-hide').removeClass('closed');
		jQuery('.pg-sidebar .pg-menu').removeClass('closed');
		if ( animate === true ) {
			jQuery('.pg-sidebar').stop(true, false).animate({'width': '240px'}, 300);
			jQuery('.pg-main-container').stop(true, false).animate({'margin-left': '240px'}, 300);
		} else {
			jQuery('.pg-sidebar').css('width', '240px');
			jQuery('.pg-main-container').css('margin-left', '240px');
		}
	}

	var sidebarCookie = readCookie('closeSidebar');

	if ( sidebarCookie === 'true' ) {
		closeSidebar();
	}

	jQuery(document).on('click', '.pg-sidebar-show-hide', function(){
		if (jQuery(this).hasClass('opened')){
			createCookie('closeSidebar', true, 1);
			jQuery('.pg-menu > li').removeClass('open');
			jQuery('.pg-submenu-wrap').slideUp(200);
			setTimeout(function(){
				closeSidebar(true);
			}, 200);

		}
		else{
			createCookie('closeSidebar', false, 1);
			openSidebar(true);
		}
	});

	jQuery('.pg-menu').perfectScrollbar();

	jQuery(document).on('click', '.media-add-ico', function(){
		jQuery('.media-add-text').fadeOut('slow');
		jQuery('.media-add-image-video').fadeIn('slow');
	})

	jQuery(document).on('focus', '.pg-pgcalendar input[type="text"]', function(){
		var obj = jQuery(this).parent();

		obj.addClass('opened');
		jQuery('#ui-datepicker-div').css('margin-left', obj.width()-jQuery('#ui-datepicker-div').width());
	})

	jQuery(document).on('focusout', '.pg-pgcalendar input[type="text"]', function(){
		jQuery(this).parent().removeClass('opened');
	})
});

jQuery(window).load(function(){
	jQuery('.pg-row-item.hiddener').each(function(){
		if(jQuery(this).find('select option:selected').val()=="1")
			jQuery(this).next('.to-hide').css('display','none');
		else
			jQuery(this).next('.to-hide').css('display','block');	
	})
	
	jQuery('.pg-row-item.hiddener select').change(function(){
		if(jQuery(this).find('option:selected').val()=="1")
			jQuery(this).parents('.pg-row-item.hiddener').next('.to-hide').css('display','none');
		else
			jQuery(this).parents('.pg-row-item.hiddener').next('.to-hide').css('display','block');		
	})

	jQuery(document).on('click', '.pg_plugin_container .pane-sliders .panel .pane-toggler', function(){
		var obj = jQuery(this).parent();
		if (obj.hasClass('open')){
			obj.removeClass('open');
			obj.find('.pane-slider').slideUp();
		}
		else{
			jQuery('.pg_plugin_container .pane-sliders .panel.open').find('.pane-slider').slideUp();
			jQuery('.pg_plugin_container .pane-sliders .panel.open').removeClass('open');

			obj.addClass('open');
			obj.find('.pane-slider').slideDown();	
		}
	})

	if (jQuery('.pg-dashboard-orders-container .pg-dashboard-orders-block').height() > jQuery('.pg-dashboard-comments-container .pg-dashboard-comment-block').height()){
		jQuery('.pg-dashboard-comments-container .pg-dashboard-comment-block').css('height', jQuery('.pg-dashboard-orders-container .pg-dashboard-orders-block').height());
	}
	else{
		jQuery('.pg-dashboard-orders-container .pg-dashboard-orders-block').css('height', jQuery('.pg-dashboard-comments-container .pg-dashboard-comment-block').height());	
	}
})

function configCategoryShowHide(){
	if(jQuery('#params_product_settings_short_desc input:checked').attr('value') == 0){
		jQuery('.pg-category-short-desc-limit').css("display","none");
	}
	else{
		jQuery('.pg-category-short-desc-limit').css("display","block");
	}

	if(jQuery('#params_product_settings_desc input:checked').attr('value') == 0){
		jQuery('.pg-category-desc-limit').css("display","none");
	}
	else{
		jQuery('.pg-category-desc-limit').css("display","block");
	}

	if(jQuery('#params_product_view_settings_short_desc input:checked').attr('value') == 0){
		jQuery('.pg-category-product-view-short-desc-limit').css("display","none");
	}
	else{
		jQuery('.pg-category-product-view-short-desc-limit').css("display","block");
	}

	if(jQuery('#params_product_view_settings_desc input:checked').attr('value') == 0){
		jQuery('.pg-category-product-view-desc-limit').css("display","none");
	}
	else{
		jQuery('.pg-category-product-view-desc-limit').css("display","block");
	}

	if(jQuery('#params_product_settings_desc input:checked').attr('value') == 0){
		jQuery('.pg-category-desc-limit').css("display","none");
	}
	else{
		jQuery('.pg-category-desc-limit').css("display","block");
	}

	if(jQuery('#params_product_view_settings_related_products input:checked').attr('value') == 0){
		jQuery('.related_products_sub').css("display","none");
	}
	else{
		jQuery('.related_products_sub').css("display","block");
	}
}
function configItemShowHide(){
	if(jQuery('#params_show_new input:checked').attr('value') == 0){
		jQuery('.pago-new-until-desc').css("display","none");
	}
	else{
		jQuery('.pago-new-until-desc').css("display","block");
	}

	if(jQuery('#params_view_settings_desc input:checked').attr('value') == 0 || jQuery('#params_view_settings_desc input:checked').attr('value') == 2){
		jQuery('.pg-product-view-desc-limit').css("display","none");
	}
	else{
		jQuery('.pg-product-view-desc-limit').css("display","block");
	}
	
	if(jQuery('#params_view_settings_short_desc input:checked').attr('value') == 0 || jQuery('#params_view_settings_short_desc input:checked').attr('value') == 2){
		jQuery('.pg-product-view-short-desc-limit').css("display","none");
	}
	else{
		jQuery('.pg-product-view-short-desc-limit').css("display","block");
	}

	if(jQuery('#params_view_settings_image_settings_show input:checked').attr('value') == 2){
		jQuery('.pago-product-view-image-settings-desc').css("display","none");
	}
	else{
		jQuery('.pago-product-view-image-settings-desc').css("display","block");
	}

	if(jQuery('#params_view_settings_related_products input:checked').attr('value') == 1){
		jQuery('.item_related_products_sub').css("display","block");
	}
	else{
		jQuery('.item_related_products_sub').css("display","none");
	}
	
	if(jQuery('#params_item_custom_layout_inherit input:checked').attr('value') == 2){
		jQuery('.pago-item-item-custom-layout-desc').css("display","none");
	}
	else{
		jQuery('.pago-item-item-custom-layout-desc').css("display","block");
	}
	
	if(jQuery('#params_view_settings_title_limit_inherit input:checked').attr('value') == 0){
		jQuery('.pg-product-view-title-limit').css("display","none");
	}
	else{
		jQuery('.pg-product-view-title-limit').css("display","block");
	}
}
function getObjectArrayLength(obj){
	keys = [];
		var k;
		var len=0;
		for (k in obj)
		{
		    if (obj.hasOwnProperty(k))
		    {
		        len++;
		    }
		}
	return len;	
}
function pull_upload_form( item_id, type, path , temp) {
	if ( !path ) {
		path = '';
	}

	tb_show( 'Add ' + type, 'index.php?option=com_pago&view=upload_form&item_id=' + item_id
		+ '&type=' + type + '&path=' + path
		+ '&tmpl=component&TB_iframe=true&height=564&width=788' );
}
function pull_edit_form( item_id, type, file, path ) {
	if ( !file ) {
		file = '';
	}
	if ( !path ) {
		path = '';
	}

	tb_show( 'Edit ' + type, 'index.php?option=com_pago&view=file&layout=form&item_id=' + item_id
		+ '&type=' + type + '&file=' + file + '&path=' + path
		+ '&tmpl=component&TB_iframe=true&height=564&width=788' );
}

function delete_config_row( el )
{
	jQuery(el).parent().parent().remove();

	return false;
}

function gup( name, search )
{
	if ( !search ) {
		search = window.location.href;
	}

	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&#]"+name+"=([^&]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( search );

	if ( results == null ) {
		return "";
	} else {
		return results[1];
	}
}

function bind_publish_buttons()
{	
	jQuery( '.publish-buttons' ).live('click',function(){
		id = jQuery(this).attr('rel');
		img = jQuery(this).children().first();
		css = jQuery(img).attr('class');
		type = jQuery(this).attr('type');

		if ( 'item-unpublish' == css ) {
			task = 'unpublish';
		} else { // Is unpublished
			task = 'publish';
		}

		var el = jQuery(this);
		
		if(type == 'currency' && task == 'unpublish' && (jQuery('#currency_' + id + ' td.pg-default div.pg-default .currency-default').hasClass('is-default-1')))
		{	
			return false;		
		}
		else if(type == 'size_unit' && task == 'unpublish' && (jQuery('#size_unit_' + id + ' td.pg-default div.pg-default .size_unit-default').hasClass('is-default-1')))
		{
			return false;		
		}
		else if(type == 'weight_unit' && task == 'unpublish' && (jQuery('#weight_unit_' + id + ' td.pg-default div.pg-default .weight_unit-default').hasClass('is-default-1')))
		{	
			return false;		
		}
		else
		{
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=file&type='+type+'&task=' +task+ '&cid[]=' +id+ '&async=1',
				success: function( content ) {
					
					if ( content ) {
						jQuery(el).replaceWith( content );
						jQuery('.publish-buttons').unbind('click');
						//bind_publish_buttons();
					}
				}
			});
		}
		
	})
}

function img_edit_desc( id )
{
	tb_show( 'Edit Description', 'index.php?option=com_pago&view=file&id='+id+'&tmpl=component&TB_iframe=true&height=550&width=788' );
	
}

function pago_check_all( pel, el )
{
	if ( jQuery(pel).attr('checked') ) {
		jQuery(el).attr('checked', true);
		jQuery(el).parents('tr').addClass('pg-highlight-row');
		jQuery('[name="boxchecked"]').val(1);
	} else {
		jQuery(el).attr('checked', false);
		jQuery(el).parents('tr').removeClass('pg-highlight-row');
		jQuery('[name="boxchecked"]').val(0);
	}
}

function pago_highlight_row ( el )
{
	if ( jQuery(el).attr('checked') ) {
		jQuery(el).parents('tr').addClass('pg-highlight-row');
	} else {
		jQuery(el).parents('tr').removeClass('pg-highlight-row');
	}
}

function delete_files( els )
{
	del = '';
	jQuery(els).each(function(){
		if ( jQuery(this).attr('checked') ) {
			tr = jQuery(this).closest('tr');
			if(tr){
				// if(jQuery(tr).find('.pg-default a').hasClass('is-default-1')){
				// 	alert("You can't remove default image");
				// 	return false;
				// }
			}
			del += '&cid[]=' + jQuery(this).val();
		}
	});
	if ( !del ) {
		return;
	}
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: 'option=com_pago&controller=file&task=remove' + del+ '&async=1',
		success: function( response ) {
			if ( 1 == response ) {
				jQuery(els).each(function(){
					if ( jQuery(this).attr( 'checked' ) ) {
						jQuery(this).parents( '.pg-table-content' ).fadeOut('fast', function () {
							jQuery(this).parents( '.pg-table-content' ).remove();
						});
					}
				});
			} else {
				alert( response );
			}
		}
	});

	return false;
}

function validValNotEmpty(value){
	if(value == ""){
	       return false;
	}else{
		return true;
	}
}

function pull_upload_form_massmove(catstr) 
{
	tb_show( 'Mass Move', 'index.php?tmpl=component&option=com_pago&view=items&layout=massmove&catStr=' + catstr + '&TB_iframe=true&height=404&width=350');
}
function addTabPrefixInUrl(e){
	setTimeout(function() { window.scrollTo(0, 0);}, 1);
	window.location.hash = jQuery(e).attr('href');
}


function checkpgCustValidation()
{
	var address_billing_company= jQuery("#address_billing_company").val();
	var address_billing_first_name= jQuery("#address_billing_first_name").val();
	var address_billing_last_name= jQuery("#address_billing_last_name").val();
	var address_billing_address_1= jQuery("#address_billing_address_1").val();
	var address_billing_city= jQuery("#address_billing_city").val();
	var address_billing_region= jQuery("#address_billing_region").val();
	var address_billing_country= jQuery("#address_billing_country").val();
	var address_billing_zip= jQuery("#address_billing_zip").val();
	var address_billing_user_email= jQuery("#address_billing_user_email").val();
	var address_billing_phone_1= jQuery("#address_billing_phone_1").val();
	var address_mailing_company= jQuery("#address_mailing_company").val();
	var address_mailing_first_name= jQuery("#address_mailing_first_name").val();
	var address_mailing_last_name= jQuery("#address_mailing_last_name").val();
	var address_mailing_address_1= jQuery("#address_mailing_address_1").val();
	var address_mailing_city= jQuery("#address_mailing_city").val();
	var address_mailing_region= jQuery("#address_mailing_region").val();
	var address_mailing_country= jQuery("#address_mailing_country").val();
	var address_mailing_zip= jQuery("#address_mailing_zip").val();
	var address_mailing_user_email= jQuery("#address_mailing_user_email").val();
	var address_mailing_phone_1= jQuery("#address_mailing_phone_1").val();
	jQuery('#address_billing_company').removeClass('cust_error');
	jQuery('#address_billing_first_name').removeClass('cust_error');
	jQuery('#address_billing_last_name').removeClass('cust_error');
	jQuery('#address_billing_address_1').removeClass('cust_error');
	jQuery('#address_billing_city').removeClass('cust_error');
	jQuery('#address_billing_region').removeClass('cust_error');
	jQuery('#address_billing_country').removeClass('cust_error');
	jQuery('#address_billing_zip').removeClass('cust_error');
	jQuery('#address_billing_user_email').removeClass('cust_error');
	jQuery('#address_billing_phone_1').removeClass('cust_error');
	jQuery('#address_mailing_company').removeClass('cust_error');
	jQuery('#address_mailing_first_name').removeClass('cust_error');
	jQuery('#address_mailing_last_name').removeClass('cust_error');
	jQuery('#address_mailing_address_1').removeClass('cust_error');
	jQuery('#address_mailing_city').removeClass('cust_error');
	jQuery('#address_mailing_region').removeClass('cust_error');
	jQuery('#address_mailing_country').removeClass('cust_error');
	jQuery('#address_mailing_zip').removeClass('cust_error');
	jQuery('#address_mailing_user_email').removeClass('cust_error');
	jQuery('#address_mailing_phone_1').removeClass('cust_error');
	
	
	if(address_billing_company == ''){
		jQuery('#address_billing_company').addClass('cust_error');
		return false;
	}
	
	if(address_billing_first_name == ''){
		jQuery('#address_billing_first_name').addClass('cust_error');
		return false;
	}

	if(address_billing_last_name == ''){
		jQuery('#address_billing_last_name').addClass('cust_error');
		return false;
	}

	if(address_billing_address_1 == ''){
		jQuery('#address_billing_address_1').addClass('cust_error');
		return false;
	}
	
	if(address_billing_city == ''){
		jQuery('#address_billing_city').addClass('cust_error');
		return false;
	}

	if(address_billing_region == ''){
		jQuery('#address_billing_region').addClass('cust_error');
		return false;
	}

	if(address_billing_country == ''){
		jQuery('#address_billing_country').addClass('cust_error');
		return false;
	}

	if(address_billing_zip == ''){
		jQuery('#address_billing_zip').addClass('cust_error');
		return false;
	}

	if(address_billing_user_email == ''){
		jQuery('#address_billing_user_email').addClass('cust_error');
		return false;
	}
	
	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(address_billing_user_email)){
    
    }
    else{
		jQuery('#address_billing_user_email').addClass('cust_error');
        return false;
    }
	
	if(address_billing_phone_1 == ''){
		jQuery('#address_billing_phone_1').addClass('cust_error');
		return false;
	}
	
	 // var phoneRegExp = /^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/;
  //    var phoneVal = jQuery("#address_billing_phone_1").val();
  //    var numbers = phoneVal.split("").length;
	 
  //    console.log('6');
  //    if (10 <= numbers && numbers <= 20 && phoneRegExp.test(phoneVal)) 
	 // {
  //    }
	 // else
	 // {
	 // 	jQuery('#address_billing_phone_1').addClass('cust_error');
	 // 	return false;
	 // }
	 
	var isChecked = jQuery('#pg-customer-address_mailing_same_as_billing').attr('checked')?true:false;
	
	if(isChecked == false)
	{
		if(address_mailing_company == ''){
			jQuery('#address_mailing_company').addClass('cust_error');
			return false;
		}

		if(address_mailing_first_name == ''){
			jQuery('#address_mailing_first_name').addClass('cust_error');
			return false;
		}

		if(address_mailing_last_name == ''){
			jQuery('#address_mailing_last_name').addClass('cust_error');
			return false;
		}

		if(address_mailing_address_1 == ''){
			jQuery('#address_mailing_address_1').addClass('cust_error');
			return false;
		}
		
		if(address_mailing_city == ''){
			jQuery('#address_mailing_city').addClass('cust_error');
			return false;
		}

		if(address_mailing_region == ''){
			jQuery('#address_mailing_region').addClass('cust_error');
			return false;
		}

		if(address_mailing_country == ''){
			jQuery('#address_mailing_country').addClass('cust_error');
			return false;
		}

		if(address_mailing_zip == ''){
			jQuery('#address_mailing_zip').addClass('cust_error');
			return false;
		}

		if(address_mailing_user_email == ''){
			jQuery('#address_mailing_user_email').addClass('cust_error');
			return false;
		}
		
		var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		if (filter.test(address_mailing_user_email)){
		
		}
		else{
			jQuery('#address_mailing_user_email').addClass('cust_error');
			return false;
		}
		
		if(address_mailing_phone_1 == ''){
			jQuery('#address_mailing_phone_1').addClass('cust_error');
			return false;
		}
		
		 // var phoneRegExp = /^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/;
		 // var phoneVal = jQuery("#address_mailing_phone_1").val();
		 // var numbers = phoneVal.split("").length;
		 
		 // if (10 <= numbers && numbers <= 20 && phoneRegExp.test(phoneVal)) 
		 // {
		 // }
		 // else
		 // {
			// jQuery('#address_mailing_phone_1').addClass('cust_error');
			// return false;
		 // }
	}
	return true;
}

function getJoomlaUserInfo(userid){
	jQuery.ajax({
        type: "POST",
        url: 'index.php',
		data: 'option=com_pago&view=customers&task=getJoomlaUserInfo&userid=' + userid+ '&async=1',
        success: function(response){
         	if (response){
        		var uinfo =response.split("_");
				var uname =uinfo[0].split(" ");
        		jQuery( "#address_billing_first_name").val(uname[0]);
        		jQuery( "#address_billing_last_name").val(uname[1]);
        		jQuery( "#address_billing_user_email").val(uinfo[1]);
        		jQuery( "#jid").val(userid);
        	}
        }
    });
}
	
function changeCustomerBillingAddress(){
	countryCode = jQuery('#address_billing_countryaddress_billingcountry').val();
	jQuery.ajax({
    	type: 'POST',
		url: 'index.php',
		data: 'option=com_pago&controller=config&task=loadState&countryCode=' +countryCode+ '',
		dataType : 'json',
        success : function(data) {
    		jQuery('#address_billing_stateaddress_billingstate').siblings("span").html('');
    		jQuery('#address_billing_stateaddress_billingstate').html("");
        
        	if(data){
        		jQuery('#address_billing_stateaddress_billingstate').append(data);	
        		jQuery('#address_billing_stateaddress_billingstate').siblings("span").html(jQuery("#address_billing_stateaddress_billingstate option:first").val());
			}
        	jQuery("#address_billing_stateaddress_billingstate").val(jQuery("#address_billing_stateaddress_billingstate option:first").val());
        }
    });
}

function changeCustomerMailingAddress(){
	countryCode = jQuery('#address_mailing_countryaddress_mailingcountry').val();
	jQuery.ajax({
        	type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=config&task=loadState&countryCode=' +countryCode+ '',
			dataType : 'json',
            success : function(data) {
        		jQuery('#address_mailing_stateaddress_mailingstate').siblings("span").html('');
        		jQuery('#address_mailing_stateaddress_mailingstate').html("");
    
            	if(data){
            		jQuery('#address_mailing_stateaddress_mailingstate').append(data);	
            		jQuery('#address_mailing_stateaddress_mailingstate').siblings("span").html(jQuery("#address_mailing_stateaddress_mailingstate option:first").val());
				}
            	jQuery("#address_mailing_stateaddress_mailingstate").val(jQuery("#address_mailing_stateaddress_mailingstate option:first").val());
            }
        });
}

// function changeTaxStates(){
// 	countryCode = jQuery('#params_pgtax_countryparamspgtax_country').val();
// 	stateCode = jQuery('#params_pgtax_stateparamspgtax_state').val();	

// 	jQuery.ajax({
//     	type: 'POST',
// 		url: 'index.php',
// 		data: 'option=com_pago&controller=config&task=loadState&countryCode=' +countryCode+ '',
// 		dataType : 'json',
//         success : function(data) {
//     		jQuery('.pg-stateslist select').siblings("span").html('');
//     		jQuery('.pg-stateslist select').html("");
        		
//     		console.log(data);
//         	if(data){
//         		jQuery('.pg-stateslist select').append(data);	
//         		jQuery(".pg-stateslist select").val(jQuery(".pg-stateslist select option:first").val());
        	
//         		if(stateCode != ""){
//         			jQuery('.pg-stateslist select').siblings("span").html(stateCode);
//         			jQuery('#params_pgtax_stateparamspgtax_state option[value="'+stateCode+'"').attr("selected", "selected");
//         		}
//         		else{
//         			jQuery('.pg-stateslist select').siblings("span").html(jQuery(".pg-stateslist select option:first").val());
//         		}
//     		}
//         }
//     });
// }

jQuery(document).on('change','#params_unlimited',function(){
	var unlimited = jQuery("#params_unlimited").val();
	
	if(unlimited == 1){
		jQuery("#params_quantity").prop("readonly",true);
		jQuery('#params_quantity').css('opacity', 0.4);
	}
	else{
		jQuery("#params_quantity").removeAttr("readonly") ;
		jQuery('#params_quantity').css('opacity', 1);
	}
});
// Category edit // Check if parameters are inherited, disable all corresponding inputs and vice versa

jQuery(document).ready(function(){
	if(jQuery('#params_inherit_parameters_fromparamsinherit_parameters_from').val() > 1){
		jQuery('#tabs-2 input, #tabs-3 input').prop('disabled', true);
	} else {
		jQuery('#tabs-2 input, #tabs-3 input').prop('disabled', false);
	}
})

jQuery(document).on('change','#params_inherit_parameters_fromparamsinherit_parameters_from',function(){

	if(jQuery(this).val() > 1){
		jQuery('#tabs-2 input, #tabs-3 input').prop('disabled', true);
	} else {
		jQuery('#tabs-2 input, #tabs-3 input').prop('disabled', false);
	}
	
});



jQuery( document ).ready(function() {

	bind_publish_buttons();

	jQuery(document).on('click','#pago .pg-customers .new_user',function(){	
    	jQuery('.existing_users_details').css('display','none');
		jQuery('.add_billing_adress').css('display', 'none');
		jQuery('.pg_user_register').fadeIn();
	});

	jQuery(document).on('click','#pago .pg-customers .existing_user',function(){	
    	jQuery('.pg_user_register').css('display','none');
		jQuery('.add_billing_adress').css('display', 'none');
		jQuery('.existing_users_details').fadeIn();
	});

	jQuery(document).on('click','#pago .pg-customers .selected_user',function(){
		var user_id = jQuery('.users_list').val();
		if(user_id != '0'){
			getUserAccount(user_id);
		}
		else{
			jQuery('.users_list').css('border', '1px solid red');
			alert('Please select a user');
		}
	});
	jQuery(document).on('click','#pago .pg-customers .pg_user_register .pg-register-button',function(){
		if(jQuery('.pg_user_register form').length){
			var form = jQuery(".pg_user_register form");
			var data = new Object();
			var error = '';
			var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			form.find('input').each(function(){
				data[jQuery(this).attr('name')] = jQuery(this).val();
			})
			data['tmpl'] = 'component';
			data['view'] = 'customers';
			data['task'] = 'saveUser';
			data['jform[username]'] = data['jform[email]'];

			if(!data['jform[name]']) error += '<div>Name is required</div>'

			if(!data['jform[email]']) error += '<div>Email is required</div>'

			if (!filter.test(data['jform[email]'])) error +=  '<div>Email is incorrect</div>'

			if(!data['jform[password1]'] && !data['jform[password2]']) error += '<div>Passwords is required</div>'  

			if(data['jform[password1]'] != data['jform[password2]']) error += '<div>Passwords do not match</div>' 

			if(error != '')
			{
				jQuery('#pg-system-messages').html('<div class="alert alert-warning">'+error+'</div>');
				return false;
			}
			jQuery.ajax({
        		type: "POST",
        		url: 'index.php',
				data: 'option=com_pago&view=customers&task=checkUserExist&email=' + data['jform[email]']+ '&async=1',
        		success: function(response){
         			if (response){
        				if(response == '0'){
        					jQuery('#pg-system-messages').html('<div class="alert alert-warning"><div>Error: This e-mail already exists</div></div>');
        					return false;
        				}else
        				{
        					jQuery.ajax({
								type: 'POST',
								url: 'index.php',
								async: false,
								data: data,
								success: function( response ) {
									getUserAccount(response);
								}
							});
        				}
        			}
        		}
    		});
		}
	});

jQuery(document).on('click','.pg-download-manager-fx .pg-remove',function(){
	if(jQuery(this).hasClass('disabled')){
		return false;
	}
	var file = jQuery(this).parent();
	var id = jQuery(this).parent().find('.publish-buttons').attr('rel');

	if(!confirm('Are you sure ?')){
		return;
	}

	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: 'option=com_pago&view=file&task=remove&cid[]=' + id+ '&async=1',
		success: function( response ) {
			if ( 1 == response ) {
				file.remove();
			} else {
				alert( response );
			}
		}
	});
});

jQuery(document).on('click','.pg-product-images .pg-remove',function(){
	if(jQuery(this).hasClass('disabled')){
		return false;
	}
	var img = jQuery(this).parent();
	var id = jQuery(this).parent().find('.id-for-delete').attr('rel');
	if(img.find('.images-default').hasClass('is-default-1')){
		if(!confirm('This is a primary image. Are you sure ?')){
			return;
		}
	}
	else{
		if(!confirm('Are you sure ?')){
			return;
		}
	}

	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: 'option=com_pago&view=file&task=remove&cid[]=' + id+ '&async=1',
		success: function( response ) {
			if ( 1 == response ) {
				img.remove();
			} else {
				alert( response );
			}
		}
	});
});

jQuery(document).on('click','.pg-download-manager .pg-remove',function(){
	var file = jQuery(this).parent();
	var id = jQuery(this).parent().attr('rel');
	id = id.replace("cid-", "");
	if(!confirm('Are you sure ?')){
		return;
	}

	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: 'option=com_pago&view=file&task=remove&cid[]=' + id+ '&async=1',
		success: function( response ) {
			if ( 1 == response ) {
				file.remove();
			} else {
				alert( response );
			}
		}
	});
});

jQuery(document).on('click','.image-item .upload-image-remove a',function(){
	var img = jQuery(this).closest( "div" );
	var id  = jQuery(this).parent().attr('id');


	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: 'option=com_pago&view=file&task=remove&cid[]=' + id+ '&async=1',
		success: function( response ) {
			if ( 1 == response ) {
				img.remove();
				jQuery('.video_thumb_upload').removeClass('disabled');
			} else {
				alert( response );
			}
		}
	});
});

jQuery(document).on('click','.existing_users_details_input .fa-times',function(){
    	jQuery("#users-list-add:text").val('');
	});
});
jQuery(window).on('load', function(){
	jQuery('select').chosen({'disable_search_threshold': 6, 'width': 'auto' });
	jQuery('ul.pg-categories-list').perfectScrollbar();
	
});


function getUserAccount(userId){
	jQuery.ajax({
    	type: "POST",
    	url: 'index.php',
		data: 'option=com_pago&view=customers&task=getCustomerAccount&userId='+userId+'&async=1',
    	success: function(response){
    		jQuery('.pg_user_register').css('display','none');
    		jQuery('.add_billing_adress').html(response);
    		jQuery('.add_billing_adress').fadeIn();
    	}
	});		
}

jQuery(window).on('load', function(){
	jQuery('select').chosen({
		'disable_search_threshold': 6, 
		'search_contains': true, 
		'width': 'auto' 
	});
});
jQuery(document).on('ready', function(){
	
	jQuery('.pg-sub-categories-list').each(function() {
         if (jQuery(this).text() == "") {
         	jQuery(this).hide();
         }
});
	jQuery('.pg-categories-list i').on('click', function(){
		if(jQuery(this).parent().hasClass('pg-primary-not-chosen')){
			jQuery('.pg-categories-list .pg-primary-category').removeClass('pg-primary-category').addClass('pg-primary-not-chosen');
			jQuery('.pg-categories-list .fa-star').removeClass('fa-star').addClass('fa-star-o');
			

			jQuery(this).parent().removeClass('pg-primary-not-chosen').addClass('pg-primary-category');
			jQuery(this).removeClass('fa-star-o').addClass('fa-star');
			jQuery('.primaryInput').val(jQuery(this).parent().attr('id'));
			
		}
	});
});


jQuery(document).on('click', '.pg-pgcheckbox', function(e){
	if(e.srcElement.localName == 'label'){
		var val = jQuery(this).find('.hiddenCheck').val();
		if(val == '0'){
			jQuery(this).find('.hiddenCheck').val('1');
			
		}
		else{
			jQuery(this).find('.hiddenCheck').val('0');
		}
	}
});

jQuery(function(){
    jQuery(".onoffswitch input:checkbox").on("change", function(){
    	var input  = jQuery(this);
    	var hidden = input.prev("input:hidden");
    	
    	var values = hidden.attr("data-values").split(",");
    	
        var value = (input.is(":checked")) ? values[0] : values[1];
        
        hidden.val(value);
    });
});