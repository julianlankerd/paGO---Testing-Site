;"use strict";

angular
	.module( 
		"pagoWingmanFormly", 
		[
			"pagoWingmanVariables",
			"formly",
		] 
	)
	.directive(
		"chosen", 
		[
			"$timeout",
			function () 
			{
				return {
					restrict: "A",
					link: function ( $scope, elm, attrs ) 
					{
						// update when data is loaded first time
						$scope.$watch(
							attrs.chosen, 
							function ( oldValue, newValue ) 
							{
								elm.trigger("chosen:updated");
							}
						);
						
						// update when the model changes
						$scope.$watch( 
							attrs.ngModel, 
							function ( oldValue, newValue ) 
							{
								elm.trigger("chosen:updated");
							}
						);
						
						// update when options change
						$scope.$watch( 
							"to.options", 
							function ( oldValue, newValue ) 
							{
								elm.trigger("chosen:updated");
							}
						);
						
						elm.chosen({
							"disable_search_threshold": 6
						}).change( function () {
							$scope.fc.$setTouched();
						});
					}
				};
			}
		]
	)
	.directive(
		"messages",
		[
			"viewurl",
			"$timeout",
			function ( viewurl, $timeout )
			{
				return {
					restrict: "E",
					templateUrl: viewurl + "directives/messages.html",
					scope: {
						messages: "="
					},
					link: function ( $scope, elm, attrs, ctrl )
					{
						$scope.messages = $scope.messages || [];
						
						$scope.$watchCollection(
							function () 
							{
								return $scope.messages;	
							},
							function ( newValue, oldValue )
							{
								elm.toggleClass( "ng-hide", newValue.length <= 0 );
							}
						);
						
						$scope.dismiss = function ( $index )
						{
							$scope.messages.splice( $index, 1 );
						};
					}
				};
			}
		]
	)
	.run(function ( formlyConfig, viewurl ) {
		
		// Formly View URL
		var fvurl = viewurl + "formly/";
		
		// Make form validate on submit
		formlyConfig.extras.errorExistsAndShouldBeVisibleExpression = "fc.$touched || form.$$parentForm.$submitted";
		
		// Form SubHeader (light grey background)
		formlyConfig.setType({
			name: "subheader",
			templateUrl: fvurl + "subheader.html",
		});
		
		// Regular input
		formlyConfig.setType({
			name: "input",
			templateUrl: fvurl + "input.html",
			link: function ( $scope, elm, attrs )
			{
				var defaults = {
					type: "text"
				};
				
				angular.extend( $scope.to, defaults );
			}
		});
		
		// Textarea
		formlyConfig.setType({
			name: "textarea",
			templateUrl: fvurl + "textarea.html",
			link: function ( $scope, elm, attrs )
			{
				var defaults = {
					rows: "3"
				};
				
				angular.extend( $scope.to, defaults );
			}
		});
		
		// Select input
		formlyConfig.setType({
			name: "select",
			templateUrl: fvurl + "select.html",
			link: function ( $scope, elm, attrs )
			{
				var defaults = {
					orderBy: "label"
				};
				
				angular.extend( defaults, $scope.to );
			}
		});
		
		// Checkbox input
		formlyConfig.setType({
			name: "checkbox",
			templateUrl: fvurl + "checkbox.html",
		});
		
		// Keywords input
		formlyConfig.setType({
			name: "keywords",
			templateUrl: fvurl + "keywords.html",
			link: function ( $scope, elm, attrs )
			{
				// Current typed keyword
				$scope.kw = null;
				
				$scope.full = function ()
				{
					return ( $scope.model.keywordsArray && $scope.model.keywordsQty ) &&
						( $scope.model.keywordsArray.length == $scope.model.keywordsQty );
				};
				
				var addKeyword = function ()
				{
					if ( !$scope.kw )
						return;
					
					if ( $scope.model.keywordsArray.length == $scope.model.keywordsQty )
						return;
					
					$scope.model.keywordsArray.push( 
						{
							keyword: $scope.kw,
							temp: true
						}
					);
					$scope.kw = null;
				};
				
				$scope.handleInput = function ( $event )
				{
					if ( $event.keyCode == 13 )
						return addKeyword();
				};
				
				$scope.removeKeyword = function ( $index )
				{
					$scope.model.keywordsArray.splice( $index, 1 );
				};
			}
		});
		
		// Validation messages
		formlyConfig.setWrapper({
			name: "messages",
			templateUrl: fvurl + "messages.html"
		});
		
	});