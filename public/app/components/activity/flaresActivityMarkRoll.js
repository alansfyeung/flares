/* global angular */
// ===============================
//  flaresActivityMarkRoll.js
//  Mark the roll +
// 	Parade State
// ===============================

var flaresApp = angular.module('flaresActivityMarkRoll', ['flaresBase']);

flaresApp.filter('rollDisplayValue', function(){
    return function(input){
		input = input || '0';
        return input === '0' ? '-' : input;
    }
});

flaresApp.controller('activityRollController', function($scope, $controller, flaresAPI, flaresLinkBuilder){
	// Add some base 
    var thisController = this;
	$controller('baseViewEditController', {$scope: $scope}).loadInto(this);
	
	$scope.state.isRollUnsaved = false;
    $scope.formData = {};
	
	$scope.activity = Object.create($scope.record);
	$scope.rollCount = 0;
	$scope.roll = [];
	$scope.activeRollEntry = 0;
	
	$scope.scrollAttendance = function(rollEntry){
		
	};
	
	$scope.markAttendance = function(){
		$scope.state.isRollUnsaved = true;
		
	};
	
	$scope.rollDisplayValue = function(rollEntry){
		if (rollEntry.locked){
			return "";
		}
		if (~['/'].indexOf(rollEntry)){
			return "present";
		}
		if (~['A'].indexOf(rollEntry)){
			return "awol";
		}
		if (~['L', 'S'].indexOf(rollEntry)){
			return "awol";
		}
		return "pending";
	};
    
    $scope.unlockRollEntry = function(){
        
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
		if ($scope.state.isRollUnsaved){
			var message = 'The roll has been marked but not saved.';
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
                    thisController.convertToDateObjects(['start_date', 'end_date', 'created_at', 'updated_at'], response.data.activity);
					$scope.activity = response.data.activity;
				}
				if (response.data.roll){
					$scope.rollCount = response.data.count || 0;
					$scope.roll = [];
					response.data.roll.forEach(function(value){
						$scope.roll.push({ 
							locked: true, 
							data: value 
						});
					});
					// $scope.roll = response.data.roll;		
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
   