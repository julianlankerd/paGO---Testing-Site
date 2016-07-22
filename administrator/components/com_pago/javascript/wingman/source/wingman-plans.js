;"use strict";

angular
	.module(
		"pagoWingmanPlans",
		[
			"pagoWingmanResource",
		]
	)
	.factory(
		"Plans",
		[
			"PagoResource",
			function( PagoResource )
			{
				return PagoResource( "plans" );
			}
		]
	)
	.directive(
		"wingmanPlan",
		[
			"viewurl",
			function( viewurl )
			{
				return {
					restrict: "E",
					scope: {
						plan: "=plan",
					},
					templateUrl: viewurl + "directives/plan.html"
				};
			}
		]
	)
	.controller(
		"PlansController",
		[
			"Plans",
			"$rootScope",
			"$scope",
			"$location",
			function ( Plans, $rootScope, $scope, $location )
			{
				$scope.ready = true;
				
				$scope.plans = null;
				$scope.customPlan = {
					name: "Custom",
					metadata: {
						description: "<ul><li>One of our representatives will be happy to help develop a package custom fit to your own unique needs.</li></ul>"
					}
				};
				$scope.interval = "month";
				
				var plans = Plans.get(function () {
					$scope.plans = plans.data;
				});
				
				
				$scope.choose = function ( plan )
				{
					if ( "custom" == plan.name.toLowerCase() )
						return window.open("http://seowingman.com/#section04");
					
					$rootScope.plan = plan;
					$location.path("/subscribe");
				};
				
				
				$scope.showSignUp = function ()
				{
					if ( $location.path().match( "plan" ) )
						return true;
				};
			}
		]
	);