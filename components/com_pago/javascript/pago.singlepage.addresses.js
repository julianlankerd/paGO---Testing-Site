;"use strict";

var pagoAddressModule = angular.module("pagoAddressModule", []);

pagoAddressModule.factory(
	"pagoAddressService",
	[ "pagoConfig", "pagoApiService", "$rootScope",
		function( pagoConfig, pagoApiService, $rootScope ){

	// Preserv scope
	var self = this;


	// Sync server with local model
	var sync = function( data )
	{
		if (200 != data.meta.code)
			return $rootScope.alert = {
				type: "warning",
				message: data.meta.message
			};
	};


	// Get current user addresses
	self.get = function()
	{
		return pagoApiService
			.setModel("addresses")
			.setAction("get")
			.resetData()
			.call();
	};

	// Save address to cart
	self.apply = function( address )
	{
		return pagoApiService
			.setModel("addresses")
			.setAction("apply")
			.setData(address)
			.call();
	};

	// Save address to cart
	self.appCart = function( req )
	{
		return pagoApiService
			.setModel("cart")
			.setAction("app_address")
			.setData(req)
			.call();
	};


	self.loadCountries = function()
	{
		return pagoApiService
			.setModel("countries")
			.setAction("get")
			.resetData()
			.call();
	};

	self.loadStates = function()
	{
		return pagoApiService
			.setModel("states")
			.setAction("get")
			.resetData()
			.call();
	};


	return self;

}]);

pagoAddressModule.filter("filterCountry", function(){
    return function( input, country ) {
        var ret = {};

        for (var i in input)
            if (input[i] == country)
                ret[i] = input[i];

        return ret;
    };
});

pagoAddressModule.controller(
	"AddressesController",
	[ "pagoConfig", "pagoAddressService", "$rootScope", "$scope",
		function( pagoConfig, pagoAddressService, $rootScope, $scope ) {


	$scope.loading = false;
	$scope.alert   = {};


	// List of addresses, countries and states
	$scope.userAddresses = [];
	$scope.countries = null;
	$scope.states    = null;


	// Current address infos
	$scope.sameForBilling = true;

	$scope.address = {
		id: ""
	};


	// get address
	var getAddress = function()
	{
		$scope.loading = true;

		pagoAddressService.get().success(function( data ){

			$scope.userAddresses = data.response.addresses;
			/*
			$scope.userAddresses.unshift({
				id: "N",
				address_type: "s",
				address_1: "Add new"
			});
			*/

		}).finally(function( data ){
			$scope.loading = false;
		});
	};

	$scope.get = function()
	{
		getAddress();
	};

	// load addresses for already logged in users
	$rootScope.$watch("user", function( newValue, oldValue) {

		if (null === newValue || undefined === newValue.id || pagoConfig.GUESTID === newValue.id)
			return $scope.userAddresses = [];

		if (oldValue && oldValue.id === newValue.id)
			return;

		getAddress();

	}, true);


	var sync = function ()
	{
		var request = {
			save_address: false,
			sameasshipping: $scope.sameForBilling,
		};

		if ( request.sameasshipping ) {
			var addresses = angular.copy($rootScope.addresses);
			addresses[1]  = angular.copy(addresses[0]);
			addresses[1].address_type = "b";
			addresses[1].address_type_name = "Billing";
			request.addresses = addresses;
		} else
			request.addresses = angular.copy($rootScope.addresses);

		pagoAddressService.appCart(request).success(function( data ) {
			$scope.loading = false;
			$rootScope.cart = data.response.cart.model;
			$rootScope.refreshShippers = true;
		});
	};


	// apply address (save / edit) and add to order
	$scope.apply = function( address, form )
	{
		form.$setSubmitted();

		if (!form.$valid && !address.saved)
			return;

		$scope.loading = true;

		if (address.id == "N")
			delete address.id;

		if ( form["same-for-billing"].$modelValue )
		{
			$rootScope.addresses = $rootScope.addresses || [];
			var billing = angular.copy(address);

			address.address_type = "s";
			address.address_type_name = "Shipping";

			billing.address_type = "b";
			billing.address_type_name = "Billing";

			$rootScope.addresses[0] = address;
			$rootScope.addresses[1] = billing;
		}
		else
		{
			if ("s" == address.address_type)
				$rootScope.addresses[0] = address;
			else
				$rootScope.addresses[1] = address;
		}

		if ( $rootScope.user.id == pagoConfig.GUESTID ) {
			address.saved = true;
			sync();
			return;
		}

		pagoAddressService.apply(address).then(function( response ){

			if (200 != response.data.meta.code)
				return $scope.alert = {
					type: "warning",
					message: response.data.meta.message
				};

			address.saved = true;

			for (var i = 0; i < response.data.response.addresses[0].length; i++)
			{
				$rootScope.addresses[i].id = response.data.response.addresses[0][i].id;
			}

			$scope.alert = {
				type: "success",
				message: "Address saved",
			};

			sync();
			getAddress();

		}, function(){

			return $scope.alert = {
				type: "warning",
				message: "Something went wrong, try again please"
			};

		}).finally(function(){
			$scope.loading = false;
		});
	};

	// load countries
	$scope.loadCountries = function()
	{
		if ( null !== $scope.countries )
			return;

		$scope.countries = {};

		pagoAddressService.loadCountries().success(function( data ){
			if (data.response.countries )
				$scope.countries = data.response.countries;
		});
	};

	// load states
	$scope.loadStates = function()
	{
		if ( null !== $scope.states )
			return;

		$scope.states = {};

		pagoAddressService.loadStates().success(function( data ){
			if ( !data.response.states )
				return;

			for ( var i in data.response.states.attribs ) {
				data.response.states.attribs[i] = data.response.states.attribs[i].replace("class=\"", "").replace("\"", "")
			}

			$scope.states = data.response.states.attribs;
		});
	};


	// Watchers
	$scope.$watch("sameForBilling", function(newValue, oldValue) {

		if ( null === $rootScope.addresses || $rootScope.addresses.length < 2 )
			return;

		if ( angular.equals( $rootScope.addresses, $rootScope.cart.user_data )
			&& ( $rootScope.cart.user_data[0].id == $rootScope.cart.user_data[1].id == newValue ) )
			return;

		if ( !$rootScope.addresses[0].saved || !$rootScope.addresses[1].saved )
			return;

		sync();

	});

	// split name into first and last
	$scope.$watch("address.name", function( newValue, oldValue ) {

	    if (!newValue)
	        return;

	    var name = newValue.split(" ");

	    $scope.address.first_name = name[0];

	    name.shift();

	    $scope.address.last_name = name.join(" ");

	});

	// join first and last to name
	$scope.$watch("address.first_name", function( newValue, oldValue ) {

		if ( !newValue )
			return;

	    if ($scope.address.name != "" && $scope.address.name !== null
	        && $scope.address.name !== undefined)
	        return;

	    var name = [ newValue, $scope.address.last_name ];

	    name = name.join(" ");

	    if (String.prototype.trim)
	        name = name.trim();

	    $scope.address.name = name;

	});

}]);

