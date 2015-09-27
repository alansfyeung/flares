{{-- Display a single member --}}
@extends('master')

@section('ng-app', 'flaresApp')
@section('ng-controller', 'memberController')
@section('title', 'Member View')

@section('heading')
<div class="alert alert-warning" ng-cloak ng-show="member.errorNotFound">
	<strong>Member Lookup failed:</strong> The user with Regt# @{{member.regtNum}} couldn't be found.
</div>
<div class="alert alert-danger" ng-cloak ng-show="member.errorServerSide">
	<strong>Member Lookup failed:</strong> There was a server-side error and this record could not be retrieved
</div>
<div class="page-header" ng-if="member.record">
	<h1>@{{member.record.last_name}}, @{{member.record.first_name}} <small>@{{member.record.regt_num}}</small></h1>
</div>
@endsection

@section('memberDisplay')
<div ng-show="member.record">
	
	<div class="row">
		<div class="col-sm-8">
		
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a bs-show-tab href="#details" aria-controls="details" role="tab">Details</a></li>
				<li role="presentation"><a bs-show-tab href="#postings" aria-controls="postings" role="tab">Postings</a></li>
				<li role="presentation"><a bs-show-tab href="#attendance" aria-controls="attendance" role="tab">Attendance</a></li>
				<li role="presentation"><a bs-show-tab href="#iddocs" aria-controls="iddocs" role="tab">ID &amp; Docs</a></li>
				<li role="presentation"><a bs-show-tab href="#payments" aria-controls="payments" role="tab">Payments</a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="details">
					<section>
						<h2>Member</h2>
						<div class="row">
							
						</div>
					</section>
					<section>
						<h2>Personal</h2>
						
					</section>
					<section>
						<h2>Health and Medical</h2>
					</section>
					<section>
						<h2>Parent</h2>
					</section>
				</div>
				<div role="tabpanel" class="tab-pane" id="postings">
					<h2>Postings</h2>
					
				</div>
				<div role="tabpanel" class="tab-pane" id="attendance">
					<h2>Attendance</h2>
				</div>
				<div role="tabpanel" class="tab-pane" id="iddocs">
					<h2>ID Card</h2>
					<h2>Documents</h2>
				</div>
				<div role="tabpanel" class="tab-pane" id="payments">
					<h2>History</h2>
					
				</div>
			</div>		
			
		</div>
		<div class="col-sm-4">
			
			<!-- Member image and quick links -->
			<div>
				<img src="@{{member.record.photo_url}}" alt="@{{member.record.last_name}}" class="img-thumbnail memberview-image">
			</div>
			<ul class="list-inline">
				<li><a href="#">Edit image</a> | <a href="#">Remove image</a></li>
			</ul>
			
		</div>
		
	</div>
	
</div>
@endsection

@section('content')
	@yield('memberDisplay')
@endsection


@section('ng-script')
<script>

var flaresApp = angular.module('flaresApp', ['flaresBase']);
flaresApp.controller('memberController', function($scope, $http, $location){
	
	$scope.allowedActions = ['view', 'edit'];
	
	$scope.member = {
		regtNum: 0,
		record: null
	};
	
	// Read the url
	// get rid of any leading slash
	var path = $location.path();
	var pathFrags = (path.indexOf('/') === 0 ? path.substring(1) : path).split('/');		
	$scope.member.regtNum = pathFrags[0];
	
	// Retrieve this member
	if ($scope.member.regtNum){
		$http.get('/api/member/'+$scope.member.regtNum).then(function(response){
			$scope.processMemberRecord(response.data);
		}, function(response){
			if (response.status == 404){
				$scope.member.errorNotFound = true;
			}
			else {
				$scope.member.errorServerSide = true;
			}
		});
		
	}
	
	// Determine which controller to instatiate
	// TBA -- is this needed
	if (pathFrags.length > 0){
		$scope.action = ~$scope.allowedActions.indexOf(pathFrags[1]) ? pathFrags[1] : 'view';
	}
	
	
	$scope.processMemberRecord = function(r){
		if (!r.photo_url){
			r.photo_url = '/img/anon.png';
		}
		
		$scope.member.record = r;
	};
	
	
});

// flaresApp.controller('memberEditController', function($scope, $routeParams, $location){
	// console.log('Edit', $routeParams);
// });


</script>
@endsection