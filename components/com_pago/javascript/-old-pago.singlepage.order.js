;"use strict";

var pagoOrderModule = angular.module( "pagoOrderModule", [ "ui-notification" ] );
    
pagoOrderModule.controller(
    "OrderController", 
    [ "Notification", "pagoConfig", "pagoApiService", "$rootScope", "$scope", 
        function( Notification, pagoConfig, pagoApiService, $rootScope, $scope ) {
    
    
    $scope.loading = false;
    
    
}]);

pagoOrderModule.directive("pagoOrder", function(){
    return {
        restrict   : "E",
        templateUrl: "/dev/components/com_pago/views/checkout/tmpl/singlepage.order.php",
        controller : "OrderController"
    };
});

pagoOrderModule.run([ "pagoApiService", "$rootScope", function( pagoApiService, $rootScope ){
    
    pagoApiService
        .setModel("cart")
        .setAction("get")
        .resetData()
        .call({
            success: function( data )
            {
                if (!data.data.response.cart)
                    return;
                
                var cart = data.data.response.cart;
                
                $rootScope.cart = cart;
                
                $rootScope.order.items = data.data.response.cart.items;
                
                if (cart.user_data)
                    $rootScope.order.addresses = cart.user_data;
                    
                if (cart.carrier)
                    $rootScope.order.shipper = cart.carrier;
            }
        });
    
}]);