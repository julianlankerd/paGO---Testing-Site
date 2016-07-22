<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
$doc = JFactory::getDocument();
$doc->addScriptDeclaration( "
	jQuery(document).ready(function(){
		jQuery('#pg-button-search').on('click',function(){	
		
	    	if(jQuery('#filter_search').val()==''){
	    	
		    	return false;
		   	}
		});
		jQuery('#pg-button-clear').on('click',function(){	
	    	if(jQuery('#filter_search').val()!='' ){
			  document.id('filter_search').value='';
			 jQuery('#adminForm').submit();
			  	
		   	}else{
		    	return false;
		    }
		});
		var counter = 1;

		function createAutocomplete(counter)
		{
			jQuery('#pg_addorder_item_c_name'+counter+'').multiselect({
			multiple: false,
			header: 'Select Item',
			noneSelectedText: 'Select Item',
			selectedList: 1,
			click: function(event, ui){
					//jQuery('#pg_addorder_item_id'+counter+'').val(ui.value);
					var userId     = jQuery('#pg-filter_user').val();
					var addressId  = jQuery('#pg-billing_address').val();
					var saddressId = jQuery('#pg-shipping_address').val();
			          jQuery.ajax({
				        type: 'POST',
				        url: 'index.php',
						data: 'option=com_pago&view=addorder&task=getItemDetail&item_id=' +ui.value+ '&user_id='+userId+'&saddressId=' + saddressId+ '&addressId=' + addressId,
				        success: function(response)
				        {
				         	if (response == '')
				        	{

				        	}
				        	else
				        	{
								var str        =response.split('##');
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
		        },
		    });

			jQuery('#pg_addorder_item_quantity'+counter+'').live('keyup',function(){
				updateOrderTotal(counter);
			});

			jQuery('#pg_addorder_item_c_name'+counter+'').live('focus',function(){
			      if(jQuery('#pg_addorder_c_item_name'+counter+'').val() == 'Enter Item Name'){
			          jQuery('#pg_addorder_c_item_name'+counter+'').val('');
			      }
		 	});

			jQuery('#pg_addorder_item_c_name'+counter+'').live('blur',function(){
			      if(jQuery('#pg_addorder_item_c_name'+counter+'').val() == ''){
			          jQuery('#pg_addorder_item_c_name'+counter+'').val('Enter Item Name');
			      }
		 	});

			jQuery('#pg_addorder_item_remove'+counter+'').live('click',function(){
				if(window.confirm('Are you sure you want to delete?'))
				{
					jQuery('#pg-items-manager #order_item_tr'+counter+'').remove();
					counter = counter-1;
					calculateOrderTotal(counter);
				}
			});
		// as
		}
		// assign item
		jQuery('#pg_addorder_item_c_name1').multiselect({
		multiple: false,
		header: 'Select Item',
		noneSelectedText: 'Select Item',
		selectedList: 1,
		click: function(event, ui){
      				//jQuery('#pg_addorder_item_id1').val(ui.value);
      				var userId = jQuery('#pg-filter_user').val();
		        	var addressId = jQuery('#pg-billing_address').val();
					var saddressId = jQuery('#pg-shipping_address').val();

					jQuery.ajax({
				        type: 'POST',
				        url: 'index.php',
						data: 'option=com_pago&view=addorder&task=getItemDetails&item_id=' +ui.value+ '&user_id='+userId+'&saddressId=' + saddressId+ '&addressId=' + addressId,
				        success: function(response)
				        {
				         	if (response == '')
				        	{

	        			}
			        	else
			        	{
			        		jQuery('.itemDetails').html(response);
			        		jQuery('.itemDetails').css('display','block');
						}
	       			 }
	    		});
			},
			close: function(ecent, ui)
			{
				jQuery('.ui-multiselect-menu').css('display','none');
			},
			open: function(ecent, ui)
			{
				jQuery('.ui-multiselect-menu').css('display','block');
			}
			}).multiselectfilter({
			    filter: function(event, matches){
			        // find the first matching checkbox
			        var first_match = jQuery( matches[0] );
			    }
			});
		
		
		// For edit order
	jQuery('#pg-ordersi_item_save_btn').live('click',function(){
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
			option: 'com_pago', // global field
			controller: 'ordersi',
			task : 'saveOrderItem',
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
					
					if(CURRENCY_SYMBOL_POSITION == '1')
					{
						jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
					
					}
					else
					{
						jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
					}

				}
				if(key == 'order_subtotal')
				{
					jQuery('#pg_addorder_order_subtotal').val(value);
					jQuery('#pg_addorder_order_subtotal_div').html(value);
					if(CURRENCY_SYMBOL_POSITION == '1')
					{
						jQuery('#pg_addorder_order_subtotal_div').prepend('( '+CURRENCY_SYMBOL+' )');
					}
					else
					{
						jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');
					}

				}
            });

            	// Update Order Items rows
				jQuery.ajax({
					type: 'POST',
					url: 'index.php',
					data: ({
						option: 'com_pago', // global field
						controller: 'ordersi',
						task : 'updateOrderItemRows',
						dataType: 'json',
						order_id : order_id,
					}),
					success: function( response ) {
						jQuery( '#pg_order_item_rows').html(response);
					}
				});
		}
	});
    });

	jQuery('#pg_addorder_item_quantity1').live('keyup',function(){
		updateOrderTotal(1);
	});
	jQuery('#pg_ordersi_item_quantity1').live('keyup',function(){
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
	
	jQuery('#pg_ordersi_item_name1').live('focus',function(){
	      if(jQuery('#pg_ordersi_item_name1').val() == 'Enter Item Name'){
	          jQuery('#pg_ordersi_item_name1').val('');
	      }
	 });

	jQuery('#pg_ordersi_item_name1').live('blur',function(){
	      if(jQuery('#pg_ordersi_item_name1').val() == ''){
	          jQuery('#pg_ordersi_item_name1').val('Enter Item Name');
	      }
	 });
	jQuery('#pg_ordersi_item_c_name').multiselect({
		multiple: false,
		header: 'Select Item',
		noneSelectedText: 'Select Item',
		selectedList: 1,
		click: function(event, ui){
      		var order_id = jQuery('#pg_ordersi_order_id').val();
    		var userId = jQuery('#pg-filter_user').val();
    		var addressId = jQuery('#pg-billing_address').val();
			var saddressId = jQuery('#pg-shipping_address').val();
			jQuery.ajax({
		        type: 'POST',
	    	    url: 'index.php',
				data: 'option=com_pago&view=addorder&task=getItemDetails&item_id=' +ui.value+ '&user_id='+userId+'&saddressId=' + saddressId+ '&addressId=' + addressId+ '&order_id=' +order_id,
	        	success: function(response)
	        	{
	         		if (response == '')
	        		{
    				}
	        		else
	        		{
	        			jQuery('.itemDetails').html(response);
	        			jQuery('.itemDetails').css('display','block');
					}
				}
		    });
   		},
   		close: function(ecent, ui)
		{
			jQuery('.ui-multiselect-menu').css('display','none');
		},
		open: function(ecent, ui)
		{
			jQuery('.ui-multiselect-menu').css('display','block');
		}
		}).multiselectfilter({
		    filter: function(event, matches){
		        // find the first matching checkbox
		        var first_match = jQuery( matches[0] );
		}
	});
		jQuery( '#savebtn' ).live('click',function(){
			jQuery('#task').val('saveOrderUser');
			jQuery( '#adminForm' ).submit();
		});

		jQuery( '#pg-billing_address' ).live('change',function(){
			var addressId = jQuery('#pg-billing_address').val();
			var userId = jQuery('#pg-filter_user').val();
			var type = jQuery(this).attr('id'); 
			getUserAddress(type, userId, addressId);

		});
		jQuery( '#pg-shipping_address' ).live('change',function(){
			var addressId = jQuery('#pg-billing_address').val();
			var saddressId = jQuery('#pg-shipping_address').val();
			var userId = jQuery('#pg-filter_user').val();
			var type = jQuery(this).attr('id');
			getUserAddress(type, userId, addressId, saddressId);

		});
		// Same as Billing
		jQuery( '#address_mailing_same_as_billing' ).live('change',function(){
				if( jQuery('#address_mailing_same_as_billing').is(':checked') )
				{
					var addressId = jQuery('#pg-billing_address').val();
					if(addressId)
					{
						jQuery('#pg-shipping_address').val(addressId);
					}
					jQuery( '#addShippingaddress').hide();
				}
				else
				{
					jQuery( '#addShippingaddress').show();
				}

		});
		jQuery('#pg-order_item_add_btn').live('click',function(){
	    	counter = counter +1;
	    	addnewRow(counter);
			createAutocomplete(counter);
	    });
		jQuery('input:radio[name=payment_option]').live('change',function(){

			if(!jQuery('#pg_addorder_item_id1').val())
			{
				alert('Please add Item first');
				return false;
			}
		});

		jQuery('#pg_addorder_product_shipping_type').live('change',function(){
			var shippingPrice = 0;
			for(i=1;i<=counter;i++)
			{
				var itemId = jQuery('#pg_addorder_item_id'+i+'').val();
				var selected = jQuery('input:radio[name=\"carrier_option['+itemId+']\"]:checked');
				if (selected.length > 0) {
				    selectedVal = selected.val();
				    var shippingOptions = selectedVal.split('|');
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
				if(CURRENCY_SYMBOL_POSITION == '1')
				{
					jQuery('#pg_addorder_order_tax_div').prepend('( '+CURRENCY_SYMBOL+' )');
				}
				else
				{
					jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
				}
			}

			jQuery('#pg_addorder_order_shipping').val(shippingPrice);
			jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
			
			if(CURRENCY_SYMBOL_POSITION == '1')
			{
				jQuery('#pg_addorder_order_shipping_div').prepend('( '+CURRENCY_SYMBOL+' )');
			}
			else
			{
				jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
			}
			jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);

			jQuery('#pg_addorder_order_total').val(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
			jQuery('#pg_addorder_order_total_div').html(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
			
			if(CURRENCY_SYMBOL_POSITION == '1')
			{
				jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
			
			}
			else
			{
				jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
			}
		});

		jQuery('input:radio[name=carrier_option]').live('change',function(){

			var selected = jQuery('input[type=radio][name=carrier_option]:checked');
			if (selected.length > 0) {
			    selectedVal = selected.val();
			}
			var finalTotal = jQuery('#pg_addorder_order_total').val() - jQuery('#pg_addorder_order_shipping_old').val();
			var shipping = selectedVal;
			var shippingOptions = shipping.split('|');
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
				if(CURRENCY_SYMBOL_POSITION == '1')
				{
					jQuery('#pg_addorder_order_tax_div').prepend('( '+CURRENCY_SYMBOL+' )');
				}
				else
				{
					jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
				}
			}

			jQuery('#pg_addorder_order_shipping').val(shippingPrice);
			jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
			
			if(CURRENCY_SYMBOL_POSITION == '1')
			{
				jQuery('#pg_addorder_order_shipping_div').prepend('( '+CURRENCY_SYMBOL+' )');
			}
			else
			{
				jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
			}
			jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);

			jQuery('#pg_addorder_order_total').val(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
			jQuery('#pg_addorder_order_total_div').html(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
			if(CURRENCY_SYMBOL_POSITION == '1')
{
	jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');

}
else
{
	jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
}
		});

	})
" );
?>

<script type="text/javascript">

var last_quickview="";jQuery.noConflict();
var errorMessage  = "";
var Count = 1;
jQuery( document ).ready(function() {
	
	
});
function addnewRow(counter)
{
	var it = jQuery('#pg_addorder_item_id'+counter).val()
		counter = Count;
		var newRow = jQuery('<tr class=pg-sub-heading new-item-con id=order_item_tr'+counter+'>' +
	    	counter + '<td><span class=new-currency-field><div id=pg_addorder_item_remove'+counter+'>Remove</div></span></td>' +
	    	counter + '<td><span class=new-item-field new-item-name><input type=text id=pg_addorder_item_name'+counter+' name=pg_addorder_item_name'+counter+' readonly=true /><input type="hidden" name="pg_addorder_item_id'+counter+'" id="pg_addorder_item_id'+counter+'" value="" variationId="0"></span></td><input type="hidden" name="pg_addorder_item_variation_id'+counter+'" id="pg_addorder_item_variation_id'+counter+'" value="" >' +
	        counter + '<td><span class=new-item-field><input type=text id=pg_addorder_item_price_without_tax'+counter+' name=pg_addorder_item_price_without_tax'+counter+' readonly=true /></span></td>' +
	        counter + '<td><span class=new-item-field><input type=text id=pg_addorder_item_tax'+counter+' name=pg_addorder_item_tax'+counter+' readonly=true /><input type=hidden id=pg_addorder_item_with_tax'+counter+' name=pg_addorder_item_with_tax'+counter+'   readonly=true/><input type=hidden id=pg_addorder_item_tax_rate'+counter+' name=pg_addorder_item_tax_rate'+counter+'   readonly=true/><input type=hidden id=pg_addorder_apply_tax_on_shipping'+counter+' name=pg_addorder_apply_tax_on_shipping'+counter+'  readonly=true/><input type=hidden id=pg_addorder_item_free_shipping'+counter+' name=pg_addorder_item_free_shipping'+counter+'  readonly=true/><input type=hidden id=pg_addorder_item_shipping_tax'+counter+' name=pg_addorder_item_shipping_tax'+counter+' value=0 readonly=true/></span></td>' +
	        counter + '<td><span class=new-item-field><input type=text id=pg_addorder_item_quantity'+counter+' name=pg_addorder_item_quantity'+counter+' onKeyup=updateOrderTotal('+counter+') /><input type=hidden name=pg_addorder_item_qty'+counter+' id=pg_addorder_item_qty'+counter+'  ></span></td>' +
	        counter + '<td><span class=new-currency-field><input type=text id=pg_addorder_item_total_price'+counter+' name=pg_item_total_price'+counter+' readonly=true /></span></td>' +
	        counter + '</tr>');
	    jQuery('#pg-items-manager').append(newRow);
	
}

function addnewRowO(counter)
{
	var it = jQuery('#pg_ordersi_item_id'+counter).val()
	counter = Count;
	var newRow = jQuery('<tr class=pg-sub-heading new-item-con id=order_item_tr'+counter+'>' +
    	counter + '<td><span class=new-currency-field><div id=pg_ordersi_item_remove'+counter+'>Remove</div></span></td>' +
    	counter + '<td><span class=new-item-field new-item-name><input type=text id=pg_ordersi_item_name'+counter+' name=pg_ordersi_item_name'+counter+' readonly=true /></span><input type="hidden" name="pg_ordersi_item_id'+counter+'" id="pg_ordersi_item_id'+counter+'" value="" variationId="0"></td><input type="hidden" name="pg_ordersi_item_variation_id'+counter+'" id="pg_ordersi_item_variation_id'+counter+'" value="" >' +
        counter + '<td><span class=new-item-field><input type=text id=pg_ordersi_item_price_without_tax'+counter+' name=pg_ordersi_item_price_without_tax'+counter+' readonly=true /></span></td>' +
        counter + '<td><span class=new-item-field><input type=text id=pg_ordersi_item_tax'+counter+' name=pg_ordersi_item_tax'+counter+' readonly=true /><input type=hidden id=pg_ordersi_item_with_tax'+counter+' name=pg_ordersi_item_with_tax'+counter+'   readonly=true/><input type=hidden id=pg_ordersi_item_tax_rate'+counter+' name=pg_ordersi_item_tax_rate'+counter+'   readonly=true/><input type=hidden id=pg_ordersi_apply_tax_on_shipping'+counter+' name=pg_ordersi_apply_tax_on_shipping'+counter+'  readonly=true/><input type=hidden id=pg_ordersi_item_free_shipping'+counter+' name=pg_ordersi_item_free_shipping'+counter+'  readonly=true/><input type=hidden id=pg_ordersi_item_shipping_tax'+counter+' name=pg_ordersi_item_shipping_tax'+counter+' value=0 readonly=true/></span></td>' +
        counter + '<td><span class=new-item-field><input type=text id=pg_ordersi_item_quantity'+counter+' name=pg_ordersi_item_quantity'+counter+' onKeyup=updateOrderTotalO('+counter+') /><input type=hidden name=pg_ordersi_item_qty'+counter+' id=pg_ordersi_item_qty'+counter+'  ></span></td>' +
        counter + '<td><span class=new-currency-field><input type=text id=pg_ordersi_item_total_price'+counter+' name=pg_item_total_price'+counter+' readonly=true /></span></td>' +
        counter + '</tr>');
    jQuery('#pg-items-manager').next().append(newRow);
	
}

function calculateOrderTotal(counter, c)
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
	for(i=1;i<=c;i++)
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
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_subtotal_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	jQuery('#pg_addorder_order_tax').val(totalTax);
	jQuery('#pg_addorder_order_tax_div').html(totalTax);
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_tax_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	jQuery('#pg_addorder_order_total').val(finalTotal);
	jQuery('#pg_addorder_order_total_div').html(finalTotal);
	
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
	
	}
	else
	{
		jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	jQuery('#pg_addorder_order_shipping').val(shippingPrice);
	jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);
	jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_shipping_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
	}


	displayShippingOptions(counter);

}

