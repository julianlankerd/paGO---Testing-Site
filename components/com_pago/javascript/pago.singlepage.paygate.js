;"use strict";

var pagoPaygateModule = angular.module( "pagoPaygateModule", [] );

pagoPaygateModule.factory(
	"pagoPaygateService", 
	[ "pagoConfig", "pagoApiService", "$rootScope", 
		function( pagoConfig, pagoApiService, $rootScope ){
	
	// Preserv scope
	var self = this;
	
	
	// Get available paygates
	self.get = function()
	{
		return pagoApiService
			.setModel("paygates")
			.setAction("get")
			.resetData()
			.call();
	};
	
	
	return self;
	
}]);

pagoPaygateModule.controller(
	"PaygateController", 
	[ "pagoConfig", "pagoPaygateService", "$rootScope", "$scope", 
		function( pagoConfig, pagoPaygateService, $rootScope, $scope ) {
	
	$scope.paygates = null;
	$scope.saved = false;
	
	$scope.get = function()
	{
		if ( null === $rootScope.user )
			return;
		
		$scope.loading = true;
		
		pagoPaygateService.get().success(function( data ){
			$scope.paygates = data.response.paygates;
			
			if ( $scope.paygatesQty() == 1 )
				angular.forEach( 
					$scope.paygates, 
					function ( paygate, key )
					{
						$rootScope.paygate = paygate;
						$rootScope.paygate.key = key;
					}
				);
				
		}).finally(function(){
			$scope.loading = false;
		});
	};
	
	
	$rootScope.$watch("user", function( newValue, oldValue ){
		
		if (null === newValue || null === newValue.id || undefined === newValue.id)
			return;
		
		if ( oldValue && oldValue.id == newValue.id )
			return;
		
		$scope.get();
		
	}, true);
	
	
	$scope.applyPaygate = function ()
	{
		if (!$scope.paygateForm.$valid)
			return;
		
		$scope.saved = true;
	};
	
	
	$scope.paygatesQty = function ()
	{
		if ( null === $scope.paygates )
			return 0;
		
		return Object.keys($scope.paygates).length;
	};
	
}]);

pagoPaygateModule.run([ "$rootScope", function( $rootScope ){
	
	$rootScope.paygate = null;
	$rootScope.cardDetails = {};
	
}]);

pagoPaygateModule.directive("pagoPaygate", [ "$cookies", function( $cookies ){
	return {
		restrict: "E",
		templateUrl: $cookies.get( "tmplUrl" ) + "singlepage.paygate.php",
		controller: "PaygateController",
		scope: true
	};
}]);

pagoPaygateModule.directive("pagoCreditCardForm", [ "$cookies", "$rootScope", function( $cookies, $rootScope ){
	return {
		restrict: "E",
		templateUrl: $cookies.get( "tmplUrl" ) + "singlepage.credit-card-form.php",
		controller: "PaygateController",
		link: function ( scope, elm, attrs, ctrl )
		{
			var cc = $rootScope.cardDetails;
			
			if ( undefined !== cc.cc_month && undefined !== cc.cc_year )
				scope.cc_exp = cc.cc_month + "/" + cc.cc_year;
			
			scope.$watch( "cc_exp", function ( newValue, oldValue ) {
				
				if ( !newValue )
					return;
					
				if ( 2 == newValue.length && ( oldValue && oldValue.length < newValue.length ) )
					scope.cc_exp += "/";
				
				var pieces = newValue.split("/");
				
				if ( pieces.length < 2 )
					return;
				
				cc.cc_month = pieces[0];
				cc.cc_year  = pieces[1];
				
			});
			
			scope.months = [
				{
					name: "January",
					index: "01",
				},
				{
					name: "February",
					index: "02",
				},
				{
					name: "March",
					index: "03",
				},
				{
					name: "April",
					index: "04",
				},
				{
					name: "May",
					index: "05",
				},
				{
					name: "June",
					index: "06",
				},
				{
					name: "July",
					index: "07",
				},
				{
					name: "August",
					index: "08",
				},
				{
					name: "September",
					index: "09",
				},
				{
					name: "October",
					index: "10",
				},
				{
					name: "November",
					index: "11",
				},
				{
					name: "December",
					index: "12",
				},
			];
			
			scope.years = (function(){
				var today = new Date();
				var year  = today.getFullYear();
				var ret   = [];
				var years = 15;
				
				for (var i = 0; i < years; i++) {
					ret.push( year + i );
				}
				
				return ret;
			})();
		},
	};
}]);