
var paGOapiObj = function(backendpoint)
{
	this.config = new function()
	{
		this.service = 'config';

		this.save = function(id)
		{
			jQuery('#pago_pg_save_'+id).prop('disabled', true);
			jQuery('#pago_pg_save_spin_'+id).attr('style', '');
			
			var ele = false;
			var dta = {'id':id};
			
			jQuery('#pg_config_form_'+id).children('input').each(function(index,data) {
				ele = jQuery(this);
				if(ele.attr('type') == 'radio'){
					if(ele.attr("checked")){
						dta[ele.attr("name")] = ele.val();
					}
				} else {
					dta[ele.attr("name")] = ele.val();
				}
			});
			
			jQuery.post(
		    	"index.php?option=com_pago&view=payoptions&task=save_pg_config", 
		    	dta
		    ).done(function( data ) {
				jQuery('#pago_pg_save_'+id).prop('disabled', false);
				jQuery('#pago_pg_save_spin_'+id).attr('style', 'display:none');
			});
			
		};
	};
	
	this.payum = new function()
	{
		this.service = 'payum';

		this.post = function(plan)
		{
			if (!confirm('Are you sure you want to subscribe to '+plan+'?')) { 
				return;
			}
			
			jQuery('#'+plan+'_subscribe_button').prop('disabled', true);
			jQuery('#'+plan+'_subscribe_saving').attr('style', '');
			jQuery.ajax({
			    url: backendpoint + this.service,
			    type: "POST",
			    contentType: "application/json",
			    data: JSON.stringify({
			    	'customer_id': jQuery('#pago_customer_id').val(),
			    	'site': window.location.host,
			    	'ip': jQuery('#ipaddress').attr('ipaddress'),
			    	'plan': plan,
			    	'subscription_id': jQuery('#'+plan+'_subscription_id').val()
				}),
			    complete: function(r){
			    	
			    	jQuery('#'+plan+'_subscribe_saving').attr('style', 'display:none');
					jQuery('#'+plan+'_subscribe_button').prop('disabled', false);
							
			    	jQuery('pre.pg-response').html(r.responseText);
			    	
			    	var response = jQuery.parseJSON(r.responseText);
			    	
			    	try {
			    		if(response.status == 500){
			    			alert(response.detail);
			    			return;
			    		}
					}catch(err){}
					
					jQuery('#'+plan+'_subscription_id').val(response.id);
		    		jQuery('#'+plan+'-ui-box').removeClass('ui-box-disabled').addClass('ui-box-enabled');
		    		jQuery('#'+plan+'-ui-indicator').find('i').removeClass('fa-times').addClass('fa-check-square-o').html('');
			    		
					jQuery('#'+plan+'_subscribe_group').attr('style', 'display:none');
					jQuery('#'+plan+'_resubscribe_group').attr('style', 'display:none');
					jQuery('#'+plan+'_unsubscribe_group').attr('style', '');
			    }
			});
		};
		
		this.delete = function(plan)
		{
			if (!confirm('Are you sure you want to cancel? Access will continue until current paid period ends.')) { 
				return;
			}
			
			jQuery('#'+plan+'_subscribe_delete_button').prop('disabled', true);
			jQuery('#'+plan+'_subscribe_saving').attr('style', '');
			jQuery.ajax({
			    url: backendpoint + this.service,
			    type: "DELETE",
			    contentType: "application/json",
			    data: JSON.stringify({
			    	'id': jQuery('#pago_customer_id').val(),
			    	'site': window.location.host,
			    	'ip': jQuery('#ipaddress').attr('ipaddress'),
			    	'subscription_id': jQuery('#'+plan+'_subscription_id').val()
				}),
			    complete: function(r){
			    	
			    	jQuery('#'+plan+'_subscribe_saving').attr('style', 'display:none');
					jQuery('#'+plan+'_subscribe_delete_button').prop('disabled', false);
					
			    	jQuery('pre.pg-response').html(r.responseText);
			    	
			    	var response = jQuery.parseJSON(r.responseText);
			    	
			    	try {
			    		if(response.status == 500){
			    			alert(response.detail);
			    			return;
			    		}
					}catch(err){}
					
		    		jQuery('#'+plan+'-ui-indicator').find('i').html('Unsubscribed!');
					jQuery('#'+plan+'_subscribe_group').attr('style', 'display:none');
					jQuery('#'+plan+'_resubscribe_group').attr('style', '');
					jQuery('#'+plan+'_unsubscribe_group').attr('style', 'display:none');
			    }
			});
		};
	};
	
	this.customers = new function()
	{
		this.service = 'customers';
		
		jQuery(document).ready(function() {
		    
  
		});

		this.post = function()
		{
			if (!confirm('Are you sure?')) { 
				return;
			}
			
			jQuery('#pago_customer_button').prop('disabled', true);
			jQuery('#pago_customer_saving').attr('style', '');
			jQuery.ajax({
			    url: backendpoint + this.service,
			    type: "POST",
			    contentType: "application/json",
			    data: JSON.stringify({
			    	'id': jQuery('#pago_customer_id').val(),
			    	'site': window.location.host,
			    	'ip': jQuery('#ipaddress').attr('ipaddress'),
			    	'name': jQuery('#pago_customer_name').val(),
					'email': jQuery('#pago_customer_email').val(),
					'phone': jQuery('#pago_customer_phone').val(),
					'number': jQuery('#pago_customer_number').val(),
					'cvc': jQuery('#pago_customer_cvc').val(),
					'exp_month': jQuery('#pago_customer_exp_month').val(),
					'exp_year': jQuery('#pago_customer_exp_year').val()
				}),
			    complete: function(r){
			    	jQuery('pre.pg-response').html(r.responseText);
			    	
			    	var response = jQuery.parseJSON(r.responseText);
			    	
			    	try {
			    		if(response.status == 500){
			    			alert(response.detail);
			    			
			    		}
					}catch(err){}
					
					try {
			    		if(response.id){
			    			jQuery.post(
						    	"index.php?option=com_pago&view=payoptions&task=store_customer_id", 
						    	{id: response.id}
						    );
						    jQuery('#pago_customer_id').val(response.id);
						    jQuery('#pago_customer_id_text').html(response.id);
						    jQuery('#pago_customer_button_delete').attr('style', '');
						    
						    jQuery('.pago_pg_nocc').attr('style', 'display:none');
						    jQuery('.pago_pg_yescc').attr('style', '');
			    		}
					}catch(err){}
					   
					jQuery('#pago_customer_saving').attr('style', 'display:none');
					jQuery('#pago_customer_button').prop('disabled', false);
			    }
			});
		};
		
		this.delete = function()
		{
			if (!confirm('Are you sure?')) { 
				return;
			}
			
			jQuery('#pago_customer_button_delete').prop('disabled', true);
			jQuery('#pago_customer_saving').attr('style', '');
			
			var customer_id = jQuery('#pago_customer_id').val();
			
			jQuery.ajax({
			    url: backendpoint + this.service,
			    type: "DELETE",
			    contentType: "application/json",
			    data: JSON.stringify({
					'id': customer_id
				}),
			    complete: function(r){
			    	jQuery('pre.pg-response').html(r.responseText);
			    	
			    	jQuery('#pago_customer_id').val('');
					jQuery('#pago_customer_id_text').html('');
					
					jQuery.post(
				    	"index.php?option=com_pago&view=payoptions&task=delete_customer_id"
				    );
					
					jQuery('#pago_customer_button_delete').attr('style', 'display:none');
					jQuery('#pago_customer_button_delete').prop('disabled', false);
					jQuery('#pago_customer_saving').attr('style', 'display:none');
					jQuery('.pago_pg_nocc').attr('style', '');
					jQuery('.pago_pg_yescc').attr('style', 'display:none');
			    }
			});
		};
	};
	
	this.recipients = new function()
	{
		this.service = 'recipients';
		
		jQuery(document).ready(function() {
		    
		    jQuery('.pg-radio').removeClass('pg-radio');
		    
		    var livemode = jQuery('input[type="radio"][name="params[payoptions_livetoggle][livemode]"]:checked').val();
			
			if(livemode == 1){
				jQuery('#payoptions_live').show();
			} else {
				jQuery('#payoptions_test').show();
			}
			
			jQuery("input[name='params[payoptions_livetoggle][livemode]']").click(function() {
				
				if(jQuery(this).val() == 1){
					jQuery('#payoptions_live').show();
					jQuery('#payoptions_test').hide();
				} else {
					jQuery('#payoptions_live').hide();
					jQuery('#payoptions_test').show();
				}
			});
			
			var entity_type_test = jQuery('input[type="radio"][name="params[payoptions_verification][legal_entity_type]"]:checked').val();
			var country_test = jQuery('#params_payoptions_country').val();
			jQuery('#fs-'+entity_type_test+country_test).show();
			jQuery('#fs-shared'+country_test).show();
			
			var entity_type_live = jQuery('input[type="radio"][name="params[payoptions_verification_live][legal_entity_type]"]:checked').val();
			var country_live = jQuery('#params_payoptions_live_country').val();
			jQuery('#fs-'+entity_type_live+country_live +'_live').show();
			jQuery('#fs-shared'+country_live +'_live').show();
			
			var onvalidate = function(type){
				entity_type_test = jQuery('input[type="radio"][name="params[payoptions_verification'+type+'][legal_entity_type]"]:checked').val();
				country_test = jQuery('#params_payoptions'+type+'_country').val();
				
				jQuery('.pago_verify_items').hide();
				jQuery('#fs-'+entity_type_test+country_test+type).show();
				jQuery('#fs-shared'+country_test+type).show();
				jQuery('#fs-'+entity_type_live+type).show();
				jQuery('#fs-shared'+country_live+type).show();
			}
			
			jQuery('#params_payoptions_country').on('change', function() {
				onvalidate('');
			});

			jQuery("input[name='params[payoptions_verification][legal_entity_type]']").click(function() {
				onvalidate('');
			});
			
			jQuery('#params_payoptions_live_country').on('change', function() {
				onvalidate('_live');
			});

			jQuery("input[name='params[payoptions_verification_live][legal_entity_type]']").click(function() {
				onvalidate('_live');
			});
			
  
		});

		this.post = function()
		{
			if (!confirm('Are you sure?')) { 
				return;
			}
			
			var send = function(event){
				
				var identity_document = false;
				
				if(event){
					identity_document = event.target.result;
				}
				
				var livemode = jQuery('input[type="radio"][name="params[payoptions_livetoggle][livemode]"]:checked').val();
				var active = jQuery('input[type="radio"][name="params[payoptions_livetoggle][active]"]:checked').val();
				
				var livefix = '';
				
				if(livemode == 1) livefix = '_live';
				
				var date = jQuery('#params_payoptions_verification'+livefix+'_legal_entity_dob').val();
				
				if(date){
					date = date.split('-');
				} else {
					return alert('Date of Birth Empty');
				}
	
				jQuery('#pago_gateway_button').prop('disabled', true);
				jQuery('#pago_payoptions_saving').attr('style', '');
				
				var country = jQuery('#params_payoptions'+livefix+'_country').val();
				var legal_entity_type = jQuery('input[type="radio"][name="params[payoptions_verification'+livefix+'][legal_entity_type]"]:checked').val();
				
				var data = {
			    	'livemode': livemode,
			    	'ip': jQuery('#ipaddress').attr('ipaddress'),
					'id': jQuery('#params_payoptions'+livefix+'_recipient_id').val(),
					'legal_entity_type': legal_entity_type,
					'legal_entity_first_name': jQuery('#params_payoptions_verification'+livefix+'_legal_entity_first_name').val(),
					'legal_entity_last_name': jQuery('#params_payoptions_verification'+livefix+'_legal_entity_last_name').val(),
					'legal_entity_dob_day': date[0],
					'legal_entity_dob_month': date[1],
					'legal_entity_dob_year': date[2],
					
					'legal_entity_address_line1': jQuery('#params_payoptions_verification'+livefix+'_legal_entity_address_line1').val(),
					'legal_entity_address_city': jQuery('#params_payoptions_verification'+livefix+'_legal_entity_address_city').val(),
					'legal_entity_address_state': jQuery('#params_payoptions_verification'+livefix+'_legal_entity_address_state').val(),
					'legal_entity_address_postal_code': jQuery('#params_payoptions_verification'+livefix+'_legal_entity_address_postal_code').val(),
					
					'legal_entity_tax_id': jQuery('#params_payoptions_verification'+livefix+'_'+country+'_legal_entity_tax_id').val(),
					'legal_entity_personal_id_number': jQuery('#params_payoptions_verification'+livefix+'_'+country+'_legal_entity_personal_id_number').val(),
					'legal_entity_business_tax_id': jQuery('#params_payoptions_verification'+livefix+'_'+country+'_legal_entity_business_tax_id').val(),
					
					'legal_entity_business_name': jQuery('#params_payoptions_verification'+livefix+'_'+country+'_legal_entity_business_name').val(),
					'business_url': window.location.href,
					'email': jQuery('#params_payoptions'+livefix+'_email').val(),
					'country': country,
					'bank_account_country': jQuery('#params_payoptions_banking'+livefix+'_bank_account_country').val(),
					'bank_account_currency': jQuery('#params_payoptions_banking'+livefix+'_bank_account_currency').val(),
					'bank_account_id': jQuery('#params_payoptions_banking'+livefix+'_bank_account_id').val(),
					'bank_account_number': jQuery('#params_payoptions_banking'+livefix+'_bank_account_number').val(),
					'bank_account_routing_number': jQuery('#params_payoptions_banking'+livefix+'_bank_account_routing_number').val()
				};
				
				if(country == 'US'){
					data['legal_entity_ssn_last_4'] = jQuery('#params_payoptions_verification'+livefix+'_legal_entity_ssn_last_4').val();
					
					/*if(legal_entity_type == 'individual'){
						
					} else {
						//data['legal_entity_business_name'] = jQuery('#params_payoptions_verification'+livefix+'_legal_entity_business_name').val();
						//data['legal_entity_business_tax_id'] = jQuery('#params_payoptions_verification'+livefix+'_legal_entity_business_tax_id').val();
					}*/
				}
				
				var formData = new FormData();
        		formData.append('legal_entity_identity_document', jQuery('#params_payoptions_verification_legal_entity_identity_document')[0].files[0]); 
        		formData.append('payload', JSON.stringify(data)); 
        
				jQuery.ajax({
				    url: backendpoint + 'recipients',
				    type: "POST",
				    contentType: false,
            		processData: false,
				    //data: JSON.stringify(data),
				    data: formData,
				    complete: function(r){
				    	jQuery('pre.pg-response').html(r.responseText);
				    	
				    	var response = jQuery.parseJSON(r.responseText);
				    	
				    	try {
				    		if(response.id){
				    			
							    jQuery('#params_payoptions'+livefix+'_recipient_id').val(response.id);
							    jQuery('#params_instantpay'+livefix+'_recipient_id_text').html(response.id);
							    jQuery('#pago_gateway_button_delete').attr('style', '');
							    
							    jQuery.post(
							    	"index.php?option=com_pago&view=payoptions&task=store_recipient_id", 
							    	{
							    		recipient_id: response.id,
							    		account_number: jQuery('#params_payoptions'+livefix+'_bank_account_number').val(),
							    		livemode: livemode,
							    		active: active
							    	}
							    );
	                            
							    if(active != 1){
							    	jQuery('#pago-payoptions').attr('style', 'background:#eee');
							    } else if(livemode != 1){
							    	jQuery('#pago-payoptions').attr('style', 'background:#ffe5e5');
							    } else {
							    	jQuery('#pago-payoptions').attr('style', 'background:#e5ffe5');
							    }
							    
							    alert('SUCCESSFULLY UPDATED ACCOUNT');
				    		} else {
				    			var msg = response.detail.replace('support@stripe.com', 'info@corephp.com');
				    			alert(msg);
				    		}
						}
						catch(err){
							alert(response.detail);
						}
						jQuery('#pago_payoptions_saving').attr('style', 'display:none');
						jQuery('#pago_gateway_button').prop('disabled', false);
				    }
				});
			}
			
			var shipOff = function(event){
				var result = event.target.result;
			    var fileName = document.getElementById('fileBox').files[0].name; //Should be 'picture.jpg'
			    $.post('/myscript.php', { data: result, name: fileName }, continueSubmission);

			}
			
			var file = document.getElementById('params_payoptions_verification_legal_entity_identity_document').files[0];
			
			if(file){
				var reader = new FileReader();
				reader.readAsText(file, 'UTF-8');
				reader.onload = send;
			} else {
				send();
			}
		};
		
		this.get = function()
		{
			jQuery.ajax({
			    url: backendpoint + this.service,
			    type: "GET",
			    contentType: "application/json",
			    data: JSON.stringify({
					'id': 'rs_asdf07'
				}),
			    complete: function(r){
			    	jQuery('pre.pg-response').html(r.responseText);
			    }
			});
		};
		
		this.delete = function()
		{
			if (!confirm('Are you sure?')) { 
				return;
			}
			
			jQuery('#pago_gateway_button_delete').prop('disabled', true);
			jQuery('#pago_payoptions_saving').attr('style', '');
			
			var livemode = jQuery('input[type="radio"][name="params[payoptions_livetoggle][livemode]"]:checked').val();
			var livefix = '';
			
			if(livemode == 1) livefix = '_live';
			
			var recipient_id = jQuery('#params_payoptions'+livefix+'_recipient_id').val();
			
			jQuery.ajax({
			    url: backendpoint + this.service,
			    type: "DELETE",
			    contentType: "application/json",
			    data: JSON.stringify({
					'id': recipient_id,
					'livemode': livemode
				}),
			    complete: function(r){
			    	jQuery('pre.pg-response').html(r.responseText);
			    	
			    	jQuery('#params_payoptions'+livefix+'_recipient_id').val('');
					jQuery('#params_instantpay'+livefix+'_recipient_id_text').html('');
					
					jQuery.get(
				    	"index.php?option=com_pago&view=payoptions&task=delete_recipient_id&livemode="+livemode
				    );
					
					jQuery('#pago_gateway_button_delete').attr('style', 'display:none');
					
					jQuery('#pago_gateway_button_delete').prop('disabled', false);
					jQuery('#pago_payoptions_saving').attr('style', 'display:none');
			    }
			});
		};
	};
};

var paGOapi = new paGOapiObj(
	'../index.php?option=com_pago&view=api&task=call&format=json&service='
);
