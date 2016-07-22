jQuery(document).ready(function(){
	jQuery(document).on('click','.modal-backdrop',function(){
		jQuery('.modal').modal('hide');
	});
	/* Chosen */	
	
	/* Media Images */
	jQuery('.pg-mod-products .pg-mod-product-image-block img:first-child').addClass('active-image');

	jQuery(document).on('mouseenter', '.pg-mod-products .pg-mod-product', function(){
		var obj = jQuery(this);
		var length = obj.find('.pg-mod-product-image-block img').length;
		if (length > 1){
			var index = obj.find('.pg-mod-product-image-block img.active-image').index()+1;
			obj.attr('rel','1');
			changeImage(index,length,obj);
		}
	})
	
	function changeImage(index,length,obj){
		setTimeout(function(){
			nextind = index+1;
			if (index == length) nextind = 1;
			obj.find('.pg-mod-product-image-block img:nth-child('+index+')').animate({'opacity':0},300).removeClass('active-image');
			obj.find('.pg-mod-product-image-block img:nth-child('+(nextind)+')').animate({'opacity':0.99},300).addClass('active-image');
			if (index == length) index = 1;
			else index++;	
			if(obj.attr('rel')=='1') changeImage(index,length,obj)
		},1000)	
	}

	jQuery(document).on('mouseleave', '.pg-mod-products .pg-mod-product', function(){
		jQuery(this).attr('rel','0');
	});

	/* Downloads */
	jQuery(document).on('click', '.pg-mod-products .pg-mod-product-downloads-block > a', function(){
		if(jQuery(this).parent().hasClass('open')){
			jQuery(this).parent().removeClass('open');
		}
		else{
			jQuery(this).parent().addClass('open');	
		}
		jQuery(this).siblings(".pg-mod-product-downloads" ).slideToggle( "fast");
	})

	jQuery(document).on('click', function(e){
        if (jQuery('.pg-mod-product-downloads-block').hasClass('open')){
            if(jQuery(e.target).parents('.pg-mod-product-downloads').length==0 
            && !jQuery(e.target).hasClass('pg-mod-product-downloads') 
        	&& jQuery(e.target).parents('.pg-mod-product-downloads-block').length==0 
        	&& !jQuery(e.target).hasClass('pg-mod-product-downloads-block')){
      			jQuery(".pg-mod-products .pg-mod-product-downloads-block.open .pg-mod-product-downloads").slideToggle( "fast");
      			jQuery('.pg-mod-products .pg-mod-product-downloads-block').removeClass('open')
            }
        }
    })
	/* Attributes */

	jQuery(document).on('change', '.pg-mod-products .pg_attr_options[attrtype="0"][attrdisplaytype="0"] select', function(){
		var obj = jQuery(this).siblings('.chosen-container').find('.chosen-single>span');
		var obj_class = jQuery(this).find('option:selected').attr('rel');
		obj.attr('class','');
		obj.addClass('pg-color-'+obj_class);
	})

	

	jQuery(document).on('click','.product-container .pg-addtocart-mod-products', function(event){
		event.preventDefault();
		jQuery('#pg-item-details .pg-notice').css('display','none');
		
		var btn = jQuery(this);

		submit = false;
		var selected_attributes = new Object();
		
		selectedVarationid = false;

		if(jQuery(this).parents('.product-container').attr('selectedVaration') > 0){
			selectedVarationid = parseInt(jQuery(this).parents('.product-container').attr('selectedVaration'));
		}

		if(selectedVarationid){ // if preselected varation set attributes
			selected_attributes = new Object();
			jQuery.ajax({
						type: 'POST',
						url: 'index.php',
						async: false,
						data: ({
							option: "com_pago",
							view: "cart",
							task : "getVaration",
							dataType: 'json',
							varationId: selectedVarationid,
						}),
						success: function( response ) {
							if(response){
								result = JSON.parse(response);
								for (var key in result) {// key = attribute,result[key] = option
									selected_attributes[key] = result[key];
								}
							}	
						}
					});	
		}
		else{
			jQuery(this).parents('.product-container').find(".pg-attribute-product-container").each(function() {
				var attr = jQuery(this).find('.pg_attr_options');
				if(attr.attr('attrdisplaytype')=='0' && attr.find('select').val()!='0'){
					selected_attributes[attr.attr('attr_id')] = attr.find('select').val();
				}else if(attr.attr('attrdisplaytype')=='1'){
					attr.find('input').each(function(){
						if(jQuery(this).val()=='1'){
							selected_attributes[attr.attr('attr_id')] = jQuery(this).attr('opt_id');
						}
					})
				}else if(attr.attr('attrdisplaytype')=='2' && attr.find('input:checked').length && attr.find('input:checked').val()!='0'){
					selected_attributes[attr.attr('attr_id')] = attr.find('input:checked').val();
				}
			});
		}

		var selected_attribute_length = 0
		for (sa in selected_attributes){
			if(typeof selected_attributes[sa] == 'function') continue;
			selected_attribute_length ++
		}
		
		if(selected_attribute_length == 0){
			submit = true;
		}

		var item_varations = '';
		$itemID = jQuery(this).parents('.product-container').attr('itemid');
		if(!$itemID) item_varations = jQuery('#item_varations').val();
		else item_varations = jQuery(this).parents('.product-container').find('#item_varations_'+$itemID).val();
		

		var varId = false;
		if(selected_attribute_length > 0){
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				async: false,
				data: ({
					option: "com_pago",
					view: "cart",
					task : "checkVarationExist",
					dataType: 'json',
					itemId: $itemID,
					selected_attributes: selected_attributes,
				}),
				success: function( response ) {
					if(response){
						result = JSON.parse(response);
						if(result.success == "success")
						{	
							varId = result.varationId;
							submit = true;
						}
					}
				}
			});
		}
		
		//jQuery('#pg-item-details .pg-notice').css('display','block');
		
		var obj = jQuery(this);
		if(!submit){
			obj.parent().parent().find('.pg-addtocart-success-text').html($PRODUCT_NOT_EXIST);
			obj.parent().parent().find('.pg-addtocart-success-block').fadeIn('fast');
		}
		else{
			qty = jQuery(this).parents('.product-container').find('.pg-item-opt-qty').val();

			name = 'attrib';
			var results = new Object();
    		var items = jQuery(this).parents('.product-container').find('[name^='+name+']');
    		
    		jQuery.each(items, function(index, value) {
    			if(jQuery(value).attr('type')=='radio'){
					value = jQuery(value);
        			id = value.attr('name').replace('attrib[','').replace(']','');
        			if(value.is(':checked'))
        				results[id] = value.val();
    			}
    			else{
					value = jQuery(value);
					name = value.attr('name');
					if(name.indexOf('selected')!='-1'){

						id = name.replace('attrib[','');
						ind = id.indexOf(']');
						var f = id.substr(0,ind)
						if(typeof results[f] === "undefined")
							results[f] = new Object();
						id = id.substr(ind+2,id.length)
						ind = id.indexOf(']');
						var s = id.substr(0,ind);
						if(typeof results[f][s] === "undefined")
							results[f][s] = new Object();
						id = id.substr(ind+2,id.length)
						id = id.replace(']','');
						results[f][s][id] = value.val();
					}else{
						id = name.replace('attrib[','').replace(']','');
        				results[id] = value.val();
					}
    			}
    		});
    		
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				async: false,
				data: ({
					option: "com_pago",
					view: "cart",
					task : "add",
					attrib: results,
					id: $itemID,
					qty: qty,
					varId: varId,
				}),
				success: function( response ) {
					if(response){
						result = JSON.parse(response);
						if(result.status == "success"){	
							if ( btn.data("jumpToCheckout") !== undefined )
								return window.location.href = btn.data("jumpToCheckout");
							
							jQuery('.pg-cart-container .pg-cart-quantity').html(result.total_qty);	
							jQuery('.pg-cart-container .pg-cart-total').html(result.total_price);
							
							obj.parent().parent().find('.pg-addtocart-success-text').html(result.message);
							obj.parent().parent().find('.pg-addtocart-success-block').fadeIn('fast');

							pgQuickCart();
						}
					}
				}
			});	
		}
	});

	//rating
	jQuery(document).on('click', '.mod-loginForRate', function(){
		jQuery('#mod-login-modal').modal();
	})

	jQuery(document).on('mouseover','.pg-mod-product-rate ul li',function(){
		if (jQuery(this).parent().hasClass('rated')){

		}
		else{
			jQuery(this).parent().find('li').removeClass('active');

			var index = jQuery(this).index()+1;
			while(index > 0){
				jQuery(this).parent().find('li:nth-child('+index+')').addClass('active');
				index--;
			}
		}
	})
	
	jQuery(document).on('mouseleave','.pg-mod-product-rate ul li',function(){
		if (jQuery(this).parent().hasClass('rated')){

		}
		else{
			jQuery(this).parent().find('li').removeClass('active');
		}
	})


	jQuery(document).on('click','.pg-mod-product-rate ul li a',function(){
		if (jQuery(this).parent().parent().hasClass('rated')){
			return;
		}

		itemId = jQuery(this).parent().parent().parent().attr('item_id');
		rating = jQuery(this).attr('rating');
		obj = jQuery(this);
		jQuery.ajax({
	        type: "POST",
	        url: 'index.php',
			data: 'option=com_pago&view=account&task=rate&rating='+rating+'&itemId=' + itemId+ '&async=1',
	        success: function(response){
   				if(response){
					var result = JSON.parse(response);
					if(result.status == 0){//user not logged in
						obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').fadeIn('fast');
						obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').html('<a class="mod-loginForRate" href="javascript:void();">'+result.message+'</a>');

						// setTimeout(function(){
						// 	obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').fadeOut('fast');
						// 	obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').html('');
						// }, 2000);
					}
					if(result.status == 1){//user voted
						obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').fadeIn('fast');
						obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').html(result.message);

						setTimeout(function(){
							obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').fadeOut('fast');
							obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').html('');
						}, 2000);
					}
					if(result.status == 2){//thanks for vote
						obj.parent().parent().find('li').removeClass('rated_star').each(function(){
							if(jQuery(this).index()+1 <= result.rate ){
								jQuery(this).addClass('rated_star');
							}
						})
						obj.parent().parent().addClass('rated');
						obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').fadeIn('fast');
						obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').html(result.message);

						setTimeout(function(){
							obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').fadeOut('fast');
							obj.parents('.pg-mod-product-rate').find('.pg-mod-product-rate-result').html('');
						}, 2000);	
					}
				}
	        }
	    });
	})

	if (jQuery('.pg-mod-products').hasClass('pg-mod-product-vertical-slide') && jQuery('.pg-mod-products').width() < 768){
		jQuery('.pg-mod-products').addClass('full_width');
	}

	jQuery(document).on('click', '.pg-addtocart-success-block-close', function(){
    	jQuery(this).parent().fadeOut('fast');
    })

    if (slideType == 'horizontal'){
    	columnCount = 0;
		if (jQuery(window).width() >= 1200){
			columnCount = lg;
		}
		else if (jQuery(window).width() >= 992 && jQuery(window).width() <= 1199){
			columnCount = md;
		}
		else if (jQuery(window).width() >= 768 && jQuery(window).width() <= 991){
			columnCount = sm;
		}
		else if (jQuery(window).width() <= 767){
			columnCount = xs;
		}

		containerWidth = jQuery('.pg-mod-products .swiper-container').width();
		jQuery('.pg-mod-products .swiper-container .swiper-slide').css('width', containerWidth/columnCount);
	}
})

