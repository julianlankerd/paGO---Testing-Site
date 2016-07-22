;"use strict";

angular
	.module( 
		"pagoWingman", 
		[
			"ngRoute", 
			"ngSanitize",
			"ngMessages",
			"pagoWingmanVariables",
			
			"pagoWingmanSubscriber",
			"pagoWingmanPlans",
			"pagoWingmanS3",
		] 
	)
	.config([
		"viewurl",
		"$routeProvider",
		"$locationProvider",
		"$httpProvider",
		"PagoResourceProvider",
		function ( viewurl, $routeProvider, $locationProvider, $httpProvider, PagoResourceProvider )
		{
			$httpProvider.defaults.cache = true;
			
			PagoResourceProvider.apiurl("../index.php?option=com_pago&view=api&task=call&format=json&service=wingman/");
			
			$routeProvider
				.when(
					"/plans", 
					{
						templateUrl: viewurl + "plans.php",
						controller: "PlansController",
						resolve: {
							_access: function () 
							{
								return 0;	
							}
						}
					}
				)
				.when(
					"/change-plan", 
					{
						templateUrl: viewurl + "change-plan.php",
						controller: "PlansController",
						resolve: {
							_access: function () 
							{
								return 1;	
							}
						}
					}
				)
				.when(
					"/subscribe", 
					{
						templateUrl: viewurl + "subscribe.php",
						controller: "SubscriberController",
						resolve: {
							_access: function () 
							{
								return 0;	
							},
							
						}
					}
				)
				.when(
					"/contact-us", 
					{
						templateUrl: viewurl + "contact-us.php",
						controller: "SubscriberController",
						resolve: {
							_access: function () 
							{
								return 0;	
							}
						}
					}
				)
				.when(
					"/unsubscribed", 
					{
						templateUrl: viewurl + "unsubscribed.php",
						controller: "SubscriberController",
						resolve: {
							_access: function () 
							{
								return 0;	
							}
						}
					}
				)
				.when(
					"/dashboard", 
					{
						templateUrl: viewurl + "dashboard.php",
						controller: "SubscriberController",
						resolve: {
							_access: function () 
							{
								return 1;	
							}
						}
					}
				).otherwise(
					{
						redirectTo: "/dashboard",
					}
				);
		}
	])
	.run(
		function ( $rootScope, $location, $timeout ) 
		{
			$rootScope.$on( 
				"$locationChangeSuccess", 
				function ( newUrl, oldUrl, newState, oldState ) 
				{
					$rootScope.path = $location.path();
				}
			);
			
			$rootScope.viewLoaded = function ()
			{
				jQuery( "#tabs" ).tabs();
			};
		}
	);

angular
	.element( document )
	.ready( 
		function () 
		{
			angular.bootstrap(
				angular.element( "#pago-wingman-app" ), 
				[
					"pagoWingman"
				]
			);
		}
	);