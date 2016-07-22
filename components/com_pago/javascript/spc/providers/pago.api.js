;"use strict";

// Isolate module
var pagoApiModule = angular.module( "pagoApi", [] );

// pagoApi object
var pagoApi = function() {
	
	// Config the default behavior of API
	this.beforePrepare = function( config ) {};
	this.afterPrepare  = function( config ) {};
	
	this.beforeParse = function( response ) {};
	this.afterParse  = function( response ) {};
	
	// Prepare request to API
	this.prepare = function( config ) {
		
		// Hook
		this.beforePrepare( config );
		
		// do code
		// ...
		
		// Hook
		this.afterPrepare( config );
		
		return config;
		
	};
	
	// Parse API response
	this.parse = function( response ) {
		
		// Hook
		this.beforeParse( response );
		
		// do code
		// ...
		
		// Hook
		this.afterParse( response );
		
		return response;
		
	};
	
};

// Expose object to application
pagoApiModule.provider( "pagoApi", function pagoApiProvider() {
	
	// Exposed provider to config phase
	this.$get = function pagoApiFactory() {
		
		// Any initalization
		
		// Return object itself
		return new pagoApi();
		
	};
	
});