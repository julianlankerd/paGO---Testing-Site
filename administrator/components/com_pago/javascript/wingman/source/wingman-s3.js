;"use strict";

angular
	.module(
		"pagoWingmanS3",
		[
			"pagoWingmanResource",
		]
	)
	.factory(
		"S3",
		[
			"PagoResource",
			function( PagoResource )
			{
				return PagoResource( "s3", null, { 
					save: { 
						isArray: true,
					},
					get: {
						cache: false,
					}
				} );
			}
		]
	)
	.controller(
		"S3Controller",
		[
			"S3",
			"$rootScope",
			"$scope",
			"$location",
			"$interval",
			"$timeout",
			function ( S3, $rootScope, $scope, $location, $interval, $timeout )
			{
				$scope.loading = true;
				
				$scope.messages = [];
				$scope.files    = [];
				$scope.analyst  = {
					analyst_name: "Unassigned",
					status: "0"
				};
				$scope.status = [ "Pending", "Started", "Completed" ];
				
				var goUp = function ()
				{
					var msgs = jQuery( ".messages" );
					msgs.scrollTop( msgs.get(0).scrollHeight );
				};
				
				var prev;
				var load = function ( stop )
				{
					$timeout.cancel(prev);
					
					S3.get(
						function ( response ) {
							$scope.messages = response.messages;
							$scope.files    = response.files;
							
							if ( response.analyst )
								$scope.analyst = response.analyst;
							
							$scope.loading = false;
							
							prev = $timeout( load, 10000 );
							
							if ( stop ) 
								$timeout.cancel(prev);
							
							$timeout( goUp, 10 );
						}
					);
				};
				
				$scope.load = function ()
				{
					load();
				};
				
				$scope.message = null;
				
				$scope.send = function ()
				{
					if ( $scope.message == "" && $scope.message == null )
						return;
					
					var msg = {
						name: $rootScope.subscriber.metadata.name,
						message: $scope.message
					};
					
					$scope.messages.push( msg );
					$scope.message = null;
					
					$timeout( goUp, 10 );
					
					S3.save( 
						msg, 
						function ()
						{
							load();
						}
					);
				};
			}
		]
	);