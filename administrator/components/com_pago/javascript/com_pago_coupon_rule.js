(function($){
	var options = {
		selectId : "#pg-rule-select",
		fieldContainer: "#pg-rule-container",
		addClass: "pg-rule-button-add",
		fieldUrl: "index.php?option=com_pago&view=coupons&task=getrulefields&format=raw"
	};

	var meth = {
		init: function () {
			$(options.selectId).bind( "click", priv.callback );
		},
		add: function( rule ) {
			var rule = rule;
			console.log(rule);
			priv.getField( rule );
		}
	};

	var priv = {
		callback: function( event ) {
			var target = $(event.target);

			if ( target.is("a") ) {
				if ( target.hasClass( options.addClass ) ) {
					meth.add(target.attr("href").split("#")[1]);
				}
			}

			return false;
		},
		getField: function( rule ) {
			var url = options.fieldUrl + "&rule=" + rule;
			$.get( url, function(data){
				console.log(options.fieldContainer);
				$(options.fieldContainer).append( data );
			});
		}
	};

	$.couponrule = function ( method ) {
		if ( meth[method] ) {
			return meth[method].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( typeof method === 'object' || ! method ) {
			return meth.init.apply( this, arguments );
		} else {
			$.error( 'Method' + method + ' does not exist on jQuery.couponruleSetup' );
			return false;
		}
	};
})(jQuery);
jQuery(document).ready(function(){
	// Coupon
	jQuery(document).on('change','#coupon-events-available-type',function(){
  		if(jQuery("#coupon-events-available-type").val() == 0){
  			jQuery("#coupon-events-filter").css("display", "none");
  		}else{
  			jQuery("#coupon-events-filter").css("display", "block");
  		}	
	});
	jQuery(document).on('change','#coupon-events-available-condition',function(){
  		if(jQuery("#coupon-events-available-condition").val() == 0){
  			jQuery("#event-available-count-amout").css("display", "none");
  			jQuery("#event-available-count-item").css("display", "block");
  		}else{
  			jQuery("#event-available-count-item").css("display", "none");
  			jQuery("#event-available-count-amout").css("display", "block");
  		}	
	});
	// Assign Coupon to item
	jQuery(document).on('change','#coupon-assign-type',function(){	
		couponId = jQuery('input[name="id"]').val();
		if(!couponId){
			couponId = 0;
		}
  		assignType = jQuery("#coupon-assign-type").val();
  		if(assignType == 0){
  			jQuery('#coupon-assign-parameters').html('');	
  		}	
  		if(assignType == 1){
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=coupons&task=show_assign_items&couponId=' +couponId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#coupon-assign-parameters').html(data);
                }
            });
  		}
  		if(assignType == 2){
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=coupons&task=show_assign_category&couponId=' +couponId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#coupon-assign-parameters').html(data);
                }
            });
  		}	
  		if(assignType == 3){
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=coupons&task=show_assign_groups&couponId=' +couponId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#coupon-assign-parameters').html(data);
                }
            });
  		}
  		if(assignType == 4){
  			 jQuery.ajax({
            	type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=coupons&task=show_assign_users&couponId=' +couponId+ '',
				dataType : 'json',
                success : function(data) {
                    jQuery('#coupon-assign-parameters').html(data);
                }
            });
  		}
  		
  		if(assignType == 5){
  			jQuery('#coupon-assign-parameters').html('');	
  		}
	});
	// assign item
	jQuery(document).on('keyup','#coupon-assign-item-add',function(){
		jQuery(this).autocomplete({
			 source : function(request, response) {
	            jQuery.ajax({
	            	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=coupons&task=assign_item&q=' +request.term+ '',
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
	// assign users
	jQuery(document).on('keyup','#coupon-assign-user-add',function(){
		jQuery(this).autocomplete({
			 source : function(request, response) {
	            jQuery.ajax({
	            	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=coupons&task=assign_user&q=' +request.term+ '',
					dataType : 'json',
	                success : function(data) {
	                    jQuery('#coupon-assign-user-add').removeClass('itemLoading');
	                    response(data, function(item) {
	                        return item;
	                    });
	                }
	            });
	       },
	        minLength : 3,
	        select : function(event, ui) {
	            jQuery('<li class="userAdded" id="' + ui.item.value + '">' + ui.item.label + '<span title="Click to remove" class="coupon-remove-assign-user">x</span></li>').appendTo('.coupon-assign-users');
	            this.value = '';
	            assign_users = jQuery('#params_assign_users').val();
				assign_users = JSON.parse(assign_users);
				len = getObjectArrayLength(assign_users);
				
				assign_users[len] = {};
				assign_users[len].id = ui.item.value;
				
				jQuery('#params_assign_users').val(JSON.stringify(assign_users));
	            return false;
	        },
	        search : function(event, ui) {
	            jQuery('#coupon-assign-user-add').addClass('itemLoading');
	        }
	    });
	});
	jQuery(document).on('click','.coupon-remove-assign-user',function(){
		deletedUser = jQuery(this).parent();
		assign_users = jQuery('#params_assign_users').val();
		assign_users = JSON.parse(assign_users);
		new_assign_users= {};
		i=0;
		for (key in assign_users) {
			if(assign_users[key].id && assign_users[key].id !=  deletedUser.attr("id")){
				new_assign_users[i++] = assign_users[key]; 	
			}
		}
		assign_users = new_assign_users;
		jQuery('#params_assign_users').val(JSON.stringify(assign_users));
		deletedUser.remove();	
	})
});