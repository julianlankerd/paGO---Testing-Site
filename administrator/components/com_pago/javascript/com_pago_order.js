var last_quickview="";jQuery.noConflict();
jQuery( document ).ready(function() {
	var counter = 1;

	function createAutocomplete(counter)
	{
		jQuery(document).on('keyup','#pg_addorder_item_name'+counter+'',function(){
			jQuery(this).autocomplete({
					source : function(request, response) {
					jQuery.ajax({
						type: 'POST',
						url: 'index.php',
						data: 'option=com_pago&controller=coupons&task=assign_item&q=' +request.term+ '',
						dataType : 'json',
						success : function(data) {
							jQuery('#pg_addorder_item_name'+counter+'').removeClass('itemLoading');
							response(data, function(item) {
								return item;
							});
						}
					});
				},
		        minLength : 1,
		        select : function(event, ui) {
					jQuery('#pg_addorder_item_name'+counter+'').val(ui.item.label);
					jQuery('#pg_addorder_item_id'+counter+'').val(ui.item.value);
					var userId     = jQuery('#pg-filter_user').val();
					var addressId  = jQuery('#pg-billing_address').val();
					var saddressId = jQuery('#pg-shipping_address').val();
			          jQuery.ajax({
				        type: "POST",
				        url: 'index.php',
						data: 'option=com_pago&view=addorder&task=getItemDetail&item_id=' +ui.item.value+ '&user_id='+userId+'&saddressId=' + saddressId+ '&addressId=' + addressId,
				        success: function(response)
				        {
				         	if (response == '')
				        	{

				        	}
				        	else
				        	{
								var str        =response.split("##");
								jQuery('#pg_addorder_item_name'+counter+'').val(str[0]);
								jQuery('#pg_addorder_item_price_without_tax'+counter+'').val(str[1]);
								jQuery('#pg_addorder_item_tax'+counter+'').val(str[2]);
								jQuery('#pg_addorder_item_total_price'+counter+'').val(str[3]);
								jQuery('#pg_addorder_item_quantity'+counter+'').val(str[4]);
								jQuery('#pg_addorder_item_tax_rate'+counter+'').val(str[5]);
								jQuery('#pg_addorder_apply_tax_on_shipping'+counter+'').val(str[6]);
								jQuery('#pg_addorder_item_free_shipping'+counter+'').val(str[7]);
								jQuery('#pg_addorder_item_shipping_tax'+counter+'').val(0);
								var currentRow = counter;
								calculateOrderTotal(currentRow, counter);

							}
				        }
				    });
			         return false;
		        },
		        search : function(event, ui) {
		            jQuery('#pg_addorder_item_name'+counter+'').addClass('itemLoading');
		        }
		    });
		});
		
		jQuery(document).on('keyup','#pg_addorder_item_quantity'+counter+'',function(){
			updateOrderTotal(counter);
		});

		jQuery(document).on('focus','#pg_addorder_item_name'+counter+'',function(){
		      if(jQuery('#pg_addorder_item_name'+counter+'').val() == 'Enter Item Name'){
		          jQuery('#pg_addorder_item_name'+counter+'').val('');
		      }
	 	});

		jQuery(document).on('blur','#pg_addorder_item_name'+counter+'',function(){
		      if(jQuery('#pg_addorder_item_name'+counter+'').val() == ''){
		          jQuery('#pg_addorder_item_name'+counter+'').val('Enter Item Name');
		      }
	 	});

		jQuery(document).on('click','#pg_addorder_item_remove'+counter+'',function(){
			if(window.confirm("Are you sure you want to delete?"))
			{
				jQuery('#pg-items-manager #order_item_tr'+counter+'').remove();
				counter--;
				calculateOrderTotal(counter);
			}
		});
	// as
	}

	jQuery(document).on('focus','#pg_addorder_item_name1',function(){
	      if(jQuery('#pg_addorder_item_name1').val() == 'Enter Item Name'){
	          jQuery('#pg_addorder_item_name1').val('');
	      }
	 });

	jQuery(document).on('blur','#pg_addorder_item_name1',function(){
	      if(jQuery('#pg_addorder_item_name1').val() == ''){
	          jQuery('#pg_addorder_item_name1').val('Enter Item Name');
	      }
	 });

	jQuery(document).on('change','input:radio[name="payment_option"]',function(){
		if(!jQuery('#pg_addorder_item_id1').val())
		{
			alert("Please add Item first");
		}
	});

	jQuery(document).on('change','#pg_addorder_product_shipping_type',function(){
		var shippingPrice = 0;
		for(i=1;i<=counter;i++)
		{
			var itemId = jQuery('#pg_addorder_item_id'+i+'').val();
			var selected = jQuery('input:radio[name="carrier_option['+itemId+']"]:checked');
			if (selected.length > 0) {
			    selectedVal = selected.val();
			    var shippingOptions = selectedVal.split("|");
				shippingPrice = parseFloat(shippingPrice) + parseFloat(shippingOptions[3]);
				var shippingMethod = shippingOptions[0]+'-'+shippingOptions[2];
				jQuery('#pg_item_based_shipping_name'+itemId+'').val(shippingMethod);
			}
		}

		var finalTotal = jQuery('#pg_addorder_order_total').val() - jQuery('#pg_addorder_order_shipping_old').val();
		finalTotal = parseFloat(finalTotal) + parseFloat(shippingPrice);

		// Update order Subtotal /Tax/ Total
		var totalTaxOnShipping = 0;
		var totaltax = 0;

		for(i=1;i<=counter;i++)
		{
			var itemTax = jQuery('#pg_addorder_item_tax'+i+'').val();
			if (jQuery('#pg_addorder_item_free_shipping'+i+'').val() == 0  && jQuery('#pg_addorder_apply_tax_on_shipping'+i+'').val() == 1)
			{
				if(parseFloat(jQuery('#pg_addorder_item_price_without_tax'+i+'').val() ) && shippingPrice > 0)
				{
					var itemShipping = ( parseFloat(shippingPrice) * (jQuery('#pg_addorder_item_qty'+i).val() * parseFloat(jQuery('#pg_addorder_item_price_without_tax'+i+'').val() ))  )/ parseFloat(jQuery('#pg_addorder_order_subtotal').val() )
					var itemTaxOnShipping = (parseFloat(jQuery('#pg_addorder_item_tax_rate'+i+'').val() ) * parseFloat(itemShipping)) / 100;
					finalTotal = parseFloat(finalTotal) - parseFloat(jQuery('#pg_addorder_item_shipping_tax'+i+'').val());
					jQuery('#pg_addorder_item_shipping_tax'+i+'').val(itemTaxOnShipping);

					totalTaxOnShipping = parseFloat(totalTaxOnShipping) + parseFloat(itemTaxOnShipping);
					totaltax =  parseFloat(totaltax) + parseFloat(itemTax) + parseFloat(itemTaxOnShipping);
				}
			}
		}
		if(totaltax > 0)
		{
			finalTotal = parseFloat(finalTotal) + parseFloat(totalTaxOnShipping);

			jQuery('#pg_addorder_order_tax').val(totaltax);
			jQuery('#pg_addorder_order_tax_div').html(totaltax);
			jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
		}

		jQuery('#pg_addorder_order_shipping').val(shippingPrice);
		jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
		jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
		jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);

		jQuery('#pg_addorder_order_total').val(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
		jQuery('#pg_addorder_order_total_div').html(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
		jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
	});
	
	jQuery(document).on('change','input:radio[name="carrier_option"]',function(){

		var selected = jQuery("input[type='radio'][name='carrier_option']:checked");
		if (selected.length > 0) {
		    selectedVal = selected.val();
		}
		var finalTotal = jQuery('#pg_addorder_order_total').val() - jQuery('#pg_addorder_order_shipping_old').val();
		var shipping = selectedVal;
		var shippingOptions = shipping.split("|");
		var shippingPrice = shippingOptions[3];
		var shippingMethod = shippingOptions[0]+'-'+shippingOptions[2];
		finalTotal = parseFloat(finalTotal) + parseFloat(shippingPrice);
		// Update order Subtotal /Tax/ Total
		var totalTaxOnShipping = 0;
		var totaltax = 0;

		for(i=1;i<=counter;i++)
		{
			var itemTax = jQuery('#pg_addorder_item_tax'+i+'').val();
			if (jQuery('#pg_addorder_item_free_shipping'+i+'').val() == 0  && jQuery('#pg_addorder_apply_tax_on_shipping'+i+'').val() == 1)
			{
				if(parseFloat(jQuery('#pg_addorder_item_price_without_tax'+i+'').val() ) && shippingPrice > 0)
				{
					var itemShipping = ( parseFloat(shippingPrice) * (jQuery('#pg_addorder_item_qty'+i).val() * parseFloat(jQuery('#pg_addorder_item_price_without_tax'+i+'').val() ))  )/ parseFloat(jQuery('#pg_addorder_order_subtotal').val() )
					var itemTaxOnShipping = (parseFloat(jQuery('#pg_addorder_item_tax_rate'+i+'').val() ) * parseFloat(itemShipping)) / 100;
					finalTotal = parseFloat(finalTotal) - parseFloat(jQuery('#pg_addorder_item_shipping_tax'+i+'').val());
					jQuery('#pg_addorder_item_shipping_tax'+i+'').val(itemTaxOnShipping);

					totalTaxOnShipping = parseFloat(totalTaxOnShipping) + parseFloat(itemTaxOnShipping);
					totaltax =  parseFloat(totaltax) + parseFloat(itemTax) + parseFloat(itemTaxOnShipping);
				}
			}
		}
		if(totaltax > 0)
		{
			finalTotal = parseFloat(finalTotal) + parseFloat(totalTaxOnShipping);

			jQuery('#pg_addorder_order_tax').val(totaltax);
			jQuery('#pg_addorder_order_tax_div').html(totaltax);
			jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
		}

		jQuery('#pg_addorder_order_shipping').val(shippingPrice);
		jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
		jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
		jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);

		jQuery('#pg_addorder_order_total').val(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
		jQuery('#pg_addorder_order_total_div').html(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
		jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
	});
	
	jQuery(document).on('keyup','#pg_addorder_item_quantity1',function(){
		updateOrderTotal(1);
	});

	// assign item
	jQuery(document).on('keyup','#pg_addorder_item_name1',function(){

		jQuery(this).autocomplete({
			 source : function(request, response) {
	            jQuery.ajax({
	            	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=coupons&task=assign_item&q=' +request.term+ '',
					dataType : 'json',
	                success : function(data) {
	                    jQuery('#pg_addorder_item_name1').removeClass('itemLoading');
	                    response(data, function(item) {
	                        return item;
	                    });
	                }
	            });
	       },
	        minLength : 1,
	        select : function(event, ui) {
	        	jQuery('#pg_addorder_item_name1').val(ui.item.label);
	        	jQuery('#pg_addorder_item_id1').val(ui.item.value);
	        	var userId = jQuery('#pg-filter_user').val();
	        	var addressId = jQuery('#pg-billing_address').val();
				var saddressId = jQuery('#pg-shipping_address').val();
		          jQuery.ajax({
			        type: "POST",
			        url: 'index.php',
					data: 'option=com_pago&view=addorder&task=getItemDetail&item_id=' +ui.item.value+ '&user_id='+userId+'&saddressId=' + saddressId+ '&addressId=' + addressId,
			        success: function(response)
			        {
			         	if (response == '')
			        	{

			        	}
			        	else
			        	{
							var str =response.split("##");
							jQuery('#pg_addorder_item_name1').val(str[0]);
							jQuery('#pg_addorder_item_price_without_tax1').val(str[1]);
							jQuery('#pg_addorder_item_tax1').val(str[2]);
							jQuery('#pg_addorder_item_total_price1').val(str[3]);
							jQuery('#pg_addorder_item_quantity1').val(str[4]);
							jQuery('#pg_addorder_item_qty1').val(str[4]);
							jQuery('#pg_addorder_item_tax_rate1').val(str[5]);
							jQuery('#pg_addorder_apply_tax_on_shipping1').val(str[6]);
							jQuery('#pg_addorder_item_free_shipping1').val(str[7]);
							jQuery('#pg_addorder_item_shipping_tax1').val(0);
							calculateOrderTotal(counter);
						}
			        }
			    });

				return false;
	        },
	        search : function(event, ui) {
	            jQuery('#coupon-assign-item-add').addClass('itemLoading');
	        }
	    });
	});


	// For edit order
	jQuery(document).on('click','#pg-ordersi_item_save_btn',function(){
		var item_id = jQuery('#pg_ordersi_item_id1').val();
		var order_id = jQuery('#pg_ordersi_order_id').val();
		var userId = jQuery('#pg-filter_user').val();
		var addressId = jQuery('#pg-billing_address').val();
		var saddressId = jQuery('#pg-shipping_address').val();
		var qty = jQuery('#pg_ordersi_item_quantity1').val();
     

       jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "ordersi",
			task : "saveOrderItem",
			dataType: 'json',
			order_id : order_id,
			item_id : item_id,
			user_id : userId,
			address_id : addressId,
			saddress_id : saddressId,
			qty : qty
		}),
		success: function( response ) {
			json = jQuery.parseJSON(response);
			
			jQuery.each( json, function( key, value ) {
				if(key == 'order_total')
				{
					jQuery('#pg_addorder_order_total').val(value);
					jQuery('#pg_addorder_order_total_div').html(value);
					jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');

				}
				if(key == 'order_subtotal')
				{
					jQuery('#pg_addorder_order_subtotal').val(value);
					jQuery('#pg_addorder_order_subtotal_div').html(value);
					jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');

				}
            });

            	// Update Order Items rows
				jQuery.ajax({
					type: 'POST',
					url: 'index.php',
					data: ({
						option: "com_pago", // global field
						controller: "ordersi",
						task : "updateOrderItemRows",
						dataType: 'json',
						order_id : order_id,
					}),
					success: function( response ) {
						//jQuery( "#pg_order_item_"+item_id ).hide();
						jQuery( "#pg_order_item_rows").html(response);
					}
				});
		}
	});
    });

	
	jQuery(document).on('keyup','#pg_ordersi_item_quantity1',function(){
		var itemTotalPrice = 0;
		var oldQty = jQuery('#pg_ordersi_item_qty1').val();
		var newQty = jQuery('#pg_ordersi_item_quantity1').val();
		if(newQty <= 0)
		{
			newQty = 1;
		}
		itemTotalPrice = (parseFloat(jQuery('#pg_ordersi_item_price_without_tax1').val()) + parseFloat(jQuery('#pg_ordersi_item_tax1').val())) * newQty;
		jQuery('#pg_ordersi_item_total_price1').val(itemTotalPrice);
	});
	
	jQuery(document).on('focus','#pg_ordersi_item_name1',function(){
	      if(jQuery('#pg_ordersi_item_name1').val() == 'Enter Item Name'){
	          jQuery('#pg_ordersi_item_name1').val('');
	      }
	 });

	jQuery(document).on('blur','#pg_ordersi_item_name1',function(){
	      if(jQuery('#pg_ordersi_item_name1').val() == ''){
	          jQuery('#pg_ordersi_item_name1').val('Enter Item Name');
	      }
	 });
	jQuery(document).on('keyup','#pg_ordersi_item_name1',function(){

		jQuery(this).autocomplete({
			 source : function(request, response) {
	            jQuery.ajax({
	            	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=coupons&task=assign_item&q=' +request.term+ '',
					dataType : 'json',
	                success : function(data) {
	                    jQuery('#pg_ordersi_item_name1').removeClass('itemLoading');
	                    response(data, function(item) {
	                        return item;
	                    });
	                }
	            });
	       },
	        minLength : 1,
	        select : function(event, ui) {
	        	jQuery('#pg_ordersi_item_name1').val(ui.item.label);
	        	jQuery('#pg_ordersi_item_id1').val(ui.item.value);
	        	var order_id = jQuery('#pg_ordersi_order_id').val();
	        	var userId = jQuery('#pg-filter_user').val();
	        	var addressId = jQuery('#pg-billing_address').val();
				var saddressId = jQuery('#pg-shipping_address').val();
		          jQuery.ajax({
			        type: "POST",
			        url: 'index.php',
					data: 'option=com_pago&view=ordersi&task=getItemDetail&item_id=' +ui.item.value+ '&user_id='+userId+'&saddressId=' + saddressId+ '&addressId=' + addressId+ '&order_id=' +order_id,
			        success: function(response)
			        {
			         	if (response == '')
			        	{

			        	}
			        	else
			        	{
							var str =response.split("##");
							jQuery('#pg_ordersi_item_name1').val(str[0]);
							jQuery('#pg_ordersi_item_price_without_tax1').val(str[1]);
							jQuery('#pg_ordersi_item_tax1').val(str[2]);
							jQuery('#pg_ordersi_item_total_price1').val(str[3]);
							jQuery('#pg_ordersi_item_quantity1').val(str[4]);
							jQuery('#pg_ordersi_item_qty1').val(str[4]);
							jQuery('#pg_ordersi_item_tax_rate1').val(str[5]);
							jQuery('#pg_ordersi_apply_tax_on_shipping1').val(str[6]);
							jQuery('#pg_ordersi_item_free_shipping1').val(str[7]);
							jQuery('#pg_ordersi_item_shipping_tax1').val(0);
						}
			        }
			    });

				return false;
	        },
	        search : function(event, ui) {
	            jQuery('#pg_ordersi_item_name1').addClass('itemLoading');
	        }
	    });
	});
	jQuery(document).on('click','#savebtn',function(){
		jQuery('#task').val('saveOrderUser');
		jQuery( "#adminForm" ).submit();
	});

	jQuery(document).on('change','#pg-billing_address',function(){
		var addressId = jQuery('#pg-billing_address').val();
		var userId = jQuery('#pg-filter_user').val();
		getUserAddress(userId, addressId);

	});

	jQuery(document).on('change','#pg-shipping_address',function(){
		var addressId = jQuery('#pg-billing_address').val();
		var saddressId = jQuery('#pg-shipping_address').val();
		var userId = jQuery('#pg-filter_user').val();
		getUserAddress(userId, addressId, saddressId);

	});
	// Same as Billing
	jQuery(document).on('change','#address_mailing_same_as_billing',function(){
			if( jQuery('#address_mailing_same_as_billing').is(':checked') )
			{
				var addressId = jQuery('#pg-billing_address').val();
				if(addressId)
				{
					jQuery('#pg-shipping_address').val(addressId);
				}
				jQuery( "#addShippingaddress").hide();
			}
			else
			{
				jQuery( "#addShippingaddress").show();
			}

	});
	
	jQuery(document).on('click','#pg-order_item_add_btn',function(){
        counter++;
        var newRow = jQuery('<tr class="pg-sub-heading new-item-con" id="order_item_tr'+counter+'">' +
        	counter + '<td><span class="new-currency-field"><div id="pg_addorder_item_remove'+counter+'">Remove</div></span></td>' +
            counter + '<td><span class="new-item-field"><input type="text" id="pg_addorder_item_name'+counter+'" name="pg_addorder_item_name'+counter+'" value="Enter Item Name" /><input type="hidden" name="pg_addorder_item_id'+counter+'" id="pg_addorder_item_id'+counter+'" value="" ></span></td>' +
            counter + '<td><span class="new-item-field"><input type="text" id="pg_addorder_item_price_without_tax'+counter+'" name="pg_addorder_item_price_without_tax'+counter+'" value="" /></span></td>' +
            counter + '<td><span class="new-item-field"><input type="text" id="pg_addorder_item_tax'+counter+'" name="pg_addorder_item_tax'+counter+'" value="" /><input type="hidden" id="pg_addorder_item_with_tax'+counter+'" name="pg_addorder_item_with_tax'+counter+'" value=""  readonly="true"/><input type="hidden" id="pg_addorder_item_tax_rate'+counter+'" name="pg_addorder_item_tax_rate'+counter+'" value=""  readonly="true"/><input type="hidden" id="pg_addorder_apply_tax_on_shipping'+counter+'" name="pg_addorder_apply_tax_on_shipping'+counter+'" value=""  readonly="true"/><input type="hidden" id="pg_addorder_item_free_shipping'+counter+'" name="pg_addorder_item_free_shipping'+counter+'" value=""  readonly="true"/><input type="hidden" id="pg_addorder_item_shipping_tax'+counter+'" name="pg_addorder_item_shipping_tax'+counter+'" value="0"  readonly="true"/></span></td>' +
            counter + '<td><span class="new-item-field"><input type="text" id="pg_addorder_item_quantity'+counter+'" name="pg_addorder_item_quantity'+counter+'" value="" /><input type="hidden" name="pg_addorder_item_qty'+counter+'" id="pg_addorder_item_qty'+counter+'" value="" ></span></td>' +
            counter + '<td><span class="new-currency-field"><input type="text" id="pg_addorder_item_total_price'+counter+'" name="pg_item_total_price'+counter+'" value="" /></span></td>' +
            counter + '</tr>');
        jQuery('#pg-items-manager').append(newRow);
        createAutocomplete(counter);
    });

	
});
function calculateOrderTotal(counter)
{
	var subTotal = 0;
	var totalTax = 0;
	var finalTotal = 0;
	var shippingPrice = 0;
	var Qty = jQuery('#pg_addorder_item_quantity'+counter+'').val();
	if(Qty <= 0)
	{
		Qty = 1;
	}

	// Update Item Total Price
	if(parseFloat(jQuery('#pg_addorder_item_price_without_tax'+counter+'').val()))
	{
		var itemTotalPrice = ( parseFloat(jQuery('#pg_addorder_item_price_without_tax'+counter+'').val()) *Qty ) + ( parseFloat(jQuery('#pg_addorder_item_tax'+counter+'').val()) * Qty);
		jQuery('#pg_addorder_item_total_price'+counter+'').val(itemTotalPrice);

		var itemPriceWithVat = ( parseFloat(jQuery('#pg_addorder_item_price_without_tax'+counter+'').val())) + ( parseFloat(jQuery('#pg_addorder_item_tax'+counter+'').val()));
		jQuery('pg_addorder_item_with_tax').val(itemPriceWithVat);
	}

	// Update order Subtotal /Tax/ Total
	for(i=1;i<=counter;i++)
	{
		if(parseFloat(jQuery('#pg_addorder_item_price_without_tax'+i+'').val() ))
		{
			rowQty = jQuery('#pg_addorder_item_quantity'+i).val();
			subTotal = parseFloat(subTotal) + (parseFloat(jQuery('#pg_addorder_item_price_without_tax'+i).val())*rowQty);
			totalTax = parseFloat(totalTax) + (parseFloat(jQuery('#pg_addorder_item_tax'+i).val())*rowQty);
			finalTotal = parseFloat(finalTotal) + (parseFloat(jQuery('#pg_addorder_item_total_price'+i).val()));
			jQuery('#pg_addorder_item_qty'+i).val(rowQty);
		}
	}
	jQuery('#pg_addorder_order_subtotal').val(subTotal);
	jQuery('#pg_addorder_order_subtotal_div').html(subTotal);
	jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');

	jQuery('#pg_addorder_order_tax').val(totalTax);
	jQuery('#pg_addorder_order_tax_div').html(totalTax);
	jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');

	jQuery('#pg_addorder_order_total').val(finalTotal);
	jQuery('#pg_addorder_order_total_div').html(finalTotal);
	jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');

	jQuery('#pg_addorder_order_shipping').val(shippingPrice);
	jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);
	jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
	jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');


	displayShippingOptions(counter);

}

