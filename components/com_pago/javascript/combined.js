function pull_upload_form( product, type ) {
	type_name = type;
	if ( 'user' == type ) {
		type_name = 'Images';
	};
	tb_show( 'Add ' + type_name, 'index.php?option=com_pago&view=upload_form&product='+product+'&type='+type+'&tmpl=component&async=1&TB_iframe=true&height=564&width=788' );
}

function pago_show_product_images( title ) {
	tb_show( title, '#TB_inline?height=500&width=700&inlineId=all-images-modal-wrap' );
}