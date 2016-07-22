function pgQuickCart(){//Small
	jQuery('.pg-cart-container').each(function() {
		if (jQuery(this).children().first().hasClass('pg-view-cart') && !jQuery(this).children().first().hasClass('link')){
			var modulID = jQuery(this).attr('moduleId');

			if (jQuery(this).find('.pg-view-cart').find('#pg-quick-cart').length) {
				jQuery(this).find('#pg-quick-cart').load('index.php?option=com_pago&view=cart&layout=cart_quickcart&tmpl=component&modulID='+modulID+'&async=2');
			}else{
				jQuery(this).find('.pg-view-cart').append('<div id="pg-quick-cart"></div>');
				jQuery(this).find('#pg-quick-cart').load('index.php?option=com_pago&view=cart&layout=cart_quickcart&tmpl=component&modulID='+modulID+'&async=2');
			}
		}else if (jQuery(this).children().first().hasClass('pg-view-cart-mode2')  && !jQuery(this).children().first().hasClass('link')){
			var modulID = jQuery(this).attr('moduleId');

			if (jQuery(this).find('.pg-view-cart-mode2').find('#pg-quick-cart').length) {
				jQuery(this).find('#pg-quick-cart').load('index.php?option=com_pago&view=cart&layout=cart_quickcart&tmpl=component&modulID='+modulID+'&async=2', function() { 
					jQuery(this).find('.pg-quick-cart-contents').after(jQuery(this).find('.pg-view-cart-content'));
				});
			}
			else{
				jQuery(this).find('.pg-view-cart-mode2').find('.pg-quick-cart-mode2').append('<div id="pg-quick-cart"></div>');
				jQuery(this).find('#pg-quick-cart').load('index.php?option=com_pago&view=cart&layout=cart_quickcart&tmpl=component&modulID='+modulID+'&async=2', function() { 
					jQuery(this).find('.pg-quick-cart-contents').after(jQuery(this).find('.pg-view-cart-content'));
				});
			}
		}
	});
}
// function pgQuickCart(){//Small

// 	jQuery('.pg-cart-container').each(function() {
// 		if (jQuery(this).children().first().hasClass('pg-view-cart') && !jQuery(this).children().first().hasClass('link')){
// 			if (jQuery('.pg-view-cart').find('#pg-quick-cart').length) {
// 				var modulID = jQuery('.pg-view-cart').parent().attr('moduleId');
// 				jQuery('#pg-quick-cart').load('index.php?option=com_pago&view=cart&layout=cart_quickcart&tmpl=component&modulID='+modulID+'&async=2');
// 			}
// 			else{
// 				jQuery('.pg-view-cart').append('<div id="pg-quick-cart"></div>');
// 				var modulID = jQuery('.pg-view-cart').parent().attr('moduleId');
// 				jQuery('#pg-quick-cart').load('index.php?option=com_pago&view=cart&layout=cart_quickcart&tmpl=component&modulID='+modulID+'&async=2');
// 			}
// 		}else if (jQuery(this).children().first().hasClass('pg-view-cart-mode2')  && !jQuery(this).children().first().hasClass('link')){
// 			if (jQuery('.pg-view-cart-mode2').find('#pg-quick-cart').length) {
// 				var modulID = jQuery('.pg-view-cart-mode2').parent().attr('moduleId');
// 				jQuery('#pg-quick-cart').load('index.php?option=com_pago&view=cart&layout=cart_quickcart&tmpl=component&modulID='+modulID+'&async=2', function() { 
// 					jQuery('.pg-quick-cart-contents').after(jQuery('.pg-view-cart-content'));
// 				});
// 			}
// 			else{
// 				jQuery('.pg-view-cart-mode2').find('.pg-quick-cart-mode2').append('<div id="pg-quick-cart"></div>');
// 				var modulID = jQuery('.pg-view-cart-mode2').parent().attr('moduleId');
// 				jQuery('#pg-quick-cart').load('index.php?option=com_pago&view=cart&layout=cart_quickcart&tmpl=component&modulID='+modulID+'&async=2', function() { 
// 					jQuery('.pg-quick-cart-contents').after(jQuery('.pg-view-cart-content'));
// 				});
// 			}
// 		}
// 	});
// }

function pgQuickCartBig(){	

	
}

jQuery(window).load(function(){
	jQuery('.pg-cart-container').each(function() {
		if (jQuery(this).children().first().hasClass('pg-view-cart') && !jQuery(this).children().first().hasClass('link')){			
			//pgQuickCartSmall();
			jQuery(document).on('mouseenter', '.pg-view-cart', function() {
				jQuery(this).addClass('open');
			})

			jQuery(document).on('mouseleave','.pg-view-cart',function() {
				jQuery(this).removeClass('open');
			})	
		}
		else if (jQuery(this).children().first().hasClass('pg-view-cart-mode2')  && !jQuery(this).children().first().hasClass('link')){			
			//pgQuickCart();
			//pgQuickCartBig();
			jQuery(document).on('mouseenter','.pg-view-cart-mode2',function() {
				jQuery(this).addClass('open');
			})

			jQuery(document).on('mouseleave','.pg-view-cart-mode2',function() {
				jQuery(this).removeClass('open');
			})	
		}
	});
	pgQuickCart();	
	var windowWidth = jQuery(window).width();
	jQuery('.pg-cart-container').each(function(){
	   	if (jQuery(this).children('div').offset().left < windowWidth/2)
	   		jQuery(this).addClass('pg-cart-left');
	})
})