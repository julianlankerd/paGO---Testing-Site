;"use strict";

var pagoUserModule = angular.module("pagoUserModule", []);

pagoUserModule.factory(
	"pagoUserService",
	[ "pagoConfig", "pagoApiService", "$rootScope",
		function( pagoConfig, pagoApiService, $rootScope ){

	// Preserv scope
	var self = this;


	// Flag vars
	self.user = null;
	self.rememberMe = true;


	// Sync server with local model
	var sync = function( data )
	{
		// return;

		if (200 != data.meta.code)
			return $rootScope.alert = {
				type: "warning",
				message: data.meta.status,
			};

		// $rootScope.user = data.response.users[0];

		$rootScope.$applyAsync(function(){ $rootScope.user = data.response.users[0] });
	};


	// Get current logged user
	self.get = function()
	{
		return pagoApiService
			.setModel("users")
			.setAction("get")
			.resetData()
			.call()
			.success(function( data ){
				if ( 200 == data.meta.code )
					sync(data);
			});
	};

	// Log user in
	self.login = function()
	{
		return pagoApiService
			.setModel("users")
			.setAction("login")
			.setData({
				username: $rootScope.user.username,
				password: $rootScope.user.password,
				remember: self.rememberMe
			})
			.call()
			.success(function( data ){
				sync(data);
			});
	};

	// Log user out
	self.logout = function()
	{
		return pagoApiService
			.setModel("users")
			.setAction("logout")
			.resetData()
			.call()
			.success(function(data) {
				sync(data);
			});
	};

	// Register user and log him in
	self.register = function()
	{
		return pagoApiService
			.setModel("users")
			.setAction("register")
			.setData({
				name:       $rootScope.user.name,
				username:   $rootScope.user.email,
				email:      $rootScope.user.email,
				password:   $rootScope.user.password,
				auto_login: true
			})
			.call()
			.success(function(data) {
				sync(data);
			});
	};


	return self;

}]);

pagoUserModule.controller(
	"UsersController",
	[ "pagoConfig", "pagoUserService", "$rootScope", "$scope", "$cookies",
		function( pagoConfig, pagoUserService, $rootScope, $scope, $cookies ) {


	$scope.loading = false;


	$rootScope.userType = null;

	$scope.rememberMe = pagoUserService.rememberMe;

	// Get config and set config after loaded
	pagoConfig.get("checkout.force_checkout_register", function( value ){

		// Default is 0
		value = value || 0;

		// 0 for Registration and Guest
		// 1 for Registration
		// 2 for Guest
		$scope.registrationPolicy = parseInt(value);

	});


	// $scope.passUrl = $cookies.passUrl;
	$scope.passUrl = $cookies.get( "passUrl");

	$scope.$watch("registrationPolicy", function( newValue, oldValue ){
		if ( newValue == 2 )
		{
			// guest checkout
			$rootScope.userType = 2;
		}
	});

	$rootScope.$watch("userType", function( newValue, oldValue ){
		if ( newValue == 2 )
		{
			// guest checkout
			$rootScope.user = $rootScope.user || {};
			$rootScope.user.id = pagoConfig.GUESTID;
		}
	});


	$scope.showLogin = function ()
	{
		if ( null == $scope.registrationPolicy)
			return false;

		if ( 2 == $scope.registrationPolicy || 1 <= $rootScope.userType )
			return false;

		return true;
	};

	$scope.showOptions = function ()
	{
		if ( null == $scope.registrationPolicy )
			return false;

		// if ( 0 != $scope.registrationPolicy )
		// 	return false;

		if ( null != $rootScope.userType )
			return false;

		return true;
	};

	$scope.showRegistrationOption = function ()
	{
		if ( 2 > $scope.registrationPolicy )
			return true;

		return false;
	};

	$scope.showGuestOption = function ()
	{
		if ( 1 != $scope.registrationPolicy )
			return true;

		return false;
	};

	$scope.showRegistration = function ()
	{
		if ( null == $scope.registrationPolicy )
			return false;

		if ( 1 < $scope.registrationPolicy )
			return false;

		if ( 1 != $rootScope.userType )
			return false;

		return true;
	};

	$rootScope.showGuestRegistrationAlert = function ()
	{
		if ( null !== $rootScope.user && $rootScope.user.id != pagoConfig.GUESTID)
			return false;

		if ( null == $scope.registrationPolicy )
			return false;

		if ( 1 < $scope.registrationPolicy )
			return false;

		return true;
	};


	$scope.auth = function ()
	{
		pagoUserService
			.get()
			// .then(
			// 	function ( response )
			// 	{
			// 		// $rootScope.$apply("user = response.data.response.users[0]");
			// 	}
			// );
	};

	$scope.auth();

	$scope.login = function( form )
	{
		if ( $rootScope.user.id )
			return;

		form.$setSubmitted();

		if (!form.$valid)
			return;

		$scope.loading = true;

		pagoUserService
			.login()
			// .then(
			// 	function ( response )
			// 	{
			// 		$rootScope.$applyAsync(function(){ $rootScope.user = response.data.response.users[0] });
			// 	}
			// )
			.finally(
				function ()
				{
					form.$setPristine();
					form.$setUntouched();
					$scope.loading = false;
				}
			);
	};

	$scope.logout = function()
	{
		if (!$rootScope.user || !$rootScope.user.id)
			return;

		$scope.loading = true;

		pagoUserService.logout().finally(function(){
			$scope.loading = false;
			$rootScope.user = {};
		});
	};

	$scope.register = function( form )
	{
		if ( $rootScope.user.id )
			return;
			
		form.$setSubmitted();

		if (!form.$valid)
			return;

		$scope.loading = true;

		pagoUserService.register().finally(function(){
			form.$setPristine();
			form.$setUntouched();
			$scope.loading = false;
		});
	};

}]);

pagoUserModule.run([ "pagoUserService", "$rootScope", function( pagoUserService, $rootScope ){

	$rootScope.user = null;

	// pagoUserService.get();

}]);

pagoUserModule.directive("pagoUsers", [ "$cookies", function( $cookies ){
	return {
		restrict: "E",
		templateUrl: $cookies.get( "tmplUrl") + "singlepage.user.php",
		controller: "UsersController"
	};
}]);
