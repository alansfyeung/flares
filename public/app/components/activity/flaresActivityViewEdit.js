// =============================
//    flaresActivityNew.js
//    Add a new activity record
// =============================

var flaresApp = angular.module('flaresActivityView', ['flaresBase']);

flaresApp.controller('activityViewEditController', function($scope, $http, $location, flaresAPI, flaresLinkBuilder){
    
    $scope.activity = {};
    $scope.originalActivity = {};
    
    $scope.formData = {};
    $scope.workflow = {
		path: {
			id: 0,
			mode: 'view',		// by default
			tab: 'details'
		},
		isActivityRequested: false,
		isActivityLoaded: false,
		isAsync: false
	};
    
    
    $scope.$watchCollection('workflow.path', function(){
		// Change the URL path if workflow details are updated (e.g. tab click)
		updatePath();
	});
	
	// Read the url
	// get rid of any leading slash
	var path = $location.path();
	var pathFrags = (path.indexOf('/') === 0 ? path.substring(1) : path).split('/');		
	if (pathFrags.length > 0 && pathFrags[0].length > 0){
		$scope.workflow.isActivityRequested = true;
		$scope.workflow.path.id = pathFrags[0];
		$scope.workflow.path.mode = pathFrags[1] ? pathFrags[1] : 'view';
		$scope.workflow.path.tab = pathFrags[2] ? pathFrags[2] : 'details';
		retrieveActivity();
	}
    
    //==================
	// Fetch reference data for activityTypes and activityNamePresets
    
    flaresAPI.refData.get(['activity']).then(function(response){
		if (response.data.types){
			$scope.formData.activityTypes = response.data.types;
		}
        if (response.data.presets){
			$scope.formData.namePresets = response.data.presets;
		}
	});
    

    //======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.workflow.isEdit()){
			var message = 'You are editing this member record, and will lose any unsaved changes.';
			return message;
		}
	};
    $scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});
    
    
    // ====================
    // Function decs

    function updatePath(){
		var swp = $scope.workflow.path;
		if (swp.id){
			$location.path([swp.id, swp.mode, swp.tab].join('/'));
		}
	};
    function retrieveActivity(){
		if ($scope.workflow.path.id){
			flaresAPI.activity.get([$scope.workflow.path.id]).then(function(response){
				// Process then store in VM
				processActivityRecord(response.data);
				$scope.workflow.isActivityLoaded = true;
				
				// activate the correct tab
				$("[bs-show-tab][aria-controls='" + $scope.workflow.path.tab + "']").tab('show');
				
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
			console.warn('Member ID not specified');
		}
	};
    function processActivityRecord(activity){
		// Convert dates to JS objects
		angular.forEach(['start_date', 'end_date', 'created_at', 'updated_at'], function(datePropKey){
			if (this[datePropKey]){
				var timestamp = Date.parse(this[datePropKey]);
				if (!isNaN(timestamp)){
					this[datePropKey] = new Date(this[datePropKey]);
				}
				else {
					this[datePropKey] = null;
				}
			}
		}, activity);
		
		$scope.activity = activity;
		$scope.originalActivity = angular.extend({}, activity);
	};
    
});