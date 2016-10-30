{{-- Home page for Flares users. Shows links for quick access --}}

@extends('primary')

@section('ng-app', 'flaresDashboard')
@section('ng-controller', 'dashboardController')
@section('title', 'Dashboard')

@section('heading')
	<h1>Dashboard</h1>
@endsection

@push('scripts')
<script src="/app/components/dashboard/flaresDashboard.js"></script>
@endpush

@section('content')
@verbatim
<div class="row">

	<div class="col-sm-4">
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Member management</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-6">
						<figure class="dashboard-stat" ng-cloak>
							<figcaption class="stat-caption">Posted Strength</figcaption>
							<div class="stat-figure">{{stats.member.numActive}}</div>						
						</figure>
					</div>
					<div class="col-xs-6">
                		<figure class="dashboard-stat" ng-cloak>
							<figcaption class="stat-caption">Total in system</figcaption>
							<div class="stat-figure">{{stats.member.numTotal}}</div>
						</figure>
					</div>
				</div>
			</div>
            <div class="list-group">
                <a href="/members" class="list-group-item">Search members</a>
                <a href="/members/new" class="list-group-item">Onboard new members</a>
            </div>
		</div>
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Decorations admin</h3>
			</div>
            <div class="panel-body bg-primary">
                “Decorations” are the digital representations of a 206 ACU member’s awards and achievements portfolio.
            </div>
            <div class="list-group">
                <a href="/members" class="list-group-item">Search decorations</a>
                <a href="/members/new" class="list-group-item">Add new decorations</a>
            </div>
		</div>
		
	</div>

</div>
@endverbatim
@endsection