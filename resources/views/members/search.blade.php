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
				<label class="control-label col-sm-3">Rank</label>
				<div class="col-sm-9">
					<select class="form-control" ng-model="searchParams.rank">
						<option ng-repeat="rank in formData.ranks" value="@{{rank.id}}">@{{rank.name}}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Surname</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="searchParams.last_name" placeholder="Any last names"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Given Names</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="searchParams.first_name" placeholder="Any given names"/>
				</div>
			</div>			
		</div>
		
		<div class="col-sm-6 col-md-4">
			<div class="form-group">
				<label class="control-label col-sm-3">Regt Number</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" ng-model="searchParams.regt_num" placeholder="Any regt number"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Sex</label>
				<div class="col-sm-9">
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value=""/> Any</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="M"/> Male</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="F"/> Female</label>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Active</label>
				<div class="col-sm-9 checkbox">
					<label><input type="checkbox" ng-model="searchParams.is_active" ng-true-value="'1'" ng-false-value="'0'"/> Show active only</label>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6 col-md-4">
			<div class="well">
				<div class="form-group">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary">Search</button>
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
				<th>Operations</th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="result in results" ng-class="{danger: !result.is_active}">
				<td>@{{result.regt_num}}</td>
				<td>@{{result.last_name}}</td>
				<td>@{{result.first_name}}</td>
				<td>@{{result.rank}}</td>
				<td>@{{result.sex}}</td>
				<td><a href="/member#!/@{{result.regt_num}}/view">View Member</a></td>
			</tr>
		</tbody>
	</table>
</section>
@endsection

@section('content')
	@yield('memberSearch')
@endsection


@section('ng-script')
<script>
var flaresApp = angular.module('flaresApp', []);

flaresApp.controller('memberSearchController', function($scope, $http, $location){
	$scope.results = [];
	$scope.formData = {
		// ranks: ['', 'REC', 'CDT', 'CDTLCPL', 'CDTCPL', 'CDTSGT', 'CDTWO2', 'CDTWO1', 'CUO']
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
		is_active: ''
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
			// console.log(response);
			$scope.results = response.data;
		}, function(response){
			console.log('Error - member search', response);
		});
	};
	
	// submit the search if stuff was given
	if (typeof $location.search() === 'object' && Object.keys($location.search()).length > 0){
		$scope.submitSearch();
	}
	
});
	



</script>
@endsection