function calculateOrderTotalO(counter, c)
{
	var subTotal = 0;
	var totalTax = 0;
	var finalTotal = 0;
	var shippingPrice = 0;
	var Qty = jQuery('#pg_ordersi_item_quantity'+counter+'').val();
	if(Qty <= 0)
	{
		Qty = 1;
	}

	// Update Item Total Price
	if(parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+counter+'').val()))
	{
		var itemTotalPrice = ( parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+counter+'').val()) *Qty ) + ( parseFloat(jQuery('#pg_ordersi_item_tax'+counter+'').val()) * Qty);
		jQuery('#pg_ordersi_item_total_price'+counter+'').val(itemTotalPrice);

		var itemPriceWithVat = ( parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+counter+'').val())) + ( parseFloat(jQuery('#pg_ordersi_item_tax'+counter+'').val()));
		jQuery('pg_ordersi_item_with_tax').val(itemPriceWithVat);
	}

	// Update order Subtotal /Tax/ Total
	for(i=1;i<=c;i++)
	{
		if(parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+i+'').val() ))
		{
			rowQty = jQuery('#pg_ordersi_item_quantity'+i).val();
			subTotal = parseFloat(subTotal) + (parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+i).val())*rowQty);
			totalTax = parseFloat(totalTax) + (parseFloat(jQuery('#pg_ordersi_item_tax'+i).val())*rowQty);
			finalTotal = parseFloat(finalTotal) + (parseFloat(jQuery('#pg_ordersi_item_total_price'+i).val()));
			jQuery('#pg_ordersi_item_qty'+i).val(rowQty);
		}
	}

	jQuery('#pg_addorder_order_subtotal').val(subTotal);
	jQuery('#pg_addorder_order_subtotal_div').html(subTotal);
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_subtotal_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	jQuery('#pg_addorder_order_tax').val(totalTax);
	jQuery('#pg_addorder_order_tax_div').html(totalTax);
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_tax_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	jQuery('#pg_addorder_order_total').val(finalTotal);
	jQuery('#pg_addorder_order_total_div').html(finalTotal);
	
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
	
	}
	else
	{
		jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	jQuery('#pg_addorder_order_shipping').val(shippingPrice);
	jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);
	jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_shipping_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
	}


	//displayShippingOptionsO(counter);

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
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_subtotal_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');
	}
	jQuery('#pg_addorder_item_qty'+counter+'').val(newQty);

	jQuery('#pg_addorder_order_tax').val(totalTax);
	jQuery('#pg_addorder_order_tax_div').html(totalTax);
	
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_tax_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	jQuery('#pg_addorder_order_total').val(finalTotal);
	jQuery('#pg_addorder_order_total_div').html(finalTotal);
	
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
	
	}
	else
	{
		jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	//	jQuery('#pg_addorder_order_shipping').val(shippingPrice);
	//	jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);

	displayShippingOptions(counter);
}

