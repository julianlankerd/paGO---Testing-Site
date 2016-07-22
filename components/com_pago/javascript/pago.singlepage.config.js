;"use strict";

var pagoConfigModule = angular.module("pagoConfigModule", []);

pagoConfigModule.factory("pagoConfig", [ "pagoApiService", function( pagoApiService )
{
    var pagoConfig = {
        // contants
        GUESTID: "guest",
    };
    var _q = [];
    
    pagoConfig.configs = {};
    pagoConfig.loaded = false;
    
    pagoConfig.load = function( opts )
    {
        opts = opts || {};
        
        if (pagoConfig.loaded)
        {
            if (typeof opts.success == "function")
                opts.success.call(pagoConfig);  
            
            return pagoConfig;
        }
        
        pagoApiService.setModel("config");
        pagoApiService.setAction("get");
        pagoApiService.setData([
            {
                type: "general"
            },
            {
                type: "language"
            },
            {
                type: "checkout"
            }
        ]);
        pagoApiService
            .call()
            .success(function( data ) {
                var configs = data.response.config;
                
                if (configs[0] === null)
                    return;
                    
                configs = configs[0];
                
                for (var type in configs) {
                    pagoConfig.configs[type] = configs[type];
                }
                
                if (typeof opts.success == "function")
                    opts.success.call(null, pagoConfig.configs);
                    
                pagoConfig.loaded = true;
                
                for (var i = 0; i < _q.length; i++) {
                    var config   = _q[i][0];
                    var callback = _q[i][1];
                    
                    if (typeof callback != "function")
                        continue;
                    
                    var c = pagoConfig.get(config);
                    
                    callback.call(pagoConfig, c);
                }
            }).error(function( data ){
                if (typeof opts.error == "function")
                    opts.error.call(pagoConfig);
            });
        
        return pagoConfig;
    };
    
    pagoConfig.get = function( config, callback )
    {
        if (!pagoConfig.loaded) {
            _q.push([config, callback]);
            return;
        }
        
        config = config || "";
        
        var c = config.split(".");
        var type = c[0];
        var item = c[1];
        
        if (config == "")
            return pagoConfig.configs;
        
        var result = pagoConfig.configs[type][item];
        
        if ( "function" == typeof callback )
            callback.call( pagoConfig, result );
        
        return result;
    };
    
    pagoConfig.reset = function()
    {
        pagoConfig.loaded = false;
        
        return pagoConfig;
    };
    
    return pagoConfig;
}]);