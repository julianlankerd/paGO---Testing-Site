;"use strict";

var pagoUsersModule = angular.module( "pagoUsers", [ "ngResource", "pagoApi" ] );

pagoUsersModule.factory( "PagoUsers", [ "$resource", "pagoApi", function( $resource, pagoApi ) {
	
	return $resource( "/dev/index.php?option=com_pago&view=api&format=json", null, {
		get: {
			method: "POST",
			params: {
				act: "users.get"
			},
			transformRequest: function( data, headers ) {
				console.log(data);
				console.log(headers);
				
                var result = JSON.stringify(data);
                return result;
			}
		}
	});
	
}]);