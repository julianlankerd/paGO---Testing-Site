;"use strict";

angular
	.module(
		"pagoWingmanGeolocation",
		[]
	)
	.provider(
		"Geolocation",
		function GeolocationProvider ()
		{
			var source = "http://api.geonames.org/";
			var user   = "corephp";
			var format = "JSON";
			
			var usernameFn = function ( newUser ) 
			{
				if ( "string" == typeof newUser )
					user = newUser;
				
				return user;
			};
			
			this.username = usernameFn;
			
			this.$get = [
				"$http",
				"$q",
				function ( $http, $q )
				{
					var Geolocation = {
						username: usernameFn,
						getCountries: function ()
						{
							var d = $q.defer();
							
							$http.get( source + "countryInfo" + format + "?username=" + this.username() ).then(
								function ( response )
								{
									var countries = response.data.geonames;
									
									for (var i = 0, l = countries.length; i < l; i++) {
										countries[i].label = countries[i].countryName;
										countries[i].value = countries[i].countryCode;
									}
									
									d.resolve(countries);
								},
								function ()
								{
									d.reject();
								}
							);
							
							return d.promise;
						},
						getStates: function ( country ) 
						{
							var d = $q.defer();
							
							$http.get( source + "search" + format + "?country=" + country + "&featureCode=ADM1" + "&username=" + this.username() ).then(
								function ( response ) 
								{
								    var states = response.data.geonames;
								    
								    for (var i = 0, l = states.length; i < l; i++) {
								    	states[i].label = states[i].value = states[i].name;
								    }
								    
								    d.resolve(states);
								},
								function () 
								{
								    d.reject();
								}
							);
							
							return d.promise;
						}
					};
					
					return Geolocation;
				}
			];
		}
	);