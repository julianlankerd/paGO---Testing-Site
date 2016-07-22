;"use strict";

var pagoUtils = angular.module("pagoUtils", []);

pagoUtils.directive("match", function(){
	return {
		require: "ngModel",
		scope:
		{
			match: "="
		},
		link: function(scope, element, attrs, ctrl)
		{
			ctrl.$validators.match = function(modelValue, viewValue)
			{
				return scope.match == modelValue;
			};
		}
	};
});

pagoUtils.directive("alert", [ "$timeout", function ( $timeout ) {
	return {
		restrict: "E",
		scope: {
			type: "=",
			message: "="
		},
		template: '<div class="alert alert-{{type}}" data-ng-show="message">{{message}}</div>',
		link: function ( scope, elm, attrs, ctrl )
		{
			scope.$watch(
				function () {
					return scope.message;
				},
				function () {
					$timeout(function () {
						scope.message = "";
					}, 6000);
				}
			);

		}
	};
}]);
