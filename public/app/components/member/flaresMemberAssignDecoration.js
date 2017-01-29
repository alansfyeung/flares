var flaresApp = angular.module('flaresMemberAssignDecoration', ['flaresBase']);

flaresApp.controller('memberAssignDecorationController', function($scope, $window, $location, $filter, $controller, $uibModal, flAPI, flResource){
    
    // Extend this controller with resourceController
    angular.extend(this, $controller('resourceController', {$scope: $scope})); 
    
    var c = this;
    c.extendConfig({
        'unloadWarning': 'You are editing this decoration record, and will lose any unsaved changes.'
    });
    
    $scope.state = Object.create(c.state);        // inherit the proto
        
    $scope.decorations = [];
	$scope.member = {};
    $scope.memberPictureUrl = '';
    $scope.award = new Award();
    
    
    var memberPictureDefaultUrl, decorationDefaultBadgeUrl;
	
	// Read the url
    if (c.loadWorkflowPath()){
        retrieveMember();
        retrieveDecorations();
    }

	flAPI('refData').get('misc').then(function(response){
        if (response.data.misc){
            var found1 = response.data.misc.find(function(misc){ return misc.name === 'PROFILE_UNKNOWN_IMAGE_PATH' });
            if (found1){
                memberPictureDefaultUrl = found1.value;
            }
            var found2 = response.data.misc.find(function(misc){ return misc.name === 'BADGE_UNKNOWN_IMAGE_PATH' });
            if (found2){
                decorationDefaultBadgeUrl = found2.value;
                console.log(decorationDefaultBadgeUrl);
            }
        }
	});
    
    $scope.submit = saveAssignDecoration;
    
    $scope.assignAnother = function(){
        $scope.award = new Award();
    };
    
    $scope.cancel = function(){
        if ($scope.member.regt_num){
            $window.location.href = flResource('member').setFragment($scope.member.regt_num).getLink();
        }
        else {
            $window.location.href = flResource('member').getLink();
        }
    };
    
    $scope.$watch('award.selectedDecoration', function(newVal){
        // Check out if the badge image exists
        if (newVal && newVal.dec_id){
            flAPI('decoration').nested('badge', [newVal.dec_id]).get('exists').then(function(response){
                if (response.data.exists){
                    $scope.award.selectedDecorationPictureUrl = flAPI('decoration').nested('badge', [newVal.dec_id]).url();
                } else {
                    console.log(decorationDefaultBadgeUrl);
                    $scope.award.selectedDecorationPictureUrl = decorationDefaultBadgeUrl;
                }
            });
        }
    });

    // ====================
    // Function decs
    
	function retrieveMember(){
		if ($scope.state.path.id){
			flAPI('member').get([$scope.state.path.id]).then(function(response){
				// c.util.convertToDateObjects(['dob', 'idcard_expiry', 'created_at', 'updated_at', 'deleted_at'], member);
                
                
                if (response.data && response.data.member){
                    
                    var memberId = response.data.member.regt_num;
                    
                    $scope.member = response.data.member;
                    $scope.state.isMemberLoaded = true;
                    
                    // Check if the member image exists, and if it does then load it
                    // Otherwise attempt to find default member image from refdata
                    flAPI('member').nested('picture', [memberId]).get('exists').then(function(response){
                        if (response.data.exists){
                            $scope.memberPictureUrl = flAPI('member').nested('picture', [memberId]).url();
                        }
                    
                    });
                }
                else {
                    throw 'BadRequest.jpg';
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
        // flAPI('member').nested('decoration', [$scope.state.path.id]).getAll().then(function(response){
        flAPI('decoration').getAll().then(function(response){
            
            
            if (response.data && response.data.decorations){
                $scope.decorations = response.data.decorations;
            }
            else {
                throw 'Failed to get list of decorations';
            }
            
        }, function(response){
            console.warn(response);
        });
    }
    
    function saveAssignDecoration(){
        console.log($scope.award.selectedDecoration);
        if ($scope.award.selectedDecoration){
            var regtNum = $scope.member.regt_num;
            var payload = {}; 
            payload.memberDecoration = angular.extend({}, $scope.award.data, {
                date: $filter('date')($scope.award.data.date, 'yyyy-MM-dd'),
                dec_id: $scope.award.selectedDecoration.dec_id
            });
            
            // console.log(JSON.stringify(payload));
            // return;
            
            flAPI('member').nested('decoration', regtNum).post(payload).then(function(response){
                $scope.award.saved = true;
            }).catch(function(){
                $scope.award.saveError = true;
            });
        }
        else {
            console.warn('No decoration assigned yet');
        }
    }

    //======================
    // Classes
	
    function Award(){
        this.saved = false;
        this.saveError = false;
        this.selectedDecoration = 0;
        this.selectedDecorationPictureUrl = '';
        this.data = {
            dec_id: 0,
            citation: '',
            date: new Date()        // Default to today
        }
    }
    
    // End Classes
    //======================
    
});