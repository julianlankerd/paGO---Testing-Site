;"use strict";

angular
	.module(
		"pagoWingmanResource",
		[
			"ngResource",
		]
	)
	.provider(
		"PagoResource", 
		function PagoResourceProvider ()
		{
			var apiurl = "";
			
			this.apiurl = function ( url )
			{
				url && (apiurl = url);
				
				return apiurl;
			};
			
			var dataTransformation = function ( data, headersGetter )
			{
				data = data || {};
				data.id = "auto";
				
				return JSON.stringify(data);
			};
			
			var defaultHeaders = {
				"Content-Type": function ( config ) 
				{
					return "application/json";
				},
			};
			
			this.$get = [
				"$resource",
				function( $resource )
				{
					return function ( resourceName, params, methods ) 
					{
						var defaults = {
							get: {
								method: "GET",
								isArray: false,
								transformRequest: dataTransformation,
								headers: defaultHeaders,
								cache: true,
							},
							save: { 
								method: "POST", 
								isArray: false,
								transformRequest: dataTransformation,
								headers: defaultHeaders,
								cache: true,
							},
							delete: { 
								method: "DELETE",
								transformRequest: dataTransformation,
								headers: defaultHeaders,
								cache: true,
							},
						};
						
						methods = angular.merge( defaults, methods );
						
						var resource = $resource( apiurl + resourceName, params, methods );
						
						return resource;
					};
				}
			];
		}
	);