function updateOrderTotalO(counter)
{
	var oldQty = jQuery('#pg_ordersi_item_qty'+counter+'').val();
	var newQty = jQuery('#pg_ordersi_item_quantity'+counter+'').val();
	if(newQty <= 0)
	{
		newQty = 1;
	}

	var subTotal = jQuery('#pg_addorder_order_subtotal').val();
	var totalTax = jQuery('#pg_addorder_order_tax').val();
	var finalTotal = jQuery('#pg_addorder_order_total').val();

	subTotal = parseFloat(subTotal) - (parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+counter).val())*oldQty);
	totalTax = parseFloat(totalTax) - (parseFloat(jQuery('#pg_ordersi_item_tax'+counter).val())*oldQty);
	finalTotal = parseFloat(finalTotal) - (parseFloat(jQuery('#pg_ordersi_item_total_price'+counter).val()));

	itemTotalPrice = (parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+counter).val()) + parseFloat(jQuery('#pg_ordersi_item_tax'+counter).val())) * newQty;
	jQuery('#pg_ordersi_item_total_price'+counter).val(itemTotalPrice);
	subTotal = parseFloat(subTotal) + (parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+counter).val())*newQty);
	totalTax = parseFloat(totalTax) + (parseFloat(jQuery('#pg_ordersi_item_tax'+counter).val())*newQty);
	finalTotal = parseFloat(finalTotal) + (parseFloat(itemTotalPrice));

	jQuery('#pg_addorder_order_subtotal').val(subTotal);
	jQuery('#pg_addorder_order_subtotal_div').html(subTotal);
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_subtotal_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');
	}
	jQuery('#pg_ordersi_item_qty'+counter+'').val(newQty);

	jQuery('#pg_addorder_order_tax').val(totalTax);
	jQuery('#pg_addorder_order_tax_div').html(totalTax);
	
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_tax_div').prepend('( '+CURRENCY_SYMBOL+' )');
	}
	else
	{
		jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
	}

	jQuery('#pg_addorder_order_total').val(finalTotal);
	jQuery('#pg_addorder_order_total_div').html(finalTotal);
	
	if(CURRENCY_SYMBOL_POSITION == '1')
	{
		jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
	
	}
	else
	{
		jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
	}

//	jQuery('#pg_addorder_order_shipping').val(shippingPrice);
//	jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);

	displayShippingOptionsO(counter);
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
			var shippingPrice = str[1];

			if(parseFloat(jQuery('#pg_addorder_order_shipping_old').val()) && shippingPrice > 0 )
			{
				var finalTotal = jQuery('#pg_addorder_order_total').val() - jQuery('#pg_addorder_order_shipping_old').val();
				finalTotal = parseFloat(finalTotal) + parseFloat(shippingPrice);
			}

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
				
				if(CURRENCY_SYMBOL_POSITION == '1')
				{
					jQuery('#pg_addorder_order_tax_div').prepend('( '+CURRENCY_SYMBOL+' )');
				}
				else
				{
					jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
				}
			}
			if(shippingPrice > 0 )
			{
				jQuery('#pg_addorder_order_shipping').val(shippingPrice);
				jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
				
				if(CURRENCY_SYMBOL_POSITION == '1')
				{
					jQuery('#pg_addorder_order_shipping_div').prepend('( '+CURRENCY_SYMBOL+' )');
				}
				else
				{
					jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
				}
				jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);

				jQuery('#pg_addorder_order_total').val(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
				jQuery('#pg_addorder_order_total_div').html(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
				
				if(CURRENCY_SYMBOL_POSITION == '1')
				{
					jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
				
				}
				else
				{
					jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
				}
			}
		}
	});
}

