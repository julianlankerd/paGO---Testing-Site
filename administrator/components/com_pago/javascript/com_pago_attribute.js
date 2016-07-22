(function($) {

	var meth = {
		settings: {
			optionTableId: "#pg-attribute-options"
		},
		addField: function() {
			// if ( $(".new_attr_opt").length != 0) {
			// 	jQuery('.new_attr_opt').removeClass("hideAttrForm");
			// 	jQuery('.new_attr_opt').addClass("showAttrForm");
			// 	return false;
			// }

			// meth.closeAllForm();
			var optionTable = $( meth.settings.optionTableId + " tbody");

			type = jQuery("#params_type").val();
			num = $("#pg-attribute-options tr.attr_opt_row").size() + 1;
			var attrId = jQuery("#params_id").val();

			if(!attrId){
				return false;
			}
			
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=attributes&task=showOptForm&type=' + type +'&num=' + num +'&attrId=' + attrId ,
				success: function( response ) {
					// optionTable.append( response );
					// jscolor.bind();
					jQuery("#add-attribute-fields").html(response);
				}
			});
		},
		editField: function( obj )
		{
			if (!obj || !obj.id)
				return;
				
			var id = obj.id;
			var type = obj.type;
			var num = obj.num;
			var attrId = jQuery("#params_id").val();
			
			var data = {
				option: 'com_pago',
				controller: 'attributes',
				task: 'showOptForm',	
			};
			
			data = $.extend( data, obj );
			
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: data,
				success: function( response ) {
					jQuery("#add-attribute-fields").html(response);
					jQuery("#addAttribute").modal();
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
		closeAllForm: function(){
			jQuery('.attr_option_form').removeClass("showAttrForm");
			jQuery('.attr_option_form').addClass("hideAttrForm");
			jQuery('.attr_opt_edit a').html("Edit");
		},
		save: function(){
			var optionTable = $( meth.settings.optionTableId + " tbody");

			attrType = jQuery("#params_type").val();

			num = $("#pg-attribute-options tr.attr_opt_row").length + 1;

			id       = $(".new_attr_opt #params_options_id_"+num).val();
			var optId = id;
			name     = $(".new_attr_opt #params_options_name_"+num).val();
			type     = $(".new_attr_opt #params_options_opt_type_"+num).val();
			ordering = $(".new_attr_opt #params_options_ordering_"+num).val();


			if(!validValNotEmpty(name)){
				alert("Attribute name is empty.");
				return false;
			}
			color = false;
			size = false;
			size_type = false;

			if(type == 0){
				color = $(".new_attr_opt #params_options_color_"+num).val();
			}
			if(type == 1){
				size = $(".new_attr_opt #params_options_size_"+num).val();
				size_type = $(".new_attr_opt #params_options_size_type_"+num).val();
			}

			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "attributes",
					task : "addOptValue",
					dataType: 'json',
					num: num,
					id: id,
					opt_type: type,
					name: name,
					ordering: ordering,

					color: color, // color field
					size: size, // size field
					size_type: size_type, // size field
				}),
				success: function( response ) {
					if(response){
						// $("tr.new_attr_opt").remove();
						optionTable.append( response );
					}
					// jQuery("#pg-attribute-options").find("tr.bind").removeClass("bind");
					jscolor.bind();
					jQuery("#addAttribute").modal("hide");
				}
			});
		},
		delField: function( rowId ) {
			if(!confirm("Delete attribute values?")){
				return false;
			};
			var optionTable = $( meth.settings.optionTableId + " tbody");
			
			$("tr[rel='"+rowId+"']").remove();
			
			jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=attributes&task=delete_attr_opt&optId=' +rowId+ '',
				dataType : 'json',
                success : function(data) {
                    
                }
            });

			// jQuery.each(jQuery("#pg-attribute-options tr"), function () {
			// 	if ( jQuery(this).find("td.pg-checkbox input").is(":checked") ) {
			// 		id = jQuery(this).attr('id');
			// 		jQuery(this).remove();
			// 		jQuery('.'+id).remove();
			// 	}
			// });

			return false;
		},
	};
	$.attributeOpts = function ( method ) {
		if ( meth[method] ) {
			return meth[method].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( typeof method === 'object' || ! method ) {
			meth.init.apply( this, arguments );
		} else {
			$.error( 'Method' + method + ' does not exist on jQuery.attributeOpts' );
		}
	};
})(jQuery);

