;"use strict";

var pagoApiService = angular.module("pagoApiModule", []);
    
pagoApiService.factory("pagoApiService", [ "$http", "$cookies", function( $http, $cookies ){
    
    var self = this;
    
    var flag = false;
    
    var requestModel  = undefined;
    var requestAction = undefined;
    var requestData   = [];
    
    var apiUrl = $cookies.get( "apiUrl" );
    var method = "POST";
    
    
    self.setModel = function( model )
    {
        requestModel = model;
        return self;
    };
    
    self.getModel = function()
    {
        return requestModel;
    };
    
    self.setAction = function( act )
    {
        requestAction = act;
        return self;
    };
    
    self.setData = function( data )
    {
        requestData = (Array.isArray(data)) ? data : [data];
        return self;
    };
    
    self.addData = function( data )
    {
        requestData.push(data);
        return self;
    };
    
    self.resetData = function () 
    {
        requestData = [];
        return self;
    };
    
    
    self.call = function( opts )
    {
        opts = opts || {};
        
        return $http({
            method : method,
            url    : apiUrl,
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                act: requestModel + "." + requestAction,
                dta: requestData
            })
        })
        /*
        .error(function( data, status, header, config ) {
            return new self.apiError();
        });
        */
    };
    
    
    self.apiError = function( opts )
    {
        this.defaultMessage = "API Error. Check console for more infos.";
        
        if ("string" == typeof opts) {
            var message = opts;
            opts = {
                message: message
            };
        }
        
        opts = opts || {};
        
        if (!opts.message)
            opts.message = this.defaultMessage;
        
        return opts;
    };

    
    return self;
    
}]);