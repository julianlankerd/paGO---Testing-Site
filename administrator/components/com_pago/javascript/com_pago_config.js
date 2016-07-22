(function($){
	var options = {
		selectId : "#params_template_pago_themeparamstemplatepago_theme",
		wrapperDiv: "#theme-wrapper"
	};
	var meth = {
		bind: function () {
			$(options.selectId).on( 'change', priv.callback );
		}
	};
	var priv = {
		callback: function( event ) {
			if ( event.type == "change" ) {
				priv.themeChange();
			}
		},
		themeChange: function() {
			$(options.wrapperDiv).empty();
			$.post(
				"index.php",
				{
					option: "com_pago",
					view: "config",
					task: "loadconfig",
					theme: $(options.selectId).val()
				},
				function ( data ) {
					$(options.wrapperDiv).append( data );
				}
			);
		}
	};
	$.configSetup = function ( method ) {
		if ( meth[method] ) {
			return meth[method].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( typeof method === 'object' || ! method ) {
			return meth.init.apply( this, arguments );
		} else {
			$.error( 'Method' + method + ' does not exist on jQuery.configSetup' );
			return false;
		}
	};
})(jQuery);

jQuery(document).ready( function () {
	jQuery.configSetup("bind");
});
