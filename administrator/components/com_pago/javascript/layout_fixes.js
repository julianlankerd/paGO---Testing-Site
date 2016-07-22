jQuery(document).ready(function() {

	jQuery('div#submenu-box').remove();

	//toolbar stuff
	var toolbar = jQuery('div#toolbar').clone();

	jQuery('div#toolbar-box').remove();
	jQuery('div#pago_toolbar').append(toolbar);
	jQuery('div#pago_toolbar').css('padding-right', '8px');

	//fix background color
	jQuery('div.pg-m').css('background-color', '#fff');

	//adapt main content and apply wrapper
	jQuery('div#element-box').find('div.m').children().not('div.pg-wrapper').appendTo( jQuery('div.pg-main') );

	//system-message pg-main-header
	jQuery('div#element-box').html(jQuery('div.pg-wrapper'));

	//Joomla! 3.4 Toolbar Not hiding
	jQuery('div.subhead-collapse').css('display', 'none');
});
