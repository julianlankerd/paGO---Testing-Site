(function($) {

	var meth = {
		settings: {
			optionTableId: ".pg-custom-attr-table"
		},
		showAddValue: function(attrId,attrType,itemId) {
			if(jQuery('.add_attribute_value_con .add_attribute_value_container').hasClass('active')){
				return false;
			}
			jQuery('.add_attribute_value_con .add_attribute_value_container').addClass('active');
			optionCount = jQuery('#attr_table_'+attrId+' tbody tr').length;

			jQuery.ajax({

				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "showAddCutsomAttrValue",
					dataType: 'json',
					attrId: attrId,
					attrType: attrType,
					itemId: itemId,
					num: optionCount +1,
					async: "1",
				}),
				success: function( content ) {
					if( content ) {
						result = JSON.parse(content);
						if( result.status == 'showNewValue' ){
							jQuery('.add_attribute_value_con .add_attribute_value_container').html( result.attributeValueHtml );
							jQuery(".add_attribute_value_container tr.pg-table-content").find("select").chosen({'disable_search': true,  'disable_search_threshold': 6});
							jscolor.bind();
							// var newAttributePosition = jQuery('.add_attribute_value_container').offset().top;
							// jQuery('html, body').animate({scrollTop:newAttributePosition}, 'slow');
							jQuery('#addAttributeValue').modal('show');
						}
					}
				}
			});
		},
		showEditValue: function(optionId,itemId) {
			// if(jQuery('.add_attribute_value_con .add_attribute_value_container').hasClass('active')){
			// 	return false;
			// }
			// jQuery('.add_attribute_value_con .add_attribute_value_container').addClass('active');

			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "showEditCutsomAttrValue",
					dataType: 'json',
					optionId: optionId,
					itemId: itemId,
					async: "1",
				}),
				success: function( content ) {
					if( content ) {
						result = JSON.parse(content);
						if( result.status == 'showEditValue' ){
							jQuery('.add_attribute_value_con .add_attribute_value_container').html( result.attributeValueHtml );
							jQuery(".add_attribute_value_container tr.pg-table-content").find("select").chosen({'disable_search': true,  'disable_search_threshold': 6});
							jscolor.bind();
							jQuery('#addAttributeValue').modal('show');

							// files = load_files(result.attributeValueId,'attr_opt');
							// var id = 'images_opt_'+result.attributeValueId;
							// jQuery('#folder_image_editor_'+ result.attributeValueId).append('<ul id=\''+id+'\'></ul>');

							// for(var f in files){
							// 	if(typeof files[f] == 'function') continue;
							// 	if(files[f].length){
							// 		jQuery('#folder_image_editor_'+ result.attributeValueId +' ul#'+id).append('<li data-folder=\"'+result.attributeValueId+'\" data-img=\"'+files[f]+'\" image_type="attr_opt"><div class="image_buttons"><div class="attr_delete_btn" alt="delete"></div><div class="attr_edit_btn" alt="edit"></div></div><img src=\"'+ JURI + 'media/pago/attr_opt/' + result.attributeValueId +'/'+files[f]+'\" /></li>');
							// 	}
							// }

							// createUploadCon(result.attributeValueId);

							// var newAttributePosition = jQuery('.add_attribute_value_container').offset().top;
							// jQuery('html, body').animate({scrollTop:newAttributePosition}, 'slow');
						}
					}
				}
			});
		},
		showForm: function(el) {
			open = true;

			if(el.next('.attr_option_form').hasClass("showAttrForm")){
				open = false;
			}

			meth.closeAllForm();

			if(open){
				el.next('.attr_option_form').removeClass("hideAttrForm");
				el.next('.attr_option_form').addClass("showAttrForm");
				jQuery(el).find(".attr_opt_edit a").html("Save");
			}
		},
		closeAttributeForm: function(){
			jQuery('.add_custom_attribute_con .add_custom_attribute_container').removeClass( 'active' );
			jQuery('.add_custom_attribute_con .add_custom_attribute_container').html( '' );
			jQuery('#addAttribute').modal('hide');
		},
		closeAttributeValueForm: function(){
			jQuery('.add_attribute_value_con .add_attribute_value_container').removeClass( 'active' );
			jQuery('.add_attribute_value_con .add_attribute_value_container').html( '' );
			jQuery('#addAttributeValue').modal('hide');
		},
		closeProductVarationForm: function(){
			jQuery('.add_product_varation_con .add_product_varation_container').removeClass( 'active' );
			jQuery('.add_product_varation_con .add_product_varation_container').html( '' );
			jQuery('#addProductVariation').modal('hide');
		},
		requiredAlert: function(attrID){
			if(attrID == 0){
				alert('Attribute can not be required until you have added option value');
			}
			else{
				jQuery.ajax({
					type: 'POST',
					url: 'index.php',
					data: ({
						option: "com_pago", // global field
						controller: "attributes",
						task : "checkAttrRequiredAlert",
						dataType: 'json',
						attrId: attrID,
					}),
					success: function( response ) {
						if(response == 'success'){
							alert(Joomla.JText.strings.PAGO_ATTRIBUTE_CANT_BE_REQUIRED);
						}
					}
				});
			}
		},
		saveAttribute: function(itemId){
			edit_id   = jQuery("#params_custom_attrubte_edit_id").val();

			var edit_id = edit_id;

			attrType     = jQuery("#params_custom_attrubte_type").val();
			name      	 = jQuery("#params_custom_attrubte_name").val();
			alias     	 = jQuery("#params_custom_attrubte_alias").val();
			showfront      = jQuery("input[name='params[custom_attrubte][showfront]']:checked").val();
			display_type = jQuery("#params_custom_attrubte_display_type").val();
			required = jQuery("input[name='params[options][custom_attrubte][required]']:checked").val();
			if(!validValNotEmpty(name)){
				alert("Attribute name is empty.");
				return false;
			}
			if(required == '1'){
				if(!confirm(Joomla.JText.strings.PAGO_ATTRIBUTE_CHOOSE_REQUIRED)){
					return false;
				}
			}
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "addCustomAttr",
					dataType: 'json',
					itemId: itemId,
					attrType: attrType,
					name: name,
					alias: alias,
					showfront: showfront,
					display_type: display_type,
					edit_id:edit_id,
					required:required,
				}),
				success: function( response ) {
					if(response){
						result = JSON.parse(response);

						if( result.status == 'addNew' ){
							jQuery('#ca_config .pg-table-wrap').append(result.attributeTitle);
							jQuery('#ca_config .pg-table-wrap').append("<div class='pg-pad-20 pg-border'>"+result.attributeHtml+"</div>");
	
							meth.closeAttributeForm();
							var newAttributePosition = jQuery('#attr_table_'+result.attributeId).offset().top;
							jQuery('html, body').animate({scrollTop:newAttributePosition}, 'slow');
						}
						if( result.status == 'edit' ){
							meth.closeAttributeForm();
							var newAttributePosition = jQuery('#attr_table_'+result.attributeId).offset().top;
							// jQuery('html, body').animate({scrollTop:newAttributePosition}, 'slow');
							jQuery('#attr_table_'+result.attributeId+' .attr_name_view').html(result.attributeName);
						}
					}
				}
			});
		},
		saveOption: function(itemId,attrId,status){

			id       	= jQuery("#params_custom_attrubte_value_id").val();
			name     	= jQuery("#params_custom_attrubte_value_name").val();
			type 	 	= jQuery("#params_custom_attrubte_value_type").val();
			price_sign 	= jQuery("#params_custom_attrubte_value_price_sign").val();
			in_stock 	= jQuery("#params_custom_attrubte_value_in_stock").val();
			ordering 	= jQuery("#params_custom_attrubte_value_ordering").val();
			published 	= jQuery("#params_custom_attrubte_value_published").val();
			//preselected = jQuery("#params_custom_attrubte_value_preselected").val();

			var optId = id;
			var attrId = attrId;

			if(!validValNotEmpty(name)){
				alert("Attribute name is empty.");
				return false;
			}

			price_sum  = false;
			price_type = false;

			if(price_sign != 0){
				price_sum = $("#params_custom_attrubte_value_price_sum").val();

				if(!validValNotEmpty(price_sum)){
					alert("Attribute price is empty.");
					return false;
				}

				price_type = $("#params_custom_attrubte_value_price_type").val();
			}else{
				price_sum = '';
			}

			color = false;
			size = false;
			size_type = false;

			if(type == 0){
				color = $("#params_custom_attrubte_value_color").val();
			}
			if(type == 1){
				size = $("#params_custom_attrubte_value_size").val();
				size_type = $("#params_custom_attrubte_value_size_type").val();
			}

			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "addCustomOption",
					dataType: 'json',
					itemId: itemId,
					attrId: attrId,
					id: id,
					opt_type: type,
					name: name,
					price_sign: price_sign,
					in_stock: in_stock,
					price_sum: price_sum,
					price_type: price_type,
					ordering: ordering,
					published: published,
					status: status,
					//preselected: preselected,
					//sku: sku,
					color: color, // color field
					size: size, // size field
					size_type: size_type, // size field
				}),
				success: function( response ) {
					if(response){
						result = JSON.parse(response);
						if( result.status == 'success' ){
							meth.closeAttributeValueForm();
							jQuery("#attr_table_"+attrId+" tbody").append( result.attributeTbody );
						}else{
							meth.closeAttributeValueForm();
							jQuery("#attr_table_"+attrId+" tbody #optionId_"+optId+' td').remove();
							jQuery("#attr_table_"+attrId+" tbody #optionId_"+optId).append( jQuery(result.attributeTbody).html() );
						}
						// var optionPosition = jQuery("#attr_table_"+attrId+" tbody #optionId_"+optId).offset().top;
						// jQuery('html, body').animate({scrollTop:optionPosition}, 'slow');
					}
				}
			});
		},
		saveProductVaration: function(itemId,status){
			id       	= jQuery("#product_varation_id").val();
			name     	= jQuery("#product_varation_name").val();
			price_type 	= jQuery("#product_varation_price_type").val();
			price 	 	= jQuery("#product_varation_price").val();
			sku 		= jQuery("#product_varation_sku").val();
			qty_limit	= jQuery("#product_varation_qty_limit").val();
			qty 		= jQuery("#product_varation_qty").val();
			published 	= jQuery("#product_varation_published").val();
			def		 	= jQuery('input[type=radio][name=default_varation]:checked').val();

			variation_attr = new Object();

			jQuery('.product_varation_form_attr').each(function(){
				if(jQuery(this).val() != 0){
					variation_attr[jQuery(this).attr('id').replace('product_varation_form_attr_','')] = jQuery(this).val();
				}
			});

			if(Object.keys(variation_attr).length === 0){
				alert("Please choose at least one attribute");
				return false;
			}
			if(def != 1 && !jQuery("#images_opt_"+id).has('li').length > 0){
				
			}
			canSave = true;


			if(sku === ""){
                alert("Variation SKU is empty");
                return false;
            }

			if(def == 1){
				jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				async: false,
				data: ({
					option: "com_pago",
					controller: "attributes",
					task : "checkItemDefaultVar",
					dataType: 'json',
					itemId: itemId,
					varId: id,
				}),
				success: function( response ) {
						if(response){
							result = JSON.parse(response);
							if(result.status == 'wrong'){
								if (!confirm(result.message)) { 
									canSave = false;
								}
							}
						}
					}
				});	
			}else{
				jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				async: false,
				data: ({
					option: "com_pago",
					controller: "attributes",
					task : "checkItemSku",
					dataType: 'json',
					itemId: itemId,
					varId: id,
					sku: sku,
				}),
				success: function( response ) {
						if(response){
							result = JSON.parse(response);
							if(result.status == 'wrong'){
								alert(result.message); 
								canSave = false;
							}
						}
					}
				});	
			}

			if(!canSave){
				return false;
			}

			if(!validValNotEmpty(name)){
				alert("Variation name is empty.");
				return false;
			}
                        
            if(price_type != '0' && price === ""){
                alert("Variation price is empty");
                price.val('');
                return false;
            }

            if(price.length>0){
            
				
				if(!price.match(/^[\d.]+$/)){

					alert("Variation price must be digit.");
					return false;
				}
			}
                        
                        

			var id = id;
			var status = status;
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago",
					controller: "attributes",
					task : "addProductVaration",
					dataType: 'json',
					itemId: itemId,
					id: id,
					name: name,
					price_type: price_type,
					price: price,
					sku: sku,
					qty_limit: qty_limit,
					qty: qty,
					published: published,
					variation_attr:variation_attr,
					def:def,
				}),
				success: function( response ) {
					if(response){
						result = JSON.parse(response);
						if(result.condition == 'success'){

							if(result.removeVarationId != 'false'){
								jQuery('#varationId_'+result.removeVarationId).remove();	
							}

							if( status == 'add' ){
								meth.closeAttributeValueForm();
								jQuery(".product_varation_list tbody").append( result.productVarationTr );
							}else{
								meth.closeAttributeValueForm();
								jQuery(".product_varation_list tbody #varationId_"+id+' td').remove();
								jQuery(".product_varation_list tbody #varationId_"+id).append( jQuery(result.productVarationTr).html() );
							}
							meth.closeProductVarationForm();
							var varatopmPosition = jQuery(".product_varation_list tbody #varationId_"+id).offset().top;
							// jQuery('html, body').animate({scrollTop:varatopmPosition}, 'slow');
							bind_publish_varation_buttons();
							bind_preselected_varation_buttons();
						}else{
							alert(result.errorMessage);
							return;
						}
					}
				}
			});
		},
		deleteCustomOption: function( optId ) {
		
			var optId = optId;
		
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "checkCustomOptVar",
					dataType: 'json',
					optId: optId,
				}),
				success: function( response ) {
					var resp = JSON.parse(response);
					if(resp['ifOne'] == '1'){
						if(resp['varations'] == '1'){
							if(!confirm(Joomla.JText.strings.PAGO_ATTRIBUTE_OPTION_HAVE_VARIATION + " " + Joomla.JText.strings.PAGO_ATTRIBUTE_OPTION_DELETE)){
								return false;
							}
						}
						else{
							if(!confirm(Joomla.JText.strings.PAGO_ATTRIBUTE_OPTION_DELETE)){
								return false;
							}
						}
						jQuery.ajax({
							type: 'POST',
							url: 'index.php',
							data: ({
								option: "com_pago", // global field
								controller: "attributes",
								task : "removeCustomOpt",
								dataType: 'json',
								optId: optId,
							}),
							success: function( response ) {
								response = JSON.parse(response);

								if(response.status == 'success'){
									
									jQuery("#optionId_"+optId).remove();
								
								}
							}
						});
					}
					else{
						if(resp['varations'] == '1' && resp['required'] == 1){
							alert(Joomla.JText.strings.PAGO_ATTRIBUTE_OPTION_HAVE_VARIATION + " " + Joomla.JText.strings.PAGO_ATTRIBUTE_IS_REQUIRED);
						}
						else{
							if(resp['required'] == '1'){
								alert(Joomla.JText.strings.PAGO_ATTRIBUTE_IS_REQUIRED);
							}
							else{
								if(resp['varations'] == '1'){
									if(!confirm(Joomla.JText.strings.PAGO_ATTRIBUTE_OPTION_HAVE_VARIATION + " " + Joomla.JText.strings.PAGO_ATTRIBUTE_OPTION_DELETE)){
										return false;
									}
								}
								else{
									if(!confirm(Joomla.JText.strings.PAGO_ATTRIBUTE_OPTION_DELETE)){
										return false;
									}
								}
								jQuery.ajax({
									type: 'POST',
									url: 'index.php',
									data: ({
										option: "com_pago", // global field
										controller: "attributes",
										task : "removeCustomOpt",
										dataType: 'json',
										optId: optId,
									}),
									success: function( response ) {
										response = JSON.parse(response);
										
										if(response.status == 'success'){
											jQuery("#optionId_"+optId).remove();
											meth.closeAttributeForm();
										}
									}
								});
							}
						}
					}
				}
			});

			return false;
		},
		delAttr: function( attrId ) {
			
			if(!confirm(Joomla.JText.strings.PAGO_ATTRIBUTE_HAVE_VARIATION + " " + Joomla.JText.strings.PAGO_ATTRIBUTE_CUSTOM_DELETE)){
				return false;
			};

			var attrId = attrId;
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "removeCustomAttr",
					dataType: 'json',
					attrId: attrId,
				}),
				success: function( response ) {
					response = JSON.parse(response);


					if(response.status == 'success'){
						jQuery("#attr_block_"+attrId).remove();
						jQuery("#attr_table_"+attrId).parent(jQuery("div")).remove();
						jQuery("#attr_table_"+attrId).remove();
					}
				}
			});

			return false;
		},
		deleteProductVaration: function( varationId ) {
			if(!confirm("Delete Variation?")){
				return false;
			};

			var varationId = varationId;
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "removeVaration",
					dataType: 'json',
					varationId: varationId,
				}),
				success: function( response ) {
					if(response == 'success'){
						jQuery("#varationId_"+varationId).remove();
						meth.closeProductVarationForm();
					}
				}
			});
			return false;
		},
		changePublish: function( attrId,itemId ) {

			var itemId = itemId;
			var attrId = attrId;

			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago",
					controller: "attributes",
					task : "publishAttr",
					dataType: 'json',
					attrId: attrId,
					itemId: itemId,
				}),
				success: function( response ) {
					jQuery('#attr_block_'+attrId+' .pg-published-attr').html(response);
				}
			});

			return false;
		},
		changeOptPublish: function( optionId,itemId ) {

			var itemId = itemId;
			var optionId = optionId;

			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago",
					controller: "attributes",
					task : "publishedAttrOption",
					dataType: 'json',
					optionId: optionId,
					itemId: itemId,
				}),
				success: function( response ) {
					jQuery('#optionId_'+optionId+' .pg-published .pg-published-attr-opt').html(response);
				}
			});

			return false;
		},
		editAttr: function( attrId ) {
			if(jQuery('.add_custom_attribute_con .add_custom_attribute_container').hasClass('active')){
				jQuery('.add_custom_attribute_con .add_custom_attribute_container').removeClass( 'active' );
				jQuery('.add_custom_attribute_con .add_custom_attribute_container').html( '' );
			}
			// var newAttributePosition = jQuery('.add_custom_attribute_con').offset().top;
			// jQuery('html, body').animate({scrollTop:newAttributePosition}, 'slow');

			jQuery('.add_custom_attribute_con .add_custom_attribute_container').addClass('active');

			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "showAddCutsomAttr",
					dataType: 'json',
					attrId: attrId,
					async: "1",
				}),
				success: function( content ) {
					if( content ) {
						jQuery('.add_custom_attribute_con .add_custom_attribute_container').html( content );
						jQuery(".add_custom_attribute_container tr.pg-table-content").find("select").chosen({'disable_search': true,  'disable_search_threshold': 6});
						jQuery('#addAttribute').modal('show');

					}
					jQuery('#edit_attribute_id_'+attrId+' #params_custom_attrubte_type').css('display','none');
					jQuery('#edit_attribute_id_'+attrId+' #uniform-params_custom_attrubte_type').unbind( "click" );
					jQuery('#edit_attribute_id_'+attrId+' #uniform-params_custom_attrubte_type').bind('click',function(e){
						alert('You can not change attribute type');
						return false;
					})
				}
			});
		},

	};
	$.customAttribute = function ( method ) {
		if ( meth[method] ) {
			return meth[method].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( typeof method === 'object' || ! method ) {
			meth.init.apply( this, arguments );
		} else {
			$.error( 'Method' + method + ' does not exist on jQuery.customAttribute' );
		}
	};
})(jQuery);