pagoAddressModule.run([ "pagoAddressService", "$rootScope", function( pagoAddressService, $rootScope ){

	$rootScope.addresses = null;

	var initAddresses = $rootScope.$watch("cart", function( newValue, oldValue ){

		if (null === newValue || !newValue.user_data)
			return;

		$rootScope.addresses = newValue.user_data;
		initAddresses();

	}, true);

}]);

pagoAddressModule.directive("pagoAddresses", [ "$cookies", function( $cookies ) {
	return {
		restrict   : "E",
		templateUrl: $cookies.get( "tmplUrl") + "singlepage.address.php",
		controller : "AddressesController"
	};
}]);

pagoAddressModule.directive(
	"pagoAddressForm",
	[
		"$rootScope",
		"$filter",
		"$cookies",
		"pagoConfig",
		function(
			$rootScope,
			$filter,
			$cookies,
			pagoConfig
		) {
			return {
				restrict   : "E",
				templateUrl: $cookies.get( "tmplUrl") + "singlepage.address-form.php",
				controller : "AddressesController",
				scope: {
					addressType : "@",
					addressTitle: "@"
				},
				link: function( scope, element, attrs, controller ) {

					scope.address = {
						id: "",
						saved: false,
					};

					scope.change = null;


					scope.showSelect = function ()
					{
						if (!scope.address.saved && scope.$parent.userAddresses.length > 0 && scope.change === null)
							return true;

						if (scope.address.saved)
							return false;

						if (scope.change !== true)
							return false;

						return true;
					};

					scope.showForm = function ()
					{
						if (scope.showSelect())
							return false;

						if (scope.address.saved)
							return false;

						if (scope.change)
							return false;

						return true;
					};

					scope.showInfo = function ()
					{
						if (!scope.address.saved)
							return false;

						return true;
					};


					scope.showStateField = function ()
					{
						return Object.keys($filter("filterCountry")(scope.$parent.states, scope.address.country)).length > 0;
					};

					scope.showSaveButton = function ()
					{
						if ( scope.loading || scope.$parent.loading )
							return false;

						if ( $rootScope.user && $rootScope.user.id == pagoConfig.GUESTID && $rootScope.config.checkout.skip_shipping )
							return false;

						return true;
					};


					$rootScope.$watch("addresses", function( newValue, oldValue ) {

						if (scope.address.saved)
							return;

						if (undefined === newValue || null === newValue)
							return;

						if (!newValue[0] && !newValue[1])
							return;

						for (var i = 0; i < newValue.length; i++) {
							newValue[i].saved = (undefined === newValue[i].saved) ? true : newValue[i].saved;
						}

						if ("s" == attrs.addressType)
							scope.address = newValue[0];
						else
							scope.address = newValue[1];

						if ( undefined === newValue[1] )
							return;

						if (!newValue[0].saved || !newValue[1].saved )
							return;

						scope.$parent.sameForBilling = (newValue[0].id == newValue[1].id);

					}, true);

					// fetch selected address and apply to model
					scope.fetch = function()
					{
						var id    = scope.address.id;
						var saved = scope.address.saved;

						if (!id || "N" === id || "" === id)
							return;

						var addr = jQuery.grep(scope.$parent.userAddresses, function( obj ){
							return obj.id == id;
						});

						addr[0].name = addr[0].first_name + " " + addr[0].last_name;

						addr[0].saved = saved;
						angular.extend(scope.address, addr[0]);

						scope.change = false;
						scope.address.saved = true;
						scope.$parent.apply(scope.address, scope.addressForm);

					};

					scope.add = function ()
					{
						scope.address = {
							id: 'N',
							saved: false
						};

						scope.change = false;
					};

					scope.cancelAdd = function ()
					{
						scope.address.id = (scope.address.id == 'N') ? '' : scope.address.id;
						scope.address.saved = (scope.address.id != '');
						scope.change = (scope.address.id == '') ? true : null;
					};

				}
			};
		}
	]
);
