/* global angular */
// ===============================
//  flaresActivityMarkRoll.js
//  Mark the roll +
// 	Parade State
// ===============================

var flaresApp = angular.module('flaresActivityMarkRoll', ['flaresBase']);

flaresApp.controller('activityRollController', function($scope, $controller, flaresAPI, flaresLinkBuilder){
	// Add some base 
	$controller('baseViewEditController', {$scope: $scope}).loadInto(this);
	
	$scope.activity = Object.create($scope.record);
    $scope.formData = {};
	
	$scope.fill = function(){
		
		
	};
	
	
	// ==============
	// Fetch all roll entries for this activity

	flaresAPI('activity').rollFor($scope.state.path.id).getAll().then(function(response){
		console.log(response);
	});
	
});

flaresApp.controller('activityParadeStateController', function(){
	
});
   