function updateOrderTotal(counter)
{
	var oldQty = jQuery('#pg_addorder_item_qty'+counter+'').val();
	var newQty = jQuery('#pg_addorder_item_quantity'+counter+'').val();
	if(newQty <= 0)
	{
		newQty = 1;
	}

	var subTotal = jQuery('#pg_addorder_order_subtotal').val();
	var totalTax = jQuery('#pg_addorder_order_tax').val();
	var finalTotal = jQuery('#pg_addorder_order_total').val();

	subTotal = parseFloat(subTotal) - (parseFloat(jQuery('#pg_addorder_item_price_without_tax'+counter).val())*oldQty);
	totalTax = parseFloat(totalTax) - (parseFloat(jQuery('#pg_addorder_item_tax'+counter).val())*oldQty);
	finalTotal = parseFloat(finalTotal) - (parseFloat(jQuery('#pg_addorder_item_total_price'+counter).val()));

	itemTotalPrice = (parseFloat(jQuery('#pg_addorder_item_price_without_tax'+counter).val()) + parseFloat(jQuery('#pg_addorder_item_tax'+counter).val())) * newQty;
	jQuery('#pg_addorder_item_total_price'+counter).val(itemTotalPrice);
	subTotal = parseFloat(subTotal) + (parseFloat(jQuery('#pg_addorder_item_price_without_tax'+counter).val())*newQty);
	totalTax = parseFloat(totalTax) + (parseFloat(jQuery('#pg_addorder_item_tax'+counter).val())*newQty);
	finalTotal = parseFloat(finalTotal) + (parseFloat(itemTotalPrice));

	jQuery('#pg_addorder_order_subtotal').val(subTotal);
	jQuery('#pg_addorder_order_subtotal_div').html(subTotal);
	jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');
	jQuery('#pg_addorder_item_qty'+counter+'').val(newQty);

	jQuery('#pg_addorder_order_tax').val(totalTax);
	jQuery('#pg_addorder_order_tax_div').html(totalTax);
	jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');

	jQuery('#pg_addorder_order_total').val(finalTotal);
	jQuery('#pg_addorder_order_total_div').html(finalTotal);
	jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');

//	jQuery('#pg_addorder_order_shipping').val(shippingPrice);
//	jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
//	jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');

	displayShippingOptions(counter);
}

