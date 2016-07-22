;'use strict';

var pagoSinglePageApp = angular.module("pagoSinglePageApp", [
	"ngMessages",
	"ngCookies",

	// API
	"pagoApiModule",

	// Commons
	"pagoConfigModule",
	"pagoUtils",
	"ngFormFixes",

	// Modules
	"pagoCartModule",
	"pagoUserModule",
	"pagoAddressModule",
	"pagoShipperModule",
	"pagoPaygateModule",
]);


pagoSinglePageApp.run([ "$rootScope", "pagoConfig", "$cookies", "$sce", function( $rootScope, pagoConfig, $cookies, $sce ){

	// bootstrap
	$rootScope.config = null;
	$rootScope.baseTmplUrl = $cookies.get( "tmplUrl" );

	pagoConfig.load({
		success: function ( configs )
		{
			$rootScope.config = configs;
			$rootScope.config.checkout.skip_shipping = ( $rootScope.config.checkout.skip_shipping > 0 );

			$rootScope.terms = $sce.trustAsHtml( configs.checkout.terms_services );
		}
	});

	$rootScope.alert = {
		type: "",
		message: "",
	};

}]);
