
var last_quickview="";
jQuery.noConflict();
var errorMessage  = "";
jQuery( document ).ready(function() {
//if (jQuery('#creditCardForm').length){jQuery( "#creditCardForm" ).hide(); }   
	//jQuery.validator.setDefaults({  ignore: ":hidden:not(select)" });
	
	// jQuery.validator.addMethod("phone", function(value, element) {
	// 	return this.optional(element) || /^[0-9\s(-)\s+\s-]*$/.test(value);
	// }, "Not valid phone");

	// jQuery.validator.addMethod("letters", function(value, element) {
	// 	return this.optional(element) || /^[a-zA-Z]*$/.test(value);
	// }, "Only letters");

	// jQuery('#pg-account-address-form').validate({
	
	// 	rules: {

	// 		"address[s][email]":{
	// 			required: true,
	// 			email: true
				
	// 		},
	// 		"address[s][telephoneno]":{
	// 			required: true,
	// 			phone: true
	// 		},
	// 		"address[s][postcodezip]":{
	// 			required: true,
	// 			number: true
	// 		},
	// 		"address[s][firstname]":{
	// 			required: true,
	// 			letters: true
	// 		},
	// 		"address[s][lastname]":{
	// 			required: true,
	// 			letters: true
	// 		},
	// 		"address[s][city]":{
	// 			required: true,
	// 			letters: true
	// 		},
	// 		"address[b][email]":{
	// 			required: true,
	// 			email: true	
	// 		},
	// 		"address[b][telephoneno]":{
	// 			required: true,
	// 			phone:true
	// 		},
	// 		"address[b][postcodezip]":{
	// 			required: true,
	// 			number: true
	// 		},
	// 		"address[b][firstname]":{
	// 			required: true,
	// 			letters: true
	// 		},
	// 		"address[b][lastname]":{
	// 			required: true,
	// 			letters: true
	// 		},
	// 		"address[b][city]":{
	// 			required: true,
	// 			letters: true
	// 		},

	// 	},
	// 	messages: {
	// 		"address[s][email]": 'Not Valid Email',
	// 		"address[s][country]": 'This field is required'	,
	// 		"address[b][email]": 'Not Valid Email',
	// 		"address[b][country]": 'This field is required'	,
	// 		"address[s][postcodezip]": 'Wrong Zip',
	// 		"address[b][postcodezip]": 'Wrong Zip',
	// 	},
	// 	errorClass: 'error',
	// 	validClass: 'valid',
	// 	errorPlacement: function(error, element) {
			
	// 		if( element.is(':radio') )
	// 			var target = element.closest('.pg-checkout-shipping-details');
	// 		else
	// 			var target = element;
			
	// 		validationQtip(error, element, ['left center', 'right right'], target);
	// 	},
	// 	success: jQuery.noop
		
	// });

	// jQuery(document).on('click', '.pg-login input[type=submit].pg-button', function(e){
 //        e.preventDefault();
        
 //        var username = jQuery(this).parents('form').find('input[name="username"]').val();
 //        var noticeCon = jQuery(this).parents('.pg-login').find( '.pg-login-notice' );
        
 //        var data = new Object();
 //        jQuery(this).parents('form').find('input').each(function(){
 //            data[jQuery(this).attr('name')] = jQuery(this).val();
 //        })
        
 //        jQuery.ajax({
 //                type: 'POST',
 //                url: '',
 //                async: false,
 //                data: {option : 'com_pago', view : 'account', task: 'getUsernameFromEmail', email : username},
 //            success:function(response){
 //                if(response == "NULL"){
 //                    jQuery(noticeCon).html($MOD_PAGO_LOGIN_LOGIN_FAILED);
 //                    jQuery(noticeCon).fadeIn(1000);
 //                    return false;
 //                } else {
 //                    data['tmpl'] = 'component';
 //                    data['username'] = response;
 //                    var url = jQuery(this).parents('form').attr('action');

 //                    jQuery(noticeCon).html();
 //                    jQuery.ajax({
 //                            type: 'POST',
 //                            url: url,
 //                            async: false,
 //                            data:data,
 //                        success:function(response){
 //                            if(response==''){
 //                                window.location = window.location;  
 //                                return;
 //                            }
 //                            systemMessage = jQuery(response).find('#system-message');
 //                            if(typeof systemMessage === "undefined" || !systemMessage.length){
 //                                window.location = window.location;  
 //                                return;
 //                            } 
 //                            jQuery(noticeCon).html($MOD_PAGO_LOGIN_LOGIN_FAILED);
 //                            jQuery(noticeCon).fadeIn();
 //                        }
 //                    });
 //                }
 //            }
 //        });
 //    })
	
	// function validationQtip(error, element, corners, tip_target) {	
	// 	// Set positioning based on the elements position in the form
	// 	var elem = jQuery(element);
		
	// 	if(!corners)
	// 		corners = ['left center', 'right center'];
	// 	if(!tip_target)
	// 		tip_position = {
	// 			my: corners[0],
	// 			at: corners[1],
	// 			viewport: jQuery(window)
	// 		};
	// 	else {
	// 		tip_position = {
	// 			target: tip_target,
	// 			my: corners[0],
	// 			at: corners[1],
	// 			viewport: jQuery(window)
	// 		}		
	// 	}
		
	// 	// Check we have a valid error message
	// 	if(!error.is(':empty')) {
	// 		// alert(qtip({ content: error}));
	// 		// Apply the tooltip only if it isn't valid
	// 		elem.parent("div").find('.pg-error-left').remove();

	// 		elem.filter(':not(.valid)').parent("div").append('<div class="pg-notification-message pg-error-left '+errorMessage+'">'+error[0].outerText+'</div>');
	// 		elem.filter(':not(.valid)').addClass("pg_input_error");

	// 		// elem.filter(':not(.valid)').qtip({
	// 			// 				// overwrite: false,
	// 			// content: error,
	// 			// position: tip_position,
	// 			// show: {
	// 			// 	event: false,
	// 			// 	ready: true
	// 			// },
	// 			// hide: false,
	// 			// style: {
	// 			// 	classes: 'pg-error-left' // Make it red... the classic error colour!
	// 			// }
	// 		// If we have a tooltip on this element already, just update its content
	// 		// }).qtip('option', 'content.text', error);
	// 	// If the error is empty, remove the qTip
	// 	} else { 
	// 		elem.parent("div").find('.pg-error-left').remove();
	// 		elem.parent("div").find('.pg_input_error').removeClass();
	// 		//elem.parent("div").remove('.pg-error-left'); 
	// 	}
	// };
});
// function getCreditcardForm(value)
// {
// 	guest = jQuery('input[name="guest"]').attr("value");
// 	jQuery.ajax({
//         type: "POST",
//         url: 'index.php',
// 		data: 'option=com_pago&view=checkout&task=checkCreditCard&payment=' + value+ '&async=1&guest='+guest,
//         success: function(response){
//           	if(response==1){
// 				jQuery( "#creditCardForm" ).show();
// 			}
// 			else{
// 				jQuery( "#creditCardForm" ).hide();
// 			}
//         }
//     });
// }

// jQuery.validator.addMethod("creditcardmonth", function(value, element) {

// 	var year= jQuery("#pg-checkout-cc-expire-year").val();
// 	var month= jQuery("#pg-checkout-cc-expire-month").val();
// 	var minMonth = new Date().getMonth() + 1;
// 	var minYear = new Date().getFullYear();
// 	if ((year > minYear) || ((year == minYear) && (month >= minMonth)))
// 	{
//         return true;
//     }


