{{-- Search all members --}}
@extends('master')

@section('ng-app', 'flaresDashboard')
@section('ng-controller', 'dashboardController')
@section('title', 'Dashboard')

@section('heading')
	<h1>Dashboard</h1>
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
						<figure class="dashboard-stat" ng-cloak>
							<figcaption class="stat-caption">Posted Strength</figcaption>
							<div class="stat-figure">@{{stats.member.numActive}}</div>						
						</figure>
					</div>
					<div class="col-sm-6">
						<figure class="dashboard-stat" ng-cloak>
							<figcaption class="stat-caption">Total in system</figcaption>
							<div class="stat-figure">@{{stats.member.numTotal}}</div>
						</figure>
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
				<h3 class="panel-title">Activity Admin</h3>
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
				<h3 class="panel-title">Activity Ops</h3>
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
<script src="/app/components/dashboard/flaresDashboard.js"></script>
@endsection