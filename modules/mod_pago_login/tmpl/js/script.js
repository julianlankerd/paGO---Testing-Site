jQuery(document).ready(function() {
	jQuery(document).on('click', '.pg-login-module input[type=submit].pg-button', function(e){
		e.preventDefault();
		
		var username = jQuery(this).parents('form').find('input[name="username"]').val();
		var noticeCon = jQuery(this).parents('.pg-login-module').find( '.pg-login-notice' );
		
		var data = new Object();
		jQuery(this).parents('form').find('input').each(function(){
			data[jQuery(this).attr('name')] = jQuery(this).val();
		})
		
		jQuery.ajax({
				type: 'POST',
				url: '',
				async: false,
				data: {option : 'com_pago', view : 'account', task: 'getUsernameFromEmail', email : username},
			success:function(response){
				if(response == "NULL"){
					jQuery(noticeCon).html($MOD_PAGO_LOGIN_LOGIN_FAILED);
					jQuery(noticeCon).fadeIn();
					return false;
				} else {
					data['tmpl'] = 'component';
					data['username'] = response;
					var url = jQuery(this).parents('form').attr('action');

					jQuery(noticeCon).html();
					jQuery.ajax({
							type: 'POST',
							url: url,
							async: false,
							data:data,
						success:function(response){
							if(response==''){
								window.location = window.location;	
								return;
							}
							systemMessage = jQuery(response).find('#system-message');
							if(typeof systemMessage === "undefined" || !systemMessage.length){
								window.location = window.location;	
								return;
							} 
							jQuery(noticeCon).html($MOD_PAGO_LOGIN_LOGIN_FAILED);
							jQuery(noticeCon).fadeIn();
						}
					});
				}
			}
		});		
	});


jQuery('.pg-login-greeting').each(function(){
	var that = jQuery(this).parent('.pg_logoutBtn');
	var pg_login_greeting_width = jQuery(that).outerWidth()-jQuery(that).children('.pg_user_image').outerWidth()-jQuery(that).children('.pg-logoutBox-toggle').outerWidth()-40;
	jQuery(that).children('.pg-login-greeting').css('max-width', pg_login_greeting_width);
});


jQuery('.pg_dropdown_login').on('hover', function() {
	jQuery(this).find('.pg_dropdown').slideToggle(10).toggleClass("pg_active").next().stop( true, true);
});

})

jQuery(document).click(function(event){	
	var that = jQuery(event.target).closest('.pg-module').children('.pg_logoutBox');
	if(!jQuery(event.target).closest('.pg_logoutBox').length){
		if(jQuery(event.target).closest('.pg_logoutBtn').length){ 
			if(that.hasClass('pg_active')){
				that.slideUp(500).removeClass('pg_active');
			}
			else{	
				if(jQuery('.pg_logoutBox').hasClass('pg_active')){
					jQuery('.pg_logoutBox').slideUp(500).removeClass('pg_active');
				}			
				that.slideDown(500).addClass('pg_active');
			}
		}
		else{
			jQuery('.pg_logoutBox').slideUp(500).removeClass('pg_active');			
		}
	}
});
