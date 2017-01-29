// ==================================
//   flaresMemberNew.js
//   Add new members
// ==================================

var flaresApp = angular.module('flaresDecoration', ['flaresBase']);

flaresApp.controller('newDecorationController', function($scope, $window, $location, $filter, flAPI, flResource){




    //=======================================
    // DATA
    
    // Onboarding Context
    
	// New member DTO
	$scope.dec = new Decoration();
    
    // END DATA
    //=======================================
    
    var state = {
        stage: 1,       // Leave deactivated but there's only a single stage
        totalStages: 1,
        isSaving: false,
        submitPreference: 1         // 1 for submit and view; 2 for submit and another
    };

    // This data should be extracted from reference service
	var formData = {
		decorationTiers: []             // TODO
	};
	
	//======================
	// Workflow Screen navigation
    
    $scope.wf = {};
    
    // Nav actions
    $scope.wf.next = function(){ state.stage++ };
    
    // Form actions
	$scope.wf.submitData = function(){
        submitData().then(function(){
            if ($scope.state.submitPreference === 2){
                $window.location.reload();           
            }
            else {
                $window.location.href = flResource('decoration').addFragment([$scope.dec.id, 'view', 'details']).build();                
            }
        });
    };
    
	// End Workflow Screen navigation
    //======================
    
    $scope.cancel = function(){
        $location.path('/');
    };
    
    $scope.setCommencementToday = function(){
        if ($scope.dec.data){            
            $scope.dec.data.date_commence = new Date();
        }
    };
    

	//==================
	// Fetch reference data for platoons and ranks
	
    flAPI('refData').getAll().then(function(response){
        // Auto-extract
        var extract = ['decorationTiers'];
        angular.forEach(extract, function(key){
            if (response.data.hasOwnProperty(key)){
                wf.formData[key] = response.data[key];
            }
        });
        
        // Then set defaults for all those values
        // TODO
        
	});

    // End reference data
	//======================
    
	//======================
	// Save-your-change niceties
    /* // TEMPORARILY COMMENTED
	window.onbeforeunload = function(event){		
        if (state.stage < 2){
            var message = 'You will lose any unsaved decoration details.';
            return message;
        }
	};
	
	$scope.$on('$destroy', function() {
		delete window.onbeforeunload;
	});
    */
    
    //=======================
    // Functions
    
    function submitData(){
        
        $scope.state.isSaving = true;
        var dec = $scope.dec;

        // Cheapo validation
        // TODO: make better
		if($scope.decorationData.$invalid){
			state.errorMessage = 'Resolve validation errors (Are all required fields filled out?)';
			return false;
		}

		// Need to flatten dates... thanks Laravel/Carbon... (sarcasm)
        var payload = {
            decoration: angular.extend({}, dec.data, {
                date_commence: $filter('date')(dec.data.date_commence, "yyyy-MM-dd"),
                date_conclude: $filter('date')(dec.data.date_conclude, "yyyy-MM-dd")
            })
        };
        
        // Check conclusion date form flag
        if (dec.hasNoConclusionDate){
            dec.data.date_conclude = null;
        }
        
        return flAPI('decoration').post(payload).then(function(response){
            if (response.data.error){
                console.warn(response.data.error);
                return;
            }
            
            dec.lastPersistTime = (new Date).toTimeString();
            if (response.data.id){
                dec.id = response.data.id;	
                dec.isSaved = true;
            }
            
            return dec.id;
            
        }).catch(function(response){
            console.warn('Error: during decoration add – ', response);
            state.errorMessage = angular.isObject(response) ? JSON.stringify(response) : response;
        }).finally(function(){
            $scope.state.isSaving = false;
        });
	}
    
    //=====================
    // Classes
    
    function Decoration(){
        this.id = null;
        this.hasNoConclusionDate = true;
        this.data = {
            // tier: 'A',
            // name: 'Comd AAC Commendation (Gold)',
            // desc: 'Awarded for great courage initiative and teamwork',
            // // date_commence: new Date('2006-01-01T00:00:00Z+10:00'),
            // date_commence: new Date('2006-01-01'),
            // authorized_by: 'HQ AAC'
        };
    }
    

});




