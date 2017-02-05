// ==================================
//   flaresMemberNew.js
//   Add new members
// ==================================

var flaresApp = angular.module('flaresDecoration', ['flaresBase']);

flaresApp.controller('newDecorationController', function($scope, $window, $location, $filter, $q, flAPI, flResource){




    //=======================================
    // DATA
    
    // Onboarding Context
    
	// New member DTO
	$scope.dec = new Decoration();
    
    // END DATA
    //=======================================
    
    $scope.state = {
        stage: 1,       // Leave deactivated but there's only a single stage
        totalStages: 1,
        isSaving: false,
        submitPreference: 1         // 1 for submit and view; 2 for submit and another
    };

    // This data should be extracted from reference service
	$scope.formData = {
		decorationTiers: []             // TODO
	};
	
	//======================
	// Workflow Screen navigation
    
    $scope.wf = {
        // Nav actions
        next: function(){ $scope.state.stage++ },
        // Form actions
        submitData: function(){
            submitData().then(function(){
                if ($scope.state.submitPreference === 2){
                    $window.location.reload();           
                }
                else {
                    $window.location.href = flResource('decoration').addFragment([$scope.dec.id, 'view', 'details']).build();                
                }
            }).catch(function(error){
                console.warn(error);
                $scope.state.errorMessage = angular.isObject(error) ? JSON.stringify(error) : error;
            });
        }
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
	// Fetch reference data for decorations
	//==================
	
    flAPI('refData').get('decorationTiers').then(function(response){
        // Specifically extract decorations
        if (response.data.length){
            $scope.formData.decorationTiers = response.data;
            $scope.dec.data.tier = $scope.formData.decorationTiers[0];
        }
	});

	
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

        var payload = {
            decoration: angular.copy(dec.data)          // the last part looks silly but hopefully should work
            // decoration: angular.merge(angular.copy(dec.data), { tier: dec.data.tier.tier })          // the last part looks silly but hopefully should work
            // decoration: angular.extend({}, dec.data, {
                // date_commence: $filter('date')(dec.data.date_commence, "yyyy-MM-dd"),
                // date_conclude: $filter('date')(dec.data.date_conclude, "yyyy-MM-dd")
            // })
        };
        
        // Check conclusion date form flag
        if (dec.hasNoConclusionDate){
            dec.data.date_conclude = null;
        }
        
        return flAPI('decoration').post(payload).then(function(response){
            if (response.data.error){
                console.warn(response.data.error);
                throw response.data.error;
            }
            
            dec.lastPersistTime = (new Date).toTimeString();
            if (response.data.id){
                dec.id = response.data.id;	
                dec.isSaved = true;
            }
            
            return dec.id;
            
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




