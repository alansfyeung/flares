// ==================================
//   flaresMemberNewSingle.js
//   Add new members
// ==================================

var flaresApp = angular.module('flaresMemberNew', ['flaresBase']);

flaresApp.controller('newSingleController', function($scope, $window, $location, flAPI, flResource){

    //=======================================
    // DATA
    
    // Onboarding Context
    $scope.context = {
		hasOverrides: false
	};
    
	$scope.member = new Member();
    $scope.formData = {};
    
    // END DATA
    //=======================================
	
    // This data should be extracted from reference service
    
	//======================
	// Workflow Screen navigation
    
    var wf = $scope.wf = {};
    
    // Screen state variable
    // Tracks the flow of screens
    // Stage 1: Enter name, DOB, gender; de-dupe, generate regt nums
    // Stage 2: Enter detailed information
    // Stage 3: Confirmation screen, go to view details
	wf.state = {
		stage: 1,
        isSaving: false,
        showOnboardingType: false,
	};

    // Nav actions
    wf.next = function(){ wf.state.stage++ };
    
    // Form actions
	wf.submitNewRecord = submitNewRecord;
	wf.submitDetailedRecord = submitDetailedRecord;
    
    $scope.reset = function(){
        console.log('resetting');
        $scope.member = new Member();
        wf.state.stage = 1;
    };
    
    $scope.cancel = function(){
        $location.path('/');
    };
    
    $scope.viewMember = function(){
        // $location.url(['member', '#!', member.regtNum, 'view', 'details'].join('/'));
        $window.location.href = flResource('member').setFragment([$scope.member.regtNum, 'view', 'details']).build();
    };
    
	
	//==================
	// Fetch reference data for platoons and ranks
	
    flAPI('refData').getAll().then(function(response){
        // Auto-extract
        var extract = ['postings', 'ranks', 'sexes', 'onboardingTypes', 'intakes'];
        angular.forEach(extract, function(key){
            if (response.data.hasOwnProperty(key)){
                $scope.formData[key] = response.data[key];
            }
        });
        
        // Then set defaults for all those values
        if (angular.isArray(response.data.onboardingTypes) && response.data.onboardingTypes.length){
            $scope.context.onboardingType = response.data.onboardingTypes[0];
        }
        if (angular.isArray(response.data.ranks) && response.data.ranks.length){
            $scope.context.newRank = response.data.ranks[0];
        }
        if (angular.isArray(response.data.postings) && response.data.postings.length){
            $scope.context.newPosting = response.data.postings[0];
        }
	});

	//======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.wf.stage > 1){
			if ($scope.wf.stage < 4){
				var message = 'You will lose any unsaved member details.';
				return message;
			}
			if ($scope.wf.stage < 6){
				var message = 'Although members are saved, the onboarding process is not yet complete.';
				return message;
			}
		}
	};
	
	$scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});
    
    //=======================
    // Functions
    
    function submitNewRecord(){
        
        // Cheapo validation
        // TODO: make better
		if($scope.newSimpleStageOne.$invalid){
			wf.state.errorMessage = 'Resolve validation errors (Are required fields are filled and emails are correctly formatted?)';
			return false;
		}

		// Submission
        var payload = {
            context: $scope.context,
            member: $scope.member.data
        };
                
        flAPI('member').post(payload).then(function(response){
            if (response.data.error){
                console.warn(response.data.error);
                return;
            }
            
            $scope.member.lastPersistTime = (new Date).toTimeString();
            if (response.data.id){
                $scope.member.regtNum = response.data.id;	
                $scope.member.isSaved = true;
            }
                        
            wf.next();
            
        }, function(response){
            console.warn('Error: during member add – ', response);
            wf.state.errorMessage = response;
        });
	}

    function submitDetailedRecord(){
		var payload = {
			context: $scope.context,
			member: $scope.member.data
		};
		
        flAPI('member').patch([$scope.member.regtNum], payload).then(function(response){				
            if (response.data.id){
                
                // Detailed save succeeded, so let's activate them
                flAPI('member').patch([$scope.member.regtNum], {
                    member: { is_active: '1' }
                });
                
                $scope.member.lastPersistTime = (new Date()).toTimeString();
                $scope.member.isUpdated = true;	
                console.log('Updated:', $scope.member);
                
                wf.next();
                
            }
        }, function(response){
            console.warn('Error: member add', response);
            wf.state.errorMessage = response;
        });
	}
    
    function skipDetailedRecord(){
        
        // Simply mark as active, then continue
        flAPI('member').patch([$scope.member.regtNum], {
            member: { is_active: '1' }
        }).then(function(){
            wf.next();
        });
        
        
    }
    
    //==================
    // CLASSES
    //==================
    function Member(){
        this.data = {};
    }
	
});