jQuery( window ).load(function() {
	jQuery("#pago_toolbar .toolbar-list ul li#toolbar-apply a").attr("onClick","applyOverwrite()");
	jQuery("#pago_toolbar .toolbar-list ul li#toolbar-save a").attr("onClick","saveOverwrite()");
});


function applyOverwrite(){
	if(validaAttrForm()){
		Joomla.submitbutton('apply');
	}
}
function saveOverwrite(){
	if(validaAttrForm()){
		Joomla.submitbutton('save');
	}
}
function validaAttrForm(){
	name = jQuery("#params_name").val();
	if(!validValNotEmpty(name)){
		alert("Attribute name is empty.");
		return false;
	}
	return true;
}
jQuery(document).ready(function(){

	// jQuery( "#pg-attribute-options tbody" ).sortable({
	//   stop: function( event, ui ) {
	//   	jQuery.each(jQuery("#pg-attribute-options tr.attr_opt_row"), function () {
	//   		headerId = jQuery(this).attr('id');
	//   		formBodyClass = headerId;
	//   		jQuery("."+formBodyClass).insertAfter(jQuery("#"+headerId));
	// 	});
	//   }
	// });

	jQuery(document).on('change','#params_type',function(){
		newType = jQuery("#params_type").val();

		jQuery.each(jQuery("#pg-attribute-options tr"), function () {
			jQuery(this).removeClass("showAttrForm");
			jQuery(this).addClass("hideAttrForm");
		});

		jQuery('.attr_opt_type_head_'+newType).removeClass("hideAttrForm");
		jQuery('.attr_opt_type_'+newType).addClass("hideAttrForm");
	});


	// assignments

	// Assign attributes
	jQuery(document).on('change','#attribute-assign-type',function(){
		attrId = jQuery('input[name="id"]').val();
		if(!attrId){
			attrId = 0;
		}
  		assignType = jQuery("#attribute-assign-type").val();
  		if(assignType == 0){
  			jQuery('#attribute-assign-parameters').html('');
  		}
  		if(assignType == 1){
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=attributes&task=show_assign_items&attrId=' +attrId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#attribute-assign-parameters').html(data);
                }
            });
  		}
  		if(assignType == 2){
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=attributes&task=show_assign_category&attrId=' +attrId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#attribute-assign-parameters').html(data);
                }
            });
  		}
	});
	// assign item
	jQuery(document).on('keyup','#coupon-assign-item-add',function(){
		jQuery(this).autocomplete({
			 source : function(request, response) {
	            jQuery.ajax({
	            	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=attributes&task=assign_item&q=' +request.term+ '',
					dataType : 'json',
	                success : function(data) {
	                    jQuery('#coupon-assign-item-add').removeClass('itemLoading');
	                    response(data, function(item) {
	                        return item;
	                    });
	                }
	            });
	       },
	        minLength : 3,
	        select : function(event, ui) {
	            jQuery('<li class="itemAdded" id="' + ui.item.value + '">' + ui.item.label + '<span title="Click to remove" class="coupon-remove-assign-item">x</span></li>').appendTo('.coupon-assign-items');
	            this.value = '';
	            assign_items = jQuery('#params_assign_items').val();
				assign_items = JSON.parse(assign_items);
				len = getObjectArrayLength(assign_items);

				assign_items[len] = {};
				assign_items[len].id = ui.item.value;

				jQuery('#params_assign_items').val(JSON.stringify(assign_items));
	            return false;
	        },
	        search : function(event, ui) {
	            jQuery('#coupon-assign-item-add').addClass('itemLoading');
	        }
	    });
	});
	jQuery(document).on('click','.coupon-remove-assign-item',function(){
		deletedItem = jQuery(this).parent();
		assign_items = jQuery('#params_assign_items').val();
		assign_items = JSON.parse(assign_items);
		new_assign_items= {};
		i=0;
		for (key in assign_items) {
			if(assign_items[key].id && assign_items[key].id !=  deletedItem.attr("id")){
				new_assign_items[i++] = assign_items[key];
			}
		}
		assign_items = new_assign_items;
		jQuery('#params_assign_items').val(JSON.stringify(assign_items));
		deletedItem.remove();
	})
})