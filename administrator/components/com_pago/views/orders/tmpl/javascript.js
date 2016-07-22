
/* Simple JavaScript Inheritance
 * By John Resig http://ejohn.org/
 * MIT Licensed.
 */
// Inspired by base2 and Prototype
(function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

  // The base Class implementation (does nothing)
  this.Class = function(){};
 
  // Create a new Class that inherits from this class
  Class.extend = function(prop) {
    var _super = this.prototype;
   
    // Instantiate a base class (but only create the instance,
    // don't run the init constructor)
    initializing = true;
    var prototype = new this();
    initializing = false;
   
    // Copy the properties over onto the new prototype
    for (var name in prop) {
      // Check if we're overwriting an existing function
      prototype[name] = typeof prop[name] == "function" &&
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn){
          return function() {
            var tmp = this._super;
           
            // Add a new ._super() method that is the same method
            // but on the super-class
            this._super = _super[name];
           
            // The method only need to be bound temporarily, so we
            // remove it when we're done executing
            var ret = fn.apply(this, arguments);       
            this._super = tmp;
           
            return ret;
          };
        })(name, prop[name]) :
        prop[name];
    }
   
    // The dummy class constructor
    function Class() {
      // All construction is actually done in the init method
      if ( !initializing && this.init )
        this.init.apply(this, arguments);
    }
   
    // Populate our constructed prototype object
    Class.prototype = prototype;
   
    // Enforce the constructor to be what we expect
    Class.prototype.constructor = Class;

    // And make this class extendable
    Class.extend = arguments.callee;
   
    return Class;
  };
})();