function displayShippingOptionsO(counter)
{
	var saddressId = jQuery('#pg-shipping_address').val();
	var userId = jQuery('#pg-filter_user').val();
	var itemsArray = [];
	var items = "";
	var j=0;
	for(i=1;i<=counter;i++)
	{
		if(jQuery("#pg_ordersi_item_id"+i) && jQuery("#pg_ordersi_item_id"+i).val()!=0)
		{
			//itemsArray[j] = jQuery("#pg_addorder_item_id"+i).val();
			var item = {
				"itemid": jQuery("#pg_ordersi_item_id"+i).val(),
				"qty": jQuery("#pg_ordersi_item_qty"+i).val(),
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
			var shippingPrice = str[1];

			if(parseFloat(jQuery('#pg_addorder_order_shipping_old').val()) && shippingPrice > 0 )
			{
				var finalTotal = jQuery('#pg_addorder_order_total').val() - jQuery('#pg_addorder_order_shipping_old').val();
				finalTotal = parseFloat(finalTotal) + parseFloat(shippingPrice);
			}

			// Update order Subtotal /Tax/ Total
			var totalTaxOnShipping = 0;
			var totaltax = 0;

			for(i=1;i<=counter;i++)
			{
				var itemTax = jQuery('#pg_ordersi_item_tax'+i+'').val() * jQuery('#pg_ordersi_item_quantity'+i).val();
				if (jQuery('#pg_ordersi_item_free_shipping'+i+'').val() == 0  && jQuery('#pg_addorder_apply_tax_on_shipping'+i+'').val() == 1)
				{
					if(parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+i+'').val() ) && shippingPrice > 0)
					{
						var itemShipping = ( parseFloat(shippingPrice) * (jQuery('#pg_ordersi_item_quantity'+i).val() * parseFloat(jQuery('#pg_ordersi_item_price_without_tax'+i+'').val() ))  )/ parseFloat(jQuery('#pg_ordersi_order_subtotal').val() )
						var itemTaxOnShipping = (parseFloat(jQuery('#pg_ordersi_item_tax_rate'+i+'').val() ) * parseFloat(itemShipping)) / 100;
						finalTotal = parseFloat(finalTotal) - parseFloat(jQuery('#pg_ordersi_item_shipping_tax'+i+'').val());

						jQuery('#pg_ordersi_item_shipping_tax'+i+'').val(itemTaxOnShipping);

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
				
				if(CURRENCY_SYMBOL_POSITION == '1')
				{
					jQuery('#pg_addorder_order_tax_div').prepend('( '+CURRENCY_SYMBOL+' )');
				}
				else
				{
					jQuery('#pg_addorder_order_tax_div').append('( '+CURRENCY_SYMBOL+' )');
				}
			}
			if(shippingPrice > 0 )
			{
				jQuery('#pg_addorder_order_shipping').val(shippingPrice);
				jQuery('#pg_addorder_order_shipping_div').html(shippingPrice);
				
				if(CURRENCY_SYMBOL_POSITION == '1')
				{
					jQuery('#pg_addorder_order_shipping_div').prepend('( '+CURRENCY_SYMBOL+' )');
				}
				else
				{
					jQuery('#pg_addorder_order_shipping_div').append('( '+CURRENCY_SYMBOL+' )');
				}
				jQuery('#pg_addorder_order_shipping_old').val(shippingPrice);

				jQuery('#pg_addorder_order_total').val(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
				jQuery('#pg_addorder_order_total_div').html(parseFloat(totaltax) + parseFloat(shippingPrice) + parseFloat(jQuery('#pg_addorder_order_subtotal').val()));
				
				if(CURRENCY_SYMBOL_POSITION == '1')
				{
					jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
				
				}
				else
				{
					jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
				}
			}
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

				jQuery('#addBillingAddress').addClass('after-js-change');
				jQuery('#addShippingaddress').addClass('after-js-change');
        	}
        	else
        	{
        		var str=response.split("####");
        		if(str[0] == 1)
        		{
        			jQuery( "#Billingaddress" ).html( str[1] );
					jQuery( "#Billingaddress" ).show();
					jQuery( "#Shippingaddress" ).html( str[2] );
					jQuery( "#Shippingaddress" ).show();
        		}
        		else
        		{
        			jQuery( "#new-pago-user-div" ).html( str[1] );
					jQuery( "#Billingaddress" ).hide();
					jQuery( "#Shippingaddress" ).hide();

					var uinfo =str[2].split("_");
					var uname =uinfo[0].split(" ");
					jQuery( "#address_billing_first_name" ).val(uname[0]);
					jQuery( "#address_billing_last_name" ).val(uname[1]);
					jQuery( "#address_billing_user_email" ).val(uinfo[1]);

					jQuery( "#address_mailing_first_name" ).val(uname[0] );
					jQuery( "#address_mailing_last_name" ).val( uname[1]);
					jQuery( "#address_mailing_user_email" ).val( uinfo[1]);
        		}

				jQuery('#addBillingAddress').removeClass('after-js-change');
				jQuery('#addShippingaddress').removeClass('after-js-change');
			}

        }
    });
}

function getUserAddress(type, userId, addressId, saddressId)
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
        			jQuery("#address_billing_countryaddress_billingcountry option[value='"+str[14]+"']").attr("selected", "selected");
	        		jQuery( "#address_billing_country" ).val( str[14] );
	        		jQuery( "#address_billing_zip" ).val( str[15] );
        			jQuery( "#address_billing_user_email" ).val( str[16] );
        			jQuery( "#address_billing_phone_1" ).val( str[7] );
        			jQuery( "#address_billing_phone_2" ).val( str[8] );
        			jQuery( "#address_billing_fax" ).val( str[9] );
					jQuery( "#Billingaddress" ).show();

					var mstr=address[1].split(":");
					if(type != 'pg-billing_address'){
		        		jQuery( "#address_shipping_company" ).val( mstr[2] );
		        		jQuery( "#address_shipping_first_name" ).val( mstr[5] );
		        		jQuery( "#address_shipping_last_name" ).val( mstr[4] );
		        		jQuery( "#address_shipping_middle_name" ).val( mstr[6] );
		        		jQuery( "#address_shipping_title" ).val( mstr[3] );
		        		jQuery( "#address_shipping_address_1" ).val( mstr[10] );
		        		jQuery( "#address_shipping_address_2" ).val( mstr[11] );
		        		jQuery( "#address_shipping_city" ).val( mstr[12] );
		        		jQuery( "#address_shipping_state" ).val( mstr[13] );
		        		jQuery("#address_shipping_regionaddress_shippingregion option[value='"+mstr[13]+"']").attr("selected", "selected");
		        		jQuery('.pg-stateslist #uniform-address_shipping_regionaddress_shippingregion select').siblings("span").html(mstr[13]);
		        		jQuery('.pg-countrieslist #uniform-address_shipping_countryaddress_shippingcountry select').siblings("span").html(mstr[14]);
		        		jQuery("#address_shipping_countryaddress_mailingcountry option[value='"+mstr[14]+"']").attr("selected", "selected");
		        		jQuery( "#address_shipping_country" ).val( mstr[14] );
		        		jQuery( "#address_shipping_zip" ).val( mstr[15] );
	        			jQuery( "#address_shipping_user_email" ).val( mstr[16] );
	        			jQuery( "#address_shipping_phone_1" ).val( mstr[7] );
	        			jQuery( "#address_shipping_phone_2" ).val( mstr[8] );
	        			jQuery( "#address_shipping_fax" ).val( mstr[9] );
			 			//jQuery( "#Billingaddress" ).html( response );
						jQuery( "#Shippingaddress" ).show();
					}
				}
	        }
	    });
}

function removeOrderItem(order_id, item_id)
{
	if (!confirm("<?php echo JText::_('PAGO_ORDER_ITEM_REMOVE_CONFIRMATION'); ?>"))
		return false;
	
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
					
					if(CURRENCY_SYMBOL_POSITION == '1')
					{
						jQuery('#pg_addorder_order_total_div').prepend('( '+CURRENCY_SYMBOL+' )');
					
					}
					else
					{
						jQuery('#pg_addorder_order_total_div').append('( '+CURRENCY_SYMBOL+' )');
					}

				}
				if(key == 'order_subtotal')
				{
					jQuery('#pg_addorder_order_subtotal').val(value);
					jQuery('#pg_addorder_order_subtotal_div').html(value);
					if(CURRENCY_SYMBOL_POSITION == '1')
					{
						jQuery('#pg_addorder_order_subtotal_div').prepend('( '+CURRENCY_SYMBOL+' )');
					}
					else
					{
						jQuery('#pg_addorder_order_subtotal_div').append('( '+CURRENCY_SYMBOL+' )');
					}

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


function updateOrderSatus(newStatus, tracking_number, item_id)
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
			item_id : item_id,
			tracking_number : tracking_number,
		}),
		success: function( response ) {
				if(item_id != 0)
				{
					jQuery( "#pg-order_item_status_update_message").html(response);
				}
				else
				{
					jQuery( "#pg-order_status_update_message").html(response);
				}
		}
	});
}

function checkOrderwithMaxmind(order_id)
{
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago", // global field
			controller: "ordersi",
			task : "checkOrderwithMaxmind",
			dataType: 'json',
			order_id : order_id,
		}),
		success: function( response ) {
			jQuery( "#pg-order_fraud_message").html(response);
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
		success: function(response) {
			
			var res = jQuery.parseJSON(response);
			var message = '';
			
			if(res.message) message = res.message;
			
			jQuery('#pg-order_refundtotal').html(message + ' ' + res.refund_amount);
			jQuery('#refund_partial'+order_id).val(res.remaining_amount);
		}
	});

}




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////      FROM VIEW_ITEM.JS     ///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



