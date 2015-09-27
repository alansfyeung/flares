{{-- Search all members --}}
@extends('master')

@section('ng-app', 'flaresDashboard')
@section('ng-controller', 'dashboardController')
@section('title', 'Dashboard')

@section('heading')
<div class="page-header">
	<h1>Dashboard</h1>
</div>
@endsection

@section('content')
<div class="row">

	<div class="col-sm-4">
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Member Stats</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-6">
						@{{stats.members}}
					</div>
					<div class="col-sm-6">
						@{{stats.awols}}
					</div>
				</div>
			</div>
		</div>
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Member Admin</h3>
			</div>
			<div class="panel-body">
				<ul>
					<li><a href="/members">Search members</a></li>
					<li><a href="/members/new">Onboard new members</a></li>
				</ul>
			</div>
		</div>
		
	</div>
	<div class="col-sm-4">
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Attendance Admin</h3>
			</div>
			<div class="panel-body">
				<ul>
					<li><a href="#">Register leave records</a></li>
					<li><a href="#">Register unit activities</a></li>
				</ul>
			</div>
		</div>
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Attendance Ops</h3>
			</div>
			<div class="panel-body">
				<ul>
					<li><a href="#">Mark the roll</a></li>
					<li><a href="#">Lookup roll</a></li>
					<li><a href="#">Create parade state</a></li>
				</ul>
			</div>
		</div>
		
	</div>
	
	
</div>
@endsection


@section('ng-script')
<script>

var memberSearchApp = angular.module('flaresDashboard', ['flaresBase']);
memberSearchApp.controller('dashboardController', function($scope, $http){
	
	$scope.stats = {
		members: 54,
		awols: 4
	};
	
});
	

</script>
@endsection