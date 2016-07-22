;"use strict";

var pagoShipperModule = angular.module( "pagoShipperModule", [] );

pagoShipperModule.factory(
	"pagoShipperService",
	[ "pagoConfig", "pagoApiService", "$rootScope",
		function( pagoConfig, pagoApiService, $rootScope ){

	// Preserv scope
	var self = this;


	// Sync server with local model
	self.sync = function( data, $scope )
	{
		// if (200 != data.meta.code)
		// 	return false;

		$rootScope.shippers = data.response.shippers;
	};


	// Get shipper options
	self.get = function()
	{
		return pagoApiService
			.setModel("shippers")
			.setAction("get")
			.resetData()
			.call();
	};


	// Apply current shipper to cart
	self.apply = function()
	{
		// $rootScope.shipper.text = $rootScope.shipper.name;

		return pagoApiService
			.setModel("cart")
			.setAction("app_shipper")
			.setData($rootScope.shipper)
			.call();
	};


	return self;

}]);

pagoShipperModule.controller(
	"ShippingController",
	[ "pagoConfig", "pagoShipperService", "$rootScope", "$scope", "$q", "$timeout",
		function( pagoConfig, pagoShipperService, $rootScope, $scope, $q, $timeout ) {

	$scope.loading = false;
	$scope.saved   = false;


	$rootScope.skipShipping = null;

	pagoConfig.get( "checkout.skip_shipping", function ( config ) {

		// 0 for no
		// 1 for skip
		$rootScope.skipShipping = config;

	});


	$rootScope.shippers = null;


	$scope.get = function()
	{
		pagoConfig.get( "checkout.skip_shipping", function ( config ) {

			if ( 1 == config )
				return;

			if (!$rootScope.user || null === $rootScope.user)
				return;

			if (!$rootScope.addresses || null === $rootScope.addresses)
				return;

			if (null !== $rootScope.shippers)
				return;

			getShippers().then(function( data ){

				if (null !== $rootScope.shippers)
					return;

				// pagoShipperService.sync( data, $scope );
				$rootScope.shippers = data.response.shippers;

			});

		});
	};

	var defer = null;
	var getShippers = function()
	{
		// if (null !== defer)
		// 	return defer.promise;

		defer = $q.defer();

		pagoShipperService.get().success(function( data ){

			// if (data.response.shippers.length == 0) {
			// 	defer.reject( data );
				// return Notification.warning("No applicable shipping options");
			// }

			// Notification.success("Shippers loaded");
			// $rootScope.shippers = data.response.shippers;
			defer.resolve( data );

		});

		return defer.promise;
	};


	$scope.applyShipper = function()
	{
		if (!$scope.shipperForm.$valid)
			return;

		pagoConfig
			.get(
				"checkout.skip_shipping",
				function ( config )
				{
					if ( config > 0 )
						return;
					
					pagoShipperService.apply().success(function( data ){

						if (200 != data.meta.code)
						return;

						$rootScope.cart = data.response.cart.model;
						$scope.saved = true;

					});
				}
			);
	};

	$rootScope.$watch("shipper", function ( newValue, oldValue ) {

		// No shipper
		if ( null === newValue )
			return;

		$scope.saved = true;

		// Same shipper as before
		if ( null !== oldValue && newValue.code == oldValue.code )
			return;

		// Shipper already in the cart
		if ( $rootScope.cart.carrier && newValue.code == $rootScope.cart.carrier.code )
			return;

		$scope.applyShipper();

	});


	$rootScope.$watch("cart", function( newValue, oldValue ){

		if (null === newValue)
			return;

		if (newValue.carrier) {
			$rootScope.shipper = $rootScope.cart.carrier;
			$scope.saved = true;
		}

		if (null === newValue.carrier) {
			$rootScope.shipper = null;
			$scope.saved = false;
		}

		if (!$rootScope.user || null === $rootScope.user)
			return;

		if (!$rootScope.addresses || null === $rootScope.addresses)
			return;

		if ( null !== oldValue && angular.equals(newValue.user_data, oldValue.user_data) )
			return;

		if (!newValue.user_data[0].saved)
			return;

		var changed = false;
		angular.forEach(newValue.user_data[0], function ( value, key ) {
			if (!angular.equals(value, oldValue.user_data[0][key]) && "saved" != key)
				changed = true;
		});

		if (!changed)
			return;

		pagoConfig.get( "checkout.skip_shipping", function ( config ) {

			if ( 1 == config )
				return;

			refreshShippers();

		});


	}, true);


	$rootScope.$watch("user", function ( newValue, oldValue ) {

		if ( null !== newValue && newValue.id && newValue.id == pagoConfig.GUESTID )
			refreshShippers();

	}, true);


	$rootScope.$watch("refreshShippers", function( newValue, oldValue ) {

		if (!newValue)
			return;


		pagoConfig.get( "checkout.skip_shipping", function ( config ) {

			if ( 1 == config )
				return;

			refreshShippers();
			$rootScope.refreshShippers = false;

		});

	});

	var refreshShippers = function ()
	{
		pagoConfig.get( "checkout.skip_shipping", function ( config ) {

			if ( 1 == config )
				return;

			// $scope.loading = true;

			getShippers().then(function( data ){

				// $scope.loading = false;

				// if (null !== $rootScope.shippers)
				// 	return;

				// pagoShipperService.sync( data, $scope );
				$timeout(
					function ()
					{
						$rootScope.shippers = data.response.shippers;
						$scope.loading = false;
					},
					0
				);

			});

		});
	};

	$scope.showShippingNotice = function ()
	{
		if ( $scope.loading )
			return false;

		// if ( !$scope.saved )
		// 	return false;

		if ( null !== $rootScope.addresses && !$rootScope.addresses.length )
			return false;

		if ( null !== $rootScope.shippers
			&& ( !$rootScope.shippers.length && !Object.keys( $rootScope.shippers ).length ) )
			return true;

		return false;
	};

	$scope.showShippingOptions = function ()
	{
		if ( $scope.loading )
			return false;

		if ( $scope.saved )
			return false;

		if ( null !== $rootScope.shippers && !Object.keys($rootScope.shippers).length )
			return false;

		return true;
	};

	/*
	$rootScope.$watch( "addresses", function( newValue, oldValue ) {

		if ( null === newValue || angular.equals( newValue, oldValue ) )
			return;

		getShippers().then(function( data ){

			if (null !== $rootScope.shippers)
				return;

			pagoShipperService.sync( data, $scope );

		});

	}, true);
	*/

}]);

pagoShipperModule.run([ "$rootScope", function( $rootScope ){

	$rootScope.shipper = null;

}]);

pagoShipperModule.directive("pagoShipping", [ "$cookies", function( $cookies ){
	return {
		restrict   : "E",
		templateUrl: $cookies.get( "tmplUrl" ) + "singlepage.shipping.php",
		controller : "ShippingController"
	};
}]);
