var flaresApp = angular.module('flaresMemberAssignDecoration', ['flaresBase']);

flaresApp.controller('memberAssignDecorationController', function($scope, $location, $controller, $uibModal, flAPI){
    
    // Extend this controller with resourceController
    angular.extend(this, $controller('resourceController', {$scope: $scope})); 
    
    var c = this;
    c.extendConfig({
        'unloadWarning': 'You are editing this decoration record, and will lose any unsaved changes.'
    });
    
    $scope.state = Object.create(c.state);        // inherit the proto
	$scope.member = {};
    $scope.decoration = [];
	
	// Read the url
    if (c.loadWorkflowPath()){
        retrieveMember();
        retrieveDecorations();
    }
	
	//==================
	// Fetch reference data for platoons and ranks
	
	// $http.get('/api/refdata').then(function(response){
	flAPI('refData').getAll().then(function(response){
        
        
        
	});

    // ====================
    // Function decs
    
	function retrieveMember(){
		if ($scope.state.path.id){
			flAPI('member').get([$scope.state.path.id]).then(function(response){
				// c.util.convertToDateObjects(['dob', 'idcard_expiry', 'created_at', 'updated_at', 'deleted_at'], member);
                
                if (response.data && response.data.member){
                    $scope.member = response.member;                    
                    $scope.state.isMemberLoaded = true;
                }
                else {
                    
                }
				
			}, function(response){
				if (response.status == 404){
					$scope.member.errorNotFound = true;
				}
				else {
					$scope.member.errorServerSide = true;
				}
			});
		}
		else {
			console.warn('Member ID not specified');
		}
	};
    
    function retrieveDecorations(){
        
    }
    
    function saveAssignDecoration(){
        if (){
            
        }
    }

	
});