/* global angular */
// ===============================
//  flaresActivityMarkRoll.js
//  Mark the roll +
// 	Parade State +
//  AWOL viewer
// ===============================

var flaresApp = angular.module('flaresActivityMarkRoll', ['flaresBase']);

flaresApp.filter('rollDisplayValue', function(){
    return function(input){
		input = input || '0';
        if (input === '0'){
            return '--';
        }
        if (~['/'].indexOf(input)){
			return "Present";
		}
		if (~['A'].indexOf(input)){
			return "AWOL";
		}
		if (~['L'].indexOf(input)){
			return "Leave";
		}
		if (~['S'].indexOf(input)){
            return "Sick";
        }
        return 'Unknown';
    }
});

flaresApp.controller('activityRollController', function($scope, $controller, flaresAPI, flaresLinkBuilder){
	// Add some base 
    var thisController = this;
	$controller('baseViewEditController', {$scope: $scope}).loadInto(this);
	
    $scope.refData = {};
	$scope.activity = Object.create($scope.record);
	$scope.rollCount = 0;
	$scope.roll = [];
	$scope.activeRollEntry = 0;
	$scope.state.isRollUnsaved = false;
    
    $scope.breadcrumbTabTitle = function(){
        switch ($scope.state.path.tab){
            case 'paradestate':
                return 'Parade State';
            case 'markroll':
                return 'Mark roll';
            case 'awols':
                return 'Review AWOLs';
        }
    };
    
    $scope.state.isMarkRoll = function(){
        return $scope.state.path.tab == 'markroll';
    }
    $scope.state.isParadeState = function(){
        return $scope.state.path.tab == 'paradestate';
    }    
    $scope.showMarkRoll = function(){
        $scope.state.path.tab = 'markroll';
    };
    $scope.showParadeState = function(){
        $scope.state.path.tab = 'paradestate';
    };
		
	// ==============
	// Fetch all roll entries for this activity
	if (this.loadWorkflowPath('fill', 'markroll')){
		retrieveActivityRoll();
	}
    
    // Get the platoons reference data
    retrieveRefData();
    
    //======================
	// Actions menu
    $scope.actions = {
        editActivity: function(){
            var frag = [$scope.activity.acty_id, 'edit', 'details'];
            return flaresLinkBuilder('activity').retrieve().hash(frag).getLink();
        },
        paradeState: function(){
            var frag = [$scope.activity.acty_id, 'fill', 'paradestate'];
            return flaresLinkBuilder('activity').roll().hash(frag).getLink();
        },
        reviewAwols: function(){
            var frag = [$scope.activity.acty_id, 'view', 'awol'];
            return flaresLinkBuilder('activity').awol().hash(frag).getLink();
        }
    };
    
   
    //==================
	// Fetch reference data for activityTypes and activityNamePresets
    flaresAPI('refData').get(['misc'], {name: 'ROLL_SYMBOLS'}).then(function(response){
		if (response.data.misc && response.data.misc.length > 0){
            var symbols = response.data.misc[0].value.split(',');
            RollEntrySymbolScroller.prototype.symbols = symbols;
		}
	});
	
    
	// ====================
    // Function decs

    function retrieveActivityRoll(){
		if ($scope.state.path.id){
			flaresAPI('activity').rollFor($scope.state.path.id).getAll().then(function(response){
				if (response.data.activity){
                    $scope.util.convertToDateObjects(['start_date', 'end_date', 'created_at', 'updated_at'], response.data.activity);
					$scope.activity = response.data.activity;
				}
				if (response.data.roll){
					$scope.rollCount = response.data.count || 0;
					$scope.roll = [];
					response.data.roll.forEach(function(value){
						$scope.roll.push(new RollEntry(value));
					});
					// $scope.roll = response.data.roll;		
				}
			}, function(response){
				console.warn(response);
				$scope.state.errorNotLoaded = true;
			});
		}
	}
    
    function RollEntry(rollEntryData){
        this.locked = true;
        this.saving = false;
        this.data = rollEntryData;
        this.scroller = new RollEntrySymbolScroller(this.data.recorded_value);
    }
    RollEntry.prototype.unlockRollEntry = function(){
        this.locked = false;
    };
    RollEntry.prototype.scrollAttendance = function(){
        this.scroller.next();
	};
	RollEntry.prototype.markAttendance = function(){
        if (this.scroller.originalSymbol !== this.scroller.selectedSymbol){
            $scope.state.isRollUnsaved = true;
            this.saving = true;
            
            // Save the record
            var self = this;
            var actyId = $scope.activity.acty_id;
            var attId = this.data.att_id;
            var payload = {
                'attendance': {
                    recorded_value: this.scroller.selectedSymbol
                }
            };
            flaresAPI('activity').rollFor(actyId).patch([attId], payload).then(function(){
                $scope.state.isRollUnsaved = false;
                self.saving = false;
                // bind it to data model
                self.data.recorded_value = self.scroller.selectedSymbol;
                // set this as the new OG
                self.scroller.originalSymbol = self.scroller.selectedSymbol;
            });
        }
        
        this.locked = true;
	};
    RollEntry.prototype.cancelAttendanceChange = function(){
        this.scroller.selectedSymbol = this.scroller.originalSymbol;
        this.locked = true;
    };
    
    function RollEntrySymbolScroller(initialSymbol){
        this.originalSymbol = initialSymbol || '0';
        this.selectedSymbol = initialSymbol || '0';          // by default
        this.selectedSymbolIndex = -1; 
    };
    RollEntrySymbolScroller.prototype.symbols = [];
    RollEntrySymbolScroller.prototype.next = function(){
        if (this.symbols.length > 0){
            var maybeIndex = this.symbols.indexOf(this.selectedSymbol);
            var nextIndex = 0;
            if (maybeIndex > -1){
                // Go to the next symbol
                nextIndex = maybeIndex + 1;
                nextIndex = nextIndex >= this.symbols.length ? 0 : nextIndex;
                this.selectedSymbol = this.symbols[this.selectedSymbolIndex];
            }
            
            this.selectedSymbolIndex = nextIndex;
            this.selectedSymbol = this.symbols[nextIndex];
        }
    };
	
    function retrieveRefData(){
        flaresAPI('refData').getAll().then(function(response){
            if (response.data.platoons){
                $scope.refData.platoons = response.data.platoons;
            }
        });
    }
    
});

