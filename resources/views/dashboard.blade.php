{{-- Home page for Flares users. Shows links for quick access --}}

@extends('layouts.base')

@section('ng-app', 'flaresDashboard')
@section('ng-controller', 'dashboardController')
@section('title', 'Dashboard')

@section('heading')
	<h1>Dashboard</h1>
@endsection

@push('scripts')
<script src="/ng-app/components/dashboard/flaresDashboard.js"></script>
@endpush

@section('content')
<div class="row">

    @verbatim

    <div class="col-sm-6 col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Activity Log</h3>
            </div>
            <div class="panel-body">
                <p>Activity here</p>
                <p>Activity here</p>
                <p>Activity here</p>
                <p>Activity here</p>
                <p>Activity here</p>
            </div>
        </div>
    </div>
    
	<div class="col-sm-6 col-md-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
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
                                        <figcaption class="stat-caption">In system</figcaption>
                                        <div class="stat-figure">{{stats.member.numTotal}}</div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                        <div class="list-group">
                            <a href="/members" class="list-group-item active">View/search members</a>
                            <a href="/members/new" class="list-group-item">Add single member</a>
                            <!-- <a href="/members/newmulti" class="list-group-item">Multi member onboarding</a> -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Decorations</h3>
                        </div>
                        <div class="panel-body">
                            <figure class="dashboard-stat" ng-cloak>
                                <figcaption class="stat-caption">Decorations</figcaption>
                                <div class="stat-figure">{{stats.decoration.num}}</div>
                            </figure>
                        </div>
                        <div class="list-group">
                            <a href="/decorations" class="list-group-item">Search decorations</a>
                            <a href="/decorations/new" class="list-group-item">Create new decoration</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @endverbatim
    
    @if (env('APP_ENV') == 'dev')
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Activities <span class="label label-warning">alpha</span></h3>
            </div>
            <div class="panel-body">
                <figure class="dashboard-stat" ng-cloak>
                    <figcaption class="stat-caption">Total ??</figcaption>
                    <div class="stat-figure">TBA</div>
                </figure>
            </div>
            <div class="list-group">
                <a href="/activities" class="list-group-item">All activities</a>
                <a href="/activities/new" class="list-group-item">Add new activity</a>
            </div>
        </div>
	</div>
    @endif

</div>
@endsection