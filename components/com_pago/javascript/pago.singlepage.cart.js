;"use strict";

var pagoCartModule = angular.module( "pagoCartModule", [] );

pagoCartModule.controller(
	"CartController",
	[ "pagoConfig", "pagoApiService", "$rootScope", "$scope", "$cookies", "$timeout", "$parse", "$q",
		function( pagoConfig, pagoApiService, $rootScope, $scope, $cookies, $timeout, $parse, $q ) {


	$scope.addCoupon = function ()
	{
		if (null === $rootScope.cart)
			return;

		$rootScope.cart.coupon = $rootScope.cart.coupon || [];

		$rootScope.cart.coupon.push({});
	};

	$scope.removeCoupon = function ( $index )
	{
		$rootScope.cart.coupon.splice( $index, 1 );
	};


	$scope.canCheckout = function ()
	{
		if ( null === $rootScope.user || undefined === $rootScope.user.id )
			return false;

		if ( null !== $rootScope.cart && !$rootScope.cart.carrier
			&& null !== $rootScope.config && 0 == $rootScope.config.checkout.skip_shipping )
			return false;

		if ( !$rootScope.paygate )
			return false;

		return true;
	};

	$scope.disabledCheckout = function ()
	{
		return !$scope.canCheckout();
	};

	$scope.checkoutText = function ()
	{
		if ( null === $rootScope.paygate )
			return "PAGO_CHOOSE_PAYMENT_METHOD";

		// @TODO warning to save the address

		if ( 0 == $rootScope.paygate.cc_form )
			return "PAGO_CONTINUE_PAYMENT";

		return "PAGO_QUICK_CART_CHECKOUT";
	};


	var loopForms = function ()
	{
		var p = $q.defer();
		var forms = [ "addressForm", "shippersForm", "paygateForm" ];

		angular
			.forEach(
				forms,
				function ( k, i )
				{
					var obj = $scope.checkoutForm[k];

					if ( !obj || !obj.$name )
						return;

					if ( typeof obj.$setSubmitted != "function" )
						return;

					if ( obj.$valid && ( !obj.$submitted || obj._resubmit ) ) {
						obj.$setSubmitted();
						delete obj._resubmit;
						$timeout(
							function ()
							{
								obj._submit();
							}
						);
					} else {
						obj.$setSubmitted();

						if ( obj.$invalid )
							obj._resubmit = true;
					}

					if ( i == forms.length - 1 )
						$timeout(
							function ()
							{
								p.resolve();
							}
							,500
						);
				}
			);

		return p.promise;
	};

	var doCheckout = function ()
	{
		if ( !$scope.checkoutForm.$valid )
			return $scope.loading = false;

		var request = {
			payment_option: $rootScope.paygate.key,
		};

		angular.extend(request, $rootScope.cardDetails);

		pagoApiService
			.setModel("cart")
			.setAction("app_paygate")
			.setData(request)
			.call()
			.success(function( data ){

				var paygateResponse = data.response.cart.paygate_response;

				var accept = {
					// P:  "Pending",
					C:  "Confirmed",
					// X:  "Cancelled",
					// R:  "Refunded",
					S:  "Shipped",
					// D:  "Denied",
					PA: "Authorized",
					// E:  "Expired",
					// U:  "Unsubscribed",
				};

				if (paygateResponse.redirectUrl)
					return window.location.href = paygateResponse.redirectUrl;

				if (typeof accept[paygateResponse.order_status] == "undefined" && "banktransfer" != paygateResponse.paymentGateway) {
					$scope.loading = false;
					return $rootScope.alert = {
						type: "warning",
						message: paygateResponse.message
					};
				}

				window.location.href = $cookies.get( "sucsUrl" );

			});
	};

	$scope.checkout = function ()
	{
		$scope.loading = true;

		if ( 1 == $rootScope.config.checkout.skip_shipping )
			return loopForms()
				.then(
					function ()
					{
						doCheckout();
					}
				);

		doCheckout();

	};

}]);

pagoCartModule.run([ "pagoApiService", "$rootScope", "$cookies", function( pagoApiService, $rootScope, $cookies ){

	$rootScope.cart = null;

	pagoApiService
		.setModel("cart")
		.setAction("get")
		.resetData()
		.call()
		.success(function( data ) {
			if (!data.response.cart)
				return;

			if (!data.response.cart.items.length)
				window.location.href = $cookies.get( "cartUrl" );

			$rootScope.cart = data.response.cart;
		});

}]);

pagoCartModule.directive("pagoOrder", [ "$cookies", function( $cookies ){
	return {
		restrict: "E",
		templateUrl: $cookies.get( "tmplUrl" ) + "singlepage.order.php",
		controller: "CartController",
		scope: true
	};
}]);
