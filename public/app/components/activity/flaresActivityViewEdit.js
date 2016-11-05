// ===============================
//    flaresActivityViewEdit.js
//    View/Edit record
// ===============================

var flaresApp = angular.module('flaresActivityView', ['flaresBase']);

flaresApp.controller('activityViewEditController', function($scope, $window, $location, $controller, flAPI, flResource){
    
    // This seems dirty, attaching a controller straight to the $scope
    // But it seems to be the best way of sharing this parentController reference to 
    // child controllers
    var thisController = this;
    $controller('baseViewEditController', {$scope: $scope}).loadInto(this);
    
    // angular.extend(thisController, $controller('baseViewEditController', {$scope: $scope})); 
    // $scope.state = Object.create(thisController.state);     // set parent workflow object as proto
    
    
    $scope.activity = Object.create($scope.record);
    $scope.originalActivity = Object.create($scope.originalRecord);
    $scope.activityRollStats = {};
    $scope.formData = {};
    
    $scope.breadcrumbTabTitle = function(){
        switch ($scope.state.path.tab){
            case 'details':
                return 'Details';
            case 'rollbuilder':
                return 'Roll preparation';
            case 'permission':
                return 'Permission notes';
        }
    };
    
    $scope.edit = function(){
		var sw = $scope.state;
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
    $scope.saveEdit = $scope.edit;      // point to the same place
	$scope.cancelEdit = function(){
		if ($scope.state.isLoaded){
			$scope.activity = angular.extend(Object.create($scope.record), $scope.originalActivity);
			$scope.state.path.mode = 'view';
			return;
		}
		console.warn('Cannot cancel - member record was never loaded');
	};
    
    $scope.delete = function(){
        deleteActivity();
    };
	
    // ============
	// Read the url
	if (this.loadWorkflowPath()){
		retrieveActivity();
	}
    
    
    //======================
	// Actions menu
    $scope.actions = {
        markRoll: function(){
            var frag = [$scope.activity.acty_id, 'fill', 'markroll'];
            return flResource('activity').roll().hash(frag).getLink();
        },
        paradeState: function(){
            var frag = [$scope.activity.acty_id, 'review', 'paradestate'];
            return flResource('activity').roll().hash(frag).getLink();
        },
        reviewAwols: function(){
            var frag = [$scope.activity.acty_id, 'view', 'awol'];
            return flResource('activity').awol().hash(frag).getLink();
        }
    };
    
    //==================
	// Fetch reference data for activityTypes and activityNamePresets
    
    flAPI('refData').get(['activity']).then(function(response){
		if (response.data.types){
			$scope.formData.activityTypes = response.data.types;
		}
	});
    

    //======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.state.isEdit()){
			var message = 'You are editing this activity record, and will lose any unsaved changes.';
			return message;
		}
	};
    $scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});
    
    
    // ====================
    // Function decs

    function retrieveActivity(){
		if ($scope.state.path.id){
			flAPI('activity').get([$scope.state.path.id]).then(function(response){
				// Process then store in VM
                if (response.data.activity){
                    processActivityRecord(response.data.activity);
                    $scope.state.isActivityLoaded = true;
                }
                else {
                    console.warn('Activity data not loaded');
                }
			}, function(response){
				console.warn(response);
                $scope.state.errorNotLoaded = true;
			});
		}
		else {
			console.warn('Activity ID not specified');
		}
	}
    function processActivityRecord(activity){
        $scope.util.convertToDateObjects(['start_date', 'end_date', 'created_at', 'updated_at'], activity);
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
			flAPI('activity').patch([$scope.activity.acty_id], payload).then(function(response){
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

flaresApp.controller('rollBuilderController', function($scope, $filter, $controller, $timeout, flAPI){
    
    // $scope.roll = []; 
    var rollRefreshPromise;
    $scope.lastError = {};
    $scope.memberList = []; 
    
    // Filter
    $scope.filtering = {
        filters: [],
        terms: '',                     // name filter
        activeFilterIndex: '0',     // category filter
        isCategoryFilter: true,
        hasFilterFired: false,
        showing: 0
    };
    $scope.filtering.switchFilterMethod = function(){
        $scope.filtering.terms = '';
        $scope.filtering.isCategoryFilter = !$scope.filtering.isCategoryFilter;
        
        // Focus on the textbox
        var containerName = $scope.filtering.isCategoryFilter ? 'bycategory' : 'byname';
        angular.element('[name=filter-' + containerName + '] .form-control')[0].focus();
    };
    $scope.filtering.runFilter = function(){
        var activeFilter = $scope.filtering.filters[0];
        if (this.activeFilterIndex > 0 && this.activeFilterIndex < $scope.filtering.filters.length){
            activeFilter = $scope.filtering.filters[this.activeFilterIndex];
        }
        $scope.memberList.forEach(function(member){
            // Run the active category filter
            if (Member.prototype.hasOwnProperty(activeFilter.handler)){
                member[activeFilter.handler](activeFilter.value);
            }
            // Run the term filter
            if (Member.prototype.hasOwnProperty('nameFilter')){
                var terms = $scope.filtering.terms.split(/\s+/);
                if (terms.length > 0 && terms[0]){      // check terms[0] is not empty string
                    member.nameFilter(terms);                
                }
            }
        });
        this.showing = $filter('filter')($scope.memberList, {visible: true}).length;
        this.hasFilterFired = true;
    };

    
    // Quick selection
    $scope.quickSelecting = {
        quickSelections: [],
        activeQuickSelectionIndex: '0',
    };
    $scope.quickSelecting.runQuickSelection = function(){
        
    };
    
    
    $scope.toggleRollSelection = function(member){   
        // console.log(member);
        
        if (member.isMarked()){      // then don't
            return;
        }
        
        member.onRoll = !member.onRoll;
        if (member.delta === 0){
            member.delta = ( member.onRoll ? 1 : -1 );
        }
        else {
            // Delta'd but not yet submitted - revert.
            if ((member.onRoll && member.delta < 0) || (!member.onRoll && member.delta > 0)){
                member.delta = 0;
            }
        }
    };
    
    // Todo: write a last-event timeout which executes processRollDelta()
    // e.g. after 2 sec inactivity

    $scope.bumpRollRefreshTimer = function(){
        $timeout.cancel(rollRefreshPromise);
        rollRefreshPromise = $timeout(function(){
            var activityId = $scope.$parent.activity.acty_id;
            if (activityId){
                processRollDelta(activityId);
                retrieveActivityNominalRoll(activityId).then(function(response){
                    if (response.data.roll){
                        $scope.memberList = mapToMemberList(response.data.roll, $scope.memberList);
                    }
                }, function(){ console.warn('Nominal roll not retrieved') });
            }
        }, 2000);
    };
    
    retrieveRefData();

    retrieveMembers().then(function(response){
        var activityId = $scope.$parent.activity.acty_id;
        if (response.data.members){
            for (var x in response.data.members){
                var newMember = new Member(response.data.members[x]);
                $scope.memberList.push(newMember);
            }
            if (activityId){
                retrieveActivityNominalRoll(activityId).then(function(response){
                    if (response.data.roll){
                        $scope.memberList = mapToMemberList(response.data.roll, $scope.memberList);
                    }
                }, function(){
                    console.warn('Nominal roll not retrieved');
                });
                console.log('Finished loading members, num loaded: ' + response.data.members.length); 
            }
        }
    });
    
    // $scope.$watch('$parent.activity.acty_id', function(){
    	// retrieveActivityNominalRoll();
    // });
    
    
    
    // ======================
    // Function decs
    
    // ----------------
    //  Member object
    // ----------------
    function Member(memberData){
        this.visible = true;
        this.onRoll = false;
        this.roll = null;
        this.delta = 0;
        this.data = memberData;
    }
    Member.prototype.associateRoll = function(rollEntry){
        this.onRoll = true;
        this.roll = rollEntry;
    };
    Member.prototype.displayStatus = function(){
        // Todo: return if they are on leave during this period
        if (this.roll){
            if (this.roll.recorded_value !== '0'){
                var markedDateFormat = 'dd MMM';
                if (this.roll.updated_at.getFullYear() !== (new Date).getFullYear()){
                    markedDateFormat += ' yyyy';
                }
                var markedDate = $filter('date')(this.roll.updated_at, markedDateFormat);
                return 'Marked [ '+this.roll.recorded_value+' ] on ' + markedDate; 
            }
        }
        return 'Ready';
    };
    Member.prototype.isMarked = function(){
        return (this.roll && this.roll.recorded_value !== '0');
    };
    
    // Define a whole bunch of self-filter-handlers
    Member.prototype.defilter = function(){
        this.visible = true;
    };
    Member.prototype.nameFilter = function(terms){      // expect typeof terms == Array
        var visible = false;
        var firstname = this.data.first_name.toLowerCase();
        var lastname = this.data.last_name.toLowerCase();
        terms.forEach(function(term){
            term = term.toLowerCase();
            if (~firstname.indexOf(term)){
                visible = true;
            };
            if (~lastname.indexOf(term)){
                visible = true;
            }
        });
        this.visible = visible;
    };
    Member.prototype.selectedFilter = function(){
        this.visible = !!this.onRoll;
    };
    Member.prototype.unselectedFilter = function(){
        this.visible = !this.onRoll;
    };
    Member.prototype.platoonFilter = function(platoon){
        this.visible = this.data.current_platoon && this.data.current_platoon.platoon === platoon;
    }
    Member.prototype.rankFilter = function(rank){
        this.visible = this.data.current_rank && this.data.current_rank.rank === rank;
    }
    
    // --------------------------------
    //  Filter & quick select objects
    // --------------------------------
    function Filter(type, value, desc, handler){  
        this.type = type || 'other';
        this.value = value || null;
        this.desc = desc || 'All';
        this.handler = handler || 'defilter';        // as string representing the Member.prototype.xxxxFilter
    }
    function QuickSelection(type, value, desc, handler){
        this.type = type || 'other';
        this.value = value || null;
        this.desc = desc || 'All';
        this.handler = handler || 'defilter';        // as string representing the Member.prototype.xxxxFilter
    }

    function retrieveRefData(){
        flAPI('refData').getAll().then(function(response){
            if (response.data.platoons){
                $scope.formData.platoons = response.data.platoons;
            }
            if (response.data.ranks){
                $scope.formData.ranks = response.data.ranks;
            }
            
            $scope.filtering.filters = buildFilters();
            // $scope.filtering.activeFilterIndex = '0';        // Set an initial one
        });
    }
    
    function retrieveMembers(){
        return flAPI('member').getAll();
    }
    
    function retrieveActivityNominalRoll(activityId){
        return flAPI('activity').rollFor(activityId).getAll();
    }

    function mapToMemberList(roll, members){
        // For each roll entry, find the corresponding member
        // for (var x in roll){
        roll.forEach(function(rollEntry){
            members.forEach(function(member, index){
                if (member.data.regt_num === rollEntry.regt_num){
                    $scope.$parent.util.convertToDateObjects(['created_at', 'updated_at'], rollEntry);
                    member.associateRoll(rollEntry);
                    return true;
                }
            });
        });
        return members;
    }
    
    // Periodically read the roll deltas and process as required
    function processRollDelta(activityId){
        var deletes = [];
        var adds = [];
        $scope.memberList.forEach(function(member, index){
            if (member.delta < 0){
                // This is delete delta
                if (member.roll){
                    deletes.push(member.roll.att_id);
                    member.delta = 0;                    
                }
            }
            if (member.delta > 0){
                // This is an add delta
                adds.push({regt_num: member.data.regt_num});
                member.delta = 0;
            }
        });
        
        if (adds.length > 0){
            var payloadAdd = {
                attendance: adds
            };
            flAPI('activity').rollFor(activityId).post(payloadAdd).then(function(response){
                $scope.lastError = response.data.error;
                console.log('added', response);
            });            
        }
        
        deletes.forEach(function(rollId){
            flAPI('activity').rollFor(activityId).delete(rollId).then(function(response){
                $scope.lastError = response.data.error;
                console.log('deleted', response);
            });
        });
    }
    
    // Filters and quick search
    function buildFilters(){
        var filters = [];
        // Add some of our own
        filters.push(new Filter());
        filters.push(new Filter('other', null, 'Not already on the roll', 'unselectedFilter'));
        filters.push(new Filter('other', null, 'Already on the roll', 'selectedFilter'));
        // Add platoons
        if ($scope.formData.platoons){      
            $scope.formData.platoons.forEach(function(pl){
                filters.push(new Filter('platoon', pl.abbr, 'Platoon: ' + pl.name, 'platoonFilter'));
            });
        }
        // Add ranks
        if ($scope.formData.ranks){
            $scope.formData.ranks.forEach(function(rank){
                filters.push(new Filter('rank', rank.abbr, 'Rank: ' + rank.name, 'rankFilter'));
            });
        }
        return filters;
    };

});


flaresApp.controller('permissionController', function($scope, flAPI){
    
    
});