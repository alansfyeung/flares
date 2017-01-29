/* global angular */
// ==================================
//   flaresMemberSearch.js
//   Search for users before selecting one to edit
//   Todo: omni-search bar (a la Google)
// ==================================

var flaresApp = angular.module('flaresMemberSearch', ['flaresBase']);

flaresApp.run(['$http', '$templateCache', function($http, $templateCache){
    $http.get('/app/components/member/memberContextMenuTemplate.html').then(function(response){
        $templateCache.put('memberContextMenuTemplate.html', response.data);
    });
}]);

flaresApp.controller('memberSearchController', function($scope, $location, $window, $uibModal, flAPI, flResource){
	$scope.results = [];
	$scope.activeMember = null;
    
    $scope.state = {
        advancedSearch: false
    };
	$scope.formData = {
		ranks: [
			{ id: '', name: 'Any rank' }
		]
	};
    
    // $scope.searchKeywords = (typeof $location.search() === 'object' && $location.search().keywords) || '';
    $scope.searchKeywords = '';
	// $scope.searchParams = angular.merge({
		// rank: '',
		// first_name: '',
		// last_name: '',
		// sex: '',
		// regt_num: '',
		// is_active: '',
		// discharged: ''
	// }, $location.search() || {});
    $scope.searchParams = $location.search() || {};
	
    $scope.submitSimpleSearch = function(){
        
        $location.search({ keywords: $scope.searchKeywords });
        flAPI('member').get(['search'], {
			params: { 'keywords': $scope.searchKeywords }
		}).then(function(response){
			$scope.results = response.data.members;
			angular.forEach($scope.results, parseMemberSearchResult);
			
		}, function(response){
			console.warn('Error - member search', response);
		});
        
    };
    
	$scope.submitAdvancedSearch = function(){

		$location.search($scope.searchParams);
		flAPI('member').get(['search'], {
			params: $scope.searchParams
		}).then(function(response){
			$scope.results = response.data.members;
			angular.forEach($scope.results, parseMemberSearchResult);
			
		}, function(response){
			console.warn('Error - member search', response);
		});
        
	};
	
    $scope.selectMember = function(member){
        $scope.activeMember = member;
        $window.location.href = flResource('member')
            .setFragment([$scope.activeMember.regt_num, 'view', 'details'])
            .getLink();
    };    
    $scope.selectMemberContext = function(member){
        $scope.activeMember = member;
        openContextMenu();
    };
	
    
    //==================
    // submit the search if params were already given
    // Or perform a default search of most recents
	if ($location.search() && Object.keys($location.search()).length > 0){
        console.log('keywords', $location.search().keywords);
        if ($location.search().keywords){
    		$scope.submitSimpleSearch();            
        }
        else {
            $scope.submitAdvancedSearch();
        }
	}
    else {
        // TODO: Add result limit to the search function
        flAPI('member').get(['search'], {
			params: { 'orderBy': 'CREATED' }
		}).then(function(response){
			$scope.results = response.data.members;
			angular.forEach($scope.results, parseMemberSearchResult);
		}, function(response){
			console.warn('Error - default member search', response);
		});
    }
    
	
	angular.element('[name=search-surname]').focus();
    
    
	//==================
	// Fetch reference data for platoons and ranks
	
	flAPI('refData').getAll().then(function(response){
		if (response.data.ranks){
			$scope.formData.ranks = response.data.ranks;
			$scope.formData.ranks.unshift({abbr: '', name: 'Any rank'});
		}
	});
	
    
    // ==============
    // Function decs
    
    var MS_PER_YEAR = 1000 * 60 * 60 * 24 * 365.2425;
    function parseMemberSearchResult(result){
        if (result.dob && !isNaN(+new Date(result.dob))){
            result.age = Math.floor((Date.now() - (new Date(result.dob)).getTime()) / MS_PER_YEAR);
            result.ageDetails = result.age;					
            var ageTurningThisYear = (new Date()).getFullYear() - (new Date(result.dob)).getFullYear();
            if (ageTurningThisYear !== result.age){
                result.ageDetails +=  ' > ' + ageTurningThisYear;
            }
        }
        else {
            result.age = '0';
            result.ageDetails = '??';
            
        }
    }
    
    function openContextMenu(){
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'memberContextMenuTemplate.html',
            controller: 'memberContextMenuController',
            scope: $scope,
            size: 'sm',
            resolve: {
                context: function(){
                    return $scope.activeMember;
                }
            }
        });

        modalInstance.result.then(function(selectedItem){
            // Item clicked
            $scope.selected = selectedItem;
        }, function(){
            // Cancellation
            console.log('Modal dismissed at: ' + new Date());
        });
    }
	
});
	
    
flaresApp.controller('memberContextMenuController', function ($scope, $parse, $window, $modalInstance, flResource, context){
    
    $scope.member = context;
    $scope.memberStatus = {
        labelClassNames: [],
        label: 'Unknown'
    };
    
    // Set memberStatus label
    if ($scope.member.is_deleted){
        $scope.memberStatus.labelClassNames.push('label-warning');
        $scope.memberStatus.label = 'Discharged';
    }
    else if (!$scope.activeMember.is_active || $scope.activeMember.is_active === '0'){
        $scope.memberStatus.labelClassNames.push('label-danger');
        $scope.memberStatus.label = 'Inactive';
    }
    else {
        $scope.memberStatus.labelClassNames.push('label-success');
        $scope.memberStatus.label = 'Active';
    }
    
    $scope.bodyButtons = [{
        label: 'Member profile',
        classNames: ['btn-default'],
        click: 'linkToMember'
    }, {
        label: 'Assign decoration',
        classNames: ['btn-primary'],
        click: 'assignDecoration'
    }];
    $scope.footerButtons = [{
        label: 'Cancel',
        classNames: ['btn-default']
    }];
    
    var clickActions = {
        linkToMember: function(){
            $window.location.href = flResource('member')
                .setFragment([$scope.member.regt_num, 'view', 'details'])
                .getLink();
            // Or if you want to return a value to the parent controller,
            // $modalInstance.close();
        },
        viewDecorations: function(){
            var frag = $scope.member.regt_num;
            $window.location.href = flResource('member').single('decorations').setFragment(frag).getLink();
        },
        assignDecoration: function(){
            var frag = $scope.member.regt_num;
            $window.location.href = flResource('member').single('decorations/new').setFragment(frag).getLink();
        }
    };
    
    $scope.parseClick = function(actionName){
        var func = $parse(actionName + '()');
        func(clickActions);
    };
    
    $scope.cancel = function(){
        $modalInstance.dismiss('cancel');
    };
    // $scope.ok = function () {
        // $modalInstance.close($scope.selected.item);
    // };
});