flaresApp.controller('activityParadeStateController', function($scope){
	//======================
    // This controller gets really down and dirty with crunching the parade
    // numbers and statistics
	//======================
    
    $scope.refData = $scope.refData || {};      // should inherit from parent scope.
    $scope.refData.relevantPlatoons = function(){
        var expected = ['1PL', '2PL', '3PL', 'PNR', 'HQ'];
        if ($scope.refData.platoons){
            return $scope.refData.platoons.filter(function(platoon){
                return ~expected.indexOf(platoon.abbr);
            });            
        }
        return [];
    };
    
    $scope.postedStrength = 0;          // todo: how to calculate this?
    $scope.totalNumbers = {};
    $scope.numbers = {};
    $scope.nonPresent = {};
    
    $scope.awolMembers = [];
    
    //============
    // Fetch posted strength
    // +++ placeholder to fetch posted strength
    
    //============
    // Listen for changes that would trigger roll counts refresh
    $scope.$watchCollection('roll', function(){
        $scope.totalNumbers = getTotalNumbers();
    });
    $scope.$watch('roll.data.recorded_value', function(){
        $scope.totalNumbers = getTotalNumbers();
    }, true);
    
    // On tab changes
    $scope.$watch('state.path.tab', function(newVal){
        if (newVal === 'paradestate'){
            $scope.numbers = getPlatoonNumbers();
            $scope.nonPresentList = getPlatoonNonPresentListings();
        }
        if (newVal === 'awols'){
            $scope.awolMembers = getAwolMembers();
        }
    });

    
    //============
    // Functions
    
    function getNumbers(roll){
        var numbers = {};
        roll.forEach(function(rollEntry){
            switch (rollEntry.data.recorded_value){
                case '/':
                    numbers.present = numbers.present + 1 || 1;
                    break;
                case 'L':
                    numbers.leave = numbers.leave + 1 || 1;
                    numbers.leaveOrSick = numbers.leaveOrSick + 1 || 1;
                    break;
                case 'A':
                    numbers.awol = numbers.awol + 1 || 1;
                    break;
                case 'S':
                    numbers.sick = numbers.sick + 1 || 1;
                    numbers.leaveOrSick = numbers.leaveOrSick + 1 || 1;
                    break;
                default:
                    numbers.other = numbers.other + 1 || 1;
            }
        });
        numbers.posted = roll.length;
        return numbers;
    };
    
    function getTotalNumbers(){
        var roll = $scope.roll;
        return getNumbers(roll);
    }
    
    function getPlatoonNonPresentListings(){
        var nonPresent = {};
        $scope.roll.forEach(function(rollEntry){
            var val = rollEntry.data.recorded_value;
            if (~['A', 'L', 'S'].indexOf(val)){
                nonPresent[val] = nonPresent[val] || [];
                nonPresent[val].push([rollEntry.data.last_name.toUpperCase, rollEntry.data.first_name].join(' ,'));       // whack in a display string    // .+' ('+rollEntry.data.current_platoon.new_platoon+')'
            };
        });
        return nonPresent;
    }
    
    function getPlatoonNumbers(){
        var roll = $scope.roll;
        var numbers = {};
        if ($scope.refData.platoons){
            $scope.refData.platoons.forEach(function(platoon){
                var rollEntriesForPlatoon = roll.filter(function(rollEntry){
                    return rollEntry.data.member.current_platoon.new_platoon === platoon.abbr;
                });
                if (rollEntriesForPlatoon.length > 0){
                    var numbersForPlatoon = getNumbers(rollEntriesForPlatoon);
                    numbers[platoon.abbr] = numbersForPlatoon;
                }
            });
        }        
        return numbers;
    }
    
    function getAwolMembers(){
        var roll = $scope.roll;
        return roll.filter(function(rollEntry){
            return rollEntry.data.recorded_value === 'A';
        });
    }
    
    
});
   