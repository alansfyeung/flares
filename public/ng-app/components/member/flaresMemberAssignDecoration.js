var flaresApp = angular.module('flaresMemberAssignDecoration', ['flaresBase']);

flaresApp.run(['$http', '$templateCache', function($http, $templateCache){
    $http.get('/ng-app/components/decoration/decorationTypeaheadTemplate.html').then(function(response){
        $templateCache.put('decorationTypeaheadTemplate.html', response.data);
    });
    $http.get('/ng-app/components/decoration/decorationTypeaheadPopupTemplate.html').then(function(response){
        $templateCache.put('template/typeahead/typeahead-popup.html', response.data);
    });
}]);

flaresApp.controller('memberAssignDecorationController', function($scope, $location, $filter, $controller, $uibModal, flAPI, flResource){
    
    // Extend this controller with viewEditController
    angular.extend(this, $controller('viewEditController', {$scope: $scope})); 
    
    var c = this;
    c.extendConfig({
        'unloadWarning': 'You are editing this decoration record, and will lose any unsaved changes.'
    });

    $scope.state = Object.create(c.state);        // inherit the proto
    $scope.state.showDecorationDropdownList = false;
    $scope.formData = { 
        decorationTiers: [],
        months: [ 
            { name: 'Jan', value: 0 },
            { name: 'Feb', value: 1 },
            { name: 'Mar', value: 2 },
            { name: 'Apr', value: 3 },
            { name: 'May', value: 4 },
            { name: 'Jun', value: 5 },
            { name: 'Jul', value: 6 },
            { name: 'Aug', value: 7 },
            { name: 'Sep', value: 8 },
            { name: 'Oct', value: 9 },
            { name: 'Nov', value: 10 },
            { name: 'Dec', value: 11 }
        ],
        awardDate: {
            month: 0,
            year: 1975,
        },
        resetAwardDate: resetAwardDate
    };
        
    $scope.decorations = [];
	$scope.member = {};
    $scope.memberPictureUrl = '';
    $scope.award = new Award();
    $scope.selectedTier = undefined;
    
    
    var memberPictureDefaultUrl, decorationDefaultBadgeUrl;
	
	// Read the url
    if (c.loadWorkflowPath()){
        retrieveMember();
        retrieveDecorations();
        resetAwardDate();
    }

	flAPI('refData').getAll().then(function(response){
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
        resetAwardDate();
        $scope.state.showDecorationDropdownList = false;
        setTimeout(function(){
            angular.element('#selectedDecorationField').focus();            
        }, 300);
    };
    
    $scope.cancelHref = function(){
        if ($scope.member.regt_num){
            return flResource('member').setFragment([$scope.member.regt_num, 'view', 'decorations']).getLink();
        }
        else {
            return flResource('member').getLink();
        }
    };

    $scope.$watch('award.selectedDecoration', function(newVal){
        // Check out if the badge image exists
        if (newVal && newVal.dec_id){
            flAPI('decoration').nested('badge', [newVal.dec_id]).get().then(function(response){
                if (response.data.exists){
                    $scope.award.selectedDecorationBadgeUrl = flResource().raw(['/media', 'decoration', newVal.dec_id, 'badge']);
                } else {
                    $scope.award.selectedDecorationBadgeUrl = decorationDefaultBadgeUrl;
                }
            });
        }
    }); 
    
    $scope.$watch('selectedTier', function (newVal){
        $scope.award.selectedDecoration = undefined;
        // var selectedDecorationField = angular.element('#selectedDecorationField');
        // if (selectedDecorationField){
            // console.log(selectedDecorationField.value);
            // selectedDecorationField.value = '';
        // }
    });
    
    $scope.$watch('formData.awardDate.month', function(newVal){
        if ($scope.award){
            $scope.award.setDateMonth(newVal);
        }
    });
    
    $scope.$watch('formData.awardDate.year', function(newVal){
        if ($scope.award){
            // Range of OK is 1975 –> (this year + 5)
            if (newVal >= 1975 && newVal <= ((new Date).getFullYear() + 5)){
                $scope.award.setDateYear(newVal);
            }
        }
    });
    
    //==================
	// Fetch reference data for decorations
	//==================
	
    flAPI('refData').get('decorationTiers').then(function(response){
        if (response.data.length){
            var tiers = response.data;
            angular.forEach(tiers, function(tier, index, tiers){
                tiers[index].tierName = tier.tier + ': ' + tier.tierName;
            });
            $scope.formData.decorationTiers = tiers;
            // $scope.selectedTier = $scope.formData.decorationTiers[0];
        }
	});

    // ====================
    // Function decs
    // ====================
    
	function retrieveMember(){
		if ($scope.state.path.id){
			flAPI('member').get([$scope.state.path.id]).then(function(response){
                
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
        flAPI('decoration').getAll().then(function(response){
            if (response.data && response.data.decorations){
                $scope.decorations = response.data.decorations;
                setTimeout(function(){
                    angular.element('#selectedDecorationField').focus();            
                }, 300);
            }
            else {
                throw 'Failed to get list of decorations';
            }
        }, function(response){
            console.warn(response);
        });
    }
    
    function saveAssignDecoration(){
        // Because we are using typeahead, which might assign strings to the Selected Decoration,
        // we should check that it contains a dec_id field
        if (angular.isObject($scope.award.selectedDecoration) && $scope.award.selectedDecoration.hasOwnProperty('dec_id')){
            var regtNum = $scope.member.regt_num;
            var payload = {}; 
            payload.memberDecoration = angular.extend({}, $scope.award.data, {
                date: $filter('date')($scope.award.data.date, 'yyyy-MM-dd'),
                dec_id: $scope.award.selectedDecoration.dec_id
            });
            
            flAPI('member').nested('decoration', regtNum).post(payload).then(function(response){
                // Focus on the "Assign another" button
                $scope.award.saved = true;
                setTimeout(function(){
                    angular.element('#assignAnotherDecorationButton').focus();            
                }, 300);
            }).catch(function(errorResponse){
                console.error(errorResponse);
                if (errorResponse.data.error){
                    if (String(errorResponse.data.error.code) === '5030'){
                        $scope.award.saveDuplicateError = true;
                    }
                }
                else {
                    $scope.award.saveError = true;                    
                }
            });
        }
        else {
            console.warn('No decoration assigned yet');
        }
    }
    
    // Attempt to find the decoration currently like this one 
    // function deduplicateExistingDecoration(){
        
    // }
    
    function resetAwardDate(){
        var awardDate = $scope.formData.awardDate;
        awardDate.month = (new Date).getMonth();
        awardDate.year = (new Date).getFullYear();
        var award = $scope.award;
        if (award){
            award.setDateMonth(awardDate.month);
            award.setDateYear(awardDate.year);
        }
    }

    //======================
    // Classes
    //======================
	
    function Award(){
        this.saved = false;
        this.saveError = false;
        this.saveDuplicateError = false;
        this.selectedDecoration = '';
        this.selectedDecorationBadgeUrl = '';
        this.data = {
            dec_id: 0,
            citation: '',
            date: new Date()        // Default to today
        };
        this.setDateMonth = function(month){
            // Always set to first day of the month
            this.data.date.setDate(1);
            this.data.date.setMonth(month);
        };
        this.setDateYear = function(year){
            this.data.date.setFullYear(year);
        };
    }
    
    //======================
    // End Classes
    //======================
    
});
