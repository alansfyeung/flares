// ==================================
//   flaresMemberSearch.js
//   Search for users before selecting one to edit
//   Todo: omni-search bar (a la Google)
// ==================================

var flaresApp = angular.module('flaresMemberSearch', []);

flaresApp.directive('launchContextmenu', function(){
	return { 
		link: function (scope, element, attr) {
			element.click(function(e) {
				e.preventDefault();
				// Find and set the active member
				var lookupRegtNum = attr.launchContextmenu;
				angular.forEach(scope.results, function(result){
					if (result.regt_num === lookupRegtNum){
						scope.$apply(function(){
							scope.activeMember = result;
							console.log(scope.activeMember);
							$('#memberSearchContextMenu').on('show.bs.modal', function (event) {
								var modal = $(this);
								modal.find('.modal-title').text(scope.activeMember.last_name + ', ' + scope.activeMember.first_name);
								var $modalMemberStatus = $('<span class="label">');
								if (scope.activeMember.is_deleted){
									$modalMemberStatus.addClass('label-warning').text('Discharged');
								}
								else if (!scope.activeMember.is_active || scope.activeMember.is_active === '0'){
									$modalMemberStatus.addClass('label-danger').text('Inactive');
								}
								else {
									$modalMemberStatus.addClass('label-success').text('Active');
								}
								modal.find('.modal-subtitle').text(scope.activeMember.regt_num + '  ').append($modalMemberStatus);
								modal.find('.activemember-view').attr('href', '/member#!/'+scope.activeMember.regt_num+'/view');
							}).modal();
						});
					}
				});
			});
		}
	};	
});

flaresApp.controller('memberSearchController', function($scope, $http, $location){
	$scope.results = [];
	$scope.activeMember = null;
	$scope.formData = {
		ranks: [
			{ id: '', name: 'Any rank' },
			{ id: 'REC', name: 'Recruit' },
			{ id: 'CDT', name: 'Cadet' },
			{ id: 'CDTLCPL', name: 'Lance Corporal' },
			{ id: 'CDTCPL', name: 'Corporal' }
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
		
		$http.get('/api/member/search', {
			params: $scope.searchParams
		}).then(function(response){
			$scope.results = response.data;
			
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
	
	//==================
	// submit the search if params were already given
	if (typeof $location.search() === 'object' && Object.keys($location.search()).length > 0){
		$scope.submitSearch();
	}
	
	
	//==================
	// Fetch reference data for platoons and ranks
	
	$http.get('/api/refdata').then(function(response){
		if (response.data.ranks){
			$scope.formData.ranks = response.data.ranks;
			$scope.formData.ranks.unshift({abbr: '', name: 'Any rank'});
		}
	});
	
	angular.element('[name=search-surname]').focus();
	
});
	