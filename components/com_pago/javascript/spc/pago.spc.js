;"use strict";

var pagoSpcModule = angular.module( "pagoSpc", [ "pagoUsers" ] );


pagoSpcModule.run( [ "PagoUsers", function( PagoUsers ) {
	
	PagoUsers.get();
	
}]);