var Gateway = Class.extend({

	init: function( gateway, txn_id ){
		
		jq = jQuery;
		
		var $this = this;
		
		this.txn_id = txn_id;
		
		this.on_before_get_order = function(){
			jq('#g-order-items').find('tbody').find('tr').remove();
			//jq( 'div#loader-return-msg' ).text( 'Connecting...' );
		}
		
		this.on_after_get_order = function(){ 
			
			var order = this.order;		
			
			var fullfill_state = order.find('fulfillment-order-state:last').text();
			
			if( 'DELIVERED' == fullfill_state || 'WILL_NOT_DELIVER' == fullfill_state ){
				jq( 'a.deliver-order' ).hide();
			}
			
			if( 'CHARGEABLE' != fullfill_state && 'PAYMENT_DECLINED' != fullfill_state ){
				//jq( 'a.cancel-order' ).hide();
			}
			
			if( 'WILL_NOT_DELIVER' == fullfill_state ){
				jq( 'a.cancel-order' ).hide();
				jq( 'a.refund-order' ).hide();
				jq( '.controls' ).hide();
			}
			//total-available-amount
			
			var total_available_amount = $this.fc( order.find('total-charge-amount:last').text() - order.find('total-refund-amount:last').text() );
		
			jq( 'a.cancel-order' ).hide();
			
			if( total_available_amount == 0 ){
				if('WILL_NOT_DELIVER' != fullfill_state) jq( 'a.cancel-order' ).show();
				jq( 'a.refund-order' ).hide();
				jq( '.controls' ).hide();
			}
			
			jq( 'div#loader-return-msg' ).text( this.last_response_msg ).addClass( 'green' );			
			jq( 'textarea.order_xml' ).text( this.order_xml )		
			jq( 'span.shipping-name' ).text( order.find('shipping-name:last').text() );			
			jq( 'span.shipping-cost' ).text( $this.fc( order.find('shipping-cost:last').text() ) );		
			jq( 'dd.total-tax' ).text( $this.fc( order.find('total-tax:last').text() ) );		
			jq( '.total-charge-amount' ).text( $this.fc( order.find('total-charge-amount:last').text() ) );			
			jq( '.total-refund-amount' ).text( $this.fc( order.find('total-refund-amount:last').text() ) );	
			jq( '.total-available-amount' ).text( total_available_amount );		
			jq( 'input.refund_amount' ).val( total_available_amount );				
			jq( 'dd.google-order-number' ).text( order.find('google-order-number:first').text() );			
			jq( 'dd.financial-order-state' ).text( order.find('financial-order-state:last').text() );			
			jq( 'dd.fulfillment-order-state' ).text( fullfill_state );		
			jq( 'dd.timestamp' ).text( order.find('timestamp:first').text() );
			
			order.find( 'new-order-notification' ).find('shopping-cart:first').find( 'item' ).each( function(){
					
				item_id = jq(this).find('merchant-item-id:first').text();
				
				jq('#g-order-items').find('tbody').append( jq('<tr>').attr('id', 'item'+item_id ) );
				
				jq('#item'+item_id).append( jq('<td>').append( item_id ) );
				jq('#item'+item_id).append( jq('<td>').append( jq(this).find('item-name:first').text() ) );
				jq('#item'+item_id).append( jq('<td>').append( jq(this).find('unit-price:first').text() ) );
				jq('#item'+item_id).append( jq('<td>').append( jq(this).find('merchant-item-id:first').text() ) );
				
			});
			
			//subscriptions
			order.find( 'new-order-notification' ).find('shopping-cart:first').find( 'subscription-item' ).each( function(){
					
				item_id = jq(this).find('merchant-item-id:first').text();
				
				jq('#g-order-subscription-items').find('tbody').append( jq('<tr>').attr('id', 'subscr-item'+item_id ) );
				
				jq('#subscr-item'+item_id).append( jq('<td>').append( item_id ) );
				jq('#subscr-item'+item_id).append( jq('<td>').append( jq(this).find('item-name:first').text() ) );
				jq('#subscr-item'+item_id).append( jq('<td>').append( jq(this).find('billing_period:first').text() ) );
				
				jq('#subscr-item'+item_id).append( jq('<td>').append( jq(this).find('start_date:first').text() ) );
				jq('#subscr-item'+item_id).append( jq('<td>').append( jq(this).find('shipping_cost:first').text() ) );
				jq('#subscr-item'+item_id).append( jq('<td>').append( jq(this).find('tax_cost:first').text() ) );
				
				jq('#subscr-item'+item_id).append( jq('<td>').append( jq(this).find('initial_price:first').text() ) );
				jq('#subscr-item'+item_id).append( jq('<td>').append( jq(this).find('unit-price:first').text() ) );
				
				jq('#subscr-item'+item_id).append( jq('<td>').append( jq(this).find('status:first').text() ) );
				
				
			});
			
			/*$item_xml->addChild( 'item-name', $item->name );
				$item_xml->addChild( 'merchant-item-id', $item->id );
				
				$item_xml->addChild( 'billing_period', $item->billing_period );
				$item_xml->addChild( 'initial_price', $item->initial_price );
				$item_xml->addChild( 'start_date', $item->start_date );
				$item_xml->addChild( 'shipping_cost', $item->shipping_cost );
				$item_xml->addChild( 'tax_cost', $item->tax_cost );
				
				$item_xml->addChild( 'unit-price', $item->price_total )
							->addAttribute( 'currency', $item->currency );*/
		}
		
		jq('#refresh').click( function(){			
			$this.get_order();
			return false;
		});
		
		jq('a.authorize-order').click( function(){			
			$this.financial_operations( 'authorize_order' );
			return false;
		});
		
		jq('a.deliver-order').click( function(){			
			$this.financial_operations({
				txn_id: $this.txn_id,
				task: 'financial_operations',
				operation: 'deliver-order',
				amount: '',
				carrier: '',
				tracking_number: ''
			});
			return false;
		});
		
		jq('a.refund-order').click( function(){			
			$this.financial_operations({
				txn_id: $this.txn_id,
				task: 'financial_operations',
				operation: 'refund-order',
				amount: jq( 'input.refund_amount' ).val()
			});
			return false;
		});
		
		jq('a.cancel-order').click( function(){			
			$this.financial_operations({
				txn_id: $this.txn_id,
				task: 'financial_operations',
				operation: 'cancel-order'
			});
			return false;
		});
	},
	
	financial_operations: function( data ){
		
		var $this = this;
		
		jQuery.ajax({
			url: 'index.php?option=com_pago&view=orders&format=gateway&gateway=' + gateway,
			type: 'POST',
			data: data,
			beforeSend: function() {
				$this.toggle_loading(1);
			},
			success: function( data ){
				
				$this.toggle_loading(0);
				
				var xmlDoc = jQuery.parseXML( data );				
				var rxml = jQuery( xmlDoc );
				
				var error_msg = rxml.find( 'error-message' ).text()
				
				if( error_msg ){
					jQuery( 'div#loader-return-msg' )
						.text( 'Error: ' + error_msg )
						.removeClass( 'green' )
						.addClass( 'red' );
				} else {
					jQuery( 'div#loader-return-msg' )
						.text( 'Operation Success' )
						.addClass( 'green' )
						.removeClass( 'red' );
					
					$this.get_order();
				}
				
			}
		});
		
	},
	
	get_order: function(){  
		
		var $this = this;
		
		jQuery.ajax({
			url: 'index.php?option=com_pago&view=orders&format=gateway&gateway=' + gateway,
			type: 'POST',
			data: {
				txn_id: $this.txn_id,
				task: 'get_transaction'
			},
			beforeSend: function() {
				$this.toggle_loading(1);
				
				$this.on_before_get_order();
			},
			success: function( data ){
				
				$this.toggle_loading(0);
			
				var xmlDoc = jQuery.parseXML( data );				
				var order = jQuery( xmlDoc );
				
				$this.success = true;
				$this.last_response_msg = 'Order Successfully Retrieved From Paygate';
				$this.order_xml =  data;
				$this.order =  order;
				//$this.pago_order_number = order( 'order_id:first' );
				$this.on_after_get_order();
			}
		});
	},
	
	toggle_loading: function( state ){
	  
		if( 1 === state ){
			 jQuery('#ajax-loader').show();
			 jQuery('#refresh').hide();
		} else {
			 jQuery('#ajax-loader').hide();
			 jQuery('#refresh').show();
		}
	},
	
	//fc = format currency ;)
	fc : function(num) {
		num = isNaN(num) || num === '' || num === null ? 0.00 : num;
		return parseFloat(num).toFixed(2);
	}

});  