jQuery(window).resize(function(){
	if (jQuery('.pg-mod-products').hasClass('pg-mod-product-vertical-slide')){
		if (jQuery('.pg-mod-products').width() < 768){
			jQuery('.pg-mod-products').addClass('full_width');
		}
		else{
			if (jQuery('.pg-mod-products').hasClass('full_width')){
				jQuery('.pg-mod-products').removeClass('full_width');
			}	
		}	
	}
})

jQuery(window).load(function(){
	if (jQuery('.pg-mod-products').hasClass('pg-mod-product-vertical-slide')){
		if (jQuery('.pg-mod-products').width() < 768){
			jQuery('.pg-mod-products').addClass('full_width');
		}
		else{
			if (jQuery('.pg-mod-products').hasClass('full_width')){
				jQuery('.pg-mod-products').removeClass('full_width');
			}	
		}	
	}
})


//Swiper
jQuery(window).load(function(){		
	var sliderViewCount = 1;
	if (slideType == 'horizontal'){
		if (jQuery(window).width() >= 1200){
			sliderViewCount = lg;
		}
		else if (jQuery(window).width() >= 992 && jQuery(window).width() <= 1199){
			sliderViewCount = md;
		}
		else if (jQuery(window).width() >= 768 && jQuery(window).width() <= 991){
			sliderViewCount = sm;
		}
		else if (jQuery(window).width() <= 767){
			sliderViewCount = xs;
		}

		var minH = jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').height();
		jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').each(function(){
			if(jQuery(this).find('.pg-mod-product-image-block').height()<minH){
				minH = jQuery(this).find('.pg-mod-product-image-block').height();	
			} 
		});

		var paddingTop = parseInt(jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('padding-top'));
		var paddingBottom = parseInt(jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('padding-bottom'));
		var borderTop = parseInt(jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('border-top-width'));
		var borderBottom = parseInt(jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('border-bottom-width'));

		minH += paddingTop+paddingBottom+borderTop+borderBottom;

		jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('height', minH);
		jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block img').css('width', 'auto');
	}

	if(slideType=='vertical'){
		jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').css('height','auto');
		var maxH = 0;
		jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').each(function(){
			if(jQuery(this).height()>maxH) maxH = jQuery(this).height();
		});
		jQuery('.pg-mod-products .swiper-container').css('height',maxH+'px');
		jQuery('.pg-mod-products .swiper-container .swiper-wrapper').css('height',maxH*jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').length+'px');
		jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').css('height',maxH+'px');
	}

	cssWidthAndHeight = false;
	calculateHeight = true;
	if (slideType=='vertical') {
		//cssWidthAndHeight = true;
		calculateHeight = false;
	};
	var mySwiper = new Swiper('.pg-mod-products .swiper-container',{
	    slidesPerView: sliderViewCount,
	    pagination: '.pg-mod-product-slider-pagination',
	    paginationClickable: true,
	    keyboardControl: true,
	    simulateTouch: false,
	    mode: slideType,
	    resizeReInit: true,
	    cssWidthAndHeight: cssWidthAndHeight,
	    calculateHeight: calculateHeight,
	    //useCSS3Transforms: false,
	    loop: false,
	});  

	jQuery(window).resize(function(){
		if(slideType=='vertical'){
			jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').css('height','auto');
			var maxH = 0;
			jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').each(function(){
				if(jQuery(this).height()>maxH) maxH = jQuery(this).height();
			});
			jQuery('.pg-mod-products .swiper-container').css('height',maxH+'px');
			jQuery('.pg-mod-products .swiper-container .swiper-wrapper').css('height',maxH*jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').length+'px');
			jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').css('height',maxH+'px');
		}
		else{
			var sliderViewCount = 0;

			if (jQuery(window).width() >= 1200){
				sliderViewCount = lg;
			}
			else if (jQuery(window).width() >= 992 && jQuery(window).width() <= 1199){
				sliderViewCount = md;
			}
			else if (jQuery(window).width() >= 768 && jQuery(window).width() <= 991){
				sliderViewCount = sm;
			}
			else if (jQuery(window).width() <= 767){
				sliderViewCount = xs;
			}
			mySwiper.params.slidesPerView = sliderViewCount;

			jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block img').css('max-height', 'none');
			jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('height', 'auto');
			var minH = jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').height();
			jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide').each(function(){
				if(jQuery(this).find('.pg-mod-product-image-block').height()<minH){
					minH = jQuery(this).find('.pg-mod-product-image-block').height();	
				} 
			});

			var paddingTop = parseInt(jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('padding-top'));
			var paddingBottom = parseInt(jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('padding-bottom'));
			var borderTop = parseInt(jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('border-top-width'));
			var borderBottom = parseInt(jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('border-bottom-width'));

			minH += paddingTop+paddingBottom+borderTop+borderBottom;

			jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block').css('height', minH);
			jQuery('.pg-mod-products .swiper-container .swiper-wrapper .swiper-slide .pg-mod-product-image-block img').css('max-height', '100%');
		}
		mySwiper.reInit();
	});
	
	jQuery(document).on('click', '.pg-mod-product-slider-prev', function(e){
		e.preventDefault();
		mySwiper.swipePrev();
	})

	jQuery(document).on('click', '.pg-mod-product-slider-next', function(e){
		e.preventDefault();
		mySwiper.swipeNext();
	})
	mySwiper.reInit();
	jQuery('.pg-mod-products select').chosen({disable_search_threshold: 10});
})