function displayShippingOptions(counter)
{
	var saddressId = jQuery('#pg-shipping_address').val();
	var userId = jQuery('#pg-filter_user').val();
	var itemsArray = [];
	var items = "";
	var j=0;
	for(i=1;i<=counter;i++)
	{
		if(jQuery("#pg_addorder_item_id"+i) && jQuery("#pg_addorder_item_id"+i).val()!=0)
		{
			//itemsArray[j] = jQuery("#pg_addorder_item_id"+i).val();
			var item = {
				"itemid": jQuery("#pg_addorder_item_id"+i).val(),
				"qty": jQuery("#pg_addorder_item_qty"+i).val(),
				};
			itemsArray.push(item);
		}
		
		j++;
	}
	items = JSON.stringify({itemsArray: itemsArray});
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "addorder",
			task : "displayShippingOptions",
			dataType: 'json',
			items :items,
			saddress_id : saddressId,
			user_id : userId
		}),
		success: function( response ) {
			var str=response.split("###");
			jQuery( "#pg-shipping_options").html(str[0]);

			var finalTotal = jQuery('#pg_addorder_order_total').val() - jQuery('#pg_addorder_order_shipping_old').val();
			var shippingPrice = str[1];
			finalTotal = parseFloat(finalTotal) + parseFloat(shippingPrice);

			// Update order Subtotal /Tax/ Total
			var totalTaxOnShipping = 0;
			var totaltax = 0;

			for(i=1;i<=counter;i++)
			{
				var itemTax = jQuery('#pg_addorder_item_tax'+i+'').val() * jQuery('#pg_addorder_item_quantity'+i).val();
				if (jQuery('#pg_addorder_item_free_shipping'+i+'').val() == 0  && jQuery('#pg_addorder_apply_tax_on_shipping'+i+'').val() == 1)
				{
					if(parseFloat(jQuery('#pg_addorder_item_price_without_tax'+i+'').val() ) && shippingPrice > 0)
					{
						var itemShipping = ( parseFloat(shippingPrice) * (jQuery('#pg_addorder_item_quantity'+i).val() * parseFloat(jQuery('#pg_addorder_item_price_without_tax'+i+'').val() ))  )/ parseFloat(jQuery('#pg_addorder_order_subtotal').val() )
						var itemTaxOnShipping = (parseFloat(jQuery('#pg_addorder_item_tax_rate'+i+'').val() ) * parseFloat(itemShipping)) / 100;
						finalTotal = parseFloat(finalTotal) - parseFloat(jQuery('#pg_addorder_item_shipping_tax'+i+'').val());

						jQuery('#pg_addorder_item_shipping_tax'+i+'').val(itemTaxOnShipping);

						totalTaxOnShipping = parseFloat(totalTaxOnShipping) + parseFloat(itemTaxOnShipping);
						totaltax =  parseFloat(totaltax) + parseFloat(itemTax) + parseFloat(itemTaxOnShipping);
					}
				}
			}

			if(totaltax > 0)
			{
				finalTotal = parseFloat(finalTotal) + parseFloat(totalTaxOnShipping);

				jQuery('#pg_addorder_order_tax').val(totaltax);
				jQuery('#pg_addorder_order_tax_div').html(totaltax);
				jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
			}

			jQuery('#pg_addorder_order_shipping').val(shippingPrice);
			jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
			jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
			jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);

			jQuery('#pg_addorder_order_total').val(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
			jQuery('#pg_addorder_order_total_div').html(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
			jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');

		}
	});
}

