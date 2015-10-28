// ==================================
//   flaresMemberSearch.js
//   Search for users before selecting one to edit
//   Todo: omni-search bar (a la Google)
// ==================================

var flaresApp = angular.module('flaresMemberSearch', ['flaresBase']);
flaresApp.run(function($templateCache){
    $templateCache.put('memberContextMenuTemplate.html', '<div class="modal-header"><h4 class="modal-title">{{member.last_name}}, {{member.first_name}}</h4><h5 class="modal-subtitle">{{member.regt_num}} <span class="label" ng-class="memberStatus.labelClassNames">{{memberStatus.label}}</span></h5></div> \
        <div class="modal-body"><a class="btn btn-block" ng-repeat="menuItem in bodyButtons" ng-class="menuItem.classNames" ng-click="parseClick(menuItem.click)">{{menuItem.label}}</a></div> \
    <div class="modal-footer"><a class="btn btn-block" ng-repeat="cancelItem in footerButtons" ng-class="cancelItem.classNames" ng-click="cancel()">{{cancelItem.label}}</a></div>');
    
});

flaresApp.controller('memberSearchController', function($scope, $location, $uibModal, flaresAPI){
	$scope.results = [];
	$scope.activeMember = null;
	$scope.formData = {
		ranks: [
			{ id: '', name: 'Any rank' }
		]
	};
	
	$scope.searchParams = angular.merge({
		rank: '',
		first_name: '',
		last_name: '',
		sex: '',
		regt_num: '',
		is_active: '',
		discharged: ''
	}, typeof $location.search() === 'object' && $location.search());
	
	$scope.submitSearch = function(){
		$location.search(function(){
			var search = {};
			angular.forEach($scope.searchParams, function(value, key){
				if (value){
					this[key] = value;
				}
			}, search);
			return search;
		}());
		
		flaresAPI.member.get(['search'], {
			params: $scope.searchParams
		}).then(function(response){
			$scope.results = response.data.members;
			
			var MS_PER_YEAR = 1000 * 60 * 60 * 24 * 365.2425;
			angular.forEach($scope.results, function(result){
				if (result.dob && !isNaN(new Date(result.dob))){
					var ageTurningThisYear = (new Date()).getFullYear() - (new Date(result.dob)).getFullYear();
					result.age = Math.floor((Date.now() - (new Date(result.dob)).getTime()) / MS_PER_YEAR);
					result.ageDetails = result.age + ' (' + ageTurningThisYear + ')';					
				}
				else {
					result.age = '0';
					result.ageDetails = '??';
					
				}
			});
			
		}, function(response){
			console.warn('Error - member search', response);
		});
	};
	
    $scope.selectMember = function(member){
        $scope.activeMember = member;
        openContextMenu();
    };

	
    
    //==================
	// submit the search if params were already given
	if (typeof $location.search() === 'object' && Object.keys($location.search()).length > 0){
		$scope.submitSearch();
	}
	
	angular.element('[name=search-surname]').focus();
    
    
	//==================
	// Fetch reference data for platoons and ranks
	
	flaresAPI.refData.getAll().then(function(response){
		if (response.data.ranks){
			$scope.formData.ranks = response.data.ranks;
			$scope.formData.ranks.unshift({abbr: '', name: 'Any rank'});
		}
	});
	
    
    // ==============
    // Function decs
    
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
	
    
flaresApp.controller('memberContextMenuController', function ($scope, $parse, $window, $modalInstance, flaresLinkBuilder, context){
    
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
        label: 'View/edit member',
        classNames: ['btn-primary'],
        click: 'linkToMember'
    }, {
        label: 'Find roll entries [TBA]',
        classNames: ['btn-default']
    }];
    $scope.footerButtons = [{
        label: 'Cancel',
        classNames: ['btn-default']
    }];
    
    var clickActions = {
        linkToMember: function(){
            var frag = [$scope.member.regt_num, 'view', 'details'];
            $window.location.href = flaresLinkBuilder.page().member().fragment(frag).getLink();
            // Or if you want to return a value to the parent controller,
            // $modalInstance.close();
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