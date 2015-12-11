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
	$scope.rollCount = 0;
	$scope.roll = [];
	
    $scope.formData = {};
	$scope.fill = function(){		
		
	};
		
	// ==============
	// Fetch all roll entries for this activity
	if (this.loadWorkflowPath()){
		retrieveActivityRoll();
	}
	
	//==================
	// Fetch reference data for activityTypes and activityNamePresets
    flaresAPI('refData').get(['misc'], {name: 'ROLL_SYMBOLS'}).then(function(response){
		if (response.data.misc && response.data.misc > 0){
			$scope.formData.rollSymbols = response.data.misc[0].value.split(',');
			console.log($scope.formData.rollSymbols);
		}
	});
	
	//======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.state.isEdit()){
			var message = '';
			return message;
		}
	};
    $scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});
	
	// ====================
    // Function decs

    function retrieveActivityRoll(){
		if ($scope.state.path.id){
			flaresAPI('activity').rollFor($scope.state.path.id).getAll().then(function(response){
				if (response.data.activity){
					$scope.activity = response.data.activity;
				}
				if (response.data.roll){
					$scope.rollCount = response.data.count || 0;
					$scope.roll = response.data.roll;		
				}
			}, function(response){
				console.warn(response);
				$scope.state.errorNotLoaded = true;
			});
		}
	}
	
	
});

flaresApp.controller('activityParadeStateController', function(){
	
});
   