function getAddressInformation(userId, addressId, saddressId)
{
	jQuery.ajax({
        type: "POST",
        url: 'index.php',
		data: 'option=com_pago&view=addorder&task=getAddressInformation&saddressId=' + saddressId+ '&addressId=' + addressId+ '&userid=' + userId+ '&async=1',
        success: function(response)
        {
         	if (response == '')
        	{
        		jQuery( "#addBillingAddress").show();
        		jQuery( "#Billingaddress").hide();
        		jQuery( "#Shippingaddress" ).hide();

        		jQuery( "#address_billing_company" ).val( response );
				jQuery( "#address_billing_first_name" ).val(response );
				jQuery( "#address_billing_last_name" ).val( response);
				jQuery( "#address_billing_middle_name" ).val( response );
				jQuery( "#address_billing_title" ).val( response );
				jQuery( "#address_billing_address_1" ).val( response );
				jQuery( "#address_billing_address_2" ).val( response );
				jQuery( "#address_billing_city" ).val( response );
				jQuery( "#address_billing_state" ).val( response );
				jQuery('.pg-stateslist #uniform-address_billing_regionaddress_billingregion select').siblings("span").html(response);
				jQuery('.pg-countrieslist #uniform-address_billing_countryaddress_billingcountry select').siblings("span").html(response);
				jQuery( "#address_billing_country" ).val( response );
				jQuery( "#address_billing_zip" ).val( response );
				jQuery( "#address_billing_user_email" ).val( response);
				jQuery( "#address_billing_phone_1" ).val(response );
				jQuery( "#address_billing_phone_2" ).val( response );
				jQuery( "#address_billing_fax" ).val( response );
				jQuery( "#address_mailing_company" ).val(response );
				jQuery( "#address_mailing_first_name" ).val( response );
				jQuery( "#address_mailing_last_name" ).val( response );
				jQuery( "#address_mailing_middle_name" ).val( response );
				jQuery( "#address_mailing_title" ).val( response );
				jQuery( "#address_mailing_address_1" ).val( response);
				jQuery( "#address_mailing_address_2" ).val( response );
				jQuery( "#address_mailing_city" ).val( response );
				jQuery( "#address_mailing_state" ).val( response );
				jQuery('.pg-stateslist #uniform-address_mailing_regionaddress_mailingregion select').siblings("span").html(response);
				jQuery('.pg-countrieslist #uniform-address_mailing_countryaddress_mailingcountry select').siblings("span").html(response);
				jQuery( "#address_mailing_country" ).val( response);
				jQuery( "#address_mailing_zip" ).val( response );
				jQuery( "#address_mailing_user_email" ).val(response );
				jQuery( "#address_mailing_phone_1" ).val( response );
				jQuery( "#address_mailing_phone_2" ).val( response);
				jQuery( "#address_mailing_fax" ).val( response);
        	}
        	else
        	{
        		var str=response.split("####");
	 			jQuery( "#Billingaddress" ).html( str[0] );
				jQuery( "#Billingaddress" ).show();
				jQuery( "#Shippingaddress" ).html( str[1] );
				jQuery( "#Shippingaddress" ).show();
			}
        }
    });
}