jQuery(document).ready(function(){

	jQuery(document).on('click', '#pg-checkout-quick-cart-show-items', function(event){
		event.preventDefault();
		
		jQuery(this).toggleClass('open');
		jQuery('.pg-quick-cart-contents').slideToggle();
		if(jQuery(this).hasClass('open')) {
			jQuery(this).text('Hide Items');
		} else {
			jQuery(this).text('Show Items');
		}
	});

	jQuery('.pg-cart-expand').each(function() {
		jQuery(document).on('click', this, function(){		
			if(jQuery(this).hasClass('collapsed')) {
				jQuery(this).removeClass('collapsed').addClass('expanded').text('-');
				jQuery(this).siblings('.pg-cart-item-details').removeClass('collapsed').addClass('expanded');
			} else if(jQuery(this).hasClass('expanded')) {
				jQuery(this).removeClass('expanded').addClass('collapsed').text('+');
				jQuery(this).siblings('.pg-cart-item-details').removeClass('expanded').addClass('collapsed');
			}
		});
	});

	jQuery(document).on('click', '.pg-checkout-expand-all', function(event){
		event.preventDefault();
		if(jQuery(this).hasClass('collapsed')) {
			jQuery(this).removeClass('collapsed').addClass('expanded').text('Collapse All');
			jQuery('.pg-cart-item-details, .pg-cart-expand').removeClass('collapsed').addClass('expanded');
			jQuery('.pg-cart-expand').text('-');
		} else if(jQuery(this).hasClass('expanded')) {
			jQuery(this).removeClass('expanded').addClass('collapsed').text('Expand All');
			jQuery('.pg-cart-item-details, .pg-cart-expand').removeClass('expanded').addClass('collapsed');
			jQuery('.pg-cart-expand').text('+');
		}
	});

	jQuery(document).on('click', '#pago .product-container .pg-addtocart', function(event){
		event.preventDefault();

		jQuery('#pg-item-details .pg-notice').css('display','none');

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
							view: "addorder",
							task : "getVaration",
							dataType: 'json',
							async: false,
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
		}else{
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
		

		// if(selected_attribute_length == 1){
		// 	if(attribueInVaration(item_varations,selected_attributes)){
		// 		submit = true;
		// 	}
		// }
		var varId = false;
		if(selected_attribute_length > 0){
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				async: false,
				data: ({
					option: "com_pago",
					view: "addorder",
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
			obj.parent().parent().find('.pg-addtocart-success-text').html("PRODUCT_NOT_EXIST");
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
    			}else{
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
    		if(!varId){
    			varId = 0;
    		}

    		
			var userId     = jQuery('#pg-filter_user').val();
			var addressId  = jQuery('#pg-billing_address').val();
			var saddressId = jQuery('#pg-shipping_address').val();
			var qty        = jQuery('.pg-item-opt-qty').val();
			
	        jQuery.ajax({
		     	type: 'POST',
		        url: 'index.php',
				data: 'option=com_pago&view=addorder&task=getItemDetail&item_id=' +$itemID+ '&user_id='+userId+'&saddressId=' + saddressId+ '&addressId=' + addressId+'&qty='+qty+'&varId='+varId,
		        success: function(response)
		        {
		         	if (response == '')
		        	{

		        	}
		        	else
		        	{
		        		if(jQuery('.itemDetails').hasClass('ordersi')){
		        			var c = Count;
			        		var newR = true;
			        		for(var i=1; i<=Count-1; i++){

			        			if($itemID == jQuery('#pg_ordersi_item_id'+i).val() && varId == 0){
			        				c = i;
			        				Count--;
			        				newR = false;
			        				break;
			        			}
			        			else{
			        				if(varId == jQuery('#pg_ordersi_item_id'+i).attr('variationId') && $itemID == jQuery('#pg_ordersi_item_id'+i).val()){
			        					c = i;
				        				Count--;
				        				newR = false;
				        				break;
			        				}
			        			}
			        		}
			        		
			        		if(Count != 1 && newR == true){
					        	addnewRowO(Count);
					        }
			        		

			        		jQuery('#pg_ordersi_item_id'+c).val($itemID);
		        			jQuery('#pg_ordersi_item_id'+c).attr('variationId', varId);
		        			jQuery('#pg_ordersi_item_variation_id'+c).val(varId);
		        			var str        =response.split('##');
							jQuery('#pg_ordersi_item_name'+c+'').val(str[0]);
							jQuery('#pg_ordersi_item_price_without_tax'+c+'').val(str[1]);
							jQuery('#pg_ordersi_item_tax'+c+'').val(str[2]);
							jQuery('#pg_ordersir_item_total_price'+c+'').val(str[3]);
							jQuery('#pg_ordersi_item_quantity'+c+'').val(str[4]);
							jQuery('#pg_ordersi_item_tax_rate'+c+'').val(str[5]);
							jQuery('#pg_ordersi_apply_tax_on_shipping'+c+'').val(str[6]);
							jQuery('#pg_ordersi_item_free_shipping'+c+'').val(str[7]);
							jQuery('#pg_ordersi_item_shipping_tax'+c+'').val(0);
							var currentRow = Count;
							jQuery('.itemDetails').css('display','none');
							calculateOrderTotalO(c, Count);

		        			Count++;
		        		}
		        		else{
							var firstItemIdTd=jQuery("#pg-items-manager").find('tbody tr:first').children(':eq(1)');
							var firstItemId = firstItemIdTd.children("span").children(':eq(1)').val();
			
							if(firstItemId==""){
							
								jQuery("#pg-items-manager").find('tbody tr:first').remove();
								Count = 2;
								addnewRowO(1);
							}
							

		        			var c = Count;

			        		var newR = true;
			        		for(var i=1; i<=Count-1; i++){

			        			if($itemID == jQuery('#pg_addorder_item_id'+i).val() && varId == 0){
			        				c = i;
			        				Count--;
			        				newR = false;
			        				break;
			        			}
			        			else{
			        				if(varId == jQuery('#pg_addorder_item_id'+i).attr('variationId') && $itemID == jQuery('#pg_addorder_item_id'+i).val()){
			        					c = i;
				        				Count--;
				        				newR = false;
				        				break;
			        				}
			        			}
			        		}
			        		
			        		if(Count != 1 && newR == true){
					        	addnewRow(Count);
					        }
			        		

			        		jQuery('#pg_addorder_item_id'+c).val($itemID);
		        			jQuery('#pg_addorder_item_id'+c).attr('variationId', varId);
		        			jQuery('#pg_addorder_item_variation_id'+c).val(varId);
		        			var str        =response.split('##');
							jQuery('#pg_addorder_item_name'+c+'').val(str[0]);
							jQuery('#pg_addorder_item_price_without_tax'+c+'').val(str[1]);
							jQuery('#pg_addorder_item_tax'+c+'').val(str[2]);
							jQuery('#pg_addorder_item_total_price'+c+'').val(str[3]);
							jQuery('#pg_addorder_item_quantity'+c+'').val(str[4]);
							jQuery('#pg_addorder_item_tax_rate'+c+'').val(str[5]);
							jQuery('#pg_addorder_apply_tax_on_shipping'+c+'').val(str[6]);
							jQuery('#pg_addorder_item_free_shipping'+c+'').val(str[7]);
							jQuery('#pg_addorder_item_shipping_tax'+c+'').val(0);
							jQuery('#pg_addorder_item_remove'+c+'').html('<label>Remove</label>');
							jQuery( '#pg_addorder_item_remove'+c+' label' ).on( "click", function() {
    							removeThisOrderItem(c);
    						})
							
							var currentRow = Count;
							jQuery('.itemDetails').css('display','none');
							calculateOrderTotal(c, Count);

		        			Count++;
		        		}
					}
		        }
		    });
		}
	});

function removeThisOrderItem(c){

	countRow = jQuery('#pg-items-manager tbody tr').length;

	if(countRow==2){
		jQuery("#pg-items-manager tr input").val("");
	}
	else{
		jQuery("#order_item_tr"+c).remove();
	}
	
}

	//Additional Images start
	//change image
	jQuery(document).on('click', '#pg-item-images-add-con .pg-image-thumbnails li > img', function(){

        var fullurl = jQuery(this).attr('fullurl');
  		// var width = 0;
		// var height = 0;
        if(jQuery('.pg-item-images-con > img').attr('src') != fullurl){
            jQuery('.pg-item-images-con > img').css('opacity', 0);
            jQuery('.pg-item-images-con').addClass('loading');

            if(jQuery(this).attr('imagetype') == 'images'){

	            jQuery('.pg-item-images-con .pg-item-video').html('');
	            jQuery('.pg-item-images-con .pg-item-video').attr('videoId','');
	            var changed_image = new Image();

	            changed_image.onload = function () {
	                jQuery('.pg-item-images-con > img').attr('src', this.src);
	                jQuery('.pg-item-images-con > img').css('opacity','1');
	                
	                jQuery('.pg-item-images-con').removeClass('loading');
	            }
	            changed_image.src = fullurl;
            }
            if(jQuery(this).attr('imagetype') == 'video'){
            	videoid = jQuery(this).attr('videoid');
            	loadItemVideo(videoid);
            }
        }
    });
    //change attribue part
    jQuery(document).on('click', '.changeAttributeSelect', function(event){
    	type = jQuery(this).attr('type');
    	itemId = jQuery(this).attr('itemId');
    	if(type && itemId){
    		if(type == 'item'){
    			jQuery.ajax({
					type: 'POST',
					url: 'index.php',
					async: false,
					data: ({
						option: "com_pago",
						view: "addorder",
						task : "getDefaultVaration",
						dataType: 'json',
						itemId: itemId,
					}),
					success: function( response ) {
						if(response){
							result = JSON.parse(response);
							if(result.status == "success"){
								jQuery('#pg-product-'+itemId+'.view-item').attr('selectedVaration',result.defaultVarationId);
								considerPrice(false,itemId);
							}else{
								jQuery('#pg-product-'+itemId+'.view-item').attr('selectedVaration',0);
								enableAllAttributeOption(itemId);
								considerPrice(false,itemId,true);
							}
						}
					}
				});		
    		}
    		if(type == 'varation'){
    			varationId = itemId;
    			itemId = jQuery(this).parents('.product-container.view-item').attr('itemid');
  				if(!itemId){
  					return;
  				}
  				jQuery('#pg-product-'+itemId+'.view-item').attr('selectedVaration',varationId);
				considerPrice(false,itemId);	
    		}
    	}
  	});
	
	jQuery(document).on('click', '#pago .pg-product-downloads-block > a', function(){
		if(jQuery(this).parent().hasClass('open')){
			jQuery(this).parent().removeClass('open');
		}
		else{
			jQuery(this).parent().addClass('open');	
		}
		jQuery(this).siblings(".pg-product-downloads" ).slideToggle( "fast");
	})

	function changeImage(index,length,obj){
		setTimeout(function(){
			nextind = index+1;
			if (index == length) nextind = 1;
			obj.find('.pg-category-product-image-block img:nth-child('+index+')').css('opacity','0').removeClass('active-image');
			obj.find('.pg-category-product-image-block img:nth-child('+(nextind)+')').css('opacity','0.9').addClass('active-image');
			if (index == length) index = 1;
			else index++;	
			if(obj.attr('rel')=='1') changeImage(index,length,obj)
		},1000)	
	}

	// jQuery(document).on('mouseleave', '#pago #pg-category-view .product-cell', function(){
	// 	jQuery(this).attr('rel','0');
	// });

	jQuery(document).on('click', function(e){
        if (jQuery('.pg-product-downloads-block').hasClass('open')){
            if(jQuery(e.target).parents('.pg-product-downloads').length==0 
            && !jQuery(e.target).hasClass('pg-product-downloads') 
        	&& jQuery(e.target).parents('.pg-product-downloads-block').length==0 
        	&& !jQuery(e.target).hasClass('pg-product-downloads-block')){
      			jQuery("#pago .pg-product-downloads-block.open .pg-product-downloads").slideToggle( "fast");
      			jQuery('#pago .pg-product-downloads-block').removeClass('open')
            }
        }
    })

	jQuery(document).on('click', '.pg-addtocart-success-block-close', function(){
    	jQuery(this).parent().fadeOut('fast');
    })
});

jQuery(window).load(function(){
	jQuery('#pago .pg-thumbnail-swiper-container .swiper-wrapper').css('visibility', 'visible');

	jQuery('.pg-checkout-panel-group .pg-checkout-panel:first-child').addClass('open');
	jQuery('.pg-checkout-panel-group .pg-checkout-panel:first-child').find('.pg-checkout-content').slideDown('fast');

	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').addClass('open');
	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').find('.pg-checkout-shipping-info-content').slideDown('fast');

	var imageMarginTop = parseInt(jQuery('#pg-category-view #pg-products .pg-category-product-image-block').css('margin-top'));
    var imageMarginBottom = parseInt(jQuery('#pg-category-view #pg-products .pg-category-product-image-block').css('margin-bottom'));

	jQuery(document).on('click', '#pg-checkout .pg-checkout-heading-change', function(){
		var obj = jQuery(this).parent().parent();
		if (obj.hasClass('pg-checkout-options')){
			jQuery('.pg-checkout-panel-group .pg-checkout-panel.open .pg-checkout-content').slideUp('fast');
			jQuery('.pg-checkout-panel-group .pg-checkout-panel.open').removeClass('open');

			jQuery('.pg-checkout-billing').slideUp('fast');

			jQuery('.pg-checkout-register-guest .pg_user_guest').html('');
			jQuery('.pg-checkout-register .pg_user_register').html('');
			jQuery('.pg-checkout-billing .billing_form').html('');

			jQuery('.pg-checkout-panel-group .pg-checkout-panel').addClass('hide-change');

			obj.addClass('open');
			obj.find('.pg-checkout-content').slideDown('fast');
		}
		else{
			jQuery('.pg-checkout-panel-group .pg-checkout-panel.open .pg-checkout-content').slideUp('fast');
			jQuery('.pg-checkout-panel-group .pg-checkout-panel.open').removeClass('hide-change');
			jQuery('.pg-checkout-panel-group .pg-checkout-panel.open').removeClass('open');

			jQuery('.pg-checkout-panel-group .pg-checkout-panel:not(".pg-checkout-panel")').addClass('hide-change');

			obj.addClass('open');
			obj.find('.pg-checkout-content').slideDown('fast');
		}
	})
})

function loadItemVideo(videoId){
	jQuery('.pg-item-images-con > img').attr('src','');
	if(jQuery('.pg-item-images-con .pg-item-video').attr('videoId') == videoId){
		return;
	}
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: ({
			option: "com_pago",
			view: "addorder",
			task : "getVideo",
			dataType: 'json',
			videoId: videoId,
		}),
		success: function( response ) {
			if(response){
				result = JSON.parse(response);
				jQuery('.pg-item-images-con .pg-item-video').html(result.videoEmbed);

				jQuery('.pg-item-images-con .pg-item-video').attr('videoId',videoid);
				
				var imageContainerWidth = jQuery('#pg-product-images').width();
				var imageContainerHeight = jQuery('#pg-product-images').height();
				var thumbnailImageHeight = jQuery('#pg-item-images-add-con li img').height();
				var thumbnailBottom = parseInt(jQuery('#pago #pg-product-view #pg-item-images-add-con-main').css('bottom'));

				jQuery('.pg-item-images-con .pg-item-video iframe').css('width', imageContainerWidth);
				jQuery('.pg-item-images-con .pg-item-video iframe').css('height', imageContainerHeight-thumbnailImageHeight-2*thumbnailBottom);
				jQuery('.pg-item-images-con .pg-item-video iframe').css('margin-top', -(thumbnailImageHeight+2*thumbnailBottom));
			}	
		}
	});	
	jQuery('.pg-item-images-con > img').css('opacity','0');
	jQuery('.pg-item-images-con').removeClass('loading');
}
function startChangeAttribute(){
	jQuery('#pg-item-details').css('opacity', 0);
	jQuery('#pg-item-details').addClass('loading');	
}
function finishChangeAttribute(){
	//considerPrice();
	jQuery('#pg-item-details').css('opacity','1');            
    jQuery('#pg-item-details').removeClass('loading');
}
function finishChangeAttributeWPrice(){
	jQuery('#pg-item-details').css('opacity','1');      
    jQuery('#pg-item-details').removeClass('loading');
}