jQuery(document).ready(function(){
	jQuery(document).on('click','#sub-tabs.sub-tab .cutom_attibute_tabs li',function(){
		if(!jQuery(this).hasClass('active')){//attr('tab')
			jQuery('#sub-tabs.sub-tab .cutom_attibute_tabs li').removeClass('active');
			jQuery('#sub-tabs.sub-tab .tab-content .tab-pane').removeClass('active');

			jQuery(this).addClass('active');
			tabId = jQuery('a',this).attr('tab');
			jQuery('#'+tabId).addClass('active');
		}
	})

	jQuery(document).on('click','.images_folder ul li .attr_edit_btn',function(){
		jQuery('.attribute-lightbox #edit_image').attr('src',jQuery(this).parent().parent().find('img').attr('src'));
		jQuery('.attribute-lightbox').attr('data-folder',jQuery(this).parent().parent().attr('data-folder'));
		jQuery('.attribute-lightbox').attr('data-img',jQuery(this).parent().parent().attr('data-img'));
		jQuery('.attribute-lightbox').attr('image_type',jQuery(this).parent().parent().attr('image_type'));
		load_data(jQuery(this).parent().parent().attr('data-folder'),jQuery(this).parent().parent().attr('data-img'),jQuery(this).parent().parent().attr('image_type'));
	});

	jQuery(document).on('click','.images_folder ul li .attr_delete_btn',function(){
		delete_data(jQuery(this).parent().parent().attr('data-folder'),jQuery(this).parent().parent().attr('data-img'),jQuery(this).parent().parent().attr('image_type'));
	});

	jQuery(document).on('click','.attribute-lightbox .lightbox-save',function(){
		jQuery('.lightbox-save').addClass('saving');
		save_data(jQuery('.attribute-lightbox').attr('data-folder'),jQuery('.attribute-lightbox').attr('data-img'),jQuery('#edit_title').val(),jQuery('#edit_alt').val(),jQuery('#edit_desc').val(),jQuery('.attribute-lightbox').attr('image_type'));
	});

	jQuery(document).on('click','.attribute-lightbox',function(e){
		jQuery('.attribute-lightbox').fadeOut();
	});

	jQuery(document).on('click','.lightbox-inner',function(e){
		e.stopPropagation();
	});

	jQuery(document).on('click','.attribute-lightbox .lightbox-cancel',function(e){
		jQuery('.attribute-lightbox').fadeOut();
	});


	/// varation default
	jQuery(document).on('change','input[type=radio][name=default_varation]',function(e){
		changeVarationEditView();	
	});
})
function changeVarationEditView(){
	defVal = jQuery('input[type=radio][name=default_varation]:checked').val();
	itemId = jQuery('#product_varation_form_item_id').val();
		
	if(defVal == 0){ // turn on change
		jQuery('#product_varation_name').val('');
		jQuery("#product_varation_name").removeAttr("disabled");

		jQuery("#product_varation_price_type").removeAttr("disabled");

		jQuery('#product_varation_price').val('');
		jQuery("#product_varation_price").removeAttr("disabled");

		jQuery('#product_varation_sku').val('');
		jQuery("#product_varation_sku").removeAttr("disabled");

		jQuery('#product_varation_qty_limit').parent().css("display", "block");

		if(jQuery('#product_varation_qty_limit').val() == 0){
			jQuery('#product_varation_qty').parent().css("display", "block");
		}

		jQuery('.upload_files_main').css("display", "block");
		jQuery('.images_folder').css("display", "block");

		jQuery('#default_varation_image').css("display", "none");

	}else{ // trun off change
		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=attributes&task=getItemInfo&itemId=' +itemId+ '&async=1',
			success: function( item ) {
				item = JSON.parse(item);

				jQuery('#product_varation_name').val(item.name);
				jQuery("#product_varation_name").prop('disabled', true);

				
				jQuery('#product_varation_price_type [value="1"]').attr('selected',true);
				jQuery('#uniform-product_varation_price_type span').html("Equal to (=)");
				jQuery("#product_varation_price_type").prop('disabled', true);

				jQuery('#product_varation_price').val(item.price);
				jQuery("#product_varation_price").prop('disabled', true);

				jQuery('#product_varation_sku').val(item.sku);
				jQuery("#product_varation_sku").prop('disabled', true);

				jQuery('#product_varation_qty_limit').parent().css("display", "none");
				jQuery('#product_varation_qty').parent().css("display", "none");

				jQuery('.upload_files_main').css("display", "none");
				jQuery('.images_folder').css("display", "none");

				jQuery('#default_varation_image').html(item.image);
				jQuery('#default_varation_image').css("display", "block");
				return;
			}
		});
	}
}
function bind_publish_attr_buttons()
{
	jQuery('.publish-attr-buttons').click(function(){
		id = jQuery(this).attr('rel');
		img = jQuery(this).children().first();
		css = jQuery(img).attr('class');


		if ( 'item-unpublish' == css ) {
			task = 'unpublish';
		} else { // Is unpublished
			task = 'publish';
		}

		el = jQuery(this);
		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=attributes&task=' +task+ '&cid[]=' +id+ '&async=1',
			success: function( content ) {
				if ( content ) {
					jQuery(el).replaceWith( content );
					jQuery('.publish-attr-buttons').unbind('click');
					bind_publish_attr_buttons();
				}
			}
		});
	})
}
function bind_publish_varation_buttons()
{
	jQuery('.publish-varation-buttons').click(function(){
		id = jQuery(this).attr('rel');
		img = jQuery(this).children().first();
		css = jQuery(img).attr('class');


		if ( 'item-unpublish' == css ) {
			task = 'unpublish_var';
		} else { // Is unpublished
			task = 'publish_var';
		}

		el = jQuery(this);
		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=attributes&task=' +task+ '&cid[]=' +id+ '&async=1',
			success: function( content ) {
				if ( content ) {
									
					jQuery(el).replaceWith( content );
					jQuery('.publish-varation-buttons').unbind('click');
					bind_publish_varation_buttons();
				}
			}
		});
	})
}
function bind_preselected_varation_buttons()
{
	jQuery('.preselected-varation-buttons').click(function(){
		id = jQuery(this).attr('rel');
		img = jQuery(this).children().first();
		css = jQuery(img).attr('class');


		if ( 'item-not_selected' == css ) {
			task = 'not_selected';
		} else {
			task = 'preselected';
		}

		el = jQuery(this);
		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=attributes&task=' +task+ '&cid[]=' +id+ '&async=1',
			success: function( content ) {
				if ( content ) {
					jQuery('.pg-preselected .pg-preselected a img').each(function(){
						if(jQuery(this).hasClass('item-not_selected')){
								jQuery(this).attr('src','components/com_pago/css/images/publish_x.png');
								jQuery(this).attr('class','item-preselected');
						}	
					})	
					jQuery(el).replaceWith( content );
					jQuery('.preselected-varation-buttons').unbind('click');
					bind_preselected_varation_buttons();
				}
			}
		});
	})
}
function showAddAttribute( itemId ){
	if(jQuery('.add_custom_attribute_con .add_custom_attribute_container').hasClass('active')){
		jQuery('.add_custom_attribute_con .add_custom_attribute_container').removeClass( 'active' );
		jQuery('.add_custom_attribute_con .add_custom_attribute_container').html( '' );		
	}
	// var newAttributePosition = jQuery('.add_custom_attribute_con').offset().top;
	// jQuery('html, body').animate({scrollTop:newAttributePosition}, 'slow');

	jQuery('.add_custom_attribute_con .add_custom_attribute_container').addClass('active');
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "attributes",
			task : "showAddCutsomAttr",
			dataType: 'json',
			itemId: itemId,
			async: "1",
		}),
		success: function( content ) {
			if( content ) {
				jQuery('.add_custom_attribute_con .add_custom_attribute_container').html( content );
				jQuery(".add_custom_attribute_container tr.pg-table-content").find("select").chosen({'disable_search': true,  'disable_search_threshold': 6});
				jQuery('#addAttribute').modal('show');
			}
			// if ( content ) {
			// 	jQuery(el).replaceWith( content );
			// 	jQuery('.publish-attr-buttons').unbind('click');
			// 	bind_publish_attr_buttons();
			// }
		}
	});
}
function showAddProductVariation( itemId ){
	if(jQuery('.add_product_varation_con .add_product_varation_container').hasClass('active')){
		jQuery('.add_product_varation_con .add_product_varation_container').removeClass( 'active' );
		jQuery('.add_product_varation_con .add_product_varation_container').html( '' );
	}
	var newVarationPosition = jQuery('.add_product_varation_con').offset().top;
	// jQuery('html, body').animate({scrollTop:newVarationPosition}, 'slow');
	jQuery('.add_product_varation_con .add_product_varation_container').addClass('active');

	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "attributes",
			task : "showAddProductVaration",
			dataType: 'json',
			itemId: itemId,
			status: "add",
			async: "1",
		}),
		success: function( response ) {

			if(response){
				result = JSON.parse(response);
				jQuery('.add_product_varation_con .add_product_varation_container').html( result.varationForm );
				jQuery(".add_product_varation_con tr.pg-table-content").find("select").chosen({'disable_search': true,  'disable_search_threshold': 6});
				createVarationUploadCon(result.varationId);
				jQuery('#addProductVariation').modal('show');

				
				jQuery('.pg-row-item-inner.hiddener').each(function(){
					if(jQuery(this).find('select option:selected').val()=="1")
						jQuery(this).next('.to-hide').css('display','none');
					else
						jQuery(this).next('.to-hide').css('display','block');	
				})
				
				jQuery('.pg-row-item-inner.hiddener select').change(function(){
					if(jQuery(this).find('option:selected').val()=="1")
						jQuery(this).parents('.pg-row-item-inner.hiddener').next('.to-hide').css('display','none');
					else
						jQuery(this).parents('.pg-row-item-inner.hiddener').next('.to-hide').css('display','block');		
				})
			}
		}
	});
}
function showEditProductVaration( varationId, itemId ){
	if(jQuery('.add_product_varation_con .add_product_varation_container').hasClass('active')){
		jQuery('.add_product_varation_con .add_product_varation_container').removeClass( 'active' );
		jQuery('.add_product_varation_con .add_product_varation_container').html( '' );
	}
	var newVarationPosition = jQuery('.add_product_varation_con').offset().top;
	// jQuery('html, body').animate({scrollTop:newVarationPosition}, 'slow');
	jQuery('.add_product_varation_con .add_product_varation_container').addClass('active');

	var varationId = varationId;
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "attributes",
			task : "showAddProductVaration",
			dataType: 'json',
			itemId: itemId,
			varationId: varationId,
			status: "edit",
			async: "1",
		}),
		success: function( response ) {
			if(response){
				result = JSON.parse(response);
				jQuery('.add_product_varation_con .add_product_varation_container').html( result.varationForm );
				jQuery(".add_product_varation_con tr.pg-table-content").find("select").chosen({'disable_search': true,  'disable_search_threshold': 6});
				jQuery('#addProductVariation').modal('show');

				files = load_files(varationId,'product_variation');
				var id = 'images_opt_'+varationId;
				jQuery('#folder_image_editor_'+ varationId).append('<ul id=\''+id+'\'></ul>');

				for(var f in files){
					if(typeof files[f] == 'function') continue;
					if(files[f].length){
						jQuery('#folder_image_editor_'+ varationId +' ul#'+id).append('<li data-folder=\"'+varationId+'\" data-img=\"'+files[f]+'\" image_type="product_variation"><div class="image_buttons"><div class="attr_delete_btn" alt="delete"></div><div class="attr_edit_btn" alt="edit"></div></div><img src=\"'+ JURI + 'media/pago/product_variation/' + varationId +'/'+files[f]+'\" /></li>');
					}
				}

				createVarationUploadCon(varationId);

				jQuery('.pg-row-item-inner.hiddener').each(function(){
					if(jQuery(this).find('select option:selected').val()=="1")
						jQuery(this).next('.to-hide').css('display','none');
					else
						jQuery(this).next('.to-hide').css('display','block');	
				})
				
				jQuery('.pg-row-item-inner.hiddener select').change(function(){
					if(jQuery(this).find('option:selected').val()=="1")
						jQuery(this).parents('.pg-row-item-inner.hiddener').next('.to-hide').css('display','none');
					else
						jQuery(this).parents('.pg-row-item-inner.hiddener').next('.to-hide').css('display','block');		
				})

				defVal = jQuery('input[type=radio][name=default_varation]:checked').val();
				if(defVal == 1){
					changeVarationEditView();
				}
			}
		}
	});
}
function createVarationUploadCon(optId){

	var optId = optId;
	var uploadHtml;

	uploadHtml += "<input id='file_upload_"+ optId +"'  name='file_upload_"+ optId +"' type='file' multiple='true'>";
	uploadHtml += "<div id='queue_"+ optId +"'></div>";

	jQuery( uploadHtml ).appendTo( jQuery(".prod_varation_form_con .upload_files_main[optid="+optId+"]") );

	jQuery(function() {
		jQuery("#file_upload_"+optId).uploadifive({
			width:"100px",
			height:"50px",
			buttonText:"Add Image",
			"queueID" : "queue_"+optId,
			dnd : false,
			//uploadScript : uploadifivePath + "?JPATH_ROOT=" + JPATH_ROOT + "&folder=" + optId +"&imageType=product_varation",
			uploadScript : uploadifivePath + "&JPATH_ROOT=" + JPATH_ROOT + "&folder=" + optId +"&imageType=product_variation",
			"onUploadComplete" : function(file) {

					var type = file.name.split('.').pop();
					var types = ["jpg", "png", "jpeg", "gif"];
					
					jQuery("#queue_"+optId).html('');
					if(types.indexOf(type)<0){
						alert("Ivalid file type");
					}
					
					jQuery('#folder_image_editor_'+ optId).html('');
					files = load_files(optId,'product_variation');

					var id = 'images_opt_'+optId;
					jQuery('#folder_image_editor_'+ optId).append('<ul id=\''+id+'\'></ul>');

					for(var f in files){
						if(typeof files[f] == 'function') continue;
						if(files[f].length){
							jQuery('#folder_image_editor_'+ optId +' ul#'+id).append('<li data-folder=\"'+optId+'\" data-img=\"'+files[f]+'\" image_type="product_variation"><div class="image_buttons"><div class="attr_delete_btn" alt="delete"></div><div class="attr_edit_btn" alt="edit"></div></div><img src=\"'+ JURI + 'media/pago/product_variation/' + optId +'/'+files[f]+'\" /></li>');
						}
					}
			},
		});
	});
}