//END Swiper

function mod_show_attr_option_form($attr_type,$attrId,$displayType,$optionId,$itemID){
	
	var parent = '';
	if($itemID) parent = '#pg-addtocart'+$itemID+' ';
	var attr = jQuery(parent).find('.pg_attr_options[attr_id="'+$attrId+'"]');
	if($attr_type == 1){
		if(attr.find('input.attr_option_'+$optionId+'+span').attr('required') == 'required'){
			if(!attr.find('input.attr_option_'+$optionId+'+span').hasClass('disabledOption')){
				return;
			}
		}	
	}
	
	jQuery(parent).attr('selectedvaration',0);

	var disabled=0;
	
	if($displayType=='1'){
		if(attr.find('input.attr_option_'+$optionId).val()=='1'){
			attr.find('input').val('0');
		}else{
			attr.find('input').val('0');
			attr.find('input.attr_option_'+$optionId).val('1');
		}
		if(attr.find('input.attr_option_'+$optionId+'+span').hasClass('disabledOption')) disabled = 1;
	}
	if($displayType=='2'){
		attr.find('input').prop('checked',false);
		attr.find('input.attr_option_'+$optionId).prop('checked',true);
		if(attr.find('input.attr_option_'+$optionId+'+span').hasClass('disabledOption')) disabled = 1;
	}
	if($displayType=='0'){
		var obj = attr.find('select option:selected');
		if(obj.hasClass('disabledOption')) disabled = 1;
		$optionId = obj.attr('attr_option');
	}

	if(disabled){
		var item_varations = new Object();
		if(!$itemID) item_varations = jQuery('#item_varations').val();
		else item_varations = jQuery(parent+'#item_varations_'+$itemID).val();
		if(typeof item_varations === "undefined") item_varations = new Object();
		attribute = $attrId;
		if(!$itemID) mod_enableAnyVariation($attrId,$optionId,item_varations);
		else mod_enableAnyVariation($attrId,$optionId,item_varations,$itemID);
		if(!$itemID) mod_considerPrice($optionId);
		else mod_considerPrice($optionId,$itemID);
		
		return;
	}
	if(!$itemID) mod_considerPrice($optionId);
	else mod_considerPrice($optionId,$itemID);
}
function mod_enableAnyVariation($attrId, $optionId,variation,$itemID){

	variation = JSON.parse(variation);
	var varID = -1;
	selected_attributes = new Object();
	selected_attributes[$attrId] = $optionId;
	for (key in variation) {	
		var varationAttributes = variation[key].attributes;
		for (ak in varationAttributes){
			if(typeof varationAttributes[ak] == 'function') continue;

			for (saval in selected_attributes){
				
				var vAttrId = varationAttributes[ak].attribute.id; // var attribute ID
				var vOptionId = varationAttributes[ak].option.id; // var attribute option ID

				var saAttrId = saval; // selected attribute ID
				var saOptionId = selected_attributes[saval]; // selected attribute option ID

				
				if(saAttrId == vAttrId && saOptionId == vOptionId){
					varID = key;
					break;
				}
			}
			
		}
	}
	var parent = '';
	if($itemID) parent = '#pg-addtocart'+$itemID+' ';
	
	jQuery(parent+' .pg-attribute-product-container .pg_attr_options').each(function(){
		if(jQuery(this).attr('attrdisplaytype')=='0'){
			jQuery(this).find('select').val('0');
			jQuery(this).find('select option').removeClass('disabledOption');
			jQuery(this).find('select+.chosen-container .chosen-single>span').removeAttr('class');
			jQuery(this).find('select+.chosen-container .chosen-results li').removeClass('disabledOption');
			jQuery(this).find('select+.chosen-container .chosen-single>span').html(jQuery(this).find('select option:selected').html());
			jQuery(this).find('select').trigger('chosen:update');
		}else{
			jQuery(this).find('input').prop('checked',false);
			jQuery(this).find('input').prop('disabled',false);
			if(jQuery(this).attr('attrdisplaytype')!='2') jQuery(this).find('input').val('0');
			jQuery(this).find('input+span').removeClass('active');
			jQuery(this).find('span').removeClass('disabledOption');
		}
	});
	for(i=0;i<variation[varID]['attributes'].length;i++){
		var attr_id = variation[varID]['attributes'][i]['attribute']['id'];
		var opt_id = variation[varID]['attributes'][i]['option']['id'];
		type = jQuery(parent+' .pg_attr_'+attr_id).attr('attrdisplaytype');
		if(type=='0'){
			jQuery(parent+' .pg_attr_'+attr_id+' select').val(opt_id);
			jQuery(parent+' .pg_attr_'+attr_id+' select').trigger('chosen:update');
			var rel = jQuery(parent+' .pg_attr_'+attr_id+' select option:selected').attr('rel');
			if(jQuery(parent+' .pg_attr_'+attr_id+' select option:selected').hasClass('pg-color-'+rel)){
				jQuery(parent+' .pg_attr_'+attr_id+' select+.chosen-container .chosen-single span').html(rel).removeAttr('class').addClass('pg-color-'+rel);
			}else{
				jQuery(parent+' .pg_attr_'+attr_id+' select+.chosen-container .chosen-single span').html(rel);
			}
		}
		if(type=='1'){
			jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id).prop('checked',true);
			jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id).val('1');
			jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id+'+span').addClass('active');
			jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id+'+span').attr('onclick',jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id+'+span').attr('rel')).removeAttr('rel');
		}
		if(type=='2'){
			jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id).prop('checked',true);
			jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id).val(opt_id);
			jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id+'+span').addClass('active');
		}
	}
}
function mod_preselectVaration(varationId,itemID,calculatePrice){
	setTimeout(function(){
		jQuery('#pg-addtocart'+itemID).attr('selectedVaration',varationId);
		if(calculatePrice){
			mod_considerPrice(false,itemID);
		}
	},300);
}
function mod_considerPrice(changePhotoOptionId,$itemID){
	var parent = '';
	if($itemID) parent = '#pg-addtocart'+$itemID+' ';

	if(!$itemID){
		itemId = jQuery("#pg-item").attr('itemId'); 
	}else{
		itemId = $itemID;
	}

	itemQty = jQuery(parent+".pg-item-opt-qty").val();
	if(typeof itemQty==='undefined') itemQty=0;
	else itemQty = parseInt(itemQty);

	selectedVarationid = false;
	if(jQuery(parent).attr('selectedVaration') > 0){
		selectedVarationid = parseInt(jQuery(parent).attr('selectedVaration'));
	}

	if(selectedVarationid){ // if preselected varation set attributes
		selected_attributes = new Object();
		jQuery.ajax({
					type: 'POST',
					url: 'index.php',
					async: false,
					data: ({
						option: "com_pago",
						view: "cart",
						task : "getVaration",
						dataType: 'json',
						varationId: selectedVarationid,
					}),
					success: function( response ) {
						if(response){
							result = JSON.parse(response);
							for (var key in result) {// key = attribute,result[key] = option
								selected_attributes[key] = result[key];
								mod_selectAttributeOption(key,result[key],itemId);
							}
						}	
					}
				});	
	}else{
		selected_attributes = new Object();
		jQuery(parent+".pg-attribute-product-container").each(function() {
			var attr = jQuery(this).find('.pg_attr_options');
			if(attr.attr('attrdisplaytype')=='0' && attr.find('select').val()!='0'){
				selected_attributes[attr.attr('attr_id')] = attr.find('select').val();
			}else if(attr.attr('attrdisplaytype')=='1'){
				attr.find('input').each(function(){
					if(jQuery(this).val()=='1'){
						selected_attributes[attr.attr('attr_id')] = jQuery(this).attr('opt_id');
					}
				})
			}else if(attr.attr('attrdisplaytype')=='2' && attr.find('input:checked').length && attr.find('input:checked').val()!='0'){
				selected_attributes[attr.attr('attr_id')] = attr.find('input:checked').val();
			}
		});
	}
	var item_varations = new Object();
	
	if(!$itemID) {
		item_varations = jQuery('#item_varations').val();
	}else{
		item_varations = jQuery(parent+'#item_varations_'+$itemID).val();
	} 

	if(typeof item_varations === "undefined") {
		item_varations = new Object();
	}

	var is_variation=0;

	if(!$itemID){
		is_variation = mod_hideExcessAttr(item_varations,selected_attributes);
	}else{
		is_variation = mod_hideExcessAttr(item_varations,selected_attributes,$itemID);
	}

	var len = jQuery.map(selected_attributes, function(n, i) { return i; }).length;
	if(len > 0 && is_variation == 0){
		jQuery(parent).attr('selectedVaration',-1);
	}

	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago",
			view: "cart",
			task : "considerPrice",
			dataType: 'json',
			itemId: itemId,
			selected_attributes:selected_attributes,
			itemQty:itemQty,
		}),
		success: function( response ) {
			if(response){
				result = JSON.parse(response);
				jQuery(parent+".pg-mod-product-price .pg-mod-product-real-price").html(result.price);
				jQuery(parent+".pg-mod-product-addtocart-price .pg-mod-product-real-price").html(result.price);

				jQuery(parent+".pg-mod-product-sku .pg-mod-product-sku-code").html(result.sku);
				
				jQuery(parent+".pg-mod-product-stock span:last-child").html(result.limit);

				jQuery(parent+".pg-mod-product-title h1").html(result.name);
				if(jQuery(parent+".pg-mod-product-title a").length)
					jQuery(parent+".pg-mod-product-title a").html(result.name);
				else
					jQuery(parent+".pg-mod-product-title").html(result.name);


				changePhotoIds = false;
				var changePhotoId = 0;
				if(typeof result.varationId !== "undefined"){ // check if varation exist
					if(result.varationDefault != 1){
						changePhotoId = result.varationId;
						changePhotoType = "varation";
					}else{
						changePhotoId = false;
					}
					jQuery('#pg-addtocart'+itemId).attr('selectedVaration',result.varationId);		
				}

				// if(changePhotoId){
				// 	/// get photo start
				// 	jQuery.ajax({
				// 		type: 'POST',
				// 		url: 'index.php',
				// 		data: ({
				// 			option: "com_pago",
				// 			view: "cart",
				// 			task : "changeImage",
				// 			dataType: 'json',
				// 			itemId: itemId,
				// 			changePhotoId: changePhotoId,
				// 			changePhotoType:changePhotoType,
				// 			changePhotoIds:changePhotoIds
				// 		}),
				// 		success: function( response ) {
				// 			if(response){
				// 				result = JSON.parse(response);
				// 				//stex harcnel Ashotin en pah@
				// 				if(result.status == 'success'){
				// 					if(jQuery(parent+'.pg-mod-product-image-block img').attr('src') != result.imagePath){
				// 					    jQuery(parent+'.pg-mod-product-image-block img').css('opacity', 0);
				// 					    jQuery(parent+'.pg-mod-product-image-block').addClass('loading');
				// 					    var changed_image = new Image();
				// 					    changed_image.onload = function () {
				// 					        jQuery(parent+'.pg-mod-product-image-block img').attr('src', this.src);
				// 					        jQuery(parent+'.pg-mod-product-image-block img').stop(true, false).animate({opacity:1},500);
									        
				// 					        jQuery(parent+'.pg-mod-product-image-block').removeClass('loading');
				// 					    }
				// 					    changed_image.src = result.imagePath;
				// 					}
				// 				}else{
				// 					if(jQuery(parent+'.pg-mod-product-image-block img').attr('src') != jQuery(parent+'ul.pg-image-thumbnails li:first-child img').attr('fullurl')){
				// 					    jQuery(parent+'.pg-mod-product-image-block img').css('opacity', 0);
				// 					    jQuery(parent+'.pg-mod-product-image-block').addClass('loading');
				// 					    var changed_image = new Image();
				// 					    changed_image.onload = function () {
				// 					        jQuery(parent+'.pg-item-images-con > img').attr('src', this.src);
				// 					        jQuery(parent+'.pg-item-images-con > img').stop(true, false).animate({opacity:1},500);
									        
				// 					        jQuery(parent+'.pg-item-images-con').removeClass('loading');
				// 					    }
				// 					    changed_image.src = jQuery(parent+'ul.pg-image-thumbnails li:first-child img').attr('fullurl');
				// 					}	
				// 				}
				// 			}	
				// 		}
				// 	});
				// }else if(jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li').length && jQuery(parent+'.pg-item-images-con > img').attr('src')!=jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li:first-child img').attr('fullurl')){
					
				// 	jQuery(parent+'.pg-item-images-con > img').css('opacity', 0);
				//     jQuery(parent+'.pg-item-images-con').addClass('loading');
				//     var changed_image = new Image();
				//     changed_image.onload = function () {
				//         jQuery(parent+'.pg-item-images-con > img').attr('src', this.src);
				//         jQuery(parent+'.pg-item-images-con > img').stop(true, false).animate({opacity:1},500);
				//         jQuery(parent+'.pg-item-images-con').removeClass('loading');
				//     }
				//     changed_image.src = jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li:first-child img').attr('fullurl');
				// }
			}		
		}
	});
	return false;
}
function mod_selectAttributeOption(attr_id,opt_id,$itemID){
	var parent = '';
	if($itemID) parent = '#pg-addtocart'+$itemID+' ';

	type = jQuery(parent+' .pg_attr_'+attr_id).attr('attrdisplaytype');

	if(type=='0'){
	jQuery(parent+' .pg_attr_'+attr_id+' select').val(opt_id).trigger("chosen:updated");
		var rel = jQuery(parent+' .pg_attr_'+attr_id+' select option:selected').attr('rel');
		if(jQuery(parent+' .pg_attr_'+attr_id+' select option:selected').hasClass('pg-color-'+rel)){
			jQuery(parent+' .pg_attr_'+attr_id+' select+.chosen-container .chosen-single span').html(rel).removeAttr('class').addClass('pg-color-'+rel);
		}else{
			jQuery(parent+' .pg_attr_'+attr_id+' select+.chosen-container .chosen-single span').html(rel);
		}

	}
	if(type=='1'){
		jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id).prop('checked',true);
		jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id).val('1');
		jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id+'+span').addClass('active');
	}
	if(type=='2'){
		jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id).prop('checked',true);
		jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id).val(opt_id);
		jQuery(parent+' .pg_attr_'+attr_id+' input.attr_option_'+opt_id+'+span').addClass('active');
	}
}
function mod_hideExcessAttr(item_varations,selected_attributes,$itemID)
{	
	var parent = '';
	if($itemID) parent = '#pg-addtocart'+$itemID+' ';

	if(!$itemID) mod_showAllAttributeOption();
	else mod_showAllAttributeOption($itemID);
		
	//var varLength = jQuery.map(item_varations, function(n, i) { return i; }).length;

	if(item_varations!=''){
		item_varations = JSON.parse(item_varations);
	}else{
		item_varations = [];
	}
	var selected_options = [];
	for ( so in selected_attributes ){

		if(typeof selected_attributes[so] == 'function') continue;
		
		selected_options[selected_attributes[so]] = 1; 
	}
	var is_variation = 0;
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		async: false,
		data: ({
			option: "com_pago",
			view: "cart",
			task : "checkVarationExist",
			dataType: 'json',
			itemId: $itemID,
			selected_attributes: selected_attributes,
		}),
		success: function( response ) {
			if(response){
				result = JSON.parse(response);
				if(result.success == "success")
				{	
					is_variation = 1;
				}
			}
		}
	});

	if( true ){//Object.keys(selected_attributes).length != 0 ){
		jQuery(parent+".pg-mod-product-attributes .pg_attr_options").each(function() {

	     	var attrtype = jQuery(this).attr('attrtype');
	     	var attrdisplaytype = jQuery(this).attr('attrdisplaytype');
	     	var attrId = jQuery(this).attr('attr_id');
	     	var resSelected_attributes = jQuery.extend(true, {}, selected_attributes);

			jQuery(this).find(".pg_attribute_option").each(function() {
				var possible = 0;
				if(attrdisplaytype == 0){
					var optionID = jQuery(this).val();
				}
				if(attrdisplaytype == 1){
					var optionID = jQuery(this).attr('attr_option');
				}
				if(attrdisplaytype == 2){
					var optionID = jQuery(this).val();
				}
				if(optionID==0) return;
				if(Object.keys(resSelected_attributes).length === 0) possible = 1;
				
				if(Object.keys(resSelected_attributes).length === 1 && typeof resSelected_attributes[attrId] !== 'undefined'){
					possible = 1;
				}

				if(possible != 1){
					resSelected_attributes[attrId]  = optionID;
					for (key in item_varations) {	
						var varationAttributes = item_varations[key].attributes;
						var resSelected_attributes_to_check =  jQuery.extend(true, {}, resSelected_attributes);
						for (ak in varationAttributes){
							if(typeof varationAttributes[ak] == 'function') continue;

							for (rstval in resSelected_attributes_to_check){
								
								var vAttrId = varationAttributes[ak].attribute.id; // var attribute ID
								var vOptionId = varationAttributes[ak].option.id; // var attribute option ID

								var saAttrId = rstval; // selected attribute ID
								var saOptionId = resSelected_attributes_to_check[rstval]; // selected attribute option ID

								
								if(saAttrId == vAttrId && saOptionId == vOptionId){
									delete resSelected_attributes_to_check[rstval];
								}
							}
							
						}
						if(Object.keys(resSelected_attributes_to_check).length === 0){
							possible=1;
							break;
						}
					}
				}

				if(attrdisplaytype == 0){
					var value = jQuery(this).val();
					if(value != 0 && !selected_options[value]){
						if(possible){
							jQuery(this).css("display", "block");
							jQuery(this).parent().trigger("chosen:updated");
						}else{
							jQuery(this).removeClass('disabledOption');
							jQuery(this).addClass("disabledOption");
							jQuery(this).parent().trigger("chosen:updated");
						}
					}
				}
				if(attrdisplaytype == 1){
					var value = jQuery(this).attr('attr_option');
					if(!selected_options[value]){
						if(possible){
							jQuery(this).removeClass("disabledOption");
							if(jQuery(this).attr('rel')){
								jQuery(this).attr('onclick',jQuery(this).attr('rel')).removeAttr('rel');
							}
						}else{
							jQuery(this).addClass("disabledOption");
							if(typeof jQuery(this).attr('onclick') !== "undefined"){
								jQuery(this).attr('rel',jQuery(this).attr('onclick')).removeAttr('onclick');
							}
						}
					}
				}
				if(attrdisplaytype == 2){
					var value = jQuery(this).val();
					if(!selected_options[value]){

						if(possible){
							jQuery(this).removeClass("disabledOption");
							jQuery(this).next( "span" ).removeClass("disabledOption");
							jQuery(this).next( "span" ).next( "span" ).removeClass("disabledOption");
							jQuery(this).next( "span" ).next( "span" ).next( "span" ).removeClass("disabledOption");
							jQuery(this).attr('disabled', false);
							if(jQuery(this).attr('rel')){
								rel = jQuery(this).attr('rel');
								jQuery(this).attr('onclick',rel).removeAttr('rel');
								jQuery(this).next( "span" ).attr('onclick',rel).removeAttr('rel');
								jQuery(this).next( "span" ).next( "span" ).attr('onclick',rel).removeAttr('rel');
								jQuery(this).next( "span" ).next( "span" ).next( "span" ).attr('onclick',rel).removeAttr('rel');
							}
						}else{
							jQuery(this).addClass("disabledOption");
							jQuery(this).attr('disabled', true);
							jQuery(this).next( "span" ).addClass("disabledOption");
							jQuery(this).next( "span" ).next( "span" ).addClass("disabledOption");
							jQuery(this).next( "span" ).next( "span" ).next( "span" ).addClass("disabledOption");
							if(typeof jQuery(this).attr('onclick') !== "undefined"){
								oclick = jQuery(this).attr('onclick');
								jQuery(this).attr('rel',oclick).removeAttr('onclick');
								jQuery(this).next( "span" ).attr('rel',oclick).removeAttr('onclick');
								jQuery(this).next( "span" ).next( "span" ).attr('rel',oclick).removeAttr('onclick');
								jQuery(this).next( "span" ).next( "span" ).next( "span" ).attr('rel',oclick).removeAttr('onclick');
							}
						}
					}
				}

			}) 
		});
	}
	return is_variation;
}
jQuery(document).on('click','.pg-mod-product .pg_attr_options[attrdisplaytype="2"]>span',function(){
	if(jQuery(this).parent().attr('attrtype')=='0'){
		if(jQuery(this).hasClass('pg_color_option_list')){
			if(jQuery(this).hasClass('active')) return;
			if(jQuery(this).hasClass('disabledOption')) return;
			jQuery(this).parent().find('span.active').removeClass('active');
			jQuery(this).addClass('active');
		}else{
			if(jQuery(this).prev().hasClass('active')) return;
			if(jQuery(this).prev().hasClass('disabledOption')) return;
			jQuery(this).parent().find('span.active').removeClass('active');
			jQuery(this).prev().addClass('active')
		}
	}else{
		if(jQuery(this).hasClass('active')) return;
		if(jQuery(this).hasClass('disabledOption')) return;
		jQuery(this).parent().find('span.active').removeClass('active');
		jQuery(this).addClass('active')
	}
});
jQuery(document).on('click','.pg-mod-product .pg_attr_options[attrdisplaytype="1"]>span',function(){
	if(jQuery(this).hasClass('disabledOption')) return;
	if(jQuery(this).hasClass('active')) {
		if(jQuery(this).attr('required') != 'required'){
			jQuery(this).removeClass('active');
		}
		return;
	}
	jQuery(this).parent().find('span.active').removeClass('active');
	jQuery(this).addClass('active')
});
jQuery(document).on('click','.pg-mod-product .disabledOption',function(){
	eval(jQuery(this).attr('rel'));
});
function mod_showAllAttributeOption($itemID){
	var parent = '';
	if($itemID) parent = '#pg-addtocart'+$itemID+' ';
	jQuery(parent+".pg-mod-product-attributes .pg_attr_options").each(function() {
     	var attrtype = jQuery(this).attr('attrtype');
     	var attrdisplaytype = jQuery(this).attr('attrdisplaytype');

		jQuery(parent+".pg_attribute_option",this).each(function() {
			//if(!selected_attributes[attrId]){
				if(attrdisplaytype == 0){
					jQuery(this).css("display", "inline");
				}
				if(attrdisplaytype == 1){
					jQuery(this).removeClass("disabledOption");
					if(jQuery(this).attr('rel')){
						jQuery(this).attr('onclick',jQuery(this).attr('rel')).removeAttr('rel');
					}
				}
				if(attrdisplaytype == 2){
					jQuery(this).removeClass("disabledOption");
					jQuery(this).next( "span" ).removeClass("disabledOption");
					jQuery(this).next( "span" ).next( "span" ).removeClass("disabledOption");
					jQuery(this).next( "span" ).next( "span" ).next( "span" ).removeClass("disabledOption");
					jQuery(this).attr('disabled', false);
					if(jQuery(this).attr('rel')){
						rel = jQuery(this).attr('rel');
						jQuery(this).attr('onclick',rel).removeAttr('rel');
						jQuery(this).next( "span" ).attr('onclick',rel).removeAttr('rel');
						jQuery(this).next( "span" ).next( "span" ).attr('onclick',rel).removeAttr('rel');
						jQuery(this).next( "span" ).next( "span" ).next( "span" ).attr('onclick',rel).removeAttr('rel');
					}
				}
			//}
		}) 
	});	
}