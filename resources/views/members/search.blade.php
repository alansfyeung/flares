{{-- Search all members --}}
@extends('master')

@section('ng-app', 'flaresApp')
@section('ng-controller', 'memberSearchController')
@section('title', 'Members')

@section('heading')
<div class="page-header">
	<h1>Search all members</h1>
</div>
@endsection

@section('memberSearch')
<form class="form-horizontal" ng-submit="submitSearch()">
	<div class="row">
	
		<div class="col-sm-6 col-md-4">
			<div class="form-group">
				<label class="control-label col-sm-3">Surname</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="search-surname" ng-model="searchParams.last_name" tabindex="1" placeholder="Any last names"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Given Names</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="search-given-names" ng-model="searchParams.first_name" tabindex="2" placeholder="Any given names"/>
				</div>
			</div>			
			<div class="form-group">
				<label class="control-label col-sm-3">Sex</label>
				<div class="col-sm-9">
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="" tabindex="5"/> Any</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="M" tabindex="6"/> Male</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="F" tabindex="7"/> Female</label>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6 col-md-4">
			<div class="form-group">
				<label class="control-label col-sm-3">Rank</label>
				<div class="col-sm-9">
					<select class="form-control" ng-model="searchParams.rank" tabindex="3">
						<option ng-repeat="rank in formData.ranks" value="@{{rank.abbr}}">@{{rank.name}}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Regt Num</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="search-regt-num" ng-model="searchParams.regt_num" tabindex="4" placeholder="Any regt number"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Discharged</label>
				<div class="col-sm-9">
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="" tabindex="8"/> Don't include</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="include" tabindex="9"/> Include</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="only" tabindex="10"/> Only</label>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6 col-md-4">
			<div class="well">
				<div class="form-group">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary" tabindex="11">Search</button>
					</div>				
				</div>
			</div>
		</div>
		
	</div>
</form>

<hr/>

<section class="search-results">
	<div class="">@{{results.length}} results for search</div>
	<table class="table table-hover" ng-show="results.length > 0">
		<thead>
			<tr>
				<th>Regt Num</th>
				<th>Last Name</th>
				<th>Given Names</th>
				<th>Rank</th>
				<th>Sex</th>
				<th>Age</th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="result in results" ng-class="{'danger': !result.is_active, 'warning': result.deleted_at}" launch-contextmenu="@{{result.regt_num}}">
				<td>@{{result.regt_num}}</td>
				<td>@{{result.last_name}}</td>
				<td>@{{result.first_name}}</td>
				<td>@{{result.rank}}</td>
				<td>@{{result.sex}}</td>
				<td>@{{result.ageDetails}}</td>
				<!-- <td><a href="/member#!/@{{result.regt_num}}/view">View Member</a></td> -->
			</tr>
		</tbody>
	</table>
</section>
@endsection

@section('memberSearchModal')
<div class="modal" id="memberSearchContextMenu" tabindex="-1" role="dialog" aria-labelledby="activeMemberTitle">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="activeMemberTitle">
					<span>@{{activeMember.last_name}}, @{{activeMember.first_name}}</span>
				</h4>
				<h5 class="modal-subtitle">@{{activeMember.regt_num}}</h5>
			</div>
			<div class="modal-body text-center">
				<a href="/member#!/@{{activeMember.regt_num}}/view" id="viewActiveMember" class="btn btn-block btn-primary activemember-view">View/Edit member details</a>
				<button class="btn btn-block btn-default">Find roll entries [TBA]</button>
			</div>
			<div class="modal-footer">
				<button class="btn btn-block btn-danger" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endsection


@section('content')
	@yield('memberSearch')
	@yield('memberSearchModal')
@endsection


@section('ng-script')
<script>
var flaresApp = angular.module('flaresApp', []);

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
	



</script>
@endsection
