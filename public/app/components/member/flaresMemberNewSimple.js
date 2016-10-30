// ==================================
//   flaresMemberNew.js
//   Add new members
// ==================================

var flaresApp = angular.module('flaresMemberNew', ['flaresBase']);

flaresApp.controller('newSimpleController', function($scope, $location, flaresAPI, flaresLinkBuilder){

	// Tracks the flow of screens
    // Stage 1: Enter name, DOB, gender; de-dupe, generate regt nums
    // Stage 2: Enter detailed information
    // Stage 3: Confirmation screen, go to view details
	var workflowState = {
		stage: 1,
        isSaving: false,
	};
    
    
    //// WHAT DO
    //// WHAT DO
    //// WHAT DO
    
    // This data should be extracted from reference service
	$scope.formData = {
		onboardingTypes: [],
		sexes: [],
		intakes: [],
		postings: [],
		ranks: []
	}
	
    
    
    //=======================================
    // DATA
    
    // Onboarding Context
    $scope.ctx = {
		hasOverrides: false,
		name: 'newRecruitment',				
		thisYear: (new Date).getFullYear(),
		thisCycle: '1',
		newRank: 'CDTREC',
		newPosting: 'MBR',
		newPlatoon: '3PL',
	};
    
	// New member DTO
	$scope.member = {
        // Dummy data
        data: {
            first_name: 'ALan',
            last_name: 'Yeung',
            sex: 'M'            
        }
        
    };
    
    // END DATA
    //=======================================
	
	//======================
	// Workflow Screen navigation
    
    var wf = $scope.wf = {};
    
    // Screen state variable
    wf.state = workflowState;
    
    // Nav actions
    wf.next = function(){ wf.state.stage++ };
    
    // Form actions
	wf.submitNewRecord = submitNewRecord;
	wf.submitDetailedRecord = submitDetailedRecord;
    
    $scope.reset = function(){
        console.log('resetting');
        $location.url('/');
    };
    
    $scope.cancel = function(){
        $location.path('/');
    };
    
    $scope.viewMember = function(){
        // $location.url(['member', '#!', member.regtNum, 'view', 'details'].join('/'));
        var where = flaresLinkBuilder('member').addFragment([member.regtNum, 'view', 'details']).build();
        $location.url(where);
    };
    
	
	//==================
	// Fetch reference data for platoons and ranks
	
    flaresAPI('refData').getAll().then(function(response){
        // Auto-extract
        var extract = ['postings', 'ranks', 'sexes', 'onboardingTypes', 'intakes'];
        angular.forEach(extract, function(key){
            if (response.data.hasOwnProperty(key)){
                $scope.formData[key] = response.data[key];
            }
        });
        
        // Then set defaults for all those values
        // TODO
        
	});

	//======================
	// Save-your-change niceties
	window.onbeforeunload = function(event){
		if ($scope.workflow.stage > 1){
			if ($scope.workflow.stage < 4){
				var message = 'You will lose any unsaved member details.';
				return message;
			}
			if ($scope.workflow.stage < 6){
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
            context: $scope.ctx,
            member: $scope.member.data
        };
        
        flaresAPI('member').post(payload).then(function(response){
            if (response.data.error){
                console.warn(response.data.error);
                return;
            }
            
            newMember.lastPersistTime = (new Date).toTimeString();
            if (response.data.recordId){
                newMember.regtNum = response.data.recordId;	
                newMember.isSaved = true;
            }
            
        }, function(response){
            console.warn('Error: during member add – ', response);
        });
            
		wf.next();		// Asynchronous
	}

    function submitDetailedRecord(){
		var sw = $scope.wf.state;
        
		var payload = {
			context: $scope.onboardingContext,
			member: $scope.member.data
		};
		
        flaresAPI('member').patch([detailedMember.regtNum], payload).then(function(response){				
            if (response.data.recordId){
                
                // Detailed save succeeded, so let's activate them
                flaresAPI('member').patch([detailedMember.regtNum], {
                    member: { is_active: '1' }
                });
                
                detailedMember.lastPersistTime = (new Date()).toTimeString();
                detailedMember.isUpdated = true;	
                console.log('Updated:', detailedMember);
            }
        }, function(response){
            console.warn('Error: member add', response);
        });
			
		
	}
	
});