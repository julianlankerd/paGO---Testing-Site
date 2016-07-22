jQuery(document).ready(function(){
	
	jQuery("#event-available-count-amout").css("display", "none");
	if( jQuery("#discount-event-type option:selected").val() == '1' )
	{
		jQuery("#discount-events-filter").css("display", "block");
		jQuery("#discount-events-item-filter").css("display", "none");
		jQuery("#discount-events-category-filter").css("display", "none");
		jQuery("#discount-custom-field-filter").css("display", "none");  
		
			
	} else if ( jQuery("#discount-event-type option:selected").val() == '2')
	{
		jQuery("#discount-events-item-filter").css("display", "block");
		jQuery("#discount-events-filter").css("display", "none");
		jQuery("#discount-events-category-filter").css("display", "none");
		jQuery("#discount-events-available-categories").css("display", "none");
		jQuery("#discount-custom-field-filter").css("display", "none"); 
	
		
	}  else if ( jQuery("#discount-event-type option:selected").val() == '3')
	{
		jQuery("#discount-events-item-filter").css("display", "none");
		jQuery("#discount-events-filter").css("display", "none");
		jQuery("#discount-events-category-filter").css("display", "block");
		jQuery("#discount-custom-field-filter").css("display", "none");  
		
	}
	else if ( jQuery("#discount-event-type option:selected").val() == '5')
	{
		jQuery("#discount-custom-field-filter").css("display", "block");
		jQuery("#discount-events-filter").css("display", "none");
		jQuery("#discount-events-category-filter").css("display", "none");
		jQuery("#discount-events-item-filter").css("display", "none");
		
	}
	else {
		
		jQuery("#discount-events-filter").css("display", "none");
		jQuery("#discount-events-item-filter").css("display", "none");
		jQuery("#discount-events-category-filter").css("display", "none");
		jQuery("#discount-custom-field-filter").css("display", "none");   
	}
	
	
	// Discount
	jQuery(document).on('change','#discount-event-type',function(){
		

  		var type = jQuery( "#discount-event-type" ).val();
  		if ( type == '1')
		{
			jQuery("#discount-events-filter").css("display", "block");
			jQuery("#discount-events-item-filter").css("display", "none");
			jQuery("#discount-events-category-filter").css("display", "none");
			jQuery("#discount-custom-field-filter").css("display", "none"); 
		} else if ( type == '2' ) {
			jQuery("#discount-events-item-filter").css("display", "block");
			jQuery("#discount-events-filter").css("display", "none");
			jQuery("#discount-events-category-filter").css("display", "none");
			jQuery("#discount-custom-field-filter").css("display", "none"); 
			
		} 
		else if ( type == '3' ) {
			jQuery("#discount-events-category-filter").css("display", "block");
			jQuery("#discount-events-filter").css("display", "none");
			jQuery("#discount-events-item-filter").css("display", "none");
			jQuery("#discount-custom-field-filter").css("display", "none"); 
		}
		else if ( type == '5' ) {
			jQuery("#discount-custom-field-filter").css("display", "block");
			jQuery("#discount-events-filter").css("display", "none");
			jQuery("#discount-events-item-filter").css("display", "none");
			jQuery("#discount-events-category-filter").css("display", "none");
			jQuery("#discount-combine-items").css("display", "none");
		}
		else {
			jQuery("#discount-events-filter").css("display", "none");
			jQuery("#discount-events-category-filter").css("display", "none");
			jQuery("#discount-events-item-filter").css("display", "none");
			jQuery("#discount-custom-field-filter").css("display", "none"); 
		}
		if(type == '4')
		{
			discountId = jQuery('input[name="id"]').val();
			if(!discountId){
				discountId = 0;
			}
	  		
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=discounts&task=show_assign_items&discountId=' +discountId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#discount-combine-items').html(data);
                }
            });
	  		
		}
		
	});
	jQuery(document).on('change','#discount-events-available-condition',function(){
  		if(jQuery("#discount-events-available-condition").val() == 0){
  			jQuery("#event-available-count-amout").css("display", "none");
  			jQuery("#event-available-count-item").css("display", "block");
  		}else if(jQuery("#discount-events-available-condition").val() == 1){
  			jQuery("#event-available-count-item").css("display", "none");
  			jQuery("#event-available-count-amout").css("display", "block");
  		} else {
			jQuery("#event-available-count-item").css("display", "none");
  			jQuery("#event-available-count-amout").css("display", "none");
		}	
	});
	
	
	// Assign Discount to item
	jQuery(document).on('change','#discount-events-available-items',function(){	
		discountId = jQuery('input[name="id"]').val();
		if(!discountId){
			discountId = 0;
		}
  		assignType = jQuery("#discount-events-available-items").val();
		
		
  		if(assignType == 0){
  			jQuery('#discount-assign-parameters').html('');	
  		}	
		
  		if(assignType == 3){
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=discounts&task=show_assign_items&discountId=' +discountId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#discount-assign-parameters').html(data);
                }
            });
  		}
		
  	
	});

	
	// assign item
	jQuery(document).on('keyup','#discount-assign-item-add',function(){
		jQuery(this).autocomplete({
			 source : function(request, response) {
	            jQuery.ajax({
	            	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=discounts&task=assign_item&q=' +request.term+ '',
					dataType : 'json',
	                success : function(data) {
	                    jQuery('#discount-assign-item-add').removeClass('itemLoading');
	                    response(data, function(item) {
	                        return item;
	                    });
	                }
	            });
	       },
	        minLength : 3,
	        select : function(event, ui) {
	            jQuery('<li class="itemAdded" id="' + ui.item.value + '">' + ui.item.label + '<span title="Click to remove" class="discount-remove-assign-item">x</span></li>').appendTo('.coupon-assign-items');
	            this.value = '';
	            assign_items = jQuery('#params_assign_item_id').val();
				assign_items = JSON.parse(assign_items);
				len = getObjectArrayLength(assign_items);
				
				assign_items[len] = {};
				assign_items[len].id = ui.item.value;
				
				jQuery('#params_assign_item_id').val(JSON.stringify(assign_items));
	            return false;
	        },
	        search : function(event, ui) {
	            jQuery('#discount-assign-item-add').addClass('itemLoading');
	        }
	    });
	});
	jQuery(document).on('click','.discount-remove-assign-item',function(){
		deletedItem = jQuery(this).parent();
		assign_items = jQuery('#params_assign_item_id').val();
		assign_items = JSON.parse(assign_items);
		new_assign_items= {};
		i=0;
		for (key in assign_items) {
			if(assign_items[key].id && assign_items[key].id !=  deletedItem.attr("id")){
				new_assign_items[i++] = assign_items[key]; 	
			}
		}
		assign_items = new_assign_items;
		jQuery('#params_assign_item_id').val(JSON.stringify(assign_items));
		deletedItem.remove();	
	})
	
	// Assign Discount to item
	jQuery(document).on('change','#discount-events-available-categories',function(){	
		discountId = jQuery('input[name="id"]').val();
		if(!discountId){
			discountId = 0;
		}
  		assignType = jQuery("#discount-events-available-categories").val();
		
		
  		if(assignType == 0){
  			jQuery('#discount-assign-parameters-cat').html('');	
  		}	
		
  		
		if(assignType == 5){
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=discounts&task=show_assign_category&discountId=' +discountId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#discount-assign-parameters-cat').html(data);
                }
            });
  		}	
  		
  	
	});
	
});
