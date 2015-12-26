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
	
	$scope.state.isRollUnsaved = false;
    $scope.formData = {};
	
	$scope.activity = Object.create($scope.record);
	$scope.rollCount = 0;
	$scope.roll = [];
	$scope.activeRollEntry = 0;
    
    $scope.state.path.mode = 'fill';        // Always fill, for markRoll
    
    $scope.showParadeState = function(){
        $scope.state.path.tab = 'paradestate';
    };
    $scope.showMarkRoll = function(){
        $scope.state.path.tab = 'markroll';
    };
    
		
	// ==============
	// Fetch all roll entries for this activity
	if (this.loadWorkflowPath('fill', 'markroll')){
		retrieveActivityRoll();
	}
	
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
    
    
    //==================
	// Fetch reference data for activityTypes and activityNamePresets
    flaresAPI('refData').get(['misc'], {name: 'ROLL_SYMBOLS'}).then(function(response){
		if (response.data.misc && response.data.misc.length > 0){
            var symbols = response.data.misc[0].value.split(',');
			// $scope.formData.rollSymbols = symbols;
            RollEntrySymbolScroller.prototype.symbols = symbols;
		}
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
        $scope.state.isRollUnsaved = true;
        this.scroller.next();
	};
	RollEntry.prototype.markAttendance = function(){
        if (this.scroller.originalSymbol !== this.scroller.selectedSymbol){
            this.saving = true;
            
            // bind it to data model
            this.data.recorded_value = this.scroller.selectedSymbol;
            
            // Save the record
            var self = this;
            var actyId = $scope.activity.acty_id;
            var attId = this.data.att_id;
            var payload = {
                'attendance': this.data
            };
            flaresAPI('activity').rollFor(actyId).patch([attId], payload).then(function(){
                $scope.state.isRollUnsaved = false;
                self.saving = false;
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
	
});

flaresApp.controller('activityParadeStateController', function(){
	
});
   