// }, 'Please set valid expiry date.');
jQuery(document).ready(function(){
	// jQuery(document).on('submit', '#pg-order-form', function(e){
	// 	var startYear = jQuery("#startdate").val();
	// 	var endYear = jQuery("#enddate").val();

	// 	if(!startYear || !endYear){
	// 		alert(Joomla.JText.strings.PAGO_ACCOUNT_ORDER_HISTORY_DATE_REQUIRED);
	// 		e.preventDefault();
	// 		return false;
	// 	}
	// 	if(endYear < startYear){
	// 		alert(Joomla.JText.strings.PAGO_ACCOUNT_ORDER_HISTORY_DATE_ERROR);
	// 		e.preventDefault();
	// 		return false;
	// 	}
	// 	return true;
	// });

	// jQuery(document).on('click', '.pg_upload_avatar_delete', function(){
	// 	jQuery.ajax({
	// 				type: 'POST',
	// 				url: 'index.php',
	// 				async: false,
	// 				data: ({
	// 					option: "com_pago",
	// 					view: "account",
	// 					task : "removeAvatar",
	// 					dataType: 'json',
	// 				}),
	// 				success:function(response){
	// 					result = JSON.parse(response);
	// 					rand = Math.random();
	// 					image = result.avatar+'?num='+rand;
	// 					jQuery('#pago .pg-account-info-image').css('background','url(' + image + ')');	
	// 					jQuery('#pago .pg_upload_avatar_delete').remove();	
	// 				}
	// 		});		
	// });
	
	// jQuery(document).on('click','.modal-backdrop',function(){
	// 	jQuery('.modal').modal('hide');
	// 	jQuery('#guest-submision .redirectAfterLogin').val(0);
	// });
	// jQuery(document).on('change','#addressscountry',function(){
	// 	var countryCode = jQuery(this).val();	
	// 	jQuery('#s_countystate option').each(function(){
	// 		if(jQuery(this).hasClass(countryCode)) jQuery(this).css('display','block');
	// 		else jQuery(this).css('display','none');
	// 	});
	// })
	// jQuery(document).on('change','#addressbcountry',function(){
	// 	var countryCode = jQuery(this).val();	
	// 	jQuery('#b_countystate option').each(function(){
	// 		if(jQuery(this).hasClass(countryCode)) jQuery(this).css('display','block');
	// 		else jQuery(this).css('display','none');
	// 	});
	// })


	// checkout start

	// 

	// jQuery(document).on('click', '.reply_comment', function(e){
	// 	var obj = jQuery(this);
				
	// 	if (!obj.hasClass('guest')){
	// 		if (jQuery('.pg-reply-comment-container textarea').hasClass('pg-reply-comment-large-textarea')){
	// 			jQuery('.pg-reply-comment-container textarea').removeClass('pg-add-comment-large-textarea');
	// 			jQuery('.pg-reply-comment-container textarea').css('height','36px');	

	// 			jQuery('.pg-reply-comment-container .addCommentBtn').addClass('pg-green-text-btn');
	// 			jQuery('.pg-reply-comment-container .addCommentBtn').removeClass('pg-green-background-btn');
	// 		}

	// 		if (obj.siblings('.pg-reply-comment-container').hasClass('open')){
	// 			obj.siblings('.pg-reply-comment-container').removeClass('open');
	// 			obj.siblings('.pg-reply-comment-container').slideUp();
				
	// 			commentId = '';
	// 			jQuery('input[name=comment_parentId]').val('');
	// 		} 
	// 		else{
	// 			jQuery('.pg-reply-comment-container').removeClass('open');
	// 			jQuery('.pg-reply-comment-container').slideUp();

	// 			obj.siblings('.pg-reply-comment-container').addClass('open');
	// 			obj.siblings('.pg-reply-comment-container').slideDown();
				
	// 			var replyCommentContainerWidth = parseInt(obj.siblings('.pg-reply-comment-container').width());
	// 			var replyCommentAvatarImageWidth = parseInt(obj.siblings('.pg-reply-comment-container').find('.pg-comment-author-image').width());
	// 			var replyCommentAvatarImageBorderLeft = parseInt(obj.siblings('.pg-reply-comment-container').find('.pg-comment-author-image').css('border-left-width'));
	// 			var replyCommentAvatarImageBorderRight = parseInt(obj.siblings('.pg-reply-comment-container').find('.pg-comment-author-image').css('border-right-width'));
	// 			var replyCommentAvatarImageMargin = parseInt(obj.siblings('.pg-reply-comment-container').find('.pg-comment-author-image').css('margin-right'));	

	// 			jQuery('.pg-reply-comment-container textarea').css('width', replyCommentContainerWidth-replyCommentAvatarImageWidth-replyCommentAvatarImageMargin-replyCommentAvatarImageBorderLeft-replyCommentAvatarImageBorderRight-1);

	// 			commentId = obj.parent().parent().parent().attr('id');
	// 			commentId = commentId.replace("comment_", "");
	// 			jQuery('input[name=comment_parentId]').val(commentId);
	// 		}
	// 	}
	// 	else{
	// 		commentId = jQuery(this).parent().parent().parent().attr('id');
	// 		commentId = commentId.replace("comment_", "");
	// 		jQuery('#guest-submision form input[name="comment_parentId"]').val(commentId)
	// 		jQuery('#guest-submision').modal();
	// 	}
	// });

	// jQuery(document).on('focus', '.pg-add-comment-container textarea', function(){
	// 	if (jQuery(this).siblings('.addCommentBtn').hasClass('guest')){
	// 		jQuery('#guest-submision').modal();
	// 		jQuery('#guest-submision .redirectAfterLogin').val('comment');
	// 	}
	// 	else{
	// 		jQuery(this).addClass('pg-add-comment-large-textarea');
	// 		jQuery(this).css('height','150px');

	// 		jQuery('.pg-add-comment-container .addCommentBtn').removeClass('pg-green-text-btn');
	// 		jQuery('.pg-add-comment-container .addCommentBtn').addClass('pg-green-background-btn');
	// 	}
	// })

	// jQuery(document).on('focus', '.pg-reply-comment-container textarea', function(){
	// 	jQuery(this).addClass('pg-reply-comment-large-textarea');
	// 	jQuery(this).css('height','150px');

	// 	jQuery('.pg-reply-comment-container .addCommentBtn').removeClass('pg-green-text-btn');
	// 	jQuery('.pg-reply-comment-container .addCommentBtn').addClass('pg-green-background-btn');
	// })

	// jQuery(document).click(function(e){
	// 	if (!jQuery(e.target).hasClass('pg-add-comment-large-textarea')){
	// 		jQuery('.pg-add-comment-container textarea').removeClass('pg-add-comment-large-textarea');
	// 		jQuery('.pg-add-comment-container textarea').css('height','36px');		

	// 		jQuery('.pg-add-comment-container .addCommentBtn').removeClass('pg-green-background-btn');
	// 		jQuery('.pg-add-comment-container .addCommentBtn').addClass('pg-green-text-btn');
	// 	}

	// 	if (!jQuery(e.target).hasClass('pg-reply-comment-large-textarea') && !jQuery(e.target).hasClass('reply_comment')){
	// 		if (jQuery('.pg-reply-comment-container').hasClass('open')){
	// 			if (jQuery('.pg-reply-comment-container textarea').hasClass('pg-reply-comment-large-textarea')){
	// 				jQuery('.pg-reply-comment-container textarea').removeClass('pg-add-comment-large-textarea');
	// 				jQuery('.pg-reply-comment-container textarea').css('height','36px');		

	// 				jQuery('.pg-reply-comment-container .addCommentBtn').removeClass('pg-green-background-btn');
	// 				jQuery('.pg-reply-comment-container .addCommentBtn').addClass('pg-green-text-btn');
	// 			}

	// 			jQuery('.pg-reply-comment-container').removeClass('open');
	// 			jQuery('.pg-reply-comment-container').slideUp();
	// 		}
	// 	}
	// })
	
	// harcakana
	// jQuery(document).on('click', '.addCommentBtn', function(e){
	// 	e.preventDefault();
	// 	if (jQuery(this).hasClass('guest')){

	// 	}
	// 	else{
	// 		var error = 0;
	// 		jQuery(this).parents('form').find('.highlight').removeClass('highlight');

	// 		$itemID = jQuery('#pg-product-view .product-container').attr('itemid');
			
	// 		commentUserId = jQuery('input[name=comment_userId]').val();

	// 		commentName = false;
	// 		commentEmail = false;
	// 		commentWebSite = false;

	// 		if(commentUserId == 0){
	// 			commentName = jQuery(this).parents('form').find('input[name=comment_name]').val();
	// 			commentEmail = jQuery(this).parents('form').find('input[name=comment_email]').val();
	// 			commentWebSite = jQuery(this).parents('form').find('input[name=comment_web_site]').val();
	// 			if(commentName.length == ""){
	// 				jQuery(this).parents('form').find('input[name=comment_name]').addClass('highlight');
	// 				error = 1;			
	// 			}
	// 			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	// 			if(commentEmail.length == '') {
	// 				jQuery(this).parents('form').find('input[name=comment_email]').addClass('highlight');
	// 				error = 1;
	// 			} else if(!emailReg.test(commentEmail)) {	
	// 				jQuery(this).parents('form').find('input[name=comment_email]').addClass('highlight');
	// 				error = 1;
	// 			}

	// 			if(commentWebSite.length != '') {
	// 				var webReg = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;
	// 				if(!webReg.test(commentWebSite)) {	
	// 					jQuery(this).parents('form').find('input[name=comment_web_site]').addClass('highlight');
	// 					error = 1;
	// 				}
	// 			}
	// 		}
	// 		commentMessage = jQuery(this).parents('form').find('textarea[name=comment_message]').val();
	// 		if(commentMessage.length == ""){
	// 			jQuery(this).parents('form').find('textarea[name=comment_message]').addClass('highlight');
	// 			error = 1;		
	// 		}
	// 		commentParentId = jQuery(this).parents('form').find('input[name=comment_parentId]').val();
			
			
	// 		if(error == 1){
	// 			return false;
	// 		}
	// 		jQuery.ajax({
	// 			type: 'POST',
	// 			url: 'index.php',
	// 			async: false,
	// 			data: ({
	// 				option: "com_pago",
	// 				view: "comments",
	// 				task : "add",
	// 				dataType: 'json',
	// 				itemId: $itemID,
	// 				commentName: commentName,
	// 				commentEmail: commentEmail,
	// 				commentWebSite: commentWebSite,
	// 				commentMessage: commentMessage,
	// 				commentParentId: commentParentId,
	// 			}),
	// 			success: function( response ) {
	// 				if(response){
	// 					result = JSON.parse(response);
						
						
	// 					if(result.status == 'success'){
	// 						var commentHtml = '';
	// 						if(result.comment.parent_id == 0){
								
	// 							commentHtml += '<div class="pg-show-comment" id="comment_'+result.comment.id+'">';
	// 							if(result.comment.author_web_site.length >5){
	// 								commentHtml += '<a target="_blank" href = "'+result.comment.author_web_site+'" class="pg_user_image pg-comment-author-image"></a>';
	// 							}else{
	// 								commentHtml += '<span class="pg_user_image pg-comment-author-image"><img src="'+result.comment_avatar+'"></span>';
	// 							}
	// 							commentHtml += '<div class = "pg-comment-info">';
	// 							if(result.comment.author_web_site.length >5){
	// 								commentHtml += '<a target="_blank" href = '+result.comment.author_web_site+'><span class="pg-comment-author-name">'+result.comment.author_name+'</span></a>';
	// 							}else{
	// 								commentHtml += '<span class="pg-comment-author-name">'+result.comment.author_name+'</span>';
	// 							}
	// 							commentHtml += '<div class="pg-comment-message">'+result.comment.text+'</div>';
	// 							if(result.comment.replay == 1){
	// 								commentHtml += '<input type="button" class="reply_comment" value="replay">';
	// 								commentHtml += '<div class="pg-show-comments-replies"></div>';
	// 							}
	// 							commentHtml += '</div>';
	// 							commentHtml += '<div class = "clearfix"></div>';
	// 							commentHtml += '</div>';
	// 							jQuery( ".pg-comments-con" ).prepend( commentHtml );
	// 						}else{
								
								
	// 							commentHtml += '<div class="pg-show-comments-reply">';
	// 							if(result.comment.author_web_site.length >5){
	// 								commentHtml += '<a target="_blank" href = "'+result.comment.author_web_site+'" class="pg_user_image pg-comment-author-image reply"></a>';
	// 							}else{
	// 								commentHtml += '<span class="pg_user_image pg-comment-author-image reply"><img src="'+result.comment_avatar+'"></span>';
	// 							}
	// 							commentHtml += '<div class = "pg-comment-info">';
	// 							if(result.comment.author_web_site.length >5){
	// 								commentHtml += '<a target="_blank" href = '+result.comment.author_web_site+'><span class="pg-comment-author-name">'+result.comment.author_name+'</span></a>';
	// 							}else{
	// 								commentHtml += '<span class="pg-comment-author-name">'+result.comment.author_name+'</span>';
	// 							}
	// 							commentHtml += '<div class="pg-comment-re-message">'+result.comment.text+'</div>';
	// 							commentHtml += '</div>';
	// 							commentHtml += '<div class = "clearfix"></div>';
	// 							commentHtml += '</div>';
	// 							jQuery( "#comment_"+result.comment.parent_id+' .pg-show-comments-replies').prepend( commentHtml );

	// 							jQuery('.pg-reply-comment-container').removeClass('open');
	// 							jQuery('.pg-reply-comment-container').slideUp();
	// 						}
							
	// 						if(commentUserId == 0){
	// 							jQuery('input[name=comment_name]').val('');
	// 							jQuery('input[name=comment_email]').val('');
	// 							jQuery('input[name=comment_web_site]').val('');	
	// 						}
							
	// 						jQuery('input[name=comment_parentId]').val('');
	// 						jQuery('textarea[name=comment_message]').val('');
	// 					}
	// 					if(result.status == 'pending'){
	// 						if(commentUserId == 0){
	// 							jQuery('input[name=comment_name]').val('');
	// 							jQuery('input[name=comment_email]').val('');
	// 							jQuery('input[name=comment_web_site]').val('');
	// 						}
	// 						jQuery('input[name=comment_parentId]').val('');
	// 						jQuery('textarea[name=comment_message]').val('');

	// 						jQuery('.pg-reply-comment-container').removeClass('open');
	// 						jQuery('.pg-reply-comment-container').slideUp();
	// 						jQuery('html, body').animate({scrollTop: jQuery('.pg-notification').offset().top}, 'slow');
	// 						jQuery(".pg-notification").html(result.message);
	// 						jQuery(".pg-notification").fadeIn(1000)	
	// 					}
	// 				}
	// 			}
	// 		});
	// 	}		
	// })
	// checkout start
	// jQuery(document).on('click', '.pg_checkout_guest_continue', function(event){
	// 	type = jQuery('.pg_checkout_guest_forum input[name="checkout_user_type"]:checked').val();
	// 	if(type == "guest"){
	// 		if(jQuery('#pg-checkout-guest-checkout-form').length){

	// 			var data = new Object();
	// 			jQuery('form#pg-checkout-guest-checkout-form input').each(function(){
	// 				data[jQuery(this).attr('name')] = jQuery(this).val();
	// 			})

	// 			data['tmpl'] = 'component';
	// 			var url = jQuery('form#pg-checkout-guest-checkout-form').attr('action');
				
	// 			jQuery.ajax({
	// 					type: 'POST',
	// 					url: url,
	// 					async: false,
	// 					data:data,
	// 				success:function(response){
	// 					jQuery('.pg-checkout-panel-group .pg-checkout-panel.open .pg-checkout-content').slideUp('fast');
	// 					jQuery('.pg-checkout-panel-group .pg-checkout-panel.open').removeClass('open');

	// 					formHtml = jQuery(response).find("#pg-checkout-shipping-info .pg-checkout-shipping-info-content").html();
	// 					jQuery('.pg-checkout-panel-group .pg-checkout-panel.pg-checkout-register-guest .pg-checkout-content .pg_user_guest').html(formHtml);
						
	// 					jQuery('.pg-checkout-panel-group .pg-checkout-panel.pg-checkout-register-guest .pg-checkout-content').slideDown('fast');
	// 					jQuery('.pg-checkout-panel-group .pg-checkout-panel.pg-checkout-register-guest').addClass('open');

	// 					jQuery('#pg-checkout select').chosen({disable_search_threshold: 10});
	// 				}
	// 			});
	// 			return;
	// 		}
	// 		//
	// 		//jQuery( "#pg-checkout-guest-checkout-form" ).submit();
	// 	}
	// 	if(type == "register"){
	// 		if(jQuery('.pg_user_register form').length){
	// 			/*var data = new Object();
	// 			jQuery('.pg_user_register form input').each(function(){
	// 				data[jQuery(this).attr('name')] = jQuery(this).val();
	// 			})
	// 			data['tmpl'] = 'component';
	// 			var url = jQuery('.pg_user_register form').attr('action');
				
	// 			jQuery.ajax({
	// 					type: 'POST',
	// 					url: url,
	// 					async: false,
	// 					data:data,
	// 				success:function(response){
	// 					systemMessage = jQuery(response).find('#system-message');
	// 					jQuery('.pg_checkout_notice').html(systemMessage.html());

	// 				}
	// 			});*/
	// 			return;
	// 		}
	// 		jQuery.ajax({
	// 			type: 'POST',
	// 			url: 'index.php',
	// 			async: false,
	// 			data: ({
	// 				option: "com_pago",
	// 				view: "register",
	// 				tmpl : "component",
	// 			}),
	// 			success: function( response ) {
	// 				jQuery('.pg-checkout-panel-group .pg-checkout-panel.open .pg-checkout-content').slideUp('fast');
	// 				jQuery('.pg-checkout-panel-group .pg-checkout-panel.open').removeClass('open');

	// 				jQuery('.pg-checkout-panel-group .pg-checkout-panel.pg-checkout-register .pg-checkout-content').slideDown('fast');
	// 				jQuery('.pg-checkout-panel-group .pg-checkout-panel.pg-checkout-register').addClass('open');

	// 				formHtml =jQuery(response).find( "form" ); 
	// 				jQuery('.pg_user_register').append("<div id='pg-system-messages'></div>");
	// 				jQuery('.pg_user_register').append(formHtml);

	// 				//jQuery('.pg_user_register form').find('fieldset legend').remove();
	// 				//jQuery('.pg_user_register form').find('fieldset .control-group:first-child').remove();
	// 				//jQuery('.pg_user_register form').html(jQuery('.pg_user_register form').find('fieldset').html()+jQuery('.pg_user_register form').html());
	// 				//jQuery('.pg_user_register form').find('fieldset').remove();
	// 				//jQuery('.pg_user_register form>dl>dt:first-child').remove();
	// 				//jQuery('.pg_user_register form>dl>dd:first-child').remove();

	// 				if(jQuery('.pg_user_register form>dl').length){
	// 					jQuery('.pg_user_register form').html(jQuery('.pg_user_register form>dl').html()+jQuery('.pg_user_register form').html());
	// 					jQuery('.pg_user_register form>dl').remove();
	// 				}

	// 				//jQuery('.pg_user_register form').find('#jform_username').attr('type','hidden').parent().css('display','none').prev().css('display','none').parents('.control-group').css('display','none');
	// 				//jQuery('.pg_user_register form').find('#jform_name').attr('type','hidden').parent().css('display','none').prev().css('display','none').parents('.control-group').css('display','none');
	// 				//jQuery('.pg_user_register form').find('#jform_email2').attr('type','hidden').parent().css('display','none').prev().css('display','none').parents('.control-group').css('display','none');
	// 				jQuery('.pg_user_register form>div:last-child').css('display','none');

	// 				email1 = jQuery('.pg_user_register form').find('#jform_email1').parents('.control-group');

	// 				if (email1.length){
	// 					jQuery('.pg_user_register form').prepend(email1);
	// 					jQuery('.pg_user_register form').prepend(jQuery('.pg_user_register form #jform_email1').parents('dd').prev());
	// 					jQuery('.pg_user_register form').prepend(jQuery('.pg_user_register form #jform_email1').parents('dd'));
	// 					jQuery('.pg_user_register form').prepend(jQuery('.pg_user_register form #jform_email1').parents('dd').next());
	// 				}

	// 				jQuery(document).on('change','.pg_user_register form #jform_email1',function(){
	// 					jQuery('.pg_user_register form #jform_username').val(jQuery(this).val());
	// 					jQuery('.pg_user_register form #jform_email2').val(jQuery(this).val());
	// 					jQuery('.pg_user_register form #jform_name').val(jQuery(this).val());
	// 				});
	// 			}
	// 		});
	// 		//pg_user_register
	// 	}
	// 	return;	
	// })

	// jQuery('.pg_checkout_guest_continue, #pago .pg_user_register .pg-register-button').click(function(){
	// 	jQuery("#member-registration .pg-notification-message").remove();
	// 	jQuery("#member-registration .errorField").removeClass("errorField");
	// 	if(jQuery('.pg_user_register form').length){
	// 		var form = jQuery(".pg_user_register form");
	// 		var data = new Object();
	// 		form.find('input').each(function(){
	// 			data[jQuery(this).attr('name')] = jQuery(this).val();
	// 		})
	// 		data['tmpl'] = 'component';
	// 		data['jform[name]'] = data['jform[name1]'];
	// 		data['jform[username]'] = data['jform[email2]'] = data['jform[email1]'];
			
	// 		if (/[0-9]/.test(data['jform[name]'])) {
	// 			jQuery('#jform_name1').addClass("errorField");
	// 			jQuery('#jform_name1').parent("div").append( '<div class="pg-notification-message pg-error-left '+errorMessage +' ">Only Letters</div>' );
	// 		}
	// 		if (data['jform[name]'].length > 20) {
	// 			jQuery('#jform_name1').addClass("errorField");
	// 			jQuery('#jform_name1').parent("div").append( '<div class="pg-notification-message pg-error-left '+errorMessage +' ">Maximum 20 words</div>' );
	// 		}
	// 		if(data['jform[name]'] == ''){
	// 			jQuery('#jform_name1').addClass("errorField");
	// 			jQuery('#jform_name1').parent("div").append( '<div class="pg-notification-message pg-error-left '+errorMessage +' ">Name Is Required</div>' );
	// 		}
	// 		if(data['jform[username]'] == ''){
	// 			jQuery('#jform_email1').addClass("errorField");
	// 			jQuery('#jform_email1').parent("div").append('<div class="pg-notification-message pg-error-left '+errorMessage +' ">Email Is Required</div>');
	// 		}
	// 		if(data['jform[password1]'] == ''){
	// 			jQuery('#jform_password1').addClass("errorField");
	// 			jQuery('#jform_password1').parent("div").append('<div class="pg-notification-message pg-error-left '+errorMessage +' ">Password Is Required</div>');
	// 		}
	// 		if(data['jform[name]'] == '' || data['jform[username]'] == '' || data['jform[password1]'] == '' || /[0-9]/.test(data['jform[name]']) || data['jform[name]'].length > 20)	{
	// 			jQuery("#member-registration").find(".pg-notification-message").fadeIn(1000);
	// 			return false;
	// 		}
	// 		jQuery.ajax({
	// 			type: 'POST',
	// 			url: 'index.php',
	// 			async: false,
	// 			data: data,
	// 			success: function( response ) {
	// 				if(response!=1){
	// 					var response = JSON.parse(response);
	// 					if(response.name1){
	// 						jQuery('#jform_name1').addClass("errorField");
	// 						jQuery('#jform_name1').parent("div").append( '<div class="pg-notification-message pg-error-left ' +errorMessage + ' ">'+response.name1+'</div>' );
							
	// 					}
	// 					if(response.email){
	// 						jQuery('#jform_email1').addClass("errorField");
	// 						jQuery('#jform_email1').parent("div").append('<div class="pg-notification-message pg-error-left ' +errorMessage + ' ">'+response.email+'</div>');
	// 					}	
	// 					if(response.password){
	// 						jQuery('#jform_password1').addClass("errorField");
	// 						jQuery('#jform_password2').addClass("errorField");
	// 						jQuery('#jform_password1').parent("div").append('<div class="pg-notification-message pg-error-left ' +errorMessage + ' ">'+response.password+'</div>');

	// 					}
	// 					jQuery("#member-registration").find(".pg-notification-message").fadeIn(1000);
	// 					var errors = '';
	// 					for(error in response){
	// 						if(error=='email'){
	// 							jQuery('#jform_email1').removeClass('pg-not-valid');
	// 							jQuery('#jform_email1').addClass('pg-not-valid');
								
	// 						}
	// 						if(error=='password'){
	// 							jQuery('#jform_password1').removeClass('pg-not-valid');
	// 							jQuery('#jform_password1').addClass('pg-not-valid');
	// 							jQuery('#jform_password2').removeClass('pg-not-valid');
	// 							jQuery('#jform_password2').addClass('pg-not-valid');
	// 						}
	// 					}
	// 				}else{

	// 					data['option']='com_users';
	// 					data['task']='registration.register';
	// 					jQuery.ajax({
	// 						type: 'POST',
	// 						url: 'index.php',
	// 						async: false,
	// 						data: data,
	// 						success: function( response ) {
	// 							systemmessage = jQuery(response).find('#system-message');
	// 							var message = '';
	// 							if($JOOMLA_VERSION < 3){
	// 								var notices = systemmessage.find('dd.notice').html();
	// 								if(notices){
	// 									systemmessage.find('dd.notice').remove();
	// 									message += '<div class="alert alert-warning">'+notices+'</div>';
										
	// 								}
	// 								var warning = systemmessage.find('dd.warning').html();
	// 								if(warning){
	// 									systemmessage.find('dd.warning').remove();
	// 									message += '<div class="alert alert-danger">'+warning+'</div>';
	// 								}
									
	// 								var successMess = systemmessage.find('dd.message li').html();
	// 								if(successMess){
	// 									systemmessage.find('dd.message').remove();
	// 									message += '<div class="alert alert-success">'+successMess+'</div>';
	// 								}
	// 							}else{
	// 								message = systemmessage;	
	// 							}
								
								
	// 							jQuery('#pg-system-messages').html(message);
	// 							jQuery('#member-registration').remove();
	// 						}
	// 					});
	// 				};
	// 			}
	// 		});
	// 	}
	// })

	// jQuery(document).on('click', '.pg-checkout-shipping-continue', function(event){
	//  	var shippingAddressId = jQuery("input[name='address[s][id]']:checked").val();
	//  	if(shippingAddressId > 0 )
	//  	{
	//  		jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').slideDown('fast');
	//  	}

	//  	jQuery.ajax({
	// 		type: 'POST',
	// 		url: 'index.php',
	// 		async: false,
	// 		dataType: 'json',
	// 		data: ({
	// 			option: "com_pago",
	// 			view: "checkout",
	// 			task : "calc_tax",
	// 			addressID : shippingAddressId,
	// 		}),
	// 		success: function( response ) 
	// 		{
	// 			if(response)
	// 			{
	// 				jQuery('.taxInCheckout').html(response['tax']);
	// 				jQuery('.totalInCheckout').html(response['total']);
	// 			}
	// 		}
	// 	});

	//  	jQuery(".pg-add-address-form").each(function() {
	// 				guest = jQuery(this).find('input[name="guest"]').val();
	// 			});
	// 			jQuery.ajax({
	// 				type: 'POST',
	// 				url: 'index.php',
	// 				async: false,
	// 				data: ({
	// 					option: "com_pago",
	// 					view: "checkout",
	// 					guest: guest,
	// 					task : "get_register_address_form",
	// 					dataType: 'json',
	// 					prefix: 'b',
	// 					preset_number: 1,
	// 				}),
	// 				success: function( response ) 
	// 				{
	// 					if(response)
	// 					{
	// 						result = JSON.parse(response);
	// 						if(result.status == "success")
	// 						{
	// 							jQuery('.pg-saved_billing_address').html(result.formHtml);
	// 							jQuery('.billing_form select').chosen({disable_search_threshold: 5});
	// 							jQuery(".pg-add-address-form").each(function() {
	// 								jQuery(this).find('input[name="guest"]').val(guest);
	// 							});

	// 							if( jQuery('#pg-checkout-billing-form input[type="radio"]').length > 1 ) {
	// 								if( !jQuery('#pg-billing-address-add').is(':checked') ) {
	// 									jQuery('.pg-checkout-billing-address-fields').hide();
	// 									deactivate_form( '.pg-checkout-billing-address-fields' );
	// 								}
	// 							}
	// 						}
	// 					}
	// 				}
	// 			});

	// 			jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel').removeClass('open');
	// 			jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').find('.pg-checkout-shipping-info-content').slideUp('fast');

	// 			jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').addClass('open');
	// 			jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').find('.pg-checkout-shipping-info-content').slideDown('fast');
	    		
	//  })
	//  jQuery(document).on('click', '.pg_checkout_save_address', function(event){

	//  	var formValidated = true;
	//  	var shippAddInfo = {};

	//  	if(jQuery("input[name='address[s][id]']:checked").val() == 0)
	//  	{
			
	// 			var frmShipname,frmShipId;
	// 			frmShipname = document.getElementById('add_ship_form');

	// 			if (frmShipname == null)
	// 			{
	// 				frmShipId = ".pg-checkout-shipping-details #pg-checkout-shipping-form";
	// 			}
	// 			else
	// 			{
	// 				frmShipId = ".pg-checkout-shipping-details #add_ship_form";
	// 			}

	// 			jQuery('input,select,textarea', frmShipId).each( function(index,el)
	// 			{

	// 				if(jQuery(this).attr('name') == 'address[s][country]'){
	// 					shippAddInfo['country'] = jQuery(this).val();
	// 				}

	// 				if(jQuery(this).attr('name') == 'address[s][countystate]'){
	// 					shippAddInfo['state'] = jQuery(this).val();
	// 				}
					
	// 				if(jQuery(this).attr('name') == 'address[s][city]'){
	// 					shippAddInfo['city'] = jQuery(this).val();
	// 				}

	// 				if(jQuery(this).attr('name') == 'address[s][postcodezip]'){
	// 					shippAddInfo['zip'] = jQuery(this).val();
	// 				}

	// 				var checkValidation = (jQuery(this).hasClass('required')) && (jQuery(this).val() == '' || jQuery(this).val() == ' ');

	// 			if(checkValidation)
	// 			{
	// 				jQuery( this ).addClass('highlight');
	// 				formValidated = false;
	// 			}
	// 			else
	// 			{
	// 				jQuery( this ).removeClass( 'highlight');

	// 				if( jQuery(this).attr('name') == 'address[s][telephoneno]')
	// 				{
						 	
	// 				}
	// 				if( jQuery(this).attr('name') == 'address[s][email]')
	// 				{
	// 					 	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	// 						if (filter.test(jQuery(this).val())) {}
	// 						else
	// 						{
	// 							jQuery( this ).addClass('highlight');
	// 							formValidated = false;
	// 						}
	// 				}
	// 				if( jQuery(this).attr('name') == 'address[s][confirm-email]')
	// 				{
	// 					if (jQuery('#pg-email').val() != jQuery( this ).val())
	// 					{
	// 						jQuery( this ).addClass('highlight');
	// 						formValidated = false;
	// 					}
	// 				}
	// 			}
	// 		});
	// 	}


	// 	if(!formValidated)
	// 	{
	// 		return false;
	// 	}



	// 	jQuery(".pg-add-address-form").each(function() {
	// 		guest = jQuery(this).find('input[name="guest"]').val();
	// 	});

	// 	jQuery.ajax({
	// 		type: 'POST',
	// 		url: 'index.php',
	// 		async: false,
	// 		dataType: 'json',
	// 		data: ({
	// 			option: "com_pago",
	// 			view: "checkout",
	// 			task : "calc_tax",
	// 			data : shippAddInfo,
	// 		}),
	// 		success: function( response ) 
	// 		{
	// 			console.log(response);
				
	// 			if(response)
	// 			{
	// 				jQuery('.shippingInCheckout').html(response['shipping']);
	// 				jQuery('.taxInCheckout').html(response['tax']);
	// 				jQuery('.totalInCheckout').html(response['total']);
	// 			}
	// 		}
	// 	});


	// 	if (jQuery(this).parents('.pg_user_guest').length != 0 ){
	// 		if(jQuery('input[name="sameasshipping"]:checked').val() != 'yes'){
	// 			jQuery('.pg-checkout-billing').slideDown('fast');
	// 		}
	// 		else{
	// 			jQuery('.pg-checkout-billing').slideUp('fast');	
	// 		}

	// 		if(jQuery('input[name="sameasshipping"]:checked').val() != 'yes') {
	// 			jQuery(".pg-add-address-form").each(function() {
	// 				guest = jQuery(this).find('input[name="guest"]').val();
	// 			});
	// 			jQuery.ajax({
	// 				type: 'POST',
	// 				url: 'index.php',
	// 				async: false,
	// 				data: ({
	// 					option: "com_pago",
	// 					view: "checkout",
	// 					task : "get_address_form",
	// 					dataType: 'json',
	// 					prefix: 'b',
	// 					guest: guest,
	// 					preset_number: 1,
	// 				}),
	// 				success: function( response ) {
	// 					if(response){

	// 						result = JSON.parse(response);
	// 						if(result.status == "success")
	// 						{
	// 							jQuery('.billing_form').html(result.formHtml);
	// 							//jQuery('.billing_form .countystate').chained(".billing_form .country");
	// 							jQuery('.billing_form select').chosen({disable_search_threshold: 5});
	// 							jQuery(".pg-add-address-form").each(function() {
	// 								jQuery(this).find('input[name="guest"]').val(guest);
	// 							});
	// 						}
	// 					}
	// 				}
	// 			});	
	// 		}else{
	// 			jQuery('.billing_form').html('');
	// 		}
	// 	}
	// 	else{
	//     	if(jQuery('input[name="sameasshipping"]:checked').val() != 'yes' || jQuery("input[name='address[s][id]']:checked").val() > 0){
	// 			jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').slideDown('fast');
	//     	}
	//     	else{
	//     		jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').slideUp('fast');	
	//     	}
	//     	if(guest == 0 && jQuery('#same_as_shipping_hidden').val() == 0)
	//     	{
	//     		if(jQuery('input[name="sameasshipping"]:checked').val() != 'yes') {
	// 			jQuery(".pg-add-address-form").each(function() {
	// 				guest = jQuery(this).find('input[name="guest"]').val();
	// 			});
	// 			jQuery.ajax({
	// 				type: 'POST',
	// 				url: 'index.php',
	// 				async: false,
	// 				data: ({
	// 					option: "com_pago",
	// 					view: "checkout",
	// 					guest: guest,
	// 					task : "get_register_address_form",
	// 					dataType: 'json',
	// 					prefix: 'b',
	// 					preset_number: 1,
	// 				}),
	// 				success: function( response ) 
	// 				{
	// 					if(response)
	// 					{
	// 						result = JSON.parse(response);
	// 						if(result.status == "success")
	// 						{
	// 							jQuery('.pg-saved_billing_address').html(result.formHtml);
	// 							//jQuery('.billing_form .countystate').chained(".billing_form .country");
	// 							jQuery('.billing_form select').chosen({disable_search_threshold: 5});
	// 							jQuery(".pg-add-address-form").each(function() {
	// 								jQuery(this).find('input[name="guest"]').val(guest);
	// 							});

	// 							if( jQuery('#pg-checkout-billing-form input[type="radio"]').length > 1 ) {
	// 								if( !jQuery('#pg-billing-address-add').is(':checked') ) {
	// 									jQuery('.pg-checkout-billing-address-fields').hide();
	// 									deactivate_form( '.pg-checkout-billing-address-fields' );
	// 								}
	// 							}
	// 						}
	// 					}
	// 				}
	// 			});	
	// 			}else{
	// 				jQuery('.pg-saved_billing_address').html('');
	// 			}
	//     	}
	//     } 

		
		
	// 	if (jQuery(this).parents('.pg_user_guest').length != 0) {
	// 	//if (jQuery(this).parents('.pg_user_guest').length != 0){
	// 		if(jQuery('input[name="sameasshipping"]:checked').val() != 'yes'){
	//     		jQuery('.pg-checkout-register-guest').removeClass('open');
	// 			jQuery('.pg-checkout-register-guest .pg-checkout-content').slideUp('fast');

	// 			jQuery('.pg-checkout-billing').addClass('open');
	// 			jQuery('.pg-checkout-billing .pg-checkout-content').slideDown('fast');

	// 			jQuery('#pg-checkout .pg-checkout-register-guest').removeClass('hide-change');



	// 			return false;
	//     	}
	//     }
	//     else{
	//     	if (!jQuery('.pg-checkout-shipping-address-fields input[id="same-shiping-address"]').is(':checked') || jQuery("input[name='address[s][id]']:checked").val() > 0){
	//     		jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel').removeClass('open');
	// 			jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').find('.pg-checkout-shipping-info-content').slideUp('fast');

	// 			jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').addClass('open');
	// 			jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').find('.pg-checkout-shipping-info-content').slideDown('fast');
	//     		if(guest == 0 && jQuery('#same_as_shipping_hidden').val() == 0)
	//     		{
	//     			jQuery('#same_as_shipping_hidden').val(1);
	// 				return false;
	//     		}
	//     	}
	//     }
	// 	event.preventDefault();


	// 	var address = new Object();
	// 	var sameasshipping;
	// 	var save_address = jQuery('.save_address:checked').val();
	// 	var guest;

	// 	if (typeof save_address == "undefined") {
	// 		save_address = 'no';
	// 	}
	// 	jQuery(".pg-add-address-form").each(function() {
	// 		addressType = jQuery(this).attr('type_of_address');
	// 		address[addressType] = new Object();
			
	// 		guest = jQuery(this).find('input[name="guest"]').val();
	// 		if(addressType == 's'){
	// 			sameasshipping = jQuery(this).find('input[name="sameasshipping"]:checked').val();
	// 			if (typeof sameasshipping == "undefined") {
	// 				sameasshipping = 'no';
	// 			}
	// 		}

	// 		jQuery(this).find("input[name^='address']").each(function() {
	// 			fieldName = jQuery(this).attr('name').substring(11);
	// 			fieldName = fieldName.substring(0, fieldName.length - 1);
	// 			address[addressType][fieldName] = jQuery(this).val();
	// 		});

	// 		jQuery(this).find("select[name^='address']").each(function() {
	// 			fieldName = jQuery(this).attr('name').substring(11);
	// 			fieldName = fieldName.substring(0, fieldName.length - 1);
	// 			address[addressType][fieldName] = jQuery(this).val();
	// 		});
	// 	});

	// 	if(!jQuery.isEmptyObject(address))
	// 	{
	// 		address['s']['id'] = jQuery("input[name='address[s][id]']:checked").val();

	// 		if(!jQuery.isEmptyObject(address['b']))
	// 		{
	// 			address['b']['id'] = jQuery("input[name='address[b][id]']:checked").val();
	// 		}
	// 	}

	// 	//start
	// 	if(jQuery("input[name='address[b][id]']:checked").val() == 0)
	// 	{
	// 		if(jQuery('input[name="sameasshipping"]:checked').val() != 'yes' || !jQuery('.pg-checkout-shipping-address-fields input[id="same-shiping-address"]').is(':checked') || jQuery("input[name='address[s][id]']:checked").val() > 0 )
	// 		{
	// 			var formBilValidated = true;
	// 			//changes start
	// 			var frmBillingCls;
	// 			if (jQuery('.billing_form').html() !="")
	// 			{
	// 			   frmBillingCls = '.billing_form #add_ship_form';
	// 			}
	// 			else
	// 			{
	// 			  frmBillingCls = '.pg-checkout-billing-address-fields #add_ship_form';
	// 			}
	// 			jQuery('input,select,textarea',frmBillingCls).each( function(index,el) {
	// 			// changes end
	// 			var checkBillValidation = (jQuery(this).hasClass('required')) && (jQuery(this).val() == '' || jQuery(this).val() == ' ');			if(checkBillValidation)
	// 				if(checkBillValidation)
	// 				{
	// 					jQuery( this ).addClass('highlight');
	// 					formBilValidated = false;
	// 				}
	// 				else
	// 				{
	// 					jQuery( this ).removeClass( 'highlight');

	// 					if( jQuery(this).attr('name') == 'address[b][telephoneno]')
	// 					{
	// 						 	var filter = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
	// 							if (filter.test(jQuery(this).val())) {}
	// 							else 
	// 							{
	// 								jQuery( this ).addClass('highlight');
	// 								formBilValidated = false;
	// 							}
	// 					}
	// 					if( jQuery(this).attr('name') == 'address[b][email]')
	// 					{
	// 						 	var filter = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
	// 							if (filter.test(jQuery(this).val())) {}
	// 							else
	// 							{
	// 								jQuery( this ).addClass('highlight');
	// 								formBilValidated = false;
	// 							}
	// 					}
	// 					if( jQuery(this).attr('name') == 'address[b][confirm_email]')
	// 					{
	// 						if (jQuery('#pg-email').val() != jQuery( this ).val())
	// 						{
	// 							jQuery( this ).addClass('highlight');
	// 							formValidated = false;
	// 						}
	// 					}
	// 				}
	// 			});

	// 			if(!formBilValidated)
	// 			{
	// 				return false;
	// 			}
	// 		}
	// 	}

	// 	//end

	// 	jQuery.ajax({
	// 		type: 'POST',
	// 		url: 'index.php',
	// 		async: false,
	// 		data: ({
	// 			option: "com_pago",
	// 			view: "checkout",
	// 			task : "set_address",
	// 			dataType: 'json',
	// 			guest: guest,
	// 			nextTask: 'shipping',
	// 			sameasshipping: sameasshipping,
	// 			address: address,
	// 			save_address: save_address,
	// 			call: 'ajax',
	// 		}),
	// 		success: function( response ) {
	// 			if(response){
	// 				result = JSON.parse(response);
	// 				if(result.status == "success")
	// 				{	
	// 					if(result.payment == "yes")
	// 					{
	// 						// For payment method
	// 						if(guest == 1)
	// 						{
	// 							jQuery('.pg-checkout-payment-methods').html(result.formHtml);
								
	// 							jQuery.validator.setDefaults
	// 							({
	// 										debug: true,
	// 										success: "valid"
	// 							});
	// 							jQuery("#pg-checkout-billing-payment-form").validate({
													
	// 									rules: {
											
	// 											"cc_cv2code": {
	// 												 required: true,
	// 												 minlength: 3,
	// 												 maxlength: 4,
	// 												number: true
	// 											}
	// 										},
	// 										messages: {
	// 											"cc_cv2code": 'Please Enter valid CVV Number.'	
	// 										},											 
	// 									submitHandler: function(form) {
	// 										form.submit();
	// 									}
	// 							});
								
	// 							jQuery('.pg-checkout-shipping-method').hide();
								
								
	// 							jQuery('.pg-checkout-register-guest').removeClass('open');
	// 							jQuery('.pg-checkout-register-guest .pg-checkout-content').slideUp('fast');

	// 							jQuery('.pg-checkout-billing').removeClass('open');
	// 							jQuery('.pg-checkout-billing .pg-checkout-content').slideUp('fast');

	// 							jQuery('.pg-checkout-shipping-method').removeClass('open');
	// 							jQuery('.pg-checkout-shipping-method .pg-checkout-content').slideUp('fast');

	// 							jQuery('.pg-checkout-payment-method').addClass('open');
	// 							jQuery('.pg-checkout-payment-method .pg-checkout-content').slideDown('fast');

	// 							jQuery('#pg-checkout .pg-checkout-register-guest').removeClass('hide-change');
	// 							jQuery('#pg-checkout .pg-checkout-billing').removeClass('hide-change');
	// 							jQuery('#pg-checkout .pg-checkout-shipping-method').removeClass('hide-change');
	// 						}
	// 						else
	// 						{
	// 							jQuery('.pg-checkout-payment-methods').html(result.formHtml);
	// 							jQuery('.pg-checkout-payment-method-panel').show();
	// 							jQuery('.pg-checkout-payment-method-content').show();
	// 							jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel .pg-checkout-shipping-method-content').slideUp('fast');
	// 							jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').removeClass('open');
	// 							jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child .pg-checkout-shipping-info-content').slideUp('fast');
	// 							jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').removeClass('open');

	// 							jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child .pg-checkout-shipping-info-content').slideUp('fast');
	// 							jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').removeClass('open');
								
	// 							jQuery.validator.setDefaults
	// 							({
	// 										debug: true,
	// 										success: "valid"
	// 							});
	// 							jQuery("#pg-checkout-billing-payment-form").validate({
	// 									rules: {
											
	// 											"cc_cv2code": {
	// 												 required: true,
	// 												 minlength: 3,
	// 												 maxlength: 4,
	// 												number: true
	// 											}
	// 										},
	// 										messages: {
	// 											"cc_cv2code": 'Please Enter valid CVV Number.'	
	// 										},
	// 									submitHandler: function(form) {
	// 										form.submit();
	// 									}
	// 							});
	// 						}
	// 						jQuery('#creditCardForm').css('display','none');
	// 					}
	// 					else
	// 					{
	// 						// For Shipping method
	// 						if(guest == 1)
	// 						{
	// 							jQuery('.pg-checkout-shipping-methods').html(result.formHtml);

	// 							jQuery('.pg-checkout-register-guest').removeClass('open');
	// 							jQuery('.pg-checkout-register-guest .pg-checkout-content').slideUp('fast');

	// 							jQuery('.pg-checkout-billing').removeClass('open');
	// 							jQuery('.pg-checkout-billing .pg-checkout-content').slideUp('fast');

	// 							jQuery('.pg-checkout-shipping-method').addClass('open');
	// 							jQuery('.pg-checkout-shipping-method .pg-checkout-content').slideDown('fast');

	// 							jQuery('#pg-checkout .pg-checkout-register-guest').removeClass('hide-change');
	// 							jQuery('#pg-checkout .pg-checkout-billing').removeClass('hide-change');

	// 						}
	// 						else
	// 						{
	// 							jQuery('.pg-checkout-shipping-methods').html(result.formHtml);
	// 							jQuery('.pg-checkout-shipping-method-panel').show();
	// 							jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel .pg-checkout-shipping-method-content').slideDown('fast');
	// 							jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').addClass('open');

	// 							jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child .pg-checkout-shipping-info-content').slideUp('fast');
	// 							jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').removeClass('open');

	// 							jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child .pg-checkout-shipping-info-content').slideUp('fast');
	// 							jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').removeClass('open');
	// 						}
	// 					}
	// 				}
	// 			}
	// 		}
	// 	});

	// })

	// jQuery(document).on('click','.pg-checkout-set-shipping-continue',function(){
	// 	var guest = jQuery('input[name=guest]').val();
		
	// 	if(jQuery('input[id=productBasedShipping]').val() == 1)
	// 	{
			
	// 		var counter = jQuery('#total_products_cart').val();
	// 		carrier_option = new Object();
				
	// 		for(i=0;i<counter;i++)
	// 		{
	// 			var selected = jQuery('input:radio[name=\"carrier_option['+i+']\"]:checked');
	// 			if (selected.length > 0) {
	// 			    selectedVal = selected.val();
	// 			   	carrier_option[i] = selectedVal;
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		var carrier_option = jQuery('input[name=carrier_option]:checked', '#pg-checkout-shipping-method-form').val()
		
	// 	}
		

	// 	jQuery.ajax({
	// 		type: 'POST',
	// 		url: 'index.php',
	// 		async: false,
	// 		data: ({
	// 			option: "com_pago",
	// 			view: "checkout",
	// 			task : "set_shipping_methods",
	// 			dataType: 'json',
	// 			guest : guest,
	// 			carrier_option: carrier_option,
	// 			call: 'ajax',
	// 		}),
	// 		success: function( response ) {
	// 			if(response){
	// 				result = JSON.parse(response);
	// 				if(result.status == "success")
	// 				{	// For Payment method
	// 					if(guest == 1)
	// 					{
	// 						jQuery('.pg-checkout-payment-methods').html(result.formHtml);
	// 						jQuery.validator.setDefaults
	// 						({
	// 									debug: true,
	// 									success: "valid"
	// 						});
					
	// 						jQuery("#pg-checkout-billing-payment-form").validate({
																				 
	// 								rules: {
											
	// 											"cc_cv2code": {
	// 												 required: true,
	// 												 minlength: 3,
	// 												 maxlength: 4,
	// 												number: true
	// 											}
	// 										},
	// 										messages: {
	// 											"cc_cv2code": 'Please Enter valid CVV Number.'	
	// 										},
	// 								submitHandler: function(form) {
	// 								// do other things for a valid form
	// 								form.submit();
	// 								}
	// 								});
							
	// 						jQuery('.pg-checkout-register-guest').removeClass('open');
	// 						jQuery('.pg-checkout-register-guest .pg-checkout-content').slideUp('fast');

	// 						jQuery('.pg-checkout-billing').removeClass('open');
	// 						jQuery('.pg-checkout-billing .pg-checkout-content').slideUp('fast');

	// 						jQuery('.pg-checkout-shipping-method').removeClass('open');
	// 						jQuery('.pg-checkout-shipping-method .pg-checkout-content').slideUp('fast');

	// 						jQuery('.pg-checkout-payment-method').addClass('open');
	// 						jQuery('.pg-checkout-payment-method .pg-checkout-content').slideDown('fast');

	// 						jQuery('#pg-checkout .pg-checkout-register-guest').removeClass('hide-change');
	// 						jQuery('#pg-checkout .pg-checkout-billing').removeClass('hide-change');
	// 						jQuery('#pg-checkout .pg-checkout-shipping-method').removeClass('hide-change');
	// 					}
	// 					else
	// 					{
	// 						jQuery('.pg-checkout-payment-methods').html(result.formHtml);
	// 						jQuery('.pg-checkout-payment-method-panel').show();
	// 						jQuery('.pg-checkout-payment-method-content').show();
	// 						jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel .pg-checkout-shipping-method-content').slideUp('fast');
	// 						jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').removeClass('open');
							
	// 						jQuery.validator.setDefaults
	// 						({
	// 									debug: true,
	// 									success: "valid"
	// 						});
					
	// 						jQuery("#pg-checkout-billing-payment-form").validate({
	// 								rules: {
											
	// 											"cc_cv2code": {
	// 												 required: true,
	// 												 minlength: 3,
	// 												 maxlength: 4,
	// 												number: true
	// 											}
	// 										},
	// 										messages: {
	// 											"cc_cv2code": 'Please Enter valid CVV Number.'	
	// 										},
	// 								submitHandler: function(form) {
	// 								// do other things for a valid form
	// 								form.submit();
	// 								}
	// 								});
							
							
	// 					}	
	// 					jQuery('#creditCardForm').css('display','none');	
	// 				}
	// 			}
	// 		}
	// 	});

	// 	jQuery.ajax({
	// 		type: 'POST',
	// 		url: 'index.php',
	// 		async: false,
	// 		dataType: 'json',
	// 		data: ({
	// 			option: "com_pago",
	// 			view: "checkout",
	// 			task : "shipping_formatt",
	// 		}),
	// 		success: function( response ) 
	// 		{
				
	// 			if(response)
	// 			{
	// 				jQuery('.shippingInCheckout').html(response['shipping']);
	// 				jQuery('.taxInCheckout').html(response['tax']);
	// 				jQuery('.totalInCheckout').html(response['total']);
	// 			}
	// 		}
	// 	});
	// })
	// jQuery(document).on('change','.country',function(){
	// 	jQuery(this).parents('form').find('select').each(function(){
	// 		jQuery(this).chosen({disable_search_threshold: 5});
	// 		jQuery(this).trigger("chosen:updated");
	// 	});
	// });
	// jQuery(document).on('change' ,'input[name="sameasshipping"]', function(event){
	// 	if(jQuery('input[name="sameasshipping"]:checked').val() != 'yes')
	// 	{
	// 		jQuery('#same_as_shipping_hidden').val(0);
	// 	}
	// 	else
	// 	{
	// 		jQuery('#same_as_shipping_hidden').val(1);
	// 	}
	// })
	// // checkout end
	// jQuery("#pago select").chosen({disable_search_threshold: 10});
	//var colorbox_orig_close = jQuery.colorbox.close;
	
	/*jQuery.colorbox.close = function() {
		if( last_quickview.length > 0 ) {
			jQuery(last_quickview).trigger('click');
		} else {
			colorbox_orig_close();
		}
	}*/
			
	// check_grid('#pg-items');
	// check_grid('#pg-cat-children')
	// check_grid('#pg-account-register-fields');
	// check_grid('#pg-checkout-shipping-info');
	// check_grid('#pg-checkout-billing-info');
	// check_grid('#pg-checkout-confirm');
	// check_grid('#pg-frontpage #pg-latest-items');
	
	/*jQuery('.pg-menu .pg-login-link').colorbox({
		inline:	true,
		href:	'#pg-form-login'
	});
	jQuery('#pg-items .pg-item-image').hover(function(e){
		jQuery(this).find('p').toggle();
	});
	jQuery('.pg-quickview').colorbox({
		onComplete: function() {
			var calling_obj = jQuery.fn.colorbox.element();

			thumbnail_rollover();
			gallery(calling_obj);
			
			last_quickview = '';
			jQuery.colorbox.resize();
		}
	});*/
	
	// if( jQuery('#pg-account').length > 0 ) {
	// 	checkUserFields('#pg-account-address-form', '#pg-account-address-billing');
	// }
	// if( jQuery('#pg-checkout').length > 0 ) {
	// 	checkUserFields('#pg-checkout-billing-form', '#pg-checkout-billing-fields');
	// }
	
	// if( typeof states !== 'undefined') {
	// 	jQuery('#m_countystate').add('#s_countystate').autocomplete({ source: states, autoFocus: true, autoSelect: true });

	// 	jQuery('#m_countystate').add('#s_countystate').on('blur', function(event) {
	// 		var autocomplete = jQuery(this).data('autocomplete');
	// 		var matcher = new RegExp("^" + jQuery.ui.autocomplete.escapeRegex( jQuery(this).val() ) + "$", "i");
	// 		var myInput = jQuery( this );
	// 		autocomplete.widget().children('.ui-menu-item').each(function() {
	// 			//Check if each autocomplete item is a case-insensitive match on the input
	// 			var item = jQuery(this).data('item.autocomplete');
	// 			if (matcher.test(item.label || item.value || item)) {
	// 				//There was a match, lets stop checking
	// 				autocomplete.selectedItem = item;
	// 				return;
	// 			}
	// 		});
	// 		//if there was a match trigger the select event on that match
	// 		//I would recommend matching the label to the input in the select event
	// 		if (autocomplete.selectedItem) {
	// 			autocomplete._trigger('select', event, {
	// 				item: autocomplete.selectedItem
	// 			});
	// 		//there was no match, clear the input
	// 		} else {
	// 			jQuery(this).val('').valid();
	// 		}
	// 	});
	// }

	// Validation on whether or not to require toc with the xpress checkout.
	// jQuery.validator.addMethod('toc', function(value, element) {
	// 	if( jQuery('#pg-checkout-express-terms').length > 0 ) 
	// 		return !this.optional(element);
	// }, 'You must agree to the Terms &amp; Conditions' );
	
	// // If the input element has a placeholder class, using a browser that doesn't support
	// // placeholder, so javascript is used to emulate using a placeholder inside the (value)
	// // attribute, so it would pass standard validation; this should prevent it from being valid
	// jQuery.validator.addMethod('pg-placeholder', function(value, element) {
	// 	if ( jQuery(element).hasClass('pg-placeholder') ) {
	// 		value = '';
	// 	}
	// 	// TODO: Someone should test it
	// 	// return !this.optional(element), value;
	// 	return !this.optional(element);
	// }, 'This field is required')

	// function checkUserFields(form, fields_wrapper) {
	// 	if(jQuery('input[name="sameasshipping"]:checked').val() == 'no') {
	// 		activate_form(fields_wrapper);
	// 		//if(billing === true) {
	// 			jQuery(fields_wrapper).show();
	// 			jQuery(form).validate({
	// 				errorClass: 'pg-error',
	// 				validClass: 'valid',
	// 				errorPlacement: function(error, element) {
	// 					validationQtip(error, element, ['right center', 'left center'])
	// 				},
	// 				success: jQuery.noop
	// 			});
	// 		//}
				
	// 	} else {
	// 		jQuery('input, select').each(function(index, element) {
	// 			if( !jQuery(element).hasClass('pg-error') ) {
	// 				jQuery(element).qtip('destroy');
	// 			}
	// 		});
	// 		//if(billing === true) {
	// 			jQuery(fields_wrapper).hide();
	// 		//} else {
	// 			jQuery(form).validate({
	// 				errorClass: 'pg-error',
	// 				validClass: 'valid',
	// 				errorPlacement: function(error, element) {
	// 					validationQtip(error, element, ['right center', 'left center'])
	// 				},
	// 				success: jQuery.noop
	// 			});
	// 		//}
				
	// 		deactivate_form(fields_wrapper);
	// 	}
	// 	// jQuery('input[name="sameasshipping"]').change(function() {
	// 	// 	if( jQuery(this).val() == 'no') {
	// 	// 		activate_form(fields_wrapper);
	// 	// 		//if(form == '#pg-checkout-billing-form' || '#pg-acccount-')
	// 	// 			jQuery(fields_wrapper).slideDown();
					
	// 	// 	} else {
	// 	// 		jQuery(form).validate().resetForm();
	// 	// 		jQuery('input, select').each(function(index, element) {
	// 	// 			if( !jQuery(element).hasClass('pg-error') ) {
	// 	// 				jQuery(element).qtip('destroy');
	// 	// 			}
	// 	// 		});
	// 	// 		jQuery(fields_wrapper).slideUp();
					
	// 	// 		deactivate_form(fields_wrapper);
	// 	// 	}
	// 	// });
	// }
	

	// jQuery(document).on('click', '.pg-account-shipping-details .pg-checkout-continue', function(){
	// 	jQuery('#pg-checkout-express-checkout-form').validate().resetForm();
	// 	jQuery('#pg-checkout-express-checkout-form input').each(function(index, element) {
	// 		if( !jQuery(element).hasClass('error') ) {
	// 			jQuery(element).qtip('destroy');
	// 		}
	// 	});
	// })

	// jQuery(document).on('click', '#pg-checkout-express-button, #pg-checkout-express-checkout-form button', function(){
	// 	jQuery('#pg-checkout-express-checkout-form').submit();
	// 	jQuery('#pg-checkout-shipping-form').validate().resetForm();
	// 	jQuery('#pg-checkout-shipping-form input, #pg-checkout-shipping-form select').each(function(index, element) {
	// 		if( !jQuery(element).hasClass('error') ) {
	// 			jQuery(element).qtip('destroy');
	// 		}
	// 	});
	// });

	// jQuery('#pg-checkout-shipping-form').validate({
	// 	rules: {
	// 		"address[s][confirm_email]": {
	// 			equalTo: '#pg-email'
	// 		}
	// 	},
	// 	messages: {
	// 		"address[s][id]": 'Please select an address or enter a new address.'	
	// 	},
	// 	errorClass: 'error',
	// 	validClass: 'valid',
	// 	errorPlacement: function(error, element) {
	// 		if( element.is(':radio') )
	// 			var target = element.closest('.pg-checkout-shipping-details');
	// 		else
	// 			var target = element;

	// 		validationQtip(error, element, ['right center', 'left center'], target);
	// 	},
	// 	success: jQuery.noop
	// });

	// jQuery('#pg-checkout-shipping-method-form').validate({
	// 	rules: {
	// 		shipping_address: 'required',
	// 		carriet_option: 'required'
	// 	},
	// 	messages: {
	// 		shipping_address: "Please select an address to ship to.",
	// 		carrier_option: "Please select a shipping method."
	// 	},
	// 	errorClass: 'error',
	// 	validClass: 'valid',
	// 	errorPlacement: function(error, element) {
	// 		var target = element.closest('.pg-checkout-shipping');
	// 		validationQtip(error, element, ['right center', 'left center'], target);
	// 	},
	// 	success: jQuery.noop
	// });

	// jQuery(document).on('click', '#pg-grid-view', function(){
	// 	jQuery(this).addClass('active');
	// 	jQuery('#pg-list-view').removeClass('active');
	// 	jQuery('.item-list').addClass('item-grid').removeClass('item-list');
	// 	window.name = 'grid'
	// });
	// jQuery(document).on('click', '#pg-list-view', function(){
	// 	jQuery(this).addClass('active');
	// 	jQuery('#pg-grid-view').removeClass('active');
	// 	jQuery('.item-grid').addClass('item-list').removeClass('item-grid');
	// 	window.name = 'list'
	// });

	// if( location.hash.substr(1) == 'list' || window.name == 'list' ) {
	// 	jQuery('#pg-list-view').trigger('click');
	// } else if( location.hash.substr(1) == 'grid' || window.name == 'grid' ) {
	// 	jQuery('#pg-grid-view').trigger('click');
	// }

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
	// if( jQuery('#pg-checkout-shipping-form input[type="radio"]').length > 1 ) {
	// 	if( !jQuery('#pg-shipping-address-add').is(':checked') ) {
	// 		jQuery('.pg-checkout-shipping-address-fields').hide();
	// 		deactivate_form( '.pg-checkout-shipping-address-fields' );
	// 	}
	// }

	// jQuery(document).on('change', '#pg-checkout-shipping-form input[type="radio"]', function(){
	// 	if( jQuery('#pg-shipping-address-add').is(':checked') ) {
	// 		activate_form( '.pg-checkout-shipping-address-fields' );
	// 		jQuery('.pg-checkout-shipping-address-fields').slideDown();
	// 		jQuery('#pg-checkout-shipping-continue').css('display', 'none');
	// 	} else {
	// 		jQuery('.pg-checkout-shipping-address-fields').slideUp();
	// 		deactivate_form( '.pg-checkout-shipping-address-fields' );
	// 		jQuery('#pg-checkout-shipping-continue').css('display', 'block');
	// 	}
	// })

	// if( jQuery('#pg-checkout-billing-form input[type="radio"]').length > 1 ) {
	// 	if( !jQuery('#pg-billing-address-add').is(':checked') ) {
	// 		jQuery('.pg-checkout-billing-address-fields').hide();
	// 		deactivate_form( '.pg-checkout-billing-address-fields' );
	// 	}
	// }

	// jQuery(document).on('change', '#pg-checkout-billing-form input[type="radio"]', function(){
	// 	if( jQuery('#pg-billing-address-add').is(':checked') ) {
	// 		activate_form( '.pg-checkout-billing-address-fields' );
	// 		jQuery('.pg-checkout-billing-address-fields').slideDown();
	// 	} else {
	// 		jQuery('.pg-checkout-billing-address-fields').slideUp();
	// 		deactivate_form( '.pg-checkout-billing-address-fields' );
	// 	}
	// })
	
		// shipping estimation on cart page
	// jQuery(document).on('click', '.pg-cart-shipping-estimation-button', function(event){

	// 	event.preventDefault();
	// 	zip = jQuery('#pg-zip-code').val();
	// 	jQuery.ajax({
	// 			type: 'POST',
	// 			url: 'index.php',
	// 			async: false,
	// 			data: ({
	// 				option: "com_pago",
	// 				view: "cart",
	// 				task : "shippingEstimation",
	// 				dataType: 'json',
	// 				zip: zip,
	// 			}),
	// 			success: function( response ) {
	// 				if(response){
	// 					result = JSON.parse(response);
	// 					if(result.status == "success")
	// 					{	
	// 						jQuery('#pg-cart-shipping-estimation-content').html(result.formHtml);
	// 					}
	// 				}
	// 			}
	// 		});
	// });

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
	                jQuery('.pg-item-images-con > img').stop(true, false).animate({opacity:1},500);
	                
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
	//Additional Images Click end
	/*thumbnail_rollover();
	gallery();	*/
	//jQuery('input[placeholder]').formHelper({task: 'placeholder'});

	//rating

	// jQuery(document).on('click', '.loginForRate', function(){
	// 	jQuery('#login-modal').modal();
	// })

	// jQuery(document).on('mouseover', '#pago .pg-product-rate ul li', function(){
		
	// 	jQuery(this).parent().find('li').removeClass('active');

	// 	var index = jQuery(this).index()+1;
	// 	var removeIndex = jQuery(this).index()+1;
	// 	while(index > 0){
	// 		jQuery(this).parent().find('li:nth-child('+index+')').addClass('active');
	// 		index--;
	// 	}
	// 	jQuery.each( jQuery(this).parent().find('li'), function( i, val ) {
	// 		if(!jQuery(this).hasClass('active')){
	// 			jQuery(this).addClass('not_active');	
	// 		}
	// 	});
	// })

	// jQuery(document).on('mouseleave', '#pago .pg-product-rate ul li', function(){
	// 	jQuery(this).parent().find('li').removeClass('active');
	// 	jQuery(this).parent().find('li').removeClass('not_active');
	// })

	// jQuery(document).on('click', '#pago .pg-product-rate ul li a', function(){
	// 	if (jQuery(this).parent().parent().hasClass('rated')){
	// 		return;
	// 	}
	// 	itemId = jQuery(this).parent().parent().parent().attr('item_id');
	// 	rating = jQuery(this).attr('rating');
	// 	obj = jQuery(this);
	// 	jQuery.ajax({
	//         type: "POST",
	//         url: 'index.php',
	// 		data: 'option=com_pago&view=account&task=rate&rating='+rating+'&itemId=' + itemId+ '&async=1',
	//         success: function(response){ 
 //   				if(response){
	// 				var result = JSON.parse(response);
	// 				if(result.status == 0){//user not logged in
	// 					obj.parents('.pg-product-rate').find('.pg-product-rate-result').fadeIn('fast');
	// 					obj.parents('.pg-product-rate').find('.pg-product-rate-result').html('<a class="loginForRate" href="javascript:void();">'+result.message+'</a>');

	// 					// setTimeout(function(){
	// 					// 	obj.parents('.pg-product-rate').find('.pg-product-rate-result').fadeOut('fast');
	// 					// 	obj.parents('.pg-product-rate').find('.pg-product-rate-result').html('');
	// 					// }, 2000);
	// 				}
	// 				if(result.status == 1){//user voted
	// 					obj.parents('.pg-product-rate').find('.pg-product-rate-result').fadeIn('fast');
	// 					obj.parents('.pg-product-rate').find('.pg-product-rate-result').html(result.message);

	// 					setTimeout(function(){
	// 						obj.parents('.pg-product-rate').find('.pg-product-rate-result').fadeOut('fast');
	// 						obj.parents('.pg-product-rate').find('.pg-product-rate-result').html('');
	// 					}, 2000);
	// 				}
	// 				if(result.status == 2){//thanks for vote
	// 					obj.parent().parent().find('li').removeClass('rated_star').each(function(){
	// 						if(jQuery(this).index()+1 <= result.rate ){
	// 							jQuery(this).addClass('rated_star');
	// 						}
	// 					})
	// 					obj.parent().parent().addClass('rated');
	// 					obj.parents('.pg-product-rate').find('.pg-product-rate-result').fadeIn('fast');
	// 					obj.parents('.pg-product-rate').find('.pg-product-rate-result').html(result.message);

	// 					setTimeout(function(){
	// 						obj.parents('.pg-product-rate').find('.pg-product-rate-result').fadeOut('fast');
	// 						obj.parents('.pg-product-rate').find('.pg-product-rate-result').html('');
	// 					}, 2000);
	// 				}
	// 			}
	//         }
	//     });
		
	// })
	////
	jQuery(document).on('click', '#pago .pg-product-downloads-block > a', function(){
		if(jQuery(this).parent().hasClass('open')){
			jQuery(this).parent().removeClass('open');
		}
		else{
			jQuery(this).parent().addClass('open');	
		}
		jQuery(this).siblings(".pg-product-downloads" ).slideToggle( "fast");
	})

	// jQuery('#pago #pg-category-view .product-cell .pg-category-product-image-block img:first-child').addClass('active-image');
	// jQuery(document).on('mouseenter', '#pago #pg-category-view .product-cell', function(){
	// 	var obj = jQuery(this);
	// 	var length = obj.find('.pg-category-product-image-block img').length;
	// 	if (length > 1){
	// 		var index = obj.find('.pg-category-product-image-block img.active-image').index()+1;
	// 		obj.attr('rel','1');
	// 		changeImage(index,length,obj);
	// 	}
	// })

	function changeImage(index,length,obj){
		setTimeout(function(){
			nextind = index+1;
			if (index == length) nextind = 1;
			obj.find('.pg-category-product-image-block img:nth-child('+index+')').animate({'opacity':0},300).removeClass('active-image');
			obj.find('.pg-category-product-image-block img:nth-child('+(nextind)+')').animate({'opacity':0.99},300).addClass('active-image');
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

    // jQuery(document).on('change', '.pg-checkout-shipping-details input[type="radio"]', function(){
    // 	jQuery('.pg-checkout-shipping-details input[type="radio"].active').removeClass('active');
    // 	jQuery(this).addClass('active');

    // 	if (jQuery(this).val() == 0){
    // 		jQuery('#pg-checkout-shipping-form button').css('display', 'none');
    // 	}
    // 	else{
    // 		jQuery('#pg-checkout-shipping-form button').css('display', 'block');	
    // 	}
    // })


    // jQuery(document).on('change', '.pg_checkout_guest_forum input[type="radio"]', function(){
    // 	jQuery('.pg_checkout_guest_forum input[type="radio"].active').removeClass('active');
    // 	jQuery(this).addClass('active');

    // 	if (jQuery(this).val() == 'guest'){
    // 		jQuery('.pg-checkout-panel.pg-checkout-register').slideUp('fast');
    // 	}
    // 	else{
    // 		jQuery('.pg-checkout-panel.pg-checkout-register').slideDown('fast');	
    // 	}
    // })

    // jQuery(document).on('click', '.contact_info_modal_close', function(){
    // 	jQuery('#contact_info_modal').modal('hide');
    // })    

    // jQuery(document).on('change', '.pg-checkout-shipping-addresses select', function(){
    // 	var index = jQuery('.pg-checkout-shipping-addresses select option:selected').index();
    // 	var addrType = jQuery('.pg-checkout-shipping-addresses select option:selected').attr("addr_type");
    	
    // 	jQuery('#pago #pg-account #pg-account-dashboard .shipping-addresses-list > div').stop(true, false).fadeOut('fast');
    // 	jQuery('#pago #pg-account #pg-account-dashboard .shipping-addresses-list > div:nth-child('+(index+1)+')').stop(true, false).fadeIn('fast');
    // 	if(addrType=="s"){
    // 		window.location.replace(shippingUrl);
    // 	}
    // })

    // jQuery(document).on('change', '.pg-checkout-billing-addresses select', function(){
    // 	var index = jQuery('.pg-checkout-billing-addresses select option:selected').index();
    // 	var addrType = jQuery('.pg-checkout-billing-addresses select option:selected').attr("addr_type");
    	
    // 	jQuery('#pago #pg-account #pg-account-dashboard .billing-addresses-list > div').stop(true, false).fadeOut('fast');
    // 	jQuery('#pago #pg-account #pg-account-dashboard .billing-addresses-list > div:nth-child('+(index+1)+')').stop(true, false).fadeIn('fast');

    // 	if(addrType=="b"){
    // 		window.location.replace(billingUrl);
    // 	}
    // })
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

	// jQuery(document).on('click', '#pg-checkout .pg-checkout-shipping-method-heading-change', function(){
	// 	var obj = jQuery(this).parent().parent();
	// 	if (obj.hasClass('pg-checkout-options')){
	// 		alert("In");
	// 		jQuery('.pg-checkout-panel-group .pg-checkout-panel.open .pg-checkout-content').slideUp('fast');
	// 		jQuery('.pg-checkout-panel-group .pg-checkout-panel.open').removeClass('open');

	// 		jQuery('.pg-checkout-billing').slideUp('fast');

	// 		jQuery('.pg-checkout-register-guest .pg_user_guest').html('');
	// 		jQuery('.pg-checkout-register .pg_user_register').html('');
	// 		jQuery('.pg-checkout-billing .billing_form').html('');

	// 		jQuery('.pg-checkout-panel-group .pg-checkout-panel').addClass('hide-change');

	// 		obj.addClass('open');
	// 		obj.find('.pg-checkout-content').slideDown('fast');
	// 	}
	// 	else{
	// 		jQuery('.pg-checkout-panel-group .pg-checkout-panel.open .pg-checkout-content').slideUp('fast');
	// 		jQuery('.pg-checkout-panel-group .pg-checkout-panel.open').removeClass('hide-change');
	// 		jQuery('.pg-checkout-panel-group .pg-checkout-panel.open').removeClass('open');

	// 		jQuery('.pg-checkout-panel-group .pg-checkout-panel:not(".pg-checkout-panel")').addClass('hide-change');

	// 		obj.addClass('open');
	// 		obj.find('.pg-checkout-content').slideDown('fast');
	// 	}
	// })

	// jQuery(document).on('click', '#pg-checkout .pg-checkout-shipping-info-heading-change', function(){
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child .pg-checkout-shipping-info-content').slideUp('fast');
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').removeClass('open');

	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').addClass('open');
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').find('.pg-checkout-shipping-info-content').slideDown('fast');
		
	// 	jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').removeClass('open');
	// 	jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').find('.pg-checkout-shipping-method-content').slideUp('fast');
		
	// 	jQuery('#pg-checkout-payment-method .pg-checkout-payment-method-panel').removeClass('open');
	// 	jQuery('#pg-checkout-payment-method .pg-checkout-payment-method-panel').find('.pg-checkout-payment-method-content').slideUp('fast');
		
	// 	jQuery('#same_as_shipping_hidden').val(1);

	// })

	// jQuery(document).on('click', '#pg-checkout .pg-checkout-billing-info-heading-change', function(){
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child .pg-checkout-shipping-info-content').slideDown('fast');
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').addClass('open');

	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').removeClass('open');
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').find('.pg-checkout-shipping-info-content').slideUp('fast');
		
	// 	jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').removeClass('open');
	// 	jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').find('.pg-checkout-shipping-method-content').slideUp('fast');
		
	// 	jQuery('#pg-checkout-payment-method .pg-checkout-payment-method-panel').removeClass('open');
	// 	jQuery('#pg-checkout-payment-method .pg-checkout-payment-method-panel').find('.pg-checkout-payment-method-content').slideUp('fast');
		
	// 	jQuery('#same_as_shipping_hidden').val(1);
		
	// })

	// jQuery(document).on('click', '#pg-checkout .pg-checkout-shipping-method-heading-change', function(){
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child .pg-checkout-shipping-info-content').slideUp('fast');
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').removeClass('open');

	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').removeClass('open');
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').find('.pg-checkout-shipping-info-content').slideUp('fast');

	// 	jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').addClass('open');
	// 	jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').find('.pg-checkout-shipping-method-content').slideDown('fast');

	// 	jQuery('#pg-checkout-payment-method .pg-checkout-payment-method-panel').removeClass('open');
	// 	jQuery('#pg-checkout-payment-method .pg-checkout-payment-method-panel').find('.pg-checkout-payment-method-content').slideUp('fast');

	// })

	// jQuery(document).on('click', '#pg-checkout .pg-checkout-payment-method-heading-change', function(){
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child .pg-checkout-shipping-info-content').slideUp('fast');
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:last-child').removeClass('open');

	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').removeClass('open');
	// 	jQuery('#pg-checkout-shipping-info .pg-checkout-shipping-info-panel:first-child').find('.pg-checkout-shipping-info-content').slideUp('fast');

	// 	jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').removeClass('open');
	// 	jQuery('#pg-checkout-shipping-method .pg-checkout-shipping-method-panel').find('.pg-checkout-shipping-method-content').slideDown('fast');

	// 	jQuery('#pg-checkout-payment-method .pg-checkout-payment-method-panel').addClass('open');
	// 	jQuery('#pg-checkout-payment-method .pg-checkout-payment-method-panel').find('.pg-checkout-payment-method-content').slideUp('fast');
	// })
})

jQuery(window).resize(function(){
	// jQuery('.pg-reply-comment-container').removeClass('open');
	// jQuery('.pg-reply-comment-container').slideUp();

	// jQuery('#pg-category-view #pg-products .pg-category-product-image-block').css('height', 'auto');
 //    jQuery('#pg-category-view #pg-products .pg-category-product-short-desc').css('height', 'auto');
 //    jQuery('#pg-category-view #pg-products .pg-category-product-long-desc').css('height', 'auto');
 //    jQuery('#pg-category-view #pg-products .pg-category-product-addtocart').css('height', 'auto');

 //    var imageMarginTop = parseInt(jQuery('#pg-category-view #pg-products .pg-category-product-image-block').css('margin-top'));
 //    var imageMarginBottom = parseInt(jQuery('#pg-category-view #pg-products .pg-category-product-image-block').css('margin-bottom'));

 //    var maxImageHeight = parseInt(jQuery('#pg-category-view #pg-products > .row > div:first-child .pg-category-product-image-block').height())+imageMarginTop+imageMarginBottom;
 //    var maxShortDescHeight = jQuery('#pg-category-view #pg-products > .row > div:first-child .pg-category-product-short-desc').height();
 //    var maxLongDescHeight = jQuery('#pg-category-view #pg-products > .row > div:first-child .pg-category-product-long-desc').height();
 //    var maxAddToCartHeight = jQuery('#pg-category-view #pg-products > .row > div:first-child .pg-category-product-addtocart').height();

 //    jQuery('#pg-category-view #pg-products > .row > div').each(function(){
 //    	if (jQuery(this).find('.pg-category-product-image-block').height()+imageMarginTop+imageMarginBottom > maxImageHeight)
 //    		maxImageHeight = parseInt(jQuery(this).find('.pg-category-product-image-block').height())+imageMarginTop+imageMarginBottom;
 //    	if (jQuery(this).find('.pg-category-product-short-desc').height() > maxShortDescHeight)
 //    		maxShortDescHeight = jQuery(this).find('.pg-category-product-short-desc').height();
 //    	if (jQuery(this).find('.pg-category-product-long-desc').height() > maxLongDescHeight)
 //    		maxLongDescHeight = jQuery(this).find('.pg-category-product-long-desc').height();
 //    	if (jQuery(this).find('.pg-category-product-addtocart').height() > maxAddToCartHeight)
 //    		maxAddToCartHeight = jQuery(this).find('.pg-category-product-addtocart').height();
 //    })

    // jQuery('#pg-category-view #pg-products > .row > div').each(function(){
    // 	if (maxImageHeight > 0){
    // 		jQuery('#pg-category-view #pg-products .pg-category-product-image-container').css('height', maxImageHeight);
    // 	}
    // 	if (maxShortDescHeight > 0){
    // 		jQuery('#pg-category-view #pg-products .pg-category-product-short-desc-container').css('height', maxShortDescHeight);
    // 	}
    // 	if (maxLongDescHeight > 0){
    // 		jQuery('#pg-category-view #pg-products .pg-category-product-long-desc-container').css('height', maxLongDescHeight);
    // 	}
    // 	if (maxAddToCartHeight > 0){
    // 		jQuery('#pg-category-view #pg-products .pg-category-product-addtocart').css('height', maxAddToCartHeight);
    // 	}
    // })	
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
	jQuery('.pg-item-images-con > img').stop(true, false).animate({opacity:0},500);
	jQuery('.pg-item-images-con').removeClass('loading');
}
function startChangeAttribute(){
	jQuery('#pg-item-details').css('opacity', 0);
	jQuery('#pg-item-details').addClass('loading');	
}
function finishChangeAttribute(){
	//considerPrice();
	jQuery('#pg-item-details').stop(true, false).animate({opacity:1},500);            
    jQuery('#pg-item-details').removeClass('loading');
}
function finishChangeAttributeWPrice(){
	jQuery('#pg-item-details').stop(true, false).animate({opacity:1},500);            
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
// function check_grid(element) {
// 	if(jQuery(element).length > 0) {
// 		grid_resize(element);
// 		jQuery(window).resize( function() {
// 			grid_resize(element);
// 		});
// 	}
// }

// function grid_resize(element) {
// 	if( jQuery(element).hasClass('grid-4') ) {
// 		if( jQuery('#pago').width() < 665 ) {
// 			jQuery(element).removeClass('grid-4').addClass('grid-3');
// 		}
// 	} else if( jQuery(element).hasClass('grid-3') ) {
// 		if( jQuery('#pago').width() > 665 ) {
// 			jQuery(element).removeClass('grid-3').addClass('grid-4');
// 		}
// 	} else if (jQuery(element).hasClass('grid-2') && jQuery('#pago').width() < 695 ) {
// 		jQuery(element).removeClass('grid-2');
// 	} else if (!jQuery(element).hasClass('grid-2') && jQuery('#pago').width() > 695 ) {
// 		jQuery(element).addClass('grid-2');
// 	}
// }
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
/*function gallery(quickview) {
	jQuery('#pg-item-images a').colorbox({
		onComplete: function() {
			if(quickview)
				last_quickview = quickview;
				
			var calling_obj = jQuery.fn.colorbox.element();
			var calling_img_id = jQuery(calling_obj).children('img').attr('id');
			var img_id = calling_img_id.split('-');

			jQuery('.pg-gallery-thumbs ul li img').click(function(){
				el = jQuery(this).attr('rel');
				el_title = jQuery(this).attr('title');
				el_alt = jQuery(this).attr('alt');
				jQuery(this).parent('li').addClass('active').siblings('li').removeClass('active');
				
				jQuery('.pg-gallery-img').attr({
					src: el,
					title: el_title,
					alt: el_alt
				});
			});
			
			jQuery('#pg-thumbimageid-' + img_id[2]).trigger('click');			
		}
	});
}*/

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

// function checkLetter(input) {
//     var inp = jQuery(input).val();
//     jQuery(input).val(inp.replace(/[^\d]/g, ''));
// };

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
										        jQuery(parent+'.pg-item-images-con > img').stop(true, false).animate({opacity:1},500);
										        
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
											        jQuery(parent+'.pg-item-images-con > img').stop(true, false).animate({opacity:1},500);
											        
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
							        jQuery(parent+'.pg-item-images-con > img').stop(true, false).animate({opacity:1},500);
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
// function attribueInVaration(item_varations,selected_attributes){
// 	item_varations = JSON.parse(item_varations);

// 	if(item_varations){
// 		for (key in item_varations) {	
// 			var varationAttributes = item_varations[key].attributes;
// 			for (ak in varationAttributes){
// 				if(typeof varationAttributes[ak] == 'function') continue;

// 				for (saval in selected_attributes){
					
// 					var vAttrId = varationAttributes[ak].attribute.id; // var attribute ID
// 					var vOptionId = varationAttributes[ak].option.id; // var attribute option ID

// 					var saAttrId = saval; // selected attribute ID
// 					var saOptionId = selected_attributes[saval]; // selected attribute option ID

					
// 					if(saAttrId == vAttrId && saOptionId == vOptionId){
// 						return false;
// 					}
// 				}
				
// 			}
// 		}
// 	}
// 	return true;
// }
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
// function pull_paymentErrorForm() 
// {
// 	jQuery.ajax({
// 		url:'index.php?option=com_pago&view=contact_info&tmpl=component&task=get_payment_form',
// 		async:true,
// 		dataType: 'text'
// 	}).done(function(response){
// 		if(response)
// 		{
// 			result = JSON.parse(response);
// 			if(result.status == "success")
// 			{
// 				jQuery('#contact_info_modal .modal-body').html(result.formHtml);
// 			}
// 		}
// 	})
// }

// function pull_upload_contctInfo(cid,id) 
// {
// 	jQuery.ajax({
// 		url:'index.php?option=com_pago&view=contact_info&id='+id+'&cid='+cid+'&tmpl=component&task=get_contact_form',
// 		async:true,
// 		dataType: 'text'
// 	}).done(function(response){
// 		if(response)
// 		{
// 			result = JSON.parse(response);
// 			if(result.status == "success")
// 			{
// 				jQuery('#contact_info_modal .modal-body').html(result.formHtml);
// 			}
// 		}
// 	})
// }
// jQuery(document).on('click','#contact_info_modal .pg-contact-submit',function(e){
// 	e.preventDefault();
// 	var form = jQuery(this).parents('form');
// 	var inputs = new Object();
// 	var formContactValidated = true;
	
// 	form.find('input').each(function(){
// 		inputs[jQuery(this).attr('name')] = jQuery(this).val();
// 		jQuery( this ).removeClass( 'highlight');
		
// 		if((jQuery(this).hasClass('required')) && (jQuery(this).val() == ''))
// 		{
// 			jQuery( this ).addClass('highlight');
// 			formContactValidated = false;
// 		}
// 	});
// 	inputs['your_message'] = jQuery('textarea#your_message').val();
// 	jQuery('textarea#your_message').removeClass('highlight');
	
// 	if(jQuery('textarea#your_message').val() == '')
// 	{
// 		jQuery('textarea#your_message').addClass('highlight');
// 		formContactValidated = false;
// 	}
// 	if(!formContactValidated)
// 	{
// 		return false;
// 	}
// 	jQuery.ajax({
// 		url: form.attr('action'),
// 		data: inputs
// 	}).done(function(response){
// 		jQuery('#contact_info_modal .contact-messages').html(response);
// 		jQuery('#contact_info_modal .modal-body #contact-form').hide();
		
// 	});
// });
// function getComments($itemID){
// 	//$itemID = jQuery('.product-container').attr('itemid');

// 	lastShowCommentid = jQuery('.pg-comments-con .pg-show-comment:last-child').attr('id');
// 	if(typeof lastShowCommentid === "undefined" || !lastShowCommentid.length){
// 		lastShowCommentid = false;	
// 	}else{
// 		lastShowCommentid = lastShowCommentid.substring(8);	
// 	}
// 	jQuery.ajax({
// 		type: 'POST',
// 		url: 'index.php',
// 		async: false,
// 		data: ({
// 			option: "com_pago",
// 			view: "comments",
// 			task : "getComments",
// 			dataType: 'json',
// 			itemId: $itemID,
// 			lastShowCommentid: lastShowCommentid,
// 		}),
// 		success: function( response ) {
// 			if(response){
// 				result = JSON.parse(response);
// 				if(result.status == "success")
// 				{	
// 					jQuery('.pg-comments-con').append(result.commentsHtml);
// 				}
// 			}
// 		}
// 	});
// }

jQuery(document).ready(function(){
    jQuery('input#pg-zip-code').keypress(function(event){
        if (event.which == 13) {
			jQuery("#pg-cart-shipping-estimation-button").click();
            event.preventDefault();
            return false;   
        }
    });
});
// jQuery(document).on('click','.pg-payment-method', function(){
// 	var obj = jQuery(this);
	
// 	jQuery('.pg-payment-method').removeClass('checked').addClass('unchecked');
// 	var check = obj.find('input[type="radio"]');
// 	if(check.is(':checked')){	
// 	obj.removeClass('unchecked').addClass('checked');

// 			// $(that).removeClass('unchecked').addClass('checked');
// 		}
// 		else{
// 			obj.removeClass('checked').addClass('unchecked');
// 			// $(that).removeClass('checked').addClass('unchecked');
// 		}

// 	// obj.removeClass('unchecked');
// 	// obj.addClass('checked');

// 	var value =check.val();
// 	var guest = jQuery('input[name="guest"]').attr("value");
// 	jQuery.ajax({
//         type: "POST",
//         url: 'index.php',
// 		data: 'option=com_pago&view=checkout&task=checkCreditCard&payment=' + value+ '&async=1&guest='+guest,
//         success: function(response){
//         	if(response == 1){
//         		jQuery(".pg-checkout-continue").removeClass('cancel');
//         		jQuery( "#creditCardForm" ).show();
//         	}else{
//         		jQuery(".pg-checkout-continue").addClass('cancel');
//         		jQuery( "#creditCardForm" ).hide();
//         	}
//         }
//     });
// });

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

// shipping estimation on cart page
	// jQuery(document).on('click', '.pag-cart-shipping-estimation-button', function(event){

	// 	event.preventDefault();
	// 	zip = jQuery('#pg-zip-code').val();
	// 	jQuery.ajax({
	// 			type: 'POST',
	// 			url: 'index.php',
	// 			async: false,
	// 			data: ({
	// 				option: "com_pago",
	// 				view: "cart",
	// 				task : "shippingEstimation",
	// 				dataType: 'json',
	// 				zip: zip,
	// 			}),
	// 			success: function( response ) {
	// 				if(response){
	// 					result = JSON.parse(response);
	// 					if(result.status == "success")
	// 					{	
	// 						jQuery('#pg-cart-shipping-estimation-content').html(result.formHtml);
	// 					}
	// 				}
	// 			}
	// 		});
	// });

// jQuery(document).on('change' ,'input[name="sameasshipping"]', function(event){
// 	if(jQuery('input[name="sameasshipping"]:checked').val() != 'yes'){
// 		activate_form('#pg-account-address-billing');
// 		jQuery('#pg-account-address-billing').slideDown('fast');
// 	}
// 	else{
// 		deactivate_form('#pg-account-address-billing');
// 		jQuery('#pg-account-address-billing').slideUp('fast');	
// 	}	
// });
// jQuery(document).ready(function(){
// 	var pgFormContainer = jQuery('.pg-form-container').outerWidth();
// 	var pgFormContainerInput = jQuery('.pg-form-container').find('input').outerWidth();	
// 	var cont_width = (pgFormContainer - pgFormContainerInput)/2;
// 	if(cont_width <= 185){
// 		errorMessage  = "pg-hidden_error_message";
// 	}

// 	jQuery('.pg-register-button, .add_save_btn').on('click', function(){
// 		var parent = jQuery(this).parents('.pg-form-container');
// 		var cont_width = (jQuery(parent).outerWidth() - jQuery(parent).find('input').outerWidth())/2;
// 		var errorWidth = jQuery('.pg-error-left').outerWidth();
		
// 		if(cont_width <= errorWidth){	
// 			jQuery(parent).find('.pg-error-left').addClass('pg-hidden_error_message');		
// 		}
// 		else{
// 			jQuery(parent).find('.pg-error-left').removeClass('pg-hidden_error_message');
// 		}
// 	});

// 	jQuery(window).on('resize', function(){
// 		var parent = jQuery('.pg-error-left').parents('.pg-form-container');
// 		var cont_width = (jQuery(parent).outerWidth() - jQuery(parent).find('input').outerWidth())/2;
// 		var errorWidth = jQuery('.pg-error-left').outerWidth();
// 		if(cont_width <= errorWidth){
// 			jQuery(parent).find('.pg-error-left').addClass('pg-hidden_error_message');
// 		}
// 		else{			
// 			jQuery(parent).find('.pg-error-left').removeClass('pg-hidden_error_message');
// 		}
// 	});
// });