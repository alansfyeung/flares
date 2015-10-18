// =============================
//    flaresActivityNew.js
//    Add a new activity record
// =============================

var flaresApp = angular.module('flaresActivityView', ['flaresBase']);

flaresApp.controller('activityViewEditController', function($scope, $http, $location, $controller, flaresAPI, flaresLinkBuilder){
    
    // Add some base 
    var veController = this;
    angular.extend(veController, $controller('baseViewEditController', {$scope: $scope})); 
    
    $scope.workflow = Object.create(veController.workflow);     // set parent workflow object as proto
    
    $scope.activity = Object.create($scope.record);
    $scope.originalActivity = Object.create($scope.originalRecord);
    $scope.activityRollStats = {};
    
    $scope.formData = {};
    
    $scope.edit = function(){
		var sw = $scope.workflow;
		if (sw.isView()){
			// If in view mode, toggle to Edit mode
			sw.path.mode = 'edit';
			return;
		}
		if (sw.isEdit()){
			// Save the changes
			// send back to view mode
			updateActivityRecord();
			sw.path.mode = 'view';
		}
	};
	$scope.cancelEdit = function(){
		if ($scope.workflow.isLoaded){
			$scope.activity = angular.extend(Object.create($scope.record), $scope.originalActivity);
			$scope.workflow.path.mode = 'view';
			return;
		}
		console.warn('Cannot cancel - member record was never loaded');
	};
	
	
	// Read the url
	if (veController.loadWorkflowPath()){
		retrieveActivity();
	}
    
    //==================
	// Fetch reference data for activityTypes and activityNamePresets
    
    flaresAPI.refData.get(['activity']).then(function(response){
		if (response.data.types){
			$scope.formData.activityTypes = response.data.types;
		}
	});
    

    //======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.workflow.isEdit()){
			var message = 'You are editing this activity record, and will lose any unsaved changes.';
			return message;
		}
	};
    $scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});
    
    
    
    // ================
    // Filling in dummy data
    
    $scope.activityRollStats = {
        Total: 20,
        Unmarked: 17,
        Leave: 2,
        Sick: 1
    };
    
    
    
    
    // ====================
    // Function decs

    function retrieveActivity(){
		if ($scope.workflow.path.id){
			flaresAPI.activity.get([$scope.workflow.path.id]).then(function(response){
				// Process then store in VM
				processActivityRecord(response.data);
				$scope.workflow.isActivityLoaded = true;
				
                // Load the roll stats
                
                
			}, function(response){
				if (response.status == 404){
					$scope.activity.errorNotFound = true;
				}
				else {
					$scope.activity.errorServerSide = true;
				}
			});
		}
		else {
			console.warn('Activity ID not specified');
		}
	}
    function processActivityRecord(activity){
        veController.convertToDateObjects(['start_date', 'end_date', 'created_at', 'updated_at'], activity);
		$scope.activity = activity;
		$scope.originalActivity = angular.extend(Object.create($scope.record), activity);
	}
    function updateActivityRecord(){
		var hasChanges = false;
		var payload = {
			activity: {}
		};	
		angular.forEach($scope.activity, function(value, key){
			if ($scope.originalActivity[key] !== value){
				// Value has changed
				hasChanges = true;
				payload.activity[key] = value;
			}
		});
		if (hasChanges){
			// $http.patch('/api/member/'+$scope.member.regt_num, payload).then(function(response){
			flaresAPI.activity.patch([$scope.activity.acty_id], payload).then(function(response){
				console.log('Save successful');
				$scope.originalActivity = angular.extend(Object.create($scope.originalRecord), $scope.activity);
				
			}, function(response){
				// Save failed. Why?
				alert('Warning: Couldn\'t save this record. Check your internet connection?');
				console.warn('Error: record update', response);
			});
		}
	}
    function retrieveActivityNominalRoll(){
        if ($scope.activity.acty_id){
            flaresAPI.activity.get([$scope.activity.acty_id, 'roll']).then(function(){
                
            });
        }
    }
    
});