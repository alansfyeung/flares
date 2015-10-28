// =============================
//    flaresActivityNew.js
//    Add a new activity record
// =============================

var flaresApp = angular.module('flaresActivityView', ['flaresBase']);

flaresApp.controller('activityViewEditController', function($scope, $location, $controller, flaresAPI, flaresLinkBuilder){
    
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
    
    $scope.delete = function(){
        deleteActivity();
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
                if (response.data.activity){
                    processActivityRecord(response.data.activity);
                    $scope.workflow.isActivityLoaded = true;
                }
                else {
                    console.warn('Activity data not loaded');
                }
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
    function deleteActivity(){
        
    }
});

flaresApp.controller('rollBuilderController', function($scope, flaresAPI){
	
    $scope.roll = []; 
    $scope.memberList = []; 
    $scope.formData = {};
    
    $scope.toggleRoll = function(member){
        member.isRoll = !member.isRoll;
    };
    
    retrieveRefData();
    retrieveMembers();
    
    // $scope.$watch('$parent.activity.acty_id', function(){
    	// retrieveActivityNominalRoll();
    // });
    

    
    // ======================
    // Function decs

    function retrieveRefData(){
        flaresAPI.refData.getAll().then(function(response){
            if (response.data.platoons){
                $scope.formData.platoons = response.data.platoons;
                $scope.formData.platoons.unshift({abbr: '', name: 'Any platoon'});
            }
            if (response.data.ranks){
                $scope.formData.ranks = response.data.ranks;
                $scope.formData.ranks.unshift({abbr: '', name: 'Any rank'});
            }
        });
    }
    
    function retrieveMembers(){
        flaresAPI.member.getAll().then(function(response){
            if (response.data.members){
                for (var x in response.data.members){
                    $scope.memberList.push({
                        isRoll: false,                // is it on the roll? 
                        isRollBlank: false,         // If this record can be deleted. blank = deletable
                        data: response.data.members[x]
                    });
                }
                retrieveActivityNominalRoll();
                console.log('Finished loading members, num loaded: ' + response.data.members.length);
            }
        });
    }
    
    function retrieveActivityNominalRoll(){
        if ($scope.$parent.activity.acty_id){
            flaresAPI.activity.get([$scope.$parent.activity.acty_id, 'roll']).then(function(response){
                if (response.data.roll){
                    mapToMemberList($scope.memberList, response.data.roll);
                }
                console.log('Mapped to member list');
            });
        }
    }
    
    function mapToMemberList(members, roll){
        // For each roll entry, find the corresponding member
        for (var x in roll){
            for (var i in members){
                var member = members[i];
                if (member.data.regt_num === roll[x].regt_num){
                    member.isRoll = true;
                    member.isRollBlank = roll[x].is_deletable;
                    break;
                }
            }
        }
    }

});