function load_all_files(imageType){
	var result=-1;
	jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: ({
				option: "com_pago", // global field
				controller: "attributes",
				task : "ajaxImage",
				action: 'images_all',
				imageType: imageType
			}),
			success: function( data ) {
				data = JSON.parse(data);
				result = data;
			},
			async:   false
		});

	return result;
}
function load_files(folder,imageType){
	var result=-1;

	jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: ({
				option: "com_pago", // global field
				controller: "attributes",
				task : "ajaxImage",
				folder: folder,
				name: folder,
				action: 'images',
				imageType: imageType
			}),
			success: function( data ) {
				data = JSON.parse(data);
				result = data;
			},
			async:   false
		});

	return result;
}
function load_data(folder,img,imageType){
	jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: ({
				option: "com_pago",
				controller: "attributes",
				task : "ajaxImage",
				folder: folder,
				img: img,
				action: 'load_data',
				imageType: imageType
			}),
		success: function( data ) {
			if(data!=-1){
				data = JSON.parse(data);
				jQuery('#edit_title').val(data['title']);
				jQuery('#edit_alt').val(data['alt']);
				jQuery('#edit_desc').val(data['description']);
			}else{
				jQuery('#edit_title').val('');
				jQuery('#edit_alt').val('');
				jQuery('#edit_desc').val('');
			}
			jQuery('.attribute-lightbox').fadeIn();
		},
		async:   false
	});
}
function save_data(folder,img,title,alt,desc,imageType){
	var result=-1;
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
				option: "com_pago",
				controller: "attributes",
				task : "ajaxImage",
				folder: folder,
				img: img,
				title: title,
				alt: alt,
				desc: desc,
				action: 'save_data',
				imageType: imageType
			}),
		success:function(data) {
				if(data){
					jQuery('.lightbox-save').removeClass('saving');
					jQuery('.attribute-lightbox').fadeOut();
				}
			},
		async:   false
	});
	return result;
}
function delete_data(folder,img,imageType){

	if(!confirm("Delete image ?")){
		return false;
	};

	var result=-1;
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
				option: "com_pago",
				controller: "attributes",
				task : "ajaxImage",
				folder: folder,
				img: img,
				action: 'delete_data',
				imageType: imageType
			}),
		success:function(data) {
				jQuery("#folder_image_editor_" + folder).attr

				if(data){
					jQuery("#folder_image_editor_" + folder + " ul li").each(function(){
	             		if(jQuery(this).attr('data-img') == img) {
	                		jQuery(this).remove();
	                	}
	            	});
				}
			},
		async:   false
	});
	return result;
}