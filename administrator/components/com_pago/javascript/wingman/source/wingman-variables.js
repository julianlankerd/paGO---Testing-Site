;"use strict";

angular
	.module( 
		"pagoWingmanVariables", 
		[] 
	)
	.constant(
		"comurl",
		"components/com_pago/"
	)
	.constant(
		"appurl",
		"components/com_pago/javascript/wingman/"
	)
	.constant(
		"viewurl",
		"components/com_pago/views/wingman/tmpl/"
	)
	.constant(
		"symbols",
		{
			"usd": "$"
		}
	)
	.run(
		function ( 
			$rootScope, 
			symbols, 
			comurl, 
			viewurl
		)
		{
			$rootScope.comurl  = comurl;
			$rootScope.viewurl = viewurl;
			$rootScope.symbols = symbols;
		}
	);