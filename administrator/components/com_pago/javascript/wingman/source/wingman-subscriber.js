;"use strict";

angular
	.module(
		"pagoWingmanSubscriber",
		[
			"pagoWingmanResource",
			"formly",
			"pagoWingmanFormly",
			"pagoWingmanStripe",
			"pagoWingmanGeolocation",
			"pagoWingmanPlans",
		]
	)
	.factory(
		"Subscriber",
		[
			"PagoResource",
			function( PagoResource )
			{
				return PagoResource( "subscriber" );
			}
		]
	)
	.factory(
		"Subscriptions",
		[
			"PagoResource",
			function( PagoResource )
			{
				return PagoResource( "subscriptions" );
			}
		]
	)
	.controller(
		"SubscriberController",
		[
			"_access",
			"Subscriber",
			"Subscriptions",
			"$scope",
			"$rootScope",
			"StripeValidator",
			"$http",
			"$q",
			"$location",
			"Geolocation",
			"$sce",
			function ( 
				_access, 
				Subscriber, 
				Subscriptions, 
				$scope, 
				$rootScope, 
				StripeValidator, 
				$http, 
				$q, 
				$location, 
				Geolocation,
				$sce
				)
			{
				$scope.ready = false;
				
				if ( _access && !$rootScope.subscriber )
					Subscriber.get(
						function ( data )
						{
							if ( !data.id )
								$location.path("/plans");
							else
								$rootScope.subscriber = data;
							
							$scope.ready = true;
						}
					);
				else
					$scope.ready = true;
				
				
				if ( undefined === $rootScope.plan && !_access && "/contact-us" != $location.path() )
					$location.path("/plans");
				
				
				$scope.loading  = false;
				$scope.messages = [];
				
				
				$scope.subscription = {};
				
				var sync = function ()
				{
					var s = $rootScope.subscriber;
					
					$scope.subscription.name          = s.metadata.name;
					$scope.subscription.phone         = s.metadata.phone;
					$scope.subscription.email         = s.email;
					$scope.subscription.site          = s.metadata.site;
					$scope.subscription.line1         = s.shipping.address.line1;
					$scope.subscription.line2         = s.shipping.address.line2;
					$scope.subscription.city          = s.shipping.address.city;
					$scope.subscription.state         = s.shipping.address.state;
					$scope.subscription.country       = s.shipping.address.country;
					$scope.subscription.postal_code   = s.shipping.address.postal_code;
					
					$scope.subscription.analyst_login    = s.subscriptions.data[0].metadata.analyst_login;
					$scope.subscription.analyst_password = s.subscriptions.data[0].metadata.analyst_password;
					
					$scope.subscription.keywordsQty   = s.subscriptions.data[0].plan.metadata.keywords;
					
					if ( typeof s.subscriptions.data[0].metadata.keywords == "string" )
						s.subscriptions.data[0].metadata.keywords = s.subscriptions.data[0].metadata.keywords.split(",");
					
					var ka = [];
					angular.forEach( 
						s.subscriptions.data[0].metadata.keywords, 
						function ( value, key )
						{
							ka.push(
								{
									keyword: value,
									temp: false
								}
							);
						}
					);
					
					$scope.subscription.keywordsArray = ka;
					
					if ( typeof s.subscriptions.data[0].metadata.top_three == "string" )
						s.subscriptions.data[0].metadata.top_three = s.subscriptions.data[0].metadata.top_three.split(",");
					
					$scope.subscription.competitorsArray = s.subscriptions.data[0].metadata.top_three || [];
					$scope.subscription.competitors1 = $scope.subscription.competitorsArray[0] || "";
					$scope.subscription.competitors2 = $scope.subscription.competitorsArray[1] || "";
					$scope.subscription.competitors3 = $scope.subscription.competitorsArray[2] || "";
				};
				
				$rootScope.$watch( 
					"subscriber",
					function ( newValue, oldValue )
					{
						if ( undefined !== newValue )
							sync();
					}
				);
				
				
				var subscriptionFields = [
					{
						type: "subheader",
						templateOptions: {
							label: "User Information"
						},
					},
					{
						className: "pg-pad-20 pg-mb-20",
						fieldGroup: [
							{
								type: "input",
								key: "name",
								wrapper: "messages",
								templateOptions: {
									required: true,
									label: "Full Name",
								},
								validators: {
									"full-name": function ( viewValue, modelValue, scope )
									{
										var v = modelValue || viewValue;
										
										return "string" == typeof v && v.trim().split(" ").length > 1;
									}
								},
							},
							{
								className: "pg-row",
								fieldGroup: [
									{
										type: "input",
										key: "email",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "Email",
											size: "pg-col-6",
											type: "email",
										}
									},
									{
										type: "input",
										key: "phone",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "Phone",
											size: "pg-col-6",
											type: "tel",
										}
									},
								]
							},
						]
					},
					{
						type: "subheader",
						templateOptions: {
							label: "Address Information"
						}
					},
					{
						className: "pg-pad-20 pg-mb-20",
						fieldGroup: [
							{
								type: "input",
								key: "line1",
								wrapper: "messages",
								templateOptions: {
									required: true,
									label: "Address Line 1",
								}
							},
							{
								type: "input",
								key: "line2",
								wrapper: "messages",
								templateOptions: {
									label: "Address Line 2",
								}
							},
							{
								className: "pg-row",
								fieldGroup: [
									{
										type: "select",
										key: "country",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "Country",
											size: "pg-col-6",
											options: []
										},
										controller: function( $scope, Geolocation ) 
										{
											Geolocation.getCountries().then(
												function ( countries )
												{
													$scope.to.options = countries;
												}
											);
										},
									},
									{
										type: "select",
										key: "state",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "State",
											size: "pg-col-6",
											orderBy: "value",
											options: []
										},
										controller: function ( $scope, Geolocation )
										{
											$scope.$watch( 
												"model.country", 
												function ( newValue, oldValue )
												{
													if ( undefined !== newValue )
														Geolocation.getStates( newValue ).then(
															function ( states )
															{
																$scope.to.options = states;
															}
														);
												}
											);
										}
									},
								]
							},
							{
								className: "pg-row",
								fieldGroup: [
									{
										type: "input",
										key: "city",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "City",
											size: "pg-col-6",
										}
									},
									{
										type: "input",
										key: "postal_code",
										wrapper: "messages",
										templateOptions: {
											required: true,
											// minlength: 5,
											// maxlength: 5,
											label: "Zip Code",
											size: "pg-col-6",
											type: "tel",
										}
									},
								]
							},
						]
					},
				];
				var creditCardFields   = [
					{
						type: "subheader",
						templateOptions: {
							label: "Credit Card Information"
						}
					},
					{
						className: "pg-pad-20 pg-mb-20",
						fieldGroup: [
							{
								className: "pg-row",
								fieldGroup: [
									{
										type: "input",
										key: "number",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "Number",
											size: "pg-col-6",
										},
										validators: {
											"cc-number": function ( viewValue, modelValue, scope )
											{
												var v = modelValue || viewValue;
												
												return StripeValidator.number(v);
											}
										}
									},
									{
										type: "input",
										key: "cvc",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "CVC",
											size: "pg-col-6",
											type: "number"
										},
										validators: {
											"cc-cvc": function ( viewValue, modelValue, scope )
											{
												var v = modelValue || viewValue;
												
												return StripeValidator.cvc(v);
											}
										}
									},
								]
							},
							{
								className: "pg-row",
								fieldGroup: [
									{
										type: "select",
										key: "exp_month",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "Expire Month",
											size: "pg-col-6",
											orderBy: "value",
											options: (function () 
											{
												var months = [
													"January",
													"February",
													"March",
													"April",
													"May",
													"June",
													"July",
													"August",
													"September",
													"October",
													"November",
													"December"
												];
												var ret = [];
												
												for (var i = 0; i < months.length; i++) {
													var k = ( i + 1 < 10 ) ? "0" + ( i + 1 ) : ( i + 1 );
													var m = months[i].substr(0, 3);
													
													ret.push({
														label: m + " (" + k + ")",
														value: i + 1
													})
												}
												
												return ret;
											})()
										},
										validators: {
											"cc-expiry-month": function ( viewValue, modelValue, scope )
											{
												var v = modelValue || viewValue;
												var y = scope.model.year;
												
												if ( undefined === y )
													return true;
												
												return StripeValidator.expiry(v, y);
											}
										},
									},
									{
										type: "select",
										key: "exp_year",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "Expire Year",
											size: "pg-col-6",
											options: (function () 
											{
												var ret = [];
												var y = new Date().getFullYear();
												var n = 20;
												
												for (var i = y; i < y + n; i++) {
													ret.push({
														label: i,
														value: i
													});
												}
												
												return ret;
											})()
										},
										validators: {
											"cc-expiry-year": function ( viewValue, modelValue, scope )
											{
												var v = modelValue || viewValue;
												var m = scope.model.month;
												
												if ( undefined === m )
													return true;
												
												return StripeValidator.expiry(m, v);
											}
										},
									},
								]
							},
						]
					},
				];
				var termsFields        = [
					/*
					{
						type: "checkbox",
						key: "terms",
						wrapper: "messages",
						templateOptions: {
							required: true,
							label: "I'm ok in closing a deal with the devil trading my soul for success.",
						}
					},
					*/
				];
				
				var keywordsFields = [
					{
						type: "keywords",
						key: "keywords",
						wrapper: "messages",
						templateOptions: {
							label: "Type your keyword and then press enter",
						},
					},
				];
				var competitorsFields = [
					{
						type: "input",
						key: "competitors1",
						wrapper: "messages",
						templateOptions: {
							label: "Competitor #1",
							placeholder: "domain.com",
							required: true,
						},
					},
					{
						type: "input",
						key: "competitors2",
						wrapper: "messages",
						templateOptions: {
							label: "Competitor #2",
							placeholder: "domain.com",
							required: true,
						},
					},
					{
						type: "input",
						key: "competitors3",
						wrapper: "messages",
						templateOptions: {
							label: "Competitor #3",
							placeholder: "domain.com",
							required: true,
						},
					},
				];
				
				var contactFields = [
					{
						type: "subheader",
						templateOptions: {
							label: "Your Information"
						},
					},
					{
						className: "pg-pad-20 pg-mb-20",
						fieldGroup: [
							{
								type: "input",
								key: "name",
								wrapper: "messages",
								templateOptions: {
									required: true,
									label: "Full Name",
								},
								validators: {
									"full-name": function ( viewValue, modelValue, scope )
									{
										var v = modelValue || viewValue;
										
										return "string" == typeof v && v.trim().split(" ").length > 1;
									}
								},
							},
							{
								className: "pg-row",
								fieldGroup: [
									{
										type: "input",
										key: "email",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "Email",
											size: "pg-col-6",
											type: "email",
										}
									},
									{
										type: "input",
										key: "phone",
										wrapper: "messages",
										templateOptions: {
											required: true,
											label: "Phone",
											size: "pg-col-6",
											type: "tel",
										}
									},
								]
							},
							{
								type: "textarea",
								key: "message",
								wrapper: "messages",
								templateOptions: {
									required: true,
									label: "Your message",
								},
							},
						],
					},
				];
				
				$scope.fields = {
					information : subscriptionFields,
					registration: Array.concat( subscriptionFields, termsFields ),
					subscription: Array.concat( subscriptionFields, creditCardFields, termsFields ),
					inputs: [
						{
							className: "pg-row pg-mt-20",
							fieldGroup: [
								{
									className: "pg-col-6",
									fieldGroup: [
										{
											type: "subheader",
											templateOptions: {
												label: "Keywords"
											},
											link: function(scope, el, attrs, ctrl) {
												$scope.$watch( "subscription", function ( newValue ) {
													if ( !newValue || !newValue.keywordsQty )
														return;
													
													scope.to.label  = "Keywords - ";
													scope.to.label += $scope.subscription.keywordsQty - $scope.subscription.keywordsArray.length;
													scope.to.label += " out of ";
													scope.to.label += $scope.subscription.keywordsQty;
													scope.to.label += " available.";
													
												}, true);
											}
										},
										{
											className: "pg-mb-20",
											fieldGroup: keywordsFields,
										}
									],
								},
								{
									className: "pg-col-6",
									fieldGroup: [
										{
											type: "subheader",
											templateOptions: {
												label: "Competitors"
											},
										},
										{
											className: "pg-mb-20",
											fieldGroup: competitorsFields,
										}
									],
								},
							],
						},
					],
					contact: contactFields,
				};
				
				
				$scope.contactUsUrl = $sce.trustAsResourceUrl( $rootScope.viewurl + "contact-us.php" );
				
				
				$scope.subscribe = function ()
				{
					if ( !$scope.subscribeForm.$valid )
						return;
					
					$scope.loading = true;
					
					// Add site to the subscriber
					$scope.subscription.site = $location.host();
					
					// Add IP address to subscriber
					getIp()
						.then(
							function ( response )
							{
								$scope.subscription.ip = response.data.ip;
							}
						)
						.then(
							function ()
							{
								return Subscriber.save( $scope.subscription ).$promise;
							}
						)
						.then(
							function ( response )
							{
								if ( response.status && 500 == response.status )
									return $q.reject( response );
								
								$rootScope.subscriber = response;
								
								return $q.resolve( Subscriptions.save( { plan_id: $rootScope.plan.id } ).$promise );
							}
						)
						.then(
							function ( response )
							{
								if ( response.status && 500 == response.status )
									return $scope.messages[0] = response.detail;
								
								$location.path("/dashboard");
							},
							function ( error )
							{
								$scope.messages[0] = error.detail;
							}
						)
						.finally(
							function ()
							{
								$scope.loading = false;
							}
						);
				};
				
				var getIp = function ()
				{
					var q = $q.defer();
					
					if ( !$scope.subscription.ip ) 
						q.resolve($http.get( "https://api.ipify.org/?format=json" ));
					else 
						q.resolve( { data: { ip: $scope.subscription.ip } } );
					
					return q.promise;
				};
				
				
				$scope.updateSubscription = function ()
				{
					$scope.loading = true;
					
					$scope.subscription.competitorsArray[0] = $scope.subscription.competitors1;
					$scope.subscription.competitorsArray[1] = $scope.subscription.competitors2;
					$scope.subscription.competitorsArray[2] = $scope.subscription.competitors3;
					
					var keywords = [];
					angular.forEach(
						$scope.subscription.keywordsArray,
						function ( value, key )
						{
							keywords.push( value.keyword );
						}
					);
					
					var metadata = {};
					
					if ( keywords.join(",").length > 0 )
						metadata.keywords = keywords.join( "," );
					
					metadata.top_three = $scope.subscription.competitorsArray.join(",");
					
					metadata.analyst_login    = $scope.subscription.analyst_login;
					metadata.analyst_password = $scope.subscription.analyst_password;
					
					Subscriptions.save(
						{
							plan_id: $scope.subscriber.subscriptions.data[0].plan.id,
							metadata: metadata,
						},
						function ( response )
						{
							$scope.loading = false;
							
							if ( response.status && 500 == response.status )
								return $scope.messages[0] = response.detail;
							
							angular.forEach(
								$scope.subscription.keywordsArray,
								function ( value, key )
								{
									$scope.subscription.keywordsArray[key].temp = false;
								}
							)
						}
					);
				};
				
				
				$scope.unsubscribe = function ()
				{
					Subscriber.remove(
						function () 
						{
							$location.path("/unsubscribed");
						}
					);
				};
				
				
				$scope.update = function ()
				{
					if ( !$scope.myAccountForm.$valid )
						return;
					
					$scope.loading = true;
					
					getIp()
						.then(
							function( response ) 
							{
								$scope.subscription.ip = response.data.ip;
								$scope.subscription.number = "xxxx";
								
								return Subscriber.save( $scope.subscription ).$promise;
							}
						)
						.then(
							function ( response ) 
							{
								$rootScope.subscriber = response;
								
								$scope.messages.push("Mensagem de sucesso");
							},
							function ( response )
							{
								$scope.messages.push("Mensagem de erro");
							}
						)
						.catch(
							function ( response )
							{
								
							}
						)
						.finally(
							function () 
							{
								$scope.loading = false;
							}
						);
				};
			}
		]
	);