function preselectVaration(varationId,itemID,calculatePrice,attributeId,optionId){
	var parent = '';
	if(itemID) parent = '#pg-product-'+itemID+' ';
	var attr_id = attributeId;
	var opt_id = optionId;
	type = jQuery(parent+' .pg_attr_'+attr_id).attr('attrdisplaytype');
	if(type=='0'){
		jQuery(parent+' .pg_attr_'+attr_id+' select').val(opt_id);
		jQuery(parent+' .pg_attr_'+attr_id+' select').trigger('chosen:update');
		var rel = jQuery(parent+' .pg_attr_'+attr_id+' select option:selected').attr('rel');
		if(jQuery(parent+' .pg_attr_'+attr_id+' select option:selected').hasClass('pg-color-'+rel)){
			setTimeout(function(){
				jQuery(parent+' .pg_attr_'+attr_id+' select+.chosen-container .chosen-single span').html(rel).addClass('pg-color-'+rel);
			},300);
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
	if(calculatePrice){
		setTimeout(function(){
			jQuery('#pg-product-'+itemID).attr('selectedVaration',varationId);
			if(calculatePrice){
				considerPrice(false,itemID);
			}
		},200);
	}else{
		jQuery('#pg-product-'+itemID).attr('selectedVaration',varationId);
	}
}
function selectAttributeOption(attr_id,opt_id,$itemID){
	var parent = '';
	if($itemID) parent = '#pg-product-'+$itemID+' ';

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

// Checking Grid
// ### TO DO implement some form of resize method to replace the above
// ### and make the template super friendly to many devices/resolutions
// ### at least within the default Joomla! template; may end up unusable
;( function( $, window, document, undefined ) {

	var gridResize = function( elem, options ) {
		this.elem = elem;
		this.$elem = $(elem);
		this.options = options;
	}

	gridResize.prototype = {
		defaults: {
			parent: window,
			sizes: { 
				'desktop': {
					'small': 800,
					'medium': 1024,
					'large': 1280,
					'widescreen': 1440,
					'hd': 1920
				},
				'mobile': {
					'pda': 320,
					'mobile': 480,
					'smartphoneh': 540,
					'smartphonev': 960,
					'tabletv': 768,
					'tableth': 1024
				}
			}
		},

		init: function() {
			this.config = $.extend( {}, this.defaults, this.options )
		}
	}
		
	gridResize.defaults = gridResize.prototype.defaults;

	$.fn.gridResize = function( options ) {
		return this.each( function() {
			new gridResize( this, options ).init();
		});
	}

} )( jQuery, window, document );

// Form Helper
// ### Playing with plugin, whether or not this is necessary, potential for 
// ### additional methods for form manipulation
;( function( $, window, document, undefined ) {

	var formHelper = function( elem, options ) {
		this.elem = elem;
		this.$elem = $(elem);
		this.options = options;
	}

	formHelper.prototype = {
		defaults: {
			task: 'activate',
			activeClass: 'active',
			placeholderClass: 'pg-placeholder'
		},

		init: function() {
			this.config = $.extend( {}, this.defaults, this.options );

			var task = this.config.task;
			
			this[task](this.config);

			return this;
		},

		activate: function(config) {
			$(this).addClass( this.config.activeClass );
			$(this + ':input').each( function() {
				$(this).removeAttr('disabled', true);
			})
		},

		deactivate: function(config) {
			$(this).removeClass( this.config.activeClass );
			$(this + ':input').each( function() {
				$(this).attr('disabled', true);
			})
		},

		placeholder: function(config) {
			var supportPlaceholder = 'placeholder' in document.createElement('input');
						
			if ( supportPlaceholder ) {
				return this;
			} else {
				var inputValue = $.trim($(this.elem).val()),
					inputId = (this.elem.id) ? this.elem.id : "placeholder" + (+new Date()),
					inputPlaceholder = $(this.elem).attr("placeholder");
				
				if ( !inputValue ) {
					$(this.elem).attr('value', inputPlaceholder).addClass( config.placeholderClass );

					$(this.elem).focus( function() {
						if( inputPlaceholder == $(this).val() )
							$(this).removeClass( config.placeholderClass ).attr('value', '');
					}).blur( function() {
						if( $(this).attr('value') == '' )
							$(this).attr('value', inputPlaceholder).addClass( config.placeholderClass );
						else
							$(this).removeClass( config.placeholderClass );
					})

				}
			}
		}
	}

	formHelper.defaults = formHelper.prototype.defaults;

	$.fn.formHelper = function( options ) {
		return this.each( function() {
			new formHelper( this, options ).init();
		});
	}

} )( jQuery, window, document );


function activate_form(element) {
	jQuery(element).addClass('active');
	jQuery(element + ' :input').each(function() { jQuery(this).removeAttr('disabled'); });
}
function deactivate_form(element) {
	jQuery(element).removeClass('active');
	jQuery(element + ' :input').each(function() { jQuery(this).attr('disabled', true); });
}

// Thumnail Rollovers function
function thumbnail_rollover() {
	jQuery('.pg-thumbnail-img').mouseover(function(){
		el = jQuery(this).attr('rel');
		el_title = jQuery(this).attr('title');
		el_alt = jQuery(this).attr('alt');
		
		jQuery('#pg-main-image img').attr({
			src: el,
			title: el_title,
			alt: el_alt
		});
	});
}

jQuery(document).on('click','#pago .pg_attr_options[attrdisplaytype="2"]>span',function(){
	if(jQuery(this).parent().attr('attrtype')=='0'){
		if(jQuery(this).hasClass('.pg_color_option_list')){
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

jQuery(document).on('click','#pago .pg_attr_options[attrdisplaytype="1"]>span',function(){
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

jQuery(document).on('click','#pago .disabledOption',function(){
	eval(jQuery(this).attr('rel'));
});

function show_attr_option_form($attr_type,$attrId,$displayType,$optionId,$itemID){
	var parent = '';
	if($itemID) parent = '#pg-product-'+$itemID+' ';
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
		if(!$itemID) enableAnyVariation($attrId,$optionId,item_varations);
		else enableAnyVariation($attrId,$optionId,item_varations,$itemID);
		if(!$itemID) considerPrice($optionId);
		else considerPrice($optionId,$itemID);
		
		return;
	}
	if(!$itemID) considerPrice($optionId);
	else considerPrice($optionId,$itemID);
}

function enableAnyVariation($attrId, $optionId,variation,$itemID){
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
	if($itemID) parent = '#pg-product-'+$itemID+' ';

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
		type = jQuery(parent+' .pg_attr_'+attr_id).attr('attrdisplaytype')
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

function considerPrice(changePhotoOptionId,$itemID,noChangeImage){
	var parent = '';
	if($itemID) parent = '#pg-product-'+$itemID+' ';

	if(!$itemID){
		itemId = jQuery("#pg-item").attr('itemId'); 
	}else{
		itemId = $itemID;
	}
	
	itemQty = jQuery(parent+".pg-item-opt-qty").val();

	//checkLetter(parent+".pg-item-opt-qty");

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
						view: "addorder",
						task : "getVaration",
						dataType: 'json',
						varationId: selectedVarationid,
					}),
					success: function( response ) {
						if(response){
							result = JSON.parse(response);
							for (var key in result) {// key = attribute,result[key] = option
								selected_attributes[key] = result[key];
								selectAttributeOption(key,result[key],itemId);
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
		is_variation = hideExcessAttr(item_varations,selected_attributes);
	}else{
		is_variation = hideExcessAttr(item_varations,selected_attributes,$itemID);
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
			view: "addorder",
			task : "considerPrice",
			dataType: 'json',
			itemId: itemId,
			selected_attributes:selected_attributes,
			itemQty:itemQty,
		}),
		success: function( response ) {
			if(response){
				result = JSON.parse(response);
				jQuery(parent+".pg-product-real-price").html(result.price);
				jQuery(parent+".pg-category-product-real-price").html(result.price);
				jQuery(parent+".pg-addtocart-product-real-price").html(result.price);

				jQuery(parent+".pg-product-sku .product_sku_code").html(result.sku);
				jQuery(parent+".pg-category-product-sku .category_product_sku_code").html(result.sku);
				
				jQuery(parent+".pg-product-stock span:last-child").html(result.limit);
				jQuery(parent+".pg-category-product-stock span:last-child").html(result.limit);


				jQuery(parent+".pg-product-title h1").html(result.name);
				if(jQuery(parent+".pg-category-product-title a").length)
					jQuery(parent+".pg-category-product-title a").html(result.name);
				else
					jQuery(parent+".pg-category-product-title").html(result.name);


				changePhotoIds = false;
				var changePhotoId = 0;
				if(typeof result.varationId !== "undefined"){ // check if varation exist
					if(result.varationDefault != 1){
						changePhotoId = result.varationId;
						changePhotoType = "varation";
					}else{
						changePhotoId = false;
					}
					jQuery('#pg-product-'+itemId).attr('selectedVaration',result.varationId);		
				}

				itemView = jQuery(parent).hasClass('view-item');

				if(itemView){
					return;
					if(changePhotoId){
						var imageSize = jQuery('#pg-image-size').val();
						/// get photo start
						jQuery.ajax({
							type: 'POST',
							url: 'index.php',
							data: ({
								option: "com_pago",
								view: "cart",
								task : "changeImage",
								dataType: 'json',
								itemId: itemId,
								changePhotoId: changePhotoId,
								changePhotoType:changePhotoType,
								changePhotoIds:changePhotoIds,
								imageSize:imageSize
							}),
							success: function( response ) {
								if(response){
									result = JSON.parse(response);
									if(result.status == 'success'){
										if(jQuery(parent+'.pg-item-images-con > img').attr('src') != result.imagePath){
											jQuery(parent+'.pg-item-images-con .pg-item-video').html('');
											jQuery(parent+'.pg-item-images-con .pg-item-video').attr('videoId','');
										    jQuery(parent+'.pg-item-images-con > img').css('opacity', 0);
										    jQuery(parent+'.pg-item-images-con').addClass('loading');
										    var changed_image = new Image();
										    changed_image.onload = function () {
										        jQuery(parent+'.pg-item-images-con > img').attr('src', this.src);
										        jQuery(parent+'.pg-item-images-con > img').css('opacity','1'); 
										        
										        jQuery(parent+'.pg-item-images-con').removeClass('loading');
										    }
										    changed_image.src = result.imagePath;
										}
									}else{
										if(jQuery(parent+'.pg-item-images-con > img').attr('src') != jQuery(parent+'ul.pg-image-thumbnails li:first-child img').attr('fullurl')){
										    if(jQuery(parent+'.pg-item-images-con > img').attr('imagetype') == 'images'){
										    	jQuery(parent+'.pg-item-images-con .pg-item-video').html('');
										    	jQuery(parent+'.pg-item-images-con .pg-item-video').attr('videoId','');
											    jQuery(parent+'.pg-item-images-con > img').css('opacity', 0);
											    jQuery(parent+'.pg-item-images-con').addClass('loading');
											    var changed_image = new Image();
											    changed_image.onload = function () {
											        jQuery(parent+'.pg-item-images-con > img').attr('src', this.src);
											        jQuery(parent+'.pg-item-images-con > img').css('opacity','1'); 
											        
											        jQuery(parent+'.pg-item-images-con').removeClass('loading');
											    }
											    changed_image.src = jQuery(parent+'ul.pg-image-thumbnails li:first-child img').attr('fullurl');
										    }
										    if(jQuery(parent+'.pg-item-images-con > img').attr('imagetype') == 'video'){
												videoid = jQuery(parent+'.pg-item-images-con > img').attr('videoid')
				            					loadItemVideo(videoid);
											}
										}	
									}
								}	
							}
						});
					}else if(jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li').length && jQuery(parent+'.pg-item-images-con > img').attr('src')!=jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li:first-child img').attr('fullurl')){
						if(typeof noChangeImage==='undefined'){
							if(jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li:first-child img').attr('imagetype') == 'images'){
								jQuery(parent+'.pg-item-images-con .pg-item-video').html('');
								jQuery(parent+'.pg-item-images-con .pg-item-video').attr('videoId','');
								jQuery(parent+'.pg-item-images-con > img').css('opacity', 0);
							    jQuery(parent+'.pg-item-images-con').addClass('loading');
							    var changed_image = new Image();
							    changed_image.onload = function () {
							        jQuery(parent+'.pg-item-images-con > img').attr('src', this.src);
							        jQuery(parent+'.pg-item-images-con > img').css('opacity','1'); 
							        jQuery(parent+'.pg-item-images-con').removeClass('loading');
							    }
							    changed_image.src = jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li:first-child img').attr('fullurl');
							}
							if(jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li:first-child img').attr('imagetype') == 'video'){
								videoid = jQuery(parent+'#pg-item-images-add-con .pg-image-thumbnails li:first-child img').attr('videoid')
            					loadItemVideo(videoid);
							}
						}
					}	
				}
			}		
		}
	});
	return false;
}

function hideExcessAttr(item_varations,selected_attributes,$itemID)
{
	var parent = '';
	if($itemID) parent = '#pg-product-'+$itemID+' ';

	if(!$itemID) showAllAttributeOption();
	else showAllAttributeOption($itemID);

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
			view: "addorder",
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
		jQuery(parent+".pg-product-attributes .pg_attr_options").each(function() {

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

function showAllAttributeOption($itemID){
	var parent = '';
	if($itemID) parent = '#pg-product-'+$itemID+' ';
	jQuery(parent+".pg-product-attributes .pg_attr_options").each(function() {
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

function enableAllAttributeOption($itemID){
	var parent = '';
	if($itemID) parent = '#pg-product-'+$itemID+'.view-item ';
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
}

jQuery(document).ready(function(){
    jQuery('input#pg-zip-code').keypress(function(event){
        if (event.which == 13) {
			jQuery("#pg-cart-shipping-estimation-button").click();
            event.preventDefault();
            return false;   
        }
    });
});

// Cart quantity update

jQuery(document).ready(function(){
	jQuery('.pg-cart-update').click(function(e){
		e.preventDefault();
		var form = jQuery(this).parents('form');
		var auth = form.find("input").last().attr('name');
		jQuery.post(
			form.attr('action'), 
			{
				option : form.find("input[name='option']").val(), 
				view : form.find("input[name='view']").val(), 
				task : form.find("input[name='task']").val(), 
				id : form.find("input[name='id']").val(), 
				qty : form.find("input[name='qty']").val(),
				price_each : form.find("input[name='price_each']").val(),
				current_subtotal : form.find("input[name='current_subtotal']").val(),
				total : form.find("input[name='total']").val(),
				subtotal : form.find("input[name='subtotal']").val(),
				auth : 1, 
			}, 
			function(response){	
				try {
					var obj_arr = JSON.parse(response);
				} catch (e){
					form.next().find('.pg-cart-qty-update-message').html('<div class="pg-addtocart-success-block-close">');
					form.next().find('.pg-cart-qty-update-message').prepend('Invalid Input');
					form.next().find('.pg-cart-qty-update-message').fadeIn('fast');
					return false;
				}
				if (obj_arr[0] == 0){
					// before_update_cart_item error
				} else {
				    // update cart normally
					form.find("input[name='qty']").val(obj_arr[8]);
					form.parents('tr').find('.pg-cart-product-total').html(obj_arr[2]);
					jQuery('.pg-cart-totals.pg-cart-subtotal .pg-cart-total').html(obj_arr[3]);
					jQuery('.pg-cart-totals.pg-cart-grand-total .pg-cart-total').html(obj_arr[4]);
					form.find("input[name='current_subtotal']").val(obj_arr[5])
					jQuery("input[name='subtotal']").val(obj_arr[6]);
					jQuery("input[name='total']").val(obj_arr[7]);
					jQuery('.pg-cart-container .pg-cart-quantity').html(obj_arr['total_qty']);
					jQuery('.pg-cart-container .pg-cart-total').html(obj_arr[9]);

					// jQuery('.pg-cart-container .pg-cart-total').html("wejhrfweuihriuheghe");
				}
				
				form.next().find('.pg-cart-qty-update-message').html('<div class="pg-addtocart-success-block-close">');
				form.next().find('.pg-cart-qty-update-message').prepend(obj_arr[1]);
				form.next().find('.pg-cart-qty-update-message').fadeIn('fast');

				pgQuickCart();
			}
		)
	})
})

</script>