function getUserAddress(userId, addressId, saddressId)
{
	jQuery.ajax({
	        type: "POST",
	        url: 'index.php',
			data: 'option=com_pago&view=addorder&task=getUserAddress&saddressId=' + saddressId+ '&addressId=' + addressId+ '&userid=' + userId+ '&async=1',
	        success: function(response)
	        {
	         	if (response == '')
	        	{

	        	}
	        	else
	        	{
	        		var address=response.split("####");
	        		var str=address[0].split(":");

	        		jQuery( "#address_billing_company" ).val( str[2] );
	        		jQuery( "#address_billing_first_name" ).val( str[5] );
	        		jQuery( "#address_billing_last_name" ).val( str[4] );
	        		jQuery( "#address_billing_middle_name" ).val( str[6] );
	        		jQuery( "#address_billing_title" ).val( str[3] );
	        		jQuery( "#address_billing_address_1" ).val( str[10] );
	        		jQuery( "#address_billing_address_2" ).val( str[11] );
	        		jQuery( "#address_billing_city" ).val( str[12] );
	        		jQuery( "#address_billing_state" ).val( str[13] );
	        		jQuery("#address_billing_regionaddress_billingregion option[value='"+str[13]+"']").attr("selected", "selected");
	        		jQuery('.pg-stateslist #uniform-address_billing_regionaddress_billingregion select').siblings("span").html(str[13]);
	        		jQuery('.pg-countrieslist #uniform-address_billing_countryaddress_billingcountry select').siblings("span").html(str[14]);
        			jQuery("#address_billing_regionaddress_billingcountry option[value='"+str[14]+"']").attr("selected", "selected");
	        		jQuery( "#address_billing_country" ).val( str[14] );
	        		jQuery( "#address_billing_zip" ).val( str[15] );
        			jQuery( "#address_billing_user_email" ).val( str[16] );
        			jQuery( "#address_billing_phone_1" ).val( str[7] );
        			jQuery( "#address_billing_phone_2" ).val( str[8] );
        			jQuery( "#address_billing_fax" ).val( str[9] );
					jQuery( "#Billingaddress" ).show();

					var mstr=address[1].split(":");

	        		jQuery( "#address_mailing_company" ).val( mstr[2] );
	        		jQuery( "#address_mailing_first_name" ).val( mstr[5] );
	        		jQuery( "#address_mailing_last_name" ).val( mstr[4] );
	        		jQuery( "#address_mailing_middle_name" ).val( mstr[6] );
	        		jQuery( "#address_mailing_title" ).val( mstr[3] );
	        		jQuery( "#address_mailing_address_1" ).val( mstr[10] );
	        		jQuery( "#address_mailing_address_2" ).val( mstr[11] );
	        		jQuery( "#address_mailing_city" ).val( mstr[12] );
	        		jQuery( "#address_mailing_state" ).val( mstr[13] );
	        		jQuery("#address_billing_regionaddress_mailingregion option[value='"+mstr[13]+"']").attr("selected", "selected");
	        		jQuery('.pg-stateslist #uniform-address_mailing_regionaddress_mailingregion select').siblings("span").html(mstr[13]);
	        		jQuery('.pg-countrieslist #uniform-address_mailing_countryaddress_mailingcountry select').siblings("span").html(mstr[14]);
	        		jQuery("#address_billing_regionaddress_mailingcountry option[value='"+mstr[14]+"']").attr("selected", "selected");
	        		jQuery( "#address_mailing_country" ).val( mstr[14] );
	        		jQuery( "#address_mailing_zip" ).val( mstr[15] );
        			jQuery( "#address_mailing_user_email" ).val( mstr[16] );
        			jQuery( "#address_mailing_phone_1" ).val( mstr[7] );
        			jQuery( "#address_mailing_phone_2" ).val( mstr[8] );
        			jQuery( "#address_mailing_fax" ).val( mstr[9] );
		 			//jQuery( "#Billingaddress" ).html( response );
					jQuery( "#Shippingaddress" ).show();
				}
	        }
	    });
}

function removeOrderItem(order_id, item_id)
{
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "ordersi",
			task : "removeOrderItem",
			dataType: 'json',
			order_id : order_id,
			item_id : item_id
		}),
		success: function( response ) {
			jQuery( "#pg_order_item_"+item_id ).hide();
			json = jQuery.parseJSON(response);

			jQuery.each( json, function( key, value ) {
				if(key == 'order_total')
				{
					jQuery('#pg_addorder_order_total').val(value);
					jQuery('#pg_addorder_order_total_div').html(value);
					jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');

				}
				if(key == 'order_subtotal')
				{
					jQuery('#pg_addorder_order_subtotal').val(value);
					jQuery('#pg_addorder_order_subtotal_div').html(value);
					jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');

				}
            });

            	// Update Order Items rows
				jQuery.ajax({
					type: 'POST',
					url: 'index.php',
					data: ({
						option: "com_pago", // global field
						controller: "ordersi",
						task : "updateOrderItemRows",
						dataType: 'json',
						order_id : order_id,
					}),
					success: function( response ) {
						//jQuery( "#pg_order_item_"+item_id ).hide();
						jQuery( "#pg_order_item_rows").html(response);
					}
				});
		}
	});

}

function updateOrderSatus(newStatus)
{
	var order_id = jQuery('#pg_ordersi_order_id').val();
	// Update Order Status
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "ordersi",
			task : "updateOrderSatus",
			dataType: 'json',
			order_id : order_id,
			order_status : newStatus,
		}),
		success: function( response ) {
			jQuery( "#pg-order_status_update_message").html(response);
		}
	});
}

function refundOrderPayment()
{
	var order_id = jQuery('#pg_ordersi_order_id').val();
	var refund_amount = jQuery('#refund_partial'+order_id).val();

	// Refund Order Amount
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "ordersi",
			task : "refundOrderPayment",
			dataType: 'json',
			order_id : order_id,
			refund_amount : refund_amount,
		}),
		success: function( response ) {
			//jQuery( "#pg-order_status_update_message").html(response);
			alert(response);
			var refund_total=response.split("###");
			jQuery( "#pg-order_refundtotal").html(refund_total[0]);
			var refund_total_original=refund_total[0].split("$");
			jQuery( "#pg_addorder_order_refundtotal_div").html(refund_total_original[1]);
			jQuery('#pg_addorder_order_refundtotal_div').append('( '+CURRENCY_SYMBOL+' )');
			var remaining_order_amount = parseFloat(refund_total[1]) - parseFloat(refund_total_original[1]);
			jQuery('#refund_partial'+order_id).val(remaining_order_amount);